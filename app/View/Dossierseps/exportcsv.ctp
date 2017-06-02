<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$this->Csv->addRow(
		array( // FIXME: traductions
			'Thématique',
			'N° de dossier',
			'Personne.qual',
			'Personne.nom',
			'Personne.prenom',
			'Personne.dtnai',
			'N° allocataire',
			'Adresse.nomcom',
			'Dossierep.created',
			'Proposition validée en COV le',
			'Structure référente',
		)
	);

	foreach( $themesChoose as $theme ){
		foreach( $dossiers[$theme] as $dossier ) {
// debug( $dossier );
			$row = array(
				__d( 'dossierep',  'ENUM::THEMEEP::'.Inflector::tableize( $theme ) ),
				$this->Type2->format( $dossier, 'Dossierep.id', array() ),
				$this->Type2->format( $dossier, 'Personne.qual', array() ),
				$this->Type2->format( $dossier, 'Personne.nom', array() ),
				$this->Type2->format( $dossier, 'Personne.prenom', array() ),
				$this->Type2->format( $dossier, 'Personne.dtnai', array() ),
				$this->Type2->format( $dossier, 'Dossier.matricule', array() ),
				$this->Type2->format( $dossier, 'Adresse.nomcom', array() ),
				$this->Type2->format( $dossier, 'StatutrdvTyperdv.motifpassageep', array() ),
				$this->Type2->format( $dossier, 'Dossierep.created', array() ),
				$this->Type2->format( $dossier, 'Structurereferente.lib_struc', array() )
			);
			$this->Csv->addRow($row);
		}

		// __d( 'dossierep',  'ENUM::THEMEEP::'.Inflector::tableize( $theme ) )
		/*echo $this->Default2->index(
			$dossiers[$theme],
			array(
	// 			'Foyer.enerreur' => array( 'type' => 'string', 'class' => 'foyer_enerreur' ),
				'Passagecommissionep.chosen' => array( 'input' => 'checkbox' ),
			),
			array(
				'cohorte' => true,
				'options' => $options,
				'hidden' => array( 'Dossierep.id', 'Passagecommissionep.id' ),
				'paginate' => Inflector::classify( $theme ),
				'actions' => array( 'Personnes::view' ),
				'id' => $theme,
				'labelcohorte' => 'Enregistrer',
				'cohortehidden' => array( 'Choose.theme' => array( 'value' => $theme ) ),
				'trClass' => $trClass,
			)
		);*/
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'liste_des_dossiers_selectionnables_en_commission_ep_'.$commissionep_id.'_'.date( 'Ymd-His' ).'.csv' );
?>