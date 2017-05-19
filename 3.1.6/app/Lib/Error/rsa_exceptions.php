<?php
	/**
	 * Les classes d'exceptions de WebRSA.
	 *
	 * PHP 5.3
	 *
	 * @package app.Lib.Error
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe de base des exceptions de WebRSA.
	 *
	 * @package app.Lib.Error
	 */
	abstract class RsaException extends CakeException
	{

		/**
		 * Constructor
		 *
		 * @param type $message
		 * @param type $code
		 * @param type $params
		 */
		public function __construct( $message, $code, $params = array( ) ) {
			parent::__construct( $message, $code );
			if( $params ) {
				$this->params = $params;
			}
		}

	}

	/**
	 * Exception lancée lorsqu'un dossier allocataire est bloqué en écriture par
	 * un autre utilisateur.
	 *
	 * @package app.Lib.Error
	 */
	class LockedDossierException extends RsaException
	{

		/**
		 * Constructor
		 *
		 * @param type $message
		 * @param type $code
		 * @param type $params
		 */
		public function __construct( $message, $code = 401, $params = array( ) ) {
			parent::__construct( $message, $code, $params );
		}

	}

	/**
	 * Exception lancée lorsqu'une action bloquante est déjà effectuée par un
	 * autre utilisateur.
	 *
	 * @package app.Lib.Error
	 */
	class LockedActionException extends RsaException
	{

		/**
		 * Constructor
		 *
		 * @param type $message
		 * @param type $code
		 * @param type $params
		 */
		public function __construct( $message, $code = 401, $params = array( ) ) {
			parent::__construct( $message, $code, $params );
		}

	}

	/**
	 * Exception lancée lorsque les dates d'habilitation de l'utilisateur connecté
	 * ne lui permettent pas de travailler avec l'application.
	 *
	 * @package app.Lib.Error
	 */
	class dateHabilitationUserException extends RsaException
	{

		/**
		 * Constructor
		 *
		 * @param type $message
		 * @param type $code
		 * @param type $params
		 */
		public function __construct( $message, $code = 401, $params = array( ) ) {
			parent::__construct( $message, $code, $params );
		}

	}

	/**
	 * Exception lancée lorsque l'utilisateur n'a pas le droit d'accéder à cette
	 * partie de l'application.
	 *
	 * @package app.Lib.Error
	 */
	class error403Exception extends RsaException
	{

		/**
		 * Constructor
		 *
		 * @param type $message
		 * @param type $code
		 * @param type $params
		 */
		public function __construct( $message, $code = 403, $params = array( ) ) {
			parent::__construct( $message, $code, $params );
		}

	}

	/**
	 * Exception lancée lorsqu'une URL n'existe pas.
	 *
	 * @package app.Lib.Error
	 */
	class error404Exception extends RsaException
	{

		/**
		 * Constructor
		 *
		 * @param type $message
		 * @param type $code
		 * @param type $params
		 */
		public function __construct( $message = null, $code = 404, $params = array( ) ) {
			parent::__construct( $message, $code, $params );
		}

	}

	/**
	 * Exception lancée lorsque l'application se trouve dans un état incohérent.
	 *
	 * @package app.Lib.Error
	 */
	class error500Exception extends RsaException
	{

		/**
		 * Constructor
		 *
		 * @param type $message
		 * @param type $code
		 * @param type $params
		 */
		public function __construct( $message, $code = 500, $params = array( ) ) {
			parent::__construct( $message, $code, $params );
		}

	}

	/**
	 * Exception lancée lorsque l'on essaie d'accéder à une URL contenant un
	 * paramètre qui n'est pas pris en compte ou dont la valeur n'existe pas.
	 *
	 * @package app.Lib.Error
	 */
	class invalidParameterException extends RsaException
	{

		/**
		 * Constructor
		 *
		 * @param type $message
		 * @param type $code
		 * @param type $params
		 */
		public function __construct( $message, $code = 404, $params = array( ) ) {
			parent::__construct( $message, $code, $params );
		}

	}

	/**
	 * Exception lancée lorsqu'un paramètre est erroné.
	 *
	 * @package app.Lib.Error
	 */
	class invalidParamForTokenException extends RsaException
	{

		/**
		 * Constructor
		 *
		 * @param type $message
		 * @param type $code
		 * @param type $params
		 */
		public function __construct( $message, $code = 404, $params = array( ) ) {
			parent::__construct( $message, $code, $params );
		}
	}

	/**
	 * Exception lancée lorsque les plages horaires ne permettent pas à
	 * l'utilisateur de se connecter à l'application.
	 *
	 * @package app.Lib.Error
	 */
	class PlageHoraireUserException extends RsaException
	{

		/**
		 * Constructor
		 *
		 * @param type $message
		 * @param type $code
		 * @param type $params
		 */
		public function __construct( $message, $code = 401, $params = array( ) ) {
			parent::__construct( $message, $code, $params );
		}

	}
?>
