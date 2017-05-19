<?php
	/**
	 * Code source de la classe IndicateurssuivisController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe IndicateurssuivisController ...
	 *
	 * @package app.Controller
	 */
	class IndicateurssuivisController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Indicateurssuivis';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Search.SearchFiltresdefaut' => array(
				'index',
				'search',
			),
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
					'search' => array('filter' => 'Search'),
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
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Search',
			'Xform',
			'Xhtml',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Dossier',
			'Dossierep',
			'Foyer',
			'Indicateursuivi',
			'Informationpe',
			'Option',
			'Personne',
			'Referent',
			'Structurereferente',
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
			'exportcsv_search' => 'read',
			'search' => 'read'
		);

		protected function _setOptions() {
			$natpfsSocle = Configure::read( 'Detailcalculdroitrsa.natpf.socle' );
			$this->set( 'natpf', ClassRegistry::init('Detailcalculdroitrsa')->enum('natpf', array('filter' => $natpfsSocle ) ));
			$this->Gestionzonesgeos->setCantonsIfConfigured();
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );
			$this->set( 'structs', $this->Structurereferente->list1Options( array( 'orientation' => 'O' ) ) );
			$this->set( 'referents', $this->InsertionsBeneficiaires->referents( array( 'type' => 'list', 'prefix' => false, 'conditions' => array( 'Referent.actif' => 'O' ) ) ) );
			$this->set( 'options', (array)Hash::get( $this->Dossierep->enums(), 'Dossierep' ) );
			$this->set( 'etatpe', (array)Hash::get( $this->Informationpe->Historiqueetatpe->enums(), 'Historiqueetatpe' ) );
		}

		/**
		 * Moteur de recherche par suivi de l'allocataire
		 */
		public function search() {
			if( Hash::check( $this->request->data, 'Search' ) ) {
				$query = $this->Indicateursuivi->search58( $this->request->data['Search'] );

				$query = $this->Allocataires->completeSearchQuery( $query );

				$this->Personne->forceVirtualFields = true;
				$results = $this->Allocataires->paginate( $query );

				$this->set( compact( 'results' ) );
			}

			$options = $this->Allocataires->options();
			$options = Hash::merge( $options, $this->Indicateursuivi->options( array( 'allocataire' => false ) ) );
			$this->set( compact( 'options' ) );
		}

		/**
		 * Export CSV des résultats du moteur de recherche par suivi de l'allocataire
		 */
		public function exportcsv_search() {
			$search = (array)Hash::get( (array)Hash::expand( $this->request->params['named'], '__' ), 'Search' );

			$query = $this->Indicateursuivi->search58( $search );

			$query = $this->Allocataires->completeSearchQuery( $query, array( 'limit' => false ) );
			$query = $this->Components->load( 'Search.SearchPaginator' )->setPaginationOrder( $query );

			$this->Personne->forceVirtualFields = true;
			$results = $this->Personne->find( 'all', $query );

			$options = $this->Allocataires->options();
			$options = Hash::merge( $options, $this->Indicateursuivi->options( array( 'allocataire' => false ) ) );
			$this->set( compact( 'options' ) );

			$this->set( compact( 'results', 'options' ) );
			$this->layout = null;
		}
	}
?>