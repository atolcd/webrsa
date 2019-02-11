<?php
	/**
	 * Code source de la classe SearchSavedRefererComponentTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'SearchSavedRefererComponent', 'Search.Controller/Component' );

	App::uses( 'CakeRequest', 'Network' );
	App::uses( 'CakeResponse', 'Network' );
	App::uses( 'Router', 'Routing' );

	App::uses( 'CakeTestSession', 'CakeTest.Model/Datasource' );

	/**
	 * SearchSavedRefererTestsController class
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class SearchSavedRefererTestsController extends AppController
	{
		/**
		 * name property
		 *
		 * @var string
		 */
		public $name = 'SearchSavedRefererTestsController';

		/**
		 * uses property
		 *
		 * @var mixed null
		 */
		public $uses = null;

		/**
		 * components property
		 *
		 * @var array
		 */
		public $components = array(
			'Search.SearchSavedReferer'
		);

		/**
		 * Les paramètres de redirection.
		 *
		 * @var array
		 */
		public $redirected = null;

		/**
		 *
		 * @param string|array $url A string or array-based URL pointing to another location within the app,
		 *     or an absolute URL
		 * @param integer $status Optional HTTP status code (eg: 404)
		 * @param boolean $exit If true, exit() will be called after the redirect
		 * @return mixed void if $exit = false. Terminates script if $exit = true
		 */
		public function redirect( $url, $status = null, $exit = true) {
			$this->redirected = array( $url, $status, $exit );
			return false;
		}
	}

	/**
	 * La classe SearchSavedRefererComponentTest ...
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class SearchSavedRefererComponentTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array( 'app.User' );

		/**
		 * Controller property
		 *
		 * @var SearchSavedRefererComponent
		 */
		public $Controller;


		/**
		 * test case startup
		 *
		 * @return void
		 */
		public static function setupBeforeClass() {
			CakeTestSession::setupBeforeClass();
		}

		/**
		 * cleanup after test case.
		 *
		 * @return void
		 */
		public static function teardownAfterClass() {
			CakeTestSession::teardownAfterClass();
		}

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();

			$Request = new CakeRequest( null, false );
			$Request->here = '/apples/edit/4';
			$Request->webroot = '/';
			$Request->base = '';

			$Request->addParams( array( 'controller' => 'apples', 'action' => 'edit', 4 ) );
			$Response = new CakeResponse();

			Router::reload();
			Router::setRequestInfo( $Request );

			$this->Controller = new SearchSavedRefererTestsController( $Request, $Response );
			$this->Controller->constructClasses();

			$_SERVER['HTTP_REFERER'] = Router::url( array( 'controller' => 'apples', 'action' => 'index' ) );

			CakeTestSession::start();
		}

		/**
		 * tearDown method
		 *
		 * @return void
		 */
		public function tearDown() {
			CakeTestSession::destroy();
			parent::tearDown();
		}

		/**
		 * Test de la méthode SearchSavedRefererComponent::sessionKey()
		 */
		public function testSessionKey() {
			$result = $this->Controller->SearchSavedReferer->sessionKey();
			$expected = 'SearchSavedReferer./apples/edit/4';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>