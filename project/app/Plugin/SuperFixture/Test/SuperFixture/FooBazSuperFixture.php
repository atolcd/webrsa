<?php
	/**
	 * Code source de la classe SuperFixture.
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */

	App::uses('SuperFixtureInterface', 'SuperFixture.Interface');

	/**
	 * SuperFixture
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class FooBazSuperFixture implements SuperFixtureInterface {
		/**
		 * Permet d'obtenir les informations nécéssaire pour charger la SuperFixture
		 * 
		 * @return array
		 */
		public static function getData() {
			return array(
				'SuperFixtureFoo' => array(
					1 => array(
						'name' => 'Foo 1',
						'super_fixture_bar_id' => 1,
						'integer_field' => 123,
						'text_field' => 'blabla bla blablabla',
						'boolean_field' => 1,
						'date_field' => '2015-06-01',
					),
					2 => array(
						'name' => 'Foo 2',
						'super_fixture_bar_id' => 1,
						'integer_field' => 123,
						'text_field' => 'blabla bla blablabla',
						'boolean_field' => 1,
						'date_field' => '2015-06-01',
					),
				),
				'SuperFixtureBar' => array(
					1 => array(
						'name' => 'Bar',
						'super_fixture_baz_id' => 1
					)
				),
			);
		}
	}
