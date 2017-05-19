<?php
	/**
	 * Code source de la classe AppHelperTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'View', 'View' );
	App::uses( 'AppHelper', 'View/Helper' );

	/**
	 * Classe AppPublicHelper, permettant d'accéder aux méthodes protégées de
	 * la classe AppHelper.
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class AppPublicHelper extends AppHelper
	{
		/**
		 * Permet l'accès public à la méthode AppHelper::_cacheKey().
		 *
		 * @param string $modelName
		 * @return string
		 */
		public function cacheKey( $modelName ) {
			return $this->_cacheKey( $modelName );
		}
	}


	/**
	 * Classe AppHelperTest.
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class AppHelperTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés par ces tests unitaires.
		 *
		 * @var array
		 */
		public $fixtures = array();

		/**
		 * Préparation du test.
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();
			$controller = null;
			$this->View = new View( $controller );
			$this->App = new AppPublicHelper( $this->View );
		}

		/**
		 * Nettoyage postérieur au test.
		 *
		 * @return void
		 */
		public function tearDown() {
			parent::tearDown();
			unset( $this->View, $this->App );
		}

		/**
		 * Test de la méthode AppHelper::_cacheKey()
		 *
		 * @return void
		 */
		public function testMethod() {
			$result = $this->App->cacheKey( 'Site' );
			$expected = 'app_public_helper_sites';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}
	}
?>