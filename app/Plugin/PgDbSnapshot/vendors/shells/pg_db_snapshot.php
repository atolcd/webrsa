<?php
	/**
	 * Shell de développement, qui sera néanmoins utilisé par la suite, permettant de tester PgDbSnapshotComponent
	 */
	App::import( 'Core', array( 'Controller' ) );
	App::import( 'Component', array( 'PgDbSnapshot.PgDbSnapshot' ) );

	class PgDbSnapshotShell extends AppShell
	{
		public $Controller = null;

		public $defaultParams = array(
			'log' => false,
			'logpath' => LOGS,
			'verbose' => false,
			'outfile' => null,
			'reference' => null,
		);

		/**
		 * Initialisation du contrôleur et du component, lecture des paramètres.
		 *
		 * @return void
		 */
		public function initialize() {
			parent::initialize();

			$this->Controller =  new Controller();
			$this->Controller->PgDbSnapshot =  new PgDbSnapshotComponent( null );
			$this->Controller->PgDbSnapshot->startup( $this->Controller );

//			$this->solve = $this->_getNamedValue( 'solve', 'boolean' );
		}

		/**
		 * Affiche l'écran de bienvenue du shell, avec une toute petite explication.
		 *
		 * @return void
		 */
		public function _welcome() {
			$this->out();
			$this->out( 'Blablabla' );
			$this->out();
			$this->hr();
		}

		/**
		 * Génère et écrit le dump de la connection courante.
		 *
		 * Ex. d'utilisation: cake/console/cake pg_db_snapshot.pg_db_snapshot dump -outfile app/tmp/test_dump.xml
		 *
		 * @return void
		 */
		public function dump() {
			$start = microtime( true );

			// Soit le fichier de sortie est spécifié en paramètre, soit on génère un nom nous-mêmes
			$outfile = $this->_getNamedValue( 'outfile', 'string' );
			if( empty( $outfile ) ) {
				$outfile = TMP.Inflector::underscore( 'PgDbSnapshot' ).'_'.__FUNCTION__.'_'.date( 'Ymd-His' ).'.xml';
			}

			// Le vrai travail
			$this->Controller->PgDbSnapshot->makeXmlDump( ClassRegistry::init( 'User' ) );

			$this->out( sprintf( "\nExécuté en %s secondes.", number_format( microtime( true ) - $start, 2, ',', ' ' ) ) );
		}

		/**
		 * Génère et écrit le fichier de différences entre la connection courante et un fichier de référence.
		 *
		 * Ex. d'utilisation: cake/console/cake pg_db_snapshot.pg_db_snapshot dump -reference app/tmp/test_dump.xml -outfile app/tmp/test_diff.xml
		 *
		 * @return void
		 */
		public function diff() {
			$start = microtime( true );

			$referencefile = $this->_getNamedValue( 'reference', 'string' );
			// Si le fichier de référence n'existe pas ou est vide, terminer sur une erreur.

			// Soit le fichier de sortie est spécifié en paramètre, soit on génère un nom nous-mêmes
			$outfile = $this->_getNamedValue( 'outfile', 'string' );
			if( empty( $outfile ) ) {
				$outfile = TMP.Inflector::underscore( 'PgDbSnapshot' ).'_'.__FUNCTION__.'_'.date( 'Ymd-His' ).'.xml';
			}

			$reference = $this->Controller->PgDbSnapshot->readXmlDump( $referencefile );
			$current = $this->Controller->PgDbSnapshot->makeXmlDump( ClassRegistry::init( 'User' ) );

			// Le vrai travail
			$this->Controller->PgDbSnapshot->compareXmlDumps( $reference, $current );

			$this->out( sprintf( "\nExécuté en %s secondes.", number_format( microtime( true ) - $start, 2, ',', ' ' ) ) );
		}

		/**
		 * Affiche l'aide par défaut.
		 *
		 * Ex. d'utilisation: cake/console/cake pg_db_snapshot.pg_db_snapshot
		 *
		 * @return void
		 */
		public function main() {
			$this->help();
		}

		/**
		 * Affiche l'écran d'aidse de ce shell.
		 * Voir par exemple app/vendors/shells/generationpdfs.php
		 *
		 * @return void
		 */
		public function help() {
		}
	}
?>