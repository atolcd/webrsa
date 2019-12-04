<?php
	/**
	 * Code source de la classe Creancerecoursgracieux.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Recourgracieux ...
	 *
	 * @package app.Model
	 */
	class Creancerecoursgracieux extends AppModel
	{
		public $name = 'Creancerecoursgracieux';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'creancesrecoursgracieux';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Associations "Belongs To".
		 * @var array
		 */
		public $belongsTo = array(
			'Recourgracieux' => array(
				'className' => 'Recourgracieux',
				'foreignKey' => 'recours_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Creance' => array(
				'className' => 'Creance',
				'foreignKey' => 'creances_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Motifproposrecoursgracieux' => array(
				'className' => 'Motifproposrecoursgracieux',
				'foreignKey' => 'motifproposrecoursgracieux_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);
	}
