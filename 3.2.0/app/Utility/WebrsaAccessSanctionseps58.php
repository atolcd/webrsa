<?php
	/**
	 * Code source de la classe WebrsaAccessSanctionseps58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessSanctionseps58 ...
	 *
	 * @package app.Utility
	 */
	class WebrsaAccessSanctionseps58 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 * 
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Sanctionep58',
				'departement' => (int)Configure::read( 'Cg.departement' ),
				'ajoutPossible' => true
			);
		}

		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _nonrespectcer(array $record, array $params) {
			$params = self::params($params);
			
			$dureeTolerance = Configure::read('Sanctionep58.nonrespectcer.dureeTolerance');
			$enCours = strtotime(Hash::get($record, 'Contratinsertion.dd_ci')) <= time()
				&& strtotime(Hash::get($record, 'Contratinsertion.df_ci')) + ($dureeTolerance * 24 * 60 * 60) >= time()
			;

			return $enCours
				&& !Hash::get($params, 'haveSanctionep')
				&& !Hash::get($params, 'erreursCandidatePassage')
			;
		}
		
		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _deleteNonrespectcer(array $record, array $params) {
			return !Hash::get($record, 'Passagecommissionep.etatdossierep');
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
			$result = self::normalize_actions(array(
				'nonrespectcer' => array('haveSanctionep' => true, 'erreursCandidatePassage' => true),
				'deleteNonrespectcer'
			));
			
			if ($params['departement'] !== 58) {
				$result = array();
			}
			
			return $result;
		}
	}
?>