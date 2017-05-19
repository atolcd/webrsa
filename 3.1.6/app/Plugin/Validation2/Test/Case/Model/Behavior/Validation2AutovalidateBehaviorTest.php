<?php
	/**
	 * Validation2AutovalidateBehaviorTest file
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
	class Validation2AutovalidateBehaviorTest extends CakeTestCase
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
		 * Règles de validation ajoutées par défaut.
		 *
		 * @var array
		 */
		public $validate = array(
			'id' => array(
				'integer' => array(
					'rule' => array( 'integer' ),
					'message' => null,
					'required' => null,
					'allowEmpty' => true,
					'on' => null
				)
			),
			'name' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' ),
					'message' => null,
					'required' => null,
					'allowEmpty' => false,
					'on' => null
				),
				'maxLength' => array(
					'rule' => array( 'maxLength', 255 ),
					'message' => null,
					'required' => null,
					'allowEmpty' => true,
					'on' => null
				),
				'isUnique' => array(
					'rule' => array( 'isUnique' ),
					'message' => null,
					'required' => NULL,
					'allowEmpty' => true,
					'on' => NULL,
				),
			),
			'user_id' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' ),
					'message' => null,
					'required' => null,
					'allowEmpty' => false,
					'on' => null
				),
				'integer' => array(
					'rule' => array( 'integer' ),
					'message' => null,
					'required' => null,
					'allowEmpty' => true,
					'on' => null
				)
			),
			'price' => array(
				'numeric' => array(
					'rule' => array( 'numeric' ),
					'message' => null,
					'required' => null,
					'allowEmpty' => true,
					'on' => null
				)
			),
			'published' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' ),
					'message' => null,
					'required' => null,
					'allowEmpty' => false,
					'on' => null
				)
			),
			'document' => array( ),
			'description' => array( ),
			'birthday' => array(
				'date' => array(
					'rule' => array( 'date' ),
					'message' => null,
					'required' => null,
					'allowEmpty' => true,
					'on' => null
				)
			),
			'birthtime' => array(
				'time' => array(
					'rule' => array( 'time' ),
					'message' => null,
					'required' => null,
					'allowEmpty' => true,
					'on' => null,
				)
			),
			'created' => array(
				'datetime' => array(
					'rule' => array( 'datetime' ),
					'message' => null,
					'required' => null,
					'allowEmpty' => true,
					'on' => null,
				)
			),
			'updated' => array(
				'datetime' => array(
					'rule' => array( 'datetime' ),
					'message' => null,
					'required' => null,
					'allowEmpty' => true,
					'on' => null,
				)
			),
		);

		/**
		 * Method executed before each test
		 */
		public function setUp() {
			parent::setUp();
			$this->Site = ClassRegistry::init( array('class' => 'Validation2.Validation2Site', 'alias' => 'Site') );
			$this->Site->Behaviors->attach( 'Validation2.Validation2Autovalidate' );
		}

		/**
		 * Method executed after each test
		 */
		public function tearDown() {
			unset( $this->Site );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Validation2AutovalidateBehavior::setup() du
		 * plugin Validation2.
		 *
		 * @return void
		 */
		public function testSetup() {
			$this->assertEquals( $this->validate, $this->Site->validate, var_export( $this->Site->validate, true ) );

		}

		/**
		 * Test de la méthode Validation2AutovalidateBehavior::beforeValidate() du
		 * plugin Validation2.
		 *
		 * @medium
		 *
		 * @return void
		 */
		public function testBeforeValidate() {
			$validate = $this->validate;
			foreach( $validate as $field => $rules ) {
				foreach( $rules as $ruleName => $rule ) {
					$params = array_values( array_slice( $rule['rule'], 1 ) );
					$domain = Hash::get( $this->Site->Behaviors->Validation2Autovalidate->settings, "{$this->Site->alias}.domain" );
					$validate[$field][$ruleName]['message'] = call_user_func_array( 'sprintf', Hash::merge( array( __d( 'default', "Validate::{$ruleName}" ) ), $params ) );
				}
			}

			$this->Site->create(array());
			$this->Site->validates();

			$this->assertEquals( $validate, $this->Site->validate, var_export( $this->Site->validate, true ) );
		}
	}
?>