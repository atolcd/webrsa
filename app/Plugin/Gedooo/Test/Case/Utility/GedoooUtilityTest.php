<?php
	/**
	 * Code source de la classe GedoooUtilityTest.
	 *
	 * PHP 5.3
	 *
	 * @package Gedooo.Test.Case.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'GedoooUtility', 'Gedooo.Utility' );

	/**
	 * La classe GedoooUtilityTest ...
	 *
	 * @package Gedooo.Test.Case.Utility
	 */
	class GedoooUtilityTest extends CakeTestCase
	{
		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();

			Configure::write( 'Config.language', 'fra' );
			App::build( array( 'locales' => CakePlugin::path( 'Gedooo' ).'Test'.DS.'Locale'.DS ) );
		}

		/**
		 * Test de la méthode GedoooUtility::key()
		 */
		public function testKey() {
			$result = GedoooUtility::key( 'Foo' );
			$this->assertEquals( 'foo', $result, var_export( $result, true ) );

			$result = GedoooUtility::key( 'FooBar' );
			$this->assertEquals( 'foobar', $result, var_export( $result, true ) );

			$result = GedoooUtility::key( 'Foo.bar' );
			$this->assertEquals( 'foo_bar', $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode GedoooUtility::msgstr()
		 */
		public function testMsgstr() {
			$result = GedoooUtility::msgstr( 'Orientstruct.typeorient_id' );
			$this->assertEquals( 'Type d\'orientation', $result, var_export( $result, true ) );

			$result = GedoooUtility::msgstr( 'Foo' );
			$this->assertEquals( 'Foo', $result, var_export( $result, true ) );
		}

	}
?>