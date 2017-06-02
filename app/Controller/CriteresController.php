<?php
	/**
	 * Code source de la classe CriteresController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe CriteresController ...
	 *
	 * @deprecated since 3.0.00
	 * @see Orientsstructs::search() et Orientsstructs::exportcsv()
	 *
	 * @package app.Controller
	 */
	class CriteresController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Criteres';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'RequestHandler',
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
			'Cake1xLegacy.Ajax',
			'Csv',
			'Search',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Critere',
			'Option',
			'Orientstruct',
			'Typeorient',
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
			'ajaxstruc',
			'constReq',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'ajaxstruc' => 'read',
			'exportcsv' => 'read',
			'index' => 'read',
		);

		/**
		*
		*/
		public function beforeFilter() {
			ini_set('max_execution_time', 0);
			ini_set('memory_limit', '1024M');
			return parent::beforeFilter();
		}


		/**
		 *
		 */
		protected function _setOptions() {
			$typeservice = ClassRegistry::init( 'Serviceinstructeur' )->find( 'list', array( 'fields' => array( 'lib_service' ) ) );
			$this->set( 'typeservice', $typeservice );
			$this->set( 'rolepers', ClassRegistry::init('Prestation')->enum('rolepers') );
			// Structures référentes
			$datas = Set::merge( $this->request->data, Hash::expand( $this->request->params['named'], '__' ) );
			$typeorient_id = Set::classicExtract( $datas, 'Critere.typeorient_id' );
			$conditions = array();
			$conditions = array( 'Structurereferente.actif' => 'O' );
			if( !empty( $typeorient_id ) ) {
				$conditions = array(
					'Structurereferente.typeorient_id' => $typeorient_id
				);
			}

			$this->set( 'sr', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'list', 'conditions' => array( 'Structurereferente.orientation' => 'O' ) + $this->InsertionsBeneficiaires->conditions['structuresreferentes'] ) ) );
			$this->set( 'typeorient', $this->InsertionsBeneficiaires->typesorients() );

			$this->set( 'statuts', $this->Orientstruct->enum( 'statut_orient' ) );
			$this->set( 'natpf', ClassRegistry::init('Detailcalculdroitrsa')->enum('natpf') );
			$this->set( 'qual', $this->Option->qual() );

			$this->set( 'referents', ClassRegistry::init( 'Referent' )->WebrsaReferent->listOptions( ) );
			$this->set( 'options', $this->Orientstruct->enums() );


			//Ajout des structures et référents orientants
			$this->set( 'refsorientants', ClassRegistry::init( 'Referent' )->WebrsaReferent->listOptions() );
			$this->set( 'structsorientantes', ClassRegistry::init( 'Structurereferente' )->listOptions( array( 'orientation' => 'O' ) ) );

			if( Configure::read( 'Cg.departement' ) == 93 ) {
				$enums = array();
				foreach( array_keys( $this->Orientstruct->Personne->Dossierep->themesCg() ) as $tableName ) {
					$modeleDecision = Inflector::classify( "decisions{$tableName}" );
					$enums[$modeleDecision] = array( 'decision' => $this->Orientstruct->Personne->Dossierep->Passagecommissionep->{$modeleDecision}->enum( 'decision' ) );
				}
				$this->set( compact( 'enums' ) );
			}

			$this->set( 'act', ClassRegistry::init('Activite')->enum('act') );
		}

		/**
		* Ajax pour la structure référente liée au type d'orientation
		*/

		protected function _selectStructs( $typeorientid = null ) {
			$conditions = array();

			if( !empty( $typeorientid ) ) {
				$conditions = array(
					'Structurereferente.typeorient_id' => $typeorientid
				);
			}

			$structs = $this->Orientstruct->Structurereferente->find(
				'all',
				array(
					'fields' => array( 'Structurereferente.id', 'Structurereferente.lib_struc' ),
					'conditions' => $conditions,
					'recursive' => -1
				)
			);

			return $structs;
		}

		/**
		*
		*/

		public function ajaxstruc() { // FIXME
			Configure::write( 'debug', 0 );
			$structs = $this->_selectStructs( Set::classicExtract( $this->request->data, 'Critere.typeorient_id' ) );

			$options = array( '<option value=""></option>' );
			foreach( $structs as $struct ) {
				$options[] = '<option value="'.$struct['Structurereferente']['id'].'">'.$struct['Structurereferente']['lib_struc'].'</option>';
			}
			echo implode( '', $options );
			$this->render( null, 'ajax' );
		}

		/**
		 * Moteur de recherche par orientation.
		 */
		public function index() {
			if( !empty( $this->request->data ) ) {
				$reorientationEp = ( Configure::read( 'Cg.departement' ) == 93 && Hash::get( $this->request->data, 'Orientstruct.origine' ) == 'reorientation' );

				$paginate = $this->Critere->search(
					$this->request->data,
					$reorientationEp
				);

				$paginate = $this->Gestionzonesgeos->completeQuery( $paginate, 'Orientstruct.structurereferente_id' );
				$paginate['conditions'][] = WebrsaPermissions::conditionsDossier();
				$paginate = $this->_qdAddFilters( $paginate );

				$paginate['limit'] = 10;

				$this->paginate = $paginate;
				$progressivePaginate = !Hash::get( $this->request->data, 'Pagination.nombre_total' );
				$orients = $this->paginate( 'Orientstruct', array(), array(), $progressivePaginate );

				$this->set( 'orients', $orients );
				$this->set( 'reorientationEp', $reorientationEp );
			}

			$this->Gestionzonesgeos->setCantonsIfConfigured();
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'conditions' => array( 'Structurereferente.orientation' => 'O' ) + $this->InsertionsBeneficiaires->conditions['structuresreferentes'], 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );

			$this->_setOptions();
		}

		/**
		 * Export du tableau en CSV
		 */
		public function exportcsv() {
			$search = Hash::expand( $this->request->params['named'], '__' );
			$reorientationEp = ( Configure::read( 'Cg.departement' ) == 93 && Hash::get( $search, 'Orientstruct.origine' ) == 'reorientation' );

			$querydata = $this->Critere->search(
				$search,
				$reorientationEp
			);

			$querydata = $this->Gestionzonesgeos->completeQuery( $querydata, 'Orientstruct.structurereferente_id' );
			$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();
			$querydata = $this->_qdAddFilters( $querydata );

			unset( $querydata['limit'] );

			$orients = $this->Orientstruct->find( 'all', $querydata );

			$this->layout = '';
			$this->_setOptions();
			$this->set( compact( 'orients', 'reorientationEp' ) );
		}
	}
?>