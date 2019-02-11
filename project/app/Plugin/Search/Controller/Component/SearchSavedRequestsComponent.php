<?php
	/**
	 * Code source de la classe SearchSavedRequestsComponent.
	 *
	 * PHP 5.3
	 *
	 * @package Search
	 * @subpackage Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * La classe SearchSavedRequestsComponent permet de sauvegarder des URL de
	 * recherche et d'y être redirigé par la suite.
	 *
	 * Utilisation:
	 *	1°) Sauvegarde
	 *		$this->SearchSavedRequests->write( $this->name, $this->action, $this->request->params );
	 *		$this->SearchSavedRequests->write( 'Apres66', 'filelink', $this->request->params );
	 *	2°) Redirection
	 *		$this->SearchSavedRequests->redirect( 'dossiers', 'index', array( 'action' => 'edit', $id ) );
	 *		$this->SearchSavedFilters->redirect( $this->name, $this->action, array( 'action' => 'index', $personne_id ) );
	 *
	 * @see Savedfilters (Apres66Controller, Cohortesnonorientes66Controller,
	 * Cohortesvalidationapres66Controller, Nonorientes66Controller)
	 *
	 * @package Search
	 * @subpackage Controller.Component
	 */
	class SearchSavedRequestsComponent extends Component
	{
		/**
		 * Nom du component.
		 *
		 * @var string
		 */
		public $name = 'SearchSavedRequests';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array( 'Session' );

		/**
		 * Retourne le nom de la clé qui sera utilisée pour manipuler la session.
		 *
		 * @param string $controllerName
		 * @param string $actionName
		 * @return string
		 */
		public function sessionKey( $controllerName, $actionName ) {
			$controllerName = Inflector::camelize( $controllerName );
			return "{$this->name}.{$controllerName}.{$actionName}";
		}

		/**
		 * Ecrit une URL dans la session.
		 *
		 * @param string $controllerName
		 * @param string $actionName
		 * @param array $params
		 * @return boolean
		 */
		public function write( $controllerName, $actionName, array $params ) {
			$sessionKey = $this->sessionKey( $controllerName, $actionName );
			$url = Hash::merge(
				array(
					'plugin' => $params['plugin'],
					'controller' => $params['controller'],
					'action' => $params['action']
				),
				$params['named']
			);

			return $this->Session->write( $sessionKey, $url );
		}

		/**
		 * Lit une URL dans la session.
		 *
		 * @param string $controllerName
		 * @param string $actionName
		 * @return array
		 */
		public function read( $controllerName, $actionName ) {
			$sessionKey = $this->sessionKey( $controllerName, $actionName );
			return $this->Session->read( $sessionKey );
		}

		/**
		 * Supprime une URL de la session.
		 *
		 * @param string $controllerName
		 * @param string $actionName
		 * @return boolean
		 */
		public function delete( $controllerName, $actionName ) {
			$sessionKey = $this->sessionKey( $controllerName, $actionName );
			return $this->Session->delete( $sessionKey );
		}

		/**
		 * Vérifie si une URL est sauvegardée dans la session.
		 *
		 * @param string $controllerName
		 * @param string $actionName
		 * @return boolean
		 */
		public function check( $controllerName, $actionName ) {
			$sessionKey = $this->sessionKey( $controllerName, $actionName );
			return $this->Session->check( $sessionKey );
		}

		/**
		 * Redirige soit vers l'URL sauvegardée en session si elle existe, soit
		 * vers l'URL passée en paramètre. L'URL sauvegardée en session est
		 * supprimée après sa lecture.
		 *
		 * @param string $controllerName
		 * @param string $actionName
		 * @param array $url
		 * @return mixed
		 */
		public function redirect( $controllerName, $actionName, array $url ) {
			$Controller = $this->_Collection->getController();

			if( $this->check( $controllerName, $actionName ) ) {
				$url = $this->read( $controllerName, $actionName );
				$this->delete( $controllerName, $actionName );
			}
			else {
				$url = Hash::merge(
					array(
						'plugin' => $Controller->request->params['plugin'],
						'controller' => $Controller->request->params['controller'],
						'action' => $Controller->request->params['action']
					),
					$url
				);
			}

			return $Controller->redirect( $url );
		}
	}
?>