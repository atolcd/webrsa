<?php
	/**
	 * Code source de la classe Histochoixcer93Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Histochoixcer93', 'Model' );

	/**
	 * Classe Histochoixcer93Test.
	 *
	 * @package app.Test.Case.Model
	 */
	class Histochoixcer93Test extends CakeTestCase
	{
		/**
		 * Fixtures utilisées dans ce test unitaire.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Cer93',
			'app.Cer93Sujetcer93',
			'app.Commissionep',
			'app.Contratcomplexeep93',
			'app.Contratinsertion',
			'app.Dossierep',
			'app.Histochoixcer93',
			'app.Nonrespectsanctionep93',
			'app.Orientstruct',
			'app.Passagecommissionep',
			'app.Personne',
			'app.Propopdo',
			'app.Relancenonrespectsanctionep93',
			'app.Sujetcer93',
		);

		/**
		 * Méthode exécutée avant chaque test.
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();
			Configure::write( 'Cg.departement', 93 );
			$this->Histochoixcer93 = ClassRegistry::init( 'Histochoixcer93' );
		}

		/**
		 * Méthode exécutée après chaque test.
		 *
		 * @return void
		 */
		public function tearDown() {
			parent::tearDown();
			unset( $this->Histochoixcer93 );
		}

		/**
		 * Test de la méthode Histochoixcer93::prepareFormData() avec une erreur
		 *
		 * @return void
		 */
		public function testPrepapreFormDataError() {
			$this->expectError( 'PHPUnit_Framework_Error_Notice' );
			$contratinsertion = array(
				'Contratinsertion' => array(
					'decision_ci' => 'V'
				),
			);
			$result = $this->Histochoixcer93->prepareFormData( $contratinsertion, '03attdecisioncg', 1 );
		}

		/**
		 * Test de la méthode Histochoixcer93::prepareFormData()
		 *
		 * @group medium
		 * @return void
		 */
		public function testPrepareFormData() {
			// 1°) Ajout à l'étape 03attdecisioncg
			$contratinsertion = array(
				'Contratinsertion' => array(
					'decision_ci' => 'E'
				),
				'Cer93' => array(
					'id' => 1,
					'duree' => 3,
					'Histochoixcer93' => array(
						array(
							'id' => '1',
							'cer93_id' => '1',
							'user_id' => '1',
							'commentaire' => 'Commentaire ...',
							'formeci' => 'S',
							'etape' => '02attdecisioncpdv',
							'prevalide' => null,
							'decisioncs' => null,
							'decisioncadre' => null,
							'datechoix' => '2012-10-24',
							'created' => '2012-10-24 11:44:38',
							'modified' => '2012-10-24 11:44:38',
							'Commentairenormecer93' => array(),
						),
					),
				),
			);
			$result = $this->Histochoixcer93->prepareFormData( $contratinsertion, '03attdecisioncg', 1 );
			$expected = array (
				'Histochoixcer93' =>  array (
					'cer93_id' => 1,
					'user_id' => 1,
					'etape' => '03attdecisioncg',
					'duree' => '3',
					'formeci' => 'S',
					'commentaire' => 'Commentaire ...',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			/*// 2°) Modification à l'étape 03attdecisioncg
			$contratinsertion = array(
				'Contratinsertion' => array(
					'decision_ci' => 'E'
				),
				'Cer93' => array(
					'id' => 1,
					'Histochoixcer93' => array(
						array(
							'id' => '1',
							'cer93_id' => '1',
							'user_id' => '1',
							'commentaire' => 'Commentaire ...',
							'formeci' => 'S',
							'etape' => '02attdecisioncpdv',
							'prevalide' => null,
							'decisioncs' => null,
							'decisioncadre' => null,
							'datechoix' => '2012-10-24',
							'created' => '2012-10-24 11:44:38',
							'modified' => '2012-10-24 11:44:38',
						),
						array(
							'id' => '2',
							'cer93_id' => '1',
							'user_id' => '2',
							'commentaire' => 'Commentaire ...',
							'formeci' => 'S',
							'etape' => '03attdecisioncg',
							'prevalide' => null,
							'decisioncs' => null,
							'decisioncadre' => null,
							'datechoix' => '2012-10-24',
							'created' => '2012-10-24 12:44:38',
							'modified' => '2012-10-24 12:44:38',
						),
					),
				),
			);
			$result = $this->Histochoixcer93->prepareFormData( $contratinsertion, '03attdecisioncg', 1 );
			$expected = array(
				'Histochoixcer93' => array(
					'id' => '2',
					'cer93_id' => '1',
					'user_id' => '2',
					'commentaire' => 'Commentaire ...',
					'formeci' => 'S',
					'etape' => '03attdecisioncg',
					'prevalide' => NULL,
					'decisioncs' => NULL,
					'decisioncadre' => NULL,
					'datechoix' => '2012-10-24',
					'created' => '2012-10-24 12:44:38',
					'modified' => '2012-10-24 12:44:38',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );*/
		}

		/**
		 * Test de la méthode Histochoixcer93::saveDecision() à l'étape
		 * 02attdecisioncpdv.
		 *
		 * @group medium
		 * @return void
		 */
		public function testSaveDecision02attdecisioncpdv() {
			$data = array(
				'Histochoixcer93' => array(
					'id' => '',
					'cer93_id' => '2',
					'user_id' => '6',
					'formeci' => 'S',
					'commentaire' => 'drsg',
					'datechoix' => array(
						'day' => '25',
						'month' => '10',
						'year' => '2012'
					),
					'etape' => '02attdecisioncpdv'
				)
			);
			$result = $this->Histochoixcer93->saveDecision( $data );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// On vérifie que l'on a bien enregistré les informations
			$result = $this->Histochoixcer93->find(
				'first',
				array(
					'fields' => array(
						'Cer93.id',
						'Cer93.contratinsertion_id',
						'Cer93.user_id',
						'Cer93.positioncer',
						'Cer93.formeci',
						'Histochoixcer93.id',
						'Histochoixcer93.cer93_id',
						'Histochoixcer93.user_id',
						'Histochoixcer93.etape',
						'Histochoixcer93.datechoix',
						'Contratinsertion.id',
						'Contratinsertion.personne_id',
						'Contratinsertion.decision_ci',
						'Contratinsertion.forme_ci',
						'Contratinsertion.datevalidation_ci',
						'Contratinsertion.datedecision',
					),
					'conditions' => array(
						'Histochoixcer93.id' => $this->Histochoixcer93->id
					),
					'joins' => array(
						$this->Histochoixcer93->join( 'Cer93' ),
						$this->Histochoixcer93->Cer93->join( 'Contratinsertion' ),
					),
					'contain' => false
				)
			);

			$expected = array(
				'Cer93' => array(
					'id' => 2,
					'contratinsertion_id' => 2,
					'user_id' => 1,
					'positioncer' => '02attdecisioncpdv',
					'formeci' => null,
				),
				'Histochoixcer93' => array(
					'id' => 1,
					'cer93_id' => 2,
					'user_id' => 6,
					'etape' => '02attdecisioncpdv',
					'datechoix' => '2012-10-25',
				),
				'Contratinsertion' => array(
					'id' => 2,
					'personne_id' => 1,
					'decision_ci' => 'E',
					'forme_ci' => NULL,
					'datevalidation_ci' => NULL,
					'datedecision' => NULL,
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Histochoixcer93::saveDecision() à l'étape
		 * 03attdecisioncg, sans rejet.
		 *
		 * @group medium
		 * @return void
		 */
		public function testSaveDecision03attdecisioncgSansRejet() {
			$data = array(
				'Histochoixcer93' => array(
					'id' => '',
					'cer93_id' => '2',
					'user_id' => '6',
					'formeci' => 'S',
					'commentaire' => 'drsg',
					'datechoix' => array(
						'day' => '25',
						'month' => '10',
						'year' => '2012'
					),
					'etape' => '03attdecisioncg',
					'isrejet' => 0
				)
			);
			$result = $this->Histochoixcer93->saveDecision( $data );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// On vérifie que l'on a bien enregistré les informations
			$result = $this->Histochoixcer93->find(
				'first',
				array(
					'fields' => array(
						'Cer93.id',
						'Cer93.contratinsertion_id',
						'Cer93.user_id',
						'Cer93.positioncer',
						'Cer93.formeci',
						'Histochoixcer93.id',
						'Histochoixcer93.cer93_id',
						'Histochoixcer93.user_id',
						'Histochoixcer93.etape',
						'Histochoixcer93.datechoix',
						'Contratinsertion.id',
						'Contratinsertion.personne_id',
						'Contratinsertion.decision_ci',
						'Contratinsertion.forme_ci',
						'Contratinsertion.datevalidation_ci',
						'Contratinsertion.datedecision',
					),
					'conditions' => array(
						'Histochoixcer93.id' => $this->Histochoixcer93->id
					),
					'joins' => array(
						$this->Histochoixcer93->join( 'Cer93' ),
						$this->Histochoixcer93->Cer93->join( 'Contratinsertion' ),
					),
					'contain' => false
				)
			);

			$expected = array(
				'Cer93' => array(
					'id' => 2,
					'contratinsertion_id' => 2,
					'user_id' => 1,
					'positioncer' => '03attdecisioncg',
					'formeci' => null,
				),
				'Histochoixcer93' => array(
					'id' => 1,
					'cer93_id' => 2,
					'user_id' => 6,
					'etape' => '03attdecisioncg',
					'datechoix' => '2012-10-25',
				),
				'Contratinsertion' => array(
					'id' => 2,
					'personne_id' => 1,
					'decision_ci' => 'E',
					'forme_ci' => NULL,
					'datevalidation_ci' => NULL,
					'datedecision' => NULL,
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Histochoixcer93::saveDecision() à l'étape
		 * 03attdecisioncg, avec rejet.
		 *
		 * @group medium
		 * @return void
		 */
		public function testSaveDecision03attdecisioncgAvecRejet() {
			$data = array(
				'Histochoixcer93' => array(
					'id' => '',
					'cer93_id' => '2',
					'user_id' => '6',
					'formeci' => 'S',
					'commentaire' => 'drsg',
					'datechoix' => array(
						'day' => '25',
						'month' => '10',
						'year' => '2012'
					),
					'etape' => '03attdecisioncg',
					'isrejet' => 1
				)
			);
			$result = $this->Histochoixcer93->saveDecision( $data );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// On vérifie que l'on a bien enregistré les informations
			$result = $this->Histochoixcer93->find(
				'first',
				array(
					'fields' => array(
						'Cer93.id',
						'Cer93.contratinsertion_id',
						'Cer93.user_id',
						'Cer93.positioncer',
						'Cer93.formeci',
						'Histochoixcer93.id',
						'Histochoixcer93.cer93_id',
						'Histochoixcer93.user_id',
						'Histochoixcer93.etape',
						'Histochoixcer93.datechoix',
						'Contratinsertion.id',
						'Contratinsertion.personne_id',
						'Contratinsertion.decision_ci',
						'Contratinsertion.forme_ci',
						'Contratinsertion.datevalidation_ci',
						'Contratinsertion.datedecision',
					),
					'conditions' => array(
						'Histochoixcer93.id' => $this->Histochoixcer93->id
					),
					'joins' => array(
						$this->Histochoixcer93->join( 'Cer93' ),
						$this->Histochoixcer93->Cer93->join( 'Contratinsertion' ),
					),
					'contain' => false
				)
			);

			$expected = array(
				'Cer93' => array(
					'id' => 2,
					'contratinsertion_id' => 2,
					'user_id' => 1,
					'positioncer' => '99rejetecpdv',
					'formeci' => 'S',
				),
				'Histochoixcer93' => array(
					'id' => 1,
					'cer93_id' => 2,
					'user_id' => 6,
					'etape' => '03attdecisioncg',
					'datechoix' => '2012-10-25',
				),
				'Contratinsertion' => array(
					'id' => 2,
					'personne_id' => 1,
					'decision_ci' => 'R',
					'forme_ci' => 'S',
					'datevalidation_ci' => NULL,
					'datedecision' => '2012-10-25',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Histochoixcer93::saveDecision() à l'étape
		 * 05secondelecture, decision valide.
		 *
		 * @group medium
		 * @return void
		 */
		public function testSaveDecision05secondelectureValide() {
			$data = array(
				'Histochoixcer93' => array(
					'id' => '',
					'cer93_id' => '2',
					'user_id' => '6',
					'formeci' => 'S',
					'duree' => 3,
					'commentaire' => 'drsg',
					'datechoix' => array(
						'day' => '25',
						'month' => '10',
						'year' => '2012'
					),
					'etape' => '05secondelecture',
					'decisioncs' => 'valide',
					'observationdecision' => null
				)
			);
			$result = $this->Histochoixcer93->saveDecision( $data );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// On vérifie que l'on a bien enregistré les informations
			$result = $this->Histochoixcer93->find(
				'first',
				array(
					'fields' => array(
						'Cer93.id',
						'Cer93.contratinsertion_id',
						'Cer93.user_id',
						'Cer93.positioncer',
						'Cer93.formeci',
						'Cer93.observationdecision',
						'Histochoixcer93.id',
						'Histochoixcer93.cer93_id',
						'Histochoixcer93.user_id',
						'Histochoixcer93.etape',
						'Histochoixcer93.decisioncs',
						'Histochoixcer93.datechoix',
						'Contratinsertion.id',
						'Contratinsertion.personne_id',
						'Contratinsertion.decision_ci',
						'Contratinsertion.forme_ci',
						'Contratinsertion.datevalidation_ci',
						'Contratinsertion.datedecision',
					),
					'conditions' => array(
						'Histochoixcer93.id' => $this->Histochoixcer93->id
					),
					'joins' => array(
						$this->Histochoixcer93->join( 'Cer93' ),
						$this->Histochoixcer93->Cer93->join( 'Contratinsertion' ),
					),
					'contain' => false
				)
			);

			$expected = array(
				'Cer93' => array(
					'id' => 2,
					'contratinsertion_id' => 2,
					'user_id' => 1,
					'positioncer' => '99valide',
					'formeci' => 'S',
					'observationdecision' => null
				),
				'Histochoixcer93' => array(
					'id' => 1,
					'cer93_id' => 2,
					'user_id' => 6,
					'etape' => '05secondelecture',
					'decisioncs' => 'valide',
					'datechoix' => '2012-10-25',
				),
				'Contratinsertion' => array(
					'id' => 2,
					'personne_id' => 1,
					'decision_ci' => 'V',
					'forme_ci' => 'S',
					'datevalidation_ci' => '2012-10-25',
					'datedecision' => '2012-10-25',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Histochoixcer93::saveDecision() à l'étape
		 * 05secondelecture, decision passageep.
		 *
		 * @group medium
		 * @return void
		 */
		public function testSaveDecision05secondelecturePassageep() {
			$data = array(
				'Histochoixcer93' => array(
					'id' => '',
					'cer93_id' => '2',
					'user_id' => '6',
					'formeci' => 'S',
					'commentaire' => 'drsg',
					'datechoix' => array(
						'day' => '25',
						'month' => '10',
						'year' => '2012'
					),
					'etape' => '05secondelecture',
					'decisioncs' => 'passageep'
				)
			);
			$result = $this->Histochoixcer93->saveDecision( $data );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// On vérifie que l'on a bien enregistré les informations
			$result = $this->Histochoixcer93->find(
				'first',
				array(
					'fields' => array(
						'Cer93.id',
						'Cer93.contratinsertion_id',
						'Cer93.user_id',
						'Cer93.positioncer',
						'Cer93.formeci',
						'Histochoixcer93.id',
						'Histochoixcer93.cer93_id',
						'Histochoixcer93.user_id',
						'Histochoixcer93.etape',
						'Histochoixcer93.decisioncs',
						'Histochoixcer93.datechoix',
						'Contratinsertion.id',
						'Contratinsertion.personne_id',
						'Contratinsertion.decision_ci',
						'Contratinsertion.forme_ci',
						'Contratinsertion.datevalidation_ci',
						'Contratinsertion.datedecision',
						'Contratcomplexeep93.id',
						'Dossierep.id',
						'Dossierep.personne_id',
						'Dossierep.themeep',
					),
					'conditions' => array(
						'Histochoixcer93.id' => $this->Histochoixcer93->id
					),
					'joins' => array(
						$this->Histochoixcer93->join( 'Cer93' ),
						$this->Histochoixcer93->Cer93->join( 'Contratinsertion' ),
						$this->Histochoixcer93->Cer93->Contratinsertion->join( 'Contratcomplexeep93' ),
						$this->Histochoixcer93->Cer93->Contratinsertion->Contratcomplexeep93->join( 'Dossierep' ),
					),
					'contain' => false
				)
			);

			$expected = array(
				'Cer93' => array(
					'id' => 2,
					'contratinsertion_id' => 2,
					'user_id' => 1,
					'positioncer' => '07attavisep',
					'formeci' => 'S',
				),
				'Histochoixcer93' => array(
					'id' => 1,
					'cer93_id' => 2,
					'user_id' => 6,
					'etape' => '05secondelecture',
					'decisioncs' => 'passageep',
					'datechoix' => '2012-10-25',
				),
				'Contratinsertion' => array(
					'id' => 2,
					'personne_id' => 1,
					'decision_ci' => 'E',
					'forme_ci' => 'S',
					'datevalidation_ci' => NULL,
					'datedecision' => NULL,
				),
				'Contratcomplexeep93' => array(
					'id' => 1,
				),
				'Dossierep' => array(
					'id' => 1,
					'personne_id' => 1,
					'themeep' => 'contratscomplexeseps93',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Histochoixcer93::saveDecision() à l'étape
		 * 05secondelecture, decision aviscadre.
		 *
		 * @group medium
		 * @return void
		 */
		public function testSaveDecision05secondelectureAviscadre() {
			$data = array(
				'Histochoixcer93' => array(
					'id' => '',
					'cer93_id' => '2',
					'user_id' => '6',
					'formeci' => 'S',
					'commentaire' => 'drsg',
					'datechoix' => array(
						'day' => '25',
						'month' => '10',
						'year' => '2012'
					),
					'etape' => '05secondelecture',
					'decisioncs' => 'aviscadre'
				)
			);
			$result = $this->Histochoixcer93->saveDecision( $data );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// On vérifie que l'on a bien enregistré les informations
			$result = $this->Histochoixcer93->find(
				'first',
				array(
					'fields' => array(
						'Cer93.id',
						'Cer93.contratinsertion_id',
						'Cer93.user_id',
						'Cer93.positioncer',
						'Cer93.formeci',
						'Histochoixcer93.id',
						'Histochoixcer93.cer93_id',
						'Histochoixcer93.user_id',
						'Histochoixcer93.etape',
						'Histochoixcer93.decisioncs',
						'Histochoixcer93.datechoix',
						'Contratinsertion.id',
						'Contratinsertion.personne_id',
						'Contratinsertion.decision_ci',
						'Contratinsertion.forme_ci',
						'Contratinsertion.datevalidation_ci',
						'Contratinsertion.datedecision',
					),
					'conditions' => array(
						'Histochoixcer93.id' => $this->Histochoixcer93->id
					),
					'joins' => array(
						$this->Histochoixcer93->join( 'Cer93' ),
						$this->Histochoixcer93->Cer93->join( 'Contratinsertion' ),
					),
					'contain' => false
				)
			);

			$expected = array(
				'Cer93' => array(
					'id' => 2,
					'contratinsertion_id' => 2,
					'user_id' => 1,
					'positioncer' => '05secondelecture',
					'formeci' => null,
				),
				'Histochoixcer93' => array(
					'id' => 1,
					'cer93_id' => 2,
					'user_id' => 6,
					'etape' => '05secondelecture',
					'decisioncs' => 'aviscadre',
					'datechoix' => '2012-10-25',
				),
				'Contratinsertion' => array(
					'id' => 2,
					'personne_id' => 1,
					'decision_ci' => 'E',
					'forme_ci' => NULL,
					'datevalidation_ci' => NULL,
					'datedecision' => NULL,
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Histochoixcer93::saveDecision() à l'étape
		 * 06attaviscadre, décision valide.
		 *
		 * @group medium
		 * @return void
		 */
		public function testSaveDecision06attaviscadreValide() {
			$data = array(
				'Histochoixcer93' => array(
					'id' => '',
					'cer93_id' => '2',
					'user_id' => '6',
					'formeci' => 'S',
					'duree' => 3,
					'commentaire' => 'drsg',
					'datechoix' => array(
						'day' => '25',
						'month' => '10',
						'year' => '2012'
					),
					'etape' => '06attaviscadre',
					'decisioncadre' => 'valide',
					'observationdecision' => null
				)
			);
			$result = $this->Histochoixcer93->saveDecision( $data );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// On vérifie que l'on a bien enregistré les informations
			$result = $this->Histochoixcer93->find(
				'first',
				array(
					'fields' => array(
						'Cer93.id',
						'Cer93.contratinsertion_id',
						'Cer93.user_id',
						'Cer93.positioncer',
						'Cer93.formeci',
						'Histochoixcer93.id',
						'Histochoixcer93.cer93_id',
						'Histochoixcer93.user_id',
						'Histochoixcer93.etape',
						'Histochoixcer93.decisioncs',
						'Histochoixcer93.datechoix',
						'Contratinsertion.id',
						'Contratinsertion.personne_id',
						'Contratinsertion.decision_ci',
						'Contratinsertion.forme_ci',
						'Contratinsertion.datevalidation_ci',
						'Contratinsertion.datedecision',
					),
					'conditions' => array(
						'Histochoixcer93.id' => $this->Histochoixcer93->id
					),
					'joins' => array(
						$this->Histochoixcer93->join( 'Cer93' ),
						$this->Histochoixcer93->Cer93->join( 'Contratinsertion' ),
					),
					'contain' => false
				)
			);

			$expected = array(
				'Cer93' => array(
					'id' => 2,
					'contratinsertion_id' => 2,
					'user_id' => 1,
					'positioncer' => '99valide',
					'formeci' => 'S',
				),
				'Histochoixcer93' => array(
					'id' => 1,
					'cer93_id' => 2,
					'user_id' => 6,
					'etape' => '06attaviscadre',
					'decisioncs' => NULL,
					'datechoix' => '2012-10-25',
				),
				'Contratinsertion' => array(
					'id' => 2,
					'personne_id' => 1,
					'decision_ci' => 'V',
					'forme_ci' => 'S',
					'datevalidation_ci' => '2012-10-25',
					'datedecision' => '2012-10-25',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Histochoixcer93::saveDecision() à l'étape
		 * 06attaviscadre, décision rejete.
		 *
		 * @group medium
		 * @return void
		 */
		public function testSaveDecision06attaviscadreRejete() {
			$data = array(
				'Histochoixcer93' => array(
					'id' => '',
					'cer93_id' => '2',
					'user_id' => '6',
					'formeci' => 'S',
					'duree' => 3,
					'dureepub' => 3,
					'commentaire' => 'drsg',
					'datechoix' => array(
						'day' => '25',
						'month' => '10',
						'year' => '2012'
					),
					'etape' => '06attaviscadre',
					'decisioncadre' => 'rejete',
					'observationdecision' => null
				)
			);
			$result = $this->Histochoixcer93->saveDecision( $data );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// On vérifie que l'on a bien enregistré les informations
			$result = $this->Histochoixcer93->find(
				'first',
				array(
					'fields' => array(
						'Cer93.id',
						'Cer93.contratinsertion_id',
						'Cer93.user_id',
						'Cer93.positioncer',
						'Cer93.formeci',
						'Histochoixcer93.id',
						'Histochoixcer93.cer93_id',
						'Histochoixcer93.user_id',
						'Histochoixcer93.etape',
						'Histochoixcer93.decisioncs',
						'Histochoixcer93.datechoix',
						'Contratinsertion.id',
						'Contratinsertion.personne_id',
						'Contratinsertion.decision_ci',
						'Contratinsertion.forme_ci',
						'Contratinsertion.datevalidation_ci',
						'Contratinsertion.datedecision',
					),
					'conditions' => array(
						'Histochoixcer93.id' => $this->Histochoixcer93->id
					),
					'joins' => array(
						$this->Histochoixcer93->join( 'Cer93' ),
						$this->Histochoixcer93->Cer93->join( 'Contratinsertion' ),
					),
					'contain' => false
				)
			);

			$expected = array(
				'Cer93' => array(
					'id' => 2,
					'contratinsertion_id' => 2,
					'user_id' => 1,
					'positioncer' => '99rejete',
					'formeci' => 'S',
				),
				'Histochoixcer93' => array(
					'id' => 1,
					'cer93_id' => 2,
					'user_id' => 6,
					'etape' => '06attaviscadre',
					'decisioncs' => NULL,
					'datechoix' => '2012-10-25',
				),
				'Contratinsertion' => array(
					'id' => 2,
					'personne_id' => 1,
					'decision_ci' => 'R',
					'forme_ci' => 'S',
					'datevalidation_ci' => NULL,
					'datedecision' => '2012-10-25',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Histochoixcer93::saveDecision() à l'étape
		 * 06attaviscadre, décision passageep.
		 *
		 * @group medium
		 * @return void
		 */
		public function testSaveDecision06attaviscadrePassageep() {
			$data = array(
				'Histochoixcer93' => array(
					'id' => '',
					'cer93_id' => '2',
					'user_id' => '6',
					'formeci' => 'S',
					'commentaire' => 'drsg',
					'datechoix' => array(
						'day' => '25',
						'month' => '10',
						'year' => '2012'
					),
					'etape' => '06attaviscadre',
					'decisioncadre' => 'passageep'
				)
			);
			$result = $this->Histochoixcer93->saveDecision( $data );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// On vérifie que l'on a bien enregistré les informations
			$result = $this->Histochoixcer93->find(
				'first',
				array(
					'fields' => array(
						'Cer93.id',
						'Cer93.contratinsertion_id',
						'Cer93.user_id',
						'Cer93.positioncer',
						'Cer93.formeci',
						'Histochoixcer93.id',
						'Histochoixcer93.cer93_id',
						'Histochoixcer93.user_id',
						'Histochoixcer93.etape',
						'Histochoixcer93.decisioncs',
						'Histochoixcer93.datechoix',
						'Contratinsertion.id',
						'Contratinsertion.personne_id',
						'Contratinsertion.decision_ci',
						'Contratinsertion.forme_ci',
						'Contratinsertion.datevalidation_ci',
						'Contratinsertion.datedecision',
						'Contratcomplexeep93.id',
						'Dossierep.id',
						'Dossierep.personne_id',
						'Dossierep.themeep',
					),
					'conditions' => array(
						'Histochoixcer93.id' => $this->Histochoixcer93->id
					),
					'joins' => array(
						$this->Histochoixcer93->join( 'Cer93' ),
						$this->Histochoixcer93->Cer93->join( 'Contratinsertion' ),
						$this->Histochoixcer93->Cer93->Contratinsertion->join( 'Contratcomplexeep93' ),
						$this->Histochoixcer93->Cer93->Contratinsertion->Contratcomplexeep93->join( 'Dossierep' ),
					),
					'contain' => false
				)
			);

			$expected = array(
				'Cer93' => array(
					'id' => 2,
					'contratinsertion_id' => 2,
					'user_id' => 1,
					'positioncer' => '07attavisep',
					'formeci' => 'S',
				),
				'Histochoixcer93' => array(
					'id' => 1,
					'cer93_id' => 2,
					'user_id' => 6,
					'etape' => '06attaviscadre',
					'decisioncs' => NULL,
					'datechoix' => '2012-10-25',
				),
				'Contratinsertion' => array(
					'id' => 2,
					'personne_id' => 1,
					'decision_ci' => 'E',
					'forme_ci' => 'S',
					'datevalidation_ci' => NULL,
					'datedecision' => NULL,
				),
				'Contratcomplexeep93' => array(
					'id' => 1,
				),
				'Dossierep' => array(
					'id' => 1,
					'personne_id' => 1,
					'themeep' => 'contratscomplexeseps93',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Histochoixcer93::sqDernier()
		 *
		 * @return void
		 */
		public function testSqDernier() {
			$result = $this->Histochoixcer93->sqDernier( 'Cer.id' );
			$expected = 'SELECT histoschoixcers93.id
					FROM histoschoixcers93
					WHERE
						histoschoixcers93.cer93_id = Cer.id
					ORDER BY histoschoixcers93.modified DESC
					LIMIT 1';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
