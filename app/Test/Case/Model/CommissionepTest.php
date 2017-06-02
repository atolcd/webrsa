<?php
	/**
	 * Code source de la classe CommissionepTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'WebrsaCommissionep', 'Model' );

	/**
	 * Classe fille du modèle Commissionep permettant d'avoir accès aux méthodes
	 * protégées.
	 */
	class CommissionepExtended extends WebrsaCommissionep
	{
		/**
		 * Méthode public permettant d'accéder à la méthode protégée _qdFichesSynthetiques()
		 *
		 * @param array|string $conditions
		 * @param boolean $fiche
		 * @return array
		 */
		public function qdFichesSynthetiques( $conditions, $fiche = false ) {
			return parent::_qdFichesSynthetiques( $conditions, $fiche );
		}
	}

	/**
	 * La classe CommissionepTest réalise les tests unitaires de la classe Commissionep.
	 *
	 * @package app.Test.Case.Model
	 */
	class CommissionepTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Adresse',
			'app.Adressefoyer',
			'app.Commissionep',
			'app.Contratinsertion',
			'app.Decisionnonrespectsanctionep93',
			'app.Dossier',
			'app.Dossierep',
			'app.Dsp',
			'app.DspRev',
			'app.Foyer',
			'app.Historiqueetatpe',
			'app.Informationpe',
			'app.Nonrespectsanctionep93',
			'app.Orientstruct',
			'app.Passagecommissionep',
			'app.Personne',
			'app.PersonneReferent',
			'app.Prestation',
			'app.Referent',
			'app.Regressionorientationep58',
			'app.Relancenonrespectsanctionep93',
			'app.Serviceinstructeur',
			'app.Structurereferente',
			'app.Suiviinstruction',
			'app.Typeorient',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Commissionep
		 */
		public $Commissionep = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Commissionep = ClassRegistry::init( 'CommissionepExtended' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Commissionep );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Commissionep::qdFichesSynthetiques()
		 */
		public function testQdFichesSynthetiques93() {
			Configure::write( 'Cg.departement', 93 );

			$result = $this->Commissionep->qdFichesSynthetiques( array( '1 = 1' ) );
			$result = Hash::combine( $result, 'joins.{n}.alias', 'joins.{n}.type' );
			$expected = array(
				'Passagecommissionep' => 'INNER',
				'Personne' => 'INNER',
				'Foyer' => 'INNER',
				'Dossier' => 'INNER',
				'Suiviinstruction' => 'LEFT OUTER',
				'Serviceinstructeur' => 'LEFT OUTER',
				'Orientstruct' => 'LEFT OUTER',
				'Informationpe' => 'LEFT OUTER',
				'Historiqueetatpe' => 'LEFT OUTER',
				'Adressefoyer' => 'LEFT OUTER',
				'Adresse' => 'LEFT OUTER',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Commissionep::qdFichesSynthetiques()
		 */
		public function testQdFichesSynthetiques93FicheTrue() {
			Configure::write( 'Cg.departement', 93 );

			$result = $this->Commissionep->qdFichesSynthetiques( array( '1 = 1' ), true );
			$result = Hash::combine( $result, 'joins.{n}.alias', 'joins.{n}.type' );
			$expected = array(
				'Passagecommissionep' => 'INNER',
				'Personne' => 'INNER',
				'Foyer' => 'INNER',
				'Dossier' => 'INNER',
				'Suiviinstruction' => 'LEFT OUTER',
				'Serviceinstructeur' => 'LEFT OUTER',
				'Orientstruct' => 'LEFT OUTER',
				'Informationpe' => 'LEFT OUTER',
				'Historiqueetatpe' => 'LEFT OUTER',
				'Adressefoyer' => 'LEFT OUTER',
				'Adresse' => 'LEFT OUTER',
				'Commissionep' => 'INNER',
				'Nonrespectsanctionep93' => 'LEFT OUTER',
				'Relance1' => 'LEFT OUTER',
				'Relance2' => 'LEFT OUTER',
				'Nonrespect1' => 'LEFT OUTER',
				'Dossier1' => 'LEFT OUTER',
				'Passage1' => 'LEFT OUTER',
				'Commission1' => 'LEFT OUTER',
				'Decision1ep' => 'LEFT OUTER',
				'Decision1cg' => 'LEFT OUTER',
				'Nonrespect2' => 'LEFT OUTER',
				'Dossier2' => 'LEFT OUTER',
				'Passage2' => 'LEFT OUTER',
				'Commission2' => 'LEFT OUTER',
				'Decision2ep' => 'LEFT OUTER',
				'Decision2cg' => 'LEFT OUTER',
				'Nonrespect3' => 'LEFT OUTER',
				'Dossier3' => 'LEFT OUTER',
				'Passage3' => 'LEFT OUTER',
				'Commission3' => 'LEFT OUTER',
				'Decision3ep' => 'LEFT OUTER',
				'Decision3cg' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT',
				'Dsp' => 'LEFT OUTER',
				'DspRev' => 'LEFT OUTER',
				'Radiationpe' => 'LEFT OUTER',
				'Dossiercaf' => 'LEFT OUTER'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Commissionep::qdFichesSynthetiques()
		 */
		public function testQdFichesSynthetiques58() {
			Configure::write( 'Cg.departement', 58 );

			$result = $this->Commissionep->qdFichesSynthetiques( array( '1 = 1' ) );
			$result = Hash::combine( $result, 'joins.{n}.alias', 'joins.{n}.type' );
			$expected = array(
				'Passagecommissionep' => 'INNER',
				'Personne' => 'INNER',
				'Foyer' => 'INNER',
				'Dossier' => 'INNER',
				'Suiviinstruction' => 'LEFT OUTER',
				'Serviceinstructeur' => 'LEFT OUTER',
				'Orientstruct' => 'LEFT OUTER',
				'Informationpe' => 'LEFT OUTER',
				'Historiqueetatpe' => 'LEFT OUTER',
				'Adressefoyer' => 'LEFT OUTER',
				'Adresse' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referent' => 'LEFT OUTER',
				'Contratinsertion' => 'LEFT OUTER',
				'Structurereferentecer' => 'LEFT OUTER',
				'Referentcer' => 'LEFT OUTER',
				'Regressionorientationep58' => 'LEFT OUTER',
				'Typeorientpropo' => 'LEFT OUTER',
				'Structurereferentepropo' => 'LEFT OUTER',
				'Referentpropo' => 'LEFT OUTER',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
