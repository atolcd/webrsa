<?php
	/**
	 * Code source de la classe DefaultActionHelperTest.
	 *
	 * PHP 5.3
	 *
	 * @package Default
	 * @subpackage Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'View', 'View' );
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( 'DefaultActionHelper', 'Default.View/Helper' );

	/**
	 * La classe DefaultActionHelperTest ...
	 *
	 * @package Default
	 * @subpackage Test.Case.View.Helper
	 */
	class DefaultActionHelperTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisées pour les tests.
		 *
		 * @var array
		 */
		public $fixtures = array();

		/**
		 * Le contrôleur utilisé pour les tests.
		 *
		 * @var Controller
		 */
		public $Controller = null;

		/**
		 * Le contrôleur utilisé pour les tests.
		 *
		 * @var View
		 */
		public $View = null;

		/**
		 * Le helper à tester.
		 *
		 * @var DefaultAction
		 */
		public $DefaultAction = null;

		/**
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
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$Request = new CakeRequest();
			$this->Controller = new Controller( $Request );
			$this->View = new View( $this->Controller );
			$this->DefaultAction = new DefaultActionHelper( $this->View );

			$this->_setRequest(
				array(
					'controller' => 'users',
					'action' => 'index',
					'pass' => array( ),
					'named' => array( )
				)
			);
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			parent::tearDown();
			unset( $this->Controller, $this->View, $this->DefaultAction );
		}

		/**
		 * Test de la méthode DefaultActionHelper::back()
		 *
		 * @medium
		 */
		public function testBack() {
			$_SERVER['HTTP_REFERER'] = Router::url( '/users/login' );

			$result = $this->DefaultAction->back();
			$expected = array(
				'/Users/login/' =>
				array(
					'text' => 'Retour',
					'msgid' => 'Retour à la page précédente',
					'enabled' => false,
					'class' => 'back',
				),
			);

			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->DefaultAction->back( '/Foos' );
			$expected = array(
				'/Foos/index' =>
				array(
					'text' => 'Retour',
					'msgid' => 'Retour à la page précédente',
					'enabled' => false,
					'class' => 'back',
				),
			);

			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>