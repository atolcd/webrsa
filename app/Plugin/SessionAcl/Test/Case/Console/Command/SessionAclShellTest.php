<?php
/**
 * SessionAclShellTest file
 *
 * PHP 5.3
 *
 * @package SessionAcl
 * @subpackage Test.Case.Controller.Component
 */
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('CommonSessionAclTestFile', 'SessionAcl.Test/Case');
App::uses('SessionAcl', 'SessionAcl.Model/Datasource');
App::uses('SessionAclShell', 'SessionAcl.Console/Command');

/**
 * SessionAclShellTest class
 *
 * @package SessionAcl
 * @subpackage Test.Case.Controller.Component
 */
class SessionAclShellTest extends CommonSessionAclTestFile
{
	/**
	 * setUp method
	 */
	public function setUp() {
		parent::setUp();

		$out = $this->getMock('ConsoleOutput');
		$in = $this->getMock('ConsoleInput');

		$this->Shell = $this->getMock(
			'SessionAclShell',
			array('out', 'err', '_stop', 'log'),
			array($out, $out, $in)
		);

		$this->Shell->params['connection'] = 'test';
		$this->Shell->sessionAclUtility = 'SessionAclUtility';
		$this->Shell->sessionAclDatasource = 'SessionAcl';
		$this->Shell->sessionAclComponent = SessionAcl::get('acl');
	}

	/**
	 * @see SessionAclUtilityTest::testUpdate
	 */
	public function testUpdateAco() {
		$Aco = ClassRegistry::init('Aco');

		$this->Shell->expects($this->once())->method('_stop')->with(0);

		$this->Shell->args = array('Aco');
		$this->Shell->update();
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
	 * @see SessionAclUtilityTest::testUpdate
	 */
	public function testUpdateAro() {
		$Aro = ClassRegistry::init('Aro');

		$this->Shell->expects($this->any())->method('_stop')->with(0);

		$this->Shell->args = array('Aro');
		$this->Shell->update();
		$result = $Aro->find('all');
		$expected = array(
			(int) 0 => array(
				'Aro' => array(
					'id' => (int) 1,
					'parent_id' => null,
					'model' => 'SessionAclTestGroup',
					'foreign_key' => (int) 1,
					'alias' => 'Admin',
					'lft' => (int) 1,
					'rght' => (int) 8
				),
				'Aco' => array()
			),
			(int) 1 => array(
				'Aro' => array(
					'id' => (int) 2,
					'parent_id' => (int) 1,
					'model' => 'SessionAclTestGroup',
					'foreign_key' => (int) 2,
					'alias' => 'Sub-Admin',
					'lft' => (int) 2,
					'rght' => (int) 5
				),
				'Aco' => array()
			),
			(int) 2 => array(
				'Aro' => array(
					'id' => (int) 4,
					'parent_id' => (int) 2,
					'model' => 'SessionAclTestUser',
					'foreign_key' => (int) 2,
					'alias' => 'User2',
					'lft' => (int) 3,
					'rght' => (int) 4
				),
				'Aco' => array()
			),
			(int) 3 => array(
				'Aro' => array(
					'id' => (int) 3,
					'parent_id' => (int) 1,
					'model' => 'SessionAclTestUser',
					'foreign_key' => (int) 1,
					'alias' => 'User1',
					'lft' => (int) 6,
					'rght' => (int) 7
				),
				'Aco' => array()
			)
		);
		$this->assertEquals($expected, $result, 'Aro calculé en fonction des utilisateurs et des groupes');

		// Suppression d'un Aro légitime
		$Aro->deleteAll(
			array(
				'model' => 'SessionAclTestUser',
				'foreign_key' => (int) 2,
			),
			false,
			false
		);

		// Ajout d'un Aro illégitime
		$Aro->create(
			array(
				'parent_id' => (int) 1,
				'model' => 'SessionAclTestUser',
				'foreign_key' => (int) 1000000,
				'alias' => 'User1000000',
				'lft' => (int) 6000000,
				'rght' => (int) 6000001
			)
		);
		$Aro->save( null, array( 'atomic' => false ) );

		$this->Shell->update();
		$result = $Aro->find('all');
		$expected = array(
			(int) 0 => array(
				'Aro' => array(
					'id' => (int) 1,
					'parent_id' => null,
					'model' => 'SessionAclTestGroup',
					'foreign_key' => (int) 1,
					'alias' => 'Admin',
					'lft' => (int) 1,
					'rght' => (int) 8
				),
				'Aco' => array()
			),
			(int) 1 => array(
				'Aro' => array(
					'id' => (int) 2,
					'parent_id' => (int) 1,
					'model' => 'SessionAclTestGroup',
					'foreign_key' => (int) 2,
					'alias' => 'Sub-Admin',
					'lft' => (int) 2,
					'rght' => (int) 5
				),
				'Aco' => array()
			),
			(int) 2 => array(
				'Aro' => array(
					'id' => (int) 6,				// NOTE : reconstruit
					'parent_id' => (int) 2,
					'model' => 'SessionAclTestUser',
					'foreign_key' => (int) 2,
					'alias' => 'User2',
					'lft' => (int) 3,
					'rght' => (int) 4
				),
				'Aco' => array()
			),
			(int) 3 => array(
				'Aro' => array(
					'id' => (int) 3,
					'parent_id' => (int) 1,
					'model' => 'SessionAclTestUser',
					'foreign_key' => (int) 1,
					'alias' => 'User1',
					'lft' => (int) 6,
					'rght' => (int) 7
				),
				'Aco' => array()
			)
		);
		$this->assertEquals($expected, $result, 'Aro rétabli');
	}

	/**
	 * @see SessionAclUtilityTest::testForceHeritage
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

		$this->Shell->expects($this->once())->method('_stop')->with(0);
		$this->Shell->expects($this->any())->method('out')->with('Suppression de 1 permissions');

		// enfant et parent ont les meme droits, on doit supprimer les droit de l'enfant (qui héritera alors des droits du parent)
		$this->Shell->forceHeritage();
	}

	/**
	 * @covers SessionAclShell::deleteOrphans()
	 */
	public function testDeleteOrphan() {
		$Aco = ClassRegistry::init('Aco');
		$Aco->create(
			array(
				'parent_id' => 999, // n'existe pas
				'model' => null,
				'foreign_key' => null,
				'alias' => 'Unknown',
				'lft' => '999',
				'rght' => '1000',
			)
		);
		$Aco->save( null, array( 'atomic' => false ) );

		$this->Shell->expects($this->once())->method('_stop')->with(0);
		$this->Shell->args = array('Aco');
		$this->Shell->deleteOrphans();

		$result = $Aco->find('first', array('conditions' => array('alias' => 'Unknown')));
		$expected = array();
		$this->assertEquals($expected, $result, 'Aco orhelin supprimé');
	}

	/**
	 * @covers SessionAclShell::fastRecover()
	 */
	public function testFastRecover() {
		$Aco = ClassRegistry::init('Aco');
		$Aco->updateAll(
			array(
				'Aco.lft' => null,
				'Aco.rght' => null,
			)
		);

		$this->Shell->expects($this->once())->method('_stop')->with(0);
		$this->Shell->args = array('Aco');
		$this->Shell->fastRecover();

		$results = $Aco->find('first');
		$expected = array(
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
		);
		$this->assertEquals($expected, $results, 'Left et Right recalculé');
	}
}