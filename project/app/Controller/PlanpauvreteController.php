<?php
	/**
	 * Code source de la classe PlanpauvreteController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe PlanpauvreteorientationsController ....
	 *
	 * @package app.Controller
	 */
	class PlanpauvreteController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Planpauvrete';

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
					'searchprimoaccedant' => array('filter' => 'Search'),
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
			'Csv',
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
			'Dossier',
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
		public $aucunDroit = array();

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'searchprimoaccedant' => 'read',
			'exportcsv' => 'read'
		);

		/**
		 * Recherche par primo-accedent
		 */
		public function searchprimoaccedant() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesPlanpauvrete' );
			$Recherches->search(
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaRecherchePlanpauvrete'
				)
			);
		}

		/**
		 * Export CSV de la recherche par primo-accédant
		 *
		 * @return void
		 */
		public function exportcsv() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesPlanpauvrete' );
			$Recherches->exportcsv(
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaRecherchePlanpauvrete'
				)
			);
		}
	}
