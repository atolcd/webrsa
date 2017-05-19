<?php
	/**
	 * Code source de la classe PrototypeObserverHelperTest.
	 *
	 * PHP 5.3
	 *
	 * @package Prototype
	 * @subpackage Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'View', 'View' );
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( 'PrototypeObserverHelper', 'Prototype.View/Helper' );

	/**
	 * La classe PrototypeObserverHelperTest ...
	 *
	 * @package Prototype
	 * @subpackage Test.Case.View.Helper
	 */
	class PrototypeObserverHelperTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisées pour les tests.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'core.Apple',
		);

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
		 * @var PrototypeObserver
		 */
		public $Observer = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();

			$Request = new CakeRequest( 'apples/index', false );
			$Request->addParams(array( 'controller' => 'apples', 'action' => 'index' ) );

			$this->Controller = new Controller( $Request );
			$this->View = new View( $this->Controller );
			$this->Observer = new PrototypeObserverHelper( $this->View );
			$this->Observer->useBuffer = false;
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			parent::tearDown();
			unset( $this->Controller, $this->View, $this->Observer );
		}

		/**
		 * Test de la méthode PrototypeObserverHelper::disableFormOnSubmit()
		 *
		 * @medium
		 */
		public function testDisableFormOnSubmit() {
			// 1. Sans paramètre
			$result = $this->Observer->disableFormOnSubmit();
			$expected = '<script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFormOnSubmit( \'AppleIndexForm\' );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Sans message
			$result = $this->Observer->disableFormOnSubmit( 'UsersLoginForm' );
			$expected = '<script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFormOnSubmit( \'UsersLoginForm\' );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3. Avec message
			$result = $this->Observer->disableFormOnSubmit( 'UsersLoginForm', 'Connexion en cours' );
			$expected = '<script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFormOnSubmit( \'UsersLoginForm\', \'Connexion en cours\' );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode PrototypeObserverHelper::disableFieldsOnCheckbox()
		 */
		public function testDisableFieldsOnCheckbox() {
			// 1. Sans message
			$result = $this->Observer->disableFieldsOnCheckbox(
				'Documentbeneffp93.Documentbeneffp93.4',
				'Ficheprescription93.documentbeneffp93_autre',
				false,
				false
			);
			$expected = '<script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsOnCheckbox( \'Documentbeneffp93Documentbeneffp934\', [ \'Ficheprescription93Documentbeneffp93Autre\' ], false, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode PrototypeObserverHelper::disableFieldsetOnCheckbox()
		 */
		public function testDisableFieldsetOnCheckbox() {
			$result = $this->Observer->disableFieldsetOnCheckbox( 'Search.User.birthday', 'SearchUserBirthdayRange' );
			$expected = '<script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsetOnCheckbox( \'SearchUserBirthday\', \'SearchUserBirthdayRange\', false, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode PrototypeObserverHelper::dependantSelect()
		 */
		public function testDependantSelect() {
			$result = $this->Observer->dependantSelect(
				array(
					'Search.User.continent' => 'Search.User.country',
					'Search.User.country' => 'Search.User.region',
				)
			);
			$expected = '<script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	dependantSelect( \'SearchUserCountry\', \'SearchUserContinent\' );
dependantSelect( \'SearchUserRegion\', \'SearchUserCountry\' );
;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script>';

			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode PrototypeObserverHelper::disableFieldsOnValue()
		 */
		public function testDisableFieldsOnValue() {
			$result = $this->Observer->disableFieldsOnValue(
				'Ficheprescription93.personne_a_integre',
				array(
					'Ficheprescription93.personne_date_integration.day',
					'Ficheprescription93.personne_date_integration.month',
					'Ficheprescription93.personne_date_integration.year',
				),
				array( null, '', '0' ),
				true
			);

			$expected = '<script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsOnValue( \'Ficheprescription93PersonneAIntegre\', [ \'Ficheprescription93PersonneDateIntegrationDay\', \'Ficheprescription93PersonneDateIntegrationMonth\', \'Ficheprescription93PersonneDateIntegrationYear\' ], [ undefined, \'\', \'0\' ], true, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode PrototypeObserverHelper::disableFieldsetOnValue()
		 */
		public function testDisableFieldsetOnValue() {
			$result = $this->Observer->disableFieldsetOnValue(
				'Search.Ficheprescription93.exists',
				'SpecificitesFichesprescriptions93',
				array( null, '1' ),
				true,
				true
			);
			$expected = '<script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsetOnValue( \'SearchFicheprescription93Exists\', \'SpecificitesFichesprescriptions93\', [ undefined, \'1\' ], true, true );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode PrototypeObserverHelper::disableFieldsetOnRadioValue()
		 */
		public function testDisableFieldsetOnRadioValue() {
			$result = $this->Observer->disableFieldsetOnRadioValue(
				'MonFormulaire',
				'Search.Ficheprescription93.exists',
				'SpecificitesFichesprescriptions93',
				array( null, '1' ),
				true,
				true
			);

			$expected = '<script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsetOnRadioValue( \'MonFormulaire\', \'data[Search][Ficheprescription93][exists]\', \'SpecificitesFichesprescriptions93\', [ undefined, \'1\' ], true, true );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode PrototypeObserverHelper::disableFieldsOnRadioValue()
		 */
		public function testDisableFieldsOnRadioValue() {
			$result = $this->Observer->disableFieldsOnRadioValue(
				'MonFormulaire',
				'Ficheprescription93.personne_a_integre',
				array(
					'Ficheprescription93.personne_date_integration.day',
					'Ficheprescription93.personne_date_integration.month',
					'Ficheprescription93.personne_date_integration.year',
				),
				array( null, '', '0' ),
				true
			);

			$expected = '<script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsOnRadioValue( \'MonFormulaire\', \'data[Ficheprescription93][personne_a_integre]\', [ \'Ficheprescription93PersonneDateIntegrationDay\', \'Ficheprescription93PersonneDateIntegrationMonth\', \'Ficheprescription93PersonneDateIntegrationYear\' ], [ undefined, \'\', \'0\' ], true, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}


		/**
		 * Test de différentes méthodes avec utilisation du buffer pour alimenter
		 * scriptBottom.
		 */
		public function testUseBuffer() {
			$this->Observer->useBuffer = true;

			$result = $this->Observer->disableFormOnSubmit( 'UsersLoginForm' );
			$this->assertNull( $result );

			$result = $this->Observer->disableFieldsetOnCheckbox( 'Search.User.birthday', 'SearchUserBirthdayRange' );
			$this->assertNull( $result );

			$result = $this->Observer->dependantSelect(
				array(
					'Search.User.continent' => 'Search.User.country',
					'Search.User.country' => 'Search.User.region',
				)
			);
			$this->assertNull( $result );

			$result = $this->Observer->disableFieldsetOnValue(
				'Search.Ficheprescription93.exists',
				'SpecificitesFichesprescriptions93',
				'1',
				true,
				true
			);
			$this->assertNull( $result );

			$this->Observer->beforeLayout( 'Foos/index.ctp' );
			$result = $this->View->fetch( 'scriptBottom' );
			$expected = '<script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() {
try {
	observeDisableFormOnSubmit( \'UsersLoginForm\' );;
} catch( e ) {
	console.error( e );
}
try {
	observeDisableFieldsetOnCheckbox( \'SearchUserBirthday\', \'SearchUserBirthdayRange\', false, false );;
} catch( e ) {
	console.error( e );
}
try {
	dependantSelect( \'SearchUserCountry\', \'SearchUserContinent\' );
dependantSelect( \'SearchUserRegion\', \'SearchUserCountry\' );
;
} catch( e ) {
	console.error( e );
}
try {
	observeDisableFieldsetOnValue( \'SearchFicheprescription93Exists\', \'SpecificitesFichesprescriptions93\', [ \'1\' ], true, true );;
} catch( e ) {
	console.error( e );
}
} );
//]]>
</script>';

			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>