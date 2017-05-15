<?php
	/**
	 * Code source de la classe Actionfp93Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Actionfp93', 'Model' );

	/**
	 * La classe Actionfp93Test réalise les tests unitaires de la classe Actionfp93.
	 *
	 * @package app.Test.Case.Model
	 */
	class Actionfp93Test extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Actionfp93',
			'app.Adresseprestatairefp93',
			'app.Categoriefp93',
			'app.Filierefp93',
			'app.Prestatairefp93',
			'app.Thematiquefp93',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Actionfp93
		 */
		public $Actionfp93 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Actionfp93 = ClassRegistry::init( 'Actionfp93' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Actionfp93 );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Actionfp93::saveParametrage()
		 */
		public function testSaveParametrage() {
			$data = array(
				'Actionfp93' => array(
					'id' => '',
					'typethematiquefp93_id' => 'pdi',
					'thematiquefp93_id' => 'pdi_1',
					'categoriefp93_id' => '1_1',
					'filierefp93_id' => '1_1',
					'adresseprestatairefp93_id' => '1',
					'annee' => 2014,
					'actif' => '1',
					'numconvention' => '93TEST14000000',
					'name' => 'Action de test supplémentaire',
				)
			);
			$result = $this->Actionfp93->saveParametrage( $data );
			$this->assertTrue( $result );
		}

		/**
		 * Test de la méthode Actionfp93::getParametrageFields()
		 */
		public function testGetParametrageFields() {
			$result = $this->Actionfp93->getParametrageFields();
			$expected = array(
				'Actionfp93.typethematiquefp93_id' => array(
					'empty' => true,
				),
				'Actionfp93.thematiquefp93_id' => array(
					'empty' => true,
				),
				'Actionfp93.categoriefp93_id' => array(
					'empty' => true,
				),
				'Actionfp93.filierefp93_id' => array(
					'empty' => true,
				),
				'Actionfp93.prestatairefp93_id' => array(
					'empty' => true,
				),
				'Actionfp93.id' => array( ),
				'Actionfp93.adresseprestatairefp93_id' => array(
					'empty' => true
				),
				'Actionfp93.name' => array( ),
				'Actionfp93.numconvention' => array( ),
				'Actionfp93.annee' => array( ),
				'Actionfp93.duree' => array( ),
				'Actionfp93.actif' => array(
					'empty' => true,
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Actionfp93::getParametrageFormData()
		 */
		public function testGetParametrageFormData() {
			$result = $this->Actionfp93->getParametrageFormData( 1 );
			$expected = array(
				'Actionfp93' => array(
					'id' => 1,
					'filierefp93_id' => '1_1',
					'adresseprestatairefp93_id' => '1_1',
					'name' => 'Action de test',
					'numconvention' => '93XXX1300001',
					'annee' => 2013,
					'duree' => NULL,
					'actif' => '0',
					'created' => NULL,
					'modified' => NULL,
					'typethematiquefp93_id' => 'pdi',
					'thematiquefp93_id' => 'pdi_1',
					'categoriefp93_id' => '1_1',
					'prestatairefp93_id' => 1,
				),
				'Adresseprestatairefp93' => array(
					'prestatairefp93_id' => 1,
				),
				'Filierefp93' => array(
					'id' => 1,
				),
				'Categoriefp93' => array(
					'id' => 1,
				),
				'Thematiquefp93' => array(
					'id' => 1,
					'type' => 'pdi',
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Actionfp93::getParametrageOptions()
		 */
		public function testGetParametrageOptions() {
			// 1. Sans s'assurer d'avoir des enregistrements liés
			$result = $this->Actionfp93->getParametrageOptions();
			$expected = array(
				'Actionfp93' => array(
					'actif' => array(
						0 => 'Inactif',
						1 => 'Actif',
					),
					'typethematiquefp93_id' => array(
						'pdi' => 'PDI',
					),
					'thematiquefp93_id' => array(
						'pdi_1' => 'Thématique de test',
					),
					'categoriefp93_id' => array(
						'1_1' => 'Catégorie de test',
					),
					'filierefp93_id' => array(
						'1_1' => 'Filière de test',
					),
					'prestatairefp93_id' => array(
						1 => 'Association LE PRISME',
						2 => 'Sol en Si',
					),
					'adresseprestatairefp93_id' => array(
						'1_1' => 'Av. de la république, 93000 Bobigny',
					),
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Actionfp93::getParametrageDependantFields()
		 */
		public function testGetParametrageDependantFields() {
			// 1. Sans s'assurer d'avoir des enregistrements liés
			$result = $this->Actionfp93->getParametrageDependantFields();
			$expected = array(
				'Actionfp93.typethematiquefp93_id' => 'Actionfp93.thematiquefp93_id',
				'Actionfp93.thematiquefp93_id' => 'Actionfp93.categoriefp93_id',
				'Actionfp93.categoriefp93_id' => 'Actionfp93.filierefp93_id',
				'Actionfp93.prestatairefp93_id' => 'Actionfp93.adresseprestatairefp93_id',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
