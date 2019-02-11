<?php
	/**
	 * Fonctions utilitaires pour le plugin Appchecks.
	 *
	 * PHP 5.3
	 *
	 * @package Appchecks
	 * @subpackage Lib
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Retourne un nombre qui sera plus grand pour une version plus élevée.
	 *
	 * @see http://az.php.net/manual/en/function.phpversion.php (Exemple 2: PHP_VERSION_ID)
	 *
	 * @param string $version La version pour laquelle obtenir l'id
	 * @return integer
	 */
	function version_id( $version ) {
		$tokens = explode( '.', $version );
		$multipliers = array( 0 => 1000000, 1 => 10000, 2 => 100, 3 => 1 );

		$result = 0;
		for( $i = 0 ; $i <= 3 ; $i++ ) {
			if( isset( $tokens[$i] ) ) {
				$result += $multipliers[$i] * $tokens[$i];
			}
		}
		return $result;
	}

	/**
	 * Vérifie qu'une version donnée soit au minimum égale à une certaine version
	 * et soit éventuellement plus petite qu'une version maximale.
	 *
	 * @param string $actual La version à tester
	 * @param string $low La version minimale
	 * @param string $high La version maximale éventuelle
	 * @return boolean
	 */
	function version_difference( $actual, $low, $high = null ) {
		$actual = version_id( $actual );
		$low = version_id( $low );
		$high = ( is_null( $high ) ? null : version_id( $high ) );

		$success = ( $actual >= $low );

		if( !is_null( $high ) ) {
			$success = ( $actual < $high );
		}

		return $success;
	}
?>
