<?php
	/**
	 * Source file for the AutovalidateBehavior class.
	 *
	 * PHP 5.3
	 *
	 * @package Validation
	 * @subpackage Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ValidateUtilitiesBehavior', 'Validation.Model/Behavior' );

	/**
	 * AutovalidateBehavior class.
	 *
	 * @package Validation
	 * @subpackage Model.Behavior
	 */
	class AutovalidateBehavior extends ValidateUtilitiesBehavior
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
				'notEmpty' => true,
				'maxLength' => true,
				'integer' => true,
				'numeric' => true,
				'date' => true,
				'isUnique' => true,
			),
			'domain' => 'default',
			'translate' => true
		);

		/**
		 * Not null -> notEmpty
		 *
		 * @param Model $model
		 * @param array $fieldParams
		 * @return boolean
		 */
		protected function _isNotEmptyField( Model $model, $fieldParams ) {
			return ( $this->settings[$model->alias]['rules']['notEmpty'] && Set::check( $fieldParams, 'null' ) && $fieldParams['null'] == false );
		}

		/**
		 * string -> maxLength
		 *
		 * @param Model $model
		 * @param array $fieldParams
		 * @return boolean
		 */
		protected function _isMaxLengthField( Model $model, $fieldParams ) {
			return ( $this->settings[$model->alias]['rules']['maxLength'] && ( $fieldParams['type'] == 'string' ) && Set::check( $fieldParams, 'length' ) && is_numeric( $fieldParams['length'] ) );
		}

		/**
		 * integer -> integer
		 *
		 * @see AutovalidateBehavior::integer
		 *
		 * @param Model $model
		 * @param array $fieldParams
		 * @return boolean
		 */
		protected function _isIntegerField( Model $model, $fieldParams ) {
			return ( $this->settings[$model->alias]['rules']['integer'] && $fieldParams['type'] == 'integer' );
		}

		/**
		 * float -> numeric
		 *
		 * @param Model $model
		 * @param array $fieldParams
		 * @return boolean
		 */
		protected function _isNumericField( Model $model, $fieldParams ) {
			return ( $this->settings[$model->alias]['rules']['numeric'] && $fieldParams['type'] == 'float' );
		}

		/**
		 * unique index -> isUnique
		 *
		 * @param Model $model
		 * @param string $field
		 * @param array $indexes
		 * @return boolean
		 */
		protected function _isUniqueField( Model $model, $field, $indexes ) {
			return ( $this->settings[$model->alias]['rules']['isUnique'] && in_array( $field, $indexes ) );
		}

		/**
		 * date -> date
		 *
		 * @param Model $model
		 * @param array $fieldParams
		 * @return boolean
		 */
		protected function _isDateField( Model $model, $fieldParams ) {
			return ( $this->settings[$model->alias]['rules']['date'] && $fieldParams['type'] == 'date' );
		}

		/**
		 * Déduction des règles de validation pour un champ d'un modèle donné.
		 *
		 * @param Model $model
		 * @param string $field
		 * @param array $params
		 * @param array $indexes
		 * @return array
		 */
		public function deduceFieldValidationRules( Model $model, $field, $params, $indexes = array() ) {
			$rules = array();

			if( $this->_isNotEmptyField( $model, $params ) && ( $field != $model->primaryKey ) ) {
				$rule = $this->normalizeValidationRule( $model, array( 'rule' => 'notEmpty', 'allowEmpty' => false ) );
				$rules[$rule['rule'][0]] = $rule;
			}

			if( $this->_isMaxLengthField( $model, $params ) ) {
				$rule = $this->normalizeValidationRule( $model, array( 'rule' => array( 'maxLength', $params['length'] ), 'allowEmpty' => true ) );
				$rules[$rule['rule'][0]] = $rule;
			}

			if( $this->_isUniqueField( $model, $field, $indexes ) ) {
				$rule = $this->normalizeValidationRule( $model, array( 'rule' => 'isUnique', 'allowEmpty' => true ) );
				$rules[$rule['rule'][0]] = $rule;
			}

			if( $this->_isIntegerField( $model, $params ) ) {
				$rule = $this->normalizeValidationRule( $model, array( 'rule' => 'integer', 'allowEmpty' => true ) );
				$rules[$rule['rule'][0]] = $rule;
			}

			if( $this->_isNumericField( $model, $params ) ) {
				$rule = $this->normalizeValidationRule( $model, array( 'rule' => 'numeric', 'allowEmpty' => true ) );
				$rules[$rule['rule'][0]] = $rule;
			}

			if( $this->_isDateField( $model, $params ) ) {
				$rule = $this->normalizeValidationRule( $model, array( 'rule' => 'date', 'allowEmpty' => true ) );
				$rules[$rule['rule'][0]] = $rule;
			}

			return $rules;
		}

		/**
		 * Retourne la liste des champs sur lesquels se trouve un index unique
		 * en base.
		 *
		 * @param Model $model
		 * @param boolean $cache
		 * @return array
		 */
		public function uniqueColumnIndexes( Model $model, $cache = true ) {
			if( $cache ) {
				$cacheKey = $this->methodCacheKey( $model, __CLASS__, __FUNCTION__ );
				$uniqueColumnIndexes = Cache::read( $cacheKey );
			}

			if( !$cache || $uniqueColumnIndexes === false ) {
				$uniqueColumnIndexes = array();

				$indexes = $model->getDataSource()->index( $model );
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
		 * @param Model $model
		 * @param boolean $cache
		 * @return array
		 */
		public function deduceValidationRules( Model $model, $cache = true ) {
			if( $cache ) {
				$cacheKey = $this->methodCacheKey( $model, __CLASS__, __FUNCTION__ );
				$validate = Cache::read( $cacheKey );
			}

			if( !$cache || $validate === false ) {
				$validate = array();
				$indexes = $this->uniqueColumnIndexes( $model );

				foreach( $model->schema() as $field => $params ) {
					$validate[$field] = $this->deduceFieldValidationRules(
						$model,
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
		 * @param Model $model
		 * @param boolean $cache
		 * @return void
		 */
		public function mergeDeducedValidationRules( Model $model, $cache = true ) {
			if( $cache ) {
				$cacheKey = $this->methodCacheKey( $model, __CLASS__, __FUNCTION__ );
				$validate = Cache::read( $cacheKey );
			}

			if( !$cache || $validate === false ) {
				$model->validate = Set::normalize( $model->validate );

				$model->validate = Set::merge(
					$model->validate,
					$this->deduceValidationRules( $model )
				);

				$validate = $model->validate;
				if( $cache ) {
					Cache::write( $cacheKey, $validate );
				}
			}

			$model->validate = $validate;
		}

		/**
		 * Setup this behavior with the specified configuration settings.
		 *
		 * @param Model $model Model using this behavior
		 * @param array $config Configuration settings for $model
		 * @return void
		 */
		public function setup( Model $model, $config = array() ) {
			parent::setup( $model, $config );
			$config = Set::merge( $this->defaultConfig, $config );

			$this->settings[$model->alias] = array_merge(
				(array)$this->settings[$model->alias],
				(array)Set::normalize( $config )
			);

			// INFO: on en a besoin avant d'utiliser les formulaires
			// pour les dates, pas pour les maxLength apparemment
			$this->mergeDeducedValidationRules( $model );
		}
	}
?>