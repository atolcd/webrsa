<?php
	/**
	 * Code source de la classe Sujetscers93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Sujetscers93Controller permet la gestion des CER du CG 93 (hors workflow).
	 *
	 * @package app.Controller
	 */
	class Sujetscers93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Sujetscers93';

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
			'Sujetcer93',
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
		 * Pagination sur les <élément>s de la table.
		 *
		 * @return void
		 */
		public function index() {

			$sujetscers93 = $this->Sujetcer93->find( 'all', array( 'recursive' => -1 ) );

			$this->set('sujetscers93', $sujetscers93);
		}

		/**
		 * Formulaire d'ajout d'un élémént.
		 *
		 * @return void
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, 'edit' ), $args );
		}

		/**
		 * Formulaire de modification d'un <élément>.
		 *
		 * @return void
		 * @throws NotFoundException
		 */
		public function edit( $sujetcer93_id = null ) {
			if( $this->action == 'edit') {
				// Vérification du format de la variable
				if( !$this->Sujetcer93->exists( $sujetcer93_id ) ) {
					throw new NotFoundException();
				}
			}
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'sujetscers93', 'action' => 'index' ) );
			}
		
			// Tentative de sauvegarde du formulaire
			if( !empty( $this->request->data ) ) {
				$this->Sujetcer93->begin();
				if( $this->Sujetcer93->saveAll( $this->request->data ) ) {
					$this->Sujetcer93->commit();
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$this->Sujetcer93->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else if( $this->action == 'edit') {
				$this->request->data = $this->Sujetcer93->find(
					'first',
					array(
						'conditions' => array(
							'Sujetcer93.id' => $sujetcer93_id
						),
						'contain' => false
					)
				);
			}
			$this->render( 'edit' );
		}
		
		public function delete( $sujetcer93_id = null ) {
			// Vérification du format de la variable
			if( !$this->Sujetcer93->exists( $sujetcer93_id ) ) {
				throw new NotFoundException();
			}

			$sujetcer93 = $this->Sujetcer93->find(
				'first',
				array( 'conditions' => array( 'Sujetcer93.id' => $sujetcer93_id )
				)
			);

			// Tentative de suppression ... FIXME
			if( $this->Sujetcer93->deleteAll( array( 'Sujetcer93.id' => $sujetcer93_id ), true ) ) {
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
				$this->redirect( array( 'controller' => 'sujetscers93', 'action' => 'index' ) );
			}
		}
	}
?>
