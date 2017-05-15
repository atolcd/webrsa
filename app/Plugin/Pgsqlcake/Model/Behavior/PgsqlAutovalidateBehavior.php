<?php
	/**
	 * Code source de la classe PgsqlAutovalidateBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package Pgsqlcake
	 * @subpackage Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AutovalidateBehavior', 'Validation.Model/Behavior' );

	/**
	 * Classe PgsqlAutovalidateBehavior.
	 *
	 * @package Pgsqlcake
	 * @subpackage Model.Behavior
	 */
	class PgsqlAutovalidateBehavior extends AutovalidateBehavior
	{
		/**
		 *
		 * @var array
		 */
		protected $_checkRules = array();

		/**
		 *
		 * @param Model $model
		 */
		protected function _readTableConstraints( Model $model ) {
			$cacheKey = $this->methodCacheKey( $model, __CLASS__, __FUNCTION__ );
			$this->_checkRules[$model->alias] = Cache::read( $cacheKey );

			if( $this->_checkRules[$model->alias] === false ) {
				$ds = $model->getDataSource();

				$sql = "SELECT
								istc.table_catalog,
								istc.table_schema,
								istc.table_name,
								istc.constraint_name,
								iscc.check_clause
							FROM information_schema.check_constraints AS iscc
								INNER JOIN information_schema.table_constraints AS istc ON (
									istc.constraint_name = iscc.constraint_name
								)
							WHERE
								istc.table_catalog = '{$ds->config['database']}'
								AND istc.table_schema = '{$ds->config['schema']}'
								AND istc.table_name = '".$ds->fullTableName( $model, false, false )."'
								AND istc.constraint_type = 'CHECK'
								AND iscc.check_clause ~ 'cakephp_validate_.*(.*)';";

				$checks = $model->query( $sql );

				$this->_checkRules[$model->alias] = array();
				foreach( $checks as $i => $check ) {
					$this->_checkRules[$model->alias] = $this->_addGuessedPgsqlConstraint( $model, $this->_checkRules[$model->alias], $check[0]['check_clause'] );
				}

				Cache::write( $cacheKey, $this->_checkRules[$model->alias] );
			}
		}

		/**
		 *
		 * @param Model $model
		 * @param type $config
		 */
		public function setup( Model $model, $config = array() ) {
			if( $model->getDataSource() instanceof Postgres ) {
				$this->defaultConfig['rules']['pgsql_constraints'] = true;
			}

			$this->_readTableConstraints( $model );

			parent::setup( $model, $config );
		}

		/**
		 *
		 * @param Model $model
		 * @param type $field
		 * @param type $params
		 * @param type $indexes
		 * @return type
		 */
		public function deduceFieldValidationRules( Model $model, $field, $params, $indexes = array() ) {
			$rules = parent::deduceFieldValidationRules( $model, $field, $params, $indexes );

			if( $this->settings[$model->alias]['rules']['pgsql_constraints'] ) {
				if( isset( $this->_checkRules[$model->alias][$field] ) && !empty( $this->_checkRules[$model->alias][$field] ) ) {
					foreach( $this->_checkRules[$model->alias][$field] as $rule ) {
						$rules[$rule['rule'][0]] = $rule;
					}
				}
			}

			return $rules;
		}

		/**
		 *
		 * @param type $parameters
		 * @return null
		 */
		protected function _extractPgsqlParams( $parameters ) {
			$params = array();

			if( isset( $parameters['params'] ) ) {
				if( preg_match( '/^ARRAY\[(.*)\]$/', $parameters['params'], $matches ) ) {
//					if( preg_match_all( '/\'([^\']+)\'/U', $matches[1], $values ) ) {
//						$params = array( $values[1] );
//					}
//					else
					if( preg_match_all( '/([^, ]+)/', $matches[1], $values ) ) {
						$params = array( $values[1] );
					}
				}
				else if( preg_match_all( '/([^, ]+),{0,1}/', $parameters['params'], $matches ) ) {
					$params = $matches[1];
				}
			}

			foreach( $params as $i => $param ) {
				if( is_string( $param ) && strtolower( $param ) == 'null' ) {
					$params[$i] = null;
				}
			}

			return $params;
		}

		/**
		 *
		 * @param type $model
		 * @param type $rules
		 * @param type $code
		 * @return type
		 */
		protected function _addGuessedPgsqlConstraint( &$model, $rules, $code ) {
			// INFO: IIF the check is "xx()" or "xx() AND xx()" etc.
			// Remove extra parenthesis
			$code = preg_replace( '/^( *\(+ *)? *(.+) *(?(1) *\)+ *)$/', '\2', $code );
			// Transform '.*'::text
			$code = preg_replace( '/\'([^\']+)\'::[^,\)\]]+/', '\1', $code );
			// Transform (0)::numeric
			$code = preg_replace( '/\(([^\(\)]+)\)::[^,\)\]]+/', '\1', $code );
			// Transform NULL::character varying
			$code = preg_replace( '/NULL::[^,\)]+/', 'NULL', $code );

			if( preg_match_all( '/cakephp_validate_.*\((\(.+\).*|.+)\)/U', $code, $matches, PREG_PATTERN_ORDER ) ) {
				foreach( $matches[0] as $rule ) {
					// INFO: '.*'::text and (0)::numeric are tramsformed above
					// if( preg_match( '/^cakephp_validate_(?<function>[^\(]+)\((?<field>\(.*\)::\w+|\w+)(, *(?<params>.*)){0,1}\)$/', $rule, $parameters ) ) {
					if( preg_match( '/^cakephp_validate_(?<function>[^\(]+)\((?<field>\(.*\)|\w+)(, *(?<params>.*)){0,1}\)$/', $rule, $parameters ) ) {
						$ruleName = Inflector::camelize( $parameters['function'] );
						$ruleName[0] = strtolower( $ruleName[0] );

						$field = trim( $parameters['field'] );
						$params = $this->_extractPgsqlParams( $parameters );

						$rules[$field][$ruleName] = $this->normalizeValidationRule( $model, array( 'rule' => array_merge( array( $ruleName ), $params ), 'allowEmpty' => true ) );
					}
				}
			}

			return $rules;
		}
	}
?>