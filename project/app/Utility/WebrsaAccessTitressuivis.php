<?php
	/**
	 * Code source de la classe WebrsaAccessTitressuivis.
	 *
	 * PHP 7.2
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility', 'Creance', 'Titrecreancier');

	/**
	 * La classe WebrsaAccessTitressuivis ...
	 *
	 * @package app.Utility
	 */
	abstract class WebrsaAccessTitressuivis extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Titressuivis',
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
					'index'
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
		protected static function _index(array $record, array $params) {
			if( $record['Titrecreancier']['etat'] == 'ATTRETOURCOMPTA' ||
				$record['Titrecreancier']['etat'] == 'TITREEMIS' ||
				$record['Titrecreancier']['etat'] == 'PAY' ||
				$record['Titrecreancier']['etat'] == 'SUP' ||
				$record['Titrecreancier']['etat'] == 'RED') {
					return true;
				}
			return false;
		}
	}
?>