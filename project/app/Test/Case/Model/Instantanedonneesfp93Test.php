<?php
	/**
	 * Code source de la classe Instantanedonneesfp93Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Instantanedonneesfp93', 'Model' );

	/**
	 * La classe Instantanedonneesfp93Test réalise les tests unitaires de la classe Instantanedonneesfp93.
	 *
	 * @package app.Test.Case.Model
	 */
	class Instantanedonneesfp93Test extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Adresse',
			'app.Adressefoyer',
			'app.Cer93',
			'app.Cer93Sujetcer93',
			'app.Calculdroitrsa',
			'app.Contratinsertion',
			'app.Dossier',
			'app.Dsp',
			'app.DspRev',
			'app.Detailcalculdroitrsa',
			'app.Detaildroitrsa',
			'app.Ficheprescription93',
			'app.Foyer',
			'app.Historiqueetatpe',
			'app.Informationpe',
			'app.Instantanedonneesfp93',
			'app.Personne',
			'app.Prestation',
			'app.Situationdossierrsa',
			'app.Sujetcer93',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Instantanedonneesfp93
		 */
		public $Instantanedonneesfp93 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Instantanedonneesfp93 = ClassRegistry::init( 'Instantanedonneesfp93' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Instantanedonneesfp93 );
			parent::tearDown();
		}


		/**
		 * Test de la méthode Instantanedonneesfp93::getVirtualFieldBenefNatpf()
		 */
		public function testGetVirtualFieldBenefNatpf() {
			$result = $this->Instantanedonneesfp93->getVirtualFieldBenefNatpf();
			$expected = '( CASE WHEN ( "Instantanedonneesfp93"."benef_natpf_socle" = \'1\' AND "Instantanedonneesfp93"."benef_natpf_activite" = \'1\' AND "Instantanedonneesfp93"."benef_natpf_majore" = \'1\' ) THEN \'socle_majore_activite\'
WHEN ( "Instantanedonneesfp93"."benef_natpf_socle" = \'1\' AND "Instantanedonneesfp93"."benef_natpf_activite" = \'1\' AND "Instantanedonneesfp93"."benef_natpf_majore" = \'0\' ) THEN \'socle_activite\'
WHEN ( "Instantanedonneesfp93"."benef_natpf_socle" = \'1\' AND "Instantanedonneesfp93"."benef_natpf_activite" = \'0\' AND "Instantanedonneesfp93"."benef_natpf_majore" = \'1\' ) THEN \'socle_majore\'
WHEN ( "Instantanedonneesfp93"."benef_natpf_socle" = \'1\' AND "Instantanedonneesfp93"."benef_natpf_activite" = \'0\' AND "Instantanedonneesfp93"."benef_natpf_majore" = \'0\' ) THEN \'socle\'
WHEN ( "Instantanedonneesfp93"."benef_natpf_socle" = \'0\' AND "Instantanedonneesfp93"."benef_natpf_activite" = \'1\' AND "Instantanedonneesfp93"."benef_natpf_majore" = \'1\' ) THEN \'activite_majore\'
WHEN ( "Instantanedonneesfp93"."benef_natpf_socle" = \'0\' AND "Instantanedonneesfp93"."benef_natpf_activite" = \'1\' AND "Instantanedonneesfp93"."benef_natpf_majore" = \'0\' ) THEN \'activite\' ELSE \'NC\' END )';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Instantanedonneesfp93::getBenefNatpf()
		 */
		public function testGetBenefNatpf() {
			$items = array(
				array( 'expected' => 'socle_majore_activite', 'conditions' => array( 'benef_natpf_socle' => '1', 'benef_natpf_activite' => '1', 'benef_natpf_majore' => '1' ) ),
				array( 'expected' => 'socle_activite', 'conditions' => array( 'benef_natpf_socle' => '1', 'benef_natpf_activite' => '1', 'benef_natpf_majore' => '0' ) ),
				array( 'expected' => 'socle_majore', 'conditions' => array( 'benef_natpf_socle' => '1', 'benef_natpf_activite' => '0', 'benef_natpf_majore' => '1' ) ),
				array( 'expected' => 'socle', 'conditions' => array( 'benef_natpf_socle' => '1', 'benef_natpf_activite' => '0', 'benef_natpf_majore' => '0' ) ),
				array( 'expected' => 'activite_majore', 'conditions' => array( 'benef_natpf_socle' => '0', 'benef_natpf_activite' => '1', 'benef_natpf_majore' => '1' ) ),
				array( 'expected' => 'activite', 'conditions' => array( 'benef_natpf_socle' => '0', 'benef_natpf_activite' => '1', 'benef_natpf_majore' => '0' ) ),
				array( 'expected' => 'NC', 'conditions' => array( 'benef_natpf_socle' => '0', 'benef_natpf_activite' => '0', 'benef_natpf_majore' => '1' ) ),
				array( 'expected' => 'NC', 'conditions' => array( 'benef_natpf_socle' => '0', 'benef_natpf_activite' => '0', 'benef_natpf_majore' => '0' ) ),
				array( 'expected' => 'NC', 'conditions' => array( 'benef_natpf_socle' => null, 'benef_natpf_activite' => null, 'benef_natpf_majore' => null ) ),
			);

			foreach( $items as $item ) {
				$result = $this->Instantanedonneesfp93->getBenefNatpf( array( 'Instantanedonneesfp93' => $item['conditions'] ) );
				$this->assertEqual( $result, $item['expected'], var_export( $result, true ) );
			}
		}

		/**
		 * Test de la méthode Instantanedonneesfp93::enums()
		 */
		public function testEnums() {
			$result = $this->Instantanedonneesfp93->enums();
			$expected = array(
				'Instantanedonneesfp93' => array(
					'benef_inscritpe' => array(
						0 => 'Non',
						1 => 'Oui',
					),
					'benef_natpf_socle' => array(
						0 => 'ENUM::BENEF_NATPF_SOCLE::0',
						1 => 'ENUM::BENEF_NATPF_SOCLE::1',
					),
					'benef_natpf_majore' => array(
						0 => 'ENUM::BENEF_NATPF_MAJORE::0',
						1 => 'ENUM::BENEF_NATPF_MAJORE::1',
					),
					'benef_natpf_activite' => array(
						0 => 'ENUM::BENEF_NATPF_ACTIVITE::0',
						1 => 'ENUM::BENEF_NATPF_ACTIVITE::1',
					),
					'benef_nivetu' => array(
						1201 => 'Niveau I/II : sorties avec un diplôme de niveau supérieur à bac+2 (licence, maîtrise, master, dea, dess, doctorat, diplôme de grande école)',
						1202 => 'Niveau III : sorties avec un diplôme de niveau Bac + 2 ans (DUT, BTS, DEUG, écoles des formations sanitaires ou sociales, etc.)',
						1203 => 'Niveau IV : sorties des classes de terminale de l\'enseignement secondaire (avec ou sans le baccalauréat). Abandons des études supérieures sans diplôme',
						1204 => 'Niveau V : sorties de dernière année de CAP, BEP ou équivalent avec ou sans diplôme. Abandons des études en seconde ou en première',
						1205 => 'Niveau Vbis : sorties de 3ème générale, de 4ème et 3ème technologiques et des classes du second cycle court (notamment CAP, BEP) avant l\'année terminale',
						1206 => 'Niveau VI : sorties du 1er cycle de l\'enseignement secondaire (6ème, 5ème, 4ème) et des formations préprofessionnelles en un an',
						1207 => 'Niveau VII : jamais scolarisé',
					),
					'benef_dip_ce' => array(
						0 => 'Hors Union européenne',
						1 => 'Union européenne',
					),
					'benef_etatdosrsa' => array(
						'Z' => 'Non défini',
						0 => 'Nouvelle demande en attente de décision CG pour ouverture du droit',
						1 => 'Droit refusé',
						2 => 'Droit ouvert et versable',
						3 => 'Droit ouvert et suspendu (le montant du droit est calculable, mais l\'existence du droit est remis en cause)',
						4 => 'Droit ouvert mais versement suspendu (le montant du droit n\'est pas calculable)',
						5 => 'Droit clos',
						6 => 'Droit clos sur mois antérieur ayant eu un contrôle dans le mois de référence pour une période antérieure',
					),
					'benef_toppersdrodevorsa' => array(
						0 => 'Non',
						1 => 'Oui',
					),
					'benef_positioncer' => array(
						'validationpdv' => 'En cours de validation PDV',
						'validationcg' => 'En cours de  validation par le CG',
						'valide' => 'Validé',
						'aucun' => 'Aucun contrat',
					),
					'benef_natpf' => array(
						'socle_majore_activite' => 'RSA socle + activité majoré',
						'socle_activite' => 'RSA socle + activité',
						'socle_majore' => 'RSA socle majoré',
						'socle' => 'RSA socle',
						'activite_majore' => 'RSA activité majoré',
						'activite' => 'RSA activité',
						'NC' => 'Non défini',
					),
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Instantanedonneesfp93::getInstantane()
		 *
		 * @medium
		 */
		public function testGetInstantane() {
			$result = $this->Instantanedonneesfp93->getInstantane( 1 );
			$expected = array(
				'Instantanedonneesfp93' => array(
					'benef_qual' => 'MR',
					'benef_nom' => 'BUFFIN',
					'benef_prenom' => 'CHRISTIAN',
					'benef_dtnai' => '1979-01-24',
					'benef_tel_fixe' => NULL,
					'benef_tel_port' => NULL,
					'benef_email' => NULL,
					'benef_numvoie' => '66',
					'benef_nomvoie' => 'DE LA REPUBLIQUE',
					'benef_complideadr' => NULL,
					'benef_compladr' => NULL,
					'benef_codepos' => '93300',
					'benef_matricule' => '123456700000000',
					'benef_natpf_activite' => '0',
					'benef_natpf_majore' => '0',
					'benef_natpf_socle' => '0',
					'benef_etatdosrsa' => '2',
					'benef_toppersdrodevorsa' => '1',
					'benef_dd_ci' => '2011-03-01',
					'benef_df_ci' => '2011-05-31',
					'benef_positioncer' => 'validationpdv',
					'benef_identifiantpe' => '0609065370Y',
					'benef_inscritpe' => '1',
					'benef_nivetu' => '1202',
					'benef_natpf' => 'NC',
					'benef_libtypevoie' => 'AVENUE',
					'benef_numcom' => '93001',
					'benef_nomcom' => 'AUBERVILLIERS',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
