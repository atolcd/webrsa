<?php
	/**
	 * Code source de la classe Nonorientationsproscovs58Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Nonorientationsproscovs58Controller ...
	 *
	 * @package app.Controller
	 */
	class Nonorientationsproscovs58Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Nonorientationsproscovs58';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'Cohortes',
			'Search.SearchFiltresdefaut' => array(
				'cohorte'
			),
			'Search.SearchPrg' => array(
				'actions' => array(
					'cohorte' => array('filter' => 'Search')
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
			'Nonorientationprocov58',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'cohorte' => 'Nonorientationsproseps:index',
			'exportcsv' => 'Nonorientationsproseps:exportcsv',
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
			'cohorte' => 'create',
			'exportcsv' => 'read'
		);

		/**
		 * Cohorte de sélection des "Demandes de maintien dans le social
		 *".
		 */
		public function cohorte() {
			$this->loadModel('Orientstruct');

			$Cohortes = $this->Components->load( 'WebrsaCohortesNonorientationsproscovs58' );

			$Cohortes->cohorte(
				array(
					'modelName' => 'Orientstruct',
					'modelRechercheName' => 'WebrsaCohorteNonorientationprocov58'
				)
			);
		}

		/**
		 * Export CSV desc résultats de la "Demandes de maintien dans le social
		 *".
		 */
		public function exportcsv() {
			$this->loadModel('Orientstruct');

			$Cohortes = $this->Components->load( 'WebrsaCohortesNonorientationsproscovs58' );

			$Cohortes->exportcsv(
				array(
					'modelName' => 'Orientstruct',
					'modelRechercheName' => 'WebrsaCohorteNonorientationprocov58'
				)
			);
		}
	}
?>
