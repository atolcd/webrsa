<?php
	/**
	 * Code source de la classe Autovalidate2Behavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Autovalidate2Behavior ...
	 *
	 * @package app.Model.Behavior
	 */
	class Autovalidate2Behavior extends ModelBehavior
	{
		public $settings = array();

		protected $_autoAddedPaths = array();

		/**
		*
		*/

		public function addValidationRule( Model $model, $field, $validate ) {
			if( !is_array( $validate ) ) {
				$validate = array( 'rule' => $validate );
				if( $validate['rule'] != 'notEmpty' ) {
					$validate['allowEmpty'] = true;
				}
			}

			if( !$this->hasValidationRule( $model, $field, $validate['rule'] ) ) {
				if( isset( $model->validate[$field] ) ) {
					$this->_autoAddedPaths[] = "{$field}.".count( $model->validate[$field] );
				}
				else {
					$this->_autoAddedPaths[] = "{$field}.0";
				}
				$model->validate[$field][] = $validate;
			}
		}

		/**
		*
		*/

		public function hasValidationRule( Model $model, $field, $rule ) {
			$validationRules = Hash::flatten( $model->validate );
			foreach( $validationRules as $path => $value ) {
				if( preg_match( "/^{$field}(\.|\.[0-9]+\.)rule/", $path ) && ( $value == $rule ) ) {
					return true;
				}
			}
			return false;
		}

		/**
		*
		*/

		public function removeValidationRule( Model $model, $field, $rule ) {
			$removed = 0;
			$validationRules = Hash::flatten( $model->validate );
			foreach( $validationRules as $path => $value ) {
				if( preg_match( "/^({$field}(\.|\.[0-9]+\.))rule/", $path, $matches ) && ( $value == $rule ) ) {
					$model->validate = Hash::remove( $model->validate, trim( $matches[1], '.' ) );
					// TODO: __backValidation ? / disableValidationRule ?
				}
			}
			return ( $removed > 0 );
		}

		/**
		* Setup this behavior with the specified configuration settings.
		*
		* @param object $model Model using this behavior
		* @param array $settings Configuration settings for $model
		* @access public
		*/

		public function setup( Model $model, $settings = array() ) {
			$cacheKey = Inflector::underscore( __CLASS__ ).'_'.Inflector::underscore( $model->alias );
			$validate = Cache::read( $cacheKey );

			if( $validate === false ) {
				$defaultSettings = array( // TODO
					'rules' => array(
						'notEmpty' => true,
						'maxLength' => true,
						'numeric' => true,
						'integer' => true,
						'numeric' => true,
	// 					'boolean' ?
	// 					'unsigned' ?

					),
					'domain' => 'default',
					'translate' => true
				);

				$settings = Set::merge( $defaultSettings, $settings );

				if (!isset($this->settings[$model->alias])) {
					$this->settings[$model->alias] = array();
				}

				$settings = Set::normalize( $settings );
				$this->settings[$model->alias] = array_merge(
					$this->settings[$model->alias],
					(array) $settings
				);

				$model->validate = Set::normalize( $model->validate );
				$schema = $model->schema();

				/// ----------------------------------------------------------------

				foreach( $schema as $field => $params ) {
					/// Prepare / cleanup
					if( Set::check( $model->validate, "{$field}.rule" ) ) {
						$model->validate[$field] = array( $model->validate[$field] );
					}

					/// Not null -> notEmpty
					// FIXME: seulement quand pas de default ?
					if( Set::check( $params, 'null' ) && $params['null'] == false && ( $field != $model->primaryKey ) ) {
						$this->addValidationRule( $model, $field, array( 'rule' => 'notEmpty' ) );
					}

					/// MaxLength
					if( ( $params['type'] == 'string' ) && Set::check( $params, 'length' ) && is_numeric( $params['length'] ) ) {
						$this->addValidationRule( $model, $field, array( 'rule' => array( 'maxLength', $params['length'] ), 'allowEmpty' => true ) );
					}

					/// Numeric
					if( $params['type'] == 'integer' ) {
						//$this->addValidationRule( $model, $field, array( 'rule' => 'numeric', 'allowEmpty' => true ) );
						$this->addValidationRule( $model, $field, array( 'rule' => 'integer', 'allowEmpty' => true ) );
					}

					/// Float <-> numeric ?
					if( $params['type'] == 'float' ) {
						$this->addValidationRule( $model, $field, array( 'rule' => 'numeric', 'allowEmpty' => true ) );
					}
				}
				$validate = $model->validate;
				Cache::write( $cacheKey, $validate );
			}
			$model->validate = $validate;
		}

		/**
		* Lorsque le behavior est détaché, on supprime les règles ajoutées.
		* FIXME: si les règles ont bougé, on va peut-être en supprimer d'autres
		*/

		public function cleanup( Model $model ) {
			if( !empty( $this->_autoAddedPaths ) ) {
				foreach( $this->_autoAddedPaths as $path ) {
					$model->validate = Hash::remove( $model->validate, $path );
				}
				$model->validate = Hash::filter( (array)$model->validate );
			}
		}

		/**
		* Before validate callback, translate validation messages
		*
		* @param object $model Model using this behavior
		* @return boolean True if validate operation should continue, false to abort
		* @access public
		*/
		public function beforeValidate( Model $model ) {
			if( Set::classicExtract( $this->settings, "{$model->alias}.translate" ) ) {
				$modelDomain = Set::classicExtract( $this->settings, "{$model->alias}.domain" );

				if( is_array( $model->validate ) ) {
					foreach( $model->validate as $field => $rules ) {
						foreach( $rules as $key => $rule ) {
							if( empty( $model->validate[$field][$key]['message'] ) ) {
								$validateRule = $model->validate[$field][$key]['rule'];
								if( is_array( $validateRule ) ) {
									$ruleName = $validateRule[0];
									$ruleParams = array_slice( $validateRule, 1 );
								}
								else {
									$ruleName = $validateRule;
									$ruleParams = array();
								}

								$model->validate[$field][$key]['message'] = "Validate::{$ruleName}";

								$ruleDomain = Set::classicExtract( $rule, 'domain' );
								if( !empty( $ruleDomain ) ) {
									$domain = $ruleDomain;
								}
								else if( !empty( $modelDomain ) ) {
									$domain = $modelDomain;
								}
								else {
									$domain = null;
								}

								// Transformation des paramètres array en chaînes de caractères
								foreach( $ruleParams as $index => $ruleParam ) {
									if( is_array( $ruleParam ) ) {
										$ruleParams[$index] = implode( ', ', $ruleParam );
									}
								}

								if( empty( $domain ) ) {
									$sprintfParams = Set::merge( array( __( $model->validate[$field][$key]['message'] ) ), $ruleParams );
								}
								else {
									$sprintfParams = Set::merge( array( __d( $domain, $model->validate[$field][$key]['message'] ) ), $ruleParams );
								}
								$model->validate[$field][$key]['message'] = call_user_func_array( 'sprintf', $sprintfParams );
							}
						}
					}
				}
			}

			return true;
		}
	}
?>