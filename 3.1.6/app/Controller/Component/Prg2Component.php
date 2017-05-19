<?php
	/**
	 * Fichier source de la classe Prg2Component.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * POST/redirect/GET.
	 *
	 * FIXME: dans /cohortesnonorientes66/isemploi, on n'a pas la clé Search.Situationdossierrsa.etatdosrsa_choice mais Situationdossierrsa.etatdosrsa_choice dans le formulaire
	 *
	 * @package app.Controller.Component
	 */
	class Prg2Component extends Component
	{

		/**
		 * Controller using this component.
		 *
		 * @var Controller
		 */
		public $controller = null;

		/**
		 * Components utilisés par ce component-ci.
		 *
		 * @var array
		 */
		public $components = array( 'Session', 'RequestHandler' );

		/**
		 *
		 * @var array
		 */
		protected $_prgActions = array( );

		/**
		 * Called before the Controller::beforeFilter().
		 *
		 * @param Controller $controller Controller with components to initialize
		 * @param array $settings
		 * @return void
		 */
		public function initialize( Controller $controller ) {
			$settings = $this->settings;
			$this->controller = $controller;
			$this->_prgActions = Set::extract( $settings, 'actions' );

			if( !is_array( $this->_prgActions ) ) {
				$this->_prgActions = array( $this->_prgActions );
			}

			$this->_prgActions = Set::normalize( $this->_prgActions );
		}

		/**
		 * FIXME: ne fonctionne pas bien
		 *
		 * @param array $params
		 * @return array
		 */
		protected function _urlencodeParams( $params, $forbiddenlist = array( '?', '/', ':', '&' ) ) {
			foreach( $params as $key => $param ) {
				foreach( $forbiddenlist as $forbidden ) {
					$param = str_replace( $forbidden, ' ', $param );
				}
				$params[$key] = urlencode( $param );
			}

			return $params;
		}

		/**
		 * Called after the Controller::beforeFilter() and before the controller action
		 *
		 * @param Controller $controller Controller with components to startup
		 * @return void
		 */
		public function startup( Controller $controller ) {
			if( in_array( $controller->action, array_keys( $this->_prgActions ) ) ) { // FIXME: function avec *
				if( !empty( $controller->request->params['form'] ) ) { // FIXME: dans Prg, on faisait un merge avec $controller->request->data juste avant
					return;
				}

				if( $controller->request->is( 'post' ) ) {
					$params = $controller->request->data;

					if( isset( $this->_prgActions[$controller->action]['filter'] ) ) {
						$key = $this->_prgActions[$controller->action]['filter'];
						$sessionParams = $params;
						$params = array( $key => ( isset( $params[$key] ) ? $params[$key] : array( ) ) );
						unset( $sessionParams[$key] );

						if( !empty( $sessionParams ) ) {
							unset( $sessionParams['sessionKey'] );
							$sessionKey = sha1( implode( '/', Hash::flatten( ( empty( $sessionParams ) ? array( ) : $sessionParams ), '__' ) ) );
							$this->Session->write( "Prg.{$controller->name}__{$controller->action}.{$sessionKey}", $sessionParams );
							$params['sessionKey'] = $sessionKey;

//							$this->log( var_export( $this->Session->read( 'Search.Prg' ), true ), LOG_DEBUG );
						}
					}

					$params = Hash::flatten( $params, '__' );
					$params = Set::merge( $controller->request->params['named'], $params );
					$params = $this->_urlencodeParams( $params );

					$redirect = array_merge( array( 'action' => $controller->action ), $params );
					$controller->redirect( $redirect );
				}
				else if( $controller->request->is( 'get' ) ) {
					if( CAKE_BRANCH == '1.2' ) {
						$controller->request->data = Hash::expand( $controller->request->params['named'], '__' );
					}
					else {
						$controller->request->data = Hash::expand( array_map( 'urldecode', $controller->request->params['named'] ), '__' );
					}

					if( isset( $controller->request->params['named']['sessionKey'] ) ) {
						$sessionParams = $this->Session->read( "Prg.{$controller->name}__{$controller->action}.{$controller->request->params['named']['sessionKey']}" );

						$this->Session->delete( "Prg.{$controller->name}__{$controller->action}.{$controller->request->params['named']['sessionKey']}" );
						$controller->request->data = Set::merge( $controller->request->data, $sessionParams );

//						$this->log( var_export( $this->Session->read( 'Search.Prg' ), true ), LOG_DEBUG );
					}
				}
			}
		}

		/**
		 * Called after Controller::render() and before the output is printed to the browser.
		 *
		 * @param Controller $controller Controller with components to shutdown
		 * @return void
		 */
		/* public function shutdown( Controller $controller ) {

		  } */

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
			return array( 'url' => $url, 'status' => $status, 'exit' => $exit );
		}

	}
?>