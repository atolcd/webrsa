<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

	$this->Csv->addRow(
		array(
			'N° Dossier',
			'Nom/Prénom allocataire',
			'Commune de l\'allocataire',
			'Date de demande d\'APRE',
			'Eligibilité',
			'Etat du dossier APRE',
			'Date de relance',
			'Date du comité examen',
			__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ),
			__d( $domain_search_plugin, 'Referentparcours.nom_complet' ),
		)
	);

	foreach( $apres as $apre ) {

		$aidesApre = array();
		$naturesaide = Set::classicExtract( $apre, 'Apre.Natureaide' );
		foreach( $naturesaide as $natureaide => $nombre ) {
			if( $nombre > 0 ) {
				$aidesApre[] = Set::classicExtract( $natureAidesApres, $natureaide );
			}
		}

		$row = array(
			Set::classicExtract( $apre, 'Dossier.numdemrsa' ),
			Set::classicExtract( $apre, 'Personne.nom' ).' '.Set::classicExtract( $apre, 'Personne.prenom'),
			Set::classicExtract( $apre, 'Adresse.nomcom' ),
			$this->Locale->date( 'Date::short', Set::classicExtract( $apre, 'Apre.datedemandeapre' ) ),
			Set::enum( Set::classicExtract( $apre, 'Apre.eligibiliteapre' ), $options['eligibiliteapre'] ),
			Set::enum( Set::classicExtract( $apre, 'Apre.etatdossierapre' ), $options['etatdossierapre'] ),
			$this->Locale->date( 'Date::short', Set::classicExtract( $apre, 'Relanceapre.daterelance' ) ),
			$this->Locale->date( 'Date::short', Set::classicExtract( $apre, 'Comiteapre.datecomite' ) ),
			Hash::get( $apre, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $apre, 'Referentparcours.nom_complet' ),
		);

		$this->Csv->addRow($row);
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'apres-'.date( 'Ymd-His' ).'.csv' );
?>