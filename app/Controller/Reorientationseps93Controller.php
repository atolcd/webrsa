<?php
	/**
	 * Fichier source de la classe Reorientationseps93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	* Gestion des saisines d'EP pour les réorientations proposées par les structures
	* référentes pour le conseil départemental du département 93.
	 *
	 * @package app.Controller
	 */
	class Reorientationseps93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Reorientationseps93';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'InsertionsBeneficiaires',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array('index')
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default',
			'Default2',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Reorientationep93',
			'Cohortetransfertpdv93',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Reorientationseps93:edit',
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
			'delete' => 'delete',
			'edit' => 'update',
			'index' => 'read',
		);

		/**
		 *
		 */
		protected function _setOptions() {

		}

		/**
		 *
		 */
		public function index() {
			$searchData = Set::classicExtract( $this->request->data, 'Search' );
			$searchMode = Set::classicExtract( $searchData, 'Reorientationep93.mode' );

			if( !empty( $searchData ) ) {
				$conditions = array( 'Dossierep.themeep' => 'reorientationseps93' );

				if( $searchMode == 'traite' ) {
					$conditions[]['passagescommissionseps.etatdossierep'] = array( 'traite', 'annule', 'reporte' );

					$searchDossierepSeanceepId = Set::classicExtract( $searchData, 'Dossierep.commissionep_id' );
					if( !empty( $searchDossierepSeanceepId ) ) {
						$conditions[]['passagescommissionseps.commissionep_id'] = $searchDossierepSeanceepId;
					}
				}
				else {
					$conditions[]['NOT']['passagescommissionseps.etatdossierep'] = array( 'traite', 'annule', 'reporte' );
				}

				$conditions = array(
					'Dossierep.id IN ( '.$this->Reorientationep93->Dossierep->Passagecommissionep->sq(
						array(
							'alias' => 'passagescommissionseps',
							'fields' => array(
								'passagescommissionseps.dossierep_id'
							),
							'conditions' => array(
								$conditions
							)
						)
					).' )'
				);

				$this->paginate = array(
					'contain' => array(
						'Orientstruct' => array(
							'Typeorient',
							'Structurereferente',
						),
						'Typeorient',
						'Motifreorientep93',
						'Structurereferente',
						'Dossierep' => array(
							'Passagecommissionep' => array(
								'Commissionep',
								'Decisionreorientationep93' => array(
									'Typeorient',
									'Structurereferente',
								),
							),
							'Personne',
						),
					),
					'conditions' => $conditions,
					'order' => array( 'Reorientationep93.created DESC' ),
					'limit' => 10
				);

				$this->set( 'reorientationseps93', $this->paginate( $this->Reorientationep93 ) );
			}

			// INFO: containable ne fonctionne pas avec les find('list')
			$commissionseps = array();
			$tmpSeanceseps = $this->Reorientationep93->Dossierep->Passagecommissionep->Commissionep->find(
				'all',
				array(
					'fields' => array(
						'Commissionep.id',
						'Commissionep.dateseance',
						'Ep.name'
					),
					'contain' => array(
						'Ep'
					),
					'order' => array( 'Ep.name ASC', 'Commissionep.dateseance DESC' )
				)
			);

			if( !empty( $tmpSeanceseps ) ) {
				foreach( $tmpSeanceseps as $key => $commissionep ) {
					$commissionseps[$commissionep['Ep']['name']][$commissionep['Commissionep']['id']] = $commissionep['Commissionep']['dateseance'];
				}
			}

			$options = Set::merge(
				$this->Reorientationep93->Dossierep->enums(),
				$this->Reorientationep93->Dossierep->Passagecommissionep->Decisionreorientationep93->enums(),
				array( 'Dossierep' => array( 'commissionep_id' => $commissionseps ) )
			);
			$this->set( compact( 'options' ) );

			$view = implode( '_', Hash::filter( (array)array( 'index', $searchMode ) ) );
			$this->render( $view );
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
		 * INFO: on passe orientstruct_id (add) ou Reorientationep93.id (edit)
		 *
		 * @param integer $id
		 * @throws NotFoundException
		 */
		protected function _add_edit( $id = null ) {
			if( $this->action == 'add' ) {
				$personne_id = $this->Reorientationep93->Orientstruct->field( 'personne_id', array( 'Orientstruct.id' => $id ) );
			}
			else {
				$personne_id = $this->Reorientationep93->personneId( $id );
			}
			if( empty( $personne_id ) ) {
				throw new NotFoundException( null );
			}
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$dossier_id = $this->Reorientationep93->Orientstruct->Personne->dossierId( $personne_id );
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'controller' => 'orientsstructs', 'action' => 'index', $personne_id ) );
			}

			// Liste des options disponibles
			$options = array(
				'Reorientationep93' => array(
					'structurereferente_id' => $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'list', 'conditions' => array( 'Structurereferente.orientation' => 'O' ) + $this->InsertionsBeneficiaires->conditions['structuresreferentes'] ) ),
					'typeorient_id' => $this->Reorientationep93->Typeorient->listOptions(),
					'motifreorientep93_id' => $this->Reorientationep93->Motifreorientep93->find( 'list' ),
					'referent_id' => $this->Reorientationep93->Referent->WebrsaReferent->listOptions()
				)
			);

			$options = Set::merge(
				$this->Reorientationep93->enums(),
				$options
			);

			$this->set( compact( 'options' ) );
			// Fin




			if( !empty( $this->request->data ) ) {
				// FIXME: dans les contrôleurs des autres thèmes aussi
				$success = true;
				$this->Reorientationep93->begin();
				$this->request->data['Dossierep']['themeep'] = Inflector::tableize( $this->modelClass );
				if( $this->action == 'add' ) {
					$this->Reorientationep93->Orientstruct->id = $this->request->data['Reorientationep93']['orientstruct_id'];
					$this->request->data['Dossierep']['personne_id'] = $this->Reorientationep93->Orientstruct->field( 'personne_id' );
					$dossierep['Dossierep'] = $this->request->data['Dossierep'];
					$this->Reorientationep93->Dossierep->create( $dossierep );
					$success = $this->Reorientationep93->Dossierep->save( null, array( 'atomic' => false ) );
					$this->request->data['Reorientationep93']['dossierep_id'] = $this->Reorientationep93->Dossierep->id;
				}

				$reorientationep93['Reorientationep93'] = $this->request->data['Reorientationep93'];
				$reorientationep93['Reorientationep93']['user_id'] = $this->Session->read( 'Auth.User.id' );
				$this->Reorientationep93->create( $reorientationep93 );
				$success = $this->Reorientationep93->save( null, array( 'atomic' => false ) ) && $success;

				if( $success ) {
					$this->Reorientationep93->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->Jetons2->release( $dossier_id );
					$this->redirect( array( 'controller' => 'orientsstructs', 'action' => 'index', $personne_id ) );
				}
				else {
					$this->Reorientationep93->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $this->Reorientationep93->find(
					'first',
					array(
						'contain' => false,
						'conditions' => array( 'Reorientationep93.id' => $id )
					)
				);
				$this->assert( !empty( $this->request->data ), 'error404' );

				// Formattage des id pour les listes liées
				$this->request->data['Reorientationep93']['referent_id'] = implode(
					'_',
					array(
						$this->request->data['Reorientationep93']['structurereferente_id'],
						$this->request->data['Reorientationep93']['referent_id']
					)
				);

				$this->request->data['Reorientationep93']['structurereferente_id'] = implode(
					'_',
					array(
						$this->request->data['Reorientationep93']['typeorient_id'],
						$this->request->data['Reorientationep93']['structurereferente_id']
					)
				);
			}
			else if( $this->action == 'add' ) {
				$this->request->data = array(
					'Reorientationep93' => array(
						'orientstruct_id' => $id
					)
				);
			}

			// Lecture de valeurs
			if( $this->action == 'add' ) {
				// Retour à l'index d'orientsstrucs s'il n'est pas possible d'ajouter une réorientation
				if( !$this->Reorientationep93->ajoutPossible( $personne_id ) ) {
					$this->Jetons2->release( $dossier_id );
					$this->Flash->error( 'Impossible d\'ajouter une orientation pour cette personne.' );
					$this->redirect( array( 'controller' => 'orientsstructs', 'action' => 'index', $personne_id ) );
				}

				$this->set( 'nb_orientations', $this->Reorientationep93->Orientstruct->WebrsaOrientstruct->rgorientMax( $personne_id ) );
				$this->set( 'toppersdrodevorsa', $this->Reorientationep93->Orientstruct->Personne->Calculdroitrsa->field( 'toppersdrodevorsa', array( 'Calculdroitrsa.personne_id' => $personne_id ) ) );
				$this->set( 'personne_id', $personne_id );
			}
			else {
				$reorientationep93 = $this->Reorientationep93->find(
					'first',
					array(
						'contain' => array(
							'Orientstruct',
							'Dossierep',
						),
						'conditions' => array( 'Reorientationep93.id' => $id )
					)
				);

				if( !( empty( $reorientationep93['Dossierep']['etatdossierep'] ) || $reorientationep93['Dossierep']['etatdossierep'] == 'cree' ) ) {
					$this->Jetons2->release( $dossier_id );
					$this->Flash->error( 'Cette demande de réorientation ne peut pas être modifiée' );
					$this->redirect( array( 'controller' => 'orientsstructs', 'action' => 'index', $reorientationep93['Orientstruct']['personne_id'] ) ); // FIXME
				}

				$this->set( 'nb_orientations', $reorientationep93['Orientstruct']['rgorient'] );
				$this->set( 'toppersdrodevorsa', $this->Reorientationep93->Orientstruct->Personne->Calculdroitrsa->field( 'toppersdrodevorsa', array( 'Calculdroitrsa.personne_id' => $reorientationep93['Orientstruct']['personne_id'] ) ) );
				$this->set( 'personne_id', $reorientationep93['Orientstruct']['personne_id'] );
			}

			$this->_setOptions();
			$this->set( 'urlmenu', '/orientsstructs/index/'.$personne_id );
			$this->render( 'add_edit' );
		}

		/**
		 * Suppression d'un dossier d'EP pour cette thématique dès lors que ce dossier ne possède pas
		 * de passage en commission EP.
		 *
		 * @param integer $id L'id de l'entrée dans la table de la thématique.
		 * @return void
		 */
		public function delete( $id ) {
			$reorientationep93 = $this->Reorientationep93->find(
				'first',
				array(
					'conditions' => array(
						"Reorientationep93.id" => $id
					),
					'contain' => array(
						'Dossierep' => array(
							'Passagecommissionep'
						)
					)
				)
			);

			// L'enregistrement existe bien
			$this->assert( !empty( $reorientationep93 ), 'error404' );

			// Le dossier ne possède pas encore de passage en commission
			$this->assert( empty( $reorientationep93['Dossierep']['Passagecommissionep'] ), 'error500' );

			$personne_id = $this->Reorientationep93->personneId( $id );
			$this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );

			$dossier_id = $this->Reorientationep93->Orientstruct->Personne->dossierId( $personne_id );
			$this->Jetons2->get( $dossier_id );

			$this->Reorientationep93->begin();
			if( $this->Reorientationep93->Dossierep->delete( $reorientationep93['Reorientationep93']['dossierep_id'] ) ) {
				$this->Reorientationep93->commit();
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Reorientationep93->rollback();
				$this->Flash->error( __( 'Delete->error' ) );
			}

			$this->Jetons2->release( $dossier_id );
			$this->redirect( $this->referer() );
		}
	}
?>