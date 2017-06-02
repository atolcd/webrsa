<?php
	/**
	 * Code source de la classe Adresseprestatairefp93Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Adresseprestatairefp93', 'Model' );

	/**
	 * La classe Adresseprestatairefp93Test réalise les tests unitaires de la classe Adresseprestatairefp93.
	 *
	 * @package app.Test.Case.Model
	 */
	class Adresseprestatairefp93Test extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Adresseprestatairefp93',
			'app.Prestatairefp93',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Adresseprestatairefp93
		 */
		public $Adresseprestatairefp93 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Adresseprestatairefp93 = ClassRegistry::init( 'Adresseprestatairefp93' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Adresseprestatairefp93 );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Adresseprestatairefp93::getParametrageOptions()
		 */
		public function testGetParametrageOptions() {
			// 1. Sans s'assurer d'avoir des adresses
			$result = $this->Adresseprestatairefp93->getParametrageOptions();
			$expected = array(
				'Adresseprestatairefp93' => array(
					'prestatairefp93_id' => array(
						1 => 'Association LE PRISME',
						2 => 'Sol en Si',
					),
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. En s'assurant d'avoir des adresses
			$result = $this->Adresseprestatairefp93->getParametrageOptions( true );
			$expected = array(
				'Adresseprestatairefp93' => array(
					'prestatairefp93_id' => array(
						1 => 'Association LE PRISME',
					),
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
