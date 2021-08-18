<?php
	/**
	 * Code source de la classe WebrsaAccessOrientstruct.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractAccess', 'Utility');

	/**
	 * La classe WebrsaAccessOrientstruct ...
	 *
	 * @package app.Utility
	 */
	class WebrsaAccessOrientsstructs extends WebrsaAbstractAccess
	{
		/**
		 * Paramètres par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		public static function params(array $params = array()) {
			return $params + array(
				'alias' => 'Orientstruct',
				'departement' => Configure::read('Cg.departement'),
				'ajout_possible' => null,
				'reorientationseps' => null,
				'isbenefinscritpe' => null,
				'listeOrientPro' => array()
			);
		}

		/**
		 * Action valider()
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _valider(array $record, array $params) {
			$Structureorientante = ClassRegistry::init('Structurereferente');
			return $record['Orientstruct']['statut_orient'] == 'En attente'
				&& in_array($record['Orientstruct']['origine'], Configure::read('Orientation.validation.listeorigine') )
				&& !empty($record['Orientstruct']['structureorientante_id'])
				&& $Structureorientante->isWorkflowActive($record['Orientstruct']['structureorientante_id']);
		}

		/**
		 * Action add()
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _add(array $record, array $params) {
			// Inaccessible pour le 93 s'il y a une réorientation en cours ou une orientation en attente de validation
			if ($params['departement'] == 93 && ( !empty($params['reorientationseps'])
				|| (!empty($record[0]['Orientstruct']['statut_orient']) && $record[0]['Orientstruct']['statut_orient'] == 'En attente' ) )
			) {
				return false;
			}
			return Hash::get($params, 'ajout_possible') == true;
		}

		/**
		 * Action addDirect allant sur add()
		 * Utilisé pour ajouter une orientation direct (CD58)
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _addDirect(array $record, array $params) {
			return Hash::get($params, 'ajout_possible') == true;
		}

		/**
		 * On ne peut modifier que l'entrée la plus récente.
		 * Au CG 66, on ne peut modifier que la dernière orientation de statut
		 * "Orienté" (celle dont le rang est le plus élevé);
		 *
		 * Champs virtuels: dernier, dernier_oriente
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _edit(array $record, array $params) {
			$result = (
					Hash::get($record, "{$params['alias']}.dernier") == true
					|| (
						976 == $params['departement']
						&& 'En attente' === Hash::get( $record, "{$params['alias']}.statut_orient" )
					)
				)
				&& Hash::get($params, 'ajout_possible') == true;

			if ($params['departement'] == 66) {
				// Délai de modification orientation (10 jours par défaut)
				$date_valid = Hash::get($record, "{$params['alias']}.date_valid");
				$nbheure = Configure::read('Periode.modifiableorientation.nbheure');
				$periodeblock = !empty($date_valid)
					&& (time() >= (strtotime($date_valid) + 3600 * $nbheure));

				$result = $result
					&& $periodeblock == false
					&& Hash::get($record, "{$params['alias']}.dernier_oriente") == true;
			} elseif( Configure::read('Orientation.validation.enabled') == true ) {
				$result = $result
					&& (
						( Hash::get($record, "{$params['alias']}.statut_orient") == 'En attente'
						&& Hash::get($record, "{$params['alias']}.origine") == null)
						|| Hash::get($record, "{$params['alias']}.statut_orient") != 'En attente'
					);
			}

			return $result;
		}

		/**
		 * On ne peut imprimer que certaines orientations (dans la table PDF pour
		 * les départements qui stockent).
		 *
		 * Champs virtuels: printable
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _impression(array $record, array $params) {
			return Hash::get($record, "{$params['alias']}.printable") == 1;
		}

		/**
		 *
		 * Champs virtuels: dernier, dernier_oriente, linked_records
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _delete(array $record, array $params) {
			$reorientationseps = Hash::get($params, 'reorientationseps');

			return (
					(
						Hash::get($record, "{$params['alias']}.dernier") == true
						&& Hash::get($record, "{$params['alias']}.dernier_oriente") == true
					)
					|| (
						976 == $params['departement']
						&& 'En attente' === Hash::get( $record, "{$params['alias']}.statut_orient" )
					)
				)
				&& Hash::get($record, "{$params['alias']}.linked_records") == false
				&& empty($reorientationseps);
		}

		/**
		 *
		 * Champs virtuels: dernier, dernier_oriente, linked_records
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _nonrespectppae(array $record, array $params) {
			$isbenefinscritpe = Hash::get($params, 'isbenefinscritpe');
			$listeOrientPro = Hash::get($params, 'listeOrientPro');
			return (
					Hash::get($record, "{$params['alias']}.dernier") == true
					&& Hash::get($record, "{$params['alias']}.dernier_oriente") == true
					&& $isbenefinscritpe == true
					&& in_array($record['Typeorient']['id'], $listeOrientPro)
				);
		}

		/**
		 * Peut-on imprimer la notif de changement de référent ou non ?
		 * Si 1ère orientation non sinon ok.
		 *
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		protected static function _impression_changement_referent(array $record, array $params) {
			return Hash::get($record, "{$params['alias']}.premier_oriente") == true
				&& Hash::get($record, "{$params['alias']}.notifbenefcliquable") == true;
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
		protected static function _deleteNonrespectppae(array $record, array $params) {
			return true;
		}

		/**
		 * Liste les actions disponnible
		 *
		 * @param array $params
		 * @return array
		 */
		public static function actions(array $params = array()) {
			$params = self::params($params);
			$result = self::normalize_actions(
				array(
					'add' => array('ajout_possible' => true),
					'addDirect' => array('ajout_possible' => true),
					'edit' => array('ajout_possible' => true),
					'impression',
					'delete' => array('reorientationseps' => true),
					'filelink'
				)
			);

			if( Configure::read('Orientation.validation.enabled') == true ) {
				$result = self::merge_actions($result, array('valider'));
			}

			if ($params['departement'] == 66) {
				$result = self::merge_actions($result, array('impression_changement_referent'));
			}

			if( Configure::read('Commissionseps.sanctionep.nonrespectppae') == true ) {
				$result = self::merge_actions($result, array('nonrespectppae'));
				$result = self::merge_actions($result, array('deleteNonrespectppae'));
			}

			return $result;
		}
	}
?>