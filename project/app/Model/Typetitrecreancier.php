<?php
	/**
	 * Code source de la classe Typetitrecreancier.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Typetitrecreancier ...
	 *
	 * @package app.Model
	 */
	class Typetitrecreancier extends AppModel
	{
		public $name = 'Typetitrecreancier';

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
			'Titrecreancier' => array(
				'className' => 'Titrecreancier',
				'foreignKey' => 'typetitrecreancier_id',
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
