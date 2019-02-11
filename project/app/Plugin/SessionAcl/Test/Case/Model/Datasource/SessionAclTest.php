<?php
/**
 * SessionAclTest file
 *
 * PHP 5.3
 *
 * @package SessionAcl
 * @subpackage Test.Case.Controller.Component
 */

App::uses('CommonSessionAclTestFile', 'SessionAcl.Test/Case');
App::uses('SessionAcl', 'SessionAcl.Model/Datasource');
App::uses('CakeSession', 'Model/Datasource');

/**
 * SessionAclTest class
 *
 * @package SessionAcl
 * @subpackage Test.Case.
 */
class SessionAclTest extends CommonSessionAclTestFile
{
	/**
	 * Test de la méthode SessionAcl::check()
	 */
	public function testCheck() {
		$result = CakeSession::read(SessionAcl::$keyPrefix);
		$expected = array('SessionAclTestUser' => array('id' => 1));
		$this->assertEquals($expected, $result, 'Lecture session');
		
		$result = SessionAcl::check(SessionAcl::$keyPrefix.'.controllers/Users/add');
		$expected = false;
		$this->assertEquals($expected, $result, 'Session check (1er appel)');
		
		$result = CakeSession::read(SessionAcl::$keyPrefix);
		$expected = array(
			'SessionAclTestUser' => array(
				'id' => (int) 1
			),
			'controllers/Users/add' => false
		);
		$this->assertEquals($expected, $result, 'Lecture session après check');
		
		CakeSession::write(SessionAcl::$keyPrefix.'.controllers/Users/add', true);
		$result = SessionAcl::check(SessionAcl::$keyPrefix.'.controllers/Users/add');
		$expected = true;
		$this->assertEquals($expected, $result, 'Session check (après modification des droits)');
	}
	
	/**
	 * Test de la méthode SessionAcl::get()
	 */
	public function testGet() {
		$result = SessionAcl::get('user')->alias;
		$expected = 'SessionAclTestUser';
		$this->assertEquals($expected, $result, 'Accès à un attribut protégé');
		
		$result = SessionAcl::get('_user')->alias;
		$expected = 'SessionAclTestUser';
		$this->assertEquals($expected, $result, 'Accès à un attribut protégé avec underscore');
	}
}