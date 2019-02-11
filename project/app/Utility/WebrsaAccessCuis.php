<?php
	/**
	 * Code source de la classe WebrsaAccessCuis.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessCuis ...
	 *
	 * @package app.Utility
	 */
	class WebrsaAccessCuis extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 * 
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Cui',
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
			return Hash::get($record, 'Cui66.etatdossiercui66') !== 'annule';
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
			$params = self::params($params);
			$result = self::normalize_actions(
				array(
					'add' => array('ajoutPossible' => true),
					'view',
					'edit',
				)
			);
			
			switch ($params['departement']) {
				case 66: 
					$result = self::merge_actions(
						$result, array(
							'Cuis66.impression_fichedeliaison',
							'Cuis66.impression',
							'Cuis66.email',
							'Cuis66.annule',
							'Cuis66.delete',
							'Cuis66.filelink',
							'Cuis66.notification',
							'Propositionscuis66.index',
							'Decisionscuis66.index',
							'Accompagnementscuis66.index',
							'Suspensionscuis66.index',
							'Rupturescuis66.index',
						)
					);
					break;
				default:
					$result = self::merge_actions(
						$result, array(
							'delete',
							'filelink',
						)
					);
			}
			
			return $result;
		}
	}