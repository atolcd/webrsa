<?php
	/**
	 * AllPrototypeTests file
	 *
	 * PHP 5.3
	 *
	 * @package Prototype
	 * @subpackage Test.Case
	 */
	CakePlugin::load( 'Prototype' );

	/**
	 * AllPrototypeTests class
	 *
	 * This test group will run all tests.
	 *
	 * @package Prototype
	 * @subpackage Test.Case
	 */
	class AllPrototypeTests extends PHPUnit_Framework_TestSuite
	{
		/**
		 * Test suite with all test case files.
		 *
		 * @return void
		 */
		public static function suite() {
			$suite = new CakeTestSuite( 'All Prototype tests' );
			$suite->addTestDirectoryRecursive( dirname( __FILE__ ).DS.'..'.DS.'Case'.DS );
			return $suite;
		}
	}
?>