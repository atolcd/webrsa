<?php
	/**
	 * Code source de la classe Textemail.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Textemail ...
	 *
	 * @package app.Model
	 */
	class Textemail extends AppModel
	{
		/**
		 * Nom du modèle
		 *
		 * @var string
		 *
		 */
		public $name = 'Textemail';

		/**
		 * Table utilisée par le modèle
		 *
		 * @var string
		 *
		 */
		public $useTable = 'textsemails';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 *
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