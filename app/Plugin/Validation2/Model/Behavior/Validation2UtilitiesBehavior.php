<?php
	/**
	 * Code source de la classe Validation2UtilitiesBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package Validation2
	 * @subpackage Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ModelBehavior', 'Model' );

	/**
	 * Classe Validation2UtilitiesBehavior.
	 *
	 * @package Validation2
	 * @subpackage Model.Behavior
	 */
	class Validation2UtilitiesBehavior extends ModelBehavior
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
		 * Configuration du behavior.
		 *
		 * @param Model $Model Le modèle qui utilise ce behavior
		 * @param array $config La configuration à appliquer
		 */
		public function setup( Model $Model, $config = array() ) {
			parent::setup( $Model, $config );
			$this->settings[$Model->alias] = $this->defaultConfig + $config;
		}

		/**
		 * Retourne le nom de la clé de l'entrée de cache pour une méthode d'une
		 * classe, liée à un modèle CakePHP.
		 *
		 * @param Model $Model
		 * @param string $className
		 * @param string $methodName
		 * @param boolean $underscore
		 * @return string
		 */
		public function methodCacheKey( Model $Model, $className, $methodName, $underscore = false ) {
			$cacheKey = implode(
				'_',
				array(
					$Model->useDbConfig,
					$className,
					$methodName,
					$Model->alias
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
		 * @param Model $Model
		 * @param string|array $rule
		 * @return array
		 */
		public function normalizeValidationRule( Model $Model, $rule ) {
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

			$rule = Hash::merge( $defaults, $rule );

			return $rule;
		}

		/**
		 * NOT_BLANK_RULE_NAME => Champ obligatoire
		 *
		 * @param Model $Model
		 * @param mixed $rule
		 * @return string
		 */
		public function defaultValidationRuleMessage( Model $Model, $rule ) {
			$rule = $this->normalizeValidationRule( $Model, $rule );
			if( !isset( $rule['rule'][0] ) ) {
				return null;
			}

			//$message = "Validation2::{$rule['rule'][0]}";
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
				$domain = $this->settings[$Model->alias]['domain'];
			}

			return call_user_func_array( 'sprintf', Hash::merge( array( __d( $domain, $message ) ), $params ) );
		}

		/**
		 * Définition d'une règle de validation au champ du modèle .
		 *
		 * @param Model $Model Le modèle auquel le behavior est attaché
		 * @param string $field Le nom du champ
		 * @param mixed $rule La règle à définir
		 * @throws LogicException Lorsque le champ spécifié n'existe pas
		 */
		public function setValidationRule( Model $Model, $field, $rule ) {
			if( is_null( $Model->schema( $field) ) ) {
				throw new LogicException( "Field '{$field}' does not exst in model '{$Model->alias}'", 500 );
			}

			$rule = $this->normalizeValidationRule( $Model, $rule );
			$ruleName = $rule['rule'][0];

			$Model->validate = Hash::merge( (array)$Model->validate, array( $field => array( $ruleName => $rule ) ) );
		}

		/**
		 * Elimination d'une règle de validation pour le champ du modèle .
		 *
		 * @param Model $Model Le modèle auquel le behavior est attaché
		 * @param string $field Le nom du champ
		 * @param mixed $rule La règle à éliminer, qui sera normalisée au préalable
		 * @throws LogicException Lorsque le champ spécifié n'existe pas
		 */
		public function unsetValidationRule( Model $Model, $field, $rule ) {
			if( is_null( $Model->schema( $field) ) ) {
				throw new LogicException( "Field '{$field}' does not exst in model '{$Model->alias}'", 500 );
			}

			$rule = $this->normalizeValidationRule( $Model, $rule );
			$ruleName = $rule['rule'][0];

			unset( $Model->validate[$field][$ruleName] );
		}

		/**
		 * Vérification de l'existence d'une règle de validation pour le champ
		 * du modèle .
		 *
		 * @param Model $Model Le modèle auquel le behavior est attaché
		 * @param string $field Le nom du champ
		 * @param mixed $rule La règle à tester, qui sera normalisée au préalable
		 * @throws LogicException Lorsque le champ spécifié n'existe pas
		 */
		public function hasValidationRule( Model $Model, $field, $rule ) {
			if( is_null( $Model->schema( $field) ) ) {
				throw new LogicException( "Field '{$field}' does not exst in model '{$Model->alias}'", 500 );
			}

			$rule = $this->normalizeValidationRule( $Model, $rule );
			$ruleName = $rule['rule'][0];

			return isset( $Model->validate[$field][$ruleName] );
		}

		/**
		 * Before validate callback, translate validation messages
		 *
		 * @param Model $Model Model using this behavior
		 * @param array $options
		 * @return boolean True if validate operation should continue, false to abort
		 */
		public function beforeValidate( Model $Model, $options = array() ) {
			$success = parent::beforeValidate( $Model, $options );

			if( $this->settings[$Model->alias]['translate'] ) {
				if( is_array( $Model->validate ) && !empty( $Model->validate ) ) {
					foreach( $Model->validate as $field => $rules ) {
						foreach( $rules as $key => $rule ) {
							$rule = $this->normalizeValidationRule( $Model, $rule );
							if( !isset( $rule['message'] ) || empty( $rule['message'] ) ) {
								$rule['message'] = $this->defaultValidationRuleMessage( $Model, $rule );
								$Model->validate[$field][$key] = $rule;
							}
						}
					}
				}
			}

			return $success;
		}
	}
?>