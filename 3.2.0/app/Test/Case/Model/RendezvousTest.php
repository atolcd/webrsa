<?php
	class RendezvousTest extends CakeTestCase
	{
		/**
		 * Fixtures associated with this test case
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Dossierep',
			'app.Foyer',
			'app.Passagecommissionep',
			'app.Personne',
			'app.Rendezvous',
			'app.RendezvousThematiquerdv',
			'app.Thematiquerdv'
		);

		/**
		 * Méthode exécutée avant chaque test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Rendezvous = ClassRegistry::init( 'Rendezvous' );
		}

		/**
		 * Méthode exécutée après chaque test.
		 */
		public function tearDown() {
			unset( $this->Rendezvous );
		}

		/**
		 * Test de la méthode Rendezvous::dossierId().
		 *
		 * @return void
		 */
		public function testDossierId() {
			$result = $this->Rendezvous->dossierId( 1 );
			$expected = 1;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = $this->Rendezvous->dossierId( 666 );
			$expected = null;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Rendezvous::conditionsThematique()
		 */
		public function testConditionsThematique() {
			$result = $this->Rendezvous->WebrsaRendezvous->conditionsThematique(
				array(),
				array(
					'Rendezvous' => array(
						'thematiquerdv_id' => array(
							0 => 3,
							1 => 5
						)
					)
				),
				'Rendezvous.thematiquerdv_id'
			);
			$expected = array( 'Rendezvous.id IN ( SELECT "rendezvous_thematiquesrdvs"."rendezvous_id" AS "rendezvous_thematiquesrdvs__rendezvous_id" FROM "thematiquesrdvs" AS "thematiquesrdvs" INNER JOIN "public"."rendezvous_thematiquesrdvs" AS "rendezvous_thematiquesrdvs" ON ("rendezvous_thematiquesrdvs"."rendezvous_id" = "Rendezvous"."id")  WHERE "rendezvous_thematiquesrdvs"."rendezvous_id" = "Rendezvous"."id" AND "rendezvous_thematiquesrdvs"."thematiquerdv_id" IN (\'3\', \'5\')    )' );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>