<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	if( $nameTableauCsv == 'noninscrits' ){

		$this->Csv->addRow(
			array(
				'Nom allocataire',
				'Prénom allocataire',
				'Date de naissance',
				'Commune de l\'allocataire',
				'Type d\'orientation',
				'Type de structure',
				'Date d\'orientation',
				'Service instructeur',
				__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ),
				__d( 'search_plugin', 'Referentparcours.nom_complet' ),
			)
		);

		foreach( $personnes as $personne ) {

			$row = array(
				Set::classicExtract( $personne, 'Personne.nom' ),
				Set::classicExtract( $personne, 'Personne.prenom'),
				date_short( Set::classicExtract( $personne, 'Personne.dtnai' ) ),
				Set::classicExtract( $personne, 'Adresse.nomcom' ),
				Set::classicExtract( $personne, 'Typeorient.lib_type_orient' ),
				Set::classicExtract( $personne, 'Structurereferente.lib_struc' ),
				$this->Locale->date( 'Date::short', Set::classicExtract( $personne, 'Orientstruct.date_valid' ) ),
				Set::classicExtract( $personne, 'Serviceinstructeur.lib_service' ),
				Hash::get( $personne, 'Structurereferenteparcours.lib_struc' ),
				Hash::get( $personne, 'Referentparcours.nom_complet' ),

			);
			$this->Csv->addRow($row);
		}
	}
	else if( $nameTableauCsv == 'radies' ) {
		$configureConditions = Configure::read( 'Selectionradies.conditions' );

		if( !empty( $configureConditions ) ) {
			$this->Csv->addRow(
				array(
					'Nom allocataire',
					'Prénom allocataire',
					'Date de naissance',
					'Commune de l\'allocataire',
					__d( 'sanctionep58', 'Historiqueetatpe.etat', true ),
					__d( 'sanctionep58', 'Historiqueetatpe.code' ),
					'Motif de radiation Pôle Emploi',
					'Date de radiation Pôle Emploi',
					'Service instructeur',
					__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ),
					__d( 'search_plugin', 'Referentparcours.nom_complet' ),
				)
			);

			foreach( $personnes as $personne ) {
				$row = array(
					Set::classicExtract( $personne, 'Personne.nom' ),
					Set::classicExtract( $personne, 'Personne.prenom'),
					date_short( Set::classicExtract( $personne, 'Personne.dtnai' ) ),
					Set::classicExtract( $personne, 'Adresse.nomcom' ),
					Set::classicExtract( $personne, 'Historiqueetatpe.etat' ),
					Set::classicExtract( $personne, 'Historiqueetatpe.code' ),
					Set::classicExtract( $personne, 'Historiqueetatpe.motif' ),
					$this->Locale->date( 'Date::short', Set::classicExtract( $personne, 'Historiqueetatpe.date' ) ),
					Set::classicExtract( $personne, 'Serviceinstructeur.lib_service' ),
					Hash::get( $personne, 'Structurereferenteparcours.lib_struc' ),
					Hash::get( $personne, 'Referentparcours.nom_complet' ),
				);
				$this->Csv->addRow($row);
			}

		}
		else {
			$this->Csv->addRow(
				array(
					'Nom allocataire',
					'Prénom allocataire',
					'Date de naissance',
					'Commune de l\'allocataire',
					'Motif de radiation',
					'Date de radiation',
					'Service instructeur',
					__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ),
					__d( 'search_plugin', 'Referentparcours.nom_complet' ),
				)
			);

			foreach( $personnes as $personne ) {
				$row = array(
					Set::classicExtract( $personne, 'Personne.nom' ),
					Set::classicExtract( $personne, 'Personne.prenom'),
					date_short( Set::classicExtract( $personne, 'Personne.dtnai' ) ),
					Set::classicExtract( $personne, 'Adresse.nomcom' ),
					Set::classicExtract( $personne, 'Historiqueetatpe.motif' ),
					$this->Locale->date( 'Date::short', Set::classicExtract( $personne, 'Historiqueetatpe.date' ) ),
					Set::classicExtract( $personne, 'Serviceinstructeur.lib_service' ),
					Hash::get( $personne, 'Structurereferenteparcours.lib_struc' ),
					Hash::get( $personne, 'Referentparcours.nom_complet' ),
				);
				$this->Csv->addRow($row);
			}

		}
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'listes_pe-'.$nameTableauCsv.''.date( 'Ymd-His' ).'.csv' );
?>