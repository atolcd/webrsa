<?php
	/**
	 * Code source de la classe Libsecactderact66SecteurDspRev.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Libsecactderact66SecteurDspRev ...
	 *
	 * @package app.Model
	 */
	class Libsecactderact66SecteurDspRev extends AppModel
	{
		public $name = 'Libsecactderact66SecteurDspRev';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $useTable = 'codesromesecteursdsps66';

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