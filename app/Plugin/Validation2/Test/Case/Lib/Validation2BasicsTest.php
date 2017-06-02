<?php
	/**
	 * Validation2BasicsTest file
	 *
	 * PHP 5.3
	 *
	 * @package Validation2
	 * @subpackage Test.Case.Lib
	 */
	CakePlugin::load( 'Validation2', array( 'bootstrap' => true ) );

	/**
	 * Validation2BasicsTest class
	 *
	 * @package Validation2
	 * @subpackage Test.Case.Lib
	 */
	class Validation2BasicsTest extends CakeTestCase
	{
		/**
		 * Test de la fonction groupKeysByValues().
		 */
		public function testGroupKeysByValues() {
			$result = groupKeysByValues( array( '0' => 'integer', '2' => 'float', '3' => 'integer' ) );
			$expected = array(
				'integer' => array( 0, 3 ),
				'float' => array( 2 ),
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction cacheKey().
		 */
		public function testCacheKey() {
			$result = cacheKey( array( 'test', 'Foo', 'Bar', 'Baz' ) );
			$expected = 'test_Foo_Bar_Baz';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = cacheKey( array( 'test', 'Foo', 'Bar', 'Baz' ), true );
			$expected = 'test__foo__bar__baz';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>