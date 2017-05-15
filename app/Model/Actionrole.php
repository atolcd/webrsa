<?php
	/**
	 * Code source de la classe Actionrole.
	 *
	 * @package app.Model
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/CakePHP Model.php.
	 */

	/**
	 * La classe Actionrole ...
	 *
	 * @package app.Model
	 */
	class Actionrole extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Actionrole';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Formattable',
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
		);
		
		/**
		 * @var array
		 */
		public $belongsTo = array(
			'Role' => array(
				'className' => 'Role',
				'foreignKey' => 'role_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Categorieactionrole' => array(
				'className' => 'Categorieactionrole',
				'foreignKey' => 'categorieactionrole_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);
	}
?>