<?php
	/**
	 * Code source de la classe WebrsaParametragesComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * La classe WebrsaParametragesComponent contient les méthodes index, add,
	 * edit et delete, permettant de paramétrer facilement les enregistrements
	 * de la table liée au modèle lié au contrôleur associé.
	 *
	 * L'ajout, la modification et la suppression sont dans des transactions.
	 *
	 * L'index et la suppression se préoccupent de savoir si des enregistrements
	 * sont liés (afin d'éviter les suppressions en cascade intempestives).
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaParametragesComponent extends Component
	{

		/**
		 * Paramètres de ce component
		 *
		 * @var array
		 */
		public $settings = array();

		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array(
			'Flash',
			'Session'
		);

		/**
		 * Liste des enregistrements, avec de la pagination par 100.
		 * Les résultats sont envoyés dans la vue dans la variable "results".
		 * Envoi d'une variable "options" dans la vue contenant les enums de
		 * modelClass.
		 *
		 * @param array $query
		 * @param array $params Clés modelClass, blacklist, progressivePaginate
		 */
		public function index( array $query = array(), array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$params += array(
				'modelClass' => $Controller->modelClass,
				'blacklist' => $Controller->blacklist,
				'progressivePaginate' => null
			);

			if( false === $Controller->{$params['modelClass']}->Behaviors->attached( 'Occurences' ) ) {
				$Controller->{$params['modelClass']}->Behaviors->attach( 'Occurences' );
			}

			$query += array(
				'fields' => array_merge(
					$Controller->{$params['modelClass']}->fields(),
					array( $Controller->{$params['modelClass']}->sqHasLinkedRecords( true, $params['blacklist'] ) )
				),
				'contain' => false,
				'limit' => 100,
				'maxLimit' => 101
			);

			$Controller->paginate = $query;
			$results = $Controller->paginate( $params['modelClass'], array(), array(), $params['progressivePaginate'] );

			$options = $Controller->{$params['modelClass']}->enums();
			$Controller->set( compact( 'results', 'options' ) );
		}

		/**
		 * Formulaire d'ajout d'un enregistrement.
		 *
		 * @param array $params Clés modelClass, redirect, view, query.
		 */
		public function add( array $params = array() ) {
			$this->edit( null, $params );
		}

		/**
		 * Formulaire de modification d'un enregistrement.
		 *
		 * @param integer $id La valeur de la clé primaire de l'enregistrement.
		 * @param array $params Clés modelClass, redirect, view, query.
		 * @throws NotFoundException
		 */
		public function edit( $id, array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$params += array(
				'modelClass' => $Controller->modelClass,
				'redirect' => array( 'action' => 'index' ),
				'view' => 'edit',
				'query' => array(),
				'method' => 'save'
			);

			// Le formulaire a été envoyé
			if( false === empty( $Controller->request->data ) ) {
				// Retour à la liste en cas d'annulation
				if( isset( $Controller->request->data['Cancel'] ) ) {
					$Controller->redirect( $params['redirect'] );
				}

				// Tentative de sauvegarde du formulaire
				$Controller->{$params['modelClass']}->begin();
				if( 'saveAll' === $params['method'] ) {
					$data = Hash::extract( $Controller->request->data, $params['modelClass'] );
					$success = $Controller->{$params['modelClass']}->saveAll( $data, array( 'atomic' => false ) );
				}
				else {
					$Controller->{$params['modelClass']}->create( $Controller->request->data );
					$success = false !== $Controller->{$params['modelClass']}->save( null, array( 'atomic' => false ) );
				}

				if( true === $success ) {
					$Controller->{$params['modelClass']}->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$Controller->redirect( $params['redirect'] );
				}
				else {
					$Controller->{$params['modelClass']}->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else if( 'edit' === $Controller->action ) {
				$query = $params['query'] + array(
					'conditions' => array(
						"{$params['modelClass']}.{$Controller->{$params['modelClass']}->primaryKey}" => $id
					),
					'contain' => false
				);

				if( 'saveAll' === $params['method'] ) {
					$data = Hash::extract(
						$Controller->{$params['modelClass']}->find( 'all', $query ),
						"{n}.{$params['modelClass']}"
					);
					$Controller->request->data = array( $params['modelClass'] => $data );
				}
				else {
					$Controller->request->data = $Controller->{$params['modelClass']}->find( 'first', $query );
				}

				if( true === empty( $Controller->request->data ) ) {
					throw new NotFoundException();
				}
			}

			$options = $Controller->{$params['modelClass']}->enums();
			$Controller->set( compact( 'options' ) );
			$Controller->view = $params['view'];
		}

		/**
		 * Suppression d'un enregistrement.
		 *
		 * @param integer $id La valeur de la clé primaire de l'enregistrement.
		 * @param array $params Clés modelClass, redirect, blacklist
		 * @throws NotFoundException
		 * @throws RuntimeException
		 */
		public function delete( $id, array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$params += array(
				'modelClass' => $Controller->modelClass,
				'redirect' => array( 'action' => 'index' ),
				'blacklist' => array()
			);

			if( false === $Controller->{$params['modelClass']}->Behaviors->attached( 'Occurences' ) ) {
				$Controller->{$params['modelClass']}->Behaviors->attach( 'Occurences' );
			}

			$query = array(
				'fields' => array(
					"{$params['modelClass']}.{$Controller->{$params['modelClass']}->primaryKey}",
					$Controller->{$params['modelClass']}->sqHasLinkedRecords( true, $params['blacklist'] )
				),
				'contain' => false,
				'conditions' => array( "{$params['modelClass']}.{$Controller->{$params['modelClass']}->primaryKey}" => $id )
			);

			$record = $Controller->{$params['modelClass']}->find( 'first', $query );

			if( true === empty( $record ) ) {
				throw new NotFoundException();
			}

			if( true == $record[$params['modelClass']]['has_linkedrecords'] ) {
				$msgid = "Erreur lors de la tentative de suppression de l'entrée d'id %d pour le modèle \"%s\" par l'utilisateur \"%s\" (id %d). Cette entrée possède des enregistrements liés.";
				$message = sprintf(
					$msgid,
					$id,
					$Controller->{$params['modelClass']}->alias,
					$this->Session->read( 'Auth.User.username' ),
					$this->Session->read( 'Auth.User.id' )
				);
				throw new RuntimeException( $message, 500 );
			}

			$Controller->{$params['modelClass']}->begin();
			if( $Controller->{$params['modelClass']}->delete( array( "{$params['modelClass']}.{$Controller->{$params['modelClass']}->primaryKey}" => $id ) ) ) {
				$Controller->{$params['modelClass']}->commit();
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$Controller->{$params['modelClass']}->rollback();
				$this->Flash->error( __( 'Delete->error' ) );
			}

			$Controller->redirect( $params['redirect'] );
		}
	}
?>