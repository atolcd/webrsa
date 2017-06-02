<?php
    /**
     * Code source de la classe Validation2DefaultFormatter.
     *
     * PHP 5.3
     *
     * @package Validation2
     * @subpackage Utility.Validation2Formatters
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */

    /**
	 * La classe Validation2DefaultFormatter fournit des méthodes de formattage
	 * basiques.
     *
     * @package Validation2
     * @subpackage Utility.Validation2Formatters
     */
	class Validation2DefaultFormatter
	{

		/**
		 * Si le paramètre est une chaîne de caractères, supprime les caractères
		 * blancs à l'avant et à l'arrière de la chaîne avant de la renvoyer.
		 *
		 * @param mixed $value
		 * @return mixed
		 */
		public static function trim( $value ) {
			if( is_string( $value ) ) {
				$value = trim( $value );
			}

			return $value;
		}

		/**
		 * Si le paramètre est une chaîne de caractères vide ou ne contenant que
		 * des caractères blancs, alors la valeur renvoyée est null.
		 *
		 * @param mixed $value
		 * @return mixed
		 */
		public static function null( $value ) {
			if( is_string( $value ) && ( strlen( trim( $value ) ) == 0 ) ) {
				$value = null;
			}

			return $value;
		}

		/**
		 * Si le paramètre ressemble à un "nombre formatté en français", la
		 * valeur renvoyée devient un nombre à entrer dans la base de données.
		 *
		 * <pre>
		 * "6 661" => 6661
		 * "-10 123,67" => -10123.67
		 * </pre>
		 *
		 * @param mixed $value
		 * @return string
		 */
		public static function numeric( $value ) {
			$cleaned = str_replace( ' ', '', $value );

			if( preg_match( '/^(\-{0,1})([0-9]+)(,([0-9]+)){0,1}$/', $cleaned, $matches ) ) {
				// Float
				if( isset( $matches[4] ) ) {
					$value = "{$matches[1]}{$matches[2]}.{$matches[4]}";
				}
				// Integer
				else {
					$value = "{$matches[1]}{$matches[2]}";
				}
			}

			return $value;
		}

		/**
		 * Retourne le suffixe d'une valeur, c'est à dire la partie de la valeur
		 * après le dernier séparateur si celui-ci existe.
		 *
		 * @param mixed $value
		 * @param string $separator
		 * @return string
		 */
		public static function suffix( $value, $separator = '_' ) {
			if( preg_match( "/^(.*){$separator}([^{$separator}]*)$/", $value, $matches ) ) {
				$value = $matches[2];
			}

			return $value;
		}
	}
?>
