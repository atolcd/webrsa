<?php
	/**
	 * Code source de la classe GestionzonesgeosComponentTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'GestionzonesgeosComponent', 'Controller/Component' );
	App::uses( 'CakeTestSession', 'CakeTest.Model/Datasource' );

	/**
	 * GestionzonesgeosTestsController class
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class GestionzonesgeosTestsController extends AppController
	{
		/**
		 * name property
		 *
		 * @var string
		 */
		public $name = 'GestionzonesgeosTestsController';

		/**
		 * uses property
		 *
		 * @var mixed null
		 */
		public $uses = array( 'Contratinsertion' );

		/**
		 * components property
		 *
		 * @var array
		 */
		public $components = array(
			'Gestionzonesgeos'
		);
	}

	/**
	 * La classe GestionzonesgeosComponentTest ...
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class GestionzonesgeosComponentTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Contratinsertion'
		);

		/**
		 * Controller property
		 *
		 * @var GestionzonesgeosComponent
		 */
		public $Controller;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			Configure::write( 'Cg.departement', 93 );
			Configure::write( 'CG.cantons', false );

			parent::setUp();

			$Request = new CakeRequest( 'gestionzonesgeos_tests/index', false );
			$Request->addParams(array( 'controller' => 'gestionzonesgeos_tests', 'action' => 'index' ) );

			$this->Controller = new GestionzonesgeosTestsController( $Request );
			$this->Controller->Components->init( $this->Controller );
			$this->Controller->Gestionzonesgeos->initialize( $this->Controller );

			CakeTestSession::start();
			CakeTestSession::delete( 'Auth' );
		}

		/**
		 * Test de la méthode GestionzonesgeosComponent::completeQuery()
		 */
		public function testCompleteQuery93FiltreZoneGeo() {
			Configure::write( 'Cg.departement', 93 );

			CakeTestSession::write('Auth.User.type', 'externe_cpdv' );
			CakeTestSession::write('Auth.Structurereferente', array( array( 'id' => 69 ) ) );
			CakeTestSession::write('Auth.User.filtre_zone_geo', true );
			CakeTestSession::write('Auth.Zonegeographique', array( 37 => '93071' ) );

			$query = array();
			$result = $this->Controller->Gestionzonesgeos->completeQuery( $query, 'Contratinsertion.structurereferente_id' );
			$expected = array(
				'fields' => array(
					'Contratinsertion.horszone' => '( NOT (( "Adresse"."numcom" IN ( \'93071\' ) )) AND "Contratinsertion"."structurereferente_id" = (69) ) AS "Contratinsertion__horszone"'
				),
				'conditions' => array(
					array(
						'OR' => array(
							'( Adresse.numcom IN ( \'93071\' ) )',
							array(
								'Contratinsertion.structurereferente_id' => array( 69 )
							)
						)
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode GestionzonesgeosComponent::completeQuery()
		 */
		public function testCompleteQuery93PasFiltreZoneGeo() {
			Configure::write( 'Cg.departement', 93 );

			CakeTestSession::write('Auth.User.type', 'externe_cpdv' );
			CakeTestSession::write('Auth.Structurereferente', array( array( 'id' => 69 ) ) );
			CakeTestSession::write('Auth.User.filtre_zone_geo', false );
			CakeTestSession::write('Auth.Zonegeographique', array( 37 => '93071' ) );

			$query = array();
			$result = $this->Controller->Gestionzonesgeos->completeQuery( $query, 'Contratinsertion.structurereferente_id' );
			$expected = array(
				'fields' => array(
					'Contratinsertion.horszone' => '( NOT (1 = 1) AND "Contratinsertion"."structurereferente_id" = (69) ) AS "Contratinsertion__horszone"'
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>