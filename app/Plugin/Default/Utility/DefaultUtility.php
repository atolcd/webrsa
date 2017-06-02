<?php
	/**
	 * Code source de la classe DefaultUtility.
	 *
	 * PHP 5.4
	 *
	 * @package Default
	 * @subpackage Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Router', 'Routing' );
	App::uses( 'DefaultUrl', 'Default.Utility' );

	/**
	 * La classe DefaultUtility ...
	 *
	 * @package Default
	 * @subpackage Utility
	 */
	class DefaultUtility
	{
		/**
		 * Retourne la chaîne de caractères $string dont les occurences de
		 * #Model.champ# ont été remplacées par leur valeur extraite depuis $data.
		 *
		 * @param array $data
		 * @param string $string
		 * @return string
		 */
		public static function evaluateString( array $data, $string ) {
			if( strpos( $string, '#' ) !== false ) {
				$pattern = '/("#[^#]+#"|\'#[^#]#\'|#[^#]+#)/';
				if( preg_match_all( $pattern, $string, $out ) ) {
					$tokens = $out[0];
					foreach( array_unique( $tokens ) as $token ) {
						// Pour échapper efficacement les guillemets simples et doubles
						if( $token[0] === '"' ) {
							$escape = '"';
							$token = trim( $token, '"' );
						}
						else if( $token[0] === "'" ) {
							$escape = "'";
							$token = trim( $token, "'" );
						}
						else {
							$escape = false;
						}

						$token = trim( $token, '#' );
						$value = Hash::get( $data, $token );

						if( false !== $escape ) {
							$value = str_replace( $escape, "\\{$escape}", $value );
						}

						$string = str_replace( "#{$token}#", $value, $string );
					}
				}
				$string = preg_replace( '/^\/\//', '/', $string );
			}

			return $string;
		}

		/**
		 * Retourne le paramètre $mixed dont les occurences de #Model.champ# ont
		 * été remplacées par leur valeur extraite depuis $data.
		 *
		 * @see Hash::get()
		 *
		 * @param array $data
		 * @param string|array $mixed
		 * @return string|array
		 */
		public static function evaluate( array $data, $mixed ) {
			if( is_array( $mixed ) ) {
				$array = array();
				if( !empty( $mixed ) ) {
					foreach( $mixed as $key => $value ) {
						$array[self::evaluateString( $data, $key )] = self::evaluate( $data, $value );
					}
				}
				return $array;
			}

			return self::evaluateString( $data, $mixed );
		}

		/**
		 * Retourn le domaine figurant dans les attributs, ou le domaine par
		 * défaut venant de l'URL.
		 *
		 * @param array $url
		 * @param array $attributes
		 * @return string
		 */
		public static function domain( array $url, array $attributes = array() ) {
			if( isset( $attributes['domain'] ) ) {
				return $attributes['domain'];
			}

			return Inflector::underscore( $url['controller'] );
		}

		/**
		 * Remplace les clés 'title' et 'confirm' des attributs par une traduction
		 * automatique, suivant le domaine (ou le nom du contrôleur) si celles-ci
		 * sont à vrai.
		 *
		 * @param array $url
		 * @param array $attributes
		 * @return array
		 */
		public static function attributes( array $url, array $attributes = array() ) {
			$domain = self::domain( $url, $attributes );
			$path = DefaultUrl::toString( $url );

			if( isset( $attributes['title'] ) && $attributes['title'] === true ) {
				$attributes['title'] = __d( $domain, "{$path}/:title" );
			}

			if( isset( $attributes['confirm'] ) && $attributes['confirm'] === true ) {
				$attributes['confirm'] = __d( $domain, "{$path} ?" );
			}

			return $attributes;
		}

		/**
		 * Retourne le texte par défaut du lien d'une URL.
		 *
		 * @param array $url Une URL complète (@see DafultUrll::toArray())
		 * @return string
		 */
		public static function msgid( array $url ) {
			$controller = Inflector::camelize( $url['controller'] );
			$plugin = Hash::get( $url, 'plugin' );
			if( !empty( $plugin ) ) {
				$controller = Inflector::camelize( $plugin ).".{$controller}";
			}

			$action = $url['action'];
			$prefix = Hash::get( $url, 'prefix' );
			if( !empty( $prefix ) && isset( $url[$url['prefix']] ) && $url[$url['prefix']] ) {
				$action = "{$prefix}_{$action}";
			}

			return "/{$controller}/{$action}";
		}

		/**
		 * Une méthode fourre-tout... à remplacer.
		 *
		 * @deprecated Remplacé par {@link DefaultUrl::toArray()},
		 * {@link #attributes()}, {@link #evaluate()}, {@link #domain()},
		 * {@link #msgid()}.
		 *
		 * @param string $path
		 * @param array $htmlAttributes
		 * @param array $data
		 * @return array
		 */
		public static function linkParams( $path, array $htmlAttributes, array $data = array() ) {
			$url = $path;

 			if( !empty( $data ) ) {
				$url = self::evaluate( $data, $url );
			}
			$url = DefaultUrl::toArray( $url );

			$controller = Hash::get( $url, 'controller' );
			$action = Hash::get( $url, 'action' );
			$plugin = Hash::get( $url, 'plugin' );
			$prefix = Hash::get( $url, 'prefix' );

			// -----------------------------------------------------------------

			$htmlAttributes = self::evaluate( $data, $htmlAttributes );

			$domain = self::domain( $url, $htmlAttributes );

			$msgid = Hash::get( $htmlAttributes, 'msgid' );
			unset( $htmlAttributes['msgid'] );

			if( $msgid === null ) {
				$msgid = '/'
					.implode(
						'.',
						Hash::filter(
							array(
								Inflector::camelize( $plugin ),
								Inflector::camelize( $controller )
							)
						)
					)
					.'/'.( !empty( $prefix ) ? "{$prefix}_" : null ).$action;
			}

			if( isset( $htmlAttributes['title'] ) && $htmlAttributes['title'] === true ) {
				$htmlAttributes['title'] = self::evaluate( $data, __d( $domain, $path ) );
			}
			if( isset( $htmlAttributes['confirm'] ) && $htmlAttributes['confirm'] === true ) {
				$htmlAttributes['confirm'] = self::evaluate( $data, __d( $domain, "{$path} ?" ) );
			}

			return array(
				__m($msgid),
				$url,
				$htmlAttributes
			);
		}
	}
?>