<?php
	/**
	 * Code source de la classe ApreTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Apre', 'Model' );

	/**
	 * La classe ApreTest réalise les tests unitaires de la classe Apre.
	 *
	 * @package app.Test.Case.Model
	 */
	class ApreTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Acccreaentr',
			'app.Acqmatprof',
			'app.Actprof',
			'app.Amenaglogt',
			'app.Apre',
			'app.Formpermfimo',
			'app.Formqualif',
			'app.Locvehicinsert',
			'app.Permisb',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Apre
		 */
		public $Apre = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();

			Configure::write( 'Cg.departement', 93 );
			$this->Apre = ClassRegistry::init( 'Apre' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Apre );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Apre::modeleOdt()
		 */
		public function testModeleOdt() {
			$result = $this->Apre->WebrsaApre->modeleOdt( array() );
			$expected = 'APRE/apre.odt';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test du champ virtuel natureaide
		 */
		public function testVirtualFieldNatureaide() {
			$result = Hash::get( $this->Apre->virtualFields, 'natureaide' );
			$expected = 'TRIM( BOTH \' \' FROM TRIM( TRAILING \'\\n\\r-\' FROM ARRAY_TO_STRING( ARRAY( SELECT \'Formqualif\' FROM "formsqualifs" AS "formsqualifs"   WHERE "formsqualifs"."apre_id" = "Apre"."id"    UNION SELECT \'Formpermfimo\' FROM "formspermsfimo" AS "formspermsfimo"   WHERE "formspermsfimo"."apre_id" = "Apre"."id"    UNION SELECT \'Actprof\' FROM "actsprofs" AS "actsprofs"   WHERE "actsprofs"."apre_id" = "Apre"."id"    UNION SELECT \'Permisb\' FROM "permisb" AS "permisb"   WHERE "permisb"."apre_id" = "Apre"."id"    UNION SELECT \'Amenaglogt\' FROM "amenagslogts" AS "amenagslogts"   WHERE "amenagslogts"."apre_id" = "Apre"."id"    UNION SELECT \'Acccreaentr\' FROM "accscreaentr" AS "accscreaentr"   WHERE "accscreaentr"."apre_id" = "Apre"."id"    UNION SELECT \'Acqmatprof\' FROM "acqsmatsprofs" AS "acqsmatsprofs"   WHERE "acqsmatsprofs"."apre_id" = "Apre"."id"    UNION SELECT \'Locvehicinsert\' FROM "locsvehicinsert" AS "locsvehicinsert"   WHERE "locsvehicinsert"."apre_id" = "Apre"."id"    ), \'\\n\\r-\' ) ) )';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
