<#include "../freemarker_functions.ftl">
<?php
	/**
	 * Code source de la classe ${name}.
	 *
<#if php_version??>
	 * PHP ${php_version}
	 *
</#if>
	 * @package app.Controller.Component
	 * @license ${license}
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * La classe ${name} ...
	 *
	 * @package app.Controller.Component
	 */
	class ${name} extends Component
	{
		/**
		 * Paramètres de ce component
		 *
		 * @var array
		 */
		public $settings = array( );

		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array( );

		/**
		 * Appelée avant Controller::beforeFilter().
		 *
		 * @param Controller $controller Controller with components to initialize
		 * @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::initialize
		 */
		public function initialize( Controller $controller ) {
		}

		/**
		 * Called after the Controller::beforeFilter() and before the controller action
		 *
		 * @param Controller $controller Controller with components to startup
		 * @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::startup
		 */
		public function startup( Controller $controller ) {
		}

		/**
		 * Called before the Controller::beforeRender(), and before
		 * the view class is loaded, and before Controller::render()
		 *
		 * @param Controller $controller Controller with components to beforeRender
		 * @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::beforeRender
		 */
		public function beforeRender( Controller $controller ) {
		}

		/**
		 * Called after Controller::render() and before the output is printed to the browser.
		 *
		 * @param Controller $controller Controller with components to shutdown
		 * @link @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::shutdown
		 */
		public function shutdown( Controller $controller ) {
		}

		/**
		 * Called before Controller::redirect().  Allows you to replace the url that will
		 * be redirected to with a new url. The return of this method can either be an array or a string.
		 *
		 * If the return is an array and contains a 'url' key.  You may also supply the following:
		 *
		 * - `status` The status code for the redirect
		 * - `exit` Whether or not the redirect should exit.
		 *
		 * If your response is a string or an array that does not contain a 'url' key it will
		 * be used as the new url to redirect to.
		 *
		 * @param Controller $controller Controller with components to beforeRedirect
		 * @param string|array $url Either the string or url array that is being redirected to.
		 * @param integer $status The status code of the redirect
		 * @param boolean $exit Will the script exit.
		 * @return array|null Either an array or null.
		 * @link @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::beforeRedirect
		 */
		public function beforeRedirect( Controller $controller, $url, $status = null, $exit = true ) {
		}
	}
?>