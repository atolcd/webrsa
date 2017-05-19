<?php
	/**
	 * Menu "Recherches"
	 */
	Configure::write(
		'ConfigurableQuery.Defautsinsertionseps66.search_noninscrits',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1'
					),
					'Situationdossierrsa' => array(
						'etatdosrsa' => array('Z', '2', '3', '4')
					)
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(
					'Situationdossierrsa.etatdosrsa' => array('Z', '2', '3', '4')
				),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				'skip' => array(),
				// 1.4 Filtres additionnels : La personne possède un(e)...
				'has' => array()
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(
					'Situationdossierrsa.etatdosrsa_choice' => '1',
					'Situationdossierrsa.etatdosrsa' => array('Z', '2', '3', '4')
				),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(),
				// 2.3 Tri par défaut
				'order' => array('Orientstruct.date_valid')
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
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Orientstruct.date_valid',
					'Foyer.enerreur' => array( 'type' => 'string', 'class' => 'foyer_enerreur' ),
					'Situationdossierrsa.etatdosrsa',
					'Canton.canton',
					'/Bilansparcours66/add/#Personne.id#/Bilanparcours66__examenauditionpe:noninscriptionpe' => array( 'class' => 'add external' ),
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
		'ConfigurableQuery.Defautsinsertionseps66.exportcsv_noninscrits',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Defautsinsertionseps66.search_noninscrits.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Defautsinsertionseps66.search_noninscrits.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Orientstruct.date_valid',
					'Foyer.enerreur' => array( 'type' => 'string', 'class' => 'foyer_enerreur' ),
					'Situationdossierrsa.etatdosrsa',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Defautsinsertionseps66.search_noninscrits.ini_set' ),
		)
	);

	/**
	 * menu "Recherches"
	 */
	Configure::write(
		'ConfigurableQuery.Defautsinsertionseps66.search_radies',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Defautsinsertionseps66.search_noninscrits.filters' ),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(
					'Situationdossierrsa.etatdosrsa_choice' => '1',
					'Situationdossierrsa.etatdosrsa' => array('Z', '2', '3', '4')
				),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(),
				// 2.3 Tri par défaut
				'order' => array('Historiqueetatpe.date', 'Historiqueetatpe.id')
			),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Orientstruct.date_valid',
					'Foyer.enerreur' => array( 'type' => 'string', 'class' => 'foyer_enerreur' ),
					'Situationdossierrsa.etatdosrsa',
					'Canton.canton',
					'/Bilansparcours66/add/#Personne.id#/Bilanparcours66__examenauditionpe:radiationpe' => array( 'class' => 'add external' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Defautsinsertionseps66.search_noninscrits.ini_set' ),
		)
	);
	
	/**
	 * Export CSV
	 */
	Configure::write(
		'ConfigurableQuery.Defautsinsertionseps66.exportcsv_radies',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Defautsinsertionseps66.search_radies.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Defautsinsertionseps66.search_radies.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Orientstruct.date_valid',
					'Foyer.enerreur' => array( 'type' => 'string', 'class' => 'foyer_enerreur' ),
					'Situationdossierrsa.etatdosrsa',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Defautsinsertionseps66.search_radies.ini_set' ),
		)
	);
?>