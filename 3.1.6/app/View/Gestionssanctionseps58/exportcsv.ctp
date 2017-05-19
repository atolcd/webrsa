<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	// En-tête
	$this->Csv->addRow(
		array(
			'Nom allocataire',
			'Commune allocataire',
			'Identifiant EP',
			'Identifiant commission',
			'Date de la commission',
			'Thématique',
			'Sanction 1',
			'Sanction 2',
			'Date prévisionnelle de radiation',
			'Modification de la sanction',
			'Date fin de sanction',
			'Commentaire',
			__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ),
			__d( 'search_plugin', 'Referentparcours.nom_complet' ),
		)
	);

	// Résultats
	$datePrevisionnelleRadiationInterval = Configure::read( 'Decisionsanctionep58.datePrevisionnelleRadiation' );

	foreach( $gestionssanctionseps58 as $gestionsanctionep58 ) {
		$modeleDecision = Inflector::classify( "decisions{$gestionsanctionep58['Dossierep']['themeep']}" );

		// Type de sanction
		$decisionSanction1 = Set::enum( $gestionsanctionep58[$modeleDecision]['decision'], $regularisationlistesanctionseps58[$modeleDecision]['decision'] );
		$decisionSanction2 = Set::enum( $gestionsanctionep58[$modeleDecision]['decision2'], $regularisationlistesanctionseps58[$modeleDecision]['decision'] );
		// Libellé de la sanction
		$libelleSanction1 = Set::enum( $gestionsanctionep58[$modeleDecision]['listesanctionep58_id'], $listesanctionseps58 );
		$libelleSanction2 = Set::enum( $gestionsanctionep58[$modeleDecision]['autrelistesanctionep58_id'], $listesanctionseps58 );

		$datePrevisionnelleRadiation = date( 'd/m/Y', strtotime( $datePrevisionnelleRadiationInterval, strtotime( Hash::get( $gestionsanctionep58, 'Commissionep.dateseance' ) ) ) );

		$fieldDecisionSanction = Set::enum( $gestionsanctionep58[$modeleDecision]['arretsanction'], $options[$modeleDecision]['arretsanction'] );
		$dateFinSanction = date_short( $gestionsanctionep58[$modeleDecision]['datearretsanction'] );
		$commentaireFinSanction = $gestionsanctionep58[$modeleDecision]['commentairearretsanction'];

		$row = array(
			$gestionsanctionep58['Personne']['qual'].' '.$gestionsanctionep58['Personne']['nom'].' '.$gestionsanctionep58['Personne']['prenom'],
			Set::classicExtract(  $gestionsanctionep58, 'Adresse.numvoie' ).' '.Set::classicExtract( $gestionsanctionep58, 'Adresse.libtypevoie' ).' '.Set::classicExtract(  $gestionsanctionep58, 'Adresse.nomvoie' )."\n".Set::classicExtract(  $gestionsanctionep58, 'Adresse.codepos' ).' '.Set::classicExtract(  $gestionsanctionep58, 'Adresse.nomcom' ),
			$gestionsanctionep58['Ep']['identifiant'],
			$gestionsanctionep58['Commissionep']['identifiant'],
			date_short( $gestionsanctionep58['Commissionep']['dateseance'] ),
			Set::classicExtract( $options['Dossierep']['themeep'], ( $gestionsanctionep58['Dossierep']['themeep'] ) ),
			$decisionSanction1."\n".$libelleSanction1,
			$decisionSanction2."\n".$libelleSanction2,
			$datePrevisionnelleRadiation,
			$fieldDecisionSanction,
			$dateFinSanction,
			$commentaireFinSanction,
			Hash::get( $gestionsanctionep58, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $gestionsanctionep58, 'Referentparcours.nom_complet' ),
		);
		$this->Csv->addRow($row);
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'listes_modification_sanctionep'.date( 'Ymd-His' ).'.csv' );