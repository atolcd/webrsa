<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$this->Csv->addRow(
		array(
			'Date du bilan de parcours',
			'Nom de la personne',
			__d( 'dossier', 'Dossier.matricule' ),
			'Type de structure',
			'Nom du prescripteur',
			'Type de commission',
			'Position du bilan',
			'Choix du parcours',
			'Saisine EP',
			'Code INSEE',
			'Localité',
			__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ),
			__d( 'search_plugin', 'Referentparcours.nom_complet' ),
		)
	);

	foreach( $bilansparcours66 as $bilanparcours66 ) {
		$isSaisine = 'Non';
		if( isset( $bilanparcours66['Dossierep']['themeep'] ) ){
			$isSaisine = 'Oui';
		}

		$motif = null;
		if (empty($bilanparcours66['Bilanparcours66']['choixparcours']) && !empty($bilanparcours66['Bilanparcours66']['examenaudition'])) {
			$motif = Set::classicExtract( $options['examenaudition'], $bilanparcours66['Bilanparcours66']['examenaudition'] );
		}
		elseif (empty($bilanparcours66['Bilanparcours66']['choixparcours']) && empty($bilanparcours66['Bilanparcours66']['examenaudition'])) {
			if ($bilanparcours66['Bilanparcours66']['maintienorientation']=='0') {
				$motif = 'Réorientation';
			}
			else {
				$motif = 'Maintien';
			}
		}
		else {
			$motif = Set::classicExtract( $options['choixparcours'], $bilanparcours66['Bilanparcours66']['choixparcours'] );
		}

		$row = array(
			$this->Locale->date( 'Date::short', Hash::get( $bilanparcours66, 'Bilanparcours66.datebilan' ) ),
			Hash::get( $bilanparcours66, 'Personne.nom_complet' ),
			Hash::get( $bilanparcours66, 'Dossier.matricule' ),
			Hash::get( $bilanparcours66, 'Structurereferente.lib_struc' ),
			Hash::get( $bilanparcours66, 'Referent.nom_complet' ),
			Hash::get( $options['proposition'], $bilanparcours66['Bilanparcours66']['proposition'] ),
			Set::enum( Hash::get( $bilanparcours66, 'Bilanparcours66.positionbilan' ), $options['positionbilan'] ),
			$motif,
			$isSaisine,
			$bilanparcours66['Adresse']['numcom'],
			$bilanparcours66['Adresse']['nomcom'],
			Hash::get( $bilanparcours66, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $bilanparcours66, 'Referentparcours.nom_complet' ),
		);
		$this->Csv->addRow($row);
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'bilansparcours66-'.date( 'Ymd-His' ).'.csv' );
?>