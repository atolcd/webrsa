<?php
	/**
	 * Code source de la classe Cataloguespdisfps93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Cataloguespdisfps93Controller ...
	 *
	 * @package app.Controller
	 */
	class Cataloguespdisfps93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cataloguespdisfps93';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Search.SearchFiltresdefaut' => array(
				'search'
			),
			'Search.SearchPrg' => array(
				'actions' => array(
					'search' => array(
						'filter' => 'Search'
					),
				)
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Allocataires',
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
			'Cataloguepdifp93',
			'Thematiquefp93',
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
			'add' => 'create',
			'delete' => 'delete',
			'edit' => 'update',
			'index' => 'read',
			'search' => 'read',
		);

		/**
		 * Tableau de suivi du référentiel.
		 */
		public function search() {
			if( Hash::check( $this->request->data, 'Search' ) ) {
				$query = $this->Cataloguepdifp93->search( $this->request->data['Search'] );

				if (Hash::get( $this->request->data, 'Search.limit')) {
					$query['limit'] = $this->request->data['Search']['limit'];
				}

				$this->paginate = array( 'Thematiquefp93' => $query );
				$results = $this->paginate(
					'Thematiquefp93',
					array(),
					$query['fields'],
					!Hash::get( $this->request->data, 'Search.Pagination.nombre_total' )
				);

				$this->set( compact( 'results' ) );
			}

			$options = $this->Cataloguepdifp93->options();

			$this->set( compact( 'options' ) );
		}

		/**
		 * Liste des enregistrements.
		 *
		 * @param string $modelName
		 * @throws Error404Exception
		 */
		public function index( $modelName ) {
			if( !in_array( $modelName, $this->Cataloguepdifp93->modelesParametrages ) ) {
				throw new Error404Exception();
			}

			$Model = ClassRegistry::init( $modelName );
			if( $Model->Behaviors->attached( 'Cataloguepdifp93' ) ) {
				$query = $Model->searchQuery();
				$query['fields'] = Hash::merge( $query['fields'], $Model->fields() );
			}
			else {
				// Début factorisation pour formulaire
				$fields = array_keys( $Model->schema() );
				foreach( $fields as $i => $field ) {
					$fields[$i] = "{$Model->alias}.{$field}";
				}

				$query = array(
					'fields' => $fields,
					'joins' => array(),
					'order' => array( "{$Model->alias}.{$Model->displayField} ASC" ),
					'limit' => 10
				);

				if( !empty( $Model->belongsTo ) ) {
					foreach( $Model->belongsTo as $alias => $params ) {
						array_remove( $query['fields'], "{$Model->alias}.{$params['foreignKey']}" );

						$OtherModel = $Model->{$alias};
						array_unshift( $fields, "{$alias}.{$OtherModel->displayField}" );
						$query['joins'][] = $Model->join( $alias, array( 'type' => 'INNER' ) );
					}
				}
				// Fin factorisation pour formulaire

				$query['fields'] = $fields;
				$query['fields'][] = "{$Model->alias}.{$Model->primaryKey}";
			}
			$query['fields'] = array_unique($query['fields']);

			$fields = $query['fields'];
			$Model->forceVirtualFields = true;
			$this->paginate = array( $Model->alias => $query );

			$results = $this->paginate( $Model, array(), $fields, false );

			// A-t'on des enregistrements liés ?
			$Model->Behaviors->attach( 'Occurences' );
			$occurences = $Model->occurencesExists();
			foreach( $results as $i => $result ) {
				$primaryKey = Hash::get( $result, "{$Model->alias}.{$Model->primaryKey}" );
				$results[$i][$Model->alias]['occurences'] = ( Hash::get( $occurences, $primaryKey ) ? '1' : '0' );
			}

			$options = $this->Cataloguepdifp93->options();

			foreach( $fields as $key => $field ) {
				list( $modelName, $fieldName ) = model_field( $field );
				if( $fieldName === 'id' || preg_match( '/_id$/', $fieldName ) ) {
					unset( $fields[$key] );
				}
			}

			$this->set( compact( 'modelName', 'results', 'fields', 'options' ) );
		}

		/**
		 * Formulaire d'ajout
		 *
		 * @param string $modelName
		 */
		public function add( $modelName ) {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Formulaire de modification
		 *
		 * @param string $modelName
		 * @param integer $id
		 */
		public function edit( $modelName, $id ) {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Formulaire d'ajout / de modification
		 *
		 * @param string $modelName
		 * @param integer $id
		 * @throws Error404Exception
		 */
		protected function _add_edit( $modelName, $id = null ) {
			if( !in_array( $modelName, $this->Cataloguepdifp93->modelesParametrages ) ) {
				throw new Error404Exception();
			}

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$referer = Hash::get( $this->request->data, "{$modelName}.referer" );
				$this->redirect( $referer );
			}

			$Model = ClassRegistry::init( $modelName );

			if( !empty( $this->request->data ) ) {
				$Model->begin();
				if( $Model->saveParametrage( $this->request->data ) ) {
					$Model->commit();
					$this->Flash->success( __( 'Save->success' ) );

					$referer = Hash::get( $this->request->data, "{$modelName}.referer" );
					$this->redirect( $referer );
				}
				else {
					$Model->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $Model->getParametrageFormData( $id );

				if( empty( $this->request->data ) ) {
					throw new Error404Exception();
				}
			}

			// Sauvegarder dans le formulaire de l'adresse de laquelle on vient
			if( !Hash::get( $this->request->data, "{$modelName}.referer" ) ) {
				$referer = $this->referer( null, true );
				$here = $this->request->here( false );

				if( in_array( $referer, array( '/', $here ), true ) ) {
					$url = array( 'controller' => $this->request->params['controller'], 'action' => 'index', $modelName );
					$referer = Router::normalize( Router::url( $url, false ) );
				}

				$this->request->data = Hash::merge(
					(array)$this->request->data,
					array( $modelName => array( 'referer' => $referer ) )
				);
			}

			$fields = $Model->getParametrageFields();
			$fields["{$modelName}.referer"] = array( 'type' => 'hidden' );
			$options = $Model->getParametrageOptions();
			$dependantFields = $Model->getParametrageDependantFields();

			$this->set( compact( 'options', 'fields', 'modelName', 'dependantFields' ) );
			$this->render( 'add_edit' );
		}

		/**
		 * Tentative de suppression d'un enregistrement
		 *
		 * @param string $modelName
		 * @param integer $id
		 * @throws Error404Exception
		 * @throws Error500Exception
		 */
		public function delete( $modelName, $id ) {
			if( !in_array( $modelName, $this->Cataloguepdifp93->modelesParametrages ) ) {
				throw new Error404Exception();
			}

			$Model = ClassRegistry::init( $modelName );

			$Model->Behaviors->attach( 'Occurences' );
			$occurences = $Model->occurencesExists();
			if( Hash::get( $occurences, $id ) ) {
				throw new Error500Exception( null );
			}

			$Model->begin();
			if( $Model->delete( $id ) ) {
				$Model->commit();
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$Model->rollback();
				$this->Flash->error( __( 'Delete->error' ) );
			}

			$this->redirect( $this->referer() );
		}
	}
?>
