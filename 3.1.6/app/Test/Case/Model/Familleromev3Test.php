<?php
	/**
	 * Code source de la classe Familleromev3Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Familleromev3', 'Model' );

	/**
	 * La classe Familleromev3Test réalise les tests unitaires de la classe Familleromev3.
	 *
	 * @package app.Test.Case.Model
	 */
	class Familleromev3Test extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Familleromev3'
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Familleromev3
		 */
		public $Familleromev3 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Familleromev3 = ClassRegistry::init( 'Familleromev3' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Familleromev3 );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Familleromev3::searchQuery() et searchConditions()
		 * via le test de la méthode search()
		 */
		public function testSearch() {
			// 1. Obtention du query
			$search = array(
				'Familleromev3' => array(
					'code' => 'a',
					'name' => 'agriculture'
				)
			);
			$result = $this->Familleromev3->search( $search );
			$expected = array (
				'fields' => array(
					'Familleromev3.id',
					'Familleromev3.code',
					'Familleromev3.name',
					'Familleromev3.created',
					'Familleromev3.modified',
				),
				'conditions' => array(
					'Familleromev3.code ILIKE' => 'a',
					'Familleromev3.name ILIKE' => '%agriculture%'
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Test du query
			$result = $this->Familleromev3->find( 'all', $result );
			$result = Hash::combine( $result, '{n}.Familleromev3.code', '{n}.Familleromev3.name' );
			$expected = array (
				'A' => 'AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Familleromev3::options()
		 */
		public function testOptions() {
			$result = $this->Familleromev3->options();
			$expected = array();
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
