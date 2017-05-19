<?php
	/**
	 * Code source de la classe WebrsaAbstractLogicTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractLogic', 'Model' );

	/**
	 * Class concrête pour les tests.
	 *
	 * @package app.Test.Case.Model
	 */
	class WebrsaConcreteLogic extends WebrsaAbstractLogic
	{

	}

	/**
	 * La classe WebrsaAbstractLogicTest réalise les tests unitaires de la classe WebrsaAbstractLogic.
	 *
	 * @package app.Test.Case.Model
	 */
	class WebrsaAbstractLogicTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Typeorient'
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();

			$this->WebrsaAbstractLogic = ClassRegistry::init( 'WebrsaConcreteLogic' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->WebrsaAbstractLogic );
			parent::tearDown();
		}

		/**
		 * Test de la méthode WebrsaAbstractLogic::loadModel().
		 */
		public function testLoadModel() {
			$this->assertTrue( $this->WebrsaAbstractLogic->loadModel( 'Typeorient' ) );

			$result = $this->WebrsaAbstractLogic->Typeorient->alias;
			$this->assertEqual( $result, 'Typeorient', var_export( $result, true ) );
		}
	}
?>
