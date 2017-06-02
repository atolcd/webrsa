<?php
	/**
	 * Code source de la classe Modeletypecourrierpcg66Situationpdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Modeletypecourrierpcg66Situationpdo ...
	 *
	 * @package app.Model
	 */
	class Modeletypecourrierpcg66Situationpdo extends AppModel
	{
		public $name = 'Modeletypecourrierpcg66Situationpdo';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Modeletypecourrierpcg66' => array(
				'className' => 'Modeletypecourrierpcg66',
				'foreignKey' => 'modeletypecourrierpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Situationpdo' => array(
				'className' => 'Situationpdo',
				'foreignKey' => 'situationpdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);
	}
?>