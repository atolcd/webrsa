<?php
	/**
	 * Fichier source de la classe InsertionsAllocatairesComponentTest
	 *
	 * PHP 5.3
	 * @package app.Test.Case.Controller.Component
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'RequestHandlerComponent', 'Controller/Component' );
	App::uses( 'InsertionsAllocatairesComponent', 'Controller/Component' );
	App::uses( 'CakeRequest', 'Network' );
	App::uses( 'CakeResponse', 'Network' );
	App::uses( 'Router', 'Routing' );

	App::uses( 'CakeTestSession', 'CakeTest.Model/Datasource' );

	/**
	 * InsertionsAllocatairesTestController class
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class InsertionsAllocatairesTestController extends Controller
	{

		/**
		 * name property
		 *
		 * @var string 'InsertionsAllocatairesTest'
		 */
		public $name = 'InsertionsAllocatairesTest';

		/**
		 * uses property
		 *
		 * @var mixed null
		 */
		public $uses = null;

		/**
		 * Les paramètres de redirection.
		 *
		 * @var array
		 */
		public $redirected = null;

		/**
		 * components property
		 *
		 * @var array
		 */
		public $components = array(
			'InsertionsAllocataires'
		);


		/**
		 *
		 * @param string|array $url A string or array-based URL pointing to another location within the app,
		 *     or an absolute URL
		 * @param integer $status Optional HTTP status code (eg: 404)
		 * @param boolean $exit If true, exit() will be called after the redirect
		 * @return mixed void if $exit = false. Terminates script if $exit = true
		 */
		public function redirect( $url, $status = null, $exit = true) {
			$this->redirected = array( $url, $status, $exit );
			return false;
		}

	}
	/**
	 * InsertionsAllocatairesTest class
	 *
	 * @package app.Plugin.Search.Test.Case.Controller.Component
	 */
	class InsertionsAllocatairesComponentTest extends CakeTestCase
	{

		/**
		 * Controller property
		 *
		 * @var InsertionsAllocatairesTestController
		 */
		public $Controller;

		/**
		 * name property
		 *
		 * @var string 'InsertionsAllocataires'
		 */
		public $name = 'InsertionsAllocataires';

		/**
		 * Fixtures utilisées dans ce test unitaire.
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
		 * setUp method
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();

			$request = new CakeRequest( 'prgs/index', false );
			$request->addParams(array( 'controller' => 'prgs', 'action' => 'index' ) );
			$this->Controller = new InsertionsAllocatairesTestController( $request );
			$this->Controller->Components->init( $this->Controller );
			$this->Controller->InsertionsAllocataires->initialize( $this->Controller );

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
		 * Test de la méthode InsertionsAllocatairesComponent::sessionKey()
		 */
		public function testSessionKey() {
			$result = $this->Controller->InsertionsAllocataires->sessionKey( 'typesorients', array() );
			$expected = 'Auth.InsertionsAllocataires.typesorients.8739602554c7f3241958e3cc9b57fdecb474d508';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode InsertionsAllocatairesComponent::typesorients()
		 * pour les CG 58 et 93, ainsi qu'au CG 66 lorsque l'utilisateur n'est
		 * pas un "externe_ci".
		 */
		public function testTypesorients() {
			Configure::write( 'Cg.departement', 93 );

			$options = array( 'conditions' => array( 'Typeorient.actif' => 'O' ) );
			$result = $this->Controller->InsertionsAllocataires->typesorients( $options );
			$expected = array (
				3 => 'Emploi',
				2 => 'Social',
				1 => 'Socioprofessionnelle',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$options = array( 'conditions' => array( 'Typeorient.actif' => 'O' ), 'empty' => true );
			$result = $this->Controller->InsertionsAllocataires->typesorients( $options );
			$expected = array (
				0 => 'Non orienté',
				3 => 'Emploi',
				2 => 'Social',
				1 => 'Socioprofessionnelle',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode InsertionsAllocatairesComponent::typesorients()
		 * pour le CG 66 lorsque l'utilisateur est un "externe_ci".
		 */
		public function testTypesorientsExterneCi66() {
			Configure::write( 'Cg.departement', 66 );
			CakeTestSession::write('Auth.User.type', 'externe_ci' );

			$options = array( 'conditions' => array( 'Typeorient.actif' => 'O' ) );
			$result = $this->Controller->InsertionsAllocataires->typesorients( $options );
			$expected = array (
				1 => 'Socioprofessionnelle',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode InsertionsAllocatairesComponent::structuresreferentes()
		 * pour les CG 58 et 93, ainsi qu'au CG 66 lorsque l'utilisateur n'est
		 * pas un "externe_ci".
		 */
		public function testStructuresreferentes() {
			Configure::write( 'Cg.departement', 93 );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', true );
			CakeTestSession::write( 'Auth.Zonegeographique', array( 1 => 93001 ) );

			$result = $this->Controller->InsertionsAllocataires->structuresreferentes();
			$expected = array(
				'1_1' => '« Projet de Ville RSA d\'Aubervilliers»',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = $this->Controller->InsertionsAllocataires->structuresreferentes( array( 'optgroup' => true ) );
			$expected = array(
				'Socioprofessionnelle' => array(
					1 => '« Projet de Ville RSA d\'Aubervilliers»',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = $this->Controller->InsertionsAllocataires->structuresreferentes( array( 'ids' => true ) );
			$expected = array(
				0 => 1,
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = $this->Controller->InsertionsAllocataires->structuresreferentes( array( 'list' => true ) );
			$expected = array(
				1 => '« Projet de Ville RSA d\'Aubervilliers»',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode InsertionsAllocatairesComponent::structuresreferentes()
		 * pour le CG 66 lorsque l'utilisateur est un "externe_ci".
		 */
		public function testStructuresreferentesExterneCi66() {
			Configure::write( 'Cg.departement', 66 );
			CakeTestSession::write( 'Auth.User.type', 'externe_ci' );
			CakeTestSession::write( 'Auth.User.structurereferente_id', 1 );

			$result = $this->Controller->InsertionsAllocataires->structuresreferentes();
			$expected = array(
				'1_1' => '« Projet de Ville RSA d\'Aubervilliers»',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode InsertionsAllocatairesComponent::referents()
		 * pour tous les CG, lorsque l'utilisateur n'est pas un "externe_ci".
		 */
		public function testReferents() {
			Configure::write( 'Cg.departement', 93 );
			CakeTestSession::write( 'Auth.User.type', 'cg' );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', false );

			$result = $this->Controller->InsertionsAllocataires->referents();
			$expected = array(
				1 => 'MR Dupont Martin',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = $this->Controller->InsertionsAllocataires->referents( array( 'prefix' => true ) );
			$expected = array(
				'1_1' => 'MR Dupont Martin',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode InsertionsAllocatairesComponent::referents()
		 * pour le CG 93 lorsque l'utilisateur est un "externe_ci".
		 */
		public function testReferentsExterneCi93() {
			Configure::write( 'Cg.departement', 93 );
			CakeTestSession::write( 'Auth.User.type', 'externe_ci' );
			CakeTestSession::write( 'Auth.Zonegeographique', array( 1 => 93001 ) );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', true );

			$result = $this->Controller->InsertionsAllocataires->referents();
			$expected = array(
				1 => 'MR Dupont Martin',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>