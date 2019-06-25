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
			'Default',
			'DossiersMenus',
			'Jetons2',
			'WebrsaAccesses',
			'Fileuploader',
			'Search.SearchPrg' => array(
				'actions' => array(
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
			'suivit' => 'update',
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
		 * @param integer $creance_id L'id technique de la créance pour laquel on veut les Titre créanciers.
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
			$historique = $this->Historiqueetat->getHisto($this->Titrecreancier->name, $titrecreancier_id, null, $creance_id);

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
			$this->set( 'historique', $historique );
			$this->set( 'foyer_id', $foyer_id );
			$this->set( 'creances', $creances );
			$this->set( 'titresCreanciers', $titresCreanciers );
			$this->set( 'urlmenu', '/creances/index/'.$foyer_id );
		}

		/**
		 * Ajouter une Titrecreancier à une Créance
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
					if( $this->Titrecreancier->saveAll( $data, array( 'validate' => 'only' ) ) ) {
						if( $this->Titrecreancier->saveAll( $data, array( 'atomic' => false ) ) ) {
							if (
								$this->Creance->setEtatOnForeignChange($data['Titrecreancier']['creance_id'],$data['Titrecreancier']['etat'])
							){
								$this->Titrecreancier->commit();
								$this->Jetons2->release( $dossier_id );
								$this->Flash->success( __( 'Save->success' ) );
								$this->redirect( array( 'action' => 'index', $creance_id ) );
							}
							else {
								$this->Titrecreancier->rollback();
								$this->Flash->error( __( 'Save->error' ) );
							}
						}
						else {
							$this->Titrecreancier->rollback();
							$this->Flash->error( __( 'Save->error' ) );
						}
					}
					else {
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
				$titrecreancier = $this->_getInfoTitrecreancier($titrecreancier, $creance_id, $foyer_id );
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

				if( $this->Titrecreancier->saveAll( $data, array( 'validate' => 'only' ) ) ) {
					if( $this->Titrecreancier->saveAll( $data, array( 'atomic' => false ) ) ) {
						$this->Titrecreancier->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'action' => 'index', $creance_id ) );
					}
					else {
						$this->Titrecreancier->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
				else {
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
					debug ($emissionValidation);
					if ( $emissionValidation['Motifemissiontitrecreancier']['emissiontitre'] == 1 ){
						$data['Titrecreancier']['etat'] = 'ATTENVOICOMPTA';
					}else{
						$data['Titrecreancier']['etat'] = 'NONVALID';
					}
				}else{
					 $data['Titrecreancier']['etat'] = 'ATTAVIS';
				}

				if( $this->Titrecreancier->saveAll( $data, array( 'validate' => 'only' ) ) ) {
					if( $this->Titrecreancier->saveAll( $data, array( 'atomic' => false ) ) ) {
						if (
								$this->Creance->setEtatOnForeignChange($data['Titrecreancier']['creance_id'],$data['Titrecreancier']['etat'])
							){
								$this->Titrecreancier->commit();
								$this->Jetons2->release( $dossier_id );
								$this->Flash->success( __( 'Save->success' ) );
								$this->redirect( array( 'action' => 'index', $creance_id ) );
							}
							else {
								$this->Titrecreancier->rollback();
								$this->Flash->error( __( 'Save->error' ) );
							}
					}
					else {
						$this->Titrecreancier->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
				else {
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
			$listMotifs = $this->Creance->Motifemissioncreance->find(
				'list',
				array(
					'fields' => array ('id', 'nom')
				)
			);
			$this->set( 'listMotifs', $listMotifs );

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
				$this->redirect( array( 'action' => 'index', $titrecreancier_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Titrecreancier->begin();
				$data = $this->request->data;
				if( $this->Titrecreancier->saveAll( $data, array( 'validate' => 'only' ) ) ) {
					if( $this->Titrecreancier->saveAll( $data, array( 'atomic' => false ) ) ) {
						if (
								$this->Creance->setEtatOnForeignChange($data['Titrecreancier']['creance_id'],$data['Titrecreancier']['etat'])
							){
								$this->Titrecreancier->commit();
								$this->Jetons2->release( $dossier_id );
								$this->Flash->success( __( 'Save->success' ) );
								$this->redirect( array( 'action' => 'index', $creance_id ) );
							}
							else {
								$this->Titrecreancier->rollback();
								$this->Flash->error( __( 'Save->error' ) );
							}
					}
					else {
						$this->Titrecreancier->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
				else {
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
			$listMotifs = $this->Creance->Motifemissioncreance->find(
				'list',
				array(
					'fields' => array ('id', 'nom')
				)
			);
			$this->set( 'listMotifs', $listMotifs );

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
					$valTiturib = array (
						"MEL" => "Monsieur et mademoiselle",
						"MEM" => "Monsieur et madame",
						"MLE" => "Mademoiselle",
						"MME" => "Madame",
						"MOL" => "Monsieur ou mademoiselle",
						"MOM" => "Monsieur ou madame",
						"MON" => "Monsieur",
						"RSO" => "Raison sociale"
					);

					$titrecreancier['Titrecreancier']['titulairecompte'] =
						$valTiturib [$infoRib['Paiementfoyer'][0]['titurib']] .' '
						.$infoRib['Paiementfoyer'][0]['nomprenomtiturib'];
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
	}
?>
