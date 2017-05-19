<?php
	/**
	 * ExtraBasicValidationRulesBehaviorTest file
	 *
	 * PHP 5.3
	 *
	 * @package Validation
	 * @subpackage Test.Case.Model.Behavior
	 */
	App::uses( 'Model', 'Model' );
	App::uses( 'AppModel', 'Model' );

	/**
	 * ExtraBasicValidationRulesTest class
	 *
	 * @package Validation
	 * @subpackage Test.Case.Model.Behavior
	 */
	class ExtraBasicValidationRulesBehaviorTest extends CakeTestCase
	{
		/**
		 * Fixtures associated with this test case
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.site'
		);

		/**
		 * Method executed before each test
		 *
		 */
		public function setUp() {
			parent::setUp();
			$this->Site = ClassRegistry::init( 'Site' );
			$this->Site->Behaviors->attach( 'Validation.ExtraBasicValidationRules' );
		}

		/**
		 * Method executed after each test
		 *
		 */
		public function tearDown() {
			unset( $this->Site );
			parent::tearDown();
		}

		/**
		 * Test de la méthode ExtraBasicValidationRules::integer
		 *
		 * @return void
		 */
		public function testInteger() {
			$result = $this->Site->integer( null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->integer( array( 'foo' => '15' ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->integer( array( 'foo' => 15 ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );


			$result = $this->Site->integer( array( 'foo' => 'bar' ) );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ExtraBasicValidationRules::boolean
		 *
		 * @return void
		 */
		public function testBoolean() {
			$result = $this->Site->boolean( null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->boolean( array( 'foo' => 1 ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->boolean( array( 'foo' => true ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->boolean( array( 'foo' => 'true' ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->boolean( array( 'foo' => 'bar' ) );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation.ExtraValidationRules::checkUnique
		 *
		 * @return void
		 */
		public function testCheckUnique() {
			$result = $this->Site->checkUnique( null, null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'name' => 'gwoo', 'birthday' => null );
			$this->Site->create( $data );

			$result = $this->Site->checkUnique( array( 'name' => $data['name'] ), null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->checkUnique( array( 'name' => $data['name'] ), array( 'name', 'birthday' ) );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'id' => 1, 'name' => 'gwoo', 'birthday' => null );
			$this->Site->create( $data );

			$result = $this->Site->checkUnique( array( 'name' => $data['name'] ), array( 'name', 'birthday' ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>