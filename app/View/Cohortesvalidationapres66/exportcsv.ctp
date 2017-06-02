<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$this->Csv->addRow(
		array(
			'N° Demande APRE/ADRE',
			'Nom/Prénom allocataire',
			'Commune de l\'allocataire',
			'Date de demande APRE/ADRE',
			'Etat du dossier',
			'Décision',
			'Montant accordé',
			'Motif du rejet',
			'Date de la décision',
			__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ),
			__d( 'search_plugin', 'Referentparcours.nom_complet' ),
		)
	);

	foreach( $apres as $apre ) {
		$row = array(
			Set::classicExtract( $apre, 'Apre66.numeroapre' ),
			Set::classicExtract( $apre, 'Personne.nom_complet' ),
			Set::classicExtract( $apre, 'Adresse.nomcom' ),
			$this->Locale->date( 'Date::short', Set::classicExtract( $apre, 'Aideapre66.datedemande' ) ),
			Set::enum( Set::classicExtract( $apre, 'Apre66.etatdossierapre' ), $options['etatdossierapre'] ),
			Set::enum( Set::classicExtract( $apre, 'Aideapre66.decisionapre' ), $optionsaideapre66['decisionapre'] ),
			Set::classicExtract( $apre, 'Aideapre66.montantaccorde' ),
			Set::classicExtract( $apre, 'Aideapre66.motifrejetequipe' ),
			$this->Locale->date( 'Date::short', Set::classicExtract( $apre, 'Aideapre66.datemontantaccorde' ) ),
			Hash::get( $apre, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $apre, 'Referentparcours.nom_complet' ),
		);
		$this->Csv->addRow($row);
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'apres_valides-'.date( 'Ymd-His' ).'.csv' );
?>