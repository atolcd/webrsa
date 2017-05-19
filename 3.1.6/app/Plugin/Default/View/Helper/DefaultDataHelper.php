<?php
	/**
	 * Code source de la classe DefaultDataHelper.
	 *
	 * PHP 5.4
	 *
	 * @package Default
	 * @subpackage View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'DefaultUtility', 'Default.Utility' );

	/**
	 * La classe DefaultDataHelper ...
	 *
	 * @package Default
	 * @subpackage View.Helper
	 */
	class DefaultDataHelper extends AppHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Text'
		);

		/**
		 * La liste des types de champs, au sens CakePHP, par nom de modèle.
		 *
		 * @var array
		 */
		protected $_cache = array();

		/**
		 * Permet de savoir si le cache a été modifié.
		 *
		 * @var boolean
		 */
		protected $_cacheChanged = false;

		/**
		 * INFO: pas d'erreur et pas utilisé si pas défini (ex: 'fast') ?
		 *
		 * @var string
		 */
		protected $_cacheConfig = 'default';

		/**
		 * Liste des formats par défaut (cf. strftime) pour les types date, time,
		 * datetime.
		 *
		 * @var array
		 */
		protected $_formats = array(
			'date' => '%d/%m/%Y',
			'datetime' => '%d/%m/%Y à %H:%M:%S',
			'time' => '%H:%M:%S'
		);

		/**
		 * Retourne le com de la clé de cache qui sera utilisée par ce helper.
		 *
		 * @return string
		 */
		public function cacheKey() {
			return implode(
				'_',
				Hash::filter(
					array(
						Inflector::camelize( __class__ ),
						Inflector::camelize( $this->request->params['plugin'] ),
						Inflector::camelize( $this->request->params['controller'] ),
						$this->request->params['action'],
					)
				)
			);
		}

		/**
		 * Lecture du cache.
		 *
		 * @param string $viewFile The view file that is going to be rendered
		 * @return void
		 */
		public function beforeRender( $viewFile ) {
			parent::beforeRender( $viewFile );
			$cacheKey = $this->cacheKey();
			$cache = Cache::read( $cacheKey, $this->_cacheConfig );

			if( $cache !== false ) {
				$this->_cache = $cache;
			}
		}

		/**
		 * Sauvegarde du cache.
		 *
		 * @param string $layoutFile The layout file that was rendered.
		 * @return void
		 */
		public function afterLayout( $layoutFile ) {
			parent::afterLayout( $layoutFile );

			if( $this->_cacheChanged ) {
				$cacheKey = $this->cacheKey();
				Cache::write( $cacheKey, $this->_cache, $this->_cacheConfig );
			}
		}

		/**
		 * Retourne le type d'un champ (au sens CakePHP).
		 *
		 * @param string $modelField
		 */
		public function type( $modelField ) {
			list( $modelName, $fieldName ) = model_field( $modelField );

			if( !isset( $this->_cache[$modelName] ) ) {
				try {
					$Model = ClassRegistry::init( $modelName );
					$schema = $Model->schema();
					$schema = array_combine( array_keys( $schema ), Hash::extract( $schema, '{s}.type' ) );
					$this->_cache[$modelName] = $schema;
					$this->_cacheChanged = true;
				} catch( Exception $e ) {
					$this->_cache[$modelName] = array();
				}
			}

			if( isset( $this->_cache[$modelName][$fieldName] ) ) {
				return $this->_cache[$modelName][$fieldName];
			}

			return null;
		}

		/**
		 * Permet de faire la traduction à partir des options pour une valeur ou
		 * une liste de valeurs.
		 *
		 * @param string|array $value
		 * @param array $params
		 * @return string|array
		 */
		public function translateOptions( $value, array $params ) {
			if( isset( $params['options'] ) ) {
				if( !is_array( $value ) ) {
					if( isset( $params['options'][$value] ) ) {
						$value = $params['options'][$value];
					}
				}
				else {
					foreach( $value as $key => $val ) {
						$value[$key] = $this->translateOptions( $val, $params );
					}
				}
			}

			return $value;
		}

		/**
		 * Retourne une chaîne de caractère à partir de la valeur et de son type.
		 *
		 * Les types pris en compte actuellement sont:
		 *	- boolean
		 *	- date
		 *	- datetime
		 *	- integer
		 *
		 * @param mixed $value
		 * @param string $type
		 * @return string
		 */
		public function format( $value, $type, $format = null ) {
			$return = null;

			if( $value === '' ) {
				$value = null;
			}

			if( !is_null( $value ) ) {
				switch( $type ) {
					case 'boolean':
						$return = ( empty( $value ) ? __( 'No' ) : __( 'Yes' ) );
						break;
					case 'date':
					case 'datetime':
					case 'time':
						$format = $format === null ? $this->_formats[$type] : $format;
						$return = strftime( $format, strtotime( $value ) );
						break;
					case 'float':
						$return = number_format( $value, 2, ',', '' );
						break;
					case 'integer':
						$return = number_format( $value );
						break;
					case 'list':
						$return = vfListeToArray( $value );
						break;
					case 'text':
					default:
						if( 'truncate' === $format ) {
							$value = $this->Text->truncate( $value, 500 );
						}
						$return = $value;
				}
			}

			return $return;
		}

		/**
		 * Renvoit les attributs de classe pour une valeur et un type donnés.
		 *
		 * Les types pris en compte actuellement sont:
		 *	- boolean
		 *	- integer
		 *	- numeric
		 *
		 * @param mixed $value
		 * @param string $type
		 * @return array
		 */
		public function attributes( $value, $type ) {
			$attributes = array( 'class' => "data {$type}" );

			if( $value === null ) {
				$attributes = $this->addClass( $attributes, 'null' );
			}
			else {
				$class = null;

				switch( $type ) {
					case 'boolean':
						$class = ( empty( $value ) ? 'false' : 'true' );
						break;
					case 'float':
					case 'integer':
					case 'numeric':
						$class = null;
						if( $value === 0 ) {
							$class = 'zero';
						}
						else if( $value > 0 ) {
							$class = 'positive';
						}
						else if( $value < 0 ) {
							$class = 'negative';
						}
						break;
					case 'list':
						$class = 'text';
						break;
//					case 'datetime':
//					case 'text':
//					default:
				}

				$attributes = $this->addClass( $attributes, $class );
			}

			return $attributes;
		}
	}
?>