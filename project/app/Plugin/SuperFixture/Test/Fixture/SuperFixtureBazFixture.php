<?php
	/**
	 * Code source de la classe SuperFixtureBazFixture.
	 *
	 * PHP 5.3
	 *
	 * @package Postgres
	 * @subpackage Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe SuperFixtureBazFixture ...
	 *
	 * @package Postgres
	 * @subpackage Test.Fixture
	 */
	class SuperFixtureBazFixture extends CakeTestFixture
	{
		/**
		 * name property
		 *
		 * @var string 'PostgresSite'
		 * @access public
		 */
		public $name = 'SuperFixtureBaz';

		/**
		 * table property
		 *
		 * @var string
		 */
		public $table = 'super_fixture_bazs';

		/**
		 * fields property
		 *
		 * @var array
		 * @access public
		 */
		public $fields = array(
			'id' => array( 'type' => 'integer', 'key' => 'primary' ),
			'name' => array( 'type' => 'string', 'length' => 255, 'null' => false ),
			'created' => 'datetime',
			'updated' => 'datetime',
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array('name' => 'Baz'),
		);
	}
?>