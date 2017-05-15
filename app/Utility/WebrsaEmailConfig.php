<?php
    /**
     * Code source de la classe WebrsaEmail.
     *
     * PHP 5.3
     *
     * @package app.Utility
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */

    /**
	 * La classe WebrsaEmail fournit des méthodes utilitaires concernant les
	 * configurations des emails.
     *
     * @package app.Utility
     */
	class WebrsaEmailConfig
	{
		protected static $_config = null;

		/**
		 * Retourne la liste des noms de configurations disponibles pour l'envoi
		 * des mails.
		 *
		 * @return array
		 * @throws InternalErrorException
		 */
		public static function keys() {
			if( !config( 'email' ) ) {
				throw new InternalErrorException( "Le fichier de configuration de mail app/Config/email.php n'est pas présent." );
			}

			$configs = new EmailConfig();
			return array_keys( get_object_vars( $configs ) );
		}

		/**
		 * Retourne le nom de la configuration à utiliser.
		 * Retourne 'default' par défaut (par exemple si la configuration n'est
		 * pas présente).
		 *
		 * @param string $name
		 * @return string
		 */
		public static function getName( $name = null ) {
			if( is_null( $name ) ) {
				$name = 'default';
			}

			$configs = self::keys();
			if( in_array( $name, $configs ) ) {
				return $name;
			}
			else if( in_array( 'default', $configs ) ) {
				return 'default';
			}

			throw new InternalErrorException( "La configuration de mail '{$name}' n'est pas paramétrée dans le fichier app/Config/email.php, la configuration 'default' n'est pas présente non plus." );
		}

		/**
		 * Retourne la valeur de la clé pour la configuration demandée, ou la
		 * valeur fournie en paramètre si la valeur recherchée n'existe pas ou
		 * si la configuration demandée n'existe pas.
		 *
		 * @param string $configName Le nom de la configuration
		 * @param string $key La clé demandée
		 * @param mixed $value La valeur à retourner lorsque la clé n'existe pas.
		 * @param boolean $force Doit-on forcer la relecture des configurations ?
		 * @return mixed
		 */
		public static function getValue( $configName, $key, $value, $force = false ) {
			if( is_null( self::$_config ) || $force ) {
				self::$_config = new EmailConfig();
			}

			if( isset( self::$_config->{$configName}[$key] ) ) {
				return self::$_config->{$configName}[$key];
			}

			return $value;
		}

		/**
		 * Permet de savoir si l'envoi de mail se fait en "mode test" (le
		 * destinataire sera le même que l'expéditeur) ou en "mode production".
		 *
		 * Lorsque le serveur est "localhost" ou "qualif.webrsa.test.adullact.org",
		 * la méthode retournera de toutes façons true.
		 *
		 * @see Configure::write( 'debug', ... )
		 * @see Configure::write( 'WebrsaEmailConfig.testEnvironments', ... )
		 *
		 * @return boolean
		 */
		public static function isTestEnvironment() {
			$environments = array_merge(
				array( 'localhost', 'qualif.webrsa.test.adullact.org' ),
				(array)Configure::read( 'WebrsaEmailConfig.testEnvironments' )
			);

			return ( Configure::read( 'debug' ) > 0 )
				|| in_array( env( 'SERVER_NAME' ), $environments );
		}
	}
?>