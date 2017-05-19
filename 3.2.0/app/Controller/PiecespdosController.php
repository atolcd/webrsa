<?php
	/**
	 * Code source de la classe PiecespdosController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe PiecespdosController ...
	 *
	 * @package app.Controller
	 */
	class PiecespdosController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Piecespdos';

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

		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Piecepdo',
			'Propopdo',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Piecespdos:edit',
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

		public function index(  ) {

		}

		public function add( $pdo_id = null ) {
			$this->assert( valid_int( $pdo_id ), 'invalidParameter' );

			$pdo = $this->Propopdo->find( 'first', array( 'conditions' => array( 'Propopdo.id' => $pdo_id ) ) );
			$this->set( 'pdo', $pdo );

			$dossier_id = Set::extract( $pdo, 'Propopdo.dossier_id' );

			if( !empty( $this->request->data ) ) {

				if( $this->Piecepdo->saveAll( $this->request->data ) ) {
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'dossierspdo', 'action' => 'index', $dossier_id ) );
				}
			}
			$this->render( 'add_edit' );

		}

		public function edit( $piecepdo_id = null ) {
			// TODO : vérif param
			// Vérification du format de la variable
			$this->assert( valid_int( $piecepdo_id ), 'invalidParameter' );

			if( !empty( $this->request->data ) ) {
				if( $this->Piecepdo->saveAll( $this->request->data ) ) {
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'piecespdos', 'action' => 'index' ) );
				}
			}
			else {
				$piecepdo = $this->Piecepdo->find(
					'first',
					array(
						'conditions' => array(
							'Piecepdo.id' => $piecepdo_id,
						)
					)
				);
				$this->request->data = $piecepdo;
			}

			$this->render( 'add_edit' );
		}

		public function delete( $piecepdo_id = null ) {
			// Vérification du format de la variable
			if( !valid_int( $piecepdo_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Recherche de la personne
			$piecepdo = $this->Piecepdo->find(
				'first',
				array( 'conditions' => array( 'Piecepdo.id' => $piecepdo_id )
				)
			);

			// Mauvais paramètre
			if( empty( $piecepdo_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Tentative de suppression ... FIXME
			if( $this->Piecepdo->delete( array( 'Piecepdo.id' => $piecepdo_id ) ) ) {
				$this->Flash->success( __( 'Delete->success' ) );
				$this->redirect( array( 'controller' => 'piecespdos', 'action' => 'index' ) );
			}
		}
	}

?>