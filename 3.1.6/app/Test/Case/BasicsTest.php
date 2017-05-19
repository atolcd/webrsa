<?php
	/**
	 * BasicsTest file
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case
	 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
	 */

	/**
	 * ParentClass class
	 *
	 * @package app.Test.Case
	 */
	class ParentClass
	{
		public function foo() {}
	}

	/**
	 * ChildClass class
	 *
	 * @package app.Test.Case
	 */
	class ChildClass extends ParentClass
	{
		public function foo() {}

		public function bar() {}
	}

	/**
	 * BasicsTest class
	 *
	 * @see http://book.cakephp.org/2.0/en/development/testing.html
	 * @package app.Test.Case
	 */
	class BasicsTest extends CakeTestCase
	{
		/**
		 * Test de la fonction app_version().
		 */
		public function testAppVersion() {
			$result = app_version();
			$this->assertPattern( '/^[0-9]+\.[0-9]+/', $result );
		}

		/**
		 * Test de la fonction array_filter_keys().
		 */
		public function testArrayFilterKeys() {
			$array = array( 'foo' => 1, 'bar' => 2 );

			$result = array_filter_keys( $array, array( 'foo' ), false );
			$expected = array( 'foo' => 1 );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = array_filter_keys( $array, array( 'foo' ), true );
			$expected = array( 'bar' => 2 );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction recursive_key_value_preg_replace().
		 */
		public function testRecursiveKeyValuePregReplace() {
			$array = array( 'foo' => 1, 'bar' => 'foo', 'baz' => array( 'foo' => 'foo' ) );

			$result = recursive_key_value_preg_replace( $array, array( '/foo/' => 'Foo' ) );
			$expected = array( 'Foo' => 1, 'bar' => 'Foo', 'baz' => array( 'Foo' => 'Foo' ) );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction strallpos().
		 */
		public function testStrallpos() {
			$string = 'Les chaussettes de l\'archiduchesse sont-elles sèches, archi-sèches ?';

			$result = strallpos( $string, "'" );
			$expected = array( 20 );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = strallpos( $string, 'sse' );
			$expected = array( 8, 31 );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction strallpos() avec une erreur.
		 */
		public function testStrallposError() {
			$string = 'Les chaussettes de l\'archiduchesse sont-elles sèches, archi-sèches ?';

			$this->expectError( 'PHPUnit_Framework_Error_Warning' );
			strallpos( $string, 'sse', ( strlen( $string ) + 1 ) );
		}

		/**
		 * Test de la fonction model_field().
		 */
		public function testModelField() {
			$result = model_field( 'Foo.bar' );
			$expected = array( 'Foo', 'bar' );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = model_field( 'Foo.Bar.baz' );
			$expected = array( 'Bar', 'baz' );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction model_field() avec une erreur.
		 */
		public function testModelFieldWithError1() {
			$result = @model_field( 'Foo' );
			$this->assertEqual( $result, null, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction model_field() avec une erreur.
		 */
		public function testModelFieldWithError2() {
			$this->expectError( 'PHPUnit_Framework_Error_Warning' );
			model_field( 'Foo' );
		}

		/**
		 * Test de la fonction byteSize().
		 */
		public function testByteSize() {
			$result = byteSize( 1024 );
			$expected = '1.00 KB';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = byteSize( 2 * 1024 * 1024 );
			$expected = '2.00 MB';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = byteSize( 3 * 1024 * 1024 * 1024 );
			$expected = '3.00 GB';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction valid_int().
		 */
		public function testValidInt() {
			$result = valid_int( 1024 );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = valid_int( '1024' );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = valid_int( 'foo' );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = valid_int( null );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction valid_date().
		 */
		public function testValidDate() {
			$result = valid_date( array( 'day' => '30', 'month' => '01', 'year' => '2009' ) );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = valid_date( array( 'day' => '30', 'month' => '', 'year' => '2009' ) );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = valid_int( null );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction date_short().
		 */
		public function testDateShort() {
			$result = date_short( '2012-01-02' );
			$expected = '02/01/2012';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = date_short( '2012-02-28 11:05:33' );
			$expected = '28/02/2012';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = date_short( null );
			$expected = null;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction required().
		 */
		public function testRequired() {
			$result = required( 'Foo' );
			$expected = 'Foo <abbr class="required" title="Champ obligatoire">*</abbr>';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction readTimeout().
		 */
		public function testReadTimeout() {
			$saved = Configure::read( 'Session.save' );

			Configure::write( 'Session.save', null );
			$result = readTimeout();
			$this->assertPattern( '/^[0-9]+$/', (string)$result );

			Configure::write( 'Session.save', 'cake' );
			$result = readTimeout();
			$this->assertPattern( '/^[0-9]+$/', (string)$result );

			Configure::write( 'Session.save', $saved );
		}

		/**
		 * Test de la fonction sec2hms().
		 */
		public function testSec2hms() {
			$result = sec2hms( 12 );
			$expected = '0:00:12';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = sec2hms( ( ( 60 * 2 ) + 12 ) );
			$expected = '0:02:12';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = sec2hms( ( ( 3 * 60 * 60 ) + ( 2* 60 ) + 12 ) );
			$expected = '3:02:12';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = sec2hms( ( ( 3 * 60 * 60 ) + ( 2* 60 ) + 12 ), true );
			$expected = '03:02:12';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction suffix().
		 */
		public function testSuffix() {
			$result = suffix( '11_4' );
			$expected = 4;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = suffix( '11_4.2', '.' );
			$expected = 2;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = suffix( '11+4', '+' );
			$expected = '4';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = suffix( '12+-+5', '+-+' );
			$expected = '5';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction prefix().
		 */
		public function testPrefix() {
			$result = prefix( '11_4' );
			$expected = 11;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = prefix( '11_4.2', '.' );
			$expected = '11_4';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = prefix( '11+4', '+' );
			$expected = '11';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = prefix( '12+-+5', '+-+' );
			$expected = '12';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction array_depth().
		 */
		public function testArrayDepth() {
			$result = array_depth( array( array( null ) ) );
			$expected = 2;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction dateComplete().
		 */
		public function testDateComplete() {
			$data = array( 'User' => array( 'birthday' => array( 'hour' => 10, 'minute' => 20, 'second' => 30 ) ) );

			$result = dateComplete( $data, 'User.birthday' );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = dateComplete( $data, 'User.arrival' );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction implode_assoc().
		 */
		public function testImplodeAssoc() {
			$data = array( 'foo' => 'bar', 'bar' => 'baz', 'baz' => null );

			$result = implode_assoc( '/', ':', $data );
			$expected = 'foo:bar/bar:baz/baz:';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = implode_assoc( '/', ':', $data, false );
			$expected = 'foo:bar/bar:baz';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = implode_assoc( '/', ':', array( 'foo' => array( 'bar', 'baz' ) ) );
			$expected = 'foo[]:bar/foo[]:baz';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction array_avg().
		 */
		public function testArrayAvg() {
			$data = array( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 );
			$result = array_avg( $data );
			$expected = 5;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = array_avg( array() );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction array_range().
		 */
		public function testArrayRange() {
			$result = array_range( 2, 3 );
			$expected = array( 2 => 2, 3 => 3 );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = array_range( 2, 5, 2 );
			$expected = array( 2 => 2, 4 => 4 );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction array_intersects().
		 */
		public function testArrayIntersects() {
			$haystack = array( 1, 2, 4 );

			$result = array_intersects( array( 1, 2, 3 ), $haystack );
			$expected = array( 1, 2 );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = array_intersects( array( 3, 5 ), $haystack );
			$expected = array();
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction array_filter_values().
		 */
		public function testArrayFilterValues() {
			$data = array( 'foo' => 'bar', 'bar' => 'baz', 'baz' => null );

			$result = array_filter_values( $data, array( 'bar' ) );
			$expected = array( 'foo' => 'bar' );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = array_filter_values( $data, array( 'bar' ), true );
			$expected = array( 'bar' => 'baz', 'baz' => null );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction get_this_class_methods().
		 */
		public function testGetThisClassMethods() {
			$result = get_this_class_methods( 'ParentClass' );
			$expected = array( 'foo' );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = get_this_class_methods( 'ChildClass' );
			$expected = array( 1 => 'bar' );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction array_any_key_exists().
		 */
		public function testArrayAnyKeyExists() {
			$data = array( 'foo' => 'bar', 'bar' => 'baz', 'baz' => null );

			$result = array_any_key_exists( array( 'bar', 'mu' ), $data );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = array_any_key_exists( 'mu', $data );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction nullify_empty_values().
		 */
		public function testNullifyEmptyValues() {
			$result = nullify_empty_values( array( 'foo' => ' ', 'bar' => '', 'baz' => null ) );
			$expected = array( 'foo' => null, 'bar' => null, 'baz' => null );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = nullify_empty_values( array( 'foo' => ' x ' ) );
			$expected = array( 'foo' => ' x ' );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction age().
		 */
		public function testAge() {
			$result = age( null );
			$expected = null;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = age( date( 'Y-m-d', strtotime( '-1 year' ) ) );
			$expected = 1;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = age( date( 'Y-m-d', strtotime( '-33 year -6 months' ) ) );
			$expected = 33;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = age( date( 'Y-m-d', strtotime( '1979-01-24' ) ), date( 'Y-m-d', strtotime( '2013-07-02' ) ) );
			$expected = 34;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction cle_nir().
		 */
		 public function testCleNir() {
			$result = cle_nir( '179012A001234' );
			$expected = '71';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = cle_nir( '179012B001234' );
			$expected = '01';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		 }

		/**
		 * Test de la fonction cle_nir() avec une erreur.
		 */
		 public function testCleNirError1() {
			$this->expectError( 'PHPUnit_Framework_Error_Warning' );
			$result = cle_nir( 'B79012B001234' );
		 }

		/**
		 * Test de la fonction cle_nir() avec une erreur.
		 */
		 public function testCleNirError2() {
			$this->expectError( 'PHPUnit_Framework_Error_Warning' );
			$result = cle_nir( '179013400123456' );
		 }

		/**
		 * Test de la fonction valid_nir().
		 */
		 public function testValidNir() {
			$result = valid_nir( '179012A00123471' );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = valid_nir( 'A79012A00123471' );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		 }

		/**
		 * Test de la fonction words_replace().
		 */
		 public function testWordsReplace() {
			// 1. Pour des "noms de champs" CakePHP
			$result = words_replace(
				'Foo.bar = Bar.foo',
				array( 'Foo' => 'Baz' )
			);
			$expected = 'Baz.bar = Bar.foo';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Pour des conditions de query CakePHP
			$result = words_replace(
				'"Fichiermodule"."modele" = \'Personne\' AND "Fichiermodule"."fk_value" = {$__cakeID__$}',
				array( '{$__cakeID__$}' => 594593 )
			);
			$expected = '"Fichiermodule"."modele" = \'Personne\' AND "Fichiermodule"."fk_value" = 594593';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		 }

		/**
		 * Test de la fonction array_words_replace().
		 */
		 public function testArrayWordsReplace() {
			// 1. Pour des "noms de champs" CakePHP
			$result = array_words_replace(
				array( 'Foo.id' => array( 'Bar' => 1 ), 'Foobar' => array( 'Foo.bar = Bar.foo' ) ),
				array( 'Foo' => 'Baz' )
			);
			$expected = array( 'Baz.id' => array( 'Bar' => 1 ), 'Foobar' => array( 'Baz.bar = Bar.foo' ) );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Pour des conditions de query CakePHP
			$result = array_words_replace(
				array( '"Fichiermodule"."modele" = \'Personne\' AND "Fichiermodule"."fk_value" = {$__cakeID__$}' ),
				array( '{$__cakeID__$}' => 594593 )
			);
			$expected = array( '"Fichiermodule"."modele" = \'Personne\' AND "Fichiermodule"."fk_value" = 594593' );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		 }

		/**
		 * Test de la fonction php_associative_array_to_js().
		 */
		 public function testPhpAssociativeArrayToJs() {
			 $data = array( 'Foo.id' => array( 'Bar' => 1 ), 'Foobar' => array( 'Foo.bar = Bar.foo' ) );
			$result = php_associative_array_to_js( $data );
			$expected = '{ "Foo.id" : { "Bar" : "1" }, "Foobar" : { "0" : "Foo.bar = Bar.foo" } }';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		 }

		/**
		 * Test de la fonction apache_bin().
		 */
		 public function testApacheBin() {
			 $saved = Configure::read( 'apache_bin' );

			 $result = apache_bin();
			 $this->assertPattern( '/^\/[^\/]+/', $result );

			 Configure::write( 'apache_bin', null );
			 $result = apache_bin();
			 $this->assertPattern( '/^\/[^\/]+/', $result );

			 Configure::write( 'apache_bin', $saved );
		 }

		/**
		 * Test de la fonction apache_version().
		 */
		 public function testApacheVersion() {
			if( defined( 'CAKEPHP_SHELL' ) && CAKEPHP_SHELL ) {
				$this->markTestSkipped( 'Ce test ne peut être exécuté que dans un navigateur.' );
			}

			 $result = apache_version();
			 $this->assertPattern( '/^[0-9]+\.[0-9]+/', $result );
		 }

		/**
		 * Test de la fonction apache_modules().
		 */
		 public function testApacheModules() {
			if( defined( 'CAKEPHP_SHELL' ) && CAKEPHP_SHELL ) {
				$this->markTestSkipped( 'Ce test ne peut être exécuté que dans un navigateur.' );
			}

			 $result = (array)apache_modules();
			 $intersect = array_intersect( $result, array( 'mod_expires', 'mod_php5', 'mod_rewrite' ) );
			 $this->assertTrue( !empty( $intersect ) );
		 }

		/**
		 * Test de la fonction date_cakephp_to_sql().
		 */
		 public function testDateCakephpToSql() {
			$result = date_cakephp_to_sql( array( 'year' => '1979', 'month' => '01', 'day' => '24' ) );
			$expected = '1979-01-24';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = date_cakephp_to_sql( array( 'year' => '1979', 'month' => '01', 'day' => '24', 'minutes' => 50 ) );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		 }

		/**
		 * Test de la fonction time_cakephp_to_sql().
		 */
		 public function testTimeCakephpToSql() {
			$result = time_cakephp_to_sql( array( 'hour' => '15', 'min' => '01' ) );
			$expected = '15:01:00';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = time_cakephp_to_sql( array( 'hour' => '15', 'min' => '01', 'sec' => '30' ) );
			$expected = '15:01:30';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = time_cakephp_to_sql( array( 'hour' => '15', 'min' => '01', 'sec' => '30', 'year' => '2012' ) );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		 }

		/**
		 * Test de la fonction date_cakephp_to_sql().
		 */
		 public function testDateSqlToCakephp() {
			$result = date_sql_to_cakephp( '1979-01-24' );
			$expected = array( 'year' => '1979', 'month' => '01', 'day' => '24' );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = date_sql_to_cakephp( '1979-01' );
			$expected = array( 'year' => null, 'month' => null, 'day' => null );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		 }

		/**
		 * Test de la fonction full_array_diff().
		 */
		 public function testFullArrayDiff() {
			$a1 = array( 'foo', 'bar' );
			$a2 = array( 'foo', 'bar', 'baz' );
			$a3 = array( 'bar', 'baz' );

			$result = full_array_diff( $a1, $a1 );
			$expected = array();
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = full_array_diff( $a1, $a2 );
			$expected = array( 4 => 'baz' );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = full_array_diff( $a1, $a3 );
			$expected = array( 'foo', 3 => 'baz' );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		 }

		/**
		 * Test de la fonction value().
		 */
		 public function testValue() {
			$result = value( array( 'foo' => 'bar', 'bar' => 'baz' ), 'bar' );
			$expected = 'baz';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = value( array( 'foo' => 'bar', 'bar' => 'baz' ), 'baz' );
			$expected = null;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		 }

		/**
		 * Test de la fonction replace_accents().
		 */
		 public function testReplaceAccents() {
			$result = replace_accents( 'Âéï' );
			$expected = 'Aei';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		 }

		/**
		 * Test de la fonction noaccents_upper().
		 */
		 public function testNoaccentsUpper() {
			$result = noaccents_upper( 'Âéï' );
			$expected = 'AEI';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		 }

		/**
		 * Test de la fonction domId().
		 */
		 public function testDomId() {
			$result = domId( 'Foo.bar_id' );
			$expected = 'FooBarId';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		 }

		/**
		 * Test de la fonction validRib().
		 */
		 public function testValidRib() {
			$result = validRib( '20041', '01005', '0500013M026', '06' );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = validRib( '00000', '0000000000', '0000000000', '97' );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		 }

		/**
		 * Test de la fonction vfListeToArray().
		 */
		 public function testVfListeToArray() {
			$result =  vfListeToArray( "- CAF\n\r- MSA" );
			$expected = array( ' CAF', ' MSA' );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		 }

		/**
		 * Test de la fonction js_escape().
		 */
		 public function testJsEscape() {
			$result =  js_escape( "Bonjour Monsieur \"Auzolat\"\nvous devez vous présenter ..." );
			$expected = 'Bonjour Monsieur \\"Auzolat\\"\\nvous devez vous présenter ...';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		 }

		 /**
		  * Test de la fonction array_remove()
		  */
		 public function testArrayRemove() {
			 // 1. Pas de paramètre supplémentaire
			 $array = array( 1, 2, '3', '4', 5 );
			 array_remove( $array, 4 );

			 $expected = array( 0 => 1, 1 => 2, 2 => 3, 4 => 5 );
			 $this->assertEqual( $array, $expected, var_export( $array, true ) );

			 // 2. Paramètre strict
			 $array = array( 1, 2, '3', '4', 5 );
			 array_remove( $array, 4, true );

			 $expected = array( 1, 2, '3', '4', 5 );
			 $this->assertEqual( $array, $expected, var_export( $array, true ) );

			 // 3. Paramètre strict
			 $array = array( 1, 2, '3', 4, 5 );
			 array_remove( $array, 4, true );

			 $expected = array( 0 => 1, 1 => 2, 2 => 3, 4 => 5 );
			 $this->assertEqual( $array, $expected, var_export( $array, true ) );

			 // 4. Paramètre reorder
			 $array = array( 1, 2, '3', 4, 5 );
			 array_remove( $array, 4, false, true );

			 $expected = array( 1, 2, 3, 5 );
			 $this->assertEqual( $array, $expected, var_export( $array, true ) );
		}

		/**
		 * Test de la fonction trim_mixed()
		 */
		public function testTrimMixed() {
			// 1. Avec une chaîne de caractères
			$string = ' "Foo, bar" ';
			$result = trim_mixed( $string );
			$expected = 'Foo, bar';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Avec un array à une dimension
			$array = array(
				0 => "\tBaz",
				'foo' => ' "Foo, bar" '
			);
			$result = trim_mixed( $array );
			$expected = array(
				0 => 'Baz',
				'foo' => 'Foo, bar',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. Avec un array à plusieurs dimensions
			$array = array(
				0 => "\tBaz",
				'foo' => ' "Foo, bar" ',
				1 => array(
					2 => ' "Bar" ',
				)
			);
			$result = trim_mixed( $array );
			$expected = array(
				0 => 'Baz',
				'foo' => 'Foo, bar',
				1 => array(
					2 => 'Bar',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction parse_csv_line()()
		 */
		public function testParseCsvLine() {
			// 1. Séparateur et délimiteur par défaut
			$line = '"Prescription professionnelle","Accompagnement a la creation d activite",,"ADIE   Association pour le Droit a l Initiative Economique   ","Micro credit professionnel, Pret d honneur, Accompagnement a la creation d entreprise","Metiers divers","0149331833","113   115 rue Daniele Casanova","93200","Saint   Denis"';
			$result = parse_csv_line( $line );
			$expected = array(
				'Prescription professionnelle',
				'Accompagnement a la creation d activite',
				NULL,
				'ADIE   Association pour le Droit a l Initiative Economique',
				'Micro credit professionnel, Pret d honneur, Accompagnement a la creation d entreprise',
				'Metiers divers',
				'0149331833',
				'113   115 rue Daniele Casanova',
				'93200',
				'Saint   Denis',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Séparateur et délimiteur spécifiés
			$line = "Foo;' Bar; ';'\'Baz'";
			$result = parse_csv_line( $line, ';', '\'' );
			$expected = array(
				'Foo',
				'Bar;',
				'\\\'Baz',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction hash_keys()
		 */
		public function testHashKeys() {
			$data = array(
				'Foo' => array(
					'Bar' => array(
						'Baz' => 1
					)
				),
				'Bar' => array(
					'Baz' => 1
				),
				'Baz' => 1
			);

			$result = hash_keys( $data );
			$expected = array(
				'Foo.Bar',
				'Bar.Baz',
				'Baz',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction preg_test().
		 */
		public function testPregTest() {
			// 1. La chaîne vide n'est pas une expression régulière correcte
			$result = preg_test( '' );
			$this->assertFalse( $result );

			// 2. Expression correcte
			$result = preg_test( '/Foo/i' );
			$this->assertTrue( $result );

			// 3. Expression incorrecte
			$result = preg_test( '/[Foo/i' );
			$this->assertFalse( $result );
		}

		/**
		 * Test de la fonction departement_uses_class().
		 */
		public function testDepartementUsesClass() {
			Configure::write( 'Cg.departement', 93 );

			$this->assertTrue( departement_uses_class( 'Orientstruct' ) );
			$this->assertTrue( departement_uses_class( 'Cer93' ) );
			$this->assertTrue( departement_uses_class( 'Orientstruct2' ) );

			$this->assertFalse( departement_uses_class( 'Apre66' ) );
			$this->assertFalse( departement_uses_class( 'Apre66' ) );
			$this->assertFalse( departement_uses_class( 'Reorientationep976' ) );
		}

		/**
		 * Test de la fonction dedupe_validation_errors().
		 */
		public function testDedupeValidationErrors() {
			$data = array(
				'familleromev3_id' => array(
					'Champ obligatoire',
					'Champ obligatoire'
				),
				'domaineromev3_id' => array(
					'Champ obligatoire'
				),
				'metierromev3_id' => array(
					'Champ obligatoire'
				),
				'appellationromev3_id' => array(
					'Champ obligatoire'
				)
			);

			$result = dedupe_validation_errors( $data );

			$expected = array(
				'familleromev3_id' => array(
					'Champ obligatoire'
				),
				'domaineromev3_id' => array(
					'Champ obligatoire'
				),
				'metierromev3_id' => array(
					'Champ obligatoire'
				),
				'appellationromev3_id' => array(
					'Champ obligatoire'
				)
			);

			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction in_array_strings
		 */
		public function testInArrayStrings() {
			// 1. Se comporte comme in_array (non strict)
			$result = in_array_strings( '0', array( 0, 1, 2, 3, 4, 5, 6 ) );
			$this->assertEqual( $result, true, var_export( $result, true ) );

			$result = in_array_strings( 0, array( 0, 1, 2, 3, 4, 5, 6 ) );
			$this->assertEqual( $result, true, var_export( $result, true ) );

			$result = in_array_strings( '2', array( 0, 1, 2, 3, 4, 5, 6 ) );
			$this->assertEqual( $result, true, var_export( $result, true ) );

			$result = in_array_strings( 2, array( 0, 1, 2, 3, 4, 5, 6 ) );
			$this->assertEqual( $result, true, var_export( $result, true ) );

			// 2. Corrige le problème de in_array (non strict) avec Z
			$result = in_array_strings( 'Z', array( 0, 1, 2, 3, 4, 5, 6 ) );
			$this->assertEqual( $result, false, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction hash_filter_keys()
		 */
		public function testHashFilterKeys() {
			$data = array(
                'Search' => array(
                        'annee' => '2015',
                        'structurereferente_id' => (int) 64,
                        'referent_id' => (int) 314,
                        'user_id' => '',
                        'tableau' => '',
                        'typethematiquefp93_id' => '',
                        'rdv_structurereferente' => '',
                        'dsps_maj_dans_annee' => '',
                        'soumis_dd_dans_annee' => '1'
                )
			);

			$filters = array(
				'Search.annee',
				'Search.communautesr_id',
				'Search.structurereferente_id',
				'Search.referent_id',
				'Search.soumis_dd_dans_annee'
			);

			$expected = array(
                'Search' => array(
					'annee' => '2015',
					'communautesr_id' => '',
					'structurereferente_id' => 64,
					'referent_id' => 314,
					'soumis_dd_dans_annee' => '1'
                )
			);

			$result = hash_filter_keys( $data, $filters );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction filesize2bytes
		 */
		public function testFilesize2bytes() {
			$this->assertEqual(filesize2bytes('123456'), 123456, 'Juste des chiffres');
			$this->assertEqual(filesize2bytes('123456B'), 123456, 'Test "B"');
			$this->assertEqual(filesize2bytes('1K'), 1024, 'Test "K"');
			$this->assertEqual(filesize2bytes('1M'), pow(1024, 2), 'Test "M"');
			$this->assertEqual(filesize2bytes('1 Go'), pow(1024, 3), 'Test "G" avec "o"');
			$this->assertEqual(filesize2bytes('25 TB'), 25 * pow(1024, 4), 'Test "T" avec "B"');
			$this->assertEqual(filesize2bytes('1.25P'), 1.25 * pow(1024, 5), 'Test "P" avec float');
		}

		/**
		 * Test de la fonction query_fields()
		 */
		public function testQueryFields() {
			// 1. Cas simple avec une clé numérique
			$query = array( 'fields' => array( 'Model.field1' ) );
			$result = query_fields( $query );
			$expected = array( 'Model.field1' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Cas simple avec une clé texte
			$query = array( 'fields' => array( 'Model.field1' => 'Model.field1' ) );
			$result = query_fields( $query );
			$expected = array( 'Model.field1' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3. Cas simple avec une clé numérique et une clé texte
			$query = array( 'fields' => array( 'Model.field1' => 'Model.field1', 'Model.field2' ) );
			$result = query_fields( $query );
			$expected = array( 'Model.field1', 'Model.field2' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 4. Cas complexe
			$query = array(
				'fields' => array(
					'Model.field1' => 'Model.field1',
					'Model.field2',
					'Model.field3' => '( 1 + 1 ) AS "Model__field3"',
					'( 2 + 2 ) AS "Model__field4"'
				)
			);
			$result = query_fields( $query );
			$expected = array( 'Model.field1', 'Model.field2', 'Model.field3' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction localized_interval()
		 */
		public function testLocalizedInterval() {
			// @fixme
			$this->markTestIncomplete( 'This test has not been implemented yet.' );

			// 1. Valeur simple
			$result = localized_interval( '1 month' );
			$expected = '1 mois';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Valeur composée
			$result = localized_interval( '1 month 2 day' );
			$expected = '1 mois, 2 jours';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3. Valeur erronée pour l'intervalle
			$result = localized_interval( 'foobar' );
			$expected = 'foobar';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 4. Valeur erronée pour la date de départ
			$result = localized_interval( '1 month 2 day', array( 'now' => 'foobar' ) );
			$expected = '1 month 2 day';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 5. Valeur composée, avec singuliers et pluriels
			$result = localized_interval( '2 year 1 month 3 day 1 hour 2 minute 1 second' );
			$expected = '2 années, 1 mois, 3 jours, 1 heure, 2 minutes, 1 seconde';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 6. Valeur composée, avec singuliers et pluriels
			$result = localized_interval( '48 days', array( 'now' => '2016-09-15', 'precision' => 'd' ) );
			$expected = '1 mois, 17 jours';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction array_complete_recursive()
		 */
		public function testArrayCompleteRecursive() {
			// 1. A un seul niveau, avec redites
			$array1 = array( 1 => 'Foo', 2 => 'Bar', 3 => 'Baz' );
			$array2 = array( 1 => 'Baz' );
			$result = array_complete_recursive( $array1, $array2 );
			$expected = array( 1 => 'Foo', 2 => 'Bar', 3 => 'Baz' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. A deux niveaux, avec redites
			$array1 = array( 1 => array( 4 => 'Foo' ), 2 => 'Bar', 3 => 'Baz' );
			$array2 = array( 1 => array( 4 => 'Bar', 5 => 'Boz' ) );
			$result = array_complete_recursive( $array1, $array2 );
			$expected = array( 1 => array( 4 => 'Foo', 5 => 'Boz' ), 2 => 'Bar', 3 => 'Baz' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3. Exemple concrêt
			$array1 = array(
				'Emploi' => array(
					91 => 'Pole Emploi de Saint Denis - Stade de France'
				),
				'Social' => array(
					54 => 'Service Social  Municipal de Saint Denis'
				),
				'Socioprofessionnelle' => array(
					67 => '« Projet de Ville RSA »-Saint Denis-Objectif Emploi',
					1 => '« Projet de Ville RSA d\'Aubervilliers»',
				)
			);
			$array2 = array(
				'Socioprofessionnelle' => array(
					 1 => '« Projet de Ville RSA d\'Aubervilliers»'
				 )
			);
			$result = array_complete_recursive( $array1, $array2 );
			$expected = array(
				'Emploi' => array(
					91 => 'Pole Emploi de Saint Denis - Stade de France'
				),
				'Social' => array(
					54 => 'Service Social  Municipal de Saint Denis'
				),
				'Socioprofessionnelle' => array(
					67 => '« Projet de Ville RSA »-Saint Denis-Objectif Emploi',
					1 => '« Projet de Ville RSA d\'Aubervilliers»',
				)
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 4. A deux niveaux, avec redites
			$array1 = array( 1 => array( 4 => 'Foo' ), 2 => 'Bar', 3 => 'Baz' );
			$array2 = array( 1 => array( 4 => array( 0 => 'Bar' ), 5 => 'Boz' ), 6 => 'Buz' );
			$result = array_complete_recursive( $array1, $array2 );
			$expected = array( 1 => array( 4 => 'Foo', 5 => 'Boz' ), 2 => 'Bar', 3 => 'Baz', 6 => 'Buz' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>