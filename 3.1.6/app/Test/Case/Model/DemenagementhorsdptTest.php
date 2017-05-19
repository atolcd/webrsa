<?php
	/**
	 * Code source de la classe DemenagementhorsdptTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Demenagementhorsdpt', 'Model' );

	/**
	 * La classe DemenagementhorsdptTest réalise les tests unitaires de la classe Demenagementhorsdpt.
	 *
	 * @package app.Test.Case.Model
	 */
	class DemenagementhorsdptTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Adressefoyer',
			'app.Adresse',
			'app.Calculdroitrsa',
			'app.Commissionep',
			'app.Detaildroitrsa',
			'app.Dossier',
			'app.Dossierep',
			'app.Foyer',
			'app.Orientstruct',
			'app.Passagecommissionep',
			'app.Personne',
			'app.PersonneReferent',
			'app.Prestation',
			'app.Referent',
			'app.Situationdossierrsa',
			'app.Structurereferente',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Demenagementhorsdpt
		 */
		public $Demenagementhorsdpt = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			Configure::write( 'CG.cantons', false );
			Configure::write( 'Cg.departement', 93 );
			$this->Demenagementhorsdpt = ClassRegistry::init( 'Demenagementhorsdpt' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Demenagementhorsdpt );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Demenagementhorsdpt::searchQuery()
		 */
		public function testSearchQuery() {
			$query = $this->Demenagementhorsdpt->searchQuery();
			$result = Hash::combine( $query, 'joins.{n}.alias', 'joins.{n}.type' );

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
				'Adressefoyer2' => 'LEFT OUTER',
				'Adresse2' => 'LEFT OUTER',
				'Adressefoyer3' => 'LEFT OUTER',
				'Adresse3' => 'LEFT OUTER',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Demenagementhorsdpt::search()
		 */
		public function testSearch() {
			$search = array( 'Adresse' => array( 'numcom' => '93000' ) );
			$query = $this->Demenagementhorsdpt->search( $search );
			$result = (array)Hash::get( $query, 'conditions' );

			$expected = array(
				'Adresse.numcom NOT LIKE' => '93%',
				array(
					'OR' => array(
						'Adresse2.numcom LIKE' => '93%',
						'Adresse3.numcom LIKE' => '93%'
					)
				),
				array(
					'OR' => array(
						array(
							'Adresse2.numcom = \'93000\''
						),
						array(
							'Adresse3.numcom = \'93000\''
						)
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Demenagementhorsdpt::options()
		 */
		public function testOptions() {
			$options = $this->Demenagementhorsdpt->options( array( 'allocataire' => false, 'find' => false ) );
			$result = array();
			foreach( $options as $modelName => $modelOptions ) {
				foreach( $modelOptions as $fieldName => $fieldOptions ) {
					$result[] = "{$modelName}.{$fieldName}";
				}
			}

			$expected = array(
				'Adresse2.pays',
				'Adresse2.typeres',
				'Adressefoyer2.rgadr',
				'Adressefoyer2.typeadr',
				'Adresse3.pays',
				'Adresse3.typeres',
				'Adressefoyer3.rgadr',
				'Adressefoyer3.typeadr',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
