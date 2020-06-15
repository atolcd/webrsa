<?php
	/**
	 * Code source de la classe WebrsaAccessProposcontratsinsertioncovs58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessProposcontratsinsertioncovs58 ...
	 *
	 * @package app.Utility
	 */
	class WebrsaAccessProposcontratsinsertioncovs58 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Propocontratinsertioncov58',
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
		protected static function _add(array $record, array $params) {
			$params = self::params($params);

			return Hash::get($params, 'haveOrient')
				&& !Hash::get($params, 'haveOrientEmploi')
				&& Hash::get($record, 'Contratinsertion.dernier')
			;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _edit(array $record, array $params) {
			return Hash::get($record, 'Passagecov58.etatdossiercov') !== 'associe';
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _delete(array $record, array $params) {
			return Hash::get($record, 'Passagecov58.etatdossiercov') === null;
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
					'add' => array('haveOrient' => true, 'haveOrientEmploi' => true),
					'edit',
					'delete'
				)
			);

			if ($params['departement'] != 58) {
				$result = array();
			}

			return $result;
		}
	}
?>