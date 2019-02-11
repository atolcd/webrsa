<?php
	/**
	 * Code source de la classe SilencedError.
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe SilencedError permet d'effectuer un appel de fonction tout en
	 * désactivant les erreurs de manière propre.
	 *
	 * @todo Remplacer les appels @<function> dans l'application, utiliser PHPCS
	 * pour les trouver.
	 *
	 * @package app.Utility
	 */
	abstract class SilencedError
	{
		/**
		 * Les constantes d'erreur et leur nom.
		 *
		 * @var array
		 */
		protected static $_severities = array(
			E_ERROR => 'E_ERROR',
			E_WARNING => 'E_WARNING',
			E_PARSE => 'E_PARSE',
			E_NOTICE => 'E_NOTICE',
			E_CORE_ERROR => 'E_CORE_ERROR',
			E_CORE_WARNING => 'E_CORE_WARNING',
			E_COMPILE_ERROR => 'E_COMPILE_ERROR',
			E_COMPILE_WARNING => 'E_COMPILE_WARNING',
			E_USER_ERROR => 'E_USER_ERROR',
			E_USER_WARNING => 'E_USER_WARNING',
			E_USER_NOTICE => 'E_USER_NOTICE',
			E_STRICT => 'E_STRICT',
			E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
			E_DEPRECATED => 'E_DEPRECATED',
			E_USER_DEPRECATED => 'E_USER_DEPRECATED'
		);

		/**
		 * Retourne le nom de la constante d'erreur.
		 *
		 * @param integer $errno
		 * @return string
		 */
		public static function name( $errno ) {
			if( true === isset( self::$_severities[$errno] ) ) {
				return self::$_severities[$errno];
			}

			return null;
		}

		/**
		 * Gestionnaire d'erreur permettant de transformer une erreur en une
		 * exception.
		 *
		 * @param int $errno
		 * @param string $errstr
		 * @param string $errfile
		 * @param int $errline
		 * @param array $errcontext
		 * @return bool
		 */
		public static function handler( $errno, $errstr, $errfile, $errline, array $errcontext ) {
			// error was suppressed with the @-operator
			if( 0 === error_reporting() ) {
				return false;
			}

			throw new ErrorException( self::name( $errno ).': '.$errstr, 500, $errno, $errfile, $errline );
		}

		/**
		 * Fonction permettant d'effectuer un appel à call_user_func_array de
		 * manière propre au niveau de la gestion des erreurs.
		 *
		 * @param callable $callback Le callback à appeler
		 * @param array $param_arr L'array de paramètres à envoyer au callback
		 * @param bool $throw Doit-on lancer une exception en cas d'erreur ?
		 * @return mixed
		 * @throws Exception
		 */
		public static function call( $callback, array $param_arr, $throw = false ) {
			set_error_handler( get_called_class().'::handler' );

			try {
				$result = call_user_func_array( $callback, $param_arr );
				restore_error_handler();
			}
			catch( Exception $exception ) {
				restore_error_handler();
				$result = null;
				if( true === $throw ) {
					throw $exception;
				}
			}

			return $result;
		}

	}
?>