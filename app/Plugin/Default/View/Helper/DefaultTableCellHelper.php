<?php
	/**
	 * Code source de la classe DefaultTableCellHelper.
	 *
	 * PHP 5.4
	 *
	 * @package Default
	 * @subpackage View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( 'DefaultUtility', 'Default.Utility' );

	/**
	 * La classe DefaultTableCellHelper génère des cellules de corps de tableau
	 * qui sont des array avec, en clé 0 le contenu de la cellule, et en clé 1 les
	 * attributs de la cellule.
	 *
	 * Ces arrays sont utilisables par la méthode HtmlHelper::tableCells().
	 *
	 * @package Default
	 * @subpackage View.Helper
	 */
	class DefaultTableCellHelper extends AppHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default.DefaultData',
			'Default.DefaultForm',
			'Default.DefaultHtml',
		);

		/**
		 * Contexte de données à utiliser.
		 *
		 * @see DefaultTableCellHelper::set()
		 *
		 * @var array
		 */
		protected $_data = array();

		/**
		 * Donne le contexte de données à utiliser.
		 *
		 * @param array $data
		 */
		public function set( array $data ) {
			$this->_data = $data;
		}

		/**
		 * Retourne la type de données d'un chemin.
		 *
		 * @param string $path
		 * @param array $attributes
		 * @return string
		 */
		protected function _type( $path, array $attributes ) {
			if( isset( $attributes['type'] ) ) {
				return $attributes['type'];
			}

			return $this->DefaultData->type( $path );
		}

		/**
		 * Retourne une cellule de données formattées.
		 *
		 * @param string $path
		 * @param array $htmlAttributes
		 * @return string
		 */
		public function data( $path, array $htmlAttributes = array() ) {
			$type = $this->_type( $path, $htmlAttributes );
			$value = Hash::get( $htmlAttributes, 'value' );
			unset( $htmlAttributes['type'], $htmlAttributes['value'] );

			if( null !== $value ) {
				// TODO: une méthode (evaluate ?)
				if( preg_match_all( '/#([^#]+)#/', $value, $matches ) ) {
					foreach( $matches[1] as $match ) {
						$tokens = explode( ',', $match );

						$tmpPath = $tokens[0];
						$tmpFunctions = array_slice( $tokens, 1 );
						$tmpValue = Hash::get( $this->_data, $tmpPath );

						if( isset( $htmlAttributes['options'] ) && isset( $htmlAttributes['options'][$tmpValue] ) ) {
							$tmpValue = $htmlAttributes['options'][$tmpValue];
						}

						foreach( $tmpFunctions as $tmpFunction ) {
							$tmpValue = call_user_func( $tmpFunction, $tmpValue );
						}

						$value = str_replace( "#{$match}#", $tmpValue, $value );
					}
				}

				$attributes = array();
			}
			else {
				$value = Hash::get( $this->_data, $path );

				/*
				 * On vérifie si on a un code de traduction dans la valeur
				 * Si oui, on affiche la traduction avec le restant de la
				 * ligne de résultat.
				 */
				$codeTraduction = array ();
				if (preg_match('#ENUM::[a-zA-Z0-9]+::[a-zA-Z0-9]+__[a-zA-Z0-9]+#', $value, $codeTraduction)) {
					$resteDuResultat = trim (str_replace($codeTraduction[0], '', $value));
					$codeTraduction = explode('__', $codeTraduction[0]);
					$value = sprintf( __d($codeTraduction[1], $codeTraduction[0]), $resteDuResultat);
				}

				// On prend les attributs avant la valeur, sinon on aura des bugs, par exemple avec boolean
				$attributes = $this->DefaultData->attributes( $value, $type );
				$value = $this->DefaultData->format( $value, $type, Hash::get( $htmlAttributes, 'format' ) );
			}

			if( isset( $htmlAttributes['options'] ) ) {
				$value = $this->DefaultData->translateOptions( $value, $htmlAttributes );
			}

			// Si la valeur est une liste de valeurs
			if( is_array( $value ) ) {
				if( !empty( $value ) ) {
					$value = '<ul><li>'.implode( '</li><li>', $value ).'</li></ul>';
				}
				else {
					$value = null;
				}
			}

			unset( $htmlAttributes['label'], $htmlAttributes['options'], $htmlAttributes['format'], $htmlAttributes['domain'] );

			$htmlAttributes['class'] = DefaultUtility::evaluate( $this->_data, Hash::get( $htmlAttributes, 'class' ) );

			return array(
				$value,
				Hash::merge(
					$attributes,
					$this->addClass( $htmlAttributes, (string)Hash::get( $attributes, 'class' ) )
				)
			);
		}


		/**
		 * Retourne une cellule contenant un lien.
		 *
		 * @see DefaultUtility::linkParams()
		 *
		 * @param string $path
		 * @param array $htmlAttributes
		 * @return string
		 */
		public function action( $path, array $htmlAttributes = array() ) {
			$htmlAttributes = Hash::merge(
				array(
					'domain' => Inflector::underscore( $this->request->params['controller'] ),
					'title' => true
				),
				$htmlAttributes
			);

			list( $text, $url, $htmlAttributes ) = DefaultUtility::linkParams(
				$path,
				$htmlAttributes,
				$this->_data
			);

			$for = null;
			if( isset( $htmlAttributes['for'] ) ) {
				$for = DefaultUtility::evaluate( $this->_data, $htmlAttributes['for'] );
				unset( $htmlAttributes['for'] );
			}

			$confirmMessage = false;
			if( isset( $htmlAttributes['confirm'] ) && $htmlAttributes['confirm'] !== false ) {
				$confirmMessage = $htmlAttributes['confirm'];
			}
			unset( $htmlAttributes['confirm'] );

			return array(
				$this->DefaultHtml->link(
					$text,
					$url,
					$htmlAttributes,
					$confirmMessage
				),
				array( 'class' => 'action', 'for' => $for )
			);
		}

		/**
		 * Retourne une cellule contenant un champ de formulaire.
		 *
		 * @param string $path
		 * @param array $attributes
		 * @return string
		 */
		public function input( $path, array $attributes = array() ) {
			$path = str_replace( '][', '.', preg_replace( '/^data\[(.*)\]$/', '\1', DefaultUtility::evaluate( $this->_data, $path ) ) );
			$type = $this->_type( $path, $attributes );

			return array(
				$this->DefaultForm->input( $path, DefaultUtility::evaluate( $this->_data, $attributes ) ),
				array( 'class' => "input {$type}" )
			);
		}

		/**
		 * Retourne automatiquement une cellule de corps de table: action, input,
		 * data - suivant le chemin:
		 * - Action: /Users/view/#User.id#
		 * - Input: data[User][#User.id#][username]
		 * - Data: User.id
		 *
		 * @param string $path
		 * @param array $attributes
		 * @return string
		 */
		public function auto( $path, array $attributes = array() ) {
			if( isset( $attributes['disabled'] ) && is_string( $attributes['disabled'] ) ) {
				$attributes['disabled'] = eval( 'return '.DefaultUtility::evaluate( $this->_data, $attributes['disabled'] ).';' );
			}

			if( strpos( $path, '/' ) === 0 ) {
				return $this->action( $path, (array)$attributes );
			}
			else if( strpos( $path, 'data[' ) === 0 ) {
				return $this->input( $path, (array)$attributes );
			}
			else {
				return $this->data( $path, (array)$attributes );
			}
		}
	}
?>