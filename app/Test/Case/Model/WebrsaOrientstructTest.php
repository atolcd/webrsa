<?php
	/**
	 * Code source de la classe WebrsaOrientstructTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaOrientstruct', 'Model' );

	/**
	 * La classe WebrsaOrientstructTest réalise les tests unitaires de la classe WebrsaOrientstruct.
	 *
	 * @package app.Test.Case.Model
	 */
	class WebrsaOrientstructTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Adresse',
			'app.Adressefoyer',
			'app.Calculdroitrsa',
			'app.Dossier',
			'app.Foyer',
			'app.Orientstruct',
			'app.Personne',
			'app.PersonneReferent',
			'app.Prestation',
			'app.Referent',
			'app.Structurereferente',
			'app.Typeorient'
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var WebrsaOrientstruct
		 */
		public $WebrsaOrientstruct = null;

		/**
		 * Préparation avant test pour un département en particulier.
		 *
		 * @param integer $departement
		 */
		public function setUpDepartement( $departement ) {
			unset( $this->WebrsaOrientstruct );
			ClassRegistry::flush();
			Configure::write( 'Cg.departement', $departement );

			$this->WebrsaOrientstruct = ClassRegistry::init( 'WebrsaOrientstruct' );

			// On mock la méthode ged()
			// TODO: trouver mieux / configuration: ça empêche la création via loadModel
			$this->WebrsaOrientstruct->Orientstruct = $this->getMock(
				'Orientstruct',
				array( 'ged' ),
				array( array( 'ds' => 'test' ) )
			);
		}

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();

			$this->setUpDepartement( 976 );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->WebrsaOrientstruct );
			parent::tearDown();
		}

		/**
		 * Test de la méthode WebrsaOrientstruct::getAddEditFormData()
		 * lorsque l'on veut ajouter une d'orientation.
		 */
		public function testGetAddFormData() {
			$result = $this->WebrsaOrientstruct->getAddEditFormData( 1, null, 1 );
			$expected = array(
				'Orientstruct' => array(
					'personne_id' => 1,
					'user_id' => 1,
					'origine' => 'manuelle',
					'date_propo' => '2009-09-01',
					'date_valid' => date( 'Y-m-d' )
				),
				'Calculdroitrsa' => array(
					'id' => 1,
					'toppersdrodevorsa' => '1',
					'personne_id' => 1
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode WebrsaOrientstruct::saveAddEditFormData()
		 * lorsque l'on veut ajouter une d'orientation.
		 */
		public function testSaveAddFormData() {
			$orientstruct = $this->WebrsaOrientstruct->getAddEditFormData( 2, null, 1 );
			$orientstruct['Orientstruct']['typeorient_id'] = 1;
			$orientstruct['Orientstruct']['structurereferente_id'] = 1;
			$orientstruct['Orientstruct']['statut_orient'] = 'Orienté';
			$this->assertTrue( $this->WebrsaOrientstruct->saveAddEditFormData( $orientstruct, 1 ) !== false );
		}

		/**
		 * Test de la méthode WebrsaOrientstruct::getAddEditFormData()
		 * lorsque l'on veut modifier une d'orientation.
		 */
		public function testGetEditFormData() {
			// 1. Sauvegarde d'une orientation orientée
			$orientstruct = $this->WebrsaOrientstruct->getAddEditFormData( 2, null, 1 );
			$orientstruct['Orientstruct']['typeorient_id'] = 1;
			$orientstruct['Orientstruct']['structurereferente_id'] = 1;
			$orientstruct['Orientstruct']['statut_orient'] = 'Orienté';
			$this->assertTrue( $this->WebrsaOrientstruct->saveAddEditFormData( $orientstruct, 1 ) !== false );

			// 2. Demande des données pour le formulaire de modification
			$result = $this->WebrsaOrientstruct->getAddEditFormData( 1, $this->WebrsaOrientstruct->Orientstruct->id, 1 );
			$expected = array(
				'Orientstruct' => array(
					'id' => 2,
					'personne_id' => 2,
					'typeorient_id' => 1,
					'structurereferente_id' => '1_1',
					'propo_algo' => NULL,
					'valid_cg' => true,
					'date_propo' => '2010-07-12',
					'date_valid' => date( 'Y-m-d' ),
					'statut_orient' => 'Orienté',
					'date_impression' => NULL,
					'daterelance' => NULL,
					'statutrelance' => 'E',
					'date_impression_relance' => NULL,
					'referent_id' => NULL,
					'etatorient' => NULL,
					'rgorient' => 1,
					'structureorientante_id' => NULL,
					'referentorientant_id' => NULL,
					'user_id' => 1,
					'haspiecejointe' => '0',
					'origine' => 'manuelle',
					'typenotification' => 'normale'
				),
				'Calculdroitrsa' => array(
					'id' => 1,
					'toppersdrodevorsa' => '1',
					'personne_id' => 1
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
