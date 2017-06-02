<?php
	/**
	 * Code source de la classe Raisonssocialespartenairescuis66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
    App::import( 'Behaviors', 'Occurences' );

	/**
	 * La classe Raisonssocialespartenairescuis66Controller permet la gestion des CER du CG 93 (hors workflow).
	 *
	 * @package app.Controller
	 */
	class Raisonssocialespartenairescuis66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Raisonssocialespartenairescuis66';

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
			'Raisonsocialepartenairecui66',
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

//			$raisonssocialespartenairescuis66 = $this->Raisonsocialepartenairecui66->find( 'all', array( 'recursive' => -1 ) );

//			$this->set('raisonssocialespartenairescuis66', $raisonssocialespartenairescuis66);
            
            $this->Raisonsocialepartenairecui66->Behaviors->attach( 'Occurences' );
  
            $querydata = $this->Raisonsocialepartenairecui66->qdOccurencesExists(
                array(
                    'fields' => $this->Raisonsocialepartenairecui66->fields(),
                    'order' => array( 'Raisonsocialepartenairecui66.name ASC' )
                )
            );

            $this->paginate = $querydata;
            $raisonssocialespartenairescuis66 = $this->paginate('Raisonsocialepartenairecui66');
            $this->set( compact('raisonssocialespartenairescuis66'));
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
		public function edit( $raisonsocialepartenairecui66_id = null ) {
			if( $this->action == 'edit') {
				// Vérification du format de la variable
				if( !$this->Raisonsocialepartenairecui66->exists( $raisonsocialepartenairecui66_id ) ) {
					throw new NotFoundException();
				}
			}
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'raisonssocialespartenairescuis66', 'action' => 'index' ) );
			}

			// Tentative de sauvegarde du formulaire
			if( !empty( $this->request->data ) ) {
				$this->Raisonsocialepartenairecui66->begin();
				if( $this->Raisonsocialepartenairecui66->save( $this->request->data ) ) {
					$this->Raisonsocialepartenairecui66->commit();
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$this->Raisonsocialepartenairecui66->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else if( $this->action == 'edit') {
				$this->request->data = $this->Raisonsocialepartenairecui66->find(
					'first',
					array(
						'conditions' => array(
							'Raisonsocialepartenairecui66.id' => $raisonsocialepartenairecui66_id
						),
						'contain' => false
					)
				);
			}
			$this->render( 'edit' );
		}
		
		public function delete( $raisonsocialepartenairecui66_id = null ) {
			// Vérification du format de la variable
			/*if( !$this->Raisonsocialepartenairecui66->exists( $raisonsocialepartenairecui66_id ) ) {
				throw new NotFoundException();
			}

			$raisonsocialepartenairecui66 = $this->Raisonsocialepartenairecui66->find(
				'first',
				array( 'conditions' => array( 'Raisonsocialepartenairecui66.id' => $raisonsocialepartenairecui66_id )
				)
			);

			// Tentative de suppression ... FIXME
			if( $this->Raisonsocialepartenairecui66->deleteAll( array( 'Raisonsocialepartenairecui66.id' => $raisonsocialepartenairecui66_id ), true ) ) {
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
				$this->redirect( array( 'controller' => 'raisonssocialespartenairescuis66', 'action' => 'index' ) );
			}*/
            $this->Default->delete( $raisonsocialepartenairecui66_id, true );
		}
	}
?>
