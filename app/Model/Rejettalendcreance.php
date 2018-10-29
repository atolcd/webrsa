<?php
	/**
	 * Code source de la classe Rejettalendcreance.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Rejettalendcreance ...
	 *
	 * @package app.Model
	 */
	class Rejettalendcreance extends AppModel
	{

		/**
		 * Nom.
		 *
		 * @var string
		 */
		 public $name = 'Rejettalendcreance';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $useDbConfig = 'log';

	}
?>