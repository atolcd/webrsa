<?php
	/**
	 * Code source de la classe WebrsaAccessProposdecisionscers66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessProposdecisionscers66 ...
	 *
	 * @package app.Utility
	 */
	class WebrsaAccessProposdecisionscers66 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Propodecisioncer66',
				'departement' => Configure::read( 'Cg.departement' ),
				'ajoutPossible' => true
			);
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _propositionsimple(array $record, array $params) {
			$params = self::params($params);

			return !in_array(Hash::get($record, 'Contratinsertion.positioncer'), array('annule'))
				&& Hash::get($record, 'Contratinsertion.decision_ci') === 'E'
				&& !Hash::get($record, 'Contratinsertion.datenotification')
				&& Hash::get($record, 'Contratinsertion.forme_ci') === 'S'
			;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _propositionparticulier(array $record, array $params) {
			$params = self::params($params);

			return !in_array(Hash::get($record, 'Contratinsertion.positioncer'), array('annule'))
				&& Hash::get($record, 'Contratinsertion.decision_ci') === 'E'
				&& !Hash::get($record, 'Contratinsertion.datenotification')
				&& Hash::get($record, 'Contratinsertion.forme_ci') === 'C'
			;
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
			$result = self::normalize_actions(
				array(
					'propositionsimple',
					'propositionparticulier'
				)
			);

			if ($params['departement'] != 66) {
				$result = array();
			}

			return $result;
		}
	}
?>