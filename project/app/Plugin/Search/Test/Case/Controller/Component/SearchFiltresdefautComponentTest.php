<?php
	/**
	 * Fichier source de la classe SearchFiltresdefautComponentTest
	 *
	 * PHP 5.3
	 *
	 * @package Search
	 * @subpackage Test.Case.Controller.Component
	 */
	App::uses( 'Controller', 'Controller' );

	/**
	 * SearchFiltresdefautTestController class
	 *
	 * @package Search
	 * @subpackage Test.Case.Controller.Component
	 */
	class SearchFiltresdefautTestController extends Controller
	{

		/**
		 * name property
		 *
		 * @var string 'SearchFiltresdefautTest'
		 */
		public $name = 'SearchFiltresdefautTest';

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
		public $components = array( 'Search.SearchFiltresdefaut' => array( 'index' ) );

	}
	/**
	 * SearchFiltresdefautTest class
	 *
	 * @package       app.Plugin.Search.Test.Case.Controller.Component
	 */
	class SearchFiltresdefautComponentTest extends CakeTestCase
	{

		/**
		 * Controller property
		 *
		 * @var SearchFiltresdefautTestController
		 */
		public $Controller;

		/**
		 * name property
		 *
		 * @var string 'SearchFiltresdefaut'
		 */
		public $name = 'SearchFiltresdefaut';

		/**
		 * setUp method
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();

			$request = new CakeRequest( 'filtresdefaut/index', false );
			$request->addParams(array( 'controller' => 'filtresdefaut', 'action' => 'index' ) );
			$this->Controller = new SearchFiltresdefautTestController( $request );
//			$this->Controller->constructClasses();
			$this->Controller->Components->init( $this->Controller );
			$this->Controller->SearchFiltresdefaut->initialize( $this->Controller, array( 'index' ) );

//			App::build(
//				array(
//				'View' => array( CAKE.'Test'.DS.'test_app'.DS.'View'.DS )
//				)
//			);
		}

		/**
		 * testConfigureKey method
		 *
		 * @return void
		 */
		public function testConfigureKey() {
			$result = $this->Controller->SearchFiltresdefaut->configureKey();
			$expected = 'Filtresdefaut.SearchFiltresdefautTest_index';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * testMerge method
		 *
		 * @return void
		 */
		public function testMerge() {
			$this->Controller->SearchFiltresdefaut->merge();
			$result = $this->Controller->request->data;
			$expected = array();
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$originalData = array( 'Foo' => array( 'bar' => 'baz' ) );
			$this->Controller->request->data = $originalData;
			$this->Controller->SearchFiltresdefaut->merge();
			$result = $this->Controller->request->data;
			$expected = $originalData;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			Configure::write(
				$this->Controller->SearchFiltresdefaut->configureKey(),
				array(
					'Foo.dernier' => '1',
					'Bar.foo' => 'baz'
				)
			);
			$originalData = array( 'Foo' => array( 'bar' => 'baz' ) );
			$this->Controller->request->data = $originalData;
			$this->Controller->SearchFiltresdefaut->merge();
			$result = $this->Controller->request->data;
			$expected = array( 'Foo' => array( 'dernier' => '1', 'bar' => 'baz' ), 'Bar' => array( 'foo' => 'baz' ) );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * testBeforeRender method
		 *
		 * @return void
		 */
		public function testBeforeRender() {
			Configure::write(
				$this->Controller->SearchFiltresdefaut->configureKey(),
				array(
					'Foo.dernier' => '1',
					'Bar.foo' => 'baz'
				)
			);
			$this->Controller->request->data = array();
			$this->Controller->SearchFiltresdefaut->beforeRender( $this->Controller );
			$result = $this->Controller->request->data;
			$expected = array( 'Foo' => array( 'dernier' => '1' ), 'Bar' => array( 'foo' => 'baz' ) );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>