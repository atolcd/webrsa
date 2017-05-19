<?php
	/**
	 * Code source de la classe WebrsaAccessesComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe WebrsaAccessesComponent fournit des méthodes de contrôle d'accès métier
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaAccessesComponent extends Component
	{
		/**
		 * Nom du component
		 *
		 * @var string
		 */
		public $name = 'WebrsaAccesses';

		/**
		 * Funcion init() appelé ?
		 *
		 * @var boolean
		 */
		protected $_initialized = false;
		
		/**
		 * Assure le chargement des modèles et Utilitaires liés
		 *
		 * @param Controller $controller
		 * @return void
		 */
		public function initialize(Controller $controller) {
			$this->settings += array(
				'mainModelName' => null,
				'webrsaModelName' => null,
				'webrsaAccessName' => null,
				'parentModelName' => null,
			);

			$this->settings['mainModelName'] = $this->settings['mainModelName'] ?: self::controllerNameToModelName($controller->name);
			$this->settings['webrsaModelName'] = $this->settings['webrsaModelName'] ?: 'Webrsa'.$this->settings['mainModelName'];
			$this->settings['webrsaAccessName'] = $this->settings['webrsaAccessName'] ?: 'WebrsaAccess'.$controller->name;

			// Si le modèle principal n'est pas chargé
			if (!isset($controller->{$this->settings['mainModelName']})) {
				$controller->uses[] = $this->settings['mainModelName'];
			}

			// Si le modèle de logique n'est pas chargé
			if (!isset($controller->{$this->settings['webrsaModelName']})) {
				$controller->uses[] = $this->settings['webrsaModelName'];
			}

			// Si l'utilitaire n'est pas chargé...
			if (!class_exists($this->settings['webrsaAccessName'])) {
				App::uses($this->settings['webrsaAccessName'], 'Utility');
			}

			$this->settings['parentModelName'] = $this->settings['parentModelName'] ?: (
				!isset($controller->{$this->settings['mainModelName']}->belongsTo['Personne']) && isset($controller->{$this->settings['mainModelName']}->belongsTo['Foyer']) ? 'Foyer' : 'Personne'
			);

			// Vérifications
			$interfaces = class_implements($controller->{$this->settings['webrsaModelName']});
			if (!in_array('WebrsaLogicAccessInterface', $interfaces)) {
				trigger_error(
					sprintf("La classe %s doit impl&eacute;menter l'interface %s", $this->settings['webrsaModelName'], 'WebrsaLogicAccessInterface')
				);
			}

			$this->_initialized = true;
			
			return parent::initialize($controller);
		}

		/**
		 * Permet de modifier les modèles liés au component
		 *
		 * @param String $attr - Nom de l'attribut
		 * @param mixed $name - Model ou String
		 * @return \WebrsaAccessesComponent
		 */
		protected function _setAttr($attr, $name) {
			$Controller = $this->_Collection->getController();
			if ($name instanceof Model) {
				$this->settings[$attr] = $name->name;
			} elseif (isset($Controller->{$name})) {
				$this->settings[$attr] = $name;
			} else {
				$Controller->uses[] = $name;
				$this->settings[$attr] = $name;
			}
			return $this;
		}

		/**
		 * Permet le changement du modèle principal (singulier du nom du controller)
		 *
		 * @param mixed $modelName
		 * @return \WebrsaAccessesComponent
		 */
		public function setWebrsaModel($modelName) {
			return $this->_setAttr('WebrsaModel', $modelName);
		}

		/**
		 * Permet le changement du modèle principal (singulier du nom du controller)
		 *
		 * @param mixed $modelName
		 * @return \WebrsaAccessesComponent
		 */
		public function setMainModel($modelName) {
			return $this->_setAttr('mainModelName', $modelName);
		}

		/**
		 * Assure l'initialisation du component
		 *
		 * @return void
		 */
		public function init() {
			return $this->_initialized ?: $this->initialize($this->_Collection->getController());
		}

		/**
		 * Fait appel à WebrsaAccess<i>Nomducontroller</i> pour vérifier les droits
		 * d'accès à une action en fonction d'un enregistrement
		 *
		 * @param integer $id			- Id de l'enregistrement si il existe
		 *								  Sera envoyé à Webrsa<i>Nomdumodel</i>::getDataForAccess
		 *
		 * @param integer $parent_id		- Le plus souvent : personne_id ou foyer_id	si disponnible (nécéssaire si $id = null)
		 *								  Sera envoyé à Webrsa<i>Nomdumodel</i>::getParamsForAccess
		 *
		 * @param array $params			- Paramètres à envoyer à WebrsaAccess<i>Nomducontroller</i>
		 *
		 * @return void
		 * @throws Error403Exception
		 * @throws Error404Exception
		 */
		public function check($id = null, $parent_id = null, array $params = array()) {
			if (($id !== null && !self::_validId($id)) || ($parent_id !== null && !self::_validId($parent_id))) {
				throw new Error404Exception();
			}

			$Controller = $this->_Collection->getController();

			$this->init();

			if (!isset($Controller->{$Controller->{$this->settings['mainModelName']}->alias})) {
				trigger_error(sprintf("Le controller '%s' doit avoir la valeur '%s' dans l'attribut 'uses'",
					$Controller->name, $Controller->{$this->settings['mainModelName']}->alias)
				);
			}

			$record = $this->_getRecord($id);
			$actionsParams = call_user_func(
				array($this->settings['webrsaAccessName'], 'getActionParamsList'),
				$Controller->action,
				$params
			);
			$paramsAccess = $Controller->{$this->settings['webrsaModelName']}->getParamsForAccess(
				$this->_parentId($id, $record, $parent_id), $actionsParams
			);

			if ($this->_haveAccess($record, $paramsAccess) === false) {
				throw new Error403Exception(
					__("Exception::access_denied",
						$Controller->name,
						$Controller->action,
						$Controller->Session->read('Auth.User.username')
					)
				);
			}
		}

		/**
		 * Permet d'obtenir le nécéssaire pour l'index d'un module (données + accès)
		 * <strong>Attention</strong> : fait un set de la variable <i>ajoutPossible</i>
		 *
		 * @param type $parent_id - personne_id ou foyer_id selon le cas
		 * @param array $query - Query utilisé pour obtenir l'index
		 * @return array - Liste des enregistrements pour un index avec règles d'accès métier
		 * @throws Error404Exception
		 */
		public function getIndexRecords($parent_id, array $query = array()) {
			if (!self::_validId($parent_id)) {
				throw new Error404Exception();
			}

			$Controller = $this->_Collection->getController();

			$queryCompleted = $Controller->{$this->settings['webrsaModelName']}->completeVirtualFieldsForAccess($query);
			$paramsActions = call_user_func(array($this->settings['webrsaAccessName'], 'getParamsList'));
			$paramsAccess = $Controller->{$this->settings['webrsaModelName']}->getParamsForAccess($parent_id, $paramsActions);

			$Controller->set('ajoutPossible', Hash::get($paramsAccess, 'ajoutPossible') !== false);

			return call_user_func(
				array($this->settings['webrsaAccessName'], 'accesses'),
				$Controller->{$this->settings['mainModelName']}->find('all', $queryCompleted),
				$paramsAccess
			);
		}

		/**
		 * Vérifi qu'un ID est un entier positif de base 10
		 *
		 * @param integer $id
		 * @return boolean
		 */
		protected static function _validId($id) {
			return is_numeric($id) && (integer)$id > 0 && preg_match('/^[\d]+$/', (string)$id);
		}

		/**
		 * Appel de la fonction check sur l'utilitaire de logique d'accès métier lié au Controller
		 *
		 * @param array $record
		 * @param array $paramsAccess
		 * @return boolean
		 */
		protected function _haveAccess(array $record, array $paramsAccess) {
			$Controller = $this->_Collection->getController();

			return call_user_func(
				array($this->settings['webrsaAccessName'], 'check'),
				$Controller->name,
				$Controller->action,
				$record,
				$paramsAccess
			);
		}

		/**
		 * Permet d'obtenir l'enregistrement lié à l'id donné
		 *
		 * @param integer $id
		 * @return array
		 */
		protected function _getRecord($id) {
			$Controller = $this->_Collection->getController();

			$record = array();
			if ($id !== null) {
				$records = $Controller->{$this->settings['webrsaModelName']}->getDataForAccess(
					array($Controller->{$this->settings['mainModelName']}->alias.'.'.$Controller->{$Controller->{$this->settings['mainModelName']}->alias}->primaryKey => $id),
					array('controller' => $Controller->name, 'action' => $Controller->action)
				);
				$record = end($records);
			}

			return (array)$record;
		}

		/**
		 * Permet d'obtenir un id à partir de différentes sources
		 *
		 * @param integer $id
		 * @param array $record
		 * @param integer $parent_id - Le plus souvent : personne_id ou foyer_id
		 * @return integer
		 */
		protected function _parentId($id, array $record, $parent_id = null) {
			$Controller = $this->_Collection->getController();

			if ($parent_id !== null) {
				$result = $parent_id;
			} else {
				$isLinkedToParent = property_exists($Controller->{$this->settings['mainModelName']}, 'belongsTo')
					&& isset($Controller->{$this->settings['mainModelName']}->belongsTo[$this->settings['parentModelName']]);
				$foreignKey = $isLinkedToParent
					? $Controller->{$this->settings['mainModelName']}->belongsTo[$this->settings['parentModelName']]['foreignKey']
					: Inflector::underscore($this->settings['parentModelName']).'_id'
				;
				$parentPrimarykey = $isLinkedToParent
					? $Controller->{$this->settings['mainModelName']}->{$this->settings['parentModelName']}->primaryKey
					: 'id'
				;
				$methodName = Inflector::underscore($this->settings['parentModelName']).'Id';

				$result = Hash::get($record, $Controller->{$this->settings['mainModelName']}->alias.'.'.$foreignKey)
					?: Hash::get($record, $this->settings['parentModelName'].'.'.$parentPrimarykey
				);

				if ($result === null) {
					if ($Controller->{$this->settings['mainModelName']}->Behaviors->attached('Allocatairelie') || method_exists($Controller->{$this->settings['mainModelName']}, $methodName)) {
						$result = $Controller->{$this->settings['mainModelName']}->{$methodName}($id);
					} else {
						trigger_error(sprintf("Field: %s.%s n'existe pas dans %s::getDataForAccess",
							$this->settings['parentModelName'], $parentPrimarykey, $Controller->{$this->settings['webrsaModelName']}->name));
						exit;
					}
				}
			}

			return $result;
		}

		/**
		 * Renvoi une chaine en Camelcase pluriel en Camelcase singulier
		 *
		 * @param String $controllerName
		 * @return String
		 */
		protected static function controllerNameToModelName($controllerName) {
			return Inflector::camelize(Inflector::singularize(Inflector::underscore($controllerName)));
		}
	}
?>