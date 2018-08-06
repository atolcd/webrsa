<?php
	/**
	 * Code source de la classe WebrsaAccessQuestionnairesd2pdvs93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractAccess', 'Utility' );
	App::uses( 'WebrsaPermissions', 'Utility' );

	/**
	 * La classe WebrsaAccessQuestionnairesd2pdvs93 ...
	 *
	 * @package app.Utility
	 */
	abstract class WebrsaAccessQuestionnairesd2pdvs93 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Questionnaired2pdv93',
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
		 * @todo
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _edit( array $record, array $params ) {
			$params = self::params( $params );
			$return = (
				WebrsaPermissions::checkD1D2( Hash::get( $record, 'Questionnaired1pdv93.Rendezvous.structurereferente_id' ) )
				|| WebrsaPermissions::checkD1D2( Hash::get( $record, 'Rendezvous.structurereferente_id' ) )
			);
			return  $return ;
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
			$return = (
				WebrsaPermissions::checkD1D2( Hash::get( $record, 'Questionnaired1pdv93.Rendezvous.structurereferente_id' ) )
				|| WebrsaPermissions::checkD1D2( Hash::get( $record, 'Rendezvous.structurereferente_id' ) )
			);
			return $return ;
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
					'edit',
					'delete',
				)
			);

			return $result;
		}

	}
?>