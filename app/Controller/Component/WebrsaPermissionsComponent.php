<?php
	/**
	 * Code source de la classe WebrsaPermissionsComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('Component', 'Controller');
	App::uses('WebrsaSessionAclUtility', 'Utility');
	App::uses('SessionAclUtility', 'SessionAcl.Utility');

	/**
	 * La classe WebrsaPermissionsComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaPermissionsComponent extends Component
	{
		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Acl',
		);

		/**
		 * Permet d'avoir la totalitée des Acos de façon structuré en arbre
		 * Ne fonctionne que sur 3 niveaux (controllers/Module/action).
		 *
		 * @return array
		 */
		public function getAcosTree() {
			$cache = Cache::read(__METHOD__);

			if (!$cache) {
				$acos = ClassRegistry::init('Aco')->find('all', array('recursive' => -1, 'order' => 'Aco.lft'));

				$ids = array();
				$cache = array();
				foreach (Hash::extract($acos, '{n}.Aco') as $aco) {
					// controllers
					if ($aco['parent_id'] === null) {
						$cache[] = $aco['alias'];
						$masterId = $aco['id'];

					// Module
					} elseif ($aco['parent_id'] === $masterId) {
						$cache[] = $ids[$masterId].'/'.$aco['alias'];

					// action
					} else {
						$cache[] = $ids[$masterId].'/'.$ids[$aco['parent_id']].'/'.$aco['alias'];
					}

					$ids[$aco['id']] = $aco['alias'];
				}

				sort($cache);

				Cache::write(__METHOD__, $cache);
			}

			return $cache;
		}

		/**
		 * Retourne la liste des Acos utilisable par le départment configuré,
		 * en fonction de la configuration des différents modules.
		 *
		 * @see WebrsaPermissionsComponent::getAcosTreeByDepartement()
		 *
		 * @return array
		 */
		public function getAcosTreeByDepartement() {
			$Aco = ClassRegistry::init('Aco');
			$WebrsaAco = ClassRegistry::init('WebrsaAco');
			$cacheKey = $Aco->useDbConfig.'_'.__CLASS__.'_'.__FUNCTION__;

			$acos = Cache::read( $cacheKey );
			if( false === $acos ) {
				$acos = $this->getAcosTree();
				if( true !== (bool)Configure::read( 'Module.Permissions.all' ) ) {
					$acos = $WebrsaAco->filterByDepartement( $acos );
				}

				Cache::write( $cacheKey, $acos );
			}

			return $acos;
		}

		/**
		 * Complète la clé contenant les permissions pour l'ensemble des acos avec
		 * un droit refusé.
		 *
		 * @param array $data
		 * @param string $key
		 * @return array
		 */
		public function getCompletedPermissions( array $data, $key = 'Permission' ) {
			$acos = $this->getAcosTree();
			$default = array_combine( $acos, array_fill( 0, count( $acos ), -1 ) );
			$data[$key] = $data[$key] + $default;
			return $data;
		}

		/**
		 * Permet d'obtenir les permissions à partir d'un modèle et d'une clef étrangère
		 *
		 * @param Model $Model				User ou Group
		 * @param integer $foreign_key		id
		 * @return array					controllers/Module/action => true|false
		 */
		public function getPermissions(Model $Model, $foreign_key) {
			if ($Model->getDatasource() instanceof Postgres) {
				$permissions = WebrsaSessionAclUtility::fastPostrgresGetAll($Model, $foreign_key);
			} else {
				// @fixme dans la partie générique ci-dessous, lorsque les accès
				// des parents sont définis, on ne peut pas en inférer les droits
				// des enfants
				$Model->id = $foreign_key;

				$matches = null;
				$skip = false;
				$permissions = array();

				$tree = $this->getAcosTree();

				foreach ($tree as $path) {
					// root
					if (!strpos($path, '/')) {
						$permissions[$path] = $this->Acl->check($Model, $path);
						$skip = $permissions[$path]; // Si root est à true, tout les enfants le sont

					// Si l'accès au module est à true, les actions le sont également (réduit le temps de chargement)
					} elseif ($skip || (preg_match('/(.*\/.*)\/.*/', $path, $matches) && $permissions[$matches[1]])) {
						$permissions[$path] = true;

					// Dans les autres cas, on calcule les droits
					} else {
						$permissions[$path] = $this->Acl->check($Model, $path);
					}

					$this->log(sprintf('Les permissions pour "%s" sont "%s"', $path, $permissions[$path] ? 'accordé' : 'refusé'));
				}
			}

			return $permissions;
		}

		/**
		 * Permet d'obtenir les permissions à partir d'un modèle et d'une clef étrangère
		 * Ne prend pas en compte l'héritage des droits
		 *
		 * @param Model $Model				User ou Group
		 * @param integer $foreign_key		id
		 * @return array					controllers/Module/action => true|false
		 */
		public function getArosAcos(Model $Model, $foreign_key) {
			$queryAro = array(
				'recursive' => -1,
				'joins' => array(
					array(
						'type' => 'INNER',
						'table' => 'aros',
						'alias' => 'Aro',
						'conditions' => 'AroAco.aro_id = Aro.id',
					)
				),
				'conditions' => array(
					'Aro.model' => $Model->alias,
					'Aro.foreign_key' => $foreign_key,
					'AroAco._create !=' => 0,
				),
			);
			$perms = ClassRegistry::init('AroAco')->find('all', $queryAro);

			$access = array();
			foreach (Hash::extract($perms, '{n}.AroAco') as $perm) {
				$access[$this->getAcoPath($perm['aco_id'])] = trim($perm['_create']) === '1';
			}

			return $access;
		}

		/**
		 * Mémoire pour la fonction getAcoPath
		 *
		 * @var array
		 */
		public $acoPaths = array();

		/**
		 * Permet d'obtenir le chemin d'un aco de façon récursif
		 *
		 * @param integer $aco_id
		 * @return string				ex: controllers/Module/action
		 */
		public function getAcoPath($aco_id) {
			if (isset($this->acoPaths[$aco_id])) {
				$path = $this->acoPaths[$aco_id];
			} else {
				$Aco = ClassRegistry::init('Aco');

				$aco = $Aco->find('first', array('recursive' => -1, 'conditions' => array('Aco.id' => $aco_id)));

				if ($parent_id = Hash::get($aco, 'Aco.parent_id')) {
					$path = $this->getAcoPath($parent_id) . '/' . Hash::get($aco, 'Aco.alias');
				} else {
					$path = Hash::get($aco, 'Aco.alias');
				}

				$this->acoPaths[$aco_id] = $path;
			}

			return $path;
		}

		/**
		 * Met à jour les permissions d'un group ou d'un utilisateur et de tous
		 * ses enfants
		 *
		 * @param Model $Model Group ou User
		 * @param integer $foreign_key id du Model
		 * @param array $data Controller->request->data
		 * @return boolean Success
		 */
		public function updatePermissions(Model $Model, $foreign_key, $data) {
			$Model->id = $foreign_key;
			$success = true;

			foreach ((array)Hash::get($data, 'Permission') as $path => $value) {
				switch ($value) {
					case '1':
						$success = $success && $this->Acl->allow($Model, $path);
						break;
					case '0':
						$success = $success && $this->Acl->inherit($Model, $path);
						break;
					case '-1':
						$success = $success && $this->Acl->deny($Model, $path);
						break;
				}
			}

			return $success;
		}

		/**
		 * Met à jour les acos en fonction des controllers de l'application
		 * Effectue également un nettoyage comme Acl->recover() mais en plus rapide
		 */
		public function updateAcos() {
			WebrsaSessionAclUtility::initialize();
			WebrsaSessionAclUtility::updateAcos();
		}

		/**
		 * Permet d'obtenir les droits en 4 états
		 * 1 => oui, -1 => non, 10 => oui (hérité), -10 => non (hérité)
		 *
		 * @param Model $Model
		 * @param integer $foreign_key
		 * @param boolean $light
		 * @return array 'controllers/Module/action' => (int){-10, -1, 1, 10}
		 */
		public function getPermissionsHeritage(Model $Model, $foreign_key, $light = false) {
			$aros_acos = $this->getArosAcos($Model, $foreign_key);
			$keys_aros_acos = array_keys($aros_acos);
			$droits = $this->getPermissions($Model, $foreign_key);

			$perms = array();
			foreach ($droits as $key => $value) {
				$keyPermission = $light ? substr ($key, 12) : $key;
				if (in_array($key, $keys_aros_acos)) {
					$perms[$keyPermission] = $aros_acos[$key] ? 1 : -1;
				} else {
					$perms[$keyPermission] = $value ? 10 : -10;
				}
			}

			return $perms;
		}
	}