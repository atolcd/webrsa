<?php
	/**
	 * Code source de la classe DefaultUrl.
	 *
	 * PHP 5.4
	 *
	 * @package Default
	 * @subpackage Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Router', 'Routing' );

	/**
	 * La classe DefaultUrl permet de faire aisément des conversions entre
	 * représentations d'URL.
	 *
	 * Il existe 3 représentations d'URL:
	 *	- un array (comme dans HtmlHelper::link())
	 *	- un objet CakeRequest
	 *  - une chaîne de caractères du type: /Plugin.Controllers/prefix_action/param1/named:value/#anchor
	 *
	 * @package Default
	 * @subpackage Utility
	 */
	class DefaultUrl
	{
		/**
		 * Permet d'obtenir une url sous la forme d'un array avec les clés suivante:
		 * prefix, plugin, controller, action, <prefix>, named, pass.
		 *
		 * @param string|array $url
		 * @return array
		 */
		public static function parse( $url ) {
			if( !is_array( $url ) ) {
				$parsed = Router::parse( $url );
			}
			else {
				$request = (array)Router::getRequest();

				$parsed = $url + array(
					'plugin' => $request['params']['plugin'],
					'controller' => $request['params']['controller'],
					'action' => $request['params']['action'],
					'prefix' => Hash::get( $request, 'params.prefix' )
				);

				$named = array();
				$pass = array();
				foreach( $parsed as $key => $value ) {
					if( !is_string( $key ) || !in_array( $key, array( 'controller', 'action', 'prefix', 'plugin' ) ) ) {
						if( is_integer( $key ) ) {
							$pass[] = $value;
							unset( $parsed[$key] );
						}
						else if( !isset( $parsed['prefix'] ) || $parsed['prefix'] != $key ) {
							$named[$key] = $value;
							unset( $parsed[$key] );
						}
					}
				}
				$parsed['named'] = $named;
				$parsed['pass'] = $pass;
			}

			return $parsed;
		}

		/**
		 * Transforme une URL à partir d'un array vers une représentation sous
		 * forme de chaîne de caractères.
		 *
		 * Il s'agit de la méthode inverse de toArray().
		 *
		 * Exemple:
		 * <pre>
		 * $expected = array(
		 * 	'prefix' => 'admin',
		 * 	'plugin' => 'acl_extras',
		 * 	'controller' => 'users',
		 * 	'action' => 'index',
		 * 	0 => 'category',
		 * 	'admin' => true,
		 * 	'Search__active' => '1',
		 * 	'Search__User__username' => 'admin',
		 * 	'#' => 'content'
		 * );
		 *
		 * donnera
		 *
		 * '/AclExtras.Users/admin_index/category/Search__active:1/Search__User__username:admin/#content'
		 * </pre>
		 *
		 * @param string|array $url
		 * @return string
		 */
		public static function toString( $url ) {
			$parsed = self::parse( $url );

			if( empty( $parsed ) ) {
				return $url;
			}

			$parsed['controller'] = Inflector::camelize( $parsed['controller'] );
			if( !empty( $parsed['plugin'] ) ) {
				$parsed['controller'] = Inflector::camelize( $parsed['plugin'] ).'.'.$parsed['controller'];
			}
			if( isset( $parsed['prefix'] ) && !empty( $parsed['prefix'] ) && !empty( $parsed[$parsed['prefix']] ) ) {
				$parsed['action'] = $parsed['prefix'].'_'.$parsed['action'];
			}

			$return = "/{$parsed['controller']}/{$parsed['action']}";

			if( !empty( $parsed['pass'] ) ) {
				$return .= '/'.implode( '/', $parsed['pass'] );
			}

			if( !empty( $parsed['named'] ) ) {
				$hash = ( isset( $parsed['named']['#'] ) ? "#{$parsed['named']['#']}" : null );
				unset( $parsed['named']['#'] );
				$return .= '/'.str_replace( '=', ':', http_build_query( $parsed['named'], null, '/' ) ).$hash;
			}

			return $return;
		}

		/**
		 * Les actions sont-elles préfixées avec une valeur figurant dans
		 * Routing.prefixes ? Si c'est le cas, les clés prefix et action de l'url
		 * sont modifiées.
		 *
		 * @param array $url
		 * @return array
		 */
		protected static function _prefixSplit( array $url ) {
			$strpos = strpos( $url['action'], '_' );
			if( $strpos !== false ) {
				$url['prefix'] = substr( $url['action'], 0, $strpos );

				if( in_array( $url['prefix'], (array)Configure::read( 'Routing.prefixes' ) ) ) {
					$url['action'] = substr( $url['action'], $strpos + 1 );
					$url[$url['prefix']] = true;
				}
				else {
					unset( $url['prefix'] );
				}
			}

			return $url;
		}

		/**
		 * Transforme les paramètres nommés de CakePHP ( ex. array( 'Foo:bar' )
		 * devient ( 'Foo' => 'bar' ) ) et le cas spécial de clé hash
		 * (ex. array( '#' => 'content' ) ).
		 *
		 * @param array $url
		 * @return array
		 */
		protected static function _extractNamedAndHash( array $url ) {
			foreach( $url as $key => $value ) {
				// CakePHP named parameters
				if( is_numeric( $key ) && preg_match( '/^([^:]*):(.*)$/', $value, $matches ) ) {
					unset( $url[$key] );
					$url[$matches[1]] = $matches[2];
				}

				// Traitement d'un hash dans les valeurs, ex: array( 0 => '6#content' ) => array( 0 => '6', #' => 'content' )
				if( strpos( $value, '#' ) !== false ) {
					if( preg_match( '/^([^#]*)#([^#]*)$/', $value, $matches ) ) {
						$url[$key] = $matches[1];
						$url['#'] = $matches[2];
					}

					if( preg_match( '/^#(.*)$/', $value, $matches ) ) {
						unset( $url[$key] );
						$url['#'] = $matches[1];
					}
				}
			}

			return $url;
		}

		/**
		 * Transforme une URL à partir d'une de chaîne de caractères vers une
		 * représentation sous forme d'array.
		 *
		 * Il s'agit de la méthode inverse de toString().
		 *
		 * Exemple:
		 * <pre>
		 * '/AclExtras.Users/admin_index/category/Search__active:1/Search__User__username:admin/#content'
		 *
		 * donnera
		 *
		 * array(
		 *	'plugin' => 'acl_extras',
		 *	'controller' => 'users',
		 *	'action' => 'index',
		 *	'prefix' => 'admin',
		 *	'admin' => true,
		 *	0 => 'category',
		 *	'Search__active' => true,
		 *	'#' => 'content',
		 * );
		 * </pre>
		 *
		 * @param string $path
		 * @return array
		 */
		public static function toArray( $path ) {
			$tokens = explode( '/', $path ) + array( null, null, null );

			list( $plugin, $controller ) = pluginSplit( $tokens[1] );

			if( strpos( $tokens[2], '#' ) !== false ) {
				if( preg_match( '/^([^#]*)#(#[^#]+#.*)$/', $tokens[2], $matches ) ) {
					$tokens[2] = $matches[1];
					$tokens[] = "#{$matches[2]}";
				}
			}

			$url = array(
				'plugin' => ( empty( $plugin ) ? null : Inflector::underscore( $plugin ) ),
				'controller' => Inflector::underscore( $controller ),
				'action' => $tokens[2],
			) + array_slice( $tokens, 3 );

			$url = self::_prefixSplit( $url );
			$url = self::_extractNamedAndHash( $url );

			return $url;
		}
	}
?>