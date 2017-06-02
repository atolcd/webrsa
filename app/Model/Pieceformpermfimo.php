<?php
	/**
	 * Code source de la classe Pieceformpermfimo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Pieceformpermfimo ...
	 *
	 * @package app.Model
	 */
	class Pieceformpermfimo extends AppModel
	{
		public $name = 'Pieceformpermfimo';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'libelle';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
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
