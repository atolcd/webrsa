<?php
	/**
	 * Code source de la classe TiersprestatairesapresController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe TiersprestatairesapresController ...
	 *
	 * @package app.Controller
	 */
	class TiersprestatairesapresController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Tiersprestatairesapres';

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
			'Tiersprestataireapre',
			'Apre',
			'Option',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Tiersprestatairesapres:edit',
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
			parent::beforeFilter();
			$this->set( 'qual', $this->Option->qual() );
			$this->set( 'typevoie', $this->Option->typevoie() );
			$options = $this->Tiersprestataireapre->enums();
			$this->set( 'options', $options );
			$this->set( 'aidesApres', $this->Apre->WebrsaApre->aidesApre );
			$this->set( 'natureAidesApres', $this->Option->natureAidesApres() );
		}

		public function index() {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'apres', 'action' => 'indexparams' ) );
			}

			$tiersprestatairesapres = $this->Tiersprestataireapre->adminList();

			$this->set('tiersprestatairesapres', $tiersprestatairesapres);
		}

		public function add() {
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			if( !empty( $this->request->data ) ) {
				if( $this->Tiersprestataireapre->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'tiersprestatairesapres', 'action' => 'index' ) );
				}
			}
			$this->render( 'add_edit' );
		}

		public function edit( $tiersprestataireapre_id = null ) {
			// TODO : vérif param
			// Vérification du format de la variable
			$this->assert( valid_int( $tiersprestataireapre_id ), 'invalidParameter' );

			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			if( !empty( $this->request->data ) ) {
				if( $this->Tiersprestataireapre->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'tiersprestatairesapres', 'action' => 'index' ) );
				}
			}
			else {
				$tiersprestataireapre = $this->Tiersprestataireapre->find(
					'first',
					array(
						'conditions' => array(
							'Tiersprestataireapre.id' => $tiersprestataireapre_id,
						)
					)
				);
				$this->request->data = $tiersprestataireapre;
			}

			$this->render( 'add_edit' );
		}

		public function delete( $tiersprestataireapre_id = null ) {
			// Vérification du format de la variable
			if( !valid_int( $tiersprestataireapre_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Recherche de la personne
			$tiersprestataireapre = $this->Tiersprestataireapre->find(
				'first',
				array( 'conditions' => array( 'Tiersprestataireapre.id' => $tiersprestataireapre_id )
				)
			);

			// Mauvais paramètre
			if( empty( $tiersprestataireapre_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Tentative de suppression ... FIXME
			if( $this->Tiersprestataireapre->delete( array( 'Tiersprestataireapre.id' => $tiersprestataireapre_id ) ) ) {
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
				$this->redirect( array( 'controller' => 'tiersprestatairesapres', 'action' => 'index' ) );
			}
		}
	}

?>