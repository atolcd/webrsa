<?php
	/**
	 * Code source de la classe Entreeromev3Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Entreeromev3', 'Model' );

	/**
	 * La classe Entreeromev3Test réalise les tests unitaires de la classe Entreeromev3.
	 *
	 * @package app.Test.Case.Model
	 */
	class Entreeromev3Test extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Appellationromev3',
			'app.Domaineromev3',
			'app.Entreeromev3',
			'app.Familleromev3',
			'app.Metierromev3'
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Entreeromev3
		 */
		public $Entreeromev3 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Entreeromev3 = ClassRegistry::init( 'Entreeromev3' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Entreeromev3 );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Entreeromev3::getCompletedRomev3Joins()
		 */
		public function testGetCompletedRomev3Joins() {
			// 1. Sans alias
			$query = array(
				'fields' => array(),
				'joins' => array()
			);
			$result = $this->Entreeromev3->getCompletedRomev3Joins( $query );
			$expected = array(
				'fields' => array(
					'Familleromev3.code' => 'Familleromev3.code',
					'Familleromev3.name' => 'Familleromev3.name',
					'Domaineromev3.code' => '( "Familleromev3"."code" || "Domaineromev3"."code" ) AS "Domaineromev3__code"',
					'Domaineromev3.name' => 'Domaineromev3.name',
					'Metierromev3.code' => '( "Familleromev3"."code" || "Domaineromev3"."code" || "Metierromev3"."code" ) AS "Metierromev3__code"',
					'Metierromev3.name' => 'Metierromev3.name',
					'Appellationromev3.name' => 'Appellationromev3.name',
				),
				'joins' => array(
					array(
						'table' => '"famillesromesv3"',
						'alias' => 'Familleromev3',
						'type' => 'LEFT OUTER',
						'conditions' => '"Entreeromev3"."familleromev3_id" = "Familleromev3"."id"',
					),
					array(
						'table' => '"domainesromesv3"',
						'alias' => 'Domaineromev3',
						'type' => 'LEFT OUTER',
						'conditions' => '"Entreeromev3"."domaineromev3_id" = "Domaineromev3"."id"',
					),
					array(
						'table' => '"metiersromesv3"',
						'alias' => 'Metierromev3',
						'type' => 'LEFT OUTER',
						'conditions' => '"Entreeromev3"."metierromev3_id" = "Metierromev3"."id"',
					),
					array(
						'table' => '"appellationsromesv3"',
						'alias' => 'Appellationromev3',
						'type' => 'LEFT OUTER',
						'conditions' => '"Entreeromev3"."appellationromev3_id" = "Appellationromev3"."id"',
					),
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Avec alias
			$query = array(
				'fields' => array(),
				'joins' => array()
			);
			$aliases = array(
				'Metierexerce' => 'Metierexerceexppro',
				'Secteuracti' => 'Secteuractiexppro',
				'Entreeromev3' => 'Entreeexppro',
				'Familleromev3' => 'Familleexppro',
				'Domaineromev3' => 'Domaineexppro',
				'Metierromev3' => 'Metierexppro',
				'Appellationromev3' => 'Appellationexppro'
			);
			$result = $this->Entreeromev3->getCompletedRomev3Joins( $query, 'LEFT OUTER', $aliases );
			$expected = array(
				'fields' => array(
					'Familleexppro.code' => 'Familleexppro.code',
					'Familleexppro.name' => 'Familleexppro.name',
					'Domaineexppro.code' => '( "Familleexppro"."code" || "Domaineexppro"."code" ) AS "Domaineexppro__code"',
					'Domaineexppro.name' => 'Domaineexppro.name',
					'Metierexppro.code' => '( "Familleexppro"."code" || "Domaineexppro"."code" || "Metierexppro"."code" ) AS "Metierexppro__code"',
					'Metierexppro.name' => 'Metierexppro.name',
					'Appellationexppro.name' => 'Appellationexppro.name',
				),
				'joins' => array(
					array(
						'table' => '"famillesromesv3"',
						'alias' => 'Familleexppro',
						'type' => 'LEFT OUTER',
						'conditions' => '"Entreeexppro"."familleromev3_id" = "Familleexppro"."id"',
					),
					array(
						'table' => '"domainesromesv3"',
						'alias' => 'Domaineexppro',
						'type' => 'LEFT OUTER',
						'conditions' => '"Entreeexppro"."domaineromev3_id" = "Domaineexppro"."id"',
					),
					array(
						'table' => '"metiersromesv3"',
						'alias' => 'Metierexppro',
						'type' => 'LEFT OUTER',
						'conditions' => '"Entreeexppro"."metierromev3_id" = "Metierexppro"."id"',
					),
					array(
						'table' => '"appellationsromesv3"',
						'alias' => 'Appellationexppro',
						'type' => 'LEFT OUTER',
						'conditions' => '"Entreeexppro"."appellationromev3_id" = "Appellationexppro"."id"',
					),
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
