<?php
	/**
	 * Code source de la classe WebrsaAccessCuis66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessCuis66 ...
	 *
	 * @package app.Utility
	 */
	class WebrsaAccessCuis66 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 * 
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Cui66',
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
		protected static function _impression_fichedeliaison(array $record, array $params) {
			return true;
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _impression(array $record, array $params) {
			return true;
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _email(array $record, array $params) {
			return true;
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _email_add(array $record, array $params) {
			return true;
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _email_view(array $record, array $params) {
			return true;
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _email_edit(array $record, array $params) {
			return Hash::get($record, 'Emailcui.dateenvoi') === null;
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _email_send(array $record, array $params) {
			return Hash::get($record, 'Emailcui.dateenvoi') === null;
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _email_delete(array $record, array $params) {
			return Hash::get($record, 'Emailcui.dateenvoi') === null;
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _annule(array $record, array $params) {
			return Hash::get($record, 'Cui66.etatdossiercui66') !== 'annule';
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
		protected static function _notification(array $record, array $params) {
			return Hash::get($record, 'Cui66.etatdossiercui66') === 'attentenotification';
		}
		
		/**
		 * Liste des "actions" à ignorer leur utilitée dans la vérification de l'application.
		 * Peut servir à ignorer des méthodes protégés qui ne concernent pas une action ou
		 * des actions qui dépendent de paramètres autre que celui du département.
		 * 
		 * @return array - normalisé avec self::normalize_actions
		 */
		public static function ignoreCheck() {
			return self::normalize_actions(
				array(
					'email_add' => array('isModuleEmail' => true),
					'email_edit' => array('isModuleEmail' => true),
					'email_view' => array('isModuleEmail' => true),
					'email_send' => array('isModuleEmail' => true),
					'email_delete' => array('isModuleEmail' => true),
					'Propositionscuis66.index' => array('isModuleProposition' => true),
				)
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
					'impression_fichedeliaison',
					'impression',
					'email',
					'annule',
					'delete',
					'filelink',
					'notification',
				)
			);
			
			if (Hash::get($params, 'isModuleEmail')) {
				$result = self::normalize_actions(
					array(
						'email_add' => array('isModuleEmail' => true),
						'email_edit' => array('isModuleEmail' => true),
						'email_view' => array('isModuleEmail' => true),
						'email_send' => array('isModuleEmail' => true),
						'email_delete' => array('isModuleEmail' => true),
					)
				);
			}
			
			if (Hash::get($params, 'isModuleProposition')) {
				$result = self::normalize_actions(
					array(
						'Propositionscuis66.index' => array('isModuleProposition' => true),
					)
				);
			}
			
			if ($params['departement'] !== 66) {
				$result = array();
			}
			
			return $result;
		}
	}