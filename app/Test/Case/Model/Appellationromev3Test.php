<?php
	/**
	 * Code source de la classe Appellationromev3Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Appellationromev3', 'Model' );

	/**
	 * La classe Appellationromev3Test réalise les tests unitaires de la classe Appellationromev3.
	 *
	 * @package app.Test.Case.Model
	 */
	class Appellationromev3Test extends CakeTestCase
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
		 * @var Appellationromev3
		 */
		public $Appellationromev3 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Appellationromev3 = ClassRegistry::init( 'Appellationromev3' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Appellationromev3 );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Appellationromev3::searchQuery() et searchConditions()
		 * via le test de la méthode search()
		 */
		public function testSearch() {
			// 1. Obtention du query
			$search = array(
				'Familleromev3' => array(
					'code' => 'a',
					'name' => 'agriculture'
				),
				'Domaineromev3' => array(
					'code' => '11',
					'name' => 'agricole'
				),
				'Metierromev3' => array(
					'code' => '01',
					'name' => 'conduite'
				),
				'Appellationromev3' => array(
					'name' => 'Conductrice'
				)
			);
			$result = $this->Appellationromev3->search( $search );
			$expected = array(
				'fields' => array(
					'Familleromev3.id',
					'Familleromev3.code',
					'Familleromev3.name',
					'Familleromev3.created',
					'Familleromev3.modified',
					'Domaineromev3.id',
					'Domaineromev3.familleromev3_id',
					'Domaineromev3.code',
					'Domaineromev3.name',
					'Domaineromev3.created',
					'Domaineromev3.modified',
					'Metierromev3.id',
					'Metierromev3.domaineromev3_id',
					'Metierromev3.code',
					'Metierromev3.name',
					'Metierromev3.created',
					'Metierromev3.modified',
					'Appellationromev3.id',
					'Appellationromev3.metierromev3_id',
					'Appellationromev3.name',
					'Appellationromev3.created',
					'Appellationromev3.modified',
					'( "Familleromev3"."code" || "Domaineromev3"."code" || "Metierromev3"."code" ) AS "Metierromev3__code"'
				),
				'joins' => array(
					array(
						'table' => '"metiersromesv3"',
						'alias' => 'Metierromev3',
						'type' => 'INNER',
						'conditions' => '"Appellationromev3"."metierromev3_id" = "Metierromev3"."id"',
					),
					array(
						'table' => '"domainesromesv3"',
						'alias' => 'Domaineromev3',
						'type' => 'INNER',
						'conditions' => '"Metierromev3"."domaineromev3_id" = "Domaineromev3"."id"',
					),
					array(
						'table' => '"famillesromesv3"',
						'alias' => 'Familleromev3',
						'type' => 'INNER',
						'conditions' => '"Domaineromev3"."familleromev3_id" = "Familleromev3"."id"',
					)
				),
				'conditions' => array(
					'Familleromev3.code ILIKE' => 'a',
					'Familleromev3.name ILIKE' => '%agriculture%',
					'Domaineromev3.code ILIKE' => '11',
					'Domaineromev3.name ILIKE' => '%agricole%',
					'Metierromev3.code ILIKE' => '01',
					'Metierromev3.name ILIKE' => '%conduite%',
					'Appellationromev3.name ILIKE' => '%Conductrice%'
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Test du query
			$result = $this->Appellationromev3->find( 'all', $result );
			$result = Hash::combine( $result, '{n}.Appellationromev3.id', '{n}.Appellationromev3.name' );
			$expected = array (
				1 => 'Conducteur / Conductrice d\'engins d\'exploitation agricole'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Familleromev3::options()
		 */
		public function testOptions() {
			$result = $this->Appellationromev3->options();
			$expected = array(
				'Domaineromev3' => array(
					'familleromev3_id' => array(
						1 => 'A - AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX',
					),
				),
				'Metierromev3' => array(
					'domaineromev3_id' => array(
						'1_1' => 'A11 - Engins agricoles et forestiers',
					),
				),
				'Appellationromev3' => array(
					'metierromev3_id' => array(
						'1_1' => 'A1101 - Conduite d\'engins d\'exploitation agricole et forestière',
					),
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Familleromev3::getParametrageOptions()
		 */
		public function testGetParametrageOptions() {
			$result = $this->Appellationromev3->getParametrageOptions();
			$expected = array(
				'Appellationromev3' => array(
					'metierromev3_id' => array(
						'1_1' => 'A1101 - Conduite d\'engins d\'exploitation agricole et forestière',
					),
					'familleromev3_id' => array(
						1 => 'A - AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX',
					),
					'domaineromev3_id' => array(
						'1_1' => 'A11 - Engins agricoles et forestiers',
					),
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Familleromev3::getParametrageFormData()
		 */
		public function testGetParametrageFormData() {
			$result = $this->Appellationromev3->getParametrageFormData( 1 );
			$expected = array(
				'Appellationromev3' => array(
					'id' => 1,
					'metierromev3_id' => '1_1',
					'name' => 'Conducteur / Conductrice d\'engins d\'exploitation agricole',
					'created' => NULL,
					'modified' => NULL,
					'familleromev3_id' => 1,
					'domaineromev3_id' => '1_1',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Familleromev3::getParametrageFields()
		 */
		public function testGetParametrageFields() {
			$result = $this->Appellationromev3->getParametrageFields();
			$expected = array(
				'Appellationromev3.familleromev3_id' => array(
					'empty' => true,
					'required' => true,
				),
				'Appellationromev3.domaineromev3_id' => array(
					'empty' => true,
					'required' => true,
				),
				'Appellationromev3.id' => array(),
				'Appellationromev3.metierromev3_id' => array(
					'empty' => true,
				),
				'Appellationromev3.name' => array(),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
