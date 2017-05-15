<?php
	/**
	 * Code source de la classe Prestatairefp93Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Prestatairefp93', 'Model' );

	/**
	 * La classe Prestatairefp93Test réalise les tests unitaires de la classe Prestatairefp93.
	 *
	 * @package app.Test.Case.Model
	 */
	class Prestatairefp93Test extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Actionfp93',
			'app.Prestatairefp93',
			'app.Adresseprestatairefp93',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Prestatairefp93
		 */
		public $Prestatairefp93 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Prestatairefp93 = ClassRegistry::init( 'Prestatairefp93' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Prestatairefp93 );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Prestatairefp93::getDependantListCondition()
		 */
		public function testGetDependantListCondition() {
			// 1. PDI
			$result = $this->Prestatairefp93->getDependantListCondition( 'pdi', array() );
			$expected = 'Prestatairefp93.id IN ( SELECT "actionsfps93"."prestatairefp93_id" AS "actionsfps93__prestatairefp93_id" FROM "actionsfps93" AS "actionsfps93"   WHERE "actionsfps93"."prestatairefp93_id" = "Prestatairefp93"."id"    )';

			// 2. Hors PDI
			$result = $this->Prestatairefp93->getDependantListCondition( 'horspdi', array() );
			$expected = '1 = 1';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
