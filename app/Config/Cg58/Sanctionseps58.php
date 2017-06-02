<?php
	/**
	 * Menu "Recherches" > "Par dossiers EP" > "Radiation de Pôle Emploi"
	 *
	 * @see les Configure::read() pour les conditions dans app/Model/Abstractclass/AbstractWebrsaCohorteSanctionep58.php
	 * qui pourraient éventuellement se trouver ici ?
	 */
	Configure::write(
		'ConfigurableQuery.Sanctionseps58.cohorte_radiespe',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				// INFO: pas besoin du restrict ci-dessous, des conditions plus générales se trouvent déjà dans: 'Dossierseps.conditionsSelection'
				'skip' => array(
					'Calculdroitrsa.toppersdrodevorsa',
					'Situationdossierrsa.etatdosrsa'
				)
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
			'auto' => true,
			// 5. Résultats de la recherche
			'results' => array(
				// 5.1 Ligne optionnelle supplémentaire d'en-tête du tableau de résultats
				'header' => array(),
				// 5.2 Colonnes du tableau de résultats
				'fields' => array (
					'Dossier.matricule',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Adresse.nomcom',
					'Historiqueetatpe.etat',
					'Historiqueetatpe.code',
					'Historiqueetatpe.motif',
					'Historiqueetatpe.date',
					'Structureorientante.lib_struc',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
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
	 * Export CSV,  menu "Recherches" > "Par dossiers EP" > "Radiation de Pôle Emploi"
	 */
	Configure::write(
		'ConfigurableQuery.Sanctionseps58.exportcsv_radiespe',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Sanctionseps58.cohorte_radiespe.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Sanctionseps58.cohorte_radiespe.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Adresse.nomcom',
					'Historiqueetatpe.etat',
					'Historiqueetatpe.code',
					'Historiqueetatpe.motif',
					'Historiqueetatpe.date',
					'Serviceinstructeur.lib_service',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Sanctionseps58.cohorte_radiespe.ini_set' ),
		)
	);

	/**
	 * Menu "Recherches" > "Par dossiers EP" > "Non inscription à Pôle Emploi"
	 *
	 * @see les Configure::read() pour les conditions dans app/Model/Abstractclass/AbstractWebrsaCohorteSanctionep58.php
	 * qui pourraient éventuellement se trouver ici ?
	 */
	Configure::write(
		'ConfigurableQuery.Sanctionseps58.cohorte_noninscritspe',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				// INFO: pas besoin du restrict ci-dessous, des conditions plus générales se trouvent déjà dans: 'Dossierseps.conditionsSelection'
				'skip' => array(
					'Calculdroitrsa.toppersdrodevorsa',
					'Situationdossierrsa.etatdosrsa'
				)
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
			'auto' => true,
			// 5. Résultats de la recherche
			'results' => array(
				// 5.1 Ligne optionnelle supplémentaire d'en-tête du tableau de résultats
				'header' => array(),
				// 5.2 Colonnes du tableau de résultats
				'fields' => array (
					'Dossier.matricule',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Adresse.nomcom',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					'Structureorientante.lib_struc',
					'Orientstruct.date_valid',
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
	 * Export CSV,  menu "Recherches" > "Par dossiers EP" > "Radiation de Pôle Emploi"
	 */
	Configure::write(
		'ConfigurableQuery.Sanctionseps58.exportcsv_noninscritspe',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Sanctionseps58.cohorte_noninscritspe.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Sanctionseps58.cohorte_noninscritspe.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Adresse.nomcom',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					'Orientstruct.date_valid',
					'Serviceinstructeur.lib_service',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Sanctionseps58.cohorte_noninscritspe.ini_set' ),
		)
	);
?>