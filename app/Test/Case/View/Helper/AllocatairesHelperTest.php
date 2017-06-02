<?php
	/**
	 * Code source de la classe AllocatairesHelperTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('Controller', 'Controller');
	App::uses( 'View', 'View' );
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( 'AllocatairesHelper', 'View/Helper' );

	App::uses( 'CakeTestSelectOptions', 'CakeTest.View/Helper' );

	/**
	 * Tests unitaires de la classe AllocatairesHelper.
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class AllocatairesHelperTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Adresse',
			'app.Adressefoyer',
			'app.Calculdroitrsa',
			'app.Detailcalculdroitrsa',
			'app.Detaildroitrsa',
			'app.Dossier',
			'app.Foyer',
			'app.Personne',
			'app.PersonneReferent',
			'app.Prestation',
			'app.Referent',
			'app.Serviceinstructeur',
			'app.Sitecov58',
			'app.Situationdossierrsa',
			'app.Structurereferente',
		);

		/**
		 * Exécuté avant chaque test.
		 */
		public function setUp() {
			Configure::delete( 'ConfigurableQuery.common' );
			Configure::delete( 'ValidateAllowEmpty' );
			Configure::delete( '_ValidationConfiguredAllowEmptyFields' );
		}

		/**
		 * Préparation du test.
		 *
		 * @param array $url Un array représentant une URL à la mode Cake (par
		 * défaut, équivaudra à /dossiers/search )
		 * @param array $settings Les paramètres éventuels à passer à AllocataireHelper
		 */
		public function setUpUrl( array $url = array(), array $settings = array() ) {
			$url += array( 'controller' => 'dossiers', 'action' => 'search' );
			$Request = new CakeRequest( "{$url['controller']}/{$url['action']}", false );
			$Request->addParams( $url );

			$this->Controller = new Controller( $Request );
			$this->View = new View( $this->Controller );
			$this->Allocataires = new AllocatairesHelper( $this->View, $settings );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			parent::tearDown();
			unset( $this->View, $this->Allocataires );
		}

		/**
		 * Test de la méthode AllocatairesHelper::blocDossier()
		 */
		public function testBlocDossier() {
			$this->setUpUrl();
			$options = CakeTestSelectOptions::ymdRange( '-1 week', 'now' );

			$result = $this->Allocataires->blocDossier( array() );
			$expected = '<fieldset><legend>Recherche par dossier</legend><div class="input text required"><label for="SearchDossierNumdemrsa">Numéro de dossier RSA</label><input name="data[Search][Dossier][numdemrsa]" maxlength="11" type="text" id="SearchDossierNumdemrsa"/></div><div class="input text"><label for="SearchDossierMatricule">' . __d( 'dossier', 'Dossier.matricule.large' ) . '</label><input name="data[Search][Dossier][matricule]" maxlength="15" type="text" id="SearchDossierMatricule"/></div><div class="input checkbox"><input type="hidden" name="data[Search][Dossier][dernier]" id="SearchDossierDernier_" value="0"/><input type="checkbox" name="data[Search][Dossier][dernier]"  value="1" id="SearchDossierDernier"/><label for="SearchDossierDernier">Uniquement la dernière demande RSA pour un même allocataire</label></div><script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsetOnCheckbox( \'SearchDossierDtdemrsa\', \'SearchDossierDtdemrsa_from_to\', false, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script><div class="input checkbox"><input type="hidden" name="data[Search][Dossier][dtdemrsa]" id="SearchDossierDtdemrsa_" value="0"/><input type="checkbox" name="data[Search][Dossier][dtdemrsa]"  value="1" id="SearchDossierDtdemrsa"/><label for="SearchDossierDtdemrsa">Filtrer par date de demande RSA</label></div><fieldset id="SearchDossierDtdemrsa_from_to"><legend>Date de demande RSA</legend><div class="input date"><label for="SearchDossierDtdemrsaFromDay">Du (inclus)</label><select name="data[Search][Dossier][dtdemrsa_from][day]" id="SearchDossierDtdemrsaFromDay">
'.$options['From']['days'].'
</select>-<select name="data[Search][Dossier][dtdemrsa_from][month]" id="SearchDossierDtdemrsaFromMonth">
'.$options['From']['months'].'
</select>-<select name="data[Search][Dossier][dtdemrsa_from][year]" id="SearchDossierDtdemrsaFromYear">
'.$options['From']['years'].'
</select></div><div class="input date"><label for="SearchDossierDtdemrsaToDay">Au (inclus)</label><select name="data[Search][Dossier][dtdemrsa_to][day]" id="SearchDossierDtdemrsaToDay">
'.$options['To']['days'].'
</select>-<select name="data[Search][Dossier][dtdemrsa_to][month]" id="SearchDossierDtdemrsaToMonth">
'.$options['To']['months'].'
</select>-<select name="data[Search][Dossier][dtdemrsa_to][year]" id="SearchDossierDtdemrsaToYear">
'.$options['To']['years'].'
</select></div></fieldset></fieldset>';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode AllocatairesHelper::blocDossier() sans fieldset.
		 */
		public function testBlocDossierNoFieldset() {
			$this->setUpUrl();
			$options = CakeTestSelectOptions::ymdRange( '-1 week', 'now' );

			$result = $this->Allocataires->blocDossier( array( 'fieldset' => false ) );
			$expected = '<div class="input text required"><label for="SearchDossierNumdemrsa">Numéro de dossier RSA</label><input name="data[Search][Dossier][numdemrsa]" maxlength="11" type="text" id="SearchDossierNumdemrsa"/></div><div class="input text"><label for="SearchDossierMatricule">' . __d( 'dossier', 'Dossier.matricule.large' ) . '</label><input name="data[Search][Dossier][matricule]" maxlength="15" type="text" id="SearchDossierMatricule"/></div><div class="input checkbox"><input type="hidden" name="data[Search][Dossier][dernier]" id="SearchDossierDernier_" value="0"/><input type="checkbox" name="data[Search][Dossier][dernier]"  value="1" id="SearchDossierDernier"/><label for="SearchDossierDernier">Uniquement la dernière demande RSA pour un même allocataire</label></div><script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsetOnCheckbox( \'SearchDossierDtdemrsa\', \'SearchDossierDtdemrsa_from_to\', false, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script><div class="input checkbox"><input type="hidden" name="data[Search][Dossier][dtdemrsa]" id="SearchDossierDtdemrsa_" value="0"/><input type="checkbox" name="data[Search][Dossier][dtdemrsa]"  value="1" id="SearchDossierDtdemrsa"/><label for="SearchDossierDtdemrsa">Filtrer par date de demande RSA</label></div><fieldset id="SearchDossierDtdemrsa_from_to"><legend>Date de demande RSA</legend><div class="input date"><label for="SearchDossierDtdemrsaFromDay">Du (inclus)</label><select name="data[Search][Dossier][dtdemrsa_from][day]" id="SearchDossierDtdemrsaFromDay">
'.$options['From']['days'].'
</select>-<select name="data[Search][Dossier][dtdemrsa_from][month]" id="SearchDossierDtdemrsaFromMonth">
'.$options['From']['months'].'
</select>-<select name="data[Search][Dossier][dtdemrsa_from][year]" id="SearchDossierDtdemrsaFromYear">
'.$options['From']['years'].'
</select></div><div class="input date"><label for="SearchDossierDtdemrsaToDay">Au (inclus)</label><select name="data[Search][Dossier][dtdemrsa_to][day]" id="SearchDossierDtdemrsaToDay">
'.$options['To']['days'].'
</select>-<select name="data[Search][Dossier][dtdemrsa_to][month]" id="SearchDossierDtdemrsaToMonth">
'.$options['To']['months'].'
</select>-<select name="data[Search][Dossier][dtdemrsa_to][year]" id="SearchDossierDtdemrsaToYear">
'.$options['To']['years'].'
</select></div></fieldset>';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode AllocatairesHelper::blocDossier() avec des options
		 * pour l'etatdosrsa et la natpf et les autres
		 */
		public function testBlocDossierOptions() {
			$this->setUpUrl();
			$options = CakeTestSelectOptions::ymdRange( '-1 week', 'now' );

			$params = array(
				'options' => array(
					'Detailcalculdroitrsa' => array(
						'natpf' => array(
							'RSD' => 'RSA Socle (Financement sur fonds Conseil général)',
							'RSI' => 'RSA Socle majoré (Financement sur fonds Conseil général)',
						)
					),
					'Dossier' => array(
						'anciennete_dispositif' => array(
							'0_0' => 'Moins de 1 an'
						),
						'fonorg' => array(
							'CAF' => 'CAF',
							'MSA' => 'MSA'
						)
					),
					'Foyer' => array(
						'sitfam' => array(
							'ABA' => 'Disparu (jugement d\'absence)'
						)
					),
					'Serviceinstructeur' => array(
						'id' => array(
							14 => 'Caisse d\'Allocations Familiales'
						)
					),
					'Situationdossierrsa' => array(
						'etatdosrsa' => array(
							'Z' => 'Non défini',
							0 => 'Nouvelle demande en attente de décision CG pour ouverture du droit'
						),
					),
				)
			);

			$result = $this->Allocataires->blocDossier( $params );
			$expected = '<fieldset><legend>Recherche par dossier</legend><div class="input text required"><label for="SearchDossierNumdemrsa">Numéro de dossier RSA</label><input name="data[Search][Dossier][numdemrsa]" maxlength="11" type="text" id="SearchDossierNumdemrsa"/></div><div class="input text"><label for="SearchDossierMatricule">' . __d( 'dossier', 'Dossier.matricule.large' ) . '</label><input name="data[Search][Dossier][matricule]" maxlength="15" type="text" id="SearchDossierMatricule"/></div><div class="input checkbox"><input type="hidden" name="data[Search][Dossier][dernier]" id="SearchDossierDernier_" value="0"/><input type="checkbox" name="data[Search][Dossier][dernier]"  value="1" id="SearchDossierDernier"/><label for="SearchDossierDernier">Uniquement la dernière demande RSA pour un même allocataire</label></div><script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsetOnCheckbox( \'SearchDossierDtdemrsa\', \'SearchDossierDtdemrsa_from_to\', false, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script><div class="input checkbox"><input type="hidden" name="data[Search][Dossier][dtdemrsa]" id="SearchDossierDtdemrsa_" value="0"/><input type="checkbox" name="data[Search][Dossier][dtdemrsa]"  value="1" id="SearchDossierDtdemrsa"/><label for="SearchDossierDtdemrsa">Filtrer par date de demande RSA</label></div><fieldset id="SearchDossierDtdemrsa_from_to"><legend>Date de demande RSA</legend><div class="input date"><label for="SearchDossierDtdemrsaFromDay">Du (inclus)</label><select name="data[Search][Dossier][dtdemrsa_from][day]" id="SearchDossierDtdemrsaFromDay">
'.$options['From']['days'].'
</select>-<select name="data[Search][Dossier][dtdemrsa_from][month]" id="SearchDossierDtdemrsaFromMonth">
'.$options['From']['months'].'
</select>-<select name="data[Search][Dossier][dtdemrsa_from][year]" id="SearchDossierDtdemrsaFromYear">
'.$options['From']['years'].'
</select></div><div class="input date"><label for="SearchDossierDtdemrsaToDay">Au (inclus)</label><select name="data[Search][Dossier][dtdemrsa_to][day]" id="SearchDossierDtdemrsaToDay">
'.$options['To']['days'].'
</select>-<select name="data[Search][Dossier][dtdemrsa_to][month]" id="SearchDossierDtdemrsaToMonth">
'.$options['To']['months'].'
</select>-<select name="data[Search][Dossier][dtdemrsa_to][year]" id="SearchDossierDtdemrsaToYear">
'.$options['To']['years'].'
</select></div></fieldset><div class="input checkbox"><input type="hidden" name="data[Search][Situationdossierrsa][etatdosrsa_choice]" id="SearchSituationdossierrsaEtatdosrsaChoice_" value="0"/><input type="checkbox" name="data[Search][Situationdossierrsa][etatdosrsa_choice]"  value="1" id="SearchSituationdossierrsaEtatdosrsaChoice"/><label for="SearchSituationdossierrsaEtatdosrsaChoice">Filtrer par état du dossier</label></div><fieldset id="SearchSituationdossierrsaEtatdosrsaFieldset"><legend>États du dossier</legend><div class="input select required"><input type="hidden" name="data[Search][Situationdossierrsa][etatdosrsa]" value="" id="SearchSituationdossierrsaEtatdosrsa"/>

<div class="checkbox"><input type="checkbox" name="data[Search][Situationdossierrsa][etatdosrsa][]" value="Z" id="SearchSituationdossierrsaEtatdosrsaZ" /><label for="SearchSituationdossierrsaEtatdosrsaZ">Non défini</label></div>
<div class="checkbox"><input type="checkbox" name="data[Search][Situationdossierrsa][etatdosrsa][]" value="0" id="SearchSituationdossierrsaEtatdosrsa0" /><label for="SearchSituationdossierrsaEtatdosrsa0">Nouvelle demande en attente de décision CG pour ouverture du droit</label></div>
</div></fieldset><script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsetOnCheckbox( \'SearchSituationdossierrsaEtatdosrsaChoice\', \'SearchSituationdossierrsaEtatdosrsaFieldset\', false, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script><div class="input checkbox"><input type="hidden" name="data[Search][Detailcalculdroitrsa][natpf_choice]" id="SearchDetailcalculdroitrsaNatpfChoice_" value="0"/><input type="checkbox" name="data[Search][Detailcalculdroitrsa][natpf_choice]"  value="1" id="SearchDetailcalculdroitrsaNatpfChoice"/><label for="SearchDetailcalculdroitrsaNatpfChoice">Filtrer par nature de prestation</label></div><fieldset id="SearchDetailcalculdroitrsaNatpfFieldset"><legend>Natures de prestation</legend><div class="input select"><input type="hidden" name="data[Search][Detailcalculdroitrsa][natpf]" value="" id="SearchDetailcalculdroitrsaNatpf"/>

<div class="checkbox"><input type="checkbox" name="data[Search][Detailcalculdroitrsa][natpf][]" value="RSD" id="SearchDetailcalculdroitrsaNatpfRSD" /><label for="SearchDetailcalculdroitrsaNatpfRSD">RSA Socle (Financement sur fonds Conseil général)</label></div>
<div class="checkbox"><input type="checkbox" name="data[Search][Detailcalculdroitrsa][natpf][]" value="RSI" id="SearchDetailcalculdroitrsaNatpfRSI" /><label for="SearchDetailcalculdroitrsaNatpfRSI">RSA Socle majoré (Financement sur fonds Conseil général)</label></div>
</div></fieldset><script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsetOnCheckbox( \'SearchDetailcalculdroitrsaNatpfChoice\', \'SearchDetailcalculdroitrsaNatpfFieldset\', false, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script><div class="input select"><label for="SearchDossierAncienneteDispositif">Ancienneté dans le dispositif</label><select name="data[Search][Dossier][anciennete_dispositif]" id="SearchDossierAncienneteDispositif">
<option value=""></option>
<option value="0_0">Moins de 1 an</option>
</select></div><div class="input select"><label for="SearchServiceinstructeurId">Nom du service instructeur</label><select name="data[Search][Serviceinstructeur][id]" id="SearchServiceinstructeurId">
<option value=""></option>
<option value="14">Caisse d&#039;Allocations Familiales</option>
</select></div><div class="input select"><label for="SearchDossierFonorg">Organisme émetteur du dossier</label><select name="data[Search][Dossier][fonorg]" id="SearchDossierFonorg">
<option value=""></option>
<option value="CAF">CAF</option>
<option value="MSA">MSA</option>
</select></div><div class="input select"><label for="SearchFoyerSitfam">Filtrer par situation familiale</label><select name="data[Search][Foyer][sitfam]" id="SearchFoyerSitfam">
<option value=""></option>
<option value="ABA">Disparu (jugement d&#039;absence)</option>
</select></div></fieldset>';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode AllocatairesHelper::blocDossier() en retirant
		 * Dossier.numdemrsa, Dossier.dtdemrsa, Situationdossierrsa.etatdosrsa et
		 * Detailcalculdroitrsa.natpf (car il n'est pas dans les options).
		 */
		public function testBlocDossierSkip() {
			$this->setUpUrl();
			$params = array(
				'options' => array(
					'Situationdossierrsa' => array(
						'etatdosrsa' => array( 'Z' => 'Non défini' )
					)
				),
				'skip' => array(
					'Search.Dossier.numdemrsa',
					'Search.Dossier.dtdemrsa',
					'Search.Situationdossierrsa.etatdosrsa',
				)
			);

			$result = $this->Allocataires->blocDossier( $params );
			$expected = '<fieldset><legend>Recherche par dossier</legend><div class="input text"><label for="SearchDossierMatricule">' . __d( 'dossier', 'Dossier.matricule.large' ) . '</label><input name="data[Search][Dossier][matricule]" maxlength="15" type="text" id="SearchDossierMatricule"/></div><div class="input checkbox"><input type="hidden" name="data[Search][Dossier][dernier]" id="SearchDossierDernier_" value="0"/><input type="checkbox" name="data[Search][Dossier][dernier]"  value="1" id="SearchDossierDernier"/><label for="SearchDossierDernier">Uniquement la dernière demande RSA pour un même allocataire</label></div></fieldset>';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode AllocatairesHelper::blocDossier() en changeant le
		 * préfixe.
		 *
		 * @todo: les traductions ne sont pas faites dans ce cas-là...
		 */
		public function testBlocDossierPrefix() {
			$this->setUpUrl();
			$options = CakeTestSelectOptions::ymdRange( '-1 week', 'now' );

			$params = array(
				'options' => array(
					'Situationdossierrsa' => array(
						'etatdosrsa' => array( 'Z' => 'Non défini' )
					)
				),
				'skip' => array(
					'Search.Dossier.dtdemrsa'
				),
				'prefix' => 'Filter'
			);

			$result = $this->Allocataires->blocDossier( $params );
			$expected = '<fieldset><legend>Recherche par dossier</legend><div class="input text required"><label for="FilterDossierNumdemrsa">Filter.Dossier.numdemrsa</label><input name="data[Filter][Dossier][numdemrsa]" maxlength="11" type="text" id="FilterDossierNumdemrsa"/></div><div class="input text"><label for="FilterDossierMatricule">Filter.Dossier.matricule</label><input name="data[Filter][Dossier][matricule]" maxlength="15" type="text" id="FilterDossierMatricule"/></div><div class="input checkbox"><input type="hidden" name="data[Filter][Dossier][dernier]" id="FilterDossierDernier_" value="0"/><input type="checkbox" name="data[Filter][Dossier][dernier]"  value="1" id="FilterDossierDernier"/><label for="FilterDossierDernier">Filter.Dossier.dernier</label></div><script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsetOnCheckbox( \'FilterDossierDtdemrsa\', \'FilterDossierDtdemrsa_from_to\', false, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script><div class="input checkbox"><input type="hidden" name="data[Filter][Dossier][dtdemrsa]" id="FilterDossierDtdemrsa_" value="0"/><input type="checkbox" name="data[Filter][Dossier][dtdemrsa]"  value="1" id="FilterDossierDtdemrsa"/><label for="FilterDossierDtdemrsa">Filtrer par filter.Dossier.dtdemrsa</label></div><fieldset id="FilterDossierDtdemrsa_from_to"><legend>Filter.Dossier.dtdemrsa</legend><div class="input date"><label for="FilterDossierDtdemrsaFromDay">Du (inclus)</label><select name="data[Filter][Dossier][dtdemrsa_from][day]" id="FilterDossierDtdemrsaFromDay">
'.$options['From']['days'].'
</select>-<select name="data[Filter][Dossier][dtdemrsa_from][month]" id="FilterDossierDtdemrsaFromMonth">
'.$options['From']['months'].'
</select>-<select name="data[Filter][Dossier][dtdemrsa_from][year]" id="FilterDossierDtdemrsaFromYear">
'.$options['From']['years'].'
</select></div><div class="input date"><label for="FilterDossierDtdemrsaToDay">Au (inclus)</label><select name="data[Filter][Dossier][dtdemrsa_to][day]" id="FilterDossierDtdemrsaToDay">
'.$options['To']['days'].'
</select>-<select name="data[Filter][Dossier][dtdemrsa_to][month]" id="FilterDossierDtdemrsaToMonth">
'.$options['To']['months'].'
</select>-<select name="data[Filter][Dossier][dtdemrsa_to][year]" id="FilterDossierDtdemrsaToYear">
'.$options['To']['years'].'
</select></div></fieldset><div class="input checkbox"><input type="hidden" name="data[Filter][Situationdossierrsa][etatdosrsa_choice]" id="FilterSituationdossierrsaEtatdosrsaChoice_" value="0"/><input type="checkbox" name="data[Filter][Situationdossierrsa][etatdosrsa_choice]"  value="1" id="FilterSituationdossierrsaEtatdosrsaChoice"/><label for="FilterSituationdossierrsaEtatdosrsaChoice">Filter.Situationdossierrsa.etatdosrsa_choice</label></div><fieldset id="FilterSituationdossierrsaEtatdosrsaFieldset"><legend>Filter.Situationdossierrsa.etatdosrsa</legend><div class="input select required"><input type="hidden" name="data[Filter][Situationdossierrsa][etatdosrsa]" value="" id="FilterSituationdossierrsaEtatdosrsa"/>

<div class="checkbox"><input type="checkbox" name="data[Filter][Situationdossierrsa][etatdosrsa][]" value="Z" id="FilterSituationdossierrsaEtatdosrsaZ" /><label for="FilterSituationdossierrsaEtatdosrsaZ">Non défini</label></div>
</div></fieldset><script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsetOnCheckbox( \'FilterSituationdossierrsaEtatdosrsaChoice\', \'FilterSituationdossierrsaEtatdosrsaFieldset\', false, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script></fieldset>';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode AllocatairesHelper::blocAdresse()
		 */
		public function testBlocAdresse() {
			$this->setUpUrl();
			Configure::write( 'CG.cantons', true );
			Configure::write( 'Cg.departement', 58 );

			// Utile pour réinitialiser la validation sur Adresse qui est modifié en cas de AllTests
			ClassRegistry::init('Adresse')->validate['nomvoie'] = array();
			ClassRegistry::init('Adresse')->validate['nomcom'] = array('notEmpty');

			$params = array(
				'options' => array(
					'Adresse' => array(
						'numcom' => array( '58000' => 'Nevers' )
					),
					'Canton' => array(
						'canton' => array( 'LA CHARITE SUR LOIRE' => 'LA CHARITE SUR LOIRE' )
					),
					'Sitecov58' => array(
						'id' => array( 1 => 'Bords de Loire' )
					),
				),
				'fieldset' => false
			);

			// 1. Sans le fieldset
			$result = $this->Allocataires->blocAdresse( $params );

			$expected = '<div class="input text"><label for="SearchAdresseNomvoie">Nom de voie de l\'allocataire</label><input name="data[Search][Adresse][nomvoie]" type="text" id="SearchAdresseNomvoie"/></div><div class="input text required"><label for="SearchAdresseNomcom">Commune de l\'allocataire</label><input name="data[Search][Adresse][nomcom]" type="text" id="SearchAdresseNomcom"/></div><div class="input select"><label for="SearchAdresseNumcom">Numéro de commune au sens INSEE</label><select name="data[Search][Adresse][numcom]" id="SearchAdresseNumcom">
<option value=""></option>
<option value="58000">Nevers</option>
</select></div><div class="input select required"><label for="SearchCantonCanton">Canton</label><select name="data[Search][Canton][canton]" id="SearchCantonCanton">
<option value=""></option>
<option value="LA CHARITE SUR LOIRE">LA CHARITE SUR LOIRE</option>
</select></div><div class="input select"><label for="SearchSitecov58Id">Site COV</label><select name="data[Search][Sitecov58][id]" id="SearchSitecov58Id">
<option value=""></option>
<option value="1">Bords de Loire</option>
</select></div>';

			$this->assertEquals( $result, $expected, var_export( $result, true ) );

			// 2. Avec le fieldset
			unset( $params['fieldset'] );
			$result = $this->Allocataires->blocAdresse( $params );

			$expected = '<fieldset><legend>Recherche par adresse</legend>'.$expected.'</fieldset>';

			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode AllocatairesHelper::blocAdresse()
		 */
		public function testBlocAdresseMultipleCheckboxNumcom() {
			Configure::write( 'ConfigurableQuery.common.filters.Adresse.numcom.multiple', true );
			$skip = array(
				'Adresse.nomvoie',
				'Adresse.nomcom'
			);
			Configure::write( 'ConfigurableQuery.Dossiers.search.filters.skip', $skip );

			$this->setUpUrl();
			Configure::write( 'CG.cantons', false );
			Configure::write( 'Cg.departement', 93 );

			$params = array(
				'options' => array(
					'Adresse' => array(
						'numcom' => array(
							93001 => '93001 AUBERVILLIERS',
							93005 => '93005 AULNAY-SOUS-BOIS'
						)
					)
				)
			);

			$result = $this->Allocataires->blocAdresse( $params );
			$expected = '<fieldset><legend>Recherche par adresse</legend><div class="input checkbox"><input type="hidden" name="data[Search][Adresse][numcom_choice]" id="SearchAdresseNumcomChoice_" value="0"/><input type="checkbox" name="data[Search][Adresse][numcom_choice]"  onclick="try { toutCocher( &#039;input[name=\\&#039;data[Search][Adresse][numcom][]\\&#039;]&#039; ); } catch( e ) { console.log( e ); };" value="1" id="SearchAdresseNumcomChoice"/><label for="SearchAdresseNumcomChoice">Filtrer par numéro de commune au sens INSEE</label></div><fieldset id="SearchAdresseNumcomFieldset"><legend>Numéro de commune au sens INSEE</legend><div class="buttons"><button type="button" onclick="try { toutCocher( \'input[name=\\\'data[Search][Adresse][numcom][]\\\']\' ); } catch( e ) { console.log( e ); }; return false;">Tout cocher</button><button type="button" onclick="toutDecocher( \'input[name=\\\'data[Search][Adresse][numcom][]\\\']\' ); return false;">Tout décocher</button></div><div class="input select"><input type="hidden" name="data[Search][Adresse][numcom]" value="" id="SearchAdresseNumcom"/>

<div class="divideInto3Columns"><input type="checkbox" name="data[Search][Adresse][numcom][]" value="93001" id="SearchAdresseNumcom93001" /><label for="SearchAdresseNumcom93001">93001 AUBERVILLIERS</label></div>
<div class="divideInto3Columns"><input type="checkbox" name="data[Search][Adresse][numcom][]" value="93005" id="SearchAdresseNumcom93005" /><label for="SearchAdresseNumcom93005">93005 AULNAY-SOUS-BOIS</label></div>
</div></fieldset><script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsetOnCheckbox( \'SearchAdresseNumcomChoice\', \'SearchAdresseNumcomFieldset\', false, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script></fieldset>';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode AllocatairesHelper::blocAdresse()
		 */
		public function testBlocAdresseMultipleCheckboxNumcomLarger1() {
			// 1. N'afficher la liste multiple que si on a plus d'un élément
			Configure::write(
				'ConfigurableQuery.common.filters.Adresse.numcom',
				array(
					'multiple' => true,
					'multiple_larger_1' => true
				)
			);
			$skip = array(
				'Adresse.nomvoie',
				'Adresse.nomcom'
			);
			Configure::write( 'ConfigurableQuery.Dossiers.search.filters.skip', $skip );

			$this->setUpUrl();
			Configure::write( 'CG.cantons', false );
			Configure::write( 'Cg.departement', 93 );

			$params = array(
				'options' => array(
					'Adresse' => array(
						'numcom' => array(
							93001 => '93001 AUBERVILLIERS'
						)
					)
				)
			);

			$result = $this->Allocataires->blocAdresse( $params );
			$expected = '<fieldset><legend>Recherche par adresse</legend><div class="input select"><label for="SearchAdresseNumcom">Numéro de commune au sens INSEE</label><select name="data[Search][Adresse][numcom]" id="SearchAdresseNumcom">
<option value=""></option>
<option value="93001">93001 AUBERVILLIERS</option>
</select></div></fieldset>';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );

			// 2. Afficher la liste multiple dans tous les cas
			Configure::write(
				'ConfigurableQuery.common.filters.Adresse.numcom',
				array(
					'multiple' => true
				)
			);
			$skip = array(
				'Adresse.nomvoie',
				'Adresse.nomcom'
			);
			Configure::write( 'ConfigurableQuery.Dossiers.search.filters.skip', $skip );

			$this->setUpUrl();
			Configure::write( 'CG.cantons', false );
			Configure::write( 'Cg.departement', 93 );

			$params = array(
				'options' => array(
					'Adresse' => array(
						'numcom' => array(
							93001 => '93001 AUBERVILLIERS'
						)
					)
				)
			);

			$result = $this->Allocataires->blocAdresse( $params );
			$expected = '<fieldset><legend>Recherche par adresse</legend><div class="input checkbox"><input type="hidden" name="data[Search][Adresse][numcom_choice]" id="SearchAdresseNumcomChoice_" value="0"/><input type="checkbox" name="data[Search][Adresse][numcom_choice]"  onclick="try { toutCocher( &#039;input[name=\\&#039;data[Search][Adresse][numcom][]\\&#039;]&#039; ); } catch( e ) { console.log( e ); };" value="1" id="SearchAdresseNumcomChoice"/><label for="SearchAdresseNumcomChoice">Filtrer par numéro de commune au sens INSEE</label></div><fieldset id="SearchAdresseNumcomFieldset"><legend>Numéro de commune au sens INSEE</legend><div class="buttons"><button type="button" onclick="try { toutCocher( \'input[name=\\\'data[Search][Adresse][numcom][]\\\']\' ); } catch( e ) { console.log( e ); }; return false;">Tout cocher</button><button type="button" onclick="toutDecocher( \'input[name=\\\'data[Search][Adresse][numcom][]\\\']\' ); return false;">Tout décocher</button></div><div class="input select"><input type="hidden" name="data[Search][Adresse][numcom]" value="" id="SearchAdresseNumcom"/>

<div class="divideInto3Columns"><input type="checkbox" name="data[Search][Adresse][numcom][]" value="93001" id="SearchAdresseNumcom93001" /><label for="SearchAdresseNumcom93001">93001 AUBERVILLIERS</label></div>
</div></fieldset><script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { try {
	observeDisableFieldsetOnCheckbox( \'SearchAdresseNumcomChoice\', \'SearchAdresseNumcomFieldset\', false, false );;
} catch( e ) {
	console.error( e );
} } );
//]]>
</script></fieldset>';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}


		/**
		 * Test de la méthode AllocatairesHelper::blocAllocataire()
		 */
		public function testBlocAllocataire() {
			$this->setUpUrl();
			$timestamp = strtotime( 'now' );

			$thisYear = date( 'Y', $timestamp );
			$years = CakeTestSelectOptions::years( $thisYear, $thisYear - 120, $thisYear );
			$years = str_replace( ' selected="selected"', '', $years );

			$thisMonth = date( 'm', $timestamp );
			$months = CakeTestSelectOptions::months( $thisMonth );
			$months = str_replace( ' selected="selected"', '', $months );

			$thisDay = date( 'd', $timestamp );
			$days = CakeTestSelectOptions::days( $thisDay );
			$days = str_replace( ' selected="selected"', '', $days );

			// -----------------------------------------------------------------

			$params = array(
				'options' => array(
					'Calculdroitrsa' => array(
						'toppersdrodevorsa' => array(
							'NULL' => 'Non défini',
							1 => 'Oui',
							0 => 'Non'
						)
					),
					'Personne' => array(
						'sexe' => array(
							1 => 'Homme',
							2 => 'Femme'
						),
						'trancheage' => array(
							'0_24' => '- 25 ans',
							'25_34' => '25 - 34 ans',
							'35_44' => '35 - 44 ans',
							'45_54' => '45 - 54 ans',
							'55_999' => '+ 55 ans'
						),
					),
				),
				'fieldset' => false
			);
			
			Configure::write('Cg.departement', 93);
			Configure::delete('Search.Options.enums.Personne.trancheage');
			
			// 1. Sans le fieldset
			$result = $this->Allocataires->blocAllocataire( $params );

			$expected = '<div class="input date required"><label for="SearchPersonneDtnaiDay">Date de naissance</label><select name="data[Search][Personne][dtnai][day]" id="SearchPersonneDtnaiDay">
<option value=""></option>
'.$days.'
</select>-<select name="data[Search][Personne][dtnai][month]" id="SearchPersonneDtnaiMonth">
<option value=""></option>
'.$months.'
</select>-<select name="data[Search][Personne][dtnai][year]" id="SearchPersonneDtnaiYear">
<option value=""></option>
'.$years.'
</select></div><div class="input text required"><label for="SearchPersonneNom">Nom</label><input name="data[Search][Personne][nom]" maxlength="50" type="text" id="SearchPersonneNom"/></div><div class="input text"><label for="SearchPersonneNomnai">Nom de naissance</label><input name="data[Search][Personne][nomnai]" maxlength="50" type="text" id="SearchPersonneNomnai"/></div><div class="input text required"><label for="SearchPersonnePrenom">Prénom</label><input name="data[Search][Personne][prenom]" maxlength="50" type="text" id="SearchPersonnePrenom"/></div><div class="input text"><label for="SearchPersonneNir">NIR</label><input name="data[Search][Personne][nir]" maxlength="15" type="text" id="SearchPersonneNir"/></div><div class="input select"><label for="SearchPersonneSexe">Sexe</label><select name="data[Search][Personne][sexe]" id="SearchPersonneSexe">
<option value=""></option>
<option value="1">Homme</option>
<option value="2">Femme</option>
</select></div><div class="input select"><label for="SearchPersonneTrancheage">Tranche d\'âge</label><select name="data[Search][Personne][trancheage]" id="SearchPersonneTrancheage">
<option value=""></option>
<option value="0_24">- 25 ans</option>
<option value="25_34">25 - 34 ans</option>
<option value="35_44">35 - 44 ans</option>
<option value="45_54">45 - 54 ans</option>
<option value="55_999">+ 55 ans</option>
</select></div><div class="input select"><label for="SearchCalculdroitrsaToppersdrodevorsa">Personne soumise à droits et devoirs ?</label><select name="data[Search][Calculdroitrsa][toppersdrodevorsa]" id="SearchCalculdroitrsaToppersdrodevorsa">
<option value=""></option>
<option value="NULL">Non défini</option>
<option value="1">Oui</option>
<option value="0">Non</option>
</select></div>';
	
			$this->assertEquals( $result, $expected, var_export( $result, true ) );

			// 2. Avec le fieldset
			unset( $params['fieldset'] );
			$result = $this->Allocataires->blocAllocataire( $params );

			$expected = '<fieldset><legend>Recherche par allocataire</legend>'.$expected.'</fieldset>';

			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Allocataires::blocReferentparcours();
		 */
		public function testBlocReferentparcours() {
			$this->setUpUrl();
			Configure::write( 'Cg.departement', 58 );
			$params = array(
				'options' => array(
					'PersonneReferent' => array(
						'structurereferente_id' => array(
							'1' => 'Pôle Emploi'
						),
						'referent_id' => array(
							'1_1' => 'M. Emploi Paul'
						),
					)
				),
				'fieldset' => false
			);

			// 1. Sans le fieldset
			$result = $this->Allocataires->blocReferentparcours( $params );
			$expected = '<div class="input select"><label for="SearchPersonneReferentStructurereferenteId">Structure du parcours</label><select name="data[Search][PersonneReferent][structurereferente_id]" id="SearchPersonneReferentStructurereferenteId">
<option value=""></option>
<option value="1">Pôle Emploi</option>
</select></div><div class="input select required"><label for="SearchPersonneReferentReferentId">Référent du parcours</label><select name="data[Search][PersonneReferent][referent_id]" id="SearchPersonneReferentReferentId">
<option value=""></option>
<option value="1_1">M. Emploi Paul</option>
</select></div><script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() {
				try {
					dependantSelect( \'SearchPersonneReferentReferentId\', \'SearchPersonneReferentStructurereferenteId\' );
				} catch(e) {
					console.log(e);
				}
			} );
//]]>
</script>';

			$this->assertEquals( $result, $expected, var_export( $result, true ) );

			// 2. Avec le fieldset
			unset( $params['fieldset'] );
			$result = $this->Allocataires->blocReferentparcours( $params );
			$expected = '<fieldset><legend>Suivi du parcours</legend>'.$expected.'</fieldset>';

			$this->assertEquals( $result, $expected, var_export( $result, true ) );

			// 3. Avec "skip" sur ces filtres
			$params['skip'] = array(
				'Search.PersonneReferent.structurereferente_id',
				'Search.PersonneReferent.referent_id'
			);
			$result = $this->Allocataires->blocReferentparcours( $params );
			$expected = '';

			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Allocataires::blocPagination();
		 */
		public function testBlocPagination() {
			$this->setUpUrl();
			// 1. Sans le fieldset
			$result = $this->Allocataires->blocPagination( array( 'fieldset' => false ) );

			$expected = '<div class="input checkbox"><input type="hidden" name="data[Search][Pagination][nombre_total]" id="SearchPaginationNombreTotal_" value="0"/><input type="checkbox" name="data[Search][Pagination][nombre_total]"  value="1" id="SearchPaginationNombreTotal"/><label for="SearchPaginationNombreTotal">Obtenir le nombre total de résultats (plus lent)</label></div>';

			$this->assertEquals( $result, $expected, var_export( $result, true ) );

			// 2. Avec le fieldset
			$result = $this->Allocataires->blocPagination();

			$expected = '<fieldset><legend>Comptage des résultats</legend>'.$expected.'</fieldset>';

			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Allocataires::blocPagination();
		 */
		public function testBlocScript() {
			$this->setUpUrl();
			$this->Allocataires->request->data = array( 'Search' => array() );
			$this->Allocataires->request->params = array(
				'plugin' => null,
				'controller' => 'users',
				'action' => 'index',
				'named' => array( 'foo' => 'bar' ),
				'pass' => array( )
			);
			$result = $this->Allocataires->blocScript();

			$expected = '<script type="text/javascript">
//<![CDATA[
document.observe( \'dom:loaded\', function() { $(\'UsersIndexForm\').hide(); } );
//]]>
</script><script type=\'text/javascript\'>document.observe( \'dom:loaded\', function() {
					observeDisableFormOnSubmit( \'UsersIndexForm\' );
				} );</script>';

			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test du bloc allocataire en ne laissant que deux champs dans la
		 * configuration.
		 */
		public function testBlocAllocataireConfigureSkipSome() {
			$skip = array(
				'Personne.dtnai',
				'Personne.nomnai',
				'Personne.nir',
				'Personne.sexe',
				'Personne.trancheage',
				'Calculdroitrsa.toppersdrodevorsa'
			);
			Configure::write( 'ConfigurableQuery.Dossiers.search.filters.skip', $skip );
			$this->setUpUrl();

			$result = $this->Allocataires->blocAllocataire();
			$expected = '<fieldset><legend>Recherche par allocataire</legend><div class="input text required"><label for="SearchPersonneNom">Nom</label><input name="data[Search][Personne][nom]" maxlength="50" type="text" id="SearchPersonneNom"/></div><div class="input text required"><label for="SearchPersonnePrenom">Prénom</label><input name="data[Search][Personne][prenom]" maxlength="50" type="text" id="SearchPersonnePrenom"/></div></fieldset>';

			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test du bloc allocataire en ne laissant aucun champs dans la
		 * configuration.
		 */
		public function testBlocAllocataireConfigureSkipAll() {
			$skip = array(
				'Personne.nom',
				'Personne.prenom',
				'Personne.dtnai',
				'Personne.nomnai',
				'Personne.nir',
				'Personne.sexe',
				'Personne.trancheage',
				'Calculdroitrsa.toppersdrodevorsa'
			);
			Configure::write( 'ConfigurableQuery.Dossiers.search.filters.skip', $skip );
			$this->setUpUrl();

			$result = $this->Allocataires->blocAllocataire();
			$expected = '';

			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test du bloc allocataire en ne laissant que trois champs dans la
		 * configuration et en utilisant une configuration alternative pour
		 * AllocatairesHelper.
		 */
		public function testBlocAllocataireConfigureSkipSomeSettings() {
			$skip = array(
				'Personne.dtnai',
				'Personne.nomnai',
				'Personne.sexe',
				'Personne.trancheage',
				'Calculdroitrsa.toppersdrodevorsa'
			);
			Configure::write( 'BarBaz.Orientsstructs.cohorte_nouvelle.filters.skip', $skip );
			$url = array(
				'controller' => 'orientsstructs',
				'action' => 'cohorte_nouvelle'
			);
			$settings = array(
				'prefix' => 'Foo',
				'configSkipPrefix' => 'BarBaz'
			);
			$this->setUpUrl( $url, $settings );

			$result = $this->Allocataires->blocAllocataire();
			$expected = '<fieldset><legend>Recherche par allocataire</legend><div class="input text required"><label for="FooPersonneNom">Foo.Personne.nom</label><input name="data[Foo][Personne][nom]" maxlength="50" type="text" id="FooPersonneNom"/></div><div class="input text required"><label for="FooPersonnePrenom">Foo.Personne.prenom</label><input name="data[Foo][Personne][prenom]" maxlength="50" type="text" id="FooPersonnePrenom"/></div><div class="input text"><label for="FooPersonneNir">Foo.Personne.nir</label><input name="data[Foo][Personne][nir]" maxlength="15" type="text" id="FooPersonneNir"/></div></fieldset>';

			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}
	}
?>