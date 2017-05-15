<?php

	/**
	 * Code source de la classe ValidationErrorsUtility.
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ValidationErrorsUtility Permet de trouver facilement la totalité des erreurs de validation
	 *
	 * @package app.Utility
	 */
	abstract class ValidationErrors {
		/**
		 * Permet de trouver facilement la totalité des erreurs de validation
		 */
		public static function all() {
			$results = array();
			foreach (App::objects('Model') as $model) {
				if (class_exists($model)) {
					$r = new ReflectionClass($model);
					if (!$r->isAbstract()) {
						$Model = ClassRegistry::init($model);
						$errors = $Model->validationErrors;	
					}
					if (!empty($errors)) {
						$results[$model] = self::_extractErrorData($Model, $errors);
					}
				}
			}
			return $results;
		}
		
		/**
		 * Permet d'extraire la valeur qui a échouée à la validation
		 * 
		 * @param Model $Model
		 * @param array $errors
		 * @return array
		 */
		protected static function _extractErrorData(Model $Model, $errors) {
			foreach (array_keys($errors) as $key) {
				if (Hash::get((array)$Model->data, $Model->alias.'.'.$key)) {
					$errors[$key]['value'] = $Model->data[$Model->alias][$key];
				}
			}
			
			return $errors;
		}
	}