<?php
	/**
	 * Code source de la classe Personnepcg66Statutpdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Personnepcg66Statutpdo ...
	 *
	 * @package app.Model
	 */
	class Personnepcg66Statutpdo extends AppModel
	{
		public $name = 'Personnepcg66Statutpdo';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Personnepcg66' => array(
				'className' => 'Personnepcg66',
				'foreignKey' => 'personnepcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Statutpdo' => array(
				'className' => 'Statutpdo',
				'foreignKey' => 'statutpdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

	}
?>
