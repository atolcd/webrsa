<?php
	/**
	 * Code source de la classe Libderact66Metier.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Libderact66Metier ...
	 *
	 * @package app.Model
	 */
	class Libderact66Metier extends AppModel
	{
		public $name = 'Libderact66Metier';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $useTable = 'codesromemetiersdsps66';

		public $displayField = 'intitule';

		public $virtualFields = array(
			'intitule' => array(
				'type'      => 'string',
				'postgres'  => '( "%s"."code" || \'. \' || "%s"."name" )'
			),
		);

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