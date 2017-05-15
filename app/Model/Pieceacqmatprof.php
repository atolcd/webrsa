<?php	
	/**
	 * Code source de la classe Pieceacqmatprof.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Pieceacqmatprof ...
	 *
	 * @package app.Model
	 */
	class Pieceacqmatprof extends AppModel
	{
		public $name = 'Pieceacqmatprof';

		public $displayField = 'libelle';

		public $order = array( 'Pieceacqmatprof.libelle ASC' );

		public $hasAndBelongsToMany = array(
			'Acqmatprof' => array(
				'className' => 'Acqmatprof',
				'joinTable' => 'acqsmatsprofs_piecesacqsmatsprofs',
				'foreignKey' => 'pieceacqmatprof_id',
				'associationForeignKey' => 'acqmatprof_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'AcqmatprofPieceacqmatprof'
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
