<?php
	/**
	 * Code source de la classe WebrsaAccessTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('ControllerCache', 'Model/Datasource');
	App::uses('WebrsaAccess', 'Utility');

	/**
	 * CacheHackForTest class
	 *
	 * @package app.Test.Case.Utility
	 */
	class CacheHackForTest extends ControllerCache
	{
		public static function addAucunDroit($action) {
			self::$_aucunDroit[] = $action;
		}
	}

	/**
	 * La classe WebrsaAccessTest réalise les tests unitaires de la classe WebrsaAccess.
	 *
	 * @package app.Test.Case.Model
	 */
	class WebrsaAccessTest extends CakeTestCase
	{
		/**
		 * setUp method
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();
			CacheHackForTest::init();
			CacheHackForTest::addAucunDroit('Moncontroller:mon_action');
			CacheHackForTest::addAucunDroit('Moncontroller:mon_autre_action');
		}
		
		/**
		 * @covers WebrsaAccess::init
		 */
		public function testInit() {
			WebrsaAccess::init(array('test'));
			$result = WebrsaAccess::$dossierMenu;
			$expected = array('test');
			
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
		}
		
		/**
		 * @covers WebrsaAccess::link
		 */
		public function testLink() {
			$result = WebrsaAccess::link('/Moncontroller/mon_action');
			$expected = array(
				'/Moncontroller/mon_action' => array('disabled' => "!'#/Moncontroller/mon_action#'")
			);
			
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
			
			/**
			 * Sans rêgles métier
			 */
			$result = WebrsaAccess::link('/Moncontroller/mon_action', array('regles_metier' => false));
			$expected = array(
				'/Moncontroller/mon_action' => array('disabled' => false)
			);
			
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
			
			$result = WebrsaAccess::link('/Moncontroller/mon_action_qui_existe_pas', array('regles_metier' => false));
			$expected = array(
				'/Moncontroller/mon_action_qui_existe_pas' => array('disabled' => true)
			);
			
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
		}
		
		/**
		 * @covers WebrsaAccess::links
		 */
		public function testLinks() {
			$result = WebrsaAccess::links(
				array(
					'/Moncontroller/mon_action',
					'/Moncontroller/mon_autre_action'
				)
			);
			$expected = array(
				'/Moncontroller/mon_action' => array('disabled' => "!'#/Moncontroller/mon_action#'"),
				'/Moncontroller/mon_autre_action' => array('disabled' => "!'#/Moncontroller/mon_autre_action#'")
			);
			
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
			
			/**
			 * Sans rêgles métier
			 */
			$result = WebrsaAccess::links(
				array(
					'/Moncontroller/mon_action'
				),
				array('regles_metier' => false)
			);
			$expected = array(
				'/Moncontroller/mon_action' => array('disabled' => false)
			);
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
		}
		
		/**
		 * @covers WebrsaAccess::actionAdd
		 */
		public function testActionAdd() {
			$ajoutPossible = true;
			$result = WebrsaAccess::actionAdd('/Moncontroller/mon_action', $ajoutPossible);
			$expected = array(
				'/Moncontroller/mon_action' => array('disabled' => false)
			);
			
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
			
			$ajoutPossible = false;
			$result = WebrsaAccess::actionAdd('/Moncontroller/mon_action', $ajoutPossible);
			$expected = array(
				'/Moncontroller/mon_action' => array('disabled' => true)
			);
			
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
		}
		
		/**
		 * @covers WebrsaAccess::isDisabled
		 * @covers WebrsaAccess::isEnabled
		 */
		public function testIsDisabled() {
			$data = array(
				'Personne' => array(
					'id' => 1,
					'nom' => 'Foo',
					'prenom' => 'Bar',
				),
				'Autremodelquisertarien' => array(
					'id' => 1,
					'commentaire' => 'test'
				),
				'/Moncontroller/mon_action' => true,
				'/Moncontroller/mon_autre_action' => false,
				'/Moncontroller/mon_action_qui_existe_pas' => true,
			);
			
			$result = WebrsaAccess::isDisabled($data, '/Moncontroller/mon_action');
			$expected = false;
			
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
			
			$result = WebrsaAccess::isDisabled($data, '/Moncontroller/mon_autre_action');
			$expected = true;
			
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
			
			$result = WebrsaAccess::isDisabled($data, '/Moncontroller/mon_action_qui_existe_pas');
			$expected = true; // Blocage Acl
			
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
			
			$result = WebrsaAccess::isDisabled($data, '/Moncontroller/mon_action');
			$expected = !WebrsaAccess::isEnabled($data, '/Moncontroller/mon_action');
			
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
		}
		
		
		/**
		 * @covers WebrsaAccess::isDisabled
		 * @expectedException PHPUnit_Framework_Error_Notice
		 */
		public function testIsDisabledException() {
			$data = array(
				'Personne' => array(
					'id' => 1,
					'nom' => 'Foo',
					'prenom' => 'Bar',
				),
				'Autremodelquisertarien' => array(
					'id' => 1,
					'commentaire' => 'test'
				),
				'/Moncontroller/mon_action' => true,
//				'/Moncontroller/mon_autre_action' => false, // Désactivé pour provoquer l'exception
				'/Moncontroller/mon_action_qui_existe_pas' => true,
			);
			
			WebrsaAccess::isDisabled($data, '/Moncontroller/mon_autre_action');
		}
		
		/**
		 * @covers WebrsaAccess::addIsEnabled
		 */
		public function addIsEnabled() {
			$ajoutPossible = true;
			$result = WebrsaAccess::addIsEnabled('/Moncontroller/mon_autre_action', $ajoutPossible);
			$expected = true;
			
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
			
			$ajoutPossible = false;
			$result = WebrsaAccess::addIsEnabled('/Moncontroller/mon_autre_action', $ajoutPossible);
			$expected = false;
			
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
			
			$ajoutPossible = true;
			$result = WebrsaAccess::addIsEnabled('/Moncontroller/mon_action_qui_existe_pas', $ajoutPossible);
			$expected = false;
			
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
		}
		
		/**
		 * @covers WebrsaAccess::actions
		 * @covers WebrsaAccess::_extractControllerAction
		 */
		public function testActions() {
			$data = array(
				'Personne' => array(
					'id' => 1,
					'nom' => 'Foo',
					'prenom' => 'Bar',
				),
				'Autremodelquisertarien' => array(
					'id' => 1,
					'commentaire' => 'test'
				),
				'/Moncontroller/mon_action' => true,
				'/Moncontroller/mon_autre_action' => false,
				'/Moncontroller/mon_action_qui_existe_pas' => true,
			);
			
			$result = WebrsaAccess::actions(
				array(
					'/Moncontroller/mon_action',
					'/Moncontroller/mon_autre_action',
					'/Moncontroller/mon_action_qui_existe_pas',
					'/Moncontroller/mon_action_qui_existe_null_part',
				), $data
			);
			
			$expected = array(
				'/Moncontroller/mon_action' => array(
					'disabled' => false // acl OK, métier OK
				),
				'/Moncontroller/mon_autre_action' => array(
					'disabled' => true // acl OK, métier FAUX
				),
				'/Moncontroller/mon_action_qui_existe_pas' => array(
					'disabled' => true // acl FAUX, métier OK
				),
				'/Moncontroller/mon_action_qui_existe_null_part' => array(
					'disabled' => true // acl FAUX, métier FAUX
				)
			);
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
			
			/**
			 * Test Acl seulement
			 */
			$result = WebrsaAccess::actions(
				array(
					'/Moncontroller/mon_action',
					'/Moncontroller/mon_autre_action',
					'/Moncontroller/mon_action_qui_existe_pas',
					'/Moncontroller/mon_action_qui_existe_null_part',
				),
				$data,
				array('regles_metier' => false)
			);
			$expected = array(
				'/Moncontroller/mon_action' => array(
					'disabled' => false // acl OK, métier OK
				),
				'/Moncontroller/mon_autre_action' => array(
					'disabled' => false // acl OK, métier FAUX	<-- Différent ICI
				),
				'/Moncontroller/mon_action_qui_existe_pas' => array(
					'disabled' => true // acl FAUX, métier OK
				),
				'/Moncontroller/mon_action_qui_existe_null_part' => array(
					'disabled' => true // acl FAUX, métier FAUX
				)
			);
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
			
			/**
			 * Attribut hidden
			 */
			$result = WebrsaAccess::actions(
				array(
					'/Moncontroller/mon_action',
					'/Moncontroller/mon_autre_action' => array('hidden' => true),
				),
				$data
			);
			$expected = array(
				'/Moncontroller/mon_action' => array(
					'disabled' => false // acl OK, métier OK
				),
			);
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
			
			/**
			 * Pas de data
			 */
			$result = WebrsaAccess::actions(
				array(
					'/Moncontroller/mon_action',
					'/Moncontroller/mon_autre_action',
				)
			);
			$expected = array(
				'/Moncontroller/mon_action' => array(
					'disabled' => false // acl OK, métier OK
				),
				'/Moncontroller/mon_autre_action' => array(
					'disabled' => false // acl OK, métier FAUX
				),
			);
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
			
			/**
			 * Lien 'add'
			 */
			$ajoutPossible = false;
			$result = WebrsaAccess::actions(
				array(
					'/Moncontroller/mon_action' => array('add' => $ajoutPossible),
					'/Moncontroller/mon_autre_action',
				)
			);
			$expected = array(
				'/Moncontroller/mon_action' => array(
					'disabled' => true // acl OK, métier OK - ajoutPossible à FAUX
				),
				'/Moncontroller/mon_autre_action' => array(
					'disabled' => false // acl OK, métier FAUX
				),
			);
			$this->assertEqual($result, $expected, 'Failed in '.__FUNCTION__.' : '.__LINE__);
		}
		
		/**
		 * @covers WebrsaAccess::_extractControllerAction
		 * @expectedException PHPUnit_Framework_Error_Notice
		 */
		public function testExtractControllerActionException() {
			WebrsaAccess::actions(
				array(
					'Une url mal orthographiée'
				)
			);
		}
	}
