<?php
	/**
	 * Source file for the Validation2AutovalidateBehavior class.
	 *
	 * PHP 5.3
	 *
	 * @package Validation
	 * @subpackage Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once dirname( __FILE__ ).DS.'..'.DS.'..'.DS.'Lib'.DS.'basics.php';
	App::uses( 'Validation2UtilitiesBehavior', 'Validation2.Model/Behavior' );

	/**
	 * Validation2AutovalidateBehavior class.
	 *
	 * @package Validation
	 * @subpackage Model.Behavior
	 */
	class Validation2AutovalidateBehavior extends Validation2UtilitiesBehavior
	{
		/**
		 * Configuration.
		 *
		 * @var array
		 */
		public $settings = array();

		/**
		 * Configuration par défaut.
		 *
		 * @var array
		 */
		public $defaultConfig = array(
			'rules' => array(
				NOT_BLANK_RULE_NAME => true,
				'maxLength' => true,
				'integer' => true,
				'numeric' => true,
				'date' => true,
				'datetime' => true,
				'time' => true,
				'isUnique' => true,
			),
			'domain' => 'default',
			'translate' => true
		);

		/**
		 * Not null -> notEmpty
		 *
		 * @param Model $Model
		 * @param array $fieldParams
		 * @return boolean
		 */
		protected function _isNotEmptyField( Model $Model, $fieldParams ) {
			return ( $this->settings[$Model->alias]['rules'][NOT_BLANK_RULE_NAME] && Hash::check( $fieldParams, 'null' ) && $fieldParams['null'] == false );
		}

		/**
		 * string -> maxLength
		 *
		 * @param Model $Model
		 * @param array $fieldParams
		 * @return boolean
		 */
		protected function _isMaxLengthField( Model $Model, $fieldParams ) {
			return ( $this->settings[$Model->alias]['rules']['maxLength'] && ( $fieldParams['type'] == 'string' ) && Hash::check( $fieldParams, 'length' ) && is_numeric( $fieldParams['length'] ) );
		}

		/**
		 * unique index -> isUnique
		 *
		 * @param Model $Model
		 * @param string $field
		 * @param array $indexes
		 * @return boolean
		 */
		protected function _isUniqueField( Model $Model, $field, $indexes ) {
			return ( $this->settings[$Model->alias]['rules']['isUnique'] && in_array( $field, $indexes ) );
		}

		/**
		 * integer -> integer
		 * date -> date
		 * time -> time
		 * datetime -> datetime
		 *
		 * @param Model $Model
		 * @param string $type
		 * @param array $fieldParams
		 * @return boolean
		 */
		protected function _isTypeField( Model $Model, $type, $fieldParams ) {
			return ( $this->settings[$Model->alias]['rules'][$type] && $fieldParams['type'] == $type );
		}

		/**
		 * float -> numeric
		 *
		 * @param Model $Model
		 * @param array $fieldParams
		 * @return boolean
		 */
		protected function _isNumericField( Model $Model, $fieldParams ) {
			return ( $this->settings[$Model->alias]['rules']['numeric'] && $fieldParams['type'] == 'float' );
		}

		/**
		 * Déduction des règles de validation pour un champ d'un modèle donné.
		 *
		 * @param Model $Model
		 * @param string $field
		 * @param array $params
		 * @param array $indexes
		 * @return array
		 */
		public function deduceFieldValidationRules( Model $Model, $field, $params, $indexes = array() ) {
			$rules = array();

			if( $this->_isNotEmptyField( $Model, $params ) && ( $field != $Model->primaryKey ) ) {
				$rule = $this->normalizeValidationRule( $Model, array( 'rule' => NOT_BLANK_RULE_NAME, 'allowEmpty' => false ) );
				$rules[$rule['rule'][0]] = $rule;
			}

			if( $this->_isMaxLengthField( $Model, $params ) ) {
				$rule = $this->normalizeValidationRule( $Model, array( 'rule' => array( 'maxLength', $params['length'] ), 'allowEmpty' => true ) );
				$rules[$rule['rule'][0]] = $rule;
			}

			if( $this->_isUniqueField( $Model, $field, $indexes ) ) {
				$rule = $this->normalizeValidationRule( $Model, array( 'rule' => 'isUnique', 'allowEmpty' => true ) );
				$rules[$rule['rule'][0]] = $rule;
			}

			// Par type de champ
			if( $this->_isNumericField( $Model, $params ) ) {
				$rule = $this->normalizeValidationRule( $Model, array( 'rule' => 'numeric', 'allowEmpty' => true ) );
				$rules[$rule['rule'][0]] = $rule;
			}
			else if( in_array( $params['type'], array( 'integer', 'date', 'datetime', 'time' ) ) && $this->_isTypeField( $Model, $params['type'], $params ) ) {
				$rule = $this->normalizeValidationRule( $Model, array( 'rule' => $params['type'], 'allowEmpty' => true ) );
				$rules[$rule['rule'][0]] = $rule;
			}

			return $rules;
		}

		/**
		 * Retourne la liste des champs sur lesquels se trouve un index unique
		 * en base.
		 *
		 * @param Model $Model
		 * @param boolean $cache
		 * @return array
		 */
		public function uniqueColumnIndexes( Model $Model, $cache = true ) {
			if( $cache ) {
				$cacheKey = cacheKey( array( $Model->useDbConfig, __CLASS__, $Model->alias, __FUNCTION__ ) );
				$uniqueColumnIndexes = Cache::read( $cacheKey );
			}

			if( !$cache || $uniqueColumnIndexes === false ) {
				$uniqueColumnIndexes = array();

				$indexes = $Model->getDataSource()->index( $Model );
				foreach( $indexes as $name => $index ) {
					if( $index['unique'] && ( $name != 'PRIMARY' ) && count( (array)$index['column'] ) == 1 ) {
						$uniqueColumnIndexes[] = $index['column'];
					}
				}

				if( $cache ) {
					Cache::write( $cacheKey, $uniqueColumnIndexes );
				}
			}

			return $uniqueColumnIndexes;
		}

		/**
		 * Liste des règles de validation déduites d'un modèle.
		 *
		 * @param Model $Model
		 * @param boolean $cache
		 * @return array
		 */
		public function deduceValidationRules( Model $Model, $cache = true ) {
			if( $cache ) {
				$cacheKey = cacheKey( array( $Model->useDbConfig, __CLASS__, $Model->alias, __FUNCTION__ ) );
				$validate = Cache::read( $cacheKey );
			}

			if( !$cache || $validate === false ) {
				$validate = array();
				$indexes = $this->uniqueColumnIndexes( $Model );

				foreach( $Model->schema() as $field => $params ) {
					$validate[$field] = $this->deduceFieldValidationRules(
						$Model,
						$field,
						$params,
						$indexes
					);
				}

				if( $cache ) {
					Cache::write( $cacheKey, $validate );
				}
			}

			return $validate;
		}

		/**
		 * Regroupement des règles de validation présentes dans le modèle et des
		 * règles de validation déduites.
		 *
		 * @param Model $Model
		 * @param boolean $cache
		 * @return void
		 */
		public function mergeDeducedValidationRules( Model $Model, $cache = true ) {
			if( $cache ) {
				$cacheKey = cacheKey( array( $Model->useDbConfig, __CLASS__, $Model->alias, __FUNCTION__ ) );
				$validate = Cache::read( $cacheKey );
			}

			if( !$cache || $validate === false ) {
				$Model->validate = Hash::normalize( $Model->validate );

				$Model->validate = Hash::merge(
					$Model->validate,
					$this->deduceValidationRules( $Model )
				);

				$validate = $Model->validate;
				if( $cache ) {
					Cache::write( $cacheKey, $validate );
				}
			}

			$Model->validate = $validate;
		}

		/**
		 * Configuration du behavior.
		 *
		 * @param Model $Model Le modèle qui utilise ce behavior
		 * @param array $config La configuration à appliquer
		 */
		public function setup( Model $Model, $config = array() ) {
			parent::setup( $Model, $config );
			$config = Hash::merge( $this->defaultConfig, $config );

			$this->settings[$Model->alias] = array_merge(
				(array)$this->settings[$Model->alias],
				(array)Hash::normalize( $config )
			);

			// INFO: on en a besoin avant d'utiliser les formulaires
			// pour les dates, pas pour les maxLength apparemment
			$this->mergeDeducedValidationRules( $Model );
		}
	}
?>