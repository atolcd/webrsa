<?php
	/**
	 * Code source de la classe CsvFileReaderTest.
	 *
	 * PHP 5.3
	 *
	 * @package Csv
	 * @subpackage Test.Case.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'CsvFileReader', 'Csv.Utility' );

	/**
	 * La classe CsvFileReaderTest ...
	 *
	 * @package Csv
	 * @subpackage Test.Case.Utility
	 */
	class CsvFileReaderTest extends CakeTestCase
	{
		public $ressourceDir = null;

		public $csvFileAnimals = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			// TODO: define( 'CSV_PLUGIN_ROOT_DIR' ); dans le bootstrap.php
			$this->ressourceDir = dirname( dirname( dirname( __FILE__ ) ) ).DS.'Ressource';

			$this->csvFileInexistant = $this->ressourceDir.DS.'inexistant.csv';
			$this->csvFileEmpty = $this->ressourceDir.DS.'empty.csv';
			$this->csvFileAnimals = $this->ressourceDir.DS.'animals.csv';
			$this->csvFileAnimalsFrench = $this->ressourceDir.DS.'animals_fr_iso88591.csv';
		}

		/**
		 * Test du constructeur avec un fichier inexistant.
		 *
		 * @expectedException RuntimeException
		 */
		public function testConstructInexistantFile() {
			$Csv = new CsvFileReader( $this->csvFileInexistant );
		}

		/**
		 * Test de la méthode CsvFileReader::count()
		 */
		public function testCount() {
			$Csv = new CsvFileReader( $this->csvFileAnimals, array( 'headers' => true ) );
			$result = $Csv->count();
			$expected = 2;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode CsvFileReader::current()
		 */
		public function testCurrent() {
			$Csv = new CsvFileReader( $this->csvFileAnimals, array( 'headers' => true ) );
			$result = $Csv->current();
			$expected = array(
				0 => 'Jaguar',
				1 => 'Panthera onca',
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode CsvFileReader::key()
		 */
		public function testKey() {
			$Csv = new CsvFileReader( $this->csvFileAnimals, array( 'headers' => true ) );
			$result = $Csv->key();
			$expected = 0;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode CsvFileReader::next()
		 */
		public function testNext() {
			$Csv = new CsvFileReader( $this->csvFileAnimals, array( 'headers' => true ) );
			$Csv->next();
			$result = $Csv->key();
			$expected = 1;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode CsvFileReader::valid()
		 */
		public function testValid() {
			$Csv = new CsvFileReader( $this->csvFileAnimals, array( 'headers' => true ) );
			$result = $Csv->valid();
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode CsvFileReader::headers()
		 */
		public function testHeaders() {
			$Csv = new CsvFileReader( $this->csvFileAnimals, array( 'headers' => true ) );
			$result = $Csv->headers();
			$expected = array (
				0 => 'Name',
				1 => 'Scientific name',
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>