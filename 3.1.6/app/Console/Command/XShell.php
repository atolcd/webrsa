<?php
	/**
	 * Code source de la classe XShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	@ini_set( 'memory_limit', '2048M' );
	App::uses( 'Shell', 'Console' );
	App::uses( 'ConnectionManager', 'Model' );

	/**
	 * La classe XShell sert de classe parente aux shells de l'application, sans
	 * surcharger la classe parente de tous les shells (notamment ceux du coeur
	 * de CakePHP).
	 *
	 * @package app.Console.Command
	 */
	class XShell extends AppShell
	{

		/**
		 *
		 * @var type
		 */
		public $tasks = array( 'XProgressBar' );

		/**
		 *
		 * @var type
		 */
		private $_start;

		/**
		 *
		 * @var type
		 */
		private $_end;

		/**
		 *
		 * @var type
		 */
		private $_logFile;

		/**
		 *
		 * @var type
		 */
		private $_iconvEncoding;

		/**
		 *
		 * @var type
		 */
		private $output = '';

		/**
		 * Outputs a series of minus characters to the standard output, acts as a visual separator.
		 *
		 * @param integer $newlines Number of newlines to pre- and append
		 * @access public
		 */
		public function hr( $newlines = 1, $symbol = '-', $length = 75, $level = Shell::NORMAL ) {
			$this->out( str_repeat( $symbol, $length ), $newlines, $level );
		}

		/**
		 *
		 */
		public function initialize() {
			parent::initialize();
			$this->_iconvEncoding = iconv_get_encoding();
			//init start time
			list($usec, $sec) = explode( ' ', microtime() );
			$this->_start = (float) $sec + (float) $usec;
			//init log file
			$this->_logFile = LOGS.$this->name.'-'.date( 'Ymd-His' ).'.log';
			//init styles
			$this->stdout->styles( 'important', array( 'text' => 'yellow', 'bold' => true ) );
			$this->stdout->styles( 'success', array( 'text' => 'green', 'bold' => true ) );
		}

		/**
		 *
		 * @return type
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->addOption( 'connection', array(
				'short' => 'c',
				'help' => 'connection',
				'default' => 'default',
				'choices' => array_keys( ConnectionManager::enumConnectionObjects() )
			) );
			$parser->addOption( 'log', array(
				'short' => 'l',
				'help' => 'log',
				'default' => 'false',
				'boolean' => true
			) );
			return $parser;
		}

		/**
		 *
		 */
		protected function _setConnection() {
			$this->connection = ConnectionManager::getDataSource( $this->params['connection'] );
		}

		/**
		 *
		 */
		public function startup() {
			$this->_setConnection();
			parent::startup();
		}

		/**
		 * Displays a header for the shell
		 *
		 * @return void
		 */
		protected function _welcome() {
			$this->out( '', 1, Shell::QUIET );
			$this->out( __d( 'cake_console', '<important>Welcome to webrsa %s </important>', 'v'.file_get_contents( APP.DS.'VERSION.txt' ) ), 1, Shell::QUIET );
			$this->out( __d( 'cake_console', '<info>CakePHP %s Console</info>', 'v'.Configure::version() ), 1, Shell::QUIET );
			$this->out( '', 1, Shell::QUIET );
			$this->out( str_repeat( ' ', 25 ).$this->name.' Shell', 1, Shell::QUIET );
			$this->out( '', 1, Shell::QUIET );
			$this->hr( 1, '-', 75, Shell::QUIET );
			$this->out( __d( 'cake_console', '<info>App : </info><important>%s</important>', APP_DIR ) );
			$this->out( __d( 'cake_console', '<info>Path : </info><important>%s</important>', APP ) );
			if( !empty( $this->params ) ) {
				$this->_showParams();
			}
			$this->hr();
		}

		/**
		 *
		 */
		protected function _showParams() {
			$this->out( '<info>Connexion : </info><important>'.$this->connection->configKeyName.'</important>' );
			$this->out( '<info>Base de donnees : </info><important>'.$this->connection->config['database'].'</important>' );
			$this->out( '<info>Journalisation : </info><important>'.($this->params['log'] ? 'true' : 'false' ).'</important>' );
		}

		/**
		 *
		 */
		protected function _scritpEnd() {
			list($usec, $sec) = explode( ' ', microtime() );
			$this->_end = (float) $sec + (float) $usec;
			$elapsed_time = round( $this->_end - $this->_start, 5 );
			$this->out( '', 1, Shell::QUIET );
			$this->hr();
			$this->out( '<info>Temps écoulé : </info><important>'.$elapsed_time.'s</important>', 1, Shell::QUIET );

			if( $this->params['log'] ) {
				$this->out( "<info>Fichier de log : </info><important>".'APP/'.preg_replace( '/^'.str_replace( '/', '\/', APP ).'/', '', $this->_logFile ).'</important>', 1, Shell::QUIET );
				file_put_contents( $this->_logFile, strip_tags( $this->output ) );
			}
			$this->hr( 1, '-', 75, Shell::QUIET );
		}

		/**
		 *
		 * CakePHP 2.2.1 Shell's runCommand method override
		 *
		 *
		 *
		 *
		 * Runs the Shell with the provided argv.
		 *
		 * Delegates calls to Tasks and resolves methods inside the class. Commands are looked
		 * up with the following order:
		 *
		 * - Method on the shell.
		 * - Matching task name.
		 * - `main()` method.
		 *
		 * If a shell implements a `main()` method, all missing method calls will be sent to
		 * `main()` with the original method name in the argv.
		 *
		 * @param string $command The command name to run on this shell. If this argument is empty,
		 *   and the shell has a `main()` method, that will be called instead.
		 * @param array $argv Array of arguments to run the shell with. This array should be missing the shell name.
		 * @return void
		 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::runCommand
		 */
		public function runCommand( $command, $argv ) {
			$isTask = $this->hasTask( $command );
			$isMethod = $this->hasMethod( $command );
			$isMain = $this->hasMethod( 'main' );

			if( $isTask || $isMethod && $command !== 'execute' ) {
				array_shift( $argv );
			}

			try {
				$this->OptionParser = $this->getOptionParser();
				list($this->params, $this->args) = $this->OptionParser->parse( $argv, $command );
			}
			catch( ConsoleException $e ) {
				/*
				 * webrsa edit begin
				 *
				 * original :
				 *
				 * 		$this->out( $this->OptionParser->help( $command ) );
				 *
				 */
				$this->_welcome();
				$this->out();
				$this->out( '<error> An error as occured ! </error>' );
				$this->out();
				$this->out( '<warning>'.$e->getMessage().'</warning>' );
				$this->out();
				$this->out( $this->OptionParser->help() );
				/*
				 * webrsa edit end
				 */
				return false;
			}

			if( !empty( $this->params['quiet'] ) ) {
				$this->_useLogger( false );
			}

			$this->command = $command;
			if( !empty( $this->params['help'] ) ) {
				$this->_setConnection();
				return $this->_displayHelp( $command );
			}

			if( ($isTask || $isMethod || $isMain) && $command !== 'execute' ) {
				$this->startup();
			}

			if( $isTask ) {
				$command = Inflector::camelize( $command );
				/*
				 * webrsa edit begin
				 *
				 * original :
				 *
				 * 		return $this->{$command}->runCommand( 'execute', $argv );
				 */
				$return = $this->{$command}->runCommand( 'execute', $argv );
				$this->_scritpEnd();
				return $return;
				/*
				 * webrsa edit end
				 */
			}
			if( $isMethod ) {
				/*
				 * webrsa edit begin
				 *
				 * original :
				 *
				 * 		return $this->{$command}();
				 *
				 */
				$return = $this->{$command}();
				$this->_scritpEnd();
				return $return;

				/*
				 * webrsa edit end
				 */
			}
			if( $isMain ) {
				/*
				 * webrsa edit begin
				 *
				 * original :
				 *
				 * 		return $this->main();
				 */
				$return = $this->main();
				$this->_scritpEnd();
				return $return;
				/**
				 * webrsa edit end
				 */
			}

			$this->out( $this->OptionParser->help( $command ) );
			return false;
		}

		/**
		 *
		 * @param type $message
		 * @param type $newlines
		 * @return type
		 */
		public function out( $message = null, $newlines = 1, $level = Shell::NORMAL ) {
			if( !empty( $this->_logFile ) && isset($this->params['log']) && $this->params['log'] ) {
				$this->output .= $message;
				for( $i = 0; $i < $newlines; $i++ ) {
					$this->output .= $this->nl();
				}
			}

			if( $this->_iconvEncoding['output_encoding'] == 'ISO-8859-1' )
				return parent::out( utf8_decode( $message ), $newlines, $level );
			else {
				return parent::out( $message, $newlines, $level );
			}
		}

		/**
		 *
		 * @param type $message
		 * @param type $newlines
		 * @return type
		 */
		public function err( $message = null, $newlines = 1 ) {
			if( !empty( $this->_logFile ) && isset($this->params['log']) && $this->params['log'] ) {
				$this->output .= "Error: ".$message;
				for( $i = 0; $i < $newlines; $i++ ) {
					$this->output .= $this->nl();
				}
			}

			$message = $this->stderr->styleText( $message );
			$message = '<error>'.$message.'</error>';

			if( $this->_iconvEncoding['output_encoding'] == 'ISO-8859-1' )
				return parent::err( utf8_decode( $message ) );
			else {
				return parent::err( $message );
			}
		}

		/**
		 *
		 * @param type $newlines
		 * @param type $symbol
		 * @param type $length
		 */
		protected function _separator( $newlines = 1, $symbol = '.', $length = 75 ) {
			$this->out();
			$this->hr( $newlines, $symbol, $length );
		}

		/**
		 *
		 * @param type $msg
		 */
		protected function _wait( $msg = '' ) {
			$realMsg = 'Traitement en cours, veuillez patienter.';
			if( $msg != '' ) {
				$realMsg = $msg;
			}
			$strlen = strlen( $realMsg ) + 10;

			if ($strlen < 75){
				$strlen = 75;
			}

			$realMsg = str_pad( $realMsg, $strlen, ' ', STR_PAD_BOTH );


			$this->_separator( 1, '.', $strlen );
			$this->out();
			$this->out( $realMsg );
			$this->_separator( 1, '.', $strlen );
		}

	}
?>