<?php
	/**
	 * Code source de la classe Pieceacccreaentr.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Pieceacccreaentr ...
	 *
	 * @package app.Model
	 */
	class Pieceacccreaentr extends AppModel
	{
		public $name = 'Pieceacccreaentr';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'libelle';

		public $order = array( 'Pieceacccreaentr.libelle ASC' );

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

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
	}
?>
