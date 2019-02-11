<?php
	/**
	 * Code source de la classe DatabaseTableBehaviorTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'DatabaseTableBehavior', 'Model/Behavior' );

	/**
	 * Classe DatabaseTableBehaviorTest.
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class DatabaseTableBehaviorTest extends CakeTestCase
	{
		/**
		 * Modèle Apple utilisé par ce test.
		 *
		 * @var Model
		 */
		public $Apple = null;

		/**
		 * Fixtures utilisés par ces tests unitaires.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'core.Apple'
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Apple = ClassRegistry::init( 'Apple' );
			$this->Apple->Behaviors->attach( 'DatabaseTable' );

			$this->Apple->bindModel(
				array(
					'belongsTo' => array(
						'Parentapple' => array(
							'className' => 'Apple'
						)
					),
					'hasMany' => array(
						'Siblingapple' => array(
							'className' => 'Apple'
						)
					),
					'hasOne' => array(
						'Childapple' => array(
							'className' => 'Apple'
						)
					)
				),
				false
			);

			$this->Apple->Parentapple->bindModel(
				array(
					'hasMany' => array(
						'Childapple2' => array(
							'className' => 'Apple'
						)
					)
				),
				false
			);
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Apple );
			parent::tearDown();
		}

		/**
		 * Test de la méthode DatabaseTableBehavior::fields()
		 *
		 * @covers DatabaseTableBehavior::fields
		 */
		public function testFields() {
			$result = $this->Apple->fields();
			$expected = array (
				'Apple.id',
				'Apple.apple_id',
				'Apple.color',
				'Apple.name',
				'Apple.created',
				'Apple.date',
				'Apple.modified',
				'Apple.mytime',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode DatabaseTableBehavior::fields() avec une exception.
		 *
		 * @expectedException RuntimeException
		 * @covers DatabaseTableBehavior::fields
		 */
		public function testFieldsException() {
			$this->Apple->useTable = false;
			$this->Apple->fields();
		}

		/**
		 * Test de la méthode DatabaseTableBehavior::sq()
		 *
		 * @covers DatabaseTableBehavior::sq
		 */
		public function testSq() {
			// 1. Avec les champs spécifiés
			$result = $this->Apple->sq(
				array(
					'fields' => array( 'Apple.id' ),
					'conditions' => array(
						'Apple.modified >' => '2012-10-31',
						'Apple.color' => 'red'
					),
					'joins' => array(
						$this->Apple->join( 'Parentapple' )
					),
					'limit' => 3
				)
			);
			$expected = 'SELECT "Apple"."id" AS "Apple__id" FROM "apples" AS "Apple" LEFT JOIN "public"."apples" AS "Parentapple" ON ("Apple"."parentapple_id" = "Parentapple"."id")  WHERE "Apple"."modified" > \'2012-10-31\' AND "Apple"."color" = \'red\'    LIMIT 3';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Sans spécifier les champs
			$result = $this->Apple->sq(
				array(
					'conditions' => array(
						'Apple.modified >' => '2012-10-31',
						'Apple.color' => 'red'
					),
					'joins' => array(
						$this->Apple->join( 'Parentapple' )
					),
					'limit' => 3
				)
			);
			$expected = 'SELECT "Apple"."id" AS "Apple__id", "Apple"."apple_id" AS "Apple__apple_id", "Apple"."color" AS "Apple__color", "Apple"."name" AS "Apple__name", "Apple"."created" AS "Apple__created", "Apple"."date" AS "Apple__date", "Apple"."modified" AS "Apple__modified", "Apple"."mytime" AS "Apple__mytime" FROM "apples" AS "Apple" LEFT JOIN "public"."apples" AS "Parentapple" ON ("Apple"."parentapple_id" = "Parentapple"."id")  WHERE "Apple"."modified" > \'2012-10-31\' AND "Apple"."color" = \'red\'    LIMIT 3';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode DatabaseTableBehavior::sq() avec une exception.
		 *
		 * @expectedException RuntimeException
		 * @covers DatabaseTableBehavior::sq
		 */
		public function testSqException() {
			$this->Apple->useTable = false;
			$this->Apple->sq( array( 'fields' => array( 'Apple.id' ) ) );
		}

		/**
		 * Test de la méthode DatabaseTableBehavior::join()
		 *
		 * @covers DatabaseTableBehavior::join
		 * @covers DatabaseTableBehavior::_mergeConditions
		 * @covers DatabaseTableBehavior::_whichHabtmModel
		 */
		public function testJoin() {
			$result = array(
				$this->Apple->join( 'Parentapple' ),
				$this->Apple->join( 'Siblingapple' ),
				$this->Apple->join( 'Childapple' )
			);
			$expected = array(
				array(
					'table' => '"apples"',
					'alias' => 'Parentapple',
					'type' => 'LEFT',
					'conditions' => '"Apple"."parentapple_id" = "Parentapple"."id"',
				),
				array(
					'table' => '"apples"',
					'alias' => 'Siblingapple',
					'type' => 'LEFT',
					'conditions' => '"Siblingapple"."apple_id" = "Apple"."id"',
				),
				array(
					'table' => '"apples"',
					'alias' => 'Childapple',
					'type' => 'LEFT',
					'conditions' => '"Childapple"."apple_id" = "Apple"."id"',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode DatabaseTableBehavior::join() avec une exception.
		 *
		 * @expectedException RuntimeException
		 * @covers DatabaseTableBehavior::join
		 */
		public function testJoinException1() {
			$this->Apple->join( 'Banana' );
		}

		/**
		 * Test de la méthode DatabaseTableBehavior::join() avec une exception.
		 *
		 * @expectedException RuntimeException
		 * @covers DatabaseTableBehavior::join
		 */
		public function testJoinException2() {
			$this->Apple->useTable = false;
			$this->Apple->join( 'Childapple' );
		}

		/**
		 * Test de la méthode DatabaseTableBehavior::join() avec une exception.
		 *
		 * @expectedException RuntimeException
		 * @covers DatabaseTableBehavior::join
		 */
		public function testJoinException3() {
			$this->Apple->Childapple->useTable = false;
			$this->Apple->join( 'Childapple' );
		}

		/**
		 * Test de la méthode DatabaseTableBehavior::join() avec une exception.
		 *
		 * @expectedException RuntimeException
		 * @covers DatabaseTableBehavior::join
		 */
		public function testJoinException4() {
			$this->Apple->useDbConfig = $this->Apple->Childapple->useDbConfig.'x';
			$this->Apple->join( 'Childapple' );
		}

		/**
		 * Test de la méthode DatabaseTableBehavior::sqLatest()
		 *
		 * @covers DatabaseTableBehavior::sqLatest
		 */
		public function testSqLatest() {
			$result = $this->Apple->sqLatest(
				'Parentapple',
				'modified'
			);
			$expected = '( "Parentapple"."id" IS NULL OR "Parentapple"."id" IN ( SELECT "parentapples"."id" AS "parentapples__id" FROM "apples" AS "parentapples"   WHERE "Apple"."parentapple_id" = "parentapples"."id"   ORDER BY "parentapples"."modified" DESC  LIMIT 1 ) )';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = $this->Apple->sqLatest(
				'Parentapple',
				'modified',
				array( 'Parentapple.color' => 'red' ),
				false
			);
			$expected = 'SELECT "parentapples"."id" AS "parentapples__id" FROM "apples" AS "parentapples"   WHERE "Apple"."parentapple_id" = "parentapples"."id" AND "parentapples"."color" = \'red\'   ORDER BY "parentapples"."modified" DESC  LIMIT 1';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode DatabaseTableBehavior::joins() avec des jointures
		 * à un seul niveau.
		 *
		 * @covers DatabaseTableBehavior::joins
		 * @covers DatabaseTableBehavior::_normalize
		 */
		public function testJoins1Level() {
			$result = $this->Apple->joins(
				array(
					'Parentapple' => array( 'type' => 'INNER' ),
					'Childapple'
				)
			);
			$expected = array(
				array(
					'table' => '"apples"',
					'alias' => 'Parentapple',
					'type' => 'INNER',
					'conditions' => '"Apple"."parentapple_id" = "Parentapple"."id"',
				),
				array(
					'table' => '"apples"',
					'alias' => 'Childapple',
					'type' => 'LEFT',
					'conditions' => '"Childapple"."apple_id" = "Apple"."id"',
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode DatabaseTableBehavior::joins() avec des jointures
		 * à plusieurs niveaux.
		 *
		 * @covers DatabaseTableBehavior::joins
		 * @covers DatabaseTableBehavior::_normalize
		 */
		public function testJoins2Levels() {
			$result = $this->Apple->joins(
				array(
					'Parentapple' => array(
						'type' => 'INNER',
						'joins' => array(
							'Childapple2' => array( 'type' => 'LEFT OUTER' )
						)
					)
				)
			);
			$expected = array(
				array(
					'table' => '"apples"',
					'alias' => 'Parentapple',
					'type' => 'INNER',
					'conditions' => '"Apple"."parentapple_id" = "Parentapple"."id"',
				),
				array(
					'table' => '"apples"',
					'alias' => 'Childapple2',
					'type' => 'LEFT OUTER',
					'conditions' => '"Childapple2"."apple_id" = "Parentapple"."id"',
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode DatabaseTableBehavior::joins() avec des jointures
		 * à plusieurs niveaux, contenant des conditions.
		 *
		 * @covers DatabaseTableBehavior::joins
		 * @covers DatabaseTableBehavior::_normalize
		 */
		public function testJoins2LevelsConditions() {
			$result = $this->Apple->joins(
				array(
					'Parentapple' => array(
						'type' => 'INNER',
						'conditions' => '1 = 1',
						'joins' => array(
							'Childapple2' => array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'1 = 2'
								)
							)
						)
					)
				)
			);
			$expected = array(
				array(
					'table' => '"apples"',
					'alias' => 'Parentapple',
					'type' => 'INNER',
					// @todo: utiliser systématiquement un array dans le behavior pour avoir les conditions supplémentaires à la fin
					'conditions' => '1 = 1 AND "Apple"."parentapple_id" = "Parentapple"."id"',
				),
				array(
					'table' => '"apples"',
					'alias' => 'Childapple2',
					'type' => 'LEFT OUTER',
					'conditions' => '"Childapple2"."apple_id" = "Parentapple"."id" AND 1 = 2',
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode DatabaseTableBehavior::joins() sans jointure.
		 *
		 * @covers DatabaseTableBehavior::joins
		 * @covers DatabaseTableBehavior::_normalize
		 */
		public function testJoinsEmpty() {
			$result = $this->Apple->joins( array() );
			$expected = array();
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode DatabaseTableBehavior::joins() avec des jointures
		 * de plusieurs types.
		 *
		 * @covers DatabaseTableBehavior::joins
		 * @covers DatabaseTableBehavior::_normalize
		 */
		public function testJoinsMixed() {
			$result = $this->Apple->joins(
				array(
					'Parentapple' => array(
						'type' => 'INNER',
						'joins' => array(
							'Childapple2' => array( 'type' => 'LEFT OUTER' )
						)
					),
					array(
						'table' => '"apples"',
						'alias' => 'Childapple1',
						'type' => 'LEFT OUTER',
						'conditions' => '"Childapple1"."parentapple_id" = "Parentapple"."id"',
					)
				)
			);
			$expected = array(
				array(
					'table' => '"apples"',
					'alias' => 'Parentapple',
					'type' => 'INNER',
					'conditions' => '"Apple"."parentapple_id" = "Parentapple"."id"',
				),
				array(
					'table' => '"apples"',
					'alias' => 'Childapple2',
					'type' => 'LEFT OUTER',
					'conditions' => '"Childapple2"."apple_id" = "Parentapple"."id"',
				),
				array(
					'table' => '"apples"',
					'alias' => 'Childapple1',
					'type' => 'LEFT OUTER',
					'conditions' => '"Childapple1"."parentapple_id" = "Parentapple"."id"',
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>