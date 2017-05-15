<?php
	/**
	 * AllMultiDomainsTranslatorTests file
	 *
	 * PHP 5.3
	 *
	 * @package MultiDomainsTranslator
	 * @subpackage Test.Case
	 */
	CakePlugin::load( 'MultiDomainsTranslator' );

	/**
	 * AllMultiDomainsTranslatorTests class
	 *
	 * This test group will run all tests.
	 *
	 * @package MultiDomainsTranslator
	 * @subpackage Test.Case
	 */
	class AllMultiDomainsTranslatorTests extends PHPUnit_Framework_TestSuite
	{
		/**
		 * Test suite with all test case files.
		 */
		public static function suite() {
			$suite = new CakeTestSuite( 'All MultiDomainsTranslator tests' );
			$suite->addTestDirectoryRecursive( dirname( __FILE__ ).DS.'..'.DS.'Case'.DS );
			return $suite;
		}
	}
?>