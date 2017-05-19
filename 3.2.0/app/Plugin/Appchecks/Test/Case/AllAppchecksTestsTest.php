<?php
	/**
	 * AllAppchecksTests file
	 *
	 * PHP 5.3
	 *
	 * @package Appchecks
	 * @subpackage Test.Case
	 */
	CakePlugin::load( 'Appchecks' );

	/**
	 * AllAppchecksTests class
	 *
	 * This test group will run all tests.
	 *
	 * @package Appchecks
	 * @subpackage Test.Case
	 */
	class AllAppchecksTests extends PHPUnit_Framework_TestSuite
	{
		/**
		 * Test suite with all test case files.
		 *
		 * @return void
		 */
		public static function suite() {
			$suite = new CakeTestSuite( 'All Appchecks tests' );
			$suite->addTestDirectoryRecursive( dirname( __FILE__ ).DS.'..'.DS.'Case'.DS );
			return $suite;
		}
	}
?>