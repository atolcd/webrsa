<?php
	/**
	 * Code source de la classe Categoriefp93Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Categoriefp93', 'Model' );
	/**
	 * La classe Categoriefp93Test réalise les tests unitaires de la classe Categoriefp93.
	 *
	 * @package app.Test.Case.Model
	 */
	class Categoriefp93Test extends CakeTestCase
	{

		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Actionfp93',
			'app.Categoriefp93',
			'app.Filierefp93',
			'app.Thematiquefp93',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Categoriefp93
		 */
		public $Categoriefp93 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Categoriefp93 = ClassRegistry::init( 'Categoriefp93' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Categoriefp93 );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Categoriefp93::getDependantListCondition()
		 */
		public function testGetDependantListCondition() {
			// 1. PDI
			$result = $this->Categoriefp93->getDependantListCondition( 'pdi', array( ) );
			$expected = 'Categoriefp93.id IN ( SELECT "filieresfps93"."categoriefp93_id" AS "filieresfps93__categoriefp93_id" FROM "filieresfps93" AS "filieresfps93" INNER JOIN "public"."actionsfps93" AS "actionsfps93" ON ("actionsfps93"."filierefp93_id" = "filieresfps93"."id")  WHERE "filieresfps93"."categoriefp93_id" = "Categoriefp93"."id"    )';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Hors PDI
			$result = $this->Categoriefp93->getDependantListCondition( 'horspdi', array( ) );
			$expected = 'Categoriefp93.id IN ( SELECT "filieresfps93"."categoriefp93_id" AS "filieresfps93__categoriefp93_id" FROM "filieresfps93" AS "filieresfps93"   WHERE "filieresfps93"."categoriefp93_id" = "Categoriefp93"."id"    )';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. PDI, avec une condition
			$result = $this->Categoriefp93->getDependantListCondition( 'pdi', array( 'Filierefp93.name' => 'Foo' ) );
			$expected = 'Categoriefp93.id IN ( SELECT "filieresfps93"."categoriefp93_id" AS "filieresfps93__categoriefp93_id" FROM "filieresfps93" AS "filieresfps93" INNER JOIN "public"."actionsfps93" AS "actionsfps93" ON ("actionsfps93"."filierefp93_id" = "filieresfps93"."id")  WHERE "filieresfps93"."name" = \'Foo\' AND "filieresfps93"."categoriefp93_id" = "Categoriefp93"."id"    )';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Categoriefp93::saveParametrage()
		 */
		public function testSaveParametrage() {
			$data = array(
				'Categoriefp93' => array(
					'id' => '',
					'typethematiquefp93_id' => 'pdi',
					'thematiquefp93_id' => 'pdi_1',
					'name' => 'Catégorie de test supplémentaire',
				)
			);
			$result = $this->Categoriefp93->saveParametrage( $data );
			$this->assertTrue( $result );
		}

		/**
		 * Test de la méthode Categoriefp93::getParametrageFields()
		 */
		public function testGetParametrageFields() {
			$result = $this->Categoriefp93->getParametrageFields( 'pdi', array( ) );
			$expected = array(
				'Categoriefp93.id' => array( ),
				'Categoriefp93.typethematiquefp93_id' => array(
					'empty' => true,
				),
				'Categoriefp93.thematiquefp93_id' => array(
					'empty' => true,
				),
				'Categoriefp93.name' => array(),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Categoriefp93::getParametrageFormData()
		 */
		public function testGetParametrageFormData() {
			$result = $this->Categoriefp93->getParametrageFormData( 1 );
			$expected = array(
				'Categoriefp93' => array(
					'id' => 1,
					'typethematiquefp93_id' => 'pdi',
					'thematiquefp93_id' => 'pdi_1',
					'name' => 'Catégorie de test',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Categoriefp93::getParametrageDependantFields()
		 */
		public function testGetParametrageDependantFields() {
			// 1. sans descendant
			$result = $this->Categoriefp93->getParametrageDependantFields();
			$expected = array(
				'Categoriefp93.typethematiquefp93_id' => 'Categoriefp93.thematiquefp93_id',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Categoriefp93::getParametrageOptions()
		 */
		public function testGetParametrageOptions() {
			// 1. sans descendant
			$result = $this->Categoriefp93->getParametrageOptions();
			$expected = array(
				'Categoriefp93' => array(
					'typethematiquefp93_id' => array(
						'pdi' => 'PDI',
						'horspdi' => 'Hors PDI',
					),
					'thematiquefp93_id' => array(
						'pdi_1' => 'Thématique de test',
					),
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>