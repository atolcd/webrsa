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
					),
					// Obtenir le nombre total de résultats
					'Pagination' => array(
						'nombre_total' => '1'
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
					'Contratinsertion.created',
					'Contratinsertion.rg_ci',
					'Contratinsertion.decision_ci',
					'Contratinsertion.datevalidation_ci',
					'Contratinsertion.forme_ci',
					'Contratinsertion.positioncer',
					'Contratinsertion.df_ci',
					'Canton.canton',
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
					'Referentparcours.nom_complet'
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
						'Contratinsertion.df_ci' => array( 'type' => 'date' ),
						'Contratinsertion.decision_ci',
						'Contratinsertion.datevalidation_ci' => array( 'type' => 'date' ),
						'Structurereferenteparcours.lib_struc',
						'Referentparcours.nom_complet',
						'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Contratsinsertion.search.ini_set' ),
		)
	);

	/**
	 * Cohorte
	 */
	Configure::write(
		'ConfigurableQuery.Contratsinsertion.cohorte_cersimpleavalider',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1',
					),
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
				'conditions' => array(
					'Contratinsertion.forme_ci' => 'S',
					'OR' => array(
						'Contratinsertion.decision_ci IS NULL',
						'Contratinsertion.decision_ci' => 'E',
					)
				),
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
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Referent.nom_complet',
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci',
					'Canton.canton',
					'/Contratsinsertion/index/#Contratinsertion.personne_id#' => array( 'class' => 'view external' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Personne.dtnai',
					'Dossier.matricule',
					'Personne.nir',
					'Adresse.codepos',
					'Adresse.numcom',
					'Contratinsertion.positioncer',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
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
	 * Export CSV
	 */
	Configure::write(
		'ConfigurableQuery.Contratsinsertion.exportcsv_cersimpleavalider',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Contratsinsertion.cohorte_cersimpleavalider.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Contratsinsertion.cohorte_cersimpleavalider.query' ),
			// 3. Résultats de la recherche
			'results' => array(
					'fields' => array(
						'Dossier.numdemrsa',
						'Personne.nom_complet',
						'Adresse.nomcom',
						'Referent.nom_complet',
						'Contratinsertion.dd_ci',
						'Contratinsertion.df_ci',
						'Personne.dtnai',
						'Dossier.matricule',
						'Personne.nir',
						'Adresse.codepos',
						'Adresse.numcom',
						'Contratinsertion.positioncer',
						'Structurereferenteparcours.lib_struc',
						'Referentparcours.nom_complet',
						'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Contratsinsertion.cohorte_cersimpleavalider.ini_set' ),
		)
	);

	/**
	 * Cohorte
	 */
	Configure::write(
		'ConfigurableQuery.Contratsinsertion.cohorte_cerparticulieravalider',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1',
					),
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
				'conditions' => array(
					'Contratinsertion.forme_ci' => 'C',
					'OR' => array(
						'Contratinsertion.decision_ci IS NULL',
						'Contratinsertion.decision_ci' => 'E',
					)
				),
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
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Referent.nom_complet',
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci',
					'Canton.canton',
					'/Contratsinsertion/index/#Contratinsertion.personne_id#' => array( 'class' => 'view external' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Personne.dtnai',
					'Dossier.matricule',
					'Personne.nir',
					'Adresse.codepos',
					'Adresse.numcom',
					'Contratinsertion.positioncer',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
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
	 * Export CSV
	 */
	Configure::write(
		'ConfigurableQuery.Contratsinsertion.exportcsv_cerparticulieravalider',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Contratsinsertion.cohorte_cerparticulieravalider.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Contratsinsertion.cohorte_cerparticulieravalider.query' ),
			// 3. Résultats de la recherche
			'results' => array(
					'fields' => array(
						'Dossier.numdemrsa',
						'Personne.nom_complet',
						'Adresse.nomcom',
						'Referent.nom_complet',
						'Contratinsertion.dd_ci',
						'Contratinsertion.df_ci',
						'Personne.dtnai',
						'Dossier.matricule',
						'Personne.nir',
						'Adresse.codepos',
						'Adresse.numcom',
						'Contratinsertion.positioncer',
						'Structurereferenteparcours.lib_struc',
						'Referentparcours.nom_complet',
						'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Contratsinsertion.cohorte_cerparticulieravalider.ini_set' ),
		)
	);

	/**
	 * Cohorte
	 */
	Configure::write(
		'ConfigurableQuery.Contratsinsertion.search_valides',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1',
					),
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
				'conditions' => array(
					'Contratinsertion.decision_ci IS NOT NULL',
					'Contratinsertion.decision_ci !=' => 'E',
				),
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
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci',
					'Contratinsertion.decision_ci',
					'Contratinsertion.datevalidation_ci',
					'Contratinsertion.observ_ci',
					'Contratinsertion.forme_ci',
					'Contratinsertion.positioncer',
					'Canton.canton',
					'/Contratsinsertion/index/#Contratinsertion.personne_id#' => array( 'class' => 'view external' ),
					'/Contratsinsertion/ficheliaisoncer/#Contratinsertion.id#' => array( 
						'class' => 'print', 
						'id' => 'ficheliaisoncer_#Contratinsertion.id#', 
						'positioncer' => '#Contratinsertion.positioncer#',
						'decision_ci' => '#Contratinsertion.decision_ci#',
					),
					'/Contratsinsertion/notifbenef/#Contratinsertion.id#' => array( 
						'class' => 'print',
						'id' => 'notifbenef_#Contratinsertion.id#', 
					),
					'/Contratsinsertion/notificationsop/#Contratinsertion.id#' => array( 
						'class' => 'print',
						'id' => 'notificationsop_#Contratinsertion.id#',
					),
					'/Contratsinsertion/impression/#Contratinsertion.id#' => array( 
						'class' => 'print',
						'id' => 'impression_#Contratinsertion.id#',
					),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Personne.dtnai',
					'Dossier.matricule',
					'Personne.nir',
					'Adresse.codepos',
					'Adresse.numcom',
					'Prestation.rolepers',
					'Situationdossierrsa.etatdosrsa',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
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
	 * Export CSV
	 */
	Configure::write(
		'ConfigurableQuery.Contratsinsertion.exportcsv_search_valides',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Contratsinsertion.search_valides.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Contratsinsertion.search_valides.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Referent.nom_complet',
					'Structurereferente.lib_struc',
					'Typocontrat.lib_typo',
					'Contratinsertion.dd_ci',
					'Contratinsertion.duree_engag',
					'Contratinsertion.df_ci',
					'Contratinsertion.decision_ci',
					'Contratinsertion.datevalidation_ci',
					'Contratinsertion.current_action',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Contratsinsertion.search_valides.ini_set' ),
		)
	);
	
	
?>