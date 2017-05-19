<?php
	/**
	 * Code source de la classe CuisController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'CakeEmail', 'Network/Email' );
	App::uses( 'WebrsaEmailConfig', 'Utility' );

	/**
	 * La classe CuisController permet de gérer les CUIs (CG 58, 66 et 93).
	 *
	 * @package app.Controller
	 * @see Cuis66Controller (refonte)
	 */
	class CuisController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cuis';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'DossiersMenus',
			'Fileuploader',
			'Gedooo.Gedooo',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array( 'search' )
			),
			'WebrsaAccesses',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Cake1xLegacy.Ajax',
			'Csv',
			'Default',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
			'Locale',
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cui',
			'Departement',
			'Option',
			'WebrsaCui',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Cuis:edit',
			'view' => 'Cuis:index',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
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
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'update',
			'delete' => 'delete',
			'download' => 'read',
			'edit' => 'update',
			'exportcsv' => 'read',
			'filelink' => 'read',
			'fileview' => 'read',
			'index' => 'read',
			'search' => 'read',
			'view' => 'read',
		);

		/**
		 * Envoi d'un fichier temporaire depuis le formualaire.
		 */
		public function ajaxfileupload() {
			$this->Fileuploader->ajaxfileupload();
		}

		/**
		 * Suppression d'un fichier temporaire.
		 */
		public function ajaxfiledelete() {
			$this->Fileuploader->ajaxfiledelete();
		}

		/**
		 * Visualisation d'un fichier temporaire.
		 *
		 * @param integer $id
		 */
		public function fileview( $id ) {
			$this->Fileuploader->fileview( $id );
		}

		/**
		 * Visualisation d'un fichier stocké.
		 *
		 * @param integer $id
		 */
		public function download( $id ) {
			$this->Fileuploader->download( $id );
		}

		/**
		 * Liste des fichiers liés à une orientation.
		 *
		 * @param integer $cui_id
		 */
		public function filelink( $cui_id ) {
			$personne_id = $this->Cui->personneId( $cui_id );
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$this->set( compact( 'dossierMenu' ) );

			$this->Fileuploader->filelink( $cui_id, array( 'action' => 'index', $personne_id ) );
			$this->set( 'urlmenu', "/cuis/index/{$personne_id}" );

			$options = $this->Cui->enums();
			$this->set( compact( 'options' ) );
		}

		/**
		 * Moteur de recherche
		 */
		public function search() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesCuis' );
			$Recherches->search();
			$this->Cui->validate = array();
			$this->Cui->Cui66->validate = array();
			$this->Cui->Cui66->Decisioncui66->validate = array();
			$this->Cui->Partenairecui->Adressecui->validate = array();
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesCuis' );
			$Recherches->exportcsv();
		}

		/**
		 * Liste des CUI du bénéficiaire.
		 *
		 * @param integer $personne_id
		 */
		public function index( $personne_id ) {
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );

			$this->_setEntriesAncienDossier( $personne_id, 'Cui' );

			$results = $this->WebrsaAccesses->getIndexRecords($personne_id, $this->Cui->WebrsaCui->queryIndex($personne_id));

			$messages = $this->Cui->WebrsaCui->messages( $personne_id );
			$addEnabled = $this->Cui->WebrsaCui->addEnabled( $messages );

			// Options
			$options = $this->Cui->WebrsaCui->options($this->Session->read( 'Auth.User.id' ));

			$this->set(
				compact('results', 'dossierMenu', 'messages', 'addEnabled', 'personne_id', 'options', 'isRsaSocle')
			);

			switch ((int)Configure::read('Cg.departement')) {
				case 66: $this->view = __FUNCTION__.'_cg66'; break;
			}
		}

		/**
		 * Formulaire d'ajout de fiche de CUI
		 *
		 * @param integer $personne_id L'id de la Personne à laquelle on veut ajouter un CUI
		 */
		public function add( $personne_id ) {
			$args = func_get_args();
			call_user_func_array( array( $this, 'edit' ), $args );
		}

		/**
		 * Méthode générique d'ajout et de modification de CUI
		 *
		 * @param integer $id L'id de la personne (add) ou du CUI (edit)
		 */
		public function edit( $id = null ) {
			if( $this->action === 'add' ) {
				$personne_id = $id;
				$id = null;
				$this->WebrsaAccesses->check(null, $personne_id);
			}
			else {
				$personne_id = $this->Cui->personneId( $id );
				$this->WebrsaAccesses->check($id);
			}

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$this->Jetons2->get( $dossierMenu['Dossier']['id'] );

			// INFO: champ non obligatoire
			unset( $this->Cui->Entreeromev3->validate['familleromev3_id'][NOT_BLANK_RULE_NAME] );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
				$this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			// On tente la sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Cui->begin();
				if( $this->Cui->WebrsaCui->saveAddEdit( $this->request->data, $this->Session->read( 'Auth.User.id' ) ) ) {
					$this->Cui->commit();
					$cui_id = $this->Cui->id;
					$this->Cui->WebrsaCui->updatePositionsCuisById( $cui_id );
					$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index', $personne_id ) );
				}
				else {
					$this->Cui->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else {
				$this->request->data = $this->Cui->WebrsaCui->prepareFormDataAddEdit( $personne_id, $id );
			}

			// Options
			$options = $this->Cui->WebrsaCui->options($this->Session->read( 'Auth.User.id' ));

			$urlmenu = "/cuis/index/{$personne_id}";

			$queryPersonne = $this->Cui->WebrsaCui->queryPersonne( $personne_id );
			$this->Cui->Personne->forceVirtualFields = true;
			$personne = $this->Cui->Personne->find( 'first', $queryPersonne );

			$this->set( compact( 'options', 'personne_id', 'dossierMenu', 'urlmenu', 'personne' ) );

			switch ((int)Configure::read('Cg.departement')) {
				case 66:
					$this->view = __FUNCTION__.'_cg66';
					$this->set('mailEmployeur', $this->action !== 'add');
					$this->set('correspondancesChamps', json_encode($this->Cui->Partenairecui->Partenairecui66->correspondancesChamps));
					break;
				default: $this->view = __FUNCTION__;
			}
		}

		/**
		 * Vue d'un CUI
		 *
		 * @param type $id
		 */
		public function view( $id = null ) {
			$this->WebrsaAccesses->check($id);
			$personne_id = $this->Cui->personneId( $id );

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$this->Jetons2->get( $dossierMenu['Dossier']['id'] );

			$query = $this->Cui->WebrsaCui->queryView( $id );
			$this->request->data = $this->Cui->find( 'first', $query );

			// Options
			$options = $this->Cui->WebrsaCui->options();

			$urlmenu = "/cuis/index/{$personne_id}";

			$Allocataire = ClassRegistry::init( 'Allocataire' );

			$queryPersonne = $Allocataire->searchQuery();
			$queryPersonne['conditions']['Personne.id'] = $personne_id;
			$fields = array(
				'Personne.nom',
				'Personne.prenom',
				'Personne.dtnai',
				'Personne.nir',
				'Personne.nomcomnai',
				'Personne.nati',
				'Adresse.numvoie',
				'Adresse.libtypevoie',
				'Adresse.nomvoie',
				'Adresse.codepos',
				'Adresse.lieudist',
				'Adresse.complideadr',
				'Adresse.compladr',
				'Adresse.nomcom',
				'Adresse.canton',
				'Dossier.matricule',
				'Dossier.dtdemrsa',
				'Dossier.fonorg',
				'Referentparcours.nom_complet' => $queryPersonne['fields']['Referentparcours.nom_complet'],
				'Titresejour.dftitsej'
			);
			$queryPersonne['fields'] = $fields;

			// Jointure spéciale adresse actuelle / département pour obtenir le nom du dpt
			$queryPersonne['fields'][] = 'Departement.name';
			$queryPersonne['joins'][] = array(
				'table' => 'departements',
				'alias' => 'Departement',
				'type' => 'LEFT OUTER',
				'conditions' => array(
					'SUBSTRING( Adresse.codepos FROM 1 FOR 2 ) = Departement.numdep'
				)
			);
			$queryPersonne['joins'][] = array(
				'table' => 'titressejour',
				'alias' => 'Titresejour',
				'type' => 'LEFT OUTER',
				'conditions' => array(
					'Titresejour.personne_id' => $personne_id
				)
			);

			$personne = $this->Cui->Personne->find('first', $queryPersonne);
			$personne['Foyer']['nb_enfants'] = $this->Cui->Personne->Prestation->getNbEnfants( $personne_id );

			$this->set( compact( 'options', 'personne_id', 'dossierMenu', 'urlmenu', 'personne' ) );

			switch ((int)Configure::read('Cg.departement')) {
				case 66:
					$this->view = __FUNCTION__.'_cg66';
					break;
				default: $this->view = __FUNCTION__;
			}
		}

		/**
		 * Tentative de suppression d'un CUI.
		 *
		 * @param integer $id
		 */
		public function delete($id) {
			$this->WebrsaAccesses->check($id);
			$this->Default->delete( $id );
		}
	}
?>