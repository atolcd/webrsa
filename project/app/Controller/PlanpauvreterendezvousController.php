<?php
	/**
	 * Code source de la classe PlanpauvreterendezvousController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe PlanpauvreterendezvousController ... (CG 66).
	 *
	 * @package app.Controller
	 */
	class PlanpauvreterendezvousController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Planpauvreterendezvous';

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
					'cohorte_infocol' => array('filter' => 'Search'),
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
            'Historiquedroit',
			'WebrsaCohortePlanpauvreterendezvous',
            'Orientstruct',
            'Rendezvous',
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
			'cohorte_infocol' => 'update',
		);

		public function _setOptions() {
			$this->set( 'options', (array)Hash::get( $this->Planpauvreterendezvous->enums(), 'Planpauvreterendezvous' ) );
		}

		/**
		 * Cohorte
		 */
		public function cohorte_infocol() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte( array( 'modelName' => 'Personne', 'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocol' ) );
		}

		/**
		 * Cohorte
		 */
		public function cohorte_infocol_stock() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte( array( 'modelName' => 'Personne', 'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvous' ) );
		}
	}
?>