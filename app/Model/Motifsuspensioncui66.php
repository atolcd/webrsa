<?php
/**
 * Code source de la classe Motifsuspensioncui66.
 *
 * PHP 5.3
 *
 * @package app.Model
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses( 'AppModel', 'Model' );

/**
 * La classe Motifsuspensioncui66 ...
 *
 * @package app.Model
 */
class Motifsuspensioncui66 extends AppModel
{
    public $name = 'Motifsuspensioncui66';

	/**
	 * Récursivité par défaut du modèle.
	 *
	 * @var integer
	 */
	public $recursive = 1;

    public $actsAs = array(
        'Postgres.PostgresAutovalidate',
        'Validation2.Validation2Formattable',
		'Validation2.Validation2RulesFieldtypes',
    );

    /**
     * Associations "Has And Belongs To Many".
     * @var array
     */
    public $hasAndBelongsToMany = array(
        'Suspensioncui66' => array(
            'className' => 'Suspensioncui66',
            'joinTable' => 'motifssuspensioncuis66_suspensionscuis66',
            'foreignKey' => 'motifsuspensioncui66_id',
            'associationForeignKey' => 'suspensioncui66_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => '',
            'with' => 'Motifsuspensioncui66Suspensioncui66'
        )
    );

}
?>