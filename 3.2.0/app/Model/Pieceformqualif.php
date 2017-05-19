<?php
	/**
	 * Code source de la classe Pieceformqualif.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Pieceformqualif ...
	 *
	 * @package app.Model
	 */
	class Pieceformqualif extends AppModel
	{
		public $name = 'Pieceformqualif';

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
