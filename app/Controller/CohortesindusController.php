<?php
	/**
	 * Code source de la classe CohortesindusController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::import('Sanitize');

	/**
	 * La classe CohortesindusController implémente un moteur de rechrche par indus.
	 *
	 * @deprecated since version 3.0.0
	 * @see IndusController::search() et IndusController::exportcsv()
	 *
	 * @package app.Controller
	 */
	class CohortesindusController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cohortesindus';

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
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
			'Locale',
			'Paginator',
			'Search',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cohorteindu',
			'Dossier',
			'Option',
			'Situationdossierrsa',
			'Structurereferente',
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

		public $paginate = array(
			'limit' => 20,
		);

		/**
		 * Envoi des options communes dans les vues.
		 *
		 * @return void
		 */
		public function beforeFilter() {
			parent::beforeFilter();
			$sr = $this->Structurereferente->find(
				'list',
				array(
					'fields' => array(
						'Structurereferente.lib_struc'
					),
				)
			);
			$this->set( 'sr', $sr );

			$this->set( 'natpfcre', ClassRegistry::init('Infofinanciere')->enum('natpfcre', array('type' => 'autreannulation')) );
			$this->set( 'typeparte', ClassRegistry::init('Dossier')->enum('typeparte') );
			//$this->set( 'etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa') );
			$this->set( 'etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa', array('filter' =>  $this->Situationdossierrsa->etatOuvert())) );
			$this->set( 'natpf', ClassRegistry::init('Detailcalculdroitrsa')->enum('natpf') );
			$this->set( 'type_allocation', ClassRegistry::init('Infofinanciere')->enum('type_allocation') );
			$this->set( 'rolepers', ClassRegistry::init('Prestation')->enum('rolepers') );
            $this->set( 'qual', $this->Option->qual() );
		}

		/**
		 * Moteur de recherche par indus.
		 *
		 * @return void
		 */
		public function index() {
			$comparators = array( '<' => '<' ,'>' => '>','<=' => '<=', '>=' => '>=' );

			$cmp = Set::extract( $this->request->data, 'Cohorteindu.compare' );
			$this->assert( empty( $cmp ) || in_array( $cmp, array_keys( $comparators ) ), 'invalidParameter' );

			if( !empty( $this->request->data ) ) {
				$paginate = $this->Cohorteindu->search(
					(array)$this->Session->read( 'Auth.Zonegeographique' ),
					$this->Session->read( 'Auth.User.filtre_zone_geo' ),
					$this->request->data
				);
				$paginate['limit'] = 10;
				$paginate = $this->_qdAddFilters( $paginate );
				$paginate['conditions'][] = WebrsaPermissions::conditionsDossier();

				$this->paginate = $paginate;
				$progressivePaginate = !Hash::get( $this->request->data, 'Pagination.nombre_total' );
				$cohorteindu = $this->paginate( 'Dossier', array(), array(), $progressivePaginate );

				$this->set( 'cohorteindu', $cohorteindu );
			}

			$this->set( 'cantons', $this->Gestionzonesgeos->listeCantons() );
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			$this->set( 'comparators', $comparators );

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'conditions' => array( 'Structurereferente.orientation' => 'O' ) + $this->InsertionsBeneficiaires->conditions['structuresreferentes'], 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );
		}

		/**
		 * Export CSV des enregistrements renvoyés par le moteur de recherche.
		 *
		 *@return void
		 */
		public function exportcsv(){
			$querydata = $this->Cohorteindu->search(
				(array)$this->Session->read( 'Auth.Zonegeographique' ),
				$this->Session->read( 'Auth.User.filtre_zone_geo' ),
				Hash::expand( $this->request->params['named'], '__' )
			);
			$querydata = $this->_qdAddFilters( $querydata );
			$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();

			unset( $querydata['limit'] );
			$indus = $this->Dossier->find( 'all', $querydata );


			$this->layout = '';
			$this->set( compact( 'headers', 'indus' ) );
		}
	}
?>