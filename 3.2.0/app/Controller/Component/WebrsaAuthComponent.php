<?php
/**
 * Fichier source de la classe SessionAclComponent.
 *
 * PHP 5.3
 *
 * @package SessionAcl.Controller.Component
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

App::uses('AuthComponent', 'Controller/Component');
App::uses('WebrsaPermissions', 'Utility');

/**
 * La classe SessionAclComponent permet l'initialisation de SessionAcl
 * 
 * @see SessionAcl/Model/Datasource/SessionAcl.php
 * @package SessionAcl.Controller.Component
 */
class WebrsaAuthComponent extends AuthComponent
{
	/**
	 * Uses the configured Authorization adapters to check whether or not a user is authorized.
	 * Each adapter will be checked in sequence, if any of them return true, then the user will
	 * be authorized for the request.
	 *
	 * @overide AuthComponent::isAuthorized()
	 * 
	 * @param array $user The user to check the authorization of. If empty the user in the session will be used.
	 * @param CakeRequest $request The request to authenticate for.  If empty, the current request will be used.
	 * @return boolean True if $user is authorized, otherwise false
	 */
	public function isAuthorized($user = null, CakeRequest $request = null) {
		if (empty($request)) {
			$request = $this->request;
		}
		
		$plugin = Inflector::camelize($request->params['plugin']);
		$controller = empty($plugin) ? $request->controller : "{$plugin}.{$this->name}";
		
		return WebrsaPermissions::check($controller, $request->action);
	}
}