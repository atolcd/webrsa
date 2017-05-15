<?php
	/**
	 * Code source de la classe ExtraBasicValidationRulesBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package Validation
	 * @subpackage Model.Behavior
	 */

	/**
	 * La classe ExtraBasicValidationRulesBehavior ajoute des règles de
	 * validation basiques, liées aux types de colonnes d'une table.
	 *
	 * @package Validation
	 * @subpackage Model.Behavior
	 */
	class ExtraBasicValidationRulesBehavior extends ModelBehavior
	{
		/**
		 * Permet de s'assurer qu'une valeur soit un nombre entier.
		 *
		 * @param Model $Model
		 * @param mixed $check
		 * @return boolean
		 */
		public function integer( Model $Model, $check ) {
			if( !is_array( $check ) ) {
				return false;
			}

			$result = true;
			foreach( Set::normalize( $check ) as $value ) {
				$result = preg_match( '/^[0-9]+$/', $value ) && $result;
			}

			return $result;
		}

		/**
		 * Permet de s'assurer qu'une valeur soit un booléen.
		 *
		 * @param Model $Model
		 * @param mixed $check
		 * @return boolean
		 */
		public function boolean( Model $Model, $check ) {
			if( !is_array( $check ) ) {
				return false;
			}

			$result = true;
			foreach( Set::normalize( $check ) as $value ) {
				$result = (
						is_bool( $value )
						|| preg_match( '/^(0|1|true|false)$/i', $value )
				) && $result;
			}

			return $result;
		}

		/**
		 * Règle de validation équivalent à un index unique sur plusieurs colonnes.
		 *
		 * public $validate = array(
		 * 'name' => array(
		 * 		array(
		 * 			'rule' => array( 'checkUnique', array( 'name', 'modeletypecourrierpcg66_id' ) ),
		 * 			'message' => 'Cet intitulé de pièce est déjà utilisé avec ce modèle de courrier.'
		 * 		)
		 * 	),
		 * 	'modeletypecourrierpcg66_id' => array(
		 * 		array(
		 * 			'rule' => array( 'checkUnique', array( 'name', 'modeletypecourrierpcg66_id' ) ),
		 * 			'message' => 'Ce modèle de courrier est déjà utilisé avec cet intitulé de pièce.'
		 * 		),
		 * 	)
		 * );
		 *
		 * @param Model $Model
		 * @param array $check
		 * @param array $fields
		 * @return boolean
		 */
		public function checkUnique( Model $Model, $check, $fields ) {
			if( !is_array( $check ) ) {
				return false;
			}

			if( !is_array( $fields ) ) {
				$fields = array( $fields );
			}

			$availableFields = array_keys( $Model->data[$Model->alias] );

			$allFieldsInThisData = true;
			foreach( $fields as $field ) {
				if( !in_array( $field, $availableFields ) ) {
					$allFieldsInThisData = false;
				}
			}

			// A°) Tous les fields sont dans this->data
			if( $allFieldsInThisData ) {
				$querydata = array( 'conditions' => array(), 'recursive' => -1, 'contain' => false );
				foreach( $fields as $field ) {
					$querydata['conditions']["{$Model->alias}.{$field}"] = $Model->data[$Model->alias][$field];
				}

				// 1°) Pas l'id -> SELECT COUNT(*) FROM table WHERE name = XXX and modeletypecourrierpcg66_id = XXXX == 0
				// 2°) On a l'id
				if( isset( $Model->data[$Model->alias][$Model->primaryKey] ) && !empty( $Model->data[$Model->alias][$Model->primaryKey] ) ) {
					// SELECT COUNT(*) FROM table WHERE name = XXX and modeletypecourrierpcg66_id = XXXX AND id <> XXXX == 0
					$querydata['conditions']["{$Model->alias}.{$Model->primaryKey} <>"] = $Model->data[$Model->alias][$Model->primaryKey];
				}

				return ( $Model->find( 'count', $querydata ) == 0 );
			}

			// B°) On n'a pas tous les fields dans this->data ... TODO -> throw_error ou réfléchir ?
			return false;
		}
	}
?>