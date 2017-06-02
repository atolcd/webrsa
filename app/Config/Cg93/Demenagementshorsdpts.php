<?php
	/**
	 * Menu "Recherches" > "Par allocataires sortants" > "Hors département (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Demenagementshorsdpts.search',
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
						'etatdosrsa' => array( '0', '2', '3', '4' )
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
				'header' => array(
					array( ' ' => array( 'colspan' => 2 ) ),
					array( 'Adresse de rang 01' => array( 'colspan' => 2 ) ),
					array( 'Adresse de rang 02' => array( 'colspan' => 2 ) ),
					array( 'Adresse de rang 03' => array( 'colspan' => 2 ) ),
					array( ' ' => array() ),
					array( ' ' => array( 'class' => 'action noprint' ) ),
				),
				// 5.2 Colonnes du tableau de résultats
				'fields' => array (
					'Dossier.matricule',
					'Personne.nom_complet',
					'Adressefoyer.dtemm',
					'Adresse.localite',
					'Adressefoyer2.dtemm' => array( 'type' => 'date' ),
					'Adresse2.localite',
					'Adressefoyer3.dtemm' => array( 'type' => 'date' ),
					'Adresse3.localite',
					'Dossier.locked' => array(
						'type' => 'boolean',
						'class' => 'dossier_locked'
					),
					'/Dossiers/view/#Dossier.id#' => array( 'class' => 'view' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array()
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array()
		)
	);

	/**
	 * Export CSV,  menu "Recherches" > "Par allocataires sortants" > "Hors département (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Demenagementshorsdpts.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Demenagementshorsdpts.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Demenagementshorsdpts.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.matricule',
					'Personne.nom_complet',
					'Adressefoyer.dtemm',
					'Adresse.localite',
					'Adressefoyer2.dtemm' => array( 'type' => 'date' ),
					'Adresse2.localite',
					'Adressefoyer3.dtemm' => array( 'type' => 'date' ),
					'Adresse3.localite',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Demenagementshorsdpts.search.ini_set' ),
		)
	);
	//--------------------------------------------------------------------------
	/**
	 * Valeurs des filtres de recherche par défaut pour la "Recherche par
	 * allocataires sortants, hors département"
	 *
	 * @deprecated since 3.0.00
	 *
	 * @var array
	 */
	Configure::write(
		'Filtresdefaut.Demenagementshorsdpts_search1',
		array(
			'Search' => array(
				'Dossier' => array(
					'dernier' => '1',
				),
				'Pagination' => array(
					'nombre_total' => '0'
				),
				'Situationdossierrsa' => array(
					'etatdosrsa_choice' => '1',
					'etatdosrsa' => array( '2', '3', '4' )
				)
			)
		)
	);
?>