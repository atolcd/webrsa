<?php
	/**
	 * Code source de la classe ModifsetatsdossiersController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
    App::uses( 'AppController', 'Controller' );
    App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe ModifsetatsdossiersController ....
	 *
	 * @package app.Controller
	 */
	class ModifsetatsdossiersController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Modifsetatsdossiers';

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
					'cohorte_modifetatdos' => array('filter' => 'Search'),
				),
			),
			//'WebrsaAccesses',
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
		public $aucunDroit = array(
			'exportcsv_modifetatdos',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'cohorte_modifetatdos' => 'update',
			'exportcsv_modifetatdos' => 'read',
		);

		/**
		 * Cohorte de modification de l'état des dossiers
		 */
		public function cohorte_modifetatdos() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesModifetatdossiers' );
			$Cohorte->cohorte( array( 'modelName' => 'Dossier', 'modelRechercheName' => 'WebrsaCohorteModifetatdossier' ) );
		}

		/**
		 * Export CSV de la liste des dossiers
		 */
		public function exportcsv_modifetatdos() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesModifetatdossiers' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Dossier',
					'modelRechercheName' => 'WebrsaCohorteModifetatdossier'
				)
			);
		}
	}
?>