<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

	$this->Csv->addRow(
		array(
			__d( 'dossier', 'Dossier.matricule' ),
			'Nom / prénom bénéficiaire',
			__d( 'personne', 'Personne.nir' ),
			'Ville',
			 __d( 'foyer', 'Foyer.enerreur' ),
			'Présence contrat ?',
			'Date de fin du dernier contrat',
			'Nbre jours depuis la fin du dernier contrat',
			'Date d\'orientation',
			'Nbre jours depuis orientation',
			'Statut EP',
			'Date de relance',
			'Rang de relance',
			__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ),
			__d( $domain_search_plugin, 'Referentparcours.nom_complet' ),
		)
	);

	foreach( $relances as $relance ) {
		$etatdossierep = $relance['Passagecommissionep']['etatdossierep'];
		if( empty( $etatdossierep ) && !empty( $relance['Dossierep']['id'] ) ) {
			$etatdossierep = 'En attente';
		}
		else {
			$etatdossierep = Set::enum( $relance['Passagecommissionep']['etatdossierep'], $options['Passagecommissionep']['etatdossierep'] );
		}

		$row = array(
			h( $relance['Dossier']['matricule'] ),
			h( "{$relance['Personne']['nom']} {$relance['Personne']['prenom']}" ),
			h( $relance['Personne']['nir'] ),
			h( $relance['Adresse']['nomcom'] ),
			h( $relance['Foyer']['enerreur'] ),
			h( empty( $relance['Contratinsertion']['id'] ) ? 'Non' : 'Oui' ),
			$this->Locale->date( 'Locale->date', $relance['Contratinsertion']['df_ci'] ),
			h( $relance['Contratinsertion']['nbjours'] ),
			$this->Locale->date( 'Locale->date', $relance['Orientstruct']['date_impression'] ),
			h( $relance['Orientstruct']['nbjours'] ),
			h( $etatdossierep ),
			$this->Locale->date( 'Locale->date', $relance['Relancenonrespectsanctionep93']['daterelance'] ),
			( ( $relance['Relancenonrespectsanctionep93']['numrelance'] < 2 ) ? '1ère relance' : "{$relance['Relancenonrespectsanctionep93']['numrelance']}ème relance" ),
			Hash::get( $relance, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $relance, 'Referentparcours.nom_complet' ),
		);
		$this->Csv->addRow( $row );
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'relances-'.date( 'Ymd-His' ).'.csv' );
?>