<?php
	/**
	 * ExtraValidationRulesBehaviorTest file
	 *
	 * PHP 5.3
	 *
	 * @package Validation
	 * @subpackage Test.Case.Model.Behavior
	 */
	App::uses( 'Model', 'Model' );
	App::uses( 'AppModel', 'Model' );

	/**
	 * Validation.ExtraValidationRulesTest class
	 *
	 * @package Validation
	 * @subpackage Test.Case.Model.Behavior
	 */
	class ExtraValidationRulesBehaviorTest extends CakeTestCase
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
			$this->Site->Behaviors->attach( 'Validation.ExtraValidationRules' );
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
		 * Test de la méthode Validation.ExtraValidationRules::exactLength
		 *
		 * @return void
		 */
		public function testExactLength() {
			$result = $this->Site->exactLength( null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->exactLength( array( 'foo' => '15' ), 2 );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->exactLength( array( 'foo' => 15 ), 2 );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );


			$result = $this->Site->exactLength( array( 'foo' => 'bar' ), 2 );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation.ExtraValidationRules::futureDate
		 *
		 * @return void
		 */
		public function testFutureDate() {
			$result = $this->Site->futureDate( null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->futureDate( array( 'foo' => date( 'Y-m-d', strtotime( '+1 day' ) ) ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->futureDate( array( 'foo' => date( 'Y-m-d' ) ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->futureDate( array( 'foo' => date( 'Y-m-d', strtotime( '-1 day' ) ) ) );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation.ExtraValidationRules::datePassee
		 *
		 * @return void
		 */
		public function testDatePassee() {
			$result = $this->Site->datePassee( null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->datePassee( array( 'foo' => date( 'Y-m-d', strtotime( '-1 day' ) ) ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->datePassee( array( 'foo' => date( 'Y-m-d' ) ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->datePassee( array( 'foo' => date( 'Y-m-d', strtotime( '+1 day' ) ) ) );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation.ExtraValidationRules::phoneFr
		 *
		 * @return void
		 */
		public function testPhoneFr() {
			$result = $this->Site->phoneFr( null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->phoneFr( array( 'phone' => '9999999999' ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->phoneFr( array( 'phone' => '04 09 80 15 09' ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->phoneFr( array( 'phone' => '3949' ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->phoneFr( array( 'phone' => '15' ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->phoneFr( array( 'phone' => '112' ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->phoneFr( array( 'phone' => '118 718' ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->phoneFr( array( 'phone' => '1187189' ) );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation.ExtraValidationRules::allEmpty
		 *
		 * @return void
		 */
		public function testAllEmpty() {
			$result = $this->Site->allEmpty( null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'phone' => '', 'fax' => null );
			$this->Site->create( $data );
			$result = $this->Site->allEmpty( array( 'phone' => '' ), 'fax' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'phone' => ' ', 'fax' => null );
			$this->Site->create( $data );
			$result = $this->Site->allEmpty( array( 'phone' => ' ' ), 'fax' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation.ExtraValidationRules::notEmptyIf
		 *
		 * @return void
		 */
		public function testNotEmptyIf() {
			$result = $this->Site->notEmptyIf( null, null, null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'phone' => 'X', 'fax' => null );
			$this->Site->create( $data );
			$result = $this->Site->notEmptyIf( array( 'phone' => 'X' ), 'fax', true, array( null ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'phone' => '', 'fax' => null );
			$this->Site->create( $data );
			$result = $this->Site->notEmptyIf( array( 'phone' => '' ), 'fax', true, array( null ) );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation.ExtraValidationRules::notNullIf()
		 *
		 * @return void
		 */
		public function testNotNullIf() {
			$result = $this->Site->notNullIf( null, null, null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'phone' => '0606060606', 'fax' => null );
			$this->Site->create( $data );
			$result = $this->Site->notNullIf(
				array( 'phone' => '0606060606' ),
				'fax',
				true,
				array( null )
			);
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'phone' => null, 'fax' => null );
			$this->Site->create( $data );
			$result = $this->Site->notNullIf(
				array( 'phone' => null ),
				'fax',
				false,
				array( null )
			);
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'phone' => '0606060606', 'fax' => '0404040404' );
			$this->Site->create( $data );
			$result = $this->Site->notNullIf(
				array( 'phone' => '0606060606' ),
				'fax',
				true,
				array( null )
			);
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation.ExtraValidationRules::greaterThanIfNotZero
		 *
		 * @return void
		 */
		public function testGreaterThanIfNotZero() {
			$result = $this->Site->greaterThanIfNotZero( null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$this->Site->create( array( 'phone' => 1, 'fax' => 1 ) );
			$result = $this->Site->greaterThanIfNotZero( array( 'phone' => 1 ), 'fax' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$this->Site->create( array( 'phone' => 1, 'fax' => 2 ) );
			$result = $this->Site->greaterThanIfNotZero( array( 'phone' => 1 ), 'fax' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation.ExtraValidationRules::compareDates
		 *
		 * @return void
		 */
		public function testCompareDates() {
			$result = $this->Site->compareDates( null, null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$this->Site->create( array( 'from' => null, 'to' => null ) );
			$result = $this->Site->compareDates( array( 'from' => null ), 'to', 'null' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'from' => '20120101', 'to' => '20120102' );
			$this->Site->create( $data );

			$result = $this->Site->compareDates( array( 'from' => $data['from'] ), 'to', '<' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->compareDates( array( 'from' => $data['from'] ), 'to', '*' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->compareDates( array( 'from' => $data['from'] ), 'to', '>' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation.ExtraValidationRules::inclusiveRange
		 *
		 * @return void
		 */
		public function testInclusiveRange() {
			$result = $this->Site->inclusiveRange( null, null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->inclusiveRange( array( 'value' => 5 ), 0, 5 );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation.ExtraValidationRules::foo
		 *
		 * @return void
		 */
		public function testFoo() {
			$result = $this->Site->foo( null, null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->foo( array(), null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$this->Site->create( array( 'Site' => array( 'value' => null, 'othervalue' => null ) ) );
			$result = $this->Site->foo( array( 'value' => null ), 'othervalue' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$this->Site->create( array( 'Site' => array( 'value' => true, 'othervalue' => false ) ) );
			$result = $this->Site->foo( array( 'value' => true ), 'othervalue' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$this->Site->create( array( 'Site' => array( 'value' => null, 'othervalue' => 5 ) ) );
			$result = $this->Site->foo( array( 'value' => null ), 'othervalue' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$this->Site->create( array( 'Site' => array( 'value' => false, 'othervalue' => null ) ) );
			$result = $this->Site->foo( array( 'value' => false ), 'othervalue' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>