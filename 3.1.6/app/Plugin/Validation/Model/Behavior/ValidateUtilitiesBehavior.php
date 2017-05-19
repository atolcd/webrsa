<?php
	/**
	 * Code source de la classe ValidateUtilitiesBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package Validation
	 * @subpackage Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe ValidateUtilitiesBehavior.
	 *
	 * @package Validation
	 * @subpackage Model.Behavior
	 */
	class ValidateUtilitiesBehavior extends ModelBehavior
	{
		/**
		 * Contains configuration settings for use with individual model objects.  This
		 * is used because if multiple models use this Behavior, each will use the same
		 * object instance.  Individual model settings should be stored as an
		 * associative array, keyed off of the model name.
		 *
		 * @var array
		 * @see Model::$alias
		 */
		public $settings = array();

		/**
		 * Configuration par défaut.
		 *
		 * @var array
		 */
		public $defaultConfig = array(
			'domain' => 'default',
			'translate' => true
		);

		/**
		 * Setup this behavior with the specified configuration settings.
		 *
		 * @param Model $model Model using this behavior
		 * @param array $config Configuration settings for $model
		 * @return void
		 */
		public function setup( Model $model, $config = array() ) {
			parent::setup( $model, $config );
			$this->settings[$model->alias] = $this->defaultConfig + $config;
		}

		/**
		 * Retourne le nom de la clé de l'entrée de cache pour une méthode d'une
		 * classe, liée à un modèle CakePHP.
		 *
		 * @param Model $model
		 * @param string $className
		 * @param string $methodName
		 * @param boolean $underscore
		 * @return string
		 */
		public function methodCacheKey( Model $model, $className, $methodName, $underscore = false ) {
			$cacheKey = implode(
				'_',
				array(
					$model->useDbConfig,
					$className,
					$methodName,
					$model->alias
				)
			);

			if( $underscore ) {
				$cacheKey = Inflector::underscore( $cacheKey );
			}

			return $cacheKey;
		}

		/**
		 * -> array( 'rule' => array( 'XXXXXXX'[, ...] ) )
		 *
		 * @param Model $model
		 * @param type $rule
		 * @return type
		 */
		public function normalizeValidationRule( Model $model, $rule ) {
			if( !is_array( $rule ) ) {
				$rule = array( 'rule' => array( $rule ) );
			}
            else if( !isset( $rule['rule'] ) && isset( $rule[0] ) ) {
                $rule = array( 'rule' => $rule );
            }
			else if( !is_array( $rule['rule'] ) ) {
				$rule['rule'] = (array)$rule['rule'];
			}

			$defaults = array(
				'rule' => null,
				'message' => null,
				'required' => null,
				'allowEmpty' => null,
				'on' => null
			);

			$rule = Set::merge( $defaults, $rule );

			return $rule;
		}

		/**
		 * 'notEmpty' => Champ obligatoire
		 *
		 * @param Model $model
		 * @param mixed $rule
		 * @return string
		 */
		public function defaultValidationRuleMessage( Model $model, $rule ) {
			$rule = $this->normalizeValidationRule( $model, $rule );
			if( !isset( $rule['rule'][0] ) ) {
				return null;
			}

			$message = "Validate::{$rule['rule'][0]}";

			$params = array();
			if( count( $rule['rule'] ) > 1 ) {
				$params = array_slice( $rule['rule'], 1 );

				if( is_array( $params[0] ) ) {
					$params = $params[0];
				}
			}

			if( strtolower( $rule['rule'][0] ) == 'inlist' ) {
				$params = implode( ', ', $params );
			}

			if( isset( $rule['domain'] ) ) {
				$domain = $rule['domain'];
			}
			else {
				$domain = $this->settings[$model->alias]['domain'];
			}

			return call_user_func_array( 'sprintf', Set::merge( array( __d( $domain, $message ) ),  $params ) );
		}

		/**
		 * Définition d'une règle de validation au champ du modèle .
		 *
		 * @param Model $model Le modèle auquel le behavior est attaché
		 * @param string $field Le nom du champ
		 * @param mixed $rule La règle à définir
		 * @throws LogicException Lorsque le champ spécifié n'existe pas
		 */
		public function setValidationRule( Model $model, $field, $rule ) {
			if( is_null( $model->schema( $field) ) ) {
				throw new LogicException( "Field '{$field}' does not exst in model '{$model->alias}'", 500 );
			}

			$rule = $this->normalizeValidationRule( $model, $rule );
			$ruleName = $rule['rule'][0];

			$model->validate = Set::merge( (array)$model->validate, array( $field => array( $ruleName => $rule ) ) );
		}

		/**
		 * Elimination d'une règle de validation pour le champ du modèle .
		 *
		 * @param Model $model Le modèle auquel le behavior est attaché
		 * @param string $field Le nom du champ
		 * @param mixed $rule La règle à éliminer, qui sera normalisée au préalable
		 * @throws LogicException Lorsque le champ spécifié n'existe pas
		 */
		public function unsetValidationRule( Model $model, $field, $rule ) {
			if( is_null( $model->schema( $field) ) ) {
				throw new LogicException( "Field '{$field}' does not exst in model '{$model->alias}'", 500 );
			}

			$rule = $this->normalizeValidationRule( $model, $rule );
			$ruleName = $rule['rule'][0];

			unset( $model->validate[$field][$ruleName] );
		}

		/**
		 * Vérification de l'existence d'une règle de validation pour le champ
		 * du modèle .
		 *
		 * @param Model $model Le modèle auquel le behavior est attaché
		 * @param string $field Le nom du champ
		 * @param mixed $rule La règle à tester, qui sera normalisée au préalable
		 * @throws LogicException Lorsque le champ spécifié n'existe pas
		 */
		public function hasValidationRule( Model $model, $field, $rule ) {
			if( is_null( $model->schema( $field) ) ) {
				throw new LogicException( "Field '{$field}' does not exst in model '{$model->alias}'", 500 );
			}

			$rule = $this->normalizeValidationRule( $model, $rule );
			$ruleName = $rule['rule'][0];

			return isset( $model->validate[$field][$ruleName] );
		}

		/**
		 * Before validate callback, translate validation messages
		 *
		 * @param Model $model Model using this behavior
		 * @return boolean True if validate operation should continue, false to abort
		 */
		public function beforeValidate( Model $model ) {
			$success = parent::beforeValidate( $model );

			if( $this->settings[$model->alias]['translate'] ) {
				if( is_array( $model->validate ) && !empty( $model->validate ) ) {
					foreach( $model->validate as $field => $rules ) {
						foreach( $rules as $key => $rule ) {
							$rule = $this->normalizeValidationRule( $model, $rule );
							if( !isset( $rule['message'] ) || empty( $rule['message'] ) ) {
								$rule['message'] = $this->defaultValidationRuleMessage( $model, $rule );
								$model->validate[$field][$key] = $rule;
							}
						}
					}
				}
			}

			return $success;
		}
	}
?>