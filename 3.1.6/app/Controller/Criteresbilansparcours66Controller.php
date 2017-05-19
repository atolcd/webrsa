<?php
	/**
	 * Fichier source de la classe Criteresbilansparcours66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Moteur de recherche de bilans de parcours (CG 66).
	 *
	 * @deprecated since version 3.0.0
	 * @see Bilansparcours66::search() et Bilansparcours66::exportcsv()
	 * @package app.Controller
	 */
	class Criteresbilansparcours66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Criteresbilansparcours66';

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
			'Default',
			'Default2',
			'Locale',
			'Search',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Criterebilanparcours66',
			'Bilanparcours66',
			'Option',
			'Referent',
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
			'exportcsv',
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
			$this->set( 'options', (array)Hash::get( $this->Bilanparcours66->enums(), 'Bilanparcours66' ) );
            $this->set( 'struct', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referents', $this->Bilanparcours66->Referent->WebrsaReferent->listOptions() );
		}

		/**
		*
		*/

		public function index() {
			$this->Gestionzonesgeos->setCantonsIfConfigured();

			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			if( !empty( $this->request->data ) ) {
				$data = $this->request->data;

				if( !empty( $data['Bilanparcours66']['referent_id'] )) {
					$referentId = suffix( $data['Bilanparcours66']['referent_id'] );
					$data['Bilanparcours66']['referent_id'] = $referentId;
				}

				$queryData = $this->Criterebilanparcours66->search( $mesCodesInsee, $this->Session->read( 'Auth.User.filtre_zone_geo' ), $data );
				$queryData['limit'] = 10;
				$queryData['conditions'][] = WebrsaPermissions::conditionsDossier();

				$this->paginate = $queryData;
				$progressivePaginate = !Hash::get( $this->request->data, 'Pagination.nombre_total' );
				$bilansparcours66 = $this->paginate( $this->Bilanparcours66, array(), array(), $progressivePaginate );

				$this->set( 'bilansparcours66', $bilansparcours66 );
			}

			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			$this->_setOptions();

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );

			$this->render( 'index' );
		}

		/**
		* Export du tableau en CSV
		*/

		public function exportcsv() {
			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			$querydata = $this->Criterebilanparcours66->search( $mesCodesInsee, $this->Session->read( 'Auth.User.filtre_zone_geo' ), Hash::expand( $this->request->params['named'], '__' ) );
			unset( $querydata['limit'] );
			$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();
			$bilansparcours66 = $this->Bilanparcours66->find( 'all', $querydata );

			$this->_setOptions();
			$this->layout = ''; // FIXME ?
			$this->set( compact( 'bilansparcours66' ) );
		}
	}
?>