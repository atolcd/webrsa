<?php
	/**
	 * Lorsque l'on enregistre les résultats d'un requête sous une clé de
	 * cache, on peut avoir plusieurs modèles liés au modèle principal.
	 *
	 * Cette classe sert à connaître les clés de cache à supprimer lorsque les
	 * données d'un modèle changent (ajout/suppression).
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Datasource
	 */

	/**
	 * Gestion plus fine du cache des requêtes sur des modèles CakePHP.
	 *
	 * @package app.Model.Datasource
	 */
	class ModelCache
	{
		/**
		 * Le nom de la clé sous laquelle le cache sera stocké.
		 *
		 * @var string
		 */
		protected static $_cacheKey = 'ModelCache';

		/**
		 * Permet de savoir si les données ont changé et si le cache doit être
		 * réécrit.
		 *
		 * @var boolean
		 */
		protected static $_hasChanged = false;

		/**
		 * Permet de savoir si la classe a déjà été initialisée.
		 *
		 * @var boolean
		 */
		protected static $_init = false;

		/**
		 *
		 * @var array
		 */
		protected static $_cache = array( );

		/**
		 *
		 * @var array
		 */
		protected static $_map = array( );

		/**
		 * Lors d'un changement dans les listes, on sauvegarde dans notre cache.
		 *
		 * @return void
		 */
		protected static function _onChange() {
			Cache::write( self::$_cacheKey, array( self::$_cache, self::$_map ) );
		}

		/**
		 * Initialisation, lecture de notre cache si nécessaire.
		 *
		 * @return void
		 */
		public static function init() {
			if( !self::$_init ) {
				list( $cache, $map ) = Cache::read( self::$_cacheKey );
				self::$_cache = (array)$cache;
				self::$_map = (array)$map;
				self::$_init = true;
			}
		}

		/**
		 * Lorsque l'on enregistre les résultats d'un requête sous une clé de
		 * cache, on peut avoir plusieurs modèles liés au modèle principal.
		 *
		 * @param string $key
		 * @param array $modelNames
		 */
		public static function write( $key, $modelNames ) {
			if( !empty( $modelNames ) ) {
				self::init();

				foreach( (array)$modelNames as $modelName ) {
					if( !isset( self::$_cache[$modelName] ) ) {
						self::$_cache[$modelName] = array();
					}

					self::$_cache[$modelName][] = $key;
				}

				self::$_map[$key] = $modelNames;

				self::_onChange();
			}
		}

		/**
		 * Lorsque les données d'un modèle ont changé, il faut retrouver toutes
		 * les clés de cache auxquelles il est lié.
		 *
		 * @param string $modelName
		 */
		public static function read( $modelName ) {
			self::init();
			$keys = array();

			if( isset( self::$_cache[$modelName] ) ) {
				$keys = self::$_cache[$modelName];
			}

			return $keys;
		}

		/**
		 * Lorsqu'une clé disparaît, il faut l'enlever des clés de l'ensemble
		 * des modèles auxquels elle est liée.
		 *
		 * @param string $key
		 * @return integer Le nombre d'occurences supprimées.
		 */
		public static function delete( $key ) {
			self::init();
			$removed = 0;

			if( isset( self::$_map[$key] ) ) {
				foreach( self::$_map[$key] as $modelName ) {
					 $keysToRemove = array_keys( self::$_cache[$modelName], $key, true );
					 if( !empty( $keysToRemove ) ) {
						 foreach( $keysToRemove as $keyToRemove ) {
							 unset( self::$_cache[$modelName][$keyToRemove] );
							 $removed++;
						 }
					 }
				}

				unset( self::$_map[$key] );
				self::_onChange();
			}

			return $removed;
		}
	}
?>