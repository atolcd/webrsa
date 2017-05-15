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
	class BadFooSuperFixture implements SuperFixtureInterface {
		/**
		 * Fixtures à charger en plus pour un bon fonctionnement
		 * 
		 * @var array
		 */
		public static $fixtures = array(
			'plugin.SuperFixture.SuperFixtureBaz',
		);
		
		/**
		 * Permet d'obtenir les informations nécéssaire pour charger la SuperFixture
		 * 
		 * @return array
		 */
		public static function getData() {
			return array(
				'BadSuperFixtureFoo' => array(
					1 => array(
						'name' => 'Bad Foo',
					),
				)
			);
		}
	}
