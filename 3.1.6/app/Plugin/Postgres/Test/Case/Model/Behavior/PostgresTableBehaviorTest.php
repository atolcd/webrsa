<?php
	/**
	 * Code source de la classe PostgresTableBehaviorTest.
	 *
	 * PHP 5.3
	 *
	 * @package Postgres
	 * @subpackage Test.Case.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'..'.DS.'models.php' );

	/**
	 * La classe PostgresTableBehaviorTest ...
	 *
	 * @package Postgres
	 * @subpackage Test.Case.Model.Behavior
	 */
	class PostgresTableBehaviorTest extends CakeTestCase
	{
		/**
		 *
		 * @var Model
		 */
		public $User = null;

		/**
		 * Fixtures utilisés par ces tests unitaires.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'plugin.Postgres.PostgresGroup',
			'plugin.Postgres.PostgresUser',
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			$this->User = ClassRegistry::init(
				array(
					'class' => 'Postgres.PostgresUser',
					'alias' => 'User',
					'ds' => 'test',
				)
			);
			// INFO: à cause de testSetup()
//			$this->User->getDataSource()->config['datasource'] = 'Database/Postgres';

			$this->User->Behaviors->attach( 'Postgres.PostgresTable' );
			$this->User->Group->Behaviors->attach( 'Postgres.PostgresTable' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->User );
			parent::tearDown();
		}

		/**
		 * Test de la méthode PostgresTableBehavior::getPostgresCheckConstraints()
		 */
		public function testGetPostgresCheckConstraints() {
			$result = $this->User->getPostgresCheckConstraints();
			$expected = array(
				array(
					'Constraint' => array(
						'schema' => 'public',
						'table' => 'postgres_users',
						'name' => 'postgres_users_active_in_list_chk',
						'clause' => 'cakephp_validate_in_list(active, ARRAY[0, 1])'
					)
				),
				array(
					'Constraint' => array(
						'schema' => 'public',
						'table' => 'postgres_users',
						'name' => 'postgres_users_phone_phone_chk',
						'clause' => 'cakephp_validate_phone((phone)::text, NULL::text, \'fr\'::text)'
					)
				),
				array(
					'Constraint' => array(
						'schema' => 'public',
						'table' => 'postgres_users',
						'name' => 'postgres_users_popularity_inclusive_range_chk',
						'clause' => 'cakephp_validate_inclusive_range((popularity)::double precision, (0)::double precision, (10)::double precision)'
					)
				),
				array(
					'Constraint' => array(
						'schema' => 'public',
						'table' => 'postgres_users',
						'name' => 'postgres_users_position_in_list_chk',
						'clause' => 'cakephp_validate_in_list(("position")::text, ARRAY[\'in line\'::text, \'out of line\'::text])',
					)
				)
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode PostgresTableBehavior::getPostgresForeignKeysFrom()
		 */
		public function testPostgresForeignKeysFrom() {
			$result = $this->User->getPostgresForeignKeysFrom();
			$expected = array(
				'postgres_users_group_id_fk' => array(
					'Foreignkey' => array(
						'name' => 'postgres_users_group_id_fk',
						'onupdate' => 'NO ACTION',
						'ondelete' => 'NO ACTION'
					),
					'From' => array(
						'schema' => 'public',
						'table' => 'postgres_users',
						'column' => 'group_id',
						'nullable' => false,
						'unique' => false
					),
					'To' => array(
						'schema' => 'public',
						'table' => 'postgres_groups',
						'column' => 'id',
						'nullable' => false,
						'unique' => true
					)
				)
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode PostgresTableBehavior::getPostgresForeignKeysTo()
		 */
		public function testPostgresForeignKeysTo() {
			$result = $this->User->Group->getPostgresForeignKeysTo();
			$expected = array(
				'postgres_users_group_id_fk' => array(
					'Foreignkey' => array(
						'name' => 'postgres_users_group_id_fk',
						'onupdate' => 'NO ACTION',
						'ondelete' => 'NO ACTION'
					),
					'From' => array(
						'schema' => 'public',
						'table' => 'postgres_users',
						'column' => 'group_id',
						'nullable' => false,
						'unique' => false
					),
					'To' => array(
						'schema' => 'public',
						'table' => 'postgres_groups',
						'column' => 'id',
						'nullable' => false,
						'unique' => true
					)
				)
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode PostgresTableBehavior::getPostgresForeignKeys()
		 */
		public function testPostgresForeignKeys() {
			$result = $this->User->Group->getPostgresForeignKeys();
			$expected = array(
				'to' => array(
					'postgres_users_group_id_fk' => array(
						'Foreignkey' => array(
							'name' => 'postgres_users_group_id_fk',
							'onupdate' => 'NO ACTION',
							'ondelete' => 'NO ACTION'
						),
						'From' => array(
							'schema' => 'public',
							'table' => 'postgres_users',
							'column' => 'group_id',
							'nullable' => false,
							'unique' => false
						),
						'To' => array(
							'schema' => 'public',
							'table' => 'postgres_groups',
							'column' => 'id',
							'nullable' => false,
							'unique' => true
						)
					)
				)
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode PostgresTableBehavior::getAllPostgresForeignKeys()
		 */
		public function testGetAllPostgresForeignKeys() {
			$result = $this->User->getAllPostgresForeignKeys();
			$expected = array(
				'postgres_users' => array(
					'from' => array(
						'postgres_users_group_id_fk' => array(
							'Foreignkey' => array(
								'name' => 'postgres_users_group_id_fk',
								'onupdate' => 'NO ACTION',
								'ondelete' => 'NO ACTION'
							),
							'From' => array(
								'schema' => 'public',
								'table' => 'postgres_users',
								'column' => 'group_id',
								'nullable' => false,
								'unique' => false
							),
							'To' => array(
								'schema' => 'public',
								'table' => 'postgres_groups',
								'column' => 'id',
								'nullable' => false,
								'unique' => true
							)
						)
					)
				),
				'postgres_groups' => array(
					'to' => array(
						'postgres_users_group_id_fk' => array(
							'Foreignkey' => array(
								'name' => 'postgres_users_group_id_fk',
								'onupdate' => 'NO ACTION',
								'ondelete' => 'NO ACTION'
							),
							'From' => array(
								'schema' => 'public',
								'table' => 'postgres_users',
								'column' => 'group_id',
								'nullable' => false,
								'unique' => false
							),
							'To' => array(
								'schema' => 'public',
								'table' => 'postgres_groups',
								'column' => 'id',
								'nullable' => false,
								'unique' => true
							)
						)
					)
				)
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>