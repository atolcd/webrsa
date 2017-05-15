<?php
	/**
	 * Code source de la classe Cohortestransfertspdvs93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Cohortestransfertspdvs93Controller s'occupe des moteurs de
	 * recherche des allocataires à transférer (ainsi que du traitement du transfert)
	 * et des allocataires transférés.
	 *
	 * @package app.Controller
	 */
	class Cohortestransfertspdvs93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cohortestransfertspdvs93';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes'=> array(
				'atransferer'
			),
			'Gedooo.Gedooo',
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Search.Filtresdefaut' => array(
				'atransferer',
				'transferes',
			),
			'Search.SearchPrg' => array(
				'actions' => array(
					'atransferer' => array('filter' => 'Search'),
					'transferes' => array('filter' => 'Search'),
				),
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
			'Search',
			'Xpaginator2',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cohortetransfertpdv93',
			'Option',
			'Transfertpdv93',
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
			'atransferer' => 'read',
			'exportcsv' => 'read',
			'impression' => 'read',
			'impressions' => 'read',
			'transferes' => 'read',
		);

		/**
		 * Recherche et traitement des allocataires à transférer.
		 *
		 * @deprecated since 3.0.0
		 *
		 * @return void
		 */
		public function atransferer() {
			$this->_index();
		}

		/**
		 * Recherche des allocataires déjà transférés.
		 *
		 * @return void
		 */
		public function transferes() {
			$this->_index();
		}

		/**
		 * Méthode commune de recherche et de traitement des allocataires à transférer
		 * et transférés.
		 *
		 * @return void
		 */
		protected function _index() {
			$structuresParZonesGeographiques = $this->Cohortetransfertpdv93->structuresParZonesGeographiquesPourTransfertPdv();

			if( !empty( $this->request->data ) ) {
				// Traitement des données renvoyées
				if( $this->action == 'atransferer' && isset( $this->request->data['Transfertpdv93'] ) ) {
					$dossiers_ids = array_unique( Set::extract( '/Transfertpdv93/dossier_id', $this->request->data ) );
					$this->Cohortes->get( $dossiers_ids );

					$data = Set::extract( '/Transfertpdv93[action=1]', $this->request->data );
					if( !empty( $data ) ) {
						$this->Transfertpdv93->begin();
						if( $this->Cohortetransfertpdv93->saveCohorte( $data, $this->Session->read( 'Auth.User.id' ) ) ) {
							$this->Transfertpdv93->commit();
							unset( $this->request->data['Transfertpdv93'] );
							$this->Cohortes->release( $dossiers_ids );
							$this->Session->setFlash( 'Enregistrement effectué', 'flash/success');
						}
						else {
							$this->Transfertpdv93->rollback();
							$this->Session->setFlash( 'Erreur(s) lors de l\'enregistrement', 'flash/error');
						}
					}
					else {
						$this->Session->setFlash( 'Aucune donnée à enregistrer', 'flash/error');
					}
				}

				// Traitement du formulaire de recherche
				$querydata = array(
					'Dossier' => $this->Cohortetransfertpdv93->search(
						$this->action,
						(array)$this->Session->read( 'Auth.Zonegeographique' ),
						$this->Session->read( 'Auth.User.filtre_zone_geo' ),
						$this->request->data['Search'],
						( ( $this->action == 'atransferer' ) ? $this->Cohortes->sqLocked( 'Dossier' ) : null ) // FIXME: saisie
					)
				);

				$this->paginate = $querydata;
				$results = $this->paginate(
					$this->Transfertpdv93->VxOrientstruct->Personne->Foyer->Dossier,
					array(),
					array(),
					!Set::classicExtract( $this->request->data, 'Search.Pagination.nombre_total' )
				);

				if( $this->action == 'atransferer' ) {
					$dossiers_ids = array_unique( Set::extract( '/Dossier/id', $results ) );
					$this->Cohortes->get( $dossiers_ids );
				}

				$this->set( compact( 'results' ) );

				if( $this->action == 'atransferer' ) {
					// Préparation des données du formulaire
					if( !isset( $this->request->data['Transfertpdv93'] ) ) {
						$this->request->data = Hash::merge(
							$this->request->data,
							$this->Cohortetransfertpdv93->prepareFormDataIndex( $results, $structuresParZonesGeographiques )
						);
					}
				}
			}

			$options = array(
				'action' => array( '1' => 'Valider', '0' => 'En attente' ),
				'cantons' => $this->Gestionzonesgeos->listeCantons(),
				'etatdosrsa' => ClassRegistry::init('Dossier')->enum('etatdosrsa'),
				'mesCodesInsee' => $this->Gestionzonesgeos->listeCodesInsee(),
				'toppersdrodevorsa' => $this->Option->toppersdrodevorsa( true ),
				'rolepers' => ClassRegistry::init('Prestation')->enum('rolepers'),
				'qual' => $this->Option->qual(),
				'structuresreferentes' => $structuresParZonesGeographiques,
				'departementsnvadresses' => array( '1' => 'Dans le département', '0' => 'Hors du départment' ),
				'typesorients' => $this->Transfertpdv93->VxOrientstruct->Typeorient->listOptions()
			);
			$options['Adresse']['numcom'] = $options['mesCodesInsee'];
			$options['Adresse']['canton'] = $options['cantons'];
			$options  = Set::merge( $options, $this->Transfertpdv93->VxOrientstruct->Personne->Contratinsertion->Cer93->enums() );
			$this->set( compact( 'options' ) );

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );
		}

		/**
		 * Export au format CSV des résultats de la recherche des allocataires transférés.
		 *
		 * @return void
		 */
		public function exportcsv() {
			$data = Hash::expand( $this->request->params['named'], '__' );

			$querydata = $this->Cohortetransfertpdv93->search(
				$this->action,
				(array)$this->Session->read( 'Auth.Zonegeographique' ),
				$this->Session->read( 'Auth.User.filtre_zone_geo' ),
				$data['Search'],
				null
			);

			unset( $querydata['limit'] );

			$results = $this->Transfertpdv93->VxOrientstruct->Personne->Foyer->Dossier->find(
				'all',
				$querydata
			);

			$options = array(
				'rolepers' => ClassRegistry::init('Prestation')->enum('rolepers'),
				'qual' => $this->Option->qual(),
			);

			$this->layout = '';
			$this->set( compact( 'results', 'options' ) );
		}

		/**
		 * Impression en cohorte des orientations des allocataires transférés.
		 *
		 * @return void
		 */
		public function impressions() {
			$data = Hash::expand( $this->request->params['named'], '__' );

			$querydata = $this->Cohortetransfertpdv93->search(
				$this->action,
				(array)$this->Session->read( 'Auth.Zonegeographique' ),
				$this->Session->read( 'Auth.User.filtre_zone_geo' ),
				$data['Search'],
				null
			);

			unset( $querydata['limit'] );

			$querydata['fields'] = array( 'Orientstruct.id' );

			$results = $this->Transfertpdv93->VxOrientstruct->Personne->Foyer->Dossier->find(
				'all',
				$querydata
			);

			$content = false;
			if( !empty( $results ) ) {
				$pdfs = array();
				foreach( Hash::extract( $results, '{n}.Orientstruct.id' ) as $orientstruct_id ) {
					$pdfs[] = $this->Transfertpdv93->getDefaultPdf( $orientstruct_id, $this->Session->read( 'Auth.User.id' ) );
				}

				$content = $this->Gedooo->concatPdfs( $pdfs, 'transfertspdvs93' );
			}

			if( $content !== false ) {
				$this->Gedooo->sendPdfContentToClient( $content, sprintf( "cohorte-transfertspdvs93-%s.pdf", date( "Ymd-H\hi" ) ) );
				die();
			}
			else {
				$this->Session->setFlash( 'Erreur lors de l\'impression en cohorte.', 'flash/error' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Impression d'une orientation d'un allocataire transféré.
		 *
		 * @param integer $id L'id de l'orientstruct que l'on souhaite imprimer.
		 * @return void
		 */
		public function impression( $id = null ) {
// 			$pdf = $this->Transfertpdv93->VxOrientstruct->Personne->Orientstruct->getStoredPdf( $id, 'date_impression' );
// 			$pdf = ( isset( $pdf['Pdf']['document'] ) ? $pdf['Pdf']['document'] : null );

			$pdf = $this->Transfertpdv93->getDefaultPdf( $id, $this->Session->read( 'Auth.User.id' ) );

			if( !empty( $pdf ) ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'transfertspdvs93_%d-%s.pdf', $id, date( 'Y-m-d' ) ) );
			}
			else {
				$this->Session->setFlash( 'Impossible de générer l\'impression de l\'orientation.', 'flash/error', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}
		}
	}
?>
