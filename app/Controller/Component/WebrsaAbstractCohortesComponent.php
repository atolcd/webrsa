<?php
	/**
	 * Code source de la classe WebrsaAbstractCohortesComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractMoteursComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaAbstractCohortesComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaAbstractCohortesComponent extends WebrsaAbstractMoteursComponent
	{
		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'Cohortes',
			'Flash',
			'WebrsaRecherches'
		);

		/**
		 * Surcharge des paramètres:
		 *  - cohorteKey: le préfixe des inputs de la cohorte
		 *  - dossierIdPath: chemin vers dossier_id revoyé en champs obligatoire ex: {n}.Foyer.dossier_id ou {n}.Dossier.id
		 *  - modelSave: modèle utilisé par $this->saveCohorte() pour sauvegarder les données
		 *
		 * @param array $params
		 * @return array
		 */
		protected function _params( array $params = array() ) {
			$Controller = $this->_Collection->getController();

			$defaults = array( 'modelRechercheName' => 'WebrsaCohorte'.$Controller->modelClass );
			$params = parent::_params( $params + $defaults );

			$params += array(
				'cohorteKey' => 'Cohorte',
				'dossierIdPath' => '{n}.Dossier.id',
				'modelSave' => $params['modelName']
			);

			return $params;
		}

		/**
		 * Surcharge de queryConditions ajoutant des conditions à un querydata
		 * afin d'exclure du jeu de résultats les dossiers lockés par d'autres
		 * utilisateurs.
		 *
		 * Si la clé "jetons" des $params vaut false, alors les jetons sur les
		 * dossiers ne seront pas pris en compte.
		 *
		 * @param array $query
		 * @param array $filters
		 * @param array $params
		 * @return array
		 */
		protected function _queryConditions( array $query, array $filters, array $params ) {
			$Controller = $this->_Collection->getController();

			$query = parent::_queryConditions( $query, $filters, $params );
			if( false !== Hash::get( $params, 'jetons' ) ) {
				$query = $Controller->Cohortes->qdConditions( $query );
			}

			return $query;
		}

		/**
		 * Ajoute des valeurs dans request->params, dans request->data et dans la session pour
		 * prendre en compte le changement de pages et le changement d'ordre d'affichage des résultats
		 *
		 * @param array $params
		 * @param array $paramsSave paramètres à envoyer à saveCohorte()
		 * @return boolean
		 */
		protected function _traitementCohorte( array $params, array $paramsSave ) {
			$Controller = $this->_Collection->getController();
			$success = null;

			$Controller->request->params['named'] += array(
				'page' => Hash::get($Controller->request->data, 'page' )
			);

			// INFO: on s'assure de ne pas ajouter de clés sort ou direction vides
			foreach( array( 'sort', 'direction' ) as $key ) {
				$value = (string)Hash::get($Controller->request->data, $key );
				if( $value !== '' ) {
					$Controller->request->params['named'][$key] = $value;
				}
			}

			// On retire la Cohorte en cas de changement de page
			$sessionKey = 'Page Check: '.$Controller->name.'_'.$Controller->action;
			$page = (int)Hash::get( $Controller->request->data, 'page' );
			$page = $page === 0 ? 1 : $page;

			if ( (int)$Controller->Session->read( $sessionKey ) !== $page ) {
				unset($Controller->request->data[$params['cohorteKey']]);
				$Controller->Session->write( $sessionKey, $page );
			}
			$Controller->request->data['page'] = $page;

			// Si un formulaire de cohorte est renvoyé, on le traite
			if( isset( $Controller->request->data[$params['cohorteKey']] ) ) {
				$dossiersIds = (array)Hash::extract(
					$Controller->request->data[$params['cohorteKey']], $params['dossierIdPath']
				);
				$Controller->Cohortes->get($dossiersIds);

				$success = $this->saveCohorte( $Controller->request->data[$params['cohorteKey']], $params, $paramsSave );

				if ( $success ) {
					$Controller->Cohortes->release($dossiersIds);
				}
			}

			return $success;
		}

		/**
		 * Utilise WebrsaAbstractRecherchesComponent et ajoute le traitement du formulaire d'une cohorte
		 *
		 * @param array $params
		 * @param array $paramsSave Utilisé pour la fonction $this->saveCohorte()
		 * @return type
		 */
		final public function cohorte( array $params = array(), array $paramsSave = array() ) { // FIXME: doubles paramètres ($paramsSave)
			$Controller = $this->_Collection->getController();
			$defaults = array( 'keys' => array( 'results.fields', 'results.innerTable' ) );
			$params = $this->_params( $params + $defaults );
			$this->_alwaysDo($params);

			// Récupération des options
			$options = $this->_options( $params );

			// Suppression des jetons en cas de changement de page
			// TODO: factoriser
			$Controller->Cohortes->clean();

			// Si la recherche doit être effectuée
			if( $this->_needsSearch( $params ) ) {
				// Initialisation de la recherche
				$this->_initializeSearch( $params );

				// Traitement des données renvoyées par la cohorte ?
				$success = $this->_traitementCohorte( $params, $paramsSave );

				// Récupération des valeurs du formulaire de recherche
				$filters = $this->_filters( $params );

				// Récupération du query
				$query = $this->_query( $filters, $params );

				// Exécution du query et assignation des résultats
				$Controller->{$params['modelName']}->forceVirtualFields = true;
				$query = $this->_fireBeforeSearch( $params, $query );
				$results = $this->Allocataires->paginate( $query, $params['modelName'] );
				$results = $this->_fireAfterSearch( $params, $results );

				//--------------------------------------------------------------
				// TODO: début factoriser
				// On pré-remplit le formulaire de cohorte
				if( $success !== false ) {
					$data = $Controller->{$params['modelRechercheName']}->prepareFormDataCohorte( $results, $params, $options );
					$Controller->request->data[$params['cohorteKey']] = $data;
				}

				// Jetons
				$dossiersIds = (array)Hash::extract($results, $params['dossierIdPath']);
				$Controller->Cohortes->get($dossiersIds);

				// On insert les élements du formulaire de cohorte dans le tableau de résultats
				$cohorteFields = $this->_formatFieldsForInsert($this->_getCohorteFields( $params ), $params);

				// On conserve les filtres de recherche en élements cachés dans le formulaire de cohorte
				$filterData =& $Controller->request->data[$params['searchKey']];
				$extraHiddenFields = array(
					$params['searchKey'] => $filterData,
					'page' => Hash::get($Controller->request->data, 'page' ),
					'sort' => Hash::get($Controller->request->data, 'sort' ),
					'direction' => Hash::get($Controller->request->data, 'direction' ),
					'limit' => Hash::get($Controller->request->data, 'limit' ),
				);

				$configuredCohorteParams = array(
					'format' => SearchProgressivePagination::format(
						!Hash::get( $Controller->request->data, 'Search.Pagination.nombre_total' )
					),
					'options' => $options,
					'extraHiddenFields' => $extraHiddenFields,
					'entityErrorPrefix' => 'Cohorte',
					'cohorteFields' => $cohorteFields,
					'view' => Configure::read($params['searchKeyPrefix'].'.'.$params['configurableQueryFieldsKey'].'.view')
				);

				$Controller->set( compact('cohorteFields', 'results', 'extraHiddenFields', 'configuredCohorteParams') );
				// TODO: fin factoriser
			}
			// Sinon
			else {
				// Récupération des valeurs par défaut des filtres
				$defaults = $this->_defaults( $params );

				// Assignation au formulaire
				$Controller->request->data = $defaults;

				// Si on doit automatiquement lancer la recherche, on met les filtres ar défaut dans l'URL
				if( $params['auto'] === true ) {
					return $this->_auto( $defaults, $params );
				}
			}

			//Orientation
			$Controller->loadModel ('Orientstruct');
			$options = array_merge ($options, $Controller->Orientstruct->enums());
			$Controller->loadModel ('Typeorient');
			$options['Orientstruct']['typeorient_id'] = $Controller->Typeorient->find('list', array ('recursive' => -1));

			// Assignation à la vue
			$configurableQueryParams = $params;
			$Controller->set( compact('options', 'configurableQueryParams') );
		}

		/**
		 * Sauvegarde des données de cohorte générique (à surcharger en cas de fonctionnement spécial)
		 * En lien avec modelSave->saveCohorte()
		 *
		 * @param array $datas
		 * @param array $paramsComponent
		 * @param array $paramsSave Params à passer a la sauvegarde
		 * @return boolean
		 */
		public function saveCohorte( array $datas, array $paramsComponent = array(), array $paramsSave = array() ) {
			$Controller = $this->_Collection->getController();
			$params = $this->_params( $paramsComponent );
			$user_id = $Controller->Session->read( 'Auth.User.id' );

			$Controller->loadModel( $params['modelSave'] ); // TODO: dans l'initialisation

			$Controller->{$params['modelSave']}->begin();
			$saved = (boolean)$Controller->{$params['modelRechercheName']}->saveCohorte( $datas, $paramsSave, $user_id );

			if ( $saved ) {
				$Controller->{$params['modelSave']}->commit();
				$this->Flash->success(__( 'Save->success' ));
			}
			else {
				$Controller->{$params['modelName']}->rollback();
				$this->Flash->error(__( 'Save->error' ));
			}

			return $saved;
		}

		/**
		 * Transforme des champs de type < Model.field >, en < data[Cohorte][][Model][field] >
		 * Destiné à être insérer avec ConfigurableQuery
		 *
		 * @param array $fields
		 * @param array $paramsComponent
		 * @return array
		 */
		protected function _formatFieldsForInsert( array $fields, array $paramsComponent = array() ) {
			$params = $this->_params( $paramsComponent );
			$formatedFields = array();
			$options = $this->_options($params);

			foreach ( Hash::normalize($fields) as $path => $value ) {
				if ( strpos($path, '.') ) {
					$model_field = model_field($path);
					$formatedKey = 'data[' . $params['cohorteKey'] . '][][' . $model_field[0] . '][' . $model_field[1] . ']';
					$value += array( 'options' => Hash::get( $options, $path ) );

					$formatedFields[$formatedKey] = $value;
				}
			}

			return $formatedFields;
		}

		/**
		 * Permet de récupérer les cohorteFields du modele de recherche et de lui appliquer les valeurs par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		protected function _getCohorteFields( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$params = $this->_params( $params );

			// Applique des valeurs par defaut aux champs cohorteFields
			$fields = array();
			foreach (Hash::normalize($Controller->{$params['modelRechercheName']}->cohorteFields) as $fieldName => $paramsField) {
				$paramsField = (array)$paramsField;
				$paramsField += array(
					'type' => 'select',
					'label' => false,
					'required' => false
				);

				switch ($paramsField['type']) {
					case 'hidden':
						$paramsField += array( 'hidden' => true ); break;
					case 'date':
						$paramsField += array(
							'dateFormat' => 'DMY',
							'minYear' => date('Y')-1,
							'maxYear' => date('Y')+1
						);
						break;
				}

				$fields[$fieldName] = $paramsField;
			}

			// Applique la configuration : donne une valeur à un champ et le cache
			$keyConf = implode('.', array($params['searchKeyPrefix'], $Controller->name, $Controller->action, 'cohorte', 'values'));
			foreach ((array)Configure::read($keyConf) as $fieldName => $value) {
				$fields[$fieldName]['value'] = $value;
				$fields[$fieldName]['type'] = 'hidden';
				$fields[$fieldName]['hidden'] = true;
			}

			return $fields;
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

			$config = array(
				'cohorte.options' => array( array( 'rule' => 'isarray', 'allowEmpty' => true ) ),
				'cohorte.values' => array( array( 'rule' => 'isarray', 'allowEmpty' => true ) ),
				'view' => array( array( 'rule' => 'boolean', 'allowEmpty' => true ) )
			);

			$result = parent::configureKeys($params);
			foreach( $config as $key => $value ) {
				$result[$this->_configureKey( $key, $params )] = $value;
			}

			return $result;
		}

		/**
		 * Vérification de la configuration des champs options de cohorte
		 *
		 * @return array
		 */
		public function checkOptionsCohorte( $params = array() ) {
			$params = $this->_params( $params );

			$result[$this->_configureKey('cohorte.options', $params)] = array( array( 'rule' => 'isarray', 'allowEmpty' => true ) );

			return $result;
		}

		/**
		 * Pour vérification de l'application : vérifi que la configuration dans cohorte.values est correcte
		 *
		 * @param array $params
		 * @return array
		 */
		public function checkHiddenCohorteValues( $params = array() ) {
			$Controller = $this->_Collection->getController();
			$params = $this->_params( $params );
			$success = true;
			$messages = array();

			foreach ((array)Configure::read($this->_configureKey('cohorte.values', $params)) as $path => $value) {
				$message = $this->_checkHiddenCohorteValueByPath($path, $value, $success, $params);

				if (!empty($message)) {
					$messages[] = $message;
				}
			}

			return array(
				'success' => $success,
				'message' => $success ? "Aucune erreur n'a été trouvée." : 'Des erreurs ont été trouvées!',
				'value' => implode("<br/>", $messages),
			);
		}

		/**
		 * Vérifi l'existance d'un champ et, dans le cas d'un foreign key,
		 * de l'existance de l'enregistrement cible.
		 *
		 * @param type $path
		 * @return string|null renvoi un message d'erreur si le champ n'est pas correct
		 */
		protected function _isNotAValidField($path, $value) {
			$model_field = model_field($path, false);

			// Syntaxe correcte
			if ($model_field === null) {
				return "La table et le champ de <b>{$path}</b> n'ont "
						. "pas été trouvé. Utilisez la syntaxe suivante : Modele.champ.";
			}

			// $path est de type hasAndBelongsToMany : ex: Monmodel.0_Monmodel
			$hasAndBelongsToMany = preg_match('/^([\w]+)\.(?:[\d]+.){0,1}([\w]+)$/', $path, $matches)
				&& $matches[1] === $matches[2];

			$Model = ClassRegistry::init($model_field[0]);

			try {
				$Model->find('first', array('contain' => false));
			} catch(Exception $e) {
				return 'La table du Model '.$model_field[0]." n'a pas été trouvée.";
			}

//			$this->_rebuildValidation($Model);

			$field = $hasAndBelongsToMany ? $Model->primaryKey : $model_field[1];

			// Table et champ existent
			if (!$hasAndBelongsToMany && !in_array($model_field[1], array_keys($Model->schema()))) {
				return "L'existance de <b>{$model_field[1]}</b> dans "
						. "le modèle <b>{$model_field[0]}</b> n'a pas été trouvé.";
			}

			// Existance de la valeur dans un hasAndBelongsToMany
			$conditions = array(
				'conditions' => array($Model->primaryKey => $value),
				'contain' => false
			);
			if ($hasAndBelongsToMany && (!is_numeric($value) || !$Model->find('first', $conditions))) {
				return "La valeur <b>{$value}</b> pour <b>{$path}</b> "
						. "ne se trouve pas dans <b>{$Model->alias}.{$Model->primaryKey}</b>.";
			}

			// Test de clef étrangère
			if ($value !== null) {
				// On regarde si la clef est une clef étrangère
				$test = $this->_isValidIfIsForeignKey( $Model, $path, $value );

				if (!$test['success']) {
					return $test['message'];
				}
			}

			return null;
		}

		/**
		 * Vérifi la possibilité
		 *
		 * @param string $path sous forme Model.field
		 * @param mixed $value valeur du champ
		 * @param boolean $success
		 * @param array $params
		 * @return string message d'erreur
		 */
		protected function _checkHiddenCohorteValueByPath($path, $value, &$success, $params) {
			$testField = $this->_isNotAValidField($path, $value);

			if ($testField) {
				$success = false;
				return $testField;
			}

			list($modelName, $field) = model_field($path);
			$Model = ClassRegistry::init($modelName);
			$Dbo = $Model->getDataSource();
			$Controller = $this->_Collection->getController();
			$ModelRecherche = $Controller->{$params['modelRechercheName']};
			$blackList = array();
			$message = null;
			$saveSuccess = true;

			// Condition qu'on peut ajouter dans le model de recherche pour
			// augmenter la cohérence des données choisies
			$conditionsSup = isset($ModelRecherche->checkHiddenCohorteValuesConditions)
				? $ModelRecherche->checkHiddenCohorteValuesConditions
				: array();

			while (count($blackList) < 10) {
				$data = $Model->find('first',
					array(
						'recursive' => -1,
						'contain' => false,
						'conditions' => array(
							!empty($blackList) ? array(
								$Model->alias.'.id NOT IN ('.implode(', ', $blackList).')'
							) : array(),
							$conditionsSup
						)
					)
				);

				// Si on a pas un enregistrement sain, inutile de continuer.
				if (empty($data)) {
					break;
				}

				// On teste avant tout l'enregistrement sans rien toucher
				// (vérification par le modèle de l'enregistrement)
				$saveSuccess = $this->_secureTestSave($Model, $data);

				// Sauvegarde échouée
				if ($saveSuccess === false) {
					$blackList[] = Hash::get($data, $Model->alias.'.id');
					continue;

				// Exception lors de la sauvegarde
				} elseif ($saveSuccess === null) {
					$message = 'Exception: '.$Dbo->lastError();
					break;
				}

				$data[$Model->alias][$field] = $value;

				$saveSuccess = $this->_secureTestSave($Model, $data);

				// Sauvegarde échouée
				if ($saveSuccess === false) {
					$errors = implode(', ', (array)Hash::get($Model->validationErrors, $field));
					$message = "La tentative d'insérer la valeur <b>{$value}</b> "
						. "dans <b>{$path}</b> a échoué : {$errors}";

				// Exception lors de la sauvegarde
				} elseif ($saveSuccess === null) {
					$message = 'Exception: '.$Dbo->lastError();
				}

				$success = $success && $saveSuccess;
				break;
			}

			return $message;
		}

		/**
		 * Permet de tester la sauvegarde sur un model sans risque d'exception
		 *
		 * @param Model $Model
		 * @param type $data
		 * @return boolean|null résultat de sauvegarde ou null en cas d'exception
		 */
		protected function _secureTestSave(Model $Model, $data) {
			$Model->begin();

			try {
				$success = $Model->save( $data, array( 'atomic' => false ) );
			} catch (Exception $e) {
				$success = null;
			}

			$Model->rollback();

			return $success;
		}

		/**
		 * Permet de reconstruire la validation uniquement avec les rêgles de la base de donnée
		 *
		 * @param Model $Model
		 * @return \WebrsaAbstractCohortesComponent
		 */
		protected function _rebuildValidation( Model $Model ) {
			$behaviors = array(
				'Validation2.Validation2Formattable' => null,
				'Validation2.Validation2RulesFieldtypes' => null,
				'Validation2.Validation2RulesComparison' => null
			);
			$autovalidate = false;
			foreach ( Hash::normalize( (array)$Model->actsAs ) as $behavior => $config ) {
				if ( preg_match( '/(autovalidate|validate|formattable)/i', $behavior, $matches ) ) {
					$behaviors[$behavior] = $config;
					$Model->Behaviors->detach( $behavior );

					if ( strtolower( $matches[1] ) === 'autovalidate' ) {
						$autovalidate = true;
					}
				}
			}
			$Model->validate = array();

			foreach ( $behaviors as $behavior => $config ) {
				$Model->Behaviors->attach( $behavior, $config );
			}
			if ( $autovalidate === false ) {
				$Model->Behaviors->attach( 'Validation2.Validation2Autovalidate' );
			}

			return $this;
		}

		/**
		 * Vérifi si le champ ciblé est une foreign key, et si c'est le cas, que la valeur soit bien numérique
		 * et existe bien dans la table concerné
		 *
		 * @param Model $Model Modele sur lequel il faut vérifier qu'il possède une foreign key correspondant à path
		 * @param type $path Model.field
		 * @param type $value valeur de la foreign key
		 * @return array array( 'success' => boolean, 'message' => string )
		 */
		protected function _isValidIfIsForeignKey( Model $Model, $path, $value ) {
			$result = array('success' => true);
			list(,$field) = explode('.', $path);

			foreach ((array)$Model->belongsTo as $modelName => $paramsBelongsTo) {
				$conditions = array(
					'conditions' => array($Model->$modelName->primaryKey => $value),
					'contain' => false
				);

				// Si la clef est une clef étrangère mais que la valeur n'est pas valide ou qu'il ne pointe pas sur un enregistrement...
				if (Hash::get($paramsBelongsTo, 'foreignKey') === $field) {
					if (!is_numeric($value) || !$Model->$modelName->find('first', $conditions)) {
						$result = array(
							'success' => false,
							'message' => "La valeur <b>{$value}</b> pour <b>{$path}</b> ne se trouve pas dans <b>{$Model->$modelName->alias}.{$Model->$modelName->primaryKey}</b>."
						);
					}
					break;
				}
			}

			return $result;
		}
	}
?>