<?php	
	/**
	 * Code source de la classe Pieceformpermfimo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Pieceformpermfimo ...
	 *
	 * @package app.Model
	 */
	class Pieceformpermfimo extends AppModel
	{
		public $name = 'Pieceformpermfimo';

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
			'Formpermfimo' => array(
				'className' => 'Formpermfimo',
				'joinTable' => 'formspermsfimo_piecesformspermsfimo',
				'foreignKey' => 'pieceformpermfimo_id',
				'associationForeignKey' => 'formpermfimo_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'FormpermfimoPieceformpermfimo'
			)
		);

	}
?>
