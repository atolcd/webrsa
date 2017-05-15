<#include "../freemarker_functions.ftl">
<?php
	/**
	 * Code source de la classe ${name}.
	 *
<#if php_version??>
	 * PHP ${php_version}
	 *
</#if>
	 * @package app.Test.Case.View.Helper
	 * @license ${license}
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'View', 'View' );
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( '${class_name(name)?replace("Test", "", "r")}', 'View/Helper' );

	/**
	 * La classe ${name} ...
	 *
	 * @package app.Test.Case.View.Helper
	 */
	class ${name} extends CakeTestCase
	{
		/**
		 * Fixtures utilisées pour les tests.
		 *
		 * @var array
		 */
		public $fixtures = array();

		/**
		 * Le contrôleur utilisé pour les tests.
		 *
		 * @var Controller
		 */
		public $Controller = null;

		/**
		 * Le contrôleur utilisé pour les tests.
		 *
		 * @var View
		 */
		public $View = null;

		/**
		 * Le helper à tester.
		 *
		 * @var ${class_name(name)?replace("HelperTest", "", "r")}
		 */
		public $${class_name(name)?replace("HelperTest", "", "r")} = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$Request = new CakeRequest();
			$this->Controller = new Controller( $Request );
			$this->View = new View( $this->Controller );
			$this->${class_name(name)?replace("HelperTest", "", "r")} = new ${class_name(name)?replace("Test", "", "r")}( $this->View );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			parent::tearDown();
			unset( $this->Controller, $this->View, $this->${class_name(name)?replace("HelperTest", "", "r")} );
		}

		/**
		 * Test de la méthode ${class_name(name)?replace("Test", "", "r")}::method()
		 */
		public function testMethod() {
			$this->Controller->request->addParams(
				array(
					'controller' => 'users',
					'action' => 'index',
					'pass' => array( ),
					'named' => array( )
				)
			);

			$this->markTestIncomplete( 'Ce test n\'a pas encoré été implémenté.' );
		}
	}
?>