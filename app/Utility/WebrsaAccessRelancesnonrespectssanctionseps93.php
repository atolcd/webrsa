<?php
	/**
	 * Code source de la classe WebrsaAccessRelancesnonrespectssanctionseps93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessRelancesnonrespectssanctionseps93 ...
	 *
	 * @package app.Utility
	 */
	class WebrsaAccessRelancesnonrespectssanctionseps93 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Relancenonrespectsanctionep93',
				'departement' => (int)Configure::read( 'Cg.departement' ),
			);
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _add( array $record, array $params ) {
			$params = self::params($params);
			return Hash::get($params, 'ajoutPossible');
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _view(array $record, array $params) {
			return false;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _impression(array $record, array $params) {
			$id = Hash::get( $record, 'Pdf.id' );
			return !empty( $id );
		}

		/**
		 * Liste les actions disponnible
		 *
		 * @param array $params
		 * @return array
		 */
		public static function actions(array $params = array()) {
			$result = self::normalize_actions(
				array(
					'add' => array( 'ajoutPossible' => true),
					'view',
					'impression'
				)
			);
			return $result;
		}
	}
?>