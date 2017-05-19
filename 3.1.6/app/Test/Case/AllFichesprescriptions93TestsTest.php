<?php
	/**
	 * AllFichesprescriptions93TestsTests file
	 *
	 * PHP 5.3
	 *
	 * @package       app.Test.Case
	 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
	 */
	App::uses('Folder', 'Utility');

	/**
	 * AllFichesprescriptions93TestsTests class
	 *
	 * This test group will run all tests.
	 *
	 * @see           http://book.cakephp.org/2.0/en/development/testing.html
	 * @package       app.Test.Case
	 */
	class AllFichesprescriptions93TestsTest extends PHPUnit_Framework_TestSuite
	{
		/**
		 * Recursively adds all the files in a directory to the test suite.
		 *
		 * @param string $directory The directory subtree to add tests from.
		 * @return void
		 */
		public static function addTestDirectoryRecursiveFilter( CakeTestSuite $Suite, $directory = '.', $filters ) {
			$Folder = new Folder( $directory );
			$files = $Folder->tree( null, true, 'files' );

			foreach( $files as $file ) {
				if( substr( $file, -4 ) === '.php' && preg_match( '/('.implode( '|', $filters ).')/i', $file ) ) {
					$Suite->addTestFile( $file );
				}
			}
		}
		/**
		 * Test suite with all test case files.
		 *
		 * @return void
		 */
		public static function suite() {
			$Suite = new CakeTestSuite( 'Suite de tests unitaires du module "Fiche de prescription" (CG 93)' );
			$filters = array( 'ficheprescription93', 'fichesprescriptions93', 'fp93', 'fps93' );
			self::addTestDirectoryRecursiveFilter( $Suite, TESTS.DS.'Case'.DS, $filters );
			return $Suite;
		}
	}
?>