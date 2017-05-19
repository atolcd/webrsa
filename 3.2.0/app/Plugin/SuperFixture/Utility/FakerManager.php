<?php
	/**
	 * Code source de la classe FakerManager.
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */

	/**
	 * Permet d'obtenir une classe de Faker commune à plusieurs utilisations
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	abstract class FakerManager
	{
		/**
		 * @var array Faker
		 */
		protected static $_Fakers = array();
		
		/**
		 * Permet d'obtenir un Faker
		 * Une instance par "$name"
		 * 
		 * @param string $name Peut servir d'espace de nom pour la clef "unique"
		 */
		public static function getInstance($name = 'default') {
			if (!isset(self::$_Fakers[$name])) {
				self::$_Fakers[$name] = Faker\Factory::create('fr_FR');
			}
			
			return self::$_Fakers[$name];
		}
	}