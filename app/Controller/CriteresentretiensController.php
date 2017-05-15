<?php
	/**
	 * Code source de la classe CriteresentretiensController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe CriteresentretiensController ...
	 *
	 * @deprecated since 3.0.00
	 * @see Entretiens::search() et Entretiens::exportcsv()
	 *
	 * @package app.Controller
	 */
	class CriteresentretiensController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Criteresentretiens';

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
			'Default2',
			'Search',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Critereentretien',
			'Entretien',
			'Option',
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
		 *
		 */
		public function _setOptions() {
			$this->set( 'options', (array)Hash::get( $this->Entretien->enums(), 'Entretien' ) );
// 			$this->set( 'structs', $this->Entretien->Structurereferente->listOptions() );
			$this->set( 'structs', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referents', $this->Entretien->Referent->WebrsaReferent->listOptions() );
		}

		/**
		 *
		 */
		public function index() {
			$this->Gestionzonesgeos->setCantonsIfConfigured();

			if( !empty( $this->request->data ) ) {

				if( !empty( $this->request->data['Entretien']['referent_id'] )) {
					$referentId = suffix( $this->request->data['Entretien']['referent_id'] );
					$this->request->data['Entretien']['referent_id'] = $referentId;
				}

				$paginate = $this->Critereentretien->search( $this->request->data );

				$paginate = $this->Gestionzonesgeos->completeQuery( $paginate, 'Entretien.structurereferente_id' );
				$paginate['conditions'][] = WebrsaPermissions::conditionsDossier();
				$paginate = $this->_qdAddFilters( $paginate );
				$paginate['limit'] = 10;

				$this->paginate = $paginate;
				$progressivePaginate = !Hash::get( $this->request->data, 'Pagination.nombre_total' );
				$entretiens = $this->paginate( 'Entretien', array(), array(), $progressivePaginate );

				$this->set( 'entretiens', $entretiens );
			}

			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );
			$this->_setOptions();

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'conditions' => array( 'Structurereferente.orientation' => 'O' ) + $this->InsertionsBeneficiaires->conditions['structuresreferentes'], 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );
		}

		/**
		 * Export du tableau en CSV
		 */
		public function exportcsv() {
			$querydata = $this->Critereentretien->search( Hash::expand( $this->request->params['named'], '__' ) );

			unset( $querydata['limit'] );
			$querydata = $this->Gestionzonesgeos->completeQuery( $querydata, 'Entretien.structurereferente_id' );
			$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();
			$querydata = $this->_qdAddFilters( $querydata );

			$entretiens = $this->Entretien->find( 'all', $querydata );

			$this->layout = '';
			$this->set( compact( 'entretiens' ) );
			$this->_setOptions();
		}
	}
?>