<?php
	/**
	 * Code source de la classe DossiersController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe DossiersController ...
	 *
	 * @package app.Controller
	 */
	class DossiersController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Dossiers';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'DossiersMenus',
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
					'search',
				)
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Allocataires',
			'Csv',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Gestionanomaliebdd',
			'Search',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Dossier',
			'Informationpe',
			'Option',
			'Tableausuivipdv93',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'search' => 'Dossiers:index',
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'unlock',
		);
		
		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'edit' => 'update',
			'exportcsv' => 'read',
			'exportcsv1' => 'read',
			'index' => 'read',
			'menu' => 'read',
			'search' => 'read',
			'unlock' => 'read',
			'view' => 'read',
		);

		/**
		 * @return void
		 */
		protected function _setOptions() {
			$this->set( 'natpf', ClassRegistry::init('Detailcalculdroitrsa')->enum('natpf') );
			$this->set( 'qual', $this->Option->qual() );
			$this->set( 'decision_ci', ClassRegistry::init('Contratinsertion')->enum('decision_ci') );
			$this->set( 'etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa') );
			$this->set( 'natfingro', ClassRegistry::init('Grossesse')->enum('natfingro') );
			$this->set( 'rolepers', ClassRegistry::init('Prestation')->enum('rolepers') );
			$this->set( 'statudemrsa', $this->Dossier->enum( 'statudemrsa' ) );
			$this->set( 'moticlorsa', ClassRegistry::init('Situationdossierrsa')->enum('moticlorsa') );
			$this->set( 'typeserins', $this->Option->typeserins() );
			$this->set( 'toppersdrodevorsa', $this->Option->toppersdrodevorsa(true) );
			$this->set( 'typevoie', $this->Option->typevoie() );
			$this->set( 'sitfam', $this->Option->sitfam() );
			$this->set( 'act', ClassRegistry::init('Activite')->enum('act') );
			$this->set( 'categorie', ClassRegistry::init('Historiqueetatpe')->enum('code') );
			$this->set( 'sexe', $this->Option->sexe() );
			$this->set( 'anciennete_dispositif', $this->Tableausuivipdv93->WebrsaTableausuivipdv93->anciennetes_dispositif );

			$enums = $this->Dossier->Foyer->Personne->Dsp->enums();
			asort( $enums['Dsp']['natlog'] );
			$this->set( 'natlog', $enums['Dsp']['natlog'] );

			$this->set(
				'trancheage',
				array(
					'0_24' => '< 25',
					'25_30' => '25 - 30',
					'31_55' => '31 - 55',
					'56_65' => '56 - 65',
					'66_999' => '> 65',
				)
			);

			// à intégrer à la fonction view pour ne pas avoir d'énormes variables
			if( $this->action == 'view' ) {
				$this->set( 'numcontrat', (array)Hash::get( $this->Dossier->Foyer->Personne->Contratinsertion->enums(), 'Contratinsertion' ) );
				$this->set( 'enumcui', array_merge(
					$this->Dossier->Foyer->Personne->Cui->enums(),
					$this->Dossier->Foyer->Personne->Cui->Cui66->enums(),
					$this->Dossier->Foyer->Personne->Cui->Cui66->Decisioncui66->enums()
				));
				$this->set( 'etatpe', (array)Hash::get( $this->Informationpe->Historiqueetatpe->enums(), 'Historiqueetatpe' ) );
				$this->set( 'relance', (array)Hash::get( $this->Dossier->Foyer->Personne->Orientstruct->Nonrespectsanctionep93->enums(), 'Nonrespectsanctionep93' ) );
				$this->set( 'dossierep', (array)Hash::get( $this->Dossier->Foyer->Personne->Dossierep->enums(), 'Dossierep' ) );
				$this->set( 'options', $this->Dossier->Foyer->Personne->Orientstruct->enums() );
			}
			else if( $this->action == 'exportcsv' ) {
				$typesorient = $this->Dossier->Foyer->Personne->Orientstruct->Typeorient->find( 'list', array( 'fields' => array( 'id', 'lib_type_orient' ) ) );
				$this->set( 'typesorient', $typesorient );
			}
			else if( $this->action == 'index' ) {
				$this->set( 'typeservice', $this->Dossier->Foyer->Personne->Orientstruct->Serviceinstructeur->listOptions() );

				$referents = $this->Dossier->Foyer->Personne->PersonneReferent->Referent->find( 'list', array( 'order' => array( 'Referent.nom' ) ) );
				$this->set( compact( 'referents') );
				$this->set( 'exists', array( '1' => 'Oui', '0' => 'Non' ) );
			}
			else if( $this->action == 'edit' ) {
				$optionsDossier = array(
					'Dossier' => array(
						'statudemrsa' => $this->Dossier->enum( 'statudemrsa' ),
						'fonorgcedmut' => $this->Dossier->enum( 'fonorgcedmut' ),
						'fonorgprenmut' => $this->Dossier->enum( 'fonorgprenmut' )
					)
				);
				$this->set( 'optionsDossier', $optionsDossier );
			}
			$this->set( 'fonorg', array( 'CAF' => 'CAF', 'MSA' => 'MSA' ) );

			$this->set(
				'chooserolepers',
				array(
					'0' => 'Sans prestation',
					'1' => 'Demandeur ou Conjoint du RSA'
				)
			);

			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$this->set( 'etat_dossier_orientation', $this->Dossier->Foyer->Personne->enum( 'etat_dossier_orientation' ) );
			}
		}

		/**
		 * Les action index et exportcsv peuvent être consommatrices, donc on augmeta la mémoire
		 * maximale et le temps d'exécution maximal du script PHP.
		 *
		 * @deprecated since 3.0.00
		 *
		 * @return voir
		 */
		public function beforeFilter() {
			if( in_array( $this->action, array( 'index', 'exportcsv' ) ) ) {
				ini_set( 'max_execution_time', 0 );
				ini_set( 'memory_limit', '512M' );
			}
			parent::beforeFilter();
		}

		/**
		 * Moteur de recherche par dossier / allocataire
		 *
		 * @return void
		 */
		public function search() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesDossiers' );
			$Recherches->search();
		}

		/**
		 * Export du tableau de résultats du moteur de recherche par dossier /
		 * allocataire au format CSV.
		 *
		 * @return void
		 */
		public function exportcsv() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesDossiers' );
			$Recherches->exportcsv();
		}

		/**
		 * Moteur de recherche par dossier/allocataire
		 *
		 * @deprecated since 3.0.00
		 *
		 * @return void
		 */
		public function index() {
			if( !empty( $this->request->data ) ) {
				$paginate = $this->Dossier->search( $this->request->data );

				$paginate = $this->Gestionzonesgeos->qdConditions( $paginate );
				$paginate['conditions'][] = WebrsaPermissions::conditionsDossier();
				$paginate = $this->_qdAddFilters( $paginate );

				$paginate['fields'][] = $this->Jetons2->sqLocked( 'Dossier', 'locked' );

				$this->paginate = $paginate;
				$progressivePaginate = !Hash::get( $this->request->data, 'Pagination.nombre_total' );
				$dossiers = $this->paginate( 'Dossier', array(), array(), $progressivePaginate );

				$this->set( 'dossiers', $dossiers );
			}
			else {
				$filtresdefaut = Configure::read( "Filtresdefaut.{$this->name}_{$this->action}" );
				$this->request->data = Set::merge( $this->request->data, $filtresdefaut );
			}

			$this->Gestionzonesgeos->setCantonsIfConfigured();
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );

			$this->_setOptions();
		}

		/**
		 * Retourne les données permettant de peupler le menu d'un dossier.
		 * Doit être systématiquement utilisé via un requestAction.
		 *
		 * @return array
		 */
		public function menu() {
			$this->assert( isset( $this->request->params['requested'] ), 'error404' );

			$dossier = $this->Dossier->menu( $this->request->params, $this->Jetons2->qdLockParts() );

			return $dossier;
		}

		/**
		 * Visualisation du dossier (écran de synthèse).
		 *
		 * @param integer $id
		 * @return void
		 */
		public function view( $id = null ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $id ) ) );

			$details = array();
			$details = $this->Dossier->find(
				'first',
				array(
					'fields' => array(
						'Dossier.id',
						'Dossier.matricule',
						'Dossier.numdemrsa',
						'Dossier.statudemrsa',
						'Dossier.dtdemrsa',
						'Foyer.id',
						'Foyer.sitfam',
						'Situationdossierrsa.id',
						'Situationdossierrsa.dtclorsa',
						'Situationdossierrsa.etatdosrsa',
						'Situationdossierrsa.moticlorsa',
					),
					'contain' => array(
						'Foyer',
						'Situationdossierrsa'
					),
					'conditions' => array(
						'Dossier.id' => $id
					)
				)
			);

			// Dernière créance
			$tCreance = $this->Dossier->Foyer->Creance->find(
				'first',
				array(
					'fields' => array(
						'Creance.motiindu'
					),
					'contain' => false,
					'conditions' => array(
						'Creance.foyer_id' => $details['Foyer']['id']
					),
					'order' => array(
						'Creance.dtdercredcretrans DESC',
					),
				)
			);
			$details = Set::merge( $details, $tCreance );

			// Récupération des informations RSA Socle / Activité
			$tDetaildroitrsa = $this->Dossier->Detaildroitrsa->find(
				'first',
				array(
					'fields' => array(
						'Detaildroitrsa.id',
						'Detaildroitrsa.dossier_id',
					),
					'contain' => array(
						'Detailcalculdroitrsa' => array(
							'fields' => array(
								'Detailcalculdroitrsa.mtrsavers',
								'Detailcalculdroitrsa.dtderrsavers',
								'Detailcalculdroitrsa.natpf',
							),
						)
					),
					'conditions' => array(
						'Detaildroitrsa.dossier_id' => $id
					)
				)
			);
			$details = Set::merge( $details, $tDetaildroitrsa );

			// Dernier suivi d'instruction
			$tSuiviinstruction = $this->Dossier->Suiviinstruction->find(
				'first',
				array(
					'fields' => array(
						'Suiviinstruction.typeserins'
					),
					'conditions' => array(
						'Suiviinstruction.dossier_id' => $id
					),
					'contain' => false,
					'order' => array(
						'Suiviinstruction.date_etat_instruction DESC'
					)
				)
			);
			$details = Set::merge( $details, $tSuiviinstruction );

			// Dernière info financière
			$tInfofinanciere = $this->Dossier->Infofinanciere->find(
				'first',
				array(
					'fields' => array(
						'Infofinanciere.mtmoucompta'
					),
					'conditions' => array(
						'Infofinanciere.dossier_id' => $id,
						'Infofinanciere.type_allocation' => 'IndusConstates'
					),
					'contain' => false,
					'order' => array( 'Infofinanciere.moismoucompta DESC' )
				)
			);
			$details = Set::merge( $details, $tInfofinanciere );

			// Dernière adresse foyer
			$adresseFoyer = $this->Dossier->Foyer->Adressefoyer->find(
				'first',
				array(
					'fields' => array(
						'Adressefoyer.id',
						'Adressefoyer.dtemm',
					),
					'conditions' => array(
						'Adressefoyer.foyer_id' => $details['Foyer']['id'],
						'Adressefoyer.rgadr'    => '01'
					),
					'order' => array( 'Adressefoyer.dtemm DESC' ),
					'contain' => array(
						'Adresse' => array(
							'fields' => array(
								'Adresse.numvoie',
								'Adresse.libtypevoie',
								'Adresse.nomvoie',
								'Adresse.nomcom',
							)
						)
					)
				)
			);
			$details = Set::merge( $details, array( 'Adresse' => Hash::get($adresseFoyer, 'Adresse') ) );

			if ( Configure::read('Alerte.changement_adresse.enabled') ) {
				if ( empty($adresseFoyer) ) {
					$this->Session->setFlash(
						'Ce foyer ne possède actuellement aucune adresse.',
						'flash/error', array(), 'notice'
					);
				}
				elseif ( !Hash::get($adresseFoyer, 'Adressefoyer.dtemm') ) {
					$this->Session->setFlash(
						'La date d\'emménagement pour la dernière adresse n\'est pas renseignée.',
						'flash/notice', array(), 'notice'
					);
				}
				else {
					$date = new DateTime(Hash::get($adresseFoyer, 'Adressefoyer.dtemm'));
					$olddate = $date->format('d/m/Y');
					$date->add(new DateInterval('P'.Configure::read('Alerte.changement_adresse.delai').'M'));

					if ( strtotime(date('Y-m-d')) <= strtotime($date->format('Y-m-d')) ) {
						$this->Session->setFlash(
							sprintf('Attention, changement d\'adresse depuis le %s.', $olddate),
							'flash/error', array(), 'notice'
						);
					}
				}
			}

			// Personnes
			$query = array(
				'fields' => array(
					'Personne.id',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Personne.sexe',
					'Personne.dtnai',
					'Personne.nir',
                                        'Personne.numfixe',
                                        'Personne.numport',
                                        'Personne.email',
					'Dsp.id',
					'Activite.act',
					'Dossiercaf.ddratdos',
					'Dossiercaf.dfratdos',
					'Calculdroitrsa.toppersdrodevorsa',
					'Prestation.rolepers',
					'Grossesse.ddgro',
					'Grossesse.dfgro',
					'Grossesse.dtdeclgro',
					'Grossesse.natfingro'
				),
				'conditions' => array(
					'Personne.foyer_id' => $details['Foyer']['id'],
					'Prestation.natprest' => 'RSA',
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					'OR' => array(
						'Activite.id IS NULL',
						'Activite.id IN ('
							.$this->Dossier->Foyer->Personne->Activite->sq(
								array(
									'alias' => 'activites',
									'fields' => array( 'activites.id' ),
									'conditions' => array( 'activites.personne_id = Personne.id' ),
									'order' => array( 'activites.ddact DESC' ),
									'limit' => 1
								)
							)
						.')'
					)
				),
				'joins' => array(
					$this->Dossier->Foyer->Personne->join( 'Prestation' ),
					$this->Dossier->Foyer->Personne->join( 'Dossiercaf' ),
					$this->Dossier->Foyer->Personne->join( 'Dsp' ),
					$this->Dossier->Foyer->Personne->join( 'Calculdroitrsa' ),
					$this->Dossier->Foyer->Personne->join( 'Activite', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossier->Foyer->Personne->join( 'Grossesse', array( 'type' => 'LEFT OUTER' ) )
				),
				'contain' => false,
				'recursive' => -1
			);

			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$query = $this->Dossier->Foyer->Personne->WebrsaPersonne->completeQueryVfEtapeDossierOrientation58( $query, array() );
			}

			$personnesFoyer = $this->Dossier->Foyer->Personne->find( 'all', $query );

			$optionsep = $this->Dossier->Foyer->Personne->Dossierep->Passagecommissionep->enums();
			$roles = Set::extract( '{n}.Prestation.rolepers', $personnesFoyer );
			foreach( $roles as $index => $role ) {
				$tPersReferent = $this->Dossier->Foyer->Personne->PersonneReferent->find(
					'first',
					array(
						'fields' => array(
							'Referent.id',
							'Referent.qual',
							'Referent.nom',
							'Referent.prenom',
							'Structurereferente.lib_struc'
						),
						'contain' => array(
							'Referent',
                            'Structurereferente'
						),
						'conditions' => array( 'PersonneReferent.personne_id' => $personnesFoyer[$index]['Personne']['id'], 'PersonneReferent.dfdesignation IS NULL' ),
						'order' => array( 'PersonneReferent.dddesignation DESC' )
					)
				);
				$personnesFoyer[$index]['Referent'] = ( !empty( $tPersReferent ) ? $tPersReferent['Referent'] : array() );
				$personnesFoyer[$index]['Structurereferente'] = ( !empty( $tPersReferent ) ? $tPersReferent['Structurereferente'] : array() );

				$tContratinsertion = $this->Dossier->Foyer->Personne->Contratinsertion->find(
					'first',
					array(
						'fields' => array(
							'Contratinsertion.dd_ci',
							'Contratinsertion.df_ci',
							'Contratinsertion.num_contrat',
							'Contratinsertion.rg_ci',
							'Contratinsertion.decision_ci',
							'Contratinsertion.positioncer',
							'Contratinsertion.datevalidation_ci'
						),
						'conditions' => array( 'Contratinsertion.personne_id' => $personnesFoyer[$index]['Personne']['id'] ),
						'contain' => false,
						'order' => array( 'Contratinsertion.dd_ci DESC', 'Contratinsertion.rg_ci DESC', 'Contratinsertion.id DESC' )
					)
				);
				$personnesFoyer[$index]['Contratinsertion'] = ( !empty( $tContratinsertion ) ? $tContratinsertion['Contratinsertion'] : array() );

				if( Configure::read( 'Cg.departement' ) == 66 ) {
					$tCui = $this->Dossier->Foyer->Personne->Cui->find(
						'first',
						array(
							'fields' => array_merge(
								$this->Dossier->Foyer->Personne->Cui->fields(),
								$this->Dossier->Foyer->Personne->Cui->Cui66->fields(),
								$this->Dossier->Foyer->Personne->Cui->Cui66->Decisioncui66->fields()
							),
							'conditions' => array( 'Cui.personne_id' => $personnesFoyer[$index]['Personne']['id'] ),
							'joins' => array(
								$this->Dossier->Foyer->Personne->Cui->join( 'Cui66' ),
								$this->Dossier->Foyer->Personne->Cui->Cui66->join( 'Decisioncui66' ),
							),
							'contain' => false,
							'order' => array(
								'Cui.faitle DESC',
								'Cui.created DESC'
							)
						)
					);
				}
				else{
					$tCui = array();
				}
				$personnesFoyer[$index] = ( !empty( $tCui ) ? array_merge( $personnesFoyer[$index], $tCui ) : $personnesFoyer[$index] );

				// Dernière orientation
				$tOrientstruct = $this->Dossier->Foyer->Personne->Orientstruct->find(
					'first',
					array(
						'fields' => array(
								'Orientstruct.origine',
								'Orientstruct.date_valid',
								'Orientstruct.statut_orient',
								'Orientstruct.referent_id',
								'Orientstruct.rgorient',
								'Orientstruct.referentorientant_id',
								'Typeorient.lib_type_orient',
								'Structurereferente.lib_struc',
								$this->Dossier->Foyer->Personne->Orientstruct->Referentorientant->sqVirtualField( 'nom_complet' )

						),
						'joins' => array(
							$this->Dossier->Foyer->Personne->Orientstruct->join( 'Typeorient' ),
							$this->Dossier->Foyer->Personne->Orientstruct->join( 'Structurereferente' ),
							$this->Dossier->Foyer->Personne->Orientstruct->join( 'Referentorientant', array( 'type' => 'LEFT OUTER' ) )
						),
						'conditions' => array(
							'Orientstruct.personne_id' => $personnesFoyer[$index]['Personne']['id']
						),
						'order' => "Orientstruct.date_valid DESC",
						'contain' => false
					)
				);
				$personnesFoyer[$index]['Orientstruct']['derniere'] = $tOrientstruct;

				if( Configure::read( 'Cg.departement' ) == 66 ) {
					$tNonoriente66 = $this->Dossier->Foyer->Personne->Nonoriente66->find(
						'first',
						array(
							'fields' => array(
								'Nonoriente66.id'
							),
							'contain' => false,
							'conditions' => array(
								'Nonoriente66.personne_id' => $personnesFoyer[$index]['Personne']['id']
							),
							'order' => "Nonoriente66.id DESC",
						)
					);
					$personnesFoyer[$index]['Nonoriente66']['derniere'] = $tNonoriente66;
				}


				// Dernière relance effective
				$tRelance = $this->Dossier->Foyer->Personne->Contratinsertion->Nonrespectsanctionep93->Relancenonrespectsanctionep93->find(
					'first',
					array(
						'fields' => array(
							'Nonrespectsanctionep93.created',
							'Nonrespectsanctionep93.origine',
							'Nonrespectsanctionep93.rgpassage',
							'Relancenonrespectsanctionep93.daterelance',
							'Relancenonrespectsanctionep93.numrelance'
						),
						'contain' => false,
						'joins' => array(
							array(
								'table'      => 'nonrespectssanctionseps93',
								'alias'      => 'Nonrespectsanctionep93',
								'type'       => 'INNER',
								'foreignKey' => false,
								'conditions' => array( 'Nonrespectsanctionep93.id = Relancenonrespectsanctionep93.nonrespectsanctionep93_id' )
							),
							array(
								'table'      => 'orientsstructs',
								'alias'      => 'Orientstruct',
								'type'       => 'LEFT OUTER',
								'foreignKey' => false,
								'conditions' => array( 'Orientstruct.id = Nonrespectsanctionep93.orientstruct_id' )
							),
							array(
								'table'      => 'contratsinsertion',
								'alias'      => 'Contratinsertion',
								'type'       => 'LEFT OUTER',
								'foreignKey' => false,
								'conditions' => array( 'Contratinsertion.id = Nonrespectsanctionep93.contratinsertion_id' )
							),
							array(
								'table'      => 'personnes',
								'alias'      => 'Personne',
								'type'       => 'INNER',
								'foreignKey' => false,
								'conditions' => array(
									'OR' => array(
										array(
											'Contratinsertion.personne_id = Personne.id'
										),
										array(
											'Orientstruct.personne_id = Personne.id'
										)
									)
								)
							)
						),
						'conditions' => array(
							'OR' => array(
								array(
									'Nonrespectsanctionep93.orientstruct_id IN ( '.$this->Dossier->Foyer->Personne->Orientstruct->sq( array( 'fields' => array( 'Orientstruct.id' ), 'conditions' => array( 'Orientstruct.personne_id' => $personnesFoyer[$index]['Personne']['id'] ) ) ).' )'
								),
								array(
									'Nonrespectsanctionep93.contratinsertion_id IN ( '.$this->Dossier->Foyer->Personne->Contratinsertion->sq( array( 'fields' => array( 'Contratinsertion.id' ), 'conditions' => array( 'Contratinsertion.personne_id' => $personnesFoyer[$index]['Personne']['id'] ) ) ).' )'
								)
							)
						),
						'order' => 'Relancenonrespectsanctionep93.daterelance DESC'
					)
				);
				$personnesFoyer[$index]['Nonrespectsanctionep93']['derniere'] = $tRelance;

				// EP: dernier passage effectif (lié à un passagecommissionep)
				$tdossierEp = $this->Dossier->Foyer->Personne->Dossierep->find(
					'first',
					array(
						'fields' => array(
							'Dossierep.themeep',
							'Commissionep.dateseance',
							'Passagecommissionep.id',
							'Passagecommissionep.etatdossierep',
						),
						'joins' => array(
							array(
								'table'      => 'passagescommissionseps',
								'alias'      => 'Passagecommissionep',
								'type'       => 'INNER',
								'foreignKey' => false,
								'conditions' => array( 'Passagecommissionep.dossierep_id = Dossierep.id' )
							),
							array(
								'table'      => 'commissionseps',
								'alias'      => 'Commissionep',
								'type'       => 'INNER',
								'foreignKey' => false,
								'conditions' => array( 'Passagecommissionep.commissionep_id = Commissionep.id' )
							),
						),
						'conditions' => array(
							'Dossierep.actif' => '1',
							'Dossierep.personne_id' => $personnesFoyer[$index]['Personne']['id']
						),
						'order' => array(
							'Commissionep.dateseance DESC'
						),
						'contain' => false,
					)
				);

				$dateDerniereCommissionep = Set::classicExtract( $tdossierEp, 'Commissionep.dateseance' );
				$dateDuJour = date( 'Y-m-d' );

				//Si la date de la dernière commission est > à 1 an, on masque l'information d'EP (CG93)
				$displayingInfoEp = true;
				if( Configure::read( 'Cg.departement' ) == 93 ) {
					if( !empty( $dateDerniereCommissionep ) ) {
						$dateCommissionEPPlusUnAn = date( 'Y-m-d', strtotime( '+1 year', strtotime( $dateDerniereCommissionep ) ) );

						$dateduJourMoinsUnAn = date( 'Y-m-d', strtotime( '-1 year', strtotime( $dateDuJour ) ) );
						if( $dateDuJour > $dateCommissionEPPlusUnAn  ) {
							$displayingInfoEp = false;
						}
					}
				}
				$this->set( 'displayingInfoEp', $displayingInfoEp );


				$decisionEP = array();
				if( !empty( $tdossierEp ) ) {
					$themeEP = Set::classicExtract( $tdossierEp, 'Dossierep.themeep' );
					$modelTheme = Inflector::classify( Inflector::singularize( $themeEP ) );
					$modelDecision = 'Decision'.Inflector::singularize( $themeEP );

					if( !isset( $optionsep[$modelDecision] ) ) {
						$optionsep = Hash::merge(
							$optionsep,
							$this->Dossier->Foyer->Personne->Dossierep->Passagecommissionep->{$modelDecision}->enums()
						);
					}

					$qdDecisionEp = array(
						'conditions' => array(
							"{$modelDecision}.passagecommissionep_id" => $tdossierEp['Passagecommissionep']['id']
						),
						'order' => array( "{$modelDecision}.etape DESC" ),
						'contain' => false
					);

					if( ( Configure::read( 'Cg.departement' ) == 58 ) && in_array( $themeEP, array( 'sanctionseps58', 'sanctionsrendezvouseps58' ) ) ) {
						$qdDecisionEp['fields'] = array_merge(
							$this->Dossier->Foyer->Personne->Dossierep->Passagecommissionep->{$modelDecision}->fields(),
							$this->Dossier->Foyer->Personne->Dossierep->Passagecommissionep->fields(),
							$this->Dossier->Foyer->Personne->Dossierep->Passagecommissionep->Commissionep->fields(),
							$this->Dossier->Foyer->Personne->Dossierep->Passagecommissionep->{$modelDecision}->Listesanctionep58->fields(),
							$this->Dossier->Foyer->Personne->Dossierep->Passagecommissionep->{$modelDecision}->Autrelistesanctionep58->fields()
						);

						$qdDecisionEp['joins'] = array(
							$this->Dossier->Foyer->Personne->Dossierep->Passagecommissionep->{$modelDecision}->join( 'Passagecommissionep' ),
							$this->Dossier->Foyer->Personne->Dossierep->Passagecommissionep->join( 'Commissionep' ),
							$this->Dossier->Foyer->Personne->Dossierep->Passagecommissionep->{$modelDecision}->join( 'Listesanctionep58' ),
							$this->Dossier->Foyer->Personne->Dossierep->Passagecommissionep->{$modelDecision}->join( 'Autrelistesanctionep58' ),
						);
					}

					$decisionEP = $this->Dossier->Foyer->Personne->Dossierep->Passagecommissionep->{$modelDecision}->find(
						'first',
						$qdDecisionEp
					);

					if( ( Configure::read( 'Cg.departement' ) == 58 ) && in_array( $themeEP, array( 'sanctionseps58', 'sanctionsrendezvouseps58' ) ) ) {
						$sanctionseps58 = $this->Dossier->Foyer->Personne->Dossierep->Passagecommissionep->{$modelDecision}->suivisanctions58( $decisionEP, null );
						$decisionEP['Sanctionep58'] = $sanctionseps58;
					}
				}

				$personnesFoyer[$index]['Dossierep']['derniere'] = Set::merge( $tdossierEp, $decisionEP );



                // Informationsdu bilan de aprcours et des dossiers PCGs liés
                if( Configure::read( 'Cg.departement' ) == 66 ) {
                    $tBilanparcours66 = $this->Dossier->Foyer->Personne->Bilanparcours66->find(
                       'first',
                       array(
                           'contain' => array(
                               'Personne',
                               'Dossierpcg66'
                           ),
                           'conditions' => array( 'Bilanparcours66.personne_id' => $personnesFoyer[$index]['Personne']['id'] ),
                           'order' => array( 'Bilanparcours66.created DESC')
                       )
                   );
                   $personnesFoyer[$index]['Bilanparcours66']['dernier'] = $tBilanparcours66;
//    debug($details);
                    if( !empty( $tBilanparcours66 ) ){
                        $tDossierpcg66 = $this->Dossier->Foyer->Personne->Bilanparcours66->Dossierpcg66->find(
                            'first',
                            array(
                                'conditions' => array(
                                    'Dossierpcg66.foyer_id' => $details['Foyer']['id'],
                                    'Dossierpcg66.bilanparcours66_id' => $tBilanparcours66['Bilanparcours66']['id']
                                ),
                                'contain' => array(
                                    'Decisiondossierpcg66' => array(
                                        'order' => array( 'Decisiondossierpcg66.modified DESC', 'Decisiondossierpcg66.id DESC' ),
                                        'conditions' => array(
                                            'Decisiondossierpcg66.validationproposition' => 'O',
                                            'Decisiondossierpcg66.etatop' => 'transmis'
                                        ),
                                        'Decisionpdo'
                                    )
                                ),
                                'order' => array( 'Dossierpcg66.created DESC' )
                            )
                        );
                        $personnesFoyer[$index]['Dossierpcg66']['dernier'] = $tDossierpcg66;

                    }
                }


				// Utilisation des nouvelles tables de stockage des infos Pôle Emploi
				$tInfope = $this->Informationpe->derniereInformation($personnesFoyer[$index]);
				$personnesFoyer[$index]['Informationpe'] = ( !empty( $tInfope ) ? $tInfope['Historiqueetatpe'] : array() );

				//  Liste des anciens dossiers par demandeurs et conjoints
				$nir13 = trim( $personnesFoyer[$index]['Personne']['nir'] );
				$nir13 = ( empty( $nir13 ) ? null : substr( $nir13, 0, 13 ) );

                $fields = array(
                    'DISTINCT Dossier.id',
                    'Dossier.numdemrsa',
                    'Dossier.dtdemrsa',
                    'Situationdossierrsa.etatdosrsa'
                );
                if( Configure::read( 'Cg.departement' ) == 66 ) {
                    $fields = Hash::merge( $fields, '( '.$this->Dossier->Foyer->vfNbDossierPCG66( 'Foyer.id ').' ) AS "Foyer__nbdossierspcgs"' );
                }

				$autreNumdemrsaParAllocataire = $this->Dossier->find(
					'all',
					array(
						'fields' => $fields,
						'joins' => array(
							array(
								'table'      => 'foyers',
								'alias'      => 'Foyer',
								'type'       => 'INNER',
								'foreignKey' => false,
								'conditions' => array( 'Foyer.dossier_id = Dossier.id' )
							),
							array(
								'table'      => 'personnes',
								'alias'      => 'Personne',
								'type'       => 'INNER',
								'foreignKey' => false,
								'conditions' => array( 'Personne.foyer_id = Foyer.id' )
							),
							array(
								'table'      => 'prestations',
								'alias'      => 'Prestation',
								'type'       => 'INNER',
								'foreignKey' => false,
								'conditions' => array(
									'Personne.id = Prestation.personne_id',
									'Prestation.natprest = \'RSA\'',
									'Prestation.rolepers' => array( 'DEM', 'CJT' )
								)
							),
							array(
								'table'      => 'situationsdossiersrsa',
								'alias'      => 'Situationdossierrsa',
								'type'       => 'INNER',
								'foreignKey' => false,
								'conditions' => array( 'Situationdossierrsa.dossier_id = Dossier.id' )
							),
						),
						'conditions' => array(
							'OR' => array(
								array(
									'nir_correct13( Personne.nir )',
									'nir_correct13( \''.$nir13.'\'  )',
									'SUBSTRING( TRIM( BOTH \' \' FROM Personne.nir ) FROM 1 FOR 13 )' => $nir13,
									'Personne.dtnai' => $personnesFoyer[$index]['Personne']['dtnai']
								),
								array(
									'UPPER(Personne.nom)' => strtoupper( replace_accents( $personnesFoyer[$index]['Personne']['nom'] ) ),
									'UPPER(Personne.prenom)' => strtoupper( replace_accents( $personnesFoyer[$index]['Personne']['prenom'] ) ),
									'Personne.dtnai' => $personnesFoyer[$index]['Personne']['dtnai']
								)
							),
							'Dossier.id NOT' => $details['Dossier']['id']
						),
						'contain' => false,
						'order' => 'Dossier.id DESC',
						'recursive' => -1
					)
				);
                $personnesFoyer[$index]['Dossiermultiple'] = $autreNumdemrsaParAllocataire;

				//Fin Ajout Arnaud

				// Anciens dossiers de l'allocataire ?
				if( Configure::read( 'AncienAllocataire.enabled' ) ) {
					$personnesFoyer[$index]['AncienDossier'] = $this->Dossier->Foyer->Personne->WebrsaPersonne->getAnciensDossiers( $personnesFoyer[$index]['Personne']['id'] );
				}

				$details[$role] = $personnesFoyer[$index];


				// Calcul des Apre par années
				if( (integer)Configure::read( 'Cg.departement' ) === 66 ) {
					$Apre66 = ClassRegistry::init('Apre66');
					$montantApres = array();
					$begin = false;

					for ($i=2009; $i<=date('Y'); $i++) {
						$dateDebut = $i.'-01-01';
						$dateFin = $i.'-12-31';
						$montant = (integer)$Apre66->WebrsaApre66->getMontantAprePeriode($dateDebut, $dateFin, $personnesFoyer[$index]['Personne']['id']);

						if ( $montant > 0 || $begin ) {
							$begin = true;
							$montantApres[$i][$role] = $montant;
						}
					}

					if (empty($montantApres)) {
						$montantApres[date('Y')][$role] = 0;
					}
				}
			}
			$this->set( 'details', $details );

			$this->_setOptions();

			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$options = (array)Hash::get( $this->viewVars, 'options' );
				$options['Personne']['etat_dossier_orientation'] = $this->Dossier->Foyer->Personne->enum( 'etat_dossier_orientation' );
				$this->set( compact( 'options' ) );
			}

			$this->set( 'optionsep', $optionsep );

			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$this->set( 'montantApres', $montantApres );
				$this->render('view66');
			}
		}

		/**
		 * Modification du dossier.
		 *
		 * @param integer $id
		 * @return void
		 */
		public function edit( $id ){
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$dossier = $this->Dossier->find(
				'first',
				array(
					'conditions' => array(
						'Dossier.id' => $id
					),
					'contain' => false
				)
			);


			if( empty( $dossier ) ) {
				throw new NotFoundException();
			}

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $id ) ) );

			$this->Jetons2->get( $id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $id );
				$this->redirect( array( 'action' => 'view', $id ) );
			}

			if( !empty( $this->request->data ) ) {
				if( $this->Dossier->saveAll( $this->request->data, array( 'validate' => 'first', 'atomic' => true ) ) ) {
					$this->Jetons2->release( $id );
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'dossiers', 'action' => 'view', $id ) );
				}
				else{
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement.', 'flash/error' );
				}
			}
			else {
				$this->request->data = $dossier;
			}
			$this->_setOptions();
			$this->set( 'id', $id );
		}

		/**
		 * Export du tableau de résultats de recherche au format CSV.
		 *
		 * @deprecated since 3.0.00
		 *
		 * @return void
		 */
		public function exportcsv1() {
			$querydata = $this->Dossier->search( Hash::expand( $this->request->params['named'], '__' ) );

			$querydata = $this->Gestionzonesgeos->qdConditions( $querydata );
			$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();
			$querydata = $this->_qdAddFilters( $querydata );

			unset( $querydata['limit'] );

			$dossiers = $this->Dossier->find( 'all', $querydata );

			$this->layout = '';
			$this->set( compact( 'headers', 'dossiers' ) );
			$this->_setOptions();
		}

		/**
		 * Permet de supprimer le jeton du dossier pour l'utilisateur courant.
		 *
		 * @param integer $id L'id du dossier à déverrouiller.
		 */
		public function unlock( $id ) {
			$this->Jetons2->get( $id );
			$this->Jetons2->release( $id );
			$this->redirect( $this->referer() );
		}
	}
?>
