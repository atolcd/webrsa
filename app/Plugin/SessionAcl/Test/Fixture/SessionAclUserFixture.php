<?php
/**
 * Code source de la classe SessionAclUserFixture.
 *
 * PHP 5.3
 *
 * @package SessionAcl
 * @subpackage Test.Fixture
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe SessionAclUserFixture ...
 *
 * @package SessionAcl
 * @subpackage Test.Fixture
 */
class SessionAclUserFixture extends CakeTestFixture
{
	/**
	 * table property
	 *
	 * @var string
	 */
	public $table = 'sessionacl_users';

	/**
	 * fields property
	 *
	 * @var array
	 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'username' => array('type' => 'string'),
		'group_id' => array('type' => 'integer'),
	);

	/**
	 * Définition des enregistrements.
	 *
	 * @var array
	 */
	public $records = array(
		array(
			'username' => 'User1',
			'group_id' => 1
		),
		array(
			'username' => 'User2',
			'group_id' => 2
		),
	);
}