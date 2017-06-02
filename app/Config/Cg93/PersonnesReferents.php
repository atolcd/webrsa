<?php
	/**
	 * Menu "Cohortes" > "Orientation" > "Demandes non orientées"
	 */
	Configure::write(
		'ConfigurableQuery.PersonnesReferents.cohorte_affectation93',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						'dernier' => '1'
					),
					'Situationdossierrsa' => array(
						'etatdosrsa_choice' => '1',
						'etatdosrsa' => array( 2, 3, 4 )
					)
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				'skip' => array(
					'Detailcalculdroitrsa.natpf',
					'Detaildroitrsa.oridemrsa',
					'Dossier.anciennete_dispositif',
					'Serviceinstructeur.id',
					'Dossier.fonorg',
					'Foyer.sitfam',
					'Personne.sexe',
				),
				// 1.4 Filtres additionnels : La personne possède un(e)...
				'has' => array(
					'Dsp',
					'Contratinsertion'
				)
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(),
				// 2.3 Tri par défaut
				'order' => array(
					'Personne.situation' => 'ASC',
					'Orientstruct.date_valid ASC',
					'Personne.nom ASC',
					'Personne.prenom ASC',
				)
			),
			// 3. Nombre d'enregistrements par page
			'limit' => 10,
			// 4. Lancer la recherche au premier accès à la page ?
			'auto' => true,
			// 5. Résultats de la recherche
			'results' => array(
				// 5.1 Ligne optionnelle supplémentaire d'en-tête du tableau de résultats
				'header' => array(),
				// 5.2 Colonnes du tableau de résultats
				'fields' => array(
					'Adresse.nomcom',
					'Dossier.dtdemrsa',
					'Orientstruct.date_valid',
					'Personne.dtnai',
					'Calculdroitrsa.toppersdrodevorsa' => array(
						'type' => 'boolean'
					),
					'Personne.has_dsp' => array(
						'type' => 'boolean'
					),
					'Personne.nom_complet_court',
					'Contratinsertion.rg_ci',
					'Cer93.positioncer',
					'Contratinsertion.df_ci',
					'PersonneReferent.dddesignation',
					'Structurereferentepcd.lib_struc',
					'/PersonnesReferents/index/#Personne.id#' => array(
						'title' => false
					)
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
					'Personne.dtnai',
					'Dossier.matricule',
					'Personne.nir',
					'Adresse.codepos',
					'Situationdossierrsa.dtclorsa',
					'Situationdossierrsa.moticlorsa',
					'Prestation.rolepers',
					'Dsp.exists',
					'Adresse.complete' => array(
						'label' => 'Adresse'
					),
					'Contratinsertion.interne' => array(
						'label' => 'CER signé dans la structure'
					),
					'Personne.situation' => array(
						'label' => 'Situation allocataire'
					)
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array()
		)
	);

	/**
	 * Export CSV, mpenu "Cohortes" > "Orientation" > "Demandes non orientées"
	 */
	Configure::write(
		'ConfigurableQuery.PersonnesReferents.exportcsv_affectation93',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.PersonnesReferents.cohorte_affectation93.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.PersonnesReferents.cohorte_affectation93.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Adresse.nomcom',
					'Dossier.dtdemrsa',
					'Orientstruct.date_valid',
					'Personne.dtnai',
					'Calculdroitrsa.toppersdrodevorsa',
					'Dsp.exists' => array(
						'type' => 'boolean',
						'label' => 'Présence d\'une DSP'
					),
					'Personne.nom_complet_court',
					'Contratinsertion.rg_ci',
					'Cer93.positioncer',
					'Contratinsertion.df_ci',
					'PersonneReferent.dddesignation' => array(
						'label' => 'Date de début d\'affectation'
					),
					'Referent.nom_complet' => array(
						'label' => 'Affectation'
					),
					'Dossier.numdemrsa' => array(
						'label' => 'N° de dossier'
					),
					'Dossier.matricule',
					'Personne.nir',
					'Adresse.codepos',
					'Situationdossierrsa.dtclorsa',
					'Situationdossierrsa.moticlorsa',
					'Prestation.rolepers' => array(
						'label' => 'Rôle'
					),
					'Situationdossierrsa.etatdosrsa',
					'Adresse.complete',
					'Contratinsertion.interne' => array(
						'label' => 'CER signé dans la structure'
					)
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.PersonnesReferents.cohorte_affectation93.ini_set' ),
		)
	);
?>