<?php
	/**
	 * Code source de la classe CommunautessrsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	/**
	 * La classe CommunautessrsController ...
	 *
	 * @package app.Controller
	 */
	class CommunautessrsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Communautessrs';

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
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Communautesr',
			'WebrsaTableausuivipdv93',
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
		);

		/**
		 * Pagination sur les <éléments> de la table.
		 */
		public function index() {
			$query = array(
				'fields' => array(
					'Communautesr.id',
					'Communautesr.name',
					'Communautesr.actif',
				),
				'order' => array(
					'Communautesr.name ASC'
				),
				'limit' => 10
			);
			$query = $this->Communautesr->qdOccurencesExists( $query, array( 'Structurereferente' ) );
			$this->paginate = array(
				'Communautesr' => $query
			);

			$this->set( 'results', $this->paginate() );
			$this->set( 'options', $this->Communautesr->enums() );
		}

		/**
		 * Formulaire d'ajout d'un <élément>.
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, 'edit' ), $args );
		}

		/**
		 * Formulaire de modification d'un <élément>.
		 *
		 * @throws NotFoundException
		 */
		public function edit( $id = null ) {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Communautesr->begin();
				$this->Communautesr->create( $this->request->data );

				$success = $this->Communautesr->checkMultipleSelect( $this->request->data, 'Structurereferente' );
				if( false === $success ) {
					$this->set( 'checkedError', 'Champ obligatoire' );
				}
				if( $this->Communautesr->save() && $success ) {
					$this->Communautesr->commit();
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$this->Communautesr->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else if( $this->action == 'edit' ) {
				// Lecture de l'enregistrement à modifier
				$query = array(
					'conditions' => array(
						'Communautesr.id' => $id
					),
					'contain' => false
				);
				$this->request->data = $this->Communautesr->find( 'first', $query );

				if( empty( $this->request->data ) ) {
					throw new NotFoundException();
				}

				// Structures référentes cochées
				$query = array(
					'fields' => array( 'CommunautesrStructurereferente.structurereferente_id' ),
					'conditions' => array(
						'CommunautesrStructurereferente.communautesr_id' => $id
					),
					'contain' => false
				);
				$checked = $this->Communautesr->CommunautesrStructurereferente->find( 'all', $query );
				$this->request->data['Structurereferente']['Structurereferente'] = Hash::extract( $checked, '{n}.CommunautesrStructurereferente.structurereferente_id' );
			}

			// Options
			$options = $this->Communautesr->enums();
			$options['Structurereferente']['Structurereferente'] = $this->WebrsaTableausuivipdv93->listePdvs();

			$this->set( compact( 'options' ) );
			$this->render( 'edit' );
		}

		/**
		 * Suppression d'un <élément> et redirection vers l'index.
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$occurences = $this->Communautesr->occurencesExists( array( 'Communautesr.id' => $id ), array( 'Structurereferente' ) );
			if( $occurences[$id] ) {
				$message = sprintf( 'Impossible de supprimer l\'enregistrement %d du modèle %s car d\'autres enregistrements en dépendent.', $id, $this->Communautesr->alias );
				throw new InternalErrorException( $message );
			}

			$this->Communautesr->begin();

			if( $this->Communautesr->delete( $id ) ) {
				$this->Communautesr->commit();
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
			}
			else {
				$this->Communautesr->rollback();
				$this->Session->setFlash( 'Erreur lors de la suppression', 'flash/error' );
			}

			$this->redirect( array( 'action' => 'index' ) );
		}

	}
?>
