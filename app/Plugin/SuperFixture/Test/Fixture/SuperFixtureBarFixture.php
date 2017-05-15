<?php
	/**
	 * Code source de la classe SuperFixtureBarFixture.
	 *
	 * PHP 5.3
	 *
	 * @package Postgres
	 * @subpackage Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe SuperFixtureBarFixture ...
	 *
	 * @package Postgres
	 * @subpackage Test.Fixture
	 */
	class SuperFixtureBarFixture extends CakeTestFixture
	{
		/**
		 * name property
		 *
		 * @var string 'PostgresSite'
		 * @access public
		 */
		public $name = 'SuperFixtureBar';

		/**
		 * table property
		 *
		 * @var string
		 */
		public $table = 'super_fixture_bars';

		/**
		 * fields property
		 *
		 * @var array
		 * @access public
		 */
		public $fields = array(
			'id' => array( 'type' => 'integer', 'key' => 'primary' ),
			'name' => array( 'type' => 'string', 'length' => 255, 'null' => false ),
			'super_fixture_baz_id' => array( 'type' => 'integer', 'null' => false ),
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