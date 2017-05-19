<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

    $this->Csv->addRow(
        array(
            __d( 'dossier', 'Dossier.numdemrsa' ),
            'Allocataire',
            'Gestionnaire',
            'Type de traitement',
            'Date du traitement',
            'Motif de la situation',
            'Description du traitement',
            'Date de création de la DO',
            'Date de réception',
            'Date d\'échéance',
            'Traitement clos ?',
            'Traitement annulé ?',
            'Nb de fichiers dans la corbeille',
			__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ),
			__d( 'search_plugin', 'Referentparcours.nom_complet' ),
        )
    );


	foreach( $results as $i => $result ) {
        $row = array(
           h( Hash::get( $result, 'Dossier.numdemrsa' ) ),
            h( Set::enum( Hash::get( $result, 'Personne.qual' ), $qual ).' '.Hash::get( $result, 'Personne.nom' ).' '.Hash::get( $result, 'Personne.prenom' ) ),
            h( Hash::get( $result, 'User.nom_complet' ) ),
            h( Set::enum( Hash::get( $result, 'Traitementpcg66.typetraitement' ), $options['Traitementpcg66']['typetraitement'] ) ),
            h( date_short( Hash::get( $result, 'Traitementpcg66.created' ) ) ),
            h( Hash::get( $result, 'Situationpdo.libelle' ) ),
            h( Set::enum( Hash::get( $result, 'Traitementpcg66.descriptionpdo_id' ), $descriptionpdo ) ),
            h( $this->Locale->date( 'Locale->date',  Hash::get( $result, 'Dossierpcg66.datereceptionpdo' ) ) ),
            h( date_short( Hash::get( $result, 'Traitementpcg66.datereception' ) ) ),
            h( date_short( Hash::get( $result, 'Traitementpcg66.dateecheance' ) ) ),
            h( Set::enum( Hash::get( $result, 'Traitementpcg66.clos' ), $options['Traitementpcg66']['clos'] ) ),
            h( Set::enum( Hash::get( $result, 'Traitementpcg66.annule' ), $options['Traitementpcg66']['annule'] ) ),
            h( $result['Fichiermodule']['nb_fichiers_lies'] ),
			Hash::get( $result, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $result, 'Referentparcours.nom_complet' ),
        );
        $this->Csv->addRow( $row );
    }



	Configure::write( 'debug', 0 );
	echo $this->Csv->render( "{$this->request->params['controller']}_{$this->request->params['action']}_".date( 'Ymd-His' ).'.csv' );
?>