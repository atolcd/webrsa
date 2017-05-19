<?php
	/**
	 * Fichier source de la classe Histoaprecomplementaire.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Histoaprecomplementaire ...
	 *
	 * @package app.Model
	 */
	class Histoaprecomplementaire extends AppModel
	{
		/**
		 * Le nom du modèle
		 *
		 * @var string
		 */
		public $name = 'Histoaprecomplementaire';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

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