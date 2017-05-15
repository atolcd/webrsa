<?php
	/**
	 * Code source de la classe Xinflector.
	 *
	 * PHP 5.3
	 *
	 * @package app.Lib
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Xinflector fournit des méthodes supplémentaires pour gérer les
	 * chaînes de caractères contenant des noms de modèles et de champs.
	 *
	 * @deprecated
	 *
	 * @package app.Lib
	 */
	class Xinflector extends Inflector
	{
		/**
		 * Extracts model name and field name from a path.
		 *
		 * @deprecated Utiliser la fonction model_field() à la place.
		 *
		 * @param string $path ie. User.username, User.0.id
		 * @return array( string $model, string $field ) ie. array( 'User', 'username' ), array( 'User', 'id' )
		 */
		static public function modelField( $path ) {
			if( preg_match( "/(?<!\w)(\w+)(\.|\.[0-9]+\.)(\w+)$/", $path, $matches ) ) {
				return array( $matches[1], $matches[3] );
			}

			trigger_error( "Could not extract model and field names from the following path: \"{$path}\"", E_USER_WARNING );
			return null;
		}

		/**
		 * Concatenates model name and an array of field names to an array of paths.
		 *
		 * @deprecated (pas / plus utilisée)
		 *
		 * @param string $model ie. 'User'
		 * @param array $fields ie. array( 'id', 'username', .. )
		 * @return array ie. array( 'User.id', 'User.username' )
		 */
		static public function fieldNames( $model, array $fields ) {
			$return = array();
			foreach( $fields as $key => $field ) {
				$return[$key] = "{$model}.{$field}";
			}
			return $return;
		}
	}
?>