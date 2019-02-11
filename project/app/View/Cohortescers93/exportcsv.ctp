<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	if( $etape == 'saisie' ) {
		$this->Csv->addRow(
			array(
				'Commune',
				'Date de demande',
				'Nom/Prénom',
				'Date de naissance',
				'Date d\'orientation',
				'Soumis à droits et devoirs',
				'Présence d\'une DSP',
				'Rang CER',
				'Statut CER',
				'Forme CER',
				'Date d\'affectation',
				'Affectation',
			)
		);

		foreach( $cers93 as $cer93 ) {
			$row = array(
				$cer93['Adresse']['nomcom'],
				date_short( $cer93['Dossier']['dtdemrsa'] ),
				$cer93['Personne']['nom_complet_court'],
				date_short( $cer93['Personne']['dtnai'] ),
				date_short( $cer93['Orientstruct']['date_valid'] ),
				$this->Xhtml->boolean( $cer93['Calculdroitrsa']['toppersdrodevorsa'], false ),
				$this->Xhtml->boolean( $cer93['Dsp']['exists'], false ),
				$cer93['Contratinsertion']['rg_ci'],
				Set::enum( $cer93['Cer93']['positioncer'], $options['Cer93']['positioncer'] ),
				Set::enum( $cer93['Histochoixcer93etape03']['formeci'], $options['formeci'] ),
				date_short( $cer93['PersonneReferent']['dddesignation'] ),
				$cer93['Referent']['nom_complet'],
			);
			$this->Csv->addRow( $row );
		}
	}
	else {
		$this->Csv->addRow(
			array(
				'Commune',
				'Nom/Prénom',
				'Structure référente',
				'Référent',
				'Saisie du CER',
				'Etape Responsable',
				'Etape CD',
				'Validation CS',
				'Etape Cadre',
				'Validation Responsable',
				'Forme du CER',
				'Commentaire du Responsable',
				'Date de transfert au CD',
				'Validation CD (1ère lecture)',
				'Commentaire du CD',
				'Validation Cadre',
				'Forme CER'
			)
		);

		foreach( $cers93 as $cer93 ) {
			if( $cer93['Histochoixcer93etape03']['isrejet'] == '1' ) {
				$validationcpdv = 'Rejeté';
			}
			else{
				$validationcpdv = Set::enum( $cer93['Histochoixcer93etape03']['etape'], $options['Cer93']['positioncer'] );
			}

			$row = array(
				$cer93['Adresse']['nomcom'],
				$cer93['Personne']['nom_complet_court'],
				$cer93['Structurereferente']['lib_struc'],
				$cer93['Referent']['nom_complet'],
				Set::enum( $cer93['Cer93']['positioncer'], $options['Cer93']['positioncer'] ),
				$validationcpdv,
				Set::enum( $cer93['Histochoixcer93etape03']['formeci'], $options['formeci'] ),
				$cer93['Histochoixcer93etape03']['commentaire'],
				date_short( $cer93['Histochoixcer93etape03']['datechoix'] ),
				Set::enum( $cer93['Histochoixcer93etape04']['prevalide'], $options['Histochoixcer93']['prevalide'] ),
				$cer93['Histochoixcer93etape04']['commentaire'],
				Set::enum( $cer93['Histochoixcer93etape05']['decisioncs'], $options['Histochoixcer93']['decisioncs'] ),
				Set::enum( $cer93['Histochoixcer93etape06']['decisioncadre'], $options['Histochoixcer93']['decisioncadre'] ),
				Set::enum( $cer93['Histochoixcer93etape06']['formeci'], $options['formeci'] ),
			);
			$this->Csv->addRow( $row );
		}
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'cers93-'.date( 'Ymd-His' ).'.csv' );
?>