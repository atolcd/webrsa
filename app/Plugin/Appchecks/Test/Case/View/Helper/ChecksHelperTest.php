<?php
	/**
	 * Code source de la classe ChecksHelperTest.
	 *
	 * PHP 5.3
	 *
	 * @package Appchecks
	 * @subpackage Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'View', 'View' );
	App::uses( 'ChecksHelper', 'Appchecks.View/Helper' );

	/**
	 * La classe ChecksHelperTest contient les tests unitaires de la classe
	 * ChecksHelper.
	 *
	 * @package Appchecks
	 * @subpackage Test.Case.View.Helper
	 */
	class ChecksHelperTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés par ces tests unitaires.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'core.Apple',
		);

		/**
		 * setUp method
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();
			$controller = null;
			$this->View = new View( $controller );
			$this->Checks = new ChecksHelper( $this->View );
		}

		/**
		 * tearDown method
		 *
		 * @return void
		 */
		public function tearDown() {
			unset( $this->View, $this->Checks );
			parent::tearDown();
		}

		/**
		 * Test de la méthode ChecksHelper::checklist()
		 *
		 * @return void
		 */
		public function testChecklist() {
			$elements = array( 'One' => true, 'Two' => false );
			$result = $this->Checks->checklist( $elements, 'drivers' );
			$expected = '<ul class="check drivers"><li class="success">One</li><li class="error">Two</li></ul>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ChecksHelper::table()
		 *
		 * @return void
		 */
		public function testTable() {
			$elements = array( 'One' => true, 'Two' => false );
			$result = $this->Checks->table( $elements );
			$expected = '<table class="checks values"><tbody><tr class="success"><th>One</th></tr><tr class="error"><th>Two</th></tr></tbody></table>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$elements = array( 'One' => array( 'value' => 4, 'message' => 'Foo', 'success' => true ), 'Two' => array( 'value' => 'Bar', 'message' => 'Baz', 'success' => false ) );
			$result = $this->Checks->table( $elements );
			$expected = '<table class="checks values"><tbody><tr class="success"><th>One</th><td class="value">4</td><td class="message">Foo</td></tr><tr class="error"><th>Two</th><td class="value">Bar</td><td class="message">Baz</td></tr></tbody></table>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>