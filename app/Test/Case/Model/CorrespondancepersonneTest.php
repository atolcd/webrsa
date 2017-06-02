<?php
	/**
	 * Code source de la classe CorrespondancepersonneTest.
	 *
	 * @package app.Test.Case.Model
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/Test/CakePHP Model Test Case.php.
	 */
	App::uses( 'Correspondancepersonne', 'Model' );

	/**
	 * La classe CorrespondancepersonneTest réalise les tests unitaires de la classe Correspondancepersonne.
	 *
	 * @package app.Test.Case.Model
	 */
	class CorrespondancepersonneTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Correspondancepersonne',
			'app.Dossier',
			'app.Foyer',
			'app.Personne',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Correspondancepersonne
		 */
		public $Correspondancepersonne = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Correspondancepersonne = ClassRegistry::init( 'Correspondancepersonne' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Correspondancepersonne );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Correspondancepersonne::updateCorrespondance()
		 */
		public function testUpdateCorrespondance() {
			$nb_entree = 4;
			
			$result = $this->Correspondancepersonne->updateCorrespondance();
			$expected = array (
				array (
					'personne1_id' => 4,
					'personne2_id' => 5,
					'anomalie' => false,
				),
				array (
					'personne1_id' => 5,
					'personne2_id' => 4,
					'anomalie' => false,
				),
				array (
					'personne1_id' => 6,
					'personne2_id' => 7,
					'anomalie' => true, // Personne1.dtnai != Personne2.dtnai
				),
				array (
					'personne1_id' => 7,
					'personne2_id' => 6,
					'anomalie' => true,
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
			
			$count = Hash::get( $this->Correspondancepersonne->find('all', array('fields'=>'count(*)')), '0.0.count' );
			$this->assertEqual( $count, $nb_entree, var_export( $count, true ) );
		}

		/**
		 * Test de la méthode Correspondancepersonne::updateByPersonneId()
		 */
		public function testUpdateByPersonneId() {
			$personne_id = 5;
			$nb_entree = 4;
			
			// On rempli la table (pour avoir {$nb_entree} rows)
			$this->Correspondancepersonne->updateCorrespondance();
			
			// On supprime et refait les relations selon $personne_id
			$result = $this->Correspondancepersonne->updateByPersonneId( $personne_id );
			$expected = array (
				array (
					'personne1_id' => 4,
					'personne2_id' => 5,
					'anomalie' => false,
				),
				array (
					'personne1_id' => 5,
					'personne2_id' => 4,
					'anomalie' => false,
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
			
			$count = Hash::get( $this->Correspondancepersonne->find('all', array('fields'=>'count(*)')), '0.0.count' );
			$this->assertEqual( $count, $nb_entree, var_export( $count, true ) );
		}		
	}
?>
