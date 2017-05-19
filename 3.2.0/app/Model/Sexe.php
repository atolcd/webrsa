<?php
	/**
	 * Code source de la classe Sexe.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Sexe ...
	 *
	 * @package app.Model
	 */
	class Sexe extends AppModel
	{
		public $name = 'Sexe';
		public $useTable = false;

		public $options = array(
			'1' => 'Homme',
			'2' => 'Femme'
		);
	}
?>