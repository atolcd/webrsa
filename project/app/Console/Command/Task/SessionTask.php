<?php
	/**
	 * Code source de la classe SessionTask.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Shell', 'Console/Command' );
	App::uses( 'CakeSession', 'Model/Datasource' );

	/**
	 * La classe SessionTask ...
	 *
	 * @package app.Console.Command
	 */
	class SessionTask extends Shell
	{
		public function id( $id = null ) {
			if( empty( $id ) ) {
				CakeSession::start();
			}
			return CakeSession::id( $id );
		}

		public function read( $name = null ) {
			return CakeSession::read( $name );
		}

		public function write( $name, $value = null ) {
			return CakeSession::write( $name, $value );
		}

		public function clear($renew = true) {
			return CakeSession::clear($renew);
		}
	}
?>