<?php
	/**
	 * Code source de la classe ComitesapresParticipantscomitesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ComitesapresParticipantscomitesController ...
	 *
	 * @package app.Controller
	 */
	class ComitesapresParticipantscomitesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'ComitesapresParticipantscomites';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Jetonsfonctions2',
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
			'ComiteapreParticipantcomite',
			'Apre',
			'Comiteapre',
			'Participantcomite',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'ComitesapresParticipantscomites:edit',
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
			'edit' => 'update',
			'rapport' => 'read',
		);

		protected function _setOptions() {
			$this->set( 'participants', $this->Participantcomite->find( 'all' ) );
			$this->set( 'options', (array)Hash::get( $this->ComiteapreParticipantcomite->enums(), 'ComiteapreParticipantcomite' ) );
		}

		/** ********************************************************************
		*
		*** *******************************************************************/

		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/** ********************************************************************
		*   Ajout et Modification des participants à un comité donné
		*** *******************************************************************/

		protected function _add_edit( $id = null ){
			$this->Jetonsfonctions2->get( array( 'action' => '_add_edit' ) );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetonsfonctions2->release( array( 'action' => '_add_edit' ) );
				$this->redirect( array( 'controller' => 'comitesapres', 'action'     => 'view', $id ) );
			}

			if( $this->action == 'add' ) {
				$comiteapre_id = $id;
				$nbrComites = $this->Comiteapre->find( 'count', array( 'conditions' => array( 'Comiteapre.id' => $comiteapre_id ), 'recursive' => -1 ) );
				$this->assert( ( $nbrComites == 1 ), 'invalidParameter' );
			}
			else if( $this->action == 'edit' ) {
				$comiteapre_id = $id;
				$comiteparticipant = $this->ComiteapreParticipantcomite->find(
					'all',
					array(
						'conditions' => array(
							'ComiteapreParticipantcomite.comiteapre_id' => $comiteapre_id
						)
					)
				);
				$this->assert( !empty( $comiteparticipant ), 'invalidParameter' );
			}

			if( !empty( $this->request->data ) ) {
				foreach( $this->request->data['Participantcomite']['Participantcomite'] as $i => $participantcomiteId ) {
					if( empty( $participantcomiteId ) ) {
						unset( $this->request->data['Participantcomite']['Participantcomite'][$i] );
					}
				}

				if( $this->Comiteapre->saveAll( $this->request->data ) ) {
					$this->Jetonsfonctions2->release( array( 'action' => '_add_edit' ) );
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'comitesapres', 'action' => 'view', $comiteapre_id ) );
				}
				else{
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else {
				if( $this->action == 'edit' ) {
					$this->request->data = array(
						'Comiteapre' => array(
							'id' => $comiteapre_id,
						),
						'Participantcomite' => array(
							'Participantcomite' => Set::extract( $comiteparticipant, '/ComiteapreParticipantcomite/participantcomite_id' )
						)
					);
				}
				else {
					$this->request->data['Comiteapre']['id'] = $comiteapre_id;
				}
			}
			$this->set( 'comiteapre_id', $comiteapre_id );
			$this->_setOptions();
			$this->Comiteapre->commit();
			$this->render( 'add_edit' );
		}

		/** ********************************************************************
		*   Recensement de la présence des participants au comité
		*** *******************************************************************/

		public function rapport( $comiteapre_id = null ){
			$comiteparticipant = $this->ComiteapreParticipantcomite->find(
				'all',
				array(
					'conditions' => array(
						'ComiteapreParticipantcomite.comiteapre_id' => $comiteapre_id
					)
				)
			);
			$this->assert( !empty( $comiteparticipant ), 'invalidParameter' );
			$this->set( 'comiteparticipant', $comiteparticipant );

			if( !empty( $this->request->data ) ) {
				$this->ComiteapreParticipantcomite->begin();
				$success = true;
				foreach( $this->request->data['ComiteapreParticipantcomite'] as $item ) {
					$success = $this->ComiteapreParticipantcomite->create( array( 'ComiteapreParticipantcomite' => $item ) ) && $success;
					$this->ComiteapreParticipantcomite->save();
				}

				if( $success ) {
					$this->ComiteapreParticipantcomite->commit();
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'comitesapres', 'action' => 'rapport', $comiteapre_id ) );
				}
				else {
					$this->ComiteapreParticipantcomite->rollback();
				}
			}
			else {
				$this->request->data = $comiteparticipant;
			}
			$this->_setOptions();
		}
	}
?>