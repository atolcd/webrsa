<?php
	/**
	 * Fichier source de la classe XformHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'FormHelper', 'View/Helper' );

	if( !defined( 'REQUIRED_MARK' ) ) {
		define( 'REQUIRED_MARK', '<abbr class="required" title="'.__( 'Validate::notEmpty' ).'">*</abbr>' );
	}

	/**
	 * La classe XformHelper adapte la classe FormHelper de CakePHP pour lui ajouter
	 * la traduction automatique du label (suivant le domain), la possibilité de
	 * régler plus finement les listes déroulantes de dates et heures, ainsi que
	 * d'auters méthodes utiles.
	 *
	 * @package app.View.Helper
	 */
	class XformHelper extends FormHelper
	{
		/**
		 *
		 * @var array
		 */
		// public $_schemas = array();

		/**
		 *
		 * @param string $fieldName
		 * @param array $options
		 * @return string
		 */
		public function input( $fieldName, $options = array( ) ) {
			$options = array_merge(
				array('before' => null, 'between' => null, 'after' => null, 'format' => null),
				$this->_inputDefaults,
				$options
			);

			if( isset( $options['multiple'] ) && !empty( $options['multiple'] ) ) {
				return $this->multiple( $fieldName, $options );
			}

			return $this->_input( $fieldName, $options );
		}

		/**
		 *
		 * @param string $fieldName
		 * @param array $options
		 * @return string
		 */
		protected function _input( $fieldName, $options = array() ) {
			if( !isset( $options['label'] ) ) {
				$labelOptions = $options;
				$labelOptions['tag'] = false;
				$options['label'] = $this->label( $fieldName, null, $labelOptions );
			}
			else if( isset( $options['required'] ) && ( $options['required'] == true ) ) {
				$options['label'] = $this->required( $options['label'] );
			}

			if( isset( $options['multiple'] ) && !empty( $options['multiple'] ) ) {
				if( !empty( $options['label'] ) && !isset( $options['legend'] ) ) {
					$options['legend'] = $options['label'];
				}
				$options['label'] = false;
			}

			unset( $options['required'] );
			unset( $options['domain'] );

			if( isset( $options['type'] ) && in_array( $options['type'], array( 'radio' ) ) && !Set::check( $options, 'legend' )  ) {
				$options['legend'] = $options['label'];
			}

			// maxLength
			/*if( ( !isset( $options['type'] ) || in_array( $options['type'], array( 'string', 'text' ) ) ) && !isset( $options['maxlength'] ) ) { // FIXME: maxLength
				list( $model, $field ) = model_field( $fieldName );
				if( ClassRegistry::isKeySet( $model ) ) {
					if( !isset( $this->_schemas[$model] ) ) {
						$this->_schemas[$model] = ClassRegistry::init( $model )->schema();
					}
					$schema = $this->_schemas[$model];
					$field = Set::classicExtract( $schema, $field );
					if( !empty( $field ) && ( $field['type'] == 'string' ) && isset( $field['length'] ) ) {
						$options['maxlength'] = $field['length'];
					}
				}
			}*/

			return parent::input( $fieldName, $options );
		}

		/**
		 *
		 * @param string $fieldName
		 * @param array $options
		 * @return string
		 */
		public function multiple( $fieldName, $options = array() ) {
			$errors = Set::extract( $this->validationErrors, $fieldName );
			$htmlAttributes = array( 'class' => 'multiple' );
			if( !empty( $errors ) ) {
				$htmlAttributes['class'] = $htmlAttributes['class'].' error';
			}

			// FIXME: legend
			$label = Set::extract( $options, 'label' );
			if( empty( $label ) ) {
				$label =  $this->label( $fieldName, null, $options );
			}

			unset( $options['label'] );

			if( !isset( $options["fieldset"] ) || $options["fieldset"] != false ) {
				return $this->Html->tag(
					'fieldset',
					$this->Html->tag( 'legend', $label ).
						$this->_input( $fieldName, $options ),
					$htmlAttributes
				);
			}
			else {
				return $this->_input( $fieldName, $options );
			}
		}

		/**
		 *
		 * @param string $label
		 * @return string
		 */
		public function required( $label ) {
			return h( $label ).' '.REQUIRED_MARK;
		}

		/**
		 *
		 * @param type $fieldName
		 * @param type $text
		 * @param type $options
		 */
		public function label( $fieldName = null, $text = null, $options = array( ) ) {
			$options = array_merge( $this->_inputDefaults, $options );

			if( !empty( $fieldName ) && is_null( $text ) ) {
				$domain = Set::extract( $options, 'domain' );

				$msgid = preg_replace( '/\.[0-9]+\./', '.', $fieldName );
				if( empty( $domain ) || ( $domain == 'default' ) ) {
					$text = __( $msgid, true );
				}
				else {
					$text = __d( $domain, $msgid, true );
				}
			}

			if( isset( $options['required'] ) && ( $options['required'] == true ) ) {
				$text = $this->required( $text );
			}

			unset(
				$options['required'],
				$options['domain'],
				$options['options'],
				$options['type'],
				$options['empty']
			);

			if( isset( $options['tag'] ) && $options['tag'] === false ) {
				return $text;
			}

			return parent::label( $fieldName, $text, $options );
		}

		/**
		 *
		 * @see FormHelper de CakePHP 2.1.1
		 *
		 * @param type $fieldName
		 * @param type $label
		 * @param type $options
		 * @return type
		 */
		protected function _inputLabel( $fieldName, $label, $options ) {
			$labelAttributes = $this->domId( array( ), 'for' );
			$idKey = null;
			if( $options['type'] === 'date' || $options['type'] === 'datetime' ) {
				$firstInput = 'M';
				if(
						array_key_exists( 'dateFormat', $options ) &&
						($options['dateFormat'] === null || $options['dateFormat'] === 'NONE')
				) {
					$firstInput = 'H';
				}
				elseif( !empty( $options['dateFormat'] ) ) {
					$firstInput = substr( $options['dateFormat'], 0, 1 );
				}
				switch( $firstInput ) {
					case 'D':
						$idKey = 'day';
						$labelAttributes['for'] .= 'Day';
						break;
					case 'Y':
						$idKey = 'year';
						$labelAttributes['for'] .= 'Year';
						break;
					case 'M':
						$idKey = 'month';
						$labelAttributes['for'] .= 'Month';
						break;
					case 'H':
						$idKey = 'hour';
						$labelAttributes['for'] .= 'Hour';
				}
			}
			if( $options['type'] === 'time' ) {
				$labelAttributes['for'] .= 'Hour';
				$idKey = 'hour';
			}
			if( isset( $idKey ) && isset( $options['id'] ) && isset( $options['id'][$idKey] ) ) {
				$labelAttributes['for'] = $options['id'][$idKey];
			}

			if( is_array( $label ) ) {
				$labelText = null;
				if( isset( $label['text'] ) ) {
					$labelText = $label['text'];
					unset( $label['text'] );
				}
				$labelAttributes = array_merge( $labelAttributes, $label );
			}
			else {
				$labelText = $label;
			}

			if( isset( $options['id'] ) && is_string( $options['id'] ) ) {
				$labelAttributes = array_merge( $labelAttributes, array( 'for' => $options['id'] ) );
			}

			// Début modification
			foreach( array( 'domain', 'required' ) as $key ) {
				if( isset( $options[$key] ) ) {
					$labelAttributes = array_merge( $labelAttributes, array( $key => $options[$key] ) );
				}
			}
			// Fin modification
			return $this->label( $fieldName, $labelText, $labelAttributes );
		}

		/**
		 *
		 * @param string $field
		 * @param array $options
		 * @return array
		 */
		protected function _initInputField( $field, $options = array( ) ) {
			if( isset( $options['required'] ) && $options['required'] ) {
				$options = $this->addClass( $options, 'required' );
			}
			unset( $options['domain'] );
			unset( $options['required'] );
			return parent::_initInputField( $field, $options );
		}

		/**
		 * Surcharge de la méthode FormHelper::submit pour traduire automatiquement la variable $caption.
		 *
		 * @param string $caption
		 * @param array $options
		 * @return string
		 */
		public function submit( $caption = null, $options = array() ) {
			return parent::submit( __( $caption ), $options );
		}

		/**
		 *  Retourne un label et une valeur comme un champ de formulaire de type texte.
		 *
		 * @param string $label
		 * @param mixed $value
		 * @param boolean $translate
		 * @param string $inputClass
		 * @param boolean $nl2br
		 * @return string
		 */
		public function fieldValue( $label, $value, $translate = true, $inputClass = 'text', $nl2br = false ) {
			if( $translate != false ) {
				if( $translate === true ) {
					list( $modelName, $fieldName ) = model_field( $label );
					$domain = Inflector::underscore( $modelName );
				}
				else {
					$domain = $translate;
				}
				$label = __d( $domain, $label );
			}

			$label = h( $label );
			$value = h( $value );
			if( $nl2br ) {
				$value = nl2br( $value );
			}

			return '<div class="input '.$inputClass.'"><span class="label">'.$label.'</span><span class="input">'.$value.'</span></div>';
		}

		/**
		 * Retourne un textarea avec les bonnes classes pour un champ d'adresse.
		 *
		 * Utilisé uniquement dans app/View/Apre/add_edit_cg93.ctp
		 *
		 * @param string $fieldName
		 * @param array $options
		 * @return string
		 */
		public function address( $fieldName, $options = array() ) {
			$options['type'] = 'textarea';

			$options['rows'] = ( isset( $options['rows'] ) ? $options['rows'] : '3' );
			$options['class'] = 'input textarea address';
			$options['label'] = $this->label( $fieldName, null, $options );

			unset( $options['required'] );
			unset( $options['domain'] );

			return parent::input( $fieldName, $options );
		}

		/**
		 * Retourne un input select pour le domaine correspondant au nom de modèle.
		 *
		 * En cas de fieldset (type => options), il n'y a pas de traduction automatique.
		 *
		 * @param string $fieldName
		 * @param array $options
		 * @return string
		 */
		public function enum( $fieldName, $options = array() ) {
			$domain = strtolower( preg_replace( '/^([^\.]+)\..*$/', '\1', $fieldName ) );
			$defaultOptions = array(
				'domain' => $domain,
				'type' => 'select',
				'empty' => ''
			);
			return $this->input( $fieldName, Set::merge( $defaultOptions, $options ) );
		}

		/**
		 * Surcharge de la méthode FormHelper::hour de CakePHP 2.2.1 pour pouvoir
		 * utiliser le paramètre hourRange dans la méthode _generateOptions.
		 *
		 * @param type $fieldName
		 * @param type $format24Hours
		 * @param type $attributes
		 * @return type
		 */
		public function hour( $fieldName, $format24Hours = false, $attributes = array( ) ) {
			$attributes += array( 'empty' => true, 'value' => null );
			$attributes = $this->_dateTimeSelected( 'hour', $fieldName, $attributes );

			if( strlen( $attributes['value'] ) > 2 ) {
				if( $format24Hours ) {
					$attributes['value'] = date( 'H', strtotime( $attributes['value'] ) );
				}
				else {
					$attributes['value'] = date( 'g', strtotime( $attributes['value'] ) );
				}
			}
			elseif( $attributes['value'] === false ) {
				$attributes['value'] = null;
			}
			return $this->select(
				$fieldName.".hour",
				// Début modification
				$this->_generateOptions( $format24Hours ? 'hour24' : 'hour', $attributes ),
				// Fin modification
				$attributes
			);
		}

		/**
		 * Apparemment ça ne sert à rien comme ça.
		 *
		 * @param type $elements
		 * @param type $parents
		 * @param type $showParents
		 * @param type $attributes
		 * @return type
		 */
		/*protected function _selectOptions( $elements = array( ), $parents = array( ), $showParents = null, $attributes = array( ) ) {
			$newElements = array();
			foreach( $elements as $key => $value ) {
				$newElements[(string)$key] = $value;
			}
			return parent::_selectOptions( $newElements, $parents, $showParents, $attributes );
		}*/

		/* FIXME: La méthode month c'était peut-être pour la traduction de mois ? */
		/* FIXME: que faisait la méthode year ? */
		/* FIXME: day/month/year -> permet d'envoyer un formulaire de recherche
		en prg avec des champs date_from et date_to sans erreur */

		/**
		 * Surcharge de la fonction permettant de choisir une plage d'heures (cf. attribut hourRange) pour
		 * la fonction hour (hour et hour24).
		 *
		 * @param string $name
		 * @param array $options
		 * @return array
		 */
		protected function _generateOptions( $name, $options = array( ) ) {
			if( !empty( $this->options[$name] ) ) {
				return $this->options[$name];
			}

			if( in_array( $name, array( 'hour', 'hour24' ) ) ) {
				$data = array( );

				$min = Set::classicExtract( $options, 'hourRange.0' );
				$max = Set::classicExtract( $options, 'hourRange.1' );

				switch( $name ) {
					case 'hour':
						$min = ( is_null( $min ) ? 1 : $min );
						$max = ( is_null( $max ) ? 12 : $max );

						for( $i = $min; $i <= $max; $i++ ) {
							$data[sprintf( '%02d', $i )] = $i;
						}
						break;
					case 'hour24':
						$min = ( is_null( $min ) ? 0 : $min );
						$max = ( is_null( $max ) ? 23 : $max );

						for( $i = $min; $i <= $max; $i++ ) {
							$data[sprintf( '%02d', $i )] = $i;
						}
						break;
				}
				$this->_options[$name] = $data;
				return $this->_options[$name];
			}
			else {
				return parent::_generateOptions( $name, $options );
			}
		}

		/**
		 * Retourne un input radio unique.
		 *
		 * Utilisé uniquement dans app/View/Traitementspcgs66/ajaxpiece.ctp
		 *
		 * @param string $path
		 * @param mixed $value
		 * @param string $label
		 * @return string
		 */
		public function singleRadioElement( $path, $value, $label ) {
			$name = 'data['.implode( '][', explode( '.', $path ) ).']';
			$currentValue = Set::classicExtract( $this->request->data, $path );
			$checked = ( ( ( $value == $currentValue ) ) ? 'checked="checked"' : '' );
			return "<label><input type=\"radio\" name=\"{$name}\" value=\"{$value}\" {$checked} />{$label}</label>";
		}

		/**
		 * Retourne un div avec une liste non ordonnée d'erreurs de validation
		 * concernant des modèles qui ne se trouvent pas dans les données renvoyées
		 * par le formulaire et qui ne se trouvent pas dans la liste des modèles
		 * supplémentaires passée en paramètre.
		 *
		 * @param array $extra Les alias des modèles supplémentaires pour lesquels
		 *	cette méthode ne doit pas retourner les erreurs.
		 * @return string
		 */
		public function getExtraValidationErrorMessages( array $extra = array() ) {
			$return = null;

			if( !empty( $this->request->data ) ) {
				$errors = $this->validationErrors;
				$used = array_merge( array_keys( $this->request->data ), $extra );

				foreach( $used as $alias ) {
					unset( $errors[$alias] );
				}

				$errors = Hash::extract( Hash::filter( $errors ), '{s}.{s}.{n}' );

				if( !empty( $errors ) ) {
					$lis = array();
					foreach( $errors as $error ) {
						$lis[] = $this->Html->tag( 'li', $error );
					}
					$ul = $this->Html->tag( 'ul', implode( '', $lis ) );
					$return = $this->Html->tag( 'div', $ul, array( 'class' => 'error_message' ) );
				}
			}

			return $return;
		}
		
		/**
		 * Génère un fieldset de type multiple checkbox
		 * 
		 * @param string $path
		 * @param array $options
		 * @param string $class
		 * @return string
		 */
		public function multipleCheckbox( $path, array $options = array(), $class = '' ) {
			$name = model_field($path);
			return $this->input($path, array(
				'label' => __m($path),
				'type' => 'select',
				'multiple' => 'checkbox',
				'options' => Hash::get($options, "{$name[0]}.{$name[1]}"),
				'class' => $class
			));
		}
		
		/**
		 * Génère un fieldset de type multiple checkbox
		 * 
		 * @param string $path
		 * @param array $options
		 * @param string $class
		 * @return string
		 */
		public function multipleCheckboxToutCocher( $path, array $options = array(), $class = '' ) {
			$name = model_field($path);
			
			$uniqueClass = 'toutCochable'.$name[0].Inflector::camelize($name[1]);
			$selecteur = 'div.'.$uniqueClass.' input';
			$buttons = '<div>'
				.$this->button('Tout cocher', array('type' => 'button', 'onclick' => "return toutCocher('$selecteur', true);"))
				.$this->button('Tout décocher', array('type' => 'button', 'onclick' => "return toutDecocher('$selecteur', true);"))
			.'</div>';
			
			return $this->input($path, array(
				'label' => __m($path),
				'type' => 'select',
				'before' => $buttons,
				'multiple' => 'checkbox',
				'options' => Hash::get($options, "{$name[0]}.{$name[1]}"),
				'class' => trim($class.' '.$uniqueClass)
			));
		}
		
		/**
		 * Renvoi une div d'afficahge d'erreur
		 * 
		 * @param mixed $errors Liste des erreurs
		 * @return string
		 */
		public function errorDiv( $errors ) {
			$result = "<div class='error-message'>";
			$result .= count((array)$errors) > 1 ? '<ul><li>'.implode('</li><li>', $errors).'</li></ul>' : implode('', (array)$errors);
			
			return $result.'</div>';
		}
	}
?>