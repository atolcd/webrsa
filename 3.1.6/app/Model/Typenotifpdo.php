<?php	
	/**
	 * Code source de la classe Typenotifpdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Typenotifpdo ...
	 *
	 * @package app.Model
	 */
	class Typenotifpdo extends AppModel
	{
		public $name = 'Typenotifpdo';

		public $displayField = 'libelle';

		public $order = 'Typenotifpdo.id ASC';

		public $validate = array(
			'libelle' => array(
				array(
						'rule' => 'notEmpty',
						'message' => 'Champ obligatoire'
				),
				array(
						'rule' => 'isUnique',
						'message' => 'Valeur déjà utilisée'
				),
			),
			'modelenotifpdo' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
			)
		);

		public $hasMany = array(
			'Propopdo' => array(
				'className' => 'Propopdo',
				'foreignKey' => 'typenotifpdo_id',
				'dependent' => false,
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
	}
?>