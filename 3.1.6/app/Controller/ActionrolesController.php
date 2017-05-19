<?php
	/**
	 * Code source de la classe ActionrolesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

    App::import( 'Behaviors', 'Occurences' );
	/**
	 * La classe ActionrolesController ...
	 *
	 * @package app.Controller
	 */
	class ActionrolesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Actionroles';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default', 
			'Default2', 
			'Theme',
			'Xform', 
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Actionrole',
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
			'add' => 'create',
			'delete' => 'delete',
			'view' => 'read',
			'edit' => 'update',
		);

		/**
		 * Listing du contenu de la table
		 */
		public function index() {
			$this->Actionrole->Behaviors->attach( 'Occurences' );
  
            $querydata = $this->Actionrole->qdOccurencesExists(
                array(
                    'fields' => $this->Actionrole->fields(),
                    'order' => array( 'Actionrole.name ASC' )
                )
            );

            $this->paginate = $querydata;
            $actionroles = $this->paginate('Actionrole');
			$options = $this->_options();
            $this->set( compact('actionroles', 'options'));
		}

		/**
		 * Ajout d'une entrée
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, 'edit' ), $args );
		}

		/**
		 * Modification d'une entrée
		 * 
		 * @param integer $id
		 */
		public function edit( $id = null ) {
            // Retour à la liste en cas d'annulation
            if( isset( $this->request->data['Cancel'] ) ) {
                $this->redirect( array( 'controller' => 'actionroles', 'action' => 'index' ) );
            }

			if( !empty( $this->request->data ) ) {
				$this->Actionrole->create( $this->request->data );
				$success = $this->Actionrole->save();

				$this->_setFlashResult( 'Save', $success );
				if( $success ) {
					Cache::config('one day', array(
						'engine' => 'File',
						'duration' => '+1 day',
						'path' => CACHE,
						'prefix' => 'cake_oneday_'
					));
					Cache::clear(false, 'one day');
					$this->redirect( array( 'action' => 'index' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $this->Actionrole->find(
					'first',
					array(
						'contain' => false,
						'conditions' => array( 'Actionrole.id' => $id )
					)
				);
				$this->assert( !empty( $this->request->data ), 'error404' );
			}
			else{
				$this->request->data['Actionrole']['actif'] = true;
			}
			
			$options = $this->_options();
			
			$this->set( compact( 'options' ) );

			$this->view = 'edit';
		}

		/**
		 * Suppression d'une entrée
		 * 
		 * @param integer $id
		 */
		public function delete( $id ) {
			$this->Default->delete( $id );
		}

		/**
		 * Visualisation de la table
		 * 
		 * @param integer $id
		 */
		public function view( $id ) {
			$this->Default->view( $id );
		}
		
		/**
		 * Options pour la vue
		 * 
		 * @return array
		 */
		protected function _options() {
			$options['Actionrole']['role_id'] = $this->Actionrole->Role->find('list', array('order' => 'name', 'conditions' => array('actif' => 1)));
			$options['Actionrole']['categorieactionrole_id'] = $this->Actionrole->Categorieactionrole->find('list', array('order' => 'name'));
			
			return $options;
		}
	}
?>