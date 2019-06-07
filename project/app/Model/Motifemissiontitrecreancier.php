<?php
	/**
	 * Code source de la classe Motifemissiontitrecreancier.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Motifemissiontitrecreancier ...
	 *
	 * @package app.Model
	 */
	class Motifemissiontitrecreancier extends AppModel
	{
		public $name = 'Motifemissiontitrecreancier';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'motifsemissionstitrescreanciers';

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
	}
