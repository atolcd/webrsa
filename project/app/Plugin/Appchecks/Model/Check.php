<?php
	/**
	 * Code source de la classe Check.
	 *
	 * PHP 5.3
	 *
	 * @package Appchecks
	 * @subpackage Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	define( 'APPCHECKS_PLUGIN_DIR', dirname( __FILE__ ).DS.'..'.DS );
	require_once  APPCHECKS_PLUGIN_DIR.'Lib'.DS.'basics.php' ;
	require_once  APPCHECKS_PLUGIN_DIR.'Lib'.DS.'xvalidation.php' ;
	App::uses( 'AppModel', 'Model' );
	App::uses( 'Validation', 'Utility' );
	App::uses( 'CakeEmail', 'Network/Email' );

	/**
	 * La classe Check fournit des méthodes de vérification de l'environnement
	 * applicatif.
	 *
	 * PHP 5.3
	 *
	 * @package Appchecks
	 * @subpackage Model
	 */
	class Check extends AppModel
	{
		/**
		 * @var string
		 */
		public $name = 'Check';

		/**
		 * @var string
		 */
		public $useTable = false;

		/**
		 * Fonction la liste des clés de l'objet Configure.
		 * A utiliser lors du développement.
		 *
		 * @return array
		 */
		public function configureKeys() {
			$Configure = Configure::getInstance();
			$Configure = Hash::flatten( $Configure );
			return array_keys( $Configure );
		}

		/**
		 * Vérifie la disponibilité de modules Apache
		 *
		 * @param array $modules Les modules à vérifier.
		 * @param string $message Le gabarit du message à utiliser en cas de non disponibilité.
		 * @return array
		 */
		public function apacheModules( array $modules, $message = "Le module Apache %s n'est pas disponible." ) {
			$loaded = apache_modules();

			$checks = array();
			foreach( $modules as $module ) {
				$success = in_array( $module, $loaded );

				$checks[$module] = array(
					'success' => $success,
					'message' => ( $success ? null : sprintf( $message, $module ) )
				);
			}

			return $checks;
		}

		/**
		 * Vérifie la disponibilité d'extensions PHP.
		 *
		 * @param array $extensions Les extensions à vérifier.
		 * @param string $message Le gabarit du message à utiliser en cas de non disponibilité.
		 * @return array
		 */
		public function phpExtensions( array $extensions, $message = "L'extension PHP %s n'est pas disponible." ) {
			$checks = array();
			foreach( $extensions as $extension ) {
				$success = extension_loaded( $extension );

				$checks[$extension] = array(
					'success' => $success,
					'message' => ( $success ? null : sprintf( $message, $extension ) )
				);
			}

			return $checks;
		}

		/**
		 * Vérifie la configuration de variables dans le php.ini
		 *
		 * @param array $inis Les variables à vérifier, avec règles de validation éventuelles.
		 * @param string $message Le gabarit du message à utiliser en cas de non disponibilité.
		 * @return array
		 */
		public function phpInis( array $inis, $message = "Le paramétrage de %s doit être fait dans le fichier php.ini" ) {
			$checks = array();
			foreach( Set::normalize( $inis ) as $ini => $rules ) {
				$message = null;
				$value = ini_get( $ini );

				if( !empty( $rules ) ) {
					foreach( $rules as $rule => $ruleParams ) {
						if( is_null( $message ) ) {
							$message = $this->_validate( $value, $rule, $ruleParams );
						}
					}
				}
				else {
					$message = ( !empty( $value ) ? null : sprintf( $message, $ini ) );
				}

				$checks[$ini] = array(
					'value' => $value,
					'success' => is_null( $message ),
					'message' => $message
				);
			}

			return $checks;
		}

		/**
		 * Vérifie la présence de binaires sur le système.
		 *
		 * @param array $binaries Les binaires à vérifier.
		 * @param string $message Le gabarit du message à utiliser en cas d'absence.
		 * @return array
		 */
		public function binaries( array $binaries, $message = "Le binaire %s n'est pas accessible sur la système." ) {
			$checks = array();
			foreach( $binaries as $binary ) {
				$which = exec( "which {$binary}" );
				$success = !empty( $which );

				$checks[$binary] = array(
					'success' => $success,
					'message' => ( $success ? null : sprintf( $message, $binary ) )
				);
			}

			return $checks;
		}

		/**
		 * Vérifie si des fichiers sont présents et accessibles en lecture.
		 *
		 * @param array $files Les chemins vers les fichiers à vérifier
		 * @param string $base Le répertoire de base de l'application (en général ROOT.DS)
		 * @return array
		 */
		public function files( array $files, $base = null ) {
			if( !is_null( $base ) ) {
				$base = '/^'.preg_quote( $base, '/' ).'/';
			}
			$checks = array();
			foreach( $files as $file ) {
				if( is_null( $base ) ) {
					$key = $file;
				}
				else {
					$key = preg_replace( $base, '', $file );
				}
				$checks[$key] = $this->filePermission( $file, 'r' );
			}

			return $checks;
		}

		/**
		 * Vérifie les permissions sur un répertoire.
		 *
		 * @todo is_executable
		 *
		 * @param string $directory
		 * @param string $permission
		 * @return array
		 */
		public function directoryPermission( $directory, $permission = 'r' ) {
			if( !in_array( $permission, array( 'r', 'w' ) ) ) {
				trigger_error( sprintf( __( 'Paramètre non permis dans %s::%s: %s. Paramètres permis: \'r\' ou \'w\'' ), __CLASS__, __FUNCTION__, $permission ), E_USER_WARNING );
			}

			$success = true;
			$message = null;
			if( !is_dir( $directory ) ) {
				$success = false;
				$message = "Le dossier {$directory} n'existe pas.";
			}
			else if( !is_readable( $directory ) ) {
				$success = false;
				$message = "Le dossier {$directory} n'est pas lisible.";
			}
			else if( $permission == 'w' && !is_writable( $directory ) ) {
				$success = false;
				$message = "Le dossier {$directory} n'est pas inscriptible.";
			}

			return array(
				'success' => $success,
				'message' => $message
			);
		}

		/**
		 * Vérifie les permissions sur des répertoires.
		 *
		 * @param array $directories
		 * @param string $base Le répertoire de base de l'application (en général ROOT.DS)
		 * @return array
		 */
		public function directories( array $directories, $base = null ) {
			if( !is_null( $base ) ) {
				$base = '/^'.preg_quote( $base, '/' ).'/'; // FIXME
			}
			$checks = array();
			foreach( Set::normalize( $directories ) as $directory => $mode ) {
				if( is_null( $base ) ) {
					$key = $directory;
				}
				else {
					$key = preg_replace( $base, '', $directory );
				}
				$checks[$key] = $this->directoryPermission( $directory, $mode );
			}

			return $checks;
		}

		/**
		 * Vérifie les paermissions sur un fichier.
		 *
		 * @todo is_executable
		 *
		 * @param string $file
		 * @param string $permission
		 * @return array
		 */
		public function filePermission( $file, $permission = 'r' ) {
			if( !in_array( $permission, array( 'r', 'w' ) ) ) {
				trigger_error( sprintf( __( 'Paramètre non permis dans %s::%s: %s. Paramètres permis: \'r\' ou \'w\'' ), __CLASS__, __FUNCTION__, $permission ), E_USER_WARNING );
			}

			$success = true;
			$message = null;
			if( !file_exists( $file ) ) {
				$success = false;
				$message = "Le fichier {$file} n'existe pas.";
			}
			else if( !is_readable( $file ) ) {
				$success = false;
				$message = "Le fichier {$file} n'est pas lisible.";
			}
			else if( $permission == 'w' && !is_writable( $file ) ) {
				$success = false;
				$message = "Le fichier {$file} n'est pas inscriptible.";
			}

			return array(
				'success' => $success,
				'message' => $message
			);
		}

		/**
		 * Vérifie la présence de modèles odt.
		 *
		 * @todo utiliser files avec un préfixe
		 *
		 * @param array $modeles
		 * @param string $prefixPath Le répertoire de base des modèles.
		 * @return array
		 */
		public function modelesOdt( array $modeles, $prefixPath ) {
			$return = array();
			foreach( $modeles as $modele ) {
				$return[$modele] = $this->filePermission( $prefixPath.$modele, 'r' );
			}
			ksort( $return );

			return $return;
		}

		/**
		 * Validation d'une valeur par-rapport à une règle de validation.
		 * Retourne un message d'erreur en cas de non validation, null sinon.
		 *
		 * @param mixed $value La valeur à tester.
		 * @param string $rule Le nom de la règle à vérifier.
		 * @param array $ruleParams Les paramètres à passer à la règle.
		 * @return string
		 */
		protected function _validate( $value, $rule, $ruleParams = array() ) {
			$allowEmpty = $ruleParams['allowEmpty'];
			unset( $ruleParams['allowEmpty'] );
			$message = null;

			if( method_exists( 'Xvalidation', $rule ) || method_exists( 'Validation', $rule ) ) {
				// FIXME: nettoyage des URL contenant %s (pour le CG 58) et des espaces
				$testValue = $value;
				if( $rule == 'url' ) {
					if( stripos( $testValue, '%s' ) !== false ) {
						$testValue = str_replace( '%s', 'XXXX', $testValue );
					}
					if( stripos( $testValue, ' ' ) !== false ) {
						$testValue = str_replace( ' ', '%20', $testValue );
					}
					if( stripos( $testValue, 'http://localhost/' ) !== false ) {
						$testValue = str_replace( 'http://localhost/', 'http://127.0.0.1/', $testValue );
					}
				}

				if( isset( $ruleParams['rule'] ) ) {
					$ruleParams = $ruleParams['rule'];
				}

				if( method_exists( 'Validation', $rule ) ) {
					$Validator =  'Validation';
					$testRuleParams = $ruleParams;

					if( isset( $testRuleParams[0] ) && $testRuleParams[0] == $rule ) {
						$testRuleParams[0] = $testValue;
					}
					else {
						array_unshift( $testRuleParams, $testValue );
					}

					$validate = call_user_func_array( array( $Validator, $rule ), $testRuleParams );
				}
				else if( method_exists( 'Xvalidation', $rule ) ) {
					$Validator =  Xvalidation::getInstance();
					$validate = call_user_func_array( array( $Validator, $rule ), array_merge( array( $testValue ), $ruleParams ) );
				}

				if( !( $allowEmpty && empty( $value ) ) && ( is_null( $value ) || !$validate ) ) {
					$message = "Validate::{$rule}";
					if( isset( $ruleParams[0] ) && $ruleParams[0] == $rule ) {
						unset( $ruleParams[0] );
					}
					$sprintfParams = Set::merge( array( __( $message ) ), $ruleParams );
					for( $i = 1 ; ( $i <= count( $sprintfParams ) - 1 ) ; $i++ ) {
						if( is_array( $sprintfParams[$i] ) ) {
							$sprintfParams[$i] = implode( ', ', $sprintfParams[$i] );
						}
					}
					$message = call_user_func_array( 'sprintf', $sprintfParams );
				}
			}
			else {
				$message = "La méthode de validation {$rule} n'existe pas.";
			}

			return $message;
		}

		/**
		 * Valide la valeur donnée par un chemin de configuration par une règle
		 * de validation.
		 *
		 * @param string $path Le chemin de la configuration.
		 * @param string $rule Le nom de la règle à vérifier.
		 * @param array $ruleParams Les paramètres à passer à la règle.
		 * @return array
		 */
		public function validateConfigurePath( $path, $rule, $ruleParams = array() ) {
			$value = Configure::read( $path );

			$message = $this->_validate( $value, $rule, $ruleParams );

			return array(
				'success' => is_null( $message ),
				'value' => var_export( $value, true ),
				'message' => $message
			);
		}

		/**
		 * Lecture des chemins de configuration et validation (à minima, les
		 * valeurs ne pourront pas être vides).
		 *
		 * @param array $paths Les chemins à vérifier
		 * @return array
		 */
		public function configure( array $paths ) {
			$return = array();
			$defaults = array(
				'allowEmpty' => false,
//				'required' => true,
			);

			foreach( Set::normalize( $paths ) as $path => $rules ) {
				if( !is_array( $rules ) ) {
					$rules = array( array( 'rule' => $rules ) );
				}

				foreach( $rules as $rule ) {
					$rule = Set::merge( $defaults, $rule );
					if( !isset( $return[$path] ) || empty( $return[$path] ) || $return[$path]['success'] ) {
						$ruleParams = $rule;
						unset( $ruleParams['rule'] );
						$validate = $this->validateConfigurePath( $path, $rule['rule'], $ruleParams );
						$return[$path] = $validate;
					}
				}
			}
			ksort( $return );

			return $return;
		}

		/**
		 * Vérifie qu'un logiciel sse trouve dans une version donnée.
		 *
		 * @param string $software Le nom du logiciel
		 * @param string $actual La version du logiciel
		 * @param string $low La version minimale
		 * @param string $high La version maximale
		 * @return array
		 */
		public function version( $software, $actual, $low, $high = null ) {
			$version_difference = version_difference( $actual, $low, $high );

			$message = null;
			if( !$version_difference ) {
				if( is_null( $high ) ) {
					$message = "La version de {$software} doit être au moins {$low}";
				}
				else {
					$message = "La version de {$software} doit être au moins {$low}, mais plus petite que {$high}";
				}
			}

			return array(
				'value' => $actual,
				'success' => $version_difference,
				'message' => $message,
			);
		}

		/**
		 * Retourne la vérification du timeout, avec en message la configuration
		 * utilisée.
		 *
		 * @fixme La fonction readTimeout n'est pas dans le plugin
		 *
		 * @return array
		 */
		public function timeout() {
			$value = readTimeout();
			$message = null;

			if( Configure::read( 'Session.save' ) == 'php' ) {
				$message = '<code>session.gc_maxlifetime</code> dans le <code>php.ini</code> (valeur actuelle: <em>'.ini_get( 'session.gc_maxlifetime' ).'</em> secondes)';
			}
			else if( Configure::read( 'Session.save' ) == 'cake' ) {
				$message = "<code>Configure::write( 'Session.timeout', '".Configure::read( 'Session.timeout' )."' )</code> dans <code>app/config/core.php</code><br/>";
				$message .= "<code>Configure::write( 'Security.level', '".Configure::read( 'Security.level' )."' )</code> dans <code>app/config/core.php</code>";
			}

			return array(
				'success' => true,
				'value' => sec2hms( $value, true ),
				'message' => $message,
			);
		}

		/**
		 * Convertit un nombre de secondes en un nombre de jours, heures, minutes
		 * et secondes.
		 *
		 * @url http://stackoverflow.com/posts/19680778/revisions
		 *
		 * @param integer $seconds
		 * @return string
		 */
		protected function _secondsToTime($seconds) {
			$dtF = new DateTime("@0");
			$dtT = new DateTime("@$seconds");
			$msgid = '%a days, %h hours, %i minutes and %s seconds';
			return $dtF->diff($dtT)->format( __( $msgid ) );
		}

		/**
		 * Retourne les noms des caches utilisés ainsi que leur durée.
		 *
		 * @return array
		 */
		public function durations() {
			$results = array();
			$debug = Configure::read( 'debug' );
			$min = 60 * 60 * 24;

			foreach( Cache::configured() as $key ) {
				$seconds = Hash::get( Cache::config( $key ), 'settings.duration' );
				$message = $this->_secondsToTime( $seconds );
				$success = $debug || ( $seconds >= $min );

				if( $success === false ) {
					$message .= sprintf( ' au lieu de %s (%d)', $this->_secondsToTime( $min ), $min );
				}

				$results[$key] = array(
					'value' => $seconds,
					'success' => $success,
					'message' => $message
				);
			}

			ksort($results);
			return $results;
		}

		/**
		 * Vérifie l'accès à un WebService.
		 *
		 * @param string $wsdl L'URL du webservice
		 * @param array $params Un array de paramètres
		 *	- "message": le gabarit du message à utiliser en cas d'erreur (par
		 *		         défaut: "Le WebService n'est pas accessible (%s)")
		 *	- "timeout": la durée du timeout en secondes (par défaut: 30)
		 * @return array
		 */
		public function webservice( $wsdl, array $params = array() ) {
			$params += array(
				'message' => 'Le WebService n\'est pas accessible (%s)',
				'timeout' => 30
			);
			$result = array(
				'success' => true,
				'message' => null,
				'value' => null
			);

			$oldTimeout = ini_get( 'default_socket_timeout' );
			ini_set( 'default_socket_timeout', $params['timeout'] );

			// Faire une méthode dans le Check ?
			$handle = @fopen( $wsdl, 'r' );
			if( !empty($handle) ) {
				fclose( $handle );
			}
			else {
				$error = error_get_last();
				$result['success'] = false;
				$result['message'] = sprintf( $params['message'], $error['message'] );
			}

			if( true === $result['success'] ) {
				// INFO: si le wsdl ne répond pas, page blanche / erreur 500 !!
				try {
					$options = array(
						'connection_timeout' => $params['timeout'],
						'exceptions' => true,
						'trace' => false,
						'cache_wsdl' => WSDL_CACHE_NONE
					);
					$client = @new SoapClient( $wsdl, $options );
					$result['success'] = true;
				} catch( Exception $e ) {
					$result['success'] = false;
					$result['message'] = sprintf( $params['message'], trim( $e->getMessage() ) );
				}
			}

			ini_set( 'default_socket_timeout', $oldTimeout );

			return $result;
		}

		/**
		 * Vérifie l'accès à une machine distante.
		 *
		 * @see http://www.php.net/manual/fr/function.fsockopen.php#65631
		 *
		 * @param string $hostname
		 * @param string $port
		 * @param array $params Un array de paramètres
		 *	- "message": le gabarit du message à utiliser en cas d'erreur (par
		 *		         défaut: "La machine distante n'est pas accessible (%s)")
		 *	- "timeout": la durée du timeout en secondes (par défaut: 30)
		 * @return array
		 */
		public function socket( $hostname, $port, array $params = array() ) {
			$params += array(
				'message' => 'La machine distante n\'est pas accessible (%s)',
				'timeout' => 30
			);

			$result = array(
				'success' => true,
				'message' => null,
				'value' => null,
			);

			$handle = @fsockopen( $hostname, $port, $errno, $errstr, $params['timeout'] );

			if( false === empty( $handle ) ){
				$result['success'] = true;
				fclose( $handle );
			}
			else {
				$result['success'] = false;
				$result['message'] = sprintf( $params['message'], "{$errno}: {$errstr}" );
			}

			return $result;
		}

		/**
		 * Vérifie la présence d'extensions PEAR
		 *
		 * @param array $extensions
		 * @param boolean $base A true, vérifie la présence des classes PEAR et PEAR_Registry.
		 * @return array
		 */
		public function pearExtensions( $extensions, $base = true ) {
			$results = array();

			$success = (bool)@include_once  'PEAR.php' ;
			if( $base ) {
				$results['PEAR'] = array(
					'success' => $success,
					'message' => ( $success ? null : "PEAR n'est pas installé. Installez le paquet php-pear (sous Ubuntu, en ligne de commande, faire: <code>sudo apt-get install php-pear</code>)" )
				);
			}

			$success = (bool)@include_once  'PEAR/Registry.php' ;
			if( $base ) {
				$results['Registry'] = array(
					'success' => $success,
					'message' => ( $success ? null : "PEAR_Registry n'est pas installé" )
				);
			}

			$Registry = null;
			if( class_exists( 'PEAR_Registry' ) ) {
				$Registry = @new PEAR_Registry();
			}

			foreach( $extensions as $extension ) {
				$success = ( is_null( $Registry ) ? false : @$Registry->packageExists( $extension ) );
				$results[$extension] = array(
					'success' => $success,
					'message' => ( $success ? null : sprintf( "L'extension PEAR %s n'est pas installée. Pour l'installer, en ligne de commande, faire: <code>sudo pear install %s</code>", $extension, $extension ) )
				);
			}

			return $results;
		}

		/**
		 * Vérification d'une configuration de CakeEmail dans le fichier
		 * app/Config/email.php.
		 *
		 * EmailComponent est déprécié depuis la version 2.0 de CakePHP.
		 *
		 * @todo ::transportClass
		 *
		 * @param string $name Le nom de la configuration
		 * @return array
		 */
		public function cakeEmailConfig( $name ) {
			$settingsFile = 'app/Config/email.php';
			$settingsMailFields = array( 'from', 'replyTo', 'readReceipt', 'returnPath', 'sender', 'to', 'cc', 'bcc' );

			$configure = array();
			$tests = array();

			try {
				$Email = new CakeEmail( $name );
				$settings = $Email->config();
			} catch( Exception $e ) {
				$Email = null;
				$settings = array();
			}

			if( empty( $settings ) ) {
				$configure["Présence de la clé {$name} dans le fichier {$settingsFile}"] = array(
					'success' => false,
					'message' => "Configuration manquante ou erronée dans le fichier {$settingsFile}",
				);
			}
			else {
				// La configuration est-elle présente ?
				$configure[$settingsFile] = array(
					'success' => true,
					'message' => null,
				);

				// Vérification des adresses mail
				foreach( $settingsMailFields as $emailAddress ) {
					if( isset( $settings[$emailAddress] ) && !empty( $settings[$emailAddress] ) ) {
						foreach( (array)$settings[$emailAddress] as $address ) {
							$success = Validation::email( $address, true );

							$configure["{$emailAddress}.{$address}"] = array(
								'success' => $success,
								'message' => ( $success ? null : "L'adresse mail {$address} n'est pas valide" ),
							);
						}
					}
				}

				// Test de connexion au serveur SMTP
				if( !is_null( $Email ) && ( isset( $settings['transport'] ) && strtolower( $settings['transport'] ) == 'smtp' ) ) {
					$tests['Connexion au serveur'] = $this->socket( $settings['host'], $settings['port'] );
				}

				// Test d'envoi de mail factice
				$success = false;
				if( !is_null( $Email ) ) {
					try {
						$Email->subject( 'Test' );
						$Email->to( $Email->from() );
						$Email->transport( 'Debug' );
						$result = $Email->send( 'Test' );
						$success = !empty( $result );
					} catch( Exception $e ) {
						$success = false;
					}
				}
				$tests['Envoi factice'] = array(
					'success' => $success,
					'message' => ( !empty( $success ) ? null : 'Erreur lors du test d\'envoi de mail factice' )
				);
			}

			return array( 'configure' => $configure, 'tests' => $tests );
		}

		/**
		 *
		 * @param string $modelName
		 * @param string $configureKey
		 * @param boolean $array_keys
		 * @return array
		 */
		public function configurePrimaryKey( $modelName, $configureKey, $array_keys = false ) {
			$primaryKeys = (array)Configure::read( $configureKey );
			if( empty( $primaryKeys ) ) {
				return array(
					'success' => false,
					'message' => "Aucune clé primaire n'est présente."
				);
			}

			$results = array();
			$Model = ClassRegistry::init( $modelName );

			if( $array_keys ) {
				$primaryKeys = array_keys( $primaryKeys );
			}

			foreach( $primaryKeys as $primaryKey ) {
				$querydata = array(
					'conditions' => array(
						"{$Model->alias}.{$Model->primaryKey}" => $primaryKey
					),
					'contain' => false
				);
				$results[$primaryKey] = ( $Model->find( 'count', $querydata ) == 1 );
			}

			if( !array_search( false, $results, true ) ) {
				return array(
					'success' => true,
					'message' => null
				);
			}
			else {
				$missing = array();
				foreach( $results as $primaryKey => $result ) {
					if( !$result ) {
						$missing[] = $primaryKey;
					}
				}
				$missing = implode( ', ', $missing );
				$table = Inflector::tableize( $Model->name );
				return array(
					'success' => false,
					'message' => "Les clés primaires suivantes sont manquantes dans la table {$table}: {$missing}"
				);
			}
		}

		/**
		 * Vérification de l'utilisateur et du groupe propriétaires des fichiers
		 * et dossiers du cache, ainsi que des permissions sur ces fichiers.
		 *
		 * @return array
		 */
		public function cacheFilePermissions() {
			$return = array();
			$configNames = (array)Cache::configured();
			$user = posix_getpwuid(posix_geteuid());
			$group = posix_getgrgid($user['gid']);
			$uname = posix_uname();

			foreach( $configNames as $configName ) {
				$config = Cache::config( $configName );
				if( 'file' === strtolower( $config['engine'] ) ) {
					$dir = $config['settings']['path'];
					if( 'linux' !== strtolower( $uname['sysname'] ) ) {
						$success = false;
						$message = sprintf( 'Vérification du propriétaire et des permissions du dossier <var>%s</var> non prévue sur un système %s', $dir, $uname['sysname'] );
					}
					else {
						$files = array();
						$return_var = 0;
						$command = "find {$dir} -maxdepth 1 \\( -not -group {$group['name']} \\) -o \\( -not -user {$user['name']} \\) -o \\( -not -perm -u=rw -o -not -perm -g=rw \\) | sort";
						exec( $command, $files, $return_var );

						if( 0 == $return_var ) {
							$success = true === empty( $files );
							$message = null;
							if( false === $success ) {
								$message = "Des fichiers ou des dossiers du dossier <var>{$dir}</var> n'appartiennent pas au bon utilisateur ou groupe ou n'ont pas les bonnes permissions.<br/>";
								$message .= "Il faudrait lancer la commande suivante pour corriger le problème: <code>sudo chown -R {$user['name']}:{$group['name']} {$dir} && sudo chmod o+rwX,g+rwX -R {$dir}</code><br/>";
								$message .= "Les fichiers concernés sont: <br/><ul><li>".implode( "</li><li>", $files )."</li></ul>";
							}
						}
						else {
							$success = false;
							$message = sprintf( 'Erreur lors de l\'exécution de la commande %s (%d)', $command, $return_var );
						}
					}

					$return[$configName] = array(
						'success' => $success,
						'message' => $message
					);
				}
			}

			return $return;
		}

		/**
		 * Effectue des tests d'écriture, de lecture et de suppression du cache,
		 * pour toutes les configurations définies.
		 *
		 * @return array
		 */
		public function cachePermissions() {
			$cacheKeyBase = __CLASS__.'_'.__FUNCTION__;
			$value = time();
			$return = array();

			$savedCacheDisable = Configure::read( 'Cache.disable' );
			Configure::write( 'Cache.disable', false );
			$savedDebug = Configure::read( 'debug' );
			Configure::write( 'debug', 0 );

			$configNames = Cache::configured();
			if( !empty( $configNames ) ) {
				foreach( $configNames as $configName ) {
					if( !Cache::isInitialized( $configName ) ) {
						Cache::config( $configName );
					}

					$cacheKey = "{$cacheKeyBase}_{$configName}";

					$write = Cache::write( $cacheKey, $value, $configName );
					$read = ( Cache::read( $cacheKey, $configName ) == $value );
					$delete = Cache::delete( $cacheKey, $configName );

					$success = ( $write && $read && $delete );
					$message = null;

					if( !$success ) {
						$actions = array();
						if( !$write ) {
							$actions[] = 'écriture';
						}
						if( !$read ) {
							$actions[] = 'lecture';
						}
						if( !$delete ) {
							$actions[] = 'suppression';
						}
						$actions = implode( ', ', $actions );
						$message = "Problème(s) rencontré(s) pour \"{$configName}\": {$actions}";

						$config = Cache::config( $configName );
						if( $config['engine'] == 'File' ) {
							$path = preg_replace( '/^'.preg_quote( APP, '/' ).'/', APP_DIR.DS, $config['settings']['path'] );
							$message .= "; vérifiez les droits sur le répertoire: {$path}";
						}
					}

					$return[$configName] = array(
						'success' => $success,
						'message' => $message
					);
				}
			}
			Configure::write( 'Cache.disable', $savedCacheDisable );
			Configure::write( 'debug', $savedDebug );

			return $return;
		}

		/**
		 * Vérifie l'espace disponible (par-rapport au seuil) pour tous les
		 * répertoires passés en paramètre.
		 *
		 * @param string|array $directories Le ou les répertoires à vé"rifier.
		 *	Il est possible de passer un array avec le chemin en clé et un array
		 *	en valeur, contenant la clé threshold pour ce répertoire en particulier.
		 * @param array $params La clé threshold permet de spécifier le nombre
		 *	 d'octets minimum requis (l'équivalent de 100 Mo par défaut).
		 * @return array
		 */
		public function freespace( $directories, array $params = array() ) {
			$directories = Hash::normalize( (array)$directories );
			$params += array( 'threshold' => 104857600 );
			$return = array();

			foreach( $directories as $directory => $options ) {
				$return[$directory] = array(
					'success' => true,
					'message' => null
				);
				$options = (array)$options;
				$options += $params;

				if( false === is_dir( $directory ) ) {
					$return[$directory]['success'] = false;
					$msgstr = 'Le répertoire %s n\'existe pas';
					$return[$directory]['message'] = sprintf( $msgstr, $directory );
				}
				else {
					$freeBytes = disk_free_space( $directory );

					if( $freeBytes < $options['threshold'] ) {
						$return[$directory]['success'] = false;
						$msgstr = 'L\'espace disponible dans le répertoire %s devrait être d\'au moins %s mais il ne reste actuellement que %s';
						$return[$directory]['message'] = sprintf( $msgstr, $directory, byteSize( $options['threshold'] ), byteSize( $freeBytes ) );
					}
				}
			}

			return $return;
		}
	}
?>