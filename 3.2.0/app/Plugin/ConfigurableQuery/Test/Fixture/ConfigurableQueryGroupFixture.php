<?php
	/**
	 * Code source de la classe ConfigurableQueryGroupFixture.
	 *
	 * PHP 5.3
	 *
	 * @package ConfigurableQuery
	 * @subpackage Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ConfigurableQueryGroupFixture ...
	 *
	 * @package ConfigurableQuery
	 * @subpackage Test.Fixture
	 */
	class ConfigurableQueryGroupFixture extends CakeTestFixture
	{
		/**
		 * name property
		 *
		 * @var string 'ConfigurableQuerySite'
		 * @access public
		 */
		public $name = 'ConfigurableQueryGroup';

		/**
		 * table property
		 *
		 * @var string
		 */
		public $table = 'configurable_query_groups';

		/**
		 * fields property
		 *
		 * @var array
		 * @access public
		 */
		public $fields = array(
			'id' => array( 'type' => 'integer', 'key' => 'primary' ),
			'name' => array( 'type' => 'string', 'length' => 50, 'null' => false ),
			'created' => 'datetime',
			'modified' => 'datetime',
			'indexes' => array(
				'configurable_query_groups_name_idx' => array(
					'column' => array( 'name' ),
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
				'name' => 'Admin',
				'created' => '2015-06-29 00:28:35',
				'modified' => '2015-06-29 00:28:35'
			)
		);
	}
?>