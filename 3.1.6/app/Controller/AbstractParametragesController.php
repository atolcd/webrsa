<?php
	/**
	 * Code source de la classe AbstractParametragesController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AppController', 'Controller');

	/**
	 * La classe AbstractParametragesController permet de donner les actions de base pour les paramétrages classique :
	 * La table lié doit contenir les champs suivants : <id, name, actif>
	 *
	 * @package app.Controller
	 */
	abstract class AbstractParametragesController extends AppController
	{
		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'Gedooo.Gedooo',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Xform',
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
		 * NOTE : Malgrès que ce soit un Controller abstrait, il apparait dans les droits
		 * Il est désormais nécéssaire d'avoir un attribut $aucunDroit pour les enfants
		 * si on veux restreindre les droits d'accès
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'add',
			'delete',
			'edit',
			'index',
			'view',
		);
		
		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'delete' => 'delete',
			'edit' => 'update',
			'index' => 'read',
			'view' => 'read',
		);
		
		/**
		 * Pagination
		 */
		public function index() {
			$this->{$this->modelClass}->Behaviors->attach('Occurences');
  
            $querydata = $this->{$this->modelClass}->qdOccurencesExists(
                array(
                    'fields' => $this->{$this->modelClass}->fields(),
                    'order' => array($this->{$this->modelClass}->alias.'.name ASC')
               )
           );

            $this->paginate = $querydata;
            $this->set('datas', $this->paginate($this->{$this->modelClass}->alias));
		}

		/**
		 * Formulaire d'ajout
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array(array($this, '_add_edit'), $args);
		}

		/**
		 * Formulaire de modification
		 */
		public function edit() {
			$args = func_get_args();
			call_user_func_array(array($this, '_add_edit'), $args);
		}

		/**
		 * Méthode générique pour l'ajout / modification d'un enregistrement
		 * 
		 * @param integer $id
		 */
		protected function _add_edit($id = null) {
            // Retour à la liste en cas d'annulation
            if (isset($this->request->data['Cancel'])) {
                $this->redirect(array('action' => 'index'));
            }

			if (!empty($this->request->data)) {
				$this->{$this->modelClass}->create($this->request->data);
				$success = $this->{$this->modelClass}->save();

				$this->_setFlashResult('Save', $success);
				if ($success) {
					$this->redirect(array('action' => 'index'));
				}
			}
			else if ($this->action == 'edit') {
				$this->request->data = $this->{$this->modelClass}->find(
					'first',
					array(
						'contain' => false,
						'conditions' => array($this->{$this->modelClass}->alias.'.id' => $id)
					)
				);
				$this->assert(!empty($this->request->data), 'error404');
			}
			else {
				$this->request->data[$this->{$this->modelClass}->alias]['actif'] = true;
			}

			$this->view = 'add_edit';
		}

		/**
		 * Suppression
		 * 
		 * @param integer $id
		 */
		public function delete($id) {
			$this->Default->delete($id, true);
		}

		/**
		 * Vue
		 * 
		 * @param integer $id
		 */
		public function view($id) {
			$this->Default->view($id);
		}
	}
?>
