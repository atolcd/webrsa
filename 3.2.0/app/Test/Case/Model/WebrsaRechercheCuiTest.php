<?php
	/**
	 * Code source de la classe WebrsaRechercheCuiTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaRechercheCui', 'Model' );

	/**
	 * La classe WebrsaRechercheCuiTest ...
	 *
	 * @package app.Test.Case.Model
	 */
	class WebrsaRechercheCuiTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Cui',
			'app.Personne',
			'app.Foyer',
			'app.Dossier',
			'app.Adresse',
			'app.Calculdroitrsa',
			'app.Entreeromev3',
			'app.Prestation',
			'app.Adressefoyer',
			'app.Situationdossierrsa',
			'app.Detaildroitrsa',
			'app.PersonneReferent',
			'app.Referent',
			'app.Structurereferente',
			'app.Emailcui',
			'app.Partenairecui',
			'app.Adressecui',
			'app.Cui66',
			'app.Accompagnementcui66',
			'app.Decisioncui66',
			'app.Propositioncui66',
			'app.Rupturecui66',
			'app.Suspensioncui66',
			'app.Historiquepositioncui66',
			'app.Canton',
			'app.AdresseCanton',
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			Configure::write( 'Cg.departement', 66 );
			Configure::write( 'CG.cantons', true );
			Configure::write( 'Canton.useAdresseCanton', true );

			parent::setUp();
			$this->WebrsaRechercheCui = ClassRegistry::init( 'WebrsaRechercheCui' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->WebrsaRechercheCui );
			parent::tearDown();
		}

		/**
		 * Test de la méthode WebrsaRechercheCui::searchQuery().
		 */
		public function testSearchQuery() {
			// 1. Jointures par défaut, à partir de Personne
			$result = Hash::combine( $this->WebrsaRechercheCui->searchQuery(), 'joins.{n}.alias', 'joins.{n}.type' );
			$expected = array(
				'Partenairecui' => 'LEFT OUTER',
				'Emailcui' => 'LEFT OUTER',
				'Adressecui' => 'LEFT OUTER',
				'Personne' => 'INNER',
				'Foyer' => 'INNER',
				'Dossier' => 'INNER',
				'Calculdroitrsa' => 'LEFT OUTER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'LEFT OUTER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Cui66' => 'INNER',
				'Accompagnementcui66' => 'LEFT OUTER',
				'Decisioncui66' => 'LEFT OUTER',
				'Propositioncui66' => 'LEFT OUTER',
				'Rupturecui66' => 'LEFT OUTER',
				'Suspensioncui66' => 'LEFT OUTER',
				'Historiquepositioncui66' => 'LEFT OUTER',
				'Canton' => 'LEFT OUTER',
				'Entreeromev3' => 'LEFT OUTER',
				'AdresseCanton' => 'LEFT OUTER'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Jointures par défaut, à partir de Dossier
			$result = Hash::combine( $this->WebrsaRechercheCui->searchQuery( array( 'Emailcui' => 'INNER', 'Dossier' => 'LEFT OUTER' ) ), 'joins.{n}.alias', 'joins.{n}.type' );
			$expected = array(
				'Partenairecui' => 'LEFT OUTER',
				'Emailcui' => 'INNER',
				'Adressecui' => 'LEFT OUTER',
				'Personne' => 'INNER',
				'Foyer' => 'INNER',
				'Dossier' => 'LEFT OUTER',
				'Calculdroitrsa' => 'LEFT OUTER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'LEFT OUTER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Cui66' => 'INNER',
				'Accompagnementcui66' => 'LEFT OUTER',
				'Decisioncui66' => 'LEFT OUTER',
				'Propositioncui66' => 'LEFT OUTER',
				'Rupturecui66' => 'LEFT OUTER',
				'Suspensioncui66' => 'LEFT OUTER',
				'Historiquepositioncui66' => 'LEFT OUTER',
				'Canton' => 'LEFT OUTER',
				'Entreeromev3' => 'LEFT OUTER',
				'AdresseCanton' => 'LEFT OUTER'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}


		/**
		 * Test de la méthode WebrsaRechercheCui::searchConditions() avec un array
		 * en paramètre.
		 *
		 * @medium
		 */
		public function testTestSearchConditionsArray() {
			$result = $this->WebrsaRechercheCui->searchConditions(
				array(
					'conditions' => array(),
				),
				array()
			);
			$expected = array( 'conditions' => array() );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
