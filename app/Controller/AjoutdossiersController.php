<?php
	/**
	 * Code source de la classe AjoutdossiersController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * FIXME
	 *
	 * @param array $data
	 * @return boolean
	 */
	function hasConjoint( $data ) {
		return ( count( array_filter( $data ) ) > 3 );
	}

	/**
	 * La classe AjoutdossiersController ...
	 *
	 * @package app.Controller
	 */
	class AjoutdossiersController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Ajoutdossiers';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Wizard',
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
			'Dossier',
			'Adresse',
			'Adressefoyer',
			'Ajoutdossier',
			'Detaildroitrsa',
			'Detailressourcemensuelle',
			'Foyer',
			'Option',
			'Orientstruct',
			'Personne',
			'Ressource',
			'Ressourcemensuelle',
			'Serviceinstructeur',
			'Suiviinstruction',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(

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
			'confirm' => 'update',
			'wizard' => 'create',
		);

		/**
		 *
		 */
		public function beforeFilter() {
			// INFO: Supprimer la session, et donc les données du wizard
//             $this->Session->destroy();
			$this->Wizard->steps = array( 'allocataire', 'conjoint', 'adresse', 'ressourcesallocataire', array( 'withConjoint' => array( 'ressourcesconjoint', 'dossier' ), 'noConjoint' => array( 'dossier' ) ) );
			$this->Wizard->completeUrl = '/ajoutdossiers/confirm';
			$this->Wizard->cancelUrl = '/ajoutdossiers/wizard';
// $this->Wizard->resetWizard();

			return parent::beforeFilter();
		}

		/**
		 *
		 */
		public function confirm() {

		}

		/**
		 *
		 */
		public function wizard( $step = null ) {
			switch( $step ) {
				case 'allocataire':
				case 'conjoint':
					$this->set( 'qual', $this->Option->qual() );
					$this->set( 'nationalite', ClassRegistry::init('Personne')->enum('nati') );
					$this->set( 'typedtnai', ClassRegistry::init('Personne')->enum('typedtnai') );
					$this->set( 'pieecpres', ClassRegistry::init('Personne')->enum('pieecpres') );
					$this->set( 'rolepers', ClassRegistry::init('Prestation')->enum('rolepers') );
					break;
				case 'adresse':
					$this->set( 'pays', ClassRegistry::init('Adresse')->enum('pays') );
					$this->set( 'rgadr', ClassRegistry::init('Adresse')->enum('rgadr') );
					$this->set( 'typeadr', ClassRegistry::init('Adressefoyer')->enum('typeadr') );
					$this->set( 'libtypevoie', $this->Adresse->enum( 'libtypevoie' ) );
					break;
				case 'ressourcesallocataire':
					$wizardData = $this->Wizard->read();
					if( hasConjoint( $wizardData['conjoint']['Personne'] ) ) { // FIXME
						$this->Wizard->branch( 'withConjoint' );
					}
					else {
						$this->Wizard->branch( 'noConjoint' );
					}
				case 'ressourcesconjoint':
					$this->set( 'natress', ClassRegistry::init('Detailressourcemensuelle')->enum('natress') );
					$this->set( 'abaneu', ClassRegistry::init('Detailressourcemensuelle')->enum('abaneu') );
					break;
				case 'dossier':
					$this->set( 'oridemrsa', ClassRegistry::init('Detaildroitrsa')->enum('oridemrsa') );
					$this->set( 'fonorg', array( 'CAF' => 'CAF', 'MSA' => 'MSA' ) );
			}

			$this->set( 'typeservice', $this->Serviceinstructeur->listOptions() );

			$this->Wizard->process( $step );
		}

		/**
		 *
		 */
		public function _processAllocataire() {
			$this->Personne->set( $this->request->data );

			if( $this->Personne->validates() ) {
				return true;
			}
			return false;
		}

		/**
		 *
		 */
		public function _processConjoint() {
			if( hasConjoint( $this->request->data['Personne'] ) ) {
				$this->Personne->set( $this->request->data );

				if( $this->Personne->validates() ) {
					return true;
				}
				return false;
			}
			else {
				return true;
			}
		}

		/**
		 *
		 */
		public function _processAdresse() {
			$this->Adresse->set( $this->request->data );
			$this->Adressefoyer->set( $this->request->data );

			$valid = $this->Adresse->validates();
			$valid = $this->Adressefoyer->validates() && $valid;
			if( $valid ) {
				return true;
			}
			return false;
		}

		/**
		 *
		 */
		public function _processRessourcesallocataire() {
			$this->Ressource->create();
			$this->Ressourcemensuelle->create();
			$this->Detailressourcemensuelle->create();

			$this->Ressource->set( $this->request->data['Ressource'] );

			$valid = $this->Ressource->validates();
			if( !empty( $this->request->data['Ressourcemensuelle'] ) ) {
				$valid = $this->Ressourcemensuelle->saveAll( $this->request->data['Ressourcemensuelle'], array( 'validate' => 'only', 'atomic' => false ) ) && $valid;
				if( !empty( $this->request->data['Detailressourcemensuelle'] ) ) {
					$valid = $this->Detailressourcemensuelle->saveAll( $this->request->data['Detailressourcemensuelle'], array( 'validate' => 'only', 'atomic' => false ) ) && $valid;
				}
			}
			if( $valid ) {
				return true;
			}
			return false;
		}

		/**
		 *
		 */
		public function _processRessourcesconjoint() {
			$wizardData = $this->Wizard->read();
			if( hasConjoint( $wizardData['conjoint']['Personne'] ) ) { // FIXME
				$this->Ressource->create();
				$this->Ressourcemensuelle->create();
				$this->Detailressourcemensuelle->create();

				$this->Ressource->set( $this->request->data['Ressource'] );

				$valid = $this->Ressource->validates();
				if( !empty( $this->request->data['Ressourcemensuelle'] ) ) {
					$valid = $this->Ressourcemensuelle->saveAll( $this->request->data['Ressourcemensuelle'], array( 'validate' => 'only', 'atomic' => false ) ) && $valid;
					if( !empty( $this->request->data['Detailressourcemensuelle'] ) ) {
						$valid = $this->Detailressourcemensuelle->saveAll( $this->request->data['Detailressourcemensuelle'], array( 'validate' => 'only', 'atomic' => false ) ) && $valid;
					}
				}
				if( $valid ) {
					return true;
				}
				return false;
			}
			else {
				return true;
			}
		}

		/**
		 *
		 */
		public function _processDossier() {
			$this->Dossier->set( $this->request->data );
			$this->Foyer->set( $this->request->data );
			$this->Ajoutdossier->set( $this->request->data );

			$valid = $this->Dossier->validates();
			$valid = $this->Foyer->validates() && $valid;
			$valid = $this->Ajoutdossier->validates() && $valid;


			if( $valid ) {
				return true;
			}
			return false;
		}

		/**
		 * Wizard Completion Callback
		 */
		public function _afterComplete() {
			$data = $this->Wizard->read();

			// Revalidation
			$this->Personne->set( $data['allocataire']['Personne'] );
			$valid = $this->Personne->validates();

			if( hasConjoint( $data['conjoint']['Personne'] ) ) { // FIXME
				$this->Personne->set( $data['conjoint']['Personne'] );
				$valid = $this->Personne->validates() && $valid;
			}

			$this->Adresse->set( $data['adresse']['Adresse'] );
			$this->Adressefoyer->set( $data['adresse']['Adressefoyer'] );
			$valid = $this->Adresse->validates() && $valid;
			$valid = $this->Adressefoyer->validates() && $valid;

			$this->Ajoutdossier->set( $data['dossier']['Ajoutdossier'] );
			$valid = $this->Ajoutdossier->validates() && $valid;

			// Ressources allocataire
			$this->Ressource->create();
			$this->Ressourcemensuelle->create();
			$this->Detailressourcemensuelle->create();

			$data['ressourcesallocataire']['Ressource']['topressnul'] = !$data['ressourcesallocataire']['Ressource']['topressnotnul'];

			$this->Ressource->set( $data['ressourcesallocataire'] );
			$valid = $this->Ressource->validates();
			if( !empty( $data['ressourcesallocataire']['Ressourcemensuelle'] ) ) {
				$valid = $this->Ressourcemensuelle->saveAll( $data['ressourcesallocataire'], array( 'validate' => 'only', 'atomic' => false ) ) && $valid;
				if( !empty( $data['ressourcesallocataire']['Detailressourcemensuelle'] ) ) {
					$valid = $this->Detailressourcemensuelle->saveAll( $data['ressourcesallocataire'], array( 'validate' => 'only', 'atomic' => false ) ) && $valid;
				}
			}

			// Ressources conjoint
			if( hasConjoint( $data['conjoint']['Personne'] ) ) { // FIXME
				$this->Ressource->create();
				$this->Ressourcemensuelle->create();
				$this->Detailressourcemensuelle->create();

				// FIXME ?
				if( isset( $data['ressourcesconjoint']['Ressource']['topressnotnul'] ) ) {
					$data['ressourcesconjoint']['Ressource']['topressnul'] = !$data['ressourcesconjoint']['Ressource']['topressnotnul'];
				}

				$this->Ressource->set( $data['ressourcesconjoint'] );
				$valid = $this->Ressource->validates();
				if( !empty( $data['ressourcesconjoint']['Ressourcemensuelle'] ) ) {
					$valid = $this->Ressourcemensuelle->saveAll( $data['ressourcesconjoint'], array( 'validate' => 'only', 'atomic' => false ) ) && $valid;
					if( !empty( $data['ressourcesconjoint']['Detailressourcemensuelle'] ) ) {
						$valid = $this->Detailressourcemensuelle->saveAll( $data['ressourcesconjoint'], array( 'validate' => 'only', 'atomic' => false ) ) && $valid;
					}
				}
			}
// debug($data);
			/**
			 * TODO
			 */
			// Sauvegarde
			if( $valid ) {
				// Début de la transaction
				$this->Dossier->begin();

				if( !empty( $data['dossier']['Dossier']['numdemrsatemp'] ) ) {
					$data['dossier']['Dossier']['numdemrsa'] = $this->Dossier->generationNumdemrsaTemporaire();
				}

				// Tentatives de sauvegarde
				$saved = $this->Dossier->save( $data['dossier']['Dossier'] , array( 'atomic' => false ) );

				// Détails du droit
				$data['dossier']['Detaildroitrsa']['dossier_id'] = $this->Dossier->id;
				$saved = $this->Detaildroitrsa->save( $data['dossier']['Detaildroitrsa'] , array( 'atomic' => false ) ) && $saved;

				// Situation dossier RSA
				$situationdossierrsa = array( 'Situationdossierrsa' => array( 'dossier_id' => $this->Dossier->id, 'etatdosrsa' => 'Z' ) ); ///FIXME Remplacement de l'état de Null à Z
				$this->Dossier->Situationdossierrsa->validate = array( );
				$saved = $this->Dossier->Situationdossierrsa->save( $situationdossierrsa , array( 'atomic' => false ) ) && $saved;

				// Foyer
				$saved = $this->Foyer->save( array( 'dossier_id' => $this->Dossier->id ), array( 'atomic' => false ) ) && $saved;

				// Adresse
				$saved = $this->Adresse->save( $data['adresse']['Adresse'] , array( 'atomic' => false ) ) && $saved;

				// Adresse foyer
				$data['adresse']['Adressefoyer']['foyer_id'] = $this->Foyer->id;
				$data['adresse']['Adressefoyer']['adresse_id'] = $this->Adresse->id;
				$saved = $this->Adressefoyer->save( $data['adresse']['Adressefoyer'] , array( 'atomic' => false ) ) && $saved;

				// Demandeur
				$this->Personne->create();
				$data['allocataire']['Personne']['foyer_id'] = $this->Foyer->id;
				$this->Personne->set( $data['allocataire'] );
				$saved = $this->Personne->save( $data['allocataire'] , array( 'atomic' => false ) ) && $saved;
				$demandeur_id = $this->Personne->id;

				// Prestation
				$this->Personne->Prestation->create();
				$data['allocataire']['Prestation']['personne_id'] = $demandeur_id;
				$this->Personne->Prestation->set( $data['allocataire'] );
				$saved = $this->Personne->Prestation->save( $data['allocataire'] , array( 'atomic' => false ) ) && $saved;

				// Type orientation demandeur
				$this->Orientstruct->create();
				$saved = $this->Orientstruct->save( array( 'Orientstruct' => array( 'personne_id' => $demandeur_id, 'statut_orient' => 'Non orienté' ) ), array( 'atomic' => false ) );

				// Conjoint
				if( hasConjoint( $data['conjoint']['Personne'] ) ) { // FIXME
					$this->Personne->create();
					$data['conjoint']['Personne']['foyer_id'] = $this->Foyer->id;
					$saved = $this->Personne->save( $data['conjoint']['Personne'] , array( 'atomic' => false ) );
					$conjoint_id = $this->Personne->id;

					// Prestation
					$this->Personne->Prestation->create();
					$data['conjoint']['Prestation']['personne_id'] = $conjoint_id;
					$this->Personne->Prestation->set( $data['conjoint'] );
					$saved = $this->Personne->Prestation->save( $data['conjoint'] , array( 'atomic' => false ) ) && $saved;

					// Type orientation conjoint
					$this->Orientstruct->create();
					$saved = $this->Orientstruct->save( array( 'Orientstruct' => array( 'personne_id' => $conjoint_id, 'statut_orient' => 'Non orienté' ) ), array( 'atomic' => false ) );
				}
				// Ressources demandeur
				$this->Ressource->create();
				$data['ressourcesallocataire']['Ressource']['personne_id'] = $demandeur_id;
				$saved = $this->Ressource->save( $data['ressourcesallocataire'] , array( 'atomic' => false ) ) && $saved;

				if( !empty( $data['ressourcesallocataire']['Ressourcemensuelle'] ) ) {
					foreach( $data['ressourcesallocataire']['Ressourcemensuelle'] as $key => $ressourcemensuelle ) {
						$ressourcemensuelle['ressource_id'] = $this->Ressource->id;
						$this->Ressourcemensuelle->create();
						$saved = $this->Ressourcemensuelle->save( $ressourcemensuelle , array( 'atomic' => false ) ) && $saved;
						if( !empty( $data['ressourcesallocataire']['Detailressourcemensuelle'] ) && !empty( $data['ressourcesallocataire']['Detailressourcemensuelle'][$key] ) ) {
							$this->Detailressourcemensuelle->create();
							$data['ressourcesallocataire']['Detailressourcemensuelle'][$key]['ressourcemensuelle_id'] = $this->Ressourcemensuelle->id;
							$saved = $this->Detailressourcemensuelle->save( $data['ressourcesallocataire']['Detailressourcemensuelle'][$key] , array( 'atomic' => false ) ) && $saved;
						}
					}
				}

				// Ressources conjoint
				if( hasConjoint( $data['conjoint']['Personne'] ) ) { // FIXME
					$this->Ressource->create();
					$data['ressourcesconjoint']['Ressource']['personne_id'] = $conjoint_id;
					$saved = $this->Ressource->save( $data['ressourcesconjoint'] , array( 'atomic' => false ) ) && $saved;

					if( !empty( $data['ressourcesconjoint']['Ressourcemensuelle'] ) ) {
						foreach( $data['ressourcesconjoint']['Ressourcemensuelle'] as $key => $ressourcemensuelle ) {
							$ressourcemensuelle['ressource_id'] = $this->Ressource->id;
							$this->Ressourcemensuelle->create();
							$saved = $this->Ressourcemensuelle->save( $ressourcemensuelle , array( 'atomic' => false ) ) && $saved;
							if( !empty( $data['ressourcesconjoint']['Detailressourcemensuelle'] ) && !empty( $data['ressourcesconjoint']['Detailressourcemensuelle'][$key] ) ) {
								$this->Detailressourcemensuelle->create();
								$data['ressourcesconjoint']['Detailressourcemensuelle'][$key]['ressourcemensuelle_id'] = $this->Ressourcemensuelle->id;
								$saved = $this->Detailressourcemensuelle->save( $data['ressourcesconjoint']['Detailressourcemensuelle'][$key] , array( 'atomic' => false ) ) && $saved;
							}
						}
					}
				}

				// Service instructeur
				$service = $this->Serviceinstructeur->find(
					'first',
					array(
						'conditions' => array(
							'Serviceinstructeur.id' => $data['dossier']['Ajoutdossier']['serviceinstructeur_id']
						),
						'recursive' => -1
					)
				);
				$this->assert( !empty( $service ), 'error500' );

				// Utilisateur
				$user = $this->User->find(
						'first', array(
					'conditions' => array(
						'User.id' => $this->Session->read( 'Auth.User.id' )
					),
					'recursive' => -1
						)
				);
				$this->assert( !empty( $user ), 'error500' );

				$suiviinstruction = array(
					'Suiviinstruction' => array(
						'dossier_id' => $this->Dossier->id,
						'suiirsa' => '01',
						'date_etat_instruction' => strftime( '%Y-%m-%d' ),
						'nomins' => $user['User']['nom'],
						'prenomins' => $user['User']['prenom'],
						'numdepins' => $service['Serviceinstructeur']['numdepins'],
						'typeserins' => $service['Serviceinstructeur']['typeserins'],
						'numcomins' => $service['Serviceinstructeur']['numcomins'],
						'numagrins' => $service['Serviceinstructeur']['numagrins']
					)
				);
				$this->Suiviinstruction->set( $suiviinstruction );

				if( $this->Suiviinstruction->validates() ) { // FIXME -> plus haut
					$saved = $this->Suiviinstruction->save( $suiviinstruction , array( 'atomic' => false ) ) && $saved;
				}

				// Fin de la transaction
				if( $saved ) {
					$this->Dossier->commit();
					$this->Wizard->resetWizard();
					$this->redirect( array( 'controller' => 'dossiers', 'action' => 'view', $this->Dossier->id ) );
				}
				// Annulation de la transaction
				else {
					$this->Dossier->rollback();
					$this->cakeError( 'error500' );
				}
			}
		}

	}
?>