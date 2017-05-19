<?php
	/**
	 * Menu "Recherches"
	 */
	Configure::write(
		'ConfigurableQuery.Traitementspcgs66.search',
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
					'Dossierpcg66.datereceptionpdo',
					'Dossierpcg66.user_id',
					'Traitementpcg66.typetraitement',
					'Traitementpcg66.created',
					'Situationpdo.libelle',
					'Traitementpcg66.descriptionpdo_id',
					'Traitementpcg66.datereception',
					'Traitementpcg66.dateecheance',
					'Traitementpcg66.clos',
					'Traitementpcg66.annule',
					'Fichiermodule.nb_fichiers_lies',
					'Dossier.locked',
					'Canton.canton',
					'/Traitementspcgs66/index/#Personnepcg66.personne_id#/#Dossierpcg66.id#' => array( 'class' => 'view' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Situationdossierrsa.etatdosrsa',
					'Personne.nomcomnai',
					'Personne.dtnai',
					'Adresse.numcom',
					'Personne.nir',
					'Dossier.matricule',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array()
		)
	);

	/**
	 * Export CSV,  menu "Recherches"
	 */
	Configure::write(
		'ConfigurableQuery.Traitementspcgs66.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Traitementspcgs66.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Traitementspcgs66.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'User.nom_complet',
					'Traitementpcg66.typetraitement',
					'Traitementpcg66.created',
					'Situationpdo.libelle',
					'Traitementpcg66.descriptionpdo_id',
					'Dossierpcg66.datereceptionpdo',
					'Traitementpcg66.datereception',
					'Traitementpcg66.dateecheance',
					'Traitementpcg66.clos',
					'Traitementpcg66.annule',
					'Fichiermodule.nb_fichiers_lies',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Traitementspcgs66.search.ini_set' ),
		)
	);
?>