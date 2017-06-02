<?php
	/**
	 * ValidateUtilitiesBehaviorTest file
	 *
	 * PHP 5.3
	 *
	 * @package Validation
	 * @subpackage Test.Case.Model.Behavior
	 */
	App::uses( 'Model', 'Model' );
	App::uses( 'AppModel', 'Model' );

	/**
	 * ValidateUtilitiesTest class
	 *
	 * TODO: ValidationUtilities::normalizeValidate
	 *
	 * @package Validation
	 * @subpackage Test.Case.Model.Behavior
	 */
	class ValidateUtilitiesBehaviorTest extends CakeTestCase
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
			$this->Site->Behaviors->attach( 'Validation.ValidateUtilities' );
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
		 * Test de la méthode ValidateUtilities::methodCacheKey
		 *
		 * @return void
		 */
		public function testMethodCacheKey() {
			$result = $this->Site->methodCacheKey( 'ClassName', 'methodName' );
			$expected = 'test_ClassName_methodName_Site';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->methodCacheKey( 'ClassName', 'methodName', true );
			$expected = 'test__class_name_method_name__site';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ValidateUtilities::normalizeValidationRule
		 *
		 * @return void
		 */
		public function testNormalizeValidationRule() {
			$result = $this->Site->normalizeValidationRule( 'notEmpty' );
			$expected = array(
				'rule' => array( 'notEmpty' ),
				'message' => null,
				'required' => null,
				'allowEmpty' => null,
				'on' => null,
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->normalizeValidationRule( array( 'notEmpty' ) );
			$expected = array(
				'rule' => array( 'notEmpty' ),
				'message' => null,
				'required' => null,
				'allowEmpty' => null,
				'on' => null,
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->normalizeValidationRule( array( 'rule' => 'alphaNumeric' ) );
			$expected = array(
				'rule' => array( 'alphaNumeric' ),
				'message' => null,
				'required' => null,
				'allowEmpty' => null,
				'on' => null,
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->normalizeValidationRule( array( 'rule' => array( 'minLength', 10 ) ) );
			$expected = array(
				'rule' => array( 'minLength', 10 ),
				'message' => null,
				'required' => null,
				'allowEmpty' => null,
				'on' => null,
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ValidateUtilities::defaultValidationRuleMessage
		 *
		 * @return void
		 */
		public function testDefaultValidationRuleMessage() {
			$result = $this->Site->defaultValidationRuleMessage( null );
			$expected = null;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->defaultValidationRuleMessage( 'notEmpty' );
			$expected = 'Champ obligatoire';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->defaultValidationRuleMessage( array( 'rule' => array( 'maxLength', 10 ) ) );
			$expected = 'Maximum 10 caractères';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->defaultValidationRuleMessage( array( 'rule' => array( 'maxLength', 10 ), 'domain' => 'default' ) );
			$expected = 'Maximum 10 caractères';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ValidateUtilities::beforeValidate
		 *
		 * @return void
		 */
		public function testBeforeValidate() {
			$this->Site->validate['name'] = array( 'notEmpty' );
			$this->Site->create( array( 'Site' => array( 'name' => '' ) ) );
			$result = $this->Site->validates();
			$this->assertFalse( $result );

			$result = $this->Site->validationErrors;
			$expected = array( 'name' => array( 'Champ obligatoire' ) );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ValidateUtilities::setValidationRule
		 *
		 * @return void
		 */
		public function testSetValidationRule() {
			$this->Site->setValidationRule( 'name', 'notEmpty' );
			$result = $this->Site->validate;
			$expected = array(
				'name' => array(
					'notEmpty' => array(
						'rule' => array(
							'notEmpty',
						),
						'message' => null,
						'required' => null,
						'allowEmpty' => null,
						'on' => null,
					)
				)
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$this->Site->setValidationRule( 'name', array( 'rule' => array( 'minLength', 10 ) ) );
			$result = $this->Site->validate;
			$expected = array(
				'name' => array(
					'notEmpty' => array(
						'rule' => array(
							'notEmpty',
						),
						'message' => null,
						'required' => null,
						'allowEmpty' => null,
						'on' => null,
					),
					'minLength' => array(
						'rule' => array(
							'minLength',
							10
						),
						'message' => null,
						'required' => null,
						'allowEmpty' => null,
						'on' => null,
					),
				)
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ValidateUtilities::setValidationRule lorsque le
		 * champ n'existe pas et qu'une exception est renvoyée.
		 *
		 * @expectedException LogicException
		 *
		 * @return void
		 */
		public function testSetValidationRuleException() {
			$this->Site->setValidationRule( 'missing', array( 'rule' => array( 'minLength', 10 ) ) );
		}

		/**
		 * Test de la méthode ValidateUtilities::unsetValidationRule
		 *
		 * @return void
		 */
		public function testUnsetValidationRule() {
			$this->Site->validate = array(
				'name' => array(
					'notEmpty' => array(
						'rule' => array(
							'notEmpty'
						)
					),
					'minLength' => array(
						'rule' => array(
							'minLength',
							10
						)
					),
				)
			);
			$this->Site->unsetValidationRule( 'name', 'notEmpty' );
			$result = $this->Site->validate;
			$expected = array(
				'name' => array(
					'minLength' => array(
						'rule' => array(
							'minLength',
							10
						)
					)
				)
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ValidateUtilities::unsetValidationRule lorsque le
		 * champ n'existe pas et qu'une exception est renvoyée.
		 *
		 * @expectedException LogicException
		 *
		 * @return void
		 */
		public function testUnsetValidationRuleException() {
			$this->Site->unsetValidationRule( 'missing', array( 'rule' => array( 'minLength', 10 ) ) );
		}

		/**
		 * Test de la méthode ValidateUtilities::hasValidationRule
		 *
		 * @return void
		 */
		public function testHasValidationRule() {
			$this->Site->validate = array(
				'name' => array(
					'notEmpty' => array(
						'rule' => array(
							'notEmpty'
						)
					),
					'minLength' => array(
						'rule' => array(
							'minLength',
							10
						)
					),
				)
			);
			$result = $this->Site->hasValidationRule( 'name', 'notEmpty' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ValidateUtilities::hasValidationRule lorsque le
		 * champ n'existe pas et qu'une exception est renvoyée.
		 *
		 * @expectedException LogicException
		 *
		 * @return void
		 */
		public function testHasValidationRuleException() {
			$this->Site->hasValidationRule( 'missing', array( 'rule' => array( 'minLength', 10 ) ) );
		}
	}
?>