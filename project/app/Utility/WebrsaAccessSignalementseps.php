<?php
	/**
	 * Code source de la classe WebrsaAccessSignalementseps.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'CakeSession', 'Model/Datasource' );
	App::uses( 'WebrsaAbstractAccess', 'Utility' );

	/**
	 * La classe WebrsaAccessSignalementseps ...
	 *
	 * @package app.Utility
	 */
	abstract class WebrsaAccessSignalementseps extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params( array $params = array() ) {
			return $params + array(
				'alias' => 'Signalementep93',
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

			$decision_ci = Hash::get( $record, 'Contratinsertion.decision_ci' );
			$dd_ci = Hash::get( $record, 'Contratinsertion.dd_ci' );
			$df_ci = Hash::get( $record, 'Contratinsertion.df_ci' );
			$dossierep_cer = Hash::get( $record, 'Dossierep.encours_cer' );
			$dossierep_possible = Hash::get( $record, 'Dossierep.possible' );

			return
				( empty( $record ) && Hash::get( $params, 'ajoutPossible' ) )
				|| (
				// Contrat validé
				( 'V' == $decision_ci )
				// En cours, avec une durée de tolérance
				&& (
					( time() >= strtotime( $dd_ci ) )
					&& ( time() <= ( strtotime( $df_ci ) + ( Configure::read( 'Signalementep93.dureeTolerance' ) * 24 * 60 * 60 ) ) )
				)
				// Aucun contrat de la personne n'est en cours de passage en EP actuellement
				&& ( false == $dossierep_cer )
				// La personne peut passer en EP
				&& ( true == $dossierep_possible )
			);
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
			$etatdossierep = Hash::get( $record, 'Passagecommissionep.etatdossierep' );
			return ( null === $etatdossierep );
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
			$etatdossierep = Hash::get( $record, 'Passagecommissionep.etatdossierep' );
			return ( null === $etatdossierep );
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
					'delete',
					'edit',
				)
			);

			return $result;
		}
	}
?>