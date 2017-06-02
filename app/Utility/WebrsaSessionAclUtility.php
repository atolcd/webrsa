<?php
/**
 * Fichier source de la classe WebrsaSessionAclUtility.
 *
 * PHP 5.3
 *
 * @package App.Utility
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

App::uses('SessionAclUtility', 'SessionAcl.Utility');

/**
 * La classe WebrsaSessionAclUtility offre des méthodes utiles pour gérer les ACLs
 * 
 * @package App.Utility
 */
class WebrsaSessionAclUtility extends SessionAclUtility
{
	/**
	 * Permet d'obtenir la liste des acos de type controllers en fonction des 
	 * controlleurs de l'application
	 * 
	 * @overide Ajoute les concepts de commeDroit et aucunDroit de webrsa
	 * 
	 * @return array array('controllers/Moncontroller/monaction', ...)
	 */
	protected static function _getAppControllersActionsTree() {
		$acos = parent::_getAppControllersActionsTree();
		
		// Retrait des commeDroit et aucunDroit
		foreach ($acos as $path) {
			if (!preg_match('/controllers(?:\/([^\/]+))?(?:\/([^\/]+))?/', $path, $matches)
				|| isset($matches[2])
				|| !isset($matches[1])
			) {
				continue;
			}
			
			$controllerName = $matches[1].'Controller';
			$Controller = new $controllerName();
			
			if (!empty($Controller->commeDroit)) {
				foreach (array_keys($Controller->commeDroit) as $action) {
					$remove[] = 'controllers/'.$matches[1].'/'.$action;
				}
			}
			
			if (!empty($Controller->aucunDroit)) {
				foreach ($Controller->aucunDroit as $action) {
					$remove[] = 'controllers/'.$matches[1].'/'.$action;
				}
			}
		}
		
		foreach ($acos as $key => $path) {
			if (in_array($path, $remove)) {
				unset($acos[$key]);
			}
		}
		
		// Retrait des controllers sans droits
		$actives = array('controllers');
		foreach ($acos as $path) {
			if (!preg_match('/controllers(?:\/([^\/]+))?(?:\/([^\/]+))?/', $path, $matches)) {
				continue;
			}
			
			if (isset($matches[2])) {
				$actives['controllers/'.$matches[1]] = 'controllers/'.$matches[1];
				$actives[$path] = $path;
				continue;
			}
		}
		
		foreach ($acos as $key => $path) {
			if (!in_array($path, $actives)) {
				unset($acos[$key]);
			}
		}
		
		return $acos;
	}
}