<?php
	/**
	 * Code source de la classe Pieceapre.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Pieceapre ...
	 *
	 * @package app.Model
	 */
	class Pieceapre extends AppModel
	{
		public $name = 'Pieceapre';

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
			'Apre' => array(
				'className' => 'Apre',
				'joinTable' => 'apres_piecesapre',
				'foreignKey' => 'pieceapre_id',
				'associationForeignKey' => 'apre_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'AprePieceapre'
			)
		);
	}
?>
