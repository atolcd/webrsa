<?php
	/**
	 * Code source de la classe Rapporttalendcreance.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Rapporttalendcreance ...
	 *
	 * @package app.Model
	 */
	class Rapporttalendcreance extends AppModel
	{

		/**
		 * Nom.
		 *
		 * @var string
		 */
		 public $name = 'Rapporttalendcreance';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $useDbConfig = 'log';

	}
		?>