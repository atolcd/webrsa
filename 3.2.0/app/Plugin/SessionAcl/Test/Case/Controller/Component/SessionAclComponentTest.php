<?php
/**
 * SessionAclComponentTest file
 *
 * PHP 5.3
 *
 * @package SessionAcl
 * @subpackage Test.Case.Controller.Component
 */

App::uses('CommonSessionAclTestFile', 'SessionAcl.Test/Case');

/**
 * SessionAclComponentTest class
 *
 * @package SessionAcl
 * @subpackage Test.Case.Controller.Component
 */
class SessionAclComponentTest extends CommonSessionAclTestFile
{
	/**
	 * Test des méthodes check, allow, deny et inherit
	 */
	public function testCheckAllowDenyInherit() {
		$result = $this->Controller->Acl->check($this->Controller->SessionAclTestUser, 'controllers/Groups/add');
		$expected = false;
		$this->assertEquals($expected, $result, 'Pas d\'aros acos -> droits à false');

		$this->Controller->Acl->allow($this->Controller->SessionAclTestUser, 'controllers/Groups/add');
		$result = $this->Controller->Acl->check($this->Controller->SessionAclTestUser, 'controllers/Groups/add');
		$expected = true;
		$this->assertEquals($expected, $result, 'Allow effectué directement sur noeud');

		$this->Controller->Acl->allow($this->Controller->SessionAclTestUser, 'controllers/Users');
		$result = $this->Controller->Acl->check($this->Controller->SessionAclTestUser, 'controllers/Users/add');
		$expected = true;
		$this->assertEquals($expected, $result, 'Allow effectué sur noeud parent');

		$this->Controller->Acl->deny($this->Controller->SessionAclTestUser, 'controllers/Users');
		$result = $this->Controller->Acl->check($this->Controller->SessionAclTestUser, 'controllers/Users/add');
		$expected = false;
		$this->assertEquals($expected, $result, 'Deny effectué sur noeud parent');

		$this->Controller->Acl->allow($this->Controller->SessionAclTestUser, 'controllers/Users/add');
		$result = $this->Controller->Acl->check($this->Controller->SessionAclTestUser, 'controllers/Users/add');
		$expected = true;
		$this->assertEquals($expected, $result, 'Allow effectué directement sur noeud (avec parent à faux)');

		$this->Controller->Acl->inherit($this->Controller->SessionAclTestUser, 'controllers/Users/add');
		$result = $this->Controller->Acl->check($this->Controller->SessionAclTestUser, 'controllers/Users/add');
		$expected = false;
		$this->assertEquals($expected, $result, 'Heritage effectué sur noeud (avec parent à faux)');

		$this->Controller->Acl->inherit($this->Controller->SessionAclTestUser, 'controllers/Users');
		$this->Controller->Acl->allow($this->Controller->SessionAclTestUser->Group, 'controllers/Users');
		$result = $this->Controller->Acl->check($this->Controller->SessionAclTestUser, 'controllers/Users/add');
		$expected = true;
		$this->assertEquals($expected, $result, 'Heritage effectué sur noeud (avec parent à vrais)');
	}
}