<?php
	/**
	 * Code source de la classe MultibyteTest.
	 *
	 * PHP 5.3
	 *
	 * @package Password.Test.Case.Lib
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'Config'.DS.'bootstrap.php';

	/**
	 * La classe MultibyteTest réalise les tests unitaires des fonctions
	 * utilitaires multibyte.
	 *
	 * @package Password.Test.Case.Lib
	 */
	class MultibyteTest extends CakeTestCase
	{
		/**
		 * Test de la fonction mb_str_split()
		 */
		public function testMbStrSplit() {
			// Sans le paramètre $split_length
			$this->assertEquals(mb_str_split('foo'), array('f', 'o', 'o'));
			$this->assertEquals(mb_str_split('libérée'), array ('l', 'i', 'b', 'é', 'r', 'é', 'e'));
			$this->assertEquals(mb_str_split('Weiße grün'), array('W', 'e', 'i', 'ß', 'e', ' ', 'g', 'r', 'ü', 'n'));

			// Avec une valeur de $split_length < 1
			$this->assertFalse(mb_str_split('foo', 0));
			$this->assertFalse(mb_str_split('foo', -1));

			// Avec une valeur de $split_length < longueur de $str
			$this->assertEquals(mb_str_split('Hello', 3), array('Hel', 'lo'));
			$this->assertEquals(mb_str_split('Hello Friend', 3), array('Hel', 'lo ', 'Fri', 'end'));

			// Avec une valeur de $split_length >= longueur de $str
			$this->assertEquals(mb_str_split('Hello', 5), array('Hello'));
			$this->assertEquals(mb_str_split('Hello Friend', 13), array('Hello Friend'));

			// En spécifiant l'encodage des caractères
			$this->assertEquals(mb_str_split('&lt;br /&gt;', 1, 'HTML-ENTITIES'), array('<', 'b', 'r', ' ', '/', '>'));
		}
	}
?>
