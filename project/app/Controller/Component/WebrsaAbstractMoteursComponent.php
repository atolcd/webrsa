<?php
	/**
	 * Code source de la classe WebrsaAbstractMoteursComponent.
	 *
	 * FIXME, voir les classes suivantes:
	 *	- WebrsaRecherchesApresComponent (__construct pour le suffixe de l'APRE suivant le département)
	 *	- WebrsaRecherchesFichesprescriptions93Component (le modèle vient de _getModelName(), de l'existence ou non de la fiche)
	 *	- WebrsaRecherchesTransfertspdvs93Component (surcharge des conditions)
	 *
	 * TODO
	 *	- ajouter la sous-classe de recherches
	 *	- ajouter la sous-classe de jetons
	 *	- ajouter la sous-classe de vérifications
	 *	- ajouter un paramétrage à ConfigurableQueryDefaultHelper (ancienne manière, nouvelle manière de nommer)
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaAbstractMoteursComponent ...
	 *
	 * @package app.Controller.Component
	 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
	 */
	abstract class WebrsaAbstractMoteursComponent extends Component implements CakeEventListener
	{
		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array( 'Allocataires' );

		/**
		 * Surcharge du constructeur pour s'assurer que les components des
		 * différentes classes de l'héritage soient chargées.
		 *
		 * @param \ComponentCollection $collection
		 * @param array $settings
		 */
		public function __construct( \ComponentCollection $collection, $settings = array() ) {
			$ancestors = get_class_hierarchy( get_called_class(), 'Component' );

			foreach( $ancestors as $ancestor ) {
				$parentClass = get_parent_class( $ancestor );
				$this->_mergeVars( array( 'components' ), $parentClass );
			}

			parent::__construct( $collection, $settings );
		}

		/**
		 * Retourne un array avec clés de paramètres suivantes complétées en
		 * fonction du contrôleur:
		 *	- modelName: le nom du modèle sur lequel se fera la pagination
		 *	- modelRechercheName: le nom du modèle de moteur de recherche
		 *	- searchKey: le préfixe des filtres renvoyés par le moteur de recherche
		 *	- searchKeyPrefix: le préfixe des champs configurés
		 *	- configurableQueryFieldsKey: les clés de configuration contenant les
		 *    champs à sélectionner dans la base de données.
		 *  - auto: la recherche doit-elle être lancée (avec les valeurs par défaut
		 *    des filtres de recherche) automatiquement au premier accès à la page,
		 *    lors de l'appel à une méthode search() ou cohorte(). Configurable
		 *    avec Configure::write( 'ConfigurableQuery.<Controller>.<action>.query.auto' )
		 *
		 * @param array $params
		 * @return array
		 */
		protected function _params( array $params = array() ) {
			$Controller = $this->_Collection->getController();

			$params += array(
				'modelName' => $Controller->modelClass,
				'modelRechercheName' => 'WebrsaRecherche'.$Controller->modelClass,
				'searchKey' => 'Search',
				'searchKeyPrefix' => 'ConfigurableQuery',
				'configurableQueryFieldsKey' => Inflector::camelize( $Controller->request->params['controller'] ).".{$Controller->request->params['action']}",
				'auto' => null,
				'limit' => 10,
				'keys' => null
			);

			$params['auto'] = Configure::read( $this->_configureKey( 'auto', $params ) ) === true;

			return $params;
		}

		/**
		 * Retourne la clé de configuration complète à partir de son suffixe.
		 *
		 * @param string $path Le suffixe
		 * @param array $params
		 * @return string
		 */
		protected function _configureKey( $path, array $params ) {
			return "{$params['searchKeyPrefix']}.{$params['configurableQueryFieldsKey']}.{$path}";
		}

		/**
		 * Doit on lancer la recherche ?
		 *
		 * @param array $params
		 * @return boolean
		 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
		 */
		protected function _needsSearch( array $params ) {
			$Controller = $this->_Collection->getController();
			return !empty( $Controller->request->data );
		}

		/**
		 * Retourne les valeurs par défaut du formulaire de recherche.
		 *
		 * @param array $params
		 * @return array
		 */
		protected function _defaults( array $params ) {
			$defaults = (array)Configure::read( $this->_configureKey( 'filters.defaults', $params ) );
			return empty( $params['searchKey'] ) ? $defaults : array( $params['searchKey'] => $defaults );
		}

		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * La mise en cache se fera dans ma méthode _options().
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params ) {
			$Controller = $this->_Collection->getController();

			if ( !isset($Controller->{$params['modelName']}) ) {
				$Controller->loadModel($params['modelName']);
			}

			return Hash::merge(
				$this->Allocataires->optionsEnums( $params ),
				$Controller->{$params['modelName']}->enums()
			);
		}

		/**
		 * Retourne les options stockées en session, liées à l'utilisateur connecté.
		 *
		 * @return array
		 */
		protected function _optionsSession( array $params ) {
			return $this->Allocataires->optionsSession( $params );
		}

		/**
		 * Retourne les options stockées liées à des enregistrements en base de
		 * données, ne dépendant pas de l'utilisateur connecté.
		 *
		 * La mise en cache se fera dans ma méthode _options().
		 *
		 * @see _optionsRecordsModels(), _options()
		 *
		 * @return array
		 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
		 */
		protected function _optionsRecords( array $params ) {
			return $this->Allocataires->optionsRecords( $params );
		}

		/**
		 * Retourne les noms des modèles dont des enregistrements seront mis en
		 * cache après l'appel à la méthode _optionsRecords() afin que la clé de
		 * cache générée par la méthode _options() se trouve associée dans
		 * ModelCache.
		 *
		 * @see _optionsRecords(), _options()
		 *
		 * @return array
		 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
		 */
		protected function _optionsRecordsModels( array $params ) {
			return array( 'Serviceinstructeur' );
		}

		/**
		 * Retourn le chemin vers la clé de cache, en fonction du contrôleur, des
		 * paramètres et d'éventuels paramètres supplémentaires.
		 *
		 * @param array $params
		 * @param array $extra
		 * @return string
		 */
		protected function _cacheKey( array $params, array $extra = array() ) {
			$Controller = $this->_Collection->getController();

			if ( !isset($Controller->{$params['modelName']}) ) {
				$Controller->loadModel($params['modelName']);
			}

			return $Controller->{$params['modelName']}->useDbConfig.'_Controllers_'.Inflector::camelize( $Controller->request->params['controller'] ).'_'.$Controller->request->params['action'].'_'.$params['modelRechercheName'].'_'.$params['modelName'].( empty( $extra ) ? '' : '_'.implode( '_', $extra ) );
		}

		/**
		 * Retourne les options à envoyer dans la vue. Effectue la mise en cache
		 * des options de type enum et records (en ajoutant les noms des modèles
		 * utilisés dans la classe ModelCache).
		 *
		 * @param array $params
		 * @return array
		 */
		final protected function _options( array $params ) {
			$cacheKey = $this->_cacheKey( $params, array( __FUNCTION__ ) );
			$options = Cache::read( $cacheKey );

			if( $options === false ) {
				$options = Hash::merge(
					$this->_optionsEnums( $params ),
					$this->_optionsRecords( $params )
				);

				Cache::write( $cacheKey, $options );
				$modelNames = array_unique( $this->_optionsRecordsModels( $params ) );
				if( !empty( $modelNames ) ) {
					ModelCache::write( $cacheKey, $modelNames );
				}
			}

			$options = Hash::merge(
				$options,
				$this->_optionsSession($params )
			);

			return $this->_optionsAccepted( $options, $params );
		}

		/**
		 * Permet de filtrer les options envoyées à la vue au moyen de la clé
		 * 'filters.accepted' dans le fichier de configuration.
		 *
		 * @param array $options
		 * @param array $params
		 * @return array
		 */
		protected function _optionsAccepted( array $options, array $params ) {
			$params = $this->_params( $params );

			$accepted = (array)Configure::read( $this->_configureKey( 'filters.accepted', $params ) );

			foreach( $accepted as $path => $acceptedValues ) {
				foreach( array_keys( (array)Hash::get( $options, $path ) ) as $value ) {
					if( in_array_strings( $value, $acceptedValues ) === false ) {
						$options = Hash::remove( $options, "{$path}.{$value}" );
					}
				}
			}

			return $options;
		}

		/**
		 * Retourne les filtres du moteur de recherche éventuellement modifiés
		 * suivant la valeur de lé clé query.restrict.
		 *
		 * Pour chacune des éléments de configuration qui possède Model.field en
		 * clé, si la valeur est une chaîne de caractères, alors la valeur du
		 * filtre sera forcée à cette valeur; si c'est un array alors la valeur
		 * du filtre sera forcée à l'intersection de l'array configurée et de l'
		 * array renvoyée par les filtres; si l'intersection est vide, la valeur
		 * du filtre sera forcée à la valeur configurée.
		 *
		 * @fixme Problème avec Situationdossierrsa.etatdosrsa 0
		 *
		 * @param array $search Les filtres renvoyés par le moteur de recherche
		 * @param array $params
		 * @return array
		 */
		protected function _filtersRestrictions( array $search, array $params ) {
			// TODO: renommer en filters.restrict ?
			$restrict = (array)Configure::read( $this->_configureKey( 'query.restrict', $params ) );

			foreach( $restrict as $path => $accepted ) {
				$value = Hash::get( $search, $path );

				if( $value === null || ( !is_array( $value ) && !in_array_strings( $value, (array)$accepted ) ) ) {
					$value = $accepted;
				}
				else if( is_array( $value ) ) {
					$intersect = array_intersect( $value, (array)$accepted );
					if( empty( $intersect ) ) {
						$value = (array)$accepted;
					}
					else {
						$value = $intersect;
					}
				}

				$search = Hash::insert( $search, $path, $value );
			}

			return $search;
		}

		/**
		 * Retourne les valeurs des filtres envoyés par la moteur de recherche,
		 * nettoyés et complétés au besoin, suivant la configuration.
		 *
		 * @param array $params
		 * @return array
		 */
		protected function _filters( array $params ) {
			$Controller = $this->_Collection->getController();

			$search = array();
			if( $Controller->request->is( 'get' ) || false !== strpos( PHP_SAPI, 'cli' ) ) {
				if( empty( $params['searchKey'] ) === false ) {
					if( isset( $Controller->request->data[$params['searchKey']] ) ) {
						$search = $Controller->request->data[$params['searchKey']];
					}
					else {
						if( !empty( $Controller->request->params['named'] ) ) {
							$search = (array)Hash::get( Hash::expand( $Controller->request->params['named'], '__' ), $params['searchKey'] );
						}
					}
				}
				else {
					if( !empty( $Controller->request->data ) ) {
						$search = $Controller->request->data;
					}
					else {
						if( !empty( $Controller->request->params['named'] ) ) {
							$search = (array)Hash::expand( $Controller->request->params['named'] );
						}
					}
				}
			}

			// Nettoyage de certaines valeurs des filtres si besoin.
			$skip = (array)Configure::read( $this->_configureKey( 'filters.skip', $params ) );
			if( !empty( $skip ) ) {
				foreach( $skip as $path ) {
					$search = Hash::remove( $search, $path );
				}
			}

			$search = $this->_filtersRestrictions( $search, $params );

			return $search;
		}

		protected function _queryBase( $keys, array $params ) {
			$Controller = $this->_Collection->getController();
			$cacheKey = $this->_cacheKey( $params, array( __FUNCTION__ ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $Controller->{$params['modelRechercheName']}->searchQuery();
				$query = ConfigurableQueryFields::getFieldsByKeys( $keys, $query );

				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		protected function _queryLimit( array $query, array $params ) {
			$query['limit'] = $params['limit'];
			$limit = Configure::read( $this->_configureKey( 'limit', $params ) );
			if( is_int( $limit) ) {
				$query['limit'] = $limit;
			}

			$Controller = $this->_Collection->getController();
			if (isset ($Controller->request->data['limit']) && is_numeric ($Controller->request->data['limit'])) {
				$query['limit'] = $Controller->request->data['limit'];
			}

			if( in_array( $query['limit'], array( null, false ) ) ) {
				unset( $query['limit'] );
			}

			return $query;
		}


		protected function _queryConditions( array $query, array $filters, array $params ) {
			$Controller = $this->_Collection->getController();

			// Conditions venant des filtres de recherche
			$query = $Controller->{$params['modelRechercheName']}->searchConditions( $query, $filters );

			// Conditions liées à l'utilisateur connecté
			$query = $this->Allocataires->completeSearchQuery( $query, $params );

			// Conditions supplémentaires depuis la configuration
			$conditions = (array)Configure::read( $this->_configureKey( 'query.conditions', $params ) );
			if( !empty( $conditions ) ) {
				$query['conditions'][] = $conditions;
			}

			$has = (array)Configure::read( $this->_configureKey( 'filters.has', $params ) );

			// On met systématiquement le contenu de has en conditions
			foreach ($has as $key => $value) {
				if (is_array($value)) {
					$has[$key] = array( 'conditions' => $value );
				}
			}

			$query = ClassRegistry::init( 'Personne' )->WebrsaPersonne->completeQueryHasLinkedRecord(
				$has,
				$query,
				$filters
			);

			// Conditions configurables
			$query['conditions'] = array_merge(
				(array)Hash::get($query, 'conditions'),
				$this->Allocataires->configurableConditions($filters, $params)
			);

			// Recherche dossier PCG
			$etat_dossierpcg66 = (string)Hash::get( $filters, 'Dossierpcg66.has_dossierpcg66' );
			$Controller->loadModel( 'Dossier' );
			if ($etat_dossierpcg66 === '0') {
				$query['conditions'][] = 'NOT ' . ' EXISTS ( ' . $Controller->Dossier->Foyer->dossiersPCG66 () . ' )';
			}
			else if ($etat_dossierpcg66 === '1') {
				$query['conditions'][] = ' EXISTS ( ' . $Controller->Dossier->Foyer->dossiersPCG66 () . ' )';
			}

			/**
			 * Recherche par Tag / état du Tag
			 *
			 * SAUF SI ON TAGUE PAR COHORTE
			 */
			if ($params['configurableQueryFieldsKey'] != 'Tags.cohorte') {
				$valeurtag_id = '';
				if (isset ($filters['Tag']['valeurtag_id'])) {
					$valeurtag_id = $filters['Tag']['valeurtag_id'];
				}
				$etat = '';
				if (isset ($filters['Tag']['etat'])) {
					$etat = $filters['Tag']['etat'];
				}
				$exclusionValeur = isset ($filters['Tag']['exclusionValeur']) ? true : false;
				$exclusionEtat = isset ($filters['Tag']['exclusionEtat']) ? true : false;
				$createdFrom =  null;
				$createdTo = null;
				if (isset ($filters['Tag']['created']) && $filters['Tag']['created'] === '1') {
					$createdFrom = isset ($filters['Tag']['created_from']) ? $filters['Tag']['created_from'] : null;
					$createdTo = isset ($filters['Tag']['created_to']) ? $filters['Tag']['created_to'] : null;
				}

				if (false === empty($valeurtag_id) || false === empty($etat) || false === is_null($createdFrom)) {
					$query['conditions'][] = ClassRegistry::init('Tag')->sqHasTagValue($valeurtag_id, '"Foyer"."id"', '"Personne"."id"', $etat, $exclusionValeur, $exclusionEtat, $createdFrom, $createdTo);
				}
			}

			return $query;
		}

		protected function _queryOrder( array $query, array $params ) {
			$Controller = $this->_Collection->getController();

			// Met les clefs sort et direction dans le query['order'] et les rends invisible pour le paginateur
			$sortCol = false;
			$toSave = array('sort', 'direction');
			foreach ($toSave as $saveName) {
				$$saveName = Hash::get($Controller->request->params, 'named.'.$saveName);
				$Controller->request->params = Hash::remove(
					$Controller->request->params,
					'named.'.$saveName
				);

				if ($$saveName !== null) {
					$sortCol = true;
					$Controller->request->params['named']['saved_'.$saveName] = $$saveName;
				}
			}
			$Controller->request->params['named']['saved_modelName'] = $params['modelName'];

			// Si un order existe dans l'url, on l'utilise, sinon on regarde dans la conf
			$query['order'] = $sortCol ? array($sort => $direction) : (array)Configure::read($this->_configureKey('query.order', $params));
			if (!isset($query['order'][$params['modelName'].'.id'])) {
				$query['order'][$params['modelName'].'.id'] = 'DESC';
			}

			return $query;
		}

		/**
		 * Restitution de l'order sauvegardé
		 *
		 * @param Controller $controller
		 */
		public function beforeRender(Controller $controller) {
			if (isset($controller->request->params['named']['saved_sort'])) {
				$sort = $controller->request->params['named']['saved_sort'];
				$direction = $controller->request->params['named']['saved_direction'];
				$order = array($sort => $direction);

				$controller->request->params['paging'][$controller->request->params['named']['saved_modelName']]['order'] = $order;
				$controller->request->params['paging'][$controller->request->params['named']['saved_modelName']]['options'] = compact(
					'sort', 'direction', 'order'
				);
			}

			return parent::beforeRender($controller);
		}

		protected function _query( array $filters, array $params ) {
			// Clés de configuration des champs à ramener par la requête
			$keys = array();
			foreach( (array)$params['keys'] as $key ) {
				$keys[] = $this->_configureKey( $key, $params );
			}

			$query = $this->_queryBase( $keys, $params );

			$query = $this->_queryConditions( $query, $filters, $params );

			$query = $this->_queryOrder( $query, $params );

			$query = $this->_queryLimit( $query, $params );

			return $query;
		}

		/**
		 * Initialisation de la recherche: chargement du modèle de recherche dans
		 * le contrôleur, modification de la configuration au moyen des ini_set.
		 *
		 * @param array $params
		 */
		protected function _initializeSearch( array $params = array() ) {
			$Controller = $this->_Collection->getController();

			// Chargement du modèle de recherche dans le contrôleur si nécessaire
			if( !isset( $Controller->{$params['modelRechercheName']} ) ) {
				$Controller->loadModel( $params['modelRechercheName'] );
			}

			$key = $this->_configureKey( 'ini_set', $params);
			$ini_set = (array)Configure::read( $key );
			foreach( $ini_set as $key => $value ) {
				ini_set( $key, $value );
			}
		}

		/**
		 * Actions à effectuer systématiquement
		 *
		 * @param array $params
		 */
		protected function _alwaysDo($params) {
			$Controller = $this->_Collection->getController();

			// Intégration du module Savesearch
			if (Configure::read('Module.Savesearch.enabled')) {
				$conditions = array(
					'user_id' => $Controller->Session->read('Auth.User.id'),
					'group_id' => $Controller->Session->read('Auth.User.group_id'),
					'controller' => $Controller->name,
					'action' => $Controller->action,
				);
				$Controller->set('moduleSavesearchDispo', ClassRegistry::init('Savesearch')->getAvailablesSearchs($conditions));
			}

			$Controller->set('configurableQueryParams', $params);
		}

		// ---------------------------------------------------------------------

		public function implementedEvents() {
			return array(
				'Component.beforeSearch' => array( 'callable' => 'beforeSearch', 'passParams' => true ),
				'Component.afterSearch' => array( 'callable' => 'afterSearch', 'passParams' => true ),
			);
		}

		protected $_eventManager = null;

		public function getEventManager() {
			if( empty( $this->_eventManager ) ) {
				$this->_eventManager = new CakeEventManager();
				$this->_eventManager->attach( $this->_Collection );
				$this->_eventManager->attach( $this );
			}
			return $this->_eventManager;
		}

		final protected function _fireBeforeSearch( array $params, array $query ) {
			$Event = new CakeEvent( 'Component.beforeSearch', $this, array( $params, $query ) );
			$this->getEventManager()->dispatch( $Event );
			return $Event->result;
		}

		final protected function _fireAfterSearch( array $params, array $results ) {
			$Event = new CakeEvent( 'Component.afterSearch', $this, array( $params, $results ) );
			$this->getEventManager()->dispatch( $Event );
			return $Event->result;
		}

		public function beforeSearch( array $params, array $query ) {
			return $query;
		}


		public function afterSearch( array $params, array $results ) {
			return $results;
		}

		// ---------------------------------------------------------------------

		/**
		 *
		 * @param array $params
		 */
		final public function exportcsv( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$defaults = array( 'keys' => array( 'results.fields' ), 'limit' => false, 'view' => '/Elements/ConfigurableQuery/exportcsv' );
			$params = $this->_params( $params + $defaults );

			// Initialisation de la recherche
			$this->_initializeSearch( $params );

			// Récupération des valeurs du formulaire de recherche
			$filters = $this->_filters( $params );

			// Récupération du query
			$query = $this->_query( $filters, $params );

			// Exécution du query et assignation des résultats
			$Controller->{$params['modelName']}->forceVirtualFields = true;
			$query = $this->_fireBeforeSearch( $params, $query );
			$results = $Controller->{$params['modelName']}->find( 'all', $query );
			$results = $this->_fireAfterSearch( $params, $results );

			// Récupération des options
			$options = $this->_options( $params );

			// Assignation à la vue
			$Controller->set( compact( 'results', 'options' ) );

			// Propre à l'export CSV, fichier de vue pour le rendu, sans layout
			$Controller->view = $params['view'];
			$Controller->layout = null;
		}

		/**
		 *
		 * @param array $params
		 */
		final public function query( array $params = array(), array $filters = array() ) {
			$Controller = $this->_Collection->getController();
			$defaults = array( 'keys' => array( 'results.fields' ), 'limit' => false, 'view' => '/Elements/ConfigurableQuery/exportcsv' );
			$params = $this->_params( $params + $defaults );

			// Initialisation de la recherche
			$this->_initializeSearch( $params );

			// Récupération des valeurs du formulaire de recherche
			$Controller->request->data = $filters;
			$filters = $this->_filters( $params );

			// Récupération du query
			$query = $this->_query( $filters, $params );

			// Exécution du query et assignation des résultats
			$Controller->{$params['modelName']}->forceVirtualFields = true;
			$query = $this->_fireBeforeSearch( $params, $query );

			return $query;
		}

		/**
		 * Redirige la page en GET avec les valeurs par défaut du moteur de
		 * recherche.
		 *
		 * @return array
		 */
		protected function _auto( array $defaults, array $params ) {
			$Controller = $this->_Collection->getController();

			$defaults = empty( $params['searchKey'] ) ? $defaults : (array)Hash::get( $defaults, $params['searchKey'] );
			$defaults = empty( $defaults ) ? array( 'search' => true ) : $defaults;

			$url = $Controller->request->params;
			$url['named'] = array();

			return $Controller->redirect( $url + Hash::flatten( empty( $params['searchKey'] ) ? $defaults : array( $params['searchKey'] => $defaults ), '__' ) );
		}

		// ---------------------------------------------------------------------
		// TODO: classe WebrsaMoteursCheckers (générique), search/exportcsv
		// ---------------------------------------------------------------------

		public function checkQuery( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$params = $this->_params( $params );

			// Initialisation de la recherche
			$this->_initializeSearch( $params );

			// Récupération du query
			$query = $this->_query( array(), $params );

			// Exécution du query et assignation des résultats
			$Controller->{$params['modelName']}->forceVirtualFields = true;
			$query = $Controller->{$params['modelName']}->beforeFind( $query );
			$sql = $Controller->{$params['modelName']}->sq( $query );

			$Dbo = $Controller->{$params['modelName']}->getDataSource();
			return $Dbo->checkPostgresSqlSyntax( $sql );
		}

		/**
		 * Vérification des champs demandés dans la configuration par-rapport
		 * aux champs disponibles.
		 *
		 * @param array $params
		 * @param array $search Filtres de recherche nécessaires pour certaines jointures
		 * @return array
		 */
		public function checkConfiguredFields( array $params = array(), array $search = array() ) {
			$Controller = $this->_Collection->getController();
			$params = $this->_params( $params );

			// Initialisation de la recherche
			$this->_initializeSearch( $params );

			// Récupération du query
			$searchQuery = $this->_query( $search, $params );

			// Champs disponibles
			$available = query_fields( $searchQuery );

			$requested = array();

			foreach( $params['keys'] as $path ) {
				$configureKey = $this->_configureKey( $path, $params );
				$requested = array_merge(
					$requested,
					array_keys( Hash::normalize( (array)Configure::read( $configureKey ) ) )
				);
			}
			foreach( $requested as $key => $value ) {
				if( strpos( $value, '/' ) !== false ) {
					unset( $requested[$key] );
				}
			}
			sort( $requested );

			$missing = array_diff( $requested, $available );

			// Exceptions pour les champs virtuels
			$exceptions = array(
				'Cui.positioncui66', // 66
				'Personne.etat_dossier_orientation', // 58
				'Activite.act' // 58
			);
			foreach($missing as $key => $miss) {
				if( in_array($miss, $exceptions) ) {
					unset($missing[$key]);
				}
			}

			$msg = 'Les champs suivants sont demandés mais ne sont pas disponibles: %s';
			$check = array(
				'success' => empty( $missing ),
				'message' => empty( $missing ) ? null : sprintf( $msg, implode( ', ', $missing ) ),
				'value' => var_export( $requested, true )
			);

			return $check;
		}

		/**
		 * Retourne la liste des clés de configuration possibles ainsi que des
		 * règles de validation pour chacune d'entre elle, à utiliser dans la
		 * partie "Vérification de l'application".
		 *
		 * Il faudra renseigner au minimum les clés suivantes dans les paramètres:
		 *	- keys
		 *	- configurableQueryFieldsKey (<Controller><.<action>)
		 *
		 * @param array $params
		 * @return array
		 */
		public function configureKeys( array $params = array() ) {
			$params = $this->_params( $params );

			$keys = Hash::normalize( (array)$params['keys'] );
			foreach( array_keys( $keys ) as $key ) {
				if( $key === 'results.fields' ) {
					$keys[$key] = array( array( 'rule' => 'isarray' ) );
				}
				else {
					$keys[$key] = array( array( 'rule' => 'isarray', 'allowEmpty' => true ) );
				}
			}

			$config = array_merge(
				array(
					'filters.defaults' => array( array( 'rule' => 'isarray', 'allowEmpty' => true ) ),
					'filters.accepted' => array( array( 'rule' => 'isarray', 'allowEmpty' => true ) ),
					'filters.skip' => array( array( 'rule' => 'isarray', 'allowEmpty' => true ) ),
					'filters.has' => array( array( 'rule' => 'isarray', 'allowEmpty' => true ) ),
					'query.restrict' => array( array( 'rule' => 'isarray', 'allowEmpty' => true ) ),
					'query.conditions' => array( array( 'rule' => 'isarray', 'allowEmpty' => true ) ),
					'query.order' => array( array( 'rule' => 'isarray', 'allowEmpty' => true ) ),
					'limit' => array( array( 'rule' => 'integer', 'allowEmpty' => true ) ),
					'auto' => array( array( 'rule' => 'boolean', 'allowEmpty' => true ) ),
					'results.header' => array( array( 'rule' => 'isarray', 'allowEmpty' => true ) ),
				),
				$keys,
				array(
					'ini_set' => array( array( 'rule' => 'isarray', 'allowEmpty' => true ) )
				)
			);

			$result = array();
			foreach( $config as $key => $value ) {
				$result[$this->_configureKey( $key, $params )] = $value;
			}

			return $result;
		}
	}
?>