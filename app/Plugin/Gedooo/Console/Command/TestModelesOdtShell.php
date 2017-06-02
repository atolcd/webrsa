<?php
	/**
	 * Code source de la classe TestModelesOdtShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppShell', 'Console/Command' );
	App::uses( 'Folder', 'Utility' );
	App::uses( 'File', 'Utility' );
	App::uses( 'GedoooBehavior', 'Gedooo.Model/Behavior' );

	/**
	 * La classe TestModelesOdtShell ...
	 *
	 * @package app.Console.Command
	 */
	class TestModelesOdtShell extends AppShell
	{

		/**
		 * La constante à utiliser dans la méthode _stop() en cas de succès.
		 */
		const SUCCESS = 0;

		/**
		 * La constante à utiliser dans la méthode _stop() en cas d'erreur.
		 */
		const ERROR = 1;

		public function initialize() {
			parent::initialize();

			$this->stdout->styles( 'success', array( 'text' => 'green', 'bold' => true ) );
		}

		/**
		 * Démarrage du shell
		 */
		public function startup() {
			parent::startup();
		}

		/**
		 * Lignes de bienvenue.
		 */
		protected function _welcome() {
			parent::_welcome();
		}

		/**
		 * Méthode principale.
		 */
		public function main() {
			$Model = ClassRegistry::init( 'User' ); // @fixme
			$Model->Behaviors->load( 'Gedooo.Gedooo' );

			$success = true;

			$dir = Hash::get( $this->args, '0' );
			$Folder = new Folder( $dir, false, 0777 );

			if( true === empty( $Folder->path ) ) {
				$this->error( sprintf( "Le répertoire \"%s\" n'existe pas ou n'est pas accessible.", $dir ) );
			}

			$this->out( sprintf( 'Test des fichiers du répertoire %s', $dir ) );

			foreach( $Folder->findRecursive('.+\.odt$') as $fileName ) {
				$shortFileName = ltrim( preg_replace( '/^'.preg_quote( $dir, '/' ).'/', '', $fileName ), DS );

				try {
					$this->out( sprintf( "\tTest du fichier %s", $shortFileName ) );
					$tmp = $Model->ged( array(), $fileName );
				} catch( Exception $e ) {
					$tmp = false;
				}

				if( empty( $tmp ) ) {
					$msgid = "\t\t<error>erreur</error> lors du test du fichier %s (voir dans le fichier error.log vers %s)";
					$this->err( sprintf( $msgid, $shortFileName, date( 'Y-m-d H:i:s' ) ) );
				}
				else {
					$msgid = "\t\t<success>succès</success> lors du test du fichier %s";
					$this->out( sprintf( $msgid, $shortFileName ) );
				}

				$success = $success && $tmp;
			}

			$this->_stop( $success ? self::SUCCESS : self::ERROR );
		}

		/**
		 * Paramétrages et aides du shell.
		 */
		public function getOptionParser() {
			$Parser = parent::getOptionParser();

			$description = array(
				'Shell de vérification des modèles odt.',
				'Ce shell parcourt le répertoire passé en paramètre et ses sous-répertoires à la recherche de fichiers possédant l\'extension .odt et réalise un test d\'impression ce qui permet de vérifier si le modèle odt est correct.'
			);
			$Parser->description( $description );

			$args = array(
				'dir' => array(
					'help' => 'Chemin du répertoire contenant les fichiers de modèles odt à tester',
					'required' => true
				)
			);
			$Parser->addArguments( $args );

			return $Parser;
		}

	}
?>