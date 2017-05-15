<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

	$this->Csv->addRow(
        array(
            'N° Dossier',
            'Nom/Prénom allocataire',
            __d( 'dossier', 'Dossier.matricule' ),
            'Numéro de voie',
            'Type de voie',
            'Nom de voie',
            'Complément adresse 1',
            'Complément adresse 2',
            'Code postal',
            'Commune',
            'Proposition de décision',
            'Motif PDO',
            'Date de proposition de décision',
            'Gestionnaire',
			__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ),
			__d( $domain_search_plugin, 'Referentparcours.nom_complet' ),
        )
    );
// debug($pdos);
// die();
	foreach( $pdos as $pdo ) {
		$row = array(
			Hash::get( $pdo, 'Dossier.numdemrsa' ),
			value( $qual, Hash::get( $pdo, 'Personne.qual' ) ).' '.Hash::get( $pdo, 'Personne.nom' ).' '.Hash::get( $pdo, 'Personne.prenom'),
			Hash::get( $pdo, 'Dossier.matricule' ),
			Hash::get( $pdo, 'Adresse.numvoie' ),
			Hash::get( $pdo, 'Adresse.libtypevoie' ),
			Hash::get( $pdo, 'Adresse.nomvoie' ),
			Hash::get( $pdo, 'Adresse.complideadr' ),
			Hash::get( $pdo, 'Adresse.compladr' ),
			Hash::get( $pdo, 'Adresse.codepos' ),
			Hash::get( $pdo, 'Adresse.nomcom' ),
			Set::enum( Hash::get( $pdo, 'Decisionpropopdo.decisionpdo_id' ), $decisionpdo ),
			Set::enum( Hash::get( $pdo, 'Propopdo.motifpdo' ), $motifpdo ),
			$this->Locale->date( 'Date::short', Hash::get( $pdo, 'Decisionpropopdo.datedecisionpdo' ) ),
			Set::enum( Hash::get( $pdo, 'Propopdo.user_id' ), $gestionnaire ),
			Hash::get( $pdo, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $pdo, 'Referentparcours.nom_complet' ),
		);

		$this->Csv->addRow($row);
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'pdos-'.date( 'Ymd-His' ).'.csv' );
?>