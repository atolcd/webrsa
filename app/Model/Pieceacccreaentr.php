<?php	
	/**
	 * Code source de la classe Pieceacccreaentr.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Pieceacccreaentr ...
	 *
	 * @package app.Model
	 */
	class Pieceacccreaentr extends AppModel
	{
		public $name = 'Pieceacccreaentr';

		public $displayField = 'libelle';

		public $order = array( 'Pieceacccreaentr.libelle ASC' );

		public $hasAndBelongsToMany = array(
			'Acccreaentr' => array(
				'className' => 'Acccreaentr',
				'joinTable' => 'accscreaentr_piecesaccscreaentr',
				'foreignKey' => 'pieceacccreaentr_id',
				'associationForeignKey' => 'acccreaentr_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'AcccreaentrPieceacccreaentr'
			)
		);

		public $validate = array(
			'libelle' => array(
				array(
					'rule' => 'isUnique',
					'message' => 'Cette valeur est déjà utilisée'
				),
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
			),
		);
	}
?>
