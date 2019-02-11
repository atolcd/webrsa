<?php
/**
 * Code source de la classe MonmodelFixture.
 *
 * PHP 5.3
 *
 * @package DisplayValidationErrors
 * @subpackage Test.Fixture
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe MonmodelFixture ...
 *
 * @package DisplayValidationErrors
 * @subpackage Test.Fixture
 */
class MonmodelFixture extends CakeTestFixture
{
	/**
	 * table property
	 *
	 * @var string
	 */
	public $table = 'monmodel';

	/**
	 * fields property
	 *
	 * @var array
	 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type' => 'string'),
		'fk' => array('type' => 'integer'),
	);

	/**
	 * Définition des enregistrements.
	 *
	 * @var array
	 */
	public $records = array();
}