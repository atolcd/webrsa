<?php
	/**
	 * Code source de la classe LinkedRecordsBehaviorTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'LinkedRecordsBehavior', 'Model/Behavior' );

	/**
	 * La classe LinkedRecordsBehaviorTest réalise les tests unitaires de la
	 * classe LinkedRecordsBehavior.
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class LinkedRecordsBehaviorTest extends CakeTestCase
	{
		/**
		 * Modèle Foyer utilisé par ce test.
		 *
		 * @var Model
		 */
		public $Foyer = null;

		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Foyer',
			'app.Personne',
			'app.Prestation',
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Foyer = ClassRegistry::init( 'Foyer' );
			$this->Foyer->Behaviors->attach( 'DatabaseTable' );
			$this->Foyer->bindModel( array( 'hasMany' => array( 'Personne' ) ), false );

			$this->Foyer->Personne->Behaviors->attach( 'DatabaseTable' );
			$this->Foyer->Behaviors->attach( 'LinkedRecords' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Foyer );
			parent::tearDown();
		}

		/**
		 * Test de la méthode LinkedRecordsBehavior::linkedRecordVirtualFieldName()
                 * 
                 * @medium
		 */
		public function testLinkedRecordVirtualFieldName() {
			$result = $this->Foyer->linkedRecordVirtualFieldName( 'Personne' );
			$expected = 'has_personne';
			$this->assertEquals( $expected, $result );
		}

		/**
		 * Test de la méthode LinkedRecordsBehavior::linkedRecordVirtualField()
		 */
		public function testLinkedRecordVirtualField() {
			// Sans argument particulier
			$result = $this->Foyer->linkedRecordVirtualField( 'Personne' );
			$expected = 'EXISTS( SELECT "personnes"."id" AS "personnes__id" FROM "personnes" AS "personnes"   WHERE "personnes"."foyer_id" = "Foyer"."id"    )';
			$this->assertEquals( $expected, $result );

			// Avec une condition supplémentaire
			$querydata = array(
				'conditions' => array( 'Personne.name' => 'article1' )
			);
			$result = $this->Foyer->linkedRecordVirtualField( 'Personne', $querydata );
			$expected = 'EXISTS( SELECT "personnes"."id" AS "personnes__id" FROM "personnes" AS "personnes"   WHERE "personnes"."name" = \'article1\' AND "personnes"."foyer_id" = "Foyer"."id"    )';
			$this->assertEquals( $expected, $result );
		}

		/**
		 * Test de la méthode LinkedRecordsBehavior::linkedRecordVirtualField()
		 * avec des jointures.
		 */
		public function testLinkedRecordVirtualFieldWithJoins() {
			$querydata = array(
				'contain' => false,
				'joins' => array(
					$this->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) )
				),
				'conditions' => array( 'Personne.nom' => 'Buffin' ),
			);
			$result = $this->Foyer->linkedRecordVirtualField( 'Personne', $querydata );
			$expected = 'EXISTS( SELECT "personnes"."id" AS "personnes__id" FROM "personnes" AS "personnes" INNER JOIN "public"."prestations" AS "prestations" ON ("prestations"."personne_id" = "personnes"."id" AND "prestations"."natprest" = \'RSA\')  WHERE "personnes"."nom" = \'Buffin\' AND "personnes"."foyer_id" = "Foyer"."id"    )';
			$this->assertEquals( $expected, $result );
		}

		/**
		 * Test de la méthode LinkedRecordsBehavior::linkedRecordsCompleteQuerydata()
		 */
		public function testLinkedRecordsCompleteQuerydata() {
			$querydata = array(
				'fields' => array(
					'Foyer.id',
					'Foyer.name',
				),
				'conditions' => array(
					'Foyer.id >' => 10
				),
				'contain' => false,
				'order' => array( 'Foyer.id DESC' )
			);
			$result = $this->Foyer->linkedRecordsCompleteQuerydata( $querydata, 'Personne' );
			$expected = array(
				'fields' => array(
					'Foyer.id',
					'Foyer.name',
					'( EXISTS( SELECT "personnes"."id" AS "personnes__id" FROM "personnes" AS "personnes"   WHERE "personnes"."foyer_id" = "Foyer"."id"    ) ) AS "Foyer__has_personne"'
				),
				'conditions' => array(
					'Foyer.id >' => 10
				),
				'contain' => false,
				'order' => array( 'Foyer.id DESC' )
			);
			$this->assertEquals( $expected, $result );
		}

		/**
		 * Test de la méthode LinkedRecordsBehavior::linkedRecordsLoadVirtualFields()
		 */
		public function testLinkedRecordsLoadVirtualFields() {
			// 1. Pour un modèle lié, sans rien de particulier
			$this->Foyer->virtualFields = array();
			$this->Foyer->linkedRecordsLoadVirtualFields( 'Personne' );
			$result = $this->Foyer->virtualFields;
			$expected = array(
				'has_personne' => 'EXISTS( SELECT "personnes"."id" AS "personnes__id" FROM "personnes" AS "personnes"   WHERE "personnes"."foyer_id" = "Foyer"."id"    )'
			);
			$this->assertEquals( $expected, $result );

			// 2. Pour un modèle lié, avec une condition et une jointure
			$this->Foyer->virtualFields = array();
			$querydata = array(
				'contain' => false,
				'joins' => array(
					$this->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) )
				),
				'conditions' => array( 'Personne.nom' => 'Buffin' ),
			);
			$this->Foyer->linkedRecordsLoadVirtualFields( array( 'Personne' => $querydata ) );
			$result = $this->Foyer->virtualFields;
			$expected = array(
				'has_personne' => 'EXISTS( SELECT "personnes"."id" AS "personnes__id" FROM "personnes" AS "personnes" INNER JOIN "public"."prestations" AS "prestations" ON ("prestations"."personne_id" = "personnes"."id" AND "prestations"."natprest" = \'RSA\')  WHERE "personnes"."nom" = \'Buffin\' AND "personnes"."foyer_id" = "Foyer"."id"    )'
			);
			$this->assertEquals( $expected, $result );
		}
	}
?>