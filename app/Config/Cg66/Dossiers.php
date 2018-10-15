<?php
	/**
	 * Menu "Recherches" > "Par dossier / allocataire"
	 */
	Configure::write(
		'ConfigurableQuery.Dossiers.search',
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
				'skip' => array(),
				// 1.4 Filtres additionnels : La personne possède un(e)...
				'has' => array(
					'Cui',
					'Orientstruct' => array(
						'Orientstruct.statut_orient' => 'Orienté',
						// Orientstruct possède des conditions supplémentaire dans le modèle WebrsaRechercheDossier pour le CD66
					),
					'Contratinsertion' => array(
						'Contratinsertion.decision_ci' => 'V'
					),
					'Personnepcg66'
				)
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(),
				// 2.3 Tri par défaut
				'order' => array( 'Personne.nom' )
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
					'Dossier.dtdemrsa',
					'Personne.nir',
					'Situationdossierrsa.etatdosrsa',
					'Personne.nom_complet_prenoms', // FIXME: nom complet/court/prenoms ?
					'Adresse.complete',
					'Dossier.locked' => array(
						'type' => 'boolean',
						'class' => 'dossier_locked'
					),
					'Canton.canton',
					'/Dossiers/view/#Dossier.id#'
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
				    'Dossier.fonorg' => array(
                        'verifMSA'=>true//ajoute la couleur dans le cas d'un bénéficiare MSA
                    ),
					'Dossier.matricule',
					'Personne.dtnai',
					'Adresse.numcom' => array(
						'options' => array()
					),
					'Prestation.rolepers',
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
	 * Export CSV, menu "Recherches" > "Par dossier / allocataire"
	 */
	Configure::write(
		'ConfigurableQuery.Dossiers.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Dossiers.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Dossiers.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
					'Personne.nir',
					'Situationdossierrsa.etatdosrsa',
					'Personne.nom_complet_prenoms', // FIXME: nom complet/court/prenoms ?
					'Personne.dtnai',
					'Adresse.numvoie',
					'Adresse.libtypevoie',
					'Adresse.nomvoie',
					'Adresse.complideadr',
					'Adresse.compladr',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Typeorient.lib_type_orient',
					'Personne.idassedic',
					'Dossier.matricule',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Personne.sexe',
					'Dsp.natlog',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Dossiers.search.ini_set' ),
		)
	);

    /**
     * Configuration des couleurs en fonction de différents attribut (appartenance bénéficiaire, type de dossiers, etc)
     */
    Configure::write(
        'ConfigurableQuery.Dossiers.couleurs',
        array(
           0    =>  array('valeurCompare'=>'MSA', 'param'=>' " verifMSA="1', 'replace'=>' contenuMSA')
        )
    );
?>