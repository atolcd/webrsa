<?php
	/**
	 * Code source de la classe GestiondoublonTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Gestiondoublon', 'Model' );

	/**
	 * La classe GestiondoublonTest ...
	 *
	 * @package app.Test.Case.Model
	 */
	class GestiondoublonTest extends CakeTestCase
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
			'app.Situationdossierrsa',
			'app.Structurereferente',
			'app.User',
		);

		public $emptySearch = array(
			'joins' => array(
				0 => array(
					'table' => '"adressesfoyers"',
					'alias' => 'Adressefoyer',
					'type' => 'LEFT OUTER',
					'conditions' => '"Adressefoyer"."foyer_id" = "Foyer"."id" AND (("Adressefoyer"."id" IS NULL) OR ("Adressefoyer"."id" IN ( SELECT "adressesfoyers"."id" AS adressesfoyers__id FROM adressesfoyers AS adressesfoyers   WHERE "adressesfoyers"."foyer_id" = "Foyer"."id" AND "adressesfoyers"."rgadr" = \'01\'   ORDER BY "adressesfoyers"."dtemm" DESC  LIMIT 1 )))',
				),
				1 => array(
					'table' => '"dossiers"',
					'alias' => 'Dossier',
					'type' => 'INNER',
					'conditions' => '"Foyer"."dossier_id" = "Dossier"."id"',
				),
				2 => array(
					'table' => '"personnes"',
					'alias' => 'Personne',
					'type' => 'INNER',
					'conditions' => '"Personne"."foyer_id" = "Foyer"."id"',
				),
				3 => array(
					'table' => '"adresses"',
					'alias' => 'Adresse',
					'type' => 'LEFT OUTER',
					'conditions' => '"Adressefoyer"."adresse_id" = "Adresse"."id"',
				),
				4 => array(
					'table' => '"situationsdossiersrsa"',
					'alias' => 'Situationdossierrsa',
					'type' => 'LEFT OUTER',
					'conditions' => '"Situationdossierrsa"."dossier_id" = "Dossier"."id"',
				),
				5 => array(
					'table' => '"prestations"',
					'alias' => 'Prestation',
					'type' => 'LEFT OUTER',
					'conditions' => '"Prestation"."personne_id" = "Personne"."id" AND "Prestation"."natprest" = \'RSA\'',
				),
				6 => array(
					'table' => '"personnes"',
					'alias' => 'Demandeur',
					'type' => 'LEFT OUTER',
					'conditions' => '"Demandeur"."foyer_id" = "Foyer"."id" AND (("Demandeur"."id" IN ( SELECT "prestations"."personne_id" FROM prestations WHERE "prestations"."personne_id" = "Demandeur"."id" AND "prestations"."natprest" = \'RSA\' AND "prestations"."rolepers" = \'DEM\' ORDER BY "prestations"."personne_id" ASC LIMIT 1 )) OR ("Demandeur"."id" IS NULL))',
				),
				7 => array(
					'table' => 'personnes',
					'alias' => 'p2',
					'type' => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						0 => 'Personne.id <> p2.id',
						1 => 'Personne.foyer_id <> p2.foyer_id',
						'OR' => array(
							0 => array(
								0 => 'nir_correct13(Personne.nir)',
								1 => 'nir_correct13(p2.nir)',
								2 => 'SUBSTRING( TRIM( BOTH \' \' FROM Personne.nir ) FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH \' \' FROM p2.nir ) FROM 1 FOR 13 )',
								3 => 'Personne.dtnai = p2.dtnai',
							),
							1 => array(
								0 => 'UPPER(Personne.nom) = UPPER(p2.nom)',
								1 => 'UPPER(Personne.prenom) = UPPER(p2.prenom)',
								2 => 'Personne.dtnai = p2.dtnai',
							),
						),
					),
				),
				8 => array(
					'table' => 'calculsdroitsrsa',
					'alias' => 'Calculdroitrsa',
					'type' => 'LEFT',
					'conditions' => array(
						0 => 'Calculdroitrsa.personne_id = Demandeur.id'
					)
				),
				9 => array(
					'table' => 'foyers',
					'alias' => 'Foyer2',
					'type' => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						0 => 'p2.foyer_id = Foyer2.id',
					),
				),
				10 => array(
					'table' => '"adressesfoyers"',
					'alias' => 'Adressefoyer2',
					'type' => 'LEFT OUTER',
					'conditions' => '"Adressefoyer2"."foyer_id" = "Foyer2"."id" AND (("Adressefoyer2"."id" IS NULL) OR ("Adressefoyer2"."id" IN ( SELECT "adressesfoyers"."id" AS adressesfoyers__id FROM adressesfoyers AS adressesfoyers   WHERE "adressesfoyers"."foyer_id" = "Foyer2"."id" AND "adressesfoyers"."rgadr" = \'01\'   ORDER BY "adressesfoyers"."dtemm" DESC  LIMIT 1 )))'
				),
				11 => array(
					'table' => '"adresses"',
					'alias' => 'Adresse2',
					'type' => 'LEFT OUTER',
					'conditions' => '"Adressefoyer2"."adresse_id" = "Adresse2"."id"',
				),
				12 => array(
					'table' => 'dossiers',
					'alias' => 'Dossier2',
					'type' => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						0 => 'Foyer2.dossier_id = Dossier2.id',
						1 => 'Dossier2.id <> Dossier.id',
					),
				),
				13 => array(
					'table' => 'situationsdossiersrsa',
					'alias' => 'Situationdossierrsa2',
					'type' => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						0 => 'Situationdossierrsa2.dossier_id = Dossier2.id',
					),
				),
				14 => array(
					'table' => '"personnes"',
					'alias' => 'Demandeur2',
					'type' => 'LEFT OUTER',
					'conditions' => '"Demandeur2"."foyer_id" = "Foyer"."id" AND (("Demandeur2"."id" IN ( SELECT "prestations"."personne_id" FROM prestations WHERE "prestations"."personne_id" = "Demandeur2"."id" AND "prestations"."natprest" = \'RSA\' AND "prestations"."rolepers" = \'DEM\' ORDER BY "prestations"."personne_id" ASC LIMIT 1 )) OR ("Demandeur2"."id" IS NULL))',
				),
			),
			'conditions' => array(
				0 => array(
					'OR' => array(
						0 => 'Prestation.id IS NULL',
						'Prestation.rolepers' => array(
							0 => 'DEM',
							1 => 'CJT',
						),
					),
				),
				'Situationdossierrsa2.etatdosrsa' => array(
					0 => 'Z',
				),
			),
			'contain' => false,
			'order' => array(
				0 => 'Demandeur.nom',
				1 => 'Demandeur.prenom',
				2 => 'Dossier.matricule',
				3 => 'Dossier.dtdemrsa DESC',
				4 => 'Dossier.id',
			),
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Gestiondoublon = ClassRegistry::init( 'Gestiondoublon' );
			Configure::write('Gestionsdoublons.index.useTag', false);

			$WebrsaCheck = ClassRegistry::init( 'WebrsaCheck' );
			if( Hash::get( $WebrsaCheck->checkPostgresPgtrgmFunctions(), "success" ) ) {
				$this->emptySearch['joins'][7]['conditions']['OR'][] = array(
					'similarity(Allocataire1.nom, Allocataire2.nom) >= 0.3',
					'similarity(Allocataire1.prenom, Allocataire2.prenom) >= 0.3',
					'Allocataire1.dtnai = Allocataire2.dtnai'
				);
			}
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Gestiondoublon );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Gestiondoublon::searchComplexes().
		 *
		 * @medium
		 */
		public function testSearchComplexes() {
			$result = $this->Gestiondoublon->searchComplexes();
			unset( $result['fields'], $result['group'] );

			$expected = $this->emptySearch;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
