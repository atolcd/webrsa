<?php
	/**
	 * Validation2RulesComparisonBehaviorTest file
	 *
	 * PHP 5.3
	 *
	 * @package Validation2
	 * @subpackage Test.Case.Model.Behavior
	 */
	App::uses( 'Model', 'Model' );
	App::uses( 'AppModel', 'Model' );

	/**
	 * ExtraBasicValidation2RulesTest class
	 *
	 * @package Validation2
	 * @subpackage Test.Case.Model.Behavior
	 */
	class Validation2RulesComparisonBehaviorTest extends CakeTestCase
	{
		/**
		 * Fixtures associated with this test case
		 *
		 * @var array
		 */
		public $fixtures = array(
			'plugin.Validation2.Validation2Site'
		);

		/**
		 * Method executed before each test
		 */
		public function setUp() {
			parent::setUp();
			$this->Site = ClassRegistry::init(
				array(
					'class' => 'Validation2.Validation2Site',
					'alias' => 'Site',
					'ds' => 'test',
				)
			);
			$this->Site->Behaviors->attach( 'Validation2.Validation2RulesComparison' );
		}

		/**
		 * Method executed after each test
		 */
		public function tearDown() {
			unset( $this->Site );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Validation2RulesComparisonBehavior::allEmpty() du
		 * plugin Validation2.
		 *
		 * @return void
		 */
		public function testAllEmpty() {
			$result = $this->Site->allEmpty( null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'phone' => '', 'fax' => null );
			$this->Site->create( $data );
			$result = $this->Site->allEmpty( array( 'phone' => '' ), 'fax' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'phone' => ' ', 'fax' => null );
			$this->Site->create( $data );
			$result = $this->Site->allEmpty( array( 'phone' => ' ' ), 'fax' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation2RulesComparisonBehavior::notEmptyIf() du
		 * plugin Validation2.
		 *
		 * @return void
		 */
		public function testNotEmptyIf() {
			$result = $this->Site->notEmptyIf( null, null, null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'phone' => 'X', 'fax' => null );
			$this->Site->create( $data );
			$result = $this->Site->notEmptyIf( array( 'phone' => 'X' ), 'fax', true, array( null ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'phone' => '', 'fax' => null );
			$this->Site->create( $data );
			$result = $this->Site->notEmptyIf( array( 'phone' => '' ), 'fax', true, array( null ) );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'phone' => '', 'fax' => 'P' );
			$this->Site->create( $data );
			$result = $this->Site->notEmptyIf( array( 'phone' => '' ), 'fax', true, array( 'P' ) );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'phone' => '', 'fax' => 'F' );
			$this->Site->create( $data );
			$result = $this->Site->notEmptyIf( array( 'phone' => '' ), 'fax', true, array( 'P' ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation2RulesComparisonBehavior::notNullIf()
		 *
		 * @return void
		 */
		public function testNotNullIf() {
			$result = $this->Site->notNullIf( null, null, null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'phone' => '0606060606', 'fax' => null );
			$this->Site->create( $data );
			$result = $this->Site->notNullIf(
				array( 'phone' => '0606060606' ),
				'fax',
				true,
				array( null )
			);
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'phone' => null, 'fax' => null );
			$this->Site->create( $data );
			$result = $this->Site->notNullIf(
				array( 'phone' => null ),
				'fax',
				false,
				array( null )
			);
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'phone' => '0606060606', 'fax' => '0404040404' );
			$this->Site->create( $data );
			$result = $this->Site->notNullIf(
				array( 'phone' => '0606060606' ),
				'fax',
				true,
				array( null )
			);
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation2RulesComparisonBehavior::greaterThanIfNotZero
		 *
		 * @return void
		 */
		public function testGreaterThanIfNotZero() {
			$result = $this->Site->greaterThanIfNotZero( null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$this->Site->create( array( 'value' => 1, 'reference' => 1 ) );
			$result = $this->Site->greaterThanIfNotZero( array( 'value' => 1 ), 'reference' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$this->Site->create( array( 'value' => 1, 'reference' => 2 ) );
			$result = $this->Site->greaterThanIfNotZero( array( 'value' => 1 ), 'reference' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation2RulesComparisonBehavior::compareDates
		 *
		 * @return void
		 */
		public function testCompareDates() {
			$result = $this->Site->compareDates( null, null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$this->Site->create( array( 'from' => null, 'to' => null ) );
			$result = $this->Site->compareDates( array( 'from' => null ), 'to', 'null' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'from' => '2012-01-01', 'to' => '2012-01-02' );
			$this->Site->create( $data );

			$result = $this->Site->compareDates( array( 'from' => $data['from'] ), 'to', '<' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->compareDates( array( 'from' => $data['from'] ), 'to', '*' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->compareDates( array( 'from' => $data['from'] ), 'to', '>' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'from' => '2012-01-02', 'to' => '2012-01-01' );
			$this->Site->create( $data );
			$result = $this->Site->compareDates( array( 'from' => $data['from'] ), 'to', '<=' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'from' => '2012-01-01', 'to' => '2012-01-01' );
			$this->Site->create( $data );
			$result = $this->Site->compareDates( array( 'from' => $data['from'] ), 'to', '==' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation2RulesComparisonBehavior::foo
		 *
		 * @return void
		 */
		public function testFoo() {
			$result = $this->Site->foo( null, null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->foo( array(), null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$this->Site->create( array( 'Site' => array( 'value' => null, 'othervalue' => null ) ) );
			$result = $this->Site->foo( array( 'value' => null ), 'othervalue' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$this->Site->create( array( 'Site' => array( 'value' => true, 'othervalue' => false ) ) );
			$result = $this->Site->foo( array( 'value' => true ), 'othervalue' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$this->Site->create( array( 'Site' => array( 'value' => null, 'othervalue' => 5 ) ) );
			$result = $this->Site->foo( array( 'value' => null ), 'othervalue' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$this->Site->create( array( 'Site' => array( 'value' => false, 'othervalue' => null ) ) );
			$result = $this->Site->foo( array( 'value' => false ), 'othervalue' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation2RulesComparisonBehavior::checkUnique
		 *
		 * @return void
		 */
		public function testCheckUnique() {
			$result = $this->Site->checkUnique( null, null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'name' => 'CakePHP', 'birthday' => null );
			$this->Site->create( $data );

			$result = $this->Site->checkUnique( array( 'name' => $data['name'] ), null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->checkUnique( array( 'name' => $data['name'] ), array( 'name', 'birthday' ) );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$data = array( 'id' => 1, 'name' => 'CakePHP', 'birthday' => null );
			$this->Site->create( $data );

			$result = $this->Site->checkUnique( array( 'name' => $data['name'] ), array( 'name', 'birthday' ) );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->Site->checkUnique( array( 'name' => $data['name'] ), array( 'name', 'user_id' ) );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Validation2RulesComparisonBehavior::checkDureeDates
		 *
		 * @return void
		 */
		public function testCheckDureeDates() {
			$result = $this->Site->checkDureeDates( null, null, null );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// Avec une durée incorrecte
			$data = array( 'dd' => '2015-05-20', 'df' => '2016-05-19' );
			$this->Site->create( $data );

			$result = $this->Site->checkDureeDates( array( 'duree' => '10' ), 'dd', 'df' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// Avec une durée correcte
			$data = array( 'dd' => '2015-05-20', 'df' => '2016-03-19' );
			$this->Site->create( $data );

			$result = $this->Site->checkDureeDates( array( 'duree' => '10' ), 'dd', 'df' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
			
			// Durée de 3, 6, 9 et 12 mois
			foreach( array(3,6,9,12) as $duree ){
				// Pour chaques jours d'une année
				$timestampDebut = strtotime( '2014-12-31' );
				for($i=1;$i<366; $i++){
					$timestampDebut = strtotime( '+1 days', $timestampDebut );
					
					// Même fonctionnement que le javascript (+X mois -1 jour)
					$timestampFin = strtotime( '-1 days', strtotime( '+' . $duree . ' months', $timestampDebut ) );
					
					$data = array( 'dd' => date('Y-m-d', $timestampDebut), 'df' => date('Y-m-d', $timestampFin) );
					$this->Site->create( $data );
					$result = $this->Site->checkDureeDates( array( 'duree' => $duree ), 'dd', 'df' );
					$expected = true;
					$this->assertEquals( $expected, $result, var_export( $result, true ) );
				}
			}
		}
		
		/**
		 * Test de la fonction Validation2RulesComparisonBehavior::emptyIf()
		 */
		public function testEmptyIf() {
			// Cas d'utilisation classique
			
			$this->Site->create(array('phone' => 'X', 'fax' => 'Y'));
			$result = $this->Site->emptyIf(array('fax' => 'Y'), 'phone', true, array(null)); // Vide si phone === null
			$expected = true;
			$this->assertEquals( $expected, $result, "Validation ok si la condition n'est pas remplie");
			
			$this->Site->create(array('phone' => null, 'fax' => null));
			$result = $this->Site->emptyIf(array('fax' => null), 'phone', true, array(null)); // Vide si phone === null
			$expected = true;
			$this->assertEquals( $expected, $result, "Validation ok si la condition est remplie et que le champ testé est vide");
			
			$this->Site->create(array('phone' => null, 'fax' => 'Y'));
			$result = $this->Site->emptyIf(array('fax' => 'Y'), 'phone', true, array(null)); // Vide si phone === null
			$expected = false;
			$this->assertEquals( $expected, $result, "Echec de validation car la condition est rempli mais le champ testé n'est pas vide");
			
			// Mauvaise utilisation
			$result = $this->Site->emptyIf(null, null, null, null);
			$expected = false;
			$this->assertEquals( $expected, $result, "Params non défini");
			
			$result = $this->Site->emptyIf(array(), 'phone', true, array(null));
			$expected = true;
			$this->assertEquals( $expected, $result, "Pas de champs à vérifier");
		}
	}
?>