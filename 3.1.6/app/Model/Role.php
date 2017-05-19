<?php
	/**
	 * Code source de la classe Role.
	 *
	 * @package app.Model
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/CakePHP Model.php.
	 */

	/**
	 * La classe Role ...
	 *
	 * @package app.Model
	 */
	class Role extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Role';

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
		 * Ces models possèdent une clef étrangère vers ce model
		 * @var array
		 */
		public $hasMany = array(
			'RoleUser' => array(
				'className' => 'RoleUser',
				'foreignKey' => 'role_id',
				'dependent' => true,
			),
			'Actionrole' => array(
				'className' => 'Actionrole',
				'foreignKey' => 'role_id',
				'dependent' => true,
			),
		);
		
		/**
		 * Associations "Has and belongs to many".
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
			'User' => array(
				'className' => 'User',
				'joinTable' => 'roles_users',
				'foreignKey' => 'role_id',
				'associationForeignKey' => 'user_id',
				'unique' => true,
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'finderQuery' => null,
				'deleteQuery' => null,
				'insertQuery' => null,
				'with' => 'RoleUser'
			),
		);
	}
?>