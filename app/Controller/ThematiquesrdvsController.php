<?php
	/**
	 * Code source de la classe ThematiquesrdvsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AppController', 'Controller');

	/**
	 * La classe ThematiquesrdvsController ...
	 *
	 * @package app.Controller
	 */
	class ThematiquesrdvsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Thematiquesrdvs';

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
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Thematiquerdv',
			'Rendezvous',
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
			$this->paginate = array(
				'Thematiquerdv' => array(
					'contain' => array(
						'Statutrdv',
						'Typerdv',
					),
					'limit' => 10
				)
			);
			$this->set( 'thematiquesrdvs', $this->paginate() );

			$options = array(
				'Thematiquerdv' => array(
					'linkedmodel' => $this->Thematiquerdv->linkedModels()
				)
			);
			$this->set( compact( 'options' ) );
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
		 * @throws NotFoundException
		 */
		public function edit( $id = null ) {
				if( !empty( $this->request->data ) ) {

				// Retour à l'index en cas d'annulation
				if( isset( $this->request->data['Cancel'] ) ) {
					$this->redirect( array( 'action' => 'index' ) );
				}

				$this->Thematiquerdv->begin();
				$this->Thematiquerdv->create( $this->request->data );

				if( $this->Thematiquerdv->save() ) {
					$this->Thematiquerdv->commit();
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$this->Thematiquerdv->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $this->Thematiquerdv->find(
					'first',
					array(
						'conditions' => array(
							"Thematiquerdv.id" => $id
						),
						'contain' => false
					)
				);

				if( empty( $this->request->data  ) ) {
					throw new NotFoundException();
				}
			}

			$options = array(
				'Thematiquerdv' => array(
					'statutrdv_id' => $this->Thematiquerdv->Statutrdv->find( 'list', array( 'contain' => false, 'order' => array( 'libelle' ) ) ),
					'typerdv_id' => $this->Thematiquerdv->Typerdv->find( 'list', array( 'contain' => false, 'order' => array( 'libelle' ) ) ),
					'linkedmodel' => $this->Thematiquerdv->linkedModels(),
				)
			);
			$this->set( compact( 'options' ) );

			$this->render( 'edit' );
		}

		/**
		 * Suppression d'un <élément> et redirection vers l'index.
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$this->Thematiquerdv->begin();

			if( $this->Thematiquerdv->delete( $id ) ) {
				$this->Thematiquerdv->commit();
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
			}
			else {
				$this->Thematiquerdv->rollback();
				$this->Session->setFlash( 'Erreur lors de la suppression', 'flash/error' );
			}

			$this->redirect( array( 'action' => 'index' ) );
		}
	}
?>
