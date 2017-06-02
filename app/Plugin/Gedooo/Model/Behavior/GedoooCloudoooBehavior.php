<?php
	/**
	 * Fichier source de la classe GedoooCloudoooBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package Gedooo
	 * @subpackage Model.Behavior
	 */
	App::uses( 'GedoooFusionConverterBehavior', 'Gedooo.Model/Behavior' );

	/**
	 * La classe GedoooCloudoooBehavior fournit une méthode de conversion de
	 * document ODT au format PDF en utilisant le serveur de conversion Cloudooo.
	 *
	 * @package Gedooo
	 * @subpackage Model.Behavior
	 */
	class GedoooCloudoooBehavior extends GedoooFusionConverterBehavior
	{
		/**
		 *
		 * @param string $fileName
		 * @param string $format
		 * @return string
		 */
		public function gedConversion( Model $model, $fileName, $format ) {
			// FIXME: http://pear.php.net/manual/en/package.webservices.xml-rpc.examples.php -> vérifier la présecence
			// pear upgrade
			// pear install xml_rpc / var_dump(class_exists('System', false));

			include_once 'XML/RPC.php'; // INFO: extension pear/pecl ?

			$content = base64_encode( file_get_contents( $fileName ) );

			$fileinfo = pathinfo( $fileName );
			$extension = preg_replace( '/^(odt|pdf).*/', 'odt', $fileinfo['extension'] );

			$params = array(
				new XML_RPC_Value( $content, 'string' ),
				new XML_RPC_Value( $extension, 'string' ),
				new XML_RPC_Value( $format, 'string' ),
				new XML_RPC_Value( false, 'boolean' ),
				new XML_RPC_Value( true, 'boolean' )
			);

			$url = Configure::read( 'Gedooo.cloudooo_host' ).':'.Configure::read( 'Gedooo.cloudooo_port' );

			$msg = new XML_RPC_Message( 'convertFile', $params );
			$cli = new XML_RPC_Client( '/', $url );
			$resp = $cli->send( $msg );

            if( empty( $resp ) ) {
                $this->log( sprintf( "Erreur dans la réponse du serveur Cloudooo: %s (serveur: %s)", $cli->errstr, $url ), LOG_ERROR );
                return false;
            }

            if( empty( $resp->xv ) ) {
                $this->log( sprintf( "Erreur dans la réponse du serveur Cloudooo: %s (serveur: %s)", $resp->fs, $url ), LOG_ERROR );
                return false;
            }

			// FIXME: PHP Notice:  Trying to get property of non-object in /home/cbuffin/www/webrsa/trunk/app/plugins/gedooo/models/behaviors/gedooo_cloudooo.php on line 42
			return base64_decode( @$resp->xv->me['string'] );
		}

		/**
		 * Retourne la liste des clés de configuration.
		 *
		 * @return array
		 */
		public function gedConfigureKeys( Model $model ) {
			return array_merge(
				parent::gedConfigureKeys( $model ),
				array(
					'Gedooo.cloudooo_host' => 'string',
					'Gedooo.cloudooo_port' => 'string'
				)
			);
		}

		/**
		 * @return array
		 */
		public function gedTests( Model $model ) {
			App::uses( 'Check', 'Appchecks.Model' );
			$Check = ClassRegistry::init( 'Appchecks.Check' );

			$results = parent::gedTests( $model );
			$results['ping_cloudooo'] = $Check->socket( Configure::read( 'Gedooo.cloudooo_host' ), Configure::read( 'Gedooo.cloudooo_port' ) );

			return $results;
		}
	}
?>