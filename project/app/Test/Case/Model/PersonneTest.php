<?php
	/**
	 * Code source de la classe PersonneTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Personne', 'Model' );

	/**
	 * La classe PersonneTest réalise les tests unitaires de la classe Personne.
	 *
	 * @package app.Test.Case.Model
	 */
	class PersonneTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.ActioncandidatPersonne',
			'app.Apre',
			'app.Bilanparcours66',
			'app.Commissionep',
			'app.Contratinsertion',
			'app.Cov58',
			'app.Cui',
			'app.Dsp',
			'app.DspRev',
			'app.Dossier',
			'app.Dossiercov58',
			'app.Dossierep',
			'app.Dossierpcg66',
			'app.Entretien',
			'app.Ficheprescription93',
			'app.Foyer',
			'app.Memo',
			'app.Orientstruct',
			'app.Passagecommissionep',
			'app.Passagecov58',
			'app.Personne',
			'app.PersonneReferent',
			'app.Prestation',
			'app.Propopdo',
			'app.Questionnaired1pdv93',
			'app.Questionnaired2pdv93',
			'app.Rendezvous',
			'app.RendezvousThematiquerdv',
			'app.Thematiquerdv',
			'app.User',
			// Tabgles liées à la table apres
			'app.Formqualif',
			'app.Formpermfimo',
			'app.Actprof',
			'app.Permisb',
			'app.Amenaglogt',
			'app.Acccreaentr',
			'app.Acqmatprof',
			'app.Locvehicinsert',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Personne
		 */
		public $Personne = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Personne = ClassRegistry::init( 'Personne' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Personne );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Personne::dossierId()
		 */
		public function testDossierId() {
			$result = $this->Personne->dossierId( 1 );
			$expected = 1;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Personne::sqAncienAllocataire()
		 *
		 * @medium
		 */
		public function testSqAncienAllocataire() {
			$result = $this->Personne->WebrsaPersonne->sqAncienAllocataire();
			$expected = '((EXISTS( SELECT "actionscandidats_personnes"."id" AS actionscandidats_personnes__id FROM actionscandidats_personnes AS actionscandidats_personnes   WHERE "actionscandidats_personnes"."personne_id" = "Personne"."id"    )) OR (EXISTS( SELECT "apres"."id" AS apres__id FROM apres AS apres   WHERE "apres"."personne_id" = "Personne"."id"    )) OR (EXISTS( SELECT "bilansparcours66"."id" AS bilansparcours66__id FROM bilansparcours66 AS bilansparcours66   WHERE "bilansparcours66"."personne_id" = "Personne"."id"    )) OR (EXISTS( SELECT "contratsinsertion"."id" AS contratsinsertion__id FROM contratsinsertion AS contratsinsertion   WHERE "contratsinsertion"."personne_id" = "Personne"."id"    )) OR (EXISTS( SELECT "cuis"."id" AS cuis__id FROM cuis AS cuis   WHERE "cuis"."personne_id" = "Personne"."id"    )) OR (EXISTS( SELECT "dsps"."id" AS dsps__id FROM dsps AS dsps   WHERE "dsps"."personne_id" = "Personne"."id"    )) OR (EXISTS( SELECT "dsps_revs"."id" AS dsps_revs__id FROM dsps_revs AS dsps_revs   WHERE "dsps_revs"."personne_id" = "Personne"."id"    )) OR (EXISTS( SELECT "entretiens"."id" AS entretiens__id FROM entretiens AS entretiens   WHERE "entretiens"."personne_id" = "Personne"."id"    )) OR (EXISTS( SELECT "fichesprescriptions93"."id" AS fichesprescriptions93__id FROM fichesprescriptions93 AS fichesprescriptions93   WHERE "fichesprescriptions93"."personne_id" = "Personne"."id"    )) OR (EXISTS( SELECT "memos"."id" AS memos__id FROM memos AS memos   WHERE "memos"."personne_id" = "Personne"."id"    )) OR (EXISTS( SELECT "orientsstructs"."id" AS orientsstructs__id FROM orientsstructs AS orientsstructs   WHERE "orientsstructs"."personne_id" = "Personne"."id"    )) OR (EXISTS( SELECT "personnes_referents"."id" AS personnes_referents__id FROM personnes_referents AS personnes_referents   WHERE "personnes_referents"."personne_id" = "Personne"."id"    )) OR (EXISTS( SELECT "propospdos"."id" AS propospdos__id FROM propospdos AS propospdos   WHERE "propospdos"."personne_id" = "Personne"."id"    )) OR (EXISTS( SELECT "questionnairesd1pdvs93"."id" AS questionnairesd1pdvs93__id FROM questionnairesd1pdvs93 AS questionnairesd1pdvs93   WHERE "questionnairesd1pdvs93"."personne_id" = "Personne"."id"    )) OR (EXISTS( SELECT "questionnairesd2pdvs93"."id" AS questionnairesd2pdvs93__id FROM questionnairesd2pdvs93 AS questionnairesd2pdvs93   WHERE "questionnairesd2pdvs93"."personne_id" = "Personne"."id"    )) OR (EXISTS( SELECT "rendezvous"."id" AS rendezvous__id FROM rendezvous AS rendezvous   WHERE "rendezvous"."personne_id" = "Personne"."id"    )))';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Personne::getEntriesAnciensDossiers()
		 *
		 * @medium
		 */
		public function testGetEntriesAnciensDossiers() {
			$result = $this->Personne->WebrsaPersonne->getEntriesAnciensDossiers( 6, 'Apre', true );
			$expected = array(
				'fields' => array(
					'Personne2.id',
					'Dossier.id',
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Dossier.dtdemrsa',
					'( SELECT COUNT( "apres"."id" ) FROM "apres" AS "apres"   WHERE "apres"."personne_id" = "Personne2"."id"    ) AS "Personne__records"',
				),
				'contain' => false,
				'joins' => array(
					array(
						'table' => '"personnes"',
						'alias' => 'Personne2',
						'type' => 'INNER',
						'conditions' => array(
							'Personne.id <> Personne2.id',
							'Personne.foyer_id <> Personne2.foyer_id',
							'OR' => array(
								array(
									'nir_correct13(Personne.nir)',
									'nir_correct13(Personne2.nir)',
									'SUBSTRING( TRIM( BOTH \' \' FROM Personne.nir ) FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH \' \' FROM Personne2.nir ) FROM 1 FOR 13 )',
									'Personne.dtnai = Personne2.dtnai',
								),
								array(
									'UPPER(Personne.nom) = UPPER(Personne2.nom)',
									'UPPER(Personne.prenom) = UPPER(Personne2.prenom)',
									'Personne.dtnai = Personne2.dtnai',
								),
							),
						),
					),
					array(
						'table' => '"foyers"',
						'alias' => 'Foyer',
						'type' => 'INNER',
						'conditions' => '"Personne2"."foyer_id" = "Foyer"."id"',
					),
					array(
						'table' => '"dossiers"',
						'alias' => 'Dossier',
						'type' => 'INNER',
						'conditions' => '"Foyer"."dossier_id" = "Dossier"."id"',
					),
				),
				'conditions' => array(
					'NOT EXISTS( SELECT prestations.id FROM prestations WHERE prestations.personne_id = Personne2.id )',
					'( SELECT COUNT( "apres"."id" ) FROM "apres" AS "apres"   WHERE "apres"."personne_id" = "Personne2"."id"    ) >' => 0,
					'Personne.id' => 6,
				),
				'order' => array(
					'Dossier.dtdemrsa DESC',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Personne::conditionsRapprochementPersonne1Personne2()
		 */
		public function testConditionsRapprochementPersonne1Personne2() {
			$result = $this->Personne->WebrsaPersonne->conditionsRapprochementPersonne1Personne2( 'Allocataire1', 'Allocataire2' );
			$expected = array(
				'Allocataire1.id <> Allocataire2.id',
				'Allocataire1.foyer_id <> Allocataire2.foyer_id',
				'OR' => array(
					array(
						'nir_correct13(Allocataire1.nir)',
						'nir_correct13(Allocataire2.nir)',
						'SUBSTRING( TRIM( BOTH \' \' FROM Allocataire1.nir ) FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH \' \' FROM Allocataire2.nir ) FROM 1 FOR 13 )',
						'Allocataire1.dtnai = Allocataire2.dtnai',
					),
					array(
						'UPPER(Allocataire1.nom) = UPPER(Allocataire2.nom)',
						'UPPER(Allocataire1.prenom) = UPPER(Allocataire2.prenom)',
						'Allocataire1.dtnai = Allocataire2.dtnai',
					),
				),
			);

			$WebrsaCheck = ClassRegistry::init( 'WebrsaCheck' );
			if( Hash::get( $WebrsaCheck->checkPostgresFuzzystrmatchFunctions(), "success" ) ) {
				$expected['OR'][] = array(
					'difference(Allocataire1.nom, Allocataire2.nom) >= 4',
					'difference(Allocataire1.prenom, Allocataire2.prenom) >= 4',
					'Allocataire1.dtnai = Allocataire2.dtnai'
				);
			}

			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Personne::getAnciensDossiers()
		 *
		 * @medium
		 */
		public function testGetAnciensDossiers() {
			Configure::write( 'Cg.departement', 66 );
			$result = $this->Personne->WebrsaPersonne->getAnciensDossiers( 6, true );
			$expected = array(
				'fields' => array(
					'Personne2.id',
					'Dossier.id',
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Dossier.dtdemrsa',
					'Situationdossierrsa.etatdosrsa',
					'( SELECT COUNT("dossierspcgs66"."id") FROM "dossierspcgs66" AS "dossierspcgs66"   WHERE "dossierspcgs66"."foyer_id" = "Foyer"."id"     ) AS "Foyer__nbdossierspcgs"',
				),
				'contain' => false,
				'joins' => array(
					array(
						'table' => '"personnes"',
						'alias' => 'Personne2',
						'type' => 'INNER',
						'conditions' => array(
							'Personne.id <> Personne2.id',
							'Personne.foyer_id <> Personne2.foyer_id',
							'OR' => array(
								array(
									'nir_correct13(Personne.nir)',
									'nir_correct13(Personne2.nir)',
									'SUBSTRING( TRIM( BOTH \' \' FROM Personne.nir ) FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH \' \' FROM Personne2.nir ) FROM 1 FOR 13 )',
									'Personne.dtnai = Personne2.dtnai',
								),
								array(
									'UPPER(Personne.nom) = UPPER(Personne2.nom)',
									'UPPER(Personne.prenom) = UPPER(Personne2.prenom)',
									'Personne.dtnai = Personne2.dtnai',
								),
							),
						),
					),
					array(
						'table' => '"foyers"',
						'alias' => 'Foyer',
						'type' => 'INNER',
						'conditions' => '"Personne2"."foyer_id" = "Foyer"."id"',
					),
					array(
						'table' => '"dossiers"',
						'alias' => 'Dossier',
						'type' => 'INNER',
						'conditions' => '"Foyer"."dossier_id" = "Dossier"."id"',
					),
					array(
						'table' => '"situationsdossiersrsa"',
						'alias' => 'Situationdossierrsa',
						'type' => 'LEFT OUTER',
						'conditions' => '"Situationdossierrsa"."dossier_id" = "Dossier"."id"',
					),
				),
				'conditions' => array(
					'NOT EXISTS( SELECT prestations.id FROM prestations WHERE prestations.personne_id = Personne2.id )',
					'OR' => array(
						'EXISTS( SELECT "actionscandidats_personnes"."id" AS "actionscandidats_personnes__id" FROM "actionscandidats_personnes" AS "actionscandidats_personnes"   WHERE "actionscandidats_personnes"."personne_id" = "Personne2"."id"    )',
						'EXISTS( SELECT "apres"."id" AS "apres__id" FROM "apres" AS "apres"   WHERE "apres"."personne_id" = "Personne2"."id"    )',
						'EXISTS( SELECT "bilansparcours66"."id" AS "bilansparcours66__id" FROM "bilansparcours66" AS "bilansparcours66"   WHERE "bilansparcours66"."personne_id" = "Personne2"."id"    )',
						'EXISTS( SELECT "contratsinsertion"."id" AS "contratsinsertion__id" FROM "contratsinsertion" AS "contratsinsertion"   WHERE "contratsinsertion"."personne_id" = "Personne2"."id"    )',
						'EXISTS( SELECT "cuis"."id" AS "cuis__id" FROM "cuis" AS "cuis"   WHERE "cuis"."personne_id" = "Personne2"."id"    )',
						'EXISTS( SELECT "dsps"."id" AS "dsps__id" FROM "dsps" AS "dsps"   WHERE "dsps"."personne_id" = "Personne2"."id"    )',
						'EXISTS( SELECT "dsps_revs"."id" AS "dsps_revs__id" FROM "dsps_revs" AS "dsps_revs"   WHERE "dsps_revs"."personne_id" = "Personne2"."id"    )',
						'EXISTS( SELECT "entretiens"."id" AS "entretiens__id" FROM "entretiens" AS "entretiens"   WHERE "entretiens"."personne_id" = "Personne2"."id"    )',
						'EXISTS( SELECT "fichesprescriptions93"."id" AS "fichesprescriptions93__id" FROM "fichesprescriptions93" AS "fichesprescriptions93"   WHERE "fichesprescriptions93"."personne_id" = "Personne2"."id"    )',
						'EXISTS( SELECT "memos"."id" AS "memos__id" FROM "memos" AS "memos"   WHERE "memos"."personne_id" = "Personne2"."id"    )',
						'EXISTS( SELECT "orientsstructs"."id" AS "orientsstructs__id" FROM "orientsstructs" AS "orientsstructs"   WHERE "orientsstructs"."personne_id" = "Personne2"."id"    )',
						'EXISTS( SELECT "personnes_referents"."id" AS "personnes_referents__id" FROM "personnes_referents" AS "personnes_referents"   WHERE "personnes_referents"."personne_id" = "Personne2"."id"    )',
						'EXISTS( SELECT "propospdos"."id" AS "propospdos__id" FROM "propospdos" AS "propospdos"   WHERE "propospdos"."personne_id" = "Personne2"."id"    )',
						'EXISTS( SELECT "questionnairesd1pdvs93"."id" AS "questionnairesd1pdvs93__id" FROM "questionnairesd1pdvs93" AS "questionnairesd1pdvs93"   WHERE "questionnairesd1pdvs93"."personne_id" = "Personne2"."id"    )',
						'EXISTS( SELECT "questionnairesd2pdvs93"."id" AS "questionnairesd2pdvs93__id" FROM "questionnairesd2pdvs93" AS "questionnairesd2pdvs93"   WHERE "questionnairesd2pdvs93"."personne_id" = "Personne2"."id"    )',
						'EXISTS( SELECT "rendezvous"."id" AS "rendezvous__id" FROM "rendezvous" AS "rendezvous"   WHERE "rendezvous"."personne_id" = "Personne2"."id"    )',
					),
					'Personne.id' => 6,
				),
				'order' => array(
					'Dossier.dtdemrsa DESC',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}


		/**
		 * Test de la méthode Personne::prechargement()
		 *
		 * @medium
		 */
		public function testPrechargement() {
			// 1. Sans AncienAllocataire
			Configure::write( 'AncienAllocataire.enabled', false );
			$result = $this->Personne->prechargement();
			$expected = null;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Avec AncienAllocataire
			Configure::write( 'AncienAllocataire.enabled', true );
			$result = $this->Personne->prechargement();
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Personne::completeQueryHasLinkedRecord()
		 */
		public function testCompleteQueryHasLinkedRecord() {
			$result = $this->Personne->WebrsaPersonne->completeQueryHasLinkedRecord(
				array(
					'Contratinsertion',
					'PersonneReferent' => array(
						'conditions' => array(
							'PersonneReferent.dfdesignation IS NULL'
						)
					)
				),
				array(),
				array(
					'Personne' => array(
						'has_contratinsertion' => '1',
						'has_personne_referent' => '0'
					)
				)
			);
			$expected = array(
				'conditions' => array(
					'EXISTS( SELECT "contratsinsertion"."id" AS "contratsinsertion__id" FROM "contratsinsertion" AS "contratsinsertion"   WHERE "contratsinsertion"."personne_id" = "Personne"."id"    )',
					'NOT EXISTS( SELECT "personnes_referents"."id" AS "personnes_referents__id" FROM "personnes_referents" AS "personnes_referents"   WHERE "personnes_referents"."dfdesignation" IS NULL AND "personnes_referents"."personne_id" = "Personne"."id"    )',
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
