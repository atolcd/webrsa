<?php
	/**
	 * Fichier source de la classe JetonsfonctionsComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * La classe JetonsfonctionsComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class JetonsfonctionsComponent extends Component
	{

		public $components = array( 'Session' );
		public $_userId;

		/**
		*   The initialize method is called before the controller's beforeFilter method.
		*/

		public function initialize( Controller $controller ) {
			$settings = $this->settings;
			$this->controller = $controller;
			// FIXME
			if( $this->_userId = $this->Session->read( 'Auth.User.id' ) ) {
				$this->controller->assert( valid_int( $this->_userId ), 'invalidParamForToken' ); // FIXME
			}

			$this->User = ClassRegistry::init( 'User' );
			$this->Jetonfonction = ClassRegistry::init( 'Jetonfonction' );
		}

		/**
		*   Supprime de la table les entrées pour lesquelles on est en timeout
		*/

		protected function _clean() {
			return $this->Jetonfonction->deleteAll(
				array(
					'Jetonfonction."modified" <' => strftime( '%Y-%m-%d %H:%M:%S', strtotime( '-'.readTimeout().' seconds' ) )
				)
			);
		}

		/**
		*   Obtenir la liste des entrées qui sont encore actives
		*/

		public function liste() {
			$this->_clean();

			$jetons = $this->Jetonfonction->find(
				'all',
				array(
					'fields' => array(
						'Jetonfonction.controller',
						'Jetonfonction.action'
					),
					'conditions' => array(
						'NOT' => array(
							'Jetonfonction.php_sid'     => session_id(),
							'Jetonfonction.user_id'     => $this->_userId
						)
					)
				)
			);

			return $jetons;
		}

		/**
		*   Vérifie si une pour un certain contrôleur et une certaine action,
		*   un autre utilisateur a déjà bloqué l'accès à la fonction et lance
		*   une erreur en cas de verrouillage par un autre utilisateur.
		*/

		public function check( $controller, $action ) {
			$this->_clean();

			$jeton = $this->Jetonfonction->find(
				'first',
				array(
					'conditions' => array(
						'Jetonfonction.controller'  => $controller,
						'Jetonfonction.action'  => $action,
						'and NOT' => array(
							'Jetonfonction.php_sid'     => session_id(), // FIXME: ou pas -> config
							'Jetonfonction.user_id'     => $this->_userId
						)
					)
				)
			);

			if( !empty( $jeton ) ) {
				$lockingUser = $this->User->find(
					'first',
					array(
						'conditions' => array(
							'User.id' => $jeton['Jetonfonction']['user_id']
						),
						'recursive' => -1
					)
				);
				$this->controller->assert( !empty( $lockingUser ), 'invalidParamForToken' );
				$this->controller->cakeError(
					'lockedAction', // FIXME: lockedQQchose d'autre
					array(
						'time' => ( strtotime( $jeton['Jetonfonction']['modified'] ) + readTimeout() ),
						'user' => $lockingUser['User']['username']
					)
				);
			}

			return empty( $jeton );
		}

		/**
		*   Vérifie si une pour un certain contrôleur et une certaine action,
		*   un autre utilisateur a déjà bloqué l'accès à la fonction
		*/

		public function locked( $controller, $action ) {
			$this->_clean();

			$jeton = $this->Jetonfonction->find(
				'first',
				array(
					'conditions' => array(
						'Jetonfonction.controller'  => $controller,
						'Jetonfonction.action'  => $action,
						'and NOT' => array(
							'Jetonfonction.php_sid'     => session_id(),
							'Jetonfonction.user_id'     => $this->_userId
						)
					)
				)
			);

			return !empty( $jeton );
		}

		/**
		*   Essaie d'obtenir un accès pour un certain contrôleur et une certaine action
		*/

		public function get( $controller, $action ) {
			if( $this->check( $controller, $action ) ) {
				$jeton = array(
					'Jetonfonction' => array(
						'controller'    => $controller,
						'action'        => $action,
						'php_sid'       => session_id(),
						'user_id'       => $this->_userId
					)
				);

				// Mise à jour éventuelle
				$vieuxJetonfonction = $this->Jetonfonction->find(
					'first',
					array(
						'conditions' => array(
							'Jetonfonction.controller'  => $controller,
							'Jetonfonction.action'      => $action,
							'Jetonfonction.php_sid'     => session_id(),
							'Jetonfonction.user_id'     => $this->_userId
						)
					)
				);
				if( !empty( $vieuxJetonfonction ) ) {
					$jeton['Jetonfonction']['id'] = $vieuxJetonfonction['Jetonfonction']['id'];
					$jeton['Jetonfonction']['created'] = $vieuxJetonfonction['Jetonfonction']['created'];
				}

				return ( $this->Jetonfonction->save( $jeton , array( 'atomic' => false ) ) !== false );
			}
			else {
				return false;
			}
		}

		/**
		*   Débloque l'accès pour un certain contrôleur et une certaine action
		*/

		public function release( $controller, $action ) {
			return $this->Jetonfonction->deleteAll(
				array(
					'Jetonfonction.controller'  => $controller,
					'Jetonfonction.action'      => $action,
					'Jetonfonction.php_sid'       => session_id(), // FIXME: ou pas -> config
					'Jetonfonction.user_id'       => $this->_userId
				)
			);
		}

		/** *******************************************************************
			The beforeRedirect method is invoked when the controller's redirect method
			is called but before any further action. If this method returns false the
			controller will not continue on to redirect the request.
			The $url, $status and $exit variables have same meaning as for the controller's method.
		******************************************************************** */
		public function beforeRedirect( Controller $controller, $url, $status = null, $exit = true ) {
			parent::beforeRedirect( $controller, $url, $status , $exit );
			return $url;
		}
	}

?>