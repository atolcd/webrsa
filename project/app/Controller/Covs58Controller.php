<?php
	/**
	 * Code source de la classe Covs58Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe Covs58Controller ...
	 *
	 * @package app.Controller
	 */
	class Covs58Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Covs58';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
				),
			),
			'Gedooo.Gedooo',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Search.SearchForm',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cov58',
			'Option',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Covs58:edit',
			'view' => 'Covs58:index',
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
			'add' => 'create',
			'decisioncov' => 'read',
			'delete' => 'delete',
			'edit' => 'update',
			'impressiondecision' => 'update',
			'impressionpv' => 'update',
			'index' => 'read',
			'ordredujour' => 'read',
			'view' => 'read',
			'visualisationdecisions' => 'read',
		);

		public $etatsActions = array(
			'cree' => array(
				'dossierseps::choose',
				'covs58::edit',
				'covs58::delete'
			),
			'associe' => array(
				'dossierscovs58::choose',
				'covs58::printConvocationBeneficiaire',
				'covs58::printConvocationsBeneficiaires',
				'covs58::printOrdreDuJour',
				'covs58::ordredujour',
				'covs58::edit',
				'covs58::delete',
				'covs58::decisioncov'
			),
			'traite' => array(
				'covs58::printOrdreDuJour',
				'covs58::ordredujour',
				'covs58::impressionpv',
				'covs58::visualisationdecisions',
				'covs58::printDecision',
				'covs58::printConvocationBeneficiaire',
				'covs58::printConvocationsBeneficiaires',
			),
			'finalise' => array(
				'covs58::printOrdreDuJour',
				'covs58::ordredujour',
				'covs58::impressionpv',
				'covs58::visualisationdecisions',
				'covs58::printDecision',
				'covs58::printConvocationBeneficiaire',
				'covs58::printConvocationsBeneficiaires',
			),
			'valide' => array(
				'covs58::ordredujour',
				'covs58::printConvocationBeneficiaire',
				'covs58::printConvocationsBeneficiaires',
				'covs58::printOrdreDuJour',
				'covs58::ordredujour',
				'covs58::visualisationdecisions',
				'covs58::delete',
				'covs58::decisioncov'
			),
			'annule' => array()
		);
		/**
		*
		*/

		public function beforeFilter() {
			return parent::beforeFilter();
		}

		/**
		*
		*/

		protected function _setOptions() {
			$themescovs58 = $this->Cov58->Passagecov58->Dossiercov58->Themecov58->find('list');

			$options = Hash::merge(
				$this->Cov58->enums(),
				$this->Cov58->Passagecov58->enums(),
				$this->Cov58->Passagecov58->Dossiercov58->enums()
			);

			$typesorients = $this->Cov58->Passagecov58->Dossiercov58->Propoorientationcov58->Structurereferente->Typeorient->listOptions();
			$structuresreferentes = $this->Cov58->Passagecov58->Dossiercov58->Propoorientationcov58->Structurereferente->list1Options();
			$referents = $this->Cov58->Passagecov58->Dossiercov58->Propoorientationcov58->Structurereferente->Referent->WebrsaReferent->listOptions();
			$sitescovs58 = $this->Cov58->Sitecov58->find( 'list', array( 'fields' => array( 'name' ) ) );

			$referentsorientants = $this->Cov58->Passagecov58->Dossiercov58->Propoorientationcov58->Structurereferente->Referent->find( 'list' );

			$decisionscovs = array( 'accepte' => 'Accepté', 'refus' => 'Refusé', 'ajourne' => 'Ajourné' );
			$this->set(compact('decisionscovs'));

			foreach( $this->Cov58->Passagecov58->Dossiercov58->Themecov58->themes() as $theme ) {
 				$model = Inflector::classify( $theme );
				$modeleDecision = Inflector::classify( "decision{$theme}" );

				$options = Hash::merge(
					$options,
					$this->Cov58->Passagecov58->Dossiercov58->{$model}->enums(),
					$this->Cov58->Passagecov58->{$modeleDecision}->enums()
				);
			}
			$this->set(compact('options', 'typesorients', 'structuresreferentes', 'referents', 'sitescovs58', 'referentsorientants' ));
		}

		/**
		 * Moteur de recherche de COV.
		 */
		public function index() {
			$search = (array)Hash::get( $this->request->data, 'Search' );
			if( !empty( $search ) ) {
				$query = $this->Cov58->search( $search );
				$query['order'] = array( 'Cov58.datecommission DESC' );
				$query['limit'] = 10;
				$this->paginate = $query;
				$results = $this->paginate( $this->Cov58 );
			}
			else {
				$this->request->data = array(
					'Search' => array(
						'datecommission_from' => strtotime( '-1 week' ),
						'datecommission_to' => strtotime( 'now' )
					)
				);
			}

			$options = array_merge(
				$this->Cov58->enums(),
				$this->Cov58->Sitecov58->enums()
			);
			$options['Cov58']['sitecov58_id'] = $this->Cov58->Sitecov58->find( 'list', array( 'contain' => false ) );
			foreach( array_keys( $options['Cov58']['etatcov'] ) as $etatcov ) {
				if( !in_array( $etatcov, array( 'cree', 'associe', 'finalise' ) ) ) {
					unset( $options['Cov58']['etatcov'][$etatcov] );
				}
			}

			$this->set( compact( 'results', 'options' ) + array( 'etatsActions' => $this->etatsActions ) );
		}

		/**
		 *
		 * @param integer $id
		 */
		public function visualisationdecisions( $id ) {
			$query = array(
				'conditions' => array(
					'Cov58.id' => $id
				),
				'contain' => array(
					'Sitecov58.name'
				)
			);
			$cov58 = $this->Cov58->find( 'first', $query );
			$this->assert( !empty( $cov58 ), 'error404' );

			// On s'assure que le commission soit dans un état accepté
			$this->assert( in_array( 'covs58::'.__FUNCTION__, (array)Hash::get( $this->etatsActions, $cov58['Cov58']['etatcov'] ) ) );

			// Préparation des résultats
			$this->loadModel( 'WebrsaDossiercov58' );
			$options = $this->WebrsaDossiercov58->options();

			$this->loadModel( 'Allocataire' );
			$types = array(
				'Calculdroitrsa' => 'LEFT OUTER',
				'Prestation' => 'LEFT OUTER',
				'Adressefoyer' => 'LEFT OUTER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'LEFT OUTER',
				'Detaildroitrsa' => 'LEFT OUTER'
			);
			$base = $this->Allocataire->searchQuery();
			$base['fields'] = array_merge(
				array(
					'Personne.id',
					'Dossiercov58.themecov58',
					'Passagecov58.id',
					'Passagecov58.etatdossiercov'
				),
				$base['fields'],
				ConfigurableQueryFields::getModelsFields(
					array(
						$this->Cov58,
						$this->Cov58->Passagecov58,
						$this->Cov58->Passagecov58->Dossiercov58
					)
				)
			);
			$base['joins'][] = $this->Cov58->Passagecov58->Dossiercov58->Personne->join( 'Dossiercov58', array( 'type' => 'INNER' ) );
			$base['joins'][] = $this->Cov58->Passagecov58->Dossiercov58->join( 'Passagecov58', array( 'type' => 'INNER' ) );
			$base['joins'][] = $this->Cov58->Passagecov58->join( 'Cov58', array( 'type' => 'INNER' ) );
			$base['conditions']['Cov58.id'] = $id;

			$fields = array();
			$results = array();

			// Récupération des thématiques de COV
			$themes = array_keys( (array)Hash::get( $options, 'Dossiercov58.themecov58' ) );
			foreach( $themes as $theme ) {
				$modelName = Inflector::classify( $theme );
				$modelDecisionName = Inflector::classify( "decisions{$theme}" );

				$webrsaModelName = 'Webrsa'.Inflector::classify( $theme );
				$webrsaModelDecisionName = 'Webrsa'.Inflector::classify( "decisions{$theme}" );
				$query = $base;

				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Cov58->Passagecov58->Dossiercov58->{$modelName},
							$this->Cov58->Passagecov58->{$modelDecisionName}
						)
					)
				);
				$query['joins'][] = $this->Cov58->Passagecov58->Dossiercov58->join( $modelName, array( 'type' => 'INNER' ) );
				$query['joins'][] = $this->Cov58->Passagecov58->join( $modelDecisionName, array( 'type' => 'INNER' ) );

				$this->loadModel( $webrsaModelName );
				$query = $this->{$webrsaModelName}->completeQuery( $query );

				$this->loadModel( $webrsaModelDecisionName );
				$query = $this->{$webrsaModelDecisionName}->completeQuery( $query );

				$configurePath = "ConfigurableQuery.{$this->name}.{$this->action}.{$theme}";
				$keys = array( $configurePath );
				$query = ConfigurableQueryFields::getFieldsByKeys( $keys, $query );
				$fields[$theme] = (array)Configure::read( $configurePath );

				$this->Cov58->Passagecov58->Dossiercov58->Personne->forceVirtualFields = true;
				$results[$theme] = $this->Cov58->Passagecov58->Dossiercov58->Personne->find( 'all', $query );
			}

			// On ne peut imprimer la décision que dans certains cas
			foreach( $fields as $theme => $themeFields ) {
				$themeFields = Hash::normalize( $themeFields );
				foreach( $themeFields as $themeField => $params ) {
					$params = (array)$params;

					if( strpos( $themeField, '/Covs58/impressiondecision' ) === 0 ) {
						// TODO: si ça existait déjà...
						$params['disabled'] = '( "#Passagecov58.etatdossiercov#" != "traite" ) || ( "#Dossiercov58.themecov58#" == "propocontratinsertioncov58" )';
					}

					$themeFields[$themeField] = $params;
				}
				$fields[$theme] = $themeFields;
			}

			$this->set( compact( 'cov58', 'results', 'fields', 'options' ) );
		}

		/**
		*
		*/

		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		*
		*/

		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Formulaire d'ajout ou de modification de COV.
		 *
		 * @param integer $id
		 */
		protected function _add_edit( $id = null ) {
            if (isset($this->request->data['Cancel'])) {
                $this->redirect(array('action' => 'index'));
            }

			if( !empty( $this->request->data ) ) {
				$this->Cov58->begin();
				$this->Cov58->create( $this->request->data );
				$success = $this->Cov58->save( null, array( 'atomic' => false ) );

				if( $success ) {
					$this->Cov58->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'view', $this->Cov58->id ) );
				}
				else {
					$this->Cov58->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $this->Cov58->find(
					'first',
					array(
						'conditions' => array( 'Cov58.id' => $id ),
						'contain' => false
					)
				);
				$this->assert( !empty( $this->request->data ), 'error404' );
				$this->set('cov58_id', $id);
			}

			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		/**
		*
		*/

		public function view( $cov58_id = null ) {
			$cov58 = $this->Cov58->find(
				'first', array(
					'conditions' => array( 'Cov58.id' => $cov58_id ),
					'contain' => array(
						'Sitecov58'
					)
				)
			);
			$this->set('cov58', $cov58);
			$this->_setOptions();

			// Dossiers à passer en séance, par thème traité
			$themes = array_keys( $this->Cov58->themesTraites( $cov58_id ) );

			$this->set(compact('themes'));
			$dossiers = array();
			$countDossiers = 0;

			foreach( $themes as $theme ) {
				$class = Inflector::classify( $theme );
				$qdListeDossier = $this->Cov58->Passagecov58->Dossiercov58->{$class}->qdListeDossier();

				if ( isset( $qdListeDossier['fields'] ) ) {
					$qd['fields'] = $qdListeDossier['fields'];
				}
				$qd['conditions'] = array( 'Passagecov58.cov58_id' => $cov58_id, 'Dossiercov58.themecov58' => Inflector::tableize( $class ) );
				$qd['joins'] = $qdListeDossier['joins'];
				$qd['contain'] = false;

				$qd['fields'][] = $this->Cov58->Passagecov58->Dossiercov58->Personne->Foyer->sqVirtualField( 'enerreur' );

				$dossiers[$theme] = $this->Cov58->Passagecov58->Dossiercov58->find(
					'all',
					$qd
				);
// debug($dossiers);
				$countDossiers += count($dossiers[$theme]);
			}

			$dossierscovs58 = $this->Cov58->Passagecov58->find(
				'all',
				array(
					'conditions' => array(
						'Passagecov58.cov58_id' => $cov58_id
					),
					'contain' => array(
						'Dossiercov58' => array(
							'Personne' => array(
								'Foyer' => array(
									'fields' => array(
										$this->Cov58->Passagecov58->Dossiercov58->Personne->Foyer->sqVirtualField( 'enerreur' )
									),
									'Adressefoyer' => array(
										'conditions' => array(
											'Adressefoyer.rgadr' => '01'
										),
										'Adresse'
									)
								)
							)
						)
					)
				)
			);

			$this->set( compact( 'dossierscovs58' ) );
			$this->set(compact('dossiers'));

			$this->set(compact('countDossiers'));
		}

		/**
		*
		*/

		public function decisioncov ( $cov58_id ) {
			$cov58 = $this->Cov58->find(
				'first',
				array(
					'conditions' => array(
						'Cov58.id' => $cov58_id,
					)
				)
			);

			$this->assert( !empty( $cov58 ), 'error404' );
			// On s'assure que le commission ne soit pas dans un état final
			$this->assert( !in_array( $cov58['Cov58']['etatcov'], array( 'traite', 'finalise', 'annule', 'reporte' ) ) );

			$dossiers = $this->Cov58->dossiersParListe( $cov58_id );

			if( !empty( $this->request->data ) ) {
				$this->Cov58->begin();
				$success = $this->Cov58->saveDecisions( $cov58_id, $this->request->data );

				if( $success ) {
					$this->Cov58->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'view', $cov58_id ) );
				}
				else {
					$this->Cov58->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			if( empty( $this->request->data ) ) {
				$this->request->data = $dossiers;
			}

			$this->set( compact( 'cov58', 'dossiers' ) );
			$this->set( 'cov58_id', $cov58_id);
			$this->_setOptions();
		}

		/**
		*
		*/

		public function delete( $id ) {
			$success = $this->Cov58->delete( $id );
			if( $success ) {
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Flash->error( __( 'Delete->error' ) );
			}
			$this->redirect( array( 'action' => 'index' ) );
		}

		/**
		*
		*/

		public function ordredujour( $cov58_id ) {
			$pdf = $this->Cov58->getPdfOrdreDuJour( $cov58_id );

			if( $pdf ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, 'OJ.pdf' );
			}
			else {
				$this->Flash->error( 'Impossible de générer l\'ordre du jour de la COV' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		*
		*/

		public function impressionpv( $cov58_id ) {
			$pdf = $this->Cov58->getPdfPv( $cov58_id );
			if( $pdf ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, 'pv.pdf' );
			}
			else {
				$this->Flash->error( 'Impossible de générer le PV de la COV' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		*
		*/

		public function impressiondecision( $passagecov58_id ) {

			$passagecov58 = $this->Cov58->Passagecov58->find(
				'first',
				array(
					'conditions' => array(
						'Passagecov58.id' => $passagecov58_id
					),
					'contain' => array(
						'Dossiercov58' => array(
							'Themecov58'
						)
					)
				)
			);
			$modeleTheme = $passagecov58['Dossiercov58']['themecov58'];
			$modeleTheme = Inflector::classify( $modeleTheme );

			$pdf = $this->Cov58->Passagecov58->Dossiercov58->{$modeleTheme}->getPdfDecision( $passagecov58_id );

			if( $pdf ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, 'pv.pdf' );
			}
			else {
				$this->Flash->error( 'Impossible de générer le courrier de décision de la COV' );
				$this->redirect( $this->referer() );
			}
		}
	}
?>