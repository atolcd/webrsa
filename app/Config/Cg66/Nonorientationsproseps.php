<?php
	/**
	 * Menu "Recherches"
	 */
	Configure::write(
		'ConfigurableQuery.Nonorientationsproseps.search',
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
				'order' => array("(DATE_PART('day', NOW() - Contratinsertion.df_ci))" => 'DESC')
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
					'Personne.dtnai',
					'Adresse.codepos',
					'Foyer.enerreur' => array( 'type' => 'string', 'class' => 'foyer_enerreur' ),
					'Orientstruct.date_valid',
					'Contratinsertion.nbjours',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					'Referent.nom_complet',
					'Canton.canton',
					'/Rendezvous/index/#Personne.id#' => array( 'class' => 'view' ),
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
		'ConfigurableQuery.Nonorientationsproseps.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Orientsstructs.exportcsv.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Orientsstructs.exportcsv.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Personne.dtnai',
					'Adresse.codepos',
					'Foyer.enerreur' => array( 'type' => 'string', 'class' => 'foyer_enerreur' ),
					'Orientstruct.date_valid',
					'Contratinsertion.nbjours',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					'Referent.nom_complet',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Orientsstructs.exportcsv.ini_set' ),
		)
	);
?>