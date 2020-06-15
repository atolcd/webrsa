<?php
	/**
	 * Code source de la classe Prechargement.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'CakeEvent', 'Event' );
	App::uses( 'File', 'Utility' );
	App::uses( 'Folder', 'Utility' );

	/**
	 * La classe Prechargement se charge du préchargement du cache de l'application
	 * WebRSA.
	 *
	 * @package app.Model
	 */
	class Prechargement extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Prechargement';

		/**
		 * Ce modèle n'est pas lié à une table.
		 *
		 * @var integer
		 */
		public $useTable = false;

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'WebrsaRecherche' );

		/**
		 * Retourne la liste des tables du schéma de la connection default.
		 *
		 * @return array
		 */
		protected function _listTables() {
			$Connection = ClassRegistry::init( 'Connection' );
			$tables = $Connection->query( 'SELECT table_name as name FROM INFORMATION_SCHEMA.tables WHERE table_schema = \'public\' ORDER BY name ASC;' );
			return Hash::extract( $tables, '{n}.0.name' );
		}

		/**
		 * Préchargement d'un domaine donné.
		 *
		 * @param string $domainName Le nom du domaine à précharger
		 */
		public function preloadDomain( $domainName ) {
			$msgid = 'Translate.me';
			if( $domainName != 'default' ) {
				__d( $domainName, $msgid );
			}
			else {
				__( $msgid );
			}
		}

		/**
		 * Retourne la liste des domaines préchargés.
		 *
		 * @return array
		 */
		public function preloadDomains() {
			$domains = array();

			$messagesDir = APP.'Locale/fre/LC_MESSAGES/';
			$Folder = new Folder( $messagesDir, false, 0777 );

			foreach( $Folder->find('.+\.po$') as $fileName ) {
				$domain = preg_replace( '/\.po$/', '', $fileName );
				$this->preloadDomain( $domain );
				$domains[] = $domain;
			}
			sort( $domains );

			return $domains;
		}

		/**
		 *
		 * @param string $eventName
		 * @param mixed $params
		 */
		protected function _dispatchEvent( $eventName, $params = array() ) {
			$Event = new CakeEvent(
				"Model.{$this->alias}.{$eventName}",
				$this,
				(array)$params
			);
			$this->getEventManager()->dispatch( $Event );
		}

		/**
		 * Préchargement complèt d'un modèle.
		 *
		 * @param type $modelName Le nom du modèle
		 * @return array
		 */
		public function preloadModel( $modelName ) {
			App::uses( $modelName, 'Model' );
			$availableDbConfigs = array_keys( ConnectionManager::enumConnectionObjects() );

			$Reflection = new ReflectionClass( $modelName );

			$return = array(
				'name' => $modelName,
				'type' => 'model',
				'initialized' => ( $Reflection->isAbstract() === false ),
				'prechargement' => null,
				'foreignKeys' => null,
			);


			if( $Reflection->isAbstract() === false ) {
				$this->_dispatchEvent( 'preloadModel.begin', $modelName );

				$attributes = get_class_vars( $modelName );

				$return['initialized'] = (
					in_array( $attributes['useDbConfig'], $availableDbConfigs )
					&& ( strstr( $modelName, 'AppModel' ) === false )
				);

				if( $return['initialized'] ) {
					// Initialisation des behaviors
					$this->_dispatchEvent( 'preloadModel.init', $modelName );
					$Model = ClassRegistry::init( $modelName );

					// Méthode préchargement
					$this->_dispatchEvent( 'preloadModel.prechargement', $modelName );
					$return['prechargement'] = $Model->prechargement();

					// Préchargement des clés étrangères de la table
					if( $Model->useTable !== false ) {
						$this->_dispatchEvent( 'preloadModel.postgresForeignKeys', $modelName );

						try {
							if( !$Model->Behaviors->attached( 'Postgres.PostgresTable' ) ) {
								$Model->Behaviors->attach( 'Postgres.PostgresTable' );
							}

							$foreignKeys = $Model->getPostgresForeignKeys();
							$return['foreignKeys'] = count( $foreignKeys );
						} catch( Exception $Exception ) {
							$return['foreignKeys'] = null;
						}
					}
				}

				$this->_dispatchEvent( 'preloadModel.end', $modelName );
			}

			return $return;
		}

		/**
		 * Nettoyage de l'ensemble du cache.
		 */
		public function clearCache() {
			$cacheConfigNames = Cache::configured();
			if( !empty( $cacheConfigNames ) ) {
				foreach( $cacheConfigNames as $cacheConfigName ) {
					Cache::clear( false, $cacheConfigName );
				}
			}
		}

		/**
		 * Préchargement des informations liées aux tables.
		 *
		 * @return array
		 */
		public function preloadTables() {
			$User = ClassRegistry::init( 'User' );

			if( !$User->Behaviors->attached( 'Postgres.PostgresTable' ) ) {
				$User->Behaviors->attach( 'Postgres.PostgresTable' );
			}

			$foreignKeys = $User->getAllPostgresForeignKeys();

			$tables = $User->query( 'SELECT table_name as name FROM INFORMATION_SCHEMA.tables WHERE table_schema = \'public\' ORDER BY name ASC;' );
			$tables = Hash::extract( $tables, '{n}.0.name' );
			sort( $tables );

			return compact( 'foreignKeys', 'tables' );
		}

		/**
		 * Préchargement du cache de l'application.
		 *
		 * @param boolean $clear Doit-on vider le cache avant le préchargement ?
		 * @return array
		 */
		public function preloadCache( $clear = true ) {
			$departement = Configure::read( 'Cg.departement' );
			if( $clear ) {
				$this->clearCache();
			}

			$this->_dispatchEvent( 'preloadTables' );

			$preloadTables = $this->preloadTables();
			$missingTables = (array)Hash::get( $preloadTables, 'tables' );

			$modelNames = App::objects( 'model' );

			$modelRechercheNames = $this->WebrsaRecherche->modelsDepartement();

			// Nettoyage de la liste des modèles à traiter
			foreach( $modelNames as $key => $modelName ) {
				// Modèles traités par WebrsaRecherche n'appartenant pas au département
				if( preg_match( '/^Webrsa(Recherche|Cohorte).+/', $modelName ) && in_array( $modelName, $modelRechercheNames ) === false ) {
					unset( $modelNames[$key] );
				}
				// Modèles n'appartenant pas au département
				else if( preg_match( '/([0-9]{2,3})$/', $modelName ) && !preg_match( '/'.$departement.'$/', $modelName ) ) {
					unset( $modelNames[$key] );
				}
			}
			sort( $modelNames );

			$this->_dispatchEvent( 'preloadModels', count( $modelNames ) );

			$loadedModels = array();
			foreach( $modelNames as $modelName ) {
				$loadedModels[] = $this->preloadModel( $modelName );

				$key = array_search( Inflector::tableize( $modelName ), $missingTables );
				if( $key !== false ) {
					unset( $missingTables[$key] );
				}
			}

			$return = array(
				'Prechargement' => array(
					array(
						'name' => 'initialized',
						'title' => 'Modèles initialisés',
						'type' => 'model',
						'entries' => Hash::extract( $loadedModels, '{n}[initialized=1].name' ),
						'error' => false,
					),
					array(
						'name' => 'uninitialized',
						'title' => 'Modèles non initialisés',
						'type' => 'model',
						'entries' => Hash::extract( $loadedModels, '{n}[initialized=0].name' ),
						'error' => false,
					),
					array(
						'name' => 'prechargements',
						'title' => 'Modèles préchargés',
						'type' => 'model',
						'entries' => Hash::extract( $loadedModels, '{n}[prechargement=1].name' ),
						'error' => false,
					),
					array(
						'name' => 'nonprechargements',
						'title' => 'Erreur(s) de préchargement',
						'type' => 'model',
						'entries' => Hash::extract( $loadedModels, '{n}[prechargement=0].name' ),
						'error' => true,
					),
					array(
						'name' => 'missing',
						'title' => 'Tables sans modèle lié',
						'type' => 'model',
						'entries' => $missingTables,
						'error' => false,
					),
					array(
						'name' => 'domains',
						'title' => 'Traductions',
						'type' => 'locale',
						'entries' => $this->preloadDomains(),
						'error' => false,
					),
					array(
						'name' => 'foreignKeys',
						'title' => 'Clés étrangères',
						'type' => 'model',
						'entries' => array_keys( (array)Hash::get( $preloadTables, 'foreignKeys' ) ),
						'error' => false,
					)
				)
			);

			return $return;
		}
	}
?>