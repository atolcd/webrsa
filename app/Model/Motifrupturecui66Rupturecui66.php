<?php
/**
 * Code source de la classe Motifrupturecui66Rupturecui66.
 *
 * PHP 5.3
 *
 * @package app.Model
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses( 'AppModel', 'Model' );

/**
 * La classe Motifrupturecui66Rupturecui66 ...
 *
 * @package app.Model
 */
class Motifrupturecui66Rupturecui66 extends AppModel
{
    public $name = 'Motifrupturecui66Rupturecui66';

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
        'Motifrupturecui66' => array(
            'className' => 'Motifrupturecui66',
            'foreignKey' => 'motifrupturecui66_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Rupturecui66' => array(
            'className' => 'Rupturecui66',
            'foreignKey' => 'rupturecui66_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}
?>