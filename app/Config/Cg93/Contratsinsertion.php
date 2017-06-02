<?php
	/**
	 * Menu "Recherches" > "Par contrats" > "Par CER (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Contratsinsertion.search',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array( 'dernier' => '1' ),
					'Situationdossierrsa' => array(
						'etatdosrsa_choice' => '0',
						'etatdosrsa' => array( '0', '2', '3', '4' )
					)
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(
					'Situationdossierrsa.etatdosrsa' => array( 'Z', 0, 1, 2, 3, 4, 5, 6 )
				),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				'skip' => array()
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(
					'Situationdossierrsa.etatdosrsa <>' => 'Z',
					'Prestation.id IS NOT NULL'
				),
				// 2.3 Tri par défaut
				'order' => array( 'Contratinsertion.df_ci' )
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
					'Adresse.nomcom',
					'Referent.nom_complet',
					'Dossier.matricule',
					'Typeorient.lib_type_orient',
					'Contratinsertion.created' => array( 'type' => 'date' ),
					'Cer93.duree',
					'Contratinsertion.rg_ci',
					'Cer93.positioncer',
					'Contratinsertion.datevalidation_ci',
					'Contratinsertion.forme_ci',
					'Contratinsertion.df_ci',
					'/Cers93/index/#Contratinsertion.personne_id#' => array(
						'disabled' => "( '#Contratinsertion.horszone#' == true )",
						'class' => 'view'
					),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Calculdroitrsa.toppersdrodevorsa',
					'Personne.age' => array( 'label' => 'Age' ),
					'Prestation.rolepers',
					'Situationdossierrsa.etatdosrsa',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array(
				'max_execution_time' => 0,
				'memory_limit' => '512M'
			)
		)
	);

	/**
	 * Export CSV, menu "Recherches" > "Par contrats" > "Par CER (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Contratsinsertion.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Contratsinsertion.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Contratsinsertion.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Situationdossierrsa.etatdosrsa',
					'Calculdroitrsa.toppersdrodevorsa',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Dossier.matricule',
					'Personne.numport',
					'Personne.email',
					'Adresse.numvoie',
					'Adresse.libtypevoie',
					'Adresse.nomvoie',
					'Adresse.complideadr',
					'Adresse.compladr',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Typeorient.lib_type_orient',
					'Referent.nom_complet',
					'Structurereferente.lib_struc',
					'Contratinsertion.forme_ci',
					'Contratinsertion.dd_ci' => array( 'type' => 'date' ),
					'Cer93.duree',
					'Contratinsertion.df_ci' => array( 'type' => 'date' ),
					'Cer93.positioncer',
					'Contratinsertion.datevalidation_ci' => array( 'type' => 'date' ),
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					// FIXME: traductions
					// 1. Expériences professionnelles significatives
					// 1.1 Codes INSEE
					'Secteuractiexppro.name',
					'Metierexerceexppro.name',
					// 1.2 Codes ROME v.3
					'Familleexppro.name',
					'Domaineexppro.name',
					'Metierexppro.name',
					'Appellationexppro.name',
					// 2. Emploi trouvé
					// 2.1 Codes INSEE
					'Secteuracti.name',
					'Metierexerce.name',
					// 2.2 Codes ROME v.3
					'Familleemptrouv.name',
					'Domaineemptrouv.name',
					'Metieremptrouv.name',
					'Appellationemptrouv.name',
					// 3. Votre contrat porte sur
					// 3.1 Sujets, ... du CER
					'Sujetcer93.name',
					'Cer93Sujetcer93.commentaireautre',
					'Soussujetcer93.name',
					'Cer93Sujetcer93.autresoussujet',
					'Valeurparsoussujetcer93.name',
					'Cer93Sujetcer93.autrevaleur',
					// 3.2 Codes ROME v.3
					'Famillesujet.name',
					'Domainesujet.name',
					'Metiersujet.name',
					'Appellationsujet.name'
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Contratsinsertion.search.ini_set' ),
		)
	);

	/**
	 * Menu "Cohortes" > "CER" > "Contrats à valider (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Contratsinsertion.cohorte_nouveaux',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1'
					),
					'Contratinsertion' => array(
						'forme_ci' => 'S'
					)
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(
					 'Situationdossierrsa.etatdosrsa' => array( 2, 3, 4 )
				),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				'skip' => array(
					'Dossier.anciennete_dispositif',
					'Serviceinstructeur.id',
					'Dossier.fonorg',
					'Dossier.dtdemrsa',
					'Foyer.sitfam',
					'Personne.sexe',
					'Personne.trancheage',
					'Detailcalculdroitrsa.natpf',
					'Detaildroitrsa.oridemrsa',
					'Calculdroitrsa.toppersdrodevorsa',
				)
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(
					'Situationdossierrsa.etatdosrsa_choice' => '1',
					'Situationdossierrsa.etatdosrsa' => array( 2, 3, 4 ),
				),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(),
				// 2.3 Tri par défaut
				'order' => array( 'Contratinsertion.df_ci' => 'ASC' )
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
					'Dossier.numdemrsa' => array(
						'sort' => false
					),
					'Personne.nom_complet_court' => array(
						'sort' => false
					),
					'Adresse.nomcom' => array(
						'sort' => false
					),
					'Contratinsertion.dd_ci' => array(
						'sort' => false
					),
					'Contratinsertion.df_ci' => array(
						'sort' => false
					),
					// Champs
					'/Cers93/view/#Contratinsertion.id#' => array(
						'class' => 'external'
					)
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Personne.dtnai',
					'Dossier.matricule',
					'Personne.nir',
					'Adresse.codepos',
					'Adresse.numcom' => array(
						'options' => array()
					),
					'Prestation.rolepers',
					'Situationdossierrsa.etatdosrsa',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array(
				'max_execution_time' => 0,
				'memory_limit' => '512M'
			)
		)
	);

	/**
	 * Menu "Cohortes" > "CER" > "Contrats validés (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Contratsinsertion.cohorte_valides',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1'
					),
					'Contratinsertion' => array(
						'forme_ci' => 'S'
					)
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(
					'Situationdossierrsa.etatdosrsa' => array( 0, 1, 2, 3, 4, 5, 6 )
				),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				'skip' => array(
					'Dossier.anciennete_dispositif',
					'Serviceinstructeur.id',
					'Dossier.fonorg',
					'Dossier.dtdemrsa',
					'Foyer.sitfam',
					'Personne.sexe',
					'Personne.trancheage',
					'Detailcalculdroitrsa.natpf',
					'Detaildroitrsa.oridemrsa',
					'Calculdroitrsa.toppersdrodevorsa',
				)
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(
					'Situationdossierrsa.etatdosrsa <>' => 'Z'
				),
				// 2.3 Tri par défaut
				'order' => array( 'Contratinsertion.df_ci' => 'ASC' )
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
					'Personne.nom_complet_court',
					'Adresse.nomcom',
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci',
					'Contratinsertion.decision_ci',
					'Contratinsertion.datevalidation_ci',
					'Contratinsertion.observ_ci',
					'Contratinsertion.forme_ci',
					'/Cers93/view/#Contratinsertion.id#' => array(
						'class' => 'external'
					)
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Personne.dtnai',
					'Dossier.matricule',
					'Personne.nir',
					'Adresse.codepos',
					'Adresse.numcom' => array(
						'options' => array()
					),
					'Prestation.rolepers',
					'Situationdossierrsa.etatdosrsa',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array(
				'max_execution_time' => 0,
				'memory_limit' => '512M'
			)
		)
	);

	/**
	 * Export CSV de menu "Cohortes" > "CER" > "Contrats validés (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Contratsinsertion.exportcsv_valides',
		array_merge(
			Configure::read( 'ConfigurableQuery.Contratsinsertion.cohorte_valides' ),
			array(
				'limit' => null,
				'results' => array(
					'fields' => array(
						'Dossier.numdemrsa',
						'Personne.nom_complet_court',
						'Adresse.nomcom',
						'Structurereferente.lib_struc',
						'Contratinsertion.num_contrat',
						'Contratinsertion.dd_ci',
						'Contratinsertion.duree_engag',
						'Contratinsertion.df_ci',
						'Contratinsertion.decision_ci',
						'Contratinsertion.datevalidation_ci',
						'Contratinsertion.actions_prev',
						'Structurereferenteparcours.lib_struc',
						'Referentparcours.nom_complet'
					)
				)
			)
		)
	);
?>