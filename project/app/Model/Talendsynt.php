<?php
	/**
	 * Code source de la classe Talendsynt.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Talendsynt ...
	 *
	 * @package app.Model
	 */
	class Talendsynt extends AppModel
	{
		public $name = 'Talendsynt';

		/**
		 * Table utilisée par le modèle
		 *
		 * @var string
		 *
		 */
		public $useTable = 'talendsynt';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $useDbConfig = 'log';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Conditionnable',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);
	}
?>