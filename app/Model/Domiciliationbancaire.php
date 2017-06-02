<?php
	/**
	 * Code source de la classe Domiciliationbancaire.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Domiciliationbancaire ...
	 *
	 * @package app.Model
	 */
	class Domiciliationbancaire extends AppModel
	{
		public $name = 'Domiciliationbancaire';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;
	}
?>