<#include "../freemarker_functions.ftl">
<?php
	/**
	 * Code source de la classe ${name}.
	 *
<#if php_version??>
	 * PHP ${php_version}
	 *
</#if>
	 * @package app.Test.Case.Model.Behavior
	 * @license ${license}
	 */
	App::uses( '${class_name(name)?replace("Test", "", "r")}', 'Model/Behavior' );

	/**
	 * La classe ${name} ...
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class ${name} extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'core.Apple'
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Apple = ClassRegistry::init( 'Apple' );
			$this->Apple->Behaviors->attach( '${class_name(name)?replace("Test", "", "r")}' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Apple );
			parent::tearDown();
		}

		/**
		 * Test de la méthode ${class_name(name)?replace("Test", "", "r")}::method()
		 */
		public function testMethod() {
			$this->markTestIncomplete( 'Ce test n\'a pas encoré été implémenté.' );
		}
	}
?>