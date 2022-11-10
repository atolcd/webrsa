<?php
	/**
	 * Code source de la classe ModescontactController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'WebrsaAccessModescontact', 'Utility' );

	/**
	 * La classe ModescontactController ...
	 * (CG 66 et 93).
	 *
	 * @package app.Controller
	 */
	class ModescontactController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Modescontact';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Jetons2',
			'WebrsaAccesses',
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
			),
			'Theme',
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Modecontact',
			'Foyer',
			'Option',
			'WebrsaModecontact',
			'Infocontactpersonne',
			'Infocontactpersonnecaf'
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'view' => 'Modescontact:index',
			'add' => 'Modescontact:edit',
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
			'edit' => 'update',
			'index' => 'read',
			'view' => 'read',
		);

		/**
		 *
		 */
		protected function _setOptions() {
			$options = $this->Modecontact->enums();
			$this->set( compact( 'options' ) );
		}

		/**
		 *
		 * @param integer $foyer_id
		 */
		public function index( $foyer_id = null, $onglet = 'foyer' ){
			// TODO : vérif param
			// Vérification du format de la variable
			$this->assert( valid_int( $foyer_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );

			//Recherche des personnes du foyer
			$personnesfoyer = $this->Modecontact->getPersonnesFoyer($foyer_id);

			// Recherche des modes de contact du foyer
			$modescontactfoyer = $this->WebrsaAccesses->getIndexRecords(
				$foyer_id, array(
					'fields' => $this->Modecontact->fields(),
					'conditions' => array('Modecontact.foyer_id' => $foyer_id),
					'contain' => false,
					'order' => ['Modecontact.id' => 'desc']
				)
			);

			//Modes de contact du demandeur
			//saisie manuelle
			$saisiemanuelleDEM = $this->Infocontactpersonne->getContactsPersonne($personnesfoyer[0][0]['id']);

			//flux modes de contacts
			$fluxcontactDEM = $this->Infocontactpersonnecaf->getContactsPersonne($personnesfoyer[0][0]['id']);

			if(isset($personnesfoyer[1])){
				//Modes de contact du conjoint
				//saisie manuelle
				$saisiemanuelleCJT = $this->Infocontactpersonne->getContactsPersonne($personnesfoyer[1][0]['id']);

				//flux modes de contacts
				$fluxcontactCJT = $this->Infocontactpersonnecaf->getContactsPersonne($personnesfoyer[1][0]['id']);


				$this->set(compact('saisiemanuelleCJT', 'fluxcontactCJT'));
				$this->set( 'conjoint', $personnesfoyer[1][0] );

			}

			// Assignations à la vue
			$this->set( 'demandeur', $personnesfoyer[0][0] );
			$this->set(compact('foyer_id', 'modescontactfoyer', 'saisiemanuelleDEM', 'fluxcontactDEM', 'onglet'));
			$this->_setOptions();
		}

		/**
		 *
		 * @param integer $foyer_id
		 */
		public function add( $foyer_id = null ){
			$this->WebrsaAccesses->check(null, $foyer_id);

			$dossier_id = $this->Foyer->dossierId( $foyer_id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $foyer_id ) );
			}

			$this->Jetons2->get( $dossier_id );

			if( !empty( $this->request->data ) ) {
				$this->Modecontact->create( $this->request->data );
				if( $this->Modecontact->validates() ) {
					$this->Modecontact->begin();

					if( $this->Modecontact->save( $this->request->data , array( 'atomic' => false ) ) ) {
						$this->Modecontact->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'controller' => 'modescontact', 'action' => 'index', $foyer_id ) );
					}
					else {
						$this->Modecontact->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
			}

			$this->set( 'foyer_id', $foyer_id );
			$this->request->data['Modecontact']['foyer_id'] = $foyer_id;

			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		/**
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ){
			$this->WebrsaAccesses->check($id);

			$dossier_id = $this->Modecontact->dossierId( $id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$this->Jetons2->get( $dossier_id );

			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$id = $this->Modecontact->field( 'foyer_id', array( 'id' => $id ) );
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Modecontact->set( $this->request->data );
				if( $this->Modecontact->validates() ) {
					$this->Modecontact->begin();

					if( $this->Modecontact->save( $this->request->data , array( 'atomic' => false ) ) ) {
						$this->Modecontact->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array(  'controller' => 'modescontact','action' => 'index', $this->request->data['Modecontact']['foyer_id'] ) );
					}
					else {
						$this->Modecontact->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
			}
			// Afficage des données
			else {
				$modecontact = $this->Modecontact->find(
					'first',
					array(
						'conditions' => array( 'Modecontact.id' => $id ),
						'contain' => false
					)
				);
				$this->assert( !empty( $modecontact ), 'invalidParameter' );

				// Assignation au formulaire
				$this->request->data = $modecontact;
			}

			$this->Modecontact->commit();
			$this->_setOptions();
			$this->render( 'add_edit' );

		}

		/**
		 *
		 * @param integer $modecontact_id
		 */
		public function view( $modecontact_id = null ) {
			$this->WebrsaAccesses->check($modecontact_id);

			$modecontact = $this->Modecontact->find(
				'first',
				array(
					'conditions' => array(
						'Modecontact.id' => $modecontact_id
					),
					'recursive' => -1
				)

			);

			$this->assert( !empty( $modecontact ), 'error404' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $modecontact['Modecontact']['foyer_id'] ) ) );

			// Assignations à la vue
			$this->set( 'foyer_id', $modecontact['Modecontact']['foyer_id'] );
			$this->set( 'modecontact', $modecontact );
			$this->_setOptions();

		}
	}
?>