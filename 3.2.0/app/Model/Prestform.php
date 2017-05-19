<?php
	/**
	 * Code source de la classe Prestform.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Prestform ...
	 *
	 * @package app.Model
	 */
	class Prestform extends AppModel
	{
		public $name = 'Prestform';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Allocatairelie' => array(
				'joins' => array( 'Actioninsertion', 'Contratinsertion' )
			),
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Actioninsertion' => array(
				'className' => 'Actioninsertion',
				'foreignKey' => 'actioninsertion_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Refpresta' => array(
				'className' => 'Refpresta',
				'foreignKey' => 'refpresta_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>