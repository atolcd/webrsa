<?php
	/**
	 * Code source de la classe CohortesrendezvousController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe CohortesrendezvousController ...
	 *
	 * @package app.Controller
	 */
	class CohortesrendezvousController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cohortesrendezvous';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'Cohortes' => array(
				'cohorte'
			),
			'InsertionsBeneficiaires',
			'Search.SearchFiltresdefaut' => array(
				'cohorte',
			),
			'Search.SearchPrg' => array(
				'actions' => array(
					'cohorte' => array(
						'filter' => 'Search',
						'ajax' => false
					),
				),
			),
			'Workflowscers93',
			'Filtresdefaut'
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Allocataires',
			'Cake1xLegacy.Ajax',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Search.SearchForm',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cohorterendezvous',
			'Rendezvous',
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
			'cohorte' => 'update',
			'exportcsv' => 'read'
		);

		/**
		 * Retourne le query à utiliser dans la méthode cohorte, que l'appel ait
		 * été fait en ajax ou non.
		 *
		 * @param array $search Les filtres venant du moteur de recherche
		 * @param array $fieldsConfigureKeys Le nom des clés de configuration dans
		 *	lesquelles récupérer les champs nécessaires.
		 * @return array
		 */
		protected function _getQuery( array $search, array $fieldsConfigureKeys ) {
			$query = $this->Cohorterendezvous->cohorte( $search );
			$query = $this->Cohortes->qdConditions( $query );
			$query = $this->Allocataires->completeSearchQuery( $query );

			$query = ConfigurableQueryFields::getFieldsByKeys( $fieldsConfigureKeys, $query );

			$query['limit'] = 10;
			$query['conditions'][] = array( 'Rendezvous.structurereferente_id' => $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'ids' ) ) );

			return $query;
		}

		/**
		 * Retourne les options nécessaires aux différentes vues, en fonction des
		 * clés de params.
		 *
		 * @param array $params Les types d'options à inclure. Par défaut: array(
		 *	'find' => true, 'allocataires' => true, 'ajax' => false )
		 * @return array
		 */
		protected function _getOptions( array $params = array() ) {
			$params += array( 'find' => true, 'allocataires' => true, 'ajax' => false );
			$result = $this->Rendezvous->enums();

			if( $params['allocataires'] === true ) {
				$result = Hash::merge( $result, $this->Allocataires->options() );
			}

			if( $params['find'] === true ) {
				$result = Hash::merge(
					$result,
					array(
						'Rendezvous' => array(
							'statutrdv_id' => $this->Rendezvous->Statutrdv->find( 'list', array('conditions' => array('actif' => 1)) ),
							'typerdv_id' => $this->Rendezvous->Typerdv->find( 'list' ),
							'permanence_id' => $this->Rendezvous->Permanence->find( 'list' )
						)
					)
				);

				if( $params['ajax'] === false && Configure::read( 'Rendezvous.useThematique' ) === true ) {
					$result['RendezvousThematiquerdv']['thematiquerdv_id'] = $this->Rendezvous->Thematiquerdv->find( 'list', array( 'fields' => array( 'Thematiquerdv.id', 'Thematiquerdv.name', 'Thematiquerdv.typerdv_id' ) ) );
				}
			}

			$result = Hash::merge(
				$result,
				$this->Allocataires->optionsSessionCommunautesr( 'Rendezvous' )
			);

			return $result;
		}

		/**
		 *
		 */
		public function cohorte() {
			$this->Workflowscers93->assertUserExterne();

			if( $this->request->is( 'ajax' ) ) {
				// TODO: à placer dans la méthode saveCohorte
				$json = array( 'success' => true, 'errors' => array() );

				$dossiers_ids = Hash::extract( $this->request->data, 'Cohorte.Hidden.Dossier.{n}.id' );
				// a. Acquisition des jetons
				$this->Cohortes->get( $dossiers_ids );

				// b. Traitement de l'enregistrement à modifier
				$offset = Hash::get( $this->request->data, 'Cohorte.Changed.offset' );
				$data = Hash::get( $this->request->data, "Cohorte.Changed.Rendezvous.{$offset}" );

				$this->Rendezvous->begin();
				$query = array(
					'conditions' => array(
						'Rendezvous.id' => Hash::get( $data, 'id' ),
						'Rendezvous.personne_id' => Hash::get( $data, 'personne_id' ),
					),
					'contain' => array(
						'Thematiquerdv' => array(
							'fields' => array( 'id' )
						)
					)
				);
				$record = $this->Rendezvous->find( 'first', $query );
				$record['Thematiquerdv'] = array(
					'Thematiquerdv' => Hash::extract( $record, 'Thematiquerdv.{n}.id' )
				);

				$record['Rendezvous']['statutrdv_id'] = Hash::get( $data, 'statutrdv_id' );
				if( Configure::read( 'Cg.departement' ) != 58 ) {
					unset( $record['Rendezvous']['rang'] );
				}

				$this->Rendezvous->create( $record );
				$success = $this->Rendezvous->save( null, array( 'atomic' => false ) );
				$json['success'] = $json['success'] && $success;

				if( !$success ) {
					$json['errors'][$offset] = array(
						'statutrdv_id' => Hash::extract( $this->Rendezvous->validationErrors, '{s}.{n}' )
					);
				}

				// En cas de succès, on renvoie le nouveau résultat en Ajax/HTML
				if( $json['success'] ) {
					$this->Rendezvous->commit();
					$dossier_id = Hash::get( $this->request->data, "Cohorte.Changed.Dossier.{$offset}.id" );
					$this->Cohortes->release( array( $dossier_id ) );

					$rendezvous_id = Hash::get( $data, 'id' );
					$rendezvous_ids = Hash::extract( $this->request->data, 'Cohorte.Hidden.Rendezvous.{n}.id' );
					array_remove( $rendezvous_ids, $rendezvous_id );

					$search = (array)Hash::get( $this->request->data, 'Search' );
					$query = $this->_getQuery( $search, array( "{$this->name}.cohorte.fields", "{$this->name}.cohorte.innerTable" ) );
					$query['conditions'][] = array(
						'NOT' => array(
							'Rendezvous.id' => $rendezvous_ids
						)
					);

					// Il faut garder le tri et la page
					$query = $this->Components->load( 'Search.SearchPaginator' )->setPaginationOrder( $query );
					$page = Hash::get( $this->request->params, 'named.page' );
					$limit = $query['limit'];
					if( empty( $page ) || $page < 1 ) {
						$page = 1;
					}
					$query['offset'] = ( $page -1 ) * $query['limit'];
					$query['limit'] = 1;

					$this->Rendezvous->forceVirtualFields = true;
					$results = $this->Rendezvous->find( 'all', $query );

					// TODO: début mise à jour des liens de pagination
					// Il me faut envoyer à la vue, pagination normale et progressive:
					$nombre_total = Hash::get( $search, 'Pagination.nombre_total' );
					$Paginator = $this->Components->load( 'Search.SearchPaginator' )->getPaginator( !$nombre_total );
					$options = $Paginator->mergeOptions( $this->Rendezvous->alias );
					$options = $Paginator->validateSort( $this->Rendezvous, $options );
					$options = $Paginator->checkLimit( $options );

					// Puisque les enregistrements déjà présents ne sont plus comptabilisés, on les ajoute à la main
					$hiddenCount = max( count( Hash::get( $this->request->data, "Cohorte.Hidden.Rendezvous" ) ) - 1, 0 );

					if( $nombre_total ) {
						$count = $this->Rendezvous->find( 'count', $query ) + $hiddenCount;
					}
					// Sinon, on s'arrange pour que les enregistrements déjà présents ne soient pas pris en compte
					else {
						$query['limit'] = $limit + 1;
						$query['fields'] = array( 'Rendezvous.id' );
						$count = count( (array)$this->Rendezvous->find( 'all', $query ) );
						$count = min( $limit + 1, $count + $hiddenCount ) + ( ( $page - 1 ) * $limit );
					}

					$defaults = $Paginator->getDefaults( $this->Rendezvous->alias );
					unset( $defaults[0] );

					$pageCount = intval( ceil( $count / $limit ) );
					$page = max( min( $page, $pageCount ), 1 );

					$paging = array(
						'page' => $page,
						'current' => min( $limit, $count ),
						'count' => $count,
						'prevPage' => ($page > 1),
						'nextPage' => ($count > ($page * $limit)),
						'pageCount' => $pageCount,
						'order' => $query['order'],
						'limit' => $limit,
						'options' => Hash::diff( $options, $defaults ),
						'paramType' => $options['paramType']
					);

					// TODO: à factoriser, ou que sais-je ?
					if( !isset( $this->request['paging'] ) ) {
						$this->request['paging'] = array( );
					}
					$this->request['paging'] = array_merge(
							(array)$this->request['paging'],
							array( $this->Rendezvous->alias => $paging )
					);

					if( !in_array( 'Paginator', $this->helpers ) && !array_key_exists( 'Paginator', $this->helpers ) ) {
						$this->helpers[] = 'Paginator';
					}

					// Acquisition des jetons du jeu de résultat, on ajoute les ids des dossiers se trouvant déjà dans la cohorte
					$dossiers_ids = array_merge(
						$dossiers_ids,
						array_filter( Hash::extract( $results, '{n}.Dossier.id' ) )
					);
					// Si on a un second enregistrement du même dossier, on garde l'id
					if( array_remove( $dossiers_ids, $dossier_id ) > 1 ) {
						$dossiers_ids[] = $dossier_id;
					}

					$this->Cohortes->get( $dossiers_ids );

					$this->set( compact( 'results' ) );
					$this->layout = 'ajax';
				}
				//En cas d'erreur, on envoie l'erreur en Ajax/JSON
				else {
					$this->Rendezvous->rollback();
					$this->set( compact( 'json' ) );
					$this->layout = 'ajax';
					$this->render( '/Elements/json' );
				}
			}
			else {
				// Traitement du formulaire de recherche
				if( empty( $this->request->data ) ) {
					$this->Filtresdefaut->merge();
				}
				// On relache les jetons obtenus lors de l'accès précédent à la page
				else {
					$this->Cohortes->cleanCurrent();
				}

				$search = (array)Hash::get( $this->request->data, 'Search' );
				$query = $this->_getQuery( $search, array( "{$this->name}.cohorte.fields", "{$this->name}.cohorte.innerTable" ) );

				$this->Rendezvous->forceVirtualFields = true;
				$this->paginate = $query;
				$results = $this->paginate(
					$this->Rendezvous,
					array(),
					array(),
					!Hash::get( $search, 'Pagination.nombre_total' )
				);

				// Acquisition des jetons du jeu de résultat
				$dossiers_ids = array_filter( Hash::extract( $results, '{n}.Dossier.id' ) );
				$this->Cohortes->get( $dossiers_ids );

				$this->set( compact( 'results' ) );
			}

			$this->set( array( 'options' => $this->_getOptions( array( 'ajax' => $this->request->is( 'ajax' ) ) ) ) );
		}

		/**
		 * Export du tableau de résultats de la cohorte en CSV.
		 */
		public function exportcsv() {
			$this->Workflowscers93->assertUserExterne();
			$search = (array)Hash::get( Hash::expand( $this->request->params['named'], '__' ), 'Search' );

			$query = $this->_getQuery( $search, array( "{$this->name}.{$this->action}" ) );
			unset( $query['limit'] );

			$this->Rendezvous->forceVirtualFields = true;
			$results = $this->Rendezvous->find( 'all', $query );
			$options = $this->_getOptions( array( 'find' => false ) );

			$this->layout = null;
			$this->set( compact( 'results', 'options' ) );
		}
	}
?>
