<?php
	/**
	 * Code source de la classe Typerecoursgracieux.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Typerecoursgracieux ...
	 *
	 * @package app.Model
	 */
	class Typerecoursgracieux extends AppModel
	{
		public $name = 'Typerecoursgracieux';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'typesrecoursgracieux';

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
			'Recourgracieux' => array(
				'className' => 'Recourgracieux',
				'foreignKey' => 'typerecoursgracieux_id',
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
