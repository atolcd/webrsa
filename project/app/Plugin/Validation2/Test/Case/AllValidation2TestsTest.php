<?php
	/**
	 * AllValidation2Tests file
	 *
	 * PHP 5.3
	 *
	 * @package Validation
	 * @subpackage Test.Case
	 */
	CakePlugin::load( 'Validation2', array( 'bootstrap' => true ) );

	/**
	 * AllValidation2Tests class
	 *
	 * This test group will run all tests.
	 *
	 * @package Validation
	 * @subpackage Test.Case
	 */
	class AllValidation2Tests extends PHPUnit_Framework_TestSuite
	{
		/**
		 * Test suite with all test case files.
		 *
		 * @return void
		 */
		public static function suite() {
			$suite = new CakeTestSuite( 'All Validation2 tests' );
			$suite->addTestDirectoryRecursive( dirname( __FILE__ ).DS.'..'.DS.'Case'.DS );
			return $suite;
		}
	}
?>