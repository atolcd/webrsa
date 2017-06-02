<?php
	/**
	 * Code source de la classe Chargeinsertion.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'User', 'Model' );

	/**
	 * La classe Chargeinsertion ...
	 *
	 * @package app.Model
	 */
	class Chargeinsertion extends User
	{
		public $name = 'Chargeinsertion';

		public $displayField = 'nom_complet';

		public $useTable = 'users';

		public $virtualFields = array(
			'nom_complet' => array(
				'type'      => 'string',
				'postgres'  => '( "%s"."qual" || \' \' || "%s"."nom" || \' \' || "%s"."prenom" )'
			)
		);
	}
?>