<?php
	/**
	 * AllCsvTests file
	 *
	 * PHP 5.3
	 *
	 * @package Csv
	 * @subpackage Test.Case
	 */
	CakePlugin::load( 'Csv' );

	/**
	 * AllCsvTests class
	 *
	 * This test group will run all tests.
	 *
	 * @package Csv
	 * @subpackage Test.Case
	 */
	class AllCsvTests extends PHPUnit_Framework_TestSuite
	{
		/**
		 * Test suite with all test case files.
		 */
		public static function suite() {
			$suite = new CakeTestSuite( 'All Csv tests' );
			$suite->addTestDirectoryRecursive( dirname( __FILE__ ).DS.'..'.DS.'Case'.DS );
			return $suite;
		}
	}
?>