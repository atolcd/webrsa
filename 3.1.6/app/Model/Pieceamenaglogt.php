<?php	
	/**
	 * Code source de la classe Pieceamenaglogt.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Pieceamenaglogt ...
	 *
	 * @package app.Model
	 */
	class Pieceamenaglogt extends AppModel
	{
		public $name = 'Pieceamenaglogt';

		public $displayField = 'libelle';

		public $order = array( 'Pieceamenaglogt.libelle ASC' );

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
			'Amenaglogt' => array(
				'className' => 'Amenaglogt',
				'joinTable' => 'amenagslogts_piecesamenagslogts',
				'foreignKey' => 'pieceamenaglogt_id',
				'associationForeignKey' => 'amenaglogt_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'AmenaglogtPieceamenaglogt'
			)
		);
	}
?>
