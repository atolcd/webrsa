<?php
	/**
	 * Fichier source de la classe GedoooComponent.
	 *
	 * PHP 5.3
	 *
	 * @package Gedooo
	 * @subpackage Controller.Component
	 */
	@set_time_limit( 0 );
	// Mémoire maximum allouée à l'exécution de ce script
	@ini_set( 'memory_limit', '512M' );
	// Temps maximum d'exécution du script (en secondes)
	@ini_set( 'max_execution_time', 2000 );
	// Temps maximum (en seconde), avant que le script n'arrête d'attendre la réponse de Gedooo
	@ini_set( 'default_socket_timeout', 12000 );

	App::uses( 'Component', 'Controller' );

	/**
	 * La classe GedoooComponent fournit des méthodes permettant de concaténer
	 * des fichiers PDF (grâce au binaire pdftk) et d'envoyer un fichier PDF au
	 * navigateur.
	 *
	 * @package Gedooo
	 * @subpackage Controller.Component
	 */
	class GedoooComponent extends Component
	{
		/**
		 * The initialize method is called before the controller's beforeFilter method.
		 *
		 * @param Controller $controller
		 */
		public function initialize( Controller $controller ) {
			$this->controller = $controller;
		}

		/**
		 * Création d'un répertoire temporaire (inscriptible par tout le monde) de manière récursive
		 * si nécessaire. Si le répertoire existe déjà, et que les permissions ne sont pas suffisantes, on
		 * essaie de le rendre inscriptible pour tout le monde.
		 *
		 * @param string $path Le chemin du répertoire temporaire à créer
		 * @return boolean true si le répertoire existe et est inscriptible, false sinon
		 */
		public function makeTmpDir( $path ) {
			$umask = 0777;
			$success = false;

			if( is_dir( $path ) ) { // Le chemin existe déjà
				$acutalmask = fileperms( $path );
				if( $acutalmask >= $umask ) { // Permissions suffisantes
					$success = true;
				}
				else {
					$return = chmod( $path, $umask );
				}
			}
			else {
				$oldUmask = umask( 0 );
				$success = @mkdir( $path, $umask, true );
				umask( $oldUmask );
			}

			return $success;
		}

		/**
		 * Concactène les pdfs grâce à pdftk (écrits dans un répertoire temporaire) et renvoit le résultat.
		 *
		 * @param array $pdfs
		 * @param string $modelName
		 * @return mixed
		 */
		public function concatPdfs( $pdfs, $modelName ) {
			$pdfTmpDir = rtrim( Configure::read( 'Cohorte.dossierTmpPdfs' ), '/' ).'/'.session_id().'/'.$modelName;
			/* $old = umask(0);
			  @mkdir( $pdfTmpDir, 0777, true ); /// FIXME: vérification
			  umask($old); */
			$this->makeTmpDir( $pdfTmpDir );

			//$places =  round ( ( count( $pdfs ) / 10 ) + 1 );
			$places = 6;
			foreach( $pdfs as $i => $pdf ) {
				$fileName = str_pad( $i, $places, '0', STR_PAD_LEFT );
				file_put_contents( "{$pdfTmpDir}/{$fileName}.pdf", $pdf );
			}

			exec( "pdftk {$pdfTmpDir}/*.pdf cat output {$pdfTmpDir}/all.pdf" ); // FIXME: nom de fichier cohorte-orientation-20100423-12h00.pdf

			if( !file_exists( "{$pdfTmpDir}/all.pdf" ) ) {
				// INFO: on nettoie quand même avant de partir
				exec( "rm {$pdfTmpDir}/*.pdf" );
				exec( "rmdir {$pdfTmpDir}" );

				return false;
			}

			$c = file_get_contents( "{$pdfTmpDir}/all.pdf" );

			exec( "rm {$pdfTmpDir}/*.pdf" );
			exec( "rmdir {$pdfTmpDir}" );

			return $c; /// FIXME: false si problème
		}

		/**
		 * Parcourt l'array de réponses renvoyée lors d'un appel à la méthode GedoooXXXBehavior::gedTests()
		 * et renvoit true lorsque tous les éléments ont une clé 'success' à true, false sinon.
		 *
		 * @param array $response
		 * @return boolean
		 */
		protected function _checkResponseAsBoolean( $response ) {
			foreach( $response as $key => $return ) {
				if( !$return['success'] ) {
					return false;
				}
			}
			return true;
		}

		/**
		 * Vérification de l'état du serveur Gedooo.
		 *
		 * @param boolean $asBoolean Doit-on renvoyer un array avec les différentes vérifications, ou un résumé
		 * @param boolean $setFlash Doit-on afficher un message d'erreur s'il Gedooo est mal configuré
		 * @return mixed
		 */
		public function check( $asBoolean = false, $setFlash = false ) {
			App::uses( 'GedoooBehavior', 'Gedooo.Model/Behavior' );

			$GedModel = ClassRegistry::init( 'User' );
			$GedModel->Behaviors->attach( 'Gedooo.Gedooo' );
			$response = @$GedModel->gedTests();

			// FIXME: traductions
			$traductions = array(
				'status' => 'Accès au WebService',
				'file_exists' => 'Présence du modèle de test',
				'print' => 'Test d\'impression',
			);

			if( $setFlash ) {
				if( !$response[$traductions['file_exists']] ) {
					$this->controller->Session->setFlash( 'Il n\'est pas certain que le serveur Gedooo fonctionne car le modèle de document de test n\'existe pas. Veuillez contacter votre administrateur système.', 'default', array( 'class' => 'notice' ) );
				}
				else if( !$response[$traductions['status']] ) {
					$this->controller->Session->setFlash( 'Impossible de se connecter au serveur Gedooo. Veuillez contacter votre administrateur système.', 'default', array( 'class' => 'error' ) );
				}
				else if( !$response[$traductions['print']] ) {
					$this->controller->Session->setFlash( 'Impossible d\'imprimer avec le serveur Gedooo. Veuillez contacter votre administrateur système.', 'default', array( 'class' => 'error' ) );
				}
				else {
					if( !$this->_checkResponseAsBoolean( $response ) ) {
						$this->controller->Session->setFlash( 'Impossible d\'imprimer avec le serveur Gedooo. Veuillez contacter votre administrateur système.', 'default', array( 'class' => 'error' ) );
					}
				}
			}
			else if( $asBoolean ) {
				return $this->_checkResponseAsBoolean( $response );
			}
			else {
				return $response;
			}
		}

		/**
		 * Envoit les en-têtes (content-type pdf, taille du fichier, nom du fichier) et le contenu d'un fichier à
		 * télécharger par le client.
		 *
		 * @param string $content Le contenu du fichier à envoyer à l'utilisateur
		 * @param string $filename Le nom du fichier envoyé à l'utilisateur
		 * @param string $type Le type MIME à renvoyer (application/pdf par défaut)
		 */
		public function sendPdfContentToClient( $content, $filename, $type = 'application/pdf' ) {
			header( 'Content-type: '.$type );
			header( 'Content-Length: '.strlen( $content ) );
			header( "Content-Disposition: attachment; filename={$filename}" );

			echo $content;
			die();
		}
	}
?>