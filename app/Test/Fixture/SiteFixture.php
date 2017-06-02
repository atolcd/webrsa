<?php
	/**
	 * Source file for the SiteFixture fixture class.
	 *
	 * PHP 5.3
	 *
	 * @package       app.Test.Fixture
	 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
	 */

	/**
	 * SiteFixture fixture class.
	 *
	 * @package       app.Test.Fixture
	 */
	class SiteFixture extends CakeTestFixture
	{

		/**
		 * name property
		 *
		 * @var string 'Aco'
		 */
		public $name = 'SiteFixture';

		public $table = 'sites';

		/**
		 * fields property
		 *
		 * @var array
		 */
		public $fields = array(
			'id' => array( 'type' => 'integer', 'key' => 'primary' ),
			'name' => array( 'type' => 'string', 'length' => 250, 'null' => false ),
			'price' => array( 'type' => 'float', 'null' => true ),
			'birthday' => array( 'type' => 'date', 'null' => true ),
			'indexes' => array(
				'site_name_idx' => array( 'unique' => true, 'column' => array( 'name' ) ),
			),
		);

		/**
		 * records property
		 *
		 * @var array
		 */
		public $records = array(
			array( 'name' => 'gwoo' ),
		);

	}
?>