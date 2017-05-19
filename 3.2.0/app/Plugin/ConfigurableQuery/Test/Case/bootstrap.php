<?php
	/**
	 * Mock models file
	 *
	 * Mock classes for use in Model and related test cases
	 *
	 * @package ConfigurableQuery
	 * @subpackage Test.Case.Model
	 */
	App::uses( 'Model', 'Model' );

	/**
	 * AppModel class
	 *
	 * @package ConfigurableQuery
	 * @subpackage Test.Case.Model
	 */
	class AppModel extends Model
	{
	}

	class ConfigurableQueryGroup extends AppModel
	{
		public $hasMany = array(
			'User' => array(
				'className' => 'ConfigurableQueryUser',
				'foreignKey' => 'group_id',
				'dependent' => true,
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'exclusive' => null,
				'finderQuery' => null,
				'counterQuery' => null
			)
		);
	}

	class ConfigurableQueryUser extends AppModel
	{
		public $virtualFields = array(
			'id_minus_1' => 'id - 1'
		);

		public $belongsTo = array(
			'Group' => array(
				'className' => 'ConfigurableQueryGroup',
				'foreignKey' => 'group_id',
				'conditions' => null,
				'fields' => null,
				'order' => null
			)
		);
	}
?>