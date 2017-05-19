<?php
	/**
	 * Code source de la classe Cataloguesromesv3Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Cataloguesromesv3Controller ...
	 *
	 * @package app.Controller
	 */
	class Cataloguesromesv3Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cataloguesromesv3';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Ajax',
			'Search.SearchFiltresdefaut' => array(
				'famillesromesv3',
				'domainesromesv3',
				'metiersromesv3',
				'appellationsromesv3',
			),
			'Search.SearchPrg' => array(
				'actions' => array(
					'famillesromesv3' => array('filter' => 'Search'),
					'domainesromesv3' => array('filter' => 'Search'),
					'metiersromesv3' => array('filter' => 'Search'),
					'appellationsromesv3' => array('filter' => 'Search'),
				)
			)
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Allocataires',
			'Ajax2' => array(
				'className' => 'Prototype.PrototypeAjax',
				'useBuffer' => false
			),
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
			'Catalogueromev3',
			'Appellationromev3',
			'Domaineromev3',
			'Familleromev3',
			'Metierromev3',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Cataloguesromesv3:edit',
			'appellationsromesv3' => 'Cataloguesromesv3:index',
			'domainesromesv3' => 'Cataloguesromesv3:index',
			'famillesromesv3' => 'Cataloguesromesv3:index',
			'metiersromesv3' => 'Cataloguesromesv3:index',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajax_appellation',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'ajax_appellation' => 'read',
			'appellationsromesv3' => 'read',
			'delete' => 'delete',
			'domainesromesv3' => 'read',
			'edit' => 'update',
			'famillesromesv3' => 'read',
			'index' => 'read',
			'metiersromesv3' => 'read',
		);

		/**
		 *
		 */
		public function ajax_appellation() {
			$data = $this->Ajax->unprefixAjaxRequest( $this->request->data );
			$json = array( 'success' => true, 'fields' => array() );

			$type = Hash::get( $data, 'Event.type' );

			$path = Hash::get( $data, 'Target.name' );
			$path = str_replace( '][', '.', preg_replace( '/^data\[(.*)\]$/', '\1', $path ) );

			// 1. Ajax::ajaxOnKeyup()
			if( $type === 'keyup' ) {
				// Partie du nom du champ
				list( $modelName, $fieldName ) = model_field( $path );

				$value = Hash::get( $data, $path );

				$query = array(
					'fields' => array(
						'Appellationromev3.id',
						'Appellationromev3.name'
					),
					'conditions' => array(
						'NOACCENTS_UPPER( "Appellationromev3"."name" ) LIKE' => '%'.noaccents_upper( $value ).'%',
					),
					'order' => array(
						'Appellationromev3.name ASC'
					)
				);

				if( trim( $value ) == '' ) {
					$query['conditions'] = '1 = 2';
				}

				$results = $this->Appellationromev3->find( 'all', $query );

				$json['fields'][$path] = array(
					'id' => $data['Target']['domId'],
					'type' => 'ajax_select',
					'prefix' => $data['prefix'],
					'options' => Hash::extract( $results, '{n}.Appellationromev3' )
				);
			}
			// 2. Ajax::ajaxClick()
			else {
				$target = str_replace( '][', '.', preg_replace( '/^data\[(.*)\]$/', '\1', $data['name'] ) );
				$targetPrefix = preg_replace( '/\.romev3$/', '', $target );
				list( $modelName, $fieldName ) = model_field( $target );
				// TODO: tester $targetPrefix si on a un préfixe
				$query = array(
					'fields' => array(
						'Domaineromev3.familleromev3_id',
						'Domaineromev3.id',
						'Metierromev3.id',
						'Appellationromev3.id',
					),
					'contain' => false,
					'joins' => array(
						$this->Appellationromev3->join( 'Metierromev3', array( 'type' => 'INNER' ) ),
						$this->Appellationromev3->Metierromev3->join( 'Domaineromev3', array( 'type' => 'INNER' ) ),
					),
					'conditions' => array(
						'Appellationromev3.id' => $data['value']
					)
				);

				$result = $this->Appellationromev3->find( 'first', $query );

				$json['fields'] = array(
					"{$targetPrefix}.{$fieldName}" => array(
						'id' => domId( "{$data['prefix']}{$targetPrefix}.{$fieldName}" ),
						'value' => '', // FIXME
						'type' => 'text'
					),
					"{$targetPrefix}.familleromev3_id" => array(
						'id' => domId( "{$data['prefix']}{$targetPrefix}.familleromev3_id" ),
						'value' => $result['Domaineromev3']['familleromev3_id'],
						'type' => 'select',
						'simulate' => true
					),
					"{$targetPrefix}.domaineromev3_id" => array(
						'id' => domId( "{$data['prefix']}{$targetPrefix}.domaineromev3_id" ),
						'value' => "{$result['Domaineromev3']['familleromev3_id']}_{$result['Domaineromev3']['id']}",
						'type' => 'select',
						'simulate' => true
					),
					"{$targetPrefix}.metierromev3_id" => array(
						'id' => domId( "{$data['prefix']}{$targetPrefix}.metierromev3_id" ),
						'value' => "{$result['Domaineromev3']['id']}_{$result['Metierromev3']['id']}",
						'type' => 'select',
						'simulate' => true
					),
					"{$targetPrefix}.appellationromev3_id" => array(
						'id' => domId( "{$data['prefix']}{$targetPrefix}.appellationromev3_id" ),
						'value' => "{$result['Metierromev3']['id']}_{$result['Appellationromev3']['id']}",
						'type' => 'select',
						'simulate' => true
					)
				);
			}

			$json['fields'] = $this->Ajax->prefixAjaxResult( $data['prefix'], $json['fields'] );

			$this->set( compact( 'json' ) );
			$this->layout = 'ajax';
			$this->render( '/Elements/json' );
		}

		/**
		 * Liste des codes familles ROME V3.
		 */
		public function famillesromesv3() {
			$this->index( 'Familleromev3' );
		}

		/**
		 * Liste des codes domaines ROME V3.
		 */
		public function domainesromesv3() {
			$this->index( 'Domaineromev3' );
		}

		/**
		 * Liste des codes metiers ROME V3.
		 */
		public function metiersromesv3() {
			$this->index( 'Metierromev3' );
		}

		/**
		 * Liste des appellations ROME V3.
		 */
		public function appellationsromesv3() {
			$this->index( 'Appellationromev3' );
		}

		/**
		 * Pagination sur les <éléments> de la table.
		 *
		 * @param string $modelName
		 * @throws NotFoundException
		 */
		public function index( $modelName ) {
			if( !in_array( $modelName, $this->Catalogueromev3->modelesParametrages ) ) {
				throw new NotFoundException();
			}

			$tableName = Inflector::tableize( $modelName );
			$search = (array)Hash::get( $this->request->data, 'Search' );
			$fields = $this->{$modelName}->searchResultFields;
			$options = $this->{$modelName}->options();

			if( !empty( $search ) ) {
				$query = $this->{$modelName}->search( $search );
				$this->paginate = array(
					$modelName => $query + array(
						'limit' => 10
					)
				);

				$results = $this->paginate(
					$modelName,
					array(),
					$fields,
					false
				);

				// A-t'on des enregistrements liés ?
				$this->{$modelName}->Behaviors->attach( 'Occurences' );
				$occurences = $this->{$modelName}->occurencesExists();
				foreach( $results as $i => $result ) {
					$primaryKey = Hash::get( $result, "{$modelName}.{$this->{$modelName}->primaryKey}" );
					$results[$i][$modelName]['occurences'] = ( Hash::get( $occurences, $primaryKey ) ? '1' : '0' );
				}

				$this->set( compact( 'results' ) );
			}

			$this->set( compact( 'options', 'fields', 'modelName', 'tableName' ) );
			$this->render( 'index' );
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
		 * @throws NotFoundException
		 */
		protected function _add_edit( $modelName, $id = null ) {
			if( !in_array( $modelName, $this->Catalogueromev3->modelesParametrages ) ) {
				throw new NotFoundException();
			}

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$referer = Hash::get( $this->request->data, "{$modelName}.referer" );
				return $this->redirect( $referer );
			}

			$Model = ClassRegistry::init( $modelName );

			if( !empty( $this->request->data ) ) {
				$Model->begin();
				if( $Model->saveParametrage( $this->request->data ) ) {
					$Model->commit();
					$this->Flash->success( __( 'Save->success' ) );

					$referer = Hash::get( $this->request->data, "{$modelName}.referer" );
					return $this->redirect( $referer );
				}
				else {
					$Model->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $Model->getParametrageFormData( $id );

				if( empty( $this->request->data ) ) {
					throw new NotFoundException();
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
		 * @throws NotFoundException
		 * @throws InternalErrorException
		 */
		public function delete( $modelName, $id ) {
			if( !in_array( $modelName, $this->Catalogueromev3->modelesParametrages ) ) {
				throw new NotFoundException();
			}

			$Model = ClassRegistry::init( $modelName );

			$Model->Behaviors->attach( 'Occurences' );
			$occurences = $Model->occurencesExists();
			if( Hash::get( $occurences, $id ) ) {
				throw new InternalErrorException( null );
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

			return $this->redirect( $this->referer() );
		}
	}
?>
