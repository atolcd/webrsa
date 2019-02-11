<?php
	/**
	 * Code source de la classe WebrsaAccessQuestionnairesd1pdvs93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractAccess', 'Utility' );
	App::uses( 'WebrsaPermissions', 'Utility' );

	/**
	 * La classe WebrsaAccessQuestionnairesd1pdvs93 ...
	 *
	 * @package app.Utility
	 */
	abstract class WebrsaAccessQuestionnairesd1pdvs93 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Questionnaired1pdv93',
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
		protected static function _delete(array $record, array $params) {
			return WebrsaPermissions::checkD1D2( Hash::get( $record, 'Rendezvous.structurereferente_id' ) );
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
					'view',
					'delete',
				)
			);

			return $result;
		}

	}
?>