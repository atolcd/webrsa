<?php
	/**
	 * Code source de la classe Criterestransfertspdvs93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AppController', 'Controller');

	/**
	 * Classe Criterestransfertspdvs93Controller.
	 *
	 * @deprecated since 3.0.0
	 * @see Transfertspdvs93::search() et Transfertspdvs93::exportcsv()
	 *
	 * @package app.Controller
	 */
	class Criterestransfertspdvs93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Criterestransfertspdvs93';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'Search.Filtresdefaut' => array(
				'index',
			),
			'Gedooo.Gedooo',
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Search.SearchPrg' => array(
				'actions' => array(
					'index' => array(
						'filter' => 'Search'
					),
				)
			),
			'Workflowscers93',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
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
			'Criteretransfertpdv93',
			'Dossier',
			'Option',
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
			'exportcsv' => 'read',
			'index' => 'read',
		);

		/**
		 * Permet de limiter les résultats de la recherche à ceux dont l'adresse
		 * de rang 02 uniquement est sur une des zones géographiques couverte par
		 * la structure référente de l'utilisateur connecté lorsque celui-ci est
		 * un externe (CG 93).
		 *
		 * @param array $query
		 * @return array
		 */
		protected function _completeSearchQuery( $query ) {
			if( Configure::read( 'Cg.departement' ) == 93 ) {
				$type = $this->Session->read( 'Auth.User.type' );

				if( stristr( $type, 'externe_' ) !== false ) {
					$query['conditions']['Adressefoyer.rgadr'] = '02';
				}
			}

			return $query;
		}

		/**
		 * Pagination sur les <élément>s de la table.
		 *
		 * @return void
		 */
		public function index() {
			if( !empty( $this->request->data ) ) {
				$querydata = array(
					'Dossier' => $this->Criteretransfertpdv93->search(
						(array)$this->Session->read( 'Auth.Zonegeographique' ),
						$this->Session->read( 'Auth.User.filtre_zone_geo' ),
						$this->request->data['Search'],
						null
					)
				);

				$querydata['Dossier'] = $this->_completeSearchQuery( $querydata['Dossier'] );

				$this->paginate = $querydata;
				$results = $this->paginate(
					$this->Dossier,
					array(),
					array(),
					!Set::classicExtract( $this->request->data, 'Search.Pagination.nombre_total' )
				);

				$this->set( compact( 'results' ) );
			}

			$options = array(
				'cantons' => $this->Gestionzonesgeos->listeCantons(),
				'etatdosrsa' => ClassRegistry::init('Dossier')->enum('etatdosrsa'),
				'mesCodesInsee' => $this->Gestionzonesgeos->listeCodesInsee(),
				'toppersdrodevorsa' => $this->Option->toppersdrodevorsa( true ),
				'rolepers' => ClassRegistry::init('Prestation')->enum('rolepers'),
				'qual' => $this->Option->qual(),
				'structuresreferentes' => $this->Dossier->Foyer->Personne->Orientstruct->Structurereferente->listOptions(),
				'typesorients' => $this->Dossier->Foyer->Personne->Orientstruct->Typeorient->listOptions(),
			);
			$this->set( compact( 'options' ) );

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );
		}

		/**
		 * Export CSV des résultats de la recherche.
		 */
		public function exportcsv() {
			$search = (array)Hash::get( (array)Hash::expand( $this->request->params['named'], '__' ), 'Search' );
			$query = $this->Criteretransfertpdv93->search(
				(array)$this->Session->read( 'Auth.Zonegeographique' ),
				$this->Session->read( 'Auth.User.filtre_zone_geo' ),
				$search,
				null
			);
			$query = $this->_completeSearchQuery( $query );

			$query = $this->Components->load( 'Search.SearchPaginator' )->setPaginationOrder( $query );
			unset( $query['limit'] );

			$results = $this->Dossier->find( 'all', $query );
			$options = $this->Allocataires->options();

			$this->set( compact( 'results', 'options' ) );
			$this->layout = null;

			/*$search = (array)Hash::get( (array)Hash::expand( $this->request->params['named'], '__' ), 'Search' );
			$query = $this->Demenagementhorsdpt->search( $search );
			$query = $this->_completeQuery( $query );
			$query = $this->Components->load( 'Search.SearchPaginator' )->setPaginationOrder( $query );
			unset( $query['limit'] );

			$results = $this->Personne->find( 'all', $query );
			$options = Hash::merge(
				$this->Allocataires->options(),
				$this->Demenagementhorsdpt->options( array( 'allocataire' => false ) )
			);

			$this->set( compact( 'results', 'options' ) );
			$this->layout = null;*/
		}
	}
?>