<?php
	/**
	 * Code source de la classe Modeletraitementpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Modeletraitementpcg66 ...
	 *
	 * @package app.Model
	 */
	class Modeletraitementpcg66 extends AppModel
	{
		public $name = 'Modeletraitementpcg66';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);


		public $belongsTo = array(
			'Traitementpcg66' => array(
				'className' => 'Traitementpcg66',
				'foreignKey' => 'traitementpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Modeletypecourrierpcg66' => array(
				'className' => 'Modeletypecourrierpcg66',
				'foreignKey' => 'modeletypecourrierpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasAndBelongsToMany = array(
			'Piecemodeletypecourrierpcg66' => array(
				'className' => 'Piecemodeletypecourrierpcg66',
				'joinTable' => 'mtpcgs66_pmtcpcgs66',
				'foreignKey' => 'modeletraitementpcg66_id',
				'associationForeignKey' => 'piecemodeletypecourrierpcg66_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Mtpcg66Pmtcpcg66'
			)
		);
	}
?>