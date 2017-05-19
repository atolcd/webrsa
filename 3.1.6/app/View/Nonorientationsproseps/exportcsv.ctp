<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

	$this->Csv->addRow(
		array(
			'N° dossier RSA',
			'Nom allocataire',
			'Prénom allocataire',
			'Date de naissance',
			'Commune de l\'allocataire',
			'Date de validation de l\'orientation',
			'Nb de jours depuis la fin du contrat lié',
			'Type d\'orientation',
			'Structure référente',
			'Référent',
			__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ),
			__d( $domain_search_plugin, 'Referentparcours.nom_complet' ),
		)
	);

	foreach( $orientsstructs as $orientstruct ) {

		$row = array(
			Set::classicExtract( $orientstruct, 'Dossier.numdemrsa' ),
			Set::classicExtract( $orientstruct, 'Personne.nom'),
			Set::classicExtract( $orientstruct, 'Personne.prenom'),
			date_short( Set::classicExtract( $orientstruct, 'Personne.dtnai' ) ),
			Set::classicExtract( $orientstruct, 'Adresse.nomcom' ),
			$this->Locale->date( 'Date::short', Set::classicExtract( $orientstruct, 'Orientstruct.date_valid' ) ),
			Set::classicExtract( $orientstruct, 'Contratinsertion.nbjours'),
			Set::classicExtract( $orientstruct, 'Typeorient.lib_type_orient'),
			Set::classicExtract( $orientstruct, 'Structurereferente.lib_struc'),
			Set::classicExtract( $orientstruct, 'Referent.nom').' '.Set::classicExtract( $orientstruct, 'Referent.prenom'),
			Hash::get( $orientstruct, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $orientstruct, 'Referentparcours.nom_complet' ),
		);
		$this->Csv->addRow($row);
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'listes_demande_maintien_social'.date( 'Ymd-His' ).'.csv' );
?>