<?php
	/**
	 * Code source de la classe DependenciesBehaviorTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'DependenciesBehavior', 'Model/Behavior' );

	/**
	 * La classe DependenciesBehaviorTest ...
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class DependenciesBehaviorTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Orientstruct',
			'app.Referent',
			'app.Structurereferente',
			'app.Typeorient',
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			Configure::write( 'Cg.departement', 66 );
			$this->Orientstruct = ClassRegistry::init( 'Orientstruct' );
			$this->Orientstruct->Behaviors->attach( 'Dependencies' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Orientstruct );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Orientstruct::dependentForeignKeys()
		 */
		public function testDependentForeignKeys() {
			$data = array(
				'Orientstruct' => array(
					'typeorient_id' => 1,
					'structurereferente_id' => 1,
					'referent_id' => 1,
					'statut_orient' => 'Orienté',
				)
			);
			$this->Orientstruct->create( $data );

			// 0. mauvais argument
			$result = $this->Orientstruct->dependentForeignKeys(
				null,
				'Structurereferente',
				'Typeorient'
			);
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 1. Existe au premier niveau, dépendance OK
			$result = $this->Orientstruct->dependentForeignKeys(
				array( 'structurereferente_id' => $data['Orientstruct']['structurereferente_id'] ),
				'Structurereferente',
				'Typeorient'
			);
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Existe au second niveau, dépendance OK
			$result = $this->Orientstruct->dependentForeignKeys(
				array( 'referent_id' => $data['Orientstruct']['referent_id'] ),
				'Referent',
				'Structurereferente'
			);
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. N'existe pas au premier niveau, dépendance KO
			$data = array(
				'Orientstruct' => array(
					'typeorient_id' => 1,
					'structurereferente_id' => 2,
					'referent_id' => 1,
					'statut_orient' => 'Orienté',
				)
			);
			$this->Orientstruct->create( $data );
			$result = $this->Orientstruct->dependentForeignKeys(
				array( 'referent_id' => $data['Orientstruct']['structurereferente_id'] ),
				'Structurereferente',
				'Typeorient'
			);
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 4. Existe au second niveau, pas au premier, dépendance KO
			$data = array(
				'Orientstruct' => array(
					'typeorient_id' => 1,
					'structurereferente_id' => 2,
					'referent_id' => 1,
					'statut_orient' => 'Orienté',
				)
			);
			$this->Orientstruct->create( $data );
			$result = $this->Orientstruct->dependentForeignKeys(
				array( 'referent_id' => $data['Orientstruct']['referent_id'] ),
				'Referent',
				'Structurereferente'
			);
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 5. Valeurs nulles
			$data = array(
				'Orientstruct' => array(
					'typeorient_id' => 1,
					'structurereferente_id' => null,
					'referent_id' => null,
					'statut_orient' => 'En attente',
				)
			);
			$this->Orientstruct->create( $data );
			$result = $this->Orientstruct->dependentForeignKeys(
				array( 'structurereferente_id' => $data['Orientstruct']['structurereferente_id'] ),
				'Structurereferente',
				'Typeorient'
			);
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Orientstruct::dependentForeignKeys() avec le troisième
		 * paramètre.
		 */
		public function testDependentForeignKeysAliases3rdParameter() {
			$data = array(
				'Orientstruct' => array(
					'structureorientante_id' => 1,
					'referentorientant_id' => 1,
					'statut_orient' => 'En attente',
				)
			);
			$this->Orientstruct->create( $data );

			// 0. mauvais argument
			$result = $this->Orientstruct->dependentForeignKeys(
				null,
				'Referentorientant',
				'Structureorientante'
			);
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 1. Dépendance OK
			$result = $this->Orientstruct->dependentForeignKeys(
				array( 'referentorientant_id' => $data['Orientstruct']['referentorientant_id'] ),
				'Referentorientant',
				'Structureorientante',
				'Structurereferente'
			);
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Dépendance KO
			$data = array(
				'Orientstruct' => array(
					'structureorientante_id' => 2,
					'referentorientant_id' => 1,
					'statut_orient' => 'En attente',
				)
			);
			$this->Orientstruct->create( $data );
			$result = $this->Orientstruct->dependentForeignKeys(
				array( 'referentorientant_id' => $data['Orientstruct']['structureorientante_id'] ),
				'Referentorientant',
				'Structureorientante',
				'Structurereferente'
			);
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. Valeurs nulles
			$data = array(
				'Orientstruct' => array(
					'structureorientante_id' => null,
					'referentorientant_id' => null,
					'statut_orient' => 'En attente',
				)
			);
			$this->Orientstruct->create( $data );
			$result = $this->Orientstruct->dependentForeignKeys(
				array( 'structureorientante_id' => $data['Orientstruct']['structureorientante_id'] ),
				'Referentorientant',
				'Structureorientante',
				'Structurereferente'
			);
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>