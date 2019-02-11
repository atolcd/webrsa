<?php
	/**
	 * Code source de la classe WebrsaAccessFichesprescriptions93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractAccess', 'Utility' );

	/**
	 * La classe WebrsaAccessFichesprescriptions93 ...
	 *
	 * @package app.Utility
	 */
	abstract class WebrsaAccessFichesprescriptions93 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params( array $params = array() ) {
			return $params + array(
				'alias' => 'Ficheprescription93',
				'departement' => (int) Configure::read( 'Cg.departement' )
			);
		}

		private static function __permissionFicheprescription93Statut( array $record ) {
			$statut = Hash::get( $record, 'Ficheprescription93.statut' );
			return ( (int) substr( $statut, 0, 2 ) != 99 );
		}

		private static function __permissionReferentHorszone( array $record ) {
			$horszone = Hash::get( $record, 'Referent.horszone' );
			return in_array( $horszone, array( false, null ), true );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _add( array $record, array $params ) {
			$params = self::params( $params );
			return Hash::get( $params, 'ajoutPossible' );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _cancel( array $record, array $params ) {
			return self::__permissionFicheprescription93Statut( $record )
				&& self::__permissionReferentHorszone( $record );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _edit(array $record, array $params) {
			return self::__permissionFicheprescription93Statut( $record )
				&& self::__permissionReferentHorszone( $record );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _impression(array $record, array $params) {
			return true;
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
			$params = self::params($params);
			$result = self::normalize_actions(
				array(
					'add' => array( 'ajoutPossible' => true ),
					'cancel',
					'edit',
					'impression',
				)
			);

			return $result;
		}
	}
?>