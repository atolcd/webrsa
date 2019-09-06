<?php
	/**
	 * Code source de la classe WebrsaAccessTitrescreanciers.
	 *
	 * PHP 7.2
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility', 'Creance', 'Titrecreancier');

	/**
	 * La classe WebrsaAccessTitrescreanciers ...
	 *
	 * @package app.Utility
	 */
	abstract class WebrsaAccessTitrescreanciers extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Titrescreanciers',
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
					'comment',
					'edit',
					'avis',
					'valider',
					'exportfica',
					'retourcompta',
					'delete',
					'filelink',
					'Titressuivis.index'
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
			if (
				$record['Creance']['etat'] == 'AEMETTRE'
				|| $record['Creance']['etat'] == 'ENEMISSION'
				|| $record['Creance']['etat'] == 'TITREEMIS'
			) {
				return true;
			}
			return false;
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
			if (
				empty($record['Titrecreancier']['etat']) &&
				$record['Creance']['etat'] == 'AEMETTRE'
			) {
				return Hash::get($params, 'ajoutPossible');
			}
			return false;
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
			if (
				$record['Titrecreancier']['etat'] == 'CREE'
				|| $record['Titrecreancier']['etat'] == 'ATTAVIS'
				|| $record['Titrecreancier']['etat'] == 'INSTRUCTION'
				|| $record['Titrecreancier']['etat'] == ''
			) {
				return true;
			}
			return false;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _comment(array $record, array $params) {
			return true;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _avis(array $record, array $params) {
			if (
				$record['Titrecreancier']['etat'] == 'CREE'
				|| $record['Titrecreancier']['etat'] == 'ATTAVIS'
				|| $record['Titrecreancier']['etat'] == 'INSTRUCTION'
				|| $record['Titrecreancier']['etat'] == 'VALIDAVIS'
			) {
				return true;
			}
			return false;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _valider(array $record, array $params) {
			if ($record['Titrecreancier']['etat'] == 'VALIDAVIS' ) {
				return true;
			}
			return false;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _exportfica(array $record, array $params) {
			if (
				$record['Titrecreancier']['etat'] == 'ATTRETOURCOMPTA'
				|| $record['Titrecreancier']['etat'] == 'ATTENVOICOMPTA'
			) {
				return true;
			}
			return false;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _retourcompta(array $record, array $params) {
			if (
				$record['Titrecreancier']['etat'] == 'ATTRETOURCOMPTA'
				|| $record['Titrecreancier']['etat'] == 'ATTENVOICOMPTA'
				|| $record['Titrecreancier']['etat'] == 'RED'
				|| $record['Titrecreancier']['etat'] == 'SUP'
			) {
				return true;
			}
			return false;
		}

		/**
		 * Permission d'accès
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _delete(array $record, array $params) {
			if (
				$record['Titrecreancier']['etat'] == 'ATTRETOURCOMPTA'
				|| $record['Titrecreancier']['etat'] == 'SUP'
				|| $record['Titrecreancier']['etat'] == 'TITREEMIS'
				|| $record['Titrecreancier']['etat'] == 'PAY'
				|| $record['Titrecreancier']['etat'] == 'RED'
			) {
				return false;
			}
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
	}
?>