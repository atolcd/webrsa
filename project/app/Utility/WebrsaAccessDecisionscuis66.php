<?php
	/**
	 * Code source de la classe WebrsaAccessDecisionscuis66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessDecisionscuis66 ...
	 *
	 * @package app.Utility
	 */
	class WebrsaAccessDecisionscuis66 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 * 
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Decisioncui66',
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
		protected static function _index(array $record, array $params) {
			return true;
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _add(array $record, array $params) {
			return self::_index($record, $params)
				&& Hash::get($params, 'ajoutPossible');
		}

		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _view(array $record, array $params) {
			return self::_index($record, $params);
		}

		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _edit(array $record, array $params) {
			return self::_index($record, $params);
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _filelink(array $record, array $params) {
			return self::_index($record, $params);
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _delete(array $record, array $params) {
			return self::_index($record, $params);
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _impression(array $record, array $params) {
			return self::_index($record, $params);
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _impression_decisionelu(array $record, array $params) {
			return self::_index($record, $params);
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _impression_notifbenef(array $record, array $params) {
			return self::_index($record, $params);
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _impression_notifemployeur(array $record, array $params) {
			return self::_index($record, $params);
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _impression_attestationcompetence(array $record, array $params) {
			return self::_index($record, $params);
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
					'index',
					'add' => array('ajoutPossible' => true, 'isModuleDecision' => true),
					'edit',
					'view',
					'impression',
					'impression_decisionelu',
					'impression_notifbenef',
					'impression_notifemployeur',
					'impression_attestationcompetence',
					'delete',
					'filelink',
				)
			);
			
			if ($params['departement'] !== 66) {
				$result = array();
			}
			
			return $result;
		}
	}