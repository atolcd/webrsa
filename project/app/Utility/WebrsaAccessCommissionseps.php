<?php
	/**
	 * Code source de la classe WebrsaAccessCommissionseps.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessCommissionseps ...
	 *
	 * @see CommissionsepsController::$etatsActions
	 *
	 * @package app.Utility
	 */
	class WebrsaAccessCommissionseps extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Commissionep',
				'departement' => Configure::read('Cg.departement'),
			);
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _decisionep(array $record, array $params) {
			$etatcommissionep = Hash::get( $record, 'Commissionep.etatcommissionep' );
			return in_array( $etatcommissionep, array( 'traiteep', 'decisioncg', 'traite', 'annule' ) );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _decisioncg(array $record, array $params) {
			$etatcommissionep = Hash::get( $record, 'Commissionep.etatcommissionep' );
			return in_array( $etatcommissionep, array( 'traite', 'annule' ) );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _delete(array $record, array $params) {
			$etatcommissionep = Hash::get( $record, 'Commissionep.etatcommissionep' );
			return false === in_array( $etatcommissionep, array( 'traiteep', 'decisioncg', 'traite', 'annule' ) );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _edit(array $record, array $params) {
			$etatcommissionep = Hash::get( $record, 'Commissionep.etatcommissionep' );
			return false === in_array( $etatcommissionep, array( 'decisionep', 'traiteep', 'decisioncg', 'traite', 'annule' ) );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _fichesynthese(array $record, array $params) {
			$etatcommissionep = Hash::get( $record, 'Commissionep.etatcommissionep' );
			return false === in_array( $etatcommissionep, array( 'cree', 'quorum' ) );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _fichessynthese(array $record, array $params) {
			$etatcommissionep = Hash::get( $record, 'Commissionep.etatcommissionep' );
			return false === in_array( $etatcommissionep, array( 'cree', 'quorum' ) );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _impressionDecision(array $record, array $params) {
			$etatcommissionep = Hash::get( $record, 'Commissionep.etatcommissionep' );
			return 'traite' === $etatcommissionep;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _impressionsDecisions(array $record, array $params) {
			$etatcommissionep = Hash::get( $record, 'Commissionep.etatcommissionep' );
			return 'traite' === $etatcommissionep;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _impressionpv(array $record, array $params) {
			$etatcommissionep = Hash::get( $record, 'Commissionep.etatcommissionep' );
			return in_array( $etatcommissionep, array( 'traiteep', 'decisioncg', 'traite' ) );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _printConvocationBeneficiaire(array $record, array $params) {
			$etatcommissionep = Hash::get( $record, 'Commissionep.etatcommissionep' );
			return false === in_array( $etatcommissionep, array( 'cree', 'quorum' ) );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _traiterep(array $record, array $params) {
			$etatcommissionep = Hash::get( $record, 'Commissionep.etatcommissionep' );
			return in_array( $etatcommissionep, array( 'presence', 'decisionep' ) );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _traitercg(array $record, array $params) {
			$etatcommissionep = Hash::get( $record, 'Commissionep.etatcommissionep' );
			return in_array( $etatcommissionep, array( 'traiteep', 'decisioncg' ) );
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
		protected static function _annulervalidation(array $record, array $params) {
			$etatcommissionep = Hash::get( $record, 'Commissionep.etatcommissionep' );
			return in_array( $etatcommissionep, array( 'traiteep', 'traite' ) );
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
					'decisionep',
					'decisioncg',
					'delete',
					'edit',
					'fichessynthese',
					'fichesynthese',
					'impressionDecision',
					'impressionsDecisions',
					'impressionpv',
					'printConvocationBeneficiaire',
					'traiterep',
					'traitercg',
					'view',
					'Historiqueseps.view_passage',
					'annulervalidation',
				)
			);

			return $result;
		}
	}