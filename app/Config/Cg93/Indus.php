<?php
	/**
	 * Menu "Recherches" > "Par indus"
	 */
	Configure::write(
		'ConfigurableQuery.Indus.search',
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
				'restrict' => array(
					'Situationdossierrsa.etatdosrsa_choice' => '1',
					'Situationdossierrsa.etatdosrsa' => array( 2, 3, 4 )
				),
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
					'Personne.nom_complet',
					'Dossier.typeparte',
					'Situationdossierrsa.etatdosrsa',
					'Indu.moismoucompta' => array( 'type' => 'date', 'format' => '%B %Y' ),
					'IndusConstates.mtmoucompta' => array( 'type' => 'float' ),
					'IndusTransferesCG.mtmoucompta' => array( 'type' => 'float' ),
					'RemisesIndus.mtmoucompta' => array( 'type' => 'float' ),
					'/Indus/view/#Dossier.id#' => array( 'class' => 'view' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Personne.dtnai',
					'Dossier.matricule',
					'Personne.nir',
					'Adresse.codepos',
					'Adresse.numcom',
					'Prestation.rolepers',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array()
		)
	);

	/**
	 * Export CSV,  menu "Recherches" > "Par indus"
	 */
	Configure::write(
		'ConfigurableQuery.Indus.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Indus.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Indus.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Adresse.numvoie',
					'Adresse.libtypevoie',
					'Adresse.nomvoie',
					'Adresse.complideadr',
					'Adresse.compladr',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Dossier.typeparte',
					'Situationdossierrsa.etatdosrsa',
					'Indu.moismoucompta' => array( 'type' => 'date', 'format' => '%B %Y' ),
					'IndusConstates.mtmoucompta' => array( 'type' => 'float' ),
					'IndusTransferesCG.mtmoucompta' => array( 'type' => 'float' ),
					'RemisesIndus.mtmoucompta' => array( 'type' => 'float' ),
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Indus.search.ini_set' ),
		)
	);
?>
