<?php
	/**
	 * Code source de la classe Filierefp93Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Filierefp93', 'Model' );
	/**
	 * La classe Filierefp93Test réalise les tests unitaires de la classe Filierefp93.
	 *
	 * @package app.Test.Case.Model
	 */
	class Filierefp93Test extends CakeTestCase
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
		 * @var Filierefp93
		 */
		public $Filierefp93 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Filierefp93 = ClassRegistry::init( 'Filierefp93' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Filierefp93 );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Filierefp93::getDependantListCondition()
		 */
		public function testGetDependantListCondition() {
			// 1. PDI
			$result = $this->Filierefp93->getDependantListCondition( 'pdi', array( ) );
			$expected = 'Filierefp93.id IN ( SELECT "actionsfps93"."filierefp93_id" AS "actionsfps93__filierefp93_id" FROM "actionsfps93" AS "actionsfps93"   WHERE "actionsfps93"."filierefp93_id" = "Filierefp93"."id"    )';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Hors PDI
			$result = $this->Filierefp93->getDependantListCondition( 'horspdi', array( ) );
			$expected = '1 = 1';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. PDI, avec une condition
			$result = $this->Filierefp93->getDependantListCondition( 'pdi', array( 'Filierefp93.name' => 'Foo' ) );
			$expected = 'Filierefp93.id IN ( SELECT "actionsfps93"."filierefp93_id" AS "actionsfps93__filierefp93_id" FROM "actionsfps93" AS "actionsfps93"   WHERE "Filierefp93"."name" = \'Foo\' AND "actionsfps93"."filierefp93_id" = "Filierefp93"."id"    )';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Categoriefp93::saveParametrage()
		 */
		public function testSaveParametrage() {
			$data = array(
				'Filierefp93' => array(
					'id' => '',
					'typethematiquefp93_id' => 'pdi',
					'thematiquefp93_id' => 'pdi_1',
					'categoriefp93_id' => '1_1',
					'name' => 'Filière de test supplémentaire',
				)
			);
			$result = $this->Filierefp93->saveParametrage( $data );
			$this->assertTrue( $result );
		}

		/**
		 * Test de la méthode Filierefp93::getParametrageFields()
		 */
		public function testGetParametrageFields() {
			$result = $this->Filierefp93->getParametrageFields( 'pdi', array( ) );
			$expected = array(
				'Filierefp93.id' => array( ),
				'Filierefp93.typethematiquefp93_id' => array(
					'empty' => true,
				),
				'Filierefp93.thematiquefp93_id' => array(
					'empty' => true,
				),
				'Filierefp93.categoriefp93_id' => array(
					'empty' => true,
				),
				'Filierefp93.name' => array( ),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Filierefp93::getParametrageFormData()
		 */
		public function testGetParametrageFormData() {
			$result = $this->Filierefp93->getParametrageFormData( 1 );
			$expected = array(
				'Filierefp93' =>
				array(
					'id' => 1,
					'typethematiquefp93_id' => 'pdi',
					'thematiquefp93_id' => 'pdi_1',
					'categoriefp93_id' => '1_1',
					'name' => 'Filière de test',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Filierefp93::getParametrageOptions()
		 */
		public function testGetParametrageOptions() {
			// 1. sans descendant
			$result = $this->Filierefp93->getParametrageOptions();
			$expected = array(
				'Filierefp93' => array(
					'typethematiquefp93_id' => array(
						'pdi' => 'PDI',
						'horspdi' => 'Hors PDI',
					),
					'thematiquefp93_id' => array(
						'pdi_1' => 'Thématique de test',
					),
					'categoriefp93_id' => array(
						'1_1' => 'Catégorie de test',
					),
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
