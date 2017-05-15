<?php
	/**
	 * Code source de la classe SecteursactisController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe SecteursactisController permet la gestion des CER du CG 93 (hors workflow).
	 *
	 * @package app.Controller
	 */
	class SecteursactisController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Secteursactis';

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
			'Secteuracti',
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

			$secteursactis = $this->Secteuracti->find( 'all', array( 'recursive' => -1 ) );

			$this->set('secteursactis', $secteursactis);
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
		public function edit( $secteuracti_id = null ) {
			if( $this->action == 'edit') {
				// Vérification du format de la variable
				if( !$this->Secteuracti->exists( $secteuracti_id ) ) {
					throw new NotFoundException();
				}
			}
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'secteursactis', 'action' => 'index' ) );
			}
		
			// Tentative de sauvegarde du formulaire
			if( !empty( $this->request->data ) ) {
				$this->Secteuracti->begin();
				if( $this->Secteuracti->saveAll( $this->request->data ) ) {
					$this->Secteuracti->commit();
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$this->Secteuracti->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else if( $this->action == 'edit') {
				$this->request->data = $this->Secteuracti->find(
					'first',
					array(
						'conditions' => array(
							'Secteuracti.id' => $secteuracti_id
						),
						'contain' => false
					)
				);
			}
			$this->render( 'edit' );
		}
		
		public function delete( $secteuracti_id = null ) {
			// Vérification du format de la variable
			if( !$this->Secteuracti->exists( $secteuracti_id ) ) {
				throw new NotFoundException();
			}

			$metierexerce = $this->Secteuracti->find(
				'first',
				array( 'conditions' => array( 'Secteuracti.id' => $secteuracti_id )
				)
			);

			// Tentative de suppression ... FIXME
			if( $this->Secteuracti->deleteAll( array( 'Secteuracti.id' => $secteuracti_id ), true ) ) {
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
				$this->redirect( array( 'controller' => 'secteursactis', 'action' => 'index' ) );
			}
		}
	}
?>
