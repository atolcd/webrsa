<?php
	/**
	 * Code source de la classe WebrsaStructurereferentelieeBehaviorTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaStructurereferenteliee', 'Model/Behavior' );

	/**
	 * La classe WebrsaStructurereferentelieeBehaviorTest ...
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class WebrsaStructurereferentelieeBehaviorTest extends CakeTestCase
	{

		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Referent',
			'app.Rendezvous',
			'app.RendezvousThematiquerdv',
			'app.Structurereferente',
			'app.Thematiquerdv'
		);

		// ---------------------------------------------------------------------

		/**
		 * Méthode utilitaire permettant d'ajouter une clé étrangère dans la base
		 * de données.
		 *
		 * @param DataSource $Dbo
		 * @param string $from
		 * @param string $to
		 * @return boolean
		 */
		public function addForeignkey( DataSource $Dbo, $from, $to ) {
			list( $fromTable, $fromField ) = model_field( $from );
			list( $toTable, $toField ) = model_field( $to );
			$sql = "ALTER TABLE {$fromTable} ADD CONSTRAINT {$fromTable}_{$fromField}_fk FOREIGN KEY ({$fromField}) REFERENCES {$toTable} ({$toField}) ON DELETE CASCADE ON UPDATE CASCADE;";
			return ( $Dbo->query( $sql ) !== false );
		}

		/**
		 * Méthode utilitaire permettant de supprimer une clé étrangère dans la
		 * base de données.
		 *
		 * @param DataSource $Dbo
		 * @param string $from
		 * @return boolean
		 */
		public function dropForeignkey( DataSource $Dbo, $from ) {
			list( $fromTable, $fromField ) = model_field( $from );
			$sql = "ALTER TABLE {$fromTable} DROP CONSTRAINT {$fromTable}_{$fromField}_fk;";
			return ( $Dbo->query( $sql ) !== false );
		}

		// ---------------------------------------------------------------------

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Rendezvous = ClassRegistry::init( 'Rendezvous' );
			$this->Rendezvous->Behaviors->attach( 'WebrsaStructurereferenteliee' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Rendezvous );
			parent::tearDown();
		}

		/**
		 * Test de la méthode WebrsaStructurereferentelieeBehavior::structurereferenteHorszone()
		 */
		public function testStructurereferenteHorszone() {
			// 1. Avec des ids de structures référentes passées en paramètres
			$result = $this->Rendezvous->structurereferenteHorszone( 'Rendezvous.structurereferente_id', array( 1, 2 ) );
			$expected = '( NOT ("Rendezvous"."structurereferente_id" IN (1, 2)) ) AS "Referent__horszone"';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Sans id de structure référente passé en paramètres
			$result = $this->Rendezvous->structurereferenteHorszone( 'Rendezvous.structurereferente_id', array() );
			$expected = '( FALSE ) AS "Referent__horszone"';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3. Sans alias
			$result = $this->Rendezvous->structurereferenteHorszone( 'Rendezvous.structurereferente_id', array( 1, 2 ), array( 'alias' => false ) );
			$expected = 'NOT ("Rendezvous"."structurereferente_id" IN (1, 2))';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 4. Avec un autre alias
			$result = $this->Rendezvous->structurereferenteHorszone( 'Rendezvous.structurereferente_id', array( 1, 2 ), array( 'alias' => 'Foo.bar' ) );
			$expected = '( NOT ("Rendezvous"."structurereferente_id" IN (1, 2)) ) AS "Foo__bar"';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode WebrsaStructurereferentelieeBehavior::referentHorszone()
		 */
		public function testReferentHorszone() {
			// 1. Avec des ids de structures référentes passées en paramètres
			$result = $this->Rendezvous->referentHorszone( 'Rendezvous.referent_id', array( 1, 2 ) );
			$expected = '( "Rendezvous"."referent_id" IN ( SELECT "referents"."id" AS "Referent__id" FROM "referents" AS "referents"   WHERE "referents"."id" = "Rendezvous"."referent_id" AND NOT ("referents"."structurereferente_id" IN (1, 2))    ) ) AS "Referent__horszone"';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Sans id de structure référente passé en paramètres
			$result = $this->Rendezvous->referentHorszone( 'Rendezvous.referent_id', array() );
			$expected = '( FALSE ) AS "Referent__horszone"';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3. Sans alias
			$result = $this->Rendezvous->referentHorszone( 'Rendezvous.referent_id', array( 1, 2 ), array( 'alias' => false ) );
			$expected = '"Rendezvous"."referent_id" IN ( SELECT "referents"."id" AS "Referent__id" FROM "referents" AS "referents"   WHERE "referents"."id" = "Rendezvous"."referent_id" AND NOT ("referents"."structurereferente_id" IN (1, 2))    )';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 4. Avec un autre alias
			$result = $this->Rendezvous->referentHorszone( 'Rendezvous.referent_id', array( 1, 2 ), array( 'alias' => 'Foo.bar' ) );
			$expected = '( "Rendezvous"."referent_id" IN ( SELECT "referents"."id" AS "Referent__id" FROM "referents" AS "referents"   WHERE "referents"."id" = "Rendezvous"."referent_id" AND NOT ("referents"."structurereferente_id" IN (1, 2))    ) ) AS "Foo__bar"';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode WebrsaStructurereferentelieeBehavior::completeQueryHorsZone()
		 */
		public function testCompleteQueryHorsZone() {
			// 1. Appel simple
			$query = array(
				'fields' => array( 'Rendezvous.id' ),
				'contain' => false
			);
			$result = $this->Rendezvous->completeQueryHorsZone(
				$query,
				array( 1, 2 ),
				array(
					'structurereferente_id' => 'Rendezvous.structurereferente_id',
					'referent_id' => 'Rendezvous.rereferent_id'
				)
			);
			$expected = array(
				'fields' => array(
					'Rendezvous.id',
					'( NOT ("Rendezvous"."structurereferente_id" IN (1, 2)) ) AS "Referent__horszone"',
				),
				'contain' => false,
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. En se basant sur le champ referent_id
			$query = array(
				'fields' => array( 'Rendezvous.id' ),
				'contain' => false
			);
			$result = $this->Rendezvous->completeQueryHorsZone(
				$query,
				array( 1, 2 ),
				array(
					'structurereferente_id' => null,
					'referent_id' => 'Rendezvous.rereferent_id'
				)
			);
			$expected = array(
				'fields' => array(
					'Rendezvous.id',
					'( "Rendezvous"."rereferent_id" IN ( SELECT "referents"."id" AS "Referent__id" FROM "referents" AS "referents"   WHERE "referents"."id" = "Rendezvous"."rereferent_id" AND NOT ("referents"."structurereferente_id" IN (1, 2))    ) ) AS "Referent__horszone"'
				),
				'contain' => false,
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3. Sans ces champs, mais avec un alias
			$query = array(
				'fields' => array( 'Rendezvous.id' ),
				'contain' => false
			);
			$result = $this->Rendezvous->completeQueryHorsZone(
				$query,
				array( 1, 2 ),
				array(
					'structurereferente_id' => null,
					'referent_id' => null,
					'alias' => 'Foo.bar'
				)
			);
			$expected = array(
				'fields' => array(
					'Rendezvous.id',
					'NULL AS "Foo__bar"',
				),
				'contain' => false,
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode WebrsaStructurereferentelieeBehavior::links()
		 */
		public function testLinks() {
			$Dbo = $this->Rendezvous->getDataSource();
			$this->addForeignkey( $Dbo, 'rendezvous.structurereferente_id', 'structuresreferentes.id' );
			$this->addForeignkey( $Dbo, 'rendezvous.referent_id', 'referents.id' );

			$result = $this->Rendezvous->links();

			$this->dropForeignkey( $Dbo, 'rendezvous.structurereferente_id' );
			$this->dropForeignkey( $Dbo, 'rendezvous.referent_id' );

			$expected = array(
				'structurereferente_id' => 'structurereferente_id',
				'referent_id' => 'referent_id'
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>