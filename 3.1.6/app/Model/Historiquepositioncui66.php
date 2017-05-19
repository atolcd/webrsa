<?php
	/**
	 * Fichier source de la classe Historiquepositioncui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Historiquepositioncui66 est la classe contenant les e-mails du CUI.
	 *
	 * @package app.Model
	 */
	class Historiquepositioncui66 extends AppModel
	{
		public $name = 'Historiquepositioncui66';
		
		public $recursive = -1;
		
		public $belongsTo = array(
			'Cui66' => array(
				'className' => 'Cui66',
				'foreignKey' => 'cui66_id',
				'dependent' => true,
			),
		);
	}
?>