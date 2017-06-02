<?php
	/**
	 * Fichier source de la classe SearchPaginatorComponentTest
	 *
	 * PHP 5.3
	 * @package Search
	 * @subpackage Test.Case.Controller.Component
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'RequestHandlerComponent', 'Controller/Component' );
	App::uses( 'SearchPaginatorComponent', 'Controller/Component' );
	App::uses( 'CakeRequest', 'Network' );
	App::uses( 'CakeResponse', 'Network' );
	App::uses( 'Router', 'Routing' );
	App::uses( 'CakeSession', 'Model/Datasource' );
	App::uses( 'SearchProgressivePagination', 'Search.Utility' );

	/**
	 * SearchPaginatorTestController class
	 *
	 * @package Search
	 * @subpackage Test.Case.Controller.Component
	 */
	class SearchPaginatorTestController extends Controller
	{

		/**
		 * name property
		 *
		 * @var string 'SearchPaginatorTest'
		 */
		public $name = 'SearchPaginatorTest';

		/**
		 * uses property
		 *
		 * @var mixed null
		 */
		public $uses = null;

		/**
		 * components property
		 *
		 * @var array
		 */
		public $components = array(
			'Search.SearchPaginator'
		);

		/**
		 * Fait-on une pagination standard ou une pagination progressive ?
		 *
		 * @param type $object
		 * @param type $scope
		 * @param type $whitelist
		 * @param type $progressivePaginate
		 * @return type
		 */
		public function paginate( $object = null, $scope = array( ), $whitelist = array( ), $progressivePaginate = null ) {
			return $this->Components->load( 'Search.SearchPaginator', $this->paginate )->paginate( $object, $scope, $whitelist, $progressivePaginate );
		}

		/**
		 *
		 * @param string|array $url A string or array-based URL pointing to another location within the app,
		 *     or an absolute URL
		 * @param integer $status Optional HTTP status code (eg: 404)
		 * @param boolean $exit If true, exit() will be called after the redirect
		 * @return mixed void if $exit = false. Terminates script if $exit = true
		 */
		public function redirect( $url, $status = null, $exit = true) {
			$this->redirected = array( $url, $status, $exit );
			return false;
		}

	}
	/**
	 * SearchPaginatorTest class
	 *
	 * @package       app.Plugin.Search.Test.Case.Controller.Component
	 */
	class SearchPaginatorComponentTest extends CakeTestCase
	{

		/**
		 * Controller property
		 *
		 * @var SearchPaginatorTestController
		 */
		public $Controller;

		/**
		 * name property
		 *
		 * @var string 'SearchPaginator'
		 */
		public $name = 'SearchPaginator';

		/**
		 * Fixtures utilisées.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'core.Apple'
		);

		/**
		 * setUp method
		 */
		public function setUp() {
			parent::setUp();

			$request = new CakeRequest( 'prgs/index', false );
			$request->addParams(array( 'controller' => 'prgs', 'action' => 'index' ) );
			$request->addParams(array( 'named' => array( 'sort' => 'Apple.id', 'direction' => 'desc' ) ) );
			$this->Controller = new SearchPaginatorTestController( $request );
			$this->Controller->action = 'index';
			$this->Controller->Apple = ClassRegistry::init( 'Apple' );
			$this->Controller->Components->init( $this->Controller );
		}

		/**
		 * Test de la méthode SearchPaginatorComponent::paginate()
		 */
		public function testPaginate() {
			$this->Controller->paginate = array(
				'fields' => array(
					'Apple.id',
					'Apple.name'
				),
				'recursive' => -1,
				'contain' => false,
				'limit' => 2
			);
			SearchProgressivePagination::disable( $this->Controller->name );

			// 1°) Pagination progressive forcée à true
			$result = $this->Controller->paginate( 'Apple', array(), array(), true );
			$expected = array(
				array(
					'Apple' => array(
						'id' => 7,
						'name' => 'Some odd color',
					),
				),
				array(
					'Apple' => array(
						'id' => 6,
						'name' => 'My new apple',
					),
				),
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Controller->request->params['paging']['Apple']['count'];
			$expected = 3;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2°) Pagination progressive forcée à false
			$result = $this->Controller->paginate( 'Apple', array(), array(), false );
			$expected = array(
				array(
					'Apple' => array(
						'id' => 7,
						'name' => 'Some odd color',
					),
				),
				array(
					'Apple' => array(
						'id' => 6,
						'name' => 'My new apple',
					),
				),
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Controller->request->params['paging']['Apple']['count'];
			$expected = 7;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3°) Pagination progressive configurée à true
			SearchProgressivePagination::enable( $this->Controller->name, $this->Controller->action );
			$result = $this->Controller->paginate( 'Apple' );
			$expected = array(
				array(
					'Apple' => array(
						'id' => 7,
						'name' => 'Some odd color',
					),
				),
				array(
					'Apple' => array(
						'id' => 6,
						'name' => 'My new apple',
					),
				),
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Controller->request->params['paging']['Apple']['count'];
			$expected = 3;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3°) Pagination progressive configurée à false
			SearchProgressivePagination::disable( $this->Controller->name, $this->Controller->action );
			$result = $this->Controller->paginate( 'Apple' );
			$expected = array(
				array(
					'Apple' => array(
						'id' => 7,
						'name' => 'Some odd color',
					),
				),
				array(
					'Apple' => array(
						'id' => 6,
						'name' => 'My new apple',
					),
				),
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Controller->request->params['paging']['Apple']['count'];
			$expected = 7;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}


		/**
		 * Test de la méthode SearchPaginatorComponent::setPaginationOrder()
		 */
		public function testSetPaginationOrder() {
			$query = array();
			$result = $this->Controller->SearchPaginator->setPaginationOrder( $query );
			$expected = array(
				'order' => array(
					'Apple.id desc'
				)
			);

			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>