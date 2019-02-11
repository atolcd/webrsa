<?php
	/**
	 * Code source de la classe WebrsaAccessTransfertspdvs93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractAccess', 'Utility' );

	/**
	 * La classe WebrsaAccessTransfertspdvs93 ...
	 *
	 * @package app.Utility
	 */
	abstract class WebrsaAccessTransfertspdvs93 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Transfertpdv93',
				'departement' => (int)Configure::read('Cg.departement')
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
					'Cohortestransfertspdvs93.impression',
				)
			);

			return $result;
		}
	}
?>