<?php
	/**
	 * Valeurs par défaut du filtre de recherche de la cohorte de RDV.
	 */
	Configure::write(
		'Filtresdefaut.Cohortesrendezvous_cohorte',
		array(
			'Search' => array(
				'Dossier' => array(
					'dernier' => '1'
				),
				'Rendezvous' => array(
					'statutrdv_id' => 2, // Statut "prévu"
					'daterdv' => '1',
					'daterdv_from' => date_sql_to_cakephp( date( 'Y-m-d', strtotime( 'first day of this month' ) ) ),
					'daterdv_to' => date_sql_to_cakephp( date( 'Y-m-d', strtotime( 'now' ) ) ),
				),
				'Pagination' => array(
					'nombre_total' => '0'
				)
			)
		)
	);

	/**
	 * Liste des champs devant apparaître dans la cohorte de rendez-vous.
	 *	- Dsps.index.fields contient les champs de chaque ligne du tableau de résultats
	 *	- Dsps.index.innerTable contient les champs de l'infobulle de chaque ligne du tableau de résultats
	 *	- Dsps.exportcsv contient les champs de chaque ligne du tableau à télécharger au format CSV
	 *
	 * Voir l'onglet "Environnement logiciel" > "WebRSA" > "Champs spécifiés dans
	 * le webrsa.inc" de la vérification de l'application.
	 */
	Configure::write(
		'Cohortesrendezvous',
		array(
			'cohorte' => array(
				'fields' => array(
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Structurereferente.lib_struc',
					'Referent.nom_complet',
					'Typerdv.libelle',
					'Rendezvous.daterdv',
					'Rendezvous.heurerdv',
					'Statutrdv.libelle'
				)/*,
				'innerTable' => array(
					'Personne.dtnai',
					'Adresse.numcom',
					'Personne.nir',
					'Situationdossierrsa.etatdosrsa',
					'Donnees.nivetu',
					'Donnees.hispro',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Deractromev3.familleromev3',
					'Deractromev3.appellationromev3',
					'Actrechromev3.familleromev3',
					'Actrechromev3.appellationromev3'
				),
				'header' => array(
					array( 'Dossier' => array( 'colspan' => 3 ) ),
					array( 'Accompagnement et difficultés' => array( 'colspan' => 3 ) ),
					array( 'Code ROME' => array( 'colspan' => 4 ) ),
					array( 'Hors code ROME' => array( 'colspan' => 4 ) ),
					array( ' ' => array( 'class' => 'action noprint' ) ),
					array( ' ' => array( 'style' => 'display: none' ) ),
				)*/
			),
			'exportcsv' => array(
				'Personne.nom_complet',
				'Adresse.nomcom',
				'Structurereferente.lib_struc',
				'Referent.nom_complet',
				'Typerdv.libelle',
				'Rendezvous.daterdv',
				'Rendezvous.heurerdv',
				'Statutrdv.libelle',
				'Personne.numfixe' => array( 'label' => 'Num de telephone fixe' ),
				'Personne.numport' => array( 'label' => 'Num de telephone portable' ),
				'Personne.email' => array( 'label' => 'Adresse mail' )
			)
		)
	);
?>