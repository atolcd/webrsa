<?php
	/**
	 * Code source de la classe DecisionspdosController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe DecisionspdosController ...
	 *
	 * @package app.Controller
	 */
	class DecisionspdosController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Decisionspdos';

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
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Decisionpdo',
			'Option',
			'Propopdo',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Decisionspdos:edit',
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
			$options = $this->Decisionpdo->enums();
			$this->set( 'decision_ci', ClassRegistry::init('Contratinsertion')->enum('decision_ci') );
			$this->set( compact( 'options' ) );
		}

		public function index() {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'pdos', 'action' => 'index' ) );
			}

			$queryData = $this->Decisionpdo->qdOccurences();
            $this->paginate = $queryData;
			$decisionspdos = $this->paginate( $this->modelClass );

            $this->set( compact( 'decisionspdos' ) );
		}

		public function add() {
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			if( !empty( $this->request->data ) ) {
				if( $this->Decisionpdo->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'decisionspdos', 'action' => 'index' ) );
				}
			}
			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		public function edit( $decisionpdo_id = null ) {
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			// TODO : vérif param
			// Vérification du format de la variable
			$this->assert( valid_int( $decisionpdo_id ), 'invalidParameter' );

			if( !empty( $this->request->data ) ) {
				if( $this->Decisionpdo->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'decisionspdos', 'action' => 'index' ) );
				}
			}
			else {
				$decisionpdo = $this->Decisionpdo->find(
					'first',
					array(
						'conditions' => array(
							'Decisionpdo.id' => $decisionpdo_id,
						)
					)
				);
				$this->request->data = $decisionpdo;
			}
			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		public function delete( $decisionpdo_id = null ) {
			// Vérification du format de la variable
			if( !valid_int( $decisionpdo_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Recherche de la personne
			$decisionpdo = $this->Decisionpdo->find(
				'first',
				array( 'conditions' => array( 'Decisionpdo.id' => $decisionpdo_id )
				)
			);

			// Mauvais paramètre
			if( empty( $decisionpdo_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Tentative de suppression ... FIXME
			if( $this->Decisionpdo->delete( array( 'Decisionpdo.id' => $decisionpdo_id ) ) ) {
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
				$this->redirect( array( 'controller' => 'decisionspdos', 'action' => 'index' ) );
			}
		}
	}

?>