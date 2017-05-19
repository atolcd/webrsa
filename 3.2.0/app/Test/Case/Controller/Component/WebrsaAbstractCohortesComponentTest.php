<?php
	/**
	 * Code source de la classe WebrsaAbstractCohortesComponentTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('AppController', 'Controller');
	App::uses('Component', 'Controller');
	App::uses('WebrsaAbstractCohortesComponent', 'Controller/Component');
	App::uses('CakeEventListener', 'Event');
	App::uses('SuperFixture', 'SuperFixture.Utility');
	App::uses( 'Orientstruct', 'Model' );
	
	/**
	 * NonAbstractCohorteTestComponent class
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class NonAbstractCohorteTestComponent extends WebrsaAbstractCohortesComponent
	{
	}

	/**
	 * WebrsaCheckTestController class
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class WebrsaCheckTestController extends AppController
	{
		/**
		 * name property
		 *
		 * @var string
		 */
		public $name = 'WebrsaCheckTestController';

		/**
		 * components property
		 *
		 * @var array
		 */
		public $components = array(
			'NonAbstractCohorteTest',
		);
	}

	/**
	 * La classe WebrsaAbstractCohortesComponentTest ...
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class WebrsaAbstractCohortesComponentTest extends CakeTestCase
	{
		public $fixtures = array(
			'Dsp',
			'DspRev',
		);
		
		/**
		 * Controller property
		 *
		 * @var WebrsaRecherchesDossiersComponent
		 */
		public $Controller;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			
			Configure::write('Recherche.qdFilters.Serviceinstructeur', false);
			
			SuperFixture::load($this, 'WebrsaAbstractCohorte');
			ClassRegistry::flush();
			
			// On mock la méthode ged()
			$Orientstruct = $this->getMock(
				'Orientstruct',
				array( 'ged' ),
				array( array( 'ds' => 'test' ) )
			);
			ClassRegistry::addObject( 'Orientstruct', $Orientstruct );
			
			$Request = new CakeRequest('dossiers/search', false);
			$Request->addParams(array('controller' => 'dossiers', 'action' => 'action_cohorte'));

			$this->Controller = new WebrsaCheckTestController($Request);
			$this->Controller->Components->init($this->Controller);
			$this->Controller->NonAbstractCohorteTest->initialize($this->Controller);
		}

		/**
		 * @covers WebrsaAbstractCohortesComponent::checkHiddenCohorteValues
		 * @covers WebrsaAbstractCohortesComponent::_isNotAValidField
		 * @covers WebrsaAbstractCohortesComponent::_checkHiddenCohorteValueByPath
		 * @covers WebrsaAbstractCohortesComponent::_secureTestSave
		 * @covers WebrsaAbstractCohortesComponent::_isValidIfIsForeignKey
		 */
		public function testCheckHiddenCohorteValues() {
			Configure::write(
				'ConfigurableQuery.Dossiers.action_cohorte.cohorte.values',
				array(
					'Orientstruct.rgorient' => 1,
					'Serviceinstructeur.0_Serviceinstructeur' => 1,
				)
			);
			
			$result = $this->Controller->NonAbstractCohorteTest->checkHiddenCohorteValues();
			$expected = array(
				'success' => true,
				'message' => 'Aucune erreur n\'a été trouvée.',
				'value' => ''
			);
			
			$this->assertEqual($result, $expected, "Execution simple de la méthode");
			
			Configure::write(
				'ConfigurableQuery.Dossiers.action_cohorte.cohorte.values',
				array(
					'Orientstruct.rgorient' => 'foo'
				)
			);
			
			$result = $this->Controller->NonAbstractCohorteTest->checkHiddenCohorteValues();
			$expected = array(
				'success' => false,
				'message' => 'Des erreurs ont été trouvées!',
				'value' => 'La tentative d\'insérer la valeur <b>foo</b> dans <b>Orientstruct.rgorient</b> a échoué : Veuillez entrer un nombre entier'
			);
			
			$this->assertEqual($result, $expected, "String dans un champ integer");
			
			Configure::write(
				'ConfigurableQuery.Dossiers.action_cohorte.cohorte.values',
				array(
					'Orientstruct.typeorient_id' => 100000,
				)
			);
			
			$result = $this->Controller->NonAbstractCohorteTest->checkHiddenCohorteValues();
			$expected = array(
				'success' => false,
				'message' => 'Des erreurs ont été trouvées!',
				'value' => 'La valeur <b>100000</b> pour <b>Orientstruct.typeorient_id</b> ne se trouve pas dans <b>Typeorient.id</b>.'
			);
			
			$this->assertEqual($result, $expected, "Foreign key inexistante");
			
			Configure::write(
				'ConfigurableQuery.Dossiers.action_cohorte.cohorte.values',
				array(
					'Model_field' => 'foo',
				)
			);
			
			$result = $this->Controller->NonAbstractCohorteTest->checkHiddenCohorteValues();
			$expected = array(
				'success' => false,
				'message' => 'Des erreurs ont été trouvées!',
				'value' => 'La table et le champ de <b>Model_field</b> n\'ont pas été trouvé. Utilisez la syntaxe suivante : Modele.champ.'
			);
			
			$this->assertEqual($result, $expected, "Model.field mal formaté");
			
			Configure::write(
				'ConfigurableQuery.Dossiers.action_cohorte.cohorte.values',
				array(
					'Orientstruct.field' => 'foo',
				)
			);
			
			$result = $this->Controller->NonAbstractCohorteTest->checkHiddenCohorteValues();
			$expected = array(
				'success' => false,
				'message' => 'Des erreurs ont été trouvées!',
				'value' => 'L\'existance de <b>field</b> dans le modèle <b>Orientstruct</b> n\'a pas été trouvé.'
			);
			
			$this->assertEqual($result, $expected, "Model.field mal formaté");
			
			Configure::write(
				'ConfigurableQuery.Dossiers.action_cohorte.cohorte.values',
				array(
					'Foo.field' => 'bar',
				)
			);
			
			$result = $this->Controller->NonAbstractCohorteTest->checkHiddenCohorteValues();
			$expected = array(
				'success' => false,
				'message' => 'Des erreurs ont été trouvées!',
				'value' => "La table du Model Foo n'a pas été trouvée."
			);
			
			$this->assertEqual($result, $expected, "Le model n'existe pas");
			
			Configure::write(
				'ConfigurableQuery.Dossiers.action_cohorte.cohorte.values',
				array(
					'Serviceinstructeur.0_Serviceinstructeur' => 1000000,
					'Serviceinstructeur.1_Serviceinstructeur' => 1000001,
				)
			);
			
			$result = $this->Controller->NonAbstractCohorteTest->checkHiddenCohorteValues();
			$expected = array(
				'success' => false,
				'message' => 'Des erreurs ont été trouvées!',
					'value' => 'La valeur <b>1000000</b> pour <b>Serviceinstructeur.0_Serviceinstructeur</b> ne se trouve pas dans <b>Serviceinstructeur.id</b>.'
				. '<br/>'
				. 'La valeur <b>1000001</b> pour <b>Serviceinstructeur.1_Serviceinstructeur</b> ne se trouve pas dans <b>Serviceinstructeur.id</b>.'
			);
			
			$this->assertEqual($result, $expected, "L'id d'un HABTM n'existe pas");
			
			/**
			 * Test de la blacklist
			 */
			ClassRegistry::init('Orientstruct')->validate = array(
				'date_propo' => array(
					NOT_BLANK_RULE_NAME => array(
						'rule' => NOT_BLANK_RULE_NAME,
						'message' => 'Test de validation'
					)
				)
			);
			
			Configure::write(
				'ConfigurableQuery.Dossiers.action_cohorte.cohorte.values',
				array(
					'Orientstruct.structurereferente_id' => 1,
				)
			);
			
			$result = $this->Controller->NonAbstractCohorteTest->checkHiddenCohorteValues();
			$expected = array(
				'success' => true,
				'message' => 'Aucune erreur n\'a été trouvée.',
				'value' => ''
			);
			
			$this->assertEqual($result, $expected, "Test de blacklist");
		}
	}
