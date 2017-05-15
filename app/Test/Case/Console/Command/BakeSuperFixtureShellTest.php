<?php
	/**
	 * Code source de la classe BakeSuperFixtureShellTest.
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
	App::uses( 'BakeSuperFixtureShell', 'SuperFixture.Console/Command' );
	App::uses('SuperFixture', 'SuperFixture.Utility');

	/**
	 * BakeSuperFixtureShellTest class
	 *
	 * @package app.Test.Case.Console.Command
	 */
	class BakeSuperFixtureShellTest extends CakeTestCase
	{
		/**
		 *
		 * @var AppShell
		 */
		public $Shell = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();

			$out = $this->getMock('ConsoleOutput', array(), array(), '', false);
			$in = $this->getMock('ConsoleInput', array(), array(), '', false);

			$this->Shell = $this->getMock(
				'BakeSuperFixtureShell',
				array('out', 'err', '_stop'),
				array($out, $out, $in)
			);

			$this->Shell->params = array(
				'help' => false,
				'verbose' => false,
				'quiet' => false,
				'connection' => 'test',
				'log' => false,
				'headers' => 'true',
				'separator' => ',',
				'delimiter' => '"',
			);
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Shell );
			parent::tearDown();
		}

		/**
		 * Test que les conditions pour réaliser les tests soit en place (évite boucles infini et bugs)
		 */
		public function testPrerequis() {
			$className = 'TestUnitaireShellSuperFixture';
			$path = APP.'Test'.DS.'SuperFixture'.DS;

			$pathIn = APP.'Vendor'.DS.'BakeSuperFixture'.DS.'DossierBaker.php';
			$pathOut = APP.'Test'.DS.'SuperFixture'.DS.'TestUnitaireShellSuperFixture.php';

			$this->assertEqual(file_exists($pathIn), true, "Le fichier DossierBaker doit exister et être readable");

			if (file_exists($pathOut) && !unlink($pathOut)) {
				trigger_error("Le fichier {$pathOut} n'a pas pu être supprimé");
			}

			$this->assertEqual(file_exists($pathOut), false, "Le fichier TestUnitaireShellSuperFixture ne doit pas exister");

			$outFile = fopen($pathOut, "w");
			if (!$outFile) {
				trigger_error("Le fichier ".$pathOut." n'a pas été crée ! Vérifiez les droits d'accès au dossier.");
			}

			$fileString = 'test';

			fputs($outFile, $fileString);
			fclose($outFile);

			$this->assertEqual(file_exists($pathOut), true, "Le fichier TestUnitaireShellSuperFixture doit exister après création");
		}

		/**
		 * Test de la méthode BakeSuperFixtureShell::main()
		 *
		 * @large
		 * @depends testPrerequis
		 */
		public function testMainDossier() {
			Faker\Factory::create('fr_FR')->seed(1234);
			SuperFixture::load($this, 'TestBakeDossier');

			$className = 'TestUnitaireShellSuperFixture';
			$path = APP.'Test'.DS.'SuperFixture'.DS;

			$this->Shell->args[0] = APP.'Vendor'.DS.'BakeSuperFixture'.DS.'DossierBaker.php';
			$this->Shell->args[1] = $path.$className.'.php';

			// Evite une boucle infinie si DossierBaker.php n'existe pas
			if (!file_exists($this->Shell->args[0])) {
				trigger_error("Le fichier {$this->Shell->args[0]} n'existe pas");
			}

			// Evite une boucle infinie en cas de fichier présent (test non terminé)
			if (file_exists($this->Shell->args[1]) && !unlink($this->Shell->args[1])) {
				trigger_error("Le fichier {$this->Shell->args[1]} n'a pas pu être supprimé");
			}

			$this->Shell->initialize();
			$this->Shell->loadTasks();
			$this->Shell->startup();
			$this->Shell->main();

			$this->Shell->expects($this->any())->method('_stop')->with(0);

			require_once $path.$className.'.php';
			$obj = new $className();
			$data = $obj->getData();
			unlink(APP.'Test'.DS.'SuperFixture'.DS.'TestUnitaireShellSuperFixture.php');

			$signatures = array();
			foreach ($data as $key => $value) {
				$signatures[$key] = md5(json_encode($value));
			}

			$expected = array(
				'Serviceinstructeur' => 'd926964fb4d3ded3d27c9436ae25acc1',
				'Group' => 'edeeb8211b69c03676936ad5e11f67c4',
				'User' => '3d66e8efb8357f175af1665969dda5db',
				'Typeorient' => '8a9ca80641d43f3e04fd73c1dd7a28b5',
				'Structurereferente' => '689fe106291131f8f829069af311c402',
				'Referent' => '520b978efb9063e49897d97d25cd0bc7',
				'Adresse' => 'a39cfc9047a2385dc088b2fa2f8edd2b',
				'Dossier' => '2f97be579a3478f2b15c9961969a2c8f',
				'Situationdossierrsa' => 'd622b08137f85e7bc40ec15ec201d88a',
				'Detaildroitrsa' => '390c8cad406d582fc9e3d9697d81d02a',
				'Detailcalculdroitrsa' => '0e0366721bdae4a0d64398b7afbe21a7',
				'Foyer' => 'dd813c2274e63d849ecd60f51c2c8851',
				'Adressefoyer' => '05ac2df1a451b0094163e7e8dbc4d24b',
				'Personne' => '58d5182c64c1c520041969d1f3bee71a',
				'Prestation' => '7ab1bda52914bed0f5343d9cfd6cab7c',
				'Calculdroitrsa' => 'cb38e473c0c885ef4e9426bd6d72718b',
				'Orientstruct' => '21a16f7f2acfa67f94e42315f17ac09c'
			);

			$this->assertEqual($signatures, $expected, "Signature des données différentes");
		}

		/**
		 * Test de la méthode BakeSuperFixtureShell::main()
		 *
		 * @large
		 * @depends testPrerequis
		 */
		public function testMainCui() {
			Faker\Factory::create('fr_FR')->seed(1234);
			Configure::write('Cui.Numeroconvention', '0661300001');

			$className = 'TestUnitaireShellSuperFixture2';
			$path = APP.'Test'.DS.'SuperFixture'.DS;

			SuperFixture::load($this, 'TestBakeDossier');

			$this->Shell->args[0] = APP.'Vendor'.DS.'BakeSuperFixture'.DS.'CuiBaker.php';
			$this->Shell->args[1] = $path.$className.'.php';

			// Evite une boucle infinie si DossierBaker.php n'existe pas
			if (!file_exists($this->Shell->args[0])) {
				trigger_error("Le fichier {$this->Shell->args[0]} n'existe pas");
			}

			// Evite une boucle infinie en cas de fichier présent (test non terminé)
			if (file_exists($this->Shell->args[1]) && !unlink($this->Shell->args[1])) {
				trigger_error("Le fichier {$this->Shell->args[1]} n'a pas pu être supprimé");
			}

			$this->Shell->initialize();
			$this->Shell->loadTasks();
			$this->Shell->startup();
			$this->Shell->main();

			$this->Shell->expects($this->any())->method('_stop')->with(0);

			require_once $path.$className.'.php';
			$obj = new $className();
			$data = $obj->getData();
			unlink(APP.'Test'.DS.'SuperFixture'.DS.'TestUnitaireShellSuperFixture2.php');

			$signatures = array();
			foreach ($data as $key => $value) {
				// Suppression des dates dans le CUI car change selon l'année en cours
				if ($key === 'Cui') {
					unset(
						$value['dateembauche'],
						$value['findecontrat'],
						$value['effetpriseencharge'],
						$value['finpriseencharge'],
						$value['decisionpriseencharge'],
						$value['faitle'],
						$value['signaturele'],
						$value['created'],
						$value['modified']
					);
				}
				$signatures[$key] = md5(json_encode($value));
			}

			$expected = array(
				'Serviceinstructeur' => 'e17c3d89d58d230ae297d7e80edb6e0b',
				'Group' => '1368d03e59b7a026843377a85191e871',
				'User' => '6172c56820815e167f3258f8007a07ae',
				'Typeorient' => 'c3bbfcd6d9c1f219cdfb48b38bf28c2f',
				'Structurereferente' => '9bba0ce91c06e7b68adf6f1f1a97803d',
				'Referent' => '520b978efb9063e49897d97d25cd0bc7',
				'Adresse' => '925736734754782482818b974db72e6d',
				'Dossier' => 'd2d858405aa120198859163acf742711',
				'Situationdossierrsa' => 'ca199b8e978e9002f76176f88733ae7a',
				'Detaildroitrsa' => 'b2874d866d77687f17b7782fe2201686',
				'Detailcalculdroitrsa' => '2194a1ec3414ebfc95a6159201ad19e6',
				'Foyer' => 'e1bc0c18c7e872ed2c970ead148fe426',
				'Adressefoyer' => '7f2b048a048035f6ccf17b8f20fc2016',
				'Personne' => '1f602b54278e8ed3a0a7ed4b45451ed5',
				'Prestation' => 'd68485f7114462c93aed705466e6edf6',
				'Calculdroitrsa' => '56f08763c56c5c93112bf4e9f3691496',
				'Orientstruct' => '4ea836ff14882bde3ddcc0d4c91699de',
				'Cui' => 'cb4fa283bc893a40a912d8be511f5a21'
			);

			$this->assertEqual($signatures, $expected, "Signature des données différentes");
		}
	}
?>