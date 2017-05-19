<?php
	/**
	 * Code source de la classe ImportCsvCodesRomeV3ShellTest.
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
	App::uses( 'ImportCsvCodesRomeV3Shell', 'Console/Command' );

	/**
	 * ImportCsvCodesRomeV3ShellTest class
	 *
	 * @package app.Test.Case.Console.Command
	 */
	class ImportCsvCodesRomeV3ShellTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés par ces tests unitaires.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Appellationromev3',
			'app.Coderomemetierdsp66',
			'app.Correspondanceromev2v3',
			'app.Domaineromev3',
			'app.Familleromev3',
			'app.Metierromev3'
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
			Configure::write( 'Cg.departement', 66 );
			Configure::write( 'Romev3.enabled', true );

			parent::setUp();

			$out = $this->getMock( 'ConsoleOutput', array( ), array( ), '', false );
			$in = $this->getMock( 'ConsoleInput', array( ), array( ), '', false );

			$this->Shell = $this->getMock(
				'ImportCsvCodesRomeV3Shell',
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
		 * Test de la méthode ImportCsvCodesRomeV3Shell::main()
		 * avec l'import de l'ensemble des lignes.
		 *
		 * @large
		 */
		public function testMainSuccess() {
			// 1. Le shell doit retourner un succès
			$this->Shell->expects( $this->any() )->method( '_stop' )->with( 0 );

			$this->Shell->args[0] = APP.DS.'Test'.DS.'File'.DS.'codes_rome_v3_emboites.csv';

			$this->Shell->initialize();
			$this->Shell->loadTasks();
			$this->Shell->startup();
			$this->Shell->main();

			// 2. On s'assure d'avoir les nouveaux enregistrements en base
			$Catalogueromev3 = ClassRegistry::init( 'Catalogueromev3' );
			$result = $Catalogueromev3->dependantSelects();
			$expected = array(
				'Catalogueromev3' => array(
					'familleromev3_id' => array(
						1 => 'A - AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX',
						2 => 'K - SERVICES A LA PERSONNE ET A LA COLLECTIVITÉ'
					),
					'domaineromev3_id' => array(
						'1_1' => 'A11 - Engins agricoles et forestiers',
						'2_2' => 'K13 - Aide à la vie quotidienne'
					),
					'metierromev3_id' => array(
						'1_1' => 'A1101 - Conduite d\'engins d\'exploitation agricole et forestière',
						'2_2' => 'K1304 - Services domestiques'
					),
					'appellationromev3_id' => array(
						'1_1' => 'Conducteur / Conductrice d\'engins d\'exploitation agricole',
						'2_2' => 'Employé / Employée de maison'
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ImportCsvCodesRomeV3Shell::main()
		 * avec le rejet de l'ensemble des lignes.
		 *
		 * @large
		 */
		public function testMainErrors() {
			// 1. Le shell doit retourner une erreur
			$this->Shell->expects( $this->any() )->method( '_stop' )->with( 1 );

			$this->Shell->args[0] = APP.DS.'Test'.DS.'File'.DS.'codes_rome_v3_emboites_errors.csv';

			$this->Shell->initialize();
			$this->Shell->loadTasks();
			$this->Shell->startup();
			$this->Shell->main();

			// 2. On s'assure de ne pas avoir de nouvel enregistrement en base
			$Catalogueromev3 = ClassRegistry::init( 'Catalogueromev3' );
			$result = $Catalogueromev3->dependantSelects();
			$expected = array(
				'Catalogueromev3' => array(
					'familleromev3_id' => array(
						1 => 'A - AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX'
					),
					'domaineromev3_id' => array(
						'1_1' => 'A11 - Engins agricoles et forestiers'
					),
					'metierromev3_id' => array(
						'1_1' => 'A1101 - Conduite d\'engins d\'exploitation agricole et forestière'
					),
					'appellationromev3_id' => array(
						'1_1' => 'Conducteur / Conductrice d\'engins d\'exploitation agricole'
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>