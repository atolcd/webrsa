<?php
/**
 * SessionAclUtilityTest file
 *
 * PHP 5.3
 *
 * @package SessionAcl
 * @subpackage Test.Case.Utility
 */

App::uses('CommonSessionAclTestFile', 'SessionAcl.Test/Case');
App::uses('SessionAclUtility', 'SessionAcl.Utility');

/**
 * SessionAclUtilityTest class
 *
 * @package SessionAcl
 * @subpackage Test.Case.Utility
 */
class SessionAclUtilityTest extends CommonSessionAclTestFile
{
	/**
	 * Test de la méthode SessionAclUtility::updateAcos() qui est une concaténation de plein d'autres méthodes
	 */
	public function testUpdateAcos() {
		$Aco = ClassRegistry::init('Aco');

		$result = $Aco->find('all');
		$expected = array(
			(int) 0 => array(
				'Aco' => array(
					'id' => (int) 1,
					'parent_id' => null,
					'model' => null,
					'foreign_key' => null,
					'alias' => 'controllers',
					'lft' => (int) 1,
					'rght' => (int) 14
				),
				'Aro' => array()
			),
			(int) 1 => array(
				'Aco' => array(
					'id' => (int) 2,
					'parent_id' => null,
					'model' => null,
					'foreign_key' => null,
					'alias' => 'modeles',
					'lft' => (int) 15,
					'rght' => (int) 20
				),
				'Aro' => array()
			),
			(int) 2 => array(
				'Aco' => array(
					'id' => (int) 3,
					'parent_id' => (int) 1,
					'model' => null,
					'foreign_key' => null,
					'alias' => 'Groups',
					'lft' => (int) 2,
					'rght' => (int) 7
				),
				'Aro' => array()
			),
			(int) 3 => array(
				'Aco' => array(
					'id' => (int) 4,
					'parent_id' => (int) 1,
					'model' => null,
					'foreign_key' => null,
					'alias' => 'Users',
					'lft' => (int) 8,
					'rght' => (int) 13
				),
				'Aro' => array()
			),
			(int) 4 => array(
				'Aco' => array(
					'id' => (int) 5,
					'parent_id' => (int) 3,
					'model' => null,
					'foreign_key' => null,
					'alias' => 'add',
					'lft' => (int) 3,
					'rght' => (int) 4
				),
				'Aro' => array()
			),
			(int) 5 => array(
				'Aco' => array(
					'id' => (int) 6,
					'parent_id' => (int) 3,
					'model' => null,
					'foreign_key' => null,
					'alias' => 'edit',
					'lft' => (int) 5,
					'rght' => (int) 6
				),
				'Aro' => array()
			),
			(int) 6 => array(
				'Aco' => array(
					'id' => (int) 7,
					'parent_id' => (int) 4,
					'model' => null,
					'foreign_key' => null,
					'alias' => 'add',
					'lft' => (int) 9,
					'rght' => (int) 10
				),
				'Aro' => array()
			),
			(int) 7 => array(
				'Aco' => array(
					'id' => (int) 8,
					'parent_id' => (int) 4,
					'model' => null,
					'foreign_key' => null,
					'alias' => 'edit',
					'lft' => (int) 11,
					'rght' => (int) 12
				),
				'Aro' => array()
			),
			(int) 8 => array(
				'Aco' => array(
					'id' => (int) 9,
					'parent_id' => (int) 2,
					'model' => 'User',
					'foreign_key' => (int) 1,
					'alias' => 'User1',
					'lft' => (int) 18,
					'rght' => (int) 19
				),
				'Aro' => array()
			)
		);
		$this->assertEquals($expected, $result, 'Aco issue des fixtures');

		/**
		 * @see CommonSessionAclTestFile::setup() - Changement des controllers
		 */
		SessionAclUtility::updateAcos();
		$result = $Aco->find('all');
		$expected = array(
			(int) 0 => array(
				'Aco' => array(
					'id' => (int) 1,
					'parent_id' => null,
					'model' => null,
					'foreign_key' => null,
					'alias' => 'controllers',
					'lft' => (int) 1,
					'rght' => (int) 12
				),
				'Aro' => array()
			),
			(int) 1 => array(
				'Aco' => array(
					'id' => (int) 10,
					'parent_id' => (int) 1,
					'model' => null,
					'foreign_key' => null,
					'alias' => 'NumeroDeux',
					'lft' => (int) 2,
					'rght' => (int) 7
				),
				'Aro' => array()
			),
			(int) 2 => array(
				'Aco' => array(
					'id' => (int) 11,
					'parent_id' => (int) 10,
					'model' => null,
					'foreign_key' => null,
					'alias' => 'action1',
					'lft' => (int) 3,
					'rght' => (int) 4
				),
				'Aro' => array()
			),
			(int) 3 => array(
				'Aco' => array(
					'id' => (int) 12,
					'parent_id' => (int) 10,
					'model' => null,
					'foreign_key' => null,
					'alias' => 'action2',
					'lft' => (int) 5,
					'rght' => (int) 6
				),
				'Aro' => array()
			),
			(int) 4 => array(
				'Aco' => array(
					'id' => (int) 13,
					'parent_id' => (int) 1,
					'model' => null,
					'foreign_key' => null,
					'alias' => 'TestSession',
					'lft' => (int) 8,
					'rght' => (int) 11
				),
				'Aro' => array()
			),
			(int) 5 => array(
				'Aco' => array(
					'id' => (int) 14,
					'parent_id' => (int) 13,
					'model' => null,
					'foreign_key' => null,
					'alias' => 'index',
					'lft' => (int) 9,
					'rght' => (int) 10
				),
				'Aro' => array()
			),
			(int) 6 => array(
				'Aco' => array(
					'id' => (int) 2,
					'parent_id' => null,
					'model' => null,
					'foreign_key' => null,
					'alias' => 'modeles',
					'lft' => (int) 13,
					'rght' => (int) 16
				),
				'Aro' => array()
			),
			(int) 7 => array(
				'Aco' => array(
					'id' => (int) 9,
					'parent_id' => (int) 2,
					'model' => 'User',
					'foreign_key' => (int) 1,
					'alias' => 'User1',
					'lft' => (int) 14,
					'rght' => (int) 15
				),
				'Aro' => array()
			)
		);
		$this->assertEquals($expected, $result, 'Aco calculé en fonction des controllers de l\'application');
	}

	/**
	 * Test de SessionAclUtility::addMissingsAcos()
	 */
	public function testAddMissingsAcos() {
		$Aco = ClassRegistry::init('Aco');
		$query = array(
			'fields' => 'alias',
			'conditions' => array('alias' => 'TestSession'),
			'recursive' => -1
		);

		$Aco->deleteAll(array('alias' => 'TestSession'));
		$result = Hash::get($Aco->find('first', $query), 'Aco.alias');
		$expected = null;
		$this->assertEquals($expected, $result, 'Aco supprimé');

		SessionAclUtility::initUpdate($Aco);
		SessionAclUtility::addMissingsAcos();
		$result = Hash::get($Aco->find('first', $query), 'Aco.alias');
		$expected = 'TestSession';
		$this->assertEquals($expected, $result, 'Aco manquant replacé');
	}

	/**
	 * Test de SessionAclUtility::deleteNotExistingAcos()
	 */
	public function testDeleteNotExistingAcos() {
		$Aco = ClassRegistry::init('Aco');
		$query = array(
			'fields' => 'alias',
			'conditions' => array('alias' => 'NotExistingController'),
			'recursive' => -1
		);

		$Aco->create(
			array(
				'alias' => 'NotExistingController',
				'parent_id' => Hash::get(
					$Aco->find('first', array('conditions' => array('alias' => 'controllers'))),
					'Aco.id'
				)
			)
		);
		$Aco->save( null, array( 'atomic' => false ) );
		$result = Hash::get($Aco->find('first', $query), 'Aco.alias');
		$expected = 'NotExistingController';
		$this->assertEquals($expected, $result, 'Aco d\'un controller qui n\'existe pas');

		SessionAclUtility::initUpdate($Aco);
		SessionAclUtility::deleteNotExistingAcos();
		$result = Hash::get($Aco->find('first', $query), 'Aco.alias');
		$expected = null;
		$this->assertEquals($expected, $result, 'Aco en trop supprimé');
	}

	/**
	 * Test de SessionAclUtility::fastPostrgresGetAll()
	 */
	public function testFastPostrgresGetAll() {
		if ($this->Controller->SessionAclTestUser->getDatasource() instanceof Postgres) {
			SessionAclUtility::updateAcos();

			$results = SessionAclUtility::fastPostrgresGetAll(
				$this->Controller->SessionAclTestUser,
				1,
				array(
					'Permission' => 'sessionacl_aros_acos /* no_cache */',
					'Aro' => 'sessionacl_aros',
					'Aco' => 'sessionacl_acos',
				)
			);
			$expected = array(
				'controllers' => false,
				'controllers/NumeroDeux' => false,
				'controllers/NumeroDeux/action1' => false,
				'controllers/NumeroDeux/action2' => false,
				'controllers/TestSession' => false,
				'controllers/TestSession/index' => false
			);
			$this->assertEquals($expected, $results, 'Chargement rapide des droits');

			$Permission = ClassRegistry::init('Permission');
			$aco_id = Hash::get(
				$Permission->Aco->find('first', array('conditions' => array('Aco.alias' => 'NumeroDeux'))),
				$Permission->Aco->alias.'.id'
			);
			$aro_id = Hash::get(
				$Permission->Aro->find('first', array('conditions' => array('Aro.alias' => 'User1'))),
				$Permission->Aro->alias.'.id'
			);
			$Permission->create(
				array(
					'aro_id' => $aro_id,
					'aco_id' => $aco_id,
					'_create' => '1',
					'_read' => '1',
					'_update' => '1',
					'_delete' => '1',
				)
			);
			$Permission->save( null, array( 'atomic' => false ) );

			$results = SessionAclUtility::fastPostrgresGetAll(
				$this->Controller->SessionAclTestUser,
				1,
				array(
					'Permission' => 'sessionacl_aros_acos',
					'Aro' => 'sessionacl_aros',
					'Aco' => 'sessionacl_acos',
				)
			);
			$expected = array(
				'controllers' => false,
				'controllers/NumeroDeux' => true,
				'controllers/NumeroDeux/action1' => true,
				'controllers/NumeroDeux/action2' => true,
				'controllers/TestSession' => false,
				'controllers/TestSession/index' => false
			);
			$this->assertEquals($expected, $results, 'Chargement rapide des droits (modifiés)');
		}
	}

	/**
	 * @covers SessionAclUtility::_findOrCreateAcoControllers
	 */
	public function testFindOrCreateAcoControllers() {
		$Aco = ClassRegistry::init('Aco');
		$Aco->deleteAll(array('alias' => 'controllers'));

		SessionAclUtility::initUpdate($Aco);

		$query = array(
			'fields' => 'id',
			'conditions' => array('alias' => 'controllers'),
			'recursive' => -1
		);
		$result = Hash::get($Aco->find('first', $query), $Aco->alias.'.id');
		$expected = 10;
		$this->assertEquals($expected, $result, 'Restauration de l\'aco "controllers"');
	}

	/**
	 * @covers SessionAclUtility::deleteOrphans
	 */
	public function testDeleteOrphans() {
		$Aco = ClassRegistry::init('Aco');
		$Aco->deleteAll(array('alias' => 'controllers'));

		SessionAclUtility::deleteOrphans($Aco);

		$results = $Aco->find('all', array('recursive' => -1));
		$expected = array(
			(int) 0 => array(
				'Aco' => array(
					'id' => (int) 2,
					'parent_id' => null, // N'est pas descendent de "controllers"
					'model' => null,
					'foreign_key' => null,
					'alias' => 'modeles',
					'lft' => (int) 15,
					'rght' => (int) 20
				)
			),
			(int) 1 => array(
				'Aco' => array(
					'id' => (int) 9,
					'parent_id' => (int) 2,
					'model' => 'User',
					'foreign_key' => (int) 1,
					'alias' => 'User1',
					'lft' => (int) 18,
					'rght' => (int) 19
				)
			)
		);
		$this->assertEquals($expected, $results, 'Suppression des orphelins');
	}

	/**
	 * @covers SessionAclUtility::forceHeritage
	 */
	public function testForceHeritage() {
		$Permission = ClassRegistry::init('Permission');
		$aco_id = Hash::get(
			$Permission->Aco->find('first', array('conditions' => array('Aco.alias' => 'Users'))),
			$Permission->Aco->alias.'.id'
		);
		$aro_id = Hash::get(
			$Permission->Aro->find('first', array('conditions' => array('Aro.alias' => 'User1'))),
			$Permission->Aro->alias.'.id'
		);
		$Permission->create(
			array(
				'aro_id' => $aro_id,
				'aco_id' => $aco_id,
				'_create' => '1',
				'_read' => '1',
				'_update' => '1',
				'_delete' => '1',
			)
		);
		$Permission->save( null, array( 'atomic' => false ) );

		$aro_id = Hash::get(
			$Permission->Aro->find('first', array('conditions' => array('Aro.alias' => 'Admin'))),
			$Permission->Aro->alias.'.id'
		);
		$Permission->create(
			array(
				'aro_id' => $aro_id, // Parent du 1er create
				'aco_id' => $aco_id,
				'_create' => '1',
				'_read' => '1',
				'_update' => '1',
				'_delete' => '1',
			)
		);
		$Permission->save( null, array( 'atomic' => false ) );

		// enfant et parent ont les meme droits, on doit supprimer les droit de l'enfant (qui héritera alors des droits du parent)
		$results = SessionAclUtility::forceHeritage();
		$expected = 1;
		$this->assertEquals($expected, $results, 'Héritage forcé (count)');

		$results = $Permission->find('all', array("recursive" => -1));
		$expected = array(
			(int) 0 => array(
				'Permission' => array(
					'id' => 2,
					'aro_id' => $aro_id,
					'aco_id' => $aco_id,
					'_create' => '1',
					'_read' => '1',
					'_update' => '1',
					'_delete' => '1',
				)
			)
		);
		$this->assertEquals($expected, $results, 'Héritage forcé (results)');

		$aco_id = Hash::get(
			$Permission->Aco->find('first', array('conditions' => array('Aco.alias' => 'add'))),
			$Permission->Aco->alias.'.id'
		);
		$aro_id = Hash::get(
			$Permission->Aro->find('first', array('conditions' => array('Aro.alias' => 'User1'))),
			$Permission->Aro->alias.'.id'
		);
		$Permission->create(
			array(
				'aro_id' => $aro_id,
				'aco_id' => $aco_id,
				'_create' => '1',
				'_read' => '1',
				'_update' => '1',
				'_delete' => '1',
			)
		);
		$Permission->save( null, array( 'atomic' => false ) );

		$aro_id = Hash::get(
			$Permission->Aro->find('first', array('conditions' => array('Aro.alias' => 'Admin'))),
			$Permission->Aro->alias.'.id'
		);
		$Permission->create(
			array(
				'aro_id' => $aro_id,
				'aco_id' => $aco_id,
				'_create' => '-1',
				'_read' => '-1',
				'_update' => '-1',
				'_delete' => '-1',
			)
		);
		$Permission->save( null, array( 'atomic' => false ) );

		$results = SessionAclUtility::forceHeritage();
		$expected = 0;
		$this->assertEquals($expected, $results, 'Pas d\'héritage possible (pas de droits parent identique)');
	}


}