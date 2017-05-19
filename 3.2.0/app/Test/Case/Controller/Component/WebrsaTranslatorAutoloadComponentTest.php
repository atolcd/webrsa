<?php
	/**
	 * WebrsaTranslatorAutoloadComponentTest file
	 *
	 * PHP 5.3
	 *
	 * @package Translator
	 * @subpackage Test.Case.Utility.Translator
	 */

	App::uses('WebrsaTranslatorAutoloadComponent', 'Controller/Component');
	App::uses('Controller', 'Controller');

	/**
	 * ControllerController class
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class ControllerController extends Controller
	{
		public $components = array(
			'WebrsaTranslatorAutoload'
		);

		public function action() {}
	}

	/**
	 * WebrsaTranslatorAutoloadComponentTest class
	 *
	 * @package Translator
	 * @subpackage Test.Case.Utility.Translator
	 */
	class WebrsaTranslatorAutoloadComponentTest extends ControllerTestCase
	{
		/**
		 * setUp method
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();
			Translator::reset();
			App::build(array('locales' => APP.'Test'.DS.'Locale'.DS));
			Configure::write('WebrsaTranslator.suffix', 'suffix');
			$request = new CakeRequest('controller/action', false);
			$request->addParams(array('controller' => 'controller', 'action' => 'action'));
			$this->Controller = new ControllerController($request);
			$this->Controller->Components->init($this->Controller);
			$this->Controller->WebrsaTranslatorAutoload->initialize($this->Controller);
			$this->testAction('/controller/action', array('method' => 'GET'));
		}

		/**
		 * Test de la méthode WebrsaTranslatorAutoloadComponent::domains();
		 */
		public function testDomains() {
			$results = $this->Controller->WebrsaTranslatorAutoload->domains();
			$expected = array(
				(int) 0 => 'controller_action_suffix',
				(int) 1 => 'controller_action',
				(int) 2 => 'controller',
				(int) 3 => 'default'
			);
			$this->assertEqual($results, $expected, "Retourne la liste de domaines avec ou sans suffix");
		}
	}