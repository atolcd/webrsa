<?php
	/**
	 * Menu "Recherches" > "Par contrats" > "Par CER"
	 */
	Configure::write(
		'ConfigurableQuery.Contratsinsertion.search',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1',
						// Case à cocher "Filtrer par date de demande RSA"
						'dtdemrsa' => '0',
						// Du (inclus)
						'dtdemrsa_from' => date_sql_to_cakephp( date( 'Y-m-d', strtotime( '-1 week' ) ) ),
						// Au (inclus)
						'dtdemrsa_to' => date_sql_to_cakephp( date( 'Y-m-d', strtotime( 'now' ) ) ),
					)
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				'skip' => array()
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(),
				// 2.3 Tri par défaut
				'order' => array( 'Contratinsertion.df_ci' )
			),
			// 3. Nombre d'enregistrements par page
			'limit' => 10,
			// 4. Lancer la recherche au premier accès à la page ?
			'auto' => false,
			// 5. Résultats de la recherche
			'results' => array(
				// 5.1 Ligne optionnelle supplémentaire d'en-tête du tableau de résultats
				'header' => array(),
				// 5.2 Colonnes du tableau de résultats
				'fields' => array (
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Referent.nom_complet',
					'Dossier.matricule',
					'Contratinsertion.date_saisi_ci' => array( 'type' => 'date' ),
					'Contratinsertion.rg_ci',
					'Contratinsertion.decision_ci',
					'Contratinsertion.datevalidation_ci',
					'Contratinsertion.forme_ci',
					'Contratinsertion.positioncer',
					'Contratinsertion.df_ci',
					'/Contratsinsertion/index/#Contratinsertion.personne_id#' => array( 'class' => 'view' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Personne.dtnai',
					'Adresse.numcom',
					'Personne.nir',
					'Prestation.rolepers',
					'Situationdossierrsa.etatdosrsa',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Personne.etat_dossier_orientation',
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array(
				'max_execution_time' => 0,
				'memory_limit' => '512M'
			)
		)
	);

	/**
	 * Export CSV, menu "Recherches" > "Par contrats" > "Par CER"
	 */
	Configure::write(
		'ConfigurableQuery.Contratsinsertion.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Contratsinsertion.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Contratsinsertion.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Situationdossierrsa.etatdosrsa',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Dossier.matricule',
					'Adresse.numvoie',
					'Adresse.libtypevoie',
					'Adresse.nomvoie',
					'Adresse.complideadr',
					'Adresse.compladr',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Typeorient.lib_type_orient',
					'Referent.nom_complet',
					'Structurereferente.lib_struc',
					'Contratinsertion.num_contrat',
					'Contratinsertion.dd_ci' => array( 'type' => 'date' ),
					'Contratinsertion.duree_engag',
					'Contratinsertion.decision_ci',
					'Contratinsertion.datevalidation_ci' => array( 'type' => 'date' ),
					'Contratinsertion.df_ci' => array( 'type' => 'date' ),
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Personne.etat_dossier_orientation'
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Contratsinsertion.search.ini_set' ),
		)
	);
?>