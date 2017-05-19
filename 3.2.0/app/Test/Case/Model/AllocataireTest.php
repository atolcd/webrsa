<?php
	/**
	 * Code source de la classe AllocataireTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Allocataire', 'Model' );

	/**
	 * La classe AllocataireTest ...
	 *
	 * @package app.Test.Case.Model
	 */
	class AllocataireTest extends CakeTestCase
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
			'app.Dsp',
			'app.Foyer',
			'app.Personne',
			'app.PersonneReferent',
			'app.Prestation',
			'app.Referent',
			'app.Situationdossierrsa',
			'app.Structurereferente',
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			Configure::write( 'Cg.departement', 93 );
			Configure::write( 'CG.cantons', false );

			parent::setUp();
			$this->Allocataire = ClassRegistry::init( 'Allocataire' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Allocataire );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Allocataire::options().
		 */
		public function testOptions() {
			$result = array_keys( $this->Allocataire->options() );
			sort( $result );

			$expected = array(
				'Adresse',
				'Adressefoyer',
				'Calculdroitrsa',
				'Detailcalculdroitrsa',
				'Detaildroitrsa',
				'Dossier',
				'Foyer',
				'Personne',
				'Prestation',
				'Referentparcours',
				'Situationdossierrsa',
				'Structurereferenteparcours'
			);

			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Allocataire::searchQuery().
		 */
		public function testSearchQuery() {
			// 1. Jointures par défaut, à partir de Personne
			$result = Hash::combine( $this->Allocataire->searchQuery(), 'joins.{n}.alias', 'joins.{n}.type' );
			$expected = array(
				'Calculdroitrsa' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'INNER',
				'Dossier' => 'INNER',
				'Adresse' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'INNER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Jointures par défaut, à partir de Dossier
			$result = Hash::combine( $this->Allocataire->searchQuery( array(), 'Dossier' ), 'joins.{n}.alias', 'joins.{n}.type' );
			$expected = array(
				'Foyer' => 'INNER',
				'Personne' => 'INNER',
				'Calculdroitrsa' => 'LEFT OUTER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'INNER',
				'Adresse' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'INNER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. Jointures par défaut, à partir d'un modèle lié à la personne
			$result = Hash::combine( $this->Allocataire->searchQuery( array(), 'Dsp' ), 'joins.{n}.alias', 'joins.{n}.type' );
			$expected = array(
				'Personne' => 'INNER',
				'Foyer' => 'INNER',
				'Dossier' => 'INNER',
				'Calculdroitrsa' => 'LEFT OUTER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'INNER',
				'Adresse' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'INNER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}



		/**
		 * Test des joins de la méthode Allocataire::searchQuery().
		 */
		public function testSearchQueryJoins() {
			// 1. Sans paramètre
			$result = $this->Allocataire->searchQuery();
			$result = Hash::combine( $result, 'joins.{n}.alias', 'joins.{n}.type' );
			$expected = array(
				'Calculdroitrsa' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'INNER',
				'Dossier' => 'INNER',
				'Adresse' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'INNER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Avec paramètre
			$joins = array(
				'Adressefoyer' => 'LEFT OUTER',
				'Adresse' => 'LEFT OUTER',
				'Prestation' => 'LEFT OUTER',
			);
			$result = $this->Allocataire->searchQuery( $joins );
			$result = Hash::combine( $result, 'joins.{n}.alias', 'joins.{n}.type' );
			$expected = array(
				'Calculdroitrsa' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Prestation' => 'LEFT OUTER',
				'Adressefoyer' => 'LEFT OUTER',
				'Dossier' => 'INNER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'INNER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test des joins de la méthode Allocataire::searchQuery(), suivant le
		 * type de jointure sur Prestation.
		 */
		public function testSearchQueryPrestationJoinType() {
			// 1. INNER JOIN
			$joins = array(
				'Prestation' => 'INNER'
			);
			$result = $this->Allocataire->searchQuery( $joins );
			$expected = array(
				array(
					'table' => '"prestations"',
					'alias' => 'Prestation',
					'type' => 'INNER',
					'conditions' => '"Prestation"."personne_id" = "Personne"."id" AND "Prestation"."natprest" = \'RSA\' AND "Prestation"."rolepers" IN (\'DEM\', \'CJT\')'
				)
			);

			$this->assertEqual( Hash::extract( $result, 'joins.{n}[alias=Prestation]' ), $expected, var_export( Hash::extract( $result, 'joins.{n}[alias=Prestation]' ), true ) );
			$expected = array();
			$this->assertEqual( $result['conditions'], $expected, var_export( $result['conditions'], true ) );

			// 2. LEFT OUTER JOIN
			$joins = array(
				'Prestation' => 'LEFT OUTER'
			);
			$result = $this->Allocataire->searchQuery( $joins );
			$expected = array(
				array(
					'table' => '"prestations"',
					'alias' => 'Prestation',
					'type' => 'LEFT OUTER',
					'conditions' => '"Prestation"."personne_id" = "Personne"."id" AND "Prestation"."natprest" = \'RSA\''
				)
			);
			$this->assertEqual( Hash::extract( $result, 'joins.{n}[alias=Prestation]' ), $expected, var_export( Hash::extract( $result, 'joins.{n}[alias=Prestation]' ), true ) );
			$expected = array(
				'OR' => array(
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					'Prestation.id IS NULL',
				)
			);
			$this->assertEqual( $result['conditions'], $expected, var_export( $result['conditions'], true ) );
		}

		/**
		 * Test des joins de la méthode Allocataire::searchQuery(), suivant le
		 * type de jointure sur Prestation et la valeur de $forceBeneficiaire.
		 */
		public function testSearchQueryPrestationJoinTypeForceBeneficiaire() {
			// 1. INNER JOIN, $forceBeneficiaire true (par défaut)
			$joins = array(
				'Prestation' => 'INNER'
			);
			$result = $this->Allocataire->searchQuery( $joins, 'Personne', true );
			$expected = array(
				array(
					'table' => '"prestations"',
					'alias' => 'Prestation',
					'type' => 'INNER',
					'conditions' => '"Prestation"."personne_id" = "Personne"."id" AND "Prestation"."natprest" = \'RSA\' AND "Prestation"."rolepers" IN (\'DEM\', \'CJT\')'
				)
			);

			$this->assertEqual( Hash::extract( $result, 'joins.{n}[alias=Prestation]' ), $expected, var_export( Hash::extract( $result, 'joins.{n}[alias=Prestation]' ), true ) );
			$expected = array();
			$this->assertEqual( $result['conditions'], $expected, var_export( $result['conditions'], true ) );

			// 2. INNER JOIN, $forceBeneficiaire false
			$joins = array(
				'Prestation' => 'INNER'
			);
			$result = $this->Allocataire->searchQuery( $joins, 'Personne', false );
			$expected = array(
				array(
					'table' => '"prestations"',
					'alias' => 'Prestation',
					'type' => 'INNER',
					'conditions' => '"Prestation"."personne_id" = "Personne"."id" AND "Prestation"."natprest" = \'RSA\''
				)
			);

			$this->assertEqual( Hash::extract( $result, 'joins.{n}[alias=Prestation]' ), $expected, var_export( Hash::extract( $result, 'joins.{n}[alias=Prestation]' ), true ) );
			$expected = array();
			$this->assertEqual( $result['conditions'], $expected, var_export( $result['conditions'], true ) );

			// 3. LEFT OUTER JOIN, $forceBeneficiaire true (par défaut)
			$joins = array(
				'Prestation' => 'LEFT OUTER'
			);
			$result = $this->Allocataire->searchQuery( $joins, 'Personne', true );
			$expected = array(
				array(
					'table' => '"prestations"',
					'alias' => 'Prestation',
					'type' => 'LEFT OUTER',
					'conditions' => '"Prestation"."personne_id" = "Personne"."id" AND "Prestation"."natprest" = \'RSA\''
				)
			);
			$this->assertEqual( Hash::extract( $result, 'joins.{n}[alias=Prestation]' ), $expected, var_export( Hash::extract( $result, 'joins.{n}[alias=Prestation]' ), true ) );
			$expected = array(
				'OR' => array(
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					'Prestation.id IS NULL',
				)
			);
			$this->assertEqual( $result['conditions'], $expected, var_export( $result['conditions'], true ) );

			// 4. LEFT OUTER JOIN, $forceBeneficiaire false
			$joins = array(
				'Prestation' => 'LEFT OUTER'
			);
			$result = $this->Allocataire->searchQuery( $joins, 'Personne', false );
			$expected = array(
				array(
					'table' => '"prestations"',
					'alias' => 'Prestation',
					'type' => 'LEFT OUTER',
					'conditions' => '"Prestation"."personne_id" = "Personne"."id" AND "Prestation"."natprest" = \'RSA\''
				)
			);
			$this->assertEqual( Hash::extract( $result, 'joins.{n}[alias=Prestation]' ), $expected, var_export( Hash::extract( $result, 'joins.{n}[alias=Prestation]' ), true ) );
			$expected = array();
			$this->assertEqual( $result['conditions'], $expected, var_export( $result['conditions'], true ) );
		}

		/**
		 * Test de la méthode Allocataire::search().
		 *
		 * @medium
		 */
		public function testSearch() {
			$query = $this->Allocataire->search();
			$query['fields'] = array(
				'Dossier.id',
				'Personne.id',
				'Personne.nom_complet',
			);

			$result = ClassRegistry::init( 'Personne' )->find( 'first', $query );
			$expected = array(
				'Dossier' => array(
					'id' => 1,
				),
				'Personne' => array(
					'id' => 1,
					'nom_complet' => 'MR BUFFIN CHRISTIAN',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Allocataire::testSearchConditions() avec une chaîne
		 * de caractères en paramètre.
		 *
		 * @medium
		 */
		public function testTestSearchConditionsString() {
			// 1. Sans réellement de condition supplémentaire
			$result = $this->Allocataire->testSearchConditions( '1 = 1' );
			unset( $result['sql'] );
			$expected = array (
				'success' => true,
				'message' => null,
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Avec une condition devant retourner un message d'erreur
			$result = $this->Allocataire->testSearchConditions( 'Foo' );
			unset( $result['sql'] );
			$expected = array (
				'success' => false,
				'message' => preg_match( '/^SQLSTATE\[42703\]: Undefined column: 7.*foo/', $result['message'] ) ? $result['message'] : null,
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. Avec une condition qui doit passer
			$result = $this->Allocataire->testSearchConditions( 'Dossier.id = 6' );
			unset( $result['sql'] );
			$expected = array (
				'success' => true,
				'message' => null,
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Allocataire::testSearchConditions() avec un array
		 * en paramètre.
		 *
		 * @medium
		 */
		public function testTestSearchConditionsArray() {
			$result = $this->Allocataire->testSearchConditions( array( 'Foo' => 6 ) );
			unset( $result['sql'] );
			$expected = array (
				'success' => false,
				'message' => preg_match( '/^SQLSTATE\[42703\]: Undefined column: 7.*Foo/', $result['message'] ) ? $result['message'] : null,
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = $this->Allocataire->testSearchConditions( array( 'Dossier.id' => '6' ) );
			unset( $result['sql'] );
			$expected = array (
				'success' => true,
				'message' => null,
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Allocataire::prechargement().
		 *
		 * @medium
		 */
		public function testPrechargement() {
			$result = $this->Allocataire->prechargement();
			$this->assertTrue( $result );
		}
	}
?>
