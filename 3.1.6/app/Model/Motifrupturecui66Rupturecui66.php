<?php
/**
 * Code source de la classe Motifrupturecui66Rupturecui66.
 *
 * PHP 5.3
 *
 * @package app.Model
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe Motifrupturecui66Rupturecui66 ...
 *
 * @package app.Model
 */
class Motifrupturecui66Rupturecui66 extends AppModel
{
    public $name = 'Motifrupturecui66Rupturecui66';

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