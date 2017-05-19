<?php
	/**
	 * Code source de la classe PasswordPassword.
	 *
	 * PHP 5.3
	 *
	 * @package Password.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Inspiration:
	 * @url http://bakery.cakephp.org/articles/deldan/2010/09/22/password-generator
	 * @url http://deldan.com/2010/02/componente-cakephp-generador-de-contrasena-aleatorio/
	 * @url http://maord.com/
	 *
	 * @package Password.Utility
	 */
	class PasswordPassword
	{
		/**
		 * Préfixe de la clé de configuration.
		 *
		 * @see PasswordPassword::options()
		 *
		 * @var string
		 */
		public static $configureKeyPrefix = 'Password';

		/**
		 * Options par défaut.
		 *
		 * @var array
		 */
		protected static $_defaults = array(
			'length' => 8,
			'typesafe' => true,
			'class_number' => true,
			'class_lower' => true,
			'class_upper' => true,
			'class_symbol' => true
		);

		/**
		 * Les caractères possibles, par classe.
		 *
		 * @var array
		 */
		protected static $_possibles = array(
			'class_number' => '0123456789',
			'class_lower' => 'abcdefghijklmnopqrstuvwxyz',
			'class_upper' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
			'class_symbol' => ',;.!?*+-'
		);

		/**
		 * Les caractères qui ne peuvent pas être employés avec l'option typesafe
		 * à true, par classe.
		 *
		 * @var array
		 */
		protected static $_unsafe = array(
			'class_number' => array( '0', '1' ),
			'class_lower' => array( 'l', 'i', 'o' ),
			'class_upper' => array( 'I', 'O' )
		);

		/**
		 * Retourne les options qui seront utilisées dans les méthodes possibles
		 * et generate: les options par défaut, surchargées par ce qui est lu
		 * dans la configuration, surchargées par ce qui est envoyé en paramètre.
		 *
		 * @param array $options Les options envoyées en paramètre
		 * @return array
		 */
		public static function options(array $options = array()) {
			$options += (array)Configure::read( static::$configureKeyPrefix ) + static::$_defaults;
			return array_intersect_key( $options, static::$_defaults );
		}

		/**
		 * Retourne un tableau contenant les classes de caractères possibles,
		 * suivant les $options passées en paramètres.
		 *
		 * @see PasswordPassword::options()
		 *
		 * @param array $options
		 * @return array
		 */
		public static function possibles( array $options = array() ) {
			$options = static::options( $options );
			$possibles = array();

			foreach( static::$_possibles as $class => $symbols ) {
				if( true === $options[$class] ) {
					$possibles[$class] = $symbols;
				}
			}

			if( true === $options['typesafe'] ) {
				foreach( static::$_unsafe as $class => $chars ) {
					if( isset( $possibles[$class] ) ) {
						$possibles[$class] = str_replace( $chars, '', $possibles[$class] );
					}
				}
			}

			return array_filter($possibles);
		}

		/**
		 * Retourne un mot de passe généré aléatoirement.
		 *
		 * @see PasswordPassword::options()
		 *
		 * @param array $options
		 * @return string
		 * @throws RuntimeException
		 */
		public static function generate(array $options = array()) {
			$options = static::options($options);

			$classes = static::possibles($options);

			if( $options['length'] < count( $classes ) ) {
				$msgid = 'Impossible de générer un mot de passe de %d caractère(s) contenant obligatoirement un élément de %d classe(s)';
				$message = sprintf( $msgid, $options['length'], count( $classes ) );
				throw new RuntimeException( $message, 500 );
			}

			$password = '';
			foreach( $classes as $class => $value ) {
				if( 0 === strpos($class, 'class_') ) {
					$password .= mb_substr( mb_str_shuffle( $classes[$class] ), 0, 1 );
				}
			}

			$remaining = $options['length'] - mb_strlen( $password );
			$alphabet = implode( '', $classes );
			$alphabet = str_repeat($alphabet, ceil($remaining/mb_strlen($alphabet)));
			return mb_str_shuffle( $password.mb_substr( mb_str_shuffle($alphabet) , 0 , $remaining ) );
		}
	}
?>