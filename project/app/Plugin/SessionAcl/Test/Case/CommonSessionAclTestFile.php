<?php
/**
 * SessionAclComponentTest file
 *
 * PHP 5.3
 *
 * @package SessionAcl
 * @subpackage Test.Case.Controller.Component
 */

App::uses('SessionAcl', 'SessionAcl.Model/Datasource');
App::uses('SessionAclComponent', 'SessionAcl.Controller/Component');
App::uses('SessionAclUtility', 'SessionAcl.Utility');
App::uses('Controller', 'Controller');

/**
 * class TestSessionController pour simuler un vrai controller
 */
class TestSessionController extends Controller
{
	public $components = array(
		'Session',
		'Auth',
		'Acl' => array(
			'className' => 'SessionAcl.SessionAcl',
			'userModel' => 'SessionAclTestUser'
		),
	);

	public $uses = array(
		'SessionAclTestUser'
	);

	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow('*');
	}

	public function index() {}
}

class NumeroDeuxController extends Controller
{
	public function action1() {}
	public function action2() {}
}

class CustomApp extends App
{
	public static function setControllers(array $controllers) {
		static::$_objects['app']['controller'] = $controllers;
	}
}

/**
 * SessionAclComponentTest class
 *
 * @package SessionAcl
 * @subpackage Test.Case.Controller.Component
 */
abstract class CommonSessionAclTestFile extends CakeTestCase
{
	/**
	 * @var array
	 */
	public $fixtures = array(
		'plugin.SessionAcl.SessionAclAco',
		'plugin.SessionAcl.SessionAclAro',
		'plugin.SessionAcl.SessionAclAroAco',
		'plugin.SessionAcl.SessionAclGroup',
		'plugin.SessionAcl.SessionAclUser',
	);

	/**
	 * setUp method
	 */
	public function setUp() {
		App::build(
			array(
				'Model' => array(CakePlugin::path('SessionAcl' ).'Test'.DS.'Model'.DS)
			),
			App::RESET
		);
		
		CustomApp::setControllers(array('TestSessionController', 'NumeroDeuxController'));

		$dbConfig = ClassRegistry::init('SessionAclTestUser')->useDbConfig;
		ClassRegistry::init('Aro')->useDbConfig = $dbConfig;
		ClassRegistry::init('Aro')->useTable = 'sessionacl_aros';
		ClassRegistry::init('Aco')->useDbConfig = $dbConfig;
		ClassRegistry::init('Aco')->useTable = 'sessionacl_acos';
		ClassRegistry::init('Permission')->useDbConfig = $dbConfig;
		ClassRegistry::init('Permission')->useTable = 'sessionacl_aros_acos';

		Router::reload();
		$request = new CakeRequest();

		$request->addParams(
			array(
				'plugin' => null,
				'controller' => 'TestSession',
				'action' => 'index',
			)
		);

		Router::setRequestInfo($request);

		$this->Controller = new TestSessionController($request);
		$this->Controller->constructClasses();
		$this->Controller->Session->destroy();
		$this->Controller->Session->write('Auth', null);
		$this->Controller->Session->write('Auth.SessionAclTestUser.id', 1);
		$this->Controller->SessionAclTestUser->id = 1;
		$this->Controller->SessionAclTestUser->Group->id = 1;
	}
}