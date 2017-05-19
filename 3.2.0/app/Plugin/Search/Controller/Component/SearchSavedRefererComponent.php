<?php
	/**
	 * Code source de la classe SearchSavedRefererComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * La classe SearchSavedRefererComponent ...
	 *
	 * Permet (certainement) de déprécier la classe SearchSavedRequests.
	 *
	 * @package app.Controller.Component
	 */
	class SearchSavedRefererComponent extends Component
	{
		/**
		 * Nom du component.
		 *
		 * @var string
		 */
		public $name = 'SearchSavedReferer';

		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array( 'Session' );

		/**
		 * Retourne le nom de la clé de session pour l'URL courante.
		 *
		 * @return string
		 */
		public function sessionKey() {
			$Controller = $this->_Collection->getController();
			$here = $Controller->request->here( false );
			return "{$this->name}.{$here}";
		}

		/**
		 * Sauvegarde l'URL du referer de l'URL courante lorsque ceux-ci sont
		 * différents.
		 *
		 * @return boolean
		 */
		public function save() {
			$Controller = $this->_Collection->getController();

			$referer = Router::parse( $Controller->referer( null, true ) );
			$here = Router::parse( $Controller->request->here( false ) );

			$sessionKey = $this->sessionKey();
			if( $referer !== $here ) {
				return $this->Session->write( $sessionKey, $referer );
			}

			return true;
		}

		/**
		 * Redirige vers l'URL du referer sauvegardée par la méthode save()
		 * ou vers l'URL passée en paramètre.
		 *
		 * @param string|array $default
		 */
		public function redirect( $default = null ) {
			$Controller = $this->_Collection->getController();
			$sessionKey = $this->sessionKey();

			if( $this->Session->check( $sessionKey ) ) {
				$referer = $this->Session->read( $sessionKey );
				$this->Session->delete( $sessionKey );

				if( isset( $referer['pass'] ) ) {
					$pass = $referer['pass'];
					unset( $referer['pass'] );
					$referer += $pass;
				}

				if( isset( $referer['named'] ) ) {
					$named = $referer['named'];
					unset( $referer['named'] );
					$referer += $named;
				}
			}
			else {
				$referer = $default;
			}

			return $Controller->redirect( $referer );
		}
	}
?>