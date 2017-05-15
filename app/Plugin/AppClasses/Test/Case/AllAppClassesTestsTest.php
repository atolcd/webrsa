<?php
	/**
	 * AllValidationTests file
	 *
	 * PHP 5.3
	 *
	 * @package AppClasses
	 * @subpackage Test.Case
	 */
	CakePlugin::load( 'AppClasses' );

	/**
	 * AllValidationTests class
	 *
	 * This test group will run all tests.
	 *
	 * @package AppClasses
	 * @subpackage Test.Case
	 */
	class AllAppClassesTests extends PHPUnit_Framework_TestSuite
	{
		/**
		 * Test suite with all test case files.
		 *
		 * @return void
		 */
		public static function suite() {
			$suite = new CakeTestSuite( 'All AppClasses tests' );
			$suite->addTestDirectoryRecursive( dirname( __FILE__ ).DS.'..'.DS.'Case'.DS );
			return $suite;
		}
	}
?>