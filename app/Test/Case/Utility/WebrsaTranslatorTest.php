<?php
	/**
	 * TranslatorTest file
	 *
	 * PHP 5.3
	 *
	 * @package Translator
	 * @subpackage Test.Case.Utility.Translator
	 */

	App::uses('WebrsaTranslator', 'Utility');

	/**
	 * TranslatorTest class
	 *
	 * @package Translator
	 * @subpackage Test.Case.Utility.Translator
	 */
	class WebrsaTranslatorTest extends CakeTestCase
	{
		/**
		 * setUp method
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();
			Configure::write('Config.language', 'fre');
			App::build(array('locales' => APP.'Test'.DS.'Locale'.DS));
			WebrsaTranslator::reset();
			WebrsaTranslator::domains(array('controller_action_suffix', 'controller_action', 'controller'));
		}

		/**
		 * Test de la méthode Translator::translate();
		 */
		public function testTranslate() {
			$result = WebrsaTranslator::translate('pas de traduction');
			$expected = 'pas de traduction';
			$this->assertEquals($result, $expected, $expected);
			
			$result = WebrsaTranslator::translate('test1');
			$expected = 'test1 dans controller_action_suffix.po';
			$this->assertEquals($result, $expected, $expected);
			
			$result = WebrsaTranslator::translate('test2');
			$expected = 'test2 dans controller_action.po';
			$this->assertEquals($result, $expected, $expected);
			
			$result = WebrsaTranslator::translate('test3');
			$expected = 'test3 dans controller.po';
			$this->assertEquals($result, $expected, $expected);
			
			$result = WebrsaTranslator::translate('Model.test1');
			$expected = 'Model.test1 dans model.po';
			$this->assertEquals($result, $expected, $expected);
		}

		/**
		 * Test de la méthode __m();
		 */
		public function test__m() {
			$result = __m('Model.test1');
			$expected = 'Model.test1 dans model.po';
			$this->assertEquals($result, $expected, $expected);
		}
	}