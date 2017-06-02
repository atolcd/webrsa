<?php	
	/**
	 * Code source de la classe Libsecactderact66Secteur.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Libsecactderact66Secteur ...
	 *
	 * @package app.Model
	 */
	class Libsecactderact66Secteur extends AppModel
	{
		public $name = 'Libsecactderact66Secteur';

		public $displayField = 'intitule';

		public $useTable = 'codesromesecteursdsps66';

		public $virtualFields = array(
			'intitule' => array(
				'type'      => 'string',
				'postgres'  => '( "%s"."code" || \'. \' || "%s"."name" )'
			),
		);
	}
?>