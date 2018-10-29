<?php
/**
 * Code source de la classe EtattitrecreancierTitrecreancier
 *
 * PHP 5.3
 *
 * @package app.Model
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses( 'AppModel', 'Model' );

/**
 * La classe EtattitrecreancierTitrecreancier ...
 *
 * @package app.Model
 */
class EtattitrecreancierTitrecreancier extends AppModel
{
    public $name = 'EtattitrecreancierTitrecreancier';

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
        'Etattitrecreancier' => array(
            'className' => 'Etattitrecreancier',
            'foreignKey' => 'etattitrecreancier_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Titrecreancier' => array(
            'className' => 'Titrecreancier',
            'foreignKey' => 'titrecreancier_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}
?>