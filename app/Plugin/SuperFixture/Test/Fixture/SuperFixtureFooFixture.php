<?php
	/**
	 * Code source de la classe SuperFixtureFooFixture.
	 *
	 * PHP 5.3
	 *
	 * @package Postgres
	 * @subpackage Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe SuperFixtureFooFixture ...
	 *
	 * @package Postgres
	 * @subpackage Test.Fixture
	 */
	class SuperFixtureFooFixture extends CakeTestFixture
	{
		/**
		 * name property
		 *
		 * @var string 'PostgresSite'
		 * @access public
		 */
		public $name = 'SuperFixtureFoo';

		/**
		 * table property
		 *
		 * @var string
		 */
		public $table = 'super_fixture_foos';

		/**
		 * fields property
		 *
		 * @var array
		 * @access public
		 */
		public $fields = array(
			'id' => array( 'type' => 'integer', 'key' => 'primary' ),
			'name' => array( 'type' => 'string', 'length' => 255, 'null' => false ),
			'super_fixture_bar_id' => array( 'type' => 'integer', 'null' => false ),
			'integer_field' => array('type' => 'integer', 'null' => false),
			'text_field' => array('type' => 'text', 'null' => false),
			'boolean_field' => array('type' => 'boolean', 'null' => false),
			'date_field' => array('type' => 'date', 'null' => false),
			'created' => 'datetime',
			'updated' => 'datetime',
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array();
	}
?>