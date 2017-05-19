<?php
	/**
	 * Code source de la classe ActionsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe ActionsController s'occupe du paramétrage des actions d'insertion.
	 *
	 * ATTENTION: impossible d'ajouter une réelle clé étrangère en base de données
	 * (66, 93) donc on ne peut pas utiliser les méthodes WebrsaParametrages::index
	 * et WebrsaParametrages::delete.
	 *
	 * @package app.Controller
	 */
	class ActionsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Actions';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Action' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Actions:edit'
		);

		/**
		 * Liste des actions d'insertion.
		 */
		public function index() {
			$query = array(
				'fields' => array_merge(
					$this->Action->fields(),
					$this->Action->Typeaction->fields(),
					// @info: impossible d'ajouter une réelle clé étrangère en base de données (66, 93)
					array(
						'EXISTS( SELECT "contratsinsertion"."id" FROM contratsinsertion WHERE "contratsinsertion"."engag_object" = "Action"."code" ) AS "Action__has_linkedrecords"'
					)
				),
				'joins' => array(
					$this->Action->join( 'Typeaction' )
				),
				'limit' => 100,
				'maxLimit' => 101
			);
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'une action d'insertion.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = array(
				'Action' => array(
					'typeaction_id' => $this->Action->Typeaction->find( 'list', array( 'fields' => array( 'libelle' ) ) )
				)
			);
			$this->set( compact( 'options' ) );
		}

		/**
		 * Suppression d'une action d'insertion.
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			if( false === $this->Action->Behaviors->attached( 'Occurences' ) ) {
				$this->Action->Behaviors->attach( 'Occurences' );
			}

			$query = array(
				'fields' => array(
					"Action.{$this->Action->primaryKey}",
					// @info: impossible d'ajouter une réelle clé étrangère en base de données (66, 93)
					'EXISTS( SELECT "contratsinsertion"."id" FROM contratsinsertion WHERE "contratsinsertion"."engag_object" = "Action"."code" ) AS "Action__has_linkedrecords"'
				),
				'contain' => false,
				'conditions' => array( "Action.{$this->Action->primaryKey}" => $id )
			);

			$record = $this->Action->find( 'first', $query );

			if( true === empty( $record ) ) {
				throw new NotFoundException();
			}

			if( true == $record['Action']['has_linkedrecords'] ) {
				$msgid = "Erreur lors de la tentative de suppression de l'entrée d'id %d pour le modèle \"%s\" par l'utilisateur \"%s\" (id %d). Cette entrée possède des enregistrements liés.";
				$message = sprintf(
					$msgid,
					$id,
					$this->Action->alias,
					$this->Session->read( 'Auth.User.username' ),
					$this->Session->read( 'Auth.User.id' )
				);
				throw new RuntimeException( $message, 500 );
			}

			$this->Action->begin();
			if( $this->Action->delete( array( "Action.{$this->Action->primaryKey}" => $id ) ) ) {
				$this->Action->commit();
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Action->rollback();
				$this->Flash->error( __( 'Delete->error' ) );
			}

			$this->redirect( array( 'action' => 'index' ) );
		}
	}
?>