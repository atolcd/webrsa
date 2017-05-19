<?php
	/**
	 * Code source de la classe Ajoutdossier.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

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
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			)
		);
	}
?>