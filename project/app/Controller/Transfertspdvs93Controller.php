<?php
	/**
	 * Code source de la classe Transfertspdvs93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Transfertspdvs93Controller ...
	 *
	 * @package app.Controller
	 */
	class Transfertspdvs93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Transfertspdvs93';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes',
			'Gedooo.Gedooo',
			'InsertionsBeneficiaires',
			'Jetons2',
			'Search.SearchFiltresdefaut' => array(
				'search',
			),
			'Search.SearchPrg' => array(
				'actions' => array(
					'search',
					'cohorte_atransferer' => array(
						'filter' => 'Search'
					),
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
			'Dossier',
			'StructureReferente'
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
			'cohorte_atransferer' => 'create',
			'exportcsv' => 'read',
			'search' => 'read',
		);

		/**
		 * Moteur de recherche par rendez-vous.
		 */
		public function search() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesTransfertspdvs93' );
			$Recherches->search(
				array(
					'modelName' => 'Dossier',
					'modelRechercheName' => 'WebrsaRechercheTransfertpdv93',
				)
			);
		}

		/**
		 * Export CSV des résultats de la recherche par rendez-vous.
		 */
		public function exportcsv() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesTransfertspdvs93' );
			$Recherches->exportcsv(
				array(
					'modelName' => 'Dossier',
					'modelRechercheName' => 'WebrsaRechercheTransfertpdv93',
				)
			);
		}

		/**
		 * Cohorte de transferts PDV, allocataires à transférer
		 */
		public function cohorte_atransferer() {
			$this->Gedooo->check( false, true );

			$Recherches = $this->Components->load( 'WebrsaCohortesTransfertspdvs93Atransferer' );
			$Recherches->cohorte(
				array(
					'modelName' => 'Dossier',
					'modelRechercheName' => 'WebrsaCohorteTransfertpdv93Atransferer',
				)
			);
		}
	}
?>
