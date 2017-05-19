<?php
	/**
	 * Code source de la classe IndusController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe IndusController ...
	 *
	 * @package app.Controller
	 */
	class IndusController  extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Indus';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Jetons2',
			'DossiersMenus',
			'Search.SearchPrg' => array(
				'actions' => array( 'search' )
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Infofinanciere',
			'Cohorteindu',
			'Dossier',
			'Foyer',
			'Indu',
			'Option',
			'Personne',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'exportcsv' => 'Cohortesindus:exportcsv',
			'search' => 'Cohortesindus:index',
			'view' => 'Indus:index',
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
			'search' => 'read',
			'view' => 'read',
		);

		/**
		 *
		 */
		public function beforeFilter() {
			parent::beforeFilter();
			$this->set( 'type_allocation', $this->Infofinanciere->enum('type_allocation') );
			$this->set( 'natpfcre', ClassRegistry::init('Infofinanciere')->enum('natpfcre') );
			$this->set( 'typeopecompta', ClassRegistry::init('Infofinanciere')->enum('typeopecompta') );
			$this->set( 'sensopecompta', ClassRegistry::init('Infofinanciere')->enum('sensopecompta') );
			$this->set( 'etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa') );
		}

		/**
		 *
		 * @param integer $dossier_id
		 */
		public function index( $dossier_id = null) {
			$this->assert( valid_int( $dossier_id ), 'invalidParameter' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			$params = $this->Indu->search( $mesCodesInsee, $this->Session->read( 'Auth.User.filtre_zone_geo' ), array( 'Dossier.id' => $dossier_id ) );
			$infofinanciere = $this->Infofinanciere->find( 'first',  $params );

			$this->set('infofinanciere', $infofinanciere );
			$this->set( 'dossier_id', $dossier_id );
		}

		/**
		*
		*/

		public function view( $dossier_id = null ) {
			// Vérification du format de la variable
			$this->assert( valid_int( $dossier_id ), 'error404' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			$params = $this->Indu->search( $mesCodesInsee, $this->Session->read( 'Auth.User.filtre_zone_geo' ), array( 'Dossier.id' => $dossier_id ) );
			$infofinanciere = $this->Infofinanciere->find( 'first',  $params );
			$this->assert( !empty( $infofinanciere ), 'error404' );

			// Assignations à la vue
			$this->set( 'dossier_id', $dossier_id );
			$this->set( 'infofinanciere', $infofinanciere );
			$this->set( 'urlmenu', '/indus/index/'.$dossier_id );
		}

		/**
		 * Moteur de recherche
		 */
		public function search() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesIndus' );
			$Recherches->search( array( 'modelRechercheName' => 'WebrsaRechercheIndu', 'modelName' => 'Dossier' ) );
			$this->Infofinanciere->validate = array();
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesIndus' );
			$Recherches->exportcsv( array( 'modelRechercheName' => 'WebrsaRechercheIndu', 'modelName' => 'Dossier' ) );
		}
	}
?>