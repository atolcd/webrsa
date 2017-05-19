<?php
	/**
	 * Code source de la classe SearchFormHelperTest.
	 *
	 * PHP 5.3
	 *
	 * @package Search
	 * @subpackage Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'View', 'View' );
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( 'SearchFormHelper', 'Search.View/Helper' );

	App::uses( 'CakeTestSelectOptions', 'CakeTest.View/Helper' );

	/**
	 * La classe SearchFormHelperTest ...
	 *
	 * @package Search
	 * @subpackage Test.Case.View.Helper
	 */
	class SearchFormHelperTest extends CakeTestCase
	{
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
		 * @var SearchForm
		 */
		public $SearchForm = null;

		/**
		 * Fixtures utilisées.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Dossier',
			'core.Apple'
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$Request = new CakeRequest();
			$this->Controller = new Controller( $Request );
			$this->View = new View( $this->Controller );
			$this->SearchForm = new SearchFormHelper( $this->View );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Controller, $this->View, $this->SearchForm );
			parent::tearDown();
		}

		/**
		 * Test de la méthode SearchFormHelper::dependantCheckboxes()
		 *
		 * @medium
		 */
		public function testDependantCheckboxes() {
			$this->Controller->request->addParams(
				array(
					'controller' => 'users',
					'action' => 'index',
					'pass' => array( ),
					'named' => array( )
				)
			);
			$options = array( '2' => 'Ouvert', '6' => 'Clos' );

			$result = $this->SearchForm->dependantCheckboxes( 'Search.Dossier.etatdosrsa', array( 'options' => $options ) );
			$expected = '<div class="input checkbox"><input type="hidden" name="data[Search][Dossier][etatdosrsa_choice]" id="SearchDossierEtatdosrsaChoice_" value="0"/><input type="checkbox" name="data[Search][Dossier][etatdosrsa_choice]" value="1" id="SearchDossierEtatdosrsaChoice"/><label for="SearchDossierEtatdosrsaChoice">Search.Dossier.etatdosrsa_choice</label></div><fieldset id="SearchDossierEtatdosrsaFieldset"><legend>Search.Dossier.etatdosrsa</legend><div class="input select"><input type="hidden" name="data[Search][Dossier][etatdosrsa]" value="" id="SearchDossierEtatdosrsa"/>

<div class="checkbox"><input type="checkbox" name="data[Search][Dossier][etatdosrsa][]" value="2" id="SearchDossierEtatdosrsa2" /><label for="SearchDossierEtatdosrsa2">Ouvert</label></div>
<div class="checkbox"><input type="checkbox" name="data[Search][Dossier][etatdosrsa][]" value="6" id="SearchDossierEtatdosrsa6" /><label for="SearchDossierEtatdosrsa6">Clos</label></div>
</div></fieldset><script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsetOnCheckbox( \'SearchDossierEtatdosrsaChoice\', \'SearchDossierEtatdosrsaFieldset\', false, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode SearchFormHelper::dependantCheckboxes() avec des
		 * boutons pour tout cocher / tout décocher.
		 *
		 * @medium
		 */
		public function testDependantCheckboxesToutCocherToutDecocher() {
			$this->Controller->request->addParams(
				array(
					'controller' => 'users',
					'action' => 'index',
					'pass' => array( ),
					'named' => array( )
				)
			);
			$options = array( '2' => 'Ouvert', '6' => 'Clos' );

			$result = $this->SearchForm->dependantCheckboxes( 'Search.Dossier.etatdosrsa', array( 'options' => $options, 'buttons' => true ) );
			$expected = '<div class="input checkbox"><input type="hidden" name="data[Search][Dossier][etatdosrsa_choice]" id="SearchDossierEtatdosrsaChoice_" value="0"/><input type="checkbox" name="data[Search][Dossier][etatdosrsa_choice]" value="1" id="SearchDossierEtatdosrsaChoice"/><label for="SearchDossierEtatdosrsaChoice">Search.Dossier.etatdosrsa_choice</label></div><fieldset id="SearchDossierEtatdosrsaFieldset"><legend>Search.Dossier.etatdosrsa</legend><div class="buttons"><button type="button" onclick="try { toutCocher( \'input[name=\\\'data[Search][Dossier][etatdosrsa][]\\\']\' ); } catch( e ) { console.log( e ); }; return false;">Tout cocher</button><button type="button" onclick="toutDecocher( \'input[name=\\\'data[Search][Dossier][etatdosrsa][]\\\']\' ); return false;">Tout décocher</button></div><div class="input select"><input type="hidden" name="data[Search][Dossier][etatdosrsa]" value="" id="SearchDossierEtatdosrsa"/>

<div class="checkbox"><input type="checkbox" name="data[Search][Dossier][etatdosrsa][]" value="2" id="SearchDossierEtatdosrsa2" /><label for="SearchDossierEtatdosrsa2">Ouvert</label></div>
<div class="checkbox"><input type="checkbox" name="data[Search][Dossier][etatdosrsa][]" value="6" id="SearchDossierEtatdosrsa6" /><label for="SearchDossierEtatdosrsa6">Clos</label></div>
</div></fieldset><script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsetOnCheckbox( \'SearchDossierEtatdosrsaChoice\', \'SearchDossierEtatdosrsaFieldset\', false, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode SearchFormHelper::dependantCheckboxes() avec coche
		 * automatique losque l'on coche le checkbox parent.
		 *
		 * @medium
		 */
		public function testDependantCheckboxesAutoCheck() {
			$this->Controller->request->addParams(
				array(
					'controller' => 'users',
					'action' => 'index',
					'pass' => array( ),
					'named' => array( )
				)
			);
			$options = array( '2' => 'Ouvert', '6' => 'Clos' );

			$result = $this->SearchForm->dependantCheckboxes( 'Search.Dossier.etatdosrsa', array( 'options' => $options, 'buttons' => true, 'autoCheck' => true ) );
			$expected = '<div class="input checkbox"><input type="hidden" name="data[Search][Dossier][etatdosrsa_choice]" id="SearchDossierEtatdosrsaChoice_" value="0"/><input type="checkbox" name="data[Search][Dossier][etatdosrsa_choice]" onclick="try { toutCocher( &#039;input[name=\\&#039;data[Search][Dossier][etatdosrsa][]\\&#039;]&#039; ); } catch( e ) { console.log( e ); };" value="1" id="SearchDossierEtatdosrsaChoice"/><label for="SearchDossierEtatdosrsaChoice">Search.Dossier.etatdosrsa_choice</label></div><fieldset id="SearchDossierEtatdosrsaFieldset"><legend>Search.Dossier.etatdosrsa</legend><div class="buttons"><button type="button" onclick="try { toutCocher( \'input[name=\\\'data[Search][Dossier][etatdosrsa][]\\\']\' ); } catch( e ) { console.log( e ); }; return false;">Tout cocher</button><button type="button" onclick="toutDecocher( \'input[name=\\\'data[Search][Dossier][etatdosrsa][]\\\']\' ); return false;">Tout décocher</button></div><div class="input select"><input type="hidden" name="data[Search][Dossier][etatdosrsa]" value="" id="SearchDossierEtatdosrsa"/>

<div class="checkbox"><input type="checkbox" name="data[Search][Dossier][etatdosrsa][]" value="2" id="SearchDossierEtatdosrsa2" /><label for="SearchDossierEtatdosrsa2">Ouvert</label></div>
<div class="checkbox"><input type="checkbox" name="data[Search][Dossier][etatdosrsa][]" value="6" id="SearchDossierEtatdosrsa6" /><label for="SearchDossierEtatdosrsa6">Clos</label></div>
</div></fieldset><script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsetOnCheckbox( \'SearchDossierEtatdosrsaChoice\', \'SearchDossierEtatdosrsaFieldset\', false, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode SearchFormHelper::dateRange()
		 */
		public function testDateRange() {
			$result = $this->SearchForm->dateRange( 'Search.Apple.date' );

			$timestampFrom = strtotime( '-1 week' );
			$timestampTo = strtotime( 'now' );

			$thisYearFrom = date( 'Y', $timestampFrom );
			$thisYearTo = date( 'Y', $timestampTo );
			$yearsFrom = CakeTestSelectOptions::years( $thisYearFrom, $thisYearFrom - 120, $thisYearFrom );
			$yearsTo = CakeTestSelectOptions::years( $thisYearTo + 5, $thisYearTo - 120, $thisYearTo );

			$thisMonthFrom = date( 'm', $timestampFrom );
			$thisMonthTo = date( 'm', $timestampTo );
			$monthsFrom = CakeTestSelectOptions::months( $thisMonthFrom );
			$monthsTo = CakeTestSelectOptions::months( $thisMonthTo );

			$thisDayFrom = date( 'd', $timestampFrom );
			$thisDayTo = date( 'd', $timestampTo );
			$daysFrom = CakeTestSelectOptions::days( $thisDayFrom );
			$daysTo = CakeTestSelectOptions::days( $thisDayTo );


			$expected = '<script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsetOnCheckbox( \'SearchAppleDate\', \'SearchAppleDate_from_to\', false, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script><div class="input checkbox"><input type="hidden" name="data[Search][Apple][date]" id="SearchAppleDate_" value="0"/><input type="checkbox" name="data[Search][Apple][date]" value="1" id="SearchAppleDate"/><label for="SearchAppleDate">Filtrer par search.Apple.date</label></div><fieldset id="SearchAppleDate_from_to"><legend>Search.Apple.date</legend><div class="input date"><label for="SearchAppleDateFromDay">Du (inclus)</label><select name="data[Search][Apple][date_from][day]" id="SearchAppleDateFromDay">
'.$daysFrom.'
</select>-<select name="data[Search][Apple][date_from][month]" id="SearchAppleDateFromMonth">
'.$monthsFrom.'
</select>-<select name="data[Search][Apple][date_from][year]" id="SearchAppleDateFromYear">
'.$yearsFrom.'
</select></div><div class="input date"><label for="SearchAppleDateToDay">Au (inclus)</label><select name="data[Search][Apple][date_to][day]" id="SearchAppleDateToDay">
'.$daysTo.'
</select>-<select name="data[Search][Apple][date_to][month]" id="SearchAppleDateToMonth">
'.$monthsTo.'
</select>-<select name="data[Search][Apple][date_to][year]" id="SearchAppleDateToYear">
'.$yearsTo.'
</select></div></fieldset>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode SearchForm::observeDisableFormOnSubmit()
		 */
		public function testObserveDisableFormOnSubmit() {
			// Sans message à l'utilisateur
			$result = $this->SearchForm->observeDisableFormOnSubmit( 'UsersEditForm' );
			$expected = '<script type=\'text/javascript\'>document.observe( \'dom:loaded\', function() {
					observeDisableFormOnSubmit( \'UsersEditForm\' );
				} );</script>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// Avec message à l'utilisateur
			$result = $this->SearchForm->observeDisableFormOnSubmit( 'UsersEditForm', 'Merci de patienter' );
			$expected = '<script type=\'text/javascript\'>document.observe( \'dom:loaded\', function() {
					observeDisableFormOnSubmit( \'UsersEditForm\', \'Merci de patienter\' );
				} );</script>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode SearchForm::jsObserveDependantSelect()
		 */
		/*public function testjsObserveDependantSelect() {
			// Sans message à l'utilisateur
			$result = $this->SearchForm->jsObserveDependantSelect(
				array(
					'Search.Master.id' => 'Search.Slave.id',
				)
			);
			$expected = '<script type="text/javascript">
//<![CDATA[
document.observe( "dom:loaded", function() {
dependantSelect( \'SearchSlaveId\', \'SearchMasterId\' );
} );
//]]>
</script>';
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}*/
	}
?>