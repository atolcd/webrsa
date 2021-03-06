<?php
	/**
	 * Code source de la classe WebrsaAccessRapportstalendscreances.php.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAccessRapportstalendscreances', 'Utility');

	/**
	 * La classe WebrsaAccessRapportstalendscreances ...
	 *
	 * @package app.Utility
	 */
	abstract class WebrsaAccessRapportstalendscreances extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Rapportstalendscreances',
				'departement' => Configure::read('Cg.departement')
			);
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
					'Rejetstalendscreances.index',
				)
			);

			return $result;
		}

	}
?>