<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$this->Csv->addRow(
        array(
			__d( 'search_plugin', 'Personne.id' ),
			__d( 'search_plugin', 'Dossier.numdemrsa' ),
			__d( 'search_plugin', 'Dossier.dtdemrsa' ),
			__d( 'search_plugin', 'Dossier.matricule' ),
			__d( 'search_plugin', 'Personne.nom' ),
			__d( 'search_plugin', 'Personne.prenom' ),
			__d( 'search_plugin', 'Prestation.rolepers' ),
			__d( 'search_plugin', 'Adresse.nomcom' ),
        )
    );

	foreach( $results as $result ) {
		$row = array(
			Hash::get( $result, 'Personne.id' ),
			Hash::get( $result, 'Dossier.numdemrsa' ),
			date( Hash::get( $result, 'Dossier.dtdemrsa' ) ),
			Hash::get( $result, 'Dossier.matricule' ),
			Hash::get( $result, 'Personne.nom' ),
			Hash::get( $result, 'Personne.prenom' ),
			value( $options['Prestation']['rolepers'], Hash::get( $result, 'Prestation.rolepers' ) ),
			Hash::get( $result, 'Adresse.nomcom' ),
		);
		$this->Csv->addRow($row);
	}
	Configure::write( 'debug', 0 );
	echo $this->Csv->render(
		$this->request->params['controller'].'_'.
		$this->request->params['action'].'_'.
		date( 'Ymd-His' ).'.csv'
	);
?>