<?php
	/**
	 * Code source de la classe RendezvousController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'LocaleHelper', 'View/Helper' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
	App::uses( 'WebrsaAccessRendezvous', 'Utility' );

	/**
	 * La classe RendezvousController ...
	 *
	 * @package app.Controller
	 */
	class RendezvousController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Rendezvous';

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
			'InsertionsBeneficiaires',
			'Jetons2',
			'Workflowscers93',
			'Search.SearchPrg' => array(
				'actions' => array(
					'search' => array('filter' => 'Search'),
				),
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
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
			'Locale',
			'Xform',
			'Search.SearchForm',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Rendezvous',
			'Option',
			'WebrsaRendezvous',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Rendezvous:edit',
			'view' => 'Rendezvous:index',
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
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'update',
			'ajaxreffonct' => 'read',
			'delete' => 'delete',
			'download' => 'read',
			'edit' => 'update',
			'exportcsv' => 'read',
			'filelink' => 'read',
			'fileview' => 'read',
			'impression' => 'read',
			'index' => 'read',
			'search' => 'read',
			'view' => 'read',
		);

		/**
		 * Moteur de recherche par rendez-vous.
		 */
		public function search() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesRendezvous' );
			$Recherches->search();
		}

		/**
		 * Export CSV des résultats de la recherche par rendez-vous.
		 */
		public function exportcsv() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesRendezvous' );
			$Recherches->exportcsv();
		}

		/**
		 *
		 */
		protected function _setOptions() {
			$this->set( 'struct', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'permanences', $this->Rendezvous->Permanence->listOptions() );
			$this->set( 'statutrdv', $this->Rendezvous->Statutrdv->find( 'list', array('conditions' => array('actif' => 1)) ) );
			$this->set( 'options', (array)Hash::get( $this->Rendezvous->enums(), 'Rendezvous' ) );
		}

		/**
		 *   Ajax pour les coordonnées du référent
		 */
		public function ajaxreffonct( $referent_id = null ) { // FIXME
			Configure::write( 'debug', 0 );

			$referent_id = trim( $referent_id, '_' );

			if( !empty( $referent_id ) ) {
				$referent_id = suffix( $referent_id );
			}
			else {
				$referent_id = suffix( Set::extract( $this->request->data, 'Rendezvous.referent_id' ) );
			}

			$referent = array( );
			if( !empty( $referent_id ) ) {
				$qd_referent = array(
					'conditions' => array(
						'Referent.id' => $referent_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$referent = $this->Rendezvous->Referent->find( 'first', $qd_referent );
			}

			$this->set( 'referent', $referent );
			$this->render( 'ajaxreffonct', 'ajax' );
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
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$fichiers = array( );
			$rendezvous = $this->Rendezvous->find(
				'first',
				array(
					'conditions' => array(
						'Rendezvous.id' => $id
					),
					'contain' => array(
						'Fichiermodule' => array(
							'fields' => array( 'name', 'id', 'created', 'modified' )
						)
					)
				)
			);

			$personne_id = $rendezvous['Rendezvous']['personne_id'];
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );
			$dossier_id = $this->Rendezvous->Personne->dossierId( $personne_id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Rendezvous->begin();

				$saved = $this->Rendezvous->updateAllUnBound(
					array( 'Rendezvous.haspiecejointe' => '\''.$this->request->data['Rendezvous']['haspiecejointe'].'\'' ),
					array(
						'"Rendezvous"."personne_id"' => $personne_id,
						'"Rendezvous"."id"' => $id
					)
				);

				if( $saved ) {
					// Sauvegarde des fichiers liés à une PDO
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, !Set::classicExtract( $this->request->data, "Rendezvous.haspiecejointe" ), $id ) && $saved;
				}

				if( $saved ) {
					$this->Rendezvous->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('action' => 'index', $personne_id));
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Rendezvous->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->_setOptions();
			$this->set( 'urlmenu', '/rendezvous/index/'.$personne_id );
			$this->set( compact( 'dossier_id', 'personne_id', 'fichiers', 'rendezvous' ) );
		}

		/**
		 *
		 * @param type $personne_id
		 */
		public function index( $personne_id = null ) {
			$this->Rendezvous->Personne->unbindModelAll();
			$nbrPersonnes = $this->Rendezvous->Personne->find(
				'count',
				array(
					'conditions' => array(
						'Personne.id' => $personne_id
					),
					'contain' => false
				)
			);
			$this->assert( ( $nbrPersonnes == 1 ), 'invalidParameter' );

			$this->_setEntriesAncienDossier( $personne_id, 'Rendezvous' );

			// Ajoute une alerte en cas EPL en cours
			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$query = $this->Rendezvous->Personne->Dossierep->qdDossiersepsOuverts( $personne_id );
				$query['fields'] = array( 'Dossierep.id' );
				$result = $this->Rendezvous->Personne->Dossierep->find( 'first', $query );
				if( !empty( $result ) ) {
					$this->Flash->error( 'Attention, une décision EPL est en cours.' );
				}
			}

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$this->set( compact( 'dossierMenu' ) );

			// On conditionne l'affichage des RDVs selon la structure référente liée au RDV
			// Si la structure de l'utilisateur connecté est différente de celle du RDV, on ne l'affiche pas.
			$conditionStructure = array();
			if( Configure::read( 'Cg.departement' ) == 93 ) {
				$structurereferente_id = $this->Workflowscers93->getUserStructurereferenteId( false );
				if( !is_null( $structurereferente_id ) ) {
					$conditionStructure = array( 'Rendezvous.structurereferente_id' => $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'ids' ) ) );
				}
			}

			$query = array(
				'fields' => array_merge(
					$this->Rendezvous->fields(),
					array(
						$this->Rendezvous->Personne->sqVirtualField( 'nom_complet' ),
						'Structurereferente.lib_struc',
						$this->Rendezvous->Referent->sqVirtualField( 'nom_complet' ),
						'Permanence.libpermanence',
						'Typerdv.libelle',
						'Statutrdv.libelle',
						$this->Rendezvous->Fichiermodule->sqNbFichiersLies( $this->Rendezvous, 'nb_fichiers_lies' )
					)
				),
				'joins' => array(
					$this->Rendezvous->join( 'Personne' ),
					$this->Rendezvous->join( 'Structurereferente' ),
					$this->Rendezvous->join( 'Referent' ),
					$this->Rendezvous->join( 'Statutrdv' ),
					$this->Rendezvous->join( 'Permanence' ),
					$this->Rendezvous->join( 'Typerdv' ),
				),
				'contain' => false,
				'conditions' => array(
					'Rendezvous.personne_id' => $personne_id,
					$conditionStructure
				),
				'order' => array(
					'Rendezvous.daterdv DESC',
					'Rendezvous.heurerdv DESC'
				)
			);

			if( Configure::read( 'Cg.departement' ) == '93' ) {
				if( false === $this->Rendezvous->Behaviors->attached( 'LinkedRecords' ) ) {
					$this->Rendezvous->Behaviors->attach( 'LinkedRecords' );
				}
				$query = $this->Rendezvous->linkedRecordsCompleteQuerydata( $query, 'Questionnaired1pdv93' );
			}

			if( true === Configure::read( 'Rendezvous.useThematique' ) ) {
				$query['fields'][] = 'Rendezvous.thematiques';
			}

			$rdvs = $this->Rendezvous->find( 'all', $query );

			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$dossierep = $this->Rendezvous->Personne->Dossierep->find(
					'first',
					array(
						'fields' => array(
							'StatutrdvTyperdv.motifpassageep',
						),
						'joins' => array(
							$this->Rendezvous->Personne->Dossierep->join( 'Sanctionrendezvousep58' ),
							$this->Rendezvous->Personne->Dossierep->Sanctionrendezvousep58->join( 'Rendezvous' ),
							$this->Rendezvous->Personne->Dossierep->Sanctionrendezvousep58->Rendezvous->join( 'Typerdv' ),
							$this->Rendezvous->Personne->Dossierep->Sanctionrendezvousep58->Rendezvous->Typerdv->join( 'StatutrdvTyperdv' )
						),
						'conditions' => array(
							'Dossierep.themeep' => 'sanctionsrendezvouseps58',
							'Dossierep.personne_id' => $personne_id,
							'Dossierep.actif' => '1',
							'Dossierep.id NOT IN ( '.
							$this->Rendezvous->Personne->Dossierep->Passagecommissionep->sq(
									array(
										'fields' => array(
											'passagescommissionseps.dossierep_id'
										),
										'alias' => 'passagescommissionseps',
										'conditions' => array(
											'passagescommissionseps.etatdossierep' => array( 'traite', 'annule' )
										)
									)
							)
							.' )'
						),
						'order' => array( 'Dossierep.created ASC' )
					)
				);
				$this->set( compact( 'dossierep' ) );

				$dossiercov = $this->Rendezvous->Personne->Dossiercov58->find(
					'first',
					array(
						'fields' => array(
							'StatutrdvTyperdv.motifpassageep',
						),
						'joins' => array(
							$this->Rendezvous->Personne->Dossiercov58->join( 'Propoorientsocialecov58' ),
							$this->Rendezvous->Personne->Dossiercov58->Propoorientsocialecov58->join( 'Rendezvous' ),
							$this->Rendezvous->Personne->Dossiercov58->Propoorientsocialecov58->Rendezvous->join( 'Typerdv' ),
							$this->Rendezvous->Personne->Dossiercov58->Propoorientsocialecov58->Rendezvous->Typerdv->join( 'StatutrdvTyperdv' )
						),
						'conditions' => array(
							'Dossiercov58.themecov58' => 'proposorientssocialescovs58',
							'Dossiercov58.personne_id' => $personne_id,
							'Dossiercov58.id NOT IN ( '.
								$this->Rendezvous->Personne->Dossiercov58->Passagecov58->sq(
									array(
										'fields' => array(
											'passagescovs58.dossiercov58_id'
										),
										'alias' => ' passagescovs58',
										'conditions' => array(
											'passagescovs58.etatdossiercov' => array( 'traite', 'annule' )
										)
									)
								)
							.' )'
						),
						'order' => array( 'Dossiercov58.created ASC' ),
						'contain' => false
					)
				);
				$this->set( compact( 'dossiercov' ) );
			}

			$rdvs = $this->WebrsaAccesses->getIndexRecords($personne_id, $query);

			$this->set(compact('rdvs', 'personne_id'));
		}

		/**
		 *
		 */
		public function view( $rendezvous_id = null ) {
			$this->WebrsaAccesses->check($rendezvous_id);
			$rendezvous = $this->Rendezvous->find(
				'first',
				array(
					'fields' => array(
						'Rendezvous.personne_id',
                        $this->Rendezvous->Personne->sqVirtualField( 'nom_complet' ),
                        $this->Rendezvous->Referent->sqVirtualField( 'nom_complet' ),
						'Structurereferente.lib_struc',
						'Referent.fonction',
						'Permanence.libpermanence',
						'Typerdv.libelle',
						'Statutrdv.libelle',
						'Rendezvous.daterdv',
						'Rendezvous.heurerdv',
						'Rendezvous.objetrdv',
						'Rendezvous.commentairerdv'
					),
					'conditions' => array(
						'Rendezvous.id' => $rendezvous_id
					),
					'contain' => array(
						'Typerdv',
						'Referent',
						'Structurereferente',
						'Permanence',
						'Statutrdv',
						'Personne',
						'Thematiquerdv',
					)
				)
			);

			$this->assert( !empty( $rendezvous ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $rendezvous['Rendezvous']['personne_id'] ) ) );

			$this->set( 'rendezvous', $rendezvous );
			$this->set( 'personne_id', $rendezvous['Rendezvous']['personne_id'] );
			$this->set( 'urlmenu', '/rendezvous/index/'.$rendezvous['Rendezvous']['personne_id'] );
		}

		/**
		 *
		 */
		public function add($personne_id) {
			$this->WebrsaAccesses->check(null, $personne_id);
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 */
		public function edit($id) {
			$this->WebrsaAccesses->check($id);
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 */
		protected function _add_edit( $id = null ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			// Récupération des id afférents
			if( $this->action == 'add' ) {
				$personne_id = $id;
				$dossier_id = $this->Rendezvous->Personne->dossierId( $personne_id );
			}
			else if( $this->action == 'edit' ) {
				$rdv_id = $id;
				$qd_rdv = array(
					'contain' => array(
						'Thematiquerdv'
					),
					'conditions' => array(
						'Rendezvous.id' => $rdv_id
					),
				);
				$rdv = $this->Rendezvous->find( 'first', $qd_rdv );
				$this->assert( !empty( $rdv ), 'invalidParameter' );

				$personne_id = $rdv['Rendezvous']['personne_id'];
				$dossier_id = $this->Rendezvous->dossierId( $rdv_id );
			}

            //date de naissance - pour alert sur +55ans
            $reqDateNaissance = $this->Rendezvous->Personne->find (
				'first',
				array (
					'recursive' => -1,
					'fields' => array ('Personne.dtnai'),
					'conditions' => array ('Personne.id' => $personne_id)
				)
			);
			$ageBeneficiaire = age ($reqDateNaissance['Personne']['dtnai']);

			//CER actif - pour alert sur +55ans
			$query = 'SELECT COUNT(id) FROM contratsinsertion WHERE decision_ci = \'V\' AND personne_id = '.$personne_id;
			$nbCER = $this->Rendezvous->query ($query);

			$ageLimite = Configure::read( 'Tacitereconduction.limiteAge' );

			if( is_numeric( $ageLimite ) ) {
				$alertTrancheAge = ($ageBeneficiaire >= $ageLimite && $nbCER[0][0]['count'] > 0) ? true : false;
				//stockage des variables - Si l'age est >55ans et qu'il y a un CER, on affiche une alerte sur la vue
				$this->set('alertTrancheAge', $alertTrancheAge);
			}

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			$dossier_id = $this->Rendezvous->Personne->dossierId( $personne_id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->Jetons2->get( $dossier_id );

			if( !empty( $this->request->data ) ) {
				$this->Rendezvous->begin();

				if( isset( $this->request->data['Rendezvous']['arevoirle'] ) ) {
					$this->request->data['Rendezvous']['arevoirle']['day'] = '01';
				}

				$success = $this->Rendezvous->saveAll( $this->request->data, array( 'validate' => 'first', 'atomic' => false ) );

				if( $this->Rendezvous->WebrsaRendezvous->provoquePassageCommission( $this->request->data ) ) {
					//On commit maintenant pour récupérer l'id du rendez-vous et pouvoir créer le passage en commission
					$this->Rendezvous->commit();
					$success = $this->Rendezvous->WebrsaRendezvous->creePassageCommission( $this->request->data, $this->Session->read( 'Auth.User.id' ) ) && $success;
				}
                else if( $this->action == 'edit' && !empty( $rdv ) && Configure::read( 'Cg.departement' ) == 58  ) {
                    // On regarde le statut du RDV (si ce dernier est modifié)
                    // On regarde si le statut provoque toujours un passage en commission
                    $statutProvoquantPassage = $this->Rendezvous->Statutrdv->provoquePassageCommission( $this->request->data['Rendezvous']['statutrdv_id'] );

					// Si c'est le cas ET qu'un dossier COV existe, on le supprime dans la thématique et dans les dossiers COVs
                    // FIXME: faire la même chose pour les dossiers EPs ????
                    if( !empty( $rdv['Propoorientsocialecov58']['dossiercov58_id'] ) && empty( $statutProvoquantPassage ) )  {
                        $success = $this->Rendezvous->Propoorientsocialecov58->deleteAll( array( 'Propoorientsocialecov58.rendezvous_id' => $rdv_id ) ) && $success;
                        $success = $this->Rendezvous->Personne->Dossiercov58->delete( $rdv['Propoorientsocialecov58']['dossiercov58_id'] ) && $success;
                    }
                }

				// Création du référent si celui-ci est non présent
				if( Configure::read( 'Cg.departement' ) == 93 && $this->action === 'add' ) {
					if( $success && !empty( $this->request->data['Rendezvous']['referent_id'] ) ) {
						$personneReferentActuel = $this->Rendezvous->Referent->PersonneReferent->find(
							'first',
							array(
								'conditions' => array(
									'PersonneReferent.personne_id' => $this->request->data['Rendezvous']['personne_id'],
									'PersonneReferent.dfdesignation IS NULL',
								),
								'contain' => false
							)
						);

						if( empty( $personneReferentActuel ) ) {
							$success = $this->Rendezvous->Referent->PersonneReferent->referentParModele( $this->request->data, 'Rendezvous', 'daterdv' ) && $success;
						}
					}
				}

				if( $success ) {
					$this->Rendezvous->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'rendezvous', 'action' => 'index', $personne_id ) );
				}
				else {
					$this->Rendezvous->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else {
				if( $this->action == 'edit' ) {
					$this->request->data = $rdv;
					// Préparation des données pour la modification
					$this->request->data['Rendezvous']['referent_id'] = false === empty( $rdv['Rendezvous']['referent_id'] )
						? $rdv['Rendezvous']['structurereferente_id'].'_'.$rdv['Rendezvous']['referent_id']
						: null;
					$this->request->data['Rendezvous']['permanence_id'] = false === empty( $rdv['Rendezvous']['permanence_id'] )
						? $rdv['Rendezvous']['structurereferente_id'].'_'.$rdv['Rendezvous']['permanence_id']
						: null;
				}
				else {
					//Récupération de la structure référente liée à l'orientation
					$orientstruct = $this->Rendezvous->Structurereferente->Orientstruct->find(
							'first', array(
						'fields' => array(
							'Orientstruct.id',
							'Orientstruct.personne_id',
							'Orientstruct.structurereferente_id'
						),
						'conditions' => array(
							'Orientstruct.personne_id' => $personne_id,
							'Orientstruct.date_valid IS NOT NULL'
						),
						'contain' => array(
							'Structurereferente',
							'Referent'
						),
						'order' => array( 'Orientstruct.date_valid DESC' )
							)
					);

					if( !empty( $orientstruct ) ) {
						$this->request->data['Rendezvous']['structurereferente_id'] = $orientstruct['Orientstruct']['structurereferente_id'];
						if( !empty( $orientstruct['Referent']['id'] ) ){
							$this->request->data['Rendezvous']['referent_id'] = $orientstruct['Orientstruct']['structurereferente_id'].'_'.$orientstruct['Referent']['id'];

						}
					}

					// Recherche du dernier référent actif lié au parcours de l'allocataire
					$personneReferent = $this->Rendezvous->Personne->PersonneReferent->find(
						'first',
						array(
							'fields' => array( 'PersonneReferent.referent_id' ),
							'conditions' => array(
								'PersonneReferent.personne_id' => $personne_id,
								'PersonneReferent.id IN ( '.$this->Rendezvous->Personne->PersonneReferent->sqDerniere( 'Personne.id', false ).' )'
							),
							'contain' => array(
								'Personne'
							)
						)
					);
					// On récupère le dernier référent actif et on charge la liste déroulante avec sa valeur
					if( !empty($this->request->data) && !empty( $personneReferent ) ) {
						$this->request->data['Rendezvous']['referent_id'] = $this->request->data['Rendezvous']['structurereferente_id'].'_'.$personneReferent['PersonneReferent']['referent_id'];
					}
				}
			}

			$struct_id = Set::classicExtract( $this->request->data, "{$this->modelClass}.structurereferente_id" );
			$this->set( 'struct_id', $struct_id );

			$permanence_id = Set::classicExtract( $this->request->data, "{$this->modelClass}.permanence_id" );
			$permanence_id = preg_replace( '/^[0-9]+_([0-9]+)$/', '\1', $permanence_id );
			$this->set( 'permanence_id', $permanence_id );

			// Options
			$options = Hash::merge(
				$this->Rendezvous->enums(),
				array(
					'Rendezvous' => array(
						'structurereferente_id' => $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ), true ),
						'referent_id' => $this->InsertionsBeneficiaires->referents(),
						'permanence_id' => $this->Rendezvous->Permanence->listOptions(),
						'typerdv_id' => $this->Rendezvous->Typerdv->find( 'list', array( 'conditions' => array('Typerdv.actif_dossier' => true) ) ),
						'statutrdv_id' => $this->Rendezvous->Statutrdv->find( 'list', array('conditions' => array('actif' => 1)) ),
						'permanence_id' => $this->Rendezvous->Permanence->listOptions()
					)
				)
			);

			if( Configure::read( 'Rendezvous.useThematique' ) ) {
				$options['Thematiquerdv']['Thematiquerdv'] = $this->Rendezvous->Thematiquerdv->find (
					'list',
					array(
						'fields' => array (
							'Thematiquerdv.id',
							'Thematiquerdv.name',
							'Thematiquerdv.typerdv_id'
						),
						'conditions' => array (
							'actif' => 1
						)
					)
				);
			}

			// On complète les options avec les éléments désactivés le cas échéant
			if( false === empty( $this->request->data ) ) {
				$options['Rendezvous'] = $this->InsertionsBeneficiaires->completeOptions(
					$options['Rendezvous'],
					$this->request->data['Rendezvous'],
					array(
						'typesorients' => false,
						'structuresreferentes' => array(
							'optgroup' => true,
							'prefix' => false,
							'type' => InsertionsBeneficiairesComponent::TYPE_OPTGROUP
						)
					)
				);

				$options = $this->Rendezvous->Permanence->completeOptions(
					$options,
					$this->request->data,
					array(
						'Rendezvous.permanence_id' => array(
							'prefix' => 'Permanence.structurereferente_id'
						)
					)
				);
			}

			$this->set( compact( 'options', 'personne_id' ) );
			$this->set( 'urlmenu', '/rendezvous/index/'.$personne_id );
			$this->render( 'add_edit' );
		}

		/**
		 * Suppression du rendez-vous et du dossier d'EP lié si celui-ci n'est pas
		 * associé à un passage en commission d'EP
		 *
		 * @param integer $id L'id du rendez-vous que l'on souhaite supprimer
		 */
		public function delete( $id ) {
			$this->WebrsaAccesses->check($id);

			$dossier_id = $this->Rendezvous->dossierId( $id );
			$this->DossiersMenus->checkDossierMenu( array( 'id' => $dossier_id ) );

			$this->Jetons2->get( $dossier_id );

			$success = true;

			$this->Rendezvous->begin();

			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$dossierep = $this->Rendezvous->Sanctionrendezvousep58->find(
					'first',
					array(
						'fields' => array(
							'Sanctionrendezvousep58.id',
							'Sanctionrendezvousep58.dossierep_id'
						),
						'conditions' => array(
							'Sanctionrendezvousep58.rendezvous_id' => $id
						),
						'contain' => false
					)
				);

				if( !empty( $dossierep ) ) {
					$success = $this->Rendezvous->Sanctionrendezvousep58->delete( $dossierep['Sanctionrendezvousep58']['id'] ) && $success;
					$success = $this->Rendezvous->Sanctionrendezvousep58->Dossierep->delete( $dossierep['Sanctionrendezvousep58']['dossierep_id'] ) && $success;
				}
			}

			$success = $this->Rendezvous->delete( $id ) && $success;

			if( $success ) {
				$this->Rendezvous->commit();
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Rendezvous->rollback();
				$this->Flash->error( __( 'Delete->error' ) );
			}

			$this->Jetons2->release( $dossier_id );

			$this->redirect( $this->referer() );
		}

		/**
		 * Impression d'un rendez-vous.
		 *
		 * @param integer $rdv_id
		 * @return void
		 */
		public function impression( $rdv_id = null ) {
			$this->WebrsaAccesses->check($rdv_id);
			$personne_id = $this->Rendezvous->personneId( $rdv_id );
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $personne_id ) );

			$pdf = $this->Rendezvous->WebrsaRendezvous->getDefaultPdf( $rdv_id, $this->Session->read( 'Auth.User.id' ) );

			if( !empty( $pdf ) ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'rendezvous-%d-%s.pdf', $rdv_id, date( 'Y-m-d' ) ) );
			}
			else {
				$this->Flash->error( 'Impossible de générer le courrier de rendez-vous.' );
				$this->redirect(array(
					'action' => 'index',
					 $personne_id
					)
				);
			}
		}

	}
?>