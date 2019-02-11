<?php
	/**
	 * Code source de la classe WebrsaRechercheDossierTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaRechercheDossier', 'Model' );
	App::uses('SuperFixture', 'SuperFixture.Utility');

	/**
	 * La classe WebrsaRechercheDossierTest réalise les tests unitaires de la classe WebrsaRechercheDossier.
	 *
	 * @package app.Test.Case.Model
	 */
	class WebrsaRechercheDossierTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array();

		/**
		 * Le modèle à tester.
		 *
		 * @var WebrsaRechercheDossier
		 */
		public $WebrsaRechercheDossier = null;
		
		public function setUp() {
			parent::setUp();
			Configure::write( 'CG.cantons', false );
			Configure::write( 'Canton.useAdresseCanton', false );
		}

		/**
		 * Préparation du test manuelle pour un département en particulier.
		 */
		public function setUpDepartement( $departement ) {
			Configure::write( 'Cg.departement', $departement );
			SuperFixture::load( $this, 'Allocataire' );
			$this->Dossier = ClassRegistry::init( 'Dossier' );
			$this->WebrsaRechercheDossier = ClassRegistry::init( 'WebrsaRechercheDossier' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->WebrsaRechercheDossier );
			parent::tearDown();
		}

		/**
		 * Test de la méthode WebrsaRechercheDossier::searchQuery()
		 *
		 * @covers WebrsaRechercheDossier::searchQuery
		 */
		public function testSearchQuery58() {
			$this->setUpDepartement( 58 );
			$query = $this->WebrsaRechercheDossier->searchQuery();
			$this->Dossier->forceVirtualFields = true;

			$query['fields'] = array(
				'Dossier.numdemrsa',
				'Dossier.dtdemrsa',
				'Personne.nir',
				'Situationdossierrsa.etatdosrsa',
				'Personne.nom_complet_prenoms',
				'Adresse.nomcom'
			);

			$result = $this->Dossier->find( 'all', $query );
			$expected = array(
				array(
					'Dossier' => array(
						'numdemrsa' => '00000010976',
						'dtdemrsa' => '2009-06-01'
					),
					'Personne' => array(
						'nir' => NULL,
						'nom_complet_prenoms' => 'MR BUFFIN CHRISTIAN MARIE JOSEPH'
					),
					'Situationdossierrsa' => array(
						'etatdosrsa' => '2'
					),
					'Adresse' => array(
						'nomcom' => 'DenisVille'
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode WebrsaRechercheDossier::searchConditions()
		 *
		 * @covers WebrsaRechercheDossier::searchConditions
		 */
		public function testSearchConditions58() {
			$this->setUpDepartement( 58 );

			$base = $this->WebrsaRechercheDossier->searchQuery();
			$base['fields'] = array(
				'Dossier.numdemrsa',
				'Dossier.dtdemrsa',
				'Personne.nir',
				'Situationdossierrsa.etatdosrsa',
				'Personne.nom_complet_prenoms',
				'Adresse.nomcom'
			);

			// 1. Ajout de la condition sur Dsp.natlog permettant de retrouver le dossier
			$query = $this->WebrsaRechercheDossier->searchConditions(
				$base,
				array(
					'Dsp' => array(
						'natlog' => '0902'
					)
				)
			);

			$this->Dossier->forceVirtualFields = true;
			$result = $this->Dossier->find( 'all', $query );
			$expected = array(
				array(
					'Dossier' => array(
						'numdemrsa' => '00000010976',
						'dtdemrsa' => '2009-06-01'
					),
					'Personne' => array(
						'nir' => NULL,
						'nom_complet_prenoms' => 'MR BUFFIN CHRISTIAN MARIE JOSEPH',
						'etat_dossier_orientation' => 'en_attente'
					),
					'Situationdossierrsa' => array(
						'etatdosrsa' => '2'
					),
					'Adresse' => array(
						'nomcom' => 'DenisVille'
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Ajout de la condition sur Dsp.natlog ne permettant pas de retrouver le dossier
			$query = $this->WebrsaRechercheDossier->searchConditions(
				$base,
				array(
					'Dsp' => array(
						'natlog' => '0903'
					)
				)
			);

			$this->Dossier->forceVirtualFields = true;
			$result = $this->Dossier->find( 'all', $query );
			$expected = array();
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. Ajout de la condition sur Propoorientationcov58.referentorientant_id permettant de retrouver le dossier
			$query = $this->WebrsaRechercheDossier->searchConditions(
				$base,
				array(
					'Propoorientationcov58' => array(
						'referentorientant_id' => '1'
					)
				)
			);

			$this->Dossier->forceVirtualFields = true;
			$result = $this->Dossier->find( 'all', $query );

			$expected = array(
				array(
					'Dossier' => array(
						'numdemrsa' => '00000010976',
						'dtdemrsa' => '2009-06-01'
					),
					'Personne' => array(
						'nir' => NULL,
						'nom_complet_prenoms' => 'MR BUFFIN CHRISTIAN MARIE JOSEPH',
						'etat_dossier_orientation' => 'en_attente'
					),
					'Situationdossierrsa' => array(
						'etatdosrsa' => '2'
					),
					'Adresse' => array(
						'nomcom' => 'DenisVille'
					)
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 4. Ajout de la condition sur Propoorientationcov58.referentorientant_id ne permettant pas de retrouver le dossier
			$query = $this->WebrsaRechercheDossier->searchConditions(
				$base,
				array(
					'Propoorientationcov58' => array(
						'referentorientant_id' => '2'
					)
				)
			);

			$this->Dossier->forceVirtualFields = true;
			$result = $this->Dossier->find( 'all', $query );
			$expected = array();
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
