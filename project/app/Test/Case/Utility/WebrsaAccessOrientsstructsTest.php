<?php
	/**
	 * Code source de la classe WebrsaAccessOrientsstructsTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAccessOrientsstructs', 'Utility' );

	/**
	 * La classe WebrsaAccessOrientsstructsTest réalise les tests unitaires de la classe WebrsaAccessOrientsstructs.
	 *
	 * @package app.Test.Case.Model
	 */
	class WebrsaAccessOrientsstructsTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array();

		/**
		 * Test de la méthode WebrsaAccessOrientsstructs::params()
		 *
		 * @covers WebrsaAccessOrientsstructs::params
		 */
		public function testParams() {
			Configure::write( 'Cg.departement', 58 );
			$params = array(
				'reorientationseps' => true,
			);
			$result = WebrsaAccessOrientsstructs::params($params);
			$expected = array(
				'alias' => 'Orientstruct',
				'departement' => (int)Configure::read( 'Cg.departement' ),
				'ajout_possible' => null,
				'reorientationseps' => true // Modifié par l'envoi de $params en paramètres
			);
			$this->assertEqual( $result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__ );
		}

		/**
		 * Test de la méthode WebrsaAccessOrientsstructs::access()
		 *
		 * @covers WebrsaAccessOrientsstructs::access
		 */
		public function testAccess() {
			Configure::write( 'Cg.departement', 58 );
			$record = array(
				'Orientstruct' => array(
					'rgorient' => 1,
					'printable' => true,
					'linked_records' => true,
					'dernier' => true,
					'dernier_oriente' => true
				)
			);
			$params = array(
				'ajout_possible' => true,
				'reorientationseps' => false
			);
			$result = WebrsaAccessOrientsstructs::access( $record, $params );
			$expected = array(
				'Orientstruct' => array(
					'rgorient' => 1,
					'printable' => true,
					'linked_records' => true,
					'dernier' => true,
					'dernier_oriente' => true
				),
				'/Orientsstructs/add' => true,
				'/Orientsstructs/edit' => true,
				'/Orientsstructs/impression' => true,
				'/Orientsstructs/delete' => false,
				'/Orientsstructs/filelink' => true
			);
			$this->assertEqual( $result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__ );
		}

		/**
		 * Test de la méthode WebrsaAccessOrientsstructs::accesses()
		 *
		 * @covers WebrsaAccessOrientsstructs::accesses
		 */
		public function testAccesses() {
			Configure::write( 'Cg.departement', 58 );
			$records = array(
				array(
					'Orientstruct' => array(
						'rgorient' => 1,
						'printable' => true,
						'linked_records' => true,
						'dernier' => true,
						'dernier_oriente' => true
					)
				),
				array(
					'Orientstruct' => array(
						'rgorient' => 2,
						'printable' => true,
						'linked_records' => true,
						'dernier' => false,
						'dernier_oriente' => false
					)
				),
			);
			$params = array(
				'ajout_possible' => true,
				'reorientationseps' => false
			);
			$accesses = WebrsaAccessOrientsstructs::accesses($records, $params);
			$result = WebrsaAccessOrientsstructs::accesses($records, $params);
			$expected = array(
				(int) 0 => array(
					'Orientstruct' => array(
						'rgorient' => (int) 1,
						'printable' => true,
						'linked_records' => true,
						'dernier' => true,
						'dernier_oriente' => true
					),
					'/Orientsstructs/add' => true,
					'/Orientsstructs/edit' => true,
					'/Orientsstructs/impression' => true,
					'/Orientsstructs/delete' => false,
					'/Orientsstructs/filelink' => true
				),
				(int) 1 => array(
					'Orientstruct' => array(
						'rgorient' => (int) 2,
						'printable' => true,
						'linked_records' => true,
						'dernier' => false,
						'dernier_oriente' => false
					),
					'/Orientsstructs/add' => true,
					'/Orientsstructs/edit' => false,
					'/Orientsstructs/impression' => true,
					'/Orientsstructs/delete' => false,
					'/Orientsstructs/filelink' => true
				)
			);
			$this->assertEqual( $result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__ );
		}

		/**
		 * @covers WebrsaAccessOrientsstructs::check
		 */
		public function testCheck() {
			Configure::write( 'Cg.departement', 58 );
			$record = array(
				'Orientstruct' => array(
					'rgorient' => 1,
					'printable' => true,
					'linked_records' => true,
				)
			);
			$params = array(
				'ajout_possible' => true,
				'reorientationseps' => false
			);

			// Défini
			$result = WebrsaAccessOrientsstructs::check( 'Orientsstructs', 'impression', $record, $params );
			$this->assertTrue( $result, 'Failed in '.__FUNCTION__.' : '.__LINE__ );

			// Non défini
			$result = WebrsaAccessOrientsstructs::check( 'Orientsstructs', 'foo', $record, $params );
			$this->assertFalse( $result, 'Failed in '.__FUNCTION__.' : '.__LINE__ );
		}

		/**
		 * @covers WebrsaAccessOrientsstructs::_edit
		 */
		public function test_edit() {
			// Conditions toutes remplies
			Configure::write( 'Cg.departement', 58 );
			$record = array(
				'Orientstruct' => array(
					'dernier' => true,
				)
			);
			$params = array(
				'ajout_possible' => true,
			);

			$result = WebrsaAccessOrientsstructs::check( 'Orientsstructs', 'edit', $record, $params );
			$this->assertTrue( $result, 'Failed in '.__FUNCTION__.' : '.__LINE__ );

			// Une des conditions non remplie
			Configure::write( 'Cg.departement', 58 );
			$record = array(
				'Orientstruct' => array(
					'dernier' => false,
				)
			);
			$params = array(
				'ajout_possible' => true,
			);

			$result = WebrsaAccessOrientsstructs::check( 'Orientsstructs', 'edit', $record, $params );
			$this->assertFalse( $result, 'Failed in '.__FUNCTION__.' : '.__LINE__ );

			// Conditions supplémentaires pour le CG 66 remplie
			$nombreJours = 10;
			$dateDuJour = new DateTime(date('Y-m-d'));
			$interval = new DateInterval('P'.($nombreJours-1).'D'); // -1
			$dateDuJour->sub($interval);
			Configure::write( 'Periode.modifiableorientation.nbheure', $nombreJours*24 );
			Configure::write( 'Cg.departement', 66 );

			$record = array(
				'Orientstruct' => array(
					'dernier' => true,
					'dernier_oriente' => true,
					'date_valid' => $dateDuJour->format('Y-m-d'), // Date du jour - le nombre d'heures limite +1
				)
			);
			$params = array(
				'ajout_possible' => true,
			);

			$result = WebrsaAccessOrientsstructs::check( 'Orientsstructs', 'edit', $record, $params );
			$this->assertTrue( $result, 'Failed in '.__FUNCTION__.' : '.__LINE__ );

			// Conditions supplémentaires pour le CG 66 Non remplie
			$nombreJours = 10;
			$dateDuJour = new DateTime(date('Y-m-d'));
			$interval = new DateInterval('P'.($nombreJours+1).'D'); // +1
			$dateDuJour->sub($interval);
			Configure::write( 'Periode.modifiableorientation.nbheure', $nombreJours*24 );
			Configure::write( 'Cg.departement', 66 );

			$record = array(
				'Orientstruct' => array(
					'dernier' => true,
					'dernier_oriente' => true,
					'date_valid' => $dateDuJour->format('Y-m-d'), // Date du jour - le nombre d'heures limite +1
				)
			);
			$params = array(
				'ajout_possible' => true,
			);

			$result = WebrsaAccessOrientsstructs::check( 'Orientsstructs', 'edit', $record, $params );
			$this->assertFalse( $result, 'Failed in '.__FUNCTION__.' : '.__LINE__ );
		}

		/**
		 * @covers WebrsaAccessOrientsstructs::_impression
		 */
		public function test_impression() {
			// Conditions toutes remplies
			$record = array(
				'Orientstruct' => array(
					'printable' => true,
				)
			);
			$params = array();
			$result = WebrsaAccessOrientsstructs::check( 'Orientsstructs', 'impression', $record, $params );
			$this->assertTrue( $result, 'Failed in '.__FUNCTION__.' : '.__LINE__ );
		}

		/**
		 * @covers WebrsaAccessOrientsstructs::_delete
		 */
		public function test_delete() {
			// Conditions toutes remplies
			$record = array(
				'Orientstruct' => array(
					'dernier' => true,
					'dernier_oriente' => true,
					'linked_records' => false,
				)
			);
			$params = array(
				'reorientationseps' => '' // empty
			);
			$result = WebrsaAccessOrientsstructs::check( 'Orientsstructs', 'delete', $record, $params );
			$this->assertTrue( $result, 'Failed in '.__FUNCTION__.' : '.__LINE__ );
		}

		/**
		 * @covers WebrsaAccessOrientsstructs::_impression_changement_referent
		 */
		public function test_impression_changement_referent() {
			Configure::write( 'Cg.departement', 66 );
			// Conditions toutes remplies
			$record = array(
				'Orientstruct' => array(
					'premier_oriente' => true,
					'notifbenefcliquable' => true,
				)
			);
			$params = array();
			$result = WebrsaAccessOrientsstructs::check( 'Orientsstructs', 'impression_changement_referent', $record, $params );
			$this->assertTrue( $result, 'Failed in '.__FUNCTION__.' : '.__LINE__ );
		}

		/**
		 * @covers WebrsaAccessOrientsstructs::actions
		 */
		public function testActions() {
			Configure::write('Cg.departement', 66);
			$params = array();
			$result = WebrsaAccessOrientsstructs::actions($params);
			$expected = array(
				'Orientsstructs.add' => array(
					'ajout_possible' => true
				),
				'Orientsstructs.edit' => array(
					'ajout_possible' => true
				),
				'Orientsstructs.impression' => array(),
				'Orientsstructs.delete' => array(
					'reorientationseps' => true
				),
				'Orientsstructs.filelink' => array(),
				'Orientsstructs.impression_changement_referent' => array()
			);
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__ );
		}
	}
?>