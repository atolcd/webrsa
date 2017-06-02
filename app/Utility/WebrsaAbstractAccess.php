<?php
    /**
     * Code source de la classe WebrsaAbstractAccess.
     *
     * PHP 5.3
     *
     * @package app.Utility
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */

	App::uses('WebrsaAccessInterface', 'Utility/Interfaces');

    /**
	 * La classe WebrsaAbstractAccess
     *
     * @package app.Utility
     */
	abstract class WebrsaAbstractAccess implements WebrsaAccessInterface
	{
		/**
		 * Liste des classes WebrsaAccess par controller
		 *
		 * @var array
		 */
		public static $WebrsaAccess = array();

		/**
		 * Permet d'obtenir les accès pour un find first
		 *
		 * @param array $record
		 * @param array $params
		 * @return array
		 */
		final public static function access(array $record, array $params = array()) {
			$matches = null;
			if (!preg_match('/^WebrsaAccess(.*)$/', get_called_class(), $matches)) {
				trigger_error("Nom de class mal défini, il doit porter WebrsaAccess suivi du nom du controller");
				exit;
			}

			list($className, $mainController) = $matches;

			$params = call_user_func(array($className, 'params'), $params);
			$actions = call_user_func(array($className, 'actions'), $params);

			foreach (array_keys($actions) as $action) {
				if (strpos($action, '.')) {
					list($controller, $action) = explode('.', $action);
				} else {
					$controller = $mainController;
				}

				$url = "/$controller/$action";

				if (!isset(self::$WebrsaAccess[$controller])) {
					App::uses("WebrsaAccess".$controller, 'Utility');
					self::$WebrsaAccess[$controller] = "WebrsaAccess".$controller;
				}

				$record[$url] = self::check($controller, $action, $record, $params);
			}

			return $record;
		}

		/**
		 * Permet d'obtenir les accès pour un find all
		 *
		 * @param array $records
		 * @param array $params
		 * @return array
		 */
		final public static function accesses(array $records, array $params = array()) {
			foreach (array_keys($records) as $key) {
				$records[$key] = self::access($records[$key], $params);
			}

			return $records;
		}

		/**
		 * Permet de vérifier les droits d'accès à une action sur un enregistrement
		 *
		 * @param string $controller
		 * @param string $action
		 * @param array $record
		 * @param array $params
		 * @return boolean
		 */
		final public static function check($controller, $action, array $record = array(), array $params = array()) {
			if (!isset(self::$WebrsaAccess[$controller])) {
				App::uses("WebrsaAccess".$controller, 'Utility');
				self::$WebrsaAccess[$controller] = "WebrsaAccess".$controller;
			}
			$params = call_user_func(array(get_called_class(), 'params'), $params);

			$className = self::$WebrsaAccess[$controller];
			$method = "_{$action}";

			$availablesActions = array();
			foreach (array_keys(call_user_func(array(get_called_class(), 'actions'), $params)) as $availableAction) {
				if (!strpos($availableAction, '.')) {
					$availablesActions[] = $controller.'.'.$availableAction;
				} else {
					$availablesActions[] = $availableAction;
				}
			}

			if (strpos($action, '.')) {
				list($controller, $action) = explode('.', $action);
			}

			return method_exists($className, $method)
				&& in_array("$controller.$action", $availablesActions)
				&& call_user_func(array($className, $method), $record, call_user_func(array($className, 'params'), $params))
			;
		}

		/**
		 * Merge et normalize les actions par défault avec celles ajoutés
		 *
		 * @param array $defaults
		 * @param array $actions
		 * @return array
		 */
		public static function merge_actions(array $defaults, array $actions) {
			return Hash::merge(
				self::normalize_actions($defaults),
				self::normalize_actions($actions)
			);
		}

		/**
		 * Normalize la liste des actions
		 *
		 * @param array $actions
		 * @return array
		 */
		public static function normalize_actions(array $actions) {
			$controller = str_replace('WebrsaAccess', '', get_called_class());
			$results = array();
			foreach (Hash::normalize($actions) as $action => $params) {
				$action = strpos($action, '.') === false ? $controller.'.'.$action : $action;
				$results[$action] = $params === null ? array() : $params;
			}
			return $results;
		}

		/**
		 * Permet d'obtenir les clefs à calculer pour connaitre les droits d'accès
		 * à toutes les actions disponnibles
		 *
		 * @return array
		 */
		public static function getParamsList(array $params = array()) {
			$params = call_user_func(array(get_called_class(), 'params'), $params);

			$paramsList = array();
			foreach (call_user_func(array(get_called_class(), 'actions'), $params) as $values) {
				foreach ($values as $key => $value) {
					if ($value) {
						$paramsList[] = $key;
					}
				}
			}

			return $paramsList;
		}

		/**
		 * Même utilitée que self::getParamsList, à la différence qu'on ne récupère
		 * la liste que d'une seule action
		 *
		 * @param String $action
		 * @param array $params
		 * @return array
		 */
		public static function getActionParamsList($action, array $params = array()) {
			$params = call_user_func(array(get_called_class(), 'params'), $params);

			$controller = str_replace('WebrsaAccess', '', get_called_class());
			$action = strpos($action, '.') === false ? $controller.'.'.$action : $action;
			$actions = call_user_func(array(get_called_class(), 'actions'), $params);

			if (!isset($actions[$action])) {
				trigger_error("L'action $action n'est pas disponnible pour le calcul des droits d'accès.");
				$actions[$action] = array();
			}

			$results = array();
			foreach ($actions[$action] as $key => $value) {
				if ($value) {
					$results[] = $key;
				}
			}

			return $results;
		}

		/**
		 * Similaire à get_class_methods() de php mais permet à l'interieur de
		 * la classe, de lister les méthodes protégées (utile pour la vérification
		 * de l'application)
		 *
		 * @return array
		 */
		final public static function get_class_methods() {
			return get_class_methods(get_called_class());
		}

		/**
		 * Liste des "actions" à ignorer leur utilitée dans la vérification de l'application.
		 * Peut servir à ignorer des méthodes protégés qui ne concernent pas une action ou
		 * des actions qui dépendent de paramètres autre que celui du département.
		 *
		 * @return array - normalisé avec self::normalize_actions
		 */
		public static function ignoreCheck() {
			return array();
		}
	}
?>
