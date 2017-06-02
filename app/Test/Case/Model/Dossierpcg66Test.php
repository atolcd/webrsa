<?php
	/**
	 * Code source de la classe Dossierpcg66Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('BakeSuperFixture', 'SuperFixture.Utility');
	App::uses('BSFObject', 'SuperFixture.Utility');

	/**
	 * La classe Dossierpcg66Test réalise les tests unitaires de la classe Dossierpcg66.
	 *
	 * @package app.Test.Case.Model
	 */
	class Dossierpcg66Test extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Dossierpcg66',
			'app.Decisiondossierpcg66',
			'app.Personnepcg66',
			'app.Personne',
			'app.Traitementpcg66',
			'app.Foyer',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Dossierpcg66
		 */
		public $Dossierpcg66 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Dossierpcg66 = ClassRegistry::init( 'Dossierpcg66' );
			Configure::write('Corbeillepcg.descriptionpdoId', array('1'));
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Dossierpcg66 );
			parent::tearDown();
		}

		/**
		 * Test de la mise à jour d'un etat du dossier PCG
		 */
		public function testUpdatePositionsPcgs() {
			$Baker = new BakeSuperFixture();
			
			// On cuisine un nouveau dossierpcg
			$dataDossierpcg66 = array(
				'orgpayeur' => array('value' => 'CAF'),
				'haspiecejointe' => array('value' => 0)
			);
			$Dossierpcg66Obj = new BSFObject('Dossierpcg66', $dataDossierpcg66);
			$Baker->create(array($Dossierpcg66Obj), $save = true);
			
			$etat = $this->_getLastEtat();
			$expected = null;
			
			$this->assertEqual($etat, $expected, "A la création, etat à NULL");
			
			$this->Dossierpcg66->WebrsaDossierpcg66->updatePositionsPcgsById($this->Dossierpcg66->id);
			$etat = $this->_getLastEtat();
			$expected = "attaffect";
			
			$this->assertEqual($etat, $expected, "1ere attribution d'un etat, en attente d'affection");
			
			// On cuisine un nouveau dossierpcg affecté
			$dataDossierpcg66 = array(
				'user_id' => array('auto' => true),
				'orgpayeur' => array('value' => 'CAF'),
				'haspiecejointe' => array('value' => 0)
			);
			$Dossierpcg66Obj = new BSFObject('Dossierpcg66', $dataDossierpcg66);
			$Baker->create(array($Dossierpcg66Obj), $save = true);
			
			$this->Dossierpcg66->WebrsaDossierpcg66->updatePositionsPcgsById($this->Dossierpcg66->id);
			$etat = $this->_getLastEtat();
			$expected = "attinstr";
			
			$this->assertEqual($etat, $expected, "Dossier PCG affecté : en attente d'instruction");
			
			$dataDecisiondossierpcg66 = array(
				'dossierpcg66_id' => array('foreignkey' => $Dossierpcg66Obj->getName()),
				'decisionpdo_id' => array('value' => 1),
				'avistechnique' => array('value' => 'N'),
				'validationproposition' => array('value' => 'O'),
				'retouravistechnique' => array('value' => 0),
				'vuavistechnique' => array('value' => 0),
				'haspiecejointe' => array('value' => 0),
			);
			$Decisiondossierpcg66 = new BSFObject('Decisiondossierpcg66', $dataDecisiondossierpcg66);
			$Baker->create(array($Decisiondossierpcg66), $save = true);
			
			$this->Dossierpcg66->WebrsaDossierpcg66->updatePositionsPcgsById($this->Dossierpcg66->id);
			$etat = $this->_getLastEtat();
			$expected = "decisionvalid";
			
			$this->assertEqual($etat, $expected, "Decision validée");
		}
		
		/**
		 * Renvoi l'etat du tout dossier dossier PCG
		 * 
		 * @return String
		 */
		protected function _getLastEtat() {
			$result = $this->Dossierpcg66->find('first', array(
				'fields' => array(
					'Dossierpcg66.etatdossierpcg'
				),
				'order' => array('Dossierpcg66.id' => 'DESC')
			));
			return Hash::get($result, 'Dossierpcg66.etatdossierpcg');
		}
	}
?>
