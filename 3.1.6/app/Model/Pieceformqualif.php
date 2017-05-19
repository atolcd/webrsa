<?php	
	/**
	 * Code source de la classe Pieceformqualif.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Pieceformqualif ...
	 *
	 * @package app.Model
	 */
	class Pieceformqualif extends AppModel
	{
		public $name = 'Pieceformqualif';

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
			'Formqualif' => array(
				'className' => 'Formqualif',
				'joinTable' => 'formsqualifs_piecesformsqualifs',
				'foreignKey' => 'pieceformqualif_id',
				'associationForeignKey' => 'formqualif_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'FormqualifPieceformqualif'
			)
		);
	}
?>
