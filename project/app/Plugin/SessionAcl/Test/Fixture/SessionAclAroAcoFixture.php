<?php
/**
 * Code source de la classe SessionAclAroAcoFixture.
 *
 * PHP 5.3
 *
 * @package SessionAcl
 * @subpackage Test.Fixture
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe SessionAclAroAcoFixture ...
 *
 * @package SessionAcl
 * @subpackage Test.Fixture
 */
class SessionAclAroAcoFixture extends CakeTestFixture
{
	/**
	 * table property
	 *
	 * @var string
	 */
	public $table = 'sessionacl_aros_acos';

	/**
	 * fields property
	 *
	 * @var array
	 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'aro_id' => array('type' => 'integer'),
		'aco_id' => array('type' => 'integer'),
		'_create' => array('type' => 'string'),
		'_read' => array('type' => 'string'),
		'_update' => array('type' => 'string'),
		'_delete' => array('type' => 'string'),
	);

	/**
	 * Définition des enregistrements.
	 *
	 * @var array
	 */
	public $records = array();
}