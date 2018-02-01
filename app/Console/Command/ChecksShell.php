<?php
	/**
	 * Code source de la classe ChecksShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );
	App::uses( 'ComponentCollection', 'Controller' );
	App::uses( 'Component', 'Controller' );
	App::uses( 'GedoooComponent', 'Gedooo.Controller/Component' );
	App::uses( 'SessionAclComponent', 'SessionAcl.Controller/Component' );
	App::uses( 'TranslatorHash', 'Translator.Utility' );

	/**
	 * La classe ChecksShell effectue les vérifications de l'application et
	 * se charge d'imprimer un rapport de ces vérifications.
	 *
	 * @fixme Pour le php.ini et Apache, il faudra refaire une vérification dans
	 * l'application
	 *
	 * @package app.Console.Command
	 */
	class ChecksShell extends XShell
	{

		/**
		 * La constante à utiliser dans la méthode _stop() en cas de succès.
		 */
		const SUCCESS = 0;

		/**
		 * La constante à utiliser dans la méthode _stop() en cas d'erreur.
		 */
		const ERROR = 1;

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'WebrsaInstallCheck'
		);

		/**
		 * Tableau intermédiaire de résultats en erreur.
		 *
		 * @var array
		 */
		protected $_errors = array();

		/**
		 * Tableau intermédiaire de résultats en succès.
		 *
		 * @var array
		 */
		protected $_successes = array();

		/**
		 * Initialisation du shell.
		 */
		public function initialize() {
			parent::initialize();

			$this->stdout->styles( 'success', array( 'text' => 'green', 'bold' => false ) );
			$this->stdout->styles( 'error', array( 'text' => 'red', 'bold' => false ) );
			$this->stdout->styles( 'info', array( 'text' => 'cyan', 'bold' => false ) );
		}

		/**
		 * Insertion d'un résultat dans le tableau intermédiaire des résultats en
		 * en erreur ou en succès.
		 *
		 * @param array $tokens
		 * @param array $result
		 * @return bool
		 */
		protected function _addResult( array $tokens, array $result ) {
			$success = (bool)$result['success'];

			if( true === $success ) {
				$this->_successes = TranslatorHash::insert(
					$this->_successes,
					$tokens,
					$result
				);
			}
			else {
				$this->_errors = TranslatorHash::insert(
					$this->_errors,
					$tokens,
					$result
				);
			}

			return $success;
		}

		/**
		 * Analyse du tableau des résultats
		 *
		 * @param array $results
		 * @param array $ancestors
		 * @return boolean
		 */
		protected function _analyze( array $results, $ancestors = array() ) {
			$spaces = str_repeat( "\t", count( $ancestors ) );
			$success = true;

			foreach( $results as $key => $result ) {
				$path = array_merge( $ancestors, array( $key ) );

				if( true === is_array( $result ) ) {
					$dimensions = Hash::dimensions( $result );

					if( 1 <= $dimensions && isset( $result['success'] ) ) {
						$success = $this->_addResult( $path, $result ) && $success;
					}
					else {
						$success = $this->_analyze( $result, $path ) && $success;
					}
				}
				else {
					$this->_successes = TranslatorHash::insert(
						$this->_successes,
						$path,
						null
					);
				}
			}

			return $success;
		}

		/**
		 * Affiche le rapport des vérifications
		 */
		protected function _report( array $results, $ancestors = array() ) {
			foreach( $results as $key => $result ) {
				$path = array_merge( $ancestors, array( $key ) );
				$spaces = str_repeat( "\t", count( $path ) - 1 );

				if( true === is_array( $result ) ) {
					$dimensions = Hash::dimensions( $result );

					if( 1 <= $dimensions && isset( $result['success'] ) ) {
						$success = Hash::get( $result, 'success' );
						$value = Hash::get( $result, 'value' );
						$message = Hash::get( $result, 'message' );

						if( false === $success || true === $this->params['verbose'] ) {
							$tag = ( true !== $success ? 'error' : 'success' );
							$this->out( rtrim( "{$spaces}<{$tag}>{$key}</{$tag}>\t{$value}\t<info>{$message}</info>" ) );
						}
					}
					else {
						$hasErrors = TranslatorHash::exists( $this->_errors, $path );

						if( true === $hasErrors || true === $this->params['verbose'] ) {
							$tag = ( true === $hasErrors ? 'error' : 'success' );
							$this->out( "{$spaces}<{$tag}>{$key}</{$tag}>" );
						}

						$this->_report( $result, $path );
					}
				}
			}
		}

		/**
		 * Méthode principale.
		 */
		public function main() {
			$componentCollection = new ComponentCollection();

			// Création éventuelle du répertoire temporaire pour les PDF
			$Gedooo = new GedoooComponent( $componentCollection );
			$Gedooo->makeTmpDir( Configure::read( 'Cohorte.dossierTmpPdfs' ) );

			// Initialisation de la classe SessionAcl pour pouvoir utiliser la classe SessionAclUtility
			$sessionAclComponent = new SessionAclComponent( $componentCollection );

			// Vérification de l'application
			$results = $this->WebrsaInstallCheck->all();
			$success = $this->_analyze( $results );

			// Affichage des résultats
			$this->_report( $results );

			$this->_scritpEnd();
			$this->_stop( $success ? self::SUCCESS : self::ERROR );
		}

		/**
		 * Paramétrages et aides du shell.
		 */
		public function getOptionParser() {
			$Parser = parent::getOptionParser();

			$Parser->description( 'Shell de vérification de l\'application web-rsa' );

			return $Parser;
		}
	}
?>