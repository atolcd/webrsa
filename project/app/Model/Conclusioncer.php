<?php
	/**
	 * Code source de la classe Conclusioncer.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Conclusioncer ...
	 *
	 * @package app.Model
	 */
	class Conclusioncer extends AppModel
	{
		public $name = 'Conclusioncer';
		public $useTable = 'conclusioncer';

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
				'foreignKey' => 'action_conclusion',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

	}