<?php
	/**
	 * SuperFixtureWithFixtureTest file
	 *
	 * PHP 5.3
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */

	App::uses('SuperFixture', 'SuperFixture.Utility');
	require_once 'SuperFixtureTestParent.php';

	/**
	 * SuperFixtureWithFixtureTest class
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */
	class SuperFixtureWithFixtureTest extends SuperFixtureTestParent
	{
		/**
		 * Fixtures pour le test
		 * ATTENTION : Utile pour la comparaison With/Without fixtures qui a deja
		 * provoqué des bugs en cas d'absence des fixtures dans la classe de test unitaire
		 *
		 * @var array
		 */
		public $fixtures = array(
			'plugin.SuperFixture.SuperFixtureBaz', // Si modifications, changez la methode testUnload()
		);

		/**
		 * Test de la méthode SuperFixture::load();
		 */
		public function testLoad() {
			SuperFixture::load($this, 'FooBaz'); // Ne pas confondre avec FooBar qui contien une fixture

			$result = ClassRegistry::init('SuperFixtureFoo')->find('all', $this->_query);
			$expected = array(
				(int) 0 => array(
					'SuperFixtureFoo' => array(
						'name' => 'Foo 2',
						'integer_field' => (int) 123,
						'text_field' => 'blabla bla blablabla',
						'boolean_field' => true,
						'date_field' => '2015-06-01'
					),
					'SuperFixtureBar' => array(
						'name' => 'Bar'
					),
					'SuperFixtureBaz' => array(
						'name' => 'Baz'
					)
				),
				(int) 1 => array(
					'SuperFixtureFoo' => array(
						'name' => 'Foo 1',
						'integer_field' => (int) 123,
						'text_field' => 'blabla bla blablabla',
						'boolean_field' => true,
						'date_field' => '2015-06-01'
					),
					'SuperFixtureBar' => array(
						'name' => 'Bar'
					),
					'SuperFixtureBaz' => array(
						'name' => 'Baz'
					)
				)
			);

			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode SuperFixture::load();
		 *
		 * @expectedException MissingTableException
		 */
		public function testNotLoad() {
			$result = ClassRegistry::init('SuperFixtureFoo')->find('all', $this->_query);
		}

		/**
		 * Test de la méthode SuperFixture::load();
		 *
		 * @expectedException NotFoundException
		 */
		public function testLoadSuperFixtureNotFound() {
			SuperFixture::load($this, 'FooBarBaz');
		}

		/**
		 * Test de la méthode SuperFixture::load();
		 *
		 * @expectedException NotFoundException
		 */
		public function testLoadInnerNotFound() {
			SuperFixture::load($this, 'BadFoo');
		}

		/**
		 * Test de la méthode SuperFixture::load();
		 */
		public function testUnLoad() {
			SuperFixture::load($this, 'FooBaz');
			$this->fixtureManager->unload($this);

			$this->assertEquals( array( 'plugin.SuperFixture.SuperFixtureBaz' ), $this->fixtures, var_export( $this->fixtures, true ) );
		}

		/**
		 * Test de la méthode SuperFixture::load();
		 *
		 * @expectedException PHPUnit_Framework_Error_Notice
		 */
		public function testLoadFixtureNotFound() {
			SuperFixture::load($this, 'BadBar');
		}
	}
?>