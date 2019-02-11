<?php
	/**
	 * Code source de la classe Refpresta.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Refpresta ...
	 *
	 * @package app.Model
	 */
	class Refpresta extends AppModel
	{
		public $name = 'Refpresta';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $hasMany = array(
			'Prestform' => array(
				'className' => 'Prestform',
				'foreignKey' => 'refpresta_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);
	}
?>