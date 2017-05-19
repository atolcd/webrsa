<?php
	/**
	 * SearchProgressivePaginationTest file
	 *
	 * PHP 5.3
	 *
	 * @package Validation
	 * @subpackage Test.Case.Utility.Validation2Formatters
	 */
	App::uses( 'SearchProgressivePagination', 'Search.Utility' );

	/**
	 * SearchProgressivePaginationTest class
	 *
	 * @package Validation
	 * @subpackage Test.Case.Utility.Validation2Formatters
	 */
	class SearchProgressivePaginationTest extends CakeTestCase
	{
		/**
		 * Préparation de l'environnement d'une méthode de test.
		 */
		public function setUp() {
			parent::setUp();
			Configure::delete( 'Optimisations' );
		}
		/**
		 * Test de la méthode SearchProgressivePagination::configureKey();
		 */
		public function testConfigureKey() {
			$result = SearchProgressivePagination::configureKey();
			$expected = 'Optimisations.progressivePaginate';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = SearchProgressivePagination::configureKey( 'my_controller_name' );
			$expected = 'Optimisations.MyControllerName.progressivePaginate';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = SearchProgressivePagination::configureKey( 'MyControllerName', 'my_action' );
			$expected = 'Optimisations.MyControllerName_my_action.progressivePaginate';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = SearchProgressivePagination::configureKey( null, 'my_action' );
			$expected = 'Optimisations.progressivePaginate';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode SearchProgressivePagination::enabled();
		 */
		public function testEnabled() {
			Configure::write( 'Optimisations.MyControllerName_my_action.progressivePaginate', true );
			$result = SearchProgressivePagination::enabled( 'MyControllerName', 'my_action' );
			$this->assertEquals( true, $result, var_export( $result, true ) );

			Configure::write( 'Optimisations.MyControllerName.progressivePaginate', true );
			$result = SearchProgressivePagination::enabled( 'MyControllerName', 'my_action' );
			$this->assertEquals( true, $result, var_export( $result, true ) );

			$result = SearchProgressivePagination::enabled( 'MyControllerName' );
			$this->assertEquals( true, $result, var_export( $result, true ) );

			Configure::write( 'Optimisations.progressivePaginate', true );
			$result = SearchProgressivePagination::enabled( 'MyControllerName', 'my_action2' );
			$this->assertEquals( true, $result, var_export( $result, true ) );

			$result = SearchProgressivePagination::enabled( 'MyControllerName' );
			$this->assertEquals( true, $result, var_export( $result, true ) );

			$result = SearchProgressivePagination::enabled();
			$this->assertEquals( true, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode SearchProgressivePagination::enable();
		 */
		public function testEnable() {
			SearchProgressivePagination::enable( 'MyControllerName', 'my_action' );
			$result = SearchProgressivePagination::enabled( 'MyControllerName', 'my_action' );
			$this->assertEquals( true, $result, var_export( $result, true ) );

			SearchProgressivePagination::enable();
			$result = SearchProgressivePagination::enabled();
			$this->assertEquals( true, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode SearchProgressivePagination::disable();
		 */
		public function testDisable() {
			SearchProgressivePagination::enable();

			SearchProgressivePagination::disable( 'MyControllerName', 'my_action' );
			$result = SearchProgressivePagination::enabled( 'MyControllerName', 'my_action' );
			$this->assertEquals( false, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode SearchProgressivePagination::format();
		 */
		public function testFormat() {
			$result = __( SearchProgressivePagination::format( true ) );
			$this->assertTrue( strstr( $result, ' au moins ' ) !== false );

			$result = __( SearchProgressivePagination::format( false ) );
			$this->assertFalse( strstr( $result, ' au moins ' ) !== false );
		}

		/**
		 * Test de la méthode SearchProgressivePagination::paginatorHelperFormat();
		 */
		public function testPaginatorHelperFormat() {
			$Request = new CakeRequest();
			$Request->addParams(
				array(
					'controller' => 'users',
					'action' => 'index',
					'pass' => array( ),
					'named' => array( ),
					'paging' => array(
						'User' => array(
							'page' => 1,
							'current' => 1,
							'count' => 21,
							'prevPage' => false,
							'nextPage' => true,
							'pageCount' => 2,
							'order' => null,
							'limit' => 20,
							'options' => array(
								'page' => 1,
								'conditions' => array()
							),
							'paramType' => 'named'
						)
					)
				)
			);
			$className = 'User';

			// 1. Sans la pagination progressive
			$result = __( SearchProgressivePagination::paginatorHelperFormat( $Request, $className ) );
			$this->assertFalse( strstr( $result, ' au moins ' ) !== false );

			// 2. Avec la pagination progressive
			SearchProgressivePagination::enable();
			$result = __( SearchProgressivePagination::paginatorHelperFormat( $Request, $className ) );
			$this->assertTrue( strstr( $result, ' au moins ' ) !== false );

			// 3. Avec un format non reconnu
			$result = __( SearchProgressivePagination::paginatorHelperFormat( $Request, $className, '%page%' ) );
			$this->assertEquals( '%page%', $result, var_export( $result, true ) );
		}
	}
?>