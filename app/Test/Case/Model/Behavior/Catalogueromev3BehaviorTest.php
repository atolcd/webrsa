<?php
	/**
	 * Code source de la classe Catalogueromev3BehaviorTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Catalogueromev3Behavior', 'Model/Behavior' );

	/**
	 * La classe Catalogueromev3BehaviorTest ...
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class Catalogueromev3BehaviorTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Domaineromev3',
			'app.Familleromev3',
			'app.Metierromev3'
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Metierromev3 = ClassRegistry::init( 'Metierromev3' );
			$this->Metierromev3->Behaviors->attach( 'Catalogueromev3' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Metierromev3 );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Catalogueromev3Behavior::getParametrageFormData()
		 */
		public function testGetParametrageFormData() {
			$result = $this->Metierromev3->getParametrageFormData( 1 );
			$expected = array(
				'Metierromev3' => array(
					'id' => 1,
					'domaineromev3_id' => '1_1',
					'code' => '01',
					'name' => 'Conduite d\'engins d\'exploitation agricole et forestière',
					'created' => NULL,
					'modified' => NULL,
					'familleromev3_id' => 1,
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Catalogueromev3Behavior:: getParametrageFields()
		 */
		public function testGetParametrageFields() {
			$result = $this->Metierromev3->getParametrageFields();
			$expected = array(
				'Metierromev3.familleromev3_id' => array( 'empty' => true, 'required' => true ),
				'Metierromev3.id' => array(),
				'Metierromev3.domaineromev3_id' => array( 'empty' => true ),
				'Metierromev3.code' => array(),
				'Metierromev3.name' => array()
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Catalogueromev3Behavior::getParametrageOptions()
		 */
		public function testGetParametrageOptions() {
			$result = $this->Metierromev3->getParametrageOptions();
			$expected = array(
				'Domaineromev3' => array(),
				'Metierromev3' => array(
					'domaineromev3_id' => array(
						'1_1' => 'A11 - Engins agricoles et forestiers',
					),
					'familleromev3_id' => array(
						1 => 'A - AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX',
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Catalogueromev3Behavior::getParametrageDependantFields()
		 */
		public function testGetParametrageDependantFields() {
			$result = $this->Metierromev3->getParametrageDependantFields();
			$expected = array(
				'Metierromev3.familleromev3_id' => 'Metierromev3.domaineromev3_id',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Catalogueromev3Behavior::saveParametrage()
		 */
		public function testSaveParametrage() {
			$data = array(
				'Metierromev3' => array(
					'id' => 1,
					'domaineromev3_id' => '1_1',
					'familleromev3_id' => 1,
					'code' => '01',
					'name' => 'Conduite d\'engins d\'exploitation agricole et forestière'
				)
			);
			$result = $this->Metierromev3->saveParametrage( $data );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

	}
?>