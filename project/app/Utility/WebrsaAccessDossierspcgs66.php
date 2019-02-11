<?php
	/**
	 * Code source de la classe WebrsaAccessDossierspcgs66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessDossierspcgs66 ...
	 *
	 * @package app.Utility
	 */
	class WebrsaAccessDossierspcgs66 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 * 
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Dossierpcg66',
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
		protected static function _add(array $record, array $params) {
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
		protected static function _imprimer(array $record, array $params) {
			return Hash::get($record, "Decisiondossierpcg66.etatdossierpcg") !== 'annule'
				&& Hash::get($record, "Decisiondossierpcg66.validationproposition") === 'O'
				&& (Hash::get($record, "Decisiondossierpcg66.retouravistechnique") !== '1'
				|| Hash::get($record, "Decisiondossierpcg66.vuavistechnique") === '1')
			;
		}

		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _cancel(array $record, array $params) {
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
			$result = self::normalize_actions(
				array(
					'add',
					'view',
					'edit',
					'imprimer',
					'cancel',
					'delete',
				)
			);
			
			return $result;
		}
	}