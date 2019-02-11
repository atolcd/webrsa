<?php
	/**
	 * Code source de la classe WebrsaPermissionsTest.
	 *
	 * @package app.Test.Case.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaPermissions', 'Utility' );
	App::uses( 'CakeTestSession', 'CakeTest.Model/Datasource' );

	/**
	 * La classe WebrsaPermissionsTest réalise les tests unitaires de la classe
	 * utilitaire WebrsaPermissions.
	 *
	 * @package app.Test.Case.Utility
	 */
	class WebrsaPermissionsTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisées dans ce test unitaire.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Dossier',
			'app.Foyer',
			'app.Orientstruct',
			'app.Personne',
			'app.Prestation',
		);

		/**
		 * Données utilisées dans les tests.
		 *
		 * @var array
		 */
		public $data = array(
			'dossierData' => array(
				'Foyer' => array(
					'Personne' => array(
						array(
							'Orientstruct' => array(
								'structurereferente_id' => 1
							)
						)
					)
				),
				'Adressefoyer' => array(
					'01' => array(
						'codeinsee' => '93066'
					),
					'02' => array(
						'codeinsee' => '93067'
					)
				)
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
		 * setUp method
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();

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
		 * Test de la méthode WebrsaPermissions::check
		 * @covers WebrsaPermissions::check
		 */
		public function testCheck() {
			// 1. Contrôleur CakeError
			$result = WebrsaPermissions::check( 'CakeError', 'index' );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. aucunDroit
			$result = WebrsaPermissions::check( 'Users', 'login' );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. commeDroit
			// 3.1. commeDroit true
			$result = WebrsaPermissions::check( 'Users', 'add' );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3.2. commeDroit false
			CakeTestSession::write( WebrsaPermissions::$sessionPermissionsKey.'.Users:edit', true );
			$result = WebrsaPermissions::check( 'Users', 'add' );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 4. Au niveau du module
			CakeTestSession::delete( WebrsaPermissions::$sessionPermissionsKey );
			CakeTestSession::write( WebrsaPermissions::$sessionPermissionsKey.'.Module:Users', true );
			$result = WebrsaPermissions::check( 'Users', 'add' );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode WebrsaPermissions::checkDossier
		 * @covers WebrsaPermissions::checkDossier
		 * @covers WebrsaPermissions::_structuresreferentesUser
		 * @covers WebrsaPermissions::_checkZoneGeographique
		 */
		public function testCheckDossier() {
			Configure::write( 'Cg.departement', 93 );

			// 1. Sans avoir les droits sur l'action
			CakeTestSession::delete( 'Auth' );
			$result = WebrsaPermissions::checkDossier( 'Orientsstructs', 'add', $this->data['dossierData'] );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Avec les droits sur l'action mais pas sur la zone géographique
			CakeTestSession::delete( 'Auth' );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', true );
			CakeTestSession::write( 'Auth.Zonegeographique', array() );
			CakeTestSession::write( WebrsaPermissions::$sessionPermissionsKey.'.Module:Orientsstructs', true );
			$result = WebrsaPermissions::checkDossier( 'Orientsstructs', 'add', $this->data['dossierData'] );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. Avec les droits sur l'action et sur la zone géographique
			CakeTestSession::delete( 'Auth' );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', true );
			CakeTestSession::write( 'Auth.Zonegeographique', array( '93066' => '93066' ) );
			CakeTestSession::write( WebrsaPermissions::$sessionPermissionsKey.'.Module:Orientsstructs', true );
			$result = WebrsaPermissions::checkDossier( 'Orientsstructs', 'add', $this->data['dossierData'] );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 4. Avec les droits sur l'action et sans limite de zone géographique
			CakeTestSession::delete( 'Auth' );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', false );
			CakeTestSession::write( WebrsaPermissions::$sessionPermissionsKey.'.Module:Orientsstructs', true );
			$result = WebrsaPermissions::checkDossier( 'Orientsstructs', 'add', $this->data['dossierData'] );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 5. Avec les droits sur l'action, sur la zone géographique de rg 02 alors qu'on modifie
			CakeTestSession::delete( 'Auth' );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', true );
			CakeTestSession::write( 'Auth.Zonegeographique', array( '93067' => '93067' ) );
			CakeTestSession::write( WebrsaPermissions::$sessionPermissionsKey.'.Module:Orientsstructs', true );
			$result = WebrsaPermissions::checkDossier( 'Orientsstructs', 'add', $this->data['dossierData'] );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 6. Avec les droits sur l'action, sur la zone géographique de rg 02 alors qu'on lit
			CakeTestSession::delete( 'Auth' );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', true );
			CakeTestSession::write( 'Auth.Zonegeographique', array( '93067' => '93067' ) );
			CakeTestSession::write( WebrsaPermissions::$sessionPermissionsKey.'.Module:Orientsstructs', true );
			$result = WebrsaPermissions::checkDossier( 'Orientsstructs', 'index', $this->data['dossierData'] );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode WebrsaPermissions::checkDossier pour le CG 66 lorsque
		 * l'utilisateur connecté est un chargé d'insertion.
		 *
		 * @covers WebrsaPermissions::checkDossier
		 * @covers WebrsaPermissions::_structuresreferentesUser
		 * @covers WebrsaPermissions::_checkZoneGeographique
		 */
		public function testCheckDossierExterneCi66() {
			// 1. Avec accès à la structure référente
			Configure::write( 'Cg.departement', 66 );
			CakeTestSession::delete( 'Auth' );
			CakeTestSession::write( 'Auth.User.type', 'externe_ci' );
			CakeTestSession::write( 'Auth.Structurereferente', array( array( 'id' => 1 ) ) );
			CakeTestSession::write( WebrsaPermissions::$sessionPermissionsKey.'.Module:Orientsstructs', true );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', true );
			CakeTestSession::write( 'Auth.Zonegeographique', array( '93066' => '93066' ) );
			$result = WebrsaPermissions::checkDossier( 'Orientsstructs', 'index', $this->data['dossierData'] );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Sans accès à la structure référente
			Configure::write( 'Cg.departement', 66 );
			CakeTestSession::delete( 'Auth' );
			CakeTestSession::write( 'Auth.User.type', 'externe_ci' );
			CakeTestSession::write( 'Auth.Structurereferente', array( array( 'id' => 5 ) ) );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', true );
			CakeTestSession::write( 'Auth.Zonegeographique', array( '93066' => '93066' ) );
			CakeTestSession::write( WebrsaPermissions::$sessionPermissionsKey.'.Module:Orientsstructs', true );
			$result = WebrsaPermissions::checkDossier( 'Orientsstructs', 'index', $this->data['dossierData'] );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode WebrsaPermissions::checkDossier pour un autre
		 * département que le 66 ou le 93.
		 *
		 * @covers WebrsaPermissions::checkDossier
		 * @covers WebrsaPermissions::_structuresreferentesUser
		 * @covers WebrsaPermissions::_checkZoneGeographique
		 */
		public function testCheckDossier58() {
			// 1. Avec accès à la structure référente
			Configure::write( 'Cg.departement', 58 );
			CakeTestSession::delete( 'Auth' );
			CakeTestSession::write( WebrsaPermissions::$sessionPermissionsKey.'.Module:Orientsstructs', true );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', true );
			CakeTestSession::write( 'Auth.Zonegeographique', array( '93066' => '93066' ) );
			$result = WebrsaPermissions::checkDossier( 'Orientsstructs', 'index', $this->data['dossierData'] );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Sans accès à la structure référente
			Configure::write( 'Cg.departement', 66 );
			CakeTestSession::delete( 'Auth' );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', true );
			CakeTestSession::write( 'Auth.Zonegeographique', array( '93067' => '93067' ) );
			CakeTestSession::write( WebrsaPermissions::$sessionPermissionsKey.'.Module:Orientsstructs', true );
			$result = WebrsaPermissions::checkDossier( 'Orientsstructs', 'index', $this->data['dossierData'] );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode WebrsaPermissions::checkDossier pour le CG 66 lorsque
		 * l'utilisateur connecté est un chargé d'insertion.
		 *
		 * @covers WebrsaPermissions::conditionsDossier
		 */
		public function testConditionsDossierExterneCi66() {
			// 1. Avec accès limité à une structure référente
			Configure::write( 'Cg.departement', 66 );
			CakeTestSession::delete( 'Auth' );
			CakeTestSession::write( 'Auth.User.type', 'externe_ci' );
			CakeTestSession::write( 'Auth.Structurereferente', array( array( 'id' => 1 ) ) );
			CakeTestSession::write( WebrsaPermissions::$sessionPermissionsKey.'.Module:Orientsstructs', true );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', true );
			CakeTestSession::write( 'Auth.Zonegeographique', array( '93066' => '93066' ) );
			$result = WebrsaPermissions::conditionsDossier();
			$expected = array(
				'Dossier.id IN ( SELECT "foyers"."dossier_id" AS "foyers__dossier_id" FROM "foyers" AS "foyers" INNER JOIN "public"."personnes" AS "personnes" ON ("personnes"."foyer_id" = "foyers"."id") INNER JOIN "public"."orientsstructs" AS "orientsstructs" ON ("orientsstructs"."personne_id" = "personnes"."id") INNER JOIN "public"."prestations" AS "prestations" ON ("prestations"."personne_id" = "personnes"."id" AND "prestations"."natprest" = \'RSA\')  WHERE "foyers"."dossier_id" = "Dossier"."id" AND "orientsstructs"."id" IN ( SELECT "derniersorientations"."id" AS derniersorientations__id FROM orientsstructs AS derniersorientations   WHERE "derniersorientations"."personne_id" = "personnes"."id" AND "derniersorientations"."statut_orient" = \'Orienté\' AND "derniersorientations"."date_valid" IS NOT NULL   ORDER BY "derniersorientations"."date_valid" DESC  LIMIT 1 ) AND "orientsstructs"."structurereferente_id" = (1)    )'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Sans limitation d'accès à la structure référente
			Configure::write( 'Cg.departement', 66 );
			CakeTestSession::delete( 'Auth' );
			CakeTestSession::write( 'Auth.User.type', 'cg' );
			CakeTestSession::write( 'Auth.User.filtre_zone_geo', true );
			CakeTestSession::write( 'Auth.Zonegeographique', array( '93066' => '93066' ) );
			CakeTestSession::write( WebrsaPermissions::$sessionPermissionsKey.'.Module:Orientsstructs', true );
			$result = WebrsaPermissions::conditionsDossier();
			$expected = array();
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode WebrsaPermissions::checkD1D2
		 * @covers WebrsaPermissions::checkD1D2
		 * @covers WebrsaPermissions::_structuresreferentesUser
		 */
		public function testCheckD1D2() {
			// 1. Sans limitation au niveau de la structure référente
			CakeTestSession::delete( 'Auth' );
			$result = WebrsaPermissions::checkD1D2( 1, true );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Avec limitation au niveau de la structure référente
			// 2.1. Lorsque l'utilisateur y a accès
			CakeTestSession::delete( 'Auth' );
			CakeTestSession::write( 'Auth.Structurereferente', array( array( 'id' => 1 ) ) );
			$result = WebrsaPermissions::checkD1D2( 1, true );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
			// 2.2. Lorsque l'utilisateur n'y a pas accès
			CakeTestSession::delete( 'Auth' );
			CakeTestSession::write( 'Auth.Structurereferente', array( array( 'id' => 5 ) ) );
			$result = WebrsaPermissions::checkD1D2( 1, true );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. Sans permission
			CakeTestSession::delete( 'Auth' );
			$result = WebrsaPermissions::checkD1D2( 1, false );
			$expected = false;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 4. En retournant un string
			// 4.1. Avec permission, tous les accès
			CakeTestSession::delete( 'Auth' );
			$result = WebrsaPermissions::checkD1D2( 1, true, true );
			$expected = '( ( count( array (
) ) == 0 || ( in_array( \'1\', array (
) ) ) ) && ( \'1\' == \'1\' ) )';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
			// 4.1. Avec permission, sans les accès
			CakeTestSession::delete( 'Auth' );
			CakeTestSession::write( 'Auth.Structurereferente', array( array( 'id' => 5 ) ) );
			$result = WebrsaPermissions::checkD1D2( 1, true, true );
			$expected = '( ( count( array (
  0 => 5,
) ) == 0 || ( in_array( \'1\', array (
  0 => 5,
) ) ) ) && ( \'1\' == \'1\' ) )';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>