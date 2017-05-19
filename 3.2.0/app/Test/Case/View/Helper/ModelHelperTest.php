<?php
	/**
	 * Code source de la classe ModelHelperTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'View', 'View' );
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( 'ModelHelper', 'View/Helper' );

	/**
	 * Classe ConcreteModelHelper, class concrète, permettant de plus d'accéder
	 * aux méthodes protégées de la classe ModelHelper.
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class ConcreteModelHelper extends ModelHelper
	{
		/**
		 * Permet l'accès public à la méthode AppHelper::_cacheKey().
		 *
		 * @param string $path
		 * @return string
		 */
		public function typeInfos( $path ) {
			return $this->_typeInfos( $path );
		}
	}

	/**
	 * Classe ModelHelperTest.
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class ModelHelperTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés par ces tests unitaires.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'core.Apple'
		);

		/**
		 * Préparation du test.
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();
			$controller = null;
			$this->View = new View( $controller );
			$this->Model = new ConcreteModelHelper( $this->View );
			$this->Apple = ClassRegistry::init( 'Apple' );
		}

		/**
		 * Nettoyage postérieur au test.
		 *
		 * @return void
		 */
		public function tearDown() {
			parent::tearDown();
			unset( $this->View, $this->Model );
		}

		/**
		 * Test de la fonction dataTranslate()
		 *
		 * @return void
		 */
		public function testDataTranslate() {
			$data = array(
				'User' => array(
					'id' => 1,
					'username' => 'BigFoot'
				)
			);
			$result = dataTranslate( $data, 'I am the #User.username#.' );
			$expected = 'I am the BigFoot.';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ModelHelper::primaryKey()
		 *
		 * @return void
		 */
		public function testPrimaryKey() {
			$result = $this->Model->primaryKey( 'Apple' );
			$expected = 'id';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ModelHelper::primaryKey() pour un modèle inexistant.
		 *
		 * @return void
		 */
		public function testPrimaryKeyInexistantModel() {
			$result = $this->Model->primaryKey( 'Foo' );
			$expected = null;
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ModelHelper::displayField()
		 *
		 * @return void
		 */
		public function testDisplayField() {
			$result = $this->Model->displayField( 'Apple' );
			$expected = 'name';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ModelHelper::type()
		 *
		 * @return void
		 */
		public function testType() {
			$result = $this->Model->type( 'Apple.name' );
			$expected = 'string';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ModelHelper::_typeInfos()
		 *
		 * @return void
		 */
		public function testTypeInfos() {
			$result = $this->Model->typeInfos( 'Apple.name' );
			$expected = array (
				'type' => 'string',
				'null' => false,
				'default' => '',
				'length' => 40,
			);
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}
	}
?>