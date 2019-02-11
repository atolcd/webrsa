<?php
/**
 * Code source de la classe SessionAclGroupFixture.
 *
 * PHP 5.3
 *
 * @package SessionAcl
 * @subpackage Test.Fixture
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe SessionAclGroupFixture ...
 *
 * @package SessionAcl
 * @subpackage Test.Fixture
 */
class SessionAclGroupFixture extends CakeTestFixture
{
	/**
	 * table property
	 *
	 * @var string
	 */
	public $table = 'sessionacl_groups';

	/**
	 * fields property
	 *
	 * @var array
	 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type' => 'string'),
		'parent_id' => array('type' => 'integer'),
	);

	/**
	 * Définition des enregistrements.
	 *
	 * @var array
	 */
	public $records = array(
		array(
			'name' => 'Admin',
			'parent_id' => null
		),
		array(
			'name' => 'Sub-Admin',
			'parent_id' => 1
		),
	);
}