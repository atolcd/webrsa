<?php
	/**
	 * Code source de la classe PermissionsHelperTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('CakeSession', 'Model/Datasource');
	App::uses('Controller', 'Controller');
	App::uses( 'View', 'View' );
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( 'PermissionsHelper', 'View/Helper' );
	App::uses( 'SessionHelper', 'View/Helper' );

	App::uses( 'CakeTestSession', 'CakeTest.Model/Datasource' );

	/**
	 * Classe PermissionsHelperTest.
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class PermissionsHelperTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés par ces tests unitaires.
		 *
		 * @var array
		 */
		public $fixtures = array(
		);

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
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();
			$controller = null;
			$this->View = new View( $controller );
			$this->Permissions = new PermissionsHelper( $this->View );

			CakeTestSession::start();

			$_SESSION = array(
				'Auth' => array(
					'Permissions' => array(
						'Module:Users' => false,
						'Users:index' => true,
						'Users:view' => true,
						'Users:add2' => true,
					)
				),
				'Otherkey' => array(
					'Perms' => array(
						'Module:Users' => false,
						'Module:Groups' => true,
					)
				),
			);
		}

		/**
		 * Nettoyage postérieur au test.
		 *
		 * @return void
		 */
		public function tearDown() {
			$_SESSION = array();
			unset( $this->View, $this->Permissions );
			parent::tearDown();
		}

		/**
		 * Test de la méthode PermissionsHelper::check()
		 *
		 * @return void
		 */
		public function testCheck() {
			$result = $this->Permissions->check( 'users', 'index' );
			$expected = true;
			$this->assertEquals( $result, $expected, var_export( $result, true ) );

			$result = $this->Permissions->check( 'users', 'add2' );
			$expected = true;
			$this->assertEquals( $result, $expected, var_export( $result, true ) );

			$result = $this->Permissions->check( 'users', 'edit' );
			$expected = false;
			$this->assertEquals( $result, $expected, var_export( $result, true ) );

			$result = $this->Permissions->check( 'users', 'view' );
			$expected = true;
			$this->assertEquals( $result, $expected, var_export( $result, true ) );

			$result = $this->Permissions->check( 'groups', 'view' );
			$expected = false;
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode PermissionsHelper::check() avec une clé de session
		 * personnalisée.
		 *
		 * @return void
		 */
		public function testCheckWithCustomSessionKey() {
			WebrsaPermissions::$sessionPermissionsKey = 'Otherkey.Perms';

			$result = $this->Permissions->check( 'groups', 'index' );
			$expected = true;
			$this->assertEquals( $result, $expected, var_export( $result, true ) );

			$result = $this->Permissions->check( 'users', 'index' );
			$expected = false;
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}
	}
?>