<?php
	/**
	 * Code source de la classe RequestsmanagerController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe RequestsmanagerController ...
	 *
	 * @package app.Controller
	 */
	class RequestsmanagerController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Requestsmanager';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(

		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Cake1xLegacy.Ajax',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Requestmanager',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'ajax_check' => 'Requestsmanager:index',
			'ajax_get' => 'Requestsmanager:index',
			'ajax_getjointure' => 'Requestsmanager:index',
			'ajax_gettable' => 'Requestsmanager:index',
			'ajax_list' => 'Requestsmanager:index',
			'ajax_load' => 'Requestsmanager:index',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(

		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'ajax_check' => 'read',
			'ajax_get' => 'read',
			'ajax_getjointure' => 'read',
			'ajax_gettable' => 'read',
			'ajax_list' => 'read',
			'ajax_load' => 'read',
			'edit' => 'update',
			'index' => 'read',
			'newrequest' => 'read',
			'savedindex' => 'read',
			'search' => 'read',
		);

		/**
		 * Les options sont rendu disponnible entre fonctions
		 *
		 * @var type
		 */
		public $options = array();

		/**
		 * Editeur de requetes.
		 * Permet de lancer une recherche sur une requete préalablement faite, ou de générer une nouvelle requete.
		 */
		public function index() {
			$options = $this->_options();
			$this->options = $options;

			$this->set( compact('options') );
		}

		/**
		 * Lance la recherche sur une requete préalablement faite
		 */
		public function search() {
			if ( !empty($this->request->data) ) {
				$result = $this->Requestmanager->find( 'first',
					array(
						'conditions' => array(
							'id' => Hash::get($this->request->data, 'Requestmanager.name')
						)
					)
				);

				$Model = ClassRegistry::init(Hash::get($result, 'Requestmanager.model'));
				$query = $this->_cleanOrder( json_decode(Hash::get($result, 'Requestmanager.json'), true) );
				$title = Hash::get($result, 'Requestmanager.name');
				$options = $this->_options();
				$categorie = Hash::get($options, 'Requestmanager.requestgroup_id.'.Hash::get($result, 'Requestmanager.requestgroup_id'));

				$this->set( compact( 'title', 'categorie', 'options' ) );

				$this->_search( $Model, $query );
			}
		}

		/**
		 * Action lorsque on envoi le formulaire d'ajout d'une requete
		 * Permet de sauvegarder la requete
		 * Lance une recherche
		 */
		public function newrequest() {
			$sessionCache = $this->Session->read('Requestmanager.last.request.data');
			if (empty($this->request->data) && $sessionCache) {
				$this->request->data = $sessionCache;
			}
			if ( !empty($this->request->data) ) {

				$query = $this->_requestDataIntoQuery( $this->request->data );
				$Model = ClassRegistry::init(Hash::get($this->request->data, 'Requestmanager.from'));

				$this->_search( $Model, $query );

				if ( isset($this->request->data['saveandsearch']) ) {
					unset($this->request->data['saveandsearch']); // Evite de sauvegarder une nouvelle ligne en cas de refresh de la page
					$this->request->data['Requestmanager']['model'] = $this->request->data['Requestmanager']['from'];
					$this->request->data['Requestmanager']['json'] = json_encode($query);
					$this->Requestmanager->create($this->request->data['Requestmanager']);

					// On évite les érreurs en cas de rechargement de la page (UNIQUE CONSTRAINT)
					try {
						$this->Requestmanager->save( null, array( 'atomic' => false ) );
					}
					catch (PDOException $e) {}
				}

				$this->Session->write('Requestmanager.last.request.data', $this->request->data);
			}
		}

		/**
		 * Transforme un request->data en requête Cakephp selon une synthaxe particulière (voir les preg_match)
		 *
		 * @param array $requestData
		 * @return array
		 */
		protected function _requestDataIntoQuery( $requestData ) {
			$requestData['Add'] += array( 'fields' => array(), 'conditions' => array(), 'order' => array() );
			$addFieldset = $this->_prepareAndExplode($requestData['Add']);
			unset($requestData['Add']);
			$data = Hash::flatten($requestData, '-');
			$Model = ClassRegistry::init(Hash::get($data, 'Requestmanager-from'));
			unset($data['Requestmanager']['from']);

			$request = array(
				'recursive' => -1,
				'contain' => false,
				'fields' => array(),
				'conditions' => array(),
				'order' => array()
			);

			$done = array();
			foreach ($data as $key => $value) {
				if ( $value === '' ) {
					continue;
				}

				// Savoir si c'est du field, du condition, du join ou du order
				switch ( true ) {
					// Fields
					case preg_match('/^[\w]+\-[\w]+\-data\-([\w]+)\-([\w]+)$/', $key, $matches):
						$request['fields'][] = $matches[1].'.'.$matches[2];
						break;

					// Conditions
					case preg_match('/^conditions\-(?:select\-){0,1}(?:text\-){0,1}[\w]+\-([\w]+)\-([\w]+)/', trim($key, '_'), $matches):
						$alias = $matches[1] === 'from' ? $Model->alias : $matches[1];
						$request['conditions'][$alias.'.'.$matches[2]][] = $value;
						break;

					// Joins
					case preg_match('/^join\-[\w]+\-([\w]+)/', $key, $matches):
						$request['joins'][] = ClassRegistry::init($matches[1])->join($value);
						break;

					case preg_match('/^joinscomplexe\-([\w]+)\-/', $key, $matches) && !in_array($matches[1], $done):
						$done[] = $matches[1];
						$request['joins'][] = array(
							'alias' => $data['joinscomplexe-'.$matches[1].'-alias'],
							'table' => $data['joinscomplexe-'.$matches[1].'-table'],
							'conditions' => $data['joinscomplexe-'.$matches[1].'-conditions'],
							'type' => $data['joinscomplexe-'.$matches[1].'-type'],
						);
						break;
				}
			}

			$request['fields'] = array_merge((array)$request['fields'], (array)$addFieldset['fields']);
			$request['conditions'] = array_merge((array)$request['conditions'], (array)$addFieldset['conditions']);
			$request['order'] = array_merge((array)$request['order'], (array)$addFieldset['order']);

			return $request;
		}

		/**
		 * Prépare les données des textarea pour les convertir en requête Cakephp
		 * Particulièrement utile pour le champs fields :
		 * Converti un String : "Monmodel1.monchamp, Monmodel2.monautrechamp"
		 * en array : array( "Monmodel1.monchamp", "Monmodel2.monautrechamp" )
		 *
		 * @param array $data
		 * @return type
		 */
		protected function _prepareAndExplode( $data ) {
			$data = array(
				'fields' => isset($data['fields']) ? $this->_explode($data['fields']) : array(),
				'conditions' => isset($data['conditions']) ? $this->_explode($data['conditions'], 'AND') : array(),
				'order' => isset($data['order']) ? $this->_explode($data['order']) : array(),
			);

			$newOrder = array();
			foreach ($data['order'] as $key => $value) {
				if (!is_string($value)) {
					continue;
				}

				if ( $pos = strpos(strtoupper($value), 'DESC') ) {
					$newOrder[trim(substr($value, 0, $pos))] = 'DESC';
				}
				elseif ( $pos = strpos(strtoupper($value), 'ASC') ) {
					$newOrder[trim(substr($value, 0, $pos))] = 'ASC';
				}
				else {
					$newOrder[$value] = 'ASC';
				}
			}

			$data['order'] = $newOrder;

			return $data;
		}

		/**
		 * Réalise un explode en préservant le contenu des parenthèses
		 *
		 * @param type $subdata
		 * @param type $delimiter
		 * @return type
		 */
		protected function _explode( $subdata, $delimiter = ',' ) {
			if ( $subdata === '' ) {
				return array();
			}

			$oldValue = $subdata;
			$regex = '/\(([^)]+)'.$delimiter.'([^)]+)\)/';
			$replaceBy = '($1__replace_key__$2)';
			$newValue = preg_replace($regex, $replaceBy, $subdata);
			while ($newValue !== $oldValue) {
				$oldValue = $newValue;
				$newValue = preg_replace($regex, $replaceBy, $newValue);
			}

			$fields = explode($delimiter, $newValue);

			$data = array();

			foreach($fields as $key => $value) {
				$data[$key] = trim(str_replace('__replace_key__', $delimiter, $value));
			}

			return $data;
		}

		/**
		 * Moteur de recherche, envoi results, fields et options à la vue
		 * Défini la vue en tant que search (pour affichage des résultats)
		 *
		 * @param type $Model
		 * @param type $query
		 */
		protected function _search( $Model, $query ) {
			$this->paginate = $query;

			$results = $this->paginate(
				$Model,
				array(),
				array(),
				false
			);

			$options = $this->_options();
			$fields = array();
			foreach ( (array)$query['fields'] as $value ) {
				$data = Hash::extract($results, '{n}.'.$value);
				$model_field = $this->_model_field($value);

				// On reformate le path pour eviter les problème en cas de champ custom
				$value = $model_field['model'] .'.'. $model_field['field'];

				if ( !isset($options[$model_field['model']]) ) {
					$options += ClassRegistry::init($model_field['model'])->enums();
				}

				if ( isset($data[0]) && preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', $data[0])) {
					$fields[$value] = array('type' => 'date');
				}
				else {
					$fields[$value] = array('type' => 'text');
				}
			}

			$this->set( compact( 'results', 'fields', 'options' ) );
			$this->view = 'search';
		}

		/**
		 * Permet d'optenir un json à partir d'un nom de model :
		 * {
		 *	'alias', // Alias du model
		 *	'fields', // Liste des champs de la table
		 *	'ids', // id de l'input au sens Cakephp : NomdumodelNomduchamp
		 *	'names', // name de l'input au sens Cakephp : data[Nomdumodel][nomduchamp]
		 *	'traductions', // Traduction depui le fichier po du model : __d( 'nomdumodel', 'Nomdumodel.nomduchamp' )
		 *	'enums', // Si le champ possède une contrainte de type validate, on liste les valeurs possible et leurs traductions
		 *	'joins' // merge des attributs belongsTo, hasOne et hasMany avec le with du hasAndBelongsToMany
		 * }
		 */
		public function ajax_get() {
			$Model = ClassRegistry::init( $this->request->data['model'] );

			$results = $Model->query( "SELECT column_name FROM information_schema.columns WHERE table_name = '".$Model->useTable."' AND table_schema = 'public'");
			$fields = array();
			$traductions = array();
			$ids = array();
			$names = array();
			foreach ( $results as $value ) {
				$fields[] = $value[0]['column_name'];
				$traductions[] = __d(strtolower($Model->alias), $Model->alias . '.' . $value[0]['column_name']);
				$ids[] = $Model->alias . Inflector::camelize($value[0]['column_name']);
				$names[] = 'data['.$Model->alias.']['.$value[0]['column_name'].']';
			}

			// pour les hasAndBelongsToMany, il faut faire des jointures type hasMany sur la table de jointure
			$joins = array_merge(
				array_keys($Model->belongsTo),
				array_keys($Model->hasOne),
				array_keys($Model->hasMany),
				Hash::extract($Model->hasAndBelongsToMany, '{s}.with')
			);

			sort($joins);

			$json = array(
				'alias' => $Model->alias,
				'fields' => $fields,
				'ids' => $ids,
				'names' => $names,
				'traductions' => $traductions,
				'enums' => $Model->enums(),
				'joins' => $joins,
				'echec' => false
			);

			$this->set( compact( 'json' ) );
			$this->layout = 'ajax';
			$this->render( '/Elements/json' );
		}

		/**
		 * Permet, à partir d'un nom de model et un nom de champs, d'avoir un échantillon de valeurs possible pour le champ concerné.
		 */
		public function ajax_list() {
			$Model = ClassRegistry::init( $this->request->data['alias'] );

			$results = $Model->find('all',
				array(
					'fields' => $this->request->data['field'],
					'contain' => false,
					'order' => $this->request->data['field'],
					'group' => $this->request->data['field'],
					'limit' => 100
				)
			);

			$enum = array();
			foreach ($results as $value) {
				$enum[] = Hash::get($value, $this->request->data['alias'].'.'.$this->request->data['field']);
			}

			$json = array(
				'enum' => $enum
			);

			$this->set( compact( 'json' ) );
			$this->layout = 'ajax';
			$this->render( '/Elements/json' );
		}

		/**
		 * Permet de vérifier la syntaxe de la requête SQL générée à l'aide du moteur de Postgresql
		 * Fait un Explain de la requête et capture l'érreur si elle existe.
		 */
		public function ajax_check() {
			$query = $this->_requestDataIntoQuery( $this->request->data );
			$Model = ClassRegistry::init(Hash::get($this->request->data, 'Requestmanager.from'));

			if ( !$Model->alias ) {
				exit;
			}

			$Dbo = $Model->getDataSource();
			$sql = $Model->sq( $query );
			$json = $Dbo->checkPostgresSqlSyntax( $sql );

			$this->set( compact( 'json' ) );
			$this->layout = 'ajax';
			$this->render( '/Elements/json' );
		}

		/**
		 * Permet de récupérer les données d'une requete précédement construite
		 */
		public function ajax_load( $id ) {
			$Dbo = $this->Requestmanager->getDataSource( $this->Requestmanager->useDbConfig );


			$result = $this->Requestmanager->find( 'first',
				array(
					'conditions' => array(
						'id' => $id
					)
				)
			);

			// Assurance d'avoir des conditions de jointure utilisable en javascript (import de requetes)
			$json = (array)json_decode(Hash::get($result, 'Requestmanager.json'), true);
			if (isset($json['joins'])) {
				foreach((array)$json['joins'] as $key => $value) {
					$json['joins'][$key]['conditions'] = $Dbo->conditions($value['conditions'], true, false);
				}
			}
			$result['Requestmanager']['json'] = json_encode($json);

			$json = Hash::get($result, 'Requestmanager');

			$this->set( compact( 'json' ) );
			$this->layout = 'ajax';
			$this->render( '/Elements/json' );
		}

		/**
		 * Permet de récupérer une jointure entre deux modèles
		 */
		public function ajax_getjointure() {
			$Model = ClassRegistry::init($this->request->data['modelName1']);
			$result = array();

			if (isset($Model->{$this->request->data['modelName2']})) {
				$result = ClassRegistry::init($this->request->data['modelName1'])->join($this->request->data['modelName2']);
				$result['conditions'] = implode(' AND ', (array)Hash::get($result, 'conditions'));
			}

			$this->set( 'json', $result );
			$this->layout = 'ajax';
			$this->render( '/Elements/json' );
		}

		/**
		 * Permet de récupérer une jointure entre deux modèles
		 */
		public function ajax_gettable() {
			$this->request->data['model'] =
				ClassRegistry::init(
					Inflector::camelize(
						Inflector::singularize(
							trim($this->request->data['table'], '"')
						)
					)
				)->alias
			;

			// Anti tentative d'injection sql
			if (!preg_match('/^[\w]+$/', trim($this->request->data['table'], '"'))) {
				$this->set( 'json', array('echec' => true) );
				$this->layout = 'ajax';
				$this->render( '/Elements/json' );
			}

			$results = $this->Requestmanager->query( "SELECT column_name FROM information_schema.columns WHERE table_name = '".trim($this->request->data['table'], '"')."' AND table_schema = 'public' LIMIT 1");

			if (!empty($results)) {
				return $this->ajax_get();
			} else {
				$this->set( 'json', array('echec' => true) );
				$this->layout = 'ajax';
				$this->render( '/Elements/json' );
			}
		}

		/**
		 * Liste des modeles qui possèdent une table (mise en cache)
		 * Liste des enregistrements existant
		 *
		 * @return array
		 */
		protected function _options() {
			if ( empty($this->options) ) {
				$options = array();
				$cache = Cache::read('RequestManager.options');

				if ( $cache === false ) {
					// Liste des modeles disponible
					foreach ( App::objects('model') as $modelName ) {
						App::uses( $modelName, 'Model' );
						$Reflection = new ReflectionClass( $modelName );
						if( $Reflection->isAbstract() === false ) {
							$Model = ClassRegistry::init( $modelName );

							if ( $Model->useTable ) {
								$options['Requestmanager']['modellist'][$Model->alias] = $Model->alias;
							}
						}
					}

					Cache::write('RequestManager.options', $options);
				}
				else {
					$options = $cache;
				}

				$this->options = $options;
			}
			else {
				$options = $this->options;
			}

			$options['Requestmanager']['requestgroup_id'] = $this->Requestmanager->Requestgroup->find('list',
				array(
					'conditions' => array( 'actif' => 1 ),
					'order' => 'name'
				)
			);

			$listNames = $this->Requestmanager->find('all',
				array(
					'fields' => array(
						'id',
						'name',
						'requestgroup_id'
					),
					'order' => 'name'
				)
			);

			// Construit des options avec titre pour le select
			foreach ($listNames as $value) {
				$groupName = Hash::get($options, 'Requestmanager.requestgroup_id.'.Hash::get($value, 'Requestmanager.requestgroup_id'));
				$options['Requestmanager']['grouped_name'][$groupName][Hash::get($value, 'Requestmanager.id')] = Hash::get($value, 'Requestmanager.name');
			}

			$options['Requestmanager']['name'] = $this->Requestmanager->find('list', array( 'order' => 'name' ) );

			return $options;
		}

		/**
		 * Liste des requêtes sous forme de parametrages
		 */
		public function savedindex() {
			$this->Requestmanager->Behaviors->attach( 'Occurences' );

            $querydata = $this->Requestmanager->qdOccurencesExists(
                array(
                    'fields' => $this->Requestmanager->fields(),
                    'order' => array( 'Requestmanager.name ASC' )
                )
            );

            $this->paginate = $querydata;
            $requestlist = $this->paginate('Requestmanager');

			$options = $this->_options();
            $this->set( compact('requestlist', 'options'));
		}

		public function edit( $id = null ) {
            // Retour à la liste en cas d'annulation
            if( isset( $this->request->data['Cancel'] ) ) {
                $this->redirect( array( 'controller' => 'requestsmanager', 'action' => 'savedindex' ) );
            }

			if( !empty( $this->request->data ) ) {
				// Si on arrive pas à décoder le json, c'est qu'il y a une erreur
				if ( json_decode(Hash::get($this->request->data, 'Requestmanager.json')) === null ) {
					$this->Flash->error( 'Erreur sur le format JSON' );
					$this->redirect( $this->referer() );
				}

				$this->Requestmanager->create( $this->request->data );
				$success = $this->Requestmanager->save( null, array( 'atomic' => false ) );

				if( $success ) {
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'savedindex' ) );
				}
				else {
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $this->Requestmanager->find(
					'first',
					array(
						'contain' => false,
						'conditions' => array( 'Requestmanager.id' => $id )
					)
				);
				$this->assert( !empty( $this->request->data ), 'error404' );
			}
			else{
				$this->request->data['Requestmanager']['actif'] = true;
			}

			$options = $this->_options();

			$this->set( compact( 'options' ) );
		}

		/**
		 * model_field prenant en compte les -> (CODE) AS "Monmodel__field"
		 *
		 * @params string
		 * @return array
		 */
		protected function _model_field( $path ) {
			preg_match('/(?: AS[ ]+"([\w]+)__([\w]+)")$|^["]*([\w]+)["]*.["]*([\w]+)["]*$/', $path, $matches);

			$modelName = Hash::get($matches, '1') ? Hash::get($matches, '1') : Hash::get($matches, '3');
			$fieldName = Hash::get($matches, '2') ? Hash::get($matches, '2') : Hash::get($matches, '4');

			return array(
				0 => $modelName,
				1 => $fieldName,
				'model' => $modelName,
				'field' => $fieldName,
			);
		}

		/**
		 * Prend un order de requête cakephp et renvoi un $order formaté pour passer au paginator (sans whitelist)
		 *
		 * @param array $query
		 * @return array
		 */
		protected function _cleanOrder( $query ) {
			if ( !isset($query['order']) ) {
				$query['order'] = array();
				return $query;
			}

			$result = array();

			// Permet de supprimer les éventuels niveaux en trop ex: 'order' => array( array( 'Monchamp' => 'DESC ) )
			foreach ( Hash::normalize( Hash::flatten( (array)$query['order'], '_@|@_' ) ) as $key => $value ) {
				$order[preg_replace('/.*_@\|@_(.*)/', '$1', $key)] = $value;
			}

			// On s'assure d'un format "field" => "value"
			foreach ( Hash::normalize($order) as $key => $value ) {
				if ( $key !== '' ) {
					$result[$key] = $value !== null ? $value : 'ASC';
				}
			}

			$query['order'] = $result;

			return $query;
		}

		/**
		 * Action de suppression
		 *
		 * @param integer $id
		 */
		public function delete($id) {
			if ($this->Requestmanager->delete($id)) {
				$this->Flash->success(__('Delete->success'));
			} else {
				$this->Flash->error(__('Delete->error'));
			}

			$this->redirect(array('action' => 'savedindex'));
		}
	}
?>
