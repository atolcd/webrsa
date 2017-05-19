<?php
	/**
	 * Code source de la classe ConfigurableQueryUserFixture.
	 *
	 * PHP 5.3
	 *
	 * @package ConfigurableQuery
	 * @subpackage Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ConfigurableQueryUserFixture ...
	 *
	 * @package ConfigurableQuery
	 * @subpackage Test.Fixture
	 */
	class ConfigurableQueryUserFixture extends CakeTestFixture
	{
		/**
		 * name property
		 *
		 * @var string 'ConfigurableQuerySite'
		 * @access public
		 */
		public $name = 'ConfigurableQueryUser';

		/**
		 * table property
		 *
		 * @var string
		 */
		public $table = 'configurable_query_users';

		/**
		 * fields property
		 *
		 * @var array
		 * @access public
		 */
		public $fields = array(
			'id' => array( 'type' => 'integer', 'key' => 'primary' ),
			'group_id' => array( 'type' => 'integer', 'null' => false ),
			'username' => array( 'type' => 'string', 'length' => 50, 'null' => false ),
			'password' => array( 'type' => 'string', 'length' => 50, 'null' => false ),
			'created' => 'datetime',
			'modified' => 'datetime',
			'indexes' => array(
				'configurable_query_users_group_id_idx' => array(
					'column' => array( 'group_id' ),
					'unique' => 0
				),
				'configurable_query_users_username_idx' => array(
					'column' => array( 'username' ),
					'unique' => 1
				)
			)
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				//'id' => null,
				'group_id' => 1,
				'username' => 'admin',
				'password' => 'admin',
				'created' => '2015-06-29 00:28:35',
				'modified' => '2015-06-29 00:28:35'
			)
		);
	}
?>