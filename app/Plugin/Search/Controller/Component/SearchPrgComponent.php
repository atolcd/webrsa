<?php
	/**
	 * Code source de la classe SearchPrgComponent.
	 *
	 * PHP 5.3
	 *
	 * @package Search
	 * @subpackage Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * La classe SearchPrgComponent gère le POST/redirect/GET pour certaines actions du
	 * contrôleur, avec la possibilité de filtrer les parties du POST à mettre
	 * dans l'URL ou dans la Session.
	 *
	 * <pre>
	 * public $components = array(
	 * 	'Search.SearchPrg' => array(
	 * 		'actions' => array( 'index' => array( 'filter' => 'Search' ) ),
	 *	)
	 * );
	 * </pre>
	 *
	 * @package Search
	 * @subpackage Controller.Component
	 */
	class SearchPrgComponent extends Component
	{
		/**
		 * Nom du component.
		 *
		 * @var string
		 */
		public $name = 'SearchPrg';

		/**
		 * Components utilisés par ce component-ci.
		 *
		 * @var array
		 */
		public $components = array( 'Session', 'RequestHandler' );

		/**
		 * Called before the Controller::beforeFilter().
		 *
		 * @param Controller $controller Controller with components to initialize
		 * @param array $settings
		 * @return void
		 */
		public function initialize( Controller $controller ) {
			parent::initialize( $controller );

			if( isset( $this->settings['actions'] ) ) {
				$this->settings = Hash::normalize( (array)$this->settings['actions'] );
			}
		}

		/**
		 * Permet de supprimer certains caractères pour la redirection.
		 *
		 * @param array $params
		 * @param array $forbiddenlist
		 * @return array
		 */
		protected function _urlencodeParams( $params, $forbiddenlist = array( '?', '/', ':', '&' ) ) {
			foreach( $params as $key => $param ) {
				foreach( $forbiddenlist as $forbidden ) {
					$param = str_replace( $forbidden, ' ', $param );
				}

				if( !is_array( $param ) ) {
					$params[$key] = urlencode( $param );
				}
				else {
					$params[$key] = $param;
				}
			}

			return $params;
		}

		/**
		 * Permet de savoir si une action doit être effectuée.
		 *
		 * @return boolean
		 */
		public function hasWork() {
			$Controller = $this->_Collection->getController();

			return (
				empty( $Controller->request->params['form'] )
				&& in_array( $Controller->action, array_keys( $this->settings ) )
				&& ( !isset( $this->settings[$Controller->action]['ajax'] ) || ( $this->settings[$Controller->action]['ajax'] == $Controller->request->is( 'ajax' ) ) )
			);
		}

		/**
		 * Lorsque l'on est appelé en POST, on stocke certaines données en session,
		 * puis on transforme les données POST en données GET et on redirige.
		 */
		protected function _postStartup() {
			$Controller = $this->_Collection->getController();

			$params = $Controller->request->data;

			if( isset( $this->settings[$Controller->action]['filter'] ) ) {
				$key = $this->settings[$Controller->action]['filter'];
				$sessionParams = $params;
				$params = array( $key => ( isset( $params[$key] ) ? $params[$key] : array( ) ) );
				unset( $sessionParams[$key] );

				if( !empty( $sessionParams ) ) {
					unset( $sessionParams['sessionKey'] );
					$sessionKey = sha1( implode( '/', Hash::flatten( ( empty( $sessionParams ) ? array( ) : $sessionParams ), '__' ) ) );
					$this->Session->write( "{$this->name}.{$Controller->name}__{$Controller->action}.{$sessionKey}", $sessionParams );
					$params['sessionKey'] = $sessionKey;
				}
			}

			$params = Hash::flatten( $params, '__' );
			$params = Hash::merge( $Controller->request->params['named'], $params );
			$params = $this->_urlencodeParams( $params );
			// INFO: évite certaines URL trop longues, comme dans la recherche par fiches de prescription
			$params = Hash::filter( $params );
			if( empty( $params ) ) {
				$filter = Hash::get( $this->settings, "{$Controller->action}.filter" );
				if( false === empty( $filter ) ) {
					$params = array( "{$filter}__active" => true, 'prg' => true );
				}
				else {
					$params = array( 'prg' => true );
				}
			}

			$redirect = array_merge( array( 'action' => $Controller->action ), $params );
			$Controller->redirect( $redirect );
		}

		/**
		 * Lorsque la page est appelée en GET, on ajoute les paramètres GET/CakePHP
		 * et les données se trouvant éventuellement dans sessionKey (la variable
		 * sessionKey est supprimée).
		 */
		protected function _getStartup() {
			$Controller = $this->_Collection->getController();

			$Controller->request->data = Hash::expand( array_map( 'urldecode', $Controller->request->params['named'] ), '__' );

			if( isset( $Controller->request->params['named']['sessionKey'] ) ) {
				$sessionParams = (array)$this->Session->read( "{$this->name}.{$Controller->name}__{$Controller->action}.{$Controller->request->params['named']['sessionKey']}" );

				$this->Session->delete( "{$this->name}.{$Controller->name}__{$Controller->action}.{$Controller->request->params['named']['sessionKey']}" );
				$Controller->request->data = Hash::merge( $Controller->request->data, $sessionParams );
			}
		}

		/**
		 * Called after the Controller::beforeFilter() and before the controller action
		 *
		 * @param Controller $controller Controller with components to startup
		 * @return void
		 */
		public function startup( Controller $controller ) {
			parent::startup( $controller );

			if( $this->hasWork() ) {
				if( $controller->request->is( 'post' ) ) {
					$this->_postStartup();
				}
				else if( $controller->request->is( 'get' ) ) {
					$this->_getStartup();
				}
			}
		}
	}
?>