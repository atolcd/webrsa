<?php
	/**
	 * Code source de la classe ChangementsadressesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'CakeEmail', 'Network/Email' );
	App::uses( 'WebrsaEmailConfig', 'Utility' );

	/**
	 * La classe ChangementsadressesController
	 *
	 * @package app.Controller
	 * @see Changementsadresses66Controller (refonte)
	 */
	class ChangementsadressesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Changementsadresses';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
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
			'Adressefoyer',
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
			'search' => 'read',
		);

		/**
		 * Moteur de recherche
		 */
		public function search() {
			$search = $this->Components->load('WebrsaRecherchesChangementsadresses');
			$search->search(array('modelName' => 'Dossier', 'modelRechercheName' => 'WebrsaRechercheChangementadresse'));
		}

		/**
		 * Export CSV du Moteur de recherche
		 */
		public function exportcsv() {
			$search = $this->Components->load('WebrsaRecherchesChangementsadresses');
			$search->exportcsv(array('modelName' => 'Dossier', 'modelRechercheName' => 'WebrsaRechercheChangementadresse'));
		}
	}
?>