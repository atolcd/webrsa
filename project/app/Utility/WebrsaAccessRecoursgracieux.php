<?php
	/**
	 * Code source de la classe WebrsaAccessRecoursgracieux.
	 *
	 * PHP 7.2
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility', 'Recourgracieux');

	/**
	 * La classe WebrsaAccessCreances ...
	 *
	 * @package app.Utility
	 */
	abstract class WebrsaAccessRecoursgracieux extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Recoursgracieux',
				'departement' => (int)Configure::read('Cg.departement')
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
					'add' => array('ajoutPossible' => true),
					'view',
					'edit',
					'filelink',
					'email',
					'affecter',
					'proposer',
					'deleteproposition',
					'decider',
					'envoyer',
					'traiter',
					'proposercontestationcreances',
					'proposerremisecreances',
					'proposercontestationindus',
					'proposerremiseindus',
					'delete',
					'Dossierspcgs66.view'
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
		protected static function _email(array $record, array $params) {
			if (
				$record['Recourgracieux']['etat'] == 'ATTSIGNATURE'
				|| $record['Recourgracieux']['etat'] == 'ATTINSTRUCTION'
			){
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _edit(array $record, array $params) {
			if (
				$record['Recourgracieux']['etat'] == 'ATTAFECT'
			){
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _affecter(array $record, array $params) {
			if (
				$record['Recourgracieux']['etat'] == 'ATTAFECT'
			){
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _proposer(array $record, array $params) {
			if (
				$record['Recourgracieux']['etat'] == 'ATTINSTRUCTION'
				|| $record['Recourgracieux']['etat'] == 'INSTRUCTION'
			){
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _proposercontestationcreances(array $record, array $params) {
			if (
				$record['Recourgracieux']['etat'] == 'ATTINSTRUCTION'
				|| $record['Recourgracieux']['etat'] == 'INSTRUCTION'
			){
				return true;
			}else{
				return false;
			}
		}
		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _proposerremisecreances(array $record, array $params) {
			if (
				$record['Recourgracieux']['etat'] == 'ATTINSTRUCTION'
				|| $record['Recourgracieux']['etat'] == 'INSTRUCTION'
			){
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _proposercontestationindus(array $record, array $params) {
			if (
				$record['Recourgracieux']['etat'] == 'ATTINSTRUCTION'
				|| $record['Recourgracieux']['etat'] == 'INSTRUCTION'
			){
				return true;
			}else{
				return false;
			}
		}
		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _proposerremiseindus(array $record, array $params) {
			if (
				$record['Recourgracieux']['etat'] == 'ATTINSTRUCTION'
				|| $record['Recourgracieux']['etat'] == 'INSTRUCTION'
			){
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _deleteproposition(array $record, array $params) {
			return true;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _decider(array $record, array $params) {
			if (
				$record['Recourgracieux']['etat'] == 'ATTVALIDATION'
				|| $record['Recourgracieux']['etat'] == 'VALIDTRAITEMENT'
			){
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _envoyer(array $record, array $params) {
			if (
				$record['Recourgracieux']['etat'] == 'ATTSIGNATURE'
			){
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _traiter(array $record, array $params) {
			if (
				$record['Recourgracieux']['etat'] == 'ATTSIGNATURE'
				|| $record['Recourgracieux']['etat'] == 'ATTENVOIE'
			){
				return true;
			}else{
				return false;
			}
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
	}
?>