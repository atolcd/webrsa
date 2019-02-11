<?php
	// TODO: config parameters
	// FIXME: security ? -> http://groups.google.com/group/cake-php/browse_thread/thread/351b57905ada78dc/76bfdd3d8ade4291
	// INFO: http://book.cakephp.org/view/65/MVC-Class-Access-Within-Components
	App::uses( 'Component', 'Controller' );

	class PrgComponent extends Component
	{

		protected $_prgActions = array( );
		protected $_realGetParams = false;
		public $components = array( 'Session' );

		/**		 * ******************************************************************
		  Configuration:
		 * "true"    -> url like ..controller/action?name=value...
		 * "false"   -> url like ..controller/action/name:value...
		 * ******************************************************************* */
		protected function _realGetParams() {
			$args = func_get_args();
			$this->_realGetParams = true;
		}

		/**		 * ******************************************************************
		  The initialize method is called before the controller's beforeFilter method.
		 * ******************************************************************* */
		public function initialize( Controller $controller ) {
			$settings = $this->settings;
			$this->controller = $controller;
			$this->_prgActions = Set::extract( $settings, 'actions' );

			if( !is_array( $this->_prgActions ) ) {
				$this->_prgActions = array( $this->_prgActions );
			}

			$this->_prgActions = Set::normalize( $this->_prgActions );
		}

		/**		 * ******************************************************************
		  The startup method is called after the controller's beforeFilter
		  method but before the controller executes the current action handler.
		 * ******************************************************************* */
		public function startup( Controller $controller ) {
			$controller->request->data = Set::merge(
							$controller->request->data, (!empty( $controller->request->params['form'] ) ? $controller->request->params['form'] : array( ) )
			);

			if( !empty( $controller->request->params['form'] ) ) {
				return;
			}

			if( in_array( '*', array_keys( $this->_prgActions ) ) || in_array( $controller->action, array_keys( $this->_prgActions ) ) ) {
				$filter = Set::extract( $this->_prgActions, "{$controller->action}.filter" );

				if( !empty( $filter ) ) {
					$datas = array( $filter => Set::extract( $controller->request->data, $filter ) );
					$filteredData = Hash::filter( (array)$controller->request->data );
					$sessionKey = sha1( implode( '/', Hash::flatten( ( empty( $filteredData ) ? array( ) : $filteredData ), '__' ) ) );
				}
				else {
					$datas = $controller->request->data;
				}

				$datas = Hash::filter( (array)$datas );

				if( !empty( $datas ) ) {
					$params = Hash::flatten( $datas, '__' );

					// Real get params
					if( $this->_realGetParams ) {
						$params = array_filter( $params );
						$getUrl = Router::url( array( 'action' => $controller->action, '?' => $params ) );
					}
					// Cakephp "named params"
					else {
						// INFO: those caracters not permitted in string or else, get params are breaked
						foreach( $params as $key => $param ) {
							foreach( array( '?', '/', ':', '&' ) as $forbidden ) {
								$param = str_replace( $forbidden, ' ', $param );
							}
							$params[$key] = urlencode( $param );
						}

						if( !empty( $filter ) ) {
							$params['sessionKey'] = urlencode( $sessionKey );
						}

						$getUrl = Router::url( array_merge( array( 'action' => $controller->action ), $params ) );
					}

					if( !empty( $filter ) ) {
						$this->Session->write( "Prg.{$controller->name}__{$controller->action}.{$sessionKey}", Set::diff( $datas, $controller->request->data ) );
					}

					header( 'Location: '.$getUrl );
					exit();
					exit();
				}
				else {
					// Real get params
					if( $this->_realGetParams ) {
						$urlParams = $controller->request->query;
						unset( $urlParams['url'] );
					}
					// Cakephp "named params"
					else {
						$urlParams = array_map( 'urldecode', $controller->request->params['named'] );
					}

					if( isset( $urlParams['sessionKey'] ) ) {
						$sessionKey = $urlParams['sessionKey'];
					}

					$sessionParams = array( );
					if( !empty( $filter ) && isset( $sessionKey ) ) {
						$sessionParams = $this->Session->read( "Prg.{$controller->name}__{$controller->action}.{$sessionKey}" );
						$this->Session->delete( "Prg.{$controller->name}.{$controller->action}.{$sessionKey}" );
					}

					$params = Set::merge(
									(!empty( $datas ) ? $datas : array( ) ), (!empty( $urlParams ) ? $urlParams : array( ) ), (!empty( $sessionParams ) ? $sessionParams : array( ) )
					);

					$controller->request->data = Hash::expand( $params, '__' );
				}
			}
		}
	}
?>