<?php
	/**
	 * Code source de la classe WebrsaAccessApres66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessApres66 ...
	 *
	 * @package app.Utility
	 */
	abstract class WebrsaAccessApres66 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Apre66',
				'departement' => Configure::read('Cg.departement')
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
			return Hash::get($params, 'ajoutPossible');
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _view66(array $record, array $params) {
			return true;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _edit(array $record, array $params) { //		ANNulé TRAité ou VALidé
			return !in_array(Hash::get($record, 'Apre66.etatdossierapre'), array('ANN', 'TRA', 'VAL'));
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _maillink(array $record, array $params) {
			return Hash::get($record, 'Apre66.etatdossierapre') !== 'ANN';
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _filelink(array $record, array $params) {
			return true;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _cancel(array $record, array $params) {
			return Hash::get($record, 'Apre66.etatdossierapre') !== 'ANN';
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _impression(array $record, array $params) { // ANNulé ou INComplet
			return !in_array(Hash::get($record, 'Apre66.etatdossierapre'), array('ANN', 'INC'));
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
					'add' => array('ajoutPossible' => true),
					'view66',
					'edit',
					'impression',
					'maillink',
					'filelink',
					'cancel',
				)
			);

			if ($params['departement'] != 66) {
				$result = array();
			}

			return $result;
		}
	}
?>