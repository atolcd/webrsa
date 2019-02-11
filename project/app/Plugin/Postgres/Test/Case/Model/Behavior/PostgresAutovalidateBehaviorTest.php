<?php
	/**
	 * Code source de la classe PostgresAutovalidateBehaviorTest.
	 *
	 * PHP 5.3
	 *
	 * @package Postgres
	 * @subpackage Test.Case.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe PostgresAutovalidateBehaviorTest ...
	 *
	 * @package Postgres
	 * @subpackage Test.Case.Model.Behavior
	 */
	class PostgresAutovalidateBehaviorTest extends CakeTestCase
	{
		/**
		 *
		 * @var Model
		 */
		public $User = null;

		/**
		 * Fixtures utilisés par ces tests unitaires.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'plugin.Postgres.PostgresGroup',
			'plugin.Postgres.PostgresUser',
		);

		/**
		 * Préparation du test.
		 *
		 * INFO: ne pas utiliser parent::setUp();
		 */
		public function setUp() {
			$this->User = ClassRegistry::init(
				array(
					'class' => 'Postgres.PostgresUser',
					'alias' => 'User',
					'ds' => 'test',
				)
			);
			$this->User->Behaviors->attach( 'Postgres.PostgresAutovalidate' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->User );
			parent::tearDown();
		}

		/**
		 * Test de la méthode PostgresAutovalidateBehavior::setup()
		 */
		public function testSetup() {
			$result = Hash::get( $this->User->validate, 'active.inList' );
			$expected = array(
				'rule' => array( 'inList', array( '0', '1' ) ),
				'message' => null,
				'required' => null,
				'allowEmpty' => true,
				'on' => null
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = Hash::get( $this->User->validate, 'popularity.inclusiveRange' );
			$expected = array(
				'rule' => array( 'inclusiveRange', '0', '10' ),
				'message' => null,
				'required' => null,
				'allowEmpty' => true,
				'on' => null
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = Hash::get( $this->User->validate, 'phone.phone' );
			$expected = array(
				'rule' => array( 'phone', null, 'fr' ),
				'message' => null,
				'required' => null,
				'allowEmpty' => true,
				'on' => null
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// inList avec des espaces et un nom de colonne entre quotes
			// CHECK (cakephp_validate_in_list("position"::text, ARRAY['in line'::text, 'out of line'::text]))
			$result = Hash::get( $this->User->validate, 'position.inList' );
			$expected = array(
				'rule' => array( 'inList', array( 'in line', 'out of line' ) ),
				'message' => null,
				'required' => null,
				'allowEmpty' => true,
				'on' => null
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>