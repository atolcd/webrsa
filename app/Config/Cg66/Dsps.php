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
					'Dossier.matricule',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Adresse.nomcom',
					'Canton.canton',
					'Donnees.toppermicondub', // Permis de conduire Cat B
					'Donnees.topmoyloco', // Moyen de transport Coll. Ou IndiV.
					'Donnees.difdisp' => array( // Obstacles à une recherche d'emploi
						'type' => 'list'
					),
					'Donnees.nivetu', // Niveau d'étude
					'Donnees.nivdipmaxobt', // Diplomes le plus élevé
					'Donnees.topengdemarechemploi', // Disponibilité à la recherche d'emploi
					'Actrechromev3.familleromev3', // Code Famille de l'emploi recherché
					'Actrechromev3.domaineromev3', // Code Domaine de l'emploi recherché
					'Actrechromev3.metierromev3', // Code Emploi de l'emploi recherché
					'Actrechromev3.appellationromev3', // Appellattion de l'emploi recherché (rome V3)
					'Libemploirech66Metier.name', // Emploi recherché (rome V2)
					'Deractromev3.appellationromev3', // Appellattion de la derniere activité (rome V3)
					'Libsecactrech66Secteur.name', // Le secteur d'activité recherché (rome v2)
					'Libderact66Metier.name', // La derniere activité (rome V2)
					'Donnees.libautrqualipro', // Qualification ou certificats professionnels
					'Donnees.nb_fichiers_lies', // Nb Fichiers Liés des dsp
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
					'Situationdossierrsa.etatdosrsa', // Position du droit
					'Calculdroitrsa.toppersdrodevorsa', // Soumis à Droit et Devoir
					'Foyer.sitfam', // Situation de famille
					'Foyer.nbenfants', // Nbre d'enfants
					'Personne.numfixe', // N° téléphone fixe
					'Personne.numport', // N° téléphone portable
					'Referentparcours.nom_complet',// Nom du référent
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
					'Dossier.matricule',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Situationdossierrsa.etatdosrsa', // Position du droit
					'Calculdroitrsa.toppersdrodevorsa', // Soumis à Droit et Devoir
					'Foyer.sitfam', // Situation de famille
					'Foyer.nbenfants', // Nbre d'enfants
					'Personne.numfixe', // N° téléphone fixe
					'Personne.numport', // N° téléphone portable
					'Referentparcours.nom_complet',// Nom du référent
					'Adresse.nomcom',
					'Canton.canton',
					'Donnees.toppermicondub', // Permis de conduire Cat B
					'Donnees.topmoyloco', // Moyen de transport Coll. Ou IndiV.
					'Donnees.difdisp' => array( // Obstacles à une recherche d'emploi
						'type' => 'list'
					),
					'Donnees.nivetu', // Niveau d'étude
					'Donnees.nivdipmaxobt', // Diplomes le plus élevé
					'Donnees.topengdemarechemploi', // Disponibilité à la recherche d'emploi
					'Actrechromev3.familleromev3', // Code Famille de l'emploi recherché
					'Actrechromev3.domaineromev3', // Code Domaine de l'emploi recherché
					'Actrechromev3.metierromev3', // Code Emploi de l'emploi recherché
					'Actrechromev3.appellationromev3', // Appellattion de l'emploi recherché (rome V3)
					'Libemploirech66Metier.name', // Emploi recherché (rome V2)
					'Deractromev3.appellationromev3', // Appellattion de la derniere activité (rome V3)
					'Libsecactrech66Secteur.name', // Le secteur d'activité recherché (rome v2)
					'Libderact66Metier.name', // La derniere activité (rome V2)
					'Donnees.libautrqualipro', // Qualification ou certificats professionnels
					'Donnees.nb_fichiers_lies', // Nb Fichiers Liés des dsp
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Dsps.search.ini_set' ),
		)
	);

	// -------------------------------------------------------------------------

	/**
	 * Liste des champs devant apparaître dans les résultats de la recherche par DSP:
	 * 	- Dsps.index.fields contient les champs de chaque ligne du tableau de résultats
	 * 	- Dsps.index.innerTable contient les champs de l'infobulle de chaque ligne du tableau de résultats
	 * 	- Dsps.exportcsv contient les champs de chaque ligne du tableau à télécharger au format CSV
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
					'Dossier.matricule',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Adresse.nomcom',
					'Canton.canton',
					'Donnees.toppermicondub', // Permis de conduire Cat B
					'Donnees.topmoyloco', // Moyen de transport Coll. Ou IndiV.
					'Donnees.difdisp', // Obstacles à une recherche d'emploi
					'Donnees.nivetu', // Niveau d'étude
					'Donnees.nivdipmaxobt', // Diplomes le plus élevé
					'Donnees.topengdemarechemploi', // Disponibilité à la recherche d'emploi
					'Actrechromev3.familleromev3', // Code Famille de l'emploi recherché
					'Actrechromev3.domaineromev3', // Code Domaine de l'emploi recherché
					'Actrechromev3.metierromev3', // Code Emploi de l'emploi recherché
					'Actrechromev3.appellationromev3', // Appellattion de l'emploi recherché (rome V3)
					'Libemploirech66Metier.name', // Emploi recherché (rome V2)
					'Deractromev3.appellationromev3', // Appellattion de la derniere activité (rome V3)
					'Libsecactrech66Secteur.name', // Le secteur d'activité recherché (rome v2)
					'Libderact66Metier.name', // La derniere activité (rome V2)
					'Donnees.libautrqualipro', // Qualification ou certificats professionnels
					'Donnees.nb_fichiers_lies', // Nb Fichiers Liés des dsp
				),
				'innerTable' => array(
					'Situationdossierrsa.etatdosrsa', // Position du droit
					'Calculdroitrsa.toppersdrodevorsa', // Soumis à Droit et Devoir
					'Foyer.sitfam', // Situation de famille
					'Foyer.nbenfants', // Nbre d'enfants
					'Personne.numfixe', // N° téléphone fixe
					'Personne.numport', // N° téléphone portable
					'Referentparcours.nom_complet',// Nom du référent
				)
			),
			'exportcsv1' => array(
				'Dossier.matricule',
				'Personne.nom',
				'Personne.prenom',
				'Personne.dtnai',
				'Situationdossierrsa.etatdosrsa', // Position du droit
				'Calculdroitrsa.toppersdrodevorsa', // Soumis à Droit et Devoir
				'Foyer.sitfam', // Situation de famille
				'Foyer.nbenfants', // Nbre d'enfants
				'Personne.numfixe', // N° téléphone fixe
				'Personne.numport', // N° téléphone portable
				'Referentparcours.nom_complet',// Nom du référent
				'Adresse.nomcom',
				'Canton.canton',
				'Donnees.toppermicondub', // Permis de conduire Cat B
				'Donnees.topmoyloco', // Moyen de transport Coll. Ou IndiV.
				'Donnees.difdisp', // Obstacles à une recherche d'emploi
				'Donnees.nivetu', // Niveau d'étude
				'Donnees.nivdipmaxobt', // Diplomes le plus élevé
				'Donnees.topengdemarechemploi', // Disponibilité à la recherche d'emploi
				'Actrechromev3.familleromev3', // Code Famille de l'emploi recherché
				'Actrechromev3.domaineromev3', // Code Domaine de l'emploi recherché
				'Actrechromev3.metierromev3', // Code Emploi de l'emploi recherché
				'Actrechromev3.appellationromev3', // Appellattion de l'emploi recherché (rome V3)
				'Libemploirech66Metier.name', // Emploi recherché (rome V2)
				'Deractromev3.appellationromev3', // Appellattion de la derniere activité (rome V3)
				'Libsecactrech66Secteur.name', // Le secteur d'activité recherché (rome v2)
				'Libderact66Metier.name', // La derniere activité (rome V2)
				'Donnees.libautrqualipro', // Qualification ou certificats professionnels
				'Donnees.nb_fichiers_lies', // Nb Fichiers Liés des dsp
			)
		)
	);
?>