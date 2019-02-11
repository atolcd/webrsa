<?php
	/**
	 * Code source de la classe SessionAclTestGroup.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe SessionAclTestGroup ...
	 *
	 * @package app.Model
	 */
	class SessionAclTestGroup extends AppModel
	{
		public $alias = 'Group';
		
		public $useTable = 'sessionacl_groups';

		public $belongsTo = array(
			'ParentGroup' => array(
				'className' => 'SessionAclTestGroup',
				'foreignKey' => 'parent_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'ChildGroup' => array(
				'className' => 'SessionAclTestGroup',
				'foreignKey' => 'parent_id',
			),
			'User' => array(
				'className' => 'SessionAclTestUser',
				'foreignKey' => 'group_id',
			)
		);
		
		public $actsAs = array(
			'Acl' => array('type' => 'requester'),
		);
		
		public function parentNode() {
			if (!$this->id && empty($this->data)) {
				return null;
			}
			if (isset($this->data['Group']['parent_id'])) {
				$groupId = $this->data['Group']['parent_id'];
			} else {
				$groupId = $this->field('parent_id');
			}
			if (!$groupId) {
				return null;
			}
			return array('SessionAclTestGroup' => array('id' => $groupId));
		}
	}
?>
