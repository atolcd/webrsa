<?php
	/**
	 * Code source de la classe Dashboards.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AppController', 'Controller');

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
			'indexparams' => 'read',
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
				'contain' => array('Actionrole' => array('Categorieactionrole')),
				'conditions' => array(
					'Role.actif' => 1,
					'RoleUser.user_id' => $this->Session->read('Auth.User.id'),
				)
			);
			$roles = $this->Role->find('all', $query);
			
			$this->set('roles', $this->Dashboard->addCounts($roles));
		}
		
		/**
		 * Parametrages
		 */
		public function indexparams(){
			
		}
		
		/**
		 * Supprime le cache pour un role_id donné
		 * 
		 * @param integer $id_reset - role_id
		 */
		public function reset_cache($id_reset) {
			Cache::config('one day', array(
				'engine' => 'File',
				'duration' => '+1 day',
				'path' => CACHE,
				'prefix' => 'cake_oneday_'
			));
			
			// On vérifi que l'utilisateur possède le role avant reset
			$query = array(
				'fields' => 'RoleUser.id',
				'conditions' => array(
					'RoleUser.user_id' => $this->Session->read('Auth.User.id'),
					'RoleUser.role_id' => $id_reset
				)
			);
			if ($this->Role->RoleUser->find('first', $query)) {
				$keyCache = 'role_'.$id_reset;
				Cache::delete($keyCache, 'one day');
				$this->Session->setFlash('Calcul du nombre de résultats effectué', 'flash/success');
			} else {
				$this->Session->setFlash('Le calcul vous a été refusé', 'flash/error');
			}
			
			$this->redirect(array('action' => 'index'));
		}
	}
?>
