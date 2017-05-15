<?php
	/**
	 * Code source de la classe ConditionnableBehaviorTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ConditionnableBehavior', 'Model/Behavior' );

	/**
	 * La classe ConditionnableBehaviorTest réalise les tests unitaires de la
	 * classe ConditionnableBehavior.
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class ConditionnableBehaviorTest extends CakeTestCase
	{
		/**
		 * Modèle Foyer utilisé par ce test.
		 *
		 * @var Model
		 */
		public $Foyer = null;

		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Adresse',
			'app.AdresseCanton',
			'app.Canton',
			'app.Zonegeographique',
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
			Configure::write( 'CG.cantons', false );
			$this->Canton = ClassRegistry::init( 'Canton' );
			$this->Canton->Behaviors->attach( 'Conditionnable' );
			Configure::read( 'CG.cantons', false );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Canton );
			parent::tearDown();
		}

		/**
		 * Retourne une partie de querydata dans laquelle les espaces multiples
		 * et retours à la ligne sont remplacés par des espaces simples.
		 *
		 * @param array $querypart
		 * @return string
		 */
		protected function _normalizeQueryPart( array $querypart ) {
			$querypart = recursive_key_value_preg_replace( $querypart, array( '/\s+/m' => ' ' ) );
			return $querypart;
		}

		/**
		 * Test de la méthode ConditionnableBehavior::conditionsAdresse() sans
		 * qu'aucun filtre sur l'adresse ne soit rempli.
		 */
		public function testConditionsAdresseFiltreVide() {
			$search = array(
				'Adresse' => array(
					'nomcom' => null,
					'nomvoie' => null,
					'numcom' => null,
				),
				'Canton' => array(
					'canton' => null
				),
			);
			$result = $this->Canton->conditionsAdresse( array(), $search );
			$expected = array();
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ConditionnableBehavior::conditionsAdresse()
		 */
		public function testConditionsAdresse() {
			// 1. Sans les cantons activés
			$search = array(
				'Adresse' => array(
					'nomcom' => '*AUBERVILLIERS*',
					'nomvoie' => '*Commune de Paris*',
					'numcom' => 93001,
				),
				'Canton' => array(
					'canton' => 'Canton 1'
				)
			);
			$result = $this->Canton->conditionsAdresse( array(), $search );
			$expected = array(
				'Adresse.nomcom ILIKE \'%AUBERVILLIERS%\'',
				'Adresse.nomvoie ILIKE \'%Commune de Paris%\'',
				'Adresse.numcom = \'93001\'',
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Sans les cantons , avec un numcom sur 4 caractères
			$search = array(
				'Adresse' => array(
					'nomcom' => '*AUBERVILLIERS*',
					'nomvoie' => '*Commune de Paris*',
					'numcom' => 9300,
				),
				'Canton' => array(
					'canton' => 'Canton 1'
				)
			);
			$result = $this->Canton->conditionsAdresse( array(), $search );
			$expected = array (
				'Adresse.nomcom ILIKE \'%AUBERVILLIERS%\'',
				'Adresse.nomvoie ILIKE \'%Commune de Paris%\'',
				'Adresse.numcom ILIKE \'%9300%\'',
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3. Avec les cantons activés
			Configure::write( 'CG.cantons', true );
			$search = array(
				'Adresse' => array(
					'nomcom' => '*AUBERVILLIERS*',
					'nomvoie' => '*Commune de Paris*',
					'numcom' => 93001,
				),
				'Canton' => array(
					'canton' => 'Canton 1'
				)
			);
			$result = $this->Canton->conditionsAdresse( array(), $search );
			$expected = array(
				'Adresse.nomcom ILIKE \'%AUBERVILLIERS%\'',
				'Adresse.nomvoie ILIKE \'%Commune de Paris%\'',
				'Adresse.numcom = \'93001\'',
				array(
					'or' => array(
						array(
							'OR' => array(
								'Adresse.numcom' => '93001',
								'Adresse.codepos' => '93300',
							),
							'Adresse.nomcom ILIKE' => 'AUBERVILLIERS',
						),
						array(
							'OR' => array(
								'Adresse.numcom' => '93008',
								'Adresse.codepos' => '93000',
							),
							'Adresse.nomcom ILIKE' => 'BOBIGNY',
						),
					),
				),
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ConditionnableBehavior::conditionsAdresse() avec
		 * des restrictions sur les zones géographiques.
		 */
		public function testConditionsAdresseFiltreZoneGeo() {
			// 1. Restriction mais aucune zone sélectionnée
			$result = $this->Canton->conditionsAdresse(
				array(),
				array(),
				true,
				array()
			);
			$expected = array( '( Adresse.numcom IN ( \'\' ) )' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Restriction avec 2 zones sélectionnées
			$result = $this->Canton->conditionsAdresse(
				array(),
				array(),
				true,
				array( '93008', '93010' )
			);
			$expected = array( '( Adresse.numcom IN ( \'93008\', \'93010\' ) )' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3. Restriction avec 2 zones sélectionnées et les cantons activés
			Configure::write( 'CG.cantons', true );
			$result = $this->Canton->conditionsAdresse(
				array(),
				array(),
				true,
				array( '93001', '93008', '93010' )
			);
			$expected = array(
				array(
					'OR' => array(
						array(
							'OR' => array(
								'Adresse.numcom' => '93001',
								'Adresse.codepos' => '93300',
							),
							'Adresse.nomcom ILIKE' => 'AUBERVILLIERS',
						),
						array(
							'OR' => array(
								'Adresse.numcom' => '93008',
								'Adresse.codepos' => '93000',
							),
							'Adresse.nomcom ILIKE' => 'BOBIGNY',
						),
					),
				),
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ConditionnableBehavior::conditionsAdresse() avec
		 * de multiples valeurs pour numcom.
		 */
		public function testConditionsAdresseNumcomMultiples() {
			// 1. Avec des codes INSEE sur 5 caractères
			$search = array(
				'Adresse' => array(
					'numcom' => array( 93001, 93002 )
				)
			);
			$result = $this->Canton->conditionsAdresse( array(), $search );
			$expected = array(
				array(
					'OR' => array(
						'Adresse.numcom = \'93001\'',
						'Adresse.numcom = \'93002\'',
					)
				)
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Avec des codes INSEE sur 4 caractères
			$search = array(
				'Adresse' => array(
					'numcom' => array( 6603, 6605 )
				)
			);
			$result = $this->Canton->conditionsAdresse( array(), $search );
			$expected = array(
				array(
					'OR' => array(
						'Adresse.numcom ILIKE \'%6603%\'',
						'Adresse.numcom ILIKE \'%6605%\'',
					)
				)
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ConditionnableBehavior::conditionsDossier().
		 */
		public function testConditionsDossier() {
			// 1. Filtre vide
			$search = array(
				'Dossier' => array(
					'numdemrsa' => null,
					'matricule' => null,
					'dtdemrsa' => null,
					'fonorg' => null,
					'anciennete_dispositif' => null,
				),
			);
			$result = $this->Canton->conditionsDossier( array(), $search );
			$expected = array();
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Filtre plein
			$search = array(
				'Dossier' => array(
					'numdemrsa' => '01234567890',
					'matricule' => '987654321098765',
					'dtdemrsa' => array(
						'year' => '2009',
						'month' => '06',
						'day' => '12',
					),
					'fonorg' => 'CAF',
					'anciennete_dispositif' => '6_12',
				),
			);
			$result = $this->Canton->conditionsDossier( array(), $search );
			$expected = array(
				'Dossier.numdemrsa ILIKE \'%01234567890%\'',
				'Dossier.matricule ILIKE \'%987654321098765%\'',
				'Dossier.dtdemrsa' => '2009-06-12',
				array( 'Dossier.fonorg' => 'CAF' ),
				'EXTRACT( YEAR FROM AGE( NOW(), "Dossier"."dtdemrsa" ) ) BETWEEN 6 AND 12'
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3. Filtre sur dtdemrsa avec des bornes
			$search = array(
				'Dossier' => array(
					'dtdemrsa' => 1,
					'dtdemrsa_from' => array(
						'year' => '2009',
						'month' => '06',
						'day' => '12',
					),
					'dtdemrsa_to' => array(
						'year' => '2010',
						'month' => '12',
						'day' => '24',
					),
				),
			);
			$result = $this->Canton->conditionsDossier( array(), $search );
			$expected = array(
				'Dossier.dtdemrsa BETWEEN \'2009-06-12\' AND \'2010-12-24\''
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ConditionnableBehavior::conditionsFoyer().
		 */
		public function testConditionsFoyer() {
			// 1. Filtre vide
			$search = array(
				'Foyer' => array(
					'sitfam' => null,
					'ddsitfam' => array(
						'year' => null,
						'month' => null,
						'day' => null,
					),
				),
			);
			$result = $this->Canton->conditionsFoyer( array(), $search );
			$expected = array();
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Filtre plein
			$search = array(
				'Foyer' => array(
					'sitfam' => 'CEL',
					'ddsitfam' => array(
						'year' => '2010',
						'month' => '11',
						'day' => '04',
					),
				),
			);
			$result = $this->Canton->conditionsFoyer( array(), $search );
			$expected = array(
				'Foyer.sitfam' => 'CEL',
				'Foyer.ddsitfam' => '2010-11-04'
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ConditionnableBehavior::conditionsSituationdossierrsa().
		 */
		public function testConditionsSituationdossierrsa() {
			// 1. Filtre vide
			$search = array(
				'Situationdossierrsa' => array(
					'etatdosrsa' => null,
				),
			);
			$result = $this->Canton->conditionsSituationdossierrsa( array(), $search );
			$expected = array();
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Filtre plein
			$search = array(
				'Situationdossierrsa' => array(
					'etatdosrsa' => array( 2, 3, 4 ),
				),
			);
			$result = $this->Canton->conditionsSituationdossierrsa( array(), $search );
			$expected = array(
				'( Situationdossierrsa.etatdosrsa IN ( \'2\', \'3\', \'4\' ) )',
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ConditionnableBehavior::conditionsPersonne().
		 */
		public function testConditionsPersonne() {
			// 1. Filtre vide
			$search = array(
				'Personne' => array(
					'nom' => null,
					'prenom' => null,
					'nomnai' => null,
					'nir' => null,
					'sexe' => null,
					'dtnai' => array(
						'year' => null,
						'month' => null,
						'day' => null,
					),
					'trancheage' => null,
				),
			);
			$result = $this->Canton->conditionsPersonne( array(), $search );
			$expected = array();
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Filtre plein
			$search = array(
				'Personne' => array(
					'nom' => 'buffin',
					'prenom' => 'christian',
					'nomnai' => 'buffin',
					'nir' => '',
					'sexe' => '1',
					'dtnai' => array(
						'year' => '1979',
						'month' => '01',
						'day' => '24',
					),
					'trancheage' => '30_35',
				),
			);
			$result = $this->Canton->conditionsPersonne( array(), $search );
			$expected = array(
				'UPPER(Personne.nom) LIKE \'BUFFIN\'',
				'UPPER(Personne.prenom) LIKE \'CHRISTIAN\'',
				'UPPER(Personne.nomnai) LIKE \'BUFFIN\'',
				'Personne.sexe' => '1',
				'DATE( Personne.dtnai ) = \'1979-01-24\'',
				'( EXTRACT ( YEAR FROM AGE( Personne.dtnai ) ) ) BETWEEN 30 AND 35'
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ConditionnableBehavior::conditionsDetailcalculdroitrsa().
		 */
		public function testConditionsDetailcalculdroitrsa() {
			// 1. Filtre vide
			$search = array(
				'Detailcalculdroitrsa' => array(
					'natpf' => null,
				),
			);
			$result = $this->Canton->conditionsDetailcalculdroitrsa( array(), $search );
			$expected = array();
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Valeur simple
			$search = array(
				'Detailcalculdroitrsa' => array(
					'natpf' => 'RSD',
				),
			);
			$result = $this->Canton->conditionsDetailcalculdroitrsa( array(), $search );
			$expected = array(
				'Detaildroitrsa.id IN (
					SELECT detailscalculsdroitsrsa.detaildroitrsa_id
						FROM detailscalculsdroitsrsa
							INNER JOIN detailsdroitsrsa ON (
								detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id
							)
						WHERE
							detailsdroitsrsa.dossier_id = Dossier.id
							AND detailscalculsdroitsrsa.natpf = \'RSD\'
				)',
			);

			$result = $this->_normalizeQueryPart( $result );
			$expected = $this->_normalizeQueryPart( $expected );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3. Valeurs multiples
			$search = array(
				'Detailcalculdroitrsa' => array(
					'natpf' => array( 'RSD', 'RCD' ),
				),
			);
			$result = $this->Canton->conditionsDetailcalculdroitrsa( array(), $search );
			$expected = array(
				'Detaildroitrsa.id IN (
					SELECT detailscalculsdroitsrsa.detaildroitrsa_id
						FROM detailscalculsdroitsrsa
							INNER JOIN detailsdroitsrsa ON (
								detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id
							)
						WHERE
							detailsdroitsrsa.dossier_id = Dossier.id
							AND detailscalculsdroitsrsa.natpf IN ( \'RSD\', \'RCD\' )
				)',
			);
			$result = $this->_normalizeQueryPart( $result );
			$expected = $this->_normalizeQueryPart( $expected );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 4. Valeur simple - valeur simple
			$search = array(
				'Detailcalculdroitrsa' => array(
					'natpf' => 'RSD-RCD',
				),
			);
			$result = $this->Canton->conditionsDetailcalculdroitrsa( array(), $search );
			$expected = array(
				'Detaildroitrsa.id IN ( SELECT detailscalculsdroitsrsa.detaildroitrsa_id FROM detailscalculsdroitsrsa INNER JOIN detailsdroitsrsa ON ( detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id ) WHERE detailsdroitsrsa.dossier_id = Dossier.id AND detailscalculsdroitsrsa.natpf = \'RSD\' )',
				array(
					'NOT' => array(
						'Detaildroitrsa.id IN ( SELECT detailscalculsdroitsrsa.detaildroitrsa_id FROM detailscalculsdroitsrsa INNER JOIN detailsdroitsrsa ON ( detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id ) WHERE detailsdroitsrsa.dossier_id = Dossier.id AND detailscalculsdroitsrsa.natpf = \'RCD\' )',
					),
				),
			);
			$result = $this->_normalizeQueryPart( $result );
			$expected = $this->_normalizeQueryPart( $expected );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 5. Valeurs multiples conjointes sous forme de valeur simple
			$search = array(
				'Detailcalculdroitrsa' => array(
					'natpf' => 'RSD,RCD',
				),
			);
			$result = $this->Canton->conditionsDetailcalculdroitrsa( array(), $search );
			$expected = array(
				'Detaildroitrsa.id IN ( SELECT detailscalculsdroitsrsa.detaildroitrsa_id FROM detailscalculsdroitsrsa INNER JOIN detailsdroitsrsa ON ( detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id ) WHERE detailsdroitsrsa.dossier_id = Dossier.id AND detailscalculsdroitsrsa.natpf = \'RSD\' )',
				'Detaildroitrsa.id IN ( SELECT detailscalculsdroitsrsa.detaildroitrsa_id FROM detailscalculsdroitsrsa INNER JOIN detailsdroitsrsa ON ( detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id ) WHERE detailsdroitsrsa.dossier_id = Dossier.id AND detailscalculsdroitsrsa.natpf = \'RCD\' )',
			);
			$result = $this->_normalizeQueryPart( $result );
			$expected = $this->_normalizeQueryPart( $expected );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 6. Combinaison de toutes les méthodes ci-dessus
			$search = array(
				'Detailcalculdroitrsa' => array(
					'natpf' => array(
						'RSD,RCD',
						'RSD-RCD',
						'RSD',
						'RCD',
					),
				),
			);
			$result = $this->Canton->conditionsDetailcalculdroitrsa( array(), $search );
			$expected = array(
				array(
					'OR' => array(
						array(
							'Detaildroitrsa.id IN ( SELECT detailscalculsdroitsrsa.detaildroitrsa_id FROM detailscalculsdroitsrsa INNER JOIN detailsdroitsrsa ON ( detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id ) WHERE detailsdroitsrsa.dossier_id = Dossier.id AND detailscalculsdroitsrsa.natpf = \'RSD\' )',
							'Detaildroitrsa.id IN ( SELECT detailscalculsdroitsrsa.detaildroitrsa_id FROM detailscalculsdroitsrsa INNER JOIN detailsdroitsrsa ON ( detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id ) WHERE detailsdroitsrsa.dossier_id = Dossier.id AND detailscalculsdroitsrsa.natpf = \'RCD\' )',
						),
						array(
							'Detaildroitrsa.id IN ( SELECT detailscalculsdroitsrsa.detaildroitrsa_id FROM detailscalculsdroitsrsa INNER JOIN detailsdroitsrsa ON ( detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id ) WHERE detailsdroitsrsa.dossier_id = Dossier.id AND detailscalculsdroitsrsa.natpf = \'RSD\' )',
							array(
								'NOT' => array(
									'Detaildroitrsa.id IN ( SELECT detailscalculsdroitsrsa.detaildroitrsa_id FROM detailscalculsdroitsrsa INNER JOIN detailsdroitsrsa ON ( detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id ) WHERE detailsdroitsrsa.dossier_id = Dossier.id AND detailscalculsdroitsrsa.natpf = \'RCD\' )',
								),
							),
						),
						array(
							'Detaildroitrsa.id IN ( SELECT detailscalculsdroitsrsa.detaildroitrsa_id FROM detailscalculsdroitsrsa INNER JOIN detailsdroitsrsa ON ( detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id ) WHERE detailsdroitsrsa.dossier_id = Dossier.id AND detailscalculsdroitsrsa.natpf = \'RSD\' )',
						),
						array(
							'Detaildroitrsa.id IN ( SELECT detailscalculsdroitsrsa.detaildroitrsa_id FROM detailscalculsdroitsrsa INNER JOIN detailsdroitsrsa ON ( detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id ) WHERE detailsdroitsrsa.dossier_id = Dossier.id AND detailscalculsdroitsrsa.natpf = \'RCD\' )',
						),
					),
				),
			);
			$result = $this->_normalizeQueryPart( $result );
			$expected = $this->_normalizeQueryPart( $expected );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 7. Valeurs multiples conjointes sous forme de valeur simple
			$search = array(
				'Detailcalculdroitrsa' => array(
					'natpf' => array(
						'RSD-RCD'
					),
				),
			);
			$result = $this->Canton->conditionsDetailcalculdroitrsa( array(), $search );
			$expected = array(
				array(
					'OR' => array(
						array(
							'Detaildroitrsa.id IN ( SELECT detailscalculsdroitsrsa.detaildroitrsa_id FROM detailscalculsdroitsrsa INNER JOIN detailsdroitsrsa ON ( detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id ) WHERE detailsdroitsrsa.dossier_id = Dossier.id AND detailscalculsdroitsrsa.natpf = \'RSD\' )',
							array(
								'NOT' => array(
									'Detaildroitrsa.id IN ( SELECT detailscalculsdroitsrsa.detaildroitrsa_id FROM detailscalculsdroitsrsa INNER JOIN detailsdroitsrsa ON ( detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id ) WHERE detailsdroitsrsa.dossier_id = Dossier.id AND detailscalculsdroitsrsa.natpf = \'RCD\' )',
								)
							)
						)
					)
				)
			);
			$result = $this->_normalizeQueryPart( $result );
			$expected = $this->_normalizeQueryPart( $expected );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ConditionnableBehavior::conditionsCalculdroitrsa().
		 */
		public function testConditionsCalculdroitrsa() {
			// 1. Filtre vide
			$search = array(
				'Calculdroitrsa' => array(
					'toppersdrodevorsa' => null,
				),
			);
			$result = $this->Canton->conditionsCalculdroitrsa( array(), $search );
			$expected = array();
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Filtre plein, valeur NULL
			$search = array(
				'Calculdroitrsa' => array(
					'toppersdrodevorsa' => 'NULL',
				),
			);
			$result = $this->Canton->conditionsCalculdroitrsa( array(), $search );
			$expected = array(
				array(
					'Calculdroitrsa.toppersdrodevorsa IS NULL',
				),
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3. Filtre plein, valeur true
			$search = array(
				'Calculdroitrsa' => array(
					'toppersdrodevorsa' => '1',
				),
			);
			$result = $this->Canton->conditionsCalculdroitrsa( array(), $search );
			$expected = array(
				array(
					'Calculdroitrsa.toppersdrodevorsa' => '1',
				),
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 4. Filtre plein, valeur faux
			$search = array(
				'Calculdroitrsa' => array(
					'toppersdrodevorsa' => '0',
				),
			);
			$result = $this->Canton->conditionsCalculdroitrsa( array(), $search );
			$expected = array(
				array(
					'Calculdroitrsa.toppersdrodevorsa' => '0',
				),
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ConditionnableBehavior::conditionsDernierDossierAllocataire().
		 */
		public function testConditionsDernierDossierAllocataire() {
			// 1. Filtre vide
			$search = array(
				'Dossier' => array(
					'dernier' => null,
				),
			);
			$result = $this->Canton->conditionsDernierDossierAllocataire( array(), $search );
			$expected = array();
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Filtre plein, valeur false
			$search = array(
				'Dossier' => array(
					'dernier' => '0',
				),
			);
			$result = $this->Canton->conditionsDernierDossierAllocataire( array(), $search );
			$expected = array();
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3. Filtre plein, valeur true, sans utiliser la table derniersdossiersallocataires
			Configure::write( 'Optimisations.useTableDernierdossierallocataire', false );
			$search = array(
				'Dossier' => array(
					'dernier' => '1',
				),
			);
			$result = $this->Canton->conditionsDernierDossierAllocataire( array(), $search );
			$expected = array (
				'Dossier.id IN ( SELECT dossiers.id FROM personnes INNER JOIN prestations ON ( personnes.id = prestations.personne_id AND prestations.natprest = \'RSA\' ) INNER JOIN foyers ON ( personnes.foyer_id = foyers.id ) INNER JOIN dossiers ON ( dossiers.id = foyers.dossier_id ) WHERE prestations.rolepers IN ( \'DEM\', \'CJT\' ) AND ( ( nir_correct13( Personne.nir ) AND nir_correct13( personnes.nir ) AND SUBSTRING( TRIM( BOTH \' \' FROM personnes.nir ) FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH \' \' FROM Personne.nir ) FROM 1 FOR 13 ) AND personnes.dtnai = Personne.dtnai ) OR ( UPPER(personnes.nom) = UPPER(Personne.nom) AND UPPER(personnes.prenom) = UPPER(Personne.prenom) AND personnes.dtnai = Personne.dtnai ) ) ORDER BY dossiers.dtdemrsa DESC, dossiers.id DESC LIMIT 1 )',
			);

			$result = $this->_normalizeQueryPart( $result );
			$expected = $this->_normalizeQueryPart( $expected );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3. Filtre plein, valeur true, en utilisant la table derniersdossiersallocataires
			Configure::write( 'Optimisations.useTableDernierdossierallocataire', true );
			$search = array(
				'Dossier' => array(
					'dernier' => '1',
				),
			);
			$result = $this->Canton->conditionsDernierDossierAllocataire( array(), $search );
			$expected = array (
				'Dossier.id IN ( SELECT derniersdossiersallocataires.dossier_id FROM derniersdossiersallocataires WHERE derniersdossiersallocataires.personne_id = Personne.id )',
			);

			$result = $this->_normalizeQueryPart( $result );
			$expected = $this->_normalizeQueryPart( $expected );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ConditionnableBehavior::conditionsDates().
		 */
		public function testConditionsDates() {
			// 1. Filtre vide
			$search = array(
				'Orientstruct' => array(
					'date_valid' => null,
				),
			);
			$result = $this->Canton->conditionsDates( array(), $search, 'Orientstruct.date_valid' );
			$expected = array();
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Filtre plein, désactivé
			$search = array(
				'Orientstruct' => array(
					'date_valid' => false,
					'date_valid_from' => array(
						'year' => '2012',
						'month' => '03',
						'day' => '01'
					),
					'date_valid_to' => array(
						'year' => '2012',
						'month' => '03',
						'day' => '02'
					),
				),
			);
			$result = $this->Canton->conditionsDates( array(), $search, 'Orientstruct.date_valid' );
			$expected = array();
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Filtre plein, activé
			$search = array(
				'Orientstruct' => array(
					'date_valid' => true,
					'date_valid_from' => array(
						'year' => '2012',
						'month' => '03',
						'day' => '01'
					),
					'date_valid_to' => array(
						'year' => '2012',
						'month' => '03',
						'day' => '02'
					),
				),
			);
			$result = $this->Canton->conditionsDates( array(), $search, 'Orientstruct.date_valid' );
			$expected = array(
				'DATE( Orientstruct.date_valid ) BETWEEN \'2012-03-01\' AND \'2012-03-02\'',
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 3. Filtre plein, valeurs multiples
			$search = array(
				'Orientstruct' => array(
					'date_valid' => true,
					'date_valid_from' => array(
						'year' => '2012',
						'month' => '03',
						'day' => '01'
					),
					'date_valid_to' => array(
						'year' => '2012',
						'month' => '03',
						'day' => '02'
					),
				),
				'Dossier' => array(
					'dtdemrsa' => true,
					'dtdemrsa_from' => array(
						'year' => '2009',
						'month' => '07',
						'day' => '01'
					),
					'dtdemrsa_to' => array(
						'year' => '2013',
						'month' => '12',
						'day' => '01'
					),
				),
			);
			$result = $this->Canton->conditionsDates(
				array(),
				$search,
				array(
					'Orientstruct.date_valid',
					'Dossier.dtdemrsa',
					'Personne.dtnai',
				)
			);
			$expected = array (
				'DATE( Orientstruct.date_valid ) BETWEEN \'2012-03-01\' AND \'2012-03-02\'',
				'DATE( Dossier.dtdemrsa ) BETWEEN \'2009-07-01\' AND \'2013-12-01\'',
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ConditionnableBehavior::conditionsDate().
		 */
		public function testConditionsDate() {
			// 1. Date correcte
			$search = array(
				'Personne' => array(
					'dtnai' => array(
						'year' => '1979',
						'month' => '01',
						'day' => '24'
					),
				)
			);

			$result = $this->Canton->conditionsDate(
				array(),
				$search,
				array(
					'Personne.dtnai'
				)
			);
			$expected = array (
				'DATE( Personne.dtnai ) = \'1979-01-24\'',
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Date incomplète
			$search = array(
				'Personne' => array(
					'dtnai' => array(
						'year' => '1979',
						'month' => '01',
						'day' => ''
					),
				)
			);

			$result = $this->Canton->conditionsDate(
				array(),
				$search,
				array(
					'Personne.dtnai'
				)
			);
			$expected = array ();
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}


		/**
		 * Test de la méthode ConditionnableBehavior::conditionsPersonneFoyerDossier().
		 */
		public function testConditionsPersonneFoyerDossier() {
			// 1. Filtre vide
			$search = array(
				'Calculdroitrsa' => array(
					'toppersdrodevorsa' => null,
				),
				'Canton' => array(
					'canton' => null
				),
				'Detailcalculdroitrsa' => array(
					'natpf' => null,
				),
				'Dossier' => array(
					'numdemrsa' => null,
					'matricule' => null,
					'dtdemrsa' => null,
					'fonorg' => null,
					'anciennete_dispositif' => null,
					'dernier' => null,
				),
				'Foyer' => array(
					'sitfam' => null,
					'ddsitfam' => array(
						'year' => null,
						'month' => null,
						'day' => null,
					),
				),
				'Personne' => array(
					'nom' => null,
					'prenom' => null,
					'nomnai' => null,
					'nir' => null,
					'sexe' => null,
					'dtnai' => array(
						'year' => null,
						'month' => null,
						'day' => null,
					),
					'trancheage' => null,
				),
				'Situationdossierrsa' => array(
					'etatdosrsa' => null,
				),
			);
			$result = $this->Canton->conditionsPersonneFoyerDossier( array(), $search );
			$expected = array();
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Filtre plein
			$search = array(
				'Calculdroitrsa' => array(
					'toppersdrodevorsa' => '1',
				),
				'Detailcalculdroitrsa' => array(
					'natpf' => 'RSD',
				),
				'Dossier' => array(
					'numdemrsa' => '01234567890',
					'matricule' => '987654321098765',
					'dtdemrsa' => 1,
					'dtdemrsa_from' => array(
						'year' => '2009',
						'month' => '06',
						'day' => '12',
					),
					'dtdemrsa_to' => array(
						'year' => '2010',
						'month' => '12',
						'day' => '24',
					),
					'fonorg' => 'CAF',
					'anciennete_dispositif' => '6_12',
					'dernier' => '1',
				),
				'Foyer' => array(
					'sitfam' => 'CEL',
					'ddsitfam' => array(
						'year' => '2010',
						'month' => '11',
						'day' => '04',
					),
				),
				'Personne' => array(
					'nom' => 'buffin',
					'prenom' => 'christian',
					'nomnai' => 'buffin',
					'nir' => '',
					'sexe' => '1',
					'dtnai' => array(
						'year' => '1979',
						'month' => '01',
						'day' => '24',
					),
					'trancheage' => '30_35',
				),
				'Situationdossierrsa' => array(
					'etatdosrsa' => array( 2, 3, 4 ),
				),
			);
			$result = $this->Canton->conditionsPersonneFoyerDossier( array(), $search );
			$expected = array(
				'Dossier.numdemrsa ILIKE \'%01234567890%\'',
				'Dossier.matricule ILIKE \'%987654321098765%\'',
				'Dossier.dtdemrsa BETWEEN \'2009-06-12\' AND \'2010-12-24\'',
				array( 'Dossier.fonorg' => 'CAF' ),
				'EXTRACT( YEAR FROM AGE( NOW(), "Dossier"."dtdemrsa" ) ) BETWEEN 6 AND 12',
				'UPPER(Personne.nom) LIKE \'BUFFIN\'',
				'UPPER(Personne.prenom) LIKE \'CHRISTIAN\'',
				'UPPER(Personne.nomnai) LIKE \'BUFFIN\'',
				'Personne.sexe' => '1',
				'DATE( Personne.dtnai ) = \'1979-01-24\'',
				'( EXTRACT ( YEAR FROM AGE( Personne.dtnai ) ) ) BETWEEN 30 AND 35',
				'Foyer.sitfam' => 'CEL',
				'Foyer.ddsitfam' => '2010-11-04',
				'( Situationdossierrsa.etatdosrsa IN ( \'2\', \'3\', \'4\' ) )',
				'Detaildroitrsa.id IN (
					SELECT detailscalculsdroitsrsa.detaildroitrsa_id
						FROM detailscalculsdroitsrsa
							INNER JOIN detailsdroitsrsa ON (
								detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id
							)
						WHERE
							detailsdroitsrsa.dossier_id = Dossier.id
							AND detailscalculsdroitsrsa.natpf = \'RSD\'
				)',
				array( 'Calculdroitrsa.toppersdrodevorsa' => '1' ),
			);

			$result = $this->_normalizeQueryPart( $result );
			$expected = $this->_normalizeQueryPart( $expected );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>