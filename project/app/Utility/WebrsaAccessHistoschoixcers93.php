<?php
	/**
	 * Code source de la classe WebrsaAccessHistoschoixcers93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'CakeSession', 'Model/Datasource' );
	App::uses( 'WebrsaAbstractAccess', 'Utility' );

	/**
	 * La classe WebrsaAccessHistoschoixcers93 ...
	 *
	 * @package app.Utility
	 */
	abstract class WebrsaAccessHistoschoixcers93 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params( array $params = array() ) {
			return $params + array(
				'alias' => 'Histochoixcer93',
				'departement' => (int) Configure::read( 'Cg.departement' )
			);
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _attdecisioncpdv(array $record, array $params) {
			$params = self::params( $params );
			$positioncer = Hash::get( $record, 'Cer93.positioncer' );
			return ( $positioncer === '01signe' );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _attdecisioncg(array $record, array $params) {
			$params = self::params( $params );
			$positioncer = Hash::get( $record, 'Cer93.positioncer' );
			return ( $positioncer === '02attdecisioncpdv' );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _premierelecture(array $record, array $params) {
			$params = self::params( $params );
			$positioncer = Hash::get( $record, 'Cer93.positioncer' );
			return ( $positioncer === '03attdecisioncg' );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _premierelecture_consultation(array $record, array $params) {
			$params = self::params( $params );
			$positioncer = Hash::get( $record, 'Cer93.positioncer' );
			$position = preg_replace( '/^([0-9]+).*$/', '\1', $positioncer );
			return intval( $position, 10 ) >= 3;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _secondelecture(array $record, array $params) {
			$params = self::params( $params );
			$positioncer = Hash::get( $record, 'Cer93.positioncer' );
			return ( $positioncer === '04premierelecture' );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _secondelecture_consultation(array $record, array $params) {
			$params = self::params( $params );
			$positioncer = Hash::get( $record, 'Cer93.positioncer' );
			$position = preg_replace( '/^([0-9]+).*$/', '\1', $positioncer );
			return intval( $position, 10 ) >= 4;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _aviscadre(array $record, array $params) {
			$params = self::params( $params );
			$positioncer = Hash::get( $record, 'Cer93.positioncer' );
			return ( $positioncer === '05secondelecture' );
		}
		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _aviscadre_consultation(array $record, array $params) {
			$params = self::params( $params );
			$positioncer = Hash::get( $record, 'Cer93.positioncer' );
			$position = preg_replace( '/^([0-9]+).*$/', '\1', $positioncer );
			return intval( $position, 10 ) >= 5;
		}

		/**
		 * Liste les actions disponnible
		 * Si une action pointe sur un autre controler, il faut préciser son nom
		 * ex : Moncontroller.monaction
		 *
		 * @param array $params
		 * @return array
		 */
		public static function actions(array $params = array()) {
			$params = self::params( $params );

			$result = self::normalize_actions(
				array(
					'attdecisioncpdv',
					'attdecisioncg',
					'premierelecture',
					'premierelecture_consultation',
					'secondelecture',
					'secondelecture_consultation',
					'aviscadre',
					'aviscadre_consultation',
				)
			);

			return $result;
		}

	}
?>