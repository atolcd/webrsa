<?php
	/**
	 * Code source de la classe SynthesedroitsController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AppController', 'Controller');

	/**
	 * La classe SynthesedroitsController ...
	 *
	 * @package app.Controller
	 */
	class SynthesedroitsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Synthesedroits';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Acl',
			'Menu',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
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
			'index' => 'Groups::index',
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
		);

		/**
		 * Fait un export CSV des droits des groupes
		 */
		public function index() {
			$query = array(
				'fields' => 'Group.name',
				'contain' => false,
				'order' => 'Group.name'
			);
			$groups = $this->Group->find('list', $query);
			
			$actions = Hash::extract($this->Menu->menuCtrlActionAffichage(), '{n}.acosAlias');
			
			$droits = array();
			foreach ($groups as $group) {
				foreach ($actions as $action) {
					$droits[$group][$action] = $this->Acl->check($group, $action);
				}
			}
			
			$this->set(compact('groups', 'actions', 'droits'));
			$this->layout = null;
			$this->view = 'exportcsv';
		}
	}
?>
