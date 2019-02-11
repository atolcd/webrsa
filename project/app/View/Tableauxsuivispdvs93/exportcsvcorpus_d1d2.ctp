<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$type = Hash::get( $search, 'Search.type' );

	// 1. Ligne d'en-tetes
	$row = array(
		// Suivi
		__d( 'cohortesd2pdvs93', 'Rendezvous.daterdv' ),
		__d( 'cohortesd2pdvs93', 'Questionnaired2pdv93.date_validation' ),
		__d( 'cohortesd2pdvs93', 'Structurereferente.lib_struc' ),
		__d( 'cohortesd2pdvs93', 'Referent.nom_complet' ),
		// Allocataire
		__d( 'questionnairesd1pdvs93', 'Situationallocataire.nom' ),
		__d( 'questionnairesd1pdvs93', 'Situationallocataire.prenom' ),
		__d( 'questionnairesd1pdvs93', 'Situationallocataire.dtnai' ),
		__d( 'questionnairesd1pdvs93', 'Situationallocataire.sexe' ),
		__d( 'questionnairesd1pdvs93', 'Situationallocataire.rolepers' ),
		__d( 'questionnairesd1pdvs93', 'Situationallocataire.codepos' ),
		__d( 'questionnairesd1pdvs93', 'Situationallocataire.nomcom' ),
		__d( 'questionnairesd1pdvs93', 'Situationallocataire.sitfam' ),
		__d( 'questionnairesd1pdvs93', 'Situationallocataire.matricule' ),
		// Détails du suivi, D1
		__d( 'questionnairesd1pdvs93', 'Questionnaired1pdv93.inscritpe' ),
		__d( 'questionnairesd1pdvs93', 'Questionnaired1pdv93.marche_travail' ),
		__d( 'questionnairesd1pdvs93', 'Questionnaired1pdv93.vulnerable' ),
		__d( 'questionnairesd1pdvs93', 'Questionnaired1pdv93.diplomes_etrangers' ),
		__d( 'questionnairesd1pdvs93', 'Questionnaired1pdv93.categorie_sociopro' ),
		__d( 'questionnairesd1pdvs93', 'Questionnaired1pdv93.nivetu' ),
		'Non scolarisé ?',
		__d( 'questionnairesd1pdvs93', 'Questionnaired1pdv93.autre_caracteristique' ),
		__d( 'questionnairesd1pdvs93', 'Questionnaired1pdv93.autre_caracteristique_autre' ),
		__d( 'questionnairesd1pdvs93', 'Questionnaired1pdv93.conditions_logement' ),
		__d( 'questionnairesd1pdvs93', 'Questionnaired1pdv93.conditions_logement_autre' ),
		// Détails du suivi, D2
		__d( 'questionnairesd2pdvs93', 'Questionnaired2pdv93.situationaccompagnement' ),
		__d( 'questionnairesd2pdvs93', 'Sortieaccompagnementd2pdv93.name' ),
		__d( 'questionnairesd2pdvs93', 'Questionnaired2pdv93.chgmentsituationadmin' ),
	);

	if( 'communaute' === $type ) {
		$row[] = 'Déménagement interne';
	}

	$this->Csv->addRow( $row );

	// 2. Résultats
	if( !empty( $results ) ) {
		foreach( $results as $result ) {
			// TODO: traductions
			$row = array(
				// Suivi
				$this->Locale->date( __( 'Date::short' ), Hash::get( $result, 'Rendezvous.daterdv' ) ),
				$this->Locale->date( __( 'Date::short' ), Hash::get( $result, 'Questionnaired2pdv93.date_validation' ) ),
				Hash::get( $result, 'Structurereferente.lib_struc' ),
				Hash::get( $result, 'Referent.nom_complet' ),
				// Allocataire
				Hash::get( $result, 'Situationallocataire.nom' ),
				Hash::get( $result, 'Situationallocataire.prenom' ),
				$this->Locale->date( __( 'Date::short' ), Hash::get( $result, 'Situationallocataire.dtnai' ) ),
				value( $options['Situationallocataire']['sexe'], Hash::get( $result, 'Situationallocataire.sexe' ) ),
				value( $options['Situationallocataire']['rolepers'], Hash::get( $result, 'Situationallocataire.rolepers' ) ),
				Hash::get( $result, 'Situationallocataire.codepos' ),
				Hash::get( $result, 'Situationallocataire.nomcom' ),
				value( $options['Situationallocataire']['sitfam'], Hash::get( $result, 'Situationallocataire.sitfam' ) ),
				'='.Hash::get( $result, 'Situationallocataire.matricule' ),
				// Détails du suivi, D1
				value( $options['Questionnaired1pdv93']['inscritpe'], Hash::get( $result, 'Questionnaired1pdv93.inscritpe' ) ),
				value( $options['Questionnaired1pdv93']['marche_travail'], Hash::get( $result, 'Questionnaired1pdv93.marche_travail' ) ),
				value( $options['Questionnaired1pdv93']['vulnerable'], Hash::get( $result, 'Questionnaired1pdv93.vulnerable' ) ),
				value( $options['Questionnaired1pdv93']['diplomes_etrangers'], Hash::get( $result, 'Questionnaired1pdv93.diplomes_etrangers' ) ),
				value( $options['Questionnaired1pdv93']['categorie_sociopro'], Hash::get( $result, 'Questionnaired1pdv93.categorie_sociopro' ) ),
				value( $options['Questionnaired1pdv93']['nivetu'], Hash::get( $result, 'Questionnaired1pdv93.nivetu' ) ),
				( Hash::get( $result, 'Questionnaired1pdv93.nivetu' ) === '1207' ? 'Non scolarisé' : null ),
				value( $options['Questionnaired1pdv93']['autre_caracteristique'], Hash::get( $result, 'Questionnaired1pdv93.autre_caracteristique' ) ),
				Hash::get( $result, 'Questionnaired1pdv93.autre_caracteristique_autre' ),
				value( $options['Questionnaired1pdv93']['conditions_logement'], Hash::get( $result, 'Questionnaired1pdv93.conditions_logement' ) ),
				Hash::get( $result, 'Questionnaired1pdv93.conditions_logement_autre' ),
				// Détails du suivi, D2
				value( $options['Questionnaired2pdv93']['situationaccompagnement'], Hash::get( $result, 'Questionnaired2pdv93.situationaccompagnement' ) ),
				Hash::get( $result, 'Sortieaccompagnementd2pdv93.name' ),
				value( $options['Questionnaired2pdv93']['chgmentsituationadmin'], Hash::get( $result, 'Questionnaired2pdv93.chgmentsituationadmin' ) ),
			);

			if( 'communaute' === $type ) {
				$row[] = ( Hash::get( $result, 'Demenagement.interne' ) ? 'Oui' : 'Non' );
			}

			$this->Csv->addRow( $row );
		}
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( $csvfile );
?>