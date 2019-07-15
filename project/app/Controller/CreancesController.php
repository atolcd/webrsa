<?php
	/**
	 * Code source de la classe CreancesController.
	 *
	 * PHP 7.2
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
			'Cohortes',
			'Gedooo.Gedooo',
			'Default',
			'DossiersMenus',
			'Jetons2',
			'WebrsaAccesses',
			'Fileuploader',
			'Search.SearchPrg' => array(
				'actions' => array(
					'dossierEntrantsCreanciers',
					'cohorte_preparation' => array('filter' => 'Search'),
					'search',
				),
			)
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
			'Locale',
			'Paginator',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
			'Cake1xLegacy.Ajax',
			'Search',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'index' => 'read',
			'view' => 'read',
			'add' => 'create',
			'edit' => 'update',
			'validation' => 'update',
			'delete' => 'delete',
			'fluxadd' => 'create',
			'copycreance' => 'create',
			'search' => 'read',
			'filelink' => 'read',
			'fileview' => 'read',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'update',
			'ajaxreffonct' => 'read',
			'dossierEntrantsCreanciers' => 'read',
			'exportcsv' => 'read'
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Creance',
			'Titrecreancier',
			'WebrsaCreance',
			'Rejettalendcreance',
			'Dossier',
			'Foyer',
			'Personne',
			'Situationdossierrsa',
			'Calculdroitrsa',
			'Option',
			'Historiqueetat'
			);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		*/
		public $aucunDroit = array(
			'ajaxfiledelete',
			'ajaxfileupload',
			'ajaxreffonct',
			'download',
			'fileview'
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'copycreance' => 'Creances:add',
			'delete' => 'Creances:add',
			'nonemission' => 'Creances:index',
			'view' => 'Creances:index',
			'exportcsv_preparation' => 'Creances:cohorte_preparation',
		);

        protected function _setOptions() {
			$this->set( 'etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa') );
			$this->set( 'droitdevoirs', ClassRegistry::init('Calculdroitrsa')->enum('toppersdrodevorsa') );
			$this->set( 'orgcre', ClassRegistry::init('Creance')->enum('orgcre') );
			$this->set( 'motiindu', ClassRegistry::init('Creance')->enum('motiindu') );
			$this->set( 'natcre', ClassRegistry::init('Creance')->enum('natcre') );
			$this->set( 'oriindu', ClassRegistry::init('Creance')->enum('oriindu') );
			$this->set( 'respindu', ClassRegistry::init('Creance')->enum('respindu') );
		}

		/**
		 * Moteur de recherche par creances
		 *
		 * @return void
		 */
		public function search() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesCreances' );
			$Recherches->search();
		}

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
						$this->Creance->fields(),
						array(
							$this->Creance->Fichiermodule->sqNbFichiersLies( $this->Creance, 'nb_fichiers_lies' )
						)
					),
					'conditions' => array(
						'Creance.foyer_id' => $foyer_id
					),
					'contain' => false,
					'order' => array(
						'Creance.dtimplcre DESC',
					)
				)
			);
			if ( !empty($creances) ){
				$creances[0]['Creance']['etatDepuis'] = __d('creance', 'ENUM::ETAT::' . $creances[0]['Creance']['etat']) . __m('since') . date('d/m/Y', strtotime( $creances[0]['Creance']['modified'] ) );
			}
			$histoDeleted = $this->Historiqueetat->getHisto($this->Creance->name, $foyer_id, 'delete');

			// Assignations à la vue
			$this->set( 'foyer_id', $foyer_id );
			$this->set( 'creances', $creances );
			$this->set( 'histoDeleted', $histoDeleted );
			$this->set( 'urlmenu', '/creances/index/'.$foyer_id );
		}

		/**
		 * Pagination sur les <éléments> de la table. *
		 * @param integer $creance_id L'id technique de la créance à affiché
		 *
		 */
		public function view($creance_id) {
			$this->WebrsaAccesses->check($creance_id);
			$this->Creance->id = $creance_id;
			$foyer_id = $this->Creance->field( 'foyer_id' );

			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array( 'foyer_id' => $foyer_id )));

			$this->set( 'options', $this->Creance->enums() );

			$creances = $this->WebrsaAccesses->getIndexRecords(
				$creance_id,
				array(
					'fields' => array_merge(
						$this->Creance->fields(),
						array(
							$this->Creance->Fichiermodule->sqNbFichiersLies( $this->Creance, 'nb_fichiers_lies' )
						)
					),
					'conditions' => array(
						'Creance.id' => $creance_id
					),
					'contain' => false,
					'order' => array(
						'Creance.dtimplcre DESC',
					)
				)
			);

			//ListMotifs
			$listMotifs = $this->Creance->Motifemissioncreance->find(
				'list',
				array(
					'fields' => array ('id', 'nom')
				)
			);
			$this->set( 'listMotifs', $listMotifs );

			// Historique de la créance
			$historiques = $this->Historiqueetat->getHisto($this->Creance->name, $creance_id, null, $foyer_id);
			foreach($historiques as $key => $histo ) {
				$historiques[$key]['Historiqueetat']['etat'] = (__d('creance', 'ENUM::ETAT::' . $histo['Historiqueetat']['etat']));
			}

			// Assignations à la vue
			$this->set( 'historiques', $historiques );
			$this->set( 'foyer_id', $foyer_id );
			$this->set( 'creances', $creances );
			$this->set( 'urlmenu', '/creances/view/'.$creance_id );
		}

		/**
		 * Ajouter une creances à un foyer
		 *
		 * @param integer $foyer_id L'id technique du foyer auquel ajouter la créance
		 * @return void
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Modification d'une creance du foyer.
		 *
		 * @param integer $id L'id technique dans la table creances.
		 * @return void
		 */
		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Fonction commune d'ajout/modification d'une créances
		 *
		 * @param integer $id
		 * 		Soit l'id technique du foyer auquel ajouter la créance
		 * 		Soit l'id technique dans la table creances.
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
					$data['Creance']['mtsolreelcretrans'] = floatval($data['Creance']['mtsolreelcretrans']);
					$data['Creance']['mtinicre'] = floatval($data['Creance']['mtinicre']);
					if($this->action == 'add' ) {
						$data['Creance']['foyer_id'] = $foyer_id;
					}
					if( $this->Creance->saveAll( $data, array( 'validate' => 'only' ) ) &&
						$this->Creance->save( $data ) &&
						$this->Historiqueetat->setHisto(
							$this->Creance->name,
							$this->Creance->id,
							$data['Creance']['foyer_id'],
							__FUNCTION__,
							'ATTAVIS',
							$data['Creance']['foyer_id'] )
					) {
						$this->Creance->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'controller' => 'Creances', 'action' => 'index', $foyer_id ) );
					} else {
						$this->Creance->rollback();
						$this->Flash->error( __( 'Save->error' ) );
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

		/**
		 * Fonction de mise en nonemission
		 *
		 * @param integer $id - l'id technique dans la table creances.
		 * @return void
		 *
		 */
		public function nonemission($id = null) {
			$this->WebrsaAccesses->check($id);
			$this->Creance->id = $id;
			$foyer_id = $this->Creance->field( 'foyer_id' );
			$dossier_id = $this->Creance->dossierId( $id );

			$this->set(
				'dossierMenu',
				$this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) )
			);
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
				$data['Creance']['motifemissioncreance_id'] = $data['Creance']['Motifemissioncreance'];

				if( $this->Creance->saveAll( $data, array( 'validate' => 'only' ) ) &&
					$this->Creance->save( $data ) &&
					$this->Historiqueetat->setHisto(
						$this->Creance->name,
						$id,
						$data['Creance']['foyer_id'],
						__FUNCTION__,
						$data['Creance']['etat'],
						$data['Creance']['foyer_id'] )
				) {
					$this->Creance->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'Creances', 'action' => 'index', $foyer_id ) );
				} else {
					$this->Creance->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			// Affichage des données
			else {
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
					$this->request->data['Creance']['Motifemissioncreance'] = $this->request->data['Creance']['motifemissioncreance_id'];
				}
			}

			//ListMotifs
			$listMotifs = $this->Creance->Motifemissioncreance->find(
				'list',
				array(
					'fields' => array ('id', 'nom')
				)
			);
			$this->set( 'listMotifs', $listMotifs );

			// Assignation à la vue
			$this->set( 'options', $this->Creance->enums() );
			$this->set( 'urlmenu', '/creances/index/'.$foyer_id );
			$this->set( 'foyer_id', $foyer_id );
			$this->render( 'nonemission' );
		}

		/**
		 * Fonction de mise en validation
		 *
		 * @param integer $id - l'id technique dans la table creances.
		 * @return void
		 *
		 */
		public function validation($id = null) {
			$this->WebrsaAccesses->check($id);
			$this->Creance->id = $id;
			$foyer_id = $this->Creance->field( 'foyer_id' );
			$dossier_id = $this->Creance->dossierId( $id );

			$this->set(
				'dossierMenu',
				$this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) )
			);
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
				if ( $data['Creance']['validation'] == 1){
					//verification de l'état post validation
					$query = array (
						'fields' => array (
							'emissiontitre'
						),
						'conditions' => array (
							'Motifemissioncreance.id' => $data['Creance']['motifemissioncreance_id']
						),
						'contain' => FALSE
					);
					$emissionValidation = $this->Creance->Motifemissioncreance->find('first',$query);
					if ( $emissionValidation['Motifemissioncreance']['emissiontitre'] == 1 ){
						$data['Creance']['etat'] = 'AEMETTRE';
					}else{
						$data['Creance']['etat'] = 'NONEMISSION';
					}
				}else{
					 $data['Creance']['etat'] = 'ATTAVIS';
				}

				if( $this->Creance->saveAll( $data, array( 'validate' => 'only' ) ) ) {
					if( $this->Creance->save( $data ) ) {
						$this->Historiqueetat->setHisto(
							$this->Creance->name,
							$id,
							$foyer_id,
							__FUNCTION__,
							$data['Creance']['etat'],
							$foyer_id
						);
						$this->Creance->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'controller' => 'Creances', 'action' => 'index', $foyer_id ) );
					} else {
						$this->Creance->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
			}
			// Affichage des données
			else {
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
					$this->request->data['Creance']['Motifemissioncreance'] = $this->request->data['Creance']['motifemissioncreance_id'];
				}
			}

			//ListMotifs
			$listMotifs = $this->Creance->Motifemissioncreance->find(
				'list',
				array(
					'fields' => array ('id', 'nom')
				)
			);
			$this->set( 'listMotifs', $listMotifs );

			// Assignation à la vue
			$this->set( 'options', $this->Creance->enums() );
			$this->set( 'urlmenu', '/creances/index/'.$foyer_id );
			$this->set( 'foyer_id', $foyer_id );
			$this->render( 'validation' );
		}

		/**
		 * Supprimée une creance d'un foyer en copiant une autre créance
		 *
		 * @param integer $id L'id technique de la créance a supprimée
		 * @return void
		 */
		public function delete($id) {
				$this->WebrsaAccesses->check($id);
				$foyer_id = $this->Creance->field( 'foyer_id' );
				$dossier_id = $this->Creance->dossierId( $id );

				$creanceQuery =  array(
						'fields' =>	$this->Creance->fields(),
						'conditions' => array(
							'Creance.id' =>$id,
						),
						'contain' => array (
							'Titrecreancier' => array (
								'fields' => array ('Titrecreancier.id'),
							)
						)
					);
				$creances = $this->Creance->find('first',$creanceQuery);

				if ($creances['Titrecreancier']['id'] != null ) {
					$success = $this->Creance->query("DELETE FROM titrescreanciers WHERE id = ".$creances['Titrecreancier']['id']  );
				}

				$success = $this->Creance->delete( $id );
				if( $success ) {
					$this->Flash->success( __( 'Delete->success' ) );
				}
				else {
					$this->Flash->error( __( 'Delete->error' ) );
				}
				$this->redirect( array( 'controller' => 'creances', 'action' => 'index', $foyer_id ) );
		}

		/**
		 * Ajouter une creances à un foyer en copiant une autre créance
		 *
		 * @param integer $id L'id technique de la créance a copiée
		 * @return void
		 */
		public function copycreance($id) {
				$this->WebrsaAccesses->check($id);
				$foyer_id = $this->Creance->field( 'foyer_id' );
				$dossier_id = $this->Creance->dossierId( $id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index', $foyer_id) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$foyerQuery =  array(
					'fields' => array('Foyer.id',),
					'conditions' => array('Dossier.numdemrsa' => 	$this->request->data['Dossier']['numdemrsa']),
					'joins' => array($this->Dossier->join( 'Foyer', array( 'type' => 'INNER' ))),
					'recursive' => -1
				);

				$foyerResult = $this->Dossier->find('first',$foyerQuery);
				$this->request->data['Creance']['foyer_id'] = $foyerResult['Foyer']['id'];
				$this->Creance->begin();
				if( $this->Creance->saveAll( $this->request->data, array( 'validate' => 'only' ) ) &&
					$this->Creance->saveAll( $this->request->data, array( 'atomic' => false ) ) &&
					$this->Historiqueetat->setHisto(
						$this->Creance->name,
						$this->Creance->id,
						$foyerResult['Foyer']['id'],
						__FUNCTION__,
						$this->request->data['Creance']['etat'],
						$foyerResult['Foyer']['id'] )
				) {
					$this->Creance->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->Creance->query(" UPDATE creances SET mention = '' WHERE mention IS NULL AND creances.id = ".$id );
					$this->Creance->query(" UPDATE creances SET mention = ' Créance copiée vers le dossier ". $this->request->data['Dossier']['numdemrsa'] . " '|| creances.mention WHERE creances.id = ".$id );
					$this->redirect( array( 'controller' => 'creances', 'action' => 'index', $this->request->data['Creance']['foyer_id'] ) );
				} else {
					$this->Creance->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			// Affichage des données
			else {
				$creanceQuery =  array(
						'fields' =>	$this->Creance->fields(),
						'conditions' => array(
							'Creance.id' =>$id,
						),
						'contain' => array (
							'Foyer' => array (
								'fields' => array ('Foyer.dossier_id'),
								'Dossier' => array (
									'fields' => array ('Dossier.numdemrsa')
								)
							)
						)
					);
				$creances = $this->Creance->find('first',$creanceQuery);
				// Assignation au formulaire
				$this->request->data = $creances;
			}

			$this->set( 'options', $this->Creance->enums() );
			$this->render( 'copycreance' );
		}

		/**
		 * Ajouter une creances à un foyer depuis un rejet d'un flux
		 *
		 * @param integer $rejet_id L'id technique du rejet duquel prendre la créance
		 * @return void
		 */
		public function fluxadd($rejet_id) {
			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array('controller' => 'Rejetstalendscreances', 'action' => 'index', $rejet_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$foyerQuery =  array(
					'fields' => array('Foyer.id',),
					'conditions' => array('Dossier.numdemrsa' => 	$this->request->data['Dossier']['numdemrsa']),
					'joins' => array($this->Dossier->join( 'Foyer', array( 'type' => 'INNER' ))),
					'recursive' => -1
				);
				$foyerResult = $this->Dossier->find('first',$foyerQuery);
				$this->request->data['Creance']['foyer_id'] = $foyerResult['Foyer']['id'];

				$this->Creance->begin();
				if( $this->Creance->saveAll( $this->request->data, array( 'validate' => 'only' ) ) ) {
					if( $this->Creance->saveAll( $this->request->data, array( 'atomic' => false ) ) ) {
						$this->Creance->commit();
						$this->Flash->success( __( 'Save->success' ) );
						$this->Rejettalendcreance->query('UPDATE administration.rejetstalendscreances SET fusion = true WHERE rejetstalendscreances.id ='. $rejet_id);
						$this->redirect( array( 'controller' => 'creances', 'action' => 'index', $this->request->data['Creance']['foyer_id'] ) );
					}
					else {
						$this->Creance->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
				else {
					$this->Creance->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			// Afficage des données
			else {
				$rejetQuery =  array(
						'fields' => $this->Rejettalendcreance->fields(),
						'conditions' => array(
							'Rejettalendcreance.id' =>$rejet_id,
						),
						'contain' => FALSE
					);
				$rejetstalendscreances = $this->Rejettalendcreance->find('first',$rejetQuery);
				// Assignation au formulaire
				$this->request->data = $rejetstalendscreances;
			}

			$this->set( 'options', $this->Creance->enums() );
			$this->render( 'fluxadd' );
		}

		/**
		 * http://valums.com/ajax-upload/
		 * http://doc.ubuntu-fr.org/modules_php
		 * increase post_max_size and upload_max_filesize to 10M
		 * debug( array( ini_get( 'post_max_size' ), ini_get( 'upload_max_filesize' ) ) ); -> 10M
		 */
		public function ajaxfileupload() {
			$this->Fileuploader->ajaxfileupload();
		}

		/**
		 * http://valums.com/ajax-upload/
		 * http://doc.ubuntu-fr.org/modules_php
		 * increase post_max_size and upload_max_filesize to 10M
		 * debug( array( ini_get( 'post_max_size' ), ini_get( 'upload_max_filesize' ) ) ); -> 10M
		 * FIXME: traiter les valeurs de retour
		 */
		public function ajaxfiledelete() {
			$this->Fileuploader->ajaxfiledelete();
		}

		/**
		 *   Fonction permettant de visualiser les fichiers chargés dans la vue avant leur envoi sur le serveur
		 */
		public function fileview( $id ) {
			$this->Fileuploader->fileview( $id );
		}

		/**
		 *   Téléchargement des fichiers préalablement associés à un traitement donné
		 */
		public function download( $fichiermodule_id ) {
			$this->assert( !empty( $fichiermodule_id ), 'error404' );
			$this->Fileuploader->download( $fichiermodule_id );
		}

		/**
		 * Fonction permettant d'accéder à la page pour lier les fichiers.
		 *
		 * @param type $id
		 */
		public function filelink( $id ) {
			$this->WebrsaAccesses->check($id);
			$dossier_id = $this->Creance->dossierId( $id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$fichiers = array();

			$creances = $this->Creance->find(
				'first',
				array(
					'conditions' => array(
						'Creance.id' => $id
					),
					'contain' => array(
						'Fichiermodule' => array(
							'fields' => array( 'name', 'id', 'created', 'modified' )
						)
					)
				)
			);

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Creance->id = $id;
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $this->Creance->field( 'foyer_id' )) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Creance->begin();

				$saved = $this->Creance->updateAllUnBound(
					array( 'Creance.haspiecejointe' => '\''.$this->request->data['Creances']['haspiecejointe'].'\'' ),
					array( '"Creance"."id"' => $id)
				);

				if( $saved ) {
					// Sauvegarde des fichiers liés à une PDO
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, Set::classicExtract( $this->request->data, "Creance.haspiecejointe" ), $id ) && $saved;
				}

				if( $saved ) {
					$this->Creance->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('action' => 'filelink', $id));
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Creance->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->set( 'options', (array)Hash::get( $this->Creance->enums(), 'Creance' ) );
			$this->set( compact( 'dossier_id', 'personne_id', 'fichiers', 'creances' ) );
		}

		/**
		 *
		 */
		public function dossierEntrantsCreanciers() {
			$options = array(
				'annees' => array( 'minYear' => date( 'Y', strtotime('01-01-2009') ), 'maxYear' => date( 'Y' ) ),
			);
			$this->set( compact( 'options' ) );

			if( !empty( $this->request->data ) ) {
				$paginate = $this->Creance->search( $this->request->data );
				$paginate['limit'] = 15;

				$this->paginate = $paginate;
				$dossierEntrantsCreanciers = $this->paginate( 'Creance' );

				$this->set( 'dossierEntrantsCreanciers', $dossierEntrantsCreanciers );
			}
            $this->_setOptions();
		}

		/**
		 *
		 */
		public function exportcsv() {
			$options = $this->Creance->search( Hash::expand( $this->request->params['named'], '__' ) );

			unset( $options['limit'] );
			$dossierEntrantsCreanciers = $this->Creance->find( 'all', $options );

            $this->_setOptions();
			$this->layout = '';
			$this->set( compact( 'headers', 'dossierEntrantsCreanciers' ) );
		}

		/**
		 * Cohorte
		 */
		public function cohorte_preparation() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesCreances' );
			$Cohortes->cohorte( array( 'modelRechercheName' => 'WebrsaCohorteCreance' ) );
		}

		/**
		 * Export CSV
		 */
		public function exportcsv_preparation() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesCreances' );
			$Cohortes->exportcsv( array( 'modelRechercheName' => 'WebrsaCohorteCreance' ) );
		}

	}
?>
