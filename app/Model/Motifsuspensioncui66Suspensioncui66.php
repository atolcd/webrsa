<?php
/**
 * Code source de la classe Motifsuspensioncui66Suspensioncui66.
 *
 * PHP 5.3
 *
 * @package app.Model
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses( 'AppModel', 'Model' );

/**
 * La classe Motifsuspensioncui66Suspensioncui66 ...
 *
 * @package app.Model
 */
class Motifsuspensioncui66Suspensioncui66 extends AppModel
{
    public $name = 'Motifsuspensioncui66Suspensioncui66';

	/**
	 * Récursivité par défaut du modèle.
	 *
	 * @var integer
	 */
	public $recursive = 1;

	/**
	 * Behaviors utilisés par le modèle.
	 *
	 * @var array
	 */
	public $actsAs = array(
		'Validation2.Validation2Formattable',
		'Validation2.Validation2RulesFieldtypes',
		'Postgres.PostgresAutovalidate'
	);

    public $belongsTo = array(
        'Motifsuspensioncui66' => array(
            'className' => 'Motifsuspensioncui66',
            'foreignKey' => 'motifsuspensioncui66_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Suspensioncui66' => array(
            'className' => 'Suspensioncui66',
            'foreignKey' => 'suspensioncui66_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}
?>