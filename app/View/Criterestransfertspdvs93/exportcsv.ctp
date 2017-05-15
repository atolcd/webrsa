<?php
	$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

	echo $this->Default3->csv(
		$results,
		array(
			'Dossier.numdemrsa' => array( 'domain' => 'dossier' ),
			'Dossier.matricule' => array( 'domain' => 'dossier' ),
			'Adresse.codepos',
			'Adresse.nomcom',
			'Personne.qual',
			'Personne.nom',
			'Personne.prenom',
			'Prestation.rolepers',
			'Transfertpdv93.created' => array( 'type' => 'date' ),
			'VxStructurereferente.lib_struc',
			'NvStructurereferente.lib_struc',
			'Structurereferenteparcours.lib_struc' => array( 'domain' => $domain_search_plugin ),
			'Referentparcours.nom_complet' => array( 'domain' => $domain_search_plugin )
		),
		array(
			'options' => $options
		)
	);
?>