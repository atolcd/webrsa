<?php
	/**
	 * Code source de la classe AppModelTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * ...
	 *
	 * @package app.Test.Case.Model
	 */
	class AppUses extends AppModel
	{
		public $useTable = false;

		public $uses = array( 'Option', 'Entretien' );
	}

	/**
	 * La classe AppModelTest réalise les tests unitaires de la classe AppModel.
	 *
	 * @package app.Test.Case.Model
	 */
	class AppModelTest extends CakeTestCase
	{

		public $Apple = null;

		/**
		 * Fixtures.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'core.Apple',
			'app.Actionfp93',
			'app.Adresseprestatairefp93',
			'app.Dossierep',
			'app.Entretien',
			'app.Fichiermodule',
			'app.Nonorientationproep58',
			'app.Passagecommissionep',
			'app.Regressionorientationep58',
			'app.Rendezvous',
			'app.RendezvousThematiquerdv',
			'app.Sanctionep58',
			'app.Sanctionrendezvousep58',
			'app.Thematiquerdv',
		);

		/**
		 * Set up the test
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();
			$this->Apple = ClassRegistry::init( 'Apple' );

			$this->Apple->validate = array(
				'name' => array(
					'notEmpty' => array(
						'rule' => array( 'notEmpty' ),
						'required' => true,
						'on' => 'create',
						'message' => null
					)
				),
				'color' => array(
					'inList' => array(
						'rule' => array( 'inList', array( 'red', 'blue', 'green' ) ),
						'required' => null,
						'on' => null,
						'message' => null
					)
				)
			);
		}

		/**
		 * tearDown method
		 *
		 * @return void
		 */
		public function tearDown() {
			parent::tearDown();
			unset( $this->Apple );
		}

		/**
		 * Test de la méthode AppModel::save()
		 *
		 * @return void
		 */
//		public function testSave() {
//			$this->Apple->create( array( 'color' => 'red' ) );
//			$this->assertEqual( $this->Apple->save(), false );
//
//			$this->Apple->create( array( 'name' => 'Bintje', 'color' => 'red' ) );
//			$this->assertEqual( $this->Apple->save(), true );
//		}

		/**
		 * Test de la méthode AppModel::save()
		 *
		 * @return void
		 */
		public function testSaveAll() {
			$data = array(
				array(
					'color' => 'red'
				),
				array(
					'name' => 'Bintje',
					'color' => 'red'
				),
			);
			$result = $this->Apple->saveAll( $data, array( 'atomic' => false ) );
			$this->assertEqual( $result, false );

			$data = array(
				array(
					'name' => 'Bintje',
					'color' => 'red'
				),
			);
			$result = $this->Apple->saveAll( $data, array( 'atomic' => false ) );
			$this->assertEqual( $result, true );
		}

		/**
		 * Test de la méthode AppModel::beforeFind() lorsque l'on force les champs
		 * virtuels.
		 */
		public function testBeforefindForceVirtualFields() {
			$this->Actionfp93 = ClassRegistry::init( 'Actionfp93' );

			$query = array(
				'fields' => array(
					'Actionfp93.name',
					'Adresseprestatairefp93.name'
				),
				'joins' => array(
					$this->Actionfp93->join( 'Adresseprestatairefp93' )
				),
				'conditions' => array(
					'Adresseprestatairefp93.name' => 'Foo'
				)
			);

			$this->Actionfp93->forceVirtualFields = true;
			$result = $this->Actionfp93->beforeFind( $query );

			$expected = array(
				'fields' => array(
					'Actionfp93.name',
					'( "Adresseprestatairefp93"."adresse" || \', \' || "Adresseprestatairefp93"."codepos" || \' \' || "Adresseprestatairefp93"."localite" ) AS  "Adresseprestatairefp93__name"'
				),
				'joins' => array(
					array(
						'table' => '"adressesprestatairesfps93"',
						'alias' => 'Adresseprestatairefp93',
						'type' => 'LEFT',
						'conditions' => '"Actionfp93"."adresseprestatairefp93_id" = "Adresseprestatairefp93"."id"',
					),
				),
				'conditions' => array(
					'( "Adresseprestatairefp93"."adresse" || \', \' || "Adresseprestatairefp93"."codepos" || \' \' || "Adresseprestatairefp93"."localite" )' => 'Foo',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode AppModel::getSqLinkedModelsDepartement()
		 */
		public function testGetSqLinkedModelsDepartement() {
			$this->Dossierep = ClassRegistry::init( 'Dossierep' );

			// Test avec un département possédant des thématiques
			Configure::write( 'Cg.departement', 58 );
			$result = $this->Dossierep->getSqLinkedModelsDepartement();
			$expected = '( EXISTS( SELECT "nonorientationsproseps58"."id" AS "nonorientationsproseps58__id" FROM "nonorientationsproseps58" AS "nonorientationsproseps58"   WHERE "nonorientationsproseps58"."dossierep_id" = "Dossierep"."id"    ) OR EXISTS( SELECT "regressionsorientationseps58"."id" AS "regressionsorientationseps58__id" FROM "regressionsorientationseps58" AS "regressionsorientationseps58"   WHERE "regressionsorientationseps58"."dossierep_id" = "Dossierep"."id"    ) OR EXISTS( SELECT "sanctionseps58"."id" AS "sanctionseps58__id" FROM "sanctionseps58" AS "sanctionseps58"   WHERE "sanctionseps58"."dossierep_id" = "Dossierep"."id"    ) OR EXISTS( SELECT "sanctionsrendezvouseps58"."id" AS "sanctionsrendezvouseps58__id" FROM "sanctionsrendezvouseps58" AS "sanctionsrendezvouseps58"   WHERE "sanctionsrendezvouseps58"."dossierep_id" = "Dossierep"."id"    ) OR EXISTS( SELECT "passagescommissionseps"."id" AS "passagescommissionseps__id" FROM "passagescommissionseps" AS "passagescommissionseps"   WHERE "passagescommissionseps"."dossierep_id" = "Dossierep"."id"    ) ) AS "Dossierep__linked_records"';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// Test avec un département ne possédant pas de thématiques
			Configure::write( 'Cg.departement', 976 );
			$result = $this->Dossierep->getSqLinkedModelsDepartement();
			$expected = '( EXISTS( SELECT "passagescommissionseps"."id" AS "passagescommissionseps__id" FROM "passagescommissionseps" AS "passagescommissionseps"   WHERE "passagescommissionseps"."dossierep_id" = "Dossierep"."id"    ) ) AS "Dossierep__linked_records"';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// Test avec des hasAndBelongsToMany
			$this->Rendezvous = ClassRegistry::init( 'Rendezvous' );

			$result = $this->Rendezvous->getSqLinkedModelsDepartement();
			$expected = '( EXISTS( SELECT "entretiens"."id" AS "entretiens__id" FROM "entretiens" AS "entretiens"   WHERE "entretiens"."rendezvous_id" = "Rendezvous"."id"    ) OR EXISTS( SELECT "fichiersmodules"."id" AS "fichiersmodules__id" FROM "fichiersmodules" AS "fichiersmodules"   WHERE "fichiersmodules"."modele" = \'Rendezvous\' AND "fichiersmodules"."fk_value" = "Rendezvous"."id"    ) OR EXISTS( SELECT "rendezvous_thematiquesrdvs"."id" AS "rendezvous_thematiquesrdvs__id" FROM "rendezvous_thematiquesrdvs" AS "rendezvous_thematiquesrdvs"   WHERE "rendezvous_thematiquesrdvs"."rendezvous_id" = "Rendezvous"."id"    ) ) AS "Rendezvous__linked_records"';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// Test sans le fieldName
			Configure::write( 'Cg.departement', 976 );

			$result = $this->Dossierep->getSqLinkedModelsDepartement( null );
			$expected = '( EXISTS( SELECT "passagescommissionseps"."id" AS "passagescommissionseps__id" FROM "passagescommissionseps" AS "passagescommissionseps"   WHERE "passagescommissionseps"."dossierep_id" = "Dossierep"."id"    ) )';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// Test sans relation et avec un nom de champ différent
			$this->Connection = ClassRegistry::init( 'Connection' );
			$result = $this->Connection->getSqLinkedModelsDepartement( 'nombre' );
			$expected = '( 1 = 0 ) AS "Connection__nombre"';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de l'attribut AppModel::uses
		 */
		public function testUses() {
			$AppUses = ClassRegistry::init( 'AppUses' );

			$this->assertTrue( isset( $AppUses->Option ) );
			$this->assertTrue( isset( $AppUses->Entretien ) );
			$this->assertFalse( isset( $AppUses->Rendezvous ) );
		}

		/**
		 * Test de la méthode AppModel::loadModel()
		 */
		public function testLoadModel() {
			$AppUses = ClassRegistry::init( 'AppUses' );

			$this->assertTrue( $AppUses->loadModel( 'Rendezvous' ) );
			$this->assertTrue( isset( $AppUses->Rendezvous ) );

			$this->assertFalse( $AppUses->loadModel( 'Entretien' ) );
			$this->assertTrue( isset( $AppUses->Entretien ) );
		}

		/**
		 * Test de la méthode AppModel::loadModel() avec un modèle inexistant.
		 * @expectedException MissingModelException
		 */
		public function testLoadModelInexistant() {
			$AppUses = ClassRegistry::init( 'AppUses' );
			$AppUses->loadModel( 'FooBarBaz' );
		}

		/**
		 * Test de la méthode AppModel::enums()
		 */
		public function testEnums() {
			$result = $this->Apple->enums();
			$expected = array(
				'Apple' => array(
					'color' => array(
						'red' => 'ENUM::COLOR::red',
						'blue' => 'ENUM::COLOR::blue',
						'green' => 'ENUM::COLOR::green',
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode AppModel::enum()
		 */
		public function testEnum() {
			// 1. Utilisation simple
			$result = $this->Apple->enum( 'color' );
			$expected = array(
				'red' => 'ENUM::COLOR::red',
				'blue' => 'ENUM::COLOR::blue',
				'green' => 'ENUM::COLOR::green',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Utilisation du tri
			$result = $this->Apple->enum( 'color', array( 'sort' => true ) );
			$expected = array(
				'blue' => 'ENUM::COLOR::blue',
				'green' => 'ENUM::COLOR::green',
				'red' => 'ENUM::COLOR::red',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. Utilisation de filter
			$result = $this->Apple->enum( 'color', array( 'filter' => array( 'blue', 'red' ) ) );
			$expected = array(
				'blue' => 'ENUM::COLOR::blue',
				'red' => 'ENUM::COLOR::red',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
