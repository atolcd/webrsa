<?php
	/**
	 * Code source de la classe Decisionspcgs66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Decisionspcgs66Controller ...
	 *
	 * @package app.Controller
	 */
	class Decisionspcgs66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Decisionspcgs66';

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
			'Default2',
			'Xform',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Decisionspcgs66:edit',
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
// 			$options = $this->Decisionpcg66->enums();
			$this->set( compact( 'options' ) );
		}

		public function index() {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'pdos', 'action' => 'index' ) );
			}

			$qdOccurences = $this->Decisionpcg66->qdOccurences();

			$decisionspcgs66 = $this->Decisionpcg66->find( 'all', $qdOccurences );

			$this->set('decisionspcgs66', $decisionspcgs66);
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

		protected function _add_edit( $decisionpcg66_id = null ) {
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			if( !empty( $this->request->data ) ) {
				if( $this->Decisionpcg66->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'decisionspcgs66', 'action' => 'index' ) );
				}
			}
			elseif ( $this->action == 'edit' ) {
				$decisionpcg66 = $this->Decisionpcg66->find(
					'first',
					array(
						'conditions' => array(
							'Decisionpcg66.id' => $decisionpcg66_id,
						)
					)
				);
				$this->request->data = $decisionpcg66;
			}
			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		public function delete( $decisionpcg66_id = null ) {
			// Vérification du format de la variable
			if( !valid_int( $decisionpcg66_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Recherche de la personne
			$decisionpcg66 = $this->Decisionpcg66->find(
				'first',
				array( 'conditions' => array( 'Decisionpcg66.id' => $decisionpcg66_id )
				)
			);

			// Mauvais paramètre
			if( empty( $decisionpcg66_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Tentative de suppression ... FIXME
			if( $this->Decisionpcg66->delete( array( 'Decisionpcg66.id' => $decisionpcg66_id ) ) ) {
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
				$this->redirect( array( 'controller' => 'decisionspcgs66', 'action' => 'index' ) );
			}
		}
	}

?>