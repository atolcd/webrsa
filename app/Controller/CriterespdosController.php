<?php
	/**
	 * Code source de la classe CriterespdosController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe CriterespdosController implémente un moteur de recherche par PDOs (CG 58 et 93).
	 *
	 * @deprecated since version 3.0.0
	 * @see PropospdosController::search(), PropospdosController::exportcsv(),
	 * PropospdosController::search_possibles() et PropospdosController::exportcsv_possibles()
	 *
	 * @package app.Controller
	 */
	class CriterespdosController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Criterespdos';

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
					'nouvelles',
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
			'Criterepdo',
			'Decisionpdo',
			'Option',
			'Originepdo',
			'Personne',
			'Propopdo',
			'Situationdossierrsa',
			'Situationpdo',
			'Statutdecisionpdo',
			'Statutpdo',
			'Typenotifpdo',
			'Typepdo',
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
			'nouvelles' => 'read',
		);

		/**
		 * Envoi des options communes dans les vues.
		 *
		 * @return void
		 */
		protected function _setOptions() {
			$this->set( 'qual', $this->Option->qual() );
			$this->set( 'pieecpres', ClassRegistry::init('Personne')->enum('pieecpres') );
			$this->set( 'etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa') );
			$this->set( 'motifpdo', ClassRegistry::init('Propopdo')->enum('motifpdo') );
			$this->set( 'typenotifpdo', $this->Typenotifpdo->find( 'list' ) );
			$this->set( 'typeserins', $this->Option->typeserins() );
			$this->set( 'typepdo', $this->Typepdo->find( 'list' ) );
			$this->set( 'decisionpdo', $this->Decisionpdo->find( 'list' ) );
			$this->set( 'originepdo', $this->Originepdo->find( 'list' ) );

			$this->set( 'statutlist', $this->Statutpdo->find( 'list' ) );
			$this->set( 'situationlist', $this->Situationpdo->find( 'list' ) );
			$this->set( 'statutdecisionlist', $this->Statutdecisionpdo->find( 'list' ) );

			$this->set( 'rolepers', ClassRegistry::init('Prestation')->enum('rolepers') );
			$this->set( 'typevoie', $this->Option->typevoie() );
			$this->set( 'qual', $this->Option->qual() );

			$this->set( 'gestionnaire', $this->User->find(
					'list',
					array(
						'fields' => array(
							'User.nom_complet'
						),
						'conditions' => array(
							'User.isgestionnaire' => 'O'
						)
					)
				)
			);

			$options = (array)Hash::get( $this->Propopdo->enums(), 'Propopdo' );
			$options = Hash::insert( $options, 'Suiviinstruction.typeserins', $this->Option->typeserins() );
			$this->set( compact( 'options' ) );
		}

		/**
		 * Moteur de recherche par PDOs.
		 *
		 * @deprecated see Propospdos::search()
		 *
		 * @return void
		 */
		public function index( ) {
			if( !empty( $this->request->data ) ) {
				$paginate = $this->Criterepdo->search(
					(array)$this->Session->read( 'Auth.Zonegeographique' ),
					$this->Session->read( 'Auth.User.filtre_zone_geo' ),
					$this->request->data
				);

				$paginate['limit'] = 10;
				$paginate = $this->_qdAddFilters( $paginate );

				$this->paginate = $paginate;
				$progressivePaginate = !Hash::get( $this->request->data, 'Pagination.nombre_total' );
				$criterespdos = $this->paginate( 'Propopdo', array(), array(), $progressivePaginate );

				$this->set( 'criterespdos', $criterespdos );
			}

			$this->set( 'cantons', $this->Gestionzonesgeos->listeCantons() );
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );
			$this->_setOptions();

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );
		}

		/**
		 * Moteur de recherche par nouvelles PDOs.
		 *
		 * @return void
		 */
		public function nouvelles() {
			if( !empty( $this->request->data ) ) {
				$querydata = $this->Criterepdo->listeDossierPDO(
					(array)$this->Session->read( 'Auth.Zonegeographique' ),
					$this->Session->read( 'Auth.User.filtre_zone_geo' ),
					$this->request->data
				);

				$querydata['limit'] = 10;
				$querydata = $this->_qdAddFilters( $querydata );

				$this->paginate = array( 'Personne' => $querydata );
				$progressivePaginate = !Hash::get( $this->request->data, 'Pagination.nombre_total' );
				$criterespdos = $this->paginate( 'Personne', array(), array(), $progressivePaginate );

				$this->set( 'criterespdos', $criterespdos );
			}


			$this->_setOptions();
			$this->set( 'cantons', $this->Gestionzonesgeos->listeCantons() );
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			// Précise les options des états de dossiers :
			$this->set( 'etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa', array('filter' =>  $this->Situationdossierrsa->etatAttente())) );

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );

			$this->render( 'liste' );
		}

		/**
		 * Export du tableau en CSV.
		 *
		 * @deprecated see Propospdos::exportcsv()
		 *
		 * @return void
		 */
		public function exportcsv() {
			$querydata = $this->Criterepdo->search(
				(array)$this->Session->read( 'Auth.Zonegeographique' ),
				$this->Session->read( 'Auth.User.filtre_zone_geo' ),
				Hash::expand( $this->request->params['named'], '__' )
			);

			unset( $querydata['limit'] );
			$querydata = $this->_qdAddFilters( $querydata );

			$pdos = $this->Propopdo->find( 'all', $querydata );

			$this->_setOptions();

			$this->layout = '';
			$this->set( compact( 'pdos' ) );
		}
	}
?>