<?php
	/**
	 * Code source de la classe AccessesController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe AccessesController ...
	 *
	 * @package app.Controller
	 */
	class AccessesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Accesses';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Acl',
			'Droits',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
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
			'Group',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'setbygroups' => 'Groups::index',
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajax_getform',
		);
		
		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'ajax_getform' => 'read',
			'setbygroups' => 'read',
		);
		
		/**
		 * Liste des controllers qu'il ne faut pas traiter
		 */
		public $ignoreList = array(
			'App', 'Pages', 'AbstractParametrages'
		);
		
		/**
		 * Permet d'attribuer ou pas des accès à un controller/action pour plusieurs groupes à la fois
		 */
		public function setbygroups() {
			$departement = (integer)Configure::read('Cg.departement');
			$matches = null;

			// Enregistrement
			if (isset($this->request->data['controller'])) {
				$controller = $this->request->data['controller'];
				unset($this->request->data['controller']);
				
				$this->Group->begin();
				$success = true;
				foreach ($this->request->data as $action => $values) {
					$acosAlias = $action === 'module' ? 'Module:'.$controller : $controller.':'.$action;
					foreach ($values as $group_id => $allow) {
						$this->Group->id = $group_id;
						
						// Mise à jour uniquement en cas de changement
						if ($this->Acl->check($this->Group, $acosAlias) !== (boolean)$allow || $group_id == 1) {
							$success = $success && $this->_updateGroup($group_id, $acosAlias, $allow ? 'allow' : 'deny');
						}
					}
				}
				
				if ($success) {
					$this->Group->commit();
					$this->Session->setFlash(sprintf('Enregistrement des droits sur <b>%s</b> effectué', $controller), 'flash/success');
				} else {
					$this->Session->setFlash(
						sprintf('Un problème est survenu lors de l\'enregistrement des droits sur <b>%s</b>', $controller), 'flash/error'
					);
				}
			}
			
			// On récupère la liste des controllers pour les options du select
			$controllers = array();
			foreach (App::objects('controller') as $controllerName) {
				App::uses($controllerName, 'Controller');
				$Reflection = new ReflectionClass($controllerName);
				if ($Reflection->isAbstract() || in_array($controllerName, array('AppController', 'Controller'))) {
					continue;
				}
				
				$controllerNameShort = preg_replace('/Controller$/', '', $controllerName);
				
				// S'il n'y a pas d'actions qui nécéssite des droits, on n'affiche pas le controller
				$actions = $this->Droits->listeActionsControleur($controllerNameShort);
				if (empty($actions)) {
					continue;
				}
				
				$notMyDepartement = preg_match('/[\d]+$/', $controllerNameShort, $matches) && (integer)$matches[0] !== $departement;
				if ($notMyDepartement || in_array($controllerName, $this->ignoreList)) {
					continue;
				}
				
				$traduction = __d('droit', 'Module:'.$controllerNameShort);
				$controllers[$controllerNameShort] = $traduction !== 'Module:'.$controllerNameShort 
					? $controllerNameShort.' - '.$traduction
					: $controllerNameShort
				;
			}
			$this->set('controllers', $controllers);
		}
		
		/**
		 * Permet d'obtenir le tableau avec les cases à cocher Groupe/action
		 */
		public function ajax_getform() {
			$controllerNameShort = $this->request->data['controller'];
			if (empty($controllerNameShort)) {
				exit;
			}
			$actions = $this->Droits->listeActionsControleur($controllerNameShort);
			$groups = $this->Group->find('list',
				array(
					'joins' => array(
						array(
							'type' => 'INNER',
							'table' => 'aros',
							'alias' => 'Aro',
							'conditions' => array(
								'Aro.model' => 'Group',
								'Aro.foreign_key = Group.id'
							)
						)
					),
					'order' => 'Group.name'
				)
			);
			
			$isDefined = (boolean)$this->Acl->Aco->find(
				'first', array('conditions' => array('alias' => 'Module:'.$controllerNameShort), 'recursive' => -1)
			);
			
			// On créer les Acos si besoin
			if (!$isDefined) {
				$maxRght = Hash::get(
					$this->Acl->Aco->find('first',
						array(
							'fields' => 'rght',
							'recursive' => -1,
							'order' => array('rght' => 'DESC'),
							'limit' => 1
						)
					),
					'Aco.rght'
				);
				
				$this->Acl->Aco->create(
					array(
						'foreign_key' => 0,
						'parent_id' => 0,
						'alias' => 'Module:'.$controllerNameShort,
						'lft' => $maxRght + 1,
						'rght' => $maxRght + ((count($actions) + 1) *2),
					)
				);
				$this->Acl->Aco->save();
				$parent_id = $this->Acl->Aco->getLastInsertId();
				
				$lft = $maxRght + 2;
				
				foreach ($actions as $action) {
					$this->Acl->Aco->create(
						array(
							'foreign_key' => 0,
							'parent_id' => $parent_id,
							'alias' => $controllerNameShort.':'.$action,
							'lft' => $lft,
							'rght' => $lft +1
						)
					);
					$lft += 2;
					$this->Acl->Aco->save();
				}
			}
			
			// Chargement des données dans data
			foreach ($actions as $action) {
				$data[$action] = array();
				$acosAlias = $controllerNameShort.':'.$action;

				foreach ($groups as $group_id => $name) {
					$data[$action][$group_id] = $isDefined ? $this->Acl->check($name, $acosAlias) : false;
				}
			}

			$module = array();
			foreach ($groups as $group_id => $name) {
				$module[$group_id] = $isDefined ? $this->Acl->check($name, 'Module:'.$controllerNameShort) : false;
			}
			
			$this->set(compact('actions', 'groups', 'data', 'module', 'controllerNameShort'));
			$this->layout = 'ajax';
		}
		
		/**
		 * Met à jour un groupe et tout ses utilisateurs
		 * N'utilise pas la notion de parent_id pour éviter les ordres contraire
		 * 
		 * @param type $group_id
		 * @param type $acosAlias
		 * @param type $method
		 */
		protected function _updateGroup($group_id, $acosAlias, $method) {
			$query = array(
				'fields' => array('User.id'),
				'joins' => array(
					array(
						'type' => 'INNER',
						'table' => 'aros',
						'alias' => 'Aro',
						'conditions' => array(
							'model' => 'Utilisateur',
							'foreign_key = User.id'
						)
					)
				),
				'conditions' => array('User.group_id' => $group_id),
				'contain' => false
			);
			
			$this->Group->id = $group_id;
			$success = $this->Acl->{$method}($this->Group, $acosAlias);
			
			foreach ((array)Hash::extract((array)$this->Group->User->find('all', $query), '{n}.User.id') as $user_id) {
				$success = $success && $this->Acl->{$method}(array('model' => 'Utilisateur', 'foreign_key' => $user_id), $acosAlias);
			}
			
			return $success;
		}
	}
?>
