<?php
	/**
	 * Code source de la classe Cmis.
	 *
	 * PHP 5.3
	 *
	 * @package app.Lib
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	if( CAKE_BRANCH == '1.2' ) {
		require_once( APP.'Vendor'.DS.'apache_chemistry'.DS.'cmis_repository_wrapper.php' );
		//App::import( 'Vendor', 'apache_chemistry'.DS.'cmis_repository_wrapper' );
	}
	else {
		require_once( APP.'Vendor'.DS.'apache_chemistry'.DS.'cmis_repository_wrapper.php' );
	}

	/**
	 * Classe utilitaire permettant de dialoguer avec un serveur CMS via le protocole
	 * CMIS grâce à l'utilisation de la librairie phpclient d'Apache Chemistry.
	 *
	 * Un seul serveur Cmis est possible. Les paramètres suivants doivent être ajoutés
	 * au fichier app/boostrap.php avec Cmis::config( $url, $username, $password )
	 *
	 * FIXME: Tickets / transactions ?
	 * FIXME: will not work on Pre CMIS-1.0 repositories
	 * FIXME: Your version of php must support DOMDocument and curl
	 *
	 * @see http://incubator.apache.org/chemistry/phpclient.html
	 * @package app.Lib
	 */
	abstract class Cmis
	{
		/**
		*
		*/

		protected static $_url = null;

		/**
		*
		*/

		protected static $_username = null;

		/**
		*
		*/

		protected static $_password = null;

		/**
		*
		*/

		protected static $_prefix = null;

		/**
		*
		*/

		protected static $_connection = null;

		/**
		* FIXME: docs
		*/

		static public function config( $url, $username, $password, $prefix = null ) {
			self::$_url = $url;
			self::$_username = $username;
			self::$_password = $password;
			self::$_prefix = $prefix;
		}

		/**
		* Vérife l'accès au serveur CMS ainsi que la version de CMIS supportée.
		*
		* @return boolean true si le serveur CMS est accessible avec l'identifiant
		* 	et le mot de passe fournis et si la version de CMIS supportée est la 1.0
		*/

		static public function configured() {
			try {
				$connection = self::connect();

				if( is_a( $connection,'CMISService' ) && $connection->authenticated ) {
					$infos = @self::$_connection->getRepositoryInfo()->repositoryInfo;

					return ( !empty( $connection ) && ( $infos['cmis:cmisVersionSupported'] == '1.0' ) );
				}
				return false;
			} catch( Exception $e ) {
				return false;
			}
		}

		/**
		* Obtient une connection à un serveur CMS avec le protocole CMIS.
		*
		* Les paramètres permettent de surcharger les paramètres par défaut.
		* Mettre le paramètre à null pour utiliser le paramètre par défaut.
		*
		* Crée une nouvelle connection si besoin et stocke la connection.
		*
		* @param string $url
		* @param string $username
		* @param string $password
		* @return mixed la connection si elle a été crée ou si elle existait déjà, false sinon.
		*/

		static public function connect( $url = null, $username = null, $password = null ) {
			try {
				if( empty( self::$_connection ) ) {
					self::$_connection = new CMISService(
						( empty( $url ) ? self::$_url : $url ),
						( empty( $username ) ? self::$_username : $username ),
						( empty( $password ) ? self::$_password : $password )
					);
				}
			} catch( Exception $e ) {//throw error
// 				debug( $e );
				return false;
			}

			if( empty( self::$_connection )  ) {
				return false;
			}

			return self::$_connection;
		}

		/**
		* INFO: contrairement à ce qui est indiqué sur la page du projet, il ne
		*	faut pas remplacer les espaces par des +
		*/

		static protected function _buildPath( $path, $prefix = null ) {
			return rtrim( $prefix, '/' ).'/'.trim( $path, '/' );
		}

		/**
		*
		*/

		static protected function _init() {
			return self::connect();
		}

		/**
		* Vérifie l'existence d'un objet au sein du CMS suivant son chemin absolu.
		*
		* @param string $path
		* @return boolean true si l'objet existe, false sinon.
		*/

		static protected function _check( $path ) {
			if( empty( self::$_connection )  ) {
				return false;
			}

			try {
				$node = @self::$_connection->getObjectByPath( $path );
				return true;
			} catch( Exception $e ) {
				return false;
			}
		}

		/**
		* Vérifie l'existence d'un objet au sein du CMS suivant son chemin relatif.
		*
		* @param string $path
		* @return boolean true si l'objet existe, false sinon.
		*/

		static public function check( $path ) {
			try {
				self::_init();
				$path = self::_buildPath( $path, self::$_prefix );

				return self::_check( $path );
			} catch( Exception $e ) {
				return false;
			}
		}

		/**
		* Obtient les propriétés d'un objet se trouvant sur le serveur CMS suivant
		* son chemin absolu.
		*
		* @param string $path Le chemin absolu de l'objet sur le serveur CMIS
		* @param boolean $deep Si l'objet est un dossier, retourne le contenu du dossier
		* 	dans la clé content, si c'est un document, retourne le document en
		*	lui-même dans la clé content.
		* @return mixed array si le l'objet a pu être obtenu, false sinon.
		*/

		static protected function _read( $path, $deep = false ) {
			if( empty( self::$_connection )  ) {
				return false;
			}

			try {
				$obj = self::$_connection->getObjectByPath( $path );
				// Document
				if( @$obj->properties['cmis:objectTypeId'] == 'cmis:document' ) {
					$return = $obj->properties;
					if( $deep ) {
						$doc = self::$_connection->getContentStream( $obj->id );
						$return['content'] = $doc;
					}
					return $return;
				}
				// Folder
				else if( @$obj->properties['cmis:objectTypeId'] == 'cmis:folder' ) {
					$return = $obj->properties;
					if( $deep ) {
						$objects = self::$_connection->getChildren( $obj->id );

						$return['content'] = array();
						foreach( $objects->objectList as $object ) {
							$return['content'][] = $object->properties;
						}
					}

					return $return;
				}
			} catch( Exception $e ) {
				return false;
			}
		}

		/**
		* Obtient les propriétés d'un objet se trouvant sur le serveur CMS suivant
		* son chemin relatif.
		*
		* @param string $path Le chemin relatif de l'objet sur le serveur CMIS
		* @param boolean $deep Si l'objet est un dossier, retourne le contenu du dossier
		* 	dans la clé content, si c'est un document, retourne le document en
		*	lui-même dans la clé content.
		* @return mixed array si le l'objet a pu être obtenu, false sinon.
		*/

		static public function read( $path, $deep = false ) {
			try {
				self::_init();
				$path = self::_buildPath( $path, self::$_prefix );

				return self::_read( $path, $deep );
			} catch( Exception $e ) {
				return false;
			}
		}

		/**
		* Crée un répertoire sur le serveur CMS à partir de son chemin absolu.
		*
		* @param string $path Le chemin relatif du répertoire sur le serveur CMIS
		* @return mixed boolean true si le répertoire a pu être créé, false sinon.
		*/

		static protected function _mkdir( $path ) {
			if( empty( self::$_connection )  ) {
				return false;
			}

			try {
				$crumbs = Hash::filter( (array)preg_split( '/\//', $path ) );

				$prevNode = null;
				$tmpPath = '';
				foreach( $crumbs as $crumb ) {
					$tmpPath .= "/{$crumb}";

					if( !self::_check( $tmpPath ) && !empty( $prevNode ) ) {
						try {
							@self::$_connection->createFolder( $prevNode->id, $crumb );
						} catch( Exception $e ) {
							if( Configure::read( 'debug' ) > 0 ) {
								CakeLog::write( LOG_DEBUG, var_export( array( __LINE__, get_class( $e ), $tmpPath, $crumb ), true ) );
							}
						}
					}

					$prevNode = @self::$_connection->getObjectByPath( $tmpPath );
					if( empty( $prevNode ) ) {
						if( Configure::read( 'debug' ) > 0 ) {
							CakeLog::write( LOG_DEBUG, var_export( array( __LINE__, get_class( $e ), $tmpPath, $crumb ), true ) );
						}
						return false;
					}
				}

				return true;
			} catch( Exception $e ) {
				if( Configure::read( 'debug' ) > 0 ) {
					CakeLog::write( LOG_DEBUG, var_export( array( __LINE__, get_class( $e ), $tmpPath, $crumb ), true ) );
				}
				return false;
			}
		}

		/**
		* Enregistre un document sur le serveur CMS à partir de son chemin absolu.
		*
		* FIXME: replace / update
		*
		* @param string $path Le chemin absolu du document sur le serveur CMIS
		* @param string $document Le contenu du document
		* @param string $mimetype Le type MIME du document
		* @param boolean $replace Si cette valeur vaut true et que le document existe déjà,
		* 	une nouvelle version du document sera créée.
		* @return boolean true le document s'il a pu être enregistré, false sinon.
		*/

		static protected function _write( $path, $document, $mimetype, $replace = false ) {
			if( empty( self::$_connection )  ) {
				return false;
			}

			try {
				$folderPath = dirname( $path );
				$fileName = basename( $path );
				$fileExists = self::_check( $path );

				if( $fileExists && $replace ) {
					// INFO: même avec l'interface Web d'Alfresco, j'ai du mal à
					// récupérer une ancienne version.
					$object = @self::$_connection->getObjectByPath( $path );
					$obj_doc = @self::$_connection->setContentStream(
						$object->id,
						$document,
						$mimetype
					);
				}
				else if( !$fileExists ) {
					if( !self::_check( $folderPath ) ) {
						self::_mkdir( $folderPath );
					}

					$myfolder = @self::$_connection->getObjectByPath( $folderPath );
					$obj_doc = @self::$_connection->postObject(
						$myfolder->id,
						$fileName,
						"cmis:document",
						array(),
						$document,
						$mimetype,
						array()
					);
				}
				else {
					return false;
				}

				return true;
			} catch( Exception $e ) {
				return false;
			}
		}

		/**
		* Enregistre un document sur le serveur CMS à partir de son chemin relatif.
		*
		* FIXME: replace / update
		*
		* @param string $path Le chemin relatif du document sur le serveur CMIS
		* @param string $document Le contenu du document
		* @param string $mimetype Le type MIME du document
		* @param boolean $replace Si cette valeur vaut true et que le document existe déjà,
		* 	une nouvelle version du document sera créée.
		* @return boolean true le document s'il a pu être enregistré, false sinon.
		*/

		static public function write( $path, $document, $mimetype, $replace = false ) {
			try {
				self::_init();
				$path = self::_buildPath( $path, self::$_prefix );

				return self::_write( $path, $document, $mimetype, $replace );
			} catch( Exception $e ) {
				return false;
			}
		}

		/**
		* Supprime un objet sur le serveur CMS à partir de son chemin absolu.
		*
		* @param string $path Le chemin absolu du document sur le serveur CMIS
		* @param boolean $recursive Si l'objet est un répertoire, la valeur true
		* 	permet de supprimer celui-ci de manière récursive.
		* @return boolean true si l'objet a pu être supprimé, false sinon.
		*/

		static protected function _delete( $path, $recursive = false ) {
			if( empty( self::$_connection )  ) {
				return false;
			}

			try {
				$obj = self::$_connection->getObjectByPath( $path );
				$isFolder = ( @$obj->properties['cmis:objectTypeId'] == 'cmis:folder' );

				if( empty( $obj ) || ( $isFolder && @$obj->properties['cmis:path'] !== $path ) ) {
					debug( $obj );
					return false;
				}

				if( $isFolder && $recursive ) {
					$children = self::$_connection->getChildren( $obj->id );
					if( $children->numItems > 0 ) {
						foreach( $children->objectList as $object ) {
							self::delete( $path.'/'.$object->properties['cmis:name'] );
						}
					}
				}

				self::$_connection->deleteObject( $obj->id );
				return true;
			} catch( Exception $e ) {
				return false;
			}
		}

		/**
		* Supprime un objet sur le serveur CMS à partir de son chemin relatif.
		*
		* @param string $path Le chemin relatif du document sur le serveur CMIS
		* @param boolean $recursive Si l'objet est un répertoire, la valeur true
		* 	permet de supprimer celui-ci de manière récursive.
		* @return boolean true si l'objet a pu être supprimé, false sinon.

		*/

		static public function delete( $path, $recursive = false ) {
			try {
				self::_init();
				$path = self::_buildPath( $path, self::$_prefix );

				return self::_delete( $path, $recursive );
			} catch( Exception $e ) {
				return false;
			}
		}
	}

	Cmis::config(
		Configure::read( 'Cmis.url' ),
		Configure::read( 'Cmis.username' ),
		Configure::read( 'Cmis.password' ),
		Configure::read( 'Cmis.prefix' )
	);
?>