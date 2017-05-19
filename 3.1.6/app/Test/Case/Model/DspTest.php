<?php
	/**
	 * Code source de la classe DspTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Dsp', 'Model' );

	/**
	 * La classe DspTest réalise les tests unitaires de la classe Dsp.
	 *
	 * @package app.Test.Case.Model
	 */
	class DspTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Calculdroitrsa',
			'app.Dsp',
			'app.DspRev',
			'app.Memo',
			'app.Personne',
			'app.Detaildifsoc',
			'app.Detailaccosocfam',
			'app.Detailaccosocindi',
			'app.Detaildifdisp',
			'app.Detailnatmob',
			'app.Detaildiflog',
			'app.Detailmoytrans',
			'app.Detaildifsocpro',
			'app.Detailprojpro',
			'app.Detailfreinform',
			'app.Detailconfort',
			'app.DetaildifsocRev',
			'app.DetailaccosocfamRev',
			'app.DetailaccosocindiRev',
			'app.DetaildifdispRev',
			'app.DetailnatmobRev',
			'app.DetaildiflogRev',
			'app.DetailmoytransRev',
			'app.DetaildifsocproRev',
			'app.DetailprojproRev',
			'app.DetailfreinformRev',
			'app.DetailconfortRev',
			'app.Familleromev3',
			'app.Domaineromev3',
			'app.Metierromev3',
			'app.Appellationromev3',
			'app.Foyer',
			'app.Dossier',
			'app.Adressefoyer',
			'app.Adresse',
			'app.Appellationromev3',
			'app.Detaildroitrsa',
			'app.Domaineromev3',
			'app.Entreeromev3',
			'app.Familleromev3',
			'app.Fichiermodule',
			'app.Metierromev3',
			'app.Modecontact',
			'app.PersonneReferent',
			'app.Prestation',
			'app.Referent',
			'app.Situationdossierrsa',
			'app.Structurereferente',
		);

		/**
		 * Le modèle à tester.
		 *
		 * @var Dsp
		 */
		public $Dsp = null;

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			Configure::write( 'Cg.departement', 93 );
			Configure::write( 'CG.cantons', false );

			parent::setUp();
			$this->Dsp = ClassRegistry::init( 'Dsp' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
			unset( $this->Dsp );
			parent::tearDown();
		}

		/**
		 * Test de la méthode Dsp::updateDerniereDsp() avec une nouvelle version
		 * des DspRev.
		 *
		 * @medium
		 */
		public function testUpdateDerniereDspNouvelleDspRev() {
			$data = array(
				'Dsp' => array(
					'nivetu' => '1203',
					'natlog' => '0908'
				)
			);
			$result = $this->Dsp->WebrsaDsp->updateDerniereDsp( 1, $data );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// On s'assure qu'il n'y a qu'une création de DspRev
			$this->assertEqual( $this->Dsp->id, false, var_export( $this->Dsp->id, true ) );
			$this->assertEqual( $this->Dsp->DspRev->id, 2, var_export( $this->Dsp->DspRev->id, true ) );
		}

		/**
		 * Test de la méthode Dsp::updateDerniereDsp() avec la création d'une DspRev.
		 *
		 * @medium
		 */
		public function testUpdateDerniereDspCreationDspRev() {
			$data = array(
				'Dsp' => array(
					'nivetu' => '1203',
					'natlog' => '0908'
				)
			);
			$result = $this->Dsp->WebrsaDsp->updateDerniereDsp( 2, $data );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// On s'assure qu'il n'y a qu'une création de DspRev
			$this->assertEqual( $this->Dsp->id, false, var_export( $this->Dsp->id, true ) );
			$this->assertEqual( $this->Dsp->DspRev->id, 2, var_export( $this->Dsp->DspRev->id, true ) );
		}

		/**
		 * Test de la méthode Dsp::updateDerniereDsp() avec la création d'une Dsp.
		 *
		 * @medium
		 */
		public function testUpdateDerniereDspCreationDsp() {
			$data = array(
				'Dsp' => array(
					'nivetu' => '1203',
					'natlog' => '0908'
				)
			);
			$result = $this->Dsp->WebrsaDsp->updateDerniereDsp( 3, $data );
			$expected = true;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// On s'assure qu'il n'y a qu'une création de Dsp
			$this->assertEqual( $this->Dsp->id, 3, var_export( $this->Dsp->id, true ) );
			$this->assertEqual( $this->Dsp->DspRev->id, false, var_export( $this->Dsp->DspRev->id, true ) );
		}

		/**
		 * Test de la méthode Dsp::searchQuery().
		 */
		public function testSearchQuery() {
			Configure::write( 'Romev3.enabled', true );

			$result = $this->Dsp->WebrsaDsp->searchQuery();
			$result = Hash::combine( $result, 'joins.{n}.alias', 'joins.{n}.type' );

			$expected = array(
				'Calculdroitrsa' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Dossier' => 'INNER',
				'Memo' => 'LEFT OUTER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'INNER',
				'Adresse' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'DspRev' => 'LEFT OUTER',
				'Dsp' => 'LEFT OUTER',
				'Modecontact' => 'LEFT OUTER',
				'Deractromev3' => 'LEFT OUTER',
				'Deractromev3Rev' => 'LEFT OUTER',
				'Deractromev3__Familleromev3' => 'LEFT OUTER',
				'Deractromev3Rev__Familleromev3' => 'LEFT OUTER',
				'Deractromev3__Domaineromev3' => 'LEFT OUTER',
				'Deractromev3Rev__Domaineromev3' => 'LEFT OUTER',
				'Deractromev3__Metierromev3' => 'LEFT OUTER',
				'Deractromev3Rev__Metierromev3' => 'LEFT OUTER',
				'Deractromev3__Appellationromev3' => 'LEFT OUTER',
				'Deractromev3Rev__Appellationromev3' => 'LEFT OUTER',
				'Deractdomiromev3' => 'LEFT OUTER',
				'Deractdomiromev3Rev' => 'LEFT OUTER',
				'Deractdomiromev3__Familleromev3' => 'LEFT OUTER',
				'Deractdomiromev3Rev__Familleromev3' => 'LEFT OUTER',
				'Deractdomiromev3__Domaineromev3' => 'LEFT OUTER',
				'Deractdomiromev3Rev__Domaineromev3' => 'LEFT OUTER',
				'Deractdomiromev3__Metierromev3' => 'LEFT OUTER',
				'Deractdomiromev3Rev__Metierromev3' => 'LEFT OUTER',
				'Deractdomiromev3__Appellationromev3' => 'LEFT OUTER',
				'Deractdomiromev3Rev__Appellationromev3' => 'LEFT OUTER',
				'Actrechromev3' => 'LEFT OUTER',
				'Actrechromev3Rev' => 'LEFT OUTER',
				'Actrechromev3__Familleromev3' => 'LEFT OUTER',
				'Actrechromev3Rev__Familleromev3' => 'LEFT OUTER',
				'Actrechromev3__Domaineromev3' => 'LEFT OUTER',
				'Actrechromev3Rev__Domaineromev3' => 'LEFT OUTER',
				'Actrechromev3__Metierromev3' => 'LEFT OUTER',
				'Actrechromev3Rev__Metierromev3' => 'LEFT OUTER',
				'Actrechromev3__Appellationromev3' => 'LEFT OUTER',
				'Actrechromev3Rev__Appellationromev3' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
			);

			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
