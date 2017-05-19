<?php
	/**
	 * Code source de la classe Cataloguepdifp93BehaviorTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Cataloguepdifp93Behavior', 'Model/Behavior' );
	/**
	 * La classe Cataloguepdifp93BehaviorTest ...
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class Cataloguepdifp93BehaviorTest extends CakeTestCase
	{

		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Categoriefp93',
			'app.Filierefp93',
			'app.Thematiquefp93',
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Filierefp93 = ClassRegistry::init( 'Filierefp93' );
			$this->Filierefp93->Behaviors->attach( 'Cataloguepdifp93' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Filierefp93 );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Cataloguepdifp93Behavior::searchQuery()
		 */
		public function testSearchQuery() {
			$result = $this->Filierefp93->searchQuery( array( ) );
			$result = Hash::combine( $result, 'joins.{n}.alias', 'joins.{n}.type' );
			$expected = array(
				'Categoriefp93' => 'INNER',
				'Thematiquefp93' => 'INNER',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Cataloguepdifp93Behavior::getInsertedPrimaryKey()
		 */
		public function testGetInsertedPrimaryKey() {
			// 1. On retrouve un enregistrement par son nom et son parent
			$conditions = array(
				'Filierefp93.categoriefp93_id' => 1,
				'Filierefp93.name' => 'Filière de test'
			);
			$complement = array();
			$result = $this->Filierefp93->getInsertedPrimaryKey( $conditions, $complement );
			$expected = 1;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. On ne retrouve pas un enregistrement par son nom et son parent
			$conditions = array(
				'Filierefp93.categoriefp93_id' => 1,
				'Filierefp93.name' => 'Filière de test supplémentaire'
			);
			$complement = array();
			$result = $this->Filierefp93->getInsertedPrimaryKey( $conditions, $complement );
			$expected = 2;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

	}
?>