<?php
	/**
	 * Permet de faire apparaître ou non dans le menu "Administration" le
	 * sous-menu "Flux Pôle Emploi".
	 */
	Configure::write( 'Module.Fluxpoleemplois.enabled', true );

	/**
	 * Blocs d'informations du flux Pôle Emploi
	 *
	 * Profil :
	 *  - correspond aux profils des groups dans la partie administration
	 *
	 * Blocs :
	 *  - individu
	 *  - allocataire
	 *  - inscription
	 *  - structure_principale
	 *  - structure_deleguee
	 *  - formation
	 *  - romev3
	 *  - ppae
	 *
	 */
	Configure::write(
		'Profil.Fluxpoleemplois.access',
		array (
			// Affichage par défaut.
			'by-default' => array (
				'individu' => true,
				'inscription' => true,
				'formation' => true,
				'romev3' => true,
				'allocataire' => true,
				'structure_principale' => true,
				'structure_deleguee' => true,
				'ppae' => true,
			),
		)
	);
?>
<?php
	/**
	 * Menu "Recherches" > "Par Pôle Emploi"
	 */
	Configure::write(
		'ConfigurableQuery.Fluxpoleemplois.search',
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
				'skip' => array(),
				// 1.4 Filtres additionnels : La personne possède un(e)...
				'has' => array(
					'Contratinsertion' => array(
						'Contratinsertion.decision_ci' => 'V'
					),
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
					'Dossier.dtdemrsa' => 'DESC',
					'Personne.nom' => 'ASC'
				)
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
					'Personne.nom_complet_prenoms',
					'Adresse.nomcom',
					'Dossier.locked' => array(
						'type' => 'boolean',
						'class' => 'dossier_locked'
					),
					'/Dossiers/view/#Dossier.id#'
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
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
	 * Export CSV, menu "Recherches" > "Par Pôle Emploi"
	 */
	Configure::write(
		'ConfigurableQuery.FluxPoleEmploi.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.FluxPoleEmploi.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.FluxPoleEmploi.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
					'Personne.nir',
					'Situationdossierrsa.etatdosrsa',
					'Prestation.natprest',
					'Calculdroitrsa.toppersdrodevorsa',
					'Personne.nom_complet_prenoms',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Personne.age' => array( 'label' => 'Age' ),
					'Adresse.numvoie',
					'Adresse.libtypevoie',
					'Adresse.nomvoie',
					'Adresse.complideadr',
					'Adresse.compladr',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Personne.email',
					'Personne.numfixe',
					'Typeorient.lib_type_orient',
					'Personne.idassedic',
					'Dsp.inscdememploi',
					'Dossier.matricule',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Personne.sexe',
					'Dsp.inscdememploi',
					'Dsp.natlog' ,
					'Dsp.nivetu'
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.FluxPoleEmploi.search.ini_set' ),
		)
	);
?>