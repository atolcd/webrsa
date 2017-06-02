<?php
	/**
	 * Code source de la classe CohortespdosController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe CohortespdosController implémente un moteur de recherche par PDOs (CG 93).
	 *
	 * @deprecated since 3.0.00
	 *
	 * @package app.Controller
	 */
	class CohortespdosController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cohortespdos';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes' => array(
				'avisdemande',
			),
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Search.Filtresdefaut' => array(
				'avisdemande',
				'valide',
			),
			'Search.SearchPrg' => array(
				'actions' => array(
					'avisdemande' => array(
						'filter' => 'Search'
					),
					'valide',
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
			'Paginator',
			'Search',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Canton',
			'Cohortepdo',
			'Decisionpdo',
			'Dossier',
			'Option',
			'Personne',
			'Propopdo',
			'Situationdossierrsa',
			'Traitementtypepdo',
			'Typenotifpdo',
			'Typepdo',
			'User',
			'Zonegeographique',
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
			'avisdemande' => 'read',
			'exportcsv' => 'read',
			'valide' => 'update'
		);

		public $paginate = array(
			'limit' => 20,
		);

		/**
		 * Envoi des options communes dans les vues.
		 *
		 * @return void
		 */
		protected function _setOptions(){

			$this->set( 'typepdo', $this->Typepdo->find( 'list' ) );
			$this->set( 'decisionpdo', $this->Decisionpdo->find( 'list' ) );
			$this->set( 'typenotifpdo', $this->Typenotifpdo->find( 'list' ) );
			$this->set( 'traitementtypepdo', $this->Traitementtypepdo->find( 'list' ) );
			$this->set( 'pieecpres', ClassRegistry::init('Personne')->enum('pieecpres') );
			$this->set( 'motifpdo', ClassRegistry::init('Propopdo')->enum('motifpdo') );
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

			$options = array(
				'cantons' => $this->Gestionzonesgeos->listeCantons(),
				'etatdosrsa' => ClassRegistry::init('Dossier')->enum('etatdosrsa', array('filter' =>  $this->Situationdossierrsa->etatAttente())),
				'mesCodesInsee' => $this->Gestionzonesgeos->listeCodesInsee(),
				'toppersdrodevorsa' => $this->Option->toppersdrodevorsa( true ),
				'rolepers' => ClassRegistry::init('Prestation')->enum('rolepers'),
				'qual' => $this->Option->qual()
			);
			$this->set( 'options', $options );
		}

		/**
		 * Moteur de recherche par PDOs non traitées (formulaire de cohorte).
		 *
		 * @return void
		 */
		public function avisdemande() {
			$this->_index( 'Decisionpdo::nonvalide' );
		}

		/**
		 * Moteur de recherche par PDOs traitées (cohorte de visualisation).
		 *
		 * @return void
		 */
		public function valide() {
			$this->_index( 'Decisionpdo::valide' );
		}

		/**
		 * Moteur de recherche par PDOs.
		 *
		 * @return void
		 */
		protected function _index( $statutValidationAvis = null ) {
			$this->assert( !empty( $statutValidationAvis ), 'invalidParameter' );

			if( !empty( $this->request->data ) ) {
				if( !empty( $this->request->data['Propopdo'] ) ) {

					$data = Set::extract( '/Propopdo[user_id=/[0-9]+/]', $this->request->data );

					$dossiers_ids = Set::extract(  $data, 'Propopdo.{n}.dossier_id'  );
					$this->Cohortes->get( $dossiers_ids );

					$valid = $this->Propopdo->saveAll( $data, array( 'validate' => 'only', 'atomic' => false ) );

					if( $valid ) {
						$this->Dossier->begin();
						$saved = $this->Propopdo->saveAll( $data, array( 'validate' => 'first', 'atomic' => false ) );
						if( $saved ) {
							$this->Dossier->commit();
							unset( $this->request->data['Propopdo'] );
							$this->Cohortes->release( $dossiers_ids );
						}
						else {
							$this->Dossier->rollback();
						}
					}
				}

				if( ( $statutValidationAvis == 'Decisionpdo::nonvalide' ) || ( ( $statutValidationAvis == 'Decisionpdo::valide' ) && !empty( $this->request->data ) ) ) {
					$queryData = $this->Cohortepdo->search(
						$statutValidationAvis,
						(array)$this->Session->read( 'Auth.Zonegeographique' ),
						$this->Session->read( 'Auth.User.filtre_zone_geo' ),
						$this->request->data,
						( $this->Cohortes->active() ? $this->Cohortes->sqLocked() : null )
					);

					$queryData['limit'] = 10;
					$this->paginate = array( 'Personne' => $queryData );
					$progressivePaginate = !Hash::get( $this->request->data, 'Search.Pagination.nombre_total' );
					$cohortepdo = $this->paginate( 'Personne', array(), array(), $progressivePaginate );

					// Obtention des jetons lorsque l'on est en cohortes
					if( $this->Cohortes->active() ) {
						$dossiers_ids = Set::extract(  $cohortepdo, '{n}.Dossier.id'  );
						$this->Cohortes->get( $dossiers_ids );
					}

					$this->set( 'cohortepdo', $cohortepdo );
				}
			}

			$this->_setOptions();
			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );

			switch( $statutValidationAvis ) {
				case 'Decisionpdo::nonvalide':
					$this->set( 'pageTitle', 'Nouvelles demandes PDOs' );
					$this->render( 'formulaire' );
					break;
				case 'Decisionpdo::valide':
					$this->set( 'pageTitle', 'PDOs validés' );
					$this->render( 'visualisation' );
					break;
			}
		}

		/**
		 * Export CSV des enregistrements renvoyés par le moteur de recherche.
		 *
		 * @return void
		 */
		public function exportcsv() {
			$params = $this->Cohortepdo->search(
				'Decisionpdo::valide',
				(array)$this->Session->read( 'Auth.Zonegeographique' ),
				$this->Session->read( 'Auth.User.filtre_zone_geo' ),
				Hash::expand( $this->request->params['named'], '__' ),
				( $this->Cohortes->active() ? $this->Cohortes->sqLocked() : null )
			);
			$this->_setOptions();

			unset( $params['limit'] );
			$pdos = $this->Propopdo->Personne->find( 'all', $params );

			$this->layout = '';
			$this->set( compact( 'headers', 'pdos' ) );
		}
	}
?>