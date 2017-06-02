<?php
	/**
	 * Code source de la classe ParticipantscomitesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ParticipantscomitesController ...
	 *
	 * @package app.Controller
	 */
	class ParticipantscomitesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Participantscomites';

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
			'Participantcomite',
			'Comiteapre',
			'Option',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Participantscomites:edit',
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

		public function beforeFilter() {
			$return = parent::beforeFilter();
			$this->set( 'qual', $this->Option->qual() );

			return $return;
		}

		public function index() {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'apres', 'action' => 'indexparams' ) );
			}

			$participants = $this->Participantcomite->find( 'all', array( 'recursive' => -1 ) );
			$this->set('participants', $participants );
		}

		public function add() {
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			if( !empty( $this->request->data ) ) {
				if( $this->Participantcomite->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'participantscomites', 'action' => 'index' ) );
				}
			}
			$this->render( 'add_edit' );
		}

		public function edit( $participant_id = null ) {
			$this->assert( valid_int( $participant_id ) , 'invalidParameter' );
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			if( !empty( $this->request->data ) ) {
				if( $this->Participantcomite->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'participantscomites', 'action' => 'index' ) );
				}
			}
			else {
				$participant = $this->Participantcomite->find(
					'first',
					array(
						'conditions' => array(
							'Participantcomite.id' => $participant_id,
						)
					)
				);
				$this->request->data = $participant;
			}

			$this->render( 'add_edit' );
		}

		public function delete( $participant_id = null ) {
			// Vérification du format de la variable
			if( !valid_int( $participant_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Recherche de la personne
			$participant = $this->Participantcomite->find(
				'first',
				array( 'conditions' => array( 'Participantcomite.id' => $participant_id )
				)
			);

			// Mauvais paramètre
			if( empty( $participant_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Tentative de suppression ... FIXME
			if( $this->Participantcomite->delete( array( 'Participantcomite.id' => $participant_id ) ) ) {
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
				$this->redirect( array( 'controller' => 'participantscomites', 'action' => 'index' ) );
			}
		}
	}

?>