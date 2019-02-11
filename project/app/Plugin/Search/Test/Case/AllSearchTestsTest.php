<?php
	/**
	 * AllSearchTests file
	 *
	 * PHP 5.3
	 *
	 * @package Search
	 * @subpackage Test.Case
	 */
	CakePlugin::load( 'Search' );

	/**
	 * AllSearchTests class
	 *
	 * This test group will run all tests.
	 *
	 * @package Search
	 * @subpackage Test.Case
	 */
	class AllSearchTests extends PHPUnit_Framework_TestSuite
	{
		/**
		 * Test suite with all test case files.
		 *
		 * @return void
		 */
		public static function suite() {
			$suite = new CakeTestSuite( 'All Search tests' );
			$suite->addTestDirectoryRecursive( dirname( __FILE__ ).DS.'..'.DS.'Case'.DS );
			return $suite;
		}
	}
?>