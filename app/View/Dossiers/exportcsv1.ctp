<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

	// En-têtes
	$row = array(
		'N° Dossier',
		'Date de demande',
		'NIR',
		'Etat du droit',
		'Nom/Prénom allocataire',
		'Date de naissance',
		'Numéro de voie',
		'Type de voie',
		'Nom de voie',
		'Complément adresse 1',
		'Complément adresse 2',
		'Code postal',
		'Commune',
		'Type d\'orientation',
		'Identifiant Pôle Emploi',
		__d( 'dossier', 'Dossier.matricule' ),
		__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ),
		__d( $domain_search_plugin, 'Referentparcours.nom_complet' ),
		'Sexe',
		'Conditions de logement',
	);

	if( Configure::read( 'Cg.departement' ) == 58 ) {
		$row[] = 'Code activité';
		$row[] = __d( 'personne', 'Personne.etat_dossier_orientation' );
	}

	$this->Csv->addRow( $row );

	// Résultats
	foreach( $dossiers as $dossier ) {
		$dspnatlog = Hash::get( $dossier, 'DspRev.natlog' );
		if( empty( $natlog ) ) {
			$dspnatlog = Hash::get( $dossier, 'Dsp.natlog' );
		}

		$row = array(
			Hash::get( $dossier, 'Dossier.numdemrsa' ),
			date_short( Hash::get( $dossier, 'Dossier.dtdemrsa' ) ),
			Hash::get( $dossier, 'Personne.nir' ),
			value( $etatdosrsa, Hash::get( $dossier, 'Situationdossierrsa.etatdosrsa' ) ),
			value( $qual, Hash::get( $dossier, 'Personne.qual' ) ).' '.Hash::get( $dossier, 'Personne.nom' ).' '.Hash::get( $dossier, 'Personne.prenom'),
			date_short( Hash::get( $dossier, 'Personne.dtnai' ) ),
			Hash::get( $dossier, 'Adresse.numvoie' ),
			Hash::get( $dossier, 'Adresse.libtypevoie' ),
			Hash::get( $dossier, 'Adresse.nomvoie' ),
			Hash::get( $dossier, 'Adresse.complideadr' ),
			Hash::get( $dossier, 'Adresse.compladr' ),
			Hash::get( $dossier, 'Adresse.codepos' ),
			Hash::get( $dossier, 'Adresse.nomcom' ),
			Hash::get( $dossier, 'Typeorient.lib_type_orient' ),
			Hash::get( $dossier, 'Personne.idassedic' ),
			Hash::get( $dossier, 'Dossier.matricule' ),
			Hash::get( $dossier, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $dossier, 'Referentparcours.nom_complet' ),
			value( $sexe, Hash::get( $dossier, 'Personne.sexe' ) ),
			value( $natlog, $dspnatlog ),
		);

		if( Configure::read( 'Cg.departement' ) == 58 ) {
			$row[] = value( $act, Hash::get( $dossier, 'Activite.act' ) );
			$row[] = value( $etat_dossier_orientation, Hash::get( $dossier, 'Personne.etat_dossier_orientation' ) );
		}

		$this->Csv->addRow($row);
	}
	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'dossiers-'.date( 'Ymd-His' ).'.csv' );
?>