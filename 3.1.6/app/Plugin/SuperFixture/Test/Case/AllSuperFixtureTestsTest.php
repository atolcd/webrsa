<?php
	/**
	 * AllSuperFixtureTests file
	 *
	 * PHP 5.3
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case
	 */
	CakePlugin::load( 'SuperFixture' );

	/**
	 * AllSuperFixtureTests class
	 *
	 * This test group will run all tests.
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case
	 */
	class AllSuperFixtureTests extends PHPUnit_Framework_TestSuite
	{
		/**
		 * Test suite with all test case files.
		 */
		public static function suite() {
			$suite = new CakeTestSuite( 'All SuperFixture tests' );
			$suite->addTestDirectoryRecursive( dirname( __FILE__ ).DS.'..'.DS.'Case'.DS );
			return $suite;
		}
	}
?>