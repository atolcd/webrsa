<?php
	/**
	 * Code source de la classe TranslatorHelperTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaTranslator', 'Utility');
	App::uses('TranslatorHelper', 'View/Helper');
	App::uses('Controller', 'Controller');
	App::uses('CakeRoute', 'Routing/Route');

	/**
	 * La classe TranslatorHelperTest ...
	 *
	 * @package app.Test.Case.View.Helper
	 */
	class TranslatorHelperTest extends CakeTestCase
	{
		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			App::build(array('locales' => APP.'Test'.DS.'Locale'.DS));
			WebrsaTranslator::domains(array('controller'));
			$Request = new CakeRequest();
			$this->Controller = new Controller($Request);
			$this->View = new View($this->Controller);
			$this->Translator = new TranslatorHelper($this->View);
		}
		
		public function testNormalize() {
			$fields = array(
				'Monmodel.field',
				'Monmodel.field2' => array('type' => 'hidden'),
				'Model.test1',
				'Fake.field' => array('label' => 'déjà defini'),
				'data[Model][input]',
				'/controller/action/#Model.id#',
				'/Controller/action2/#Model.id#' => array('confirm' => true),
				'/pas_de/traduction/#Model.id#',
			);
			$results = $this->Translator->normalize($fields);
			$expected = array(
				'Monmodel.field' => array('label' => 'Monmodel.field dans controller.po'),
				'Monmodel.field2' => array('type' => 'hidden'),
				'Model.test1' => array('label' => 'Model.test1 dans model.po'),
				'Fake.field' => array('label' => 'déjà defini'),
				'data[Model][input]' => array(),
				'/controller/action/#Model.id#' => array(
					'title' => 'Traduction (titre) path dans controller.po',
					'msgid' => 'Traduction path dans controller.po'
				),
				'/Controller/action2/#Model.id#' => array(
					'title' => 'Traduction (titre) path 2 dans controller.po',
					'msgid' => 'Traduction path 2 dans controller.po',
					'confirm' => 'Traduction (confirm) path 2 dans controller.po'
				),
				'/pas_de/traduction/#Model.id#' => array(
					'title' => '/PasDe/traduction/#Model.id#',
					'msgid' => '/PasDe/traduction'
				),
			);
			$this->assertEquals($results, $expected, "All in one");
		}
	}
?>