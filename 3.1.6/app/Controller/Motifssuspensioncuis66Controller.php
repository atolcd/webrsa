<?php
	/**
	 * Code source de la classe Motifssuspensioncuis66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
		App::import( 'Behaviors', 'Occurences' );

	/**
	 * La classe Motifssuspensioncuis66Controller ...
	 *
	 * @package app.Controller
	 */
	class Motifssuspensioncuis66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Motifssuspensioncuis66';

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
			'Motifsuspensioncui66',
			'Option',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Motifssuspensioncuis66:edit',
			'view' => 'Motifssuspensioncuis66:index',
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
		 *   Ajout à la suite de l'utilisation des nouveaux helpers
		 *   - default.php
		 *   - theme.php
		 */

		public function index() {
			$this->Motifsuspensioncui66->Behaviors->attach( 'Occurences' );

			$querydata = $this->Motifsuspensioncui66->qdOccurencesExists(
				array(
					'fields' => $this->Motifsuspensioncui66->fields(),
					'order' => array( 'Motifsuspensioncui66.name ASC' )
				)
			);

			$this->paginate = $querydata;
			$motifssuspensioncuis66 = $this->paginate('Motifsuspensioncui66');
			$this->set( compact('motifssuspensioncuis66'));

		}

		/**
		 *
		 */

		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 */

		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 */

		protected function _add_edit( $id = null){
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'motifssuspensioncuis66', 'action' => 'index' ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Motifsuspensioncui66->create( $this->request->data );
				$success = $this->Motifsuspensioncui66->save();

				$this->_setFlashResult( 'Save', $success );
				if( $success ) {
					$this->redirect( array( 'action' => 'index' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $this->Motifsuspensioncui66->find(
					'first',
					array(
						'contain' => false,
						'conditions' => array( 'Motifsuspensioncui66.id' => $id )
					)
				);
				$this->assert( !empty( $this->request->data ), 'error404' );
			}
			else{
				$this->request->data['Motifsuspensioncui66']['actif'] = true;
			}

			$this->render( 'add_edit' );
		}

		/**
		 *
		 */

		public function delete( $id ) {
			$this->Default->delete( $id );
		}

		/**
		 *
		 */

		public function view( $id ) {
			$this->Default->view( $id );
		}
	}
?>