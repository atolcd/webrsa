<?php
	/**
	 * MultiDomainsTranslatorTest file
	 *
	 * PHP 5.3
	 *
	 * @package MultiDomainsTranslator
	 * @subpackage Test.Case.Utility.MultiDomainsTranslator
	 */
	App::uses( 'MultiDomainsTranslator', 'MultiDomainsTranslator.Utility' );

	/**
	 * MultiDomainsTranslatorTest class
	 *
	 * @package MultiDomainsTranslator
	 * @subpackage Test.Case.Utility.MultiDomainsTranslator
	 */
	class MultiDomainsTranslatorTest extends CakeTestCase
	{
		/**
		 * Defini une url fictive
		 *
		 * @param array $requestParams
		 */
		protected function _setRequest( array $requestParams = array() ) {
			$default = array(
				'plugin' => null,
				'controller' => 'users',
				'action' => 'login',
			);

			$requestParams = Hash::merge( $default, $requestParams );

			Router::reload();
			$request = new CakeRequest();

			$request->addParams( $requestParams );

			Router::setRequestInfo( $request );
		}

		/**
		 * setUp method
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();

			$this->_setRequest( array('controller' => 'tests', 'action' => 'index') );
			App::build( array( 'locales' => CakePlugin::path( 'MultiDomainsTranslator' ).'Test'.DS.'Locale'.DS ) );
		}

		/**
		 * Test de la méthode MultiDomainsTranslator::translate();
		 */
		public function testTranslate() {
			Configure::write( 'Cg.departement', 66 ); // Juste pour l'exemple d'utilisation
			Configure::write( 'MultiDomainsTranslator.prefix', 'cd'.Configure::read( 'Cg.departement' ) );

			$result = MultiDomainsTranslator::translate( 'test' );
			$expected = 'Traduction pour controller CD66';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = MultiDomainsTranslator::translate( 'test.n2', 1 );
			$expected = '1 Traduction pour controller CD66';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = MultiDomainsTranslator::translate( 'Test.test' );
			$expected = 'Traduction pour model CD66';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			MultiDomainsTranslator::reset();
			Configure::write( 'MultiDomainsTranslator.prefix', 'cd93' );

			$result = MultiDomainsTranslator::translate( 'test' );
			$expected = 'Traduction pour controller CD93';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = MultiDomainsTranslator::translate( 'Test.test' );
			$expected = 'Traduction pour model CD93';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = MultiDomainsTranslator::translate( 'test2' );
			$expected = 'Controller par defaut';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = MultiDomainsTranslator::translate( 'Test.test2' );
			$expected = 'Model par defaut';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = MultiDomainsTranslator::translate( 'Foo.bar' ); // Not found
			$expected = 'Foo.bar';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = MultiDomainsTranslator::translate( 'Autremodeltest.test' );
			$expected = 'Selection d\'un autre model';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			MultiDomainsTranslator::reset();
			Configure::write( 'MultiDomainsTranslator', array( 'prefix' => 'monprefix', 'separator' => '%%' ) );

			$result = MultiDomainsTranslator::translate( 'test' );
			$expected = 'Changement de prefix et de séparateur';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			MultiDomainsTranslator::reset();
			Configure::write( 'MultiDomainsTranslator', array( 'prefix' => 'eng', 'separator' => '_' ) );
			$_SESSION['Config']['language'] = 'eng';

			$result = MultiDomainsTranslator::translate( 'test' );
			$expected = 'English traduction\'s file';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode MultiDomainsTranslator::translatePlural();
		 */
		public function testTranslatePlural() {
			MultiDomainsTranslator::reset();
			Configure::write( 'MultiDomainsTranslator', array( 'prefix' => 'cd66', 'separator' => '_' ) );

			$result = MultiDomainsTranslator::translatePlural( 'test', 'tests', 1 );
			$expected = 'Traduction pour controller CD66';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = MultiDomainsTranslator::translatePlural( 'test', 'test_plural', 2 );
			$expected = 'Traductions pour controller CD66';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = MultiDomainsTranslator::translatePlural( 'Test.test2', 'Test.test2_plural', 2 );
			$expected = 'Models par defaut';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = MultiDomainsTranslator::translatePlural( 'test.n2', 'tests.n2', 1, 1 );
			$expected = '1 Traduction pour controller CD66';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = MultiDomainsTranslator::translatePlural( 'test.n2', 'test_plural.n2', 2, 2 );
			$expected = '2 Traductions pour controller CD66';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = MultiDomainsTranslator::translatePlural( 'foo', 'foos', 2 ); // Not found
			$expected = 'foos';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode MultiDomainsTranslator::language();
		 */
		public function testLanguage() {
			$_SESSION['Config']['language'] = 'eng';
			$result = MultiDomainsTranslator::language();
			$expected = 'eng';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$_SESSION['Config']['language'] = 'fre';
			$result = MultiDomainsTranslator::language();
			$expected = 'fre';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode MultiDomainsTranslator::reset();
		 */
		public function testReset() {
			MultiDomainsTranslator::reset();
			Configure::write( 'MultiDomainsTranslator', array( 'prefix' => 'cd66', 'separator' => '_' ) );

			$result = MultiDomainsTranslator::translate( 'test' );
			$expected = 'Traduction pour controller CD66';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			Configure::write( 'MultiDomainsTranslator.prefix', 'cd93' );

			$result = MultiDomainsTranslator::translate( 'test' );
			$expected = 'Traduction pour controller CD66'; // Pas de changement malgrès le changement du prefix
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			MultiDomainsTranslator::reset();

			$result = MultiDomainsTranslator::translate( 'test' );
			$expected = 'Traduction pour controller CD93';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode MultiDomainsTranslator::urlDomains();
		 */
		public function testUrlDomains() {
			MultiDomainsTranslator::reset();
			Configure::write( 'MultiDomainsTranslator', array( 'prefix' => 'cd66', 'separator' => '_' ) );

			$result = MultiDomainsTranslator::urlDomains();
			$expected = array( 'cd66_tests_index', 'tests_index', 'cd66_tests', 'tests' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			MultiDomainsTranslator::reset();
			$this->_setRequest( array('controller' => 'foos', 'action' => 'bar') );
			Configure::write( 'MultiDomainsTranslator', array( 'prefix' => 'cd93', 'separator' => '#' ) );

			$result = MultiDomainsTranslator::urlDomains();
			$expected = array( 'cd93#foos#bar', 'foos#bar', 'cd93#foos', 'foos' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode MultiDomainsTranslator::model_field();
		 */
		public function testModel_field() {
			$result = MultiDomainsTranslator::model_field( 'Testmodel.test' );
			$expected = array( 'Testmodel', 'test' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = MultiDomainsTranslator::model_field( 'Testmodel.0.test' );
			$expected = array( 'Testmodel', 'test' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = MultiDomainsTranslator::model_field( 'Testmodel' );
			$expected = null;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>