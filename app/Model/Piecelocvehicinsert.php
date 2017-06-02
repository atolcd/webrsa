<?php
	/**
	 * Code source de la classe Piecelocvehicinsert.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Piecelocvehicinsert ...
	 *
	 * @package app.Model
	 */
	class Piecelocvehicinsert extends AppModel
	{
		public $name = 'Piecelocvehicinsert';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'libelle';

		public $order = array( 'Piecelocvehicinsert.libelle ASC' );

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
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
