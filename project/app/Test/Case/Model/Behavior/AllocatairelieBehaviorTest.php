<?php
	/**
	 * Code source de la classe AllocatairelieBehaviorTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AllocatairelieBehavior', 'Model/Behavior' );
	Configure::write( 'Cg.departement', 93 );

	/**
	 * La classe AllocatairelieBehaviorTest réalise les tests unitaires de la
	 * classe AllocatairelieBehavior.
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class AllocatairelieBehaviorTest extends CakeTestCase
	{
		/**
		 *
		 * @var Cer93
		 */
		public $Cer93 = null;

		/**
		 *
		 * @var Questionnaired2pdv93
		 */
		public $Questionnaired2pdv93 = null;

		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Dossier',
			'app.Foyer',
			'app.Personne',
			'app.Questionnaired2pdv93',
			'app.Contratinsertion',
			'app.Cer93',
			'app.Cer93Sujetcer93',
			'app.Sujetcer93',
		);

		/**
		 * Préparation du test pour le modèle Questionnaired2pdv93.
		 */
		protected function _setupQuestionnaired2pdv93() {
			$this->Questionnaired2pdv93 = ClassRegistry::init( 'Questionnaired2pdv93' );
			$this->Questionnaired2pdv93->Behaviors->attach( 'Allocatairelie' );
		}

		/**
		 * Préparation du test pour le modèle Cer93.
		 */
		protected function _setupCer93() {
			$this->Cer93 = ClassRegistry::init( 'Cer93' );
			$this->Cer93->Behaviors->attach( 'Allocatairelie', array( 'joins' => array( 'Contratinsertion' ) ) );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Cer93, $this->Questionnaired2pdv93 );
			ClassRegistry::flush();
			parent::tearDown();
		}

		/**
		 * Test de la méthode AllocatairelieBehavior::personneId()
		 */
		public function testPersonneId() {
			$this->_setupQuestionnaired2pdv93();

			$result = $this->Questionnaired2pdv93->personneId( 1 );
			$expected = 1;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Questionnaired2pdv93->personneId( 3 );
			$expected = 3;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Questionnaired2pdv93->personneId( 6661 );
			$expected = null;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}


		/**
		 * Test de la méthode AllocatairelieBehavior::personneId() lorsque des
		 * jointures ont été définies dans la configuration.
                 *
                 * @medium
		 */
		public function testPersonneIdJoins() {
			$this->_setupCer93();

			$result = $this->Cer93->personneId( 1 );
			$expected = 2;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Cer93->personneId( 2 );
			$expected = 1;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Cer93->personneId( 6661 );
			$expected = null;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode AllocatairelieBehavior::dossierId()
		 */
		public function testDossierId() {
			$this->_setupQuestionnaired2pdv93();

			$result = $this->Questionnaired2pdv93->dossierId( 1 );
			$expected = 1;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Questionnaired2pdv93->dossierId( 3 );
			$expected = 2;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Questionnaired2pdv93->dossierId( 6661 );
			$expected = null;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode AllocatairelieBehavior::dossierId() lorsque des
		 * jointures ont été définies dans la configuration.
                 *
                 * @medium
		 */
		public function testDossierIdJoins() {
			$this->_setupCer93();

			$result = $this->Cer93->dossierId( 1 );
			$expected = 2;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Cer93->dossierId( 2 );
			$expected = 1;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Cer93->dossierId( 6661 );
			$expected = null;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>