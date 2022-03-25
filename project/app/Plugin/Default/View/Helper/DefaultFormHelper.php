<?php
	/**
	 * Code source de la classe DefaultFormHelper.
	 *
	 * PHP 5.4
	 *
	 * @package Default
	 * @subpackage View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'FormHelper', 'View/Helper' );

	/**
	 * La classe DefaultFormHelper étend la classe FormHelper de CakePHP
	 * dans le cadre de son utilisation dans le plugin Default.
	 *
	 * @package Default
	 * @subpackage View.Helper
	 */
	class DefaultFormHelper extends FormHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default.DefaultData',
			'Html',
		);

		/**
		 * "Surcharge" des types de champs utilisés pour les nombres entiers et
		 * à virgule flottante afin que les champs "text" soient utilisés à la
		 * place des champs "number".
		 *
		 * @var array
		 */
		public $map = array(
			'decimal' => 'text',
			'float' => 'text',
			'integer' => 'text',
			'biginteger' => 'text'
		);

		/**
		 * Permet de savoir quels sont les champs à ne pas prendre en compte dans
		 * l'affichage du formulaire selon la configuration
		 */
		public $skip = array();

		/**
		 * Surcharge du constructeur avec possibilité de choisir les paramètres
		 * par défaut.
		 *
		 * @param View $View
		 * @param array $settings
		 */
		public function __construct( View $View, $settings = array( ) ) {
			parent::__construct( $View, $settings );

			$this->_readSkipConfig();
		}

		/**
		 * Lecture des champs à ne pas afficher ("skip") à partir de la
		 * configuration.
		 *
		 * Par exemple, pour l'URL "/orientsstructs/cohorte_nouvelles", la valeur
		 * de "ConfigurableQueryOrientsstructs.cohorte_nouvelles.skip" sera lue.
		 *
		 */
		protected function _readSkipConfig() {
			$action = $this->request->params['action'];
			$actionName = '';
			if(strpos($action, 'search') !== false || strpos($action, 'cohorte') !== false) {
				$actionName = 'Search';
			}
			if(!empty($actionName)) {
				$configurePath = 'ConfigurableQuery.'.Inflector::camelize($this->request->params['controller']).'.'.$this->request->params['action'];
				$skip = (array)Configure::read( "{$configurePath}.filters.skip" );

				if( !empty( $skip ) ) {
					foreach( $skip as $key => $value ) {
						$skip[$key] = "{$actionName}.{$value}";
					}

					$this->skip = array_merge( $this->skip, $skip );
				}
			}
		}

		/**
		 * Permet de savoir si un champ doit être affiché ou non, suivant les
		 * champs présents dans l'attribut 'skip' des paramètres.
		 *
		 *
		 * @param string $path
		 * @return boolean
		 */
		protected function _isSkipped( $path ) {
			if( in_array( $path, $this->skip ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Retourne une liste de boutons de formulaire, dans le div submit, à
		 * la mode CakePHP.
		 *
		 * @param array $buttons
		 * @return string
		 */
		public function buttons( array $buttons ) {
			$return = null;

			if( !empty( $buttons ) ) {
				$submit = '';

				foreach( Hash::normalize( $buttons ) as  $buttonName => $buttonParams ) {
					$buttonLabel = ( isset( $buttonParams['label'] ) && !empty( $buttonParams['label'] ) ? $buttonParams['label'] : __( $buttonName ) );
					$buttonType = ( isset( $buttonParams['type'] ) && !empty( $buttonParams['type'] ) ? $buttonParams['type'] : 'submit' );
					$submit .= $this->submit( $buttonLabel, array( 'div' => false, 'name' => $buttonName, 'type' => $buttonType ) );
				}

				$return = $this->Html->tag( 'div', $submit, array( 'class' => 'submit' ) );
			}

			return $return;
		}

		/**
		 * Retourne un élément de formulaire contenant la valeur sous forme de
		 * texte à partir des données dans $this->request->data.
		 *
		 * Les clés suivantes sont prises en compte dans les options:
		 *	- label(string): spécifie le label si on ne veut pas de la traduction automatique
		 *	- options(array): permet de traduire la valeur
		 *	- nl2br(boolean): applique la fonction nl2br sur la valeur
		 *	- hidden(boolean): ajoute un champ caché en plus de l'affichage
		 *	- type(string): spécifie la classe du div
		 *
		 * @param string $fieldName
		 * @param array $params
		 * @return string
		 */
		public function fieldValue( $fieldName, array $params = array() ) {
			// Label
			if( isset( $params['label'] ) ) {
				$label = $params['label'];
			}
			else {
				$label = $this->label( $fieldName, null, $params );
				$label = preg_replace( '/^.*>([^<]*)<.*$/', '\1', $label );
			}
			$label = $this->Html->tag( 'span', $label, array( 'class' => 'label' ) );

			// Valeur
			$value = Hash::get( $this->request->data, $fieldName );

			if( isset( $params['options'][$value] ) ) {
				$value = $params['options'][$value];
			}

			if( Hash::get( $params, 'nl2br' ) ) {
				$value = nl2br( $value );
			}

			if( isset( $params['type'] ) && $params['type'] !== 'text' ) {
				$value = $this->DefaultData->format( $value, $params['type'], Hash::get( $params, 'format' ) );
			}

			// Permet d'avoir la fin de tag
			if( $value === null ) {
				$value = ' ';
			}

			$value = $this->Html->tag( 'span', $value, array( 'class' => 'input' ) );

			// Ajout d'un champ caché ?
			$hidden = Hash::get( $params, 'hidden' );
			if( $hidden ) {
				$hidden = $this->input( $fieldName, array( 'type' => 'hidden' ) );
			}
			else {
				$hidden = '';
			}

			// Options
			$params = $this->addClass( $params, 'input value' );
			if( isset( $params['type'] ) ) {
				$params = $this->addClass( $params, $params['type'] );
			}
			unset( $params['options'], $params['label'], $params['hidden'], $params['nl2br'], $params['type'], $params['format'] );

			return $this->Html->tag( 'div', $hidden.$label.$value, $params );
		}

		/**
		 * Retourn un champ de type input (@see FormHelper) ou une valeur
		 * (@see fieldValue) si la clé 'view' est à true dans les options.
		 *
		 * @param string $fieldName
		 * @param array $options
		 * @return string
		 */
		public function input( $fieldName, $options = array( ) ) {
			if( !$this->_isSkipped( $fieldName ) ) {
				if( isset( $options['view'] ) && $options['view'] ) {
					unset( $options['view'] );
					return $this->fieldValue( $fieldName, $options );
				}

				// Pas d'option pour les champs cachés, sinon ce sera transformé en attribut
				if( Hash::get( $options, 'type' ) == 'hidden' ) {
					unset( $options['options'] );
				}

				// Legend par défaut est la traduction du fieldName
				if (Hash::get($options, "type") === 'radio' && Hash::get($options, "legend") === null) {
					$options['legend'] = __m($fieldName);
				}

				// Prise en charge de l'option fieldset
				if( Hash::get( $options, "fieldset") ) {
					$legend = $options['label'];
					$options['label'] = '';
					return $this->Html->tag(
						'fieldset',
						$this->Html->tag( 'legend', $legend ).
						parent::input( $fieldName, $options )
					);
				}

				unset( $options['domain'] );
				return parent::input( $fieldName, $options );
			}
			return null;
		}

		/**
		 * Permet d'ajouter l'astérisque dans une abbr au libellé, lorsqu'un champ
		 * est obligatoire.
		 *
		 * @param string $label
		 * @param array $options
		 * @return string
		 */
		protected function _required( $label, array $options = array() ) {
			if( isset( $options['required'] ) && $options['required'] ) {
				$abbr = $this->Html->tag( 'abbr', '*', array( 'class' => 'required', 'title' => __( 'Validate::notEmpty' ) ) );
				$label = h( $label )." {$abbr}";
			}

			return $label;
		}

		/**
		 * Ajoute une étoile lorsqu'un champ est obligatoire (clé required à true
		 * dans les options), en plus de la fonctionnalité de base de
		 * FormHelper::_inputLabel().
		 *
		 * @see DefaultFormHelper::_required()
		 *
		 * @param string $fieldName
		 * @param string $label
		 * @param array $options Options for the label element.
		 * @return string Generated label element
		 */
		protected function _inputLabel( $fieldName, $label, $options ) {
			if( !isset( $options['required'] ) ) {
				list( $modelKey, $fieldKey ) = model_field( $fieldName );
				$options['required'] = ( $this->_introspectModel( $modelKey, 'validates', $fieldKey ) !== null );
			}

			$label = $this->_required( $label, $options );
			unset( $options['required'] );
			return parent::_inputLabel( $fieldName, $label, $options );
		}

		/**
		 * Ajoute une étoile lorsqu'un champ est obligatoire (clé required à true
		 * dans les options), en plus de la fonctionnalité de base de
		 * FormHelper::label().
		 *
		 * @see DefaultFormHelper::_required()
		 *
		 * @param string $fieldName This should be "Modelname.fieldname"
		 * @param string $text Text that will appear in the label field.  If
		 *   $text is left undefined the text will be inflected from the
		 *   fieldName.
		 * @param array|string $options An array of HTML attributes, or a string, to be used as a class name.
		 * @return string The formatted LABEL element
		 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#FormHelper::label
		 */
		public function label( $fieldName = null, $text = null, $options = array( ) ) {
			$text = $this->_required( $text, $options );
			unset( $options['required'] );
			return parent::label( $fieldName, $text, $options );
		}

		/**
		 * Surcharge de la méthode FormHelper::create pour ajouter l'attribut
		 * novalidate à true dans les options si celui-ci n'est pas spécifié.
		 *
		 * @param string|array $model
		 * @param array $options
		 * @return string
		 */
		public function create( $model = null, $options = array() ) {
			if( is_array( $model ) && empty( $options ) ) {
				$options = $model;
				$model = null;
			}

			$options += array( 'novalidate' => true );

			return parent::create( $model, $options );
		}
	}
?>