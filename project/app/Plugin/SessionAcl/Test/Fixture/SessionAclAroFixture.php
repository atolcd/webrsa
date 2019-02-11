<?php
/**
 * Code source de la classe SessionAclAroFixture.
 *
 * PHP 5.3
 *
 * @package SessionAcl
 * @subpackage Test.Fixture
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe SessionAclAroFixture ...
 *
 * @package SessionAcl
 * @subpackage Test.Fixture
 */
class SessionAclAroFixture extends CakeTestFixture
{
	/**
	 * table property
	 *
	 * @var string
	 */
	public $table = 'sessionacl_aros';

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
		array(
			'parent_id' => null,
			'model' => 'SessionAclTestGroup',
			'foreign_key' => 1,
			'alias' => 'Admin',
			'lft' => '1',
			'rght' => '8',
		),
		array(
			'parent_id' => 1,
			'model' => 'SessionAclTestGroup',
			'foreign_key' => 2,
			'alias' => 'Sub-Admin',
			'lft' => '2',
			'rght' => '5',
		),
		array(
			'parent_id' => 1,
			'model' => 'SessionAclTestUser',
			'foreign_key' => 1,
			'alias' => 'User1',
			'lft' => '6',
			'rght' => '7',
		),
		array(
			'parent_id' => 2,
			'model' => 'SessionAclTestUser',
			'foreign_key' => 2,
			'alias' => 'User2',
			'lft' => '3',
			'rght' => '4',
		),
	);
}