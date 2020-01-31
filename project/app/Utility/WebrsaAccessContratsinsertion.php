<?php
	/**
	 * Code source de la classe WebrsaAccessContratinsertion.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessContratinsertion ...
	 *
	 * @package app.Utility
	 */
	abstract class WebrsaAccessContratsinsertion extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 * 
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Contratinsertion',
				'departement' => (int)Configure::read('Cg.departement')
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
			$params = self::params($params);
			
			switch ($params['departement']) {
				case 58:
				case 66:
					$access = Hash::get($params, 'ajoutPossible');
					break;
				default: 
					$access = true;
			}
			
			return $access;
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
			$params = self::params($params);
			
			switch ($params['departement']) {
				case 66: 
					$access = Hash::get($record, $params['alias'].'.positioncer') !== 'annule'
						&& !Hash::get($record, $params['alias'].'.datenotification')
						&& !Hash::get($record, 'Propodecisioncer66.isvalidcer');
					break;
				case 58: 
					$access = Hash::get($params, 'haveOrient')
						&& !Hash::get($params, 'haveOrientEmploi');
					break;
				default: 
					$access = true;
			}
			
			return $access;
		}

		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _impression(array $record, array $params) {
			$params = self::params($params);
			
			switch ($params['departement']) {
				case 66: 
					$access = Hash::get($record, $params['alias'].'.positioncer') !== 'annule';
					break;
				default:
					$access = true;
			}
			
			return $access;	
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
		protected static function _cancel(array $record, array $params) {
			$params = self::params($params);
			
			switch ($params['departement']) {
				case 66:
					$access = Hash::get($record, $params['alias'].'.positioncer') !== 'annule';
					break;
				default: 
					$access = true;
			}
			
			return $access;
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
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _ficheliaisoncer(array $record, array $params) {
			$params = self::params($params);
			
			return Hash::get($record, $params['alias'].'.positioncer') !== 'annule'
				&& Hash::get($record, 'Propodecisioncer66.isvalidcer')
			;
		}

		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _notifbenef(array $record, array $params) {
			$params = self::params($params);
			
			return Hash::get($record, $params['alias'].'.positioncer') !== 'annule'
				&& Hash::get($record, 'Propodecisioncer66.isvalidcer')
			;
		}

		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _notificationsop(array $record, array $params) {
			$params = self::params($params);
			
			return Hash::get($record, $params['alias'].'.positioncer') !== 'annule'
				&& Hash::get($record, 'Propodecisioncer66.isvalidcer')
				&& Hash::get($record, 'Propodecisioncer66.isvalidcer') !== 'N'
			;
		}

		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _notification(array $record, array $params) {
			$params = self::params($params);
			
			return Hash::get($record, $params['alias'].'.positioncer') !== 'annule'
				&& Hash::get($record, $params['alias'].'.decision_ci') !== 'E'
			;
		}

		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _reconduction_cer_plus_55_ans(array $record, array $params) {
			$params = self::params($params);
			$tabAllow = Configure::read( 'Contratinsertion.Reconduction.Allow' );
			$intDuree = Configure::read( 'Contratinsertion.Reconduction.Duree' );

			# L'affichage du lien se fait dans 2 cas :
			# --> Soit le CER n'est pas annulé et l'âge est d'au moins 55 ans
			# --> Soit il y a une fiche de candidature en cours, inférieure à 24 mois, éligible FSE, et que le CER soit "en cours" ou "fin de contrat"
			return ((
						Hash::get($record, $params['alias'].'.positioncer') !== 'annule' &&
						Hash::get($record, 'Personne.age') >= 55
					)
					||
					(
						$params['idFicheCandidature'] > 0 &&
						$params['dureeFicheCandidature'] <= $intDuree &&
						$params['eligibleFSE'] == 1 &&
						in_array(Hash::get($record, $params['alias'].'.positioncer'), $tabAllow)
					));
		}

		/**
		 * Permission d'accès
		 * 
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _valider(array $record, array $params) {
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
			$params = self::params($params);
			$result = self::normalize_actions(
				array(
					'add' => array('ajoutPossible' => true), 
					'view', 
					'edit', 
					'impression', 
					'filelink'
				)
			);
			
			switch ($params['departement']) {
				case 66:
					$result = self::merge_actions($result,
						array(
							'Proposdecisionscers66.propositionsimple',
							'Proposdecisionscers66.propositionparticulier',
							'ficheliaisoncer',
							'notifbenef',
							'notificationsop',
							'notification',
							'reconduction_cer_plus_55_ans',
							'cancel',
						)
					);
					break;
				case 58:
					$result = self::merge_actions($result,
						array(
							'edit' => array('haveOrient' => true, 'haveOrientEmploi' => true),
							'delete',
							'Sanctionseps58.nonrespectcer' => array('haveSanctionep' => true, 'erreursCandidatePassage' => true),
							'Proposcontratsinsertioncovs58.add',
							'Sanctionseps58.deleteNonrespectcer',
						)
					);
					break;
				case 976:
					$result = self::merge_actions($result,
						array(
							'add' => array('ajoutPossible' => false),
							'delete',
							'valider',
							'cancel',
						)
					);
					break;
			}
			
			return $result;
		}
	}
?>