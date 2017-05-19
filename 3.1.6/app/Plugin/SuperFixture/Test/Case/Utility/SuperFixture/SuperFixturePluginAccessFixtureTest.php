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

	/**
	 * SuperFixtureWithoutFixtureTest class
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */
	class SuperFixturePluginAccessFixtureTest extends CakeTestCase
	{
		/**
		 * Test de la méthode SuperFixture::load();
		 */
		public function testLoad() {
			App::build(array('SuperFixture' => APP.'Plugin'.DS.'SuperFixture'.DS.'Test'.DS.'SuperFixture'.DS));
			SuperFixture::load($this, 'PluginAccess');
			
			$result = ClassRegistry::init('SuperFixtureFoo')->find('first', 
				array(
					'fields' => array(
						'SuperFixtureFoo.name',
						'Bar.name',
						'Baz.name',
					),
					'joins' => array(
						array(
							'alias' => 'Bar',
							'table' => '"super_fixture_bars"',
							'conditions' => array('Bar.id = SuperFixtureFoo.super_fixture_bar_id'),
							'type' => 'INNER'
						),
						array(
							'alias' => 'Baz',
							'table' => '"super_fixture_bazs"',
							'conditions' => array('Baz.id = Bar.super_fixture_baz_id'),
							'type' => 'LEFT'
						),
					)
				)
			);
			$expected = array(
				'SuperFixtureFoo' => array(
					'name' => 'Foo 1'
				),
				'Bar' => array(
					'name' => 'Bar'
				),
				'Baz' => array(
					'name' => null
				)
			);
			
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>