<?php
	/**
	 * Code source de la classe Dashboards.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Dashboards ...
	 *
	 * @package app.Controller
	 */
	class DashboardsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Dashboards';

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
			'Ajax2' => array(
				'className' => 'Prototype.PrototypeAjax',
				'useBuffer' => false
			),
			'Cake1xLegacy.Ajax',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Fileuploader',
			'Observer' => array(
				'className' => 'Prototype.PrototypeObserver',
				'useBuffer' => true
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Dashboard',
			'Role',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(

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
			'index' => 'read',
			'reset_cache' => 'read',
		);

		/**
		 * Action par défaut
		 */
		public function index() {
			$query = array(
				'joins' => array(
					$this->Role->join('RoleUser'),
				),
				'contain' => array(
					'Actionrole' => array(
						'Actionroleresultuser' => array(
							'conditions' => array(
								'Actionroleresultuser.user_id' => $this->Session->read('Auth.User.id')
							)
						),
						'Categorieactionrole'
					)
				),
				'conditions' => array(
					'Role.actif' => 1,
					'RoleUser.user_id' => $this->Session->read('Auth.User.id'),
				)
			);
			$roles = $this->Role->find('all', $query);
			$this->set(compact('roles'));
		}

		/**
		 * Mise à jour du cache pour un role_id donné.
		 *
		 * @param integer $role_id
		 */
		public function reset_cache($role_id) {
			// On vérifie que l'utilisateur possède le role avant reset
			$query = array(
				'conditions' => array(
					'RoleUser.user_id' => $this->Session->read('Auth.User.id'),
					'RoleUser.role_id' => $role_id
				),
				'contain' => array(
					'Role' => array(
						'Actionrole' => array(
							'Actionroleresultuser'
						)
					)
				)
			);
			$result = $this->Role->RoleUser->find('first', $query);

			if (false === empty($result)) {
				$this->Role->begin();
				$success = true;

				foreach($result['Role']['Actionrole'] as $actionrole) {
					$success = $this->Role->Actionrole->Actionroleresultuser->refresh(
						$actionrole['id'],
						$this->Session->read('Auth.User.id')
					) && $success;
				}
				if(true === $success) {
					$this->Role->commit();
					$this->Flash->success( 'Calcul du nombre de résultats effectué' );
				}
				else {
					$this->Role->rollback();
					$this->Flash->error( 'Erreur(s) lors du calcul du nombre de résultats' );
				}
			} else {
				$this->Flash->error( 'Le calcul vous a été refusé' );
			}

			$this->redirect(array('action' => 'index'));
		}
	}
?>
