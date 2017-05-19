<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

	$this->Csv->addRow(
		array(
			__d( 'dossier', 'Dossier.numdemrsa' ),
			__d( 'dossier', 'Dossier.matricule' ),
			'Adresse actuelle',
			'Allocataire',
			__d( 'prestation', 'Prestation.rolepers' ),
			'Date de transfert',
			'Structure référente source',
			'Structure référente cible',
			__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ),
			__d( $domain_search_plugin, 'Referentparcours.nom_complet' ),
		)
	);

	foreach( $results as $result ) {
		$row = array(
			h( $result['Dossier']['numdemrsa'] ),
			h( $result['Dossier']['matricule'] ),
			h( "{$result['Adresse']['codepos']} {$result['Adresse']['nomcom']}" ),
			h( "{$options['qual'][$result['Personne']['qual']]} {$result['Personne']['nom']} {$result['Personne']['prenom']}" ),
			$options['rolepers'][$result['Prestation']['rolepers']],
			$this->Locale->date( __( 'Date::short' ), $result['Transfertpdv93']['created'] ),
			$result['VxStructurereferente']['lib_struc'],
			$result['Structurereferente']['lib_struc'],
			Hash::get( $result, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $result, 'Referentparcours.nom_complet' ),
		);
		$this->Csv->addRow( $row );
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( "{$this->request->params['controller']}_{$this->request->params['action']}_".date( 'Ymd-His' ).'.csv' );
?>