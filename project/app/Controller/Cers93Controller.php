<?php
	/**
	 * Code source de la classe Cers93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'DefaultUtility', 'Default.Utility' );

	/**
	 * La classe Cers93Controller permet la gestion des CER du CG 93 (hors workflow).
	 *
	 * @package app.Controller
	 */
	class Cers93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cers93';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Gedooo.Gedooo',
			'InsertionsBeneficiaires',
			'Jetons2',
			'WebrsaAccesses' => array(
				'mainModelName' => 'Contratinsertion',
				'webrsaModelName' => 'WebrsaCer93',
				'webrsaAccessName' => 'WebrsaAccessCers93',
				'parentModelName' => 'Personne',
			),
			'WebrsaAjaxInsertions' => array(
				'structurereferente_id' => 'Contratinsertion.structurereferente_id',
				'referent_id' => 'Contratinsertion.referent_id'
			)
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
			'Cer93',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Locale',
			'Romev3',
			'Webrsa',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cer93',
			'Catalogueromev3',
			'WebrsaCer93',
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
			'ajaxref',
			'ajaxstruct',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'ajaxref' => 'read',
			'ajaxstruct' => 'read',
			'cancel' => 'update',
			'delete' => 'delete',
			'edit' => 'update',
			'edit_apres_signature' => 'update',
			'impression' => 'read',
			'impressionDecision' => 'read',
			'index' => 'read',
			'signature' => 'update',
			'view' => 'read',
		);

		/**
		 * Ajax pour les coordonnées de la structure référente liée (CG 93).
		 *
		 * @param type $structurereferente_id
		 */
		public function ajaxstruct( $structurereferente_id = null ) {
			return $this->WebrsaAjaxInsertions->structurereferente( $structurereferente_id );
		}

		/**
		 * Ajax pour les coordonnées du référent (CG 58, 66, 93).
		 *
		 * @param integer $referent_id
		 */
		public function ajaxref( $referent_id = null ) {
			return $this->WebrsaAjaxInsertions->referent( $referent_id );
		}

		/**
		 * Pagination sur les <élément>s de la table.
		 *
		 * @param integer $personne_id L'id technique de l'allocataire auquel le CER est attaché.
		 * @return void
		 * @throws NotFoundException
		 */
		public function index( $personne_id = null ) {
			if( !$this->Cer93->Contratinsertion->Personne->exists( $personne_id ) ) {
				throw new NotFoundException();
			}

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$this->_setEntriesAncienDossier( $personne_id, 'Contratinsertion' );

			//------------------------------------------------------------------
			// Partie passage en EP
			//------------------------------------------------------------------
			// Signalements en EP
			$qdSignalementep93 = $this->WebrsaCer93->qdSignalementseps93( array( 'Dossierep.personne_id' => $personne_id ) );
			$signalementseps93 = $this->Cer93->Contratinsertion->Signalementep93->Dossierep->find( 'all', $qdSignalementep93 );
			$this->WebrsaAccesses->settings = array(
				'mainModelName' => 'Dossierep',
				'webrsaModelName' => 'WebrsaSignalementep',
				'webrsaAccessName' => 'WebrsaAccessSignalementseps',
				'parentModelName' => 'Personne',
			);
			$this->WebrsaAccesses->initialize( $this );
			$signalementseps93 = $this->WebrsaAccesses->getIndexRecords( $personne_id, $qdSignalementep93 );

			// CER complexes; il n'y a pas moyen de modifier ou de supprimer le dossier d'EP
			$qdContratcomplexeep93 = $this->WebrsaCer93->qdContratscomplexes( array( 'Dossierep.personne_id' => $personne_id ) );
			$contratscomplexeseps93 = $this->Cer93->Contratinsertion->Contratcomplexeep93->Dossierep->find( 'all', $qdContratcomplexeep93 );

			// L'allocataire peut-il passer en EP ?
			$erreursCandidatePassage = $this->Cer93->Contratinsertion->Signalementep93->Dossierep->getErreursCandidatePassage( $personne_id );

			//------------------------------------------------------------------
			// Partie CER
			//------------------------------------------------------------------
			$this->WebrsaAccesses->settings = $this->components['WebrsaAccesses'];
			$this->WebrsaAccesses->initialize( $this );

			$querydata = array(
				'fields' => array(
					$this->Cer93->Contratinsertion->Fichiermodule->sqNbFichiersLies( $this->Cer93->Contratinsertion, 'nb_fichiers_lies' ),
					'Cer93.positioncer',
					'Cer93.formeci',
					'Contratinsertion.id',
					'Contratinsertion.personne_id',
					'Cer93.id',
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci',
					'Contratinsertion.rg_ci',
					'Contratinsertion.decision_ci',
					'Contratinsertion.datedecision',
					'Contratinsertion.datevalidation_ci',
					'Contratinsertion.forme_ci',
				),
				'contain' => array(
					'Cer93' => array(
						'Histochoixcer93' => array(
							'fields' => array(
								'Histochoixcer93.isrejet'
							)
						)
					)
				),
				'conditions' => array(
					'Contratinsertion.personne_id' => $personne_id,
					'Cer93.id IS NOT NULL'
				),
				'order' => array( 'Contratinsertion.dd_ci DESC', 'Contratinsertion.rg_ci DESC' )
			);

			$results = $this->WebrsaAccesses->getIndexRecords( $personne_id,  $querydata );

			$options = array_merge(
				array(
					'Contratinsertion' => array(
						'decision_ci' => $this->Cer93->Contratinsertion->enum('decision_ci')
					)
				),
				$this->Cer93->enums()
			);

			$this->set(
				compact(
					array(
						'erreursCandidatePassage',
						'signalementseps93',
						'contratscomplexeseps93',
						'options',
						'personne_id',
					)
				)
			);
			$this->set( 'optionsdossierseps', $this->Cer93->Contratinsertion->Signalementep93->Dossierep->Passagecommissionep->enums() );
			$this->set( 'cers93', $results );
		}

		/**
		 * Formulaire d'ajout d'un élémént.
		 *
		 * @return void
		 */
		public function add( $personne_id = null ) {
			$args = func_get_args();
			call_user_func_array( array( $this, 'edit' ), $args );
		}

		/**
		 * Formulaire de modification d'un <élément>.
		 *
		 * @return void
		 * @throws NotFoundException
		 */
		public function edit( $id = null ) {
			if( $this->action == 'add' ) {
				$personne_id = $id;
				$this->WebrsaAccesses->check( null, $personne_id );
			}
			else {
				$this->WebrsaAccesses->check( $id );

				$this->Cer93->Contratinsertion->id = $id;
				$personne_id = $this->Cer93->Contratinsertion->field( 'personne_id' );
			}

			// Le dossier auquel appartient la personne
			$dossier_id = $this->Cer93->Contratinsertion->Personne->dossierId( $personne_id );

			// On s'assure que l'id passé en paramètre et le dossier lié existent bien
			if( empty( $personne_id ) || empty( $dossier_id ) ) {
				throw new NotFoundException();
			}

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			// Tentative d'acquisition du jeton sur le dossier
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			// Tentative de sauvegarde du formulaire
			if( !empty( $this->request->data ) ) {
				$this->Cer93->Contratinsertion->begin();

				if( $this->Cer93->WebrsaCer93->saveFormulaire( $this->request->data, $this->Session->read( 'Auth.User.type' ) ) ) {
					$this->Cer93->Contratinsertion->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index', $personne_id ) );
				}
				else {
					$this->Cer93->Contratinsertion->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			if( empty( $this->request->data ) ) {
				try {
					$this->request->data = $this->Cer93->WebrsaCer93->prepareFormDataAddEdit( $personne_id, ( ( $this->action == 'add' ) ? null : $id ), $this->Session->read( 'Auth.User.id' ) );
				}
				catch( Exception $e ) {
					$this->Jetons2->release( $dossier_id );
					$this->Flash->error( $e->getMessage() );
					$this->redirect( array( 'action' => 'index', $personne_id ) );
				}
			}

			$naturescontrats = $this->Cer93->Naturecontrat->find(
				'all',
				array(
					'order' => array( 'Naturecontrat.name ASC' )
				)
			);
			$naturecontratDuree = Set::extract( '/Naturecontrat[isduree=1]', $naturescontrats );
			$naturecontratDuree = Set::extract( '/Naturecontrat/id', $naturecontratDuree );
			$this->set( 'naturecontratDuree', $naturecontratDuree );

			// TODO: on peut certainement combiner avec la liste ci-dessous
			$listeSujets = $this->Cer93->Sujetcer93->find(
				'all',
				array(
					'contain' => array(
						'Soussujetcer93' => array(
							'order' => array( 'Soussujetcer93.sujetcer93_id ASC', 'Soussujetcer93.name ASC' ),
							'Valeurparsoussujetcer93' => array(
								'conditions' => array(
									'Valeurparsoussujetcer93.actif' => '1'
								),
								'order' => array( 'Valeurparsoussujetcer93.isautre DESC', 'Valeurparsoussujetcer93.name ASC' )
							)
						)
					),
					'order' => array( 'Sujetcer93.isautre DESC', 'Sujetcer93.name ASC' )
				)
			);

			$sujets_ids_valeurs_autres = array();
			$sujets_ids_soussujets_autres = array();
			foreach( $listeSujets as $sujetcer93 ) {
				if( !empty( $sujetcer93['Soussujetcer93'] ) ) {
					foreach( $sujetcer93['Soussujetcer93'] as $soussujetcer93 ) {
						if( $soussujetcer93['isautre'] == '1' ) {
							$sujets_ids_soussujets_autres[] = $sujetcer93['Sujetcer93']['id'];
						}
						if( !empty( $soussujetcer93['Valeurparsoussujetcer93'] ) ) {
							foreach( $soussujetcer93['Valeurparsoussujetcer93'] as $valeurparsousujetcer93 ) {
								if( $valeurparsousujetcer93['isautre'] == '1' ) {
									$sujets_ids_valeurs_autres[] = $sujetcer93['Sujetcer93']['id'];
								}
							}
						}
					}
				}
			}
			$sujets_ids_valeurs_autres = array_unique( $sujets_ids_valeurs_autres );
			$sujets_ids_soussujets_autres = array_unique( $sujets_ids_soussujets_autres );
			$this->set( compact( 'sujets_ids_valeurs_autres', 'sujets_ids_soussujets_autres' ) );

			// -----------------------------------------------------------------
			// TODO: à mettre dans les options, et dans une méthode
			// -----------------------------------------------------------------

			$sujetscers93 = $this->Cer93->Sujetcer93->find(
				'all',
				array(
					'conditions' => array(
						'Sujetcer93.actif' => '1'
					),
					'order' => array( 'Sujetcer93.isautre DESC', 'Sujetcer93.name ASC' )
				)
			);

			$soussujetscers93 = $this->Cer93->Sujetcer93->Soussujetcer93->find(
				'list',
				array(
					'fields' => array(
						'Soussujetcer93.id',
						'Soussujetcer93.name',
						'Soussujetcer93.sujetcer93_id',
					),
					'joins' => array(
						$this->Cer93->Sujetcer93->Soussujetcer93->join( 'Sujetcer93', array( 'type' => 'INNER' ) )
					),
					'conditions' => array(
						'Sujetcer93.actif' => '1',
						'Soussujetcer93.actif' => '1'
					),
					'order' => array( 'Soussujetcer93.sujetcer93_id ASC', 'Soussujetcer93.name ASC' )
				)
			);
			$this->set( 'soussujetscers93', $soussujetscers93 );


			$tmpValeursparsoussujetscers93 = $this->Cer93->Sujetcer93->Soussujetcer93->Valeurparsoussujetcer93->find(
				'all',
				array(
					'fields' => array(
						'( "Soussujetcer93"."id" || \'_\'|| "Valeurparsoussujetcer93"."id" ) AS "Valeurparsoussujetcer93__id"',
						'Valeurparsoussujetcer93.name',
						'Soussujetcer93.sujetcer93_id',
					),
					'joins' => array(
						$this->Cer93->Sujetcer93->Soussujetcer93->Valeurparsoussujetcer93->join( 'Soussujetcer93', array( 'type' => 'INNER' ) ),
						$this->Cer93->Sujetcer93->Soussujetcer93->join( 'Sujetcer93', array( 'type' => 'INNER' ) )
					),
					'conditions' => array(
						'Sujetcer93.actif' => '1',
						'Soussujetcer93.actif' => '1',
						'Valeurparsoussujetcer93.actif' => '1'
					),
					'order' => array( 'Valeurparsoussujetcer93.soussujetcer93_id ASC', 'Valeurparsoussujetcer93.name ASC' )
				)
			);

			$valeursparsoussujetscers93 = array();
			foreach( $tmpValeursparsoussujetscers93 as $tmp ) {
				if( !isset( $valeursparsoussujetscers93[$tmp['Soussujetcer93']['sujetcer93_id']] ) ) {
					$valeursparsoussujetscers93[$tmp['Soussujetcer93']['sujetcer93_id']] = array();
				}

				$valeursparsoussujetscers93[$tmp['Soussujetcer93']['sujetcer93_id']][$tmp['Valeurparsoussujetcer93']['id']] = $tmp['Valeurparsoussujetcer93']['name'];
			}

			// Liste des valeurs possédant un champ texte dans Valeursparsoussujetscers93
			$valeursAutre = $this->Cer93->Sujetcer93->Soussujetcer93->Valeurparsoussujetcer93->find(
				'all',
				array(
					'order' => array( 'Valeurparsoussujetcer93.isautre DESC', 'Valeurparsoussujetcer93.name ASC' )
				)
			);
			$autrevaleur_isautre_id = Hash::extract( $valeursAutre, '{n}.Valeurparsoussujetcer93[isautre=1]' );
			$autrevaleur_isautre_id = Hash::format( $autrevaleur_isautre_id, array( '{n}.soussujetcer93_id', '{n}.id' ), '%d_%d' );

			$this->set( 'autrevaleur_isautre_id', $autrevaleur_isautre_id );
			$this->set( 'valeursparsoussujetscers93', $valeursparsoussujetscers93 );

			// Liste des valeurs possédant un champ texte dans Soussujetscers93
			$valeursSoussujet = $this->Cer93->Sujetcer93->Soussujetcer93->find(
				'all',
				array(
					'order' => array( 'Soussujetcer93.isautre DESC', 'Soussujetcer93.name ASC' )
				)
			);
			$autresoussujet_isautre_id = Hash::extract( $valeursSoussujet, '{n}.Soussujetcer93[isautre=1].id' );
			$this->set( 'autresoussujet_isautre_id', $autresoussujet_isautre_id );

			// Quelles sont les valeurs de valeurparsoussujetcer93_id présentes
			// dans l'ancien formulaire et plus présentes dans les listes actuelles ?
			$query = array(
				'fields' => array_merge(
					$this->Cer93->Cer93Sujetcer93->fields(),
					$this->Cer93->Cer93Sujetcer93->Sujetcer93->fields(),
					$this->Cer93->Cer93Sujetcer93->Soussujetcer93->fields(),
					$this->Cer93->Cer93Sujetcer93->Valeurparsoussujetcer93->fields()
				),
				'joins' => array(
					$this->Cer93->Cer93Sujetcer93->join( 'Valeurparsoussujetcer93', array( 'type' => 'LEFT OUTER' ) ),
					$this->Cer93->Cer93Sujetcer93->join( 'Soussujetcer93', array( 'type' => 'LEFT OUTER' ) ),
					$this->Cer93->Cer93Sujetcer93->join( 'Sujetcer93', array( 'type' => 'LEFT OUTER' ) )
				),
				'contain' => false,
				'conditions' => array(
					'Cer93Sujetcer93.cer93_id' => Hash::get( $this->request->data, 'Cer93.id' ),
					'OR' => array(
						'Sujetcer93.actif' => '0',
						'Soussujetcer93.actif' => '0',
						'Valeurparsoussujetcer93.actif' => '0'
					)
				)
			);
			$sujetscers93enregistres = $this->Cer93->Cer93Sujetcer93->find( 'all', $query );
			$this->set( compact( 'sujetscers93enregistres' ) );

			// Options
			$options = array(
				'Contratinsertion' => array(
					'structurereferente_id' => $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ), true ),
					'referent_id' => $this->Cer93->Contratinsertion->Referent->WebrsaReferent->listOptions()
				),
				'Prestation' => array(
					'rolepers' => ClassRegistry::init('Prestation')->enum('rolepers')
				),
				'Personne' => array(
					'qual' => ClassRegistry::init( 'Option' )->qual()
				),
				'Serviceinstructeur' => array(
					'typeserins' => ClassRegistry::init( 'Option' )->typeserins()
				),
				'Expprocer93' => array(
					'metierexerce_id' => $this->Cer93->Expprocer93->Metierexerce->find( 'list' ),
					'secteuracti_id' => $this->Cer93->Expprocer93->Secteuracti->find( 'list' )
				),
				'Foyer' => array(
					'sitfam' => ClassRegistry::init( 'Option' )->sitfam()
				),
				'Dsp' => array(
					'natlog' => ClassRegistry::init('Dsp')->enum('natlog')
				),
				'Naturecontrat' => array(
					'naturecontrat_id' => Set::combine( $naturescontrats, '{n}.Naturecontrat.id', '{n}.Naturecontrat.name' )
				),
				'Sujetcer93' => array(
					'sujetcer93_id' => Set::combine( $sujetscers93, '{n}.Sujetcer93.id', '{n}.Sujetcer93.name' )
				),
				'Valeurparsoussujetcer93' => array(
					'soussujetcer93_id' => Set::combine( $valeursAutre, '{n}.Valeurparsoussujetcer93.id', '{n}.Valeurparsoussujetcer93.name' )
				),
				'dureehebdo' => array_range( '0', '39' ),
				'dureecdd' => ClassRegistry::init('Contratinsertion')->enum('duree_cdd')
			);

			$options = Set::merge(
				$this->Cer93->Contratinsertion->Personne->Dsp->enums(),
				$this->Cer93->enums(),
				$this->Cer93->Expprocer93->enums(),
				$options,
				$this->Catalogueromev3->dependantSelects()
			);

			$this->set( 'personne_id', $personne_id );
			$this->set( compact( 'options' ) );
			$this->set( 'urlmenu', '/cers93/index/'.$personne_id );
			$this->render( 'edit' );
		}

		/**
		 * Permet la modification d'un CER après sa signature.
		 *
		 * @param integer $id L'id dans la table contratsinsertion
		 * @throws NotFoundException
		 */
		public function edit_apres_signature( $id ) {
			$this->WebrsaAccesses->check( $id );

			$query = array(
				'fields' => array(
					'Contratinsertion.id',
					'Contratinsertion.personne_id',
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci',
					'Cer93.id',
					'Cer93.duree',
					'Cer93.positioncer'
				),
				'joins' => array(
					$this->Cer93->Contratinsertion->join( 'Cer93', array( 'type' => 'INNER' ) )
				),
				'contain' => false,
				'conditions' => array(
					'Contratinsertion.id' => $id
				)
			);
			$contratinsertion = $this->Cer93->Contratinsertion->find( 'first', $query );

			if( empty( $contratinsertion ) ) {
				throw new NotFoundException();
			}

			// Vérification des droits de l'utilisateur
			$personne_id = Hash::get( $contratinsertion, 'Contratinsertion.personne_id' );
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$dossier_id = Hash::get( $dossierMenu, 'Dossier.id' );

			// Début du traitement
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				return $this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$success = true;
				$errorMsg = __( 'Save->error' );
				$this->Cer93->Contratinsertion->begin();

				// Validation de la date de début de contrat : non vide et compris entre les dates configurés dans la variable de configuration Cer93.dateCER
				$dtDebutContrat = date_cakephp_to_sql(Hash::get( $this->request->data, 'Contratinsertion.dd_ci' ));
				$dtDebutMax = $this->Cer93->getDebutContratMax();
				if($dtDebutContrat == false) {
					$success = false;
					$errorMsg = __d('cer93', 'Cer93.Error.Champobligatoire');
				} else if( $dtDebutContrat < Configure::read('Cer93.dateCER.dtdebutMin') || $dtDebutContrat > $dtDebutMax ) {
					$success = false;
					$errorMsg =  sprintf(
						__d('cer93', 'Cer93.Error.Datedebutcontrat'),
						date('d/m/Y', strtotime(Configure::read('Cer93.dateCER.dtdebutMin'))),
						date('d/m/Y', strtotime($dtDebutMax) )
					);
				}

				$success = $success &&
					$this->Cer93->Contratinsertion->saveAll( $this->request->data, array( 'atomic' => false, 'validate' => 'only' ) )
					&& $this->Cer93->Contratinsertion->updateAllUnbound(
						array(
							'Contratinsertion.dd_ci' => "'". $dtDebutContrat ."'",
							'Contratinsertion.df_ci' => "'".date_cakephp_to_sql( Hash::get( $this->request->data, 'Contratinsertion.df_ci' ) )."'"
						),
						array( 'Contratinsertion.id' => Hash::get( $contratinsertion, 'Contratinsertion.id' ) )
					)
					&& $this->Cer93->updateAllUnbound(
						array( 'Cer93.duree' => Hash::get( $this->request->data, 'Cer93.duree' ) ),
						array( 'Cer93.id' => Hash::get( $contratinsertion, 'Cer93.id' ) )
					)
					&&  $this->Cer93->Contratinsertion->WebrsaContratinsertion->updateRangsContratsPersonne( $personne_id );

				if( $success ) {
					$this->Cer93->Contratinsertion->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					return $this->redirect( array( 'action' => 'index', $personne_id ) );
				}
				else {
					$this->Cer93->Contratinsertion->rollback();
					$this->Flash->error( $errorMsg );
				}
			}
			else {
				$this->request->data = $contratinsertion;
			}

			$contratinsertion = $this->Cer93->WebrsaCer93->dataView( $id );
			$options = $this->WebrsaCer93->optionsView();
			$urlmenu = "/cers93/index/{$personne_id}";

			$this->set( compact( 'dossierMenu', 'options', 'urlmenu', 'contratinsertion' ) );
		}

		/**
		 * Fonction permettant de saisir la date de signature du CER.
		 * Le statut du CER est également mis à jour à la valeur "signé"
		 *
		 * @param integer $id du contratinsertion en question
		 * @return void
		 */
		public function signature( $id ) {
			$this->WebrsaAccesses->check( $id );

			// On s'assure que l'id passé en paramètre existe bien
			if( empty( $id ) ) {
				throw new NotFoundException();
			}

			$this->Cer93->Contratinsertion->id = $id;
			$personne_id = $this->Cer93->Contratinsertion->field( 'personne_id' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			// Le dossier auquel appartient la personne
			$dossier_id = $this->Cer93->Contratinsertion->Personne->dossierId( $personne_id );

			// On s'assure que l'id passé en paramètre et le dossier lié existent bien
			if( empty( $personne_id ) || empty( $dossier_id ) ) {
				throw new NotFoundException();
			}

			// Tentative d'acquisition du jeton sur le dossier
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Cer93->begin();
				$saved = $this->Cer93->save( $this->request->data , array( 'atomic' => false ) );
				if( $saved ) {
					$this->Cer93->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index', $personne_id ) );
				}
				else {
					$this->Cer93->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			if( empty( $this->request->data ) ) {
				$this->request->data = $this->Cer93->WebrsaCer93->prepareFormDataAddEdit( $personne_id, $id, $this->Session->read( 'Auth.User.id' ) );
			}

			$this->set( 'personne_id', $personne_id );
			$this->set( 'urlmenu', '/cers93/index/'.$personne_id );
		}

		/**
		 * Imprime un CER 93.
		 * INFO: http://localhost/webrsa/trunk/cers93/impression/44327
		 *
		 * @param integer $contratinsertion_id
		 * @return void
		 */
		public function impression( $contratinsertion_id = null ) {
			$this->WebrsaAccesses->check( $contratinsertion_id );

			$personne_id = $this->Cer93->Contratinsertion->personneId( $contratinsertion_id );
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $personne_id ) );

			$pdf = $this->Cer93->getDefaultPdf( $contratinsertion_id, $this->Session->read( 'Auth.User.id' ) );

			if( !empty( $pdf ) ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, "contratinsertion_{$contratinsertion_id}_nouveau.pdf" );
			}
			else {
				$this->Flash->error( 'Impossible de générer le courrier de contrat d\'engagement réciproque.' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Visualisation du CER 93.
		 *
		 * @param integer $contratinsertion_id
		 * @return void
		 */
		public function view( $id ) {
			$this->WebrsaAccesses->check( $id );

			$this->Cer93->Contratinsertion->id = $id;
			$personne_id = $this->Cer93->Contratinsertion->field( 'personne_id' );
			$this->set( 'personne_id', $personne_id );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$this->set( 'options', $this->WebrsaCer93->optionsView() );
			$this->set( 'contratinsertion', $this->Cer93->WebrsaCer93->dataView( $id ) );
			$this->set( 'urlmenu', '/cers93/index/'.$personne_id );
		}

		/**
		 * Suppression d'un CER 93.
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$this->WebrsaAccesses->check( $id );

			$personne_id = $this->Cer93->Contratinsertion->personneId( $id );
			$dossier_id = $this->Cer93->Contratinsertion->dossierId( $id );
			$this->DossiersMenus->checkDossierMenu( array( 'id' => $dossier_id ) );

			$this->Jetons2->get( $dossier_id );

			$this->Cer93->Contratinsertion->begin();
			$success = $this->Cer93->Contratinsertion->Actioninsertion->deleteAll( array( 'Actioninsertion.contratinsertion_id' => $id ) );
			$success = $this->Cer93->Contratinsertion->delete( $id ) && $success;

			if( $success ) {
				$this->Cer93->Contratinsertion->commit();
				$this->Jetons2->release( $dossier_id );
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Cer93->Contratinsertion->rollback();
				$this->Flash->error( __( 'Delete->error' ) );
			}

			$this->redirect( $this->referer() );
		}

		/**
		 * Imprime une décision sur le CER 93.
		 * INFO: http://localhost/webrsa/trunk/cers93/printdecision/44327
		 *
		 * @param integer $contratinsertion_id
		 * @return void
		 */
		public function impressionDecision( $contratinsertion_id = null ) {
			$this->WebrsaAccesses->check( $contratinsertion_id );

			$personne_id = $this->Cer93->Contratinsertion->personneId( $contratinsertion_id );
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $personne_id ) );

			$pdf = $this->Cer93->getDecisionPdf( $contratinsertion_id, $this->Session->read( 'Auth.User.id' ) );

			if( !empty( $pdf ) ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, "contratinsertion_{$contratinsertion_id}.pdf" );
			}
			else {
				$this->Flash->error( 'Impossible de générer le courrier.' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Permet d'annuler un CER.
		 *
		 * @param integer $id L'id dans la table contratsinsertion
		 * @throws NotFoundException
		 */
		public function cancel( $id ) {
			$this->WebrsaAccesses->check( $id );

			$query = array(
				'fields' => array(
					'Contratinsertion.id',
					'Contratinsertion.personne_id',
					'Contratinsertion.motifannulation',
					'Cer93.id',
					'Cer93.date_annulation',
					'Cer93.annulateur_id'
				),
				'joins' => array(
					$this->Cer93->Contratinsertion->join( 'Cer93', array( 'type' => 'INNER' ) )
				),
				'contain' => false,
				'conditions' => array(
					'Contratinsertion.id' => $id
				)
			);
			$contratinsertion = $this->Cer93->Contratinsertion->find( 'first', $query );

			if( empty( $contratinsertion ) ) {
				throw new NotFoundException();
			}

			$personne_id = Hash::get( $contratinsertion, 'Contratinsertion.personne_id' );
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$dossier_id = Hash::get( $dossierMenu, 'Dossier.id' );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				return $this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Cer93->Contratinsertion->begin();

				// Modification des règles de validation
				$this->Cer93->validate['date_annulation'] = array(
					NOT_BLANK_RULE_NAME => array(
						'rule' => array( NOT_BLANK_RULE_NAME )
					),
					'date' => array(
						'rule' => array( 'date' )
					)
				);
				$this->Cer93->Contratinsertion->validate['motifannulation'][NOT_BLANK_RULE_NAME] = array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				);

				$success = $this->Cer93->Contratinsertion->saveAll( $this->request->data, array( 'atomic' => false, 'validate' => 'only' ) )
					&& $this->Cer93->Contratinsertion->updateAllUnbound(
						array(
							'Contratinsertion.rg_ci' => null,
							'Contratinsertion.decision_ci' => "'A'",
							'Contratinsertion.datevalidation_ci' => null,
							'Contratinsertion.motifannulation' => "'".Hash::get( $this->request->data, 'Contratinsertion.motifannulation' )."'"
						),
						array( 'Contratinsertion.id' => Hash::get( $contratinsertion, 'Contratinsertion.id' ) )
					)
					&& $this->Cer93->updateAllUnbound(
						array(
							'Cer93.positioncer' => "'99annule'",
							'Cer93.date_annulation' => "'".date_cakephp_to_sql( Hash::get( $this->request->data, 'Cer93.date_annulation' ) )."'",
							'Cer93.annulateur_id' => $this->Session->read( 'Auth.User.id' )
						),
						array( 'Cer93.id' => Hash::get( $contratinsertion, 'Cer93.id' ) )
					)
					&& $this->Cer93->Contratinsertion->WebrsaContratinsertion->updateRangsContratsPersonne( $personne_id );

				if( $success ) {
					$this->Cer93->Contratinsertion->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					return $this->redirect( array( 'action' => 'index', $personne_id ) );
				}
				else {
					$this->Cer93->Contratinsertion->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else {
				// Préparation des données pour le formulaire
				if( Hash::get( $contratinsertion, 'Cer93.date_annulation' ) === null ) {
					$contratinsertion['Cer93']['date_annulation'] = date( 'Y-m-d' );
				}
				// Affectation des données au formulaire
				$this->request->data = $contratinsertion;
			}

			$contratinsertion = $this->Cer93->WebrsaCer93->dataView( $id );
			$options = $this->WebrsaCer93->optionsView();
			$urlmenu = "/cers93/index/{$personne_id}";

			$this->set( compact( 'dossierMenu', 'options', 'urlmenu', 'contratinsertion' ) );
		}
	}
?>