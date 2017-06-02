<?php
	/**
	 * Code source de la classe CriteresrdvController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe CriteresrdvController implémente un moteur de recherche par rendez-vous (CG 58, 66 et 93).
	 *
	 * @deprecated since 3.0.00
	 * @see Rendezvous::search() et Rendezvous::exportcsv()
	 *
	 * @package app.Controller
	 */
	class CriteresrdvController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Criteresrdv';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
				),
			),
			'Workflowscers93',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
			'Paginator',
			'Search',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Critererdv',
			'Option',
			'Rendezvous',
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
			'index' => 'read',
		);

		/**
		 * Changement du temps d'exécution maximum et de la quantité de mémoire maximale.
		 *
		 * @return void
		 */
		public function beforeFilter() {
			ini_set( 'max_execution_time', 0 );
			ini_set( 'memory_limit', '256M' );
			parent::beforeFilter();
		}

		/**
		 * Envoi des options communes dans les vues.
		 *
		 * @return void
		 */
		protected function _setOptions() {
			$this->set( 'statutrdv', $this->Rendezvous->Statutrdv->find( 'list' ) );
// 			$this->set( 'struct', $this->Rendezvous->Structurereferente->listOptions() );

			$this->set( 'struct', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );

			$typerdv = $this->Rendezvous->Typerdv->find( 'list', array( 'fields' => array( 'id', 'libelle' ) ) );
			$this->set( 'typerdv', $typerdv );
			$this->set( 'permanences', $this->Rendezvous->Permanence->find( 'list' ) );
			$this->set( 'referents', $this->Rendezvous->Referent->WebrsaReferent->listOptions() );

			$this->set( 'natpf', ClassRegistry::init('Detailcalculdroitrsa')->enum('natpf') );
			$this->set( 'rolepers', ClassRegistry::init('Prestation')->enum('rolepers') );
			$this->set( 'qual', $this->Option->qual() );

            if( Configure::read( 'Rendezvous.useThematique' ) ) {
                $thematiquesrdvs = $this->Rendezvous->Thematiquerdv->find( 'list', array( 'fields' => array( 'Thematiquerdv.id', 'Thematiquerdv.name', 'Thematiquerdv.typerdv_id' ) ) );
                $this->set( compact( 'thematiquesrdvs' ) );
            }

			$this->set( 'toppersdrodevorsa', $this->Option->toppersdrodevorsa(true) );
		}

		/**
		 * Moteur de recherche par rendez-vous.
		 *
		 * @return void
		 */
		public function index() {
			if( !empty( $this->request->data ) ) {
				$querydata = $this->Critererdv->search( $this->request->data );

				$querydata['limit'] = 10;
				$querydata = $this->Gestionzonesgeos->completeQuery( $querydata, 'Rendezvous.structurereferente_id' );
				$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();
				$querydata = $this->_qdAddFilters( $querydata );

				$this->paginate = array( 'Rendezvous' => $querydata );
				$progressivePaginate = !Hash::get( $this->request->data, 'Pagination.nombre_total' );
				$rdvs = $this->paginate( 'Rendezvous', array(), array(), $progressivePaginate );

                if( Configure::read( 'Rendezvous.useThematique' ) ) {
                    $rdvs = $this->Rendezvous->containThematique( $rdvs );
                }

				$this->set( 'rdvs', $rdvs );
			}

			$this->set( 'cantons', $this->Gestionzonesgeos->listeCantons() );
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );
			$this->_setOptions();

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );
		}

		/**
		 * Export CSV des RDVs.
		 *
		 * @return void
		 */
		public function exportcsv() {
			$querydata = $this->Critererdv->search( Hash::expand( $this->request->params['named'], '__' ) );

			unset( $querydata['limit'] );
			$querydata = $this->Gestionzonesgeos->completeQuery( $querydata, 'Rendezvous.structurereferente_id' );
			$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();
			$querydata = $this->_qdAddFilters( $querydata );

			$rdvs = $this->Rendezvous->find( 'all', $querydata );

            if( Configure::read( 'Rendezvous.useThematique' ) ) {
                $rdvs = $this->Rendezvous->containThematique( $rdvs );
            }

            if( Configure::read( 'Rendezvous.useThematique' ) ) {
                $this->set( 'useThematiques', $this->Rendezvous->Thematiquerdv->used() );
            }

			$this->layout = '';
			$this->_setOptions();
			$this->set( 'typevoie', $this->Option->typevoie() );
			$this->set( compact( 'rdvs' ) );
		}
	}
?>