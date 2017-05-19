<?php
	/**
	 * Fichier source de la classe ControllertcShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );
	App::uses( 'File', 'Utility' );

	/**
	 * La classe ControllertcShell ...
	 *
	 * @package app.Console.Command
	 */
	class ControllertcShell extends XShell
	{

		/**
		 *
		 * @var type
		 */
		public $controllers = array( );

		/**
		 *
		 * @return type
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->addOption( 'force', array(
				'short' => 'f',
				'help' => '',
				'default' => 'false',
				'boolean' => true
			) );
			$parser->addOption( 'all', array(
				'short' => 'a',
				'help' => '',
				'default' => 'false',
				'boolean' => true
			) );
			$parser->addOption( 'controllers', array(
				'short' => 'C',
				'help' => 'Liste des controlleurs séparés par des virgules (sans esapce). Ex: controller1,controller2,controller3 '
			) );
			return $parser;
		}

		/**
		 *
		 */
		protected function _showParams() {
			parent::_showParams();
			$this->out( '<info>Liste des controlleurs : </info><important>'.(!empty( $this->params['controllers'] ) ? $this->params['controllers'] : '').'</important>' );
			$this->out( '<info>Prendre en compte tous les controlleurs : </info><important>'.($this->params['all'] ? 'true' : 'false').'</important>' );
			$this->out( '<info>Forcer l\'écriture du fichier : </info><important>'.($this->params['force'] ? 'true' : 'false').'</important>' );
		}

		/**
		 *
		 */
		public function startup() {
			if( $this->params['all'] !== true && empty( $this->params['controllers'] ) ) {
				$this->out( $this->OptionParser->help() );
				$this->_stop( 1 );
			}


			if( !empty( $this->params['controllers'] ) ) {
				$this->controllers = explode( ',', $this->params['controllers'] );
			}

			parent::startup();
		}

		/**
		 *
		 */
		public function main() {
			if( $this->params['all'] ) {
				if( $dir = opendir( sprintf( '%sController/', APP ) ) ) {
					while( ($file = readdir( $dir )) !== false ) {
						$explose = explode( '~', $file );
						if( (count( $explose ) == 1) && (!is_dir( $file )) && ($file != '.svn') && ($file != 'empty') )
							$this->controllers[] = $file;
					}
					closedir( $dir );
				}

				if( !file_exists( sprintf( '%sController/AppController.php', APP ) ) )
					$this->controllers[] = 'app';
			}
			else {
				foreach( $this->controllers as $controller ) {
					if( $controller == 'app' ) {
						if( !file_exists( sprintf( '%sController/AppController.php', APP ) ) )
							$this->err( 'This app controller doesn\'t exist.' );
					}
					else {
						if( !file_exists( sprintf( '%sController/%sController.php', APP, Inflector::camelize( $controller ) ) ) )
							$this->err( sprintf( 'The %s controller doesn\'t exist.', Inflector::camelize( $controller ) ) );
					}
				}
			}

			if( !empty( $this->controllers ) ) {
				foreach( $this->controllers as $controller ) {
					$explose = explode( '.php', $controller );
					$nameFile = $explose[0];
					$name = Inflector::classify( $controller );
					$explose = explode( 'Controller.php', $name );
					$name = $explose[0];

					$file = sprintf( '%sTest/Case/Controller/%sTest.php', APP, Inflector::camelize( $nameFile ) );
					$File = new File( $file );
					if( $File->exists() && !$this->params['force'] ) {
						$this->err( sprintf( 'File %s already exists, use --force option.', $file ) );
						continue;
					}

					$out = array( );
					$out[] = '<?php';
					$out[] = '';
					$out[] = '	require_once( dirname( __FILE__ ).\'/../cake_app_controller_test_case.php\' );';
					$out[] = '';
					$out[] = sprintf( '	App::uses( \'%sController\', \'Controller\' );', $name );
					$out[] = '';
					$out[] = sprintf( '	class Test%sController extends %sController {', $name, $name );
					$out[] = '';
					$out[] = '		public $autoRender = false;';
					$out[] = '		public $redirectUrl;';
					$out[] = '		public $redirectStatus;';
					$out[] = '		public $renderedAction;';
					$out[] = '		public $renderedLayout;';
					$out[] = '		public $renderedFile;';
					$out[] = '		public $stopped;';
					$out[] = sprintf( '		public $name=\'%s\';', $name );
					$out[] = '';
					$out[] = '		public function redirect($url, $status = null, $exit = true) {';
					$out[] = '			$this->redirectUrl = $url;';
					$out[] = '			$this->redirectStatus = $status;';
					$out[] = '		}';
					$out[] = '';
					$out[] = '		public function render($action = null, $layout = null, $file = null) {';
					$out[] = '			$this->renderedAction = $action;';
					$out[] = '			$this->renderedLayout = (is_null($layout) ? $this->layout : $layout);';
					$out[] = '			$this->renderedFile = $file;';
					$out[] = '		}';
					$out[] = '';
					$out[] = '		public function _stop($status = 0) {';
					$out[] = '			$this->stopped = $status;';
					$out[] = '		}';
					$out[] = '';
					$out[] = '		public function assert( $condition, $error = \'error500\', $parameters = array() ) {';
					$out[] = '			$this->condition = $condition;';
					$out[] = '			$this->error = $error;';
					$out[] = '			$this->parameters = $parameters;';
					$out[] = '		}';
					$out[] = '';
					$out[] = '	}';
					$out[] = '';
					$out[] = sprintf( '	class %sControllerTest extends CakeAppControllerTestCase {', $name );
					$out[] = '';
					$out[] = '	}';
					$out[] = '';
					$out[] = '?>';

					$File->write( join( "\n", $out ) );
					$this->out( sprintf( '-> Create %s TestCase', $name ) );
				}
			}
			else {
				$this->_stop( 1 );
			}
		}

	}
?>
