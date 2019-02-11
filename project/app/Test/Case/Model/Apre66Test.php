<?php
	/**
	 * Code source de la classe Apre66Test.
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Apre66', 'Model' );

	/**
	 * La classe Apre66Test réalise les tests unitaires de la classe Apre66.
	 *
	 * @package app.Test.Case.Model
	 */
	class Apre66Test extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Apre',
			'app.Correspondancepersonne',
			'app.Dossier',
			'app.Foyer',
			'app.Personne',
			'app.Aideapre66',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Apre66
		 */
		public $Apre66 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Apre66 = ClassRegistry::init( 'Apre66' );
			$this->Correspondancepersonne = ClassRegistry::init( 'Correspondancepersonne' );
			Configure::write( 'Apre.periodeMontantMaxComplementaires', 1 );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Apre66 );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Apre66::getMontantApreEnCours()
		 */
		public function testGetMontantApreEnCours() {
			// @see Fixture/PersonneFixture.php
			$personne_id = 5;
			$nb_entree = 4;
			
			$this->Correspondancepersonne->updateCorrespondance();
			$count = Hash::get( $this->Correspondancepersonne->find('all', array('fields'=>'count(*)')), '0.0.count' );
			$this->assertEqual( $count, $nb_entree, var_export( $count, true ) );
			
			$result = $this->Apre66->WebrsaApre66->getMontantApreEnCours( $personne_id );
			$expected = 2000;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
