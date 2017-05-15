<?php
	/**
	 * Code source de la classe ValeurstagsController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AppController', 'Controller');

	/**
	 * La classe ValeurstagsController ...
	 *
	 * @package app.Controller
	 */
	class ValeurstagsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Valeurstags';

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
			'Valeurtag',
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
			'add' => 'create',
			'delete' => 'delete',
			'edit' => 'update',
			'index' => 'read',
			'view' => 'read',
		);

		/**
		 * Listing du contenu de la table
		 */
		public function index() {
			$this->Valeurtag->Behaviors->attach( 'Occurences' );
  
            $querydata = $this->Valeurtag->qdOccurencesExists(
                array(
                    'fields' => $this->Valeurtag->fields(),
                    'order' => array( 'Valeurtag.name ASC' )
                )
            );

            $this->paginate = $querydata;
            $valeurstags = $this->paginate('Valeurtag');
			$options = $this->_options();
            $this->set( compact('valeurstags', 'options'));
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
                $this->redirect( array( 'controller' => 'valeurstags', 'action' => 'index' ) );
            }

			if( !empty( $this->request->data ) ) {
				$this->Valeurtag->create( $this->request->data );
				$success = $this->Valeurtag->save();

				$this->_setFlashResult( 'Save', $success );
				if( $success ) {
					$this->redirect( array( 'action' => 'index' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $this->Valeurtag->find(
					'first',
					array(
						'contain' => false,
						'conditions' => array( 'Valeurtag.id' => $id )
					)
				);
				$this->assert( !empty( $this->request->data ), 'error404' );
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
			$options['Valeurtag']['categorietag_id'] = $this->Valeurtag->Categorietag->find('list');
			
			return $options;
		}
	}
?>
