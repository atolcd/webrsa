<?php
	/**
	 * Code source de la classe Questionspcgs66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Questionspcgs66Controller ...
	 *
	 * @package app.Controller
	 */
	class Questionspcgs66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Questionspcgs66';

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
			'add' => 'Questionspcgs66:edit',
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
			$options = $this->Questionpcg66->enums();
			$options['Decisionpcg66'] = $this->Questionpcg66->Decisionpcg66->find( 'list' );
			$options['Compofoyerpcg66'] = $this->Questionpcg66->Compofoyerpcg66->find( 'list' );
			$this->set( compact( 'options' ) );
		}

		public function index() {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'pdos', 'action' => 'index' ) );
			}

			$questionspcgs66 = $this->Questionpcg66->find(
				'all',
				array(
					'contain' => array(
						'Decisionpcg66',
						'Compofoyerpcg66'
					),
					'order' => array( 'Questionpcg66.id ASC' )
				)
			);
			$this->set('questionspcgs66', $questionspcgs66);

			$compteurs = array(
				'Decisionpcg66' => $this->Questionpcg66->Decisionpcg66->find( 'count' ),
				'Compofoyerpcg66' => $this->Questionpcg66->Compofoyerpcg66->find( 'count' )
			);
			$this->set( compact( 'compteurs' ) );

			$this->_setOptions();
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

		protected function _add_edit( $questionpcg66_id = null ) {
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			if( !empty( $this->request->data ) ) {
				if( $this->Questionpcg66->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'questionspcgs66', 'action' => 'index' ) );
				}
			}
			elseif ( $this->action == 'edit' ) {
				$questionpcg66 = $this->Questionpcg66->find(
					'first',
					array(
						'conditions' => array(
							'Questionpcg66.id' => $questionpcg66_id,
						)
					)
				);
				$this->request->data = $questionpcg66;
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
				$this->redirect( array( 'controller' => 'decisionspdos', 'action' => 'index' ) );
			}
		}
	}

?>