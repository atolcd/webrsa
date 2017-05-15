<?php
	/**
	 * Code source de la classe Cuis66.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AppController', 'Controller');
	App::uses('CakeEmail', 'Network/Email');
	App::uses( 'WebrsaEmailConfig', 'Utility' );

	/**
	 * La classe Cuis66 ...
	 *
	 * @package app.Controller
	 */
	class Cuis66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cuis66';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'DossiersMenus',
			'Fileuploader',
			'Gedooo.Gedooo',
			'Gestionzonesgeos',
			'Jetons2',
			'WebrsaAccesses',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Ajax2' => array(
				'className' => 'Prototype.PrototypeAjax',
				'useBuffer' => false
			),
			'Cake1xLegacy.Ajax',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Fileuploader',
			'Observer' => array(
				'className' => 'Prototype.PrototypeObserver',
				'useBuffer' => true
			),
			'Romev3',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cui',
			'Emailcui',
			'Option',
			'Personne',
			'WebrsaCui',
			'WebrsaEmailcui',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'ajaxfileupload' => 'Cuis66::filelink',
			'ajaxfiledelete' => 'Cuis66::filelink',
			'ajax_generate_email' => 'Cuis66::email_add',
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
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
			'ajax_generate_email' => 'read',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'create',
			'annule' => 'delete',
			'delete' => 'delete',
			'download' => 'read',
			'edit' => 'update',
			'email' => 'read',
			'email_add' => 'create',
			'email_delete' => 'delete',
			'email_edit' => 'update',
			'email_send' => 'create',
			'email_view' => 'read',
			'filelink' => 'read',
			'fileview' => 'read',
			'impression' => 'read',
			'impression_fichedeliaison' => 'update',
			'index' => 'read',
			'indexparams' => 'read',
			'notification' => 'update',
			'view' => 'read',
		);
		
		/**
		 * Nom de l'array contenant la config pour l'envoi d'e-mails
		 * @see app/Config/email.php
		 * @var String
		 */
		public $configEmail = 'mail_employeur_cui';
		
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
			$this->WebrsaAccesses->setMainModel('Cui')->check($cui_id, $personne_id);
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$this->set( compact( 'dossierMenu' ) );

			$this->Fileuploader->filelink( $cui_id, array( 'controller' => 'cuis', 'action' => 'index', $personne_id ) );
			$this->set( 'urlmenu', "/cuis/index/{$personne_id}" );

			$options = $this->Cui->enums();
			$this->set( compact( 'options' ) );
		}
		
		/**
		 * Liste des CUI du bénéficiaire.
		 * 
		 * @param integer $personne_id
		 * @deprecated since version 3.1
		 * @see CuisController::index
		 */
		public function index( $personne_id ) {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );

			$this->_setEntriesAncienDossier( $personne_id, 'Cui' );
			
			$query = $this->Cui->WebrsaCui->queryIndex($personne_id);
			$results = $this->Cui->find( 'all', $query );
			
			$messages = $this->Cui->WebrsaCui->messages( $personne_id );
			$addEnabled = $this->Cui->WebrsaCui->addEnabled( $messages );

			// Options
			$options = $this->Cui->WebrsaCui->options($this->Session->read( 'Auth.User.id' ));

			$this->set( compact( 'results', 'dossierMenu', 'messages', 'addEnabled', 'personne_id', 'options', 'isRsaSocle' ) );
		}
		
		/**
		 * Formulaire d'ajout de fiche de CUI
		 *
		 * @param integer $personne_id L'id de la Personne à laquelle on veut ajouter un CUI
		 * @deprecated since version 3.1
		 * @see CuisController::add
		 */
		public function add( $personne_id ) {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			$args = func_get_args();
			call_user_func_array( array( $this, 'edit' ), $args );
		}

		/**
		 * Méthode générique d'ajout et de modification de CUI
		 *
		 * @param integer $id L'id de la personne (add) ou du CUI (edit)
		 * @deprecated since version 3.1
		 * @see CuisController::edit
		 */
		public function edit( $id = null ) {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			if( $this->action === 'add' ) {
				$personne_id = $id;
				$id = null;
				$mailEmployeur = false;
			}
			else {
				$personne_id = $this->Cui->personneId( $id );
			}

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$this->Jetons2->get( $dossierMenu['Dossier']['id'] );
			
			// INFO: champ non obligatoire
			unset( $this->Cui->Entreeromev3->validate['familleromev3_id']['notEmpty'] );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
				$this->redirect( array( 'controller' => 'cuis', 'action' => 'index', $personne_id ) );
			}

			// On tente la sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Cui->begin();
				if( $this->Cui->WebrsaCui->saveAddEdit( $this->request->data, $this->Session->read( 'Auth.User.id' ) ) ) {
					$this->Cui->commit();
					$cui_id = $this->Cui->id;
					$this->Cui->WebrsaCui->updatePositionsCuisById( $cui_id );
					$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'cuis', 'action' => 'index', $personne_id ) );
				}
				else {
					$this->Cui->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
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

			$correspondancesChamps = json_encode( $this->Cui->Partenairecui->Partenairecui66->correspondancesChamps );
			$this->set( compact( 'options', 'personne_id', 'dossierMenu', 'urlmenu', 'personne', 'mailEmployeur', 'correspondancesChamps' ) );
			$this->render( 'edit' );
		}
		
		/**
		 * Vue d'un CUI
		 * 
		 * @param type $id
		 * @deprecated since version 3.1
		 * @see CuisController::view
		 */
		public function view( $id = null ) {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
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
		}
		
		/**
		 * Permet d'annuler un CUI
		 * 
		 * @param type $cui66_id
		 */
		public function annule( $cui66_id ){
			$this->WebrsaAccesses->check($cui66_id);
			$query = array(
				'fields' => array(
					'Cui.id',
					'Cui.personne_id'
				),
				'conditions' => array(
					'Cui66.id' => $cui66_id
				),
				'joins' => array(
					$this->Cui->join( 'Cui66' )
				)
			);
			$result = $this->Cui->find('first', $query);
			
			$cui_id = $result['Cui']['id'];
			$personne_id = $result['Cui']['personne_id'];

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$this->Jetons2->get( $dossierMenu['Dossier']['id'] );
			
			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
				$this->redirect( array( 'controller' => 'cuis', 'action' => 'index', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Cui->Cui66->begin();
				if( $this->Cui->Cui66->WebrsaCui66->annule( $this->request->data, $this->Session->read( 'Auth.User.id' ) ) ) {
					$this->Cui->Cui66->commit();
					$this->Cui->Cui66->WebrsaCui66->updatePositionsCuisById( $cui_id );
					$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
					$this->Session->setFlash( 'Le CUI à été annulé.', 'flash/success' );
					$this->redirect( array( 'controller' => 'cuis', 'action' => 'index', $personne_id ) );
				}
				else {
					$this->Cui->Cui66->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else {
				$this->request->data = array( 'Cui66' => array( 'id' => $cui66_id ) );
			}

			$urlmenu = "/cuis/index/{$personne_id}";

			$this->set( compact( 'options', 'personne_id', 'dossierMenu', 'urlmenu' ) );
		}
		
		/**
		 * Supprime un CUI
		 * 
		 * @param type $cui_id
		 */
		public function delete( $cui_id ){
			$this->WebrsaAccesses->setMainModel('Cui')->check($cui_id);
			$this->Cui->begin();
			$success = $this->Cui->delete($cui_id);
			$this->_setFlashResult('Delete', $success);

			if ($success) {
				$this->Cui->commit();
			} else {
				$this->Cui->rollback();
			}
			$this->redirect($this->referer());
		}
		
		/**
		 * Supprime un E-mail du CUI
		 * 
		 * @param type $id
		 */
		public function email_delete( $id ){
			$this->WebrsaAccesses->setWebrsaModel('WebrsaEmailcui')->setMainModel('Emailcui')
					->check($id, null, array('isModuleEmail' => true));
			$this->Cui->Emailcui->begin();
			$success = $this->Cui->Emailcui->delete($id);
			$this->_setFlashResult('Delete', $success);

			if ($success) {
				$this->Cui->Emailcui->commit();
			} else {
				$this->Cui->Emailcui->rollback();
			}
			$this->redirect($this->referer());
		}
		
		/**
		 * Passe le champ Cui66.notifie à 1
		 * Utile pour un changement de position/etat du CUI
		 * 
		 * @param type $cui66_id
		 */
		public function notification( $cui66_id ){
			$this->WebrsaAccesses->check($cui66_id);
			$this->Cui->Cui66->id = $cui66_id;
			$this->Cui->Cui66->saveField( 'notifie', 1 );
			$this->Session->setFlash( 'Le CUI à été notifié.');
			
			$result = $this->Cui->Cui66->find( 'first', array( 'fields' => 'cui_id', 'conditions' => array( 'Cui66.id' => $cui66_id ) ) );
			$cui_id = $result['Cui66']['cui_id'];
			$this->Cui->Cui66->WebrsaCui66->updatePositionsCuisById( $cui_id );
			$this->redirect( Router::url( $this->referer(), true ) );
		}
		
		/**
		 * Liste des Emails du CUI du bénéficiaire.
		 * 
		 * @param integer $personne_id
		 */
		public function email( $personne_id = null, $cui_id = null ) {
			$this->WebrsaAccesses->setMainModel('Cui')->check($cui_id, $personne_id);
			if ( empty($personne_id) || empty($cui_id) || !is_numeric($personne_id) || !is_numeric($cui_id) ){
				throw new NotFoundException();
			}
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );

			$this->_setEntriesAncienDossier( $personne_id, 'Cui' );
			
			$urlmenu = "/cuis/index/{$personne_id}";

			$params = WebrsaAccessCuis66::getParamsList(array('isModuleEmail' => true));
			$paramsAccess = $this->WebrsaEmailcui->getParamsForAccess($cui_id, $params);
			$query = $this->WebrsaEmailcui->completeVirtualFieldsForAccess(
				array(
					'fields' => array(
						'Emailcui.id',
						'Emailcui.cui_id',
						'Emailcui.personne_id',
						'Emailcui.titre',
						'Emailcui.created',
						'Emailcui.dateenvoi',
					),
					'conditions' => array(
						'Emailcui.cui_id' => $cui_id,
						'Emailcui.personne_id' => $personne_id
					),
					'order' => array( 'Emailcui.created DESC' )
				)
			);
			$results = WebrsaAccessCuis66::accesses($this->Cui->Emailcui->find('all', $query), $paramsAccess);
			$ajoutPossible = Hash::get($paramsAccess, 'ajoutPossible') !== false;
			$messages = $this->Cui->Emailcui->messages( $personne_id );

			// Options
			$options = $this->Cui->WebrsaCui->options();

			$this->set( compact( 'results', 'dossierMenu', 'messages', 'ajoutPossible', 'personne_id', 'options', 'cui_id', 'urlmenu' ) );
		}
		
		/**
		 * Formulaire d'ajout d'un e-mail pour le CUI
		 *
		 * @param integer $personne_id L'id de la Personne à laquelle on veut ajouter un CUI
		 * @param integer $cui_id L'id du cui visé
		 */
		public function email_add( $personne_id, $cui_id ) {
			$this->WebrsaAccesses->setWebrsaModel('WebrsaEmailcui')->setMainModel('Cui')
				->check($cui_id, $personne_id, array('isModuleEmail' => true));
			$args = func_get_args();
			call_user_func_array( array( $this, 'email_edit' ), $args );
		}
		
		/**
		 * Méthode générique d'ajout et de modification d'E-mail CUI
		 *
		 * @param integer $personne_id L'id de la personne
		 * @param integer $id L'id du cui visé (add) ou l'id du l'Emailcui
		 */
		public function email_edit( $personne_id = null, $id = null ) {
			if( $this->action === 'email_add' ) {
				$cui_id = $id;
				$email_id = null;
			}
			else {
				$cui_id = isset( $this->request->data['Emailcui']['cui_id'] ) ? $this->request->data['Emailcui']['cui_id'] : null;
				$email_id = $id;
				$this->WebrsaAccesses->setWebrsaModel('WebrsaEmailcui')->setMainModel('Emailcui')
					->check($email_id, $personne_id, array('isModuleEmail' => true));
			}

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$this->Jetons2->get( $dossierMenu['Dossier']['id'] );
			
			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
				$this->redirect( array( 'action' => 'email', $personne_id, $cui_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Cui->Emailcui->begin();
				if( $this->Cui->Emailcui->saveAddEdit( $this->request->data, $this->Session->read( 'Auth.User.id' ) ) ) { 
					$this->Cui->Emailcui->commit();
					$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'action' => 'email', $personne_id, $cui_id ) );
				}
				else {
					$this->Cui->Emailcui->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else {
				$this->request->data = $this->Cui->Emailcui->prepareFormDataAddEdit( $personne_id, $cui_id, $email_id );
			}
			
			// Options
			$options = $this->Cui->Emailcui->options( array( 'allocataire' => false, 'find' => false, 'autre' => false ) );

			$urlmenu = "/cuis/index/{$personne_id}";
			
			$this->set( compact( 'options', 'personne_id', 'dossierMenu', 'urlmenu', 'personne', 'mailEmployeur', 'correspondancesChamps', 'files' ) );
			$this->render( 'email_edit' );
		}
		
		/**
		 * Vue d'un E-mail
		 * 
		 * @param type $personne_id
		 * @param type $id
		 * @throws NotFoundException
		 */
		public function email_view( $personne_id = null, $id = null ) {
			$this->WebrsaAccesses->setWebrsaModel('WebrsaEmailcui')->setMainModel('Emailcui')
					->check($id, $personne_id, array('isModuleEmail' => true));
			if ( ($personne_id === null && $id === null) || ($this->action === 'email_add' && $id === null) || ($personne_id !== null && !is_numeric($personne_id)) || ($id !== null && !is_numeric($id)) ){
				throw new NotFoundException();
			}
			$email_id = $id;
			unset( $id );

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			
			$query = $this->Cui->Emailcui->queryView( $email_id );
			$result = $this->Cui->Emailcui->find( 'first', $query );
			$result['Emailcui']['pj'] = explode( '_', $result['Emailcui']['pj'] );
			$result['Emailcui']['piecesmanquantes'] = explode( '_', $result['Emailcui']['piecesmanquantes'] );
			$this->request->data = $result;
			
			// Options
			$options = $this->Cui->Emailcui->options( array( 'allocataire' => false, 'find' => false, 'autre' => false ) );

			$urlmenu = "/cuis/index/{$personne_id}";
			
			$this->set( compact( 'options', 'personne_id', 'dossierMenu', 'urlmenu', 'personne', 'mailEmployeur', 'correspondancesChamps', 'files' ) );
		}
		
		/**
		 * Envoi d'un E-mail
		 * 
		 * @param type $personne_id
		 * @param type $cui_id
		 * @param type $email_id
		 * @throws Exception
		 */
		public function email_send( $personne_id, $cui_id, $email_id ){
			$this->WebrsaAccesses->setWebrsaModel('WebrsaEmailcui')->setMainModel('Emailcui')
					->check($email_id, $personne_id, array('isModuleEmail' => true));
			$datas = $this->Cui->Emailcui->find('first', array( 'conditions' => array( 'Emailcui.id' => $email_id ) ) );
			$data = $datas['Emailcui'];
			
			$Piecemailcui66 = ClassRegistry::init( 'Piecemailcui66' );
			
			$filesIds = explode( '_', $data['pj'] );
			
			$filesNames = array();
			foreach( $filesIds as $fileId ){
				$filesNames = array_merge( $filesNames, $Piecemailcui66->getFichiersLiesById( $fileId ) );
			}
			
			$filesNames = $this->_transformOdtIntoPdf($filesNames, $cui_id);
			
			$Email = new CakeEmail( $this->configEmail );
			if ( !empty($data['emailredacteur']) ){
				$Email->replyTo( $data['emailredacteur'] );
				$Email->cc( $data['emailredacteur'] );
			}
			
			$Email	->subject( $data['titre'] )
					->attachments( $filesNames );
			
			// Si le mode debug est activé, on envoi l'e-mail à l'éméteur ( @see app/Config/email.php )
			if ( WebrsaEmailConfig::isTestEnvironment() ){
				$Email->to ( WebrsaEmailConfig::getValue( $this->configEmail, 'to', $Email->to() ) );
			}
			else{
				$Email->to( $data['emailemployeur'] );
			}
		
			$this->Cui->Emailcui->id = $email_id;
			$this->Cui->Emailcui->begin();
			try {
				if ( $Email->send( $data['message'] ) ){
					$this->Session->setFlash( 'E-mail envoyé avec succès', 'flash/success' );
					if ( !$this->Cui->Emailcui->saveField( 'dateenvoi', date('Y-m-d G:i:s') ) ){
						$this->Cui->Emailcui->rollback();
						$this->Session->setFlash( 'Sauvegarde en base impossible!', 'flash/error' );
					}
					$this->Cui->Emailcui->commit();
				}
				else{
					$this->Cui->Emailcui->rollback();
					throw new Exception( 'Envoi E-mail échoué!' );
				}
			}
			catch (Exception $e) {
				$this->Cui->Emailcui->rollback();
				$this->Session->setFlash( 'Erreur lors de l\'envoi de l\'E-mail.', 'flash/error' );
			}
			
			$this->Cui->Cui66->WebrsaCui66->updatePositionsCuisById( $cui_id );
			$this->redirect( array( 'action' => 'email', $personne_id, $cui_id ) );
		}
		
		/**
		 * Permet de récupérer en base les informations nécéssaire afin de générer le texte d'un e-mail
		 */
		public function ajax_generate_email(){
			$query = array(
				'conditions' => array(
					'id' => $this->request->data['Emailcui_textmailcui66_id']
				)
			);
			$modelEmail = ClassRegistry::init( 'Textmailcui66' )->find( 'first', $query );
			
			$options = $this->Cui->Emailcui->options( array( 'allocataire' => false, 'find' => false, 'autre' => false ) );
			
			$text = $modelEmail['Textmailcui66']['contenu'];
			preg_match_all('/#([A-Z][a-z_0-9]+)\.([a-z_0-9]+)#/', $text, $matches);
			
			$erreurs = array();
			foreach( $this->request->data as $input => $data ){
				if ( $input === 'Emailcui_insertiondate' ){
					$formatedDate = strftime("%A %d %B %Y", strtotime($data));
					$text = str_replace( '#Emailcui.insersiondate#', $formatedDate, $text );
				}
				elseif ( $input === 'Emailcui_piecesmanquantes' ){
					$piecesmanquantes = explode( '_', $data);
					
					foreach ($piecesmanquantes as $num_piece => $id_piece){
						$piecesmanquantes[$num_piece] = isset($options['Emailcui']['piecesmanquantes'][$id_piece]) ? $options['Emailcui']['piecesmanquantes'][$id_piece] : '';
					}
					$text = str_replace( '#Emailcui.piecesmanquantes#', implode("\n", $piecesmanquantes), $text );
				}
				elseif ( preg_match( '/^Emailcui_[\w]+$/', $input ) ){
					$input = str_replace( '.id', '_id', str_replace( '_', '.', $input ) );
					$text = str_replace( '#' . $input . '#', $data, $text );
				}
			}
			
			$options = $this->Cui->WebrsaCui->options();
			
			foreach( $matches[1] as $key => $value ){
				$modelName = $value;
				$fieldName = $matches[2][$key];
			
				if ( $modelName !== 'Emailcui' && isset($this->request->data[ $modelName . '_id' ]) ){
					if ( !isset($$modelName) ){
						$$modelName = ClassRegistry::init( $modelName );
						$$modelName->id = $this->request->data[ $modelName . '_id' ];
					}

					$fieldValue = $$modelName->field( $fieldName );
					
					$isDate = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])/", $fieldValue);
					
					if ( $isDate ){
						$tradFieldValue = strftime("%A %d %B %Y", strtotime($fieldValue));
					}
					else{
						$tradFieldValue = isset( $options[$modelName][$fieldName][$fieldValue] ) ? 
							$options[$modelName][$fieldName][$fieldValue] : 
							$fieldValue
						;
					}
					
					if ( empty($tradFieldValue) ){
						$erreurs[] = __d( 'cuis66', $modelName . '.' . $fieldName );
					}
					else{
						$text = str_replace( '#' . $modelName . '.' . $fieldName . '#', $tradFieldValue, $text );
					}
				}
			}
			
			// On retire les retours à la ligne en trop
			$text = preg_replace('/[\n\r]{3,}/', "\n\n", $text);
			
			if ( !empty($erreurs) ){
				$text = "[[[----------ERREURS----------]]]\n" . implode("\n", $erreurs) . "\n\nMerci de compléter les champs requis pour envoyer cet e-mail.";
			}
			
			$json = array(
				'EmailcuiTitre' => $modelEmail['Textmailcui66']['sujet'],
				'EmailcuiMessage' => $text
			);
			
			$this->set( compact( 'json' ) );
			$this->layout = 'ajax';
			$this->render( '/Elements/json' );
		}
		
		/**
		 * On lui donne l'id du CUI et le modèle de document et il renvoi le pdf
		 * 
		 * @param integer $cui_id
		 * @param string $modeleOdt
		 * @return PDF
		 */
		protected function _getCuiPdf( $cui_id, $modeleOdt = null ){
			$modeleOdt = 
				$modeleOdt === null || !isset($this->Cui->Cui66->modelesOdt[$modeleOdt]) 
				? $this->Cui->Cui66->modelesOdt['default'] 
				: $this->Cui->Cui66->modelesOdt[$modeleOdt]
			;
			$query = $this->Cui->Cui66->WebrsaCui66->queryImpression( $cui_id );
			$this->Cui->forceVirtualFields = true;
			
			$data = $this->Cui->find( 'first', $query );
			$options = $this->Cui->WebrsaCui->options();

			$data = $this->Cui->Cui66->WebrsaCui66->completeDataImpression( $data );

			$result = $this->Cui->ged(
				$data,
				$modeleOdt,
				true,
				$options
			);
			
			return $result;
		}
		
		/**
		 * Méthode générique d'impression d'un Cui.
		 * 
		 * @param integer $cui_id
		 * @param string $modeleOdt
		 */
		protected function _impression( $cui_id, $modeleOdt = null ){
			$this->WebrsaAccesses->setMainModel('Cui')->check($cui_id);
			$personne_id = $this->Cui->personneId( $cui_id );
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $personne_id ) );

			$pdf = $this->_getCuiPdf( $cui_id, $modeleOdt );

			if( !empty( $pdf ) ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'cui_%d-%s.pdf', $cui_id, date( 'Y-m-d' ) ) );
			}
			else {
				$this->Session->setFlash( 'Impossible de générer le PDF.', 'default', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}
		}
		
		/**
		 * Fiche de laison d'un Cui (ou fiche de synthèse)
		 *
		 * @param integer $cui_id La clé primaire du CUI
		 */
		public function impression_fichedeliaison( $cui_id ) {
			$this->_impression($cui_id, 'ficheLiaison');
		}
		
		/**
		 * Impression d'un CUI
		 *
		 * @param integer $cui_id La clé primaire du CUI
		 */
		public function impression( $cui_id ) {
			$this->_impression($cui_id);
		}
		
		/**
		 * Parametrages liés au CUI
		 */
		public function indexparams(){
			
		}
		
		/**
		 * Transforme les fichiers ODT en document PDF (avec remplissage des champs)
		 * 
		 * @param array $paths Liste des chemins vers les fichiers
		 * @param integer $cui_id
		 * @return array retourne la liste de fichiers avec retrait des ODT et ajout des PDF
		 */
		protected function _transformOdtIntoPdf( $paths, $cui_id ) {
			$newPaths = array();
			foreach ($paths as $path) {
				// Si l'extension du fichier n'est pas odt on l'ajoute à la nouvelle liste et on passe au prochain
				if ( strpos($path, '.odt') !== strlen($path)-4 ) {
					$newPaths[] = $path;
					continue;
				}
				
				// On sépare le chemin du nom de fichier
				$dirPath = explode(DS, $path);
				$odtName = $dirPath[count($dirPath)-1];
				unset($dirPath[count($dirPath)-1]); // Retrait du nom de fichier
				$dirPath = implode(DS, $dirPath);
				$fileName = substr($odtName, 0, strlen($odtName) -4); // Nom du fichier sans extension
				$pdfPath = $dirPath.DS.$fileName.'.pdf';
				
				// On récupère les données pour le remplissage du ODT
				$query = $this->Cui->Cui66->WebrsaCui66->queryImpression( $cui_id );
				$this->Cui->Cui66->forceVirtualFields = true;
				$data = $this->Cui->Cui66->find( 'first', $query );
				$options = $this->Cui->WebrsaCui->options();
				$data = $this->Cui->Cui66->WebrsaCui66->completeDataImpression( $data );
				$pdf = $this->Cui->ged(
					$data,
					$path,
					true,
					$options
				);
				
				// On créer un nouveau fichier à coté du fichier ODT
				if (is_file($pdfPath) ) {
					unlink($pdfPath); // Supprime le fichier pdf du même nom si il existe
				}
				$File = fopen($pdfPath, 'w');
				fwrite($File, $pdf);
				fclose($File);
				
				// On ajoute finalement le fichier à la liste
				$newPaths[] = $pdfPath;
			}
			
			return $pdfPath;
		}
	}
?>
