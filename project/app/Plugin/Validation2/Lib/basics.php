<?php
	/**
	 * Lib basiques du plugin Validation2.
	 *
	 * PHP 5.3
	 *
	 * @package Validation2
	 * @subpackage Lib
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Retourne un array contenant les valeurs en clé et les clés dans des arrays
	 * de valeurs.
	 *
	 * @param array $input
	 * @return array
	 */
	function groupKeysByValues( array $input = array() ) {
		$output = array();

		if( !empty( $input ) ) {
			foreach( $input as $key => $value ) {
				if( !isset( $output[$value] ) ) {
					$output[$value] = array();
				}

				$output[$value][] = $key;
			}
		}

		return $output;
	}

	/**
	 * Retourne le nom de la clé de l'entrée de cache pour une méthode d'une
	 * classe, liée à un modèle CakePHP.
	 *
	 * @param array $cacheKey
	 * @param boolean $underscore
	 * @return string
	 */
	function cacheKey( array $cacheKey, $underscore = false ) {
		$cacheKey = implode( '_', (array)$cacheKey );

		if( $underscore ) {
			$cacheKey = Inflector::underscore( $cacheKey );
		}

		return $cacheKey;
	}

	if( !class_exists( 'MissingUtilityException' ) ) {
		/**
		 * Used when a Utility class cannot be found.
		 *
		 * @package Validation2
		 * @subpckage Lib.Error
		 */
		class MissingUtilityException extends CakeException
		{
			/**
			 * The error message template.
			 *
			 * @var string
			 */
			protected $_messageTemplate = 'Utility class %s could not be found.';

		}
	}
?>
