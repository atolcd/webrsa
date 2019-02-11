<?php
	/**
	 * SuperFixtureWithoutFixtureTest file
	 *
	 * PHP 5.3
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */

	App::uses('SuperFixture', 'SuperFixture.Utility');
	require_once 'SuperFixtureTestParent.php';

	/**
	 * SuperFixtureWithoutFixtureTest class
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */
	class SuperFixtureWithoutFixtureTest extends SuperFixtureTestParent
	{
		/**
		 * Test de la méthode SuperFixture::load();
		 */
		public function testLoad() {
			SuperFixture::load($this, 'FooBar'); // Ne pas confondre avec FooBaz qui ne contien pas de fixture
			
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
					'SuperFixtureBaz' => array(		// Note : SuperFixtureBaz présent dans les 
						'name' => null				// fixtures de SuperFixture ne renvoi pas de données
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
						'name' => null
					)
				)
			);
			
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>