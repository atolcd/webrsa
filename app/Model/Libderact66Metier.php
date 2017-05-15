<?php	
	/**
	 * Code source de la classe Libderact66Metier.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Libderact66Metier ...
	 *
	 * @package app.Model
	 */
	class Libderact66Metier extends AppModel
	{
		public $name = 'Libderact66Metier';

		public $useTable = 'codesromemetiersdsps66';

		public $displayField = 'intitule';

		public $virtualFields = array(
			'intitule' => array(
				'type'      => 'string',
				'postgres'  => '( "%s"."code" || \'. \' || "%s"."name" )'
			),
		);
	}
?>