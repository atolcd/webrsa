<?php
	/**
	 * Code source de la classe Validation2RulesComparisonBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package Validation2
	 * @subpackage Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Validation2RulesComparisonBehavior fournit des méthodes de
	 * validation contenant un lien entre les valeurs de plusieurs champs.
	 *
	 * @package Validation2
	 * @subpackage Model.Behavior
	 */
	class Validation2RulesComparisonBehavior extends ModelBehavior
	{
		/**
		 * Vérifie que les paramètres sont bons.
		 *
		 * @param mixed $checks
		 * @param mixed $references
		 * @return boolean
		 */
		protected function _checkParams( $checks, $references ) {
			return ( is_array( $checks ) && !empty( $references ) );
		}

		/**
		 * Vérifie que la valeur de l'ensemble des champs soit vide (au sens PHP).
		 *
		 * @see ExtraValidation2RulesBehavior::allEmpty()
		 *
		 * @param Model $Model
		 * @param mixed $checks
		 * @param string|array $references
		 * @return boolean
		 */
		public function allEmpty( Model $Model, $checks, $references ) {
			if( !$this->_checkParams( $checks, $references ) ) {
				return false;
			}

			$success = true;
			$references = (array)$references;

			foreach( $references as $reference ) {
				$value = Hash::get( $Model->data, "{$Model->alias}.{$reference}" );
				$success = $success && empty( $value );
			}

			if( !empty( $checks ) ) {
				foreach( $checks as $value ) {
					$success = $success && empty( $value );
				}
			}

			return $success;
		}

		/**
		 * Exemple: 'dateentreeemploi' => notEmptyIf( $check, 'activitebeneficiaire', true, array( 'P' ) )
		 *
		 * @see ExtraValidation2RulesBehavior::notEmptyIf()
		 *
		 * @param Model $Model
		 * @param array $checks
		 * @param string $reference
		 * @param boolean $condition
		 * @param array $values
		 * @return boolean
		 */
		public function notEmptyIf( Model $Model, $checks, $reference, $condition, $values ) {
			if( !$this->_checkParams( $checks, $reference ) ) {
				return false;
			}

			$success = true;
			$referenceValue = Hash::get( $Model->data, "{$Model->alias}.{$reference}" );

			if( !empty( $checks ) ) {
				foreach( $checks as $value ) {
					if ( in_array( $referenceValue, $values, true ) === $condition ) {
						$success = $success && !empty( $value );
					}
				}
			}

			return $success;
		}
		
		/**
		 * Vérifi qu'un champ est à vide si le champ $reference ($condition ? '===' : '!==') $values
		 * 
		 * @param Model $Model
		 * @param array $checks
		 * @param string $reference
		 * @param boolean $condition
		 * @param array $values
		 * @return boolean
		 */
		public function emptyIf(Model $Model, $checks, $reference, $condition, $values) {
			if (!$this->_checkParams($checks, $reference)) {
				return false;
			}
			
			$check = in_array(Hash::get($Model->data, "{$Model->alias}.{$reference}"), (array)$values);
			
			if (empty($checks) || $check !== $condition) {
				return true;
			}
			
			foreach ($checks as $value) {
				if (!empty($value)) {
					return false;
				}
			}
			return true;
		}

		/**
		 * Exemple: 'dateentreeemploi' => notNullIf( $check, 'activitebeneficiaire', true, array( 'P' ) )
		 *
		 * @see ExtraValidation2RulesBehavior::notNullIf()
		 *
		 * @param Model $Model
		 * @param array $checks
		 * @param string $reference
		 * @param boolean $condition
		 * @param array $values
		 * @return boolean
		 */
		public function notNullIf( Model $Model, $checks, $reference, $condition, $values ) {
			if( !$this->_checkParams( $checks, $reference ) ) {
				return false;
			}

			$success = true;
			$referenceValue = Hash::get( $Model->data, "{$Model->alias}.{$reference}" );

			if( !empty( $checks ) ) {
				foreach( $checks as $value ) {
					if ( in_array( $referenceValue, $values, true ) === $condition ) {
						$success = $success && !is_null( $value );
					}
				}
			}

			return $success;
		}

		/**
		 * Vérifie que la valeur soit supérieure à la valeur de référence si
		 * celle-ci est supérieure à zéro.
		 *
		 * @param Model $Model
		 * @param array $check
		 * @param string $reference
		 * @return boolean
		 */
		public function greaterThanIfNotZero( Model $Model, $check, $reference ) {
			if( !$this->_checkParams( $check, $reference ) ) {
				return false;
			}

			$check = array_values( $check );
			$check_value = Hash::get( $check, '0' );

			$reference_value = Hash::get( $Model->data, "{$Model->alias}.{$reference}" );

			return ( ( $check_value > 0 ) && !( $check_value < $reference_value ) );
		}

		/**
		 * Compare la date en valeur par-rapport à la date de référence, suivant
		 * le comparateur.
		 *
		 * @param Model $Model
		 * @param array $check
		 * @param string $reference
		 * @param string $comparator Une valeur parmi: >, <, ==, <=, >=
		 * @return boolean
		 */
		public function compareDates( Model $Model, $check, $reference, $comparator ) {
			if( !$this->_checkParams( $check, $reference ) ) {
				return false;
			}

			$check_value = strtotime( Hash::get( array_values( $check ), '0' ) );
			$reference_value = strtotime( Hash::get( $Model->data, "{$Model->alias}.{$reference}" ) );

			if( empty( $reference_value ) || empty( $check_value ) ) {
				return true;
			}

			$seconds = $reference_value - $check_value;
			$days = floor( $seconds / 3600 / 24 );

			if( $days > 0 ) {
				return in_array( $comparator, array( '<', '<=' ), true );
			}
			else if( $days < 0 ) {
				return in_array( $comparator, array( '>', '>=' ), true );
			}

			return in_array( $comparator, array( '==', '<=', '>=' ), true );
		}

		/**
		 * Validates that the value of the field and the values of the other
		 * fields are either all null or all not null.
		 *
		 * TODO: renommer en NullPaired et simplifier ?
		 *
		 * @see allEmpty, notEmptyIf, notNullIf
		 *
		 * @param Model $Model
		 * @param array $check
		 * @param string|array $otherFields
		 * @return boolean
		 */
		public function foo( Model $Model, $check, $otherFields ) {
			if( !is_array( $check ) || empty( $otherFields ) ) {
				return false;
			}

			$success = true;

			foreach( $check as $value ) {
				foreach( (array)$otherFields as $otherField ) {
					$nullValue = is_null( $value );
					$nullOtherValue = is_null( $Model->data[$Model->alias][$otherField] );

					$success = ( ( ( $nullValue && $nullOtherValue ) || ( !$nullValue && !$nullOtherValue ) ) ) && $success;
				}
			}

			return $success;
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
			if( !is_array( $check ) || empty( $fields ) ) {
				return false;
			}

			$fields = (array)$fields;
			$available = array_keys( $Model->data[$Model->alias] );

			// A°) On n'a pas tous les champs dans this->data
			if( array_intersect( $fields, $available ) !== $fields ) {
				// TODO -> throw_error ou réfléchir ?
				return false;
			}

			// B°) Tous les champs sont dans this->data
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

			$found = $Model->find( 'first', $querydata );
			return empty( $found );
		}

		/**
		 * Vérifie que la durée en mois contenue dans le champ soit bien cohérente
		 * avec la durée définie par les dates de début et de fin, bornes comprises,
		 * avec arrondi.
		 *
		 * @param Model $Model
		 * @param array $checks
		 * @param string $dateDebut Le champ représentant la date de début
		 * @param string $dateFin Le champ représentant la date de fin
		 * @return boolean
		 */
		public function checkDureeDates( Model $Model, $checks, $dateDebut, $dateFin ) {
			if( !is_array( $checks ) ) {
				return false;
			}

			$success = true;

			$dd = Hash::get( $Model->data, "{$Model->alias}.{$dateDebut}" );
			$df = Hash::get( $Model->data, "{$Model->alias}.{$dateFin}" );

			$expected = round( ( strtotime( $df ) - strtotime( $dd ) ) / ( 60 * 60 * 24 * 30 ) );

			if( !empty( $checks ) ) {
				foreach( $checks as $value ) {
					$success = ( $expected == $value ) && $success;
				}
			}

			return $success;
		}
	}
?>