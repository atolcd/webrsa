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

	/**
	 * La classe PlanpauvreteorientationsController ... (CG 66).
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
				),
			),
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
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Nonoriente66',
			'Personne',
			'WebrsaCohortePlanpauvreteorientationsIsemploi',
			'Orientstruct',
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
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'cohorte_isemploi' => 'update',
			'cohorte_isemploi_stock' => 'update'
		);

		public function _setOptions() {
			$this->set( 'options', (array)Hash::get( $this->Planpauvreteorientations->enums(), 'Planpauvreteorientations' ) );
		}

		/**
		 * Cohorte
		 */
		public function cohorte_isemploi() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreteorientations' );
			$Cohorte->cohorte( array( 'modelName' => 'Personne', 'modelRechercheName' => 'WebrsaCohortePlanpauvreteorientationsIsemploi' ) );
		}

		/**
		 * Cohorte
		 */
		public function cohorte_isemploi_stock() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreteorientations' );
			$Cohorte->cohorte( array( 'modelName' => 'Personne', 'modelRechercheName' => 'WebrsaCohortePlanpauvreteorientationsIsemploistock' ) );
		}

	}
?>