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
	class BadBarSuperFixture implements SuperFixtureInterface {
		/**
		 * Fixtures à charger en plus pour un bon fonctionnement
		 * 
		 * @var array
		 */
		public static $fixtures = array(
			'plugin.SuperFixture.BadSuperFixtureBaz',
		);
		
		/**
		 * Permet d'obtenir les informations nécéssaire pour charger la SuperFixture
		 * 
		 * @return array
		 */
		public static function getData() {
			return array(
				'SuperFixtureFoo' => array(
					1 => array(
						'name' => 'Bad Foo',
						'super_fixture_bar_id' => 1,
						'integer_field' => 123,
						'text_field' => 'blabla bla blablabla',
						'boolean_field' => 1,
						'date_field' => array('type' => 'date', 'null' => false),
					),
				)
			);
		}
	}
