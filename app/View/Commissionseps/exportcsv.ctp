<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$this->Csv->addRow(
		array(
			'Civilité',
			'Nom',
			'Prénom',
			__d( 'dossier', 'Dossier.matricule' ),
			'Personne',
			'Date de naissance',
			'Commune',
			'Structure chargée de l\'évaluation',
			'Type de structure',
			'Date de création du dossier',
			'Thème du dossier',
			'État du dossier d\'EP',
			'Proposition validée par la COV le',
			'Alerte composition du foyer ?',
		)
	);

	foreach( $dossierseps as $dossierep ) {

		$row = array(
			Set::classicExtract( $dossierep, 'Personne.qual' ),
			Set::classicExtract( $dossierep, 'Personne.nom' ),
			Set::classicExtract( $dossierep, 'Personne.prenom' ),
			Set::classicExtract( $dossierep, 'Dossier.matricule' ),
			Set::classicExtract( $dossierep, 'Personne.id' ),
			$this->Locale->date( 'Date::short', Set::classicExtract( $dossierep, 'Personne.dtnai' ) ),
			Set::classicExtract( $dossierep, 'Adresse.nomcom' ),
			Set::classicExtract( $dossierep, 'Structureorientante.lib_struc' ),
			Set::classicExtract( $dossierep, 'Structurereferente.lib_struc' ),
			$this->Locale->date( 'Datetime::short', Set::classicExtract( $dossierep, 'Dossierep.created') ),
			Set::enum( Set::classicExtract( $dossierep, 'Dossierep.themeep'), $options['Dossierep']['themeep'] ),
			Set::enum( Set::classicExtract( $dossierep, 'Passagecommissionep.etatdossierep'), $options['Passagecommissionep']['etatdossierep'] ),
			$this->Locale->date( 'Date::short', Set::classicExtract( $dossierep, 'Cov58.datecommission') ),
			Set::classicExtract( $dossierep, 'Foyer.enerreur' ),
		);
		$this->Csv->addRow($row);
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'listes_demande_maintien_social'.date( 'Ymd-Hhm' ).'.csv' );
?>