<?php
	/**
	 * Code source de la classe DossierssimplifiesControllerTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'RequestsmanagerController', 'Controller' );
	
	class RequestsmanagerpublicController extends RequestsmanagerController
	{
		public function prepareAndExplode( $data ) {
			return $this->_prepareAndExplode($data);
		}
	}

	/**
	 * La classe DossierssimplifiesControllerTest ...
	 *
	 * @see http://book.cakephp.org/2.0/en/development/testing.html#testing-controllers
	 *
	 * @package app.Test.Case.Controller
	 */
	class RequestsmanagerControllerTest extends ControllerTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			$this->Requestsmanager = new RequestsmanagerpublicController();
		}

		/**
		 * Test de la méthode DossierssimplifiesControllerTest::add() lors de
		 * l'accès au formulaire.
		 */
		public function testPrepareAndExplode() {
			$data = array(
				'fields' => 'Monmodel.monchamp',
			);
			$result = $this->Requestsmanager->prepareAndExplode($data);
			$expected = array(
				'fields' => array(
					'Monmodel.monchamp',
				),
				'conditions' => array(),
				'order' => array(),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
			
			$data = array(
				'fields' => 'Monmodel.monchamp, Monautremodel.monautrechamp',
			);
			$result = $this->Requestsmanager->prepareAndExplode($data);
			$expected = array(
				'fields' => array(
					'Monmodel.monchamp',
					'Monautremodel.monautrechamp',
				),
				'conditions' => array(),
				'order' => array(),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
			
			$data = array(
				'fields' => '"Monmodel"."monchamp" AS "Monmodel__monchamp", "Monautremodel"."monautrechamp" AS "Monautremodel__monautrechamp"'
			);
			$result = $this->Requestsmanager->prepareAndExplode($data);
			$expected = array(
				'fields' => array(
					'"Monmodel"."monchamp" AS "Monmodel__monchamp"',
					'"Monautremodel"."monautrechamp" AS "Monautremodel__monautrechamp"',
				),
				'conditions' => array(),
				'order' => array(),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
			
			$data = array(
				'fields' => '("Monmodel"."monchamp" || \' concatenation\') AS "Monmodel__monchamp", ("Monautremodel"."monautrechamp" || \' concatenation\') AS "Monautremodel__monautrechamp"'
			);
			$result = $this->Requestsmanager->prepareAndExplode($data);
			$expected = array(
				'fields' => array(
					'("Monmodel"."monchamp" || \' concatenation\') AS "Monmodel__monchamp"',
					'("Monautremodel"."monautrechamp" || \' concatenation\') AS "Monautremodel__monautrechamp"',
				),
				'conditions' => array(),
				'order' => array(),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
			
			$data = array(
				'fields' => 'MAFUNCTION("Monmodel"."monchamp", 1, 2) AS "Monmodel__monchamp", MAFUNCTION("Monautremodel"."monautrechamp",1 ,2) AS "Monautremodel__monautrechamp"'
			);
			$result = $this->Requestsmanager->prepareAndExplode($data);
			$expected = array(
				'fields' => array(
					'MAFUNCTION("Monmodel"."monchamp", 1, 2) AS "Monmodel__monchamp"',
					'MAFUNCTION("Monautremodel"."monautrechamp",1 ,2) AS "Monautremodel__monautrechamp"',
				),
				'conditions' => array(),
				'order' => array(),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
			
			$data = array(
				'fields' => '( SUBSTR("Adresse"."codepos", 1, 2) ) AS "Adresse__numdep", ("Personne"."qual" || \' \' || "Personne"."nom" || \' \' || "Personne"."prenom") AS "Personne__nom_complet"'
			);
			$result = $this->Requestsmanager->prepareAndExplode($data);
			$expected = array(
				'fields' => array(
					'( SUBSTR("Adresse"."codepos", 1, 2) ) AS "Adresse__numdep"',
					'("Personne"."qual" || \' \' || "Personne"."nom" || \' \' || "Personne"."prenom") AS "Personne__nom_complet"',
				),
				'conditions' => array(),
				'order' => array(),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
			
			$data = array(
				'order' => '( SUBSTR("Adresse"."codepos", 1, 2) ) AS "Adresse__numdep", "Personne"."dtnai" DESC'
			);
			$result = $this->Requestsmanager->prepareAndExplode($data);
			$expected = array(
				'fields' => array(),
				'conditions' => array(),
				'order' => array(
					'( SUBSTR("Adresse"."codepos", 1, 2) ) AS "Adresse__numdep"' => 'ASC',
					'"Personne"."dtnai"' => 'DESC'
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
			
			$data = array(
				'conditions' => '"Adresse"."codepos" IS NOT NULL AND ( SUBSTR("Adresse"."codepos", 1, 2) ) != \'66\''
			);
			$result = $this->Requestsmanager->prepareAndExplode($data);
			$expected = array(
				'fields' => array(),
				'conditions' => array(
					'"Adresse"."codepos" IS NOT NULL',
					'( SUBSTR("Adresse"."codepos", 1, 2) ) != \'66\'',
				),
				'order' => array(),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
			
			$data = array(
				'conditions' => '"Adresse"."codepos" IS NOT NULL OR ( "Adresse"."codepos" = \'66\' AND "Adresse"."numcom" LIKE \'%66%\' ) AND "Personne"."id" IS NOT NULL'
			);
			$result = $this->Requestsmanager->prepareAndExplode($data);
			$expected = array(
				'fields' => array(),
				'conditions' => array(
					'"Adresse"."codepos" IS NOT NULL OR ( "Adresse"."codepos" = \'66\' AND "Adresse"."numcom" LIKE \'%66%\' )',
					'"Personne"."id" IS NOT NULL'
				),
				'order' => array(),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>