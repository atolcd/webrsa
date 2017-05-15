<?php
	/**
	 * Code source de la classe ValidateTranslateBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ValidateTranslateBehavior ...
	 *
	 * @package app.Model.Behavior
	 */
	class ValidateTranslateBehavior extends ModelBehavior
	{
		/**
		 * Contains configuration settings for use with individual model objects.  This
		 * is used because if multiple models use this Behavior, each will use the same
		 * object instance.  Individual model settings should be stored as an
		 * associative array, keyed off of the model name.
		 *
		 * @var array
		 * @access public
		 * @see Model::$alias
		 */
		public $settings = array( );

		/**
		 * Setup this behavior with the specified configuration settings.
		 *
		 * @param object $model Model using this behavior
		 * @param array $settings Configuration settings for $model
		 * @access public
		 */
		public function setup( Model $model, $settings = array( ) ) {
			if( !isset( $this->settings[$model->alias] ) ) {
				$this->settings[$model->alias] = array( );
			}
			$this->settings[$model->alias] = array_merge( $this->settings[$model->alias], (array) $settings );
		}

		/**
		 * Before validate callback, translate validation messages
		 *
		 * @param object $model Model using this behavior
		 * @return boolean True if validate operation should continue, false to abort
		 * @access public
		 */
		public function beforeValidate( Model $model ) {
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
								$ruleParams = array( );
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

							if( empty( $domain ) ) {
								$sprintfParams = Set::merge( array( __( $model->validate[$field][$key]['message'] ) ), $ruleParams );
							}
							else {
								$sprintfParams = Set::merge( array( __d( $domain, $model->validate[$field][$key]['message'] ) ), $ruleParams );
							}

							// Si on a des array en parmètres, on transforme en chaîne de caractères
							foreach( $sprintfParams as $kspp => $spp ) {
								if( is_array( $spp ) ) {
									$sprintfParams[$kspp] = implode( ', ', $spp );
								}
							}

							$model->validate[$field][$key]['message'] = call_user_func_array( 'sprintf', $sprintfParams );
						}
					}
				}
			}

			return true;
		}
	}
?>