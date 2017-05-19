<?php
	/**
	 * Code source de la classe InsertionsBeneficiairesComponentTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'InsertionsBeneficiairesComponent', 'Controller/Component' );
	App::uses( 'CakeTestSession', 'CakeTest.Model/Datasource' );

	/**
	 * InsertionsBeneficiairesTestsController class
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class InsertionsBeneficiairesTestsController extends AppController
	{

		/**
		 * name property
		 *
		 * @var string
		 */
		public $name = 'InsertionsBeneficiairesTestsController';

		/**
		 * uses property
		 *
		 * @var mixed null
		 */
		public $uses = array( 'Apple' );

		/**
		 * components property
		 *
		 * @var array
		 */
		public $components = array(
			'InsertionsBeneficiaires'
		);

	}
	/**
	 * La classe InsertionsBeneficiairesComponentTest réalise les tests de la
	 * classe InsertionsBeneficiairesComponent
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class InsertionsBeneficiairesComponentTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Referent',
			'app.Structurereferente',
			'app.StructurereferenteZonegeographique',
			'app.Typeorient',
			'app.Zonegeographique',
		);

		/**
		 * Controller property
		 *
		 * @var InsertionsBeneficiairesComponent
		 */
		public $Controller;


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
			parent::setUp();

			Configure::write( 'with_parentid', false );

			$Request = new CakeRequest( 'apples/index', false );
			$Request->addParams( array( 'controller' => 'apples', 'action' => 'index' ) );

			$this->Controller = new InsertionsBeneficiairesTestsController( $Request );
			$this->Controller->Components->init( $this->Controller );
			$this->Controller->InsertionsBeneficiaires->initialize( $this->Controller );

			CakeTestSession::start();
			CakeTestSession::delete( 'Auth' );
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
		 * Test de la méthode InsertionsBeneficiairesComponent::sessionKey()
		 *
		 * @covers InsertionsBeneficiairesComponent::sessionKey
		 */
		public function testSessionKey() {
			$result = $this->Controller->InsertionsBeneficiaires->sessionKey( 'typesorients', array() );
			$expected = 'Auth.InsertionsBeneficiaires.typesorients.8739602554c7f3241958e3cc9b57fdecb474d508';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = $this->Controller->InsertionsBeneficiaires->sessionKey( 'structuresreferentes', array( 'conditions' => array() ) );
			$expected = 'Auth.InsertionsBeneficiaires.structuresreferentes.44e853563ed46d4e94cbfa397ba4ddee622ffb2b';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode InsertionsBeneficiairesComponent::options()
		 *
		 * @covers InsertionsBeneficiairesComponent::options
		 */
		public function testOptions() {
			// 1. Tests de typesorients
			// 1.1. Sans options supplémentaires
			$result = $this->Controller->InsertionsBeneficiaires->options( 'typesorients' );
			$expected = array(
				'conditions' => array(
					'Typeorient.actif' => 'O',
				),
				'empty' => false,
				'cache' => true,
				'with_parentid' => false
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 1.2. Avec des options supplémentaires
			$options = array( 'conditions' => array(), 'empty' => true, 'foo' => 'bar' );
			$result = $this->Controller->InsertionsBeneficiaires->options( 'typesorients', $options );
			$expected = array(
				'conditions' => array(),
				'empty' => true,
				'cache' => true,
				'foo' => 'bar',
				'with_parentid' => false
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Tests de structuresreferentes
			// 2.1. Sans options supplémentaires
			$result = $this->Controller->InsertionsBeneficiaires->options( 'structuresreferentes' );
			$expected = array(
				'conditions' => array(
					'Typeorient.actif' => 'O',
					'Structurereferente.actif' => 'O',
				),
				'prefix' => true,
				'type' => 'list',
				'cache' => true,
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2.2. Avec des options supplémentaires
			$options = array( 'conditions' => array( 'Structurereferente.actif' => array( 'O', 'N' ) ), 'prefix' => false, 'foo' => 'bar' );
			$result = $this->Controller->InsertionsBeneficiaires->options( 'structuresreferentes', $options );
			$expected = array(
				'conditions' => array(
					'Structurereferente.actif' => array( 'O', 'N' ),
				),
				'prefix' => false,
				'foo' => 'bar',
				'type' => 'list',
				'cache' => true,
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. Tests de referents
			// 3.1. Sans options supplémentaires
			$result = $this->Controller->InsertionsBeneficiaires->options( 'referents' );
			$expected = array(
				'conditions' => array(
					'Typeorient.actif' => 'O',
					'Structurereferente.actif' => 'O',
					'Referent.actif' => 'O',
				),
				'prefix' => true,
				'type' => 'list',
				'cache' => true,
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3.2. Avec des options supplémentaires
			$options = array( 'conditions' => array(), 'type' => InsertionsBeneficiairesComponent::TYPE_IDS, 'foo' => 'bar' );
			$result = $this->Controller->InsertionsBeneficiaires->options( 'referents', $options );
			$expected = array(
				'conditions' => array(),
				'type' => 'ids',
				'foo' => 'bar',
				'prefix' => true,
				'cache' => true,
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode InsertionsBeneficiairesComponent::options() lorsqu'un
		 * mauvais nom de méthode est envoyé.
		 *
		 * @covers InsertionsBeneficiairesComponent::options
		 * @expectedException RuntimeException
		 */
		public function testOptionsUnknownMethod() {
			$this->Controller->InsertionsBeneficiaires->options( 'foo' );
		}

		/**
		 * Test de la méthode InsertionsBeneficiairesComponent::typesorients()
		 *
		 * @covers InsertionsBeneficiairesComponent::typesorients
		 * @covers InsertionsBeneficiairesComponent::_typesorients
		 */
		public function testTypesorientsCg93() {
			Configure::write( 'with_parentid', false );
			Configure::write( 'Cg.departement', 93 );

			// 1. Sans spécifier d'option
			$options = array();
			$result = $this->Controller->InsertionsBeneficiaires->typesorients( $options );
			$expected = array (
				3 => 'Emploi',
				2 => 'Social',
				1 => 'Socioprofessionnelle'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. En spécifiant des conditions
			$options = array( 'conditions' => array( 'Typeorient.actif' => 'N' ) );
			$result = $this->Controller->InsertionsBeneficiaires->typesorients( $options );
			$expected = array (
				4 => 'Foo'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. En spécifiant l'ajout de la valeur vide
			$options = array( 'empty' => true );
			$result = $this->Controller->InsertionsBeneficiaires->typesorients( $options );
			$expected = array (
				0 => 'Non orienté',
				3 => 'Emploi',
				2 => 'Social',
				1 => 'Socioprofessionnelle',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode InsertionsBeneficiairesComponent::typesorients()
		 * pour le CG 66 lorsque l'utilisateur est un "externe_ci".
		 *
		 * @covers InsertionsBeneficiairesComponent::typesorients
		 * @covers InsertionsBeneficiairesComponent::_typesorients
		 */
		public function testTypesorientsCg66() {
			Configure::write( 'Cg.departement', 66 );
			CakeTestSession::write('Auth.User.type', 'externe_ci' );

			$result = $this->Controller->InsertionsBeneficiaires->typesorients();
			$expected = array (
				1 => 'Socioprofessionnelle',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Insertion des types d'orientation de second niveau pour tester avec
		 * la configuration "with_parentid".
		 */
		protected function _insertTypeorientChildren() {
			$this->Controller->loadModel( 'Structurereferente' );
			$data = array(
				array(
					'parentid' => 3,
					'lib_type_orient' => 'Professionnelle - Pôle Emploi',
					'actif' => 'O'
				),
				array(
					'parentid' => 2,
					'lib_type_orient' => 'Social - Site de Chiconi',
					'actif' => 'O'
				),
				array(
					'parentid' => 1,
					'lib_type_orient' => 'Socioprofessionnelle - Site de Chiconi',
					'actif' => 'O'
				),
			);
			$success = $this->Controller->Structurereferente->Typeorient->saveAll( $data );
			$this->assertTrue( $success );
		}

		/**
		 * Test de la méthode InsertionsBeneficiairesComponent::typesorients()
		 * pour le CG 976, with_parentid.
		 *
		 * @covers InsertionsBeneficiairesComponent::typesorients
		 * @covers InsertionsBeneficiairesComponent::_typesorients
		 */
		public function testTypesorientsWithParentId976() {
			Configure::write( 'Cg.departement', 976 );
			Configure::write( 'with_parentid', true );

			$this->_insertTypeorientChildren();

			$result = $this->Controller->InsertionsBeneficiaires->typesorients();
			$expected = array(
				'Emploi' => array(
					5 => 'Professionnelle - Pôle Emploi'
				),
				'Social' => array(
					6 => 'Social - Site de Chiconi'
				),
				'Socioprofessionnelle' => array(
					7 => 'Socioprofessionnelle - Site de Chiconi'
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode InsertionsBeneficiairesComponent::structuresreferentes()
		 * pour les CG 58 et 93, ainsi qu'au CG 66 lorsque l'utilisateur n'est
		 * pas un "externe_ci".
		 *
		 * @covers InsertionsBeneficiairesComponent::structuresreferentes
		 * @covers InsertionsBeneficiairesComponent::_structuresreferentes
		 * @covers InsertionsBeneficiairesComponent::_sqStructurereferenteZonesgeographiques93
		 */
		public function testStructuresreferentes() {
			Configure::write( 'Cg.departement', 93 );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', true );
			CakeTestSession::write( 'Auth.Zonegeographique', array( 1 => 93001 ) );

			// 1. Par défaut, liste simple avec préfixe du type d'orientation
			$result = $this->Controller->InsertionsBeneficiaires->structuresreferentes();
			$expected = array(
				'1_1' => '« Projet de Ville RSA d\'Aubervilliers»'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Liste simple sans préfixe
			$result = $this->Controller->InsertionsBeneficiaires->structuresreferentes( array( 'prefix' => false ) );
			$expected = array(
				1 => '« Projet de Ville RSA d\'Aubervilliers»'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. Liste d'ids avec préfixe du type d'orientation
			$result = $this->Controller->InsertionsBeneficiaires->structuresreferentes( array( 'type' => InsertionsBeneficiairesComponent::TYPE_IDS ) );
			$expected = array(
				'1_1' => 1
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 4. Liste d'ids sans préfixe
			$result = $this->Controller->InsertionsBeneficiaires->structuresreferentes( array( 'type' => InsertionsBeneficiairesComponent::TYPE_IDS, 'prefix' => false ) );
			$expected = array(
				1 => 1
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 5. Liste d'optgroup avec préfixe du type d'orientation
			$result = $this->Controller->InsertionsBeneficiaires->structuresreferentes( array( 'type' => InsertionsBeneficiairesComponent::TYPE_OPTGROUP ) );
			$expected = array(
				'Socioprofessionnelle' => array(
					'1_1' => '« Projet de Ville RSA d\'Aubervilliers»',
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 6. Liste d'optgroup sans préfixe
			$result = $this->Controller->InsertionsBeneficiaires->structuresreferentes( array( 'type' => InsertionsBeneficiairesComponent::TYPE_OPTGROUP, 'prefix' => false ) );
			$expected = array(
				'Socioprofessionnelle' => array(
					1 => '« Projet de Ville RSA d\'Aubervilliers»',
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode InsertionsBeneficiairesComponent::structuresreferentes()
		 * pour le CG 66 lorsque l'utilisateur est un "externe_ci".
		 *
		 * @covers InsertionsBeneficiairesComponent::structuresreferentes
		 * @covers InsertionsBeneficiairesComponent::_structuresreferentes
		 * @covers InsertionsBeneficiairesComponent::_sqStructurereferenteZonesgeographiques93
		 */
		public function testStructuresreferentesExterneCi66() {
			Configure::write( 'Cg.departement', 66 );
			CakeTestSession::write( 'Auth.User.type', 'externe_ci' );
			CakeTestSession::write( 'Auth.User.structurereferente_id', 1 );

			$result = $this->Controller->InsertionsBeneficiaires->structuresreferentes();
			$expected = array(
				'1_1' => '« Projet de Ville RSA d\'Aubervilliers»',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode InsertionsBeneficiairesComponent::structuresreferentes()
		 * lorsqu'une valeur erronée est envoyée dans la clé "type".
		 *
		 * @covers InsertionsBeneficiairesComponent::structuresreferentes
		 * @covers InsertionsBeneficiairesComponent::_structuresreferentes
		 * @expectedException RuntimeException
		 */
		public function testStructuresreferentesUnknownType() {
			$this->Controller->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'foo' ) );
		}

		/**
		 * Test de la méthode InsertionsBeneficiairesComponent::referents()
		 * pour tous les CG, lorsque l'utilisateur n'est pas un "externe_ci".
		 *
		 * @covers InsertionsBeneficiairesComponent::referents
		 * @covers InsertionsBeneficiairesComponent::_referents
		 * @covers InsertionsBeneficiairesComponent::_sqStructurereferenteZonesgeographiques93
		 */
		public function testReferents() {
			Configure::write( 'Cg.departement', 93 );
			CakeTestSession::write( 'Auth.User.type', 'cg' );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', false );

			// 1. Par défaut, liste simple avec préfixe de la structure référente
			$result = $this->Controller->InsertionsBeneficiaires->referents();
			$expected = array(
				'1_1' => 'MR Dupont Martin'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Liste simple sans préfixe
			$result = $this->Controller->InsertionsBeneficiaires->referents( array( 'prefix' => false ) );
			$expected = array(
				1 => 'MR Dupont Martin'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. Liste d'ids avec préfixe
			$result = $this->Controller->InsertionsBeneficiaires->referents( array( 'type' => InsertionsBeneficiairesComponent::TYPE_IDS ) );
			$expected = array(
				'1_1' => 1
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 4. Liste d'ids sans préfixe
			$result = $this->Controller->InsertionsBeneficiaires->referents( array( 'type' => InsertionsBeneficiairesComponent::TYPE_IDS, 'prefix' => false ) );
			$expected = array(
				1 => 1
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 5. Liste d'optgroup avec préfixe
			$result = $this->Controller->InsertionsBeneficiaires->referents( array( 'type' => InsertionsBeneficiairesComponent::TYPE_OPTGROUP ) );
			$expected = array(
				'« Projet de Ville RSA d\'Aubervilliers»' =>
				array(
					'1_1' => 'MR Dupont Martin'
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 6. Liste d'optgroup sans préfixe
			$result = $this->Controller->InsertionsBeneficiaires->referents( array( 'type' => InsertionsBeneficiairesComponent::TYPE_OPTGROUP, 'prefix' => false ) );
			$expected = array(
				'« Projet de Ville RSA d\'Aubervilliers»' =>
				array(
					1 => 'MR Dupont Martin'
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode InsertionsBeneficiairesComponent::referents()
		 * pour le CG 93 lorsque l'utilisateur est un "externe_ci".
		 *
		 * @covers InsertionsBeneficiairesComponent::referents
		 * @covers InsertionsBeneficiairesComponent::_referents
		 * @covers InsertionsBeneficiairesComponent::_sqStructurereferenteZonesgeographiques93
		 */
		public function testReferentsExterneCi93() {
			Configure::write( 'Cg.departement', 93 );
			CakeTestSession::write( 'Auth.User.type', 'externe_ci' );
			CakeTestSession::write( 'Auth.Zonegeographique', array( 1 => 93001 ) );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', true );

			$result = $this->Controller->InsertionsBeneficiaires->referents();
			$expected = array(
				'1_1' => 'MR Dupont Martin',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode InsertionsBeneficiairesComponent::referents() lorsqu'une
		 * valeur erronée est envoyée dans la clé "type".
		 *
		 * @covers InsertionsBeneficiairesComponent::referents
		 * @covers InsertionsBeneficiairesComponent::_referents
		 * @expectedException RuntimeException
		 */
		public function testReferentsUnknownType() {
			$this->Controller->InsertionsBeneficiaires->referents( array( 'type' => 'foo' ) );
		}

		/**
		 * Test de la méthode InsertionsBeneficiairesComponent::completeOptions()
		 *
		 * @covers InsertionsBeneficiairesComponent::completeOptions
		 * @covers InsertionsBeneficiairesComponent::_structuresreferentes
		 * @covers InsertionsBeneficiairesComponent::_referents
		 */
		public function testCompleteOptions() {
			$result = $this->Controller->InsertionsBeneficiaires->completeOptions(
				array(
					'structurereferente_id' => array(
						'Social' => array(
							2 => 'ADEPT',
						)
					),
					'referent_id' => array(
						'2_3' => 'MME Nom Prénom',
					)
				),
				array(
					'structurereferente_id' => 1,
					'referent_id' => 1
				),
				array(
					'typesorients' => false,
					'structuresreferentes' => array(
						'path' => 'structurereferente_id',
						'type' => 'optgroup',
						'prefix' => false
					),
					'referents' => array(
						'path' => 'referent_id',
						'type' => 'list',
						'prefix' => true
					)
				)
			);
			$expected = array(
				'structurereferente_id' => array(
					'Social' => array(
						2 => 'ADEPT',
					),
					'Socioprofessionnelle' => array(
						1 => '« Projet de Ville RSA d\'Aubervilliers»',
					)
				),
				'referent_id' => array(
					'2_3' => 'MME Nom Prénom',
					'1_1' => 'MR Dupont Martin',
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode InsertionsBeneficiairesComponent::completeOptions()
		 *
		 * @covers InsertionsBeneficiairesComponent::completeOptions
		 * @covers InsertionsBeneficiairesComponent::_structuresreferentes
		 * @covers InsertionsBeneficiairesComponent::_referents
		 */
		public function testCompleteOptionsCompleteArray() {
			CakeTestSession::write( 'Auth.User.type', 'externe_cpdv' );
			CakeTestSession::write( 'Auth.User.structurereferente_id', 2 );

			$result = $this->Controller->InsertionsBeneficiaires->completeOptions(
				array(
					'typeorient_id' => array (
						1 => 'Socioprofessionnelle',
						2 => 'Social'
					),
					'structurereferente_id' => array(
						'Socioprofessionnelle' => array(
							3 => '« Projet de Ville RSA »-Saint Denis-Objectif Emploi',
						),
						'Social' => array(
							2 => 'ADEPT',
						)
					),
					'referent_id' => array(
						'2_3' => 'MME Nom Prénom',
					)
				),
				array(
					'typeorient_id' => 1,
					'structurereferente_id' => 1,
					'referent_id' => 1
				),
				array(
					'typesorients' => array(),
					'structuresreferentes' => array(
						'path' => 'structurereferente_id',
						'type' => 'optgroup',
						'prefix' => false
					),
					'referents' => array(
						'path' => 'referent_id',
						'type' => 'list',
						'prefix' => true
					)
				)
			);
			$expected = array(
				'typeorient_id' => array(
					1 => 'Socioprofessionnelle',
					2 => 'Social'
				),
				'structurereferente_id' => array(
					'Socioprofessionnelle' => array(
						1 => '« Projet de Ville RSA d\'Aubervilliers»',
						3 => '« Projet de Ville RSA »-Saint Denis-Objectif Emploi',
					),
					'Social' => array(
						2 => 'ADEPT'
					)
				),
				'referent_id' => array(
					'1_1' => 'MR Dupont Martin',
					'2_3' => 'MME Nom Prénom'
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode InsertionsBeneficiairesComponent::completeOptions()
		 * pour le CG 976, with_parentid.
		 *
		 * @covers InsertionsBeneficiairesComponent::completeOptions
		 * @covers InsertionsBeneficiairesComponent::_typesorients
		 * @covers InsertionsBeneficiairesComponent::_structuresreferentes
		 * @covers InsertionsBeneficiairesComponent::_referents
		 */
		public function testCompleteOptionsCg976WithParentid() {
			Configure::write( 'Cg.departement', 976 );
			Configure::write( 'with_parentid', true );
			$this->_insertTypeorientChildren();

			$result = $this->Controller->InsertionsBeneficiaires->completeOptions(
				array(
					'typeorient_id' => array(),
					'structurereferente_id' => array(
						'Social' => array(
							2 => 'ADEPT',
						)
					),
					'referent_id' => array(
						'2_3' => 'MME Nom Prénom',
					)
				),
				array(
					'typeorient_id' => 7,
					'structurereferente_id' => 1,
					'referent_id' => 1
				),
				array(
					'typesorients' => array(
						'path' => 'typeorient_id',
					),
					'structuresreferentes' => array(
						'path' => 'structurereferente_id',
						'type' => 'optgroup',
						'prefix' => false
					),
					'referents' => array(
						'path' => 'referent_id',
						'type' => 'list',
						'prefix' => true
					)
				)
			);
			$expected = array(
				'typeorient_id' => array(
					'Socioprofessionnelle' => array(
						7 => 'Socioprofessionnelle - Site de Chiconi'
					)
				),
				'structurereferente_id' => array(
					'Social' => array(
						2 => 'ADEPT'
					),
					'Socioprofessionnelle' => array(
						1 => '« Projet de Ville RSA d\'Aubervilliers»'
					)
				),
				'referent_id' => array(
					'2_3' => 'MME Nom Prénom',
					'1_1' => 'MR Dupont Martin'
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>