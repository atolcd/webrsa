<?php
	/**
	 * Fichier source de la classe FiltresdefautComponentTest
	 *
	 * PHP 5.3
	 *
	 * @package Search
	 * @subpackage Test.Case.Controller.Component
	 */
	App::uses( 'Controller', 'Controller' );

	/**
	 * FiltresdefautTestController class
	 *
	 * @package Search
	 * @subpackage Test.Case.Controller.Component
	 */
	class FiltresdefautTestController extends Controller
	{

		/**
		 * name property
		 *
		 * @var string 'FiltresdefautTest'
		 */
		public $name = 'FiltresdefautTest';

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
		public $components = array( 'Search.Filtresdefaut' => array( 'index' ) );

	}
	/**
	 * FiltresdefautTest class
	 *
	 * @package       app.Plugin.Search.Test.Case.Controller.Component
	 */
	class FiltresdefautComponentTest extends CakeTestCase
	{

		/**
		 * Controller property
		 *
		 * @var FiltresdefautTestController
		 */
		public $Controller;

		/**
		 * name property
		 *
		 * @var string 'Filtresdefaut'
		 */
		public $name = 'Filtresdefaut';

		/**
		 * setUp method
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();

			$request = new CakeRequest( 'filtresdefaut/index', false );
			$request->addParams(array( 'controller' => 'filtresdefaut', 'action' => 'index' ) );
			$this->Controller = new FiltresdefautTestController( $request );
//			$this->Controller->constructClasses();
			$this->Controller->Components->init( $this->Controller );
			$this->Controller->Filtresdefaut->initialize( $this->Controller, array( 'index' ) );

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
			$result = $this->Controller->Filtresdefaut->configureKey();
			$expected = 'Filtresdefaut.FiltresdefautTest_index';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * testMerge method
		 *
		 * @return void
		 */
		public function testMerge() {
			$this->Controller->Filtresdefaut->merge();
			$result = $this->Controller->request->data;
			$expected = array();
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$originalData = array( 'Foo' => array( 'bar' => 'baz' ) );
			$this->Controller->request->data = $originalData;
			$this->Controller->Filtresdefaut->merge();
			$result = $this->Controller->request->data;
			$expected = $originalData;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			Configure::write(
				$this->Controller->Filtresdefaut->configureKey(),
				array(
					'Foo.dernier' => '1',
					'Bar.foo' => 'baz'
				)
			);
			$originalData = array( 'Foo' => array( 'bar' => 'baz' ) );
			$this->Controller->request->data = $originalData;
			$this->Controller->Filtresdefaut->merge();
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
				$this->Controller->Filtresdefaut->configureKey(),
				array(
					'Foo.dernier' => '1',
					'Bar.foo' => 'baz'
				)
			);
			$this->Controller->request->data = array();
			$this->Controller->Filtresdefaut->beforeRender( $this->Controller );
			$result = $this->Controller->request->data;
			$expected = array( 'Foo' => array( 'dernier' => '1' ), 'Bar' => array( 'foo' => 'baz' ) );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>