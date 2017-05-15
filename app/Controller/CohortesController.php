<?php
	/**
	 * Fichier source de la classe CohortesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( APPLIBS.'cmis.php' );

	/**
	 * La classe CohortesController permet de traiter les orientations en cohorte (CG 66 et 93).
	 *
	 * @deprecated since 3.0.00 (TODO: orientees)
	 *
	 * @package app.Controller
	 */
	class CohortesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cohortes';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes'  => array(
				'nouvelles',
				'enattente',
			),
			'Gedooo.Gedooo',
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Search.SearchPrg' => array(
				'actions' => 'orientees',
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
			'Locale',
			'Search',
			'Xpaginator',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cohorte',
			'Option',
			'Personne',
			'Zonegeographique',
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
			'cohortegedooo' => 'update',
			'enattente' => 'read',
			'nouvelles' => 'read',
			'orientees' => 'read',
		);

		public $paginate = array( 'limit' => 20 );

		/**
		 *
		 */
		public function beforeFilter() {
			ini_set('max_execution_time', 0);
			ini_set('memory_limit', '1024M');
			parent::beforeFilter();

			if( in_array( $this->action, array( 'orientees'/*, 'exportcsv'*/ ) ) ) {
				$this->set( 'options', $this->Personne->Orientstruct->enums() );
			}

			$this->set( 'rolepers', ClassRegistry::init('Prestation')->enum('rolepers') );

			$this->set( 'toppersdrodevorsa', $this->Option->toppersdrodevorsa( true ) );

			$etats = Configure::read( 'Situationdossierrsa.etatdosrsa.ouvert' );
			$this->set('etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa', array('filter' =>  $etats)));

			$hasDsp = array( 'O' => 'Oui', 'N' => 'Non' );
			$this->set( 'hasDsp', $hasDsp );

			$natpfsSocle = Configure::read( 'Detailcalculdroitrsa.natpf.socle' );
			$this->set('natpf', ClassRegistry::init('Detailcalculdroitrsa')->enum('natpf', array('filter' => $natpfsSocle)));
		}

		/**
		 *
		 */
		public function _setOptions() {
			$typesOrient = $this->Personne->Orientstruct->Typeorient->find(
				'list',
				array(
					'fields' => array(
						'Typeorient.id',
						'Typeorient.lib_type_orient'
					),
					'conditions' => array(
						'Typeorient.actif' => 'O'
					),
					'order' => 'Typeorient.lib_type_orient ASC'
				)
			);
			$this->set( 'typesOrient', $typesOrient );
			$this->set( 'structuresReferentes', $this->Personne->Orientstruct->Structurereferente->list1Options() );
			$this->set( 'oridemrsa', ClassRegistry::init('Detaildroitrsa')->enum('oridemrsa') );
			$this->set( 'typeserins', $this->Option->typeserins() );
			$this->set( 'printed', $this->Option->printed() );
			$this->set( 'structuresAutomatiques', $this->Cohorte->structuresAutomatiques() );
			if( Configure::read( 'CG.cantons' ) ) {
				$this->set( 'cantons', $this->Zonegeographique->Canton->selectList() );
			}

			$this->set(
				'modeles',
				$this->Personne->Orientstruct->Typeorient->find(
					'list',
					array(
						'fields' => array( 'lib_type_orient' ),
						'conditions' => array(
							'Typeorient.parentid IS NULL', 'Typeorient.actif' => 'O' )
					)
				)
			);

			if( in_array( $this->action, array( 'orientees'/*, 'exportcsv', 'statistiques'*/ ) ) ) {
				$this->set( 'options', $this->Personne->Orientstruct->enums() );
			}
			$this->set( 'moticlorsa', ClassRegistry::init('Situationdossierrsa')->enum('moticlorsa') );
		}

		/**
		 *
		 */
		public function nouvelles() {
			$this->Gedooo->check( false, true );
			$this->_index( 'Non orienté' );
		}

		/**
		 *
		 */
		public function orientees() {
			$this->_index( 'Orienté' );
		}

		/**
		 *
		 */
		public function enattente() {
			$this->Gedooo->check( false, true );
			$this->_index( 'En attente' );
		}

		/**
		 *
		 */
		/*public function preconisationscalculables() {
			$this->Gedooo->check( false, true );
			$this->_index( 'Calculables' );
		}*/

		/**
		 *
		 */
		/*public function preconisationsnoncalculables() {
			$this->Gedooo->check( false, true );
			$this->_index( 'Non calculables' );
		}*/

		/**
		 *
		 * @param string $statutOrientation
		 */
		protected function _index( $statutOrientation = null ) {
			$this->assert( !empty( $statutOrientation ), 'invalidParameter' );

			if( !empty( $this->request->data ) ) {
				// -----------------------------------------------------------------
				// Formulaire de cohorte
				// -----------------------------------------------------------------
				if( !empty( $this->request->data['Orientstruct'] ) ) {
					$dossiers_ids = Set::extract(  '/dossier_id', $this->request->data['Orientstruct'] );
					$this->Cohortes->get( $dossiers_ids );

					// Sauvegarde de l'utilisateur orientant
					foreach( array_keys( $this->request->data['Orientstruct'] ) as $key ) {
						if( $this->request->data['Orientstruct'][$key]['statut_orient'] == 'Orienté' ) {
							$this->request->data['Orientstruct'][$key]['user_id'] = $this->Session->read( 'Auth.User.id' );
							$this->request->data['Orientstruct'][$key]['structurereferente_id'] = preg_replace( '/^[0-9]+_([0-9]+)$/', '\1', $this->request->data['Orientstruct'][$key]['structurereferente_id'] );
							$this->request->data['Orientstruct'][$key]['date_valid'] = date( 'Y-m-d' );
						}
						else {
							$this->request->data['Orientstruct'][$key]['user_id'] = null;
							$this->request->data['Orientstruct'][$key]['origine'] = null;
							if( $this->request->data['Orientstruct'][$key]['statut_orient'] == 'Non orienté' ) {
								$this->request->data['Orientstruct'][$key]['date_propo'] = date( 'Y-m-d' );
							}
						}
					}

					$valid = $this->Personne->Orientstruct->saveAll( $this->request->data['Orientstruct'], array( 'validate' => 'only', 'atomic' => false ) );
					$valid = ( count( $this->Personne->Orientstruct->validationErrors ) == 0 );

					if( $valid ) {
						$this->Personne->Foyer->Dossier->begin();
						$saved = $this->Personne->Orientstruct->saveAll( $this->request->data['Orientstruct'], array( 'validate' => 'first', 'atomic' => false ) );

						if( $saved ) {
							$this->Personne->Foyer->Dossier->commit();
							$this->Cohortes->release( $dossiers_ids );

							$this->request->data['Orientstruct'] = array();
						}
						else {
							$this->Personne->Foyer->Dossier->rollback();
							$this->Session->setFlash( 'Erreur lors de l\'enregistrement.', 'flash/error' );
						}
					}
				}

				// Nettoyage, formattage et envoi du filtre à la vue pour en faire des champs cachés du formulaire du bas.
				$tmpFiltre = $this->request->data;
				unset( $tmpFiltre['Orientstruct'] );
				$filtre = array();
				foreach( $tmpFiltre as $modelName => $modelValues ) {
					if( is_array( $modelValues ) ) {
						foreach( $modelValues as $fieldName => $values ) {
							$filtre["{$modelName}.{$fieldName}"] = $values;
						}
					}
				}
				$this->set( compact( 'filtre' ) );

				// -----------------------------------------------------------------
				// Filtre
				// -----------------------------------------------------------------
				if( isset( $this->request->data['Filtre'] ) ) {
					$progressivePaginate = !Hash::get( $this->request->data, 'Filtre.paginationNombreTotal' );

					$filtre = $this->request->data;
					if( Configure::read( 'Cg.departement' ) == 66 && empty( $filtre['Situationdossierrsa']['etatdosrsa_choice'] ) ) {
						$filtre['Situationdossierrsa']['etatdosrsa'] = Configure::read( 'Situationdossierrsa.etatdosrsa.ouvert' );
					}
					unset( $filtre['Filtre']['actif'] );

					$queryData = $this->Cohorte->search(
						$statutOrientation,
						(array)$this->Session->read( 'Auth.Zonegeographique' ),
						$this->Session->read( 'Auth.User.filtre_zone_geo' ),
						$filtre,
						( ( $statutOrientation == 'Orienté' ) ? false : $this->Cohortes->sqLocked() )
					);
					$queryData['conditions'][] = WebrsaPermissions::conditionsDossier();

					if( $statutOrientation == 'Orienté' ) {
						$queryData['limit'] = 10;
						$this->paginate = array( 'Personne' => $queryData );
						$cohorte = $this->paginate( $this->Personne, array(), array(), $progressivePaginate );

						$this->set( compact( 'cohorte' ) );
					}
					else {
						$queryData['limit'] = 10;

						$this->paginate = array( 'Personne' => $queryData );
						$cohorte = $this->paginate( $this->Personne, array(), array(), $progressivePaginate );

						$dossiers_ids = Set::extract(  '/Dossier/id', $cohorte );
						$this->Cohortes->get( $dossiers_ids );

						$this->set( compact( 'cohorte' ) );
					}
				}
			}
			else {
				// Valeurs par défaut des filtres
				$progressivePaginate = SearchProgressivePagination::enabled( $this->name, $this->action );
				if( !is_null( $progressivePaginate ) ) {
					$this->request->data['Filtre']['paginationNombreTotal'] = !$progressivePaginate;
				}
				$filtresdefaut = Configure::read( "Filtresdefaut.{$this->name}_{$this->action}" );
				$this->request->data = Set::merge( $this->request->data, $filtresdefaut );
			}

			// Options à passer au formulaire
			$this->set( 'typesOrient', $this->Personne->Orientstruct->Typeorient->listOptionsCohortes93() );
			$this->set( 'structuresReferentes', $this->Personne->Orientstruct->Structurereferente->list1Options() );

			$this->set( 'oridemrsa', ClassRegistry::init('Detaildroitrsa')->enum('oridemrsa') );
			$this->set( 'typeserins', $this->Option->typeserins() );
			$this->set( 'printed', $this->Option->printed() );

			$this->set( 'structuresAutomatiques', $this->Cohorte->structuresAutomatiques() );

			// Zones géographiques et cantons
			$this->set( 'cantons', $this->Gestionzonesgeos->listeCantons() );
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			// Préorientations
			$modeles = $this->Personne->Orientstruct->Typeorient->listOptionsPreorientationCohortes93();
			if ( Configure::read( 'Cg.departement' ) == 93 && ( in_array( $this->action, array( 'nouvelles', 'enattente', 'preconisationscalculables' ) ) ) ) {
				$modeles['NOTNULL'] = 'Renseigné';
				$modeles['NULL'] = 'Non renseigné';
			}
			$this->set( 'modeles', $modeles );

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );

			// On n'utilise pas le même layout suivant l'action.
			switch( $statutOrientation ) {
				case 'En attente':
					$this->set( 'pageTitle', 'Demandes en attente de validation d\'orientation' );
					$this->render( 'formulaire' );
					break;
				case 'Non orienté':
					$this->set( 'pageTitle', 'Demandes non orientées' );
					$this->render( 'formulaire' );
					break;
//				case 'Calculables':
//					$this->set( 'pageTitle', 'Demandes d\'orientation préorientées' );
//					$this->render( 'formulaire' );
//					break;
//				case 'Non calculables':
//					$this->set( 'pageTitle', 'Demandes d\'orientation non préorientées' );
//					$this->render( 'formulaire' );
//					break;
				case 'Orienté':
					$this->set( 'pageTitle', 'Demandes orientées' );
					$this->render( 'visualisation' );
					break;
			}
		}

		/**
		 *
		 * @param type $personne_id
		 */
		public function cohortegedooo( $personne_id = null ) {
			$queryData = $this->Cohorte->search(
				'Orienté',
				(array)$this->Session->read( 'Auth.Zonegeographique' ),
				$this->Session->read( 'Auth.User.filtre_zone_geo' ),
				Hash::expand( $this->request->params['named'], '__' ),
				false
			);

			if( $limit = Configure::read( 'nb_limit_print' ) ) {
				$queryData['limit'] = $limit;
			}

			$queryData['fields'] = array(
				'Orientstruct.id',
				'Pdf.document',
			);

			$queryData['joins'][] = array(
				'table'      => 'pdfs',
				'alias'      => 'Pdf',
				'type'       => 'INNER',
				'foreignKey' => false,
				'conditions' => array(
					'Pdf.fk_value = Orientstruct.id',
					'Pdf.modele' => 'Orientstruct',
				)
			);

			$queryData = $this->Components->load( 'Search.SearchPaginator' )->setPaginationOrder( $queryData );

			$queryData['fields'][] = 'Pdf.cmspath';

			$queryData['conditions'][] = WebrsaPermissions::conditionsDossier();

			$results = $this->Personne->find( 'all', $queryData );

			// Si le contenu du PDF n'est pas dans la table pdfs, aller le chercher sur le serveur CMS
			$nErrors = 0;
			foreach( $results as $i => $result ) {
				if( empty( $result['Pdf']['document'] ) && !empty( $result['Pdf']['cmspath'] ) ) {
					$pdf = Cmis::read( $result['Pdf']['cmspath'], true );
					if( !empty( $pdf['content'] ) ) {
						$results[$i]['Pdf']['document'] = $pdf['content'];
					}
				}

				// Gestion des erreurs: si on n'a toujours pas le document
				if( empty( $results[$i]['Pdf']['document'] ) ) {
					$nErrors++;
					unset( $results[$i] );
				}
			}

			if( $nErrors > 0 ) {
				$this->Session->setFlash( "Erreur lors de l'impression en cohorte: {$nErrors} documents n'ont pas pu être imprimés. Abandon de l'impression de la cohorte. Demandez à votre administrateur d'exécuter la commande bash suivante: sudo -u www-data lib/Cake/Console/cake generationpdfs orientsstructs -username <username> (où <username> est l'identifiant de l'utilisateur qui sera utilisé pour la récupération d'informations lors de l'impression)", 'flash/error' );
				$this->redirect( $this->referer() );
			}

			$content = $this->Gedooo->concatPdfs( Set::extract( $results, '/Pdf/document' ), 'orientsstructs' );

			$this->Personne->Foyer->Dossier->begin();

			$success = ( $content !== false ) && $this->Personne->Orientstruct->updateAllUnBound(
				array( 'Orientstruct.date_impression' => date( "'Y-m-d'" ) ),
				array(
					'Orientstruct.id' => Set::extract( $results, '/Orientstruct/id' ),
					'Orientstruct.date_impression IS NULL'
				)
			);

			if( $content !== false ) {
				$this->Personne->Foyer->Dossier->commit();
				$this->Gedooo->sendPdfContentToClient( $content, sprintf( "cohorte-orientations-%s.pdf", date( "Ymd-H\hi" ) ) );
				die();
			}
			else {
				$this->Personne->Foyer->Dossier->rollback();
				$this->Session->setFlash( 'Erreur lors de l\'impression en cohorte.', 'flash/error' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 *
		 */
		/*public function statistiques() {
			if( !empty( $this->request->data ) ) {
				$statistiques = $this->Cohorte->statistiques(
					(array)$this->Session->read( 'Auth.Zonegeographique' ),
					$this->Session->read( 'Auth.User.filtre_zone_geo' ),
					$this->request->data
				);
			}

			$this->_setOptions();
			$this->set( compact( 'statistiques' ) );
			$this->set( 'pageTitle', 'Statistiques' );
			$this->render( 'statistiques' );
		}*/
	}
?>