<?php
	/**
	 * Code source de la classe PasswordFactory.
	 *
	 * PHP 5.3
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe PasswordFactory ...
	 *
	 * @package app.Utility
	 */
	abstract class PasswordFactory
	{
		/**
		 * Préfixe de la clé de configuration.
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
			'generators' => array( 'default' => 'Password.PasswordPassword' ),
			'checkers' => array( 'default' => 'Password.PasswordAnssi' )
		);

		/**
		 * Liste des alias et des classes permettant la vérification de mots de passe.
		 *
		 * @var array
		 */
		protected static $_checkers = array();

		/**
		 * Liste des alias et des classes permettant la génération de mots de passe.
		 *
		 * @var array
		 */
		protected static $_generators = array();

		/**
		 *
		 * @param array $options
		 * @return array
		 */
		public static function options( array $options = array() ) {
			$defaults = (array)Configure::read( static::$configureKeyPrefix ) + static::$_defaults;

			foreach( array_keys( static::$_defaults ) as $key ) {
				$options[$key] = false === isset( $options[$key] ) ? array() : $options[$key];
				$options[$key] += $defaults[$key];
			}

			return $options;
		}

		/**
		 *
		 * @param string $name
		 * @param array $options
		 * @return Interface
		 * @throws RuntimeException
		 */
		public static function generator( $name = 'default', array $options = array() ) {
			$options = static::options( $options );

			if( false === isset( static::$_generators[$name] ) ) {
				if( false === isset( $options['generators'][$name] ) ) {
					$message = '@fixme';
					throw new RuntimeException( $message, 500 );
				}

				list($pluginName, $className) = pluginSplit( $options['generators'][$name], true );
				App::uses( $className, "{$pluginName}Utility" );

				// @todo: si la classe n'existe pas
				// @todo: et si l'interface...

				static::$_generators[$name] = new $className();
			}

			return static::$_generators[$name];
		}

		/**
		 *
		 * @param type $name
		 * @param array $options
		 * @return Interface
		 * @throws RuntimeException
		 */
		public static function checker( $name = 'default', array $options = array() ) {
			$options = static::options( $options );

			if( false === isset( static::$_checkers[$name] ) ) {
				if( false === isset( $options['checkers'][$name] ) ) {
					$message = '@fixme';
					throw new RuntimeException( $message, 500 );
				}

				list($pluginName, $className) = pluginSplit( $options['checkers'][$name], true );
				App::uses( $className, "{$pluginName}Utility" );

				// @todo: si la classe n'existe pas
				// @todo: et si l'interface...

				static::$_checkers[$name] = new $className();
			}

			return static::$_checkers[$name];
		}
	}
?>