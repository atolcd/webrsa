<?php
	/**
	 * Code source de la classe ImportcsvCataloguespdisfps93ShellTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ConsoleOutput', 'Console' );
	App::uses( 'ConsoleInput', 'Console' );
	App::uses( 'ShellDispatcher', 'Console' );
	App::uses( 'Shell', 'Console' );
	App::uses( 'AppShell', 'Console/Command' );
	App::uses( 'ImportcsvCataloguespdisfps93Shell', 'Console/Command' );

	/**
	 * ImportcsvCataloguespdisfps93ShellTest class
	 *
	 * @package app.Test.Case.Console.Command
	 */
	class ImportcsvCataloguespdisfps93ShellTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés par ces tests unitaires.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'Thematiquefp93',
			'Categoriefp93',
			'Filierefp93',
			'Prestatairefp93',
			'Adresseprestatairefp93',
			'Actionfp93',
		);

		/**
		 *
		 * @var AppShell
		 */
		public $Shell = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			Configure::write( 'Cg.departement', 93 );

			parent::setUp();

			$out = $this->getMock( 'ConsoleOutput', array( ), array( ), '', false );
			$in = $this->getMock( 'ConsoleInput', array( ), array( ), '', false );

			$this->Shell = $this->getMock(
				'ImportcsvCataloguespdisfps93Shell',
				array( 'out', 'err', '_stop' ),
				array( $out, $out, $in )
			);

			$this->Shell->params['headers'] = 'true';
			$this->Shell->params['separator'] = ',';
			$this->Shell->params['delimiter'] = '"';
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Shell );
			parent::tearDown();
		}

		/**
		 * Test de la méthode ImportcsvCataloguespdisfps93Shell::main()
		 * avec l'import de l'ensemble des lignes.
		 *
		 * @large
		 */
		public function testMainSuccess() {
			$this->Shell->expects( $this->any() )->method( '_stop' )->with( 0 );

			$this->Shell->args[0] = APP.DS.'Test'.DS.'File'.DS.'cataloguepdi.csv';

			$this->Shell->initialize();
			$this->Shell->loadTasks();
			$this->Shell->startup();
			$this->Shell->main();
		}

		/**
		 * Test de la méthode ImportcsvCataloguespdisfps93Shell::main()
		 * avec le rejet de l'ensemble des lignes.
		 *
		 * @large
		 */
		public function testMainErrors() {
			$this->Shell->expects( $this->any() )->method( '_stop' )->with( 1 );

			$this->Shell->args[0] = APP.DS.'Test'.DS.'File'.DS.'cataloguepdi_errors.csv';

			$this->Shell->initialize();
			$this->Shell->loadTasks();
			$this->Shell->startup();
			$this->Shell->main();
		}
	}
?>