<?php
	/**
	 * Code source de la classe AppExceptionRenderer.
	 *
	 * PHP 5.3
	 *
	 * @package app.Lib.Error
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ExceptionRenderer', 'Error' );

	/**
	 * La classe AppExceptionRenderer ...
	 *
	 * @package app.Lib.Error
	 */
	class AppExceptionRenderer extends ExceptionRenderer
	{
		/**
		 *
		 * @param CakeException $error
		 * @param string $template
		 */
		protected function _generic( $error, $template ) {
			$message = $error->getMessage();
			if( Configure::read( 'debug' ) == 0 && $error instanceof CakeException ) {
				$message = __d( 'cake', 'Not Found' );
			}
			$url = $this->controller->request->here();
			$this->controller->response->statusCode( $error->getCode() );
			$this->controller->set( array(
				'name' => $message,
				'url' => h( $url ),
				'error' => $error,
				'_serialize' => array( 'name', 'url' )
			) );
			$this->_outputMessage( $template );
		}

		/**
		 * Convenience method to display a 403 series page.
		 *
		 * @param Exception $error
		 * @return void
		 */
		public function error403( $error ) {
			$this->_generic( $error, 'error403' );
		}

		/**
		 * Convenience method to display a 404 series page.
		 *
		 * @param Exception $error
		 * @return void
		 */
		public function error404( $error ) {
			$this->_generic( $error, 'error404' );
		}

		/**
		 * Convenience method to display a 500 series page.
		 *
		 * @param Exception $error
		 * @return void
		 */
		public function error500( $error ) {
			$this->_generic( $error, 'error500' );
		}

		/**
		 * Convenience method to display a 404 series page.
		 *
		 * @param Exception $error
		 * @return void
		 */
		public function notFound( $error ) {
			$this->error404( $error );
		}

		/**
		 * Convenience method to display a 404 series page.
		 *
		 * @param Exception $error
		 * @return void
		 */
		public function invalidParameter( $error ) {
			$this->error404( $error );
		}

		/**
		 * Convenience method to display a 404 series page.
		 *
		 * @param Exception $error
		 * @return void
		 */
		public function invalidParamForToken( $error ) {
			$this->error404( $error );
		}

		/**
		 * Convenience method to display a 404 series page.
		 *
		 * @param Exception $error
		 * @return void
		 */
		public function lockedDossier( $error ) {
			$this->_generic( $error, 'locked_dossier' );
		}

		/**
		 * Convenience method to display a 404 series page.
		 *
		 * @param Exception $error
		 * @return void
		 */
		public function lockedAction( $error ) {
			$this->_generic( $error, 'locked_action' );
		}

		/**
		 * Convenience method to display a 404 series page.
		 *
		 * @param Exception $error
		 * @return void
		 */
		public function dateHabilitationUser( $error ) {
			$this->_generic( $error, 'date_habilitation_user' );
		}

		/**
		 * Rendu de la vue app/View/Errors/plage_horaire_user.ctp lorsque l'on
		 * est en mode "production".
		 *
		 * @param Exception $error
		 * @return void
		 */
		public function plageHoraireUser( $error ) {
			$this->_generic( $error, 'plage_horaire_user' );
		}
	}
?>
