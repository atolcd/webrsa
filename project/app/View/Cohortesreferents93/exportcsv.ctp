<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$this->Csv->addRow(
		array(
			'Commune',
			'Date de demande',
			'Date d\'orientation',
			'Date de naissance',
			'Soumis à droits et devoirs',
			'Présence d\'une DSP',
			'Nom, prénom',
			'Rang CER',
			'État CER',
			'Date de fin de CER',
			'Date de début d\'affectation',
			'Affectation',
			'N° de dossier',
			'Date ouverture de droit',
			'Date de naissance',
			__d( 'dossier', 'Dossier.matricule' ),
			'NIR',
			'Code postal',
			'Date de fin de droit',
			'Motif de fin de droit',
			'Rôle',
			'Etat du dossier',
			'Présence DSP',
			'Adresse',
			'CER signé dans la structure',
		)
	);

	foreach( $personnes_referents as $personne_referent ) {
		$row = array(
			$personne_referent['Adresse']['nomcom'],
			date_short( $personne_referent['Dossier']['dtdemrsa'] ),
			date_short( $personne_referent['Orientstruct']['date_valid'] ),
			date_short( $personne_referent['Personne']['dtnai'] ),
			$this->Xhtml->boolean( $personne_referent['Calculdroitrsa']['toppersdrodevorsa'], false ),
			$this->Xhtml->boolean( $personne_referent['Dsp']['exists'], false ),
			$personne_referent['Personne']['nom_complet_court'],
			$personne_referent['Contratinsertion']['rg_ci'],
			Set::enum( $personne_referent['Cer93']['positioncer'], $options['Cer93']['positioncer'] ),
			date_short( $personne_referent['Contratinsertion']['df_ci'] ),
			date_short( $personne_referent['PersonneReferent']['dddesignation'] ),
			Set::enum( $personne_referent['PersonneReferent']['referent_id'], $options['referents'] ),
			$personne_referent['Dossier']['numdemrsa'],
			date_short( $personne_referent['Dossier']['dtdemrsa'] ),
			date_short( $personne_referent['Personne']['dtnai'] ),
			$personne_referent['Dossier']['matricule'],
			$personne_referent['Personne']['nir'],
			$personne_referent['Adresse']['codepos'],
			$personne_referent['Situationdossierrsa']['dtclorsa'],
			$personne_referent['Situationdossierrsa']['moticlorsa'],
			Set::enum( $personne_referent['Prestation']['rolepers'], $options['rolepers'] ),
			Set::classicExtract( $options['etatdosrsa'], $personne_referent['Situationdossierrsa']['etatdosrsa'] ),
			$this->Xhtml->boolean( $personne_referent['Dsp']['exists'], false ),
			$personne_referent['Adresse']['numvoie'].' '.$personne_referent['Adresse']['libtypevoie'].' '.$personne_referent['Adresse']['nomvoie'].' '.$personne_referent['Adresse']['codepos'].' '.$personne_referent['Adresse']['nomcom'],
			$this->Xhtml->boolean( $personne_referent['Contratinsertion']['interne'], false ),
		);
		$this->Csv->addRow( $row );
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'personnes_referents-'.date( 'Ymd-His' ).'.csv' );
?>