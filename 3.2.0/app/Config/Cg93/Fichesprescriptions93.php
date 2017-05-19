<?php
	/**
	 * Menu "Recherches" > "Par fiches de prescription"
	 */
	Configure::write(
		'ConfigurableQuery.Fichesprescriptions93.search',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Calculdroitrsa' => array(
						'toppersdrodevorsa' => '1'
					),
					'Dossier' => array(
						'dernier' => '1',
					),
					'Ficheprescription93' => array(
						'exists' => '1'
					),
					'Pagination' => array(
						'nombre_total' => '0'
					),
					'Situationdossierrsa' => array(
						'etatdosrsa_choice' => '0',
						'etatdosrsa' => array( '0','2', '3', '4' )
					)
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(
					'Situationdossierrsa.etatdosrsa' => array( 0, 1, 2, 3, 4, 5, 6 )
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
					'Situationdossierrsa.etatdosrsa <>' => 'Z'
				),
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
					'Dossier.matricule',
					'Personne.nom_complet',
					'Adresse.nomcom',
					'Ficheprescription93.statut',
					'Actionfp93.name',
					'Prestatairefp93.name',
					'Dossier.locked' => array(
						'type' => 'boolean',
						'class' => 'dossier_locked'
					),
					// Début: données nécessaires pour les permissions sur les liens, sans affichage
					'Referent.horszone' => array( 'hidden' => true ),
					'Ficheprescription93.id' => array( 'hidden' => true ),
					// Fin: données nécessaires pour les permissions sur les liens, sans affichage
					'/Fichesprescriptions93/edit/#Ficheprescription93.id#' => array(
						'disabled' => "( '#Referent.horszone#' == true || '#Ficheprescription93.id#' == '' || '#/Fichesprescriptions93/edit#' == false )",
						'class' => 'external'
					),
					'/Fichesprescriptions93/index/#Personne.id#' => array(
						'title' => 'Voir les fiches de prescription de #Personne.nom_complet#',
						'disabled' => "( '#Referent.horszone#' == true )",
						'class' => 'view external'
					),
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Calculdroitrsa.toppersdrodevorsa',
					'Personne.age' => array( 'label' => 'Age' ),
					'Ficheprescription93.benef_retour_presente',
					'Ficheprescription93.personne_a_integre',
					'Personne.dtnai',
					'Dossier.numdemrsa',
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
	 * Export CSV,  menu "Recherches" > "Par fiches de prescription"
	 */
	Configure::write(
		'ConfigurableQuery.Fichesprescriptions93.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Fichesprescriptions93.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Fichesprescriptions93.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
					'Dossier.matricule',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Prestation.rolepers',
					'Ficheprescription93.statut',
					'Referent.nom_complet' => array( 'label' => 'Referent etablissant la FP' ),
					'Adresse.numvoie' => array( 'domain' => 'adresse' ),
					'Adresse.libtypevoie' => array( 'domain' => 'adresse' ),
					'Adresse.nomvoie' => array( 'domain' => 'adresse' ),
					'Adresse.complideadr' => array( 'domain' => 'adresse' ),
					'Adresse.compladr' => array( 'domain' => 'adresse' ),
					'Adresse.lieudist' => array( 'domain' => 'adresse' ),
					'Adresse.numcom' => array( 'domain' => 'adresse' ),
					'Adresse.numcom' => array( 'domain' => 'adresse' ),
					'Adresse.codepos' => array( 'domain' => 'adresse' ),
					'Adresse.nomcom' => array( 'domain' => 'adresse' ),
					'Ficheprescription93.rdvprestataire_date',
					'Actionfp93.numconvention' => array( 'domain' => 'cataloguespdisfps93' ),
					'Thematiquefp93.type' => array( 'domain' => 'cataloguespdisfps93' ),
					'Thematiquefp93.name' => array( 'domain' => 'cataloguespdisfps93' ),
					'Categoriefp93.name' => array( 'domain' => 'cataloguespdisfps93' ),
					'Filierefp93.name' => array( 'domain' => 'cataloguespdisfps93' ),
					'Prestatairefp93.name' => array( 'domain' => 'cataloguespdisfps93' ),
					'Actionfp93.name' => array( 'domain' => 'cataloguespdisfps93' ),
					'Ficheprescription93.benef_retour_presente',
					'Ficheprescription93.dd_action',
					'Ficheprescription93.df_action',
					'Ficheprescription93.date_signature',
					'Ficheprescription93.date_transmission',
					'Ficheprescription93.date_retour',
					'Ficheprescription93.personne_recue',
					'Ficheprescription93.motifnonreceptionfp93_id',
					'Ficheprescription93.personne_nonrecue_autre',
					'Ficheprescription93.personne_retenue',
					'Ficheprescription93.motifnonretenuefp93_id',
					'Ficheprescription93.personne_nonretenue_autre',
					'Ficheprescription93.personne_souhaite_integrer',
					'Ficheprescription93.motifnonsouhaitfp93_id',
					'Ficheprescription93.personne_nonsouhaite_autre',
					'Ficheprescription93.personne_a_integre',
					'Ficheprescription93.personne_date_integration',
					'Ficheprescription93.motifnonintegrationfp93_id',
					'Ficheprescription93.personne_nonintegre_autre',
					'Ficheprescription93.date_bilan_mi_parcours',
					'Ficheprescription93.date_bilan_final',
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Fichesprescriptions93.search.ini_set' ),
		)
	);

	//--------------------------------------------------------------------------

	/**
	 * Liste des intitulés et des URL à faire apparaître dans le cadre
	 * "Prescripteur/Référent" de la fiche de prescription du CG 93.
	 *
	 * @var array
	 */
	Configure::write(
		'Cataloguepdifp93.urls',
		array(
			'Consultation du catalogue des actions (PDI)' => 'http://www.seine-saint-denis.fr/Catalogue-des-Actions-d-Insertion.html',
			'Consultation du site Defi Metiers' => 'http://www.carif-idf.org/',
			'Consultation INSER\'ECO93' => 'http://www.insereco93.com/',
		)
	);

	/**
	 * Mise en évidence de certains champs du formulaire des fiches de
	 * prescription, pour remplir les tableaux de bord B5
	 */
	Configure::write(
		'Evidence.Fichesprescriptions93.add',
		array(
			'fields' => array(
				// Structure du référent
				'#Ficheprescription93StructurereferenteId',
				// Type
				'#Ficheprescription93Typethematiquefp93Id',
				// Thématique
				'#Ficheprescription93Thematiquefp93Id',
				// Catégorie
				'#Ficheprescription93Categoriefp93Id',
				// Date de début de l'action
				'#Ficheprescription93DdActionDay',
				// Signé le
				'#Ficheprescription93DateSignatureDay',
				// La personne s'est présentée
				'#Ficheprescription93BenefRetourPresente',
				// Signé par le partenaire le
				'#Ficheprescription93DateSignaturePartenaireDay',
				// La personne a été reçue en entretien
				'#Ficheprescription93PersonneRecue',
				// La personne a été retenue par la structure
				'#Ficheprescription93PersonneRetenue',
				// La personne souhaite intégrer l'action
				'#Ficheprescription93PersonneSouhaiteIntegrer',
				// L'allocataire a intégré l'action
				'#Ficheprescription93PersonneAIntegre',
			)
		)
	);
	Configure::write( 'Evidence.Fichesprescriptions93.edit', Configure::read( 'Evidence.Fichesprescriptions93.add' ) );
?>