<?php
	/**
	 * Code source de la classe Dossierspcgs66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'WebrsaPdfUtility', 'Utility' );
	App::uses( 'WebrsaAccessDecisionsdossierspcgs66', 'Utility' );
	App::uses( 'ZipUtility', 'Utility' );

	/**
	 * La classe Dossierspcgs66Controller ... (CG 66).
	 *
	 * @package app.Controller
	 */
	class Dossierspcgs66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Dossierspcgs66';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes',
			'DossiersMenus',
			'Fileuploader',
			'Gedooo.Gedooo',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
					'search',
					'search_gestionnaire',
					'search_affectes',
					'cohorte_imprimer',
					'cohorte_enattenteaffectation' => array(
						'filter' => 'Search'
					),
					'cohorte_atransmettre' => array(
						'filter' => 'Search'
					),
					'cohorte_heberge' => array(
						'filter' => 'Search'
					),
					'cohorte_rsamajore' => array(
						'filter' => 'Search'
					),
				)
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Cake1xLegacy.Ajax',
			'Default',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Dossierpcg66',
			'Decisionpdo',
			'Option',
			'Typenotifpdo',
			'WebrsaDossierpcg66',
			'WebrsaDecisiondossierpcg66',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Dossierspcgs66:edit',
			'imprimer' => 'Decisionsdossierspcgs66:decisionproposition',
			'view' => 'Dossierspcgs66:index',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajax_getetatdossierpcg66',
			'ajax_view_decisions',
			'ajaxfiledelete',
			'ajaxfileupload',
			'download',
			'fileview',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'ajax_getetatdossierpcg66' => 'read',
			'ajax_view_decisions' => 'read',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'update',
			'cancel' => 'update',
			'cohorte_atransmettre' => 'update',
			'cohorte_enattenteaffectation' => 'update',
			'cohorte_heberge' => 'update',
			'cohorte_imprimer' => 'update',
			'cohorte_imprimer_impressions' => 'update',
			'cohorte_rsamajore' => 'update',
			'delete' => 'delete',
			'download' => 'read',
			'edit' => 'update',
			'exportcsv' => 'read',
			'exportcsv_affectes' => 'read',
			'exportcsv_atransmettre' => 'read',
			'exportcsv_enattenteaffectation' => 'read',
			'exportcsv_gestionnaire' => 'read',
			'exportcsv_heberge' => 'read',
			'exportcsv_imprimer' => 'read',
			'exportcsv_rsamajore' => 'read',
			'fileview' => 'read',
			'imprimer' => 'update',
			'index' => 'read',
			'search' => 'read',
			'search_affectes' => 'read',
			'search_gestionnaire' => 'read',
			'view' => 'read',
		);

		/**
		 *
		 */
		protected function _setOptions() {
			$options = $this->Dossierpcg66->enums();

			$this->set( 'etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa') );
			$this->set( 'pieecpres', ClassRegistry::init('Personne')->enum('pieecpres') );
			$this->set( 'rolepers', ClassRegistry::init('Prestation')->enum('rolepers') );
			$this->set( 'qual', $this->Option->qual() );
			$this->set( 'motifpdo', ClassRegistry::init('Propopdo')->enum('motifpdo') );
			$this->set( 'categoriegeneral', ClassRegistry::init('Contratinsertion')->enum('sect_acti_emp') );
			$this->set( 'categoriedetail', ClassRegistry::init('Contratinsertion')->enum('emp_occupe') );

			$this->set( 'typeserins', $this->Option->typeserins() );
			$this->set( 'typenotifpdo', $this->Typenotifpdo->find( 'list' ) );
			$this->set( 'decisionpdo', $this->Decisionpdo->find( 'list', array( 'order' => 'Decisionpdo.libelle ASC' ) ) );

			$options = Set::merge(
				$this->Dossierpcg66->Decisiondossierpcg66->enums(),
				$options
			);

			$options = Hash::insert( $options, 'Suiviinstruction.typeserins', $this->Option->typeserins() );
			$this->set( compact( 'options' ) );
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
		 * Fonction permettant de visualiser les fichiers chargés dans la vue avant leur envoi sur le serveur
		 *
		 * @param integer $id
		 */
		public function fileview( $id ) {
			$this->Fileuploader->fileview( $id );
		}

		/**
		 * Téléchargement des fichiers préalablement associés
		 *
		 * @param integer $fichiermodule_id
		 */
		public function download( $fichiermodule_id ) {
			$this->assert( !empty( $fichiermodule_id ), 'error404' );
			$this->Fileuploader->download( $fichiermodule_id );
		}

		/**
		 * Permet de recalculer l'etat d'un dossier pcg et d'obtenir la nouvelle valeur
		 *
		 * @param type $id du Dossierpcg66
		 */
		public function ajax_getetatdossierpcg66( $id = null ) {
			if ( $id === null ) {
				$etatdossierpcg = null;
				$datetransmission = null;
				$orgs = null;

				$this->set( compact( 'etatdossierpcg', 'datetransmission', 'orgs' ) );
				$this->render( 'ajaxetatpdo', 'ajax' );
			}

			$this->Dossierpcg66->WebrsaDossierpcg66->updatePositionsPcgsById($id);

			$sqOrgs = str_replace('Decisiondossierpcg66', 'decision', $this->Dossierpcg66->Decisiondossierpcg66->sq(
				array(
					'fields' => 'Orgtransmisdossierpcg66.name',
					'joins' => array(
						$this->Dossierpcg66->Decisiondossierpcg66->join('Decdospcg66Orgdospcg66'),
						$this->Dossierpcg66->Decisiondossierpcg66->Decdospcg66Orgdospcg66->join('Orgtransmisdossierpcg66'),
					),
					'conditions' => array(
						'Decisiondossierpcg66.dossierpcg66_id = Dossierpcg66.id',
						'Decisiondossierpcg66.validationproposition' => 'O'
					),
				)
			));

			$query = array(
				'fields' => array(
					'Dossierpcg66.etatdossierpcg',
					'Decisiondossierpcg66.datetransmissionop',
					"(ARRAY_TO_STRING(ARRAY({$sqOrgs}), ', ')) AS \"Notificationdecisiondossierpcg66__name\""
				),
				'conditions' => array(
					'Dossierpcg66.id' => $id
				),
				'joins' => array(
					$this->Dossierpcg66->join('Decisiondossierpcg66'),
				),
				'order' => array(
					'Decisiondossierpcg66.id' => 'DESC'
				),
				'contain' => false
			);
			$result = $this->Dossierpcg66->find('first', $query);

			$etatdossierpcg = Hash::get($result, 'Dossierpcg66.etatdossierpcg');
			$datetransmission = Hash::get($result, 'Decisiondossierpcg66.datetransmissionop');
			$orgs = Hash::get($result, 'Notificationdecisiondossierpcg66.name' );

			$this->set( compact( 'etatdossierpcg', 'datetransmission', 'orgs' ) );
			$this->render( 'ajaxetatpdo', 'ajax' );
		}

		/**
		 * Liste des dossiers PCG d'un foyer
		 *
		 * @param integer $foyer_id
		 */
		public function index( $foyer_id = null ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );

			$personneDem = $this->WebrsaDossierpcg66->findPersonneDem($foyer_id);

			$results = $this->WebrsaDossierpcg66->getIndexData( $foyer_id );

			// Alerte controle sur place effectué de moins de 3 ans.
			$alertControleSurPlace = false;
			$controleadministratif = ClassRegistry::init( 'Controleadministratif' )->find(
				'first',
				array (
					'conditions' => array(
						'Controleadministratif.foyer_id' => $foyer_id,
					),
					'order' => array (
						'Controleadministratif.dtdeteccontro DESC'
					)
				)
			);
			if (isset ($controleadministratif['Controleadministratif'])) {
				$dateDuJour = new DateTime ();
				$dateControle = new DateTime ($controleadministratif['Controleadministratif']['dtdeteccontro']);
				$diff = $dateDuJour->diff($dateControle);
				if ($diff->format ('%y') > 3) {
					$alertControleSurPlace = true;
				}
			}

			$this->set( compact( 'personneDem', 'results', 'foyer_id', 'alertControleSurPlace' ) );
			$this->_setOptions();
		}

		/**
		 * Action d'ajout d'un dossier pcg
		 *
		 * @param type $foyer_id
		 */
		public function add( $foyer_id ) {
			// Initialisation
			$this->_init_add_edit($foyer_id);

			// Sauvegarde du formulaire
			if( !empty( $this->request->data ) ) {
				$this->_save_add_edit();
			}
			else {
				$this->request->data['Dossierpcg66']['haspiecejointe'] = 0;
			}

			// Modification du request data uniquement à la fin
			$this->set( 'personnedecisionmodifiable', $this->_isDecisionModifiable($foyer_id) );

			// Vue
			$this->view = 'edit';
		}

		/**
		 * Action d'edition d'un dossier pcg
		 *
		 * @param type $dossierpcg66_id
		 */
		public function edit( $dossierpcg66_id ) {
			// Initialisation
			$foyer_id = $this->Dossierpcg66->field( 'foyer_id', array( 'id' => $dossierpcg66_id ) );
			$this->_init_add_edit($foyer_id);

			// Récupération de données
			$dossierpcg66 = $this->WebrsaDossierpcg66->findDossierpcg($dossierpcg66_id);
			$this->assert( !empty( $dossierpcg66 ), 'invalidParameter' );
			$personnespcgs66 = $this->WebrsaDossierpcg66->findPersonnepcg($dossierpcg66_id);

			$paramsAccess = $this->WebrsaDecisiondossierpcg66->getParamsForAccess(
				$dossierpcg66_id, WebrsaAccessDecisionsdossierspcgs66::getParamsList()
			);
			$this->set('ajoutDecisionPossible', Hash::get($paramsAccess, 'ajoutPossible') !== false);

			$query = $this->WebrsaDecisiondossierpcg66->completeVirtualFieldsForAccess(
				$this->WebrsaDecisiondossierpcg66->queryIndex($dossierpcg66_id)
			);

			$decisionsdossierspcgs66 =  WebrsaAccessDecisionsdossierspcgs66::accesses(
				$this->Dossierpcg66->Decisiondossierpcg66->find('all', $query)
			);

			$fichiersEnBase = Hash::extract( $this->WebrsaDossierpcg66->findFichiers($dossierpcg66_id), '{n}.Fichiermodule' );

			// Variables pour la vue
			$etatdossierpcg = Hash::get($dossierpcg66, 'Dossierpcg66.etatdossierpcg');
			$lastDecisionId = Hash::get($decisionsdossierspcgs66, '0.Decisiondossierpcg66.id');
			$ajoutDecision = Hash::get($decisionsdossierspcgs66, '0.Decisiondossierpcg66.validationproposition') !== null;
			$this->set(
				compact(
					'ajoutDecision',
					'lastDecisionId',
					'decisionsdossierspcgs66',
					'personnespcgs66',
					'dossierpcg66_id',
					'etatdossierpcg',
					'fichiersEnBase',
					'dossierpcg66'
				)
			);

			// Sauvegarde du formulaire
			if( !empty( $this->request->data ) ) {
				$this->_save_add_edit();
			}
			else {
				$this->request->data = $dossierpcg66;
			}

			// On complète les options avec les valeurs du pole et du gestionnaire enregistrés le cas échéant
			$options = $this->User->Poledossierpcg66->WebrsaPoledossierpcg66->completeOptions(
				$this->viewVars['options'],
				$this->request->data,
				array( 'prefix' => true )
			);
			$options = $this->Dossierpcg66->Originepdo->completeOptions(
				$options,
				$this->request->data,
				array( 'Dossierpcg66.originepdo_id' )
			);
			$options = $this->Dossierpcg66->Typepdo->completeOptions(
				$options,
				$this->request->data,
				array( 'Dossierpcg66.typepdo_id' )
			);
			$this->set( compact( 'options' ) );

			// Modification du request data uniquement à la fin
			$this->set( 'personnedecisionmodifiable', $this->_isDecisionModifiable( $foyer_id, $etatdossierpcg ) );
			$this->request->data['Dossierpcg66']['user_id'] = $dossierpcg66['Dossierpcg66']['poledossierpcg66_id'].'_'.$dossierpcg66['Dossierpcg66']['user_id'];

			$this->set( 'urlmenu', '/dossierspcgs66/index/'.$foyer_id );
		}

		/**
		 * Initialisation du formulaire d'edition d'un dossier pcg
		 * Informations sur le demandeur, jeton, redirection en cas de retour
		 *
		 * @todo $gestionnairemodifiable est inutile, vérifier son utilité initiale, le retirer ?
		 * @param integer $foyer_id
		 */
		protected function _init_add_edit( $foyer_id ) {
			// Validité de l'url
			$this->assert( valid_int( $foyer_id ), 'invalidParameter' );

			//Gestion des jetons
			$dossier_id = $this->Dossierpcg66->Foyer->dossierId( $foyer_id );
			$this->Jetons2->get( $dossier_id );

			// Redirection si Cancel
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $foyer_id ) );
			}

			// Récupération de données
			$personneDem = $this->WebrsaDossierpcg66->findPersonneDem($foyer_id);

			// Variables pour la vue
			$gestionnairemodifiable = true;
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );
			$this->set( compact( 'personneDem', 'gestionnairemodifiable', 'foyer_id', 'dossier_id' ) );
			$this->_setOptions();

			// Ajout des options des listes déroulantes
			$options = $this->viewVars['options'];
			$options['Dossierpcg66']['originepdo_id'] = $this->Dossierpcg66->Originepdo->findForTraitement( 'list' );
			$options['Dossierpcg66']['typepdo_id'] = $this->Dossierpcg66->Typepdo->findForTraitement( 'list' );
			$options['Dossierpcg66']['poledossierpcg66_id'] = $this->User->Poledossierpcg66->WebrsaPoledossierpcg66->polesdossierspcgs66();
			$options['Dossierpcg66']['serviceinstructeur_id'] = $this->Dossierpcg66->Serviceinstructeur->listOptions();
			$options['Dossierpcg66']['user_id'] = $this->User->WebrsaUser->gestionnaires( true, true );
			$this->set( compact( 'options' ) );
		}

		/**
		 * Sauvegarde d'un formulaire add ou edit
		 */
		protected function _save_add_edit() {
			$this->Dossierpcg66->begin();

			if ( !Hash::get($this->request->data, 'Dossierpcg66.etatdossierpcg') ) {
				$this->request->data['Dossierpcg66']['etatdossierpcg'] = 'attaffect';
			}

			$saved = $this->Dossierpcg66->saveAll( $this->request->data, array( 'validate' => 'first', 'atomic' => false ) );
			$etatdossierpcg = Hash::get($this->viewVars, 'dossierpcg66.Dossierpcg66.etatdossierpcg');
			$etatFinal = in_array( $etatdossierpcg, array( 'annule', 'traite', 'decisionvalid', 'transmisop' ) );
			$id = $saved ? $this->Dossierpcg66->id : Hash::get($this->viewVars, 'dossierpcg66.Dossierpcg66.id');
			$decisiondefautinsertionep66_id = Hash::get(
				$this->viewVars, 'dossierpcg66.Dossierpcg66.decisiondefautinsertionep66_id'
			);

			/**
			 * INFO : Passe l'etat d'une EP Audition transformé en EP Parcours
			 * en "traité" si EP Parcours n'est pas traité alors que le Dossierpcg est validé.
			 * En pratique ça n'arrive jamais et je ne comprend pas l'utilité de ce processus...
			 * Ne semble pas avoir de conséquences.
			 */
			if ( $saved && $etatFinal && $decisiondefautinsertionep66_id ) {
				$saved = $this->Dossierpcg66->WebrsaDossierpcg66->updateEtatPassagecommissionep( $decisiondefautinsertionep66_id );
			}

			if( $saved && $this->_saveFichiers($id) ) {
				$this->Dossierpcg66->commit();
				$this->Jetons2->release( $this->viewVars['dossier_id'] );
				$this->Flash->success( __( 'Save->success' ) );
				$this->redirect( array(  'controller' => 'dossierspcgs66','action' => 'index', $this->viewVars['foyer_id'] ) );
			}
			else {
				$fichiers = $this->Fileuploader->fichiers(
					'add' === $this->action ? $this->viewVars['foyer_id'] : $id,
					false
				);
				$this->set( compact('fichiers') );
				$this->Dossierpcg66->rollback();
				$this->Flash->error( __( 'Save->error' ) );
			}
		}

		/**
		 * Sauvegarde des fichiers liés
		 *
		 * @param integer $id
		 * @return boolean
		 */
		protected function _saveFichiers( $id ) {
			$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
			return $this->Fileuploader->saveFichiers(
				$dir,
				!Set::classicExtract( $this->request->data, "Dossierpcg66.haspiecejointe" ),
				$id
			);
		}

		/**
		 * Décide si on affiche la partie "décision" du formulaire
		 * Attention, rempli le request->data
		 *
		 * @param integer $foyer_id
		 * @param string $etatdossierpcg
		 * @return boolean
		 */
		protected function _isDecisionModifiable( $foyer_id, $etatdossierpcg = '' ) {
			// Récupération du gestionnaire précédent et remplissage de la liste déroulante avec cette valeur par défaut
            $dossierpcg66Pcd = $this->Dossierpcg66->find(
                'first',
                array(
                    'conditions' => array(
                        'Dossierpcg66.foyer_id' => $foyer_id
                    ),
                    'recursive' => -1,
                    'order' => array( 'Dossierpcg66.created DESC'),
                    'limit' => 1
                )
            );

            if( !empty( $dossierpcg66Pcd ) && in_array( $etatdossierpcg, array( '', 'attaffect' ) ) ) {
                $this->request->data['Dossierpcg66']['poledossierpcg66_id'] = $dossierpcg66Pcd['Dossierpcg66']['poledossierpcg66_id'];
                $this->request->data['Dossierpcg66']['user_id'] = $dossierpcg66Pcd['Dossierpcg66']['poledossierpcg66_id'].'_'.$dossierpcg66Pcd['Dossierpcg66']['user_id'];
                $this->request->data['Dossierpcg66']['etatdossierpcg'] = 'attinstr';
            }

			return !in_array($etatdossierpcg, array( '', 'attaffect' ));
		}

		/**
		 *
		 * @param integer $id
		 */
		public function view( $dossierpcg66_id = null ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $this->Dossierpcg66->dossierId( $dossierpcg66_id ) ) ) );

			$dossierpcg66 = $this->WebrsaDossierpcg66->findDossierpcg($dossierpcg66_id);
			$this->assert( !empty( $dossierpcg66 ), 'invalidParameter' );

			$foyer_id = Set::classicExtract( $dossierpcg66, 'Dossierpcg66.foyer_id' );

			// Retour à l'entretien en cas de retour
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'dossierspcgs66', 'action' => 'index', $foyer_id ) );
			}

			$traitementsCourriersEnvoyes = array();
			if( Hash::get($dossierpcg66, 'Personnepcg66') ) {
				//Récupération de la liste des courriers envoyés à l'allocataire:
				$personnesIds = array();
				foreach( $dossierpcg66['Personnepcg66'] as $i => $personnepcg66 ) {
					$personnesIds[] = $personnepcg66['personne_id'];
				}
				$traitementsCourriersEnvoyes = $this->Dossierpcg66->WebrsaDossierpcg66->listeCourriersEnvoyes( $personnesIds, $dossierpcg66 );
			}

            // Liste des organismes auxquels on transmet le dossier
			$listOrgs = (array)Hash::extract( $dossierpcg66, 'Decisiondossierpcg66.0.Notificationdecisiondossierpcg66.{n}.name' );
			$orgs = implode( ', ',  $listOrgs );

			$this->_setOptions();
			$this->set( compact( 'dossierpcg66', 'orgs', 'datetransmissionop', 'foyer_id', 'traitementsCourriersEnvoyes' ) );
			$this->set( 'urlmenu', '/dossierspcgs66/index/'.$foyer_id );
		}

		/**
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$this->DossiersMenus->checkDossierMenu( array( 'id' => $this->Dossierpcg66->dossierId( $id ) ) );

			$dossierpcg66 = $this->Dossierpcg66->find(
				'first',
				array(
					'conditions' => array(
						'Dossierpcg66.id' => $id
					),
					'contain' => false,
					'fields' => array(
						'Dossierpcg66.foyer_id'
					)
				)
			);

			$foyer_id = Set::classicExtract( $dossierpcg66, 'Dossierpcg66.foyer_id' );

			$success = $this->Dossierpcg66->delete( $id );
			if( $success ) {
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Flash->error( __( 'Delete->error' ) );
			}
			$this->redirect( array( 'controller' => 'dossierspcgs66', 'action' => 'index', $foyer_id ) );
		}

		/**
		 *
		 * @param integer $id
		 */
		public function cancel( $id ) {
			$qd_dossierpcg66 = array(
				'conditions' => array(
					'Dossierpcg66.id' => $id
				),
				'recursive' => -1
			);
			$dossierpcg66 = $this->Dossierpcg66->find( 'first', $qd_dossierpcg66 );

			$foyer_id = Hash::get( $dossierpcg66, 'Dossierpcg66.foyer_id' );
			$this->set( 'foyer_id', $foyer_id );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );

			$dossier_id = $this->Dossierpcg66->dossierId( $id );
			$this->Jetons2->get( $dossier_id );

			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $foyer_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Dossierpcg66->begin();

				$this->request->data['Dossierpcg66']['etatdossierpcg'] = 'annule';
				$saved = $this->Dossierpcg66->save( $this->request->data , array( 'atomic' => false ) );

				if( $saved ) {
					$this->Dossierpcg66->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index', $foyer_id ) );
				}
				else {
					$this->Dossierpcg66->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else {
				$this->request->data = $dossierpcg66;
			}
			$this->set( 'urlmenu', '/dossierspcgs66/index/'.$foyer_id );
		}

		/**
		 * Moteur de recherche
		 */
		public function search() {
			$this->helpers[] = 'Search.SearchForm';
			$Recherches = $this->Components->load( 'WebrsaRecherchesDossierspcgs66' );
			$Recherches->search();
			$this->Dossierpcg66->validate = array();
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesDossierspcgs66' );
			$Recherches->exportcsv( array( 'view' => 'exportcsv' ) );
		}

		/**
		 * Moteur de recherche
		 */
		public function search_gestionnaire() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesDossierspcgs66' );
			$Recherches->search();
			$this->Dossierpcg66->validate = array();
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv_gestionnaire() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesDossierspcgs66' );
			$Recherches->exportcsv( array( 'view' => 'exportcsv' ) );
		}

		/**
		 * Moteur de recherche
		 */
		public function search_affectes() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesDossierspcgs66' );
			$Recherches->search();
			$this->Dossierpcg66->validate = array();
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv_affectes() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesDossierspcgs66' );
			$Recherches->exportcsv();
		}

		/**
		 * Cohorte
		 */
		public function cohorte_enattenteaffectation() {
			$Recherches = $this->Components->load( 'WebrsaCohortesDossierspcgs66' );
			$this->Dossierpcg66->validate = array(
				'poledossierpcg66_id' => array( NOT_BLANK_RULE_NAME => array( 'rule' => NOT_BLANK_RULE_NAME ) )
			);
			$this->Dossierpcg66->Typepdo->validate = array();
			$this->Dossierpcg66->Originepdo->validate = array();

			$Recherches->cohorte( array( 'modelRechercheName' => 'WebrsaCohorteDossierpcg66Enattenteaffectation' ) );
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv_enattenteaffectation() {
			$Recherches = $this->Components->load( 'WebrsaCohortesDossierspcgs66' );
			$Recherches->exportcsv( array( 'modelRechercheName' => 'WebrsaCohorteDossierpcg66Enattenteaffectation' ) );
		}

		/**
		 * Cohorte
		 */
		public function cohorte_atransmettre() {
			$Recherches = $this->Components->load( 'WebrsaCohortesDossierspcgs66' );
			$this->Dossierpcg66->validate = array();
			$this->Dossierpcg66->Typepdo->validate = array();
			$this->Dossierpcg66->Originepdo->validate = array();
			$this->Dossierpcg66->Decisiondossierpcg66->Decdospcg66Orgdospcg66->validate = array(
				'orgtransmisdossierpcg66_id' => array( NOT_BLANK_RULE_NAME => array( 'rule' => NOT_BLANK_RULE_NAME ) )
			);

			$Recherches->cohorte( array( 'modelRechercheName' => 'WebrsaCohorteDossierpcg66Atransmettre' ) );
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv_atransmettre() {
			$Recherches = $this->Components->load( 'WebrsaCohortesDossierspcgs66' );
			$Recherches->exportcsv( array( 'modelRechercheName' => 'WebrsaCohorteDossierpcg66Atransmettre' ) );
		}

		/**
		 * Cohorte
		 */
		public function cohorte_heberge() {
			$Recherches = $this->Components->load( 'WebrsaCohortesDossierspcgs66' );
			$Recherches->cohorte( array( 'modelName' => 'Dossier', 'modelRechercheName' => 'WebrsaCohorteDossierpcg66Heberge' ) );
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv_heberge() {
			$Recherches = $this->Components->load( 'WebrsaCohortesDossierspcgs66' );
			$Recherches->exportcsv( array( 'modelName' => 'Dossier', 'modelRechercheName' => 'WebrsaCohorteDossierpcg66Heberge' ) );
		}

		/**
		 * Cohorte
		 */
		public function cohorte_rsamajore() {
			$Recherches = $this->Components->load( 'WebrsaCohortesDossierspcgs66' );
			$Recherches->cohorte( array( 'modelName' => 'Dossier', 'modelRechercheName' => 'WebrsaCohorteDossierpcg66Rsamajore' ) );
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv_rsamajore() {
			$Recherches = $this->Components->load( 'WebrsaCohortesDossierspcgs66' );
			$Recherches->exportcsv( array( 'modelName' => 'Dossier', 'modelRechercheName' => 'WebrsaCohorteDossierpcg66Rsamajore' ) );
		}

		/**
		 * Cohorte
		 */
		public function cohorte_imprimer() {
			$Recherches = $this->Components->load( 'WebrsaCohortesDossierspcgs66Impressions' );
			$Recherches->search( array( 'modelName' => 'Dossierpcg66', 'modelRechercheName' => 'WebrsaCohorteDossierpcg66Imprimer' ) );
		}

		/**
		 * Impression de la cohorte
		 */
		public function cohorte_imprimer_impressions() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesDossierspcgs66Impressions' );
			$Cohortes->impressions(
				array(
					'modelName' => 'Dossierpcg66',
					'modelRechercheName' => 'WebrsaCohorteDossierpcg66Imprimer',
					'configurableQueryFieldsKey' => 'Dossierspcgs66.cohorte_imprimer'
				)
			);
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv_imprimer() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesDossierspcgs66' );
			$Recherches->exportcsv( array( 'modelName' => 'Dossierpcg66', 'modelRechercheName' => 'WebrsaCohorteDossierpcg66Imprimer' ) );
		}

		/**
		 * Créer et envoi à l'utilisateur un fichier zip comprenant les Décisions valide d'un Dossier PCG
		 * et les traitements de type courrier à imprimer.
		 *
		 * Remplace l'ancienne fonction : Decisionsdossierspcgs66::decisionproposition()
		 * qui envoyait un unique PDF de la proposition
		 *
		 * NOTE : Un traitement doit avoir la valeur imprimer = 1 pour être imprimé (dans tout les cas)
		 * Un traitement ne sera imprimé que s'il est attaché à une proposition valide ou si il n'y a pas de proposition
		 * Dans la cohorte, ne sera affiché que les dossiers PCG avec une proposition validée
		 * ou bien un traitement à imprimer sans proposition
		 *
		 * Cas 1:	Dans le dossier pcg, la proposition est validée et un traitement est à imprimer.
		 *			Il faut imprimer la proposition et le traitement.
		 *
		 * Cas 2:	Dans le dossier pcg, la proposition n'est pas validée et un traitement est à imprimer.
		 *			Il faut imprimer uniquement la proposition.
		 *
		 * Cas 3:	Dans la cohorte, la proposition est validée et un traitement est à imprimer.
		 *			Il faut imprimer la proposition et le traitement.
		 *
		 * Cas 4:	Dans la cohorte, il n'y a aucune proposition mais il y a un traitement à imprimer.
		 *			Il faut imprimer uniquement le traitement.
		 *
		 * @param integer $id
		 * @param integer $decision_id decisiondossierpcg66 Appelé "proposition de décision"
		 */
		public function imprimer( $id, $decision_id = null ) {
			$this->assert( !empty( $id ), 'error404' );
			$this->DossiersMenus->checkDossierMenu( array( 'id' => $this->Dossierpcg66->dossierId( $id ) ) );

			$query = $this->Dossierpcg66->WebrsaDossierpcg66->getImpressionBaseQuery( $id );

			// Cas n° 1 et 2 : Dans dossier pcg, on précise $decision_id (pas dans la cohorte qui inclue les traitements sans proposition)
			// Note : Logiquement, il ne peut y avoir une proposition non validé
			if ( $decision_id !== null ) {
				$query['conditions'][] = array( 'Decisiondossierpcg66.id' => $decision_id );
			}

			$results = $this->Dossierpcg66->find( 'first', $query );
			$decisionsdossierspcgs66_id = Hash::get($results, 'Decisiondossierpcg66.id');

			if ( !empty($results) ) {
				$success = true;

				$this->Dossierpcg66->Decisiondossierpcg66->begin();

				// Si l'etat du dossier est decisionvalid on le passe en atttransmiop avec une date d'impression
				if ( Hash::get( $results, 'Dossierpcg66.etatdossierpcg' ) === 'decisionvalid' ) {
					$results['Dossierpcg66']['dateimpression'] = date('Y-m-d');
					$results['Dossierpcg66']['etatdossierpcg'] = 'atttransmisop';
					$success = $this->Dossierpcg66->Decisiondossierpcg66->Dossierpcg66->save( $results['Dossierpcg66'], array( 'atomic' => false ) );
				}

				$decisionPdf = $decisionsdossierspcgs66_id !== null
					? $this->Dossierpcg66->Decisiondossierpcg66->WebrsaDecisiondossierpcg66->getPdfDecision( $decisionsdossierspcgs66_id )
					: null
				;

				$courriers = $this->Dossierpcg66->Personnepcg66->Traitementpcg66->WebrsaTraitementpcg66->getPdfsByDossierpcg66Id( $id, $this->Session->read('Auth.User.id') );
				$queryCourrier = $this->Dossierpcg66->Personnepcg66->Traitementpcg66->WebrsaTraitementpcg66->getPdfsQuery($id);

				$traitementspcgs66_ids = Hash::extract($this->Dossierpcg66->Foyer->find('all', $queryCourrier), '{n}.Traitementpcg66.id');

				if ($success && !empty($traitementspcgs66_ids)) {
					$success = $this->Dossierpcg66->Personnepcg66->Traitementpcg66->updateAllUnbound(
						array(
							'etattraitementpcg' => "'attente'",
							'imprimer' => 0
						),
						array( 'id' => $traitementspcgs66_ids )
					);
				}

				if( $success && ( $decisionPdf !== null || !empty($courriers) ) ) {

					$this->Dossierpcg66->Decisiondossierpcg66->commit();

					$prefix = 'Dossier_PCG';
					$date = date('Y-m-d');
					$allocatairePrincipal = Hash::get( $results, 'Personne.nom' ) . '_' . Hash::get( $results, 'Personne.prenom' );
					$fileName = "{$date}_{$prefix}_{$id}_Courrier_{$allocatairePrincipal}.pdf";
					$PdfUtility = new WebrsaPdfUtility();
					$pdfList = array();

					if ( $decisionPdf !== null ) {
						$pdfList[] = $decisionPdf;
						$fileName = "{$date}_{$prefix}_{$id}_Decision_{$allocatairePrincipal}.pdf";
					}

					foreach ( $courriers as $i => $courrier ) {
						$pdf = $courrier;
						$pdfList[] = $pdf;
					}

					if ( Configure::read('Dossierspcgs66.imprimer_cohorte.Impression.RectoVerso') ) {
						$pdfList = $PdfUtility->preparePdfListForRectoVerso( $pdfList );
					}

					$concatPdf = $this->Gedooo->concatPdfs($pdfList, 'Dossierpcg66');
					$this->Gedooo->sendPdfContentToClient($concatPdf, $fileName);
				}
				else {
					$this->Dossierpcg66->Decisiondossierpcg66->rollback();
				}

			}

			$this->Flash->error( 'Impossible de générer le(s) fichier PDF' );
			$this->redirect( $this->referer() );
		}

		public function ajax_view_decisions($dossierpcg66_id) {
			$decisionsdossierspcgs66 = $this->Dossierpcg66->find('all',
				array(
					'fields' => array_merge(
						$this->Dossierpcg66->Decisiondossierpcg66->fields(),
						array(
							'Dossierpcg66.id',
							'Dossierpcg66.foyer_id',
							'Decisionpdo.libelle'
						)
					),
					'contain' => false,
					'joins' => array(
						$this->Dossierpcg66->join('Decisiondossierpcg66'),
						$this->Dossierpcg66->Decisiondossierpcg66->join('Decisionpdo'),
					),
					'conditions' => array('Dossierpcg66.id' => $dossierpcg66_id)
				)
			);

			$users = array();
			$users_list = $this->Dossierpcg66->Decisiondossierpcg66->User->find('all',
				array('fields' => array('id', 'nom', 'prenom'), 'contain' => false)
			);
			foreach ($users_list as $user) {
				$users[$user['User']['id']] = $user['User']['nom'].' '.$user['User']['prenom'];
			}

			$this->set(compact('decisionsdossierspcgs66', 'dossierMenu', 'users'));

			$this->render('ajax_view_decisions', 'ajax');
		}
	}
?>