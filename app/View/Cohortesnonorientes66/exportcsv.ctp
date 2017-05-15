<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$this->Csv->addRow(
		array(
			'N° Caf (Matricule)',
			'NIR',
			'Genre',
			'Nom',
			'Prénom',
			'Date de naissance',
			'Nombre d\'enfants',
			'Ville',
			'Canton',
			'MSP',
			'Date de demande',
			'Date d\'envoi du courrier avec questionnaire',
			'Réponse ?',
			'Date de l\'orientation',
			'Orientation',
			'Structure référente',
			'Etat Pôle Emploi',
			'Date de l\'état Pôle Emploi',
			'Fichier lié ?',
			__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ),
			__d( 'search_plugin', 'Referentparcours.nom_complet' ),
		)
	);

	foreach( $nonorientes66 as $nonoriente66 ) {
		$reponseallocataire = Set::classicExtract( $nonoriente66, 'Nonoriente66.reponseallocataire' );
		$etatHistoriqueetatpe = Set::enum( $nonoriente66['Historiqueetatpe']['etat'], $historiqueetatpe['etat'] );
		$structureLieeAdresse = Set::enum( $nonoriente66['Canton']['structureliee'], $listestructures );

		$row = array(
			Set::classicExtract( $nonoriente66, 'Dossier.matricule' ),
			Set::classicExtract( $nonoriente66, 'Personne.nir' ),
			Set::classicExtract( $nonoriente66, 'Personne.qual' ),
			Set::classicExtract( $nonoriente66, 'Personne.nom' ),
			Set::classicExtract( $nonoriente66, 'Personne.prenom' ),
			date_short( Set::classicExtract( $nonoriente66, 'Personne.dtnai' ) ),
			$nonoriente66['Foyer']['nbenfants'],
			Set::classicExtract( $nonoriente66, 'Adresse.nomcom' ),
			Set::classicExtract( $nonoriente66, 'Canton.canton' ),
			$structureLieeAdresse, // MSP
			date_short( Set::classicExtract( $nonoriente66, 'Dossier.dtdemrsa' ) ),
			date_short( Set::classicExtract( $nonoriente66, 'Nonoriente66.dateimpression' ) ),
			( !empty( $reponseallocataire ) ? 'Oui' : 'Non' ),
			date_short( Set::classicExtract( $nonoriente66, 'Orientstruct.date_valid' ) ),
			Set::classicExtract( $nonoriente66, 'Typeorient.lib_type_orient' ),
			Set::classicExtract( $nonoriente66, 'Structurereferente.lib_struc' ),
			$etatHistoriqueetatpe,
			Set::classicExtract( $nonoriente66, 'Historiqueetatpe.date' ),
			!empty( $nonoriente66['Nonoriente66']['nbfichiers'] ) ? 'Oui' : 'Non',
			Hash::get( $nonoriente66, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $nonoriente66, 'Referentparcours.nom_complet' ),
		);
		$this->Csv->addRow($row);
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'orientes_notifies-'.date( 'Ymd-His' ).'.csv' );
?>