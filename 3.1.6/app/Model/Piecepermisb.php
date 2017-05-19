<?php	
	/**
	 * Code source de la classe Piecepermisb.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Piecepermisb ...
	 *
	 * @package app.Model
	 */
	class Piecepermisb extends AppModel
	{
		public $name = 'Piecepermisb';

		public $displayField = 'libelle';

		public $validate = array(
			'libelle' => array(
				array(
					'rule' => 'isUnique',
					'message' => 'Cette valeur est déjà utilisée'
				),
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			)
		);

		public $hasAndBelongsToMany = array(
			'Permisb' => array(
				'className' => 'Permisb',
				'joinTable' => 'permisb_piecespermisb',
				'foreignKey' => 'piecepermisb_id',
				'associationForeignKey' => 'permisb_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'PermisbPiecepermisb'
			)
		);
	}
?>
