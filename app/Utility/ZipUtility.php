<?php

	/**
	 * Code source de la classe ZipUtility.
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ZipUtility permet de créer une arborescence de fichiers reseigné par la fonction add() 
	 * en vue de les transformer en fichier Zip
	 * 
	 * Par défaut, les fichiers se trouvent dans APP.'tmp/files/'
	 *
	 * @package app.Utility
	 */
	class ZipUtility {
		/**
		 * Dossier racine où serons créer les fichiers.
		 * 
		 * @var string
		 */
		public $basePath = '';
		
		/**
		 * Nombre d'erreurs, si tout s'est bien passé, doit être égal à 0
		 * 
		 * @var integer
		 */
		public $error = 0;
		
		/**
		 * Contien la liste des fichiers à creer -> zipper
		 * 
		 * @var array
		 */
		protected $_files = array();
		
		/**
		 * Constructeur de classe, défini le basePath
		 */
		public function __construct() {
			$this->basePath = APP . 'tmp/files/';
		}
		
		/**
		 * Ajoute un fichier au fichier zip à creer
		 * 
		 * @param string $file Contenu du fichier à créer / zipper
		 * @param string $pathInZip Chemin et nom du fichier dans le zip
		 * @return $this
		 */
		public function add( $file, $pathInZip ) {
			$this->_files[] = array( 'file' => $file, 'path' => preg_replace( '/[^a-zA-Z0-9_\-\.\/]+/', '_', trim($pathInZip, '/') ) );
			
			return $this;
		}
		
		/**
		 * Transforme la liste des fichiers en Zip
		 * 
		 * @param type $zipFileName Nom du fichier zip (ne pas oublier le .zip à la fin)
		 * @return string Chemin vers le fichier zip
		 */
		public function zip( $zipFileName ) {
			if ( empty($this->_files) ) {
				$this->error++;
				return true;
			}
			
			$Zip = new ZipArchive();
			!is_dir( $this->basePath ) && mkdir( $this->basePath, 0777, true );
			$zipFilePath = $this->basePath . $zipFileName;
			
			if ( is_file($zipFilePath) ) {
				unlink($zipFilePath);
			}
			
			if ($Zip->open($zipFilePath, ZipArchive::CREATE)!==TRUE) {
				$this->error++;
				return false;
			}
			
			$this->_createTmpFiles();
			
			foreach ( $this->_files as $value ) {
				$Zip->addFile( $this->basePath.$value['path'], $value['path'] );
			}
			
			$Zip->close();
			
			chmod($zipFilePath, 0777);
			return $zipFilePath;
		}
		
		/**
		 * Envoit les en-têtes (content-type pdf, taille du fichier, nom du fichier) et le contenu d'un fichier à
		 * télécharger par le client.
		 *
		 * @param string $path Le chemin vers le fichier zip à envoyer à l'utilisateur
		 */
		public static function sendZipToClient( $path ) {
			$fileName = basename($path);
			header( 'Content-type: application/zip' );
			header( 'Content-Length: '.filesize( $path ) );
			header( 'Content-Disposition: attachment; filename='.$fileName );

			readfile($path);
			unlink($path);
			exit();
		}
		
		/**
		 * Transforme $this->_files en véritables fichiers temporaires.
		 * 
		 * @return $this
		 */
		protected function _createTmpFiles() {
			foreach ( $this->_files as $key => $value ) {
				$this->_mkdirRecurcive($value['path']);
				
				$File = fopen( $this->basePath.$value['path'], "w+");
				
				if ( $File && chmod($this->basePath.$value['path'], 0777) && fwrite($File, $value['file']) ) {
					fclose($File);
				}
				else {
					$this->error++;
					unset( $this->_files[$key] );
				}
			}
			
			return $this;
		}
		
		/**
		 * Crée l'arborescence de dossiers avec un chmod de 0777
		 * ex: 'dir1/dir2/monfichier.odt' créera dir1 et dir2 avec un chmod 0777
		 * 
		 * @param string $path Chemin relatif vers le fichier dans APP.'tmp/files/'
		 * @return $this
		 */
		protected function _mkdirRecurcive( $path ) {
			if ( strpos($path, '/') === false ) {
				return true;
			}
			
			$dirPath = explode('/', $path);
			unset( $dirPath[count($dirPath) -1] );
			$newDir = $this->basePath;
			
			foreach ( $dirPath as $dir ) {
				$newDir .= '/' . $dir;
				!is_dir($newDir) && mkdir( $newDir, 0777, true );
				chmod($newDir, 0777);
			}
			
			return $this;
		}
	}
