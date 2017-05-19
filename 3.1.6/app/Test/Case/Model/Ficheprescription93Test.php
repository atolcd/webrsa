<?php
	/**
	 * Code source de la classe Ficheprescription93Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Ficheprescription93', 'Model' );

	/**
	 * La classe Ficheprescription93Test réalise les tests unitaires de la classe Ficheprescription93.
	 *
	 * @package app.Test.Case.Model
	 */
	class Ficheprescription93Test extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Actionfp93',
			'app.Adresse',
			'app.Adressefoyer',
			'app.Adresseprestatairefp93',
			'app.Calculdroitrsa',
			'app.Categoriefp93',
			'app.Cer93',
			'app.Cer93Sujetcer93',
			'app.Contratinsertion',
			'app.Detailcalculdroitrsa',
			'app.Detaildroitrsa',
			'app.Documentbeneffp93',
			'app.Documentbeneffp93Ficheprescription93',
			'app.Dsp',
			'app.DspRev',
			'app.Dossier',
			'app.Ficheprescription93',
			'app.Ficheprescription93Modtransmfp93',
			'app.Filierefp93',
			'app.Foyer',
			'app.Historiqueetatpe',
			'app.Informationpe',
			'app.Instantanedonneesfp93',
			'app.Modtransmfp93',
			'app.Motifnonintegrationfp93',
			'app.Motifnonreceptionfp93',
			'app.Motifnonretenuefp93',
			'app.Motifnonsouhaitfp93',
			'app.Personne',
			'app.PersonneReferent',
			'app.Prestatairefp93',
			'app.Prestatairehorspdifp93',
			'app.Prestation',
			'app.Referent',
			'app.Situationdossierrsa',
			'app.Structurereferente',
			'app.Sujetcer93',
			'app.Thematiquefp93',
			'app.User',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Ficheprescription93
		 */
		public $Ficheprescription93 = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			Configure::write( 'Cg.departement', 93 );
			Configure::write( 'CG.cantons', false );

			parent::setUp();

			// On mock la méthode ged()
			$this->Ficheprescription93 = $this->getMock(
				'Ficheprescription93',
				array( 'ged' ),
				array( array( 'ds' => 'test' ) )
			);
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Ficheprescription93 );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Ficheprescription93::search()
		 *
		 * @medium
		 */
		public function testSearch() {
			$result = $this->Ficheprescription93->search();
			$result = Hash::combine( $result, 'joins.{n}.alias', 'joins.{n}.type' );
			$expected = array(
				'Calculdroitrsa' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Prestation' => 'LEFT OUTER',
				'Adressefoyer' => 'INNER',
				'Dossier' => 'INNER',
				'Adresse' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Ficheprescription93' => 'LEFT OUTER',
				'Actionfp93' => 'LEFT OUTER',
				'Referent' => 'LEFT OUTER',
				'Filierefp93' => 'LEFT OUTER',
				'Prestatairefp93' => 'LEFT OUTER',
				'Categoriefp93' => 'LEFT OUTER',
				'Thematiquefp93' => 'LEFT OUTER',
				'Prestatairehorspdifp93' => 'LEFT OUTER',
				'Adresseprestatairefp93' => 'LEFT OUTER'
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Ficheprescription93::searchConditions()
		 */
		public function testSearchConditions() {
			$query = array(
				'conditions' => array( )
			);
			$search = array(
				'Actionfp93' => array(
					'numconvention' => '007'
				),
				'Ficheprescription93' => array(
					'exists' => '1',
					'typethematiquefp93_id' => 'pdi',
					'statut' => '03transmise_partenaire',
					'has_date_bilan_final' => '1',
				)
			);
			$result = $this->Ficheprescription93->searchConditions( $query, $search );
			$expected = array(
				'conditions' => array(
					'Ficheprescription93.id IS NOT NULL',
					'Thematiquefp93.type' => 'pdi',
					'UPPER( Actionfp93.numconvention ) LIKE' => '007%',
					'Ficheprescription93.statut' => '03transmise_partenaire',
					'Ficheprescription93.date_bilan_final IS NOT NULL',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Ficheprescription93::searchConditions()
		 */
		public function testSearchConditions2() {
			$query = array(
				'conditions' => array( )
			);
			$search = array(
				'Prestatairehorspdifp93' => array(
					'name' => 'maison'
				),
				'Ficheprescription93' => array(
					'exists' => '0',
					'actionfp93_id' => 1,
					'has_date_bilan_mi_parcours' => '0'
				)
			);
			$result = $this->Ficheprescription93->searchConditions( $query, $search );
			$expected = array(
				'conditions' => array(
					0 => 'Ficheprescription93.id IS NULL',
					'Actionfp93.id' => 1,
					'UPPER( Prestatairehorspdifp93.name ) LIKE' => '%MAISON%',
					1 => 'Ficheprescription93.date_bilan_mi_parcours IS NULL'
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Ficheprescription93::options()
		 *
		 * @medium
		 */
		public function testOptions() {
			// 1. find et autre
			$options = $this->Ficheprescription93->options( array( 'find' => true, 'autre' => true ) );
			$result = hash_keys( $options );
			sort( $result );

			$expected = array(
				'Dossier.fonorg',
				'Dossier.statudemrsa',
				'Dossier.fonorgcedmut',
				'Dossier.fonorgprenmut',
				'Dossier.anciennete_dispositif',
				'Dossier.numorg',
				'Dossier.typeparte',
				'Adresse.pays',
				'Adresse.typeres',
				'Adressefoyer.rgadr',
				'Adressefoyer.typeadr',
				'Calculdroitrsa.toppersdrodevorsa',
				'Detailcalculdroitrsa.natpf',
				'Detaildroitrsa.oridemrsa',
				'Detaildroitrsa.topfoydrodevorsa',
				'Detaildroitrsa.topsansdomfixe',
				'Foyer.haspiecejointe',
				'Foyer.sitfam',
				'Foyer.typeocclog',
				'Personne.pieecpres',
				'Personne.qual',
				'Personne.sexe',
				'Personne.typedtnai',
				'Prestation.rolepers',
				'Referentparcours.qual',
				'Structurereferenteparcours.type_voie',
				'Situationdossierrsa.etatdosrsa',
				'Situationdossierrsa.moticlorsa',
				'Ficheprescription93.statut',
				'Ficheprescription93.benef_retour_presente',
				'Ficheprescription93.personne_recue',
				'Ficheprescription93.personne_retenue',
				'Ficheprescription93.personne_souhaite_integrer',
				'Ficheprescription93.personne_a_integre',
				'Ficheprescription93.exists',
				'Ficheprescription93.typethematiquefp93_id',
				'Ficheprescription93.motifnonreceptionfp93_id',
				'Ficheprescription93.motifnonretenuefp93_id',
				'Ficheprescription93.motifnonsouhaitfp93_id',
				'Ficheprescription93.motifnonintegrationfp93_id',
				'Ficheprescription93.documentbeneffp93_id',
				'Actionfp93.actif',
				'Thematiquefp93.type',
				'Instantanedonneesfp93.benef_inscritpe',
				'Instantanedonneesfp93.benef_natpf_socle',
				'Instantanedonneesfp93.benef_natpf_majore',
				'Instantanedonneesfp93.benef_natpf_activite',
				'Instantanedonneesfp93.benef_nivetu',
				'Instantanedonneesfp93.benef_dip_ce',
				'Instantanedonneesfp93.benef_etatdosrsa',
				'Instantanedonneesfp93.benef_toppersdrodevorsa',
				'Instantanedonneesfp93.benef_positioncer',
				'Instantanedonneesfp93.benef_natpf',
				'Modtransmfp93.Modtransmfp93',
				'Documentbeneffp93.Documentbeneffp93',
				'Autre.Ficheprescription93',
				'Dossier.etatdosrsa',
				'Dossier.typeinsrmi',
				'Foyer.regagrifam',
			);
			sort( $expected );

			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. allocataire et pdf
			$options = $this->Ficheprescription93->options( array( 'allocataire' => true, 'pdf' => true ) );
			$result = hash_keys( $options );
			sort( $result );

			$expected = array(
				'Dossier.fonorg',
				'Dossier.statudemrsa',
				'Dossier.fonorgcedmut',
				'Dossier.fonorgprenmut',
				'Dossier.anciennete_dispositif',
				'Dossier.numorg',
				'Dossier.typeparte',
				'Adresse.pays',
				'Adresse.typeres',
				'Adressefoyer.rgadr',
				'Adressefoyer.typeadr',
				'Calculdroitrsa.toppersdrodevorsa',
				'Detailcalculdroitrsa.natpf',
				'Detaildroitrsa.oridemrsa',
				'Detaildroitrsa.topfoydrodevorsa',
				'Detaildroitrsa.topsansdomfixe',
				'Foyer.haspiecejointe',
				'Foyer.sitfam',
				'Foyer.typeocclog',
				'Personne.pieecpres',
				'Personne.qual',
				'Personne.sexe',
				'Personne.typedtnai',
				'Prestation.rolepers',
				'Referentparcours.qual',
				'Structurereferenteparcours.type_voie',
				'Situationdossierrsa.etatdosrsa',
				'Situationdossierrsa.moticlorsa',
				'Ficheprescription93.statut',
				'Ficheprescription93.benef_retour_presente',
				'Ficheprescription93.personne_recue',
				'Ficheprescription93.personne_retenue',
				'Ficheprescription93.personne_souhaite_integrer',
				'Ficheprescription93.personne_a_integre',
				'Ficheprescription93.exists',
				'Actionfp93.actif',
				'Thematiquefp93.type',
				'Instantanedonneesfp93.benef_inscritpe',
				'Instantanedonneesfp93.benef_natpf_socle',
				'Instantanedonneesfp93.benef_natpf_majore',
				'Instantanedonneesfp93.benef_natpf_activite',
				'Instantanedonneesfp93.benef_nivetu',
				'Instantanedonneesfp93.benef_dip_ce',
				'Instantanedonneesfp93.benef_etatdosrsa',
				'Instantanedonneesfp93.benef_toppersdrodevorsa',
				'Instantanedonneesfp93.benef_positioncer',
				'Instantanedonneesfp93.benef_natpf',
				'Instantanedonnees93.benef_qual',
				'Instantanedonnees93.structure_type_voie',
				'Referent.qual',
				'Type.voie',
				'Dossier.etatdosrsa',
				'Dossier.typeinsrmi',
				'Foyer.regagrifam',
			);
			sort( $expected );
			
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Ficheprescription93::prepareFormDataAddEdit(),
		 * partie création.
		 *
		 * @medium
		 */
		public function testPrepareFormDataAddEditAdd() {
			$result = $this->Ficheprescription93->prepareFormDataAddEdit( 1 );
			$expected = array(
				'Instantanedonneesfp93' => array(
					'benef_qual' => 'MR',
					'benef_nom' => 'BUFFIN',
					'benef_prenom' => 'CHRISTIAN',
					'benef_dtnai' => '1979-01-24',
					'benef_tel_fixe' => NULL,
					'benef_tel_port' => NULL,
					'benef_email' => NULL,
					'benef_numvoie' => '66',
					'benef_libtypevoie' => 'AVENUE',
					'benef_nomvoie' => 'DE LA REPUBLIQUE',
					'benef_complideadr' => NULL,
					'benef_compladr' => NULL,
					'benef_numcom' => '93001',
					'benef_codepos' => '93300',
					'benef_nomcom' => 'AUBERVILLIERS',
					'benef_matricule' => '123456700000000',
					'benef_natpf_activite' => '0',
					'benef_natpf_majore' => '0',
					'benef_natpf_socle' => '0',
					'benef_etatdosrsa' => '2',
					'benef_toppersdrodevorsa' => '1',
					'benef_dd_ci' => '2011-03-01',
					'benef_df_ci' => '2011-05-31',
					'benef_positioncer' => 'validationpdv',
					'benef_identifiantpe' => '0609065370Y',
					'benef_inscritpe' => '1',
					'benef_nivetu' => '1202',
					'benef_natpf' => 'NC',
					'benef_adresse' => '66 AVENUE DE LA REPUBLIQUE',
				),
				'Ficheprescription93' => array(
					'personne_id' => 1,
					'statut' => '01renseignee',
				),
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Ficheprescription93::prepareFormDataAddEdit(),
		 * partie modification
		 *
		 * @medium
		 */
		public function testPrepareFormDataAddEditEdit() {
			$result = $this->Ficheprescription93->prepareFormDataAddEdit( 1, 1 );
			$expected = array(
				'Ficheprescription93' => array(
					'id' => 1,
					'personne_id' => 1,
					'statut' => '01renseignee',
					'referent_id' => '1_1',
					'objet' => '',
					'rdvprestataire_date' => NULL,
					'rdvprestataire_personne' => '',
					'filierefp93_id' => 1,
					'actionfp93_id' => 1,
					'actionfp93' => NULL,
					'prestatairefp93_id' => 1,
					'adresseprestatairefp93_id' => 1,
					'prestatairehorspdifp93_id' => NULL,
					'rdvprestataire_adresse' => '',
					'dd_action' => NULL,
					'df_action' => NULL,
					'duree_action' => NULL,
					'documentbeneffp93_autre' => '',
					'date_signature' => NULL,
					'date_transmission' => NULL,
					'date_retour' => NULL,
					'benef_retour_presente' => NULL,
					'retour_nom_partenaire' => '',
					'date_signature_partenaire' => NULL,
					'personne_recue' => NULL,
					'motifnonreceptionfp93_id' => NULL,
					'personne_nonrecue_autre' => '',
					'personne_retenue' => NULL,
					'motifnonretenuefp93_id' => NULL,
					'personne_nonretenue_autre' => '',
					'personne_souhaite_integrer' => NULL,
					'motifnonsouhaitfp93_id' => NULL,
					'personne_nonsouhaite_autre' => '',
					'personne_a_integre' => NULL,
					'personne_date_integration' => NULL,
					'motifnonintegrationfp93_id' => NULL,
					'personne_nonintegre_autre' => '',
					'date_bilan_mi_parcours' => NULL,
					'date_bilan_final' => NULL,
					'motif_annulation' => '',
					'date_annulation' => NULL,
					'created' => NULL,
					'modified' => NULL,
					'actioncandidat_personne_id' => NULL,
					'structurereferente_id' => 1,
					'numconvention' => '93XXX1300001',
					'categoriefp93_id' => 1,
					'thematiquefp93_id' => 1,
					'typethematiquefp93_id' => 'pdi',
					'rdvprestataire_adresse_check' => false,
					'date_presente_benef' => null,
				),
				'Instantanedonneesfp93' => array(
					'id' => 1,
					'ficheprescription93_id' => 1,
					'referent_fonction' => 'Référent',
					'structure_name' => '« Projet de Ville RSA d\'Aubervilliers»',
					'structure_num_voie' => '117',
					'structure_type_voie' => 'R',
					'structure_nom_voie' => 'Andre Karman',
					'structure_code_postal' => '93300',
					'structure_ville' => 'Aubervilliers',
					'structure_tel' => NULL,
					'structure_fax' => NULL,
					'referent_email' => NULL,
					'benef_qual' => NULL,
					'benef_nom' => NULL,
					'benef_prenom' => NULL,
					'benef_dtnai' => NULL,
					'benef_numvoie' => NULL,
					'benef_libtypevoie' => NULL,
					'benef_nomvoie' => NULL,
					'benef_complideadr' => NULL,
					'benef_compladr' => NULL,
					'benef_numcom' => NULL,
					'benef_codepos' => NULL,
					'benef_nomcom' => NULL,
					'benef_tel_fixe' => NULL,
					'benef_tel_port' => NULL,
					'benef_email' => NULL,
					'benef_identifiantpe' => NULL,
					'benef_inscritpe' => NULL,
					'benef_matricule' => NULL,
					'benef_natpf_socle' => NULL,
					'benef_natpf_majore' => NULL,
					'benef_natpf_activite' => NULL,
					'benef_natpf_3mois' => NULL,
					'benef_nivetu' => NULL,
					'benef_dernier_dip' => NULL,
					'benef_dip_ce' => NULL,
					'benef_etatdosrsa' => NULL,
					'benef_toppersdrodevorsa' => NULL,
					'benef_dd_ci' => NULL,
					'benef_df_ci' => NULL,
					'benef_positioncer' => NULL,
					'created' => NULL,
					'modified' => NULL,
					'benef_natpf' => 'NC',
					'benef_adresse' => '  ',
				),
				'Prestatairehorspdifp93' => array(
					'id' => NULL,
					'name' => NULL,
					'adresse' => NULL,
					'codepos' => NULL,
					'localite' => NULL,
					'tel' => NULL,
					'fax' => NULL,
					'email' => NULL,
					'created' => NULL,
					'modified' => NULL
				),
				'Referent' => array(
					'structurereferente_id' => 1,
				),
				'Actionfp93' => array(
					'numconvention' => '93XXX1300001',
					'filierefp93_id' => 1,
					'adresseprestatairefp93_id' => 1
				),
				'Adresseprestatairefp93' => array(
					'prestatairefp93_id' => 1,
				),
				'Filierefp93' => array(
					'categoriefp93_id' => 1,
				),
				'Categoriefp93' => array(
					'thematiquefp93_id' => 1,
				),
				'Thematiquefp93' => array(
					'type' => 'pdi',
				),
				'Modtransmfp93' => array(
					'Modtransmfp93' => array( ),
				),
				'Documentbeneffp93' => array(
					'Documentbeneffp93' => array( ),
				),
			);

			$unset = array(
				'Ficheprescription93.actioncandidat_personne_id',
				'Prestatairehorspdifp93.actioncandidat_personne_id'
			);
			foreach( $unset as $path ) {
				list( $modelName, $fieldName ) = explode( '.', $path );

				unset( $result[$modelName][$fieldName] );
				unset( $expected[$modelName][$fieldName] );
			}

			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Ficheprescription93::saveAddEdit() dans le cas d'un
		 * ajout.
		 *
		 * @medium
		 */
		public function testSaveAddEditAdd() {
			$data = array(
				'Ficheprescription93' => array(
					'personne_id' => 1,
					'referent_id' => 1,
					'typethematiquefp93_id' => 'pdi',
					'filierefp93_id' => 1,
					'actionfp93_id' => 1,
					'prestatairefp93_id' => 1,
					'prestatairehorspdifp93_id' => null,
					'objet' => 'Test'
				)
			);
			$result = $this->Ficheprescription93->saveAddEdit( $data );
			$this->assertTrue( $result );
		}

		/**
		 * Test de la méthode Ficheprescription93::saveAddEdit(), dans le cas
		 * d'une modification.
		 *
		 * @medium
		 */
		public function testSaveAddEditEdit() {
			$data = array(
				'Ficheprescription93' => array(
					'id' => 1,
					'typethematiquefp93_id' => 'pdi',
					'actionfp93_id' => 1,
					'instantanedonneesfp93_id' => 1,
					'prestatairefp93_id' => 1,
					'prestatairehorspdifp93_id' => null,
					'objet' => 'Test'
				)
			);
			$result = $this->Ficheprescription93->saveAddEdit( $data );
			$this->assertTrue( $result );
		}

		/**
		 * Test de la méthode Ficheprescription93::messages()
		 */
		public function testMessages() {
			// 1. Pas de message
			$result = $this->Ficheprescription93->messages( 1 );
			$expected = array( );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. Avec des notice
			$result = $this->Ficheprescription93->messages( 2 );
			$expected = array(
				'Instantanedonneesfp93.benef_toppersdrodevorsa_notice' => 'notice',
				'Instantanedonneesfp93.benef_etatdosrsa_ouverts' => 'notice',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Ficheprescription93::getDataForPdf()
		 *
		 * @medium
		 */
		public function testGetDataForPdf() {
			$data = $this->Ficheprescription93->getDataForPdf( 1, 1 );
			$result = hash_keys( $data );

			$expected = array(
				'0.Ficheprescription93',
				'0.Actionfp93',
				'0.Adresseprestatairefp93',
				'0.Filierefp93',
				'0.Instantanedonneesfp93',
				'0.Motifnonintegrationfp93',
				'0.Motifnonreceptionfp93',
				'0.Motifnonretenuefp93',
				'0.Motifnonsouhaitfp93',
				'0.Personne',
				'0.Referent',
				'0.Prestatairefp93',
				'0.Prestatairehorspdifp93',
				'0.Categoriefp93',
				'0.Thematiquefp93',
				'0.Structurereferente',
				'0.User',
				'documentbeneffp93',
				'modtransmfp93',
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode Ficheprescription93::getDataForPdf()
		 *
		 * @medium
		 */
		public function testGetDefaultPdf() {
			$this->Ficheprescription93
					->expects( $this->once() )
					->method( 'ged' )
					->will( $this->returnValue( true ) );
			$this->Ficheprescription93->getDefaultPdf( 1, 1 );
		}
	}
?>
