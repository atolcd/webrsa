<?php
	/**
	 * Code source de la classe PrototypeAjaxHelperTest.
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
	App::uses( 'PrototypeAjaxHelper', 'Prototype.View/Helper' );

	/**
	 * La classe PrototypeAjaxHelperTest ...
	 *
	 * @package Prototype
	 * @subpackage Test.Case.View.Helper
	 */
	class PrototypeAjaxHelperTest extends CakeTestCase
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
		 * @var PrototypeAjax
		 */
		public $Ajax = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();

			$Request = new CakeRequest( 'apples/index', false );
			$Request->addParams(array( 'controller' => 'apples', 'action' => 'index' ) );

			$this->Controller = new Controller( $Request );
			$this->View = new View( $this->Controller );
			$this->Ajax = new PrototypeAjaxHelper( $this->View );
			$this->Ajax->useBuffer = false;
		}

		/**
		 * Contient les résultats attendus par fonction pour éviter le copier/coller.
		 *
		 * @var array
		 */
		public $results = array(
			'updateDivOnFieldsChange' => 'function updateDivOnFieldsChangeCoordonneesPrescripteur() {
		new Ajax.Updater(
			\'CoordonneesPrescripteur\',
			\'/ajax_prescripteur\',
			{
				asynchronous: true,
				evalScripts: true,
				parameters: { \'data[Ficheprescription93][structurereferente_id]\': $F( \'Ficheprescription93StructurereferenteId\' ),\'data[Ficheprescription93][referent_id]\': $F( \'Ficheprescription93ReferentId\' ) }
			}
		);
	}
	document.observe( \'dom:loaded\', function() { updateDivOnFieldsChangeCoordonneesPrescripteur(); } );
Event.observe( $( \'Ficheprescription93StructurereferenteId\' ), \'change\', function() { updateDivOnFieldsChangeCoordonneesPrescripteur(); } );
Event.observe( $( \'Ficheprescription93ReferentId\' ), \'change\', function() { updateDivOnFieldsChangeCoordonneesPrescripteur(); } );'
		);

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			parent::tearDown();
			unset( $this->Controller, $this->View, $this->Ajax );
		}

		/**
		 * Test de la méthode PrototypeAjaxHelper::updateDivOnFieldsChange()
		 */
		public function testUpdateDivOnFieldsChange() {
			$result = $this->Ajax->updateDivOnFieldsChange(
				'CoordonneesPrescripteur',
				array( 'action' => 'ajax_prescripteur' ),
				array(
					'Ficheprescription93.structurereferente_id',
					'Ficheprescription93.referent_id',
				)
			);

			$expected = "<script type=\"text/javascript\">
//<![CDATA[
{$this->results['updateDivOnFieldsChange']}
//]]>
</script>";
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode PrototypeAjaxHelper::observe()
		 */
		public function testObserve() {
			$result = $this->Ajax->observe(
				array(
					'Ficheprescription93.numconvention' => array( 'event' => 'keyup' ),
					'Ficheprescription93.typethematiquefp93_id',
					'Prestatairehorspdifp93.adresse' => array( 'event' => false )
				),
				array(
					'url' => array( 'action' => 'ajax_action' )
				)
			);

			$expected = '<script type="text/javascript">
//<![CDATA[
var ajax_parameters_f4996e4fb30300f09983a129c7e14162 = { \'url\': \'/ajax_action\', \'prefix\': \'\', \'fields\': [ \'Ficheprescription93Numconvention\', \'Ficheprescription93Typethematiquefp93Id\', \'Prestatairehorspdifp93Adresse\' ], \'min\': \'3\', \'delay\': \'500\' };
$( \'Ficheprescription93Numconvention\' ).writeAttribute( \'autocomplete\', \'off\' );Event.observe( $( \'Ficheprescription93Numconvention\' ), \'keyup\', function(event) { ajax_action( event, ajax_parameters_f4996e4fb30300f09983a129c7e14162 ); } );
Event.observe( $( \'Ficheprescription93Typethematiquefp93Id\' ), \'change\', function(event) { ajax_action( event, ajax_parameters_f4996e4fb30300f09983a129c7e14162 ); } );
var ajax_onload_parameters_f4996e4fb30300f09983a129c7e14162 =  Object.clone( ajax_parameters_f4996e4fb30300f09983a129c7e14162 );
				ajax_onload_parameters_f4996e4fb30300f09983a129c7e14162[\'values\'] = { \'Ficheprescription93Numconvention\': \'\', \'Ficheprescription93Typethematiquefp93Id\': \'\', \'Prestatairehorspdifp93Adresse\': \'\' };
				document.observe( \'dom:loaded\', function(event) { ajax_action( event, ajax_onload_parameters_f4996e4fb30300f09983a129c7e14162 ); } );

//]]>
</script>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de différentes méthodes avec utilisation du buffer pour alimenter
		 * scriptBottom.
		 */
		public function testUseBuffer() {
			$this->Ajax->useBuffer = true;

			$result = $this->Ajax->updateDivOnFieldsChange(
				'CoordonneesPrescripteur',
				array( 'action' => 'ajax_prescripteur' ),
				array(
					'Ficheprescription93.structurereferente_id',
					'Ficheprescription93.referent_id',
				)
			);
			$this->assertNull( $result );

			$this->Ajax->beforeLayout( 'Foos/index.ctp' );
			$result = $this->View->fetch( 'scriptBottom' );
			$expected = "<script type=\"text/javascript\">
//<![CDATA[
\n{$this->results['updateDivOnFieldsChange']}
//]]>
</script>";

			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>