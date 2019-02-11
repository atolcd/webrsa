<?php
	/**
	 * Code source de la classe User.
	 *
	 * PHP 5.3
	 *
	 * @package Postgres
	 * @subpackage Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe User ...
	 *
	 * @package Postgres
	 * @subpackage Model
	 */
	class User extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'User';

		/**
		 * Table utilisée.
		 *
		 * @var string
		 */
		public $useTable = 'postgres_users';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Group' => array(
				'className' => 'Group',
				'foreignKey' => 'group_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);
	}
?>