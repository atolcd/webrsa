<?php
	/**
	 * Code source de la classe Autreavissuspension.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Autreavissuspension ...
	 *
	 * @package app.Model
	 */
	class Autreavissuspension extends AppModel
	{
		public $name = 'Autreavissuspension';

		public $recursive = -1;

		public $actsAs = array(
			'Enumerable' => array(
				'fields' => array(
					'autreavissuspension'
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