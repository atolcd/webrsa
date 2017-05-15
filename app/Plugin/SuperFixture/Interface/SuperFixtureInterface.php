<?php
	/**
	 * Code source de la classe SuperFixtureInterface.
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */

	/**
	 * Interface pour SuperFixture
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	interface SuperFixtureInterface
	{
		/**
		 * Permet d'obtenir les informations nécéssaire pour charger la SuperFixture
		 * 
		 * @return array
		 */
		public static function getData();
	}