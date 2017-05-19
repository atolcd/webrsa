<?php	
	/**
	 * Code source de la classe Ajoutdossier.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Ajoutdossier ...
	 *
	 * @package app.Model
	 */
	class Ajoutdossier extends AppModel
	{
		public $name = 'Ajoutdossier';

		public $useTable = false;

		public $validate = array(
			'serviceinstructeur_id' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			)
		);
	}
?>