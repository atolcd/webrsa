<?php
	/**
	 * Code source de la classe DemenagementshorsdptsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe DemenagementshorsdptsController ...
	 *
	 * @package app.Controller
	 */
	class DemenagementshorsdptsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Demenagementshorsdpts';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'DossiersMenus',
			'Jetons2',
			'Search.SearchFiltresdefaut' => array(
				'search',
				'search1',
			),
			'Search.SearchPrg' => array(
				'actions' => array(
					'search1' => array('filter' => 'Search'),
					'search'
				)
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Allocataires',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Search.SearchForm',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Personne',
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

		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'exportcsv' => 'read',
			'search' => 'read',
		);

		/**
		 * Moteur de recherche
		 */
		public function search() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesDemenagementshorsdpts' );
			$Recherches->search( array('modelName' => 'Personne', 'modelRechercheName' => 'WebrsaRechercheDemenagementhorsdpt') );
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesDemenagementshorsdpts' );
			$Recherches->exportcsv( array('modelName' => 'Personne', 'modelRechercheName' => 'WebrsaRechercheDemenagementhorsdpt') );
		}
	}
?>
