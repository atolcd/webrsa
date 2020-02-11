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
	 * La classe PlanpauvreterendezvousController ...
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
					'cohorte_infocol_stock' => array('filter' => 'Search'),
					'cohorte_infocol_imprime' => array('filter' => 'Search'),
					'cohorte_infocol_imprime_stock' => array('filter' => 'Search'),
					'cohorte_infocol_venu_nonvenu_nouveau' => array('filter' => 'Search'),
					'cohorte_infocol_venu_nonvenu_stock' => array('filter' => 'Search'),
					'cohorte_infocol_second_rdv_nouveaux' => array('filter' => 'Search'),
					'cohorte_infocol_second_rdv_stock' => array('filter' => 'Search'),
					'cohorte_infocol_imprime_second_rdv_nouveaux' => array('filter' => 'Search'),
					'cohorte_infocol_imprime_second_rdv_stock' => array('filter' => 'Search'),
					'cohorte_infocol_imprime_second_rdv_nouveaux' => array('filter' => 'Search'),
					'cohorte_infocol_imprime_second_rdv_stock' => array('filter' => 'Search'),
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
			'cohorte_infocol_imprime' => 'select',
			'cohorte_infocol_imprime_stock' => 'select',
			'cohorte_infocol_imprime_second_rdv_nouveaux' => 'select',
			'cohorte_infocol_imprime_second_rdv_stock' => 'select',
			'cohorte_infocol_venu_nonvenu_nouveau' => 'update',
			'cohorte_infocol_venu_nonvenu_stock' => 'update',
			'cohorte_infocol_second_rdv_nouveaux' => 'update',
			'cohorte_infocol_second_rdv_stock' => 'update'
		);

		public function _setOptions() {
			$this->set( 'options', (array)Hash::get( $this->Planpauvreterendezvous->enums(), 'Planpauvreterendezvous' ) );
		}

		/**
		 * Cohorte Information Collective - nouveaux entrants
		 * Créé un rendez vous d'information collective pour les nouveaux entrants
		 */
		public function cohorte_infocol() {
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
		 * Cohorte Information Collective - Stock
		 * Créé un rendez vous d'information collective pour le stock
		 */
		public function cohorte_infocol_stock() {
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
		 * Cohorte Impression Information Collective - Nouveaux entrants
		 *
		 * Permet l'impression de la convocation d'information collective
		 */
		public function cohorte_infocol_imprime() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte (
				array
				(
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
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprimeStock',
					'nom_cohorte' => 'cohorte_infocol_imprime_stock'
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
		 * Cohorte Impression Information Collective - Nouveaux entrants
		 *
		 * Permet l'impression de la convocation d'information collective
		 */
		public function cohorte_infocol_imprime_second_rdv_nouveaux () {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprimeSecondRdvNouveaux',
					'nom_cohorte' => 'cohorte_infocol_imprime_second_rdv_nouveaux'
				)
			);
		}

		/**
		 * Cohorte Impression Information Collective - Stock
		 *
		 * Permet l'impression de la convocation d'information collective du stock
		 */
		public function cohorte_infocol_imprime_second_rdv_stock () {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprimeSecondRdvStock',
					'nom_cohorte' => 'cohorte_infocol_imprime_second_rdv_stock'
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
		 * Imprime un par un la convocation d'information collective
		 *
		 * @param int rdv_id
		 */
		public function imprime ($rdv_id = null) {
			$this->WebrsaAccesses->check($rdv_id);
			$personne_id = $this->Rendezvous->personneId( $rdv_id );
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $personne_id ) );

			$pdf = $this->Rendezvous->WebrsaRendezvous->getDefaultPdf( $rdv_id, $this->Session->read( 'Auth.User.id' ) );

			if( !empty( $pdf ) ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'rendezvous-%d-%s.pdf', $rdv_id, date( 'Y-m-d' ) ) );
			}
			else {
				$this->Flash->error( __d ('planpauvreterendezvous', 'Erreur.Impression') );
				$this->redirect(array('action' => 'index', $personne_id));
			}
		}

		/**
		 * Imprime en cohorte les informations collectives nouveaux entrants
		 */
		public function cohorte_infocol_imprime_impressions () {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->impressions(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprime',
					'configurableQueryFieldsKey' => 'Planpauvreterendezvous.cohorte_infocol_imprime'
				)
			);
		}

		/**
		 * Imprime en cohorte les informations collectives stock
		 */
		public function cohorte_infocol_imprime_stock_impressions () {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->impressions(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprimeStock',
					'configurableQueryFieldsKey' => 'Planpauvreterendezvous.cohorte_infocol_imprime_stock'
				)
			);
		}

		/**
		 * Imprime en cohorte les SECONDS RENDEZ-VOUS des informations collectives nouveaux entrants
		 */
		public function cohorte_infocol_imprime_second_rdv_nouveaux_impressions () {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->impressions(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprimeSecondRdvNouveaux',
					'configurableQueryFieldsKey' => 'Planpauvreterendezvous.cohorte_infocol_imprime_second_rdv_nouveaux'
				)
			);
		}

		/**
		 * Imprime en cohorte les SECONDS RENDEZ-VOUS des informations collectives stock
		 */
		public function cohorte_infocol_imprime_second_rdv_stock_impressions () {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->impressions(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprimeSecondRdvStock',
					'configurableQueryFieldsKey' => 'Planpauvreterendezvous.cohorte_infocol_imprime_second_rdv_stock'
				)
			);
		}
	}
?>