<?php
	/**
	 * Code source de la classe RegroupementszonesgeoController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe RegroupementszonesgeoController ...
	 *
	 * @package app.Controller
	 */
	class RegroupementszonesgeoController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Regroupementszonesgeo';

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
			'Regroupementzonegeo',
			'Adresse',
			'Structurereferente',
			'User',
			'Zonegeographique',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Regroupementszonesgeo:edit',
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

		public function index() {
			$rgpts = $this->Regroupementzonegeo->find(
				'all',
				array(
					'recursive' => -1
				)
			);
			$this->set('rgpts', $rgpts);
		}

		public function add() {
			$zg = $this->Zonegeographique->find(
				'list',
				array(
					'fields' => array(
						'Zonegeographique.id',
						'Zonegeographique.libelle'
					)
				)
			);
			$this->set( 'zglist', $zg );

			if( !empty( $this->request->data ) ) {
				if( $this->Regroupementzonegeo->saveAll( $this->request->data ) ) {
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'regroupementszonesgeo', 'action' => 'index' ) );
				}
			}
			$this->render( 'add_edit' );
		}

		public function edit( $rgpt_id = null ) {
			// TODO : vérif param
			// Vérification du format de la variable
			$this->assert( valid_int( $rgpt_id ), 'invalidParameter' );

			$zg = $this->Zonegeographique->find(
				'list',
				array(
					'fields' => array(
						'Zonegeographique.id',
						'Zonegeographique.libelle'
					)
				)
			);
			$this->set( 'zglist', $zg );

			if( !empty( $this->request->data ) ) {
				if( $this->Regroupementzonegeo->saveAll( $this->request->data ) ) {
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'regroupementszonesgeo', 'action' => 'index' ) );
				}
			}
			else {
				$rgpt = $this->Regroupementzonegeo->find(
					'first',
					array(
						'conditions' => array(
							'Regroupementzonegeo.id' => $rgpt_id,
						)
					)
				);
				$this->request->data = $rgpt;
			}

			$this->render( 'add_edit' );
		}

		public function delete( $rgpt_id = null ) {
			// Vérification du format de la variable
			if( !valid_int( $rgpt_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Recherche de la personne
			$rgpt = $this->Regroupementzonegeo->find(
				'first',
				array( 'conditions' => array( 'Regroupementzonegeo.id' => $rgpt_id )
				)
			);

			// Mauvais paramètre
			if( empty( $rgpt_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Tentative de suppression ... FIXME
			if( $this->Regroupementzonegeo->delete( array( 'Regroupementzonegeo.id' => $rgpt_id ) ) ) {
				$this->Flash->success( __( 'Delete->success' ) );
				$this->redirect( array( 'controller' => 'regroupementszonesgeo', 'action' => 'index' ) );
			}
		}
	}

?>