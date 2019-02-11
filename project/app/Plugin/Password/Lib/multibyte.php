<?php
	/**
	 * Code source des fonctions utilitaires multibyte.
	 *
	 * PHP 5.3
	 *
	 * @package Password.Lib
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	// http://php.net/manual/fr/ref.mbstring.php
	// http://php.net/manual/en/mbstring.overload.php
	// str_replace is binary-safe (http://php.net/str_replace#refsect1-function.str-replace-notes)
	// preg_quote is binary-safe (php.net/preg_quote#refsect1-function.preg-quote-notes)

	if(false === function_exists('mb_str_split')) {
		// @see http://php.net/str_split
		function mb_str_split($string, $split_length = 1, $encoding = null) {
			$encoding = null == $encoding ? mb_internal_encoding() : $encoding;

			if( 1 > $split_length ) {
				return false;
			}

			$result = array();
			$length = mb_strlen($string, $encoding);

			for($i = 0; $i < $length; $i += $split_length) {
				$result[] = mb_substr($string, $i, $split_length, $encoding);
			}

			return $result;
		}
	}

	if( false === function_exists( 'mb_str_shuffle' ) ) {
		// @url http://stackoverflow.com/a/18719855
		function mb_str_shuffle( $str ) {
			$tmp = preg_split( '//u', $str, -1, PREG_SPLIT_NO_EMPTY );
			shuffle( $tmp );
			return join( '', $tmp );
		}

	}
?>