<#include "../freemarker_functions.ftl">
<?php
	/**
	 * Code source de la classe ${name}.
	 *
<#if php_version??>
	 * PHP ${php_version}
	 *
</#if>
	 * @package app.Test.Case.Model
	 * @license ${license}
	 */
	App::uses( '${class_name(name)?replace("Test", "", "r")}', 'Model' );

	/**
	 * La classe ${name} réalise les tests unitaires de la classe ${class_name(name)?replace("Test", "", "r")}.
	 *
	 * @package app.Test.Case.Model
	 */
	class ${name} extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.${class_name(name)?replace("Test", "", "r")}',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var ${class_name(name)?replace("Test", "", "r")}
		 */
		public $${class_name(name)?replace("Test", "", "r")} = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->${class_name(name)?replace("Test", "", "r")} = ClassRegistry::init( '${class_name(name)?replace("Test", "", "r")}' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->${class_name(name)?replace("Test", "", "r")} );
			parent::tearDown();
		}

		/**
		 * Test de la méthode ${class_name(name)?replace("Test", "", "r")}::method()
		 */
		public function testMethod() {
			$this->markTestIncomplete( 'Ce test n\'a pas encoré été implémenté.' );

			$result = $this->${class_name(name)?replace("Test", "", "r")}->method();
			$expected = null;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
