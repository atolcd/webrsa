<?php
	/**
	 * Code source de la classe Relancesnonrespectssanctionseps93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	require_once  APPLIBS.'cmis.php' ;

	/**
	 * La classe Relancesnonrespectssanctionseps93Controller ...
	 *
	 * @package app.Controller
	 */
	class Relancesnonrespectssanctionseps93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Relancesnonrespectssanctionseps93';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes' => array( 'cohorte' ),
			'DossiersMenus',
			'Gedooo.Gedooo',
			'InsertionsBeneficiaires',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
					'cohorte' => array('filter' => 'Search'),
					'impressions',
				),
			),
			'WebrsaAccesses' => array(
				'mainModelName' => 'Nonrespectsanctionep93',
				'webrsaModelName' => 'WebrsaRelancenonrespectsanctionep93',
				'webrsaAccessName' => 'WebrsaAccessRelancesnonrespectssanctionseps93',
				'parentModelName' => 'Personne',
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
			'Default2',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Relancenonrespectsanctionep93',
			'Contratinsertion',
			'Dossier',
			'Dossierep',
			'Nonrespectsanctionep93',
			'Orientstruct',
			'Pdf',
			'WebrsaRelancenonrespectsanctionep93',
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
			'add' => 'create',
			'cohorte' => 'read',
			'exportcsv' => 'read',
			'impression' => 'read',
			'impression_cohorte' => 'read',
			'impressions' => 'read',
			'index' => 'read',
		);

		/**
		 *
		 */
		protected function _setOptions() {
			/// Mise en cache (session) de la liste des codes Insee pour les selects
			/// TODO: Une fonction ?
			/// TODO: Voir où l'utiliser ailleurs
			if( !$this->Session->check( 'Cache.mesCodesInsee' ) ) {
				if( Configure::read( 'Zonesegeographiques.CodesInsee' ) ) {
					$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
					$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );
					$listeCodesInseeLocalites = ClassRegistry::init('Zonegeographique')->listeCodesInseeLocalites( $mesCodesInsee, $this->Session->read( 'Auth.User.filtre_zone_geo' ) );
				}
				else {
					$listeCodesInseeLocalites = $this->Dossier->Foyer->Adressefoyer->Adresse->listeCodesInsee();
				}
				$this->Session->write( 'Cache.mesCodesInsee', $listeCodesInseeLocalites );
			}
			else {
				$listeCodesInseeLocalites = $this->Session->read( 'Cache.mesCodesInsee' );
			}

			$options = array(
				'Adresse' => array( 'numcom' => $listeCodesInseeLocalites ),
				'Serviceinstructeur' => array( 'id' => $this->Orientstruct->Serviceinstructeur->find( 'list' ) )
			);
			$options = Set::merge(
				$options,
				$this->Relancenonrespectsanctionep93->enums(),
				$this->Relancenonrespectsanctionep93->Nonrespectsanctionep93->enums(),
				$this->Relancenonrespectsanctionep93->Nonrespectsanctionep93->Dossierep->enums(),
				$this->Relancenonrespectsanctionep93->Nonrespectsanctionep93->Dossierep->Passagecommissionep->enums()
			);

			$this->set( compact( 'options' ) );
		}

		/**
		 *
		 * @param integer $personne_id
		 */
		public function index( $personne_id = null ) {
			$this->assert( is_numeric( $personne_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$erreurs = $this->Relancenonrespectsanctionep93->erreursPossibiliteAjout( $personne_id );

			// INFO: on fera une jointure spéciale sur Personne car l'on vient soit d'une orientation, soit d'un CER
			$joinOrientstruct = $this->Nonrespectsanctionep93->Orientstruct->join( 'Personne', array( 'type' => 'LEFT OUTER' ) );
			$joinContratinsertion = $this->Nonrespectsanctionep93->Contratinsertion->join( 'Personne', array( 'type' => 'LEFT OUTER' ) );

			$query = array(
				'fields' => array(
					'Dossier.matricule',
					'Personne.nom',
					'Personne.prenom',
					'Adresse.localite',
					'Orientstruct.id',
					'Orientstruct.date_valid',
					'Contratinsertion.id',
					'Contratinsertion.df_ci',
					'Relancenonrespectsanctionep93.id',
					'Relancenonrespectsanctionep93.numrelance',
					'Relancenonrespectsanctionep93.daterelance',
					'Pdf.id',
					'Nonrespectsanctionep93.origine',
					'( CASE WHEN "Orientstruct"."id" IS NOT NULL THEN \'Non contractualisation\' ELSE \'Non renouvellement\' END ) AS "Nonrespectsanctionep93__origine_label"',
					'( CASE WHEN "Orientstruct"."id" IS NOT NULL THEN "Orientstruct"."date_valid" ELSE "Contratinsertion"."df_ci" END ) AS "Nonrespectsanctionep93__date_pivot"',
					'DATE_PART( \'day\', CASE WHEN "Orientstruct"."id" IS NOT NULL THEN NOW() + interval \'12 hours\' - "Orientstruct"."date_valid" ELSE NOW() + interval \'12 hours\' - "Contratinsertion"."df_ci" END ) AS "Nonrespectsanctionep93__nb_jours"'
				),
				'conditions' => array(
					'Nonrespectsanctionep93.origine' => array( 'orientstruct', 'contratinsertion' ),
					'OR' => array(
						'Orientstruct.personne_id' => $personne_id,
						'Contratinsertion.personne_id' => $personne_id
					)
				),
				'joins' => array(
					$this->Nonrespectsanctionep93->join( 'Relancenonrespectsanctionep93', array( 'type' => 'INNER' ) ),
					$this->Nonrespectsanctionep93->join( 'Orientstruct', array( 'type' => 'LEFT OUTER' ) ),
					$this->Nonrespectsanctionep93->join( 'Contratinsertion', array( 'type' => 'LEFT OUTER' ) ),
					$this->Nonrespectsanctionep93->Relancenonrespectsanctionep93->join( 'Pdf', array( 'type' => 'LEFT OUTER' ) ),
					array(
						'table' => $joinOrientstruct['table'],
						'alias' => 'Personne',
						'type' => 'INNER',
						'conditions' => array(
							'OR' => array(
								$joinOrientstruct['conditions'],
								$joinContratinsertion['conditions']
							)
						)
					),
					$this->Nonrespectsanctionep93->Orientstruct->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Nonrespectsanctionep93->Orientstruct->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$this->Nonrespectsanctionep93->Orientstruct->Personne->Foyer->join(
						'Adressefoyer',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'Adressefoyer.id IN ( '.$this->Nonrespectsanctionep93->Orientstruct->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
							)
						)
					),
					$this->Nonrespectsanctionep93->Orientstruct->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) )
				),
				'order' => array( 'Relancenonrespectsanctionep93.daterelance DESC', 'Relancenonrespectsanctionep93.numrelance DESC' )
			);

			$this->Nonrespectsanctionep93->forceVirtualFields = true;
			$relances = $this->WebrsaAccesses->getIndexRecords( $personne_id, $query );

			$ajoutPossible = $this->WebrsaRelancenonrespectsanctionep93->ajoutPossible( $personne_id, $erreurs );
			$this->set( compact( 'relances', 'erreurs', 'personne', 'ajoutPossible', 'personne_id' ) );
		}

		/**
		 * Formulaire d'ajout de relances en cohorte, pour un premier passage.
		 */
		public function cohorte() {
			if( !empty( $this->request->data ) ) {
				$this->request->data = Hash::expand( $this->request->data );
				$search = $this->request->data['Search'];

				$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
				$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

				/// Enregistrement de la cohorte de relances
				if( isset( $this->request->data['Relancenonrespectsanctionep93'] ) ) {
					$data = $this->request->data['Relancenonrespectsanctionep93'];

					// On filtre les relances en attente, on récupère les ids des dossiers pour les jetons
					$dossiersIds = array();
					$newData = array();
					foreach( $data as $i => $relance ) {
						if( is_array( $relance ) ) { // INFO: sinon on prend en compte la clé sessionKey
							if( isset( $relance['dossier_id'] ) ) {
								$dossiersIds[] = $relance['dossier_id'];
							}

							if( isset( $relance['arelancer'] ) && $relance['arelancer'] == 'R' ) {
								$newData[$i] = $relance;
							}
						}
					}

					if( !empty( $newData ) ) {
						$this->Nonrespectsanctionep93->begin();

						// Relances non respect orientation
						$success = $this->Relancenonrespectsanctionep93->saveCohorte( $newData, $search );

						if( $success ) {
							unset( $this->request->data['Relancenonrespectsanctionep93'], $this->request->data['sessionKey'] );
							$this->Nonrespectsanctionep93->commit();
							$this->Flash->success( __( 'Save->success' ) );
							// On libère les jetons
							$this->Cohortes->release( $dossiersIds );

							$url = Set::merge( array( 'action' => $this->action ), Hash::flatten( $this->request->data ) );
							$this->redirect( $url );
						}
						else {
							$this->Nonrespectsanctionep93->rollback();
							$this->Flash->error( __( 'Save->error' ) );
						}
					}
					else { // On libère les jetons de toutes façons
						$this->Cohortes->release( $dossiersIds );
					}
				}

				/// Moteur de recherche
				$search = Hash::flatten( $search );
				$search = Hash::filter( (array)$search );
				unset( $search['Pagination.nombre_total'] );

				$search['limit'] = Configure::read( 'ResultatsParPage.nombre_par_defaut' );
				if (isset ($this->request->data['Search']['limit'])) {
					$search['limit'] = $this->request->data['Search']['limit'];
				}

				$progressivePaginate = !Hash::get( $this->request->data, 'Search.Pagination.nombre_total' );

				if( $this->request->data['Search']['Relance']['contrat'] == 0 ) {
					$this->paginate = array(
						'Orientstruct' => $this->Relancenonrespectsanctionep93->search(
							$mesCodesInsee,
							$this->Session->read( 'Auth.User.filtre_zone_geo' ),
							$search,
							$this->Cohortes->sqLocked( 'Dossier' )
						)
					);

					$results = $this->paginate( $this->Nonrespectsanctionep93->Orientstruct, array(), array(), $progressivePaginate );
				}
				else if( $this->request->data['Search']['Relance']['contrat'] == 1 ) {
					$this->paginate = array(
						'Contratinsertion' => $this->Relancenonrespectsanctionep93->search(
							$mesCodesInsee,
							$this->Session->read( 'Auth.User.filtre_zone_geo' ),
							$search,
							$this->Cohortes->sqLocked( 'Dossier' )
						)
					);

					$results = $this->paginate( $this->Nonrespectsanctionep93->Contratinsertion, array(), array(), $progressivePaginate );
				};

				if( !empty( $results ) ) {
					$dossiersIds = Hash::extract( $results, '{n}.Dossier.id' );
					$this->Cohortes->get( $dossiersIds );

					$results = $this->Relancenonrespectsanctionep93->prepareFormData( $results, $search );
				}
				$this->set( compact( 'results' ) );

				if( $this->Relancenonrespectsanctionep93->checkCompareError( Hash::expand( $search ) ) == true ) {
					$this->Flash->error( 'Vos critères de recherche entrent en contradiction avec les critères de base' );
				}
			}

			$this->_setOptions();
			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );
		}

		/**
		 * Formulaire d'ajout de relances en individuel, pour un premier passage.
		 *
		 * @param integer $personne_id
		 * @throws NotFoundException
		 * @throws InternalErrorException
		 */
		public function add( $personne_id ) {
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$dossier_id = Hash::get( $dossierMenu, 'Dossier.id' );

			// On s'assure que l'id passé en paramètre et le dossier lié existent bien
			if( empty( $personne_id ) || empty( $dossier_id ) ) {
				throw new NotFoundException();
			}

			$this->WebrsaAccesses->check( null, $personne_id );

			// Tentative d'acquisition du jeton sur le dossier
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$success = true;
				$this->Relancenonrespectsanctionep93->begin();

				$nonrespectsanctionep93 = array( 'Nonrespectsanctionep93' => $this->request->data['Nonrespectsanctionep93'] );
				$this->Relancenonrespectsanctionep93->Nonrespectsanctionep93->create( $nonrespectsanctionep93 );
				$success = $this->Relancenonrespectsanctionep93->Nonrespectsanctionep93->save( null, array( 'atomic' => false ) ) && $success;

				$relancenonrespectsanctionep93 = array( 'Relancenonrespectsanctionep93' => $this->request->data['Relancenonrespectsanctionep93'] );
				$relancenonrespectsanctionep93['Relancenonrespectsanctionep93']['nonrespectsanctionep93_id'] = $this->Nonrespectsanctionep93->id;
				$this->Relancenonrespectsanctionep93->create( $relancenonrespectsanctionep93 );
				$success = $this->Relancenonrespectsanctionep93->save( null, array( 'atomic' => false ) ) && $success;

				// Création du dossier d'EP pour la seconde relance
				if( Hash::get( $this->request->data, 'Relancenonrespectsanctionep93.numrelance' ) == 2 ) {
					$dossierep = array(
						'Dossierep' => array(
							'personne_id' => $personne_id,
							'themeep' => 'nonrespectssanctionseps93',
						),
					);
					$this->Relancenonrespectsanctionep93->Nonrespectsanctionep93->Dossierep->create( $dossierep );
					$success = $this->Relancenonrespectsanctionep93->Nonrespectsanctionep93->Dossierep->save( null, array( 'atomic' => false ) ) && $success;

					if( $success ) {
						$success = $this->Relancenonrespectsanctionep93->Nonrespectsanctionep93->updateAllUnBound(
							array(
								'"Nonrespectsanctionep93"."sortieprocedure"' => null,
								'"Nonrespectsanctionep93"."active"' => '\'0\'',
								'"Nonrespectsanctionep93"."dossierep_id"' => $this->Relancenonrespectsanctionep93->Nonrespectsanctionep93->Dossierep->id,
							),
							array( '"Nonrespectsanctionep93"."id"' => $this->Relancenonrespectsanctionep93->Nonrespectsanctionep93->id )
						) && $success;
					}
				}

				if( $success ) {
					$this->Relancenonrespectsanctionep93->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index', $personne_id ) );
				}
				else {
					$this->Relancenonrespectsanctionep93->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else {
				// On prépare les valeurs par défaut du formulaire; pour cela, on se sert des méthodes existant en cohortes
				$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
				$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

				$results = $this->Relancenonrespectsanctionep93->getRelance(
					$personne_id,
					$mesCodesInsee,
					$this->Session->read( 'Auth.User.filtre_zone_geo' ),
					$this->Cohortes->sqLocked( 'Dossier' ),
					$this->Session->read( 'Auth.user.id' )
				);
//debug($results);
				if( !empty( $results ) ) {
					$results = $this->Relancenonrespectsanctionep93->prepareFormData( $results );
					$this->request->data = $this->Relancenonrespectsanctionep93->prepareFormDataAdd( $results[0], $this->Session->read( 'Auth.User.id' ) );
				}
				else {
					throw new InternalErrorException();
				}
			}

			$this->set( compact( 'dossierMenu' ) );
			$this->_setOptions();
		}

		/**
		 *
		 */
		public function impressions() {
			if( !empty( $this->request->data ) ) {
				$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
				$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

				$search = $this->request->data;
				unset( $search['Search']['Pagination'] );

				$queryData = $this->Relancenonrespectsanctionep93->qdSearchRelances(
					$mesCodesInsee,
					$this->Session->read( 'Auth.User.filtre_zone_geo' ),
					$search
				);

				$queryData['limit'] = Configure::read( 'ResultatsParPage.nombre_par_defaut' );
				if (isset ($this->request->data['Search']['limit'])) {
					$queryData['limit'] = $this->request->data['Search']['limit'];
				}

				$this->Relancenonrespectsanctionep93->forceVirtualFields = true;

				$this->paginate = $queryData;
				$progressivePaginate = !Hash::get( $this->request->data, 'Search.Pagination.nombre_total' );
				$relances = $this->paginate( $this->Relancenonrespectsanctionep93, array(), array(), $progressivePaginate );

				$this->set( compact( 'relances' ) );
			}

			$this->_setOptions();
			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );
		}

		/**
		 *
		 */
		public function exportcsv() {
			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			$queryData = $this->Relancenonrespectsanctionep93->qdSearchRelances(
				$mesCodesInsee,
				$this->Session->read( 'Auth.User.filtre_zone_geo' ),
				Hash::expand( $this->request->params['named'], '__' )
			);

			$this->Relancenonrespectsanctionep93->forceVirtualFields = true;

			$relances = $this->Relancenonrespectsanctionep93->find( 'all', $queryData );

			$this->layout = '';
			$this->set( compact( 'relances' ) );
			$this->_setOptions();
		}

		/**
		 *
		 * @param integer $id
		 */
		public function impression( $id ) {
			$this->assert( is_numeric( $id ), 'invalidParameter' );

			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->Relancenonrespectsanctionep93->personneId( $id ) ) );

//			$this->WebrsaAccesses->check( $id ); // FIXME

			$this->Relancenonrespectsanctionep93->begin();

			$pdf = $this->Relancenonrespectsanctionep93->getStoredPdf( $id, 'dateimpression' );

			if( empty( $pdf ) ) {
				$this->Relancenonrespectsanctionep93->rollback();
				$this->cakeError( 'error404' );
			}
			else if( !empty( $pdf['Pdf']['document'] ) ) {
				$this->Relancenonrespectsanctionep93->commit();
				$this->layout = '';
				$this->Gedooo->sendPdfContentToClient( $pdf['Pdf']['document'], sprintf( "relance-%s.pdf", date( "Ymd-H\hi" ) ) );
			}
			else {
				$this->Relancenonrespectsanctionep93->rollback();
				$this->cakeError( 'error500' );
			}

		}

		/**
		 *
		 */
		public function impression_cohorte() {
			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			$queryData = $this->Relancenonrespectsanctionep93->qdSearchRelances(
				$mesCodesInsee,
				$this->Session->read( 'Auth.User.filtre_zone_geo' ),
				Hash::expand( $this->request->params['named'], '__' )
			);

			$queryData['fields'] = array(
				'Pdf.document',
				'Pdf.cmspath',
				'Relancenonrespectsanctionep93.id',
				'Relancenonrespectsanctionep93.dateimpression',
			);

			$this->Relancenonrespectsanctionep93->begin();

			$nErrors = 0;
			$contents = $this->Relancenonrespectsanctionep93->find( 'all', $queryData );
			foreach( $contents as $i => $content ) {
				if( empty( $content['Pdf']['document'] ) && !empty( $content['Pdf']['cmspath'] ) ) {
					$cmisPdf = Cmis::read( $content['Pdf']['cmspath'], true );
					if( !empty( $cmisPdf['content'] ) ) {
						$contents[$i]['Pdf']['document'] = $cmisPdf['content'];
					}
				}
				// Gestion des erreurs: si on n'a toujours pas le document
				if( empty( $contents[$i]['Pdf']['document'] ) ) {
					$nErrors++;
					unset( $contents[$i] );
				}
			}

			if( $nErrors > 0 ) {
				$this->Flash->error( "Erreur lors de l'impression en cohorte: {$nErrors} documents n'ont pas pu être imprimés. Abandon de l'impression de la cohorte. Demandez à votre administrateur d'exécuter la commande bash suivante: sudo -u www-data lib/Cake/Console/cake generationpdfs relancenonrespectsanctionep93" );
				$this->redirect( $this->referer() );
			}

			$ids = Set::extract( '/Relancenonrespectsanctionep93/id', $contents );
			$pdfs = Set::extract( '/Pdf/document', $contents );

			if( empty( $content['Relancenonrespectsanctionep93']['dateimpression'] ) ) {
				$this->Relancenonrespectsanctionep93->updateAllUnBound(
					array( 'Relancenonrespectsanctionep93.dateimpression' => date( "'Y-m-d'" ) ),
					array( '"Relancenonrespectsanctionep93"."id"' => $ids, '"Relancenonrespectsanctionep93"."dateimpression" IS NOT NULL' )
				);
			}

			$pdfs = $this->Gedooo->concatPdfs( $pdfs, 'Relancenonrespectsanctionep93' );

			if( !empty( $pdfs ) ) {
				$this->Relancenonrespectsanctionep93->commit();
				$this->layout = '';
				$this->Gedooo->sendPdfContentToClient( $pdfs, sprintf( "cohorterelances-%s.pdf", date( "Ymd-H\hi" ) ) );
			}
			else {
				$this->Relancenonrespectsanctionep93->rollback();
				$this->Flash->error( 'Erreur lors de l\'impression en cohorte.' );
				$this->redirect( $this->referer() );
			}
		}
	}
?>