<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$this->Csv->addRow( array(
		'Numero CAF/MSA',
		'Nom / Prénom du demandeur',
		'Date de naissance du demandeur',
		'Adresse',
		'Nom / Prénom du conjoint',
		'Date ouverture de droits',
		'Ref. charge de l\'evaluation',
		'Date orientation (COV)',
		'Rang orientation (COV)',
		'Referent unique',
		'Date debut (CER)',
		'Date fin (CER)',
		'Rang (CER)',
		'Date inscription Pole Emploi',
		'Date (EP)',
		'Motif (EP)'
	) );

	foreach( $indicateurs as $indicateur ) {
		$adresse = Set::classicExtract( $indicateur, 'Adresse.numvoie' ).' '.Set::classicExtract( $indicateur, 'Adresse.libtypevoie' ).' '.Set::classicExtract( $indicateur, 'Adresse.nomvoie' ).'<br /> '.Set::classicExtract( $indicateur, 'Adresse.compladr' ).'<br /> '.Set::classicExtract( $indicateur, 'Adresse.codepos' ).' '.Set::classicExtract( $indicateur, 'Adresse.nomcom' );

		$conjoint = $indicateur['Personne']['qualcjt'].' '.$indicateur['Personne']['nomcjt'].' '.$indicateur['Personne']['prenomcjt'];

		$row = array(
			$indicateur['Dossier']['matricule'],
			$indicateur['Personne']['nom_complet'],
			date_short( $indicateur['Personne']['dtnai'] ),
			$adresse,
			$conjoint,
			date_short( $indicateur['Dossier']['dtdemrsa'] ),
			$indicateur['Referentorientant']['nom_complet'],
			date_short( $indicateur['Orientstruct']['date_valid']),
			$indicateur['Orientstruct']['rgorient'],
			$indicateur['Referentunique']['nom_complet'],
			date_short( $indicateur['Contratinsertion']['dd_ci'] ),
			date_short( $indicateur['Contratinsertion']['df_ci'] ),
			$indicateur['Contratinsertion']['rg_ci'],
			Set::enum( $indicateur['Historiqueetatpe']['etat'], $etatpe['etat'] ).' '.date_short( $indicateur['Historiqueetatpe']['date'] ),
			date_short( $indicateur['Commissionep']['dateseance'] ),
			!empty( $indicateur['Dossierep']['themeep'] ) ? Set::classicExtract( $options['themeep'], $indicateur['Dossierep']['themeep'] ) : null
		);
		$this->Csv->addRow($row);
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'indicateurssuivis-'.date( 'Ymd-His' ).'.csv' );
?>