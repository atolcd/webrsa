<?php
	/**
	 * Code source de la classe Domaineromev3Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Domaineromev3', 'Model' );

	/**
	 * La classe Domaineromev3Test réalise les tests unitaires de la classe Domaineromev3.
	 *
	 * @package app.Test.Case.Model
	 */
	class Domaineromev3Test extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Domaineromev3',
			'app.Familleromev3'
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Domaineromev3
		 */
		public $Domaineromev3 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Domaineromev3 = ClassRegistry::init( 'Domaineromev3' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Domaineromev3 );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Domaineromev3::searchQuery() et searchConditions()
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
				)
			);
			$result = $this->Domaineromev3->search( $search );
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
					'( "Familleromev3"."code" || "Domaineromev3"."code" ) AS "Domaineromev3__code"'
				),
				'joins' => array(
					array(
						'table' => '"famillesromesv3"',
						'alias' => 'Familleromev3',
						'type' => 'INNER',
						'conditions' => '"Domaineromev3"."familleromev3_id" = "Familleromev3"."id"'
					),
				),
				'conditions' => array(
					'Familleromev3.code ILIKE' => 'a',
					'Familleromev3.name ILIKE' => '%agriculture%',
					'Domaineromev3.code ILIKE' => '11',
					'Domaineromev3.name ILIKE' => '%agricole%'
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Test du query
			$result = $this->Domaineromev3->find( 'all', $result );
			$result = Hash::combine( $result, '{n}.Domaineromev3.code', '{n}.Domaineromev3.name' );
			$expected = array (
				'A11' => 'Engins agricoles et forestiers'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Familleromev3::options()
		 */
		public function testOptions() {
			$result = $this->Domaineromev3->options();
			$expected = array(
				'Domaineromev3' => array(
					'familleromev3_id' => array(
						1 => 'A - AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX',
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Familleromev3::getParametrageOptions()
		 */
		public function testGetParametrageOptions() {
			$result = $this->Domaineromev3->getParametrageOptions();
			$expected = array(
				'Domaineromev3' => array(
					'familleromev3_id' => array(
						1 => 'A - AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX',
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
