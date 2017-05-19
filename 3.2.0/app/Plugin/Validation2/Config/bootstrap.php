<?php
	/**
	 * Boostrap du plugin Validation2.
	 *
	 * PHP 5.3
	 *
	 * @package Validation2
	 * @subpackage Config
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once dirname( __FILE__ ).DS.'..'.DS.'Lib'.DS.'basics.php';

	// @see http://api.cakephp.org/2.7/annotation-group-deprecated.html
	if( false === defined( 'NOT_BLANK_RULE_NAME' ) ) {
		define( 'NOT_BLANK_RULE_NAME', version_compare( Configure::version(), '2.7.0', '<' ) ? 'notEmpty' : 'notBlank' );
	}
?>
