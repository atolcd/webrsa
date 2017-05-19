<?php
	/**
	 * Code source de la classe DetailcalculdroitrsaTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Detailcalculdroitrsa', 'Model' );

	/**
	 * La classe DetailcalculdroitrsaTest réalise les tests unitaires de la classe
	 * Detailcalculdroitrsa.
	 *
	 * @package app.Test.Case.Model
	 */
	class DetailcalculdroitrsaTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Detailcalculdroitrsa',
			'app.Detaildroitrsa'
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Detailcalculdroitrsa = ClassRegistry::init( 'Detailcalculdroitrsa' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Detailcalculdroitrsa );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Detailcalculdroitrsa::__constructor()
		 */
		public function testConstructor() {
			$result = $this->Detailcalculdroitrsa->virtualFields;
			$expected = array (
				'natpf_socle' => '"Detailcalculdroitrsa"."natpf" IN ( \'RSD\', \'RSI\', \'RSU\', \'RSB\', \'RSJ\' )',
				'natpf_activite' => '"Detailcalculdroitrsa"."natpf" IN ( \'RCD\', \'RCI\', \'RCU\', \'RCB\', \'RCJ\' )',
				'natpf_majore' => '"Detailcalculdroitrsa"."natpf" IN ( \'RSI\', \'RCI\' )',
			);
			$message = var_export( $result, true );
			$this->assertEqual($result, $expected, $message);
		}

		/**
		 * Test de la méthode Detailcalculdroitrsa::vfsSummary()
		 */
		public function testVfsSummary() {
			$result = $this->Detailcalculdroitrsa->vfsSummary();
			$expected = array (
				'activite' => '( EXISTS( SELECT "Detailcalculdroitrsa"."id" AS "Detailcalculdroitrsa__id" FROM "detailscalculsdroitsrsa" AS "Detailcalculdroitrsa"   WHERE "Detailcalculdroitrsa"."detaildroitrsa_id" = "Detaildroitrsa"."id" AND "Detailcalculdroitrsa"."natpf" IN (\'RCD\', \'RCI\', \'RCU\', \'RCB\', \'RCJ\')    ) ) AS "Detailcalculdroitrsa__natpf_activite"',
				'majore' => '( EXISTS( SELECT "Detailcalculdroitrsa"."id" AS "Detailcalculdroitrsa__id" FROM "detailscalculsdroitsrsa" AS "Detailcalculdroitrsa"   WHERE "Detailcalculdroitrsa"."detaildroitrsa_id" = "Detaildroitrsa"."id" AND "Detailcalculdroitrsa"."natpf" IN (\'RSI\', \'RCI\')    ) ) AS "Detailcalculdroitrsa__natpf_majore"',
				'socle' => '( EXISTS( SELECT "Detailcalculdroitrsa"."id" AS "Detailcalculdroitrsa__id" FROM "detailscalculsdroitsrsa" AS "Detailcalculdroitrsa"   WHERE "Detailcalculdroitrsa"."detaildroitrsa_id" = "Detaildroitrsa"."id" AND "Detailcalculdroitrsa"."natpf" IN (\'RSD\', \'RSI\', \'RSU\', \'RSB\', \'RSJ\')    ) ) AS "Detailcalculdroitrsa__natpf_socle"',
			);
			$message = var_export( $result, true );
			$this->assertEqual($result, $expected, $message);
		}

		/**
		 * Test de la méthode Detailcalculdroitrsa::vfsSummary() avec un alias
		 * et des conditions.
		 */
		public function testVfsSummaryAliasConditions() {
			$result = $this->Detailcalculdroitrsa->vfsSummary( 'Detaildroitrsa', array( 'Detailcalculdroitrsa.detaildroitrsa_id = Detaildroitrsa.id', 'Detaildroitrsa.dossier_id' => 5 ) );
			$expected = array (
				'activite' => '( EXISTS( SELECT "Detailcalculdroitrsa"."id" AS "Detailcalculdroitrsa__id" FROM "detailscalculsdroitsrsa" AS "Detailcalculdroitrsa"   WHERE "Detailcalculdroitrsa"."detaildroitrsa_id" = "Detaildroitrsa"."id" AND "Detaildroitrsa"."dossier_id" = 5 AND "Detailcalculdroitrsa"."natpf" IN (\'RCD\', \'RCI\', \'RCU\', \'RCB\', \'RCJ\')    ) ) AS "Detaildroitrsa__natpf_activite"',
				'majore' => '( EXISTS( SELECT "Detailcalculdroitrsa"."id" AS "Detailcalculdroitrsa__id" FROM "detailscalculsdroitsrsa" AS "Detailcalculdroitrsa"   WHERE "Detailcalculdroitrsa"."detaildroitrsa_id" = "Detaildroitrsa"."id" AND "Detaildroitrsa"."dossier_id" = 5 AND "Detailcalculdroitrsa"."natpf" IN (\'RSI\', \'RCI\')    ) ) AS "Detaildroitrsa__natpf_majore"',
				'socle' => '( EXISTS( SELECT "Detailcalculdroitrsa"."id" AS "Detailcalculdroitrsa__id" FROM "detailscalculsdroitsrsa" AS "Detailcalculdroitrsa"   WHERE "Detailcalculdroitrsa"."detaildroitrsa_id" = "Detaildroitrsa"."id" AND "Detaildroitrsa"."dossier_id" = 5 AND "Detailcalculdroitrsa"."natpf" IN (\'RSD\', \'RSI\', \'RSU\', \'RSB\', \'RSJ\')    ) ) AS "Detaildroitrsa__natpf_socle"',
			);
			$message = var_export( $result, true );
			$this->assertEqual($result, $expected, $message);
		}
	}
?>
