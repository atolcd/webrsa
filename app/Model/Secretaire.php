<?php	
	/**
	 * Code source de la classe Secretaire.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Secretaire ...
	 *
	 * @package app.Model
	 */
	class Secretaire extends User
	{
		public $name = 'Secretaire';

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