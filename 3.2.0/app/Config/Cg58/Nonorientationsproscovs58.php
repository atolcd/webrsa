<?php
	/**
	 * Menu "Recherches" > "Par dossiers COV" > "Demandes de maintien dans le social"
	 */
	Configure::write(
		'ConfigurableQuery.Nonorientationsproscovs58.cohorte',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Contratinsertion' => array(
						'df_ci_from' => date_sql_to_cakephp( date( 'Y-m-d', strtotime( '-1 week' ) ) ),
						'df_ci_to' => date_sql_to_cakephp( date( 'Y-m-d', strtotime( 'now' ) ) )
					),
					'Pagination' => array(
						'nombre_total' => 0
					)
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				'skip' => array(
					'Calculdroitrsa.toppersdrodevorsa',
					'Dossier.dernier',
					'Situationdossierrsa.etatdosrsa'
				)
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(
					'Calculdroitrsa.toppersdrodevorsa' => '1',
					'Dossier.dernier' => '1',
					'Situationdossierrsa.etatdosrsa_choice' => '1',
					'Situationdossierrsa.etatdosrsa' => array( 'Z', 2, 3, 4 )
				),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(),
				// 2.3 Tri par défaut
				// TODO: ORDER BY ( DATE_PART( 'day', NOW() - "Contratinsertion"."df_ci" ) ) DESC
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
					'Personne.dtnai',
					'Adresse.codepos',
					'Foyer.enerreur' => array( 'sort' => false ),
					'Orientstruct.date_valid',
					'Contratinsertion.nbjours',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					'Referent.nom_complet',
					'/Orientsstructs/index/#Personne.id#'
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
	 * Export CSV,  menu "Recherches" > "Par dossiers COV" > "Demandes de maintien dans le social"
	 */
	Configure::write(
		'ConfigurableQuery.Nonorientationsproscovs58.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Nonorientationsproscovs58.cohorte.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Nonorientationsproscovs58.cohorte.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Adresse.nomcom',
					'Orientstruct.date_valid',
					'Contratinsertion.nbjours',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					'Referent.nom_complet',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Nonorientationsproscovs58.cohorte.ini_set' ),
		)
	);
?>