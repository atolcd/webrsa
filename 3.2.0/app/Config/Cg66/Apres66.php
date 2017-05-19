<?php
	/**
	 * Cohorte
	 */
	Configure::write(
		'ConfigurableQuery.Apres66.cohorte_validation',
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
				'skip' => array()
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(
					'Apre66.etatdossierapre' => 'COM',
					'Apre66.isdecision' => 'N',
				),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(),
				// 2.3 Tri par défaut
				'order' => array(
					'Personne.nom',
					'Personne.prenom'
				)
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
					'Apre66.numeroapre',
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Referentapre.nom_complet',
					'Aideapre66.datedemande',
					'Aideapre66.montantpropose',
					'Canton.canton',
					'/Apres66/index/#Personne.id#' => array( 'class' => 'view external' ),
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
		'ConfigurableQuery.Apres66.exportcsv_validation',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Apres66.cohorte_validation.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Apres66.cohorte_validation.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Apre66.numeroapre',
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Referentapre.nom_complet',
					'Apre66.datedemandeapre',
					'Aideapre66.montantpropose',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Apres66.cohorte_validation.ini_set' ),
		)
	);
	
	/**
	 * Cohorte
	 */
	Configure::write(
		'ConfigurableQuery.Apres66.cohorte_imprimer',
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
				'skip' => array()
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(
					'Apre66.etatdossierapre' => 'VAL',
				),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(
					'Apre66.datenotifapre IS NULL',
					'Typeaideapre66.isincohorte' => 'O'
				),
				// 2.3 Tri par défaut
				'order' => array(
					'Personne.nom',
					'Personne.prenom'
				)
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
					'Apre66.numeroapre',
					'Personne.nom_complet',
					'Referentapre.nom_complet',
					'Aideapre66.datedemande',
					'Aideapre66.decisionapre',
					'Aideapre66.montantaccorde',
					'Aideapre66.motifrejetequipe',
					'Aideapre66.datemontantaccorde',
					'Canton.canton',
					'/Apres66/index/#Personne.id#' => array( 'class' => 'view external' ),
					'/Apres66/notifications/#Apre66.id#' => array( 'class' => 'print' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Themeapre66.name',
					'Typeaideapre66.name',
					'Personne.dtnai',
					'Dossier.matricule',
					'Personne.nir',
					'Adresse.codepos',
					'Adresse.nomcom',
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
		'ConfigurableQuery.Apres66.exportcsv_imprimer',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Apres66.cohorte_imprimer.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Apres66.cohorte_imprimer.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Apre66.numeroapre',
					'Personne.nom_complet',
					'Referentapre.nom_complet',
					'Aideapre66.datedemande',
					'Aideapre66.decisionapre',
					'Aideapre66.montantaccorde',
					'Aideapre66.motifrejetequipe',
					'Aideapre66.datemontantaccorde',
					'Themeapre66.name',
					'Typeaideapre66.name',
					'Personne.dtnai',
					'Dossier.matricule',
					'Personne.nir',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Apres66.cohorte_imprimer.ini_set' ),
		)
	);
	
	/**
	 * Cohorte
	 */
	Configure::write(
		'ConfigurableQuery.Apres66.cohorte_notifiees',
		array(
			// 1. Filtres de recherche
			'filters' => Configure::read( 'ConfigurableQuery.Apres66.cohorte_imprimer.filters' ),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(
					'Apre66.etatdossierapre' => 'VAL',
				),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(
					'Apre66.datenotifapre IS NOT NULL',
					'Typeaideapre66.isincohorte' => 'O'
				),
				// 2.3 Tri par défaut
				'order' => array(
					'Personne.nom',
					'Personne.prenom'
				)
			),
			// 3. Nombre d'enregistrements par page
			'limit' => 10,
			// 4. Lancer la recherche au premier accès à la page ?
			'auto' => false,
			// 5. Résultats de la recherche
			'results' => Configure::read( 'ConfigurableQuery.Apres66.cohorte_imprimer.results' ),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Apres66.cohorte_imprimer.ini_set' ),
		)
	);

	/**
	 * Export CSV
	 */
	Configure::write(
		'ConfigurableQuery.Apres66.exportcsv_notifiees',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Apres66.exportcsv_imprimer.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Apres66.exportcsv_imprimer.query' ),
			// 3. Résultats de la recherche
			'results' => Configure::read( 'ConfigurableQuery.Apres66.exportcsv_imprimer.results' ),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Apres66.exportcsv_imprimer.ini_set' ),
		)
	);
	
	/**
	 * Cohorte
	 */
	Configure::write(
		'ConfigurableQuery.Apres66.cohorte_transfert',
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
				'skip' => array()
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(
					'Apre66.etatdossierapre' => 'VAL',
				),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(
					'Apre66.datenotifapre IS NOT NULL',
					'Apre66.istraite' => '0',
					'Apre66.istransfere' => '0',
					'Typeaideapre66.isincohorte' => 'O'
				),
				// 2.3 Tri par défaut
				'order' => array(
					'Personne.nom',
					'Personne.prenom'
				)
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
					'Apre66.numeroapre',
					'Personne.nom_complet',
					'Referentapre.nom_complet',
					'Aideapre66.datedemande',
					'Aideapre66.decisionapre',
					'Aideapre66.montantaccorde',
					'Aideapre66.motifrejetequipe',
					'Aideapre66.datemontantaccorde',
					'Canton.canton',
					'Apre66.nb_fichiers_lies' => array( 'class' => 'center ajax_refresh' ),
					'/Apres66/filelink/#Apre66.id#' => array( 'class' => 'external' ),
					'/Apres66/index/#Personne.id#' => array( 'class' => 'view external' ),
					'/Apres66/notifications/#Apre66.id#' => array( 'class' => 'print' ),
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
		'ConfigurableQuery.Apres66.exportcsv_transfert',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Apres66.cohorte_transfert.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Apres66.cohorte_transfert.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Apre66.numeroapre',
					'Personne.nom_complet',
					'Referentapre.nom_complet',
					'Aideapre66.datedemande',
					'Aideapre66.decisionapre',
					'Aideapre66.montantaccorde',
					'Aideapre66.motifrejetequipe',
					'Aideapre66.datemontantaccorde',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Apres66.cohorte_transfert.ini_set' ),
		)
	);
	
	/**
	 * Cohorte
	 */
	Configure::write(
		'ConfigurableQuery.Apres66.cohorte_traitement',
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
				'skip' => array()
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(
					'Apre66.etatdossierapre' => 'TRA',
				),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(
					'Apre66.datenotifapre IS NOT NULL',
					'Apre66.istraite' => '0',
					'Apre66.istransfere' => '1',
					'Typeaideapre66.isincohorte' => 'O'
				),
				// 2.3 Tri par défaut
				'order' => array(
					'Personne.nom',
					'Personne.prenom'
				)
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
					'Apre66.numeroapre',
					'Personne.nom_complet',
					'Referentapre.nom_complet',
					'Aideapre66.datedemande',
					'Aideapre66.decisionapre',
					'Aideapre66.montantaccorde',
					'Aideapre66.motifrejetequipe',
					'Aideapre66.datemontantaccorde',
					'Canton.canton',
					'/Apres66/index/#Personne.id#' => array( 'class' => 'view external' ),
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
		'ConfigurableQuery.Apres66.exportcsv_traitement',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Apres66.cohorte_traitement.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Apres66.cohorte_traitement.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Apre66.numeroapre',
					'Personne.nom_complet',
					'Referentapre.nom_complet',
					'Aideapre66.datedemande',
					'Aideapre66.decisionapre',
					'Aideapre66.montantaccorde',
					'Aideapre66.motifrejetequipe',
					'Aideapre66.datemontantaccorde',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Apres66.cohorte_traitement.ini_set' ),
		)
	);
?>