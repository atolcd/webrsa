<?php
	/**
	 * Validation2Validation2BehaviorTest file
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
	class Validation2Validation2BehaviorTest extends CakeTestCase
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
			$this->Site->Behaviors->attach( 'Validation2.Validation2Validation2' );
		}

		/**
		 * Method executed after each test
		 */
		public function tearDown() {
			unset( $this->Site );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Validation2Validation2Behavior::beforeValidate() du
		 * plugin Validation2.
		 *
		 * @return void
		 */
		public function testBeforeValidate() {
			$behaviors = array(
				'Validation2.Validation2Formattable',
				'Validation2.Validation2RulesComparison',
				'Validation2.Validation2RulesFieldtypes',
			);
			foreach( $behaviors as $behavior ) {
				list( $pluginName, $behaviorName ) = pluginSplit( $behavior );
				$this->assertFalse( isset( $this->Site->Behaviors->{$behaviorName} ) );
			}

			$this->Site->create( array() );
			$this->Site->validates();

			foreach( $behaviors as $behavior ) {
				list( $pluginName, $behaviorName ) = pluginSplit( $behavior );
				$this->assertTrue( isset( $this->Site->Behaviors->{$behaviorName} ) );
			}
		}

		/**
		 * Test de la méthode Validation2Validation2Behavior::beforeSave() du
		 * plugin Validation2.
		 *
		 * @return void
		 */
		public function testBeforeSave() {
			$behaviors = array(
				'Validation2.Validation2Formattable',
				'Validation2.Validation2RulesComparison',
				'Validation2.Validation2RulesFieldtypes',
			);
			foreach( $behaviors as $behavior ) {
				list( $pluginName, $behaviorName ) = pluginSplit( $behavior );
				$this->assertFalse( isset( $this->Site->Behaviors->{$behaviorName} ) );
			}

			$data = array(
				'Site' => array(
					'name' => 'Foo',
					'user_id' => 1,
					'published' => true,
				)
			);
			$this->Site->create( $data );
			$this->Site->save();

			foreach( $behaviors as $behavior ) {
				list( $pluginName, $behaviorName ) = pluginSplit( $behavior );
				$this->assertTrue( isset( $this->Site->Behaviors->{$behaviorName} ) );
			}
		}

		/**
		 * Test de la méthode de chargement différé de Validation2Validation2
		 * lorsque le behavior configuré n'existe pas.
		 *
		 * La couverture de code n'a pas l'air de le prendre en compte, mais le
		 * test est tout de même fait. Pour s'en convaincre, changer le nom de
		 * l'exception attendue.
		 *
		 * @expectedException MissingBehaviorException
		 *
		 * @return void
		 */
		public function testLazyLoadException() {
			$config = array(
				'InexistantRuleset'
			);

			$this->Site->Behaviors->detach( 'Validation2.Validation2Validation2' );
			$this->Site->Behaviors->attach( 'Validation2.Validation2Validation2', $config );
			$this->Site->create( array( 'Site' => array( 'id' => 5 ) ) );
			$this->Site->validates();
		}
	}
?>