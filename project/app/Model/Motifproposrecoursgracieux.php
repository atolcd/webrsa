<?php
	/**
	 * Code source de la classe Motifproposrecoursgracieux.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Motifproposrecoursgracieux ...
	 *
	 * @package app.Model
	 */
	class Motifproposrecoursgracieux extends AppModel
	{
		public $name = 'Motifproposrecoursgracieux';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'motifsproposrecoursgracieux';

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
		 * Associations "Has Many".
		 * @var array
		 */
		public $hasMany = array(
			'Creancerecoursgracieux' => array(
				'className' => 'Creancerecoursgracieux',
				'foreignKey' => 'motifproposrecoursgracieux_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Indurecoursgracieux' => array(
				'className' => 'Indurecoursgracieux',
				'foreignKey' => 'motifproposrecoursgracieux_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

	}
