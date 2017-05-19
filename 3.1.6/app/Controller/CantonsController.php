<?php
	/**
	 * Code source de la classe CantonsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe CantonsController ...
	 *
	 * @package app.Controller
	 */
	class CantonsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cantons';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Search.SearchPrg' => array(
				'actions' => array(
					'index'
				)
			)
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
			'Canton',
			'Option',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Cantons:edit',
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
		
		public $paginate = array(
			'limit' => 20,
			'recursive' => -1,
			'order' => array( 'canton ASC' )
		);

		/**
		 * 	FIXME: docs
		 */
		protected function _setOptions() {
			$this->set( 'zonesgeographiques', $this->Canton->Zonegeographique->find( 'list' ) );
			$this->set( 'typesvoies', $this->Option->typevoie() );
			$this->set( 'typevoie', $this->Option->typevoie() );
			$this->set( 'libtypesvoies', ClassRegistry::init( 'Adresse' )->enum( 'libtypevoie' ) );
		}

		/**
		 * 	FIXME: docs
		 */
		public function index() {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'parametrages', 'action' => 'index' ) );
			}

			if( !empty( $this->request->data ) ) {
				$queryData = $this->Canton->search( $this->request->data );
				$queryData['limit'] = 20;
				$this->paginate = $queryData;
				$cantons = $this->paginate( 'Canton' );
				$this->set( 'cantons', $cantons);
			}
			$this->_setOptions();
		}

		/**
		 * 	FIXME: docs
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * 	FIXME: docs
		 */
		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * 	FIXME: docs
		 */
		protected function _add_edit( $id = null ) {
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			if( $this->action == 'edit' ) {
				$qd_canton = array(
					'conditions' => array(
						'Canton.id' => $id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$canton = $this->Canton->find( 'first', $qd_canton );
				$this->assert( !empty( $canton ), 'invalidParameter' );
			}

			if( !empty( $this->request->data ) ) {
				$this->Canton->create( $this->request->data );
				if( $this->Canton->save() ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->Session->setFlash( 'Attention, en cas de modifications sur les cantons, il peut être utile de lancer AdresseCantonShell en console pour recalculer les relations entre Adresses et Cantons', 'flash/notice', array(), 'notice' );
					$this->redirect( array( 'action' => 'index' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $canton;
			}

			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		/**
		 * 	FIXME: docs
		 */
		public function delete( $id = null ) {
			$qd_canton = array(
				'conditions' => array(
					'Canton.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$canton = $this->Canton->find( 'first', $qd_canton );
			$this->assert( !empty( $canton ), 'invalidParameter' );

			if( $this->Canton->delete( Set::classicExtract( $canton, 'Canton.id' ) ) ) {
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
				$this->redirect( array( 'action' => 'index' ) );
			}
		}

	}
?>