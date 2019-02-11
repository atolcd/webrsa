<?php
	/**
	 * Fichier source de la classe Type2Helper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ModelHelper', 'View/Helper' );

	/**
	 * TODO: dane une classe, un meilleur nom ?
	 *
	 * @param array $data
	 * @param array $params
	 * @return string
	 */
	function dataUrl( $data, $params ) {
		if( !is_string( $params['url'] ) ) {
			$params['url'] = Router::url( $params['url'] );
		}
		return dataTranslate( $data, $params['url'] );
	}

	/**
	 * La classe Type2Helper ...
	 *
	 * TODO: disabled -> conditions, default false pour les url
	 *
	 * @package app.View.Helper
	 */
	class Type2Helper extends ModelHelper
	{
		public $helpers = array( 'Xhtml', 'Html', 'Locale', 'Xform' );

		/**
		*
		*/

		public function prepare( $mode, $path, $params = array() ) {
			$translate = array(
				'text' => 'textarea',
				'string' => 'text',
				'float' => 'text',
				'integer' => 'text',
				'boolean' => 'checkbox',
			);

			/// Prepare
			list( $modelName, $fieldName ) = model_field( $path );
			if( isset( $params['model'] ) ) {
				$modelName = $params['model'];
			}
			if( isset( $params['field'] ) ) {
				$fieldName = $params['field'];
			}

			$typeInfos = $this->_typeInfos( "{$modelName}.{$fieldName}" );

			if( $mode == 'input' && isset( $translate[$typeInfos['type']] ) ) {
				$typeInfos['type'] = $translate[$typeInfos['type']];
			}

			$typeInfos['domain'] = Inflector::singularize( Inflector::tableize( $modelName ) );
			$params = Set::merge( $typeInfos, $params );

			if( isset( $params['type'] ) && in_array( $params['type'], array( 'date', 'datetime', 'timestamp' ) ) ) {
				if( !isset( $params['empty'] ) ) {
					$params['empty'] = true;
				}
				// dateFormat
				if( !isset( $params['dateFormat'] ) ) {
					$params['dateFormat'] = __( 'Locale->dateFormat' );
				}
			}
			else if( isset( $params['options'] ) && !( isset( $params['type'] ) && $params['type'] == 'hidden' ) ) {
				if( !isset( $params['type'] ) || $params['type'] != 'radio' ) {
					$params['type'] = 'select';
				}
				if( !isset( $params['empty'] ) ) {
					$params['empty'] = true;
				}
			}
			return $params;
		}

		/**
		*
		*/

		protected function _formatBooleanValue( $value, $params ) {
			$params = $this->addClass( $params, 'number' );
			if( is_null( $value ) ) {
				$params = $this->addClass( $params, 'null' );
			}
			else {
				if( $value ) {
					$params = $this->addClass( $params, 'true' );
					$value = __( 'Yes' );
				}
				else {
					$params = $this->addClass( $params, 'false' );
					$value = __( 'No' );
				}
			}
			if( !empty( $params['tag'] ) ) {
				$value = $this->Xhtml->tag( 'span', $value );
			}
			return array( $value, $params );
		}

		/**
		*
		*/

		protected function _formatNumericValue( $value, $params ) {
			$params = $this->addClass( $params, 'number '.( ( $value >= 0 ) ? 'positive' : 'negative' ) );
			if( is_numeric( $value ) ) {
					$value = $this->Locale->number(
						$value,
						(
							( $params['type'] == 'float' )
							? $params['precision']
							: 0
						)
					);
			}
			return array( $value, $params );
		}

		/**
		*
		*/

		protected function _formatChronicValue( $value, $params ) {
			$value = $this->Locale->date( "Locale->{$params['type']}", $value );
			return array( $value, $params );
		}

		/**
		* CakePHP SQL types (standard)
		*
		* 'User.id', null
		* 	1 000
		* 'User.id', array( 'type' => 'string', 'tag' => 'span' )
		* 	<span class="string">1000</span>
		* 'User.username', array( 'url' => true, 'tag' => 'auto' )
		* 	<a href="/users/view/1000" class="string">cbuffin</a>
		* 'User.username', array( 'url' => array( 'controller' => 'users', 'action' => 'view', '#User.id#' ) )
		* 	<span class="string"><a href="/users/view/1000">1000</a></span>
		* 'User.site', array( 'type' => 'url', 'tag' => 'span' )
		* 	<span class="url"><a href="http://www.site.com/">www.site.com</a></span>
		* TODO ailleurs: 'User.email', array( 'type' => 'email', 'encode' => 'hex' )
		* 	<a href="mailto:&#99;&#104;&#114;&#105;&#115;&#116;&#105;&#97;&#110;&#46;&#98;&#117;&#102;&#102;&#105;&#110;&#64;&#103;&#109;&#97;&#105;&#108;&#46;&#99;&#111;&#109;">&#99;&#104;&#114;&#105;&#115;&#116;&#105;&#97;&#110;&#46;&#98;&#117;&#102;&#102;&#105;&#110;&#64;&#103;&#109;&#97;&#105;&#108;&#46;&#99;&#111;&#109;</a>
		*/

		protected function _formatValue( $value, $params, $path ) {
			if( $params['type'] == 'boolean' ) {
				return $this->_formatBooleanValue( $value, $params );
			}
			else if( in_array( $params['type'], array( 'float', 'integer','decimal'  ) ) ) {
				return $this->_formatNumericValue( $value, $params );
			}
			else if( in_array( $params['type'], array( 'date', 'time', 'timestamp', 'datetime' ) ) ) {
				return $this->_formatChronicValue( $value, $params );
			}
			else if( in_array( $params['type'], array( 'string', 'text' ) ) ) {
				return array( $value, $params );
			}
			else {
				trigger_error( "Unrecognized type '{$params['type']}' for path {$path}", E_USER_WARNING );
				return null;
			}
		}

		/**
		*
		*/

		public function evaluate( $data, $condition ) {
			$code_str = 'return '.dataTranslate( $data, $condition ).';';
			return eval( $code_str );
		}

		/**
		* TODO: protected
		*/

		public function translateDisabled( $data, $params ) {
			if( !isset( $params['disabled'] ) ) {
				return false;
			}
			return $this->evaluate( $data, $params['disabled'] );
		}

		/**
		*	- params['tag'], default true, (true, false, string)
		*	- params['type'], default null, (string)
		*	- params['url'], default false, (true, false, string, array)
		*	- params['options'], default null, (array)
		*	- params['class'], default null, (string)
		*	- params['precision'], default 2, (int) pour les float
		*	- params['format'], default null, (string) <-- TODO pour dates
		*	- params['id'], default null, (string) <-- TODO 'id' => 'Foo#Post.id#'
		*	- params['enum'], default null, (null|abbr) <-- TODO/TODO 'id' => <abbr title='mititle'>MiValue</abbr>
		*/

		public function format( $data, $path, $params = array() ) {
			list( $modelName, $fieldName ) = model_field( $path );

			$defaultParams = array(
				'tag' => false,
				'type' => null,
				'url' => false,
				'options' => null,
				'class' => null,
				'precision' => 2
			);
			$params = Set::merge( $defaultParams, (array) $params );
			$params['type'] = ( !empty( $params['type'] ) ? $params['type'] : $this->type( $modelName, $fieldName ) );
			$params = $this->addClass( $params, $params['type'] );

			if( Set::check( $params, 'value' ) ) {
				$value = Set::classicExtract( $params, 'value' );
				$value = dataTranslate( $data, $value );
			}
			else {
				$value = Set::classicExtract( $data, $path );
			}

			// If field is of "type enum", translate it -> TODO: only if text / string ?
			if( Set::check( $params, "options.{$value}" ) ) {
				$params = Hash::insert( array(), "options.{$modelName}.{$fieldName}", $params );
			}

			if( Set::check( $params, "options.{$modelName}.{$fieldName}" ) ) {
				$domain = Inflector::singularize( Inflector::tableize( $modelName ) );
				$paramValues = Set::classicExtract( $params, "options.{$modelName}.{$fieldName}" );
				if( isset( $paramValues[$value] ) ) {
					$value = $paramValues[$value];
				}
				$value = __d( $domain, $value );
			}

			list( $value, $params ) = $this->_formatValue( $value, $params, $path );

			$disabled = $this->translateDisabled( $data, $params );

			if( isset( $params['url'] ) && $params['url'] !== false ) {
				if( $params['url'] === true ) {
					if( $disabled ) {
						$params = $this->addClass( $params, 'disabled' );
						$value = $this->Xhtml->tag( 'span', $value, array( 'class' => 'link view' ) );
					}
					else {
						$primaryKey = $this->primaryKey( $modelName );
						$params['url'] = Router::url(
							array(
								'controller' => Inflector::tableize( $modelName ),
								'action' => 'view',
								"#{$modelName}.{$primaryKey}#"
							)
						);
						$value = $this->Xhtml->link( $value, dataUrl( $data, $params ) );
					}
				}
				else {
					if( $disabled ) {
						$params = $this->addClass( $params, 'disabled' );
						$value = $this->Xhtml->tag( 'span', $value, array( 'class' => "link {$params['url']['action']}" ) );
					}
					else {
						$value = $this->Xhtml->link( $value, dataUrl( $data, $params ) );
					}
				}
			}

			if( is_string( $params['tag'] ) ) {
				if( is_null( $value ) || trim( $value ) == '' ) {
					$params = $this->addClass( $params, 'empty' );
					$value = '&nbsp;';
				}

				if( in_array( $params['type'], array( 'float', 'integer' ) ) ) {
					$value = str_replace( ' ', '&nbsp;', $value );
				}

				$value = $this->Xhtml->tag(
					$params['tag'],
					$value,
					$params['class']
				);
			}

			return $value;
		}

		/**
		* @param string $path ie. User.id
		* @param array $params
		* @return string
		* Valid keys for params:
		*	- options ie. array( 'User' => array( 'status' => array( 1 => 'Enabled', 0 => 'Disabled' ) ) )
		*/

		public function input( $path, $params = array() ) {
			$params = $this->prepare( 'input', $path, $params );
			list( $modelName, $fieldName ) = model_field( $path );

			if( isset( $params['type'] ) ) {
				if( $params['type'] == 'text' && isset( $params['length'] ) ) {
					$params['maxlength'] = $params['length'];
				}
			}

			/// TODO
			unset(
				$params['null'],
				$params['country'],
				$params['length'],
				$params['virtual'],
				$params['key'],
				$params['suffix'],
				$params['currency'],
				$params['model'],
				$params['field'],
				$params['default']
			);

			list( $model, $field ) = model_field( $path );

			if( isset( $params['type'] ) && $params['type'] == 'radio' ) {
				unset( $params['empty'] );
			}

			return $this->Xform->input( $path, $params );
		}
	}
?>