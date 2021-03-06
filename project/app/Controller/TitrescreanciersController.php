<?php
	/**
	 * Code source de la classe TitrescreanciersController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'LocaleHelper', 'View/Helper' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
	App::uses( 'WebrsaAccessTitrescreanciers', 'Utility' );

	/**
	 * La classe TitrescreanciersController ...
	 *
	 * @package app.Controller
	 */
	class TitrescreanciersController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Titrescreanciers';

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
					'search',
					'cohorte_validation' => array('filter' => 'Search'),
					'cohorte_transmissioncompta' => array('filter' => 'Search'),
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
			'Search'
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
			'edit' => 'update',
			'delete' => 'delete',
			'suivi' => 'update',
			'comment' => 'update',
			'avis' => 'update',
			'valider' => 'update',
			'retourcompta' => 'update',
			'filelink' => 'read',
			'fileview' => 'read',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'update',
			'ajaxreffonct' => 'read',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Titrecreancier',
			'WebrsaTitrecreancier',
			'Titresuiviannulationreduction',
			'Creance',
			'WebrsaCreance',
			'Dossier',
			'Foyer',
			'Personne',
			'Adresse',
			'Adressefoyer',
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
			'fileview',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'view' => 'Titrescreanciers:index',
			'exportcsv_validation' => 'Titrescreanciers:cohorte_validation',
			'exportcsv_transmissioncompta' => 'Titrescreanciers:cohorte_transmissioncompta',
			'cohorte_transmissioncompta_exportzip' => 'Titrescreanciers:cohorte_transmissioncompta_exportfica',
		);

		/**
		 *
		 * Fonction de définition des options des selections
		 */
        protected function _setOptions() {
			$this->set( 'qual', ClassRegistry::init('Titrecreancier')->enum('qual') );
			$this->set( 'qualcjt', ClassRegistry::init('Titrecreancier')->enum('qual') );
			$this->set( 'etat', ClassRegistry::init('Titrecreancier')->enum('etat') );
			$this->set( 'typeadr', ClassRegistry::init('Titrecreancier')->enum('typeadr') );
			$this->set( 'etatadr', ClassRegistry::init('Titrecreancier')->enum('etatadr') );
		}

		/**
		 * Moteur de recherche par creances
		 *
		 * @return void
		 */
		public function search() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesTitrescreanciers' );
			$Recherches->search();
		}

		/**
		 *
		 * @param integer $creance_id L'id technique de la créance pour laquel on veut les Titre créanciers.
		 *
		 */
		public function index($creance_id) {
			$foyer_id = $this->Titrecreancier->foyerId( $creance_id );
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array( 'foyer_id' => $foyer_id )));

			$titresCreanciers = $this->WebrsaAccesses->getIndexRecords(
				$foyer_id,
				array(
					'fields' => array_merge(
						$this->Titrecreancier->fields()
						,array(
							$this->Titrecreancier->Fichiermodule->sqNbFichiersLies( $this->Titrecreancier, 'nb_fichiers_lies' )
						)
					),
					'conditions' => array(
						'Titrecreancier.creance_id' => $creance_id
					),
					'contain' => false,
					'order' => array(
						'Titrecreancier.dtemissiontitre DESC',
					)
				)
			);

			if ( !empty($titresCreanciers) ){
				$this->set( 'ajoutPossible', false);
				foreach ($titresCreanciers as $key => $titreCreancier) {
					$titresCreanciers[$key]['Titrecreancier']['etatDepuis'] =
						__d('titrecreancier',
							'ENUM::ETAT::' . $titresCreanciers[$key]['Titrecreancier']['etat'])
							. __m('since') . date('d/m/Y', strtotime( $titresCreanciers[$key]['Titrecreancier']['modified'] )
						);

					$titrecreancier_id = $titresCreanciers[$key]['Titrecreancier']['id'];
					$contentIndex = $this->Titresuiviannulationreduction->getContext();
					$query = $this->Titresuiviannulationreduction->getQuery($titrecreancier_id);
					$titresAnnRed = $this->WebrsaAccesses->getIndexRecords($foyer_id, $query, $contentIndex);
					$montantReduitTotal = 0;
					if( !empty($titresAnnRed) ) {
						foreach($titresAnnRed as $titres ) {
							if ( $titres['Titresuiviannulationreduction']['etat'] == 'CERTIMP' ) {
								$montantReduitTotal += $titres['Titresuiviannulationreduction']['mtreduit'];
							}
						}
					}
					$titresCreanciers[$key]['Titrecreancier']['soldetitr'] = $titresCreanciers[$key]['Titrecreancier']['mntinit'] - $montantReduitTotal;
					$titresCreanciers[$key]['Titrecreancier']['acommentaire'] = 0;
					if( !is_null($titresCreanciers[$key]['Titrecreancier']['mention'])  && !empty($titresCreanciers[$key]['Titrecreancier']['mention']) ) {
						$titresCreanciers[$key]['Titrecreancier']['acommentaire'] = 1;
					}

					$titresCreanciers[$key]['Titresuivit']['count'] = $this->Titrecreancier->getCount($titrecreancier_id);
				}
			}

			// Historique du titre - si suppression
			$histoDeleted = $this->Historiqueetat->getHisto($this->Titrecreancier->name, $creance_id, 'delete');

			// Assignations à la vue
			$this->set( 'options', array_merge(
					$this->Titrecreancier->options(),
					$this->Creance->enums()
				)
			);

			$this->set( 'histoDeleted', $histoDeleted );
			$this->set( 'foyer_id', $foyer_id );
			$this->set( 'titresCreanciers', $titresCreanciers );
			$this->set( 'urlmenu', '/creances/index/'.$foyer_id );
		}

		 /**
		 *
		 * @param integer $titrecreancier_id L'id technique du Titre créanciers à afficher.
		 *
		 */
		public function view($titrecreancier_id) {
			$titresCreanciersIDS = $this->Titrecreancier->find('first',
				array(
					'fields' => array (
						'Titrecreancier.id',
						'Titrecreancier.creance_id'
					),
					'conditions' => array(
						'Titrecreancier.id' => $titrecreancier_id
					),
					'contain' => false
				)
			);
			$creance_id  = $titresCreanciersIDS['Titrecreancier']['creance_id'];
			$foyer_id = $this->Titrecreancier->foyerId( $creance_id );
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array( 'foyer_id' => $foyer_id )));

			$titresCreanciers = $this->WebrsaAccesses->getIndexRecords(
				$foyer_id,
				array(
					'fields' => array_merge(
						$this->Titrecreancier->fields()
						,array(
							$this->Titrecreancier->Fichiermodule->sqNbFichiersLies( $this->Titrecreancier, 'nb_fichiers_lies' )
						)
					),
					'conditions' => array(
						'Titrecreancier.id' => $titrecreancier_id
					),
					'contain' => false,
					'order' => array(
						'Titrecreancier.dtemissiontitre DESC',
					)
				)
			);
			$creances = $this->Creance->find('first',
				array(
					'conditions' => array(
						'Creance.id ' => $creance_id
					),
					'contain' => false
				)
			);

			// Historique de la créance
			$historiques = $this->Historiqueetat->getHisto($this->Titrecreancier->name, $titrecreancier_id, null, $creance_id);
			foreach($historiques as $key => $histo ) {
				$historiques[$key]['Historiqueetat']['etat'] = (__d('titrecreancier', 'ENUM::ETAT::' . $histo['Historiqueetat']['etat']));
			}

			// Assignations à la vue
			$this->set( 'options', array_merge(
					$this->Titrecreancier->options(),
					$this->Creance->enums()
				)
			);

			//ListMotifs
			$listMotifs = $this->Titrecreancier->Motifemissiontitrecreancier->find(
				'list',
				array(
					'fields' => array ('id', 'nom')
				)
			);
			$this->set( 'listMotifs', $listMotifs );

			$this->set( 'creance_id', $creance_id );
			$this->set( 'historique', $historiques );
			$this->set( 'foyer_id', $foyer_id );
			$this->set( 'creances', $creances );
			$this->set( 'titresCreanciers', $titresCreanciers );
			$this->set( 'urlmenu', '/creances/index/'.$foyer_id );
		}

		/**
		 * Ajouter un Titrecreancier à une Créance
		 *
		 * @param integer $foyer_id L'id technique de la creance auquel ajouter le Titrecreancier
		 * @return void
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Modification d'un Titrecreancier d'une creance.
		 *
		 * @param integer $id L'id technique dans la table titrescreanciers.
		 * @return void
		 */
		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Supprimée un Titrecreancier d'une créance
		 *
		 * @param integer $id L'id technique du Titrecreancier a supprimée
		 * @return void
		 */
		public function delete($id) {
			$this->WebrsaAccesses->check($id);
			$titresCreanciersIDS = $this->Titrecreancier->find('first',
				array(
					'fields' => array (
						'Titrecreancier.id',
						'Titrecreancier.creance_id'
					),
					'conditions' => array(
						'Titrecreancier.id' => $id
					),
					'contain' => false
				)
			);
			$creance_id  = $titresCreanciersIDS['Titrecreancier']['creance_id'];

			if(
				$this->Titrecreancier->delete( $id ) &&
				$this->Creance->setEtatOnForeignChange(
					$creance_id,
					'SUP',
					__FUNCTION__
				) &&
				$this->Historiqueetat->setHisto(
					$this->Titrecreancier->name,
					$id,
					$creance_id,
					__FUNCTION__, 'SUP',
					$this->Titrecreancier->foyerId( $creance_id) )
			 ) {
				$this->Creance->commit();
				$this->Titrecreancier->commit();
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Flash->error( __( 'Delete->error' ) );
			}
			$this->redirect( array( 'controller' => 'creances', 'action' => 'index', $this->Titrecreancier->foyerId( $creance_id) ) );
		}

		/**
		 * Commenter un Titrecreancier
		 *
		 * @param integer $id L'id technique du titre
		 * @return void
		 */
		public function comment($id) {
			$titrecreancier = $this->Titrecreancier->find('first', array(
				'conditions' => array('Titrecreancier.id' => $id),
				'recursive' => 0
				)
			);

			$this->set('titrecreancier', $titrecreancier);
			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$creance_id = $this->Titrecreancier->creanceId( $id );
				$this->Titrecreancier->begin();
				$data = $this->request->data;
				if ($this->Titrecreancier->saveAll( $data, array( 'validate' => 'true') ) ) {
					$this->Titrecreancier->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index', $creance_id ) );
				} else {
					$this->Titrecreancier->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
		}


		/**
		 * Fonction commune d'ajout/modification d'un titrecreancier
		 *
		 * @param integer $id
		 * 		Soit l'id technique de la creance auquel ajouter le Titrecreancier
		 * 		Soit l'id technique dans la table titrescreanciers
		 * @return void
		 */
		protected function _add_edit( $id = null ) {
			if($this->action == 'add' ) {
				$creance_id = $id;
				$foyer_id = $this->Titrecreancier->foyerId( $creance_id );
				$dossier_id = $this->Titrecreancier->dossierId( $creance_id );
			}elseif($this->action == 'edit' ){
				$this->WebrsaAccesses->check($id);
				$creance_id = $this->Titrecreancier->creanceId( $id );
				$foyer_id = $this->Titrecreancier->foyerId( $creance_id );
				$dossier_id = $this->Titrecreancier->dossierId( $creance_id );
			}
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Titrecreancier->id = $id;
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $creance_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Titrecreancier->begin();
				$data = $this->request->data;

				//A traité -> instructionencours
				if ( $data['Titrecreancier']['instructionencours'] ){
					$data['Titrecreancier']['etat'] = 'INSTRUCTION' ;
				}elseif (
					$data['Titrecreancier']['etat'] == 'INSTRUCTION'
					&& !$data['Titrecreancier']['instructionencours']
				) {
					$data['Titrecreancier']['etat'] = 'ATTAVIS';
				}

				$data['Titrecreancier']['mntinit'] = $data['Titrecreancier']['mnttitr'];
				if ( $data['Titrecreancier']['mnttitr'] == '' ) {
					$this->Titrecreancier->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}else{
					if( $this->Titrecreancier->saveAll( $data, array( 'validate' => 'only' ) ) &&
						$this->Titrecreancier->saveAll( $data, array( 'atomic' => false ) ) &&
						$this->Creance->setEtatOnForeignChange($data['Titrecreancier']['creance_id'],$data['Titrecreancier']['etat'],__FUNCTION__) &&
						$this->Historiqueetat->setHisto(
							$this->Titrecreancier->name,
							$this->Titrecreancier->id,
							$data['Titrecreancier']['creance_id'],
							__FUNCTION__, $data['Titrecreancier']['etat'],
							$this->Titrecreancier->foyerId( $data['Titrecreancier']['creance_id']) )
					) {
						$this->Creance->commit();
						$this->Titrecreancier->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'action' => 'index', $creance_id ) );
					} else {
						$this->Titrecreancier->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
			}
			// Affichage des données
			elseif ($this->action != 'add' ) {
				$titrecreancier = $this->Titrecreancier->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Titrecreancier->fields()
						),
						'conditions' => array(
							'Titrecreancier.id' => $id
						),
						'contain' => FALSE
					)
				);

				// Mauvais paramètre
				$this->assert( !empty( $titrecreancier ), 'invalidParameter' );

				// Assignation au formulaire
				$this->request->data = $titrecreancier;
			}elseif ( $this->action == 'add'){
				$titrecreancier['Titrecreancier']['etat'] = 'CREE';
				$titrecreancier = $this->Titrecreancier->getInfoTitrecreancier($titrecreancier, $creance_id, $foyer_id );
				// Assignation au formulaire
				$this->request->data = $titrecreancier;
			}

			$creances = $this->Creance->find('all',
				array(
					'conditions' => array(
						'Creance.id ' => $creance_id
					),
					'contain' => false
				)
			);
			$this->set( 'creances', $creances );

			//Assignation a la vue
			$this->set( 'options', array_merge(
					$this->Titrecreancier->options(),
					$this->Creance->enums()
				)
			);
			$this->set( 'creance_id', $creance_id );
			$this->set( 'foyer_id', $foyer_id );

			$this->set( 'urlmenu', '/titrescreanciers/index/'.$creance_id );

			$this->render( 'add_edit' );
		}

		/**
		 * Ajouter un avis a un Titrecreancier
		 *
		 * @param integer $titrecreancier_id L'id technique ddu titrecreancier auquel ajouter l'avis
		 * @return void
		 */
		public function avis( $titrecreancier_id ) {
			$this->WebrsaAccesses->check( $titrecreancier_id );

			$creance_id = $this->Titrecreancier->creanceId( $titrecreancier_id );
			$foyer_id = $this->Titrecreancier->foyerId( $creance_id );
			$dossier_id = $this->Titrecreancier->dossierId( $creance_id );

			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Titrecreancier->id = $titrecreancier_id;
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $creance_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Titrecreancier->begin();
				$data = $this->request->data;

				//Gestion de l'état
				//A traité -> instructionencours
				if ( $data['Titrecreancier']['instructionencours'] ){
					$data['Titrecreancier']['etat'] = 'INSTRUCTION' ;
				}

				$data['Titrecreancier']['motifemissiontitrecreancier_id'] = $data['Titrecreancier']['Motifemissiontitrecreancier'];

				if( $this->Titrecreancier->saveAll( $data, array( 'validate' => 'only' ) ) &&
					$this->Titrecreancier->saveAll( $data, array( 'atomic' => false ) ) &&
					$this->Historiqueetat->setHisto(
						$this->Titrecreancier->name,
						$titrecreancier_id,
						$data['Titrecreancier']['creance_id'],
						__FUNCTION__,
						$data['Titrecreancier']['etat'],
						$this->Titrecreancier->foyerId($data['Titrecreancier']['creance_id'])
					)
				) {
						$this->Titrecreancier->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'action' => 'index', $creance_id ) );
				} else {
						$this->Titrecreancier->rollback();
						$this->Flash->error( __( 'Save->error' ) );
				}
			}
			// Affichage des données
			else {
				$titrecreancier = $this->Titrecreancier->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Titrecreancier->fields()
						),
						'conditions' => array(
							'Titrecreancier.id' => $titrecreancier_id
						),
						'contain' => FALSE
					)
				);

				$titrecreancier['Titrecreancier']['commentairevalidateur'] = "<b>".$titrecreancier['Titrecreancier']['commentairevalidateur'].'</b>';
				// Mauvais paramètre
				$this->assert( !empty( $titrecreancier ), 'invalidParameter' );
				// Assignation au formulaire
				$this->request->data = $titrecreancier;
			}

			//ListMotifs
			$listMotifs = $this->Titrecreancier->Motifemissiontitrecreancier->find(
				'list',
				array(
					'fields' => array ('id', 'nom')
				)
			);
			$this->set( 'listMotifs', $listMotifs );

			//Creance
			$creance = $this->Creance->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Creance->fields()
						),
						'conditions' => array(
							'Creance.id' => $titrecreancier['Titrecreancier']['creance_id']
						),
						'contain' => FALSE
					)
				);
			$this->set( 'creance', $creance );

			//Assignation a la vue
			$this->set( 'options', array_merge(
					$this->Titrecreancier->options(),
					$this->Creance->enums()
				)
			);
			$this->set( 'creance_id', $creance_id );
			$this->set( 'foyer_id', $foyer_id );

			$this->set( 'urlmenu', '/titrescreanciers/index/'.$creance_id );

			$this->render( 'avis' );
		}

		/**
		 * Validation d'une Titrecreancier d'une Créance
		 *
		 * @param integer $titrecreancier_id L'id technique dans la table titrescreanciers
		 * @return void
		 */
		public function valider( $titrecreancier_id ) {
			$this->WebrsaAccesses->check( $titrecreancier_id );

			$creance_id = $this->Titrecreancier->creanceId( $titrecreancier_id );
			$foyer_id = $this->Titrecreancier->foyerId( $creance_id );
			$dossier_id = $this->Titrecreancier->dossierId( $creance_id );

			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Titrecreancier->id = $titrecreancier_id;
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $creance_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Titrecreancier->begin();
				$data = $this->request->data;
				$data['Titrecreancier']['creance_id'] = $this->Titrecreancier->creanceId($titrecreancier_id);
				if ( $data['Titrecreancier']['validation'] == 1){
					 //verification de l'état post validation
					$query = array (
						'fields' => array (
							'emissiontitre'
						),
						'conditions' => array (
							'Motifemissiontitrecreancier.id' => $data['Titrecreancier']['motifemissiontitrecreancier_id']
						),
						'contain' => FALSE
					);
					$emissionValidation = $this->Titrecreancier->Motifemissiontitrecreancier->find('first',$query);
					if ( $emissionValidation['Motifemissiontitrecreancier']['emissiontitre'] == 1 ){
						$data['Titrecreancier']['etat'] = 'ATTENVOICOMPTA';
					}else{
						$data['Titrecreancier']['etat'] = 'NONVALID';
					}
				}else{
					 $data['Titrecreancier']['etat'] = 'ATTAVIS';
				}

				if( $this->Titrecreancier->saveAll( $data, array( 'validate' => 'only' ) ) &&
					$this->Titrecreancier->saveAll( $data, array( 'atomic' => false ) ) &&
					$this->Creance->setEtatOnForeignChange($data['Titrecreancier']['creance_id'],$data['Titrecreancier']['etat'],__FUNCTION__) &&
					$this->Historiqueetat->setHisto(
						$this->Titrecreancier->name,
						$titrecreancier_id,
						$data['Titrecreancier']['creance_id'],
						__FUNCTION__,
						$data['Titrecreancier']['etat'],
						$this->Titrecreancier->foyerId($data['Titrecreancier']['creance_id']) )
				) {
					$this->Creance->commit();
					$this->Titrecreancier->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index', $creance_id ) );
				} else {
					$this->Titrecreancier->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			// Affichage des données
			else {
				$titrecreancier = $this->Titrecreancier->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Titrecreancier->fields()
						),
						'conditions' => array(
							'Titrecreancier.id' => $titrecreancier_id
						),
						'contain' => FALSE
					)
				);

				// Mauvais paramètre
				$this->assert( !empty( $titrecreancier ), 'invalidParameter' );
				// Assignation au formulaire
				$this->request->data = $titrecreancier;

			}

			//ListMotifs
			$listMotifs = $this->Titrecreancier->Motifemissiontitrecreancier->find(
				'list',
				array(
					'fields' => array ('id', 'nom')
				)
			);
			$this->set( 'listMotifs', $listMotifs );

			//Creance
			$creance = $this->Creance->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Creance->fields()
						),
						'conditions' => array(
							'Creance.id' => $titrecreancier['Titrecreancier']['creance_id']
						),
						'contain' => FALSE
					)
				);
			$this->set( 'creance', $creance );

			//Assignation a la vue
			$this->set( 'options', array_merge(
					$this->Titrecreancier->options(),
					$this->Creance->enums()
				)
			);
			$this->set( 'creance_id', $creance_id );
			$this->set( 'foyer_id', $foyer_id );

			$this->set( 'urlmenu', '/titrescreanciers/index/'.$creance_id );

			$this->render( 'valider' );
		}
		/**
		 * Validation d'une Titrecreancier d'une Créance
		 *
		 * @param integer $titrecreancier_id L'id technique dans la table titrescreanciers
		 * @return void
		 */
		public function exportfica( $titrecreancier_id ) {
			$this->WebrsaAccesses->check( $titrecreancier_id );

			$infosFICA = $this->Titrecreancier->buildFICA( array($titrecreancier_id) );
			if( empty( $infosFICA ) ) {
				throw new NotFoundException();
			}

			//Initialisation
			$this->Titrecreancier->begin();
			$value['Titrecreancier']['id'] = $titrecreancier_id;
			$value['Titrecreancier']['creance_id'] = $this->Titrecreancier->creanceId($titrecreancier_id);
			$value['Titrecreancier']['etat'] = 'ATTRETOURCOMPTA';

			//Validation de la sauvegarde
			if( $this->Titrecreancier->saveAll( $value, array( 'validate' => 'only' ) ) &&
				$this->Titrecreancier->saveAll( $value, array( 'atomic' => false ) ) &&
				$this->Historiqueetat->setHisto(
					$this->Titrecreancier->name,
					$titrecreancier_id,
					$value['Titrecreancier']['creance_id'],
					__FUNCTION__,
					$value['Titrecreancier']['etat'],
					$this->Titrecreancier->foyerId($value['Titrecreancier']['creance_id'])
				)
			) {
				$success = true;
			} else {
					$success = false;
			}

			if ( !$success ) {
				$this->Titrecreancier->rollback();
				$this->Titrecreancier->id = $titrecreancier_id;
				$this->redirect( array( 'action' => 'index', $this->Titrecreancier->creanceId($titrecreancier_id) ) );
			}else{
				$this->Titrecreancier->commit();
			}

			$csvfile = 'FICA'.Configure::read('Creances.FICA.NumAppliTiers').'.csv';
			$options = $this->Titrecreancier->options();

			$this->set( compact(  'options','infosFICA','csvfile'  ) );

		}

		/**
		 * Fonction commune d'ajout/modification d'un titrecreancier
		 *
		 * @param integer $id
		 * 		Soit l'id technique de la creance auquel ajouter le Titrecreancier
		 * 		Soit l'id technique dans la table titrescreanciers
		 * @return void
		 */
		public function retourcompta(  $titrecreancier_id  ) {
			$this->WebrsaAccesses->check( $titrecreancier_id );

			$creance_id = $this->Titrecreancier->creanceId( $titrecreancier_id );
			$foyer_id = $this->Titrecreancier->foyerId( $creance_id );
			$dossier_id = $this->Titrecreancier->dossierId( $creance_id );

			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Titrecreancier->id = $titrecreancier_id;
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $creance_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Titrecreancier->begin();
				$data = $this->request->data;
				if( $this->Titrecreancier->saveAll( $data, array( 'validate' => 'only' ) ) &&
					$this->Titrecreancier->saveAll( $data, array( 'atomic' => false ) ) &&
					$this->Creance->setEtatOnForeignChange($data['Titrecreancier']['creance_id'],$data['Titrecreancier']['etat'],__FUNCTION__) &&
					$this->Historiqueetat->setHisto(
						$this->Titrecreancier->name,
						$titrecreancier_id,
						$data['Titrecreancier']['creance_id'],
						__FUNCTION__,
						$data['Titrecreancier']['etat'],
						$this->Titrecreancier->foyerId($data['Titrecreancier']['creance_id'])
					)
				) {
						$this->Creance->commit();
						$this->Titrecreancier->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'action' => 'index', $creance_id ) );
				} else {
						$this->Titrecreancier->rollback();
						$this->Flash->error( __( 'Save->error' ) );
				}
			}
			// Affichage des données
			else {
				$titrecreancier = $this->Titrecreancier->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Titrecreancier->fields()
						),
						'conditions' => array(
							'Titrecreancier.id' => $titrecreancier_id
						),
						'contain' => FALSE
					)
				);

				// Mauvais paramètre
				$this->assert( !empty( $titrecreancier ), 'invalidParameter' );
				// Assignation au formulaire
				$this->request->data = $titrecreancier;
			}

			//Assignation a la vue
			$this->set( 'options', array_merge(
					$this->Titrecreancier->options(),
					$this->Creance->enums()
				)
			);
			$this->set( 'creance_id', $creance_id );
			$this->set( 'foyer_id', $foyer_id );

			$this->set( 'urlmenu', '/titrescreanciers/index/'.$creance_id );

			$this->render( 'retourcompta' );
		}

		/**
		 * Génération des informations récuperer pour la créaction d'un titre créancier.
		 *
		 * @param array $titrecreancier tableau d'informations de base
		 * @param integer $creance_id id technique de la créance dont récuperer les infos
		 * @param integer $foyer_id id technique du foyer dont récuperer les infos
		 *
		 * @return array $titrecreancier tableau d'informations remplis
		 *
		**/
		private function _getInfoTitrecreancier($titrecreancier =array(), $creance_id = null, $foyer_id = null ){

			if (!is_null($creance_id)){
				/* get value from Créance */
				$creances = $this->Creance->find('first',
					array(
						'conditions' => array(
							'Creance.id ' => $creance_id
						),
						'contain' => false
					)
				);
				if ( !empty ($creances['Creance'] ) ) {
					$titrecreancier['Titrecreancier']['mnttitr'] = $creances['Creance']['mtsolreelcretrans'];
				}
			}

			if (!is_null($foyer_id)){
				/* get nom, prénom, nir du bénéficiaire */
				$personne = $this->Personne->find('first',
					array(
						'conditions' => array(
							'Foyer.id ' => $foyer_id,
							'Prestation.rolepers' => 'DEM'
						),
						'contain' => array (
							'Foyer',
							'Prestation'
						)
					)
				);
				if ( !empty ($personne['Personne'] ) ) {
					$titrecreancier['Titrecreancier']['qual'] = $personne['Personne']['qual'] ;
					$titrecreancier['Titrecreancier']['nom'] = $personne['Personne']['nom']." ". $personne['Personne']['prenom']  ;
					$titrecreancier['Titrecreancier']['nir'] = $personne['Personne']['nir'] ;
					$titrecreancier['Titrecreancier']['numtel'] =( $personne['Personne']['numfixe'] == null ) ? $personne['Personne']['numport'] : $personne['Personne']['numfixe'] ;
				}

				/* get nom, prénom, nir du bénéficiaire */
				$personne = $this->Personne->find('first',
					array(
						'conditions' => array(
							'Foyer.id ' => $foyer_id,
							'Prestation.rolepers' => 'CJT'
						),
						'contain' => array (
							'Foyer',
							'Prestation'
						)
					)
				);
				if ( !empty ($personne['Personne'] ) ) {
					$titrecreancier['Titrecreancier']['qualcjt'] = $personne['Personne']['qual'] ;
					$titrecreancier['Titrecreancier']['nomcjt'] = $personne['Personne']['nom']." ". $personne['Personne']['prenom']  ;
					$titrecreancier['Titrecreancier']['nircjt'] = $personne['Personne']['nir'] ;
				}

				/* get RIB from RIB foyer */
				$infoRib = $this->Foyer->find('first',
					array(
						'conditions' => array(
							'Foyer.id ' => $foyer_id
						),
						'contain' => array (
							'Paiementfoyer'
						)
					)
				);

				if ( !empty ($infoRib['Paiementfoyer'] ) ) {
					$titrecreancier['Titrecreancier']['titulairecompte'] =
						(__d('paiementfoyer', 'ENUM::TITURIB::'.$infoRib['Paiementfoyer'][0]['titurib'] ))
						.' '.$infoRib['Paiementfoyer'][0]['nomprenomtiturib'];

					$titrecreancier['Titrecreancier']['iban'] =
						$infoRib['Paiementfoyer'][0]['numdebiban']
						.$infoRib['Paiementfoyer'][0]['etaban']
						.$infoRib['Paiementfoyer'][0]['guiban']
						.$infoRib['Paiementfoyer'][0]['numcomptban']
						.$infoRib['Paiementfoyer'][0]['clerib']
						.$infoRib['Paiementfoyer'][0]['numfiniban'] ;
					$titrecreancier['Titrecreancier']['bic'] = $infoRib['Paiementfoyer'][0]['bic'];
					$titrecreancier['Titrecreancier']['comban'] = $infoRib['Paiementfoyer'][0]['comban'];
				}

				/* get Adresse from Adresse foyer */
				$infoAdress = $this->Adressefoyer->find('all',
					array(
						'conditions' => array(
							'Foyer.id ' => $foyer_id,
							'Adressefoyer.rgadr' => '01'
						),
						'contain' => array (
							'Adresse',
							'Foyer'
						)
					)
				);
				$titrecreancier['Titrecreancier']['dtemm'] = $infoAdress[0]['Adressefoyer']['dtemm'];
				$titrecreancier['Titrecreancier']['typeadr'] = $infoAdress[0]['Adressefoyer']['typeadr'];
				$titrecreancier['Titrecreancier']['etatadr'] = $infoAdress[0]['Adressefoyer']['etatadr'];
				$titrecreancier['Titrecreancier']['complete'] = $infoAdress[0]['Adresse']['complete'];
				$titrecreancier['Titrecreancier']['localite'] = $infoAdress[0]['Adresse']['localite'];
			}

			return $titrecreancier;
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

			$creance_id = $this->Titrecreancier->creanceId( $id );
			$foyer_id = $this->Titrecreancier->foyerId( $creance_id );
			$dossier_id = $this->Titrecreancier->dossierId( $creance_id );

			$this->assert( !empty( $dossier_id ), 'invalidParameter' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$fichiers = array();

			$titrescreanciers = $this->Titrecreancier->find(
				'first',
				array(
					'conditions' => array(
						'Titrecreancier.id' => $id
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
				$this->Titrecreancier->id = $id;
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $creance_id) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Titrecreancier->begin();

				$saved = $this->Titrecreancier->updateAllUnBound(
					array( 'Titrecreancier.haspiecejointe' => '\''.$this->request->data['Titrescreanciers']['haspiecejointe'].'\'' ),
					array( '"Titrecreancier"."id"' => $id)
				);

				if( $saved ) {
					// Sauvegarde des fichiers liés à une PDO
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, Set::classicExtract( $this->request->data, "Titrecreancier.haspiecejointe" ), $id ) && $saved;
				}

				if( $saved ) {
					$this->Titrecreancier->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('action' => 'filelink', $id));
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Titrecreancier->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->set( 'options', (array)Hash::get( $this->Titrecreancier->options(), 'Titrecreancier' ) );
			$this->set( compact( 'dossier_id', 'personne_id', 'fichiers', 'titrescreanciers' ) );
		}

		/**
		 * Cohorte
		 */
		public function csv_retourcompta() {
			//Initialisation des variables
			$uploadData = '';
			$titrescreanciers = array();

			//Vérification de l'existance d'un fichier
            if( !empty($this->request->data) ){
	            if( !empty($this->request->data['Titrecreancier']['file']['name']) ){
					//Récupération des infos du fichier
					$fileName = $this->request->data['Titrecreancier']['file']['name'];
					$uploadPath = 'uploads/files/';
					$uploadFile = $uploadPath.$fileName;
					$tmpFileName = $this->request->data['Titrecreancier']['file']['tmp_name'];
					$filecontents = file_get_contents($tmpFileName);
					$filetype = $this->request->data['Titrecreancier']['file']['type'];
				}elseif ( !empty($this->request->data['Titrecreancier']['Fichier']['name'])  ) {
					//Récupération des infos du fichier
					$fileName = $this->request->data['Titrecreancier']['Fichier']['name'];
					$uploadPath = 'uploads/files/';
					$uploadFile = $uploadPath.$fileName;
					$tmpFileName = $this->request->data['Titrecreancier']['Fichier']['tmp_name'];
					$filecontents = file_get_contents($tmpFileName);
					$filetype = $this->request->data['Titrecreancier']['Fichier']['type'];
				}
				if ( !empty ($fileName) ){
					//Si le fichier est type CSV et que le contenu est lisible
					if(!empty($filecontents) && $filetype == 'text/csv' ){
						$lines = file( $tmpFileName );

						//Pour Chaque ligne du fichier
						foreach ( $lines as $key => $line ){
							$delimiteur = Configure::read( 'Titrescreanciers.csvfica.delimiteur' );
							$elements = explode($delimiteur,$line);

							$reftitrecreancier = Configure::read( 'Titrescreanciers.csvfica.fieldid.reftitrecreancier' );
							$refdtbordereau = Configure::read( 'Titrescreanciers.csvfica.fieldid.dtbordereau' );
							$refnumtier = 	Configure::read( 'Titrescreanciers.csvfica.fieldid.numtier' );
							$refnumbordereau = Configure::read( 'Titrescreanciers.csvfica.fieldid.numbordereau' );
							$refnumtitr = Configure::read( 'Titrescreanciers.csvfica.fieldid.numtitr' );

							//Si on as une référence de Titre créancier et qu'elle est numérique
							if( isset($elements[$reftitrecreancier]) && is_numeric($elements[$reftitrecreancier]) ){
								$titrecreancier_id = $elements[$reftitrecreancier];
								// On cherche le titre créancier correspondant
								$titrecreancier = $this->Titrecreancier->find('first',
									array(
										'fields' => $this->Titrecreancier->fields(),
										'conditions' => array(
											'Titrecreancier.id' => $titrecreancier_id
										),
										'contain' => false
									)
								);

								// Si le titre créancier existe
								if ( !empty($titrecreancier) ){
									//On récupère dans le fichier les éléments attendus
									if( isset($elements[$refdtbordereau]) ){
										//A voir si on doit testé la qualité et le type YYYY-mm-dd ou dd-MM-YYYY de la date
										$dtbordereau = trim( $elements[$refdtbordereau] );
										$titrecreancier['Titrecreancier']['dtbordereau'] = $dtbordereau;
									}

									if( isset($elements[$refnumtier]) ){
										$numtier = trim( $elements[$refnumtier] );
										$titrecreancier['Titrecreancier']['numtier'] = $numtier;
									}

									if( isset($elements[$refnumbordereau]) && is_numeric($elements[$refnumbordereau]) ){
										$numbordereau = trim( $elements[$refnumbordereau] );
										$titrecreancier['Titrecreancier']['numbordereau'] = $numbordereau;
									}

									if( isset($elements[$refnumtitr]) ){
										$numtitr = trim( $elements[$refnumtitr] );
										$titrecreancier['Titrecreancier']['numtitr'] = $numtitr;
									}

									//On set l'état du titre créancier
									$titrecreancier['Titrecreancier']['etat'] = 'TITREEMIS';

									//On agrége le tout en tableau pour la vue
									$titrescreanciers[] = $titrecreancier;
								}else{
									$abandonedlines[]['ligneperdu'] = $elements;
								}
							}else{
								$abandonedlines[$key]['ligneperdu'] = $elements;
							}
						}

						/*
						// En cas de besoin de sauvegarde en BDD ou sur serveur du contenu du fichier au future.
						if(move_uploaded_file($this->request->data['file']['tmp_name'],$uploadFile)){
							$uploadData = $this->Files->newEntity();
							$uploadData->name = $fileName;
							$uploadData->path = $uploadPath;
							$uploadData->created = date("Y-m-d H:i:s");
							$uploadData->modified = date("Y-m-d H:i:s");
							if ($this->Files->save($uploadData)) {
								$this->Flash->success(__('File has been uploaded and inserted successfully.'));
							}else{
								$this->Flash->error(__('Unable to upload file, please try again.'));
							}
						}else{
							$this->Flash->error(__('Unable to upload file, please try again.'));
						}
						*/

						//Set des éléments pour la vue.
						$this->set( 'options', (array)Hash::get( $this->Titrecreancier->options(), 'Titrecreancier' ) );
						$this->set( compact( 'titrescreanciers', 'abandonedlines' ) );

					}else{
						$this->Flash->error(__('Unable to upload file, please try again.'));
					}
				}elseif(  !empty($this->request->data['Save']) ){
					if ( $this->request->data['Save'] == 'Enregistrer' ){
						$fail = false;

						foreach ($this->request->data['Titrecreancier'] as $Titrecreancier){

							//Formatage de la data
							$data['Titrecreancier']['id'] = $Titrecreancier['id'];
							$data['Titrecreancier']['creance_id'] = $Titrecreancier['creance_id'];
							$data['Titrecreancier']['numtitr'] = $Titrecreancier['numtitr'];
							$data['Titrecreancier']['dtbordereau'] = $Titrecreancier['dtbordereau'];
							$data['Titrecreancier']['numbordereau'] = $Titrecreancier['numbordereau'];
							$data['Titrecreancier']['numtier'] = $Titrecreancier['numtier'];
							$data['Titrecreancier']['etat'] = $Titrecreancier['etat'];

							$creance_id = $Titrecreancier['creance_id'];
							$etat = $Titrecreancier['etat'];

							$dossier_id = $this->Titrecreancier->dossierId( $creance_id );
							$this->Jetons2->get( $dossier_id );

							//Essay de Sauvegarde
							if( $this->Titrecreancier->saveAll( $data, array( 'validate' => 'only' ) ) ) {
								//Sauvegarde
								if( $this->Titrecreancier->saveAll( $data, array( 'atomic' => false ) ) ) {
									// Sauvegarde du changement d'état des créances & historique du changement d'état du titre
									if (
										$this->Creance->setEtatOnForeignChange($creance_id,$etat,__FUNCTION__) &&
										$this->Historiqueetat->setHisto(
											$this->Titrecreancier->name,
											$data['Titrecreancier']['id'],
											$data['Titrecreancier']['creance_id'],
											__FUNCTION__,
											$data['Titrecreancier']['etat'],
											$this->Titrecreancier->foyerId($data['Titrecreancier']['creance_id'])
											)
									){
										$this->Jetons2->release( $dossier_id );
									}else {
										$fail = true;
										break;
									}
								}else {
									$fail = true;
									break;
								}
							}else {
								$fail = true;
								break;
							}
						}

						if ( $fail ) {
							$this->Creance->rollback();
							$this->Titrecreancier->rollback();
							$this->Flash->error( __( 'Save->error' ) );
						} else {
							$this->Creance->commit();
							$this->Titrecreancier->commit();
							$this->Flash->success( __( 'Save->success' ) );
						}

						$this->redirect( array( 'action' => 'search' ) );
					}
				} elseif( isset( $this->request->data['Cancel'] ) ) {
					// Retour au chargement du fichier en cas d'annulation
					$this->redirect( array( 'action' => 'search' ) );
				}
			}
	        $this->set('uploadData', $uploadData);
		}

		/**
		 * Cohorte
		 */
		public function cohorte_validation() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesTitrescreanciers' );
			$Cohortes->cohorte( array( 'modelRechercheName' => 'WebrsaCohorteTitrecreancierValidation' ) );
		}

		/**
		 * Export CSV Validation Cohorte
		 */
		public function exportcsv_validation() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesTitrescreanciers' );
			$Cohortes->exportcsv( array( 'modelRechercheName' => 'WebrsaCohorteTitrecreancierValidation' ) );
		}

		/**
		 * Export CSV des recherches
		 */
		public function exportcsv() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesTitrescreanciers' );
			$Recherches->exportcsv();
		}

		/**
		 * Cohorte
		 */
		public function cohorte_transmissioncompta() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesTitrescreanciers' );
			$Cohortes->cohorte( array( 'modelRechercheName' => 'WebrsaCohorteTitrecreancierTransmissioncompta' ) );
		}

		/**
		 * Cohorte
		 */
		public function cohorte_transmissioncompta_exportfica() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesTitrescreanciers' );
			$Cohortes->exportFICA(
				array(
					'modelRechercheName' => 'WebrsaCohorteTitrecreancierTransmissioncompta',
					'configurableQueryFieldsKey' => 'Titrescreanciers.cohorte_fica'
				));
		}

		/**
		 * Cohorte
		 */
		public function cohorte_transmissioncompta_exportzip() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesTitrescreanciers' );
			$Cohortes->exportZIP(
				array(
					'modelRechercheName' => 'WebrsaCohorteTitrecreancierTransmissioncompta',
					'configurableQueryFieldsKey' => 'Titrescreanciers.cohorte_zip'
				));
		}

		/**
		 * Export CSV
		 */
		public function exportcsv_transmissioncompta() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesTitrescreanciers' );
			$Cohortes->exportcsv( array( 'modelRechercheName' => 'WebrsaCohorteTitrecreancierTransmissioncompta' ) );
		}

	}
?>
