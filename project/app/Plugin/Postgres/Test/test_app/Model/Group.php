<?php
	/**
	 * Code source de la classe Group.
	 *
	 * PHP 5.3
	 *
	 * @package Postgres
	 * @subpackage Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Group ...
	 *
	 * @package Postgres
	 * @subpackage Model
	 */
	class Group extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Group';

		/**
		 * Table utilisée.
		 *
		 * @var string
		 */
		public $useTable = 'postgres_groups';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'group_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
		);
	}
?>