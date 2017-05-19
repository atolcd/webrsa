<#include "../freemarker_functions.ftl">
<?php
	/**
	 * Code source de la classe ${name}.
	 *
<#if php_version??>
	 * PHP ${php_version}
	 *
</#if>
	 * @package app.Test.Case.Controller
	 * @license ${license}
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'AppController', 'Controller' );
	App::uses( '${class_name(name)?replace("ControllerTest", "Controller", "r")}', 'Controller' );

	/**
	 * La classe ${name} ...
	 *
	 * @package app.Test.Case.Controller
	 */
	class ${name} extends ControllerTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array();

		/**
		 * Test de la méthode ${class_name(name)?replace("ControllerTest", "Controller", "r")}::method()
		 */
		public function testMethod() {
			$this->markTestIncomplete( 'Ce test n\'a pas encoré été implémenté.' );

			$result = $this->testAction( '/controller/method' );
			$expected = null;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>