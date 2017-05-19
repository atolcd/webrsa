<?php
	/**
	 * Fichier source de la classe AllocatairesComponentTest
	 *
	 * PHP 5.3
	 * @package app.Test.Case.Controller.Component
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'RequestHandlerComponent', 'Controller/Component' );
	App::uses( 'AllocatairesComponent', 'Controller/Component' );
	App::uses( 'CakeRequest', 'Network' );
	App::uses( 'CakeResponse', 'Network' );
	App::uses( 'Router', 'Routing' );

	App::uses( 'CakeTestSession', 'CakeTest.Model/Datasource' );

	/**
	 * AllocatairesTestController class
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class AllocatairesTestController extends Controller
	{

		/**
		 * name property
		 *
		 * @var string 'AllocatairesTest'
		 */
		public $name = 'AllocatairesTest';

		/**
		 * uses property
		 *
		 * @var mixed null
		 */
		public $uses = array( 'Personne' );

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
			'Allocataires'
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
	 * AllocatairesTest class
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class AllocatairesComponentTest extends CakeTestCase
	{
		/**
		 * name property
		 *
		 * @var string 'Allocataires'
		 */
		public $name = 'Allocataires';

		/**
		 * Fixtures utilisées dans ce test unitaire.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Canton',
			'app.Dossier',
			'app.Foyer',
			'app.Jeton',
			'app.Orientstruct',
			'app.Personne',
			'app.Prestation',
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

			$request = new CakeRequest( 'dossiers/index', false );
			$request->addParams(array( 'controller' => 'dossiers', 'action' => 'index' ) );
			$this->Controller = new AllocatairesTestController( $request );
			$this->Controller->Components->init( $this->Controller );
			$this->Controller->Allocataires->initialize( $this->Controller );

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
		 * Test de la méthode AllocatairesComponent::addQdFilters()
		 */
		public function testAddQdFilters() {
			Configure::write( 'Recherche.qdFilters.Serviceinstructeur', true );
			$sqrecherche = '( SELECT COUNT(dossier_id) FROM suivisinstruction WHERE suivisinstruction.dossier_id = Dossier.id ) = 0';
			CakeTestSession::write( 'Auth.Serviceinstructeur.sqrecherche', $sqrecherche );
			$result = $this->Controller->Allocataires->addQdFilters( array() );

			$expected = array(
				'conditions' =>
				array(
					'( SELECT COUNT(dossier_id) FROM suivisinstruction WHERE suivisinstruction.dossier_id = Dossier.id ) = 0',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode AllocatairesComponent::addAllConditions()
		 */
		public function testAddAllConditions() {
			Configure::write( 'Recherche.qdFilters.Serviceinstructeur', true );
			$sqrecherche = '( SELECT COUNT(dossier_id) FROM suivisinstruction WHERE suivisinstruction.dossier_id = Dossier.id ) = 0';
			CakeTestSession::write( 'Auth.Serviceinstructeur.sqrecherche', $sqrecherche );

			Configure::write( 'CG.cantons', false );

			Configure::write( 'Cg.departement', 66 );
			CakeTestSession::write( 'Auth.User.type', 'externe_ci' );

			CakeTestSession::write( 'Auth.User.filtre_zone_geo', true );
			CakeTestSession::write( 'Auth.Zonegeographique', array( '66000' ) );

			$result = $this->Controller->Allocataires->addAllConditions( array() );
			$expected = array(
				'conditions' => array(
					'( Adresse.numcom IN ( \'66000\' ) )',
					array(
						'Dossier.id IN ( SELECT "foyers"."dossier_id" AS "foyers__dossier_id" FROM "foyers" AS "foyers" INNER JOIN "public"."personnes" AS "personnes" ON ("personnes"."foyer_id" = "foyers"."id") INNER JOIN "public"."orientsstructs" AS "orientsstructs" ON ("orientsstructs"."personne_id" = "personnes"."id") INNER JOIN "public"."prestations" AS "prestations" ON ("prestations"."personne_id" = "personnes"."id" AND "prestations"."natprest" = \'RSA\')  WHERE "foyers"."dossier_id" = "Dossier"."id" AND "orientsstructs"."id" IN ( SELECT "derniersorientations"."id" AS derniersorientations__id FROM orientsstructs AS derniersorientations   WHERE "derniersorientations"."personne_id" = "personnes"."id" AND "derniersorientations"."statut_orient" = \'Orienté\' AND "derniersorientations"."date_valid" IS NOT NULL   ORDER BY "derniersorientations"."date_valid" DESC  LIMIT 1 )    )',
					),
					'( SELECT COUNT(dossier_id) FROM suivisinstruction WHERE suivisinstruction.dossier_id = Dossier.id ) = 0',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode AllocatairesComponent::addAllConditions() avec paramètres
		 */
		public function testAddAllConditionsParams() {
			Configure::write( 'Recherche.qdFilters.Serviceinstructeur', true );
			$sqrecherche = '( SELECT COUNT(dossier_id) FROM suivisinstruction WHERE suivisinstruction.dossier_id = Dossier.id ) = 0';
			CakeTestSession::write( 'Auth.Serviceinstructeur.sqrecherche', $sqrecherche );

			Configure::write( 'CG.cantons', false );

			Configure::write( 'Cg.departement', 66 );
			CakeTestSession::write( 'Auth.User.type', 'externe_ci' );

			CakeTestSession::write( 'Auth.User.filtre_zone_geo', true );
			CakeTestSession::write( 'Auth.Zonegeographique', array( '66000' ) );

			$result = $this->Controller->Allocataires->addAllConditions( array() );
			$expected = array(
				'conditions' => array(
					'( Adresse.numcom IN ( \'66000\' ) )',
					array(
						'Dossier.id IN ( SELECT "foyers"."dossier_id" AS "foyers__dossier_id" FROM "foyers" AS "foyers" INNER JOIN "public"."personnes" AS "personnes" ON ("personnes"."foyer_id" = "foyers"."id") INNER JOIN "public"."orientsstructs" AS "orientsstructs" ON ("orientsstructs"."personne_id" = "personnes"."id") INNER JOIN "public"."prestations" AS "prestations" ON ("prestations"."personne_id" = "personnes"."id" AND "prestations"."natprest" = \'RSA\')  WHERE "foyers"."dossier_id" = "Dossier"."id" AND "orientsstructs"."id" IN ( SELECT "derniersorientations"."id" AS derniersorientations__id FROM orientsstructs AS derniersorientations   WHERE "derniersorientations"."personne_id" = "personnes"."id" AND "derniersorientations"."statut_orient" = \'Orienté\' AND "derniersorientations"."date_valid" IS NOT NULL   ORDER BY "derniersorientations"."date_valid" DESC  LIMIT 1 )    )',
					),
					'( SELECT COUNT(dossier_id) FROM suivisinstruction WHERE suivisinstruction.dossier_id = Dossier.id ) = 0',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}


		/**
		 * Test de la méthode AllocatairesComponent::paginate()
		 */
		public function testPaginate() {
			$query = array(
				'fields' => array(
					'Personne.id',
					'Personne.nom_complet',
				),
				'contain' => false
			);
			$result = $this->Controller->Allocataires->paginate( $query );

			$expected = array(
				array(
					'Personne' => array(
						'id' => 1,
						'nom_complet' => 'MR BUFFIN CHRISTIAN',
					),
				),
				array(
					'Personne' => array(
						'id' => 2,
						'nom_complet' => 'MME DURAND JEANNE',
					),
				),
				array(
					'Personne' => array(
						'id' => 3,
						'nom_complet' => 'MR DURAND RAOUL',
					),
				),
				array(
					'Personne' => array(
						'id' => 4,
						'nom_complet' => 'MR FOO BAR',
					),
				),
				array(
					'Personne' => array(
						'id' => 5,
						'nom_complet' => 'MR FOO BAR',
					),
				),
				array(
					'Personne' => array(
						'id' => 6,
						'nom_complet' => 'MR FOO BAZ',
					),
				),
				array(
					'Personne' => array(
						'id' => 7,
						'nom_complet' => 'MR FOO BAZ',
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>