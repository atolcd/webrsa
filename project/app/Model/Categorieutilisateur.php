<?php
	/**
	 * Code source de la classe Categorieutilisateur.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Categorieutilisateur ...
	 *
	 * @package app.Model
	 */
	class Categorieutilisateur extends AppModel
	{
		public $name = 'Categorieutilisateur';
		public $useTable = 'categoriesutilisateurs';

		public $displayField = 'libelle';

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


	}