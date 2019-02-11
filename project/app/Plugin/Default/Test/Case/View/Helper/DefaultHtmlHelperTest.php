<?php
	/**
	 * Code source de la classe DefaultHtmlHelperTest.
	 *
	 * PHP 5.3
	 *
	 * @package Default
	 * @subpackage Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'View', 'View' );
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( 'DefaultHtmlHelper', 'Default.View/Helper' );
	App::uses( 'DefaultAbstractTestCase', 'Default.Test/Case' );
	App::uses( 'CakeTestSession', 'CakeTest.Model/Datasource' );

	/**
	 * La classe DefaultHtmlHelperTest ...
	 *
	 * @package Default
	 * @subpackage Test.Case.View.Helper
	 */
	class DefaultHtmlHelperTest extends DefaultAbstractTestCase
	{
		/**
		 * Fixtures utilisés par ces tests unitaires.
		 *
		 * @var array
		 */
		public $fixtures = array(
		);

		/**
		 * Préparation du test.
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();
			CakeTestSession::start();

			$controller = null;
			$this->View = new View( $controller );
			$this->DefaultHtml = new DefaultHtmlHelper( $this->View );

			$this->DefaultHtml->Permissions = $this->getMock(
				'PermissionsHelper',
				array( 'check' )
			);
		}

		/**
		 * Nettoyage postérieur au test.
		 *
		 * @return void
		 */
		public function tearDown() {
			CakeTestSession::destroy();
			parent::tearDown();
			unset( $this->View, $this->DefaultHtml );
		}

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
		 * Test de la méthode DefaultHtmlHelper::link()
		 *
		 * @return void
		 */
		public function testLink() {
			$_SESSION['Auth']['Permissions']['Module:Users'] = true;
			$_SESSION['Auth']['Permissions']['Module:Apples'] = true;

			$url = array(
				'plugin' => 'default',
				'controller' => 'users',
				'action' => 'add',
				'prefix' => 'admin',
				'admin' => true
			);

			$this->DefaultHtml->Permissions->expects($this->any())->method('check')->will($this->returnValue(true));

			$result = $this->DefaultHtml->link( 'Test', $url );
			$expected = '<a href="/admin/default/users/add" class="default users admin_add">Test</a>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->DefaultHtml->link( 'Test', Hash::remove( $url, 'plugin' ) );
			$expected = '<a href="/admin/users/add" class="users admin_add">Test</a>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->DefaultHtml->link( 'Test', $url, array( 'disabled' => true ) );
			$expected = '<span class="default users admin_add link disabled">Test</span>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$this->DefaultHtml->request = new CakeRequest( null, false );
			$this->DefaultHtml->request->addParams( array( 'controller' => 'apples', 'action' => 'index' ) );
			$result = $this->DefaultHtml->link( 'Test', array( 'action' => 'view', 666 ) );
			// INFO: ce n'est pas bien configuré, on n'a pas la bonne URL par la fonction link de Cake
			$expected = '<a href="/view/666" class="apples view">Test</a>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
			unset( $this->DefaultHtml->request );
		}
	}
?>