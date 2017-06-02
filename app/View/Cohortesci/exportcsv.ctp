<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

	$this->Csv->addRow(
		array(
			'N° Dossier',
			'Nom/Prénom allocataire',
			'Commune de l\'allocataire',
			'Référent',
			'Service référent',
			'Type de contrat',
			'Date début contrat',
			'Durée',
			'Date fin contrat',
			'Décision et date validation',
			'Action prévue',
			__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ),
			__d( $domain_search_plugin, 'Referentparcours.nom_complet' ),
		)
	);

	foreach( $contrats as $contrat ) {

		$row = array(
			Set::classicExtract( $contrat, 'Dossier.numdemrsa' ),
			Set::classicExtract( $contrat, 'Personne.nom' ).' '.Set::classicExtract( $contrat, 'Personne.prenom'),
			Set::classicExtract( $contrat, 'Adresse.nomcom' ),
			value( $referents, Set::classicExtract( $contrat, 'PersonneReferent.referent_id' ) ),
			value( $struct, Set::classicExtract( $contrat, 'Contratinsertion.structurereferente_id' ) ),
			Set::enum( Set::classicExtract( $contrat, 'Contratinsertion.num_contrat' ), $numcontrat['num_contrat'] ),
			$this->Locale->date( 'Date::short', Set::classicExtract( $contrat, 'Contratinsertion.dd_ci' ) ),
			//Set::enum( Set::extract( $contrat, 'Contratinsertion.duree_engag' ), $duree_engag_cg93 ),
			value( $duree_engag, Hash::get( $contrat, 'Contratinsertion.duree_engag' ) ),
			$this->Locale->date( 'Date::short', Set::classicExtract( $contrat, 'Contratinsertion.df_ci' ) ),
			Set::classicExtract( $decision_ci, Set::classicExtract( $contrat, 'Contratinsertion.decision_ci' ) ).' '.$this->Locale->date( 'Date::short', Set::classicExtract( $contrat, 'Contratinsertion.datevalidation_ci' ) ),
			Set::enum( Set::classicExtract( $contrat, 'Contratinsertion.actions_prev' ), $action ),
			Hash::get( $contrat, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $contrat, 'Referentparcours.nom_complet' ),
		);
		$this->Csv->addRow($row);
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'contrats_valides-'.date( 'Ymd-His' ).'.csv' );
?>