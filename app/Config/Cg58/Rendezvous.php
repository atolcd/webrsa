<?php
	/**
	 * Menu "Recherches" > "Par rendez-vous (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Rendezvous.search',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1'
					),
					'Rendezvous' => array(
						// Case à cocher "Filtrer par date de RDV"
						'daterdv' => '0',
						// Du (inclus)
						'daterdv_from' => date_sql_to_cakephp( date( 'Y-m-d', strtotime( '-1 week' ) ) ),
						// Au (inclus)
						'daterdv_to' => date_sql_to_cakephp( date( 'Y-m-d', strtotime( 'now' ) ) ),
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
				'order' => array( 'Rendezvous.daterdv' )
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
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Structurereferente.lib_struc',
					'Referent.nom_complet',
					'Typerdv.libelle',
					'Rendezvous.daterdv',
					'Rendezvous.heurerdv',
					'Statutrdv.libelle',
					// FIXME: caché dans le title, attention au thead
					/*'Dossier.numdemrsa' => array(
						'condition' => false
					),*/
					'/Rendezvous/index/#Rendezvous.personne_id#',
					'/Rendezvous/impression/#Rendezvous.id#'
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Personne.dtnai',
					'Adresse.numcom',
					'Personne.nir',
					'Prestation.rolepers',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array(
				'max_execution_time' => 0,
				'memory_limit' => '256M'
			)
		)
	);

	/**
	 * Export CSV,  menu "Recherches" > "Par rendez-vous (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Rendezvous.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Rendezvous.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Rendezvous.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Structurereferente.lib_struc',
					'Referent.nom_complet',
					'Typerdv.libelle',
					'Rendezvous.daterdv',
					'Rendezvous.heurerdv',
					'Statutrdv.libelle',
					'Personne.dtnai',
					'Adresse.numcom',
					'Personne.nir',
					'Prestation.rolepers',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Rendezvous.search.ini_set' ),
		)
	);
?>