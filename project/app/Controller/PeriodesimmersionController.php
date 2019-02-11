<?php
	/**
	 * Code source de la classe PeriodesimmersionController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'LocaleHelper', 'View/Helper' );
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe PeriodesimmersionController ...
	 *
	 * @package app.Controller
	 * @deprecated since version 2.9.0
	 */
	class PeriodesimmersionController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Periodesimmersion';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'DossiersMenus',
			'Gedooo.Gedooo',
			'Jetons2',
			'RequestHandler',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
			'Default',
			'Default2',
			'Locale',
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Periodeimmersion',
			'Adressefoyer',
			'Cui',
			'Dossier',
			'Option',
			'Personne',
			'Referent',
			'Structurereferente',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Periodesimmersion:edit',
			'view' => 'Periodesimmersion:index',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'gedooo',
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
			'gedooo' => 'read',
			'index' => 'read',
			'view' => 'read',
		);

		/**
		 *
		 */
		protected function _setOptions() {
			$options = $this->Cui->enums();
			$this->set( 'rolepers', ClassRegistry::init('Prestation')->enum('rolepers') );
			$this->set( 'qual', $this->Option->qual() );
			$this->set( 'nationalite', ClassRegistry::init('Personne')->enum('nati') );

			$options['typevoie'] = $this->Option->typevoie();

			$this->set( compact( 'options', 'dept' ) );
		}

		/**
		 *
		 * @param integer $cui_id
		 */
		public function index( $cui_id = null ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Periodeimmersion->Cui->personneId( $cui_id ) ) ) );

			$nbrCuis = $this->Periodeimmersion->Cui->find( 'count', array( 'conditions' => array( 'Cui.id' => $cui_id ), 'recursive' => -1 ) );
			$this->assert( ( $nbrCuis == 1 ), 'invalidParameter' );

			$qd_cui = array(
				'conditions' => array(
					'Cui.id' => $cui_id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$cui = $this->Cui->find( 'first', $qd_cui );

			$personne_id = Set::classicExtract( $cui, 'Cui.personne_id' );

			$periodesimmersion = $this->Periodeimmersion->find(
					'all', array(
				'conditions' => array(
					'Periodeimmersion.cui_id' => $cui_id
				),
				'recursive' => -1
					)
			);

			$this->_setOptions();
			$this->set( 'personne_id', $personne_id );
			$this->set( 'cui_id', $cui_id );
			$this->set( compact( 'cuis', 'periodesimmersion' ) );
			$this->set( 'urlmenu', '/cuis/index/'.$personne_id );

			// Retour à la liste des CUI en cas de retour
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'cuis', 'action' => 'index', $personne_id ) );
			}
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
		 *
		 * @param integer $id
		 */
		protected function _add_edit( $id = null ) {
			if( $this->action == 'add' ) {
				$cui_id = $id;
				$qd_cui = array(
					'conditions' => array(
						'Cui.id' => $cui_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$cui = $this->Cui->find( 'first', $qd_cui );

				$personne_id = Set::classicExtract( $cui, 'Cui.personne_id' );
			}
			else if( $this->action == 'edit' ) {
				$periodeimmersion_id = $id;
				$qd_periodeimmersion = array(
					'conditions' => array(
						'Periodeimmersion.id' => $periodeimmersion_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$periodeimmersion = $this->Periodeimmersion->find( 'first', $qd_periodeimmersion );

				$this->assert( !empty( $periodeimmersion ), 'invalidParameter' );

				$cui_id = Set::classicExtract( $periodeimmersion, 'Periodeimmersion.cui_id' );
				$qd_cui = array(
					'conditions' => array(
						'Cui.id' => $cui_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$cui = $this->Cui->find( 'first', $qd_cui );

				$personne_id = Set::classicExtract( $cui, 'Cui.personne_id' );
			}

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			/// Peut-on prendre le jeton ?
			$dossier_id = $this->Periodeimmersion->Cui->Personne->dossierId( $personne_id );
			$this->Jetons2->get( $dossier_id );

			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				if( $this->action == 'edit' ) {
					$id = $this->Periodeimmersion->field( 'cui_id', array( 'id' => $id ) );
				}
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $id ) );
			}

			///On ajout l'ID de l'utilisateur connecté afind e récupérer son service instructeur
			$personne = $this->{$this->modelClass}->Cui->Personne->WebrsaPersonne->detailsApre( $personne_id, $this->Session->read( 'Auth.User.id' ) );
			$this->set( 'personne', $personne );
			$this->set( 'cui', $cui );
			$this->set( 'cui_id', $cui_id );

			$this->set( 'referents', $this->Referent->find( 'list' ) );
			$this->set( 'structs', $this->Structurereferente->listOptions() );

			if( !empty( $this->request->data ) ) {
				$this->Periodeimmersion->begin();

				$valid = $this->Periodeimmersion->saveAll( $this->request->data, array( 'validate' => 'only', 'atomic' => false ) );

				if( $valid ) {
					$saved = $this->Periodeimmersion->saveAll( $this->request->data, array( 'validate' => 'first', 'atomic' => false ) );

					if( $saved ) {
						$this->Periodeimmersion->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'controller' => 'periodesimmersion', 'action' => 'index', $cui_id ) );
					}
					else {
						$this->Periodeimmersion->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
				else {
					$this->Periodeimmersion->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else {
				if( $this->action == 'edit' ) {
					$this->request->data = $periodeimmersion;
				}
			}

			$this->_setOptions();
			$this->set( 'personne_id', $personne_id );
			$this->set( 'urlmenu', '/cuis/index/'.$personne_id );
			$this->render( 'add_edit' );
		}

		/**
		 *
		 * @param integer $id
		 */
		public function gedooo( $id ) {
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->Periodeimmersion->personneId( $id ) ) );

			$qual = $this->Option->qual();
			$typevoie = $this->Option->typevoie();
			$options = array();

			$periodeimmersion = $this->{$this->modelClass}->find(
				'first',
				array(
					'conditions' => array(
						"{$this->modelClass}.id" => $id
					),
					'contain' => array(
						'Cui'
					)
				)
			);

			$personne_id = Set::classicExtract( $periodeimmersion, 'Cui.personne_id' );
			$qd_personne = array(
				'conditions' => array(
					'Personne.id' => $personne_id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$personne = $this->Personne->find( 'first', $qd_personne );

			$periodeimmersion['Personne'] = $personne['Personne'];

			$this->Adressefoyer->bindModel(
					array(
						'belongsTo' => array(
							'Adresse' => array(
								'className' => 'Adresse',
								'foreignKey' => 'adresse_id'
							)
						)
					)
			);

			$adresse = $this->Adressefoyer->find(
				'first',
				array(
					'conditions' => array(
						'Adressefoyer.foyer_id' => Set::classicExtract( $periodeimmersion, 'Personne.foyer_id' ),
						'Adressefoyer.rgadr' => '01',
					)
				)
			);
			$periodeimmersion['Adresse'] = $adresse['Adresse'];

			$periodeimmersion_id = Set::classicExtract( $periodeimmersion, 'Actioncandidat.id' );

			///Traduction pour les données de la Personne/Contact/Partenaire/Référent
			$LocaleHelper = new LocaleHelper( new $this->viewClass( $this ) );
			//Données Periode immersion
			$periodeimmersion['Periodeimmersion']['typevoieentaccueil'] = Set::enum( Set::classicExtract( $periodeimmersion, 'Periodeimmersion.typevoieentaccueil' ), $typevoie );
			$periodeimmersion['Periodeimmersion']['datedebperiode'] = $LocaleHelper->date( 'Date::short', Set::classicExtract( $periodeimmersion, 'Periodeimmersion.datedebperiode' ) );
			$periodeimmersion['Periodeimmersion']['datefinperiode'] = $LocaleHelper->date( 'Date::short', Set::classicExtract( $periodeimmersion, 'Periodeimmersion.datefinperiode' ) );
			$periodeimmersion['Periodeimmersion']['datesignatureimmersion'] = $LocaleHelper->date( 'Date::short', Set::classicExtract( $periodeimmersion, 'Periodeimmersion.datesignatureimmersion' ) );
			$periodeimmersion['Periodeimmersion']['objectifimmersion'] = Set::enum( Set::classicExtract( $periodeimmersion, 'Periodeimmersion.objectifimmersion' ), $options['objectifimmersion'] );
			//Données Cui
			$periodeimmersion['Cui']['datedebprisecharge'] = $LocaleHelper->date( 'Date::short', Set::classicExtract( $periodeimmersion, 'Cui.datedebprisecharge' ) );
			$periodeimmersion['Cui']['datefinprisecharge'] = $LocaleHelper->date( 'Date::short', Set::classicExtract( $periodeimmersion, 'Cui.datefinprisecharge' ) );
			$periodeimmersion['Cui']['signaturele'] = $LocaleHelper->date( 'Date::short', Set::classicExtract( $periodeimmersion, 'Cui.signaturele' ) );
			//Données Personne
			$periodeimmersion['Personne']['qual'] = Set::enum( Set::classicExtract( $periodeimmersion, 'Personne.qual' ), $qual );
			$periodeimmersion['Personne']['dtnai'] = $LocaleHelper->date( 'Date::short', Set::classicExtract( $periodeimmersion, 'Personne.dtnai' ) );
		}

		/**
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->Periodeimmersion->personneId( $id ) ) );

			$this->Default->delete( $id );
		}

		/**
		 *
		 * @param integer $id
		 */
		public function view( $id ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Periodeimmersion->personneId( $id ) ) ) );

			$this->_setOptions();
			$this->Default->view( $id );
		}

	}
?>