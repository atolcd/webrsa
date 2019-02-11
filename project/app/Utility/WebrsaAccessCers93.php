<?php
	/**
	 * Code source de la classe WebrsaAccessCers93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'CakeSession', 'Model/Datasource' );
	App::uses( 'WebrsaAbstractAccess', 'Utility' );

	/**
	 * La classe WebrsaAccessCers93 ...
	 *
	 * @package app.Utility
	 */
	abstract class WebrsaAccessCers93 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params( array $params = array() ) {
			return $params + array(
				'alias' => 'Cer93',
				'departement' => (int) Configure::read( 'Cg.departement' )
			);
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _add( array $record, array $params ) {
			$params = self::params( $params );
			return Hash::get( $params, 'ajoutPossible' );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _cancel( array $record, array $params ) {
			$params = self::params( $params );

			$user_type = CakeSession::read( 'Auth.User.type' );
			$positioncer = Hash::get( $record, 'Cer93.positioncer' );

			return (
				'cg' === $user_type
				&& false === in_array( $positioncer, array( '00enregistre', '01signe', '07attavisep', '99annule' ) )
			);
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _delete(array $record, array $params) {
			$params = self::params( $params );
			$positioncer = Hash::get( $record, 'Cer93.positioncer' );
			return ( $positioncer === '00enregistre' );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _edit(array $record, array $params) {
			$params = self::params( $params );
			$positioncer = Hash::get( $record, 'Cer93.positioncer' );
			return ( $positioncer === '00enregistre' );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _edit_apres_signature(array $record, array $params) {
			$params = self::params( $params );

			$user_type = CakeSession::read( 'Auth.User.type' );
			$positioncer = Hash::get( $record, 'Cer93.positioncer' );

			return (
				( 'externe_cpdv' === $user_type && in_array( $positioncer, array( '01signe', '02attdecisioncpdv' ) ) )
				|| ( 'cg' === $user_type && !in_array( $positioncer, array( '00enregistre', '99annule' ) ) )
			);
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
		protected static function _impressionDecision(array $record, array $params) {
			$positioncer = Hash::get( $record, 'Cer93.positioncer' );
			return in_array( $positioncer, array( '99rejete', '99valide' ) );
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _signature(array $record, array $params) {
			$params = self::params( $params );
			$positioncer = Hash::get( $record, 'Cer93.positioncer' );
			return ( $positioncer === '00enregistre' );
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
		 * Liste les actions disponnible
		 * Si une action pointe sur un autre controler, il faut préciser son nom
		 * ex : Moncontroller.monaction
		 *
		 * @param array $params
		 * @return array
		 */
		public static function actions(array $params = array()) {
			$params = self::params( $params );

			$result = self::normalize_actions(
				array(
					'add' => array( 'ajoutPossible' => true ),
					'cancel',
					'delete',
					'edit',
					'edit_apres_signature',
					'impression',
					'impressionDecision',
					'signature',
					'view',
					'Histoschoixcers93.attdecisioncpdv',
					'Histoschoixcers93.attdecisioncg',
					'Histoschoixcers93.premierelecture',
					'Histoschoixcers93.secondelecture',
					'Histoschoixcers93.aviscadre',
					'Signalementseps.add',
					'Contratsinsertion.filelink',
				)
			);

			return $result;
		}
	}
?>