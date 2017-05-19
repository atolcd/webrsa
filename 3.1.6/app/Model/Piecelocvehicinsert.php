<?php	
	/**
	 * Code source de la classe Piecelocvehicinsert.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Piecelocvehicinsert ...
	 *
	 * @package app.Model
	 */
	class Piecelocvehicinsert extends AppModel
	{
		public $name = 'Piecelocvehicinsert';

		public $displayField = 'libelle';

		public $order = array( 'Piecelocvehicinsert.libelle ASC' );

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
			'Locvehicinsert' => array(
				'className' => 'Locvehicinsert',
				'joinTable' => 'locsvehicinsert_pieceslocsvehicinsert',
				'foreignKey' => 'piecelocvehicinsert_id',
				'associationForeignKey' => 'locvehicinsert_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'LocvehicinsertPiecelocvehicinsert'
			)
		);

	}
?>
