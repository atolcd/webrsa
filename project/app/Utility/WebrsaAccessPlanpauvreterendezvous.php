<?php
	/**
	 * Code source de la classe WebrsaAccessPlanpauvreterendezvous.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessRendezvous ...
	 *
	 * @package app.Utility
	 */
	class WebrsaAccessPlanpauvreterendezvous extends WebrsaAbstractAccess
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
		protected static function cohorte_infocol(array $record, array $params) {
			return true;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function cohorte_infocol_stock(array $record, array $params) {
			return true;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function cohorte_infocol_imprime(array $record, array $params) {
			return true;
		}
		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function imprime_infocol(array $record, array $params) {
			return true;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _cohorte_infocol_imprime_impression(array $record, array $params) {
			return true;
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
					'cohorte_infocol',
					'cohorte_infocol_stock',
					'cohorte_infocol_imprime',
					'imprime_infocol',
					'cohorte_infocol_imprime_impression',
				)
			);
			return $result;
		}
	}
?>