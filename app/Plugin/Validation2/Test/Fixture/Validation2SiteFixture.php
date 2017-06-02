<?php
	/**
	 * Short description for file.
	 *
	 * PHP version 5.3
	 *
	 * @package Validation2
	 * @subpackage Test.Fixture
	 */

	/**
	 * Short description for class.
	 *
	 * @package Validation2
	 * @subpackage Test.Fixture
	 */
	class Validation2SiteFixture extends CakeTestFixture
	{

		/**
		 * name property
		 *
		 * @var string 'Validation2Site'
		 * @access public
		 */
		public $name = 'Validation2Site';

		/**
		 * fields property
		 *
		 * @var array
		 * @access public
		 */
		public $fields = array(
			'id' => array( 'type' => 'integer', 'key' => 'primary' ),
			'name' => array( 'type' => 'string', 'length' => 255, 'null' => false ),
			'user_id' => array( 'type' => 'integer', 'null' => false ),
			'price' => array( 'type' => 'float', 'null' => true ),
			'published' => array( 'type' => 'boolean', 'null' => false ),
			'document' => array( 'type' => 'binary', 'null' => true ),
			'description' => 'text',
			'birthday' => 'date',
			'birthtime' => 'time',
			'created' => 'datetime',
			'updated' => 'datetime',
			'indexes' => array(
				'sites_name_idx' => array(
					'column' => array( 'name' ),
					'unique' => 1
				)
			)
		);

		/**
		 * records property
		 *
		 * @var array
		 * @access public
		 */
		public $records = array(
			array(
				'name' => 'CakePHP',
				'user_id' => 1,
				'published' => true,
				'created' => '2007-03-17 01:16:23',
				'updated' => '2007-03-17 01:18:31'
			),
		);

	}
?>