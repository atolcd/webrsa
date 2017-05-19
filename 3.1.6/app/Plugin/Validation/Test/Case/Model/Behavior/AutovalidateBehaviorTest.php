<?php
	/**
	 * AutovalidateBehaviorTest file
	 *
	 * PHP 5.3
	 *
	 * @package Validation
	 * @subpackage Test.Case.Model.Behavior
	 */
	App::uses( 'Model', 'Model' );
	App::uses( 'AppModel', 'Model' );
	App::uses( 'AutovalidateBehavior', 'Validation.Model/Behavior' );

	/**
	 * AutovalidateTest class
	 *
	 * @package Validation
	 * @subpackage Test.Case.Model.Behavior
	 */
	class AutovalidateBehaviorTest extends CakeTestCase
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
		 * Les règles de validation déduites.
		 *
		 * @var array
		 */
		protected $_expectedDeducedRules = array(
			'id' => array(
				'integer' => array(
					'rule' => array( 'integer' ),
					'message' => null,
					'required' => null,
					'allowEmpty' => true,
					'on' => null,
				),
			),
			'name' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' ),
					'message' => null,
					'required' => null,
					'allowEmpty' => null,
					'on' => null,
				),
				'maxLength' => array(
					'rule' => array( 'maxLength', 250 ),
					'message' => null,
					'required' => null,
					'allowEmpty' => true,
					'on' => null,

				),
				'isUnique' => array (
					'rule' => array ( 'isUnique' ),
					'message' => NULL,
					'required' => NULL,
					'allowEmpty' => true,
					'on' => NULL,
				),
			),
			'price' => array (
				'numeric' => array (
					'rule' => array ( 'numeric' ),
					'message' => NULL,
					'required' => NULL,
					'allowEmpty' => true,
					'on' => NULL,
				),
			),
			'birthday' => array (
				'date' => array (
					'rule' => array ( 'date' ),
					'message' => NULL,
					'required' => NULL,
					'allowEmpty' => true,
					'on' => NULL
				),
			),
		);

		/**
		 * Method executed before each test
		 *
		 */
		public function setUp() {
			parent::setUp();
			foreach( $this->fixtures as $fixture ) {
				$tokens = explode( '.', $fixture );
				$modelName = Inflector::camelize( $tokens[1] );
				$this->{$modelName} = ClassRegistry::init( $modelName );
				$this->{$modelName}->validate = array();
				$this->{$modelName}->Behaviors->attach( 'Validation.Autovalidate' );
			}
		}

		/**
		 * Method executed after each test
		 *
		 */
		public function tearDown() {
			foreach( $this->fixtures as $fixture ) {
				$tokens = explode( '.', $fixture );
				$modelName = Inflector::camelize( $tokens[1] );
				unset( $this->{$modelName} );
			}
			parent::tearDown();
		}

		/**
		 * Test de la méthode Autovalidate::setup
		 *
		 * @fixme: on dirait qu'il utilise la classe AutovalidateBehavior qui
		 *	n'est pas dans le plugin lorsqu'on fait passer tous les tests.
		 *
		 * @return void
		 */
		public function testAutovalidationRules() {
			$this->assertEquals(
				$this->_expectedDeducedRules,
				$this->Site->validate,
				var_export( $this->Site->validate, true )
			);
		}

		/**
		 * Test de la méthode Autovalidate::setup sans le cache.
		 *
		 * @fixme: on dirait qu'il utilise la classe AutovalidateBehavior qui
		 *	n'est pas dans le plugin lorsqu'on fait passer tous les tests.
		 *
		 * @return void
		 */
		public function testAutovalidationRulesWithoutCache() {
			Cache::clear();
			$this->assertEquals(
				$this->_expectedDeducedRules,
				$this->Site->validate,
				var_export( $this->Site->validate, true )
			);
		}
	}
?>