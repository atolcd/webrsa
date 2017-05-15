<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$this->Csv->addRow(
		array(
			'N° Dossier',
			'Allocataire principal',
			'Commune de l\'allocataire',
			'Date de réception DO',
			'Type de dossier',
			'Origine du dossier',
			'Organisme payeur',
			'Service instructeur',
			'Gestionnaire',
			__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ),
			__d( 'search_plugin', 'Referentparcours.nom_complet' ),
		)
	);

	foreach( $dossierspcgs66 as $dossierpcg66 ) {
		$row = array(
			$dossierpcg66['Dossier']['numdemrsa'],
			$dossierpcg66['Personne']['nom'].' '.$dossierpcg66['Personne']['prenom'],
			$dossierpcg66['Adresse']['nomcom'],
			date_short( $dossierpcg66['Dossierpcg66']['datereceptionpdo'] ),
			$dossierpcg66['Typepdo']['libelle'],
			$dossierpcg66['Originepdo']['libelle'],
			$dossierpcg66['Dossierpcg66']['orgpayeur'],
			$dossierpcg66['Serviceinstructeur']['lib_service'],
			Set::enum( Set::classicExtract( $dossierpcg66, 'Dossierpcg66.user_id' ), $gestionnaire ),
			Hash::get( $dossierpcg66, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $dossierpcg66, 'Referentparcours.nom_complet' ),
		);
		$this->Csv->addRow($row);
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'dossierspcgs66_affectes-'.date( 'Ymd-His' ).'.csv' );
?>