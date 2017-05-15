<?php
	/**
	 * Code source de la classe TypesnotifspdosController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe TypesnotifspdosController ...
	 *
	 * @package app.Controller
	 */
	class TypesnotifspdosController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Typesnotifspdos';

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
			'Typenotifpdo',
			'Propopdo',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Typesnotifspdos:edit',
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
			'deleteparametrage' => 'delete',
			'edit' => 'update',
			'index' => 'read',
		);

		public function index() {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'pdos', 'action' => 'index' ) );
			}

			$typesnotifspdos = $this->Typenotifpdo->find(
				'all',
				array(
					'recursive' => -1
				)
			);
			$this->set('typesnotifspdos', $typesnotifspdos);
		}

		public function add() {
			if( !empty( $this->request->data ) ) {
				if( $this->Typenotifpdo->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'typesnotifspdos', 'action' => 'index' ) );
				}
			}
			$this->render( 'add_edit' );
		}

		public function edit( $typenotifpdo_id = null ) {
			// TODO : vérif param
			// Vérification du format de la variable
			$this->assert( valid_int( $typenotifpdo_id ), 'invalidParameter' );

			if( !empty( $this->request->data ) ) {
				if( $this->Typenotifpdo->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'typesnotifspdos', 'action' => 'index' ) );
				}
			}
			else {
				$typenotifpdo = $this->Typenotifpdo->find(
					'first',
					array(
						'conditions' => array(
							'Typenotifpdo.id' => $typenotifpdo_id,
						)
					)
				);
				$this->request->data = $typenotifpdo;
			}

			$this->render( 'add_edit' );
		}

		public function deleteparametrage( $typenotifpdo_id = null ) {
			// Vérification du format de la variable
			if( !valid_int( $typenotifpdo_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Recherche de la personne
			$typenotifpdo = $this->Typenotifpdo->find(
				'first',
				array( 'conditions' => array( 'Typenotifpdo.id' => $typenotifpdo_id )
				)
			);
			// Mauvais paramètre
			if( empty( $typenotifpdo_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Tentative de suppression ... FIXME
			if( $this->Typenotifpdo->delete( array( 'Typenotifpdo.id' => $typenotifpdo_id ) ) ) {
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
				$this->redirect( array( 'controller' => 'typesnotifspdos', 'action' => 'index' ) );
			}
		}
	}

?>