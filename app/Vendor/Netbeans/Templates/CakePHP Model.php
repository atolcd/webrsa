<#include "freemarker_functions.ftl">
<?php
	/**
	 * Code source de la classe ${name}.
	 *
<#if php_version??>
	 * PHP ${php_version}
	 *
</#if>
	 * @package app.Model
	 * @license ${license}
	 */

	/**
	 * La classe ${name} ...
	 *
	 * @package app.Model
	 */
	class ${name} extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = '${name}';

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
		public $actsAs = array();

		/**
		 * Associations "Has one".
		 *
		 * @var array
		 */
		public $hasOne = array(
			/*'' => array(
				'className' => '',
				'foreignKey' => '${foreign_key(name)}',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'dependent' => true
			),*/
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			/*'' => array(
				'className' => '',
				'foreignKey' => '_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),*/
		);

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			/*'' => array(
				'className' => '',
				'foreignKey' => '${foreign_key(name)}',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),*/
		);

		/**
		 * Associations "Has and belongs to many".
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
			/*'' => array(
				'className' => '',
				'joinTable' => '',
				'foreignKey' => '${foreign_key(name)}',
				'associationForeignKey' => '_id',
				'unique' => true,
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'finderQuery' => null,
				'deleteQuery' => null,
				'insertQuery' => null,
				'with' => null
			),*/
		);
	}
?>