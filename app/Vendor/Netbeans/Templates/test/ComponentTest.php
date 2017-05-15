<#include "../freemarker_functions.ftl">
<?php
	/**
	 * Code source de la classe ${name}.
	 *
<#if php_version??>
	 * PHP ${php_version}
	 *
</#if>
	 * @package app.Test.Case.Controller.Component
	 * @license ${license}
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'AppController', 'Controller' );
	App::uses( '${class_name(name)?replace("Test", "", "r")}', 'Controller/Component' );

	/**
	 * ${class_name(name)?replace("ComponentTest", "", "r")}TestsController class
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class ${class_name(name)?replace("ComponentTest", "", "r")}TestsController extends AppController
	{
		/**
		 * name property
		 *
		 * @var string
		 */
		public $name = '${class_name(name)?replace("ComponentTest", "", "r")}TestsController';

		/**
		 * uses property
		 *
		 * @var mixed null
		 */
		public $uses = array( 'Apple' );

		/**
		 * components property
		 *
		 * @var array
		 */
		public $components = array(
			'${class_name(name)?replace("ComponentTest", "", "r")}'
		);
	}

	/**
	 * La classe ${name} ...
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class ${name} extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array();

		/**
		 * Controller property
		 *
		 * @var ${class_name(name)?replace("Test", "", "r")}
		 */
		public $Controller;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();

			$Request = new CakeRequest( 'apples/index', false );
			$Request->addParams(array( 'controller' => 'apples', 'action' => 'index' ) );

			$this->Controller = new ${class_name(name)?replace("ComponentTest", "", "r")}TestsController( $Request );
			$this->Controller->Components->init( $this->Controller );
			$this->Controller->${class_name(name)?replace("ComponentTest", "", "r")}->initialize( $this->Controller );
		}

		/**
		 * Test de la méthode ${class_name(name)?replace("Test", "", "r")}::method()
		 */
		public function testMethod() {
			$this->markTestIncomplete( 'Ce test n\'a pas encoré été implémenté.' );

			$result = $this->Controller->${class_name(name)?replace("ComponentTest", "", "r")}->method();
			$expected = null;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>