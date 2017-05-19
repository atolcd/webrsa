<?php
	/**
	 * Cohorte
	 */
	Configure::write(
		'ConfigurableQuery.Nonorientes66.cohorte_isemploi',
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
				'order' => array('Personne.id')
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
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Dossier.dtdemrsa',
					'Personne.nom_complet',
					'Situationdossierrsa.etatdosrsa',
					'Adresse.nomcom',
					'Foyer.enerreur',
					'Canton.canton',
					'/Dossiers/view/#Dossier.id#' => array( 'class' => 'view external' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// Configuration du formulaire de cohorte
			'cohorte' => array(
				// Remplacement des options dans la cohorte
				'options' => array(),
				// Valeurs à remplir dans les champs de la cohorte avant de les cacher
				'values' => array(
					'Orientstruct.typeorient_id' => 2, // Type d'orientation - Emploi - Pôle emploi
					'Orientstruct.structurereferente_id' => 23, // Structure référente - Pôle emploi
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
		'ConfigurableQuery.Nonorientes66.exportcsv_isemploi',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Nonorientes66.cohorte_isemploi.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Nonorientes66.cohorte_isemploi.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Dossier.dtdemrsa',
					'Personne.nom_complet',
					'Situationdossierrsa.etatdosrsa',
					'Adresse.nomcom',
					'Foyer.enerreur',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Nonorientes66.cohorte_isemploi.ini_set' ),
		)
	);
	
	/**
	 * Cohorte
	 */
	Configure::write(
		'ConfigurableQuery.Nonorientes66.cohorte_imprimeremploi',
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
				'order' => array('Personne.id')
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
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Dossier.dtdemrsa',
					'Personne.nom_complet',
					'Situationdossierrsa.etatdosrsa',
					'Adresse.nomcom',
					'Historiqueetatpe.etat',
					'Foyer.nbenfants',
					'Foyer.enerreur',
					'Canton.canton',
					'/Dossiers/view/#Dossier.id#' => array( 'class' => 'view external' ),
					'/Nonorientes66/imprimeremploi/#Personne.id#' => array( 'class' => 'print imprimer' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
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
		'ConfigurableQuery.Nonorientes66.exportcsv_imprimeremploi',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Nonorientes66.cohorte_imprimeremploi.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Nonorientes66.cohorte_imprimeremploi.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Dossier.dtdemrsa',
					'Personne.nom_complet',
					'Situationdossierrsa.etatdosrsa',
					'Adresse.nomcom',
					'Historiqueetatpe.etat',
					'Foyer.nbenfants',
					'Foyer.enerreur',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Nonorientes66.cohorte_imprimeremploi.ini_set' ),
		)
	);
	
	/**
	 * Cohorte
	 */
	Configure::write(
		'ConfigurableQuery.Nonorientes66.cohorte_reponse',
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
				'order' => array('Personne.id')
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
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Dossier.dtdemrsa',
					'Personne.nom_complet',
					'Situationdossierrsa.etatdosrsa',
					'Adresse.nomcom',
					'Nonoriente66.dateimpression',
					'Foyer.nbenfants',
					'Foyer.enerreur',
					'Canton.canton',
					'/Dossiers/view/#Dossier.id#' => array( 'class' => 'view external' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
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
		'ConfigurableQuery.Nonorientes66.exportcsv_reponse',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Nonorientes66.cohorte_reponse.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Nonorientes66.cohorte_reponse.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Dossier.dtdemrsa',
					'Personne.nom_complet',
					'Situationdossierrsa.etatdosrsa',
					'Adresse.nomcom',
					'Nonoriente66.dateimpression',
					'Foyer.nbenfants',
					'Foyer.enerreur',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Nonorientes66.cohorte_reponse.ini_set' ),
		)
	);
	
	/**
	 * Cohorte
	 */
	Configure::write(
		'ConfigurableQuery.Nonorientes66.cohorte_imprimernotifications',
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
				'order' => array('Personne.id')
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
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Dossier.dtdemrsa',
					'Personne.nom_complet',
					'Situationdossierrsa.etatdosrsa',
					'Adresse.nomcom',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					'Foyer.enerreur',
					'Canton.canton',
					'/Dossiers/view/#Dossier.id#' => array( 'class' => 'view external' ),
					'/Nonorientes66/imprimernotifications/#Orientstruct.id#' => array( 'class' => 'print imprimer' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
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
		'ConfigurableQuery.Nonorientes66.exportcsv_imprimernotifications',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Nonorientes66.cohorte_imprimernotifications.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Nonorientes66.cohorte_imprimernotifications.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Dossier.dtdemrsa',
					'Personne.nom_complet',
					'Situationdossierrsa.etatdosrsa',
					'Adresse.nomcom',
					'Historiqueetatpe.etat',
					'Foyer.nbenfants',
					'Foyer.enerreur',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Nonorientes66.cohorte_imprimernotifications.ini_set' ),
		)
	);
	
	/**
	 * Cohorte
	 */
	Configure::write(
		'ConfigurableQuery.Nonorientes66.recherche_notifie',
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
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Dossier.dtdemrsa',
					'Personne.nom_complet',
					'Situationdossierrsa.etatdosrsa',
					'Adresse.nomcom',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					'Foyer.enerreur',
					'Orientstruct.nbfichier_lies',
					'Canton.canton',
					'/Dossiers/view/#Dossier.id#' => array( 'class' => 'view external' ),
					'/Nonorientes66/imprimernotifications/#Orientstruct.id#' => array( 'class' => 'print imprimer' ),
					'/Orientsstructs/filelink/#Orientstruct.id#' => array( 'class' => 'external' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
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
		'ConfigurableQuery.Nonorientes66.exportcsv_notifie',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Nonorientes66.recherche_notifie.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Nonorientes66.recherche_notifie.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Dossier.dtdemrsa',
					'Personne.nom_complet',
					'Situationdossierrsa.etatdosrsa',
					'Adresse.nomcom',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					'Foyer.enerreur',
					'Orientstruct.nbfichier_lies',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Nonorientes66.recherche_notifie.ini_set' ),
		)
	);
?>