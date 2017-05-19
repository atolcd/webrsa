<?php
	/**
	 * MultiDomainsTranslatorConfigBootstrapTest file
	 *
	 * PHP 5.3
	 *
	 * @package MultiDomainsTranslator
	 * @subpackage Test.Case.Utility.MultiDomainsTranslator
	 */
	App::uses( 'MultiDomainsTranslator', 'MultiDomainsTranslator.Utility' );

	/**
	 * MultiDomainsTranslatorConfigBootstrapTest class
	 *
	 * @package MultiDomainsTranslator
	 * @subpackage Test.Case.Utility.MultiDomainsTranslator
	 */
	class MultiDomainsTranslatorConfigBootstrapTest extends CakeTestCase
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
		 * Test de la méthode __m();
		 */
		public function test__m() {
			Configure::write( 'Cg.departement', 66 ); // Juste pour l'exemple d'utilisation
			Configure::write( 'MultiDomainsTranslator.prefix', 'cd'.Configure::read( 'Cg.departement' ) );

			$result = __m( 'test' );
			$expected = 'Traduction pour controller CD66';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode __mn();
		 */
		public function test__mn(){
			MultiDomainsTranslator::reset();
			Configure::write( 'MultiDomainsTranslator', array( 'prefix' => 'cd66', 'separator' => '_' ) );

			$result = __mn( 'test', 'tests', 1 );
			$expected = 'Traduction pour controller CD66';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>