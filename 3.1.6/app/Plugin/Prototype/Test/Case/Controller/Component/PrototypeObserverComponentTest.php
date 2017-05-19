<?php
	/**
	 * Code source de la classe PrototypeObserverComponentTest.
	 *
	 * PHP 5.3
	 *
	 * @package Prototype
	 * @subpackage Test.Case.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'PrototypeObserverComponent', 'Prototype.Controller/Component' );

	/**
	 * PrototypeObserverTestsController class
	 *
	 * @package Prototype
	 * @subpackage Test.Case.Controller.Component
	 */
	class PrototypeObserverTestsController extends AppController
	{
		/**
		 * name property
		 *
		 * @var string
		 */
		public $name = 'PrototypeObserverTestsController';

		/**
		 * uses property
		 *
		 * @var mixed null
		 */
		public $uses = array( 'Apple' );

		/**
		 * components property
		 *
		 * @var array
		 */
		public $components = array(
			'Prototype.PrototypeObserver'
		);
	}

	/**
	 * La classe PrototypeObserverComponentTest ...
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class PrototypeObserverComponentTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array();

		/**
		 * Controller property
		 *
		 * @var PrototypeObserverComponent
		 */
		public $Controller;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();

			$Request = new CakeRequest( 'apples/index', false );
			$Request->addParams(array( 'controller' => 'apples', 'action' => 'index' ) );

			$this->Controller = new PrototypeObserverTestsController( $Request );
			$this->Controller->Components->init( $this->Controller );
			$this->Controller->PrototypeObserver->initialize( $this->Controller );
		}

		/**
		 * Test de la méthode PrototypeObserverComponent::disableFieldsOnValue()
		 */
		public function testDisableFieldsOnValue() {
			// Les données
			$data = array(
				'Foo' => array(
					'state' => 'bar',
					'field1' => 3,
					'field2' => 4,
					'field3' => 5,
					'otherfield' => 6
				)
			);

			// 1. Désactivation
			$result = $this->Controller->PrototypeObserver->disableFieldsOnValue(
				$data,
				'Foo.state',
				array(
					'Foo.field1',
					'Foo.field2',
					'Foo.field3'
				),
				array( 'bar', 'baz' ),
				true
			);

			$expected = array(
				'Foo' => array(
					'state' => 'bar',
					'field1' => NULL,
					'field2' => NULL,
					'field3' => NULL,
					'otherfield' => 6
				),
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Masquage
			$result = $this->Controller->PrototypeObserver->disableFieldsOnValue(
				$data,
				'Foo.state',
				array(
					'Foo.field1',
					'Foo.field2',
					'Foo.field3'
				),
				array( 'bar', 'baz' ),
				true,
				true
			);

			$expected = array(
				'Foo' => array(
					'state' => 'bar',
					'otherfield' => 6
				),
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode PrototypeObserverComponent::disableFieldsOnCheckbox()
		 */
		public function testDisableFieldsOnCheckbox() {
			// Les données
			$data = array(
				'Foo' => array(
					'active' => '0',
					'field1' => 3,
					'field2' => 4,
					'field3' => 5,
					'otherfield' => 6
				)
			);

			// 1. Désactivation
			$result = $this->Controller->PrototypeObserver->disableFieldsOnCheckbox(
				$data,
				'Foo.active',
				array(
					'Foo.field1',
					'Foo.field2',
					'Foo.field3'
				),
				false,
				false
			);

			$expected = array(
				'Foo' => array(
					'active' => '0',
					'field1' => NULL,
					'field2' => NULL,
					'field3' => NULL,
					'otherfield' => 6
				),
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Masquage
			$result = $this->Controller->PrototypeObserver->disableFieldsOnCheckbox(
				$data,
				'Foo.active',
				array(
					'Foo.field1',
					'Foo.field2',
					'Foo.field3'
				),
				false,
				true
			);

			$expected = array(
				'Foo' => array(
					'active' => 0,
					'otherfield' => 6
				),
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>