<?php
	/**
	 * Code source de la classe OrientstructTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Orientstruct', 'Model' );

	/**
	 * La classe OrientstructTest réalise les tests unitaires de la classe Orientstruct.
	 *
	 * @package app.Test.Case.Model
	 */
	class OrientstructTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Adresse',
			'app.Adressefoyer',
			'app.Dossier',
			'app.Foyer',
			'app.Nonoriente66',
			'app.Orientstruct',
			'app.Pdf', // FIXME: mock
			'app.Personne',
			'app.PersonneReferent',
			'app.Prestation',
			'app.Referent',
			'app.Structurereferente',
			'app.Typeorient',
			'app.User',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Orientstruct
		 */
		public $Orientstruct = null;

		/**
		 * "Extraction simplifiée" des règles de validation.
		 *
		 * @todo: à mettre dans une classe parente, ou une fonction utilitaire...
		 *
		 * @param Model $Model
		 * @return array
		 */
		public function extractValidateRules( Model $Model ) {
			$result = array();

			$result = array();
			foreach( Hash::flatten( $Model->validate ) as $key => $value ) {
				if( preg_match( '/^([^.]+).([^.]+).rule.0$/', $key, $matches ) ) {
					$result["{$matches[1]}.{$matches[2]}"] = Hash::get( $this->Orientstruct->validate, "{$matches[1]}.{$matches[2]}.rule" );
				}
			}
			ksort( $result );

			return $result;
		}

		/**
		 * Préparation avant test pour un département en particulier.
		 *
		 * @param integer $departement
		 */
		public function setUpDepartement( $departement ) {
			unset( $this->Orientstruct );
			ClassRegistry::flush();
			Configure::write( 'Cg.departement', $departement );

			// On mock la méthode ged()
			$this->Orientstruct = $this->getMock(
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

			$this->setUpDepartement( 93 );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Orientstruct );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Orientstruct::beforeSave() pour une première entrée.
		 */
		public function testBeforeSavePremiereOrientation() {
			// 1. Sans autre donnée que l'id de la personne
			$data = array(
				'Orientstruct' => array(
					'personne_id' => 1,
					'statut_orient' => '',
				)
			);
			$this->Orientstruct->create( $data );
			$this->assertTrue( $this->Orientstruct->beforeSave() );
			$result = $this->Orientstruct->data;
			$expected = array(
				'Orientstruct' => array(
					'valid_cg' => false,
					'statutrelance' => 'E',
					'typenotification' => 'normale',
					'personne_id' => 1,
					'statut_orient' => '',
					'origine' => null,
					'rgorient' => null,
					'date_valid' => null,
					'haspiecejointe' => '0'
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Avec une orientation "En attente"
			$data = array(
				'Orientstruct' => array(
					'personne_id' => 1,
					'statut_orient' => 'En attente',
				)
			);
			$this->Orientstruct->create( $data );
			$this->assertTrue( $this->Orientstruct->beforeSave() );
			$result = $this->Orientstruct->data;
			$expected = array(
				'Orientstruct' => array(
					'valid_cg' => false,
					'statutrelance' => 'E',
					'typenotification' => 'normale',
					'personne_id' => 1,
					'statut_orient' => 'En attente',
					'rgorient' => null,
					'date_valid' => null,
					'origine' => null,
					'haspiecejointe' => '0'
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Avec une orientation "Non orienté"
			$data = array(
				'Orientstruct' => array(
					'personne_id' => 1,
					'statut_orient' => 'Non orienté',
				)
			);
			$this->Orientstruct->create( $data );
			$this->assertTrue( $this->Orientstruct->beforeSave() );
			$result = $this->Orientstruct->data;
			$expected = array(
				'Orientstruct' => array(
					'valid_cg' => false,
					'statutrelance' => 'E',
					'typenotification' => 'normale',
					'personne_id' => 1,
					'statut_orient' => 'Non orienté',
					'rgorient' => null,
					'date_valid' => null,
					'origine' => null,
					'haspiecejointe' => '0'
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. Avec une orientation "Orienté"
			$data = array(
				'Orientstruct' => array(
					'personne_id' => 1,
					'statut_orient' => 'Orienté',
				)
			);
			$this->Orientstruct->create( $data );
			$this->assertTrue( $this->Orientstruct->beforeSave() );
			$result = $this->Orientstruct->data;
			$expected = array(
				'Orientstruct' => array(
					'valid_cg' => false,
					'statutrelance' => 'E',
					'typenotification' => 'normale',
					'personne_id' => 1,
					'statut_orient' => 'Orienté',
					'rgorient' => 2,
					'haspiecejointe' => '0'
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Orientstruct::beforeSave() lors de la modification
		 * d'une orientation.
		 */
		public function testBeforeSaveModificationOrientation() {
			// 0. Enregistrement de la première orientation
			$data = array(
				'Orientstruct' => array(
					'personne_id' => 1,
					'statut_orient' => 'Orienté',
					'typeorient_id' => 1,
					'structurereferente_id' => 1,
					'haspiecejointe' => '0',
					'origine' => 'manuelle',
					'date_valid' => '2015-01-01'
				)
			);
			$this->Orientstruct->create( $data );
			$success = $this->Orientstruct->save();
			$this->assertTrue( !empty( $success ) );

			$id = $this->Orientstruct->id;

			// 1. Modification de l'orientation de la personne
			$data = array(
				'Orientstruct' => array(
					'id' => $id,
					'personne_id' => 1,
					'statut_orient' => 'Orienté',
					'typeorient_id' => 1,
					'structurereferente_id' => 1,
					'haspiecejointe' => '0',
					'origine' => 'manuelle',
					'date_valid' => '2015-01-01'
				)
			);
			$this->Orientstruct->create( $data );
			$this->assertTrue( $this->Orientstruct->beforeSave() );
			$result = $this->Orientstruct->data;
			$expected = array(
				'Orientstruct' => array(
					'id' => $id,
					'valid_cg' => false,
					'statutrelance' => 'E',
					'typenotification' => 'normale',
					'personne_id' => 1,
					'statut_orient' => 'Orienté',
					'typeorient_id' => 1,
					'structurereferente_id' => 1,
					'haspiecejointe' => '0',
					'rgorient' => 2,
					'origine' => 'reorientation',
					'date_valid' => '2015-01-01'
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Orientstruct::beforeSave() pour une seconde entrée
		 * "Orienté".
		 */
		public function testBeforeSaveSecondeOrientation() {
			// 0. Enregistrement de la première orientation
			$data = array(
				'Orientstruct' => array(
					'personne_id' => 2,
					'statut_orient' => 'Orienté',
					'typeorient_id' => 1,
					'structurereferente_id' => 1,
					'haspiecejointe' => '0',
					'origine' => 'manuelle',
					'date_valid' => '2015-01-01'
				)
			);
			$this->Orientstruct->create( $data );
			$success = $this->Orientstruct->save();
			$this->assertTrue( !empty( $success ) );

			// 1. Ajout d'une nouvelle orientation à la personne
			$data = array(
				'Orientstruct' => array(
					'personne_id' => 2,
					'statut_orient' => 'Orienté',
					'typeorient_id' => 1,
					'structurereferente_id' => 1,
					'haspiecejointe' => '0',
					'origine' => 'reorientation',
					'date_valid' => '2015-02-01'
				)
			);
			$this->Orientstruct->create( $data );
			$this->assertTrue( $this->Orientstruct->beforeSave() );
			$result = $this->Orientstruct->data;
			$expected = array(
				'Orientstruct' => array(
					'valid_cg' => false,
					'statutrelance' => 'E',
					'typenotification' => 'normale',
					'personne_id' => 2,
					'statut_orient' => 'Orienté',
					'typeorient_id' => 1,
					'structurereferente_id' => 1,
					'haspiecejointe' => '0',
					'rgorient' => 2,
					'origine' => 'reorientation',
					'date_valid' => '2015-02-01'
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Orientstruct::beforeSave() pour une seconde entrée
		 * "Orienté" alors que la première est "En attente".
		 */
		public function testBeforeSavePremiereOrientationSecondeEntree() {
			// 0. Enregistrement de la première orientation
			$data = array(
				'Orientstruct' => array(
					'personne_id' => 2,
					'statut_orient' => 'En attente',
					'typeorient_id' => 1,
					'structurereferente_id' => 1,
					'haspiecejointe' => '0',
				)
			);
			$this->Orientstruct->create( $data );
			$success = $this->Orientstruct->save();
			$this->assertTrue( !empty( $success ) );

			// 1. Enregistrement de la seconde orientation
			$data = array(
				'Orientstruct' => array(
					'personne_id' => 2,
					'statut_orient' => 'En attente',
					'typeorient_id' => 1,
					'structurereferente_id' => 1,
					'haspiecejointe' => '0',
				)
			);
			$this->Orientstruct->create( $data );
			$success = $this->Orientstruct->save();
			$this->assertTrue( !empty( $success ) );

			$id = $this->Orientstruct->id;

			// 2. Modification de la seconde orientation de la personne
			$data = array(
				'Orientstruct' => array(
					'id' => $id,
					'personne_id' => 2,
					'statut_orient' => 'Orienté',
					'typeorient_id' => 1,
					'structurereferente_id' => 1,
					'haspiecejointe' => '0',
				)
			);
			$this->Orientstruct->create( $data );
			$this->assertTrue( $this->Orientstruct->beforeSave() );
			$result = $this->Orientstruct->data;
			$expected = array(
				'Orientstruct' => array(
					'id' => $id,
					'valid_cg' => false,
					'statutrelance' => 'E',
					'typenotification' => 'normale',
					'personne_id' => 2,
					'statut_orient' => 'Orienté',
					'typeorient_id' => 1,
					'structurereferente_id' => 1,
					'haspiecejointe' => '0',
					'rgorient' => 1,
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$success = $this->Orientstruct->save();
			$this->assertTrue( !empty( $success ) );

			// 3. Ajout d'une troisième orientation à la personne
			$data = array(
				'Orientstruct' => array(
					'personne_id' => 2,
					'statut_orient' => 'Orienté',
					'typeorient_id' => 1,
					'structurereferente_id' => 1,
					'haspiecejointe' => '0',
					'origine' => 'manuelle',
				)
			);
			$this->Orientstruct->create( $data );
			$this->assertTrue( $this->Orientstruct->beforeSave() );
			$result = $this->Orientstruct->data;
			$expected = array(
				'Orientstruct' => array(
					'valid_cg' => false,
					'statutrelance' => 'E',
					'typenotification' => 'normale',
					'personne_id' => 2,
					'statut_orient' => 'Orienté',
					'typeorient_id' => 1,
					'structurereferente_id' => 1,
					'haspiecejointe' => '0',
					'rgorient' => 2,
					'origine' => 'reorientation',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Vérification des règles de validation mises en place pour le
		 * département 976 (constructeur, règles par défaut, ...).
		 */
		public function testOrientstructValidate976() {
			$this->setUpDepartement( 976 );

			$result = Hash::get( $this->Orientstruct->validate, 'typeorient_id.notEmptyIf' );
			$expected = array(
				'rule' => array(
					'notEmptyIf',
					'statut_orient',
					true,
					array( 'Orienté', 'En attente', '' )
				),
				'message' => 'Champ obligatoire'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = Hash::get( $this->Orientstruct->validate, 'structurereferente_id.notEmptyIf' );
			$expected = array(
				'rule' => array(
					'notEmptyIf',
					'statut_orient',
					true,
					array( 'Orienté', 'En attente', '' )
				),
				'message' => 'Champ obligatoire'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Vérification des règles de validation mises en place pour le
		 * département 66 (constructeur, règles par défaut, ...).
		 */
		public function testOrientstructValidate66() {
			$this->setUpDepartement( 66 );

			$result = Hash::get( $this->Orientstruct->validate, 'structureorientante_id.notEmptyIf' );
			$expected = array(
				'rule' => array(
					'notEmptyIf',
					'statut_orient',
					true,
					array( 'Orienté' ),
				),
				'message' => 'Veuillez choisir une structure orientante'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = Hash::get( $this->Orientstruct->validate, 'referentorientant_id.notEmptyIf' );
			$expected = array(
				'rule' => array(
					'notEmptyIf',
					'statut_orient',
					true,
					array( 'Orienté' ),
				),
				'message' => 'Veuillez choisir un référent orientant'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Vérification des règles de validation mises en place pour le
		 * département 58 (constructeur, règles par défaut, ...), où ce sont les
		 * règles par défaut qui s'appliquent.
		 */
		public function testOrientstructValidate() {
			$this->setUpDepartement( 58 );

			$result = $this->extractValidateRules( $this->Orientstruct );
			$expected = array(
				'date_impression.date' => array(
					'date'
				),
				'date_impression_relance.date' => array(
					'date'
				),
				'date_propo.date' => array(
					'date'
				),
				'date_valid.date' => array(
					'date'
				),
				'date_valid.notEmptyIf' => array(
					'notEmptyIf',
					'statut_orient',
					true,
					array( 'Orienté' ),
				),
				'daterelance.date' => array(
					'date'
				),
				'etatorient.inList' => array(
					'inList',
					array(
						'proposition',
						'decision'
					)
				),
				'etatorient.maxLength' => array(
					'maxLength',
					11
				),
				'haspiecejointe.inList' => array(
					'inList',
					array( '0', '1' )
				),
				'haspiecejointe.maxLength' => array(
					'maxLength',
					1
				),
				'haspiecejointe.notEmpty' => array(
					'notEmpty'
				),
				'id.integer' => array(
					'integer'
				),
				'origine.inList' => array(
					'inList',
					array( 'manuelle', 'cohorte', 'reorientation', 'demenagement' )
				),
				'origine.maxLength' => array(
					'maxLength',
					13
				),
				'personne_id.integer' => array(
					'integer'
				),
				'personne_id.notEmpty' => array(
					'notEmpty'
				),
				'propo_algo.integer' => array(
					'integer'
				),
				'referent_id.integer' => array(
					'integer'
				),
				'referent_id.dependentForeignKeys' => array(
					'dependentForeignKeys',
					'Referent',
					'Structurereferente'
				),
				'referentorientant_id.integer' => array(
					'integer'
				),
				'rgorient.integer' => array(
					'integer'
				),
				'statut_orient.inList' => array(
					'inList',
					array( 'Orienté', 'En attente', 'Non orienté' )
				),
				'statut_orient.maxLength' => array(
					'maxLength',
					15
				),
				'statut_orient.notEmpty' => array(
					'notEmpty'
				),
				'statutrelance.inList' => array(
					'inList',
					array( 'E', 'R' )
				),
				'statutrelance.maxLength' => array(
					'maxLength',
					1
				),
				'structureorientante_id.integer' => array(
					'integer'
				),
				'structurereferente_id.dependentForeignKeys' => array(
					'dependentForeignKeys',
					'Structurereferente',
					'Typeorient'
				),
				'structurereferente_id.integer' => array(
					'integer'
				),
				'structurereferente_id.notEmptyIf' => array(
					'notEmptyIf',
					'statut_orient',
					true,
					array( 'Orienté' )
				),
				'typenotification.inList' => array(
					'inList',
					array(
						'normale',
						'systematique',
						'dejainscritpe'
					)
				),
				'typenotification.maxLength' => array(
					'maxLength',
					15
				),
				'typeorient_id.integer' => array(
					'integer'
				),
				'typeorient_id.notEmptyIf' => array(
					'notEmptyIf',
					'statut_orient',
					true,
					array( 'Orienté' )
				),
				'user_id.integer' => array(
					'integer'
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

	}
?>
