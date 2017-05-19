<?php
	/**
	 * Code source de la classe XformHelperTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'View', 'View' );
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( 'XformHelper', 'View/Helper' );

	/**
	 * La classe XformHelperTest ...
	 *
	 * @package app.Test.Case.View.Helper
	 */
	class XformHelperTest extends CakeTestCase
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
		 * @var Xform
		 */
		public $Xform = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$Request = new CakeRequest();
			$this->Controller = new Controller( $Request );
			$this->View = new View( $this->Controller );
			$this->Xform = new XformHelper( $this->View );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			parent::tearDown();
			unset( $this->Controller, $this->View, $this->Xform );
		}

		/**
		 * Normalise un morceau de code HTML en supprimant les espaces excédentaires
		 * et en remplaçant les retours à la ligne par un espace.
		 *
		 * @param string $string
		 * @return string
		 */
		protected function _normalizeHtml( $string ) {
			return preg_replace( '/[[:space:]]+/m', ' ', $string );
		}

		/**
		 * Test de la méthode XformHelper::getExtraValidationErrorMessages()
		 * lorsqu'aucun formulaire n'a été renvoyé.
		 */
		public function testGetExtraValidationErrorMessagesNoFormSent() {
			$this->Controller->request->addParams(
				array(
					'controller' => 'users',
					'action' => 'add',
					'pass' => array( ),
					'named' => array( )
				)
			);

			$result = $this->_normalizeHtml( $this->Xform->getExtraValidationErrorMessages() );
			$expected = $this->_normalizeHtml( '' );
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode XformHelper::getExtraValidationErrorMessages()
		 * lorsqu'un formulaire a été renvoyé.
		 */
		public function testGetExtraValidationErrorMessagesFormSent() {
			$data = array(
				'User' => array(
					'id' => null,
					'username' => 'localhost'
				)
			);
			$this->Controller->request->data = array( $data );
			$this->Xform->validationErrors = array(
				'User' => array(),
				'Extra1' => array(
					'field' => array(
						0 => 'Extra validation error 1'
					)
				),
				'Extra2' => array(
					'field' => array(
						0 => 'Extra validation error 2'
					)
				),
			);
			$this->Controller->request->addParams(
				array(
					'controller' => 'users',
					'action' => 'add',
					'pass' => array( ),
					'named' => array( )
				)
			);

			$result = $this->_normalizeHtml( $this->Xform->getExtraValidationErrorMessages() );
			$expected = $this->_normalizeHtml( '<div class="error_message"><ul><li>Extra validation error 1</li><li>Extra validation error 2</li></ul></div>' );
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode XformHelper::getExtraValidationErrorMessages()
		 * lorsqu'un formulaire a été renvoyé et que des alias supplémentaires
		 * ont été passés en paramètre.
		 */
		public function testGetExtraValidationErrorMessagesFormSentWithExtra() {
			$data = array(
				'User' => array(
					'id' => null,
					'username' => 'localhost'
				)
			);
			$this->Controller->request->data = array( $data );
			$this->Xform->validationErrors = array(
				'User' => array(),
				'Extra1' => array(
					'field' => array(
						0 => 'Extra validation error 1'
					)
				),
				'Extra2' => array(
					'field' => array(
						0 => 'Extra validation error 2'
					)
				),
			);
			$this->Controller->request->addParams(
				array(
					'controller' => 'users',
					'action' => 'add',
					'pass' => array( ),
					'named' => array( )
				)
			);

			$result = $this->_normalizeHtml( $this->Xform->getExtraValidationErrorMessages( array( 'Extra2' ) ) );
			$expected = $this->_normalizeHtml( '<div class="error_message"><ul><li>Extra validation error 1</li></ul></div>' );
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}
	}
?>