<?php
	/**
	 * Code source de la classe Typeaction.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Typeaction ...
	 *
	 * @package app.Model
	 */
	class Typeaction extends AppModel
	{
		public $name = 'Typeaction';

		public $displayField = 'libelle';

		public $order = array( 'Typeaction.libelle ASC' );

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
			)
		);

		public $hasMany = array(
			'Action' => array(
				'className' => 'Action',
				'foreignKey' => 'typeaction_id',
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
	}
?>