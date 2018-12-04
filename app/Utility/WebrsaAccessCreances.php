<?php
	/**
	 * Code source de la classe WebrsaAccessCreances.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility', 'Creance');

	/**
	 * La classe WebrsaAccessCreances ...
	 *
	 * @package app.Utility
	 */
	abstract class WebrsaAccessCreances extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Creances',
				'departement' => (int)Configure::read('Cg.departement')
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
		protected static function _edit(array $record, array $params) {
			return true;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _delete(array $record, array $params) {
			if ($record['Creance']['orgcre'] == 'FLU'){
				return false;
			}
			if ( $record['Titrecreancier']['etat'] == null
				|| $record['Titrecreancier']['etat'] == 'CREE'
				|| $record['Titrecreancier']['etat'] == 'VALI' 
			) {
				return true;
			}
			return false;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _fluxadd(array $record, array $params) {
			return true;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _copycreance(array $record, array $params) {
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
					'add' => array('ajoutPossible' => true),
					'view',
					'edit',
					'delete',
					'fluxadd',
					'copycreance',
					'filelink',
					'Titrescreanciers.index',
					'Titrescreanciers.add',
				)
			);

			return $result;
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
		protected static function _view(array $record, array $params) {
			return true;
		}
	}
?>