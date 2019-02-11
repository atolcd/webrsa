<?php
	/**
	 * Code source de la classe AdressesfoyersController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'WebrsaAccessAdressesfoyers', 'Utility' );

	/**
	 * La classe AdressesfoyersController permet de lister, voir, ajouter et supprimer des adresses à un foyer RSA.
	 *
	 * @package app.Controller
	 */
	class AdressesfoyersController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Adressesfoyers';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Jetons2',
			'DossiersMenus',
			'WebrsaAccesses',
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
			'Adressefoyer',
			'Option',
			'WebrsaAdressefoyer',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Adressesfoyers:edit',
			'view' => 'Adressesfoyers:index',
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
		 * Commun à toutes les fonctions
		 *
		 * @return void
		 */
		public function beforeFilter() {
			parent::beforeFilter();

			$this->set( 'pays', ClassRegistry::init('Adresse')->enum('pays') );
			$this->set( 'rgadr', ClassRegistry::init('Adresse')->enum('rgadr') );
			$this->set( 'typeadr', ClassRegistry::init('Adressefoyer')->enum('typeadr') );
			$this->set( 'options', $this->Adressefoyer->Adresse->enums() );
		}

		/**
		 * Liste des adresses d'un foyer.
		 *
		 * @param integer $foyer_id L'id technique du Foyer pour lequel on veut les adresses.
		 */
		public function index( $foyer_id = null ) {
			// Vérification du format de la variable
			$this->assert( valid_int( $foyer_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );

			// Recherche des adresses du foyer
			$adresses = $this->WebrsaAccesses->getIndexRecords(
				$foyer_id, array(
					'fields' => array_merge(
						$this->Adressefoyer->fields(),
						$this->Adressefoyer->Adresse->fields()
					),
					'conditions' => array( 'Adressefoyer.foyer_id' => $foyer_id ),
					'contain' => array(
						'Adresse'
					)
				)
			);

			// Assignations à la vue
			$this->set( 'foyer_id', $foyer_id );
			$this->set( 'adresses', $adresses );
		}

		/**
		 * Visualisation d'une adresse spécifique.
		 *
		 * @param integer $id  L'id technique de l'enregistrement de la table adressesfoyers
		 */
		public function view( $id = null ) {
			$this->WebrsaAccesses->check($id);
			$foyer_id = $this->Adressefoyer->foyerId($id);

			$query = $this->WebrsaAdressefoyer->completeVirtualFieldsForAccess(
				array(
					'conditions' => array( 'Adressefoyer.id' => $id ),
					'contain' => array(
						'Adresse'
					)
				)
			);

			$paramsAccess = $this->WebrsaAdressefoyer->getParamsForAccess($foyer_id, WebrsaAccessAdressesfoyers::getParamsList());
			$adresse = WebrsaAccessAdressesfoyers::access($this->Adressefoyer->find('first', $query), $paramsAccess);

			// Mauvais paramètre
			$this->assert( !empty( $adresse ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $adresse['Adressefoyer']['foyer_id'] ) ) );

			// Assignation à la vue
			$this->set( 'adresse', $adresse );
			$this->set( 'urlmenu', '/adressesfoyers/index/'.$adresse['Adressefoyer']['foyer_id'] );
		}


		/**
		 * Ajouter une adresse à un foyer
		 *
		 * @param integer $foyer_id L'id technique du foyer auquel ajouter l'adresse.
		 * @return void
		 */
		public function add( $foyer_id = null ) {
			$this->WebrsaAccesses->check(null, $foyer_id);

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );

			$dossier_id = $this->Adressefoyer->Foyer->dossierId( $foyer_id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $foyer_id ) );
			}


			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				if( $this->Adressefoyer->saveAll( $this->request->data, array( 'validate' => 'only' ) ) ) {
					$this->Adressefoyer->begin();

					if( $this->Adressefoyer->saveNouvelleAdresse( $this->request->data ) ) {
						$this->Adressefoyer->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'controller' => 'adressesfoyers', 'action' => 'index', $foyer_id ) );
					}
					else {
						$this->Adressefoyer->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
			}

			// Assignation à la vue
			$this->set( 'foyer_id', $foyer_id );
			$this->render( 'add_edit' );
		}

		/**
		 * Modification d'une adresse du foyer.
		 *
		 * @param integer $id L'id technique dans la table adressesfoyers.
		 * @return void
		 */
		public function edit( $id = null ) {
			$this->WebrsaAccesses->check($id);

			$dossier_id = $this->Adressefoyer->dossierId( $id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Adressefoyer->id = $id;
				$foyer_id = $this->Adressefoyer->field( 'foyer_id' );
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $foyer_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Adressefoyer->begin();

				if( $this->Adressefoyer->saveAll( $this->request->data, array( 'validate' => 'only' ) ) ) {

					if( $this->Adressefoyer->saveAll( $this->request->data, array( 'atomic' => false ) ) ) {
						$this->Adressefoyer->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'controller' => 'adressesfoyers', 'action' => 'index', $this->request->data['Adressefoyer']['foyer_id'] ) );
					}
					else {
						$this->Adressefoyer->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
				else {
					$this->Adressefoyer->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			// Afficage des données
			else {
				$adresse = $this->Adressefoyer->find(
					'first',
					array(
						'conditions' => array( 'Adressefoyer.id' => $id ),
						'contain' => array(
							'Adresse'
						)
					)
				);

				// Mauvais paramètre
				$this->assert( !empty( $adresse ), 'invalidParameter' );

				// Assignation au formulaire
				$this->request->data = $adresse;
			}

			$this->set( 'urlmenu', '/adressesfoyers/index/'.$this->request->data['Adressefoyer']['foyer_id'] );
			$this->render( 'add_edit' );
		}
	}
?>