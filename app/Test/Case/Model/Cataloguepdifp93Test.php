<?php
	/**
	 * Code source de la classe Cataloguepdifp93Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Cataloguepdifp93', 'Model' );
	/**
	 * La classe Cataloguepdifp93Test réalise les tests unitaires de la classe Cataloguepdifp93.
	 *
	 * @package app.Test.Case.Model
	 */
	class Cataloguepdifp93Test extends CakeTestCase
	{

		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Actionfp93',
			'app.Adresseprestatairefp93',
			'app.Categoriefp93',
			'app.Filierefp93',
			'app.Prestatairefp93',
			'app.Thematiquefp93',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Cataloguepdifp93
		 */
		public $Cataloguepdifp93 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Cataloguepdifp93 = ClassRegistry::init( 'Cataloguepdifp93' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Cataloguepdifp93 );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Cataloguepdifp93::searchQuery()
		 */
		public function testSearchQuery() {
			$result = $this->Cataloguepdifp93->searchQuery();
			$result = Hash::combine( $result, 'joins.{n}.alias', 'joins.{n}.type' );
			$expected = array(
				'Categoriefp93' => 'LEFT OUTER',
				'Filierefp93' => 'LEFT OUTER',
				'Actionfp93' => 'LEFT OUTER',
				'Prestatairefp93' => 'LEFT OUTER',
				'Adresseprestatairefp93' => 'LEFT OUTER'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Cataloguepdifp93::searchConditions()
		 */
		public function testSearchConditions() {
			$query = array( );
			$search = array(
				'Actionfp93' => array(
					'annee' => 2008
				),
				'Thematiquefp93' => array(
					'name' => 'Prescription'
				),
			);
			$result = $this->Cataloguepdifp93->searchConditions( $query, $search );
			$expected = array(
				'conditions' =>
				array(
					'Actionfp93.annee' => '2008',
					'Thematiquefp93.name ILIKE' => '%Prescription%',
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Cataloguepdifp93::options()
		 */
		public function testOptions() {
			$result = $this->Cataloguepdifp93->options();
			$expected = array(
				'Thematiquefp93' => array(
					'type' => array(
						'pdi' => 'PDI',
						'horspdi' => 'Hors PDI',
					),
				),
				'Actionfp93' => array(
					'actif' => array(
						0 => 'Inactif',
						1 => 'Actif',
					),
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

	}
?>
