<?php
	/**
	 * Code source de la classe Textmailcui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Textmailcui66 ...
	 *
	 * @package app.Model
	 */
	class Textmailcui66 extends AppModel
	{
		public $name = 'Textmailcui66';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

        /**
         * Associations "Has Many".
         * @var array
         */
        public $hasMany = array();

	}
?>