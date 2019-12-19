<?php
	/**
	 * Code source de la classe WebrsaAccessPlanpauvreteorientation.
	 *
	 * PHP 7.2
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessPlanpauvreteorientation ...
	 *
	 * @package app.Utility
	 */
	class WebrsaAccessPlanpauvreteorientation extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
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
		protected static function _cohorte_isemploi(array $record, array $params) {
			return true;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _cohorte_isemploi_stock(array $record, array $params) {
			return true;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _cohorte_isemploi_imprime(array $record, array $params) {
			return true;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _cohorte_isemploi_stock_imprime(array $record, array $params) {
			return true;
		}

		/**
		 * Liste les actions disponibles
		 *
		 * @param array $params
		 * @return array
		 */
		public static function actions(array $params = array()) {
			$result = self::normalize_actions(
				array(
					'cohorte_isemploi',
					'cohorte_isemploi_stock',
					'cohorte_isemploi_imprime',
					'cohorte_isemploi_stock_imprime'
				)
			);
			return $result;
		}
	}
?>