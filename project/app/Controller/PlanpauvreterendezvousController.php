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
					'cohorte_infocol_stock' => array('filter' => 'Search'),
					'cohorte_infocol_imprime' => array('filter' => 'Search'),
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
			'cohorte_infocol_imprime_stock' => 'select'
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
			$Cohorte->cohorte( array
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
			$Cohorte->cohorte( array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolStock',
					'nom_cohorte' => 'cohorte_infocol_stock'
				)
			);
		}

		/**
		 * Cohorte Impression Information Collective - nouveaux entrants
		 * Permet l'impression de la convocation d'information collective
		 */
		public function cohorte_infocol_imprime() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->cohorte( array( 'modelName' => 'Personne', 'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprime' ) );
		}

		/**
		 * Imprime un par un la convocation d'information collective
		 *
		 * @param int rdv_id
		 */
		public function imprime_infocol($rdv_id = null) {
			$this->WebrsaAccesses->check($rdv_id);
			$personne_id = $this->Rendezvous->personneId( $rdv_id );
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $personne_id ) );

			$pdf = $this->Rendezvous->WebrsaRendezvous->getDefaultPdf( $rdv_id, $this->Session->read( 'Auth.User.id' ) );

			if( !empty( $pdf ) ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'rendezvous-%d-%s.pdf', $rdv_id, date( 'Y-m-d' ) ) );
			}
			else {
				$this->Flash->error( 'Impossible de générer le courrier de rendez-vous.' );
				$this->redirect(array('action' => 'index', $personne_id));
			}
		}

		/**
		 * Imprime en cohorte les information collectives
		 */
		public function cohorte_infocol_imprime_impressions() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesPlanpauvreterendezvous' );
			$Cohorte->impressions(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortePlanpauvreterendezvousInfocolImprime',
					'configurableQueryFieldsKey' => 'Planpauvreterendezvous.cohorte_infocol_imprime'
				)
			);
		}
	}
?>