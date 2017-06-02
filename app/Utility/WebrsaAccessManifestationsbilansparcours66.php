<?php
	/**
	 * Code source de la classe WebrsaAccessManifestationsbilansparcours66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessManifestationsbilansparcours66 ...
	 *
	 * @package app.Utility
	 */
	class WebrsaAccessManifestationsbilansparcours66 extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 * 
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Manifestationbilanparcours66',
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
		protected static function _index(array $record, array $params) {
			return Hash::get($record, 'Bilanparcours66.positionbilan') !== 'annule'
				&& in_array(Hash::get($record, 'Bilanparcours66.proposition'), array('audition', 'auditionpe'))
				&& isset($record['Defautinsertionep66']['dateimpressionconvoc'])
				&& !empty($record['Defautinsertionep66']['dateimpressionconvoc'])
			;
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
				'index' => array(),
			));
			
			if ($params['departement'] !== 66) {
				$result = array();
			}
			
			return $result;
		}
	}
?>