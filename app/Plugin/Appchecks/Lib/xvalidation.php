<?php
	/**
	 * Code source de la classe Xvalidation.
	 *
	 * PHP 5.3
	 *
	 * @package Appchecks
	 * @subpackage Lib
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Xvalidation fournit des méthodes de validation de paramètres
	 * simples.
	 *
	 * Cette classe est une adaptation d'une classe du coeur de 1.2.11.
	 *
	 * @package Appchecks
	 * @subpackage Lib
	 */
	class Xvalidation
	{
		/**
		 * Some complex patterns needed in multiple places
		 * Vient du coeur de CakePHP 1.2.11
		 *
		 * @var array
		 * @access private
		 */
		protected $__pattern = array(
			'ip' => '(?:(?:25[0-5]|2[0-4][0-9]|(?:(?:1[0-9])?|[1-9]?)[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|(?:(?:1[0-9])?|[1-9]?)[0-9])',
			'hostname' => '(?:[a-z0-9][-a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,4}|museum|travel)'
		);

		/**
		 *
		 * @staticvar array $instance
		 * @return \Xvalidation
		 */
		public static function &getInstance() {
			static $instance = array( );

			if( !$instance ) {
				$instance[0] =  new Xvalidation();
			}
			return $instance[0];
		}

		/**
		 *
		 * @param type $check
		 * @return boolean
		 */
		public function integer( $check ) {
			return is_int( $check );
		}

		/**
		 *
		 * @param type $check
		 * @return boolean
		 */
		public function numeric( $check ) {
			return is_numeric( $check );
		}

		/**
		 *
		 * @param type $check
		 * @param type $list
		 * @return type
		 */
		public function inList( $check, $list ) {
			return in_array( $check, $list );
		}

		/**
		 *
		 * @param type $check
		 * @return type
		 */
		public function string( $check ) {
			return is_string( $check );
		}

		/**
		 *
		 * @param type $check
		 * @return type
		 */
		public function boolean( $check ) {
			return is_bool( $check );
		}

		/**
		 *
		 * @param type $check
		 * @return type
		 */
		public function isarray( $check ) {
			return is_array( $check );
		}

		/**
		 * Vérifie que la ou les valeurs fassent toutes partie de la liste des
		 * valeurs acceptées.
		 *
		 * @param mixed $check Une valeur ou un ensemble de valeurs
		 * @param array $accepted La liste des valeurs acceptées
		 * @return boolean
		 */
		public function inListArray( $check, array $accepted ) {
			$check = (array)$check;

			foreach( $check as $value ) {
				if( false === in_array( $value, $accepted ) ) {
					return false;
				}
			}

			return true;
		}

		/**
		 *
		 * @param type $check
		 * @return type
		 */
		public function dir( $check ) {
			return is_dir( $check ) && is_readable( $check );
		}

		/**
		 *
		 * @param type $check
		 * @return type
		 */
		public function writableDir( $check ) {
			return is_dir( $check ) && is_writable( $check );
		}

		/**
		 * Modification de la méthode de CakePHP 1.2.11.
		 * Accepte localhost en plus en TLD.
		 *
		 * @see http://cakephp.lighthouseapp.com/projects/42648/tickets/2544-url-validation-fails-on-localhost
		 * @see http://www.w3.org/Addressing/URL/url-spec.txt
		 *
		 * @param mixed $check
		 * @param boolean $strict
		 * @return boolean
		 */
		public function url( $check, $strict = false ) {
			$_this =  Xvalidation::getInstance();
			$validChars = '(['.preg_quote( '!"$&\'()*+,-.@_:;=' ).'\/0-9a-z]|(%[0-9a-f]{2}))';
			$regex = '/^(?:(?:https?|ftps?|file|news|gopher):\/\/)'.(!empty( $strict ) ? '' : '?').
					'(?:'.$_this->__pattern['ip'].'|'.$_this->__pattern['hostname'].'|localhost'.')(?::[1-9][0-9]{0,3})?'.
					'(?:\/?|\/'.$validChars.'*)?'.
					'(?:\?'.$validChars.'*)?'.
					'(?:#'.$validChars.'*)?$/i';
			return preg_match( $regex, $check );
		}
	}
?>