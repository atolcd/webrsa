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
	App::uses( 'DossierssimplifiesController', 'Controller' );

	/**
	 * La classe DossierssimplifiesControllerTest ...
	 *
	 * @see http://book.cakephp.org/2.0/en/development/testing.html#testing-controllers
	 *
	 * @package app.Test.Case.Controller
	 */
	class DossierssimplifiesControllerTest extends ControllerTestCase
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
			'app.Decisionregressionorientationcov58',
			'app.Decisionnonorientationprocov58',
			'app.Decisionnonorientationproep58',
			'app.Decisionpropoorientsocialecov58',
			'app.Decisionnonorientationproep93',
			'app.Dernierdossierallocataire',
			'app.Dossier',
			'app.Foyer',
			'app.Nonoriente66',
			'app.Orientstruct',
			'app.Pdf',
			'app.Personne',
			'app.PersonneReferent',
			'app.Prestation',
			'app.Referent',
			'app.Regressionorientationcov58',
			'app.Situationdossierrsa',
			'app.Typeorient',
			'app.Structurereferente',
			'app.User',
		);

		/**
		 * Les données vides renvoyées par le formulaire add.
		 *
		 * @var array
		 */
		public $emptyAddRequestData = array(
			'Dossier' => array(
				'numdemrsatemp' => '0',
				'numdemrsa' => '',
				'dtdemrsa' => array(
					'day' => '17',
					'month' => '06',
					'year' => '2014'
				),
				'matricule' => '',
				'fonorg' => 'CAF'
			),
			'Foyer' => array(
				'id' => ''
			),
			'Prestation' => array(
				0 => array(
					'natprest' => 'RSA',
					'rolepers' => ''
				),
				1 => array(
					'natprest' => 'RSA',
					'rolepers' => ''
				)
			),
			'Personne' => array(
				0 => array(
					'id' => '',
					'qual' => '',
					'nom' => '',
					'prenom' => '',
					'nir' => '',
					'dtnai' => array(
						'day' => '',
						'month' => '',
						'year' => ''
					)
				),
				1 => array(
					'id' => '',
					'qual' => '',
					'nom' => '',
					'prenom' => '',
					'nir' => '',
					'dtnai' => array(
						'day' => '',
						'month' => '',
						'year' => ''
					)
				)
			),
			'Calculdroitrsa' => array(
				0 => array(
					'toppersdrodevorsa' => ''
				),
				1 => array(
					'toppersdrodevorsa' => ''
				)
			),
			'Orientstruct' => array(
				0 => array(
					'origine' => 'manuelle',
					'structureorientante_id' => '',
					'referentorientant_id' => '',
					'statut_orient' => '',
					'typeorient_id' => '',
					'structurereferente_id' => ''
				),
				1 => array(
					'origine' => 'manuelle',
					'structureorientante_id' => '',
					'referentorientant_id' => '',
					'statut_orient' => '',
					'typeorient_id' => '',
					'structurereferente_id' => ''
				)
			)
		);

		/**
		 * Le querydata de base à utiliser pour rechercher les enregistrements
		 * effectués via les méthodes add et edit.
		 *
		 * @var array
		 */
		public $dossierQuery = array(
			'contain' => array(
				'Foyer' => array(
					'Personne' => array(
						'fields' => array(
							'Personne.nom',
							'Personne.prenom'
						),
						'Calculdroitrsa' => array(
							'fields' => array(
								'Calculdroitrsa.toppersdrodevorsa'
							),
						),
						'Orientstruct' => array(
							'fields' => array(
								'Orientstruct.statut_orient',
								'Orientstruct.typeorient_id',
								'Orientstruct.structurereferente_id',
								'Orientstruct.referent_id',
								'Orientstruct.origine'
							),
						),
						'Prestation' => array(
							'fields' => array(
								'Prestation.natprest',
								'Prestation.rolepers'
							),
						)
					)
				),
				'Situationdossierrsa' => array(
					'fields' => array(
						'Situationdossierrsa.etatdosrsa'
					)
				)
			)
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			Configure::write( 'Cg.departement', 66 );

			if( defined( 'CAKEPHP_SHELL' ) && CAKEPHP_SHELL ) {
				$_SERVER['REQUEST_URI'] = '/';
			}

			$this->controller = $this->generate(
				'Dossierssimplifies',
				array(
					'models' => array(
						// Pour bien faire, il faudrait faire un mock de Pdf ou de StoredPdfBehavior
						'Orientstruct' => array( 'ged' )
					)
				)
			);

			parent::setUp();
		}

		/**
		 * Test de la méthode DossierssimplifiesControllerTest::add() lors de
		 * l'accès au formulaire.
		 */
		public function testAddFormAccess() {
			$this->testAction( '/dossierssimplifies/add' );
			$result = $this->controller->viewVars;

			$expected = array(
				'refsorientants' => array(
					'1_1' => 'MR Dupont Martin'
				),
				'structsReferentes' => array(
					'1_1' => '« Projet de Ville RSA d\'Aubervilliers»'
				),
				'options' => array(
					3 => 'Emploi',
					2 => 'Social',
					1 => 'Socioprofessionnelle'
				),
				'statut_orient' => array(
					'Orienté' => 'Orienté',
				),
				'toppersdrodevorsa' => array(
					'' => 'Non défini',
					1 => 'Oui',
					0 => 'Non'
				),
				'rolepers' => array(
					'DEM' => 'Demandeur du RSA',
					'CJT' => 'Conjoint du demandeur'
				),
				'fonorg' => array(
					'CAF' => 'CAF',
					'MSA' => 'MSA'
				),
				'qual' => array(
					'MME' => 'Madame',
					'MR' => 'Monsieur'
				),
				'pays' => array(
					'FRA' => 'France',
					'HOR' => 'Hors de France'
				),
				'typesStruct' => array(),
				'typesOrient' => array(
					1 => 'Socioprofessionnelle',
					2 => 'Social',
					3 => 'Emploi',
					4 => 'Foo'
				),
				'structures' => array(
					'1_1' => '« Projet de Ville RSA d\'Aubervilliers»'
				),
				'etatdosrsa' => array(
					'Z' => 'Non défini',
					0 => 'Nouvelle demande en attente de décision CG pour ouverture du droit',
					1 => 'Droit refusé',
					2 => 'Droit ouvert et versable',
					3 => 'Droit ouvert et suspendu (le montant du droit est calculable, mais l\'existence du droit est remis en cause)',
					4 => 'Droit ouvert mais versement suspendu (le montant du droit n\'est pas calculable)',
					5 => 'Droit clos',
					6 => 'Droit clos sur mois antérieur ayant eu des créances transferées ou une régularisation dans le mois de référence pour une période antérieure.',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode DossierssimplifiesControllerTest::add() lors de
		 * l'envoi du formulaire comportant des erreurs.
		 */
		public function testAddFormSendWithErrors() {
			$data = $this->emptyAddRequestData;
			$this->testAction( '/dossierssimplifies/add', array('data' => $data, 'method' => 'post') );

			$result = array(
				'Dossier' => $this->controller->Dossier->validationErrors,
				'Personne' => $this->controller->Dossier->Foyer->Personne->validationErrors,
			);

			$expected = array(
				'Dossier' => array(
					'numdemrsa' => array(
						'Veuillez n\'utiliser que des lettres et des chiffres',
					),
				),
				'Personne' => array(
					array(
						'qual' => array(
							'Champ obligatoire',
						),
						'nom' => array(
							'Champ obligatoire',
						),
						'prenom' => array(
							'Champ obligatoire',
						),
						'dtnai' => array(
							'Veuillez vérifier le format de la date.',
						),
					),
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Fonction utilitaire permettant de compléter les données renvoyées au
		 * contrôleur dans le cas d'un add pour que le test unitaire passe (dans
		 * l'application, ça passe sans les valeurs par défaut des paramètres).
		 *
		 * @param array $data
		 * @return array
		 */
		protected function _completeRequestDataAdd( array $data ) {
			$data = Hash::merge(
				array(
					'Dossier' => Hash::normalize(
						array_keys( ClassRegistry::init( 'Dossier' )->schema() )
					)
				),
				$data
			);
			$data['Dossier']['haspiecejointe'] = '0';

			$data['Personne'][0]['haspiecejointe'] = '0';
			$data['Personne'][1]['haspiecejointe'] = '0';

			$data['Orientstruct'][0]['haspiecejointe'] = '0';
			$data['Orientstruct'][1]['haspiecejointe'] = '0';

			return $data;
		}

		/**
		 * Test de la méthode DossierssimplifiesControllerTest::add() lors de
		 * l'envoi du formulaire contenant uniquement le demandeur, avec la
		 * génération d'un numéro de demande temporaire et une orientation
		 * "En attente".
		 */
		public function testAddFormSendDemandeurOk() {
			$data = Hash::merge(
				$this->emptyAddRequestData,
				array(
					'Dossier' => array(
						'numdemrsatemp' => '1'
					),
					'Prestation' => array(
						0 => array(
							'natprest' => 'RSA',
							'rolepers' => 'DEM'
						),
					),
					'Personne' => array(
						0 => array(
							'id' => '',
							'qual' => 'MR',
							'nom' => 'AUZOLAT',
							'prenom' => 'ARNAUD',
							'nir' => '',
							'dtnai' => array(
								'day' => '11',
								'month' => '09',
								'year' => '1981'
							)
						),
					),
					'Calculdroitrsa' => array(
						0 => array(
							'toppersdrodevorsa' => '0'
						),
					),
				)
			);
			$data['Orientstruct'][0] = array(
				'statut_orient' => 'En attente',
				'Orientstruct.typeorient_id' => '',
				'Orientstruct.structurereferente_id' => '',
				'Orientstruct.referent_id' => '',
				'origine' => 'manuelle',
			);

			$data = $this->_completeRequestDataAdd( $data );

			$result = $this->testAction( '/dossierssimplifies/add', array( 'data' => $data, 'method' => 'post' ) );
			$this->assertNull( $result );

			// Les enregistrements ont-ils été effectués ?
			$Dossier = ClassRegistry::init( 'Dossier' );
			$query = $this->dossierQuery;
			$query['conditions'] = array(
				'Dossier.numdemrsa' => 'TMP00000001'
			);
			$result = (array)$Dossier->find( 'first', $query );

			$expected = array(
				'Dossier' => array(
					'id' => 3,
					'numdemrsa' => 'TMP00000001',
					'dtdemrsa' => '2014-06-17',
					'dtdemrmi' => NULL,
					'numdepinsrmi' => NULL,
					'typeinsrmi' => NULL,
					'numcominsrmi' => NULL,
					'numagrinsrmi' => NULL,
					'numdosinsrmi' => NULL,
					'numcli' => NULL,
					'numorg' => NULL,
					'fonorg' => 'CAF',
					'matricule' => NULL,
					'statudemrsa' => NULL,
					'typeparte' => NULL,
					'ideparte' => NULL,
					'fonorgcedmut' => NULL,
					'numorgcedmut' => NULL,
					'matriculeorgcedmut' => NULL,
					'ddarrmut' => NULL,
					'codeposanchab' => NULL,
					'fonorgprenmut' => NULL,
					'numorgprenmut' => NULL,
					'dddepamut' => NULL,
					'detaildroitrsa_id' => NULL,
					'avispcgdroitrsa_id' => NULL,
					'organisme_id' => NULL,
					'statut' => 'Nouvelle demande'
				),
				'Foyer' => array(
					'id' => 3,
					'dossier_id' => 3,
					'sitfam' => NULL,
					'ddsitfam' => NULL,
					'typeocclog' => NULL,
					'mtvallocterr' => NULL,
					'mtvalloclog' => NULL,
					'contefichliairsa' => '',
					'mtestrsa' => NULL,
					'raisoctieelectdom' => '',
					'regagrifam' => '',
					'haspiecejointe' => '0',
					'enerreur' => NULL,
					'sansprestation' => NULL,
					'Personne' => array(
						0 => array(
							'nom' => 'AUZOLAT',
							'prenom' => 'ARNAUD',
							'id' => 8,
							'foyer_id' => 3,
							'Calculdroitrsa' => array(
								'toppersdrodevorsa' => '0',
								'id' => 2,
							),
							'Prestation' => array(
								'natprest' => 'RSA',
								'rolepers' => 'DEM',
								'id' => 4,
							),
							'Orientstruct' => array(
								0 => array(
									'statut_orient' => 'En attente',
									'origine' => null,
									'typeorient_id' => null,
									'structurereferente_id' => null,
									'referent_id' => null,
									'id' => 2,
									'personne_id' => 8,
								),
							),
						),
					),
				),
				'Situationdossierrsa' => array(
					'etatdosrsa' => 'Z',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode DossierssimplifiesControllerTest::add() lors de
		 * l'envoi du formulaire contenant le demandeur, le conjoint, avec la
		 * génération d'un numéro de demande temporaire, une orientation
		 * "Non orienté" pour le demandeur et une orientation "Orienté" pour le
		 * conjoint.
		 */
		public function testAddFormSendDemandeurConjointOk() {
			$data = Hash::merge(
				$this->emptyAddRequestData,
				array(
					'Dossier' => array(
						'numdemrsatemp' => '1'
					),
					'Prestation' => array(
						0 => array(
							'natprest' => 'RSA',
							'rolepers' => 'DEM'
						),
						1 => array(
							'natprest' => 'RSA',
							'rolepers' => 'CJT'
						),
					),
					'Personne' => array(
						0 => array(
							'id' => '',
							'qual' => 'MR',
							'nom' => 'AUZOLAT',
							'prenom' => 'ARNAUD',
							'nir' => '',
							'dtnai' => array(
								'day' => '11',
								'month' => '09',
								'year' => '1981'
							)
						),
						1 => array(
							'id' => '',
							'qual' => 'MME',
							'nom' => 'AUZOLAT',
							'prenom' => 'CELINE',
							'nir' => '',
							'dtnai' => array(
								'day' => '08',
								'month' => '11',
								'year' => '1983'
							)
						),
					),
					'Calculdroitrsa' => array(
						0 => array(
							'toppersdrodevorsa' => '0'
						),
						1 => array(
							'toppersdrodevorsa' => '0'
						),
					),
				)
			);
			$data['Orientstruct'][0] = array(
				'statut_orient' => 'Non orienté',
				'origine' => 'manuelle',
				'typeorient_id' => null,
				'structurereferente_id' => null,
				'referent_id' => null,
			);
			$data['Orientstruct'][1] = array(
				'statut_orient' => 'Orienté',
				'origine' => 'manuelle',
				'typeorient_id' => 1,
				'structurereferente_id' => 1,
				'referent_id' => null,
			);

			$data = $this->_completeRequestDataAdd( $data );

			$result = $this->testAction( '/dossierssimplifies/add', array( 'data' => $data, 'method' => 'post' ) );
			$this->assertNull( $result );

			// Les enregistrements ont-ils été effectués ?
			$Dossier = ClassRegistry::init( 'Dossier' );
			$query = $this->dossierQuery;
			$query['conditions'] = array(
				'Dossier.numdemrsa' => 'TMP00000001'
			);
			$result = (array)$Dossier->find( 'first', $query );

			$expected = array(
				'Dossier' => array(
					'id' => 3,
					'numdemrsa' => 'TMP00000001',
					'dtdemrsa' => '2014-06-17',
					'dtdemrmi' => NULL,
					'numdepinsrmi' => NULL,
					'typeinsrmi' => NULL,
					'numcominsrmi' => NULL,
					'numagrinsrmi' => NULL,
					'numdosinsrmi' => NULL,
					'numcli' => NULL,
					'numorg' => NULL,
					'fonorg' => 'CAF',
					'matricule' => NULL,
					'statudemrsa' => NULL,
					'typeparte' => NULL,
					'ideparte' => NULL,
					'fonorgcedmut' => NULL,
					'numorgcedmut' => NULL,
					'matriculeorgcedmut' => NULL,
					'ddarrmut' => NULL,
					'codeposanchab' => NULL,
					'fonorgprenmut' => NULL,
					'numorgprenmut' => NULL,
					'dddepamut' => NULL,
					'detaildroitrsa_id' => NULL,
					'avispcgdroitrsa_id' => NULL,
					'organisme_id' => NULL,
					'statut' => 'Nouvelle demande'
				),
				'Foyer' => array(
					'id' => 3,
					'dossier_id' => 3,
					'sitfam' => NULL,
					'ddsitfam' => NULL,
					'typeocclog' => NULL,
					'mtvallocterr' => NULL,
					'mtvalloclog' => NULL,
					'contefichliairsa' => '',
					'mtestrsa' => NULL,
					'raisoctieelectdom' => '',
					'regagrifam' => '',
					'haspiecejointe' => '0',
					'enerreur' => NULL,
					'sansprestation' => NULL,
					'Personne' => array(
						0 => array(
							'nom' => 'AUZOLAT',
							'prenom' => 'ARNAUD',
							'id' => 8,
							'foyer_id' => 3,
							'Calculdroitrsa' => array(
								'toppersdrodevorsa' => '0',
								'id' => 2,
							),
							'Prestation' => array(
								'natprest' => 'RSA',
								'rolepers' => 'DEM',
								'id' => 4,
							),
							'Orientstruct' => array(
								0 => array(
									'statut_orient' => 'Non orienté',
									'origine' => null,
									'typeorient_id' => null,
									'structurereferente_id' => null,
									'referent_id' => null,
									'id' => 2,
									'personne_id' => 8,
								),
							),
						),
						1 => array(
							'nom' => 'AUZOLAT',
							'prenom' => 'CELINE',
							'id' => 9,
							'foyer_id' => 3,
							'Calculdroitrsa' => array(
								'toppersdrodevorsa' => '0',
								'id' => 3,
							),
							'Prestation' => array(
								'natprest' => 'RSA',
								'rolepers' => 'CJT',
								'id' => 5,
							),
							'Orientstruct' => array(
								0 => array(
									'statut_orient' => 'Orienté',
									'origine' => 'manuelle',
									'typeorient_id' => 1,
									'structurereferente_id' => 1,
									'referent_id' => null,
									'id' => 3,
									'personne_id' => 9,
								),
							),
						),
					),
				),
				'Situationdossierrsa' => array(
					'etatdosrsa' => 'Z',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode DossierssimplifiesControllerTest::add() lors de
		 * l'envoi du formulaire contenant uniquement le demandeur, avec la
		 * génération d'un numéro de demande temporaire et pas d'orientation
		 * car le statut de l'orientation n'est pas renseigné.
		 */
		public function testAddFormSendDemandeurOkSansOrientation() {
			$data = Hash::merge(
				$this->emptyAddRequestData,
				array(
					'Dossier' => array(
						'numdemrsatemp' => '1'
					),
					'Prestation' => array(
						0 => array(
							'natprest' => 'RSA',
							'rolepers' => 'DEM'
						),
					),
					'Personne' => array(
						0 => array(
							'id' => '',
							'qual' => 'MR',
							'nom' => 'AUZOLAT',
							'prenom' => 'ARNAUD',
							'nir' => '',
							'dtnai' => array(
								'day' => '11',
								'month' => '09',
								'year' => '1981'
							)
						),
					),
					'Calculdroitrsa' => array(
						0 => array(
							'toppersdrodevorsa' => '1'
						),
					),
				)
			);
			$data['Orientstruct'][0] = array(
				'statut_orient' => '',
				'Orientstruct.typeorient_id' => '',
				'Orientstruct.structurereferente_id' => '',
				'Orientstruct.referent_id' => '',
				'origine' => 'manuelle',
			);

			$data = $this->_completeRequestDataAdd( $data );

			$result = $this->testAction( '/dossierssimplifies/add', array( 'data' => $data, 'method' => 'post' ) );
			$this->assertNull( $result );

			// Les enregistrements ont-ils été effectués ?
			$Dossier = ClassRegistry::init( 'Dossier' );
			$query = $this->dossierQuery;
			$query['conditions'] = array(
				'Dossier.numdemrsa' => 'TMP00000001'
			);
			$result = (array)$Dossier->find( 'first', $query );

			$expected = array(
				'Dossier' => array(
					'id' => 3,
					'numdemrsa' => 'TMP00000001',
					'dtdemrsa' => '2014-06-17',
					'dtdemrmi' => NULL,
					'numdepinsrmi' => NULL,
					'typeinsrmi' => NULL,
					'numcominsrmi' => NULL,
					'numagrinsrmi' => NULL,
					'numdosinsrmi' => NULL,
					'numcli' => NULL,
					'numorg' => NULL,
					'fonorg' => 'CAF',
					'matricule' => NULL,
					'statudemrsa' => NULL,
					'typeparte' => NULL,
					'ideparte' => NULL,
					'fonorgcedmut' => NULL,
					'numorgcedmut' => NULL,
					'matriculeorgcedmut' => NULL,
					'ddarrmut' => NULL,
					'codeposanchab' => NULL,
					'fonorgprenmut' => NULL,
					'numorgprenmut' => NULL,
					'dddepamut' => NULL,
					'detaildroitrsa_id' => NULL,
					'avispcgdroitrsa_id' => NULL,
					'organisme_id' => NULL,
					'statut' => 'Nouvelle demande'
				),
				'Foyer' => array(
					'id' => 3,
					'dossier_id' => 3,
					'sitfam' => NULL,
					'ddsitfam' => NULL,
					'typeocclog' => NULL,
					'mtvallocterr' => NULL,
					'mtvalloclog' => NULL,
					'contefichliairsa' => '',
					'mtestrsa' => NULL,
					'raisoctieelectdom' => '',
					'regagrifam' => '',
					'haspiecejointe' => '0',
					'enerreur' => NULL,
					'sansprestation' => NULL,
					'Personne' => array(
						0 => array(
							'nom' => 'AUZOLAT',
							'prenom' => 'ARNAUD',
							'id' => 8,
							'foyer_id' => 3,
							'Calculdroitrsa' => array(
								'toppersdrodevorsa' => '1',
								'id' => 2,
							),
							'Prestation' => array(
								'natprest' => 'RSA',
								'rolepers' => 'DEM',
								'id' => 4,
							),
							'Orientstruct' => array(
							),
						),
					),
				),
				'Situationdossierrsa' => array(
					'etatdosrsa' => 'Z',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

	}
?>