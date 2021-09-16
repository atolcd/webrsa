<?php
	/**
	 * Code source de la classe PlanpauvreterendezvousController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'PlanpauvretecohorteController', 'Controller' );

	/**
	 * La classe PlanpauvreterendezvousController ...
	 *
	 * @package app.Controller
	 */
	class PlanpauvreterendezvousController extends PlanpauvretecohorteController
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
			'InsertionsBeneficiaires',
			'Gedooo.Gedooo',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
					'cohorte_infocol' => array('filter' => 'Search'),
					'cohorte_infocol_stock' => array('filter' => 'Search'),
					'cohorte_infocol_imprime' => array('filter' => 'Search'),
					'cohorte_infocol_imprime_stock' => array('filter' => 'Search'),
					'cohorte_infocol_venu_nonvenu_nouveaux' => array('filter' => 'Search'),
					'cohorte_infocol_venu_nonvenu_stock' => array('filter' => 'Search'),
					'cohorte_infocol_second_rdv_nouveaux' => array('filter' => 'Search'),
					'cohorte_infocol_second_rdv_stock' => array('filter' => 'Search'),
					'cohorte_infocol_imprime_second_rdv_nouveaux' => array('filter' => 'Search'),
					'cohorte_infocol_imprime_second_rdv_stock' => array('filter' => 'Search'),
					'cohorte_infocol_venu_nonvenu_second_rdv_nouveaux' => array('filter' => 'Search'),
					'cohorte_infocol_venu_nonvenu_second_rdv_stock' => array('filter' => 'Search'),
					'cohorte_infocol_rdv_cer_stock' => array('filter' => 'Search'),
					'cohorte_infocol_rdv_cer_nouveaux' => array('filter' => 'Search'),
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
			'Historiquedroit',
			'WebrsaCohortePlanpauvreterendezvous',
			'Orientstruct',
			'Rendezvous',
			'DossiersMenus',
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
			'exportcsv_infocol',
			'exportcsv_infocol_stock',
			'exportcsv_infocol_imprime',
			'exportcsv_infocol_imprime_stock',
			'exportcsv_infocol_venu_nonvenu_nouveau',
			'exportcsv_infocol_venu_nonvenu_stock',
			'exportcsv_infocol_second_rdv_nouveaux',
			'exportcsv_infocol_second_rdv_stock',
			'exportcsv_infocol_imprime_second_rdv_nouveaux',
			'exportcsv_infocol_imprime_second_rdv_stock',
			'exportcsv_infocol_venu_nonvenu_second_rdv_nouveaux',
			'exportcsv_infocol_venu_nonvenu_second_rdv_stock',
			'exportcsv_infocol_rdv_cer_stock',
			'exportcsv_infocol_rdv_cer_nouveaux',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'cohorte_infocol' => 'update',
			'cohorte_infocol_stock' => 'update',
			'cohorte_infocol_imprime' => 'read',
			'cohorte_infocol_imprime_impressions' => 'read',
			'cohorte_infocol_imprime_stock' => 'read',
			'cohorte_infocol_imprime_second_rdv_nouveaux' => 'read',
			'cohorte_infocol_imprime_second_rdv_stock' => 'read',
			'cohorte_infocol_venu_nonvenu_nouveau' => 'update',
			'cohorte_infocol_venu_nonvenu_stock' => 'update',
			'cohorte_infocol_second_rdv_nouveaux' => 'update',
			'cohorte_infocol_second_rdv_stock' => 'update',
			'cohorte_infocol_venu_nonvenu_second_rdv_nouveaux' => 'update',
			'cohorte_infocol_venu_nonvenu_second_rdv_stock' => 'update',
			'cohorte_infocol_rdv_cer_stock' => 'update',
			'cohorte_infocol_rdv_cer_nouveaux' => 'update',
			'exportcsv_infocol' => 'read',
			'exportcsv_infocol_stock' => 'read',
			'exportcsv_infocol_venu_nonvenu_nouveau' => 'read',
			'exportcsv_infocol_venu_nonvenu_stock' => 'read',
			'exportcsv_infocol_second_rdv_nouveaux' => 'read',
			'exportcsv_infocol_second_rdv_stock' => 'read',
			'exportcsv_infocol_imprime' => 'read',
			'exportcsv_infocol_imprime_stock' => 'read',
			'exportcsv_infocol_imprime_second_rdv_nouveaux' => 'read',
			'exportcsv_infocol_imprime_second_rdv_stock' => 'read',
			'exportcsv_infocol_venu_nonvenu_second_rdv_nouveaux' => 'read',
			'exportcsv_infocol_venu_nonvenu_second_rdv_stock' => 'read',
			'exportcsv_infocol_rdv_cer_stock' => 'read',
			'exportcsv_infocol_rdv_cer_nouveaux' => 'read',
		);

		public function _setOptions() {
			$this->set( 'options', (array)Hash::get( $this->Planpauvreterendezvous->enums(), 'Planpauvreterendezvous' ) );
		}

		/**
		 * Cohorte Information Collective - nouveaux entrants
		 * Créé un rendez vous d'information collective pour les nouveaux entrants
		 */
		public function cohorte_infocol() {
			// Texte pour flux des nouveaux entrants.
			$this->loadModel('WebrsaCohortePlanpauvrete');
			$texteFlux = $this->WebrsaCohortePlanpauvrete->texteFluxNouveauxEntrants ();
			$this->set ('texteFlux', $texteFlux);

			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocol',
					'nom_cohorte' => 'cohorte_infocol'
				)
			);
		}

		/**
		 * Export CSV de la
		 * Cohorte Information Collective - nouveaux entrants
		 */
		public function exportcsv_infocol() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocol',
					'nom_cohorte' => 'cohorte_infocol'
				)
			);
		}

		/**
		 * Cohorte Information Collective - Stock
		 * Créé un rendez vous d'information collective pour le stock
		 */
		public function cohorte_infocol_stock() {
			// Texte pour flux du stock.
			$this->loadModel('WebrsaCohortePlanpauvrete');
			$texteFlux = $this->WebrsaCohortePlanpauvrete->texteFluxStock ();
			$this->set ('texteFlux', $texteFlux);

			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolStock',
					'nom_cohorte' => 'cohorte_infocol_stock'
				)
			);
		}

		/**
		 * Export CSV de la
		 * Cohorte Information Collective - Stock
		 */
		public function exportcsv_infocol_stock() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolStock',
					'nom_cohorte' => 'cohorte_infocol_stock'
				)
			);
		}

		/**
		 * Cohorte Impression Information Collective - Nouveaux entrants
		 *
		 * Permet l'impression de la convocation d'information collective
		 */
		public function cohorte_infocol_imprime() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvousImpressions' );
			$Cohorte->search (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprime',
					'nom_cohorte' => 'cohorte_infocol_imprime'
				)
			);
		}

		/**
		 * Export CSV de la
		 * Cohorte Impression Information Collective - Nouveaux entrants
		 */
		public function exportcsv_infocol_imprime() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprime',
					'nom_cohorte' => 'cohorte_infocol_imprime'
				)
			);
		}

		/**
		 * Cohorte Impression Information Collective - Stock
		 *
		 * Permet l'impression de la convocation d'information collective du stock
		 */
		public function cohorte_infocol_imprime_stock () {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvousImpressions' );
			$Cohorte->search (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprimeStock',
					'nom_cohorte' => 'cohorte_infocol_imprime_stock'
				)
			);
		}

		/**
		 * Export CSV de la
		 * Cohorte Impression Information Collective - Stock
		 */
		public function exportcsv_infocol_imprime_stock() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprimeStock',
					'nom_cohorte' => 'cohorte_infocol_imprime'
				)
			);
		}

		/**
		 * Cohorte Information Collective Second Rendez-vous - Nouveaux entrants
		 *
		 * Créé un second rendez vous d'information collective
		 */
		public function cohorte_infocol_second_rdv_nouveaux() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolSecondRdvNouveaux',
					'nom_cohorte' => 'cohorte_infocol_second_rdv_nouveaux'
				)
			);
		}

		/**
		 * Export CSV de la
		 * Cohorte Information Collective Second Rendez-vous - Nouveaux entrants
		 */
		public function exportcsv_infocol_second_rdv_nouveaux() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolSecondRdvNouveaux',
					'nom_cohorte' => 'cohorte_infocol_second_rdv_nouveaux'
				)
			);
		}

		/**
		 * Cohorte Information Collective Second Rendez-vous - Stock
		 *
		 * Créé un second rendez vous d'information collective
		 */
		public function cohorte_infocol_second_rdv_stock() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolSecondRdvStock',
					'nom_cohorte' => 'cohorte_infocol_second_rdv_stock'
				)
			);
		}

		/**
		 * Export CSV de la
		 * Cohorte Information Collective Second Rendez-vous - Stock
		 */
		public function exportcsv_infocol_second_rdv_stock() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolSecondRdvStock',
					'nom_cohorte' => 'cohorte_infocol_second_rdv_stock'
				)
			);
		}

		/**
		 * Cohorte Impression Information Collective Second Rendez-vous - Nouveaux entrants
		 *
		 * Permet l'impression de la convocation d'information collective
		 */
		public function cohorte_infocol_imprime_second_rdv_nouveaux () {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvousImpressions' );
			$Cohorte->search (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprimeSecondRdvNouveaux',
					'nom_cohorte' => 'cohorte_infocol_imprime_second_rdv_nouveaux'
				)
			);
		}

		/**
		 * Export CSV de la
		 * Cohorte Impression Information Collective Second Rendez-vous - Nouveaux entrants
		 */
		public function exportcsv_infocol_imprime_second_rdv_nouveaux() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprimeSecondRdvNouveaux',
					'nom_cohorte' => 'cohorte_infocol_imprime_second_rdv_nouveaux'
				)
			);
		}

		/**
		 * Cohorte Impression Information Collective Second Rendez-vous - Stock
		 *
		 * Permet l'impression de la convocation d'information collective du stock
		 */
		public function cohorte_infocol_imprime_second_rdv_stock () {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvousImpressions' );
			$Cohorte->search (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprimeSecondRdvStock',
					'nom_cohorte' => 'cohorte_infocol_imprime_second_rdv_stock'
				)
			);
		}

		/**
		 * Export CSV de la
		 * Cohorte Impression Information Collective Second Rendez-vous - Stock
		 */
		public function exportcsv_infocol_imprime_second_rdv_stock() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprimeSecondRdvStock',
					'nom_cohorte' => 'cohorte_infocol_imprime_second_rdv_nouveaux'
				)
			);
		}

		/**
		 * Cohorte Information Collective - nouveaux entrants - Venu / Non venu
		 * Modifie le statut du rendez vous en venu / non venu
		 * pour les personnes ayant eu un rdv info coll nouveaux entrant
		 */
		public function cohorte_infocol_venu_nonvenu_nouveaux() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolVenuNonvenuNouveaux',
					'nom_cohorte' => 'cohorte_infocol_venu_nonvenu_nouveau'
				)
			);
		}

		/**
		 * Export CSV de la
		 * Cohorte Information Collective - nouveaux entrants - Venu / Non venu
		 */
		public function exportcsv_infocol_venu_nonvenu_nouveaux() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolVenuNonvenuNouveaux',
					'nom_cohorte' => 'cohorte_infocol_venu_nonvenu_nouveau'
				)
			);
		}

		/**
		 * Cohorte Information Collective - Stock - Venu / Non venu
		 * Modifie le statut du rendez vous en venu / non venu
		 * pour les personnes ayant eu un rdv info coll stock
		 */
		public function cohorte_infocol_venu_nonvenu_stock() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolVenuNonvenuStock',
					'nom_cohorte' => 'cohorte_infocol_venu_nonvenu_stock'
				)
			);
		}

		/**
		 * Export CSV de la
		 * Cohorte Information Collective - Stock - Venu / Non venu
		 */
		public function exportcsv_infocol_venu_nonvenu_stock() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolVenuNonvenuStock',
					'nom_cohorte' => 'cohorte_infocol_venu_nonvenu_stock'
				)
			);
		}

		/**
		 * Imprime en cohorte les informations collectives nouveaux entrants
		 */
		public function cohorte_infocol_imprime_impressions () {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvousImpressions' );

			$Cohorte->impressions(
				array(
					'modelName' => 'Rendezvous',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprime',
					'configurableQueryFieldsKey' => 'Planpauvreterendezvous.cohorte_infocol_imprime'
				)
			);
		}

		/**
		 * Imprime en cohorte les informations collectives stock
		 */
		public function cohorte_infocol_imprime_stock_impressions () {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvousImpressions' );
			$Cohorte->impressions(
				array(
					'modelName' => 'Rendezvous',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprimeStock',
					'configurableQueryFieldsKey' => 'Planpauvreterendezvous.cohorte_infocol_imprime_stock'
				)
			);
		}

		/**
		 * Imprime en cohorte les SECONDS RENDEZ-VOUS des informations collectives nouveaux entrants
		 */
		public function cohorte_infocol_imprime_second_rdv_nouveaux_impressions () {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvousImpressions' );
			$Cohorte->impressions(
				array(
					'modelName' => 'Rendezvous',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprimeSecondRdvNouveaux',
					'configurableQueryFieldsKey' => 'Planpauvreterendezvous.cohorte_infocol_imprime_second_rdv_nouveaux'
				)
			);
		}

		/**
		 * Imprime en cohorte les SECONDS RENDEZ-VOUS des informations collectives stock
		 */
		public function cohorte_infocol_imprime_second_rdv_stock_impressions () {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvousImpressions' );
			$Cohorte->impressions(
				array(
					'modelName' => 'Rendezvous',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprimeSecondRdvStock',
					'configurableQueryFieldsKey' => 'Planpauvreterendezvous.cohorte_infocol_imprime_second_rdv_stock'
				)
			);
		}

		/**
		 * Cohorte Convoqués Second Rendez-vous - Nouveaux entrants
		 * Modifie le statut du rendez vous en venu / non venu pour un rendez vous 3 en 1
		 * Si la personne est venue, créé une orientation
		 */
		public function cohorte_infocol_venu_nonvenu_second_rdv_nouveaux() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolVenuNonvenuSecondRdvNouveaux',
					'nom_cohorte' => 'cohorte_infocol_venu_nonvenu_second_rdv_nouveaux'
				)
			);
		}

		/**
		 * Export CSV de la
		 * Cohorte Convoqués Second Rendez-vous - Nouveaux entrants
		 */
		public function exportcsv_infocol_venu_nonvenu_second_rdv_nouveaux() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->exportcsv (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolVenuNonvenuSecondRdvNouveaux',
					'nom_cohorte' => 'cohorte_infocol_venu_nonvenu_second_rdv_nouveaux'
				)
			);
		}

		/**
		 * Cohorte Convoqués Second Rendez-vous - Stock
		 * Modifie le statut du rendez vous en venu / non venu pour un rendez vous 3 en 1
		 * Si la personne est venue, créé une orientation
		 */
		public function cohorte_infocol_venu_nonvenu_second_rdv_stock() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolVenuNonvenuSecondRdvStock',
					'nom_cohorte' => 'cohorte_infocol_venu_nonvenu_second_rdv_stock'
				)
			);
		}

		/**
		 * Export CSV de la
		 * Cohorte Convoqués Second Rendez-vous - Stock
		 */
		public function exportcsv_infocol_venu_nonvenu_second_rdv_stock() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->exportcsv (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolVenuNonvenuSecondRdvStock',
					'nom_cohorte' => 'cohorte_infocol_venu_nonvenu_second_rdv_stock'
				)
			);
		}

		/**
		 * Cohorte de rendez vous élaboration CER - Stock
		 */
		public function cohorte_infocol_rdv_cer_stock() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolRdvCerStock',
					'nom_cohorte' => 'cohorte_infocol_rdv_cer_stock'
				)
			);
		}

		/**
		 * Cohorte de rendez vous élaboration CER - Nouveaux entrants
		 */
		public function cohorte_infocol_rdv_cer_nouveaux() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolRdvCerNouveaux',
					'nom_cohorte' => 'cohorte_infocol_rdv_cer_nouveaux'
				)
			);
		}

		/**
		 * Export CSV de la cohorte de rendez vous élaboration CER - Stock
		 */
		public function exportcsv_infocol_rdv_cer_stock() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->exportcsv (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolRdvCerStock',
					'nom_cohorte' => 'cohorte_infocol_rdv_cer_stock'
				)
			);
		}

		/**
		 * Export CSV de la cohorte de rendez vous élaboration CER - Nouveaux entrants
		 */
		public function exportcsv_infocol_rdv_cer_nouveaux() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->exportcsv (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolRdvCerNouveaux',
					'nom_cohorte' => 'cohorte_infocol_rdv_cer_nouveaux'
				)
			);
		}

	}
?>