<?php	
	/**
	 * Code source de la classe Codesromemetiersdsps66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Codesromemetiersdsps66Controller ...
	 *
	 * @package app.Controller
	 */
	class Codesromemetiersdsps66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Codesromemetiersdsps66';

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
			'Default',
			'Default2',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Codesromemetiersdsps66:edit',
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
		);

		protected function _setOptions() {
			$options['Coderomesecteurdsp66'] = $this->Coderomemetierdsp66->Coderomesecteurdsp66->find(
				'list',
				array(
					'contain' => false,
					'order' => array( 'Coderomesecteurdsp66.code' )
				)
			);
			$this->set( compact( 'options' ) );
		}

		public function index() {
			$this->paginate = array(
				'fields' => array(
					'Coderomemetierdsp66.code',
					'Coderomemetierdsp66.name',
					$this->Coderomemetierdsp66->Coderomesecteurdsp66->sqVirtualField( 'intitule' )
				),
				'contain' => array(
					'Coderomesecteurdsp66'
				),
				'limit' => 10
			);
			$this->_setOptions();
			$this->set( 'codesromemetiersdsps66', $this->paginate( $this->Coderomemetierdsp66 ) );
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

		protected function _add_edit( $id = null ) {
			if( !empty( $this->request->data ) ) {
				$this->Coderomemetierdsp66->create( $this->request->data );
				$success = $this->Coderomemetierdsp66->save();

				$this->_setFlashResult( 'Save', $success );
				if( $success ) {
					$this->redirect( array( 'action' => 'index' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $this->Coderomemetierdsp66->find(
					'first',
					array(
						'contain' => array(
							'Coderomesecteurdsp66'
						),
						'conditions' => array( 'Coderomemetierdsp66.id' => $id )
					)
				);
				$this->assert( !empty( $this->request->data ), 'error404' );
			}

			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		/**
		*
		*/

		public function delete( $id ) {
			$success = $this->Coderomemetierdsp66->delete( $id );
			$this->_setFlashResult( 'Delete', $success );
			$this->redirect( array( 'action' => 'index' ) );
		}
	}
?>