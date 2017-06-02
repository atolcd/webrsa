<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$this->Csv->addRow(
		array(
			'N° dossier RSA',
			'Nom allocataire',
			'Prénom allocataire',
			'Date de naissance',
			'Commune de l\'allocataire',
			'Date de validation de l\'orientation',
			'Nb de jours depuis la fin du contrat lié',
			'Type d\'orientation',
			'Structure référente',
			'Référent',
			__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ),
			__d( 'search_plugin', 'Referentparcours.nom_complet' ),
		)
	);

	foreach( $results as $result ) {

		$row = array(
			Hash::get( $result, 'Dossier.numdemrsa' ),
			Hash::get( $result, 'Personne.nom'),
			Hash::get( $result, 'Personne.prenom'),
			date_short( Hash::get( $result, 'Personne.dtnai' ) ),
			Hash::get( $result, 'Adresse.nomcom' ),
			$this->Locale->date( 'Date::short', Hash::get( $result, 'Orientstruct.date_valid' ) ),
			Hash::get( $result, 'Contratinsertion.nbjours'),
			Hash::get( $result, 'Typeorient.lib_type_orient'),
			Hash::get( $result, 'Structurereferente.lib_struc'),
			Hash::get( $result, 'Referent.nom_complet'),
			Hash::get( $result, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $result, 'Referentparcours.nom_complet' ),
		);
		$this->Csv->addRow($row);
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'listes_demande_maintien_social'.date( 'Ymd-His' ).'.csv' );
?>