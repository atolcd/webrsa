<?php
	/**
	 * Code source de la classe CriteresciController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe CriteresciController ...
	 *
	 * @deprecated since 3.0.00
	 * @see Contratsinsertion::search() et Contratsinsertion::exportcsv()
	 *
	 * @package app.Controller
	 */
	class CriteresciController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Criteresci';

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
			'Cake1xLegacy.Ajax',
			'Csv',
			'Romev3',
			'Search',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cohorteci',
			'Action',
			'Catalogueromev3',
			'Contratinsertion',
			'Option',
			'Referent',
			'Situationdossierrsa',
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
			'ajaxreferent',
			'constReq',
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
		 *
		 */
		protected function _setOptions() {
// 			$struct = ClassRegistry::init( 'Structurereferente' )->find( 'list', array( 'fields' => array( 'id', 'lib_struc' ) ) );
// 			$this->set( 'struct', $struct );
			$this->set( 'struct', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referents', $this->Contratinsertion->Referent->WebrsaReferent->listOptions() );

			$this->set( 'rolepers', ClassRegistry::init('Prestation')->enum('rolepers') );
			$personne_suivi = $this->Contratinsertion->find(
				'list',
				array(
					'fields' => array(
						'Contratinsertion.pers_charg_suivi',
						'Contratinsertion.pers_charg_suivi'
					),
					'order' => 'Contratinsertion.pers_charg_suivi ASC',
					'group' => 'Contratinsertion.pers_charg_suivi',
				)
			);
			$this->set( 'personne_suivi', $personne_suivi );
			$this->set( 'natpf', ClassRegistry::init('Detailcalculdroitrsa')->enum('natpf') );

			$this->set( 'decision_ci', ClassRegistry::init('Contratinsertion')->enum('decision_ci') );
			$this->set( 'duree_engag', $this->Option->duree_engag() );
			$this->set( 'numcontrat', (array)Hash::get( $this->Contratinsertion->enums(), 'Contratinsertion' ) );

			$this->set( 'action', $this->Action->find( 'list' ) );

			$forme_ci = array();
			if( Configure::read( 'nom_form_ci_cg' ) == 'cg93' ) {
				$forme_ci = array( 'S' => 'Simple', 'C' => 'Complexe' );
			}
			else if( Configure::read( 'nom_form_ci_cg' ) == 'cg66' ) {
				$forme_ci = array( 'S' => 'Simple', 'C' => 'Particulier' );
			}
			$this->set( 'forme_ci', $forme_ci );

 			$this->set( 'etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa') );
			$this->set( 'qual', $this->Option->qual() );

			$this->set(
				'trancheage',
				array(
					'0_24' => '- 25 ans',
					'25_34' => '25 - 34 ans',
					'35_44' => '35 - 44 ans',
					'45_54' => '45 - 54 ans',
					'55_999' => '+ 55 ans'
				)
			);

			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$options = (array)Hash::get( $this->viewVars, 'options' );
				$options['Personne']['etat_dossier_orientation'] = $this->Contratinsertion->Personne->enum( 'etat_dossier_orientation' );
				$this->set( compact( 'options' ) );
			}
		}


		/**
		 *
		 */
		public function index() {
			$this->Gestionzonesgeos->setCantonsIfConfigured();

			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			$params = $this->request->data;

			if( !empty( $params ) ) {
				$paginate = $this->Cohorteci->search(
					null,
					$this->request->data
				);

				$paginate = $this->Gestionzonesgeos->completeQuery( $paginate, 'Contratinsertion.structurereferente_id' );
				$paginate['conditions'][] = WebrsaPermissions::conditionsDossier();
				$paginate = $this->_qdAddFilters( $paginate );

				$paginate['limit'] = 10;

				$progressivePaginate = !Set::classicExtract( $this->request->data, 'Contratinsertion.paginationNombreTotal' );
				$this->paginate = $paginate;

				$contrats = $this->paginate( 'Contratinsertion', array(), array(), $progressivePaginate  );

				$this->set( 'contrats', $contrats );
			}
			else {
				// Valeurs par défaut des filtres
				$progressivePaginate = SearchProgressivePagination::enabled( $this->name, $this->action );
				if( !is_null( $progressivePaginate ) ) {
					$this->request->data['Contratinsertion']['paginationNombreTotal'] = !$progressivePaginate;
				}
			}

			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			/// Population du select référents liés aux structures
			$conditions = array();
			$structurereferente_id = Set::classicExtract( $this->request->data, 'Contratinsertion.structurereferente_id' );

			if( !empty( $structurereferente_id ) ) {
				$conditions['Referent.structurereferente_id'] = Set::classicExtract( $this->request->data, 'Contratinsertion.structurereferente_id' );
			}

			$referents = $this->Referent->find(
				'all',
				array(
					'recursive' => -1,
					'fields' => array( 'Referent.id', 'Referent.qual', 'Referent.nom', 'Referent.prenom' ),
					'conditions' => $conditions
				)
			);

			if( !empty( $referents ) ) {
				$ids = Set::extract( $referents, '/Referent/id' );
				$values = Set::format( $referents, '{0} {1} {2}', array( '{n}.Referent.qual', '{n}.Referent.nom', '{n}.Referent.prenom' ) );
				$referents = array_combine( $ids, $values );
			}

			$this->set( 'referents', $referents );

			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$typesorientsNiveau0 = $this->InsertionsBeneficiaires->typesorients( array( 'conditions' => array( 'Typeorient.parentid IS NULL' ) + $this->InsertionsBeneficiaires->conditions['typesorients'] ) );
				$this->set( compact( 'typesorientsNiveau0' ) );
			}

			$this->set( 'typesorients', $this->InsertionsBeneficiaires->typesorients( array( 'empty' => ( Configure::read( 'Cg.departement' ) != 58 ) ) ) );
			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );

			$this->_setOptions();

			$options = (array)Hash::get( $this->viewVars, 'options' );
			// Ajout des éléments de listes déroulantes propres au CG 93
			if( Configure::read( 'Cg.departement' ) == 93 ) {
				$options = Hash::merge(
					$options,
					$this->Contratinsertion->Cer93->enums(),
					$this->Contratinsertion->Cer93->options( array( 'autre' => true, 'find' => true ) ),
					$this->Catalogueromev3->dependantSelects()
				);
				$options['Expprocer93']['metierexerce_id'] = $this->Contratinsertion->Cer93->Expprocer93->Metierexerce->find( 'list' );
				$options['Expprocer93']['secteuracti_id'] = $this->Contratinsertion->Cer93->Expprocer93->Secteuracti->find( 'list' );
			}
			$this->set( compact( 'options' ) );
		}

		/**
		 * Export du tableau en CSV
		 */
		public function exportcsv() {
			ini_set( 'max_execution_time', 0 );
			ini_set( 'memory_limit', '512M' );

			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			$querydata = $this->Cohorteci->search(
				null,
				Hash::expand( $this->request->params['named'], '__' )
			);

			$querydata = $this->Gestionzonesgeos->completeQuery( $querydata, 'Contratinsertion.structurereferente_id' );
			$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();
			$querydata = $this->_qdAddFilters( $querydata );

			unset( $querydata['limit'] );

			$contrats = $this->Contratinsertion->find( 'all', $querydata );

			$this->layout = '';
			$this->set( compact( 'contrats' ) );
			$this->_setOptions();
			$options = (array)Hash::get( $this->viewVars, 'options' );
			$options = Hash::merge( $options, $this->Contratinsertion->Cer93->enums() );
			$this->set( compact( 'options' ) );
		}
	}
?>