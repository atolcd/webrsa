<?php
	/**
	 * AllValidationTests file
	 *
	 * PHP 5.3
	 *
	 * @package AnalyseSql
	 * @subpackage Test.Case
	 */
	CakePlugin::load( 'AnalyseSql' );

	/**
	 * AllValidationTests class
	 *
	 * This test group will run all tests.
	 *
	 * @package AnalyseSql
	 * @subpackage Test.Case
	 */
	class AllAnalyseSqlTests extends PHPUnit_Framework_TestSuite
	{
		/**
		 * Test suite with all test case files.
		 *
		 * @return void
		 */
		public static function suite() {
			$suite = new CakeTestSuite( 'All AnalyseSql tests' );
			$suite->addTestDirectoryRecursive( dirname( __FILE__ ).DS.'..'.DS.'Case'.DS );
			return $suite;
		}
	}
?>