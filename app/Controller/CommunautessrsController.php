<?php
	/**
	 * Code source de la classe CommunautessrsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe CommunautessrsController s'occupe du paramétrage des projets de
	 * villes territoriaux (CD 93).
	 *
	 * @package app.Controller
	 */
	class CommunautessrsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Communautessrs';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Communautesr', 'WebrsaTableausuivipdv93' );

		/**
		 * Liste des tables à ne pas prendre en compte dans les enregistrements
		 * vérifiés pour éviter les suppressions en cascade intempestives.
		 *
		 * @var array
		 */
		public $blacklist = array( 'communautessrs_structuresreferentes' );

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
				if( $this->Communautesr->save( null, array( 'atomic' => false ) ) && $success ) {
					$this->Communautesr->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$this->Communautesr->rollback();
					$this->Flash->error( __( 'Save->error' ) );
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
			$this->render( 'add_edit' );
		}
	}
?>
