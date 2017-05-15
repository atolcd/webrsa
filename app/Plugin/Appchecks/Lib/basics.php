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
	 * @param string $version
	 * @return integer
	 */
	function version_id( $version ) {
		$version = explode( '.', $version );
		return ( @$version[0] * 1000000 + @$version[1] * 10000 + @$version[2] * 100 + @$version[3] );
	}

	/**
	 *
	 * @param string $actual
	 * @param string $low
	 * @param string $high
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
