<?php
	/**
	 * @deprecated
	 */
	class CacheMap
	{
		/**
		 * Le nom de la clé sous laquelle le cache sera stocké.
		 * Utile pour sous-classer.
		 *
		 * @var string
		 */
		protected static $_cacheKey = 'CacheMap';

		/**
		 *
		 * @var array
		 */
		protected static $_map = array();

		/**
		 * Permet de savoir si la classe a déjà été initialisée.
		 *
		 * @var boolean
		 */
		protected static $_init = false;

		/**
		 * Lors d'un changement, on sauvegarde dans le cache.
		 *
		 * @return void
		 */
		protected static function _onChange() {
			Cache::write( self::$_cacheKey, self::$_map );
		}

		/**
		 * Initialisation, lecture de notre cache si nécessaire.
		 *
		 * @return void
		 */
		protected static function _init() {
			if( !self::$_init ) {
				self::$_map = Cache::read( self::$_cacheKey );
				self::$_init = true;
			}
		}

		/**
		 *
		 * @param string $key
		 * @return array
		 */
		public static function read( $key = null ) {
			self::_init();

			if( is_null( $key ) ) {
				return self::$_map;
			}

			return Hash::get( self::$_map, $key );
		}

		/**
		 *
		 * @param string $key
		 * @param array $values
		 */
		public static function write( $key, $values ) {
			self::_init();
			$values = (array)$values;

			if( !empty( $key ) && !empty( $values ) ) {
				foreach( $values as $value ) {
					if( !isset( self::$_map[$key] ) ) {
						self::$_map[$key] = array();
					}
					self::$_map[$key][] = $value;

					if( !isset( self::$_map[$value] ) ) {
						self::$_map[$value] = array();
					}
					self::$_map[$value][] = $key;
				}
				self::_onChange();
			}
		}

		/**
		 *
		 * @param string $key
		 * @return int
		 */
		public static function delete( $key ) {
			self::_init();
			$removed = 0;

			if( isset( self::$_map[$key] ) ) {
				$otherKeys = self::$_map[$key];
				unset( self::$_map[$key] );

				if( !empty( $otherKeys ) ) {
					foreach( $otherKeys as $otherKey ) {
						unset( self::$_map[$otherKey] );
						$removed++;

						foreach( array_keys( self::$_map ) as $key ) {
							$keysToRemove = array_keys( self::$_map[$key], $otherKey, true );
							if( !empty( $keysToRemove ) ) {
								foreach( $keysToRemove as $keyToRemove ) {
									unset( self::$_map[$key][$keyToRemove] );
								}
							}
						}
					}
				}

				self::_onChange();
			}

			return $removed;
		}
	}
?>
