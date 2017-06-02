<?php
	/**
	 * Menu "Recherches"
	 */
	Configure::write(
		'ConfigurableQuery.ActionscandidatsPersonnes.search',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1'
					)
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				'skip' => array(),
				// 1.4 Filtres additionnels : La personne possède un(e)...
				'has' => array()
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(),
				// 2.3 Tri par défaut
				'order' => array()
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
					'Actioncandidat.name',
					'Partenaire.libstruc',
					'Personne.nom_complet',
					'Referent.nom_complet',
					'ActioncandidatPersonne.positionfiche',
					'ActioncandidatPersonne.datesignature',
					'Canton.canton',
					'/ActionscandidatsPersonnes/index/#ActioncandidatPersonne.personne_id#' => array( 'class' => 'view' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Adresse.numcom',
					'Adresse.nomcom',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array()
		)
	);

	/**
	 * Export CSV,  menu "Recherches"
	 */
	Configure::write(
		'ConfigurableQuery.ActionscandidatsPersonnes.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.ActionscandidatsPersonnes.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.ActionscandidatsPersonnes.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'ActioncandidatPersonne.datesignature',
					'Personne.nom_complet',
					'Dossier.matricule',
					'Referent.nom_complet',
					'Actioncandidat.name',
					'ActioncandidatPersonne.formationregion',
					'ActioncandidatPersonne.nomprestataire',
					'Progfichecandidature66.name',
					'Partenaire.libstruc',
					'ActioncandidatPersonne.positionfiche',
					'ActioncandidatPersonne.sortiele',
					'ActioncandidatPersonne.motifsortie_id',
					'Adresse.numcom',
					'Adresse.nomcom',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.ActionscandidatsPersonnes.search.ini_set' ),
		)
	);
	
	/**
	 * Cohorte
	 */
	Configure::write(
		'ConfigurableQuery.ActionscandidatsPersonnes.cohorte_enattente',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1'
					)
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				'skip' => array(),
				// 1.4 Filtres additionnels : La personne possède un(e)...
				'has' => array()
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(
					'ActioncandidatPersonne.positionfiche' => 'enattente'
				),
				// 2.3 Tri par défaut
				'order' => array('ActioncandidatPersonne.datesignature')
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
					'Dossier.matricule',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Actioncandidat.name',
					'Partenaire.libstruc',
					'Referent.nom_complet',
					'ActioncandidatPersonne.datesignature',
					'Canton.canton',
					'/ActionscandidatsPersonnes/index/#ActioncandidatPersonne.personne_id#' => array( 'class' => 'view' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array()
		)
	);

	/**
	 * Export CSV
	 */
	Configure::write(
		'ConfigurableQuery.ActionscandidatsPersonnes.exportcsv_enattente',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.ActionscandidatsPersonnes.cohorte_enattente.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.ActionscandidatsPersonnes.cohorte_enattente.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.matricule',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Actioncandidat.name',
					'Partenaire.libstruc',
					'Referent.nom_complet',
					'ActioncandidatPersonne.datesignature',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.ActionscandidatsPersonnes.cohorte_enattente.ini_set' ),
		)
	);
	
	/**
	 * Cohorte
	 */
	Configure::write(
		'ConfigurableQuery.ActionscandidatsPersonnes.cohorte_encours',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1'
					)
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				'skip' => array(),
				// 1.4 Filtres additionnels : La personne possède un(e)...
				'has' => array()
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(
					'ActioncandidatPersonne.positionfiche' => 'encours'
				),
				// 2.3 Tri par défaut
				'order' => array('ActioncandidatPersonne.datesignature')
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
					'Dossier.matricule',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Actioncandidat.name',
					'Partenaire.libstruc',
					'Referent.nom_complet',
					'ActioncandidatPersonne.datesignature',
					'ActioncandidatPersonne.bilanvenu',
					'ActioncandidatPersonne.bilanretenu',
					'Canton.canton',
					'/ActionscandidatsPersonnes/index/#ActioncandidatPersonne.personne_id#' => array( 'class' => 'view external' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array()
		)
	);

	/**
	 * Export CSV
	 */
	Configure::write(
		'ConfigurableQuery.ActionscandidatsPersonnes.exportcsv_encours',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.ActionscandidatsPersonnes.cohorte_encours.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.ActionscandidatsPersonnes.cohorte_encours.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.matricule',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Actioncandidat.name',
					'Partenaire.libstruc',
					'Referent.nom_complet',
					'ActioncandidatPersonne.datesignature',
					'ActioncandidatPersonne.bilanvenu',
					'ActioncandidatPersonne.bilanretenu',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.ActionscandidatsPersonnes.cohorte_encours.ini_set' ),
		)
	);
?>