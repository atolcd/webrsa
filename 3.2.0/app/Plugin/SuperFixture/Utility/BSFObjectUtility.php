<?php
	/**
	 * Code source de la classe SuperFixture.
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */

	App::uses('BSFObject', 'SuperFixture.Utility');
	
	/**
	 * La classe BSFObjectUtility (Bake Super Fixture Object) permet la créations des composants
	 * pour la génération de SuperFixtures
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	abstract class BSFObjectUtility
	{
		/**
		 * Permet de retrouver la clef du premier BSFObject dans le contain d'un BSFObject
		 * qui possède l'attribut modelName avec la valeur demandée
		 * 
		 * @param BSFObject $object
		 * @param String $modelName
		 * @return integer clef du contain pour accéder à l'objet
		 */
		public static function extractKey(BSFObject &$object, $modelName) {
			foreach ($object->contain as $key => $obj) {
				if ($obj->modelName === $modelName) {
					return $key;
				}
			}
			
			return false;
		}
	}