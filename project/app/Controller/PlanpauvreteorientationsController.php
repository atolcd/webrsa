<?php
	/**
	 * Code source de la classe PlanpauvreteorientationsController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'WebrsaAccessOrientsstructs', 'Utility' );

	/**
	 * La classe PlanpauvreteorientationsController ....
	 *
	 * @package app.Controller
	 */
	class PlanpauvreteorientationsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Planpauvreteorientations';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes',
			'Default',
			'DossiersMenus',
			'Gedooo.Gedooo',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
					'cohorte_isemploi' => array('filter' => 'Search'),
					'cohorte_isemploi_stock' => array('filter' => 'Search'),
					'cohorte_isemploi_imprime' => array('filter' => 'Search'),
					'cohorte_isemploi_stock_imprime' => array('filter' => 'Search'),
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
			'Default',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
			'Locale',
			'Xform',
			'Xhtml',
			'Search.SearchForm',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Personne',
			'WebrsaCohortePlanpauvreteorientations',
			'Orientstruct',
			'WebrsaOrientstruct',
			'Option',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array();

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'exportcsv_isemploi',
			'exportcsv_isemploi_stock',
			'exportcsv_isemploi_imprime',
			'exportcsv_isemploi_stock_imprime',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'cohorte_isemploi' => 'update',
			'cohorte_isemploi_imprime' => 'read',
			'cohorte_isemploi_imprime_impressions' => 'read',
			'cohorte_isemploi_stock' => 'update',
			'cohorte_isemploi_stock_imprime' => 'read',
			'cohorte_isemploi_stock_imprime_impressions' => 'read',
			'exportcsv_isemploi' => 'read',
			'exportcsv_isemploi_stock' => 'read'
		);

		public function _setOptions() {
			$this->set( 'options', (array)Hash::get( $this->Planpauvreteorientations->enums(), 'Planpauvreteorientations' ) );
		}

		/**
		 * Cohorte d'orientation des personne inscrites à Pole emploi le mois dernier
		 */
		public function cohorte_isemploi() {
			// Texte pour flux des nouveaux entrants.
			$this->loadModel('WebrsaCohortePlanpauvrete');
			$texteFlux = $this->WebrsaCohortePlanpauvrete->texteFluxNouveauxEntrants ();
			$this->set ('texteFlux', $texteFlux);

			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreteorientations' );
			$Cohorte->cohorte( array( 'modelName' => 'Personne', 'modelRechercheName' => 'WebrsaCohortePlanpauvreteorientationsIsemploi' ) );
		}

		/**
		 * Cohorte d'impression des personne inscrites et orientés à Pole emploi le mois dernier
		 */
		public function cohorte_isemploi_imprime() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreteorientations' );
			$Cohorte->cohorte( array( 'modelName' => 'Personne', 'modelRechercheName' => 'WebrsaCohortePlanpauvreteorientationsIsemploiImprime' ) );
		}

		/**
		 * Cohorte d'orientation des personne inscrites à Pole emploi le mois dernier
		 */
		public function cohorte_isemploi_imprime_impressions() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreteorientationsImpressions' );
			$Cohorte->impressions( array( 'modelName' => 'Personne', 'modelRechercheName' => 'WebrsaCohortePlanpauvreteorientationsIsemploiImprime' ) );
		}

		/**
		 * Export CSV de la
		 * Fonction d'impression de la cohorte d'impression des personne inscrites et orientés à Pole emploi le mois dernier
		 */
		public function exportcsv_isemploi() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesPlanpauvreteorientations' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreteorientationsIsemploi'
				)
			);
		}

		/**
		 * Export CSV de la
		 * Cohorte d'impression des personne inscrites et orientés à Pole emploi le mois dernier
		 */
		public function exportcsv_isemploi_imprime() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesPlanpauvreteorientations' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreteorientationsIsemploiImprime'
				)
			);
		}

		/**
		 * Cohorte d'orientation des personne inscrites à Pole emploi en stock (avant le mois dernier)
		 */
		public function cohorte_isemploi_stock() {
			// Texte pour flux du stock.
			$this->loadModel('WebrsaCohortePlanpauvrete');
			$texteFlux = $this->WebrsaCohortePlanpauvrete->texteFluxStock ();
			$this->set ('texteFlux', $texteFlux);

			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreteorientations' );
			$Cohorte->cohorte( array( 'modelName' => 'Personne', 'modelRechercheName' => 'WebrsaCohortePlanpauvreteorientationsIsemploistock' ) );
		}

		/**
		 * Cohorte d'impression des personne inscrites et orientés à Pole emploi (avant le mois dernier)
		 */
		public function cohorte_isemploi_stock_imprime() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreteorientations' );
			$Cohorte->cohorte( array( 'modelName' => 'Personne', 'modelRechercheName' => 'WebrsaCohortePlanpauvreteorientationsIsemploistockImprime' ) );
		}

		/**
		 * Fonction d'impression de la cohorte d'impresson des personne inscrites à Pole emploi (avant le mois dernier)
		 */
		public function cohorte_isemploi_stock_imprime_impressions() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreteorientationsImpressions' );
			$Cohorte->impressions( array( 'modelName' => 'Personne', 'modelRechercheName' => 'WebrsaCohortePlanpauvreteorientationsIsemploistockImprime' ) );
		}

		/**
		 * Export CSV de la
		 * Cohorte d'orientation des personne inscrites à Pole emploi en stock (avant le mois dernier)
		 */
		public function exportcsv_isemploi_stock() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesPlanpauvreteorientations' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreteorientationsIsemploistock'
				)
			);
		}

		/**
		 * Export CSV de la
		 * Cohorte d'impression des personne inscrites et orientés à Pole emploi (avant le mois dernier)
		 */
		public function exportcsv_isemploi_stock_imprime() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesPlanpauvreteorientations' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreteorientationsIsemploistockImprime'
				)
			);
		}

	}
?>