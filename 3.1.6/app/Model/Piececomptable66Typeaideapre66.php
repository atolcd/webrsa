<?php	
	/**
	 * Code source de la classe Piececomptable66Typeaideapre66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Piececomptable66Typeaideapre66 ...
	 *
	 * @package app.Model
	 */
	class Piececomptable66Typeaideapre66 extends AppModel
	{
		public $name = 'Piececomptable66Typeaideapre66';

		public $validate = array(
			'piececomptable66_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'typeaideapre66_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
		);

		public $belongsTo = array(
			'Piececomptable66' => array(
				'className' => 'Piececomptable66',
				'foreignKey' => 'piececomptable66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Typeaideapre66' => array(
				'className' => 'Typeaideapre66',
				'foreignKey' => 'typeaideapre66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>