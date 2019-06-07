<?php
	/**
	 * Code source de la classe Typetitrecreancierautreinfo.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Typetitrecreancierautreinfo ...
	 *
	 * @package app.Model
	 */
	class Typetitrecreancierautreinfo extends AppModel
	{
		public $name = 'Typetitrecreancierautreinfo';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'typestitrescreanciersautresinfos';

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
			'Titresuiviautreinfo' => array(
				'className' => 'Titresuiviautreinfo',
				'foreignKey' => 'typesautresinfos_id',
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
