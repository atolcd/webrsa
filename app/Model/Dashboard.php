<?php
	/**
	 * Code source de la classe Dashboard.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Dashboard ...
	 *
	 * @package app.Model
	 */
	class Dashboard extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Dashboard';

		/**
		 * Table utilisé par le modèle
		 *
		 * @var string
		 */
		public $useTable = false;

		/**
		 * Modèles utilisé par ce modèle
		 */
		public $uses = array(
			'WebrsaRecherche'
		);
	}
?>