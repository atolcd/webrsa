<?php
	/**
	 * Code source de la classe DefaultUrlTest.
	 *
	 * PHP 5.3
	 *
	 * @package Default
	 * @subpackage Test.Case.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'DefaultUrl', 'Default.Utility' );
	App::uses( 'DefaultAbstractTestCase', 'Default.Test/Case' );
	App::uses( 'Router', 'Routing' );

	/**
	 * La classe DefaultUrlTest ...
	 *
	 * @package Default
	 * @subpackage Test.Case.Utility
	 */
	class DefaultUrlTest extends DefaultAbstractTestCase
	{
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
		 * setUp method
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();

			$this->_setRequest();
		}

		/**
		 * Test de la méthode DefaultUrl::toString()
		 *
		 * @return void
		 */
		public function testToString() {
			$url = array( 'action' => 'logout' );
			$result = DefaultUrl::toString( $url );
			$expected = '/Users/logout';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$url = array( 'action' => 'index', '#' => 'results' );
			$result = DefaultUrl::toString( $url );
			$expected = '/Users/index/#results';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$url = array( 'plugin' => 'plugin', 'controller' => 'controllers', 'action' => 'action', 'prefix' => 'prefix', 0 => 'param1', 'named' => 'value' );
			$result = DefaultUrl::toString( $url );
			$expected = '/Plugin.Controllers/prefix_action/param1/named:value';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$url = array( 'plugin' => 'plugin', 'controller' => 'controllers', 'action' => 'action', 'prefix' => 'prefix', 0 => 'param1', '#' => 'anchor,subanchor', 'named' => 'value' );
			$result = DefaultUrl::toString( $url );
			$expected = '/Plugin.Controllers/prefix_action/param1/named:value#anchor,subanchor';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$url = array(
				'plugin' => 'acl_extras',
				'controller' => 'users',
				'action' => 'index',
				'prefix' => 'admin',
				'admin' => true,
				0 => 'category',
				'Search__active' => true,
				'Search__User__username' => 'admin',
				'#' => 'content',
			);
			$result = DefaultUrl::toString( $url );
			$expected = '/AclExtras.Users/admin_index/category/Search__active:1/Search__User__username:admin#content';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$url = array( 'action' => 'index', 'prefix' => 'admin', 'admin' => true );
			$this->_setRequest( $url );
			$result = DefaultUrl::toString( $url );
			$expected = '/Users/admin_index';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode DefaultUrl::toString()
		 *
		 * @return void
		 */
		public function testToString2() {
			$result = DefaultUrl::toString( array( 'controller' => 'foos', 'action' => 'bar' ) );
			$expected = '/Foos/bar';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = DefaultUrl::toString( '/Foos/bar' );
			$expected = '/Foos/bar';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = DefaultUrl::toString( array( 'controller' => 'foos', 'action' => 'bar', 'Search__active' => 1 ) );
			$expected = '/Foos/bar/Search__active:1';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = DefaultUrl::toString( '/Foos/bar/Search__active:1' );
			$expected = '/Foos/bar/Search__active:1';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = DefaultUrl::toString( array( 'plugin' => 'tests','controller' => 'foos', 'prefix' => 'admin', 'admin' => true, 'action' => 'bar', 0 => 666, 'Search__active' => 1 ) );
			$expected = '/Tests.Foos/admin_bar/666/Search__active:1';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode DefaultUrl::toArray()
		 *
		 * @return void
		 */
		public function testToArray() {
			$url = '/Users/logout';
			$result = DefaultUrl::toArray( $url );
			$expected = array(
				'plugin' => null,
				'controller' => 'users',
				'action' => 'logout'
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$url = '/Users/index#results';
			$result = DefaultUrl::toArray( $url );
			$expected = array(
				'plugin' => null,
				'controller' => 'users',
				'action' => 'index',
				'#' => 'results'
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$url = '/Users/index/#results';
			$result = DefaultUrl::toArray( $url );
			$expected = array(
				'plugin' => null,
				'controller' => 'users',
				'action' => 'index',
				'#' => 'results'
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$url = '/Plugin.Controllers/admin_action/param1/named:value';
			$result = DefaultUrl::toArray( $url );
			$expected = array(
				'prefix' => 'admin',
				'plugin' => 'plugin',
				'controller' => 'controllers',
				'action' => 'action',
				0 => 'param1',
				'admin' => true,
				'named' => 'value'
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$url = '/Plugin.Controllers/prefix_action/param1/named:value/#anchor,subanchor';
			$result = DefaultUrl::toArray( $url );
			$expected = array(
				'plugin' => 'plugin',
				'controller' => 'controllers',
				'action' => 'prefix_action',
				0 => 'param1',
				'named' => 'value',
				'#' => 'anchor,subanchor'
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$url = '/AclExtras.Users/admin_index/category/Search__active:1/Search__User__username:admin/#content';
			$result = DefaultUrl::toArray( $url );
			$expected = array(
				'prefix' => 'admin',
				'plugin' => 'acl_extras',
				'controller' => 'users',
				'action' => 'index',
				0 => 'category',
				'admin' => true,
				'Search__active' => '1',
				'Search__User__username' => 'admin',
				'#' => 'content'
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}


		/**
		 * Test de la méthode DefaultUrl::toArray()
		 *
		 * @return void
		 */
		public function testToArray2() {
			$result = DefaultUrl::toArray( '/Foos/bar' );
			$expected = array( 'plugin' => null, 'controller' => 'foos', 'action' => 'bar' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = DefaultUrl::toArray( '/Foos/bar/Search__active:1' );
			$expected = array( 'plugin' => null, 'controller' => 'foos', 'action' => 'bar', 'Search__active' => 1 );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = DefaultUrl::toArray( '/Tests.Foos/admin_bar/666/Search__active:1' );
			$expected = array( 'plugin' => 'tests','controller' => 'foos', 'prefix' => 'admin', 'admin' => true, 'action' => 'bar', 0 => 666, 'Search__active' => 1 );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = DefaultUrl::toArray( '/Foos/bar#6' );
			$expected = array( 'plugin' => null, 'controller' => 'foos', 'action' => 'bar', '#' => 6 );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = DefaultUrl::toArray( '/Foos/bar##Model.field#' );
			$expected = array( 'plugin' => null, 'controller' => 'foos', 'action' => 'bar', '#' => '#Model.field#' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>