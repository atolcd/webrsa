<?php
	/**
	 * Code source de la classe Pieceacqmatprof.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Pieceacqmatprof ...
	 *
	 * @package app.Model
	 */
	class Pieceacqmatprof extends AppModel
	{
		public $name = 'Pieceacqmatprof';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'libelle';

		public $order = array( 'Pieceacqmatprof.libelle ASC' );

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

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
	}
?>
