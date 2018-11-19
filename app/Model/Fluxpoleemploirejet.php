<?php
	/**
	 * Code source de la classe Fluxpoleemploirejet.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Fluxpoleemploirejet ...
	 *
	 * @package app.Model
	 */
	class Fluxpoleemploirejet extends AppModel
	{
		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'informationsperejets';

		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Fluxpoleemploirejet';

	}
?>