<?php
	/**
	 * Code source de la classe MenuHelperTest.
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
	App::uses( 'MenuHelper', 'View/Helper' );
	App::uses( 'PermissionsHelper', 'View/Helper' );
	App::uses( 'SessionHelper', 'View/Helper' );

	App::uses( 'CakeTestSession', 'CakeTest.Model/Datasource' );

	/**
	 * Classe MenuHelperTest.
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class MenuHelperTest extends CakeTestCase
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
		 */
		public static function setupBeforeClass() {
			CakeTestSession::setupBeforeClass();
		}

		/**
		 * cleanup after test case.
		 */
		public static function teardownAfterClass() {
			CakeTestSession::teardownAfterClass();
		}

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$controller = null;
			$this->View = new View( $controller );
			$this->Menu = new MenuHelper( $this->View );

			CakeTestSession::start();
			CakeTestSession::delete( 'Auth.Permissions' );
			WebrsaPermissions::$sessionPermissionsKey = 'Auth.Permissions';
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			parent::tearDown();
			unset( $this->View, $this->Menu );
		}

		/**
		 * Test de la méthode MenuHelper::make()
		 *
		 * INFO: si on utilise des contrôleurs de l'application, on risque de ne
		 * pas avoir de bons résultats à cause de attributs $commeDroit et
		 * $aucunDroit.
		 *
		 * @medium
		 */
		public function testMake() {
			CakeTestSession::write(
				'Auth.Permissions',
				array(
					'Apples:index' => true,
					'Apples:view' => false,
					'Worms:index' => true,
				)
			);

			$items = array(
				'Panier' => array(
					'url' => array( 'controller' => 'apples', 'action' => 'index', 1 ),
					'Pomme Granny' => array(
						'url' => array( 'controller' => 'apples', 'action' => 'view', 2 ),
						'Vers' => array(
							'url' => array( 'controller' => 'worms', 'action' => 'index', 2 )
						)
					)
				)
			);
			$result = $this->Menu->make( $items );
			$expected = '<ul><li class="branch"><a href="/apples/index/1">Panier</a><ul><li class="branch"><span>Pomme Granny</span><ul><li class="leaf"><a href="/worms/index/2">Vers</a></li></ul></li></ul></li></ul>';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode MenuHelper::make2()
		 *
		 * INFO: si on utilise des contrôleurs de l'application, on risque de ne
		 * pas avoir de bons résultats à cause de attributs $commeDroit et
		 * $aucunDroit.
		 *
		 * @medium
		 */
		public function testMake2() {
			CakeTestSession::write(
				'Auth.Permissions',
				array(
					'Apples:index' => true,
					'Apples:view' => false,
					'Worms:index' => true,
					'Pips:index' => true,
				)
			);

			$items = array(
				'Panier' => array(
					'url' => array( 'controller' => 'apples', 'action' => 'index', 1 ),
					'Pomme Granny' => array(
						'url' => array( 'controller' => 'apples', 'action' => 'view', 2 ),
						'Vers' => array(
							'disabled' => true,
							'url' => array( 'controller' => 'worms', 'action' => 'index', 2 ),
						),
						'Pépins' => array(
							'disabled' => false,
							'url' => array( 'controller' => 'pips', 'action' => 'index', 2 ),
							'title' => 'Des pépins pour replanter'
						),
					)
				)
			);
			$result = $this->Menu->make2( $items, 'a' );
			$expected = '<ul><li class="branch"><a href="/apples/index/1">Panier</a><ul><li class="branch"><a href="#">Pomme Granny</a><ul><li class="leaf"><a href="/pips/index/2" title="Des pépins pour replanter">Pépins</a></li></ul></li></ul></li></ul>';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}
	}
?>