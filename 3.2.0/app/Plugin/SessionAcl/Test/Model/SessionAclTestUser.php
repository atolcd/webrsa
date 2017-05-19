<?php
	/**
	 * Fichier source de la classe SessionAclTestUser.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe SessionAclTestUser ...
	 *
	 * @package app.Model
	 */
	class SessionAclTestUser extends AppModel
	{	
		public $useTable = 'sessionacl_users';

		public $actsAs = array(
			'Acl' => array('type' => 'requester'),
		);
		
		public $belongsTo = array(
			'Group' => array(
				'className' => 'SessionAclTestGroup',
				'foreignKey' => 'group_id',
			),
		);
		
		public function parentNode() {
			if (!$this->id && empty($this->data)) {
				return null;
			}
			if (isset($this->data['User']['group_id'])) {
				$groupId = $this->data['User']['group_id'];
			} else {
				$groupId = $this->field('group_id');
			}
			if (!$groupId) {
				return null;
			}
			return array('SessionAclTestGroup' => array('id' => $groupId));
		}
	}