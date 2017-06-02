<?php
	/**
	 * Code source de la classe Catalogueromev3Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Catalogueromev3', 'Model' );

	/**
	 * La classe Catalogueromev3Test réalise les tests unitaires de la classe Catalogueromev3.
	 *
	 * @package app.Test.Case.Model
	 */
	class Catalogueromev3Test extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Appellationromev3',
			'app.Domaineromev3',
			'app.Familleromev3',
			'app.Metierromev3'
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Catalogueromev3
		 */
		public $Catalogueromev3 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Catalogueromev3 = ClassRegistry::init( 'Catalogueromev3' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Catalogueromev3 );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Catalogueromev3::dependantSelects()
		 */
		public function testDependantSelects() {
			$result = $this->Catalogueromev3->dependantSelects();
			$expected = array(
				'Catalogueromev3' => array(
					'familleromev3_id' => array(
						1 => 'A - AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX',
					),
					'domaineromev3_id' => array(
						'1_1' => 'A11 - Engins agricoles et forestiers',
					),
					'metierromev3_id' => array(
						'1_1' => 'A1101 - Conduite d\'engins d\'exploitation agricole et forestière',
					),
					'appellationromev3_id' => array(
						'1_1' => 'Conducteur / Conductrice d\'engins d\'exploitation agricole',
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Catalogueromev3::prechargement()
		 */
		public function testPrechargement() {
			$result = $this->Catalogueromev3->prechargement();
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
