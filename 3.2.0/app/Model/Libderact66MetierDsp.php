<?php
	/**
	 * Code source de la classe Libderact66MetierDsp.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Libderact66MetierDsp ...
	 *
	 * @package app.Model
	 */
	class Libderact66MetierDsp extends AppModel
	{
		public $name = 'Libderact66MetierDsp';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $useTable = 'codesromemetiersdsps66';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);
	}
?>