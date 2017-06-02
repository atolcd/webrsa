<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

	$this->Csv->addRow(
        array(
            'N° Dossier',
            __d( 'dossier', 'Dossier.matricule' ),
            'Nom/Prénom allocataire',
            'Numéro de voie',
            'Type de voie',
            'Nom de voie',
            'Complément adresse 1',
            'Complément adresse 2',
            'Code postal',
            'Commune',
            'Suivi',
            'Situation des droits',
            'Date indus',
            'Montant initial de l\'indu',
            'Montant transféré CG',
            'Remise CG',
			__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ),
			__d( $domain_search_plugin, 'Referentparcours.nom_complet' ),
        )
    );

	foreach( $indus as $indu ) {
		$row = array(
			Hash::get( $indu, 'Dossier.numdemrsa' ),
			Hash::get( $indu, 'Dossier.matricule' ),
			value( $qual, Hash::get( $indu, 'Personne.qual' ) ).' '.Hash::get( $indu, 'Personne.nom' ).' '.Hash::get( $indu, 'Personne.prenom'),
            Hash::get( $indu, 'Adresse.numvoie' ),
			Hash::get( $indu, 'Adresse.libtypevoie' ),
			Hash::get( $indu, 'Adresse.nomvoie' ),
			Hash::get( $indu, 'Adresse.complideadr' ),
			Hash::get( $indu, 'Adresse.compladr' ),
			Hash::get( $indu, 'Adresse.codepos' ),
			Hash::get( $indu, 'Adresse.nomcom' ),
			Hash::get( $indu, 'Dossier.typeparte' ),
			value( $etatdosrsa, Hash::get( $indu, 'Situationdossierrsa.etatdosrsa' ) ),
			$this->Locale->date( 'Date::miniLettre', $indu[0]['moismoucompta'] ),
			$this->Locale->money( $indu[0]['mt_indus_constate'] ),
			$this->Locale->money( $indu[0]['mt_indus_transferes_c_g'] ),
			$this->Locale->money( $indu[0]['mt_remises_indus'] ),
			Hash::get( $indu, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $indu, 'Referentparcours.nom_complet' ),
		);
		$this->Csv->addRow($row);
	}
	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'indus-'.date( 'Ymd-His' ).'.csv' );
?>