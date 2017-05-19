<?php
	/**
	 * Validation2UtilitiesBehaviorTest file
	 *
	 * PHP 5.3
	 *
	 * @package Validation2
	 * @subpackage Test.Case.Model.Behavior
	 */
	App::uses( 'Model', 'Model' );
	App::uses( 'AppModel', 'Model' );

	/**
	 * ExtraBasicValidation2RulesTest class
	 *
	 * @package Validation2
	 * @subpackage Test.Case.Model.Behavior
	 */
	class Validation2UtilitiesBehaviorTest extends CakeTestCase
	{
		/**
		 * Fixtures associated with this test case
		 *
		 * @var array
		 */
		public $fixtures = array(
			'plugin.Validation2.Validation2Site',
		);

		/**
		 * Method executed before each test
		 */
		public function setUp() {
			parent::setUp();
			$this->Site = ClassRegistry::init( array('class' => 'Validation2.Validation2Site', 'alias' => 'Site') );
			$this->Site->Behaviors->attach( 'Validation2.Validation2Utilities' );
		}

		/**
		 * Method executed after each test
		 */
		public function tearDown() {
			unset( $this->Site );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Validation2UtilitiesBehavior::methodCacheKey() du
		 * plugin Validation2.
		 */
		public function testMethodCacheKey() {
			$result = $this->Site->methodCacheKey( 'FooBar', 'baz' );
			$expected = 'test_FooBar_baz_Site';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->methodCacheKey( 'FooBar', 'baz', true );
			$expected = 'test__foo_bar_baz__site';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation2UtilitiesBehavior::normalizeValidationRule() du
		 * plugin Validation2.
		 */
		public function testNormalizeValidationRule() {
			$result = $this->Site->normalizeValidationRule( 'notEmpty' );
			$expected = array(
				'rule' => array( 'notEmpty' ),
				'message' => NULL,
				'required' => NULL,
				'allowEmpty' => NULL,
				'on' => NULL,
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->normalizeValidationRule( array( 'between', 2, 5 ) );
			$expected = array(
				'rule' => array( 'between', 2, 5 ),
				'message' => NULL,
				'required' => NULL,
				'allowEmpty' => NULL,
				'on' => NULL,
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->normalizeValidationRule( array( 'rule' => array( 'between', 2, 5 ), 'allowEmpty' => true ) );
			$expected = array(
				'rule' => array( 'between', 2, 5 ),
				'message' => NULL,
				'required' => NULL,
				'allowEmpty' => true,
				'on' => NULL,
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->normalizeValidationRule( array( 'rule' => 'integer', 'allowEmpty' => true ) );
			$expected = array(
				'rule' => array( 'integer' ),
				'message' => NULL,
				'required' => NULL,
				'allowEmpty' => true,
				'on' => NULL,
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation2UtilitiesBehavior::defaultValidationRuleMessage() du
		 * plugin Validation2.
		 */
		public function testDefaultValidationRuleMessage() {
			$result = $this->Site->defaultValidationRuleMessage( null );
			$expected = null;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->defaultValidationRuleMessage( 'notEmpty' );
			$expected = __( 'Validate::notEmpty' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->defaultValidationRuleMessage( array( 'between', 2, 5 ) );
			$expected = sprintf( __( 'Validate::between' ), array( 2, 5 ) );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->defaultValidationRuleMessage( array( 'between', array( 2, 5 ) ) );
			$expected = sprintf( __( 'Validate::between' ), array( 2, 5 ) );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->defaultValidationRuleMessage( array( 'rule' => array( 'between', array( 2, 5 ) ), 'domain' => 'default' ) );
			$expected = sprintf( __( 'Validate::between' ), array( 2, 5 ) );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation2UtilitiesBehavior::setValidationRule().
		 */
		public function testSetValidationRule() {
			$this->Site->setValidationRule( 'id', 'notEmpty' );
			$result = $this->Site->validate;
			$expected = array(
				'id' => array(
					'notEmpty' => array(
						'rule' => array( 'notEmpty' ),
						'message' => null,
						'required' => null,
						'allowEmpty' => null,
						'on' => null
					)
				)
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de l'eception renvoyée par la méthode Validation2UtilitiesBehavior::setValidationRule()
		 *
		 * @expectedException LogicException
		 */
		public function testSetValidationRuleException() {
			$this->Site->setValidationRule( 'inexistant', 'notEmpty' );
		}

		/**
		 * Test de la méthode Validation2UtilitiesBehavior::unsetValidationRule().
		 */
		public function testUnsetValidationRule() {
			$this->Site->validate = array(
				'id' => array(
					'notEmpty' => array(
						'rule' => array( 'notEmpty' ),
						'message' => null,
						'required' => null,
						'allowEmpty' => null,
						'on' => null
					)
				)
			);
			$this->Site->unsetValidationRule( 'id', 'notEmpty' );
			$result = $this->Site->validate;
			$expected = array( 'id' => array() );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de l'eception renvoyée par la méthode Validation2UtilitiesBehavior::unsetValidationRule()
		 *
		 * @expectedException LogicException
		 */
		public function testUnsetValidationRuleException() {
			$this->Site->unsetValidationRule( 'inexistant', 'notEmpty' );
		}

		/**
		 * Test de la méthode Validation2UtilitiesBehavior::hasValidationRule().
		 */
		public function testHasValidationRule() {
			$this->Site->validate = array(
				'id' => array(
					'notEmpty' => array(
						'rule' => array( 'notEmpty' ),
						'message' => null,
						'required' => null,
						'allowEmpty' => null,
						'on' => null
					)
				)
			);
			$result = $this->Site->hasValidationRule( 'id', 'notEmpty' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->hasValidationRule( 'id', 'inexistant' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de l'eception renvoyée par la méthode Validation2UtilitiesBehavior::hasValidationRule()
		 *
		 * @expectedException LogicException
		 */
		public function testHasValidationRuleException() {
			$this->Site->hasValidationRule( 'inexistant', 'notEmpty' );
		}

		/**
		 * Test de la méthode Validation2UtilitiesBehavior::beforeValidate() du
		 * plugin Validation2.
		 *
		 * @return void
		 */
		public function testBeforeValidate() {
			$this->Site->validate = array(
				'id' => array(
					'notEmpty' => array(
						'rule' => array( 'notEmpty' ),
						'message' => null,
						'required' => null,
						'allowEmpty' => null,
						'on' => null
					)
				)
			);

			$this->Site->create(array());
			$this->Site->validates();

			$result = $this->Site->validate;
			$expected = array(
				'id' => array(
					'notEmpty' => array(
						'rule' => array( 'notEmpty', ),
						'message' => __( 'Validate::notEmpty' ),
						'required' => NULL,
						'allowEmpty' => NULL,
						'on' => NULL,
					),
				),
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>