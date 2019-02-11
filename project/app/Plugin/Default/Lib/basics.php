<?php
	/**
	 * Librairie du plugin Default.
	 *
	 * PHP 5.4
	 *
	 * @package AclUtilities
	 * @subpackage Lib
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	if( !function_exists( 'model_field') ) {
		/**
		 * Extracts model name and field name from a path.
		 *
		 * @param string $path ie. User.username, User.0.id
		 * @return array( string $model, string $field ) ie. array( 'User', 'username' ), array( 'User', 'id' )
		 */
		function model_field( $path ) {
			if( preg_match( "/(?<!\w)(\w+)(\.|\.[0-9]+\.)(\w+)$/", $path, $matches ) ) {
				return array( $matches[1], $matches[3] );
			}

			trigger_error( "Could not extract model and field names from the following path: \"{$path}\"", E_USER_WARNING );
			return null;
		}
	}

	if( !function_exists( 'vfListeToArray') ) {
		/**
		 * Transforme un chaîne de caractères représentant une implosion de valeurs,
		 * chacune d'entre elle étant préfixée par le caractère "-" et étant séparée
		 * de la suivante par ""\n\r" en un tableau contenant chacune des valeurs non
		 * préfixée.
		 *
		 * Par exemple, un champ virtuel construit avec AppModel::vfListe().
		 *
		 * Exemple:
		 * <pre>vfListeToArray( '- CAF' ) </pre>
		 * donne
		 * <pre>array( 0 => 'CAF' )</pre>
		 *
		 * @param string $liste
		 * @param string $separator
		 * @return array
		 */
		function vfListeToArray( $liste, $separator = "\n\r-" ) {
			$liste = trim( $liste, '-' );
			return Hash::filter( explode( $separator, $liste ) );
		}
	}
?>
