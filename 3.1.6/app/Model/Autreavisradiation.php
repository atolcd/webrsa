<?php
	/**
	 * Code source de la classe Autreavisradiation.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Autreavisradiation ...
	 *
	 * @package app.Model
	 */
	class Autreavisradiation extends AppModel
	{
		public $name = 'Autreavisradiation';

		public $recursive = -1;

		public $actsAs = array(
			'Enumerable' => array(
				'fields' => array(
					'autreavisradiation'
				)
			),
			'Autovalidate2',
			'ValidateTranslate'
		);

		public $belongsTo = array(
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'contratinsertion_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>