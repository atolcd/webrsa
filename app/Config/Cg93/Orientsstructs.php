<?php
	/**
	 * Menu "Recherches" > "Par orientation"
	 */
	Configure::write(
		'ConfigurableQuery.Orientsstructs.search',
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
					'Situationdossierrsa' => array(
						'etatdosrsa_choice' => '0',
						'etatdosrsa' => array( '0','2', '3', '4' )
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
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Dossier.dtdemrsa',
					'Orientstruct.date_valid',
					'Orientstruct.propo_algo' => array( 'type' => 'string' ),
					'Orientstruct.origine',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					'Orientstruct.statut_orient',
					'Calculdroitrsa.toppersdrodevorsa' => array( 'type' => 'boolean' ),
					'/Orientsstructs/index/#Orientstruct.personne_id#' => array(
						'disabled' => "( '#Orientstruct.horszone#' == true )",
						'class' => 'view'
					),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Situationdossierrsa.etatdosrsa',
					'Personne.nomcomnai',
					'Personne.dtnai',
					'Adresse.numcom',
					'Personne.nir',
					'Historiqueetatpe.identifiantpe',
					'Modecontact.numtel',
					'Prestation.rolepers',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array(
				'max_execution_time' => 0,
				'memory_limit' => '1024M'
			)
		)
	);

	/**
	 * Export CSV,  menu "Recherches" > "Par orientation"
	 */
	Configure::write(
		'ConfigurableQuery.Orientsstructs.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Orientsstructs.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Orientsstructs.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Personne.nir',
					'Personne.dtnai',
					'Dossier.matricule',
					'Historiqueetatpe.identifiantpe',
					'Modecontact.numtel',
					'Adresse.numvoie',
					'Adresse.libtypevoie',
					'Adresse.nomvoie',
					'Adresse.complideadr',
					'Adresse.compladr',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Dossier.dtdemrsa',
					'Situationdossierrsa.etatdosrsa',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Orientstruct.origine',
					'Orientstruct.date_valid',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					'Orientstruct.statut_orient',
					'Calculdroitrsa.toppersdrodevorsa',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Orientsstructs.search.ini_set' ),
		)
	);

	/**
	 * Menu "Cohortes" > "Orientation" > "Demandes non orientées"
	 */
	Configure::write(
		'ConfigurableQuery.Orientsstructs.cohorte_nouvelles',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1'
					),
					'Detailcalculdroitrsa' => array(
						'natpf_choice' => '1',
						'natpf' => array( 'RSD', 'RSI' )
					),
					'Detaildroitrsa' => array(
						'oridemrsa_choice' => '1',
						'oridemrsa' => array( 'DEM' )
					),
					'Situationdossierrsa' => array(
						'etatdosrsa_choice' => '1',
						'etatdosrsa' => array( 2, 3, 4 )
					)
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(
					'Situationdossierrsa.etatdosrsa' => array( 2, 3, 4 ),
					'Detailcalculdroitrsa.natpf' => array( 'RSD', 'RSI', 'RSU', 'RSJ' )
				),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				'skip' => array(
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Dossier.anciennete_dispositif',
					'Serviceinstructeur.id',
					'Dossier.fonorg',
					'Foyer.sitfam',
					'Personne.dtnai',
					'Personne.nomnai',
					'Personne.nir',
					'Personne.sexe',
					'Personne.trancheage'
				)
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(
					'Situationdossierrsa.etatdosrsa_choice' => '1',
					'Situationdossierrsa.etatdosrsa' => array( 2, 3, 4 ),
					'Detailcalculdroitrsa.natpf_choice' => '1',
					'Detailcalculdroitrsa.natpf' => array( 'RSD', 'RSI', 'RSU', 'RSJ' )
				),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(),
				// 2.3 Tri par défaut
				'order' => array( 'Dossier.dtdemrsa' )
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
					'Adresse.nomcom' => array(
						'sort' => false
					),
					'Dossier.dtdemrsa' => array(
						'sort' => false
					),
					'Personne.has_dsp' => array(
						'sort' => false,
						'type' => 'boolean'
					),
					'Personne.nom_complet_court' => array(
						'sort' => false
					),
					'Suiviinstruction.typeserins' => array(
						'sort' => false
					),
					'Orientstruct.propo_algo' => array(
						'sort' => false
					),
					'Dossier.statut' => array(
						'sort' => false
					),
					'/Dossiers/view/#Dossier.id#' => array(
						'class' => 'external'
					)
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
					'Personne.dtnai',
					'Dossier.matricule',
					'Personne.nir',
					'Adresse.codepos',
					'Situationdossierrsa.dtclorsa',
					'Situationdossierrsa.moticlorsa',
					'Prestation.rolepers',
					'Situationdossierrsa.etatdosrsa',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array(
				'max_execution_time' => 0,
				'memory_limit' => '1024M'
			)
		)
	);

	/**
	 * Menu "Cohortes" > "Orientation" > "Demandes non orientées"
	 */
	Configure::write(
		'ConfigurableQuery.Orientsstructs.cohorte_enattente',
		Configure::read( 'ConfigurableQuery.Orientsstructs.cohorte_nouvelles' )
	);

	/**
	 * Menu "Cohortes" > "Orientation" > "Demandes orientées"
	 */
	Configure::write(
		'ConfigurableQuery.Orientsstructs.cohorte_orientees',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Detailcalculdroitrsa' => array(
						'natpf_choice' => '1',
						'natpf' => array( 'RSD', 'RSI' )
					),
					'Detaildroitrsa' => array(
						'oridemrsa_choice' => '1',
						'oridemrsa' => array( 'DEM' )
					),
					'Situationdossierrsa' => array(
						'etatdosrsa_choice' => '1',
						'etatdosrsa' => array( 2, 3, 4 )
					)
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(
					'Situationdossierrsa.etatdosrsa' => array( 2, 3, 4 ),
					'Detailcalculdroitrsa.natpf' => array( 'RSD', 'RSI', 'RSU', 'RSJ' )
				),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				'skip' => array(
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Dossier.anciennete_dispositif',
					'Serviceinstructeur.id',
					'Dossier.fonorg',
					'Foyer.sitfam',
					'Personne.dtnai',
					'Personne.nomnai',
					'Personne.nir',
					'Personne.sexe',
					'Personne.trancheage'
				),
				// 1.4 Filtres additionnels : La personne possède un(e)...
				'has' => array( 'Dsp' )
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(
					'Situationdossierrsa.etatdosrsa_choice' => '1',
					'Situationdossierrsa.etatdosrsa' => array( 2, 3, 4 ),
					'Detailcalculdroitrsa.natpf_choice' => '1',
					'Detailcalculdroitrsa.natpf' => array( 'RSD', 'RSI', 'RSU', 'RSJ' )
				),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(),
				// 2.3 Tri par défaut
				'order' => array( 'Dossier.dtdemrsa' )
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
					'Adresse.nomcom',
					'Personne.nom_complet_court',
					'Dossier.dtdemrsa',
					'Personne.has_dsp' => array(
						'type' => 'boolean'
					),
					'Suiviinstruction.typeserins',
					'Orientstruct.origine',
					'Orientstruct.propo_algo',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					'Orientstruct.statut_orient',
					'Orientstruct.date_propo',
					'Orientstruct.date_valid',
					'/Orientsstructs/impression/#Orientstruct.id#' => array(
						'class' => 'external'
					)
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
					'Personne.dtnai',
					'Dossier.matricule',
					'Personne.nir',
					'Adresse.codepos',
					'Situationdossierrsa.dtclorsa',
					'Situationdossierrsa.moticlorsa',
					'Prestation.rolepers',
					'Situationdossierrsa.etatdosrsa',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array(
				'max_execution_time' => 0,
				'memory_limit' => '1024M'
			)
		)
	);
?>