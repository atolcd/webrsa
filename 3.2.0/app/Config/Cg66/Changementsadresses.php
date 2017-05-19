<?php
	/**
	 * Menu "Recherches"
	 */
	Configure::write(
		'ConfigurableQuery.Changementsadresses.search',
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
				'order' => array(
					'Adressefoyer.dtemm DESC',
					'Adressefoyer.id ASC',
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
					'Personne.nom_complet',
					'Dossier.matricule',
					'Adresse.complete',
					'Adressefoyer.dtemm',
					'Referentparcours.nom_complet',
					'Canton.canton',
					'/Dossiers/view/#Dossier.id#' => array('class' => 'view'),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array()
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array()
		)
	);

	/**
	 * Export CSV,  menu "Recherches"
	 */
	Configure::write(
		'ConfigurableQuery.Changementsadresses.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Changementsadresses.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Changementsadresses.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Personne.nom_complet',
					'Dossier.matricule',
					'Adresse.complete',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Changementsadresses.search.ini_set' ),
		)
	);
?>