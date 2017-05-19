<?php
	/**
	 * @deprecated
	 */
	class AppModels
	{
		protected static $_instance = null;

		// TODO
		protected static $_cache = array();

		/**
		 * Le nom de la clé sous laquelle le cache sera stocké.
		 *
		 * @var string
		 */
		protected static $_cacheKey = 'AppModels';

		protected function __construct() {
		}

		/**
		 *
		 * @url http://php.net/manual/en/language.oop5.magic.php
		 *
		 * @var array
		 */
		protected static $_magicMethods = array(
			'__construct',
			'__destruct',
			'__call',
			'__callStatic',
			'__get',
			'__set',
			'__isset',
			'__unset',
			'__sleep',
			'__wakeup()',
			'__toString()',
			'__invoke',
			'__set_state',
			'__clone',
		);

		protected static function _visibility( $method ) {
			if( strpos( $method, '__' ) === 0 ) {
				if( in_array( $method, self::$_magicMethods ) ) {
					return 'magic';
				}
				else {
					return 'private';
				}
			}
			else if( strpos( $method, '_' ) === 0 ) {
				return 'protected';
			}
			else {
				return 'public';
			}
		}

		protected static function _init() {
			App::uses( 'AppModel', 'Model' );
			$rawAppModelMethods = get_class_methods( 'AppModel' );
			$models = App::objects( 'model' );

			foreach( $models as $model ) {
				if( !in_array( $model, array( 'AppModel' ) ) ) {
					App::uses( $model, 'Model' );

					if( class_exists( $model ) ) {
						// Attributes
						$vars = get_class_vars( $model );
						self::$_cache[$model]['attributes'] = $vars;

						// Methods
						$rawMethods = get_class_methods( $model );
						$rawMethods = array_diff( $rawMethods, $rawAppModelMethods );

						$methods = array( 'public' => array(), 'protected' => array(), 'private' => array(), 'magic' => array() );
						foreach( $rawMethods as $rawMethod ) {
							$methods[self::_visibility( $rawMethod )][] = $rawMethod;
						}

						self::$_cache[$model]['methods'] = $methods;
					}
				}
			}
		}

		public static function &getInstance() {
			if( is_null( self::$_instance ) ) {
				self::$_instance = new self();
				self::_init();
			}
			return self::$_instance;
		}

		public static function withMethod( $methodName ) {
			$self = self::getInstance();
			$return = array();
			$visibility = self::_visibility( $methodName );

			foreach( self::$_cache as $modelName => $data ) {
				if( in_array( $methodName, $data['methods'][$visibility] ) ) {
					$return[] = $modelName;
				}
			}

			return $return;
		}
	}

//	App::uses( 'AppModels', 'AppClasses.Utility' );
////	debug( AppModels::getInstance() );
//	debug( AppModels::withMethod( 'checkPostgresqlIntervals' ) );
//	debug( AppModels::withMethod( 'storedDataErrors' ) );
//	debug( AppModels::withMethod( 'sqrechercheErrors' ) );
//	debug( AppModels::getInstance()->withMethod( 'search' ) );
//	App::uses( 'AppModels', 'AppClasses.Utility' );
//	debug( AppModels::getInstance()->withMethod( 'sqrechercheErrors' ) );
?>
