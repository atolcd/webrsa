<?php
	/**
	 * Code source de la classe WebrsaModelUtilityTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaModelUtility', 'Utility');

	/**
	 * La classe WebrsaModelUtilityTest réalise les tests unitaires de la classe WebrsaModelUtility.
	 *
	 * @package app.Test.Case.Model
	 */
	class WebrsaModelUtilityTest extends CakeTestCase
	{
		public $fixtures = array(
			'app.Personne',
			'app.Foyer',
			'app.Dossier',
			'app.Contratinsertion',
			'app.Cui',
			'app.Rendezvous'
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			Configure::write( 'Cg.departement', 66 );
			Configure::write('Rendezvous.useThematique', false );

			parent::setUp();
		}

		/**
		 * @covers WebrsaModelUtility::findJoinKey
		 */
		public function testFindJoinKey() {
			$Personne = ClassRegistry::init('Personne');

			$query = array(
				'joins' => array(),
			);

			$result = WebrsaModelUtility::findJoinKey('Foyer', $query);
			$expected = false;
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);

			// On ajoute les jointures sur Foyer et sur Dossier
			$query = WebrsaModelUtility::addJoins($Personne, array('Foyer' => array('Dossier')), $query);

			$result = WebrsaModelUtility::findJoinKey('Foyer', $query);
			$expected = 0; // $query['joins'][0]['alias'] === 'Foyer'
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);

			$result = WebrsaModelUtility::findJoinKey('Dossier', $query);
			$expected = 1; // $query['joins'][1]['alias'] === 'Dossier'
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
		}

		/**
		 * @covers WebrsaModelUtility::changeJoinPriority
		 */
		public function testChangeJoinPriority() {
			$Personne = ClassRegistry::init('Personne');

			$query = array(
				'joins' => array(
					$Personne->join('Contratinsertion'),
					$Personne->join('Cui'),
					$Personne->join('Rendezvous'),
				),
			);

			$result = Hash::extract($query, 'joins.{n}.alias');
			$expected = array('Contratinsertion', 'Cui', 'Rendezvous');
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);

			$priority = array('Cui');
			$query = WebrsaModelUtility::changeJoinPriority($priority, $query);

			$result = Hash::extract($query, 'joins.{n}.alias');
			$expected = array('Cui', 'Contratinsertion', 'Rendezvous');
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);

			$priority = array('Rendezvous', 'Cui');
			$query = WebrsaModelUtility::changeJoinPriority($priority, $query);

			$result = Hash::extract($query, 'joins.{n}.alias');
			$expected = array('Rendezvous', 'Cui', 'Contratinsertion');
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
		}

		/**
		 * @covers WebrsaModelUtility::changeJoinPriority
		 * @expectedException PHPUnit_Framework_Error_Notice
		 */
		public function testChangeJoinPriorityException() {
			$Personne = ClassRegistry::init('Personne');

			$query = array(
				'joins' => array(
					$Personne->join('Contratinsertion'),
				),
			);

			WebrsaModelUtility::changeJoinPriority(array('Cui'), $query);
		}

		/**
		 * @covers WebrsaModelUtility::unsetJoin
		 */
		public function testUnsetJoin() {
			$Personne = ClassRegistry::init('Personne');

			$query = array(
				'joins' => array(
					$Personne->join('Contratinsertion'),
					$Personne->join('Rendezvous'),
				),
			);

			$query = WebrsaModelUtility::unsetJoin(array('Contratinsertion', 'Rendezvous'), $query);
			$result = empty($query['joins']);
			$expected = true;
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
		}

		/**
		 * @covers WebrsaModelUtility::addJoins
		 */
		public function testAddJoins() {
			$Personne = ClassRegistry::init('Personne');

			$query = array(
				'joins' => array(
					$Personne->join('Contratinsertion'),
				),
			);

			$joinList = array(
				'Foyer' => array('Dossier'),
				'Rendezvous',
				'Cui'
			);
			$query = WebrsaModelUtility::addJoins($Personne, $joinList, $query);

			$result = Hash::extract($query, 'joins.{n}.alias');
			$expected = array('Contratinsertion', 'Foyer', 'Dossier', 'Rendezvous', 'Cui');
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
		}

		/**
		 * @covers WebrsaModelUtility::addJoins
		 * @expectedException PHPUnit_Framework_Error_Notice
		 */
		public function testAddJoinsException() {
			$Personne = ClassRegistry::init('Personne');

			$query = array(
				'joins' => array(
					$Personne->join('Contratinsertion'),
				),
			);

			$joinList = array(
				'Rendezvous' => array('Dossier'), // On doit passer par Foyer pour atteindre Dossier, pas par Rendezvous
				'Cui'
			);
			WebrsaModelUtility::addJoins($Personne, $joinList, $query);
		}

		/**
		 * @covers WebrsaModelUtility::addConditionDernier
		 */
		public function testAddConditionDernier() {
			$Personne = ClassRegistry::init('Personne');

			$query = array(
				'joins' => array(
					$Personne->join('Contratinsertion'),
					$Personne->join('Rendezvous'),
				),
			);

			$query = WebrsaModelUtility::addConditionDernier('Contratinsertion', $query);
			$result = $query['conditions'];
			$expected = array(
				array(
					'OR' => array(
						(int) 0 => 'Contratinsertion.id IS NULL',
						(int) 1 => 'Contratinsertion.id IN (SELECT "a"."id" AS "a__id" FROM "contratsinsertion" AS "a"   WHERE "a"."personne_id" = "Personne"."id"   ORDER BY "a"."id" DESC  LIMIT 1)'
					)
				)
			);
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);

			$query['conditions'] = array();

			$query = WebrsaModelUtility::addConditionDernier($Personne->Rendezvous, $query, array('Rendezvous.created' => 'DESC'));
			$result = $query['conditions'];
			$expected = array(
				array(
					'OR' => array(
						(int) 0 => 'Rendezvous.id IS NULL',
						(int) 1 => 'Rendezvous.id IN (SELECT "a"."id" AS "a__id" FROM "rendezvous" AS "a"   WHERE "a"."personne_id" = "Personne"."id"   ORDER BY "a"."created" DESC  LIMIT 1)'
					)
				)
			);

			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);

			$query['conditions'] = array('Personne.foyer_id' => 123);

			$query = WebrsaModelUtility::addConditionDernier($Personne, $query);
			$result = $query['conditions'];
			$expected = array(
				'Personne.foyer_id' => (int) 123,
				(int) 0 => array(
					'OR' => array(
						(int) 0 => 'Personne.id IS NULL',
						(int) 1 => 'Personne.id IN (SELECT "a"."id" AS "a__id" FROM "personnes" AS "a"   WHERE "a"."foyer_id" = 123   ORDER BY "a"."id" DESC  LIMIT 1)'
					)
				)
			);

			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
		}
	}
