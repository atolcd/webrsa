<?php
	/**
	 * Code source de la classe Thematiquefp93Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Thematiquefp93', 'Model' );

	/**
	 * La classe Thematiquefp93Test réalise les tests unitaires de la classe Thematiquefp93.
	 *
	 * @package app.Test.Case.Model
	 */
	class Thematiquefp93Test extends CakeTestCase
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
		 * @var Thematiquefp93
		 */
		public $Thematiquefp93 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Thematiquefp93 = ClassRegistry::init( 'Thematiquefp93' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Thematiquefp93 );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Thematiquefp93::getDependantListCondition()
		 */
		public function testGetDependantListCondition() {
			$result = $this->Thematiquefp93->getDependantListCondition( 'pdi', array() );
			$expected = 'Thematiquefp93.id IN ( SELECT "categoriesfps93"."thematiquefp93_id" AS "categoriesfps93__thematiquefp93_id" FROM "categoriesfps93" AS "categoriesfps93" INNER JOIN "public"."filieresfps93" AS "filieresfps93" ON ("filieresfps93"."categoriefp93_id" = "categoriesfps93"."id") INNER JOIN "public"."actionsfps93" AS "actionsfps93" ON ("actionsfps93"."filierefp93_id" = "filieresfps93"."id")  WHERE "categoriesfps93"."thematiquefp93_id" = "Thematiquefp93"."id"    )';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}


		/**
		 * Test de la méthode Actionfp93::saveParametrage()
		 */
		public function testSaveParametrage() {
			$data = array(
				'Thematiquefp93' => array(
					'id' => '',
					'type' => 'pdi',
					'name' => 'Thématique de test supplémentaire',
				)
			);
			$result = $this->Thematiquefp93->saveParametrage( $data );
			$this->assertTrue( $result );
		}

		/**
		 * Test de la méthode Thematiquefp93::getParametrageFields()
		 */
		public function testGetParametrageFields() {
			$result = $this->Thematiquefp93->getParametrageFields();
			$expected = array(
				'Thematiquefp93.id' => array( ),
				'Thematiquefp93.type' => array(
					'empty' => true
				),
				'Thematiquefp93.name' => array( )
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Thematiquefp93::getParametrageOptions()
		 */
		public function testGetParametrageOptions() {
			$result = $this->Thematiquefp93->getParametrageOptions();
			$expected = array(
				'Thematiquefp93' => array(
					'type' => array(
						'pdi' => 'PDI',
						'horspdi' => 'Hors PDI',
					),
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Thematiquefp93::getParametrageFormData()
		 */
		public function testGetParametrageFormData() {
			$result = $this->Thematiquefp93->getParametrageFormData( 1 );
			$expected = array(
				'Thematiquefp93' =>
				array(
					'id' => 1,
					'type' => 'pdi',
					'name' => 'Thématique de test',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
