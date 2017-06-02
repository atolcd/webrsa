<?php
	/**
	 * Code source de la classe Soussujetscers93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Soussujetscers93Controller permet la gestion des CER du CG 93 (hors workflow).
	 *
	 * @package app.Controller
	 */
	class Soussujetscers93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Soussujetscers93';

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
			'Soussujetcer93',
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
			$soussujetscers93 = $this->Soussujetcer93->find(
				'all',
				array(
					'fields' => array_merge(
						$this->Soussujetcer93->fields(),
						$this->Soussujetcer93->Sujetcer93->fields()
					),
					'contain' => array(
						'Sujetcer93'
					),
					'order' => array( 'Sujetcer93.name ASC' )
				)
			);

			$this->set('soussujetscers93', $soussujetscers93);
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
		public function edit( $soussujetcer93_id = null ) {
			if( $this->action == 'edit') {
				// Vérification du format de la variable
				if( !$this->Soussujetcer93->exists( $soussujetcer93_id ) ) {
					throw new NotFoundException();
				}
			}
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'soussujetscers93', 'action' => 'index' ) );
			}
		
			// Tentative de sauvegarde du formulaire
			if( !empty( $this->request->data ) ) {
				$this->Soussujetcer93->begin();
				if( $this->Soussujetcer93->saveAll( $this->request->data ) ) {
					$this->Soussujetcer93->commit();
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$this->Soussujetcer93->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else if( $this->action == 'edit') {
				$this->request->data = $this->Soussujetcer93->find(
					'first',
					array(
						'conditions' => array(
							'Soussujetcer93.id' => $soussujetcer93_id
						),
						'contain' => array(
							'Sujetcer93'
						)
					)
				);
			}
			
			$options = array(
				'Soussujetcer93' => array(
					'sujetcer93_id' => $this->Soussujetcer93->Sujetcer93->find( 'list', array( 'fields' => array( 'id', 'name' ), 'conditions' => array( 'Sujetcer93.isautre' => '0' ) ) )
				)
			);
			$this->set( compact( 'options' ) );

			$this->render( 'edit' );
		}
		
		public function delete( $soussujetcer93_id = null ) {
			// Vérification du format de la variable
			if( !$this->Soussujetcer93->exists( $soussujetcer93_id ) ) {
				throw new NotFoundException();
			}

			$soussujetcer93 = $this->Soussujetcer93->find(
				'first',
				array( 'conditions' => array( 'Soussujetcer93.id' => $soussujetcer93_id )
				)
			);

			// Tentative de suppression ... FIXME
			if( $this->Soussujetcer93->deleteAll( array( 'Soussujetcer93.id' => $soussujetcer93_id ), true ) ) {
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
				$this->redirect( array( 'controller' => 'soussujetscers93', 'action' => 'index' ) );
			}
		}
	}
?>
