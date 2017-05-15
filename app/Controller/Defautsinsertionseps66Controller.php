<?php
	/**
	 * Code source de la classe Defautsinsertionseps66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Defautsinsertionseps66Controller ...
	 *
	 * @package app.Controller
	 */
	class Defautsinsertionseps66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Defautsinsertionseps66';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Gedooo.Gedooo' ,
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
					'selectionnoninscrits',
					'selectionradies',
					'courriersinformations',
					'search_noninscrits',
					'search_radies'
				)
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Search',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Defautinsertionep66',
			'Personne',
			'WebrsaRechercheNoninscrit',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'search_noninscrits' => 'Defautsinsertionseps66:selectionnoninscrits',
			'search_radies' => 'Defautsinsertionseps66:selectionradies',
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
			'courriersinformations' => 'update',
			'exportcsv_noninscrits' => 'read',
			'exportcsv_radies' => 'read',
			'printCourriersInformations' => 'update',
			'search_noninscrits' => 'read',
			'search_radies' => 'read',
			'selectionnoninscrits' => 'read',
			'selectionradies' => 'read',
		);
		
		/**
		* 'qdNonInscrits', 'noninscriptionpe'
		*/
		protected function _selectionPassageDefautinsertionep66( $qdName, $actionbp ) {
			$this->set( 'etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa', array('filter' =>  ClassRegistry::init('Situationdossierrsa')->etatOuvert())) );

			if( Configure::read( 'CG.cantons' ) ) {
				$this->set( 'cantons', ClassRegistry::init( 'Canton' )->selectList() );
			}

			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			if( Configure::read( 'Zonesegeographiques.CodesInsee' ) ) {
				$mesCodesInsee = ClassRegistry::init( 'Zonegeographique' )->listeCodesInseeLocalites( $mesCodesInsee, $this->Session->read( 'Auth.User.filtre_zone_geo' ) );
			}
			else {
				$mesCodesInsee = ClassRegistry::init( 'Adresse' )->listeCodesInsee();
			}
			$this->set( compact( 'mesCodesInsee' ) );

			if( !empty( $this->request->data ) ) {
				$queryData = $this->Defautinsertionep66->{$qdName}( $this->request->data, ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() ), $this->Session->read( 'Auth.User.filtre_zone_geo' ) );
				$queryData['limit'] = 10;
				$queryData['conditions'][] = WebrsaPermissions::conditionsDossier();

				if (isset($this->paginate)) {
					$this->paginate['Personne'] += $queryData;
				}
				else{
					$this->paginate = array( 'Personne' => $queryData );
				}

				$progressivePaginate = !Hash::get( $this->request->data, 'Pagination.nombre_total' );
				$personnes = $this->paginate( $this->Defautinsertionep66->Dossierep->Personne, array(), array(), $progressivePaginate );
			}

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );

			$this->set( compact( 'personnes' ) );

			$this->set( compact( 'actionbp' ) );

			$this->render( 'selectionnoninscrits' );
		}

		/**
		 * @deprecated since version 3.0.0
		 * @see self::search_noninscrits()
		 */
		public function selectionnoninscrits() {
			$this->_selectionPassageDefautinsertionep66( 'qdNonInscrits', 'noninscriptionpe' );
		}

		/**
		 * @deprecated since version 3.0.0
		 * @see self::search_radies()
		 */
		public function selectionradies() {
			$this->_selectionPassageDefautinsertionep66( 'qdRadies', 'radiationpe' );
		}

		/**
		*
		*/

		public function courriersinformations() {
			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			$this->Gestionzonesgeos->setCantonsIfConfigured();
			$this->set( 'printed',  ClassRegistry::init('Option')->printed() );

			if( !empty( $this->request->data ) ) {
				$search = $this->request->data['Search'];

				if ( !empty( $search ) ) {
					$querydata = array(
						'Dossierep' => $this->Defautinsertionep66->search(
							$mesCodesInsee,
							$this->Session->read( 'Auth.User.filtre_zone_geo' ),
							$search
						)
					);
					$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();
					$this->paginate = $querydata;

					$progressivePaginate = !Hash::get( $this->request->data, 'Search.Pagination.nombre_total' );
					$results = $this->paginate( $this->Defautinsertionep66->Dossierep, array(), array(), $progressivePaginate );
					$this->set( compact( 'results' ) );
				}
			}

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );

			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );
		}

		public function printCourriersInformations() {
			$this->Defautinsertionep66->begin();

			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			if( Configure::read( 'CG.cantons' ) ) {
                            $this->set( 'cantons', ClassRegistry::init( 'Canton' )->selectList() );
			}

                        $data = Hash::expand( $this->request->params['named'], '__' );
			$querydata = $this->Defautinsertionep66->search(
				$mesCodesInsee,
				$this->Session->read( 'Auth.User.filtre_zone_geo' ),
				$data['Search']
			);
			unset( $querydata['limit'] );
			$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();

			$defautsinsertionseps66 = $this->Defautinsertionep66->Dossierep->find( 'all', $querydata );

			$pdfs = array();
			foreach( Set::extract( '/Dossierep/id', $defautsinsertionseps66 ) as $dossierep_id ) {
				$pdfs[] = $this->Defautinsertionep66->getCourrierInformationPdf( $dossierep_id, $this->Session->read( 'Auth.User.id' ) );
			}

			$pdfs = $this->Gedooo->concatPdfs( $pdfs, 'CourriersInformation' );

			if( $pdfs ) {
				$this->Defautinsertionep66->commit(); //FIXME
				$this->Gedooo->sendPdfContentToClient( $pdfs, 'CourriersInformation.pdf' );
			}
			else {
				$this->Defautinsertionep66->rollback();
				$this->Session->setFlash( 'Impossible de générer les courriers d\'information pour cette commission.', 'default', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Moteur de recherche
		 */
		public function search_noninscrits() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesDefautsinsertionseps66' );
			$Recherches->search( array('modelRechercheName' => 'WebrsaRechercheDefautinsertionep66Noninscrit', 'modelName' => 'Personne') );
			$this->Personne->validate = array();
			$this->view =  'search';
		}

		/**
		 * Moteur de recherche
		 */
		public function search_radies() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesDefautsinsertionseps66' );
			$Recherches->search( array('modelRechercheName' => 'WebrsaRechercheDefautinsertionep66Selectionradie', 'modelName' => 'Personne') );
			$this->Personne->validate = array();
			$this->view =  'search';
		}

		/**
		 * Moteur de recherche
		 */
		public function exportcsv_noninscrits() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesDefautsinsertionseps66' );
			$Recherches->exportcsv( array('modelRechercheName' => 'WebrsaRechercheDefautinsertionep66Noninscrit', 'modelName' => 'Personne') );
		}

		/**
		 * Moteur de recherche
		 */
		public function exportcsv_radies() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesDefautsinsertionseps66' );
			$Recherches->exportcsv( array('modelRechercheName' => 'WebrsaRechercheDefautinsertionep66Selectionradie', 'modelName' => 'Personne') );
		}
	}
?>