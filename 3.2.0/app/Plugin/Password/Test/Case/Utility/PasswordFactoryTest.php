<?php
	/**
	 * Code source de la classe PasswordFactoryTest.
	 *
	 * PHP 5.3
	 *
	 * @package Password.Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'PasswordFactory', 'Password.Utility' );

	/**
	 * La classe PasswordFactoryTest s'occupe des tests unitaires de la classe
	 * PasswordFactory du plugin Password.
	 *
	 * @package Password.Test.Case.View.Helper
	 */
	class PasswordFactoryTest extends CakeTestCase
	{
		/**
		 * Méthode exécutée avant chaque méthode de test.
		 */
		public function setUp() {
			parent::setUp();
			Configure::write('Password', array());
		}

		/**
		 * Test de la méthode PasswordFactory::options().
		 */
		public function testOptions() {
			// Par défaut
			$options = array();
			$actual = PasswordFactory::options($options);
			$expected = array(
				'generators' => array(
					'default' => 'Password.PasswordPassword'
				),
				'checkers' => array(
					'default' => 'Password.PasswordAnssi'
				)
			);
			$this->assertEquals($expected, $actual);

			// Avec une configuration différente pour les generators.
			$options = array(
				'generators' => array(
					'default' => 'Password.PasswordAnssi',
					'ancien' => 'Password.PasswordPassword'
				)
			);
			$actual = PasswordFactory::options($options);
			$expected = array(
				'generators' => array(
					'default' => 'Password.PasswordAnssi',
					'ancien' => 'Password.PasswordPassword'
				),
				'checkers' => array(
					'default' => 'Password.PasswordAnssi'
				)
			);
			$this->assertEquals($expected, $actual);
		}

		/**
		 * Test de la méthode PasswordFactory::generator().
		 */
		public function testGenerator() {
			// Par défaut
			$actual = PasswordFactory::generator();
			$this->assertTrue($actual instanceof PasswordPassword);
		}

		/**
		 * Test de la méthode PasswordFactory::checker().
		 */
		public function testChecker() {
			// Par défaut
			$actual = PasswordFactory::checker();
			$this->assertTrue($actual instanceof PasswordAnssi);
		}
	}
?>