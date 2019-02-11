<?php
	/**
	 * Fichier source de la classe Jetonsfonctions2Component.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * @package app.Controller.Component
	 */
	class Jetonsfonctions2Component extends Component
	{
		/**
		 * Controller using this component.
		 *
		 * @var Controller
		 */
		public $Controller = null;

		/**
		 * On a besoin d'un esession.
		 *
		 * @var array
		 */
		public $components = array( 'Session' );

		/**
		 * On initialise le modèle Jetonfonction si Configure::write( 'Jetonsfonctions2.disabled' ) n'est pas à true.
		 *
		 * @param Controller $controller Controller with components to initialize
		 * @return void
		 */
		public function initialize( Controller $controller ) {
			$settings = $this->settings;
			parent::initialize( $controller, $settings );
			$this->Controller = $controller;

			if( Configure::read( 'Jetonsfonctions2.disabled' ) ) {
				return;
			}

			$this->Jetonfonction = ClassRegistry::init( 'Jetonfonction' );
		}

		/**
		 * @return boolean
		 */
		public function get( $params = array() ) {
			if( Configure::read( 'Jetonsfonctions2.disabled' ) ) {
				return true;
			}

			$params = Set::merge( array( 'controller' => $this->Controller->name, 'action' => $this->Controller->action ), $params );
			$controllerName = $params['controller'];
			$actionName = $params['action'];
			unset( $params['controller'], $params['action'] );

			$params = serialize( !empty( $params ) ? (array)$params : null );

			$this->Jetonfonction->begin();

			$sq = $this->Jetonfonction->sq(
				array(
					'alias' => 'jetonsfonctions',
					'fields' => array(
						'jetonsfonctions.id',
						'jetonsfonctions.php_sid',
						'jetonsfonctions.user_id',
						'jetonsfonctions.modified',
					),
					'conditions' => array(
						'jetonsfonctions.controller' => $controllerName,
						'jetonsfonctions.action' => $actionName,
						'jetonsfonctions.params' => $params,
						// INFO: si on get et que l'on release dans la même page, il ne fera pas le second
						// SELECT même si cacheQueries est à false.
						'( \''.microtime( true ).'\' IS NOT NULL )'
					),
					'recursive' => -1
				)
			);

			$sq = "{$sq} FOR UPDATE";
			$result =@$this->Jetonfonction->query( $sq );

			if( $result === false ) {
				$this->Jetonfonction->rollback();
				$this->Controller->cakeError( 'error500' );
				return;
			}

			$fonctionNonVerrouillee = (
				empty( $result )
				|| $result[0]['jetonsfonctions']['php_sid'] ==  $this->Session->id()
				|| ( strtotime( $result[0]['jetonsfonctions']['modified'] ) < strtotime( '-'.readTimeout().' seconds' ) )
			);

			if( $fonctionNonVerrouillee ) {
				$jetonfonction = array(
					'Jetonfonction' => array(
						'controller' => $controllerName,
						'action' => $actionName,
						'params' => $params,
						'php_sid' => $this->Session->id(),
						'user_id' => $this->Session->read( 'Auth.User.id' ),
					)
				);

				if( !empty( $result ) && !empty( $result[0]['jetonsfonctions']['id'] ) ) {
					$jetonfonction['Jetonfonction']['id'] = $result[0]['jetonsfonctions']['id'];
				}

				$this->Jetonfonction->create( $jetonfonction );
				if( !$this->Jetonfonction->save( null, array( 'atomic' => false ) ) ) {
					$this->Jetonfonction->rollback();
					$this->Controller->cakeError( 'error500' );
				}

				$this->Jetonfonction->commit();
			}
			else {
				$this->Jetonfonction->rollback();

				$lockingUser = $this->Jetonfonction->User->find(
					'first',
					array(
						'conditions' => array(
							'User.id' => $result[0]['jetonsfonctions']['user_id']
						),
						'recursive' => -1
					)
				);

				throw new LockedActionException(
					'Action verrouillée',
					401,
					array(
						'time' => ( strtotime( $result[0]['jetonsfonctions']['modified'] ) + readTimeout() ),
						'user' => $lockingUser['User']['username']
					)

				);
				return;
			}

			return true;
		}

		/**
		 * On relache un (ensemble de) jeton(s).
		 *
		 * @param mixed $dossiers Un id de dossier ou un array d'ids de dossiers.
		 * @return boolean
		 */
		public function release( $params = array() ) {
			if( Configure::read( 'Jetonsfonctions2.disabled' ) ) {
				return true;
			}

			$params = Set::merge( array( 'controller' => $this->Controller->name, 'action' => $this->Controller->action ), $params );
			$controllerName = $params['controller'];
			$actionName = $params['action'];
			unset( $params['controller'], $params['action'] );

			$params = serialize( !empty( $params ) ? (array)$params : null );

			$this->Jetonfonction->begin();

			$conditions = array(
				'jetonsfonctions.controller' => $controllerName,
				'jetonsfonctions.action' => $actionName,
				'jetonsfonctions.params' => $params,
				// INFO: si on get et que l'on release dans la même page, il ne fera pas le second
				// SELECT même si cacheQueries est à false.
				'( \''.microtime( true ).'\' IS NOT NULL )'
			);

			$sq = $this->Jetonfonction->sq(
				array(
					'alias' => 'jetonsfonctions',
					'fields' => array(
						'jetonsfonctions.id',
						'jetonsfonctions.php_sid',
						'jetonsfonctions.user_id',
						'jetonsfonctions.modified',
					),
					'conditions' => $conditions,
					'recursive' => -1
				)
			);

			$sq = "{$sq} FOR UPDATE";

			$results =@$this->Jetonfonction->query( $sq );
			if( $results === false ) {
				$this->Jetonfonction->rollback();
				die( 'Erreur étrange' );
				return false;
			}

			if( $this->Jetonfonction->deleteAll( $conditions, false, false ) == false ) {
				$this->Jetonfonction->rollback();
				die( 'Erreur étrange' );
				return false;
			}

			$this->Jetonfonction->commit();

			return true;
		}

		/**
		 * Retourne une condition concernant l'instant pivot en-dessous duquel les connections sont
		 * considérées comme étant expirées.
		 *
		 * @return string
		 */
		protected function _conditionsValid() {
			return array( 'modified >=' => strftime( '%Y-%m-%d %H:%M:%S', strtotime( '-'.readTimeout().' seconds' ) ) );
		}

		/**
		 * Vérifie si une pour un certain contrôleur, une certaine action et d'éventuels paramètres, un autre
		 * utilisateur a déjà bloqué l'accès à la fonction.
		 *
		 * @param mixed $params
		 * @return boolean
		 */
		public function locked( $params = array() ) {
			if( Configure::read( 'Jetonsfonctions2.disabled' ) ) {
				return true;
			}

			$params = Set::merge( array( 'controller' => $this->Controller->name, 'action' => $this->Controller->action ), $params );
			$controllerName = $params['controller'];
			$actionName = $params['action'];
			unset( $params['controller'], $params['action'] );

			$params = serialize( !empty( $params ) ? (array)$params : null );

			$conditions = array(
				'jetonsfonctions.controller' => $controllerName,
				'jetonsfonctions.action' => $actionName,
				'jetonsfonctions.params' => $params,
				// INFO: si on get et que l'on release dans la même page, il ne fera pas le second
				// SELECT même si cacheQueries est à false.
				'( \''.microtime( true ).'\' IS NOT NULL )'
			);

			$sq = $this->Jetonfonction->sq(
				array(
					'alias' => 'jetonsfonctions',
					'fields' => array(
						'jetonsfonctions.id',
						'jetonsfonctions.php_sid',
						'jetonsfonctions.user_id',
						'jetonsfonctions.modified',
					),
					'conditions' => $conditions,
					'recursive' => -1
				)
			);

			$results =@$this->Jetonfonction->query( $sq );
			if( $results === false ) {
				die( 'Erreur étrange' );
				return false;
			}

			return !empty( $results );
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
			return array( 'url' => $url, 'status' => $status, 'exit' => $exit );
		}
	}
?>