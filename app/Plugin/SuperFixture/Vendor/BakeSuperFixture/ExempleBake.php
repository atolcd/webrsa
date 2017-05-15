<?php
	/**
	 * Code source de la classe SuperFixture.
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */

	App::uses('BSFObject', 'SuperFixture.Utility');
	App::uses('BakeSuperFixtureInterface', 'SuperFixture.Interface');

	/**
	 * Generateur de SuperFixture
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class ExempleBake implements BakeSuperFixtureInterface {
		/**
		 * Permet d'obtenir les informations nécéssaire pour générer la SuperFixture
		 * 
		 * @return array
		 */
		public static function getData() {
			$user1 = new BSFObject();
		}
	}
