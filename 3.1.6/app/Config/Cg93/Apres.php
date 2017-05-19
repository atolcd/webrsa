<?php
	/**
	 * Menu "APRE" > "Liste des demandes d'APRE" > "Toutes les APREs (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Apres.search',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Apre' => array(
						'statutapre' => 'C' // TODO: à ajouter dans les filtres
					),
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1'
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
				'fields' => array(
					'Dossier.numdemrsa',
					'Apre.numeroapre',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Apre.datedemandeapre',
					'Apre.natureaide' => array(
						'type' => 'list'
					),
					'Apre.typedemandeapre',
					'Apre.activitebeneficiaire',
					'Apre.etatdossierapre',
					'/Apres/index/#Apre.personne_id#' => array(
						'class' => 'view'
					),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Dossier.matricule',
					'Personne.dtnai',
					'Adresse.numcom' => array(
						'options' => array()
					),
					'Personne.nir',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array()
		)
	);

	/**
	 * Export CSV,  menu "APRE" > "Liste des demandes d'APRE" > "Toutes les APREs (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Apres.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Apres.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Apres.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Apre.datedemandeapre',
					'Apre.natureaide' => array(
						'type' => 'list'
					),
					'Apre.typedemandeapre',
					'Apre.activitebeneficiaire',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Apres.search.ini_set' ),
		)
	);

	//--------------------------------------------------------------------------
	/**
	 * Menu "APRE" > "Liste des demandes d'APRE" > "Eligibilité des APREs (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Apres.search_eligibilite',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Apre' => array(
						'statutapre' => 'C' // TODO: à ajouter dans les filtres
					),
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1'
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
				'fields' => array(
					'Dossier.numdemrsa',
					'Apre.numeroapre',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Apre.datedemandeapre',
					'Apre.eligibiliteapre',
					'Apre.etatdossierapre',
					'Relanceapre.daterelance',
					'Comiteapre.datecomite',
					'/Apres/index/#Apre.personne_id#' => array(
						'class' => 'view'
					),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Dossier.matricule',
					'Personne.dtnai',
					'Adresse.numcom' => array(
						'options' => array()
					),
					'Personne.nir',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array()
		)
	);

	/**
	 * Export CSV,  menu "APRE" > "Liste des demandes d'APRE" > "Eligibilité des APREs (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Apres.exportcsv_eligibilite',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Apres.search_eligibilite.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Apres.search_eligibilite.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Apre.datedemandeapre',
					'Apre.eligibiliteapre',
					'Apre.etatdossierapre',
					'Relanceapre.daterelance',
					'Comiteapre.datecomite',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Apres.search_eligibilite.ini_set' ),
		)
	);
?>