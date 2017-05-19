<?php
	/**
	 * Fichier source de la classe TypeHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( 'ConnectionManager', 'Model' );

	/**
	 * La classe TypeHelper ...
	 *
	 * @package app.View.Helper
	 */
	class TypeHelper extends AppHelper
	{
		public $helpers = array( 'Xhtml', 'Html', 'Locale', 'Xform' );

		/**
		* Cache for schemas
		*/

		protected $_columnTypes = array();

		/**
		* Cache for type infos
		*/

		protected $_typeInfos = array();

		/**
		* Cache for table names
		*/

		protected $_sources = array();

		/**
		* @param string $path ie. User.username, User.0.id
		* @return string ie. string, integer
		*/

		protected function _fieldType( $path ) {
			list( $model, $field ) = model_field( $path );

			if( empty( $this->_columnTypes[$model] ) ) {
				$modelClass = ClassRegistry::init( Inflector::classify( $model ) );
				$this->_columnTypes[$model] = $modelClass->getColumnTypes( true );
			}

			if( !Set::check( $this->_columnTypes[$model], $field ) ) {
				trigger_error( "Could not find column {$field} in model {$model}", E_USER_WARNING );
				return null;
			}

			return Set::classicExtract( $this->_columnTypes[$model], $field );
		}

		/**
		* @param string $path ie. User.username, User.0.id
		* @return string ie. string, integer
		*/

		protected function _typeInfos( $path ) {
			list( $modelName, $fieldName ) = model_field( $path );

			if( empty( $this->_typeInfos["{$modelName}.{$fieldName}"] ) ) {
				$defaultTypeInfos = array(
					'type' => 'text',
					'null' => true,
					'default' => '',
					'length' => null
				);

				try {
					$model = ClassRegistry::init( $modelName );

					if( false === isset( $this->_sources[$model->useDbConfig] ) ) {
						$Db = ConnectionManager::getDataSource( $model->useDbConfig );
						if( true === method_exists( $Db, 'listSources' ) ) {
							$this->_sources[$model->useDbConfig] = array_map( 'strtolower', (array)$Db->listSources() );
						}
						else {
							$this->_sources[$model->useDbConfig] = array();
						}
					}

					if( true === in_array( strtolower( $model->tablePrefix . $model->useTable ), $this->_sources[$model->useDbConfig] ) ) {
						if( false === $model->Behaviors->attached( 'Typeable' ) ) {
							$model->Behaviors->attach( 'Typeable' );
						}
						$typeInfos = $model->getTypeInfos( $fieldName );
					}
					else {
						$typeInfos = $defaultTypeInfos;
					}

				} catch( Exception $e ) {
					$typeInfos = $defaultTypeInfos;
					$this->log( sprintf( '%s:%d %s', $e->getFile(), $e->getCode(), $e->getMessage() ), LOG_DEBUG );
				}

				$this->_typeInfos = Hash::insert( $this->_typeInfos, "{$modelName}.{$fieldName}", $typeInfos );
			}

			if( !Set::check( $this->_typeInfos, "{$modelName}.{$fieldName}" ) ) {
				trigger_error( "Could not find column {$fieldName} in model {$modelName}", E_USER_WARNING );
				return null;
			}

			return Set::classicExtract( $this->_typeInfos, "{$modelName}.{$fieldName}" );
		}

		/**
		*
		*/

		public function prepare( $mode, $path, $params = array() ) {
			$translate = array(
				'input' => array(
					'text' => 'textarea',
					'string' => 'text',
					'phone' => 'text',
					'amount' => 'text',
					'integer' => 'text',
					'boolean' => 'checkbox',
					'email' => 'text',
				),
				'output' => array(
					'amount' => 'money',
				),
			);

			/// Prepare
			list( $modelName, $fieldName ) = model_field( $path );
			if( Set::check( $params, 'model' ) ) {
				$modelName = Set::classicExtract( $params, 'model' );
			}
			if( Set::check( $params, 'field' ) ) {
				$fieldName = Set::classicExtract( $params, 'field' );
			}

			$typeInfos = $this->_typeInfos( "{$modelName}.{$fieldName}" );
			if( preg_match( '/^(enum)::(.*)$/', $typeInfos['type'], $matches ) ) {
				$newOptions = array();
				$options = Set::extract( $typeInfos , 'options');
				foreach( $options as $key => $value ) {
					$newOptions[$value] = __( ( strtoupper( $matches['1'] ).'::'.strtoupper( $matches['2'] ).'::'.$value ) );
				}
				$typeInfos['options'] = $newOptions;
			}

			if( Set::check( $translate, "{$mode}.{$typeInfos['type']}" ) ) {
				$typeInfos['type'] = Set::classicExtract( $translate, "{$mode}.{$typeInfos['type']}" );
			}

			$typeInfos['domain'] = Inflector::singularize( Inflector::tableize( $modelName ) );
			$params = Set::merge( $typeInfos, $params );

			/// Input
			if( $mode == 'input' ) {
				if( isset( $params['type'] ) && preg_match( '/^enum::/', $params['type'] ) ) {
					$params['type'] = 'select';
				}
				if( !Set::check( $params, 'empty' ) ) {
					$params['empty'] = true;
				}
				if( Set::check( $params, 'options' ) && isset( $params['type'] ) && in_array( $params['type'], array( 'textarea', 'input' ) ) ) {
					$params['type'] = 'select';
				}
			}

			return $params;
		}

		/**
		*
		*/

		public function format( $data, $path, $params = array() ) {
			list( $modelName, $fieldName ) = model_field( $path );
			$params = $this->prepare( 'output', $path, $params );

			$classes = array();
			$value = Set::classicExtract( $data, $path );

			if( Set::classicExtract( $params, 'valueclass' ) ) {
				$classes = Set::merge( $classes, array( $fieldName, $value ) );
			}

			// If field is of "type enum", translate it
			if( Set::check( $params, "options.{$modelName}.{$fieldName}" ) ) {
				$domain = Inflector::singularize( Inflector::tableize( $modelName ) );
				$paramValues = Set::classicExtract( $params, "options.{$modelName}.{$fieldName}" );
				if( isset( $paramValues[$value] ) ) {
					$value = $paramValues[$value];
				}
				$value = __d( $domain, $value );
			}
			/// FIXME
			else if( Set::check( $params, "options" ) && Set::check( $params, "options.{$value}" ) ) {
				$domain = Inflector::singularize( Inflector::tableize( $modelName ) );
				$value = Set::enum( $value, Set::classicExtract( $params, "options" ) );
				$value = __d( $domain, $value );
			}
			else {
				// Format entry and get classes
				$classes[] = $params['type'];
				switch( $params['type'] ) {
					case 'email':
						if( !empty( $params['tag'] ) && !empty( $value ) ) {
							$value = $this->Xhtml->link( $value, "mailto:{$value}" );
						}
						break;
					// TODO: l10n + spécialisation des types
					case 'phone':
						$value = implode( ' ', str_split( $value, 2 ) );
						break;
					case 'money':
						$classes = Set::merge( $classes, array( 'number', ( ( $value >= 0 ) ? 'positive' : 'negative' ) ) );
						$value = $this->Locale->money( $value, 2 );
						break;
					/// SQL
					case 'boolean':
						switch( $value ) {
							case null:
								$value = ' ';
								$classes = Set::merge( $classes, array( 'number', 'null' ) );
								break;
							default:
								$classes = Set::merge( $classes, array( 'number', ( $value ? 'true' : 'false' ) ) );
								$value = ( $value ? __( 'Yes', true ) : __( 'No' ) );
						}
						break;
					case 'float':
						$classes = Set::merge( $classes, array( 'number', ( ( $value >= 0 ) ? 'positive' : 'negative' ) ) );
						$value = $this->Locale->number( $value, 2 );
						break;
					case 'integer':
						$classes = Set::merge( $classes, array( 'number', ( ( $value >= 0 ) ? 'positive' : 'negative' ) ) );
						$value = $this->Locale->number( $value );
						break;
					case 'date':
					case 'time':
					case 'timestamp':
					case 'datetime':
						$value = $this->Locale->date( "Locale->{$params['type']}", $value );
						break;
					case 'string':
					case 'text':
						$value = ( !empty( $value ) ? $value : '&nbsp;' );
						break;
					default:
						if( preg_match( '/^enum(\W)*.*$/', $params['type'] ) ) {
							$classes[] = 'enum string'; // FIXME: class enum::presence
							$value = ( !empty( $value ) ? $value : '&nbsp;' );
						}
						else {
							trigger_error( "Unrecognized type {$params['type']}", E_USER_WARNING );
							return null;
						}
				}
			}

			// Empty ?
			$checkValue = htmlentities( trim( str_replace( '&nbsp;', ' ', $value ) ) ); // FIXME ?
			if( empty( $checkValue ) && !is_numeric( $checkValue ) ) {
				$classes[] = 'empty';
			}

			// Add non breakable spaces to numeric output if inside a tag
			if( !empty( $params['tag'] ) && in_array( $params['type'], array( 'integer', 'float', 'money', 'phone', 'boolean' ) ) ) {
				$value = str_replace( ' ', '&nbsp;', $value );
			}

			// Format for inside a tag
			if( !empty( $params['tag'] ) ) {
				$params = $this->addClass( $params, implode( ' ', $classes ) );

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
				if( $params['type'] == 'text' ) {
					$params['maxlength'] = $params['length'];
				}
			}

			/// FIXME
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

			if( Set::check( $params, 'options' ) && ( isset( $params['type'] ) && in_array( $params['type'], array( 'text', 'integer' ) ) ) ) {
				$params['type'] = 'select';
			}

			if( isset( $params['type'] ) && $params['type'] == 'radio' ) {
				unset( $params['empty'] );
			}

			return $this->Xform->input( $path, $params );
		}
	}
?>