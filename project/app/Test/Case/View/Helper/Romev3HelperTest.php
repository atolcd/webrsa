<?php
	/**
	 * Code source de la classe Romev3HelperTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'View', 'View' );
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( 'Romev3Helper', 'View/Helper' );

	/**
	 * La classe Romev3HelperTest ...
	 *
	 * @package app.Test.Case.View.Helper
	 */
	class Romev3HelperTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisées pour les tests.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Dsp'
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
		 * @var Romev3
		 */
		public $Romev3 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$Request = new CakeRequest();
			$this->Controller = new Controller( $Request );
			$this->View = new View( $this->Controller );
			$this->Romev3 = new Romev3Helper( $this->View );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			parent::tearDown();
			unset( $this->Controller, $this->View, $this->Romev3 );
		}

		/**
		 * Test de la méthode Romev3Helper::fieldset() lorsque Romev3.enabled
		 * vaut false.
		 *
		 * @medium
		 */
		public function testFieldsetDisabled() {
			Configure::write( 'Romev3.enabled', false );
			$this->Controller->request->addParams(
				array(
					'controller' => 'dsps',
					'action' => 'add',
					'pass' => array(),
					'named' => array()
				)
			);

			$params = array(
				//'prefix' => 'deract',
				'options' => array(
					'Deractromev3' => array(
						'familleromev3_id' => array(
							'1' => 'A - AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX'
						),
						'domaineromev3_id' => array(
							'1_1' => 'A11 - Engins agricoles et forestiers'
						),
						'metierromev3_id' => array(
							'1_1' => 'A1101 - Conduite d\'engins d\'exploitation agricole et forestière'
						),
						'appellationromev3_id' => array(
							'1_1' => 'Conducteur / Conductrice d\'engins de débardage'
						)
					)
				)
			);

			$result = $this->Romev3->fieldset( 'Deractromev3', $params );
			$expected = '';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Romev3Helper::fieldset() lorsque Romev3.enabled
		 * vaut true.
		 *
		 * @medium
		 */
		public function testFieldsetEnabled() {
			Configure::write( 'Romev3.enabled', true );
			$this->Controller->request->addParams(
				array(
					'controller' => 'dsps',
					'action' => 'add',
					'pass' => array(),
					'named' => array()
				)
			);

			$params = array(
				//'prefix' => 'deract',
				'options' => array(
					'Deractromev3' => array(
						'familleromev3_id' => array(
							'1' => 'A - AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX'
						),
						'domaineromev3_id' => array(
							'1_1' => 'A11 - Engins agricoles et forestiers'
						),
						'metierromev3_id' => array(
							'1_1' => 'A1101 - Conduite d\'engins d\'exploitation agricole et forestière'
						),
						'appellationromev3_id' => array(
							'1_1' => 'Conducteur / Conductrice d\'engins de débardage'
						)
					)
				)
			);

			$result = $this->Romev3->fieldset( 'Deractromev3', $params );
			$expected = '<fieldset id="Deractromev3FieldsetId" class="romev3"><legend>'.__d( 'dsps', 'Deractromev3' ).'</legend><div class="input text"><label for="Deractromev3Romev3">Recherche rapide</label><input name="data[Deractromev3][romev3]" type="text" id="Deractromev3Romev3"/></div><input type="hidden" name="data[Deractromev3][id]" id="Deractromev3Id"/><div class="input select"><label for="Deractromev3Familleromev3Id">Code famille</label><select name="data[Deractromev3][familleromev3_id]" id="Deractromev3Familleromev3Id">
<option value=""></option>
<option value="1">A - AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX</option>
</select></div><div class="input select"><label for="Deractromev3Domaineromev3Id">Code domaine</label><select name="data[Deractromev3][domaineromev3_id]" id="Deractromev3Domaineromev3Id">
<option value=""></option>
<option value="1_1">A11 - Engins agricoles et forestiers</option>
</select></div><div class="input select"><label for="Deractromev3Metierromev3Id">Code métier</label><select name="data[Deractromev3][metierromev3_id]" id="Deractromev3Metierromev3Id">
<option value=""></option>
<option value="1_1">A1101 - Conduite d&#039;engins d&#039;exploitation agricole et forestière</option>
</select></div><div class="input select"><label for="Deractromev3Appellationromev3Id">Appellation métier</label><select name="data[Deractromev3][appellationromev3_id]" id="Deractromev3Appellationromev3Id">
<option value=""></option>
<option value="1_1">Conducteur / Conductrice d&#039;engins de débardage</option>
</select></div><script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	dependantSelect( \'Deractromev3Domaineromev3Id\', \'Deractromev3Familleromev3Id\' );
dependantSelect( \'Deractromev3Metierromev3Id\', \'Deractromev3Domaineromev3Id\' );
dependantSelect( \'Deractromev3Appellationromev3Id\', \'Deractromev3Metierromev3Id\' );
;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script><script type="text/javascript">
//<![CDATA[
var ajax_parameters_7bb6e4326590a907d44a8d9609ef7a41 = { \'url\': \'/cataloguesromesv3/ajax_appellation\', \'prefix\': \'\', \'fields\': [ \'Deractromev3Romev3\' ], \'min\': \'3\', \'delay\': \'500\' };
$( \'Deractromev3Romev3\' ).writeAttribute( \'autocomplete\', \'off\' );Event.observe( $( \'Deractromev3Romev3\' ), \'keyup\', function(event) { ajax_action( event, ajax_parameters_7bb6e4326590a907d44a8d9609ef7a41 ); } );

//]]>
</script></fieldset>';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Romev3Helper::fieldset() lorsque Romev3.enabled
		 * vaut true, avec le préfixe Search.
		 *
		 * @medium
		 */
		public function testFieldsetEnabledPrefix() {
			Configure::write( 'Romev3.enabled', true );
			$this->Controller->request->addParams(
				array(
					'controller' => 'dsps',
					'action' => 'add',
					'pass' => array(),
					'named' => array()
				)
			);

			$params = array(
				'prefix' => 'Search',
				'options' => array(
					'Deractromev3' => array(
						'familleromev3_id' => array(
							'1' => 'A - AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX'
						),
						'domaineromev3_id' => array(
							'1_1' => 'A11 - Engins agricoles et forestiers'
						),
						'metierromev3_id' => array(
							'1_1' => 'A1101 - Conduite d\'engins d\'exploitation agricole et forestière'
						),
						'appellationromev3_id' => array(
							'1_1' => 'Conducteur / Conductrice d\'engins de débardage'
						)
					)
				)
			);

			$result = $this->Romev3->fieldset( 'Deractromev3', $params );
			$expected = '<fieldset id="SearchDeractromev3FieldsetId" class="romev3"><legend>'.__d( 'dsps', 'Search.Deractromev3' ).'</legend><div class="input text"><label for="SearchDeractromev3Romev3">'.__d( 'dsps', 'Search.Deractromev3.romev3' ).'</label><input name="data[Search][Deractromev3][romev3]" type="text" id="SearchDeractromev3Romev3"/></div><input type="hidden" name="data[Search][Deractromev3][id]" id="SearchDeractromev3Id"/><div class="input select"><label for="SearchDeractromev3Familleromev3Id">'.__d( 'dsps', 'Search.Deractromev3.familleromev3_id' ).'</label><select name="data[Search][Deractromev3][familleromev3_id]" id="SearchDeractromev3Familleromev3Id">
<option value=""></option>
<option value="1">A - AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX</option>
</select></div><div class="input select"><label for="SearchDeractromev3Domaineromev3Id">'.__d( 'dsps', 'Search.Deractromev3.domaineromev3_id' ).'</label><select name="data[Search][Deractromev3][domaineromev3_id]" id="SearchDeractromev3Domaineromev3Id">
<option value=""></option>
<option value="1_1">A11 - Engins agricoles et forestiers</option>
</select></div><div class="input select"><label for="SearchDeractromev3Metierromev3Id">'.__d( 'dsps', 'Search.Deractromev3.metierromev3_id' ).'</label><select name="data[Search][Deractromev3][metierromev3_id]" id="SearchDeractromev3Metierromev3Id">
<option value=""></option>
<option value="1_1">A1101 - Conduite d&#039;engins d&#039;exploitation agricole et forestière</option>
</select></div><div class="input select"><label for="SearchDeractromev3Appellationromev3Id">'.__d( 'dsps', 'Search.Deractromev3.appellationromev3_id' ).'</label><select name="data[Search][Deractromev3][appellationromev3_id]" id="SearchDeractromev3Appellationromev3Id">
<option value=""></option>
<option value="1_1">Conducteur / Conductrice d&#039;engins de débardage</option>
</select></div><script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	dependantSelect( \'SearchDeractromev3Domaineromev3Id\', \'SearchDeractromev3Familleromev3Id\' );
dependantSelect( \'SearchDeractromev3Metierromev3Id\', \'SearchDeractromev3Domaineromev3Id\' );
dependantSelect( \'SearchDeractromev3Appellationromev3Id\', \'SearchDeractromev3Metierromev3Id\' );
;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script><script type="text/javascript">
//<![CDATA[
var ajax_parameters_72abcbaee0877be0b1bb8721ace4137f = { \'url\': \'/cataloguesromesv3/ajax_appellation\', \'prefix\': \'\', \'fields\': [ \'SearchDeractromev3Romev3\' ], \'min\': \'3\', \'delay\': \'500\' };
$( \'SearchDeractromev3Romev3\' ).writeAttribute( \'autocomplete\', \'off\' );Event.observe( $( \'SearchDeractromev3Romev3\' ), \'keyup\', function(event) { ajax_action( event, ajax_parameters_72abcbaee0877be0b1bb8721ace4137f ); } );

//]]>
</script></fieldset>';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Romev3Helper::fields() lorsque Romev3.enabled
		 * vaut false.
		 *
		 * @medium
		 */
		public function testFieldsDisabled() {
			Configure::write( 'Romev3.enabled', false );

			$this->Controller->request->addParams(
				array(
					'controller' => 'dsps',
					'action' => 'add',
					'pass' => array(),
					'named' => array()
				)
			);

			$result = $this->Romev3->fields( 'Deractromev3' );

			$expected = array();

			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Romev3Helper::fields() lorsque Romev3.enabled
		 * vaut true.
		 *
		 * @medium
		 */
		public function testFieldsEnabled() {
			Configure::write( 'Romev3.enabled', true );

			$this->Controller->request->addParams(
				array(
					'controller' => 'dsps',
					'action' => 'add',
					'pass' => array(),
					'named' => array()
				)
			);

			$result = $this->Romev3->fields( 'Deractromev3' );

			$expected = array(
				'Deractfamilleromev3.name' => array(
					'label' => 'Code famille de la dernière activité',
					'type' => 'text',
				),
				'Deractdomaineromev3.name' => array(
					'label' => 'Code domaine de la dernière activité',
					'type' => 'text',
				),
				'Deractmetierromev3.name' => array(
					'label' => 'Code métier de la dernière activité',
					'type' => 'text',
				),
				'Deractappellationromev3.name' => array(
					'label' => 'Appellation métier de la dernière activité',
					'type' => 'text',
				),
			);

			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}
	}
?>