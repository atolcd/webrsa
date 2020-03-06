<?php
	/**
	 * Code source de la classe WebrsaAccessDecisionsdossierspcgs66.
	 *
	 * PHP 7.2
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessDecisionsdossierspcgs66 ...
	 *
	 * @package app.Utility
	 */
	class WebrsaAccessDecisionsdossierspcgs66 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Decisiondossierpcg66',
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
			return Hash::get($record, "Decisiondossierpcg66.dernier")
				&& Hash::get($record, "Dossierpcg66.etatdossierpcg") !== 'decisionvalid'
				&& Hash::get($record, "Dossierpcg66.etatdossierpcg") !== 'decisionnonvalid'
				&& Hash::get($record, "Dossierpcg66.etatdossierpcg") !== 'transmisop'
				&& Hash::get($record, "Decisiondossierpcg66.etatdossierpcg") !== 'annule'
			;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _avistechnique(array $record, array $params) {
			return Hash::get($record, "Decisiondossierpcg66.dernier")
				&& Hash::get($record, "Dossierpcg66.etatdossierpcg") !== 'transmisop'
				&& Hash::get($record, "Dossierpcg66.etatdossierpcg") !== 'decisionvalid'
				&& Hash::get($record, "Dossierpcg66.etatdossierpcg") !== 'decisionnonvalid'
				&& Hash::get($record, "Decisiondossierpcg66.etatdossierpcg") !== 'annule'
				&& Hash::get($record, "Decisiondossierpcg66.instrencours") !== '1'
				&& Hash::get($record, "Decisiondossierpcg66.decisionpdo_id") !== null
			;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _validation(array $record, array $params) {
			return Hash::get($record, "Decisiondossierpcg66.dernier")
				&& Hash::get($record, "Decisiondossierpcg66.avistechnique")
				&& Hash::get($record, "Dossierpcg66.etatdossierpcg") !== 'decisionvalid'
				&& Hash::get($record, "Dossierpcg66.etatdossierpcg") !== 'decisionnonvalid'
				&& Hash::get($record, "Dossierpcg66.etatdossierpcg") !== 'transmisop'
				&& Hash::get($record, "Decisiondossierpcg66.etatdossierpcg") !== 'annule'
			;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _transmitop(array $record, array $params) {
			return Hash::get($record, "Decisiondossierpcg66.dernier")
				&& Hash::get($record, "Decisiondossierpcg66.etatdossierpcg") !== 'annule'
				&& Hash::get($record, "Decisiondossierpcg66.validationproposition") === 'O'
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
			return Hash::get($record, "Decisiondossierpcg66.etatdossierpcg") !== 'annule';
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
					'add' => array('ajoutPossible' => true),
					'view',
					'edit',
					'avistechnique',
					'validation',
					'Dossierspcgs66.imprimer',
					'transmitop',
					'cancel',
					'delete',
					'filelink',
				)
			);
			return $result;
		}

	}