<?php
	/**
	 * Code source de la classe Typecontrat.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Typecontrat ...
	 *
	 * @package app.Model
	 */
	class Typecontrat extends AppModel
	{
		public $name = 'Typecontrat';
		public $useTable = 'typescontrats';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes'
		);

		public $hasMany = array(
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'type_contrat_travail',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

	}