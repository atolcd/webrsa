<?php
	/**
	 * Code source de la classe Rapporttalendmodescontact.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Rapporttalendmodescontact ...
	 *
	 * @package app.Model
	 */
	class Rapporttalendmodescontact extends AppModel
	{

		/**
		 * Nom.
		 *
		 * @var string
		 */
		 public $name = 'Rapporttalendmodescontact';

		 public $useTable = 'rapportstalendmodescontacts';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $useDbConfig = 'log';

	}
		?>