<?php
	/**
	 * Validation2FormattableBehaviorTest file
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
	class Validation2FormattableBehaviorTest extends CakeTestCase
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
		 * Jeu de données et de résultats attendus.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'data' => array(
					'Site' => array(
						'name' => ' X   ',
						'price' => ' '
					)
				),
				'expected' => array(
					'Site' => array(
						'name' => 'X',
						'price' => null
					)
				)
			),
			array(
				'data' => array(
					'Site' => array(
						'id' => null,
						'name' => ' X   ',
						'user_id' => '255_25',
						'price' => ' 6 666,987 ',
						'published' => null,
						'description' => null,
						'birthday' => ' ',
						'birthtime' => ' ',
						'document' => ' XXX '
					)
				),
				'expected' => array(
					'Site' => array(
						'id' => null,
						'name' => 'X',
						'user_id' => 25,
						'price' => 6666.987,
						'published' => null,
						'description' => null,
						'birthday' => null,
						'birthtime' => null,
						'document' => ' XXX '
					)
				)
			),
		);

		/**
		 * Method executed before each test
		 */
		public function setUp() {
			parent::setUp();
			$this->Site = ClassRegistry::init( array('class' => 'Validation2.Validation2Site', 'alias' => 'Site') );
			$this->Site->Behaviors->attach( 'Validation2.Validation2Formattable' );
		}

		/**
		 * Method executed after each test
		 */
		public function tearDown() {
			unset( $this->Site );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Validation2FormattableBehavior::beforeValidate() du
		 * plugin Validation2.
		 *
		 * @return void
		 */
		public function testBeforeValidate() {
			foreach( $this->records as $record ) {
				$this->Site->create( $record['data'] );
				$this->Site->validates();
				$result = $this->Site->data;
				$this->assertEquals( $record['expected'], $result, var_export( $result, true ) );
			}
		}

		/**
		 * Test de la méthode Validation2FormattableBehavior::beforeSave() du
		 * plugin Validation2.
		 *
		 * @return void
		 */
		public function testBeforeSave() {
			foreach( $this->records as $record ) {
				$this->Site->create( $record['data'] );
				$this->Site->Behaviors->trigger( 'beforeSave', array( $this->Site ) );
				$result = $this->Site->data;

				$this->assertEquals( $record['expected'], $result, var_export( $result, true ) );
			}
		}

		/**
		 * Test de la méthode ValidateUtilities::doFormatting() lorsqu'une
		 * exception est renvoyée.
		 *
		 * @expectedException MissingUtilityException
		 *
		 * @return void
		 */
		public function testDoFormattingException() {
			$config = array(
				'InexistantFormatter' => array(
					'null' => true,
				)
			);

			$this->Site->Behaviors->detach( 'Validation2.Validation2Formattable' );
			$this->Site->Behaviors->attach( 'Validation2.Validation2Formattable', $config );
			$this->Site->doFormatting( array( 'Site' => array( 'id' => 5 ) ) );
		}
	}
?>