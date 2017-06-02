<?php
	/**
	 * Code source de la classe PermanencesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe PermanencesController ...
	 *
	 * @package app.Controller
	 */
	class PermanencesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Permanences';

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
			'Permanence',
			'Option',
			'Structurereferente',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Permanences:edit',
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
			$this->set( 'typevoie', $this->Option->typevoie() );
			$this->set( 'sr', $this->Structurereferente->find( 'list' ) );
			$this->set( 'options', (array)Hash::get( $this->Permanence->enums(), 'Permanence' ) );
		}

		public function index() {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'parametrages', 'action' => 'index' ) );
			}

			$permanences = $this->Permanence->find(
				'all',
				array(
					'recursive' => -1
				)

			);
			$this->_setOptions();
			$this->set( 'permanences', $permanences );
		}

		public function add() {
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			if( !empty( $this->request->data ) ) {
				if( $this->Permanence->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'permanences', 'action' => 'index' ) );
				}
			}

			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		public function edit( $permanence_id = null ) {
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			// TODO : vérif param
			// Vérification du format de la variable
			$this->assert( valid_int( $permanence_id ), 'error404' );

			if( !empty( $this->request->data ) ) {
				if( $this->Permanence->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'permanences', 'action' => 'index' ) );
				}
			}
			else {
				$permanence = $this->Permanence->find(
					'first',
					array(
						'conditions' => array(
							'Permanence.id' => $permanence_id,
						)
					)
				);
				$this->request->data = $permanence;
			}

			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		public function delete( $permanence_id = null ) {
			// Vérification du format de la variable
			if( !valid_int( $permanence_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Recherche de la personne
			$permanence = $this->Permanence->find(
				'first',
				array( 'conditions' => array( 'Permanence.id' => $permanence_id )
				)
			);

			// Mauvais paramètre
			if( empty( $permanence_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Tentative de suppression ... FIXME
			if( $this->Permanence->delete( array( 'Permanence.id' => $permanence_id ) ) ) {
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
				$this->redirect( array( 'controller' => 'permanences', 'action' => 'index' ) );
			}
		}
	}
?>