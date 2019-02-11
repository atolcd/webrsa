<?php
	/**
	 * Code source de la classe PasswordPasswordTest.
	 *
	 * PHP 5.3
	 *
	 * @package Password.Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'PasswordPassword', 'Password.Utility' );
	require_once dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'Config'.DS.'bootstrap.php';

	/**
	 * La classe PasswordPasswordTest s'occupe des tests unitaires de la classe
	 * PasswordPassword du plugin Password.
	 *
	 * @package Password.Test.Case.View.Helper
	 */
	class PasswordPasswordTest extends CakeTestCase
	{
		/**
		 * Méthode exécutée avant chaque méthode de test.
		 */
		public function setUp() {
			parent::setUp();
			Configure::write('Password', array());
		}

		/**
		 * Test de la méthode PasswordPassword::options().
		 */
		public function testOptions() {
			// Par défaut
			$options = array();
			$actual = PasswordPassword::options($options);
			$expected = array(
				'length' => 8,
				'typesafe' => true,
				'class_number' => true,
				'class_lower' => true,
				'class_upper' => true,
				'class_symbol' => true
			);
			$this->assertEquals($expected, $actual);

			// Sucharge en paramètre
			$options = array('length' => 10, 'class_lower' => false);
			$actual = PasswordPassword::options($options);
			$expected = array(
				'length' => 10,
				'typesafe' => true,
				'class_number' => true,
				'class_lower' => false,
				'class_upper' => true,
				'class_symbol' => true
			);
			$this->assertEquals($expected, $actual);

			// Sucharge en configuration et en paramètre
			Configure::write('Password', array('typesafe' => false, 'class_symbol' => false));
			$options = array('length' => 12, 'class_upper' => false);
			$actual = PasswordPassword::options($options);
			$expected = array(
				'length' => 12,
				'typesafe' => false,
				'class_number' => true,
				'class_lower' => true,
				'class_upper' => false,
				'class_symbol' => false
			);
			$this->assertEquals($expected, $actual);
		}

		/**
		 * Test de la méthode PasswordPassword::possibles().
		 */
		public function testPosssibles() {
			// Avec typesafe
			$options = array(
				'class_number' => true,
				'class_lower' => true,
				'class_upper' => true,
				'class_symbol' => true,
				'typesafe' => true
			);
			$actual = PasswordPassword::possibles($options);
			$expected = array(
				'class_number' => '23456789',
				'class_lower' => 'abcdefghjkmnpqrstuvwxyz',
				'class_upper' => 'ABCDEFGHJKLMNPQRSTUVWXYZ',
				'class_symbol' => ',;.!?*+-'
			);
			$this->assertEquals($expected, $actual);

			// Sans typesafe
			$options = array(
				'class_number' => true,
				'class_lower' => true,
				'class_upper' => true,
				'class_symbol' => true,
				'typesafe' => false
			);
			$actual = PasswordPassword::possibles($options);
			$expected = array(
				'class_number' => '0123456789',
				'class_lower' => 'abcdefghijklmnopqrstuvwxyz',
				'class_upper' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'class_symbol' => ',;.!?*+-'
			);
			$this->assertEquals($expected, $actual);
		}

		/**
		 * Test de la méthode PasswordPassword::generate().
		 */
		public function testGenerate() {
			// Avec une longueur de 8, toutes les options à vrai
			$actual = PasswordPassword::generate();
			$this->assertEquals( 8, strlen( $actual ) );
			$this->assertRegExp( '/[a-z]/', $actual );
			$this->assertRegExp( '/[A-Z]/', $actual );
			$this->assertRegExp( '/[0-9]/', $actual );
			$this->assertRegExp( '/['.preg_quote( ',;.!?*+-', '/' ).']/', $actual );

			// Avec une longueur de 32, toutes les options à vrai
			$actual = PasswordPassword::generate( array( 'length' => 32 ) );
			$this->assertEquals( 32, strlen( $actual ) );
			$this->assertRegExp( '/[a-z]/', $actual );
			$this->assertRegExp( '/[A-Z]/', $actual );
			$this->assertRegExp( '/[0-9]/', $actual );
			$this->assertRegExp( '/['.preg_quote( ',;.!?*+-', '/' ).']/', $actual );

			// Avec une longueur de 2, uniquement des nombres et des symboles
			$options = array(
				'length' => 2,
				'class_number' => true,
				'class_lower' => false,
				'class_upper' => false,
				'class_symbol' => true
			);
			$actual = PasswordPassword::generate( $options );
			$this->assertEquals( 2, strlen( $actual ) );
			$this->assertRegExp( '/[^a-z]/', $actual );
			$this->assertRegExp( '/[^A-Z]/', $actual );
			$this->assertRegExp( '/[0-9]/', $actual );
			$this->assertRegExp( '/['.preg_quote( ',;.!?*+-', '/' ).']/', $actual );
		}

		/**
		 * Test de la méthode PasswordPassword::generate() avec une exception
		 * concernant la longueur désirée par-rapport au nombre de classes de
		 * caractères obligatoires.
		 *
		 * @expectedException RuntimeException
		 * @expectedExceptionCode 500
		 * @expectedExceptionMessage Impossible de générer un mot de passe de 2 caractère(s) contenant obligatoirement un élément de 4 classe(s)
		 */
		public function testGenerateExceptionLength() {
			PasswordPassword::generate(array('length' => 2));
		}
	}
?>