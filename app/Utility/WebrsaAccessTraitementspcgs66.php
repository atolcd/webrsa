<?php
	/**
	 * Code source de la classe WebrsaAccessDsp.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessDsp ...
	 *
	 * @package app.Utility
	 */
	class WebrsaAccessTraitementspcgs66 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 * 
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Traitementpcg66',
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
			return Hash::get($record, 'Traitementpcg66.annule') !== 'O';
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _cancel(array $record, array $params) {
			return Hash::get($record, 'Traitementpcg66.annule') !== 'O';
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _delete(array $record, array $params) {
			return Hash::get($record, 'Traitementpcg66.annule') !== 'O';
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _printFicheCalcul(array $record, array $params) {
			return Hash::get($record, 'Traitementpcg66.annule') !== 'O'
				&& Hash::get($record, 'Traitementpcg66.typetraitement') === 'revenu';
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _switch_imprimer(array $record, array $params) {
			return Hash::get($record, 'Traitementpcg66.annule') !== 'O'
				&& Hash::get($record, 'Traitementpcg66.dateenvoicourrier') === null
				&& Hash::get($record, 'Traitementpcg66.typetraitement') === 'courrier'
			;
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _printModeleCourrier(array $record, array $params) {
			return Hash::get($record, 'Traitementpcg66.annule') !== 'O'
				&& Hash::get($record, 'Traitementpcg66.typetraitement') === 'courrier'
			;
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _envoiCourrier(array $record, array $params) {
			return Hash::get($record, 'Traitementpcg66.annule') !== 'O'
				&& Hash::get($record, 'Traitementpcg66.dateenvoicourrier') === null
				&& Hash::get($record, 'Traitementpcg66.typetraitement') === 'courrier'
			;
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _deverseDO(array $record, array $params) {
			return Hash::get($record, 'Traitementpcg66.annule') !== 'O'
				&& Hash::get($record, 'Traitementpcg66.typetraitement') === 'revenu'
				&& Hash::get($record, 'Traitementpcg66.reversedo') === '1'
			;
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _reverseDO(array $record, array $params) {
			return Hash::get($record, 'Traitementpcg66.annule') !== 'O'
				&& Hash::get($record, 'Traitementpcg66.typetraitement') === 'revenu'
				&& Hash::get($record, 'Traitementpcg66.reversedo') !== '1'
			;
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _clore(array $record, array $params) {
			return Hash::get($record, 'Traitementpcg66.annule') !== 'O'
				&& Hash::get($record, 'Traitementpcg66.clos') !== 'O'
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
			$params = self::params($params);
			$result = self::normalize_actions(
				array(
					'add' => array('ajoutPossible' => true),
					'view',
					'edit',
					'cancel',
					'delete',
					'printFicheCalcul',
					'switch_imprimer',
					'printModeleCourrier',
					'envoiCourrier',
					'deverseDO',
					'reverseDO',
					'clore',
				)
			);
			
			if ($params['departement'] !== 66) {
				$result = array();
			}
			
			return $result;
		}
	}
?>