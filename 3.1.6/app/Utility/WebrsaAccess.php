<?php
	/**
	 * Code source de la classe WebrsaAccess.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaPermissions', 'Utility');

	/**
	 * La classe WebrsaAccess utilise les rêgles métier afin de griser ou pas un ou plusieurs liens
	 *
	 * @package app.Utility
	 */
	class WebrsaAccess
	{	
		/**
		 * Renseignez le dossier menu si besoin
		 * 
		 * @var array
		 */
		public static $dossierMenu = null;
		
		/**
		 * Permet de définir en une fois les variables utiles
		 * 
		 * @param array $dossierMenu
		 */
		public static function init($dossierMenu = null) {
			self::$dossierMenu = $dossierMenu;
		}
		
		/**
		 * Permet de produire un lien aux standards du plugin Defaut3 avec import
		 * de la vérification acl et métier (standard WebrsaAccess)
		 * 
		 * Fonction avec ou sans dossierMenu/modèle
		 * @see WebrsaAccess::init
		 * 
		 * @param String $url - ex: '/controller/action/params'
		 * @param array $params - array('condition' => true, 'msgid' => 'foo', ...)
		 *						- Spécial <b>(boolean) 'regles_metier'</b> Prise en compte ou non des règles métier
		 *						- Spécial <b>(String) 'controller'</b> Permet de spécifier l'action d'un autre controller
		 * @return array - array('/controller/action/id' => array('disable' => true))
		 */
		public static function link($url, $params = array()) {
			$path = self::_extractControllerAction($url);
			$useModel = Hash::get((array)$params, 'regles_metier');
			unset($params['regles_metier']);
			
			$action = $path['action'];
			$aclAccess = self::$dossierMenu !== null
				? WebrsaPermissions::checkDossier($path['controller'], $action, self::$dossierMenu)
				: WebrsaPermissions::check($path['controller'], $action)
			;
			
			$toEval = "!'#/".Inflector::camelize($path['controller'])."/$action#'";
			
			$disabled = $useModel !== false
				? ($aclAccess ? $toEval : true)
				: !$aclAccess
			;
			
			return array($url => array('disabled' => $disabled) + (array)$params);
		}
		
		/**
		 * Permet de traiter en une seule fois, une liste d'actions, utile pour Default3::index
		 * 
		 * @param array $urls - Liste des urls ex: array('/controller/action/params', ...)
		 * @param array $allParams - Paramètres à appliquer à tous les liens
		 * @return array
		 */
		public static function links(array $urls, array $allParams = array()) {
			$results = array();
			foreach (Hash::normalize($urls) as $url => $params) {
				$link = self::link($url, (array)$params + $allParams);
				$results[$url] = $link[$url];
			}
			return $results;
		}
		
		/**
		 * Utile pour l'action add, permet d'ajouter un boolean $ajoutPossible à la fonction link
		 * Désactive également tout seul les rêgles métier (déjà traité par $ajoutPossible)
		 * 
		 * @param String $url - ex: '/controller/action/params'
		 * @param boolean $ajoutPossible
		 * @param array $params
		 * @return array
		 */
		public static function actionAdd($url, $ajoutPossible = true, $params = array()) {
			$params['regles_metier'] = false;
			$link = self::link($url, $params);
			
			if ($link[$url]['disabled'] === false) {
				$link[$url]['disabled'] = !$ajoutPossible;
			}
			
			return $link;
		}
		
		/**
		 * Permet de vérifier les droits d'accès à partir d'un $record 
		 * (équivalent find->first complété par WebrsaAbstractAccess::access)
		 * et d'une url
		 * 
		 * @param Array $record
		 * @param String $url - /$controller/$action
		 * @return boolean
		 */
		public static function isDisabled($record, $url) {
			$path = self::_extractControllerAction($url);
			$controller = $path['controller'];
			$action = $path['action'];
			$access = Hash::get($record, "/$controller/$action") !== null
				? Hash::get($record, "/$controller/$action")
				: Hash::get($record, "/".Inflector::camelize($controller)."/$action")
			;
			
			if ($access === null) {
				trigger_error("L'URL <b>/$controller/$action</b> n'a pas &eacute;t&eacute; trouv&eacute;e dans les donn&eacute;es envoy&eacute;es");
			}
			
			$aclAccess = self::$dossierMenu !== null
				? WebrsaPermissions::checkDossier($controller, $action, self::$dossierMenu)
				: WebrsaPermissions::check($controller, $action)
			;
			
			return $access && $aclAccess ? false : true;
		}
		
		/**
		 * Permet de vérifier les droits d'accès à partir d'un $record 
		 * (équivalent find->first complété par WebrsaAbstractAccess::access)
		 * et d'une url
		 * 
		 * @see WebrsaAccess::isDisabled
		 * @param Array $record
		 * @param String $url - /$controller/$action
		 * @return boolean
		 */
		public static function isEnabled($record, $url) {
			return !self::isDisabled($record, $url);
		}
		
		/**
		 * Mix entre WebrsaAccess::isEnabled et WebrsaAccess::actionAdd
		 * Permet de savoir si un bouton ajouter est grisé ou pas
		 * 
		 * @param String $url
		 * @param boolean $ajoutPossible
		 * @return boolean
		 */
		public static function addIsEnabled($url, $ajoutPossible) {
			$path = self::_extractControllerAction($url);
			$aclAccess = self::$dossierMenu !== null
				? WebrsaPermissions::checkDossier($path['controller'], $path['action'], self::$dossierMenu)
				: WebrsaPermissions::check($path['controller'], $path['action'])
			;
			
			return $ajoutPossible && $aclAccess;
		}
		
		public static function actions($urls, $data = array(), array $params = array()) {
			$links = array();
			
			if (empty($data)) {
				$params += array('regles_metier' => false);
			}
			
			foreach (Hash::normalize($urls) as $url => $urlParams) {
				if (Hash::get((array)$urlParams, 'hidden')) {
					continue;
				}
				
				$allParams = (array)$urlParams + $params;
				
				if (isset($urlParams['add'])) {
					$link = self::actionAdd($url, $urlParams['add']);
				} elseif (!isset($allParams['regles_metier']) || $allParams['regles_metier']) {
					$link = self::link($url, $allParams);
					$link[$url]['disabled'] = !($link[$url]['disabled'] !== true && self::isEnabled($data, $url));
				} else {
					$link = self::link($url, $allParams);
				}
				
				$links = array_merge($links, $link);
			}
			
			return $links;
		}
		
		/**
		 * Extrait le nom du controller et l'action à partir d'une url
		 * 
		 * @param String $url - /MonController/monaction
		 * @return array - array('controller' => 'mon_controller', 'action' => 'monaction')
		 */
		protected static function _extractControllerAction($url) {
			if (!preg_match('/^\/([\w]+)(?:\/([\w]+)){0,1}/', $url, $matches)) {
				trigger_error("URL mal d&eacute;finie");
			}
			
			return array(
				'controller' => Inflector::underscore($matches[1]),
				'action' => count($matches) === 3 ? $matches[2] : 'index'
			);
		}
	}
?>