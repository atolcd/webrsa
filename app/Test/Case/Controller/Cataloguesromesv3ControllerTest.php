<?php
	/**
	 * Code source de la classe Cataloguesromesv3ControllerTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'Cataloguesromesv3Controller', 'Controller' );

	App::uses( 'CakeTestSession', 'CakeTest.Model/Datasource' );

	/**
	 * La classe Cataloguesromesv3ControllerTest ...
	 *
	 * @package app.Test.Case.Controller
	 */
	class Cataloguesromesv3ControllerTest extends ControllerTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Correspondanceromev2v3',
			'app.Domaineromev3',
			'app.Familleromev3',
			'app.Metierromev3',
			'app.Appellationromev3'
		);

		/**
		 * Les données envoyées à la vue dans le formulaire d'ajout / d'édition.
		 *
		 * @var array
		 */
		public $addEditViewVars = array(
			'options' => array(
				'Domaineromev3' => array(),
				'Metierromev3' => array(
					'domaineromev3_id' => array(
						'1_1' => 'A11 - Engins agricoles et forestiers',
					),
					'familleromev3_id' => array(
						1 => 'A - AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX',
					),
				),
			),
			'fields' => array(
				'Metierromev3.familleromev3_id' => array( 'empty' => true, 'required' => true, ),
				'Metierromev3.id' => array(),
				'Metierromev3.domaineromev3_id' => array( 'empty' => true, ),
				'Metierromev3.code' => array(),
				'Metierromev3.name' => array(),
				'Metierromev3.referer' => array( 'type' => 'hidden', ),
			),
			'modelName' => 'Metierromev3',
			'dependantFields' =>
			array(
				'Metierromev3.familleromev3_id' => 'Metierromev3.domaineromev3_id',
			)
		);

		/**
		 * test case startup
		 *
		 * @return void
		 */
		public static function setupBeforeClass() {
			CakeTestSession::setupBeforeClass();
		}

		/**
		 * cleanup after test case.
		 *
		 * @return void
		 */
		public static function teardownAfterClass() {
			CakeTestSession::teardownAfterClass();
		}

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			Configure::write( 'Cg.departement', 66 );
			Configure::write( 'Romev3.enabled', true );

			if( defined( 'CAKEPHP_SHELL' ) && CAKEPHP_SHELL ) {
				$_SERVER['REQUEST_URI'] = '/';
			}

			$this->controller = $this->generate(
				'Cataloguesromesv3',
				array(
					'methods' => array( 'render' )
				)
			);

			parent::setUp();
			CakeTestSession::start();
		}

		/**
		 * tearDown method
		 *
		 * @return void
		 */
		public function tearDown() {
			CakeTestSession::destroy();
			parent::tearDown();
		}

		/**
		 * Test de la méthode Cataloguesromesv3Controller::metiersromesv3()
		 */
		public function testMetiersromesv3() {
			$data = array(
				'Search' => array(
					'Familleromev3' => array(
						'code' => null
					)
				)
			);
			$this->testAction( '/cataloguesromesv3/metiersromesv3', array( 'data' => $data, 'method' => 'post') );

			// 1. Vérification de la variable $results
			$result = Hash::get( $this->controller->viewVars, 'results' );
			$expected = array(
				array(
					'Familleromev3' => array(
						'id' => 1,
						'code' => 'A',
						'name' => 'AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX',
						'created' => NULL,
						'modified' => NULL,
					),
					'Domaineromev3' => array(
						'id' => 1,
						'familleromev3_id' => 1,
						'code' => '11',
						'name' => 'Engins agricoles et forestiers',
						'created' => NULL,
						'modified' => NULL,
					),
					'Metierromev3' => array(
						'id' => 1,
						'domaineromev3_id' => 1,
						'code' => 'A1101',
						'name' => 'Conduite d\'engins d\'exploitation agricole et forestière',
						'created' => NULL,
						'modified' => NULL,
						'occurences' => '1',
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Vérification de la variable $options
			$result = Hash::get( $this->controller->viewVars, 'options' );
			$expected = array(
				'Domaineromev3' => array(
					'familleromev3_id' => array(
						1 => 'A - AGRICULTURE ET PÊCHE, ESPACES NATURELS ET ESPACES VERTS, SOINS AUX ANIMAUX',
					)
				),
				'Metierromev3' => array(
					'domaineromev3_id' => array(
						'1_1' => 'A11 - Engins agricoles et forestiers',
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. Vérification de la variable $fields
			$result = Hash::get( $this->controller->viewVars, 'fields' ); // modelName, tableName
			$expected = array(
				'Metierromev3.code',
				'Familleromev3.name',
				'Domaineromev3.name',
				'Metierromev3.name',
				'Metierromev3.created',
				'Metierromev3.modified',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 4. Vérification de la variable $modelName
			$result = Hash::get( $this->controller->viewVars, 'modelName' );
			$this->assertEqual( $result, 'Metierromev3', var_export( $result, true ) );

			// 5. Vérification de la variable $tableName
			$result = Hash::get( $this->controller->viewVars, 'tableName' );
			$this->assertEqual( $result, 'metiersromesv3', var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Cataloguesromesv3Controller::ajax_appellation() lors
		 * de l'événement keyup (retourne des propositions d'appellations).
		 */
		public function testAjaxAppellationKeyupFound() {
			$data = array(
				'Deractromev3' => array( 'romev3' => 'conducteur' ),
				'prefix' => '',
				'Event' => array( 'type' => 'keyup' ),
				'Target' => array(
					'domId' => 'Deractromev3Romev3',
					'name' => 'data[Deractromev3][romev3]'
				)
			);
			$this->testAction( '/cataloguesromesv3/ajax_appellation', array( 'data' => $data, 'method' => 'post') );

			$result = Hash::get( $this->controller->viewVars, 'json' );
			$expected = array(
				'success' => true,
				'fields' => array(
					'Deractromev3.romev3' => array(
						'prefix' => '',
						'id' => 'Deractromev3Romev3',
						'type' => 'ajax_select',
						'options' => array(
							array(
								'id' => 1,
								'name' => 'Conducteur / Conductrice d\'engins d\'exploitation agricole'
							)
						)
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Cataloguesromesv3Controller::ajax_appellation() lors
		 * de l'événement keyup (retourne des propositions d'appellations), lorsque
		 * le texte n'est pas trouvé.
		 */
		public function testAjaxAppellationKeyupNotFound() {
			$data = array(
				'Deractromev3' => array( 'romev3' => 'cxnducteur' ),
				'prefix' => '',
				'Event' => array( 'type' => 'keyup' ),
				'Target' => array(
					'domId' => 'Deractromev3Romev3',
					'name' => 'data[Deractromev3][romev3]'
				)
			);
			$this->testAction( '/cataloguesromesv3/ajax_appellation', array( 'data' => $data, 'method' => 'post') );

			$result = Hash::get( $this->controller->viewVars, 'json' );
			$expected = array(
				'success' => true,
				'fields' => array(
					'Deractromev3.romev3' => array(
						'id' => 'Deractromev3Romev3',
						'type' => 'ajax_select',
						'prefix' => '',
						'options' => array(
						)
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Cataloguesromesv3Controller::ajax_appellation() lors
		 * de l'événement click (pré-remplissage des listes déroulantes).
		 */
		public function testAjaxAppellationClick() {
			$data = array(
				'id' => 'Deractromev3Romev3',
				'prefix' => '',
				'name' => 'data[Deractromev3][romev3]',
				'value' => 1,
				'Event' => array( 'type' => 'click' )
			);
			$this->testAction( '/cataloguesromesv3/ajax_appellation', array( 'data' => $data, 'method' => 'post' ) );

			$result = Hash::get( $this->controller->viewVars, 'json' );
			$expected = array(
				'success' => true,
				'fields' => array(
					'Deractromev3.romev3' => array(
						'id' => 'Deractromev3Romev3',
						'value' => '',
						'type' => 'text'
					),
					'Deractromev3.familleromev3_id' => array(
						'id' => 'Deractromev3Familleromev3Id',
						'value' => 1,
						'type' => 'select',
						'simulate' => true
					),
					'Deractromev3.domaineromev3_id' => array(
						'id' => 'Deractromev3Domaineromev3Id',
						'value' => '1_1',
						'type' => 'select',
						'simulate' => true
					),
					'Deractromev3.metierromev3_id' => array(
						'id' => 'Deractromev3Metierromev3Id',
						'value' => '1_1',
						'type' => 'select',
						'simulate' => true
					),
					'Deractromev3.appellationromev3_id' => array(
						'id' => 'Deractromev3Appellationromev3Id',
						'value' => '1_1',
						'type' => 'select',
						'simulate' => true
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Cataloguesromesv3Controller::add() lors de l'accès
		 * au formulaire.
		 */
		public function testAddFormAccess() {
			$this->testAction( '/cataloguesromesv3/add/Metierromev3' );

			// 1. Vérification des données envoyées à la vue, partie viewVars
			$result = $this->controller->viewVars;
			unset( $result['etatdosrsa'] );

			$expected = $this->addEditViewVars;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Vérification des données envoyées à la vue, partie data
			$result = $this->controller->request->data;
			$expected = array(
				'Metierromev3' => array(
					'referer' => Hash::get( $result, 'Metierromev3.referer' )
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Cataloguesromesv3Controller::add() lors du renvoi
		 * du formulaire.
		 */
		public function testAddFormSendOk() {
			$data = array(
				'Save' => 'Enregistrer',
				'Metierromev3' => array(
					'familleromev3_id' => '1',
					'id' => '',
					'domaineromev3_id' => '1_1',
					'code' => '02',
					'name' => 'Intitulé test',
					'referer' => '/cataloguesromesv3/index/Metierromev3'
				)
			);

			// 1. Tentative de sauvegarde
			$result = $this->testAction( '/cataloguesromesv3/add/Metierromev3', array( 'data' => $data, 'method' => 'post' ) );
			$this->assertNull( $result );

			// 2. Vérification de l'enregistrement des données
			$Metierromev3 = ClassRegistry::init( 'Metierromev3' );
			$query = array(
				'fields' => array(
					'Metierromev3.id',
					'Metierromev3.name'
				),
				'conditions' => array(
					'Metierromev3.domaineromev3_id' => 1,
					'Metierromev3.code' => '02'
				)
			);
			$result = (array)$Metierromev3->find( 'first', $query );
			$expected = array(
				'Metierromev3' => array(
					'id' => 2,
					'name' => 'Intitulé test'
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Cataloguesromesv3Controller::add() lors du renvoi
		 * du formulaire avec le bouton "Annuler"/
		 */
		public function testAddFormSendCancel() {
			$data = array(
				'Cancel' => 'Annuler',
				'Metierromev3' => array(
					'familleromev3_id' => '1',
					'id' => '',
					'domaineromev3_id' => '1_1',
					'code' => '66',
					'name' => 'Intitulé test',
					'referer' => '/cataloguesromesv3/index/Metierromev3'
				)
			);

			// 1. Tentative de sauvegarde
			$result = $this->testAction( '/cataloguesromesv3/add/Metierromev3', array( 'data' => $data, 'method' => 'post' ) );
			$this->assertNull( $result );

			// 2. Vérification du non enregistrement des données
			$Metierromev3 = ClassRegistry::init( 'Metierromev3' );
			$query = array(
				'conditions' => array(
					'Metierromev3.code' => '66'
				)
			);
			$result = (array)$Metierromev3->find( 'first', $query );
			$expected = array();
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Cataloguesromesv3Controller::edit() lors de l'accès
		 * au formulaire.
		 */
		public function testEditFormAccess() {
			$this->testAction( '/cataloguesromesv3/edit/Metierromev3/1' );

			// 1. Vérification des données envoyées à la vue, partie viewVars
			$result = $this->controller->viewVars;
			unset( $result['etatdosrsa'] );

			$expected = $this->addEditViewVars;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Vérification des données envoyées à la vue, partie data
			$result = $this->controller->request->data;
			$expected = array(
				'Metierromev3' => array(
					'id' => 1,
					'domaineromev3_id' => '1_1',
					'code' => '01',
					'name' => 'Conduite d\'engins d\'exploitation agricole et forestière',
					'created' => NULL,
					'modified' => NULL,
					'familleromev3_id' => 1,
					'referer' => Hash::get( $result, 'Metierromev3.referer' )
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Cataloguesromesv3Controller::edit() lors de l'accès
		 * au formulaire pour une valeur non existante.
		 * @expectedException NotFoundException
		 */
		public function testEditFormAccessNotFound() {
			$this->testAction( '/cataloguesromesv3/edit/Metierromev3/2' );
		}

		/**
		 * Test de la méthode Cataloguesromesv3Controller::delete() sans erreur.
		 */
		public function testDeleteOk() {
			$result = $this->testAction( '/cataloguesromesv3/delete/Appellationromev3/1' );
			$this->assertNull( $result );

			$result = $this->controller->Session->read( 'Message.flash.message' );
			$this->assertEqual( $result, 'Suppression effectuée', var_export( $result, true ) );

			$Appellationromev3 = ClassRegistry::init( 'Appellationromev3' );
			$query = array(
				'fields' => array(
					'Appellationromev3.id',
					'Appellationromev3.name'
				),
				'conditions' => array(
					'Appellationromev3.id' => 1
				)
			);
			$result = $Appellationromev3->find( 'first', $query );
			$this->assertEqual( $result, array(), var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Cataloguesromesv3Controller::delete() avec exception
		 * car on passe le nom d'un modèle non permis.
		 * @expectedException NotFoundException
		 */
		public function testDeleteErrorNotFoundModel() {
			$this->testAction( '/cataloguesromesv3/delete/Fooromev3/1' );
		}

		/**
		 * Test de la méthode Cataloguesromesv3Controller::delete() avec exception
		 * car des enregistrements dépendent de l'enregistrement que l'on veut
		 * supprimer.
		 * @expectedException InternalErrorException
		 */
		public function testDeleteErrorOccurencesFound() {
			$this->testAction( '/cataloguesromesv3/delete/Metierromev3/1' );
		}

		/**
		 * Test de la méthode Cataloguesromesv3Controller::delete() avec erreur
		 * car l'enregistrement que l'on veut supprimer n'existe pas.
		 */
		public function testDeleteError() {
			$result = $this->testAction( '/cataloguesromesv3/delete/Appellationromev3/2' );
			$this->assertNull( $result );

			$result = $this->controller->Session->read( 'Message.flash.message' );
			$this->assertEqual( $result, 'Erreur lors de la suppression', var_export( $result, true ) );
		}
	}
?>