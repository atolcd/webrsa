<?php
	/**
	 * AllValidationTests file
	 *
	 * PHP 5.3
	 *
	 * @package Password
	 * @subpackage Test.Case
	 */
	CakePlugin::load( 'Password' );

	/**
	 * AllValidationTests class
	 *
	 * This test group will run all tests.
	 *
	 * @package Password
	 * @subpackage Test.Case
	 */
	class AllPasswordTests extends PHPUnit_Framework_TestSuite
	{
		/**
		 * Test suite with all test case files.
		 *
		 * @return void
		 */
		public static function suite() {
			$suite = new CakeTestSuite( 'All Password tests' );
			$suite->addTestDirectoryRecursive( dirname( __FILE__ ).DS.'..'.DS.'Case'.DS );
			return $suite;
		}
	}
?>