<?php
	/**
	 * Code source de la classe ReferentsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );
	App::uses( 'DefaultUrl', 'Default.Utility' );
	App::uses( 'File', 'Utility' );
	App::uses( 'Folder', 'Utility' );
	App::uses( 'WebrsaPermissions', 'Utility' );

	/**
	 * La classe ReferentsController ...
	 *
	 * @package app.Controller
	 */
	class ReferentsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Referents';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes',
			'Default',
			'InsertionsBeneficiaires',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
					'clotureenmasse',
					'cohorte_ajout' => array( 'filter' => 'Search' ),
					'cohorte_modif' => array( 'filter' => 'Search' ),
				),
			),
			'WebrsaParametrages',
			'Workflowscers93'
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
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Referent',
			'Option',
			'Structurereferente',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Referents:edit',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajax_getreferent',
			'exportcsv_ajout',
			'exportcsv_modif'
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'ajax_getreferent' => 'read',
			'clotureenmasse' => 'read',
			'cloturer' => 'read',
			'cohorte_ajout' => 'update',
			'cohorte_modif' => 'update',
			'delete' => 'delete',
			'edit' => 'update',
			'index' => 'read',
		);

		/**
		 * Liste des tables à ne pas prendre en compte dans les enregistrements
		 * vérifiés pour éviter les suppressions en cascade intempestives.
		 *
		 * @var array
		 */
		public $blacklist = array( 'derniersreferents' );

		/**
		 * Moteur de recherche par référents.
		 */
		public function index() {
			if( !empty( $this->request->data ) ) {
				$search = $this->request->data['Search'];
				$query = $this->Referent->WebrsaReferent->search($search);
				$query['limit'] = 20;
				$this->paginate = $query;
				$results = $this->paginate( 'Referent', array(), array(), !Hash::get($search, 'Pagination.nombre_total') );

				$this->set( compact( 'results' ) );
			}

			$options = $this->Referent->enums();
			$options['Referent']['structurereferente_id'] = $this->InsertionsBeneficiaires->structuresreferentes(
				array(
					'type' => 'optgroup',
					'conditions' => (
						false === empty( $structuresreferentes_ids )
						? array( 'Structurereferente.id' => $structuresreferentes_ids )
						: array()
					)
				)
			);
			$this->set( compact( 'options' ) );
		}

		/**
		 * Formulaire de modification d'un référent.
		 *
		 * @param integer $id La valeur de la clé primaire de l'enregistrement à
		 *	modifier.
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];

			$structuresreferentes_ids = $this->Workflowscers93->getUserStructurereferenteId( false );
			$options['Referent']['structurereferente_id'] = $this->InsertionsBeneficiaires->structuresreferentes(
				array(
					'type' => 'optgroup',
					'prefix' => false,
					'conditions' => (
						false === empty( $structuresreferentes_ids )
						? array( 'Structurereferente.id' => $structuresreferentes_ids )
						: array()
					)
				)
			);

			$options['Dernierreferent']['prevreferent_id'] = $this->Referent->find('list',
				array(
					'joins' => array(
						$this->Referent->join('Dernierreferent')
					),
					'conditions' => array(
						'Dernierreferent.referent_id = Dernierreferent.dernierreferent_id'
					),
					'order' => array('Referent.nom', 'Referent.prenom')
				)
			);
			$this->set( compact( 'options' ) );

			$bindDernierreferent = $this->Referent->hasOne['Dernierreferent'];
			$this->Referent->unbindModelAll();
			$this->Referent->bindModel( array( 'hasOne' => array( 'Dernierreferent' => $bindDernierreferent ) ) );
		}

		/**
		 * Formulaire de clôture d'un référent du parcours.
		 *
		 * @param integer $id L'id technique de l'enregistrement dans la table personnes_referents
		 * @return void
		 */
		public function cloturer( $id = null ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$referent = $this->Referent->find(
				'first',
				array(
					'conditions' => array(
						'Referent.id' => $id
					)
				)
			);
			$this->assert( !empty( $referent ), 'invalidParameter' );

			$this->set( compact( 'referent' ) );

			// Les administrateurs n'ont pas accès à la cohorte de clôture en masse
			$referer = Hash::get( $this->request->data, 'Referent.referer' );
			if( null !== $referer ) {
				$redirectUrl = Router::parse( $referer );
				$redirectUrl = array_merge(
					$redirectUrl,
					$redirectUrl['pass'],
					$redirectUrl['named']
				);
				unset( $redirectUrl['pass'], $redirectUrl['named'] );
			}
			else {
				$redirectUrl = (
					WebrsaPermissions::check( $this->name, 'clotureenmasse' )
					? array( 'controller' => 'referents', 'action' => 'clotureenmasse' )
					: array( 'controller' => 'referents', 'action' => 'index' )
				);
			}

			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( $redirectUrl );
			}

			// Tentative d'enregistrement du formulaire
			if( !empty( $this->request->data ) ) {
				$this->Referent->begin();

				$datedfdesignation = ( is_array( $this->request->data['Referent']['datecloture'] ) ? date_cakephp_to_sql( $this->request->data['Referent']['datecloture'] ) : $this->request->data['Referent']['datecloture'] );

				$count = $this->Referent->PersonneReferent->find(
					'count',
					array(
						'conditions' => array(
							'PersonneReferent.referent_id' => $id,
							'PersonneReferent.dfdesignation IS NULL'
						)
					)
				);

				$success = true;
				if( $count > 0 ) {
					$success = $this->Referent->PersonneReferent->updateAllUnBound(
						array( 'PersonneReferent.dfdesignation' => '\''.$datedfdesignation.'\'' ),
						array(
							'"PersonneReferent"."referent_id"' => $id,
							'PersonneReferent.dfdesignation IS NULL'
						)
					);
				}

				if( $success ) {
					$success = $this->Referent->updateAllUnBound(
						array( 'Referent.datecloture' => '\''.$datedfdesignation.'\'' ),
						array(
							'"Referent"."id"' => $id
						)
					) && $success;

					$this->Referent->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( $redirectUrl );
				}
				else {
					$this->Flash->error( __( 'Save->error' ) );
					$this->Referent->rollback();
				}
			}
			else {
				$this->request->data = $referent;
			}

			if( null === $referer ) {
				$this->request->data['Referent']['referer'] = $this->referer( null, true );
			}
		}


        /**
         * Fonction de clôture en masse des référents, cloisonnée selon le type de structure
         * Uniquement pour les CPDV
         */
		public function clotureenmasse() {
			$structurereferente_id = $this->Workflowscers93->getUserStructurereferenteId();
			if( !empty( $this->request->data ) ) {
				$query = $this->Referent->WebrsaReferent->search( (array)Hash::get( $this->request->data, 'Search' ) );
				$query['limit'] = 20;
                $query['conditions'][] = array( 'Referent.structurereferente_id' => $structurereferente_id );
				$this->paginate = $query;

				$progressivePaginate = !Hash::get( $this->request->data, 'Search.Pagination.nombre_total' );
				$results = $this->paginate( 'Referent', array(), array(), $progressivePaginate );

				$this->set( compact( 'results' ) );

			}

			$options = $this->Referent->enums();
			$options['Referent']['structurereferente_id'] = $this->InsertionsBeneficiaires->structuresreferentes(
				array(
					'type' => 'optgroup',
					'conditions' => (
						false === empty( $structuresreferentes_ids )
						? array( 'Structurereferente.id' => $structuresreferentes_ids )
						: array()
					)
				)
			);
			$this->set( compact( 'options' ) );
            $this->render( 'index' );
		}

		/**
		 * Cohorte d'ajout de référents
		 */
		public function cohorte_ajout() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesReferents' );
			$Cohorte->cohorte (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohorteReferentAjout',
				)
			);
		}

		/**
		 * Export CSV de la Cohorte d'ajout de référents
		 */
		public function exportcsv_ajout() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesReferents' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohorteReferentAjout',
				)
			);
		}

		/**
		 * Cohorte de modification de référents
		 */
		public function cohorte_modif() {
			$Cohorte = $this->Components->load( 'WebrsaCohortesReferents' );
			$Cohorte->cohorte (
				array
				(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohorteReferentModif',
				)
			);
		}

		/**
		 * Export CSV de la Cohorte de modification de référents
		 */
		public function exportcsv_modif() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesReferents' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohorteReferentModif',
				)
			);
		}

		/**
		 * Lecture de la table Dernierreferent pour ajax sur add_edit
		 */
		public function ajax_getreferent() {
			$id = Hash::get($this->request->data, 'id');
			$json = false;

			if (!empty($id) && preg_match('/^[\d]+$/', (string)$id)) {
				$json = $this->Referent->find('first', array(
					'fields' => array_merge(
						$this->Referent->fields(),
						$this->Referent->Dernierreferent->fields()
					),
					'joins' => array(
						$this->Referent->join('Dernierreferent')
					),
					'contain' => false,
					'conditions' => array('Referent.id' => $id),
				));
			}

			$this->set('json', Hash::flatten($json, '__'));
			$this->layout = 'ajax';
			$this->view = '/Elements/json';
		}
	}
?>