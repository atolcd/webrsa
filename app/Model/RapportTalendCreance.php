<?php
	/**
	 * Code source de la classe RapportTalendCreance.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe RapportTalendCreance ...
	 *
	 * @package app.Model
	 */
	class RapportTalendCreance extends AppModel
	{
		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'RapportsTalendsCreances';

		/**
		 * Nom.
		 *
		 * @var string
		 */
		 public $name = 'RapportTalendCreance';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $useDbConfig = 'log';

	}
		?>