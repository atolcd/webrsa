<?php
	/**
	 * AllValidationTests file
	 *
	 * PHP 5.3
	 *
	 * @package ConfigurableQuery
	 * @subpackage Test.Case
	 */
	CakePlugin::load( 'ConfigurableQuery' );

	/**
	 * AllValidationTests class
	 *
	 * This test group will run all tests.
	 *
	 * @package ConfigurableQuery
	 * @subpackage Test.Case
	 */
	class AllConfigurableQueryTests extends PHPUnit_Framework_TestSuite
	{
		/**
		 * Test suite with all test case files.
		 *
		 * @return void
		 */
		public static function suite() {
			$suite = new CakeTestSuite( 'All ConfigurableQuery tests' );
			$suite->addTestDirectoryRecursive( dirname( __FILE__ ).DS.'..'.DS.'Case'.DS );
			return $suite;
		}
	}
?>