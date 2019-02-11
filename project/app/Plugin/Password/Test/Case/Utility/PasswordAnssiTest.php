<?php
	/**
	 * Code source de la classe PasswordAnssiTest.
	 *
	 * PHP 5.3
	 *
	 * @package Password.Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'PasswordAnssi', 'Password.Utility' );
	require_once dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'Config'.DS.'bootstrap.php';

	/**
	 * La classe PasswordAnssiTest s'occupe des tests unitaires de la classe
	 * PasswordAnssi du plugin Password.
	 *
	 * @package Password.Test.Case.View.Helper
	 */
	class PasswordAnssiTest extends CakeTestCase
	{
		/**
		 * Méthode exécutée avant chaque méthode de test.
		 */
		public function setUp() {
			parent::setUp();
			Configure::write('Password', array());
		}

		/**
		 * Test de la méthode PasswordAnssi::symbols().
		 */
		public function testSymbols() {
			// Binaire
			$this->assertEquals(2, PasswordAnssi::symbols('0110'));
			// Décimal
			$this->assertEquals(10, PasswordAnssi::symbols('0123'));
			// Hexadécimal
			$this->assertEquals(16, PasswordAnssi::symbols('012F'));
			// Hexadécimal, majuscules et minuscules
			$this->assertEquals(22, PasswordAnssi::symbols('012Ff'));
			// Alphabétique
			$this->assertEquals(26, PasswordAnssi::symbols('azerty'));
			// Alphabétique, majuscules et minuscules
			$this->assertEquals(52, PasswordAnssi::symbols('aZeRtY'));
			// Alphanumérique
			$this->assertEquals(36, PasswordAnssi::symbols('az3rty'));
			// Alphanumérique, majuscules et minuscules
			$this->assertEquals(62, PasswordAnssi::symbols('aZ3RtY'));
			// 70 symboles
			$this->assertEquals(70, PasswordAnssi::symbols('aZ3Rt?'));
			// 90 symboles
			$this->assertEquals(90, PasswordAnssi::symbols('aZ3Rt?&'));

			// 8 symboles d'extra1
			foreach(mb_str_split('€!#$*%? ') as $char) {
				$this->assertEquals(8, PasswordAnssi::symbols($char));
			}

			// 20 symboles d'extra2
			foreach(mb_str_split('&[|]@^µ§:/,.,<>°²³\'"') as $char) {
				$this->assertEquals(20, PasswordAnssi::symbols($char));
			}
		}

		/**
		 * Test de la méthode PasswordAnssi::entropyBits() avec des caractères
		 * binaires.
		 */
		public function testEntropyBitsBinary() {
			// 4 caractères
			$this->assertEquals(4, PasswordAnssi::entropyBits('0110'));
			// 6 caractères
			$this->assertEquals(6, PasswordAnssi::entropyBits('011001'));
			// 8 caractères
			$this->assertEquals(8, PasswordAnssi::entropyBits('01100110'));
			// 10 caractères
			$this->assertEquals(10, PasswordAnssi::entropyBits('0110011001'));
			// 12 caractères
			$this->assertEquals(12, PasswordAnssi::entropyBits('011001100110'));
			// 16 caractères
			$this->assertEquals(16, PasswordAnssi::entropyBits('0110011001101111'));
			// 20 caractères
			$this->assertEquals(20, PasswordAnssi::entropyBits('01100110011011111001'));
			// 25 caractères
			$this->assertEquals(25, PasswordAnssi::entropyBits('0110011001101111100110011'));
			// 30 caractères
			$this->assertEquals(30, PasswordAnssi::entropyBits('011001100110111110011001101001'));
		}

		/**
		 * Test de la méthode PasswordAnssi::entropyBits() avec des caractères
		 * décimaux.
		 */
		public function testEntropyBitsDecimal() {
			// 4 caractères
			$this->assertEquals(13, PasswordAnssi::entropyBits('0123'));
			// 6 caractères
			$this->assertEquals(20, PasswordAnssi::entropyBits('012301'));
			// 8 caractères
			$this->assertEquals(27, PasswordAnssi::entropyBits('01230123'));
		}

		/**
		 * Test de la méthode PasswordAnssi::entropyBits() avec des caractères
		 * hexadécimaux.
		 */
		public function testEntropyBitsHexadecimal() {
			// Majuscules
			$this->assertEquals(16, PasswordAnssi::entropyBits('012F'));
			$this->assertEquals(32, PasswordAnssi::entropyBits('012F012F'));

			// Minuscules
			$this->assertEquals(16, PasswordAnssi::entropyBits('012f'));
			$this->assertEquals(32, PasswordAnssi::entropyBits('012f012f'));

			// Majuscules et minuscules ?
		}

		/**
		 * Test de la méthode PasswordAnssi::entropyBits() avec des caractères
		 * alphabétiques.
		 */
		public function testEntropyBitsAlphabetical() {
			// Majuscules
			$this->assertEquals(19, PasswordAnssi::entropyBits('ABCZ'));
			$this->assertEquals(38, PasswordAnssi::entropyBits('ABCZABCZ'));

			// Minuscules
			$this->assertEquals(19, PasswordAnssi::entropyBits('abcz'));
			$this->assertEquals(38, PasswordAnssi::entropyBits('abczabcz'));

			// Majuscules et minuscules
			$this->assertEquals(23, PasswordAnssi::entropyBits('AbCz'));
			$this->assertEquals(46, PasswordAnssi::entropyBits('AbCzaBcZ'));
		}

		/**
		 * Test de la méthode PasswordAnssi::entropyBits() avec des caractères
		 * alphanumériques.
		 */
		public function testEntropyBitsAlphanumeric() {
			// Majuscules
			$this->assertEquals(21, PasswordAnssi::entropyBits('01AZ'));
			$this->assertEquals(41, PasswordAnssi::entropyBits('01AZ01AF'));
		}

		/**
		 * Test de la méthode PasswordAnssi::strength() avec des mots de passe
		 * de force "très faible".
		 */
		public function testStrengthReallyWeak() {
			// 9 caractères latin, majuscules et minuscules => 51 entropyBits
			$this->assertEquals(
				PasswordAnssi::STRENGTH_REALLY_WEAK,
				PasswordAnssi::strength(str_repeat('Abz',3))
			);
		}

		/**
		 * Test de la méthode PasswordAnssi::strength() avec des mots de passe
		 * de force "faible".
		 */
		public function testStrengthWeak() {
			// 16 caractères latin majuscules => 75 entropyBits
			$this->assertEquals(
				PasswordAnssi::STRENGTH_WEAK,
				PasswordAnssi::strength(str_repeat('ABCZ',4))
			);
		}

		/**
		 * Test de la méthode PasswordAnssi::strength() avec des mots de passe
		 * de force "moyenne".
		 */
		public function testStrengthMedium() {
			// 14 caractères latin, majuscules et minuscules => 80 entropyBits
			$this->assertEquals(
				PasswordAnssi::STRENGTH_MEDIUM,
				PasswordAnssi::strength('AbCzaBcZAbCzaB')
			);

			// 16 caractères latin, majuscules et minuscules => 91 entropyBits
			$this->assertEquals(
				PasswordAnssi::STRENGTH_MEDIUM,
				PasswordAnssi::strength('AbCzaBcZAbCzaBcZ')
			);
		}

		/**
		 * Test de la méthode PasswordAnssi::strength() avec des mots de passe
		 * de force "forte".
		 */
		public function testStrengthStrong() {
			// 30 caractères numériques => 100 entropyBits
			$this->assertEquals(
				PasswordAnssi::STRENGTH_STRONG,
				PasswordAnssi::strength(str_repeat('0123456789',3))
			);

			// 20 caractères latin, majuscules et minuscules => 114 entropyBits
			$this->assertEquals(
				PasswordAnssi::STRENGTH_STRONG,
				PasswordAnssi::strength('AbCzaBcZAbCzaBcZaBcZ')
			);
		}

		/**
		 * Test de la méthode PasswordAnssi::strength() avec des mots de passe
		 * de force "très forte".
		 */
		public function testStrengthVeryStrong() {
			// 20 caractères des 70 possibles => 130 entropyBits
			$this->assertEquals(
				PasswordAnssi::STRENGTH_VERY_STRONG,
				PasswordAnssi::strength(str_repeat('aZ3?&',4))
			);
		}

		/**
		 * Test de la méthode PasswordAnssi::options().
		 */
		public function testOptions() {
			// Par défaut
			$options = array();
			$actual = PasswordAnssi::options($options);
			$expected = array(
				'length' => 20,
				'typesafe' => true,
				'class_extra2' => true,
				'class_extra1' => true,
				'class_alphabetical_lower' => true,
				'class_hexadeciaml_lower' => true,
				'class_alphabetical_upper' => true,
				'class_hexadecimal_upper' => true,
				'class_numerical' => true,
				'class_binary' => true
			);
			$this->assertEquals($expected, $actual);

			// Sucharge en paramètre
			$options = array('length' => 10, 'class_alphabetical_lower' => false);
			$actual = PasswordAnssi::options($options);
			$expected = array(
				'length' => 10,
				'typesafe' => true,
				'class_extra2' => true,
				'class_extra1' => true,
				'class_alphabetical_lower' => false,
				'class_hexadeciaml_lower' => true,
				'class_alphabetical_upper' => true,
				'class_hexadecimal_upper' => true,
				'class_numerical' => true,
				'class_binary' => true
			);
			$this->assertEquals($expected, $actual);

			// Sucharge en configuration et en paramètre
			Configure::write('Password', array('typesafe' => false, 'class_extra1' => false));
			$options = array('length' => 12, 'class_extra2' => false);
			$actual = PasswordAnssi::options($options);
			$expected = array(
				'length' => 12,
				'typesafe' => false,
				'class_extra2' => false,
				'class_extra1' => false,
				'class_alphabetical_lower' => true,
				'class_hexadeciaml_lower' => true,
				'class_alphabetical_upper' => true,
				'class_hexadecimal_upper' => true,
				'class_numerical' => true,
				'class_binary' => true
			);
			$this->assertEquals($expected, $actual);
		}

		/**
		 * Test de la méthode PasswordAnssi::generate().
		 */
		public function testGenerate() {
			// Par défaut, très fort: 20 caractères parmi 74 caractères
			$result = PasswordAnssi::generate();
			$this->assertEquals(
				130,
				PasswordAnssi::entropyBits($result),
				$result
			);

			// Security.salt de CakePHP 2.x, très fort: 40 alphanumeriques parmi 55 caractères
			$options = array(
				'length' => 40,
				'class_extra2' => false,
				'class_extra1' => false
			);
			$result = PasswordAnssi::generate($options);
			$this->assertEquals(
				238,
				PasswordAnssi::entropyBits($result),
				$result
			);

			// Security.cipherSeed de CakePHP, moyen: 29 numeriques parmi 10 caractères
			$options = array(
				'length' => 29,
				'typesafe' => false,
				'class_extra2' => false,
				'class_extra1' => false,
				'class_alphabetical_lower' => false,
				'class_hexadeciaml_lower' => false,
				'class_alphabetical_upper' => false,
				'class_hexadecimal_upper' => false
			);
			$result = PasswordAnssi::generate($options);
			$this->assertEquals(
				96,
				PasswordAnssi::entropyBits($result),
				$result
			);
		}

		/**
		 * Test de la méthode PasswordAnssi::generate() avec une exception
		 * concernant la longueur désirée par-rapport au nombre de classes.
		 *
		 * @expectedException RuntimeException
		 * @expectedExceptionCode 500
		 * @expectedExceptionMessage Impossible de générer un mot de passe de 2 caractère(s) contenant obligatoirement un élément de 7 classe(s)
		 */
		public function testGenerateExceptionLength() {
			PasswordAnssi::generate(array('length' => 2));
		}
	}
?>