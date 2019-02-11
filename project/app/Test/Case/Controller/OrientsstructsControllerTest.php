<?php
	/**
	 * Code source de la classe DossierssimplifiesControllerTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'OrientsstructsController', 'Controller' );
	App::uses( 'CakeTestSession', 'CakeTest.Model/Datasource' );

	class OrientsstructspublicController extends OrientsstructsController
	{
		public function getIndexActionsList(array $records, array $params = array()) {
			return $this->_getIndexActionsList($records, $params);
		}
	}

	/**
	 * La classe DossierssimplifiesControllerTest ...
	 *
	 * @see http://book.cakephp.org/2.0/en/development/testing.html#testing-controllers
	 *
	 * @package app.Test.Case.Controller
	 */
	class OrientsstructsControllerTest extends ControllerTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array();


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
			$this->Orientsstructs = new OrientsstructspublicController();
			$this->Orientsstructs->request->params['controller'] = 'orientsstructs';

			CakeTestSession::start();
			CakeTestSession::delete( 'Auth' );
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
		 * Test de la méthode OrientsstructsController::_getIndexActionsList()
		 */
		public function testPrepareAndExplode() {
			$records = array(
				array(
					'Orientstruct' => array(
						'id' => 1
					)
				)
			);
			$params = array(
				'ajout_possible' => true,
				'rgorient_max' => 1,
				'personne_id' => 1,
				'dossier_menu' => array(
					'AdresseFoyer' => array(
						'01' => array(
							'codeinsee'
						)
					)
				)
			);

			/**
			 * Test 66
			 */
			Configure::write('Cg.departement', 66);
			CakeTestSession::write( WebrsaPermissions::$sessionPermissionsKey.'.Module:Orientsstructs', true );

			$results = $this->Orientsstructs->getIndexActionsList($records, $params);
			$expected = array(
				'/Orientsstructs/add/1' => array(
					'domain' => 'orientsstructs',
					'msgid' => 'Ajouter',
					'enabled' => true
				)
			);
			$this->assertEqual($results, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);

			/**
			 * Test 58
			 */
			Configure::write('Cg.departement', 58);
			CakeTestSession::write( WebrsaPermissions::$sessionPermissionsKey.'.Module:Proposorientationscovs58', true );

			$results = $this->Orientsstructs->getIndexActionsList($records, $params);
			$expected = array(
				'/Proposorientationscovs58/add/1' => array(
					'domain' => 'orientsstructs',
					'msgid' => null,
					'enabled' => true
				)
			);
			$this->assertEqual($results, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);

			/**
			 * Test 93 - rgorient_max >= 1
			 */
			Configure::write('Cg.departement', 93);
			CakeTestSession::write( WebrsaPermissions::$sessionPermissionsKey.'.Module:Reorientationseps93', true );

			$results = $this->Orientsstructs->getIndexActionsList($records, $params);
			$expected = array(
				'/Reorientationseps93/add/1' => array(
					'domain' => 'orientsstructs',
					'msgid' => null,
					'enabled' => true
				)
			);
			$this->assertEqual($results, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);

			/**
			 * Test 93 - rgorient_max === 0
			 */
			$params['rgorient_max'] = 0;
			$results = $this->Orientsstructs->getIndexActionsList($records, $params);
			$expected = array(
				'/Orientsstructs/add/1' => array(
					'domain' => 'orientsstructs',
					'msgid' => 'Demander une réorientation',
					'enabled' => true
				)
			);
			$this->assertEqual($results, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
		}
	}
?>