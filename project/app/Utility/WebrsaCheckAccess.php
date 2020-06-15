<?php
	/**
	 * Code source de la classe WebrsaCheckAccess.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	define('APP_UTILITY_PATH', APP.'Utility');

	/**
	 * La classe WebrsaCheckAccess vérifi la cohérence des données des classes de type WebrsaAccess
	 *
	 * @package app.Utility
	 */
	abstract class WebrsaCheckAccess
	{
		/**
		 * Liste des départements utilisant Webrsa
		 * @var array
		 */
		public static $departements = array(58, 66, 93, 976, '99X');

		/**
		 * Liste des chemins vers les classes de type WebrsaAccess
		 * @var array
		 */
		public static $utilityPaths = array(APP_UTILITY_PATH);

		/**
		 * Liste des classes chargés en clef avec le nom de leurs controller en valeur
		 * @var array - ex: array('WebrsaAccessMoncontroller' => 'Moncontroller', ...)
		 */
		protected static $_toCheck = array();

		/**
		 * Si la méthode WebrsaAccess<Nomducontroller>::_<nomAction> existe, elle sera dans cette liste
		 * @var array - ex: array(0 => 'Moncontroller/monaction', ...)
		 */
		protected static $_actionExists = array();

		/**
		 * Liste des actions existante dont il ne faut pas vérifier leur utilité
		 * @var array - ex: array(0 => 'Moncontroller/monaction', ...)
		 */
		protected static $_ignoreList = array();

		/**
		 * Vérifi que les rêgles d'accès métier sont bien défini pour toutes les actions
		 *
		 * @return array
		 */
		public static function checkWebrsaAccess() {
			self::_initialize();
			$defined = self::_checkIfActionsAreDefined();
			$results = self::_checkIfActionsAreUsed($defined);

			ksort($results);
			return $results;
		}

		/**
		 * Vérifi qu'une action définie (méthode existante dans WebrsaAccess<Nomducontroller>),
		 * est bien utilisée dans la méthode WebrsaAccess<Nomducontroller>::actions
		 *
		 * @param array $defined - Liste des actions définies par self::_checkIfActionsAreDefined
		 *							ex: array('Moncontroller/action' => array('success' => true,  ...))
		 * @return array - complète $defined
		 */
		protected static function _checkIfActionsAreUsed(array $defined) {
			$results = array();

			foreach (self::$_actionExists as $action) {
				if (!in_array($action, array_keys($defined))) {
					list($controllerName) = explode('/',$action);
					$ignore = in_array($action, self::$_ignoreList);

					$results[$action] = array(
						'success' => $ignore,
						'value' => $ignore ? 'N/A' : 'false',
						'message' => $ignore
							? 'Cette action possède des règles particulières non '
								. 'prise en compte dans la vérification de l\'application'
							: "L'action est définie dans WebrsaAccess$controllerName mais n'est jamais utilisée"
					);
				}
			}

			return array_merge($defined, $results);
		}

		/**
		 * Vérifi l'existance d'une méthode protégée.
		 * Si une action est "demandée" dans la méthode WebrsaAccess<Nomducontroller>::actions,
		 * Elle doit exister sous la forme WebrsaAccess<Nomducontroller>::_<nomAction>
		 *
		 * @return array - ex: array('Moncontroller/action' => array('success' => true,  ...))
		 */
		protected static function _checkIfActionsAreDefined() {
			$myDepartement = Configure::read('Cg.departement');
			$results = array();
			$actionUsedByMyCg = array();
			foreach (array_keys(self::$_toCheck) as $className) {
				foreach (self::$departements as $departement) {
					foreach (array_keys(call_user_func(array($className, 'actions'), array('departement' => $departement))) as $fullAction) {
						list($controller, $action) = explode('.', $fullAction);
						$actionClassName = 'WebrsaAccess'.$controller;

						// Pour affichage des actions disponible
						if ($departement == $myDepartement) {
							$actionUsedByMyCg[] = $controller.'/'.$action;
						}

						$results[$controller.'/'.$action] = array(
							'success' => $success = in_array($controller.'/'.$action, self::$_actionExists),
							'value' => $access = in_array($controller.'/'.$action, $actionUsedByMyCg) ? 'true' : 'false',
							'message' => $success
								? ($access === 'true' ? null : 'Action non disponible pour votre département')
								: 'L\'action semble ne pas être définie dans le fichier '.$actionClassName
						);
					}
				}
			}

			return $results;
		}

		/**
		 * Initialise les attributs de classe
		 */
		protected static function _initialize() {
			self::_loadAllWebrsaAccessClass();
			self::_getActionExistsList();
			self::_getIgnoreList();
		}

		/**
		 * App::uses de toutes les classes WebrsaAccess<controllerName>
		 * Remplit <strong>self::self::$_toCheck</strong> avec un array des classes chargés en clef
		 * avec le nom de leurs controller en valeur
		 *
		 * ex: array('WebrsaAccessMoncontroller' => 'Moncontroller', ...)
		 */
		protected static function _loadAllWebrsaAccessClass() {
			$matches = null;
			foreach (self::$utilityPaths as $path) {
				foreach (scandir($path) as $utilityName) {
					// On ne veux que les fichiers correspondant à : WebrsaAccess<Nomducontroller>.php
					if (!preg_match('/^(WebrsaAccess([A-Z][\w]+))\.php$/', $utilityName, $matches)) {
						continue;
					}

					list(, $className, $controllerName) = $matches;

					// On charge le fichier
					App::uses($className, 'Utility');

					// On garni la liste
					self::$_toCheck[$className] = $controllerName;
				}
			}
		}

		/**
		 * On récupère la liste des actions définies
		 * Garnit self::$_actionExists
		 *
		 * ex: array(0 => 'Moncontroller/monaction', ...)
		 */
		protected static function _getActionExistsList() {
			foreach (self::$_toCheck as $className => $controllerName) {
				foreach (call_user_func(array($className, 'get_class_methods')) as $method) {
					if (strpos($method, '_') === 0) {
						self::$_actionExists[] = $controllerName.'/'.substr($method, 1);
					}
				}
			}
		}

		/**
		 * On récupère la liste des actions définies
		 * Garnit self::$_ignoreList
		 *
		 * ex: array(0 => 'Moncontroller/monaction', ...)
		 */
		protected static function _getIgnoreList() {
			foreach (array_keys(self::$_toCheck) as $className) {
				foreach (array_keys(call_user_func(array($className, 'ignoreCheck'))) as $ignore) {
					list($controller, $action) = explode('.', $ignore);
					self::$_ignoreList[] = $controller.'/'.$action;
				}
			}
		}
	}