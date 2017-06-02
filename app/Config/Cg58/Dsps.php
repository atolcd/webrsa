<?php
	/**
	 * Menu "Recherches" > "Par DSPs (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Dsps.search',
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
					'Personne.nom_complet_court',
					'Adresse.nomcom',
					'Dossier.matricule',
					'Donnees.libsecactdomi',
					'Donnees.libactdomi',
					'Donnees.libsecactrech',
					'Donnees.libemploirech',
					'/Dsps/view_revs/#DspRev.id#' => array(
						'class' => 'view',
						'condition' => 'trim("#DspRev.id#") !== ""'
					),
					'/Dsps/view/#Personne.id#' => array(
						'class' => 'view',
						'condition' => 'trim("#DspRev.id#") === ""'
					)
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Personne.dtnai',
					'Adresse.numcom',
					'Personne.nir',
					'Situationdossierrsa.etatdosrsa',
					'Donnees.nivetu',
					'Donnees.hispro',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array()
		)
	);

	/**
	 * Export CSV,  menu "Recherches" > "Par DSPs (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Dsps.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Dsps.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Dsps.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa', // N° Dossier
					'Dossier.matricule', // N° CAF
					'Situationdossierrsa.etatdosrsa', // Etat du droit
					'Personne.qual', // Qualité
					'Personne.nom', // Nom
					'Personne.prenom', // Prénom
					'Dossier.matricule', // N° CAF
					'Adresse.numvoie', // Numéro de voie
					'Adresse.libtypevoie', // Type de voie
					'Adresse.nomvoie', // Nom de voie
					'Adresse.complideadr', // Complément adresse 1
					'Adresse.compladr', // Complément adresse 2
					'Adresse.codepos', // Code postal
					'Adresse.nomcom', // Commune
					'Donnees.libsecactderact', // Secteur dernière activité
					'Donnees.libderact', // Dernière activité
					'Donnees.libsecactdomi', // Secteur dernière activité dominante
					'Donnees.libactdomi', // Dernière activité dominante
					'Donnees.libsecactrech', // Secteur activité recherché
					'Donnees.libemploirech', // Activité recherchée
					'Structurereferenteparcours.lib_struc', // Structure du parcours
					'Referentparcours.nom_complet', // Référent du parcours
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Dsps.search.ini_set' ),
		)
	);

	// -------------------------------------------------------------------------

	/**
	 * Liste des champs devant apparaître dans les résultats de la recherche par DSP:
	 *	- Dsps.index.fields contient les champs de chaque ligne du tableau de résultats
	 *	- Dsps.index.innerTable contient les champs de l'infobulle de chaque ligne du tableau de résultats
	 *	- Dsps.exportcsv contient les champs de chaque ligne du tableau à télécharger au format CSV
	 *
	 * Voir l'onglet "Environnement logiciel" > "WebRSA" > "Champs spécifiés dans
	 * le webrsa.inc" de la vérification de l'application.
	 *
	 * @deprecated since 3.0.00
	 */
	Configure::write(
		'Dsps',
		array(
			'index' => array(
				'fields' => array(
					'Personne.nom_complet_court',
					'Adresse.nomcom',
					'Dossier.matricule',
					'Donnees.libsecactdomi',
					'Donnees.libactdomi',
					'Donnees.libsecactrech',
					'Donnees.libemploirech'
				),
				'innerTable' => array(
					'Personne.dtnai',
					'Adresse.numcom',
					'Personne.nir',
					'Situationdossierrsa.etatdosrsa',
					'Donnees.nivetu',
					'Donnees.hispro',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			'exportcsv1' => array(
				'Dossier.numdemrsa', // N° Dossier
				'Dossier.matricule', // N° CAF
				'Situationdossierrsa.etatdosrsa', // Etat du droit
				'Personne.qual', // Qualité
				'Personne.nom', // Nom
				'Personne.prenom', // Prénom
				'Dossier.matricule', // N° CAF
				'Adresse.numvoie', // Numéro de voie
				'Adresse.libtypevoie', // Type de voie
				'Adresse.nomvoie', // Nom de voie
				'Adresse.complideadr', // Complément adresse 1
				'Adresse.compladr', // Complément adresse 2
				'Adresse.codepos', // Code postal
				'Adresse.nomcom', // Commune
				'Donnees.libsecactderact', // Secteur dernière activité
				'Donnees.libderact', // Dernière activité
				'Donnees.libsecactdomi', // Secteur dernière activité dominante
				'Donnees.libactdomi', // Dernière activité dominante
				'Donnees.libsecactrech', // Secteur activité recherché
				'Donnees.libemploirech', // Activité recherchée
				'Structurereferenteparcours.lib_struc', // Structure du parcours
				'Referentparcours.nom_complet', // Référent du parcours
			)
		)
	);
?>