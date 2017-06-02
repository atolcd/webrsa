<?php
	/**
	 * Code source de la classe WebrsaRecherchesDossiersComponentTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'WebrsaRecherchesDossiersComponent', 'Controller/Component' );

	/**
	 * WebrsaRecherchesDossiersTestsController class
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class WebrsaRecherchesDossiersTestsController extends AppController
	{
		/**
		 * name property
		 *
		 * @var string
		 */
		public $name = 'WebrsaRecherchesDossiersTestsController';

		/**
		 * uses property
		 *
		 * @var mixed null
		 */
		public $uses = array( 'Dossier', 'WebrsaRecherche', 'User' );

		/**
		 * components property
		 *
		 * @var array
		 */
		public $components = array(
			'WebrsaRecherchesDossiers',
			'Jetons2'
		);
	}

	/**
	 * La classe WebrsaRecherchesDossiersComponentTest ...
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class WebrsaRecherchesDossiersComponentTest extends CakeTestCase
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
			'app.Detaildroitrsa',
			'app.Dossier',
			'app.Dsp',
			'app.DspRev',
			'app.Jeton',
			'app.Foyer',
			'app.Orientstruct',
			'app.Personne',
			'app.PersonneReferent',
			'app.Prestation',
			'app.Referent',
			'app.Serviceinstructeur',
			'app.Situationdossierrsa',
			'app.Structurereferente',
			'app.Typeorient',
			'app.User',
			'app.Zonegeographique',
		);

		/**
		 * Controller property
		 *
		 * @var WebrsaRecherchesDossiersComponent
		 */
		public $Controller;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();

			Configure::write( 'Cg.departement', 976 );
			Configure::delete( 'ConfigurableQuery.Dossiers.search' );
			Configure::delete( 'Module.Savesearch.enabled' );
			Configure::write( 'CG.cantons', false );
			Configure::write( 'Canton.useAdresseCanton', false );
			
			$Request = new CakeRequest( 'dossiers/search', false );
			$Request->addParams(array( 'controller' => 'dossiers', 'action' => 'search' ) );

			$this->Controller = new WebrsaRecherchesDossiersTestsController( $Request );
			$this->Controller->Components->init( $this->Controller );
			$this->Controller->Jetons2->initialize( $this->Controller );
			$this->Controller->WebrsaRecherchesDossiers->initialize( $this->Controller );
		}

		/**
		 * Test de la méthode WebrsaRecherchesDossiersComponent::method() lorsque
		 * le formulaire de recherche est affiché.
		 */
		public function testSearchForm() {
			$config = $this->Controller->WebrsaRecherche->searches['Dossiers.search'];
			$this->Controller->WebrsaRecherchesDossiers->search( $config );
			$result = Hash::get( $this->Controller->viewVars, 'options.PersonneReferent' );
			$expected = array(
				'structurereferente_id' => array(
					'Socioprofessionnelle' => array(
						1 => '« Projet de Ville RSA d\'Aubervilliers»',
					),
				),
				'referent_id' => array(
					'1_1' => 'MR Dupont Martin',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode WebrsaRecherchesDossiersComponent::method() lorsque
		 * les résultats de la recherche sont envoyés à la vue.
		 */
		public function testSearch() {
			Configure::write(
				'ConfigurableQuery.Dossiers.search',
				array(
					'query' => array(
						'order' => array( 'Personne.nom' )
					),
					'limit' => 10,
					'results' => array(
						'fields' => array(
							'Dossier.numdemrsa',
							'Dossier.dtdemrsa',
							'Personne.nir',
							'Situationdossierrsa.etatdosrsa',
							'Personne.nom_complet_prenoms',
							'Adresse.nomcom',
							'Dossier.locked' => array(
								'type' => 'boolean',
								'class' => 'dossier_locked'
							),
							'/Dossiers/view/#Dossier.id#'
						),
						'innerTable' => array(
							'Dossier.matricule',
							'Personne.dtnai',
							'Adresse.numcom' => array(
								'options' => array()
							),
							'Prestation.rolepers',
							'Structurereferenteparcours.lib_struc',
							'Referentparcours.nom_complet'
						)
					)
				)
			);

			$config = $this->Controller->WebrsaRecherche->searches['Dossiers.search'];
			$this->Controller->request->data = array( 'Search' => array( 'active' => 1 ) );
			$this->Controller->WebrsaRecherchesDossiers->search( $config );
			$result = Hash::get( $this->Controller->viewVars, 'results' );
			$expected = array(
				0 => array(
					'Dossier' => array(
						'id' => 1,
						'numdemrsa' => '66666666693',
						'dtdemrsa' => '2009-09-01',
						'matricule' => '123456700000000',
						'locked' => false,
					),
					'Personne' => array(
						'dtnai' => '1979-01-24',
						'nir' => NULL,
						'nom_complet_prenoms' => 'MR BUFFIN CHRISTIAN  ',
					),
					'Prestation' => array(
						'rolepers' => 'DEM',
					),
					'Adresse' => array(
						'numcom' => '93001',
						'nomcom' => 'AUBERVILLIERS',
					),
					'Situationdossierrsa' => array(
						'etatdosrsa' => '2',
					),
					'Referentparcours' => array(
						'nom_complet' => NULL,
					),
					'Structurereferenteparcours' => array(
						'lib_struc' => NULL,
					),
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>