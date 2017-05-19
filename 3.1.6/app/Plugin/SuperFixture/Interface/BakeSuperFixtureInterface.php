<?php
	/**
	 * Code source de la classe BakeSuperFixtureInterface.
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */

	/**
	 * Interface pour generateur de SuperFixture
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	interface BakeSuperFixtureInterface
	{
		/**
		 * Permet d'obtenir les informations nécéssaire pour générer la SuperFixture
		 * 
		 * @return array
		 */
		public static function getData();
	}