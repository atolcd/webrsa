<?php
	/**
	 * Code source de la classe LocvehicinsertPiecelocvehicinsert.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe LocvehicinsertPiecelocvehicinsert ...
	 *
	 * @package app.Model
	 */
	class LocvehicinsertPiecelocvehicinsert extends AppModel
	{
		public $name = 'LocvehicinsertPiecelocvehicinsert';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $validate = array(
			'locvehicinsert_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'piecelocvehicinsert_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
		);

		public $belongsTo = array(
			'Locvehicinsert' => array(
				'className' => 'Locvehicinsert',
				'foreignKey' => 'locvehicinsert_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Piecelocvehicinsert' => array(
				'className' => 'Piecelocvehicinsert',
				'foreignKey' => 'piecelocvehicinsert_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>