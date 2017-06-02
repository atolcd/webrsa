<?php
	/**
	 * Menu Recherche de dossiers PCGs
	 */
	Configure::write(
		'ConfigurableQuery.Dossierspcgs66.search',
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
				'skip' => array()
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(),
				// 2.3 Tri par défaut
				//'order' => array( 'Personne.nom', 'Personne.prenom' )
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
					// 'Personne.nom_complet', // Demandeur rsa
					'Personnepcg66.noms_complet', // Liste des personnes dans le dossier pcg
					'Originepdo.libelle',
					'Typepdo.libelle',
					'Dossierpcg66.datereceptionpdo',
					'Poledossierpcg66.name',
					'User.nom_complet',
					'Situationpdo.libelles',
					'Statutpdo.libelles',
					'Traitementpcg66.datereception' => array('label' => 'Date de réception des pièces demandées'),
					'Dossierpcg66.nbpropositions',
					'Decisionpdo.libelle',
					'Decisiondossierpcg66.datepropositiontechnicien',
					'Dossierpcg66.etatdossierpcg',
					'Decisiondossierpcg66.org_id',
					'Decisiondossierpcg66.datetransmissionop',
					'Canton.canton',
					'/Dossierspcgs66/ajax_view_decisions/#Dossierpcg66.id#' => array(
						'class' => 'view ajax-link',
						'msgid' => 'Voir propositions (#Decisiondossierpcg66.count#)',
						'disabled' => "!'#Decisiondossierpcg66.count#'"
					),
					'/Dossierspcgs66/index/#Dossierpcg66.foyer_id#' => array( 'class' => 'view' ),
					'/Dossierspcgs66/edit/#Dossierpcg66.id#' => array( 'class' => 'edit' ),
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
	 * Export CSV,  menu "Recherches" > "Par entretiens"
	 */
	Configure::write(
		'ConfigurableQuery.Dossierspcgs66.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Dossierpcg66.originepdo_id',
					'Dossierpcg66.typepdo_id',
					'Dossierpcg66.datereceptionpdo',
					'Dossierpcg66.poledossierpcg66_id',
					'User.nom_complet',
					'Decisionpdo.libelle',
					'Dossierpcg66.nbpropositions',
					'Dossierpcg66.etatdossierpcg',
					'Decisiondossierpcg66.org_id',
					'Decisiondossierpcg66.datetransmissionop',
					'Traitementpcg66.datereception',
					'Situationpdo.libelles',
					'Statutpdo.libelles',
					'Fichiermodule.nb_fichiers_lies',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Familleromev3.name',
					'Domaineromev3.name',
					'Metierromev3.name',
					'Appellationromev3.name',
					'Categoriemetierromev2.code',
					'Categoriemetierromev2.name',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.search.ini_set' ),
		)
	);

	/**
	 * Menu Recherche de dossiers PCGs
	 */
	Configure::write(
		'ConfigurableQuery.Dossierspcgs66.search_affectes',
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
				'skip' => array()
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(
					'Dossierpcg66.etatdossierpcg' => 'attinstr'
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
					'Adresse.nomcom',
					'Dossierpcg66.datereceptionpdo',
					'Typepdo.libelle',
					'Originepdo.libelle',
					'Dossierpcg66.orgpayeur',
					'Serviceinstructeur.lib_service',
					'Dossierpcg66.poledossierpcg66_id',
					'Dossierpcg66.user_id',
					'Dossierpcg66.dateaffectation',
					'Canton.canton',
					'/Dossierspcgs66/index/#Dossierpcg66.foyer_id#' => array( 'class' => 'view' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array(),
		)
	);

	/**
	 * Export CSV,  menu "Recherches" > "Par entretiens"
	 */
	Configure::write(
		'ConfigurableQuery.Dossierspcgs66.exportcsv_affectes',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.search_affectes.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.search_affectes.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Dossierpcg66.datereceptionpdo',
					'Typepdo.libelle',
					'Originepdo.libelle',
					'Dossierpcg66.orgpayeur',
					'Serviceinstructeur.lib_service',
					'Dossierpcg66.poledossierpcg66_id',
					'Dossierpcg66.user_id',
					'Dossierpcg66.dateaffectation',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.search_affectes.ini_set' ),
		)
	);

	/**
	 * Menu Recherche de dossiers PCGs
	 */
	Configure::write(
		'ConfigurableQuery.Dossierspcgs66.cohorte_imprimer',
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
				'fields' => array (
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Dossierpcg66.datereceptionpdo',
					'Typepdo.libelle',
					'Originepdo.libelle',
					'Dossierpcg66.orgpayeur',
					'Serviceinstructeur.lib_service',
					'Dossierpcg66.poledossierpcg66_id',
					'Dossierpcg66.user_id',
					'Dossierpcg66.dateaffectation',
					'Decisiondossierpcg66.decisionpdo_id',
					'Canton.canton',
					'/Dossierspcgs66/index/#Dossierpcg66.foyer_id#' => array( 'class' => 'view' ),
					'/Dossierspcgs66/edit/#Dossierpcg66.id#' => array( 'class' => 'edit' ),
					'/Dossierspcgs66/imprimer/#Dossierpcg66.id#/#Decisiondossierpcg66.id#' => array( 'class' => 'print' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array(),
		)
	);

	/**
	 * Export CSV,  menu "Recherches" > "Par entretiens"
	 */
	Configure::write(
		'ConfigurableQuery.Dossierspcgs66.exportcsv_imprimer',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.search_imprimer.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.search_imprimer.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Dossierpcg66.datereceptionpdo',
					'Typepdo.libelle',
					'Originepdo.libelle',
					'Dossierpcg66.orgpayeur',
					'Serviceinstructeur.lib_service',
					'Dossierpcg66.poledossierpcg66_id',
					'Dossierpcg66.user_id',
					'Dossierpcg66.dateaffectation',
					'Decisiondossierpcg66.decisionpdo_id',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.search_imprimer.ini_set' ),
		)
	);

	/**
	 * Menu Gestionnaire de dossiers PCG
	 */
	Configure::write(
		'ConfigurableQuery.Dossierspcgs66.search_gestionnaire',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1'
					),
					'Dossierpcg66' => array(
						'etatdossierpcg' => array(
							'attinstr', // En attente d'instruction
							'decisionvalid', // Décision validée
							'decisionnonvalid', // Décision validée
						),
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
				'fields' => array (
					'Dossier.numdemrsa',
					// 'Personne.nom_complet', // Demandeur rsa
					'Personnepcg66.noms_complet', // Liste des personnes dans le dossier pcg
					'Dossierpcg66.originepdo_id',
					'Dossierpcg66.typepdo_id',
					'Traitementpcg66.dateecheances',
					'Dossierpcg66.user_id',
					'Dossierpcg66.nbpropositions',
					'Personnepcg66.nbtraitements',
					'Dossierpcg66.listetraitements',
					'Dossierpcg66.etatdossierpcg',
					'Decisiondossierpcg66.decisionpdo_id',
					'Situationpdo.libelles',
					'Statutpdo.libelles',
					'Fichiermodule.nb_fichiers_lies',
					'Dossier.locked',
					'Canton.canton',
					'/Dossierspcgs66/index/#Dossierpcg66.foyer_id#' => array( 'class' => 'view' ),
					'/Dossierspcgs66/edit/#Dossierpcg66.id#' => array( 'class' => 'edit' ),
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
	 * Export CSV,  menu "Recherches" > "Par entretiens"
	 */
	Configure::write(
		'ConfigurableQuery.Dossierspcgs66.exportcsv_gestionnaire',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Dossierpcg66.originepdo_id',
					'Dossierpcg66.typepdo_id',
					'Dossierpcg66.datereceptionpdo',
					'User.nom_complet',
					'Dossierpcg66.nbpropositions',
					'Personnepcg66.nbtraitements',
					'Dossierpcg66.listetraitements',
					'Dossierpcg66.etatdossierpcg',
					'Fichiermodule.nb_fichiers_lies',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.search.ini_set' ),
		)
	);

	/**
	 * Menu Dossiers PCGs en attente d'affectation
	 */
	Configure::write(
		'ConfigurableQuery.Dossierspcgs66.cohorte_enattenteaffectation',
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
				'fields' => array (
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Dossierpcg66.datereceptionpdo',
					'Typepdo.libelle',
					'Originepdo.libelle',
					'Dossierpcg66.orgpayeur',
					'Serviceinstructeur.lib_service',
					'Canton.canton',
					'/Dossierspcgs66/index/#Dossierpcg66.foyer_id#' => array( 'class' => 'view' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array()
		)
	);

	/**
	 * Export CSV
	 */
	Configure::write(
		'ConfigurableQuery.Dossierspcgs66.exportcsv_enattenteaffectation',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.cohorte_enattenteaffectation.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.cohorte_enattenteaffectation.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Dossierpcg66.datereceptionpdo',
					'Typepdo.libelle',
					'Originepdo.libelle',
					'Dossierpcg66.orgpayeur',
					'Serviceinstructeur.lib_service',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				),
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.cohorte_enattenteaffectation.ini_set' ),
		)
	);

	/**
	 * Menu Dossiers PCGs en attente d'affectation
	 */
	Configure::write(
		'ConfigurableQuery.Dossierspcgs66.cohorte_atransmettre',
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
				'fields' => array (
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Dossierpcg66.datereceptionpdo',
					'Typepdo.libelle',
					'Originepdo.libelle',
					'Dossierpcg66.orgpayeur',
					'Serviceinstructeur.lib_service',
					'Canton.canton',
					'/Dossierspcgs66/index/#Dossierpcg66.foyer_id#' => array( 'class' => 'view' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array()
		)
	);

	/**
	 * Export CSV
	 */
	Configure::write(
		'ConfigurableQuery.Dossierspcgs66.exportcsv_atransmettre',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.cohorte_atransmettre.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.cohorte_atransmettre.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Dossierpcg66.datereceptionpdo',
					'Typepdo.libelle',
					'Originepdo.libelle',
					'Dossierpcg66.orgpayeur',
					'Serviceinstructeur.lib_service',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				),
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.cohorte_atransmettre.ini_set' ),
		)
	);

	Configure::write(
		'ConfigurableQuery.Dossierspcgs66.cohorte_heberge',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1'
					),
					'Adresse' => array(
						'heberge' => '1'
					),
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(
					'Requestmanager.name' => array( 'Cohorte de tag' ), // Noter nom de catégorie - Cohorte de tag
				),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				'skip' => array(
					'Situationdossierrsa.etatdosrsa_choice',
					'Situationdossierrsa.etatdosrsa',
					'Detailcalculdroitrsa.natpf_choice' => '1',
					'Detailcalculdroitrsa.natpf',
					'Calculdroitrsa.toppersdrodevorsa'
				),
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
				'restrict' => array(
					'Tag.valeurtag_id' => '2', // Valeur du tag pour la cohorte hebergé
					'Prestation.rolepers' => 'DEM', // Demandeur du RSA
					'Adresse.heberge' => '1', // Conditions pour trouver les allocataires hébergé
					'Situationdossierrsa.etatdosrsa_choice' => '1',
					'Situationdossierrsa.etatdosrsa' => array('2'), // Droit ouvert et versable
					'Detailcalculdroitrsa.natpf_choice' => '1',
					'Detailcalculdroitrsa.natpf' => array(
						'RSD', // RSA Socle (Financement sur fonds Conseil général)
						'RSI', // RSA Socle majoré (Financement sur fonds Conseil général)
					),
					'Calculdroitrsa.toppersdrodevorsa' => '1', // Personne soumise à droits et devoirs ? > Oui
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
				'fields' => array(
					'Dossier.matricule',
					'Personne.nom_complet_prenoms',
					'Detailcalculdroitrsa.mtrsavers',
					'Foyer.nb_enfants' => array( 'options' => array() ),
					'Adresse.nomcom',
					'Adressefoyer.dtemm',
					'Canton.canton',
					'/Dossiers/view/#Dossier.id#' => array( 'class' => 'external' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// Configuration du formulaire de cohorte
			'cohorte' => array(
				// Remplacement des options dans la cohorte
				'options' => array(
					'Tag.calcullimite' => array(
						'1' => '1 mois',
						'1.5' => '1 mois et demi', // Supporte les nombres de type float
						2 => '2 mois',
						3 => '3 mois',
						6 => '6 mois',
						12 => '1 an',
						24 => '2 ans',
						36 => '3 ans',
					),
					'Traitementpcg66.typetraitement' => array(
						'courrier' => 'Courrier',
						'dossierarevoir' => 'Dossier à revoir',
					)
				),
				// Valeurs à remplir dans les champs de la cohorte avant de les cacher
				'values' => array(
					'Dossierpcg66.typepdo_id' => 16, // Position mission PDU-MMR
					'Dossierpcg66.datereceptionpdo' => date('Y-m-d'), // Date de réception du dossier
					'Dossierpcg66.serviceinstructeur_id' => null, // Service instructeur
					'Dossierpcg66.commentairepiecejointe' => null, // Commentaire
					'Dossierpcg66.dateaffectation' => date('Y-m-d'), // Date d'affectation
					'Situationpdo.Situationpdo' => 34, // Cible hébergé
					'Dossierpcg66.originepdo_id' => 21, // PDU - MMR Cible Imposition
					'Dossierpcg66.poledossierpcg66_id' => 1, // PDU
					'Traitementpcg66.typecourrierpcg66_id' => 9, // PDU - Cibles
					'Traitementpcg66.descriptionpdo_id' => 1, // Courrier à l'allocataire
					'Traitementpcg66.datereception' => null, // Date de reception
					'Modeletraitementpcg66.modeletypecourrierpcg66_id' => 82, // Cible hébergé
					'Modeletraitementpcg66.montantdatedebut' => date('Y-m-d'),
					'Modeletraitementpcg66.montantdatefin' => date_format(date_add(new DateTime(), date_interval_create_from_date_string('+3 months')), 'Y-m-d'),
					'Piecemodeletypecourrierpcg66.0_Piecemodeletypecourrierpcg66' => 131, // Attestation ci-jointe dûment complétée
					'Piecemodeletypecourrierpcg66.1_Piecemodeletypecourrierpcg66' => 132, // Attestation d'hébergement dûment remplie (en pièce jointe)
					'Piecemodeletypecourrierpcg66.2_Piecemodeletypecourrierpcg66' => 129, // Avis d'imposition sur les revenus de l'année précédente...
					'Piecemodeletypecourrierpcg66.3_Piecemodeletypecourrierpcg66' => 133, // Justificatifs de résidence de moins de 3 mois...
					'Piecemodeletypecourrierpcg66.4_Piecemodeletypecourrierpcg66' => 128, // Pièce d'identité et passeport en intégralité et en cours...
					'Piecemodeletypecourrierpcg66.5_Piecemodeletypecourrierpcg66' => 130, // Relevés bancaires des 3 derniers mois
					'Traitementpcg66.serviceinstructeur_id' => null, // Service à contacter (insertion)
					'Traitementpcg66.datedepart' => date('Y-m-d'), // Date de départ (pour le calcul de l'échéance)
					'Tag.valeurtag_id' => 2, // Valeur du tag
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array(),
			// 7. Affichage vertical des résultats
			'view' => false,
		)
	);

	/**
	 * Export CSV
	 */
	Configure::write(
		'ConfigurableQuery.Dossierspcgs66.exportcsv_heberge',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.cohorte_heberge.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.cohorte_heberge.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.matricule',
					'Personne.nom_complet_prenoms',
					'Detailcalculdroitrsa.mtrsavers',
					'Foyer.nb_enfants' => array( 'options' => array() ),
					'Adresse.nomcom',
					'Adressefoyer.dtemm',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				),
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.cohorte_heberge.ini_set' ),
		)
	);

	Configure::write(
		'ConfigurableQuery.Dossierspcgs66.cohorte_rsamajore',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1'
					),
					'Adresse' => array(
						'heberge' => '1'
					),
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(
					'Requestmanager.name' => array( 'Cohorte de tag' ), // Noter nom de catégorie - Cohorte de tag
				),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				'skip' => array(
					'Situationdossierrsa.etatdosrsa_choice',
					'Situationdossierrsa.etatdosrsa',
					'Detailcalculdroitrsa.natpf_choice' => '1',
					'Detailcalculdroitrsa.natpf',
					'Calculdroitrsa.toppersdrodevorsa'
				),
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
				'restrict' => array(
					'Tag.valeurtag_id' => '3', // Valeur du tag pour la cohorte hebergé
					'Prestation.rolepers' => 'DEM', // Demandeur du RSA
					'Situationdossierrsa.etatdosrsa_choice' => '1',
					'Situationdossierrsa.etatdosrsa' => array('2'), // Droit ouvert et versable
					'Detailcalculdroitrsa.natpf_choice' => '1',
					'Detailcalculdroitrsa.natpf' => array(
						'RSI', // RSA Socle majoré (Financement sur fonds Conseil général)
					),
					'Calculdroitrsa.toppersdrodevorsa' => '1', // Personne soumise à droits et devoirs ? > Oui
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
				'fields' => array(
					'Dossier.matricule',
					'Personne.nom_complet_prenoms',
					'Detailcalculdroitrsa.mtrsavers',
					'Foyer.nb_enfants' => array( 'options' => array() ),
					'Adresse.nomcom',
					'Foyer.ddsitfam',
					'Canton.canton',
					'/Dossiers/view/#Dossier.id#' => array( 'class' => 'external' ),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet'
				)
			),
			// Configuration du formulaire de cohorte
			'cohorte' => array(
				// Remplacement des options dans la cohorte
				'options' => array(
					'Tag.calcullimite' => array(
						'1' => '1 mois',
						'1.5' => '1 mois et demi', // Supporte les nombres de type float
						2 => '2 mois',
						3 => '3 mois',
						6 => '6 mois',
						12 => '1 an',
						24 => '2 ans',
						36 => '3 ans',
					),
					'Traitementpcg66.typetraitement' => array(
						'courrier' => 'Courrier',
						'dossierarevoir' => 'Dossier à revoir',
					)
				),
				// Valeurs à remplir dans les champs de la cohorte avant de les cacher
				'values' => array(
					'Dossierpcg66.typepdo_id' => 16, // Position mission PDU-MMR
					'Dossierpcg66.datereceptionpdo' => date('Y-m-d'), // Date de réception du dossier
					'Dossierpcg66.serviceinstructeur_id' => null, // Service instructeur
					'Dossierpcg66.commentairepiecejointe' => null, // Commentaire
					'Dossierpcg66.dateaffectation' => date('Y-m-d'), // Date d'affectation
					'Situationpdo.Situationpdo' => 38, // Cible majoré
					'Dossierpcg66.originepdo_id' => 21, // PDU - MMR Cible Imposition
					'Dossierpcg66.poledossierpcg66_id' => 1, // PDU
					'Traitementpcg66.typecourrierpcg66_id' => 9, // PDU - Cibles
					'Traitementpcg66.descriptionpdo_id' => 1, // Courrier à l'allocataire
					'Traitementpcg66.datereception' => null, // Date de reception
					'Modeletraitementpcg66.modeletypecourrierpcg66_id' => 90, // Cible majoré
					'Modeletraitementpcg66.montantdatedebut' => date('Y-m-d'),
					'Modeletraitementpcg66.montantdatefin' => date_format(date_add(new DateTime(), date_interval_create_from_date_string('+3 months')), 'Y-m-d'),
					'Piecemodeletypecourrierpcg66.0_Piecemodeletypecourrierpcg66' => 185, // Attestation ci-jointe dûment complétée
//					'Piecemodeletypecourrierpcg66.1_Piecemodeletypecourrierpcg66' => 132, // Attestation d'hébergement dûment remplie (en pièce jointe)
//					'Piecemodeletypecourrierpcg66.2_Piecemodeletypecourrierpcg66' => 129, // Avis d'imposition sur les revenus de l'année précédente...
//					'Piecemodeletypecourrierpcg66.3_Piecemodeletypecourrierpcg66' => 133, // Justificatifs de résidence de moins de 3 mois...
//					'Piecemodeletypecourrierpcg66.4_Piecemodeletypecourrierpcg66' => 128, // Pièce d'identité et passeport en intégralité et en cours...
//					'Piecemodeletypecourrierpcg66.5_Piecemodeletypecourrierpcg66' => 130, // Relevés bancaires des 3 derniers mois
					'Traitementpcg66.serviceinstructeur_id' => null, // Service à contacter (insertion)
					'Traitementpcg66.datedepart' => date('Y-m-d'), // Date de départ (pour le calcul de l'échéance)
					'Tag.valeurtag_id' => 3, // Valeur du tag
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array(),
			// 7. Affichage vertical des résultats
			'view' => false,
		)
	);

	/**
	 * Export CSV
	 */
	Configure::write(
		'ConfigurableQuery.Dossierspcgs66.exportcsv_rsamajore',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.cohorte_rsamajore.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.cohorte_rsamajore.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.matricule',
					'Personne.nom_complet_prenoms',
					'Detailcalculdroitrsa.mtrsavers',
					'Foyer.nb_enfants' => array( 'options' => array() ),
					'Adresse.nomcom',
					'Foyer.ddsitfam',
					'Structurereferenteparcours.lib_struc',
					'Referentparcours.nom_complet',
					'Canton.canton',
				),
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Dossierspcgs66.cohorte_rsamajore.ini_set' ),
		)
	);
?>