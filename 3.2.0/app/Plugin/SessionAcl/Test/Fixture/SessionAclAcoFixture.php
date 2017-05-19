<?php
/**
 * Code source de la classe SessionAclAcoFixture.
 *
 * PHP 5.3
 *
 * @package SessionAcl
 * @subpackage Test.Fixture
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe SessionAclAcoFixture ...
 *
 * @package SessionAcl
 * @subpackage Test.Fixture
 */
class SessionAclAcoFixture extends CakeTestFixture
{
	/**
	 * table property
	 *
	 * @var string
	 */
	public $table = 'sessionacl_acos';

	/**
	 * fields property
	 *
	 * @var array
	 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'parent_id' => array('type' => 'integer'),
		'model' => array('type' => 'string'),
		'foreign_key' => array('type' => 'integer'),
		'alias' => array('type' => 'string'),
		'lft' => array('type' => 'integer'),
		'rght' => array('type' => 'integer'),
	);

	/**
	 * Définition des enregistrements.
	 *
	 * @var array
	 */
	public $records = array(
		1 => array(
			'parent_id' => null,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'controllers',
			'lft' => '1',
			'rght' => '14',
		),
		2 => array(
			'parent_id' => null,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'modeles',
			'lft' => '15',
			'rght' => '20',
		),
		3 => array(
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Groups',
			'lft' => '2',
			'rght' => '7',
		),
		4 => array(
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Users',
			'lft' => '8',
			'rght' => '13',
		),
		5 => array(
			'parent_id' => 3,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'add',
			'lft' => '3',
			'rght' => '4',
		),
		6 => array(
			'parent_id' => 3,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'edit',
			'lft' => '5',
			'rght' => '6',
		),
		7 => array(
			'parent_id' => 4,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'add',
			'lft' => '9',
			'rght' => '10',
		),
		8 => array(
			'parent_id' => 4,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'edit',
			'lft' => '11',
			'rght' => '12',
		),
		9 => array(
			'parent_id' => 2,
			'model' => 'Group',
			'foreign_key' => 1,
			'alias' => 'Admin',
			'lft' => '16',
			'rght' => '17',
		),
		9 => array(
			'parent_id' => 2,
			'model' => 'User',
			'foreign_key' => 1,
			'alias' => 'User1',
			'lft' => '18',
			'rght' => '19',
		),
	);
}