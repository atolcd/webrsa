<?php
	/**
	 * Code source de la classe WebrsaAccessHistoriqueseps.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessHistoriqueseps ...
	 *
	 * @package app.Utility
	 */
	class WebrsaAccessHistoriqueseps extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Passagecommissionep',
				'departement' => Configure::read('Cg.departement'),
			);
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _view_passage(array $record, array $params) {
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
					'view_passage',
				)
			);

			switch ($params['departement']) {
				case 58:
					$result = self::merge_actions(
						$result, array(
							'Commissionseps.decisionep',
						)
					);
					break;
				default:
					$result = self::merge_actions(
						$result, array(
							'Commissionseps.decisioncg',
						)
					);
			}

			return $result;
		}
	}