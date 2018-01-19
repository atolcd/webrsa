<?php
/**
 * Catégories des requetes obtenus par le request manager affiché par actions
 */
Configure::write('Tags.cohorte.allowed.Requestgroup.id',
	array(
		7, // Noter nom de catégorie - Cohorte de tag
	)
);

/**
 * Choix possible pour le préremplissage de la date butoir
 */
Configure::write('Tags.cohorte.range_date_butoir',
	array(
		'1' => '1 mois',
		'1.5' => '1 mois et demi', // Supporte les nombres de type float
		2 => '2 mois',
		3 => '3 mois',
		6 => '6 mois',
		12 => '1 an',
		24 => '2 ans',
		36 => '3 ans',
	)
);

/**
 * Menu "Recherches"
 */
Configure::write(
	'ConfigurableQuery.Tags.cohorte',
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
			'has' => array(
				'Cui',
				'Orientstruct' => array(
					'Orientstruct.statut_orient' => 'Orienté',
					// Orientstruct possède des conditions supplémentaire dans le modèle WebrsaRechercheDossier pour le CD66
				),
				'Contratinsertion' => array(
					'Contratinsertion.decision_ci' => 'V'
				),
				//'Personnepcg66'
			)
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
				'Dossier.numdemrsa',
				'Dossier.dtdemrsa',
				'Personne.nir',
				'Situationdossierrsa.etatdosrsa',
				'Personne.nom_complet_prenoms',
				'Adresse.complete',
				'Canton.canton',
				'/Dossiers/view/#Dossier.id#' => array( 'class' => 'external' ),
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
		'ini_set' => array()
	)
);