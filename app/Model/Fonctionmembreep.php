<?php	
	/**
	 * Code source de la classe Fonctionmembreep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Fonctionmembreep ...
	 *
	 * @package app.Model
	 */
	class Fonctionmembreep extends AppModel
	{
		public $name = 'Fonctionmembreep';

		public $actsAs = array(
			'Autovalidate2',
			'ValidateTranslate',
			'Formattable'
		);

		public $hasMany = array(
			'Membreep' => array(
				'className' => 'Membreep',
				'foreignKey' => 'fonctionmembreep_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Compositionregroupementep' => array(
				'className' => 'Compositionregroupementep',
				'foreignKey' => 'fonctionmembreep_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

		public $validate = array(
			'name' => array(
				array(
					'rule' => array( 'isUnique' )
				)
			)
		);
	}
?>