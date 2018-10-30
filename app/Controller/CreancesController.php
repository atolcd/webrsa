<?php
	/**
	 * Code source de la classe CreancesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'LocaleHelper', 'View/Helper' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
	App::uses( 'WebrsaAccessCreances', 'Utility' );

	/**
	 * La classe CreancesController ...
	 *
	 * @package app.Controller
	 */
	class CreancesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Creances';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'DossiersMenus',
			'Jetons2',
			'WebrsaAccesses'
			);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default',
			'Default2',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			)
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'index' => 'read',
			'add' => 'create',
			'edit' => 'update'
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Creance',
			'WebrsaCreance'
			);

		/**
		 * Pagination sur les <éléments> de la table. *
		 * @param integer $foyer_id L'id technique du Foyer pour lequel on veut les Creances.
		 *
		 */
		public function index($foyer_id) {
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array( 'foyer_id' => $foyer_id )));

			$this->set( 'options', $this->Creance->enums() );

			$creances = $this->WebrsaAccesses->getIndexRecords(
				$foyer_id,
				array(
					'fields' => array_merge(
						$this->Creance->fields()
					),
					'conditions' => array(
						'Creance.foyer_id' => $foyer_id
					),
					'order' => array(
						'Creance.dtimplcre DESC',
					),
					'contain' => FALSE
				)
			);

			// Assignations à la vue
			$this->set( 'foyer_id', $foyer_id );
			$this->set( 'creances', $creances );
			$this->set( 'urlmenu', '/creances/index/'.$foyer_id );
		}

		/**
		*
		*/
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		*
		*/
		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Ajouter une creances à un foyer
		 *
		 * @param integer $foyer_id L'id technique du foyer auquel ajouter la créance
		 * @return void
		 */
		protected function _add_edit($id = null) {
			if($this->action == 'add' ) {
				$foyer_id = $id;
				$id = null;
				$dossier_id = $this->Creance->Foyer->dossierId( $foyer_id );
			}elseif($this->action == 'edit' ){
				$this->WebrsaAccesses->check($id);
				$this->Creance->id = $id;
				$foyer_id = $this->Creance->field( 'foyer_id' );
				$dossier_id = $this->Creance->dossierId( $id );
			}

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $foyer_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Creance->begin();
				$data = $this->request->data;
				
				if ( $data['Creance']['mtsolreelcretrans'] == '' ||  $data['Creance']['mtinicre'] =='' ) {
					$this->Creance->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}else{
					if($this->action == 'add' ) {
						$data['Creance']['foyer_id'] = $foyer_id;
					}
					if( $this->Creance->saveAll( $data, array( 'validate' => 'only' ) ) ) {
						if( $this->Creance->save( $data ) ) {
							$this->Creance->commit();
							$this->Jetons2->release( $dossier_id );
							$this->Flash->success( __( 'Save->success' ) );
							$this->redirect( array( 'controller' => 'Creances', 'action' => 'index', $foyer_id ) );
						}
						else {
							$this->Creance->rollback();
							$this->Flash->error( __( 'Save->error' ) );
						}
					}
				}
			}
			// Affichage des données
			elseif($this->action == 'edit' ) {
				$creance = $this->Creance->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Creance->fields()
						),
						'conditions' => array(
							'Creance.id' => $id
						),
						'contain' => FALSE
					)
				);
				if (!empty( $creance ) ){
					// Assignation au formulaire
					$this->request->data = $creance;		
				}
			}

			// Assignation à la vue
			$this->set( 'options', $this->Creance->enums() );
			$this->set( 'urlmenu', '/creances/index/'.$foyer_id );
			$this->set( 'foyer_id', $foyer_id );
			$this->render( 'add_edit' );
			
		}
	}
?>
