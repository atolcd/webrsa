<?php
	/**
	 * Liste des valeurs par défaut des moteurs de recherches pour les tableaux
	 * de suivi D1, D2, 1B3, 1B4, 1B5 et 1B6 (clé Tableauxsuivispdvs93.<tableau>.defaults).
	 *
	 * Liste des champs pris en compte dans l'export CSV des corpus des tableaux
	 * de suivi 1B3, 1B4, 1B5 et 1B6 (clé Tableauxsuivispdvs93.<tableau>.exportcsvcorpus).
	 *
	 * La liste complète des champs utilisables pour chacun des tableaux se
	 * trouvera dans le répertoire /app/tmp/logs après le lancement du shell de
	 * Prechargement, lorsque la valeur de "production" sera à true dans le fichier
	 * app/Config/core.php.
	 *
	 * Les fichiers concernés sont: Tableausuivipdv93__tableau1b3.csv, Tableausuivipdv93__tableau1b4.csv,
	 * Tableausuivipdv93__tableau1b5.csv et Tableausuivipdv93__tableau1b6.csv.
	 *
	 * Après avoir configuré ces champs, vérifiez qu'il n'y ait pas d'erreur en
	 * vous rendant dans le partie "Vérification de l'application", onglet "Environnement logiciel"
	 * > "WebRSA" > "Champs spécifiés dans le webrsa.inc" (ceux qui commencent par "Tableauxsuivispdvs93").
	 *
	 * @var array
	 */
	Configure::write(
		'Tableauxsuivispdvs93',
		array(
			'tableaud1' => array(
				'defaults' => array(
					'Search' => array(
						'soumis_dd_dans_annee' => '1'
					)
				),
			),
			'tableaud2' => array(
				'defaults' => array(
					'Search' => array(
						'soumis_dd_dans_annee' => '1'
					)
				),
			),
			'tableau1b3' => array(
				'defaults' => array(
					'Search' => array(
						'dsps_maj_dans_annee' => '1'
					)
				),
				'exportcsvcorpus' => array(
					// Rendez-vous
					'Rendezvous.daterdv',
					'Structurereferente.lib_struc',
					'Referent.nom_complet',
					// Allocataire
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Personne.sexe',
					'Prestation.rolepers',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Foyer.sitfam',
					'Dossier.matricule',
					// Difficultés exprimées
					'Difficulte.sante',
					'Difficulte.logement',
					'Difficulte.familiales',
					'Difficulte.modes_gardes',
					'Difficulte.surendettement',
					'Difficulte.administratives',
					'Difficulte.linguistiques',
					'Difficulte.mobilisation',
					'Difficulte.qualification_professionnelle',
					'Difficulte.acces_emploi',
					'Difficulte.autres'
				)
			),
			'tableau1b4' => array(
				'defaults' => array(
					'Search' => array(
						'rdv_structurereferente' => '1'
					)
				),
				'exportcsvcorpus' => array(
					// Fiche de prescription
					'Ficheprescription93.date_signature',
					'Structurereferente.lib_struc',
					'Referent.nom_complet',
					// Allocataire
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Personne.sexe',
					'Prestation.rolepers',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Foyer.sitfam',
					'Dossier.matricule',
					// Fiche de prescription
					'Thematiquefp93.type',
					'Thematiquefp93.name',
					'Thematiquefp93.yearthema',
					'Categoriefp93.name',
				)
			),
			'tableau1b5' => array(
				'defaults' => array(
					'Search' => array(
						'rdv_structurereferente' => '1'
					)
				),
				'exportcsvcorpus' => array(
					// Fiche de prescription
					'Ficheprescription93.date_signature',
					'Structurereferente.lib_struc',
					'Referent.nom_complet',
					'Ficheprescription93.personne_a_integre',
					'Ficheprescription93.personne_pas_deplace',
					'Ficheprescription93.en_attente',
					// Allocataire
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Personne.sexe',
					'Prestation.rolepers',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Foyer.sitfam',
					'Dossier.matricule',
					// Fiche de prescription
					'Thematiquefp93.type',
					'Thematiquefp93.name',
					'Thematiquefp93.yearthema',
					'Categoriefp93.name',
				)
			),
			'tableau1b6' => array(
				'defaults' => array(
					'Search' => array(
						'rdv_structurereferente' => '0'
					)
				),
				'exportcsvcorpus' => array(
					// Rendez-vous
					'Rendezvous.daterdv',
					'Thematiquerdv.name',
					'Statutrdv.libelle',
					'Structurereferente.lib_struc',
					'Referent.nom_complet',
					// Allocataire
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Personne.sexe',
					'Prestation.rolepers',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Foyer.sitfam',
					'Dossier.matricule',
				)
			),
			'tableaub7' => array(
				'defaults' => array(
					'Search' => array(
						'rdv_structurereferente' => '1'
					)
				),
				'exportcsvcorpus' => array(
					// Allocataire
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Personne.sexe'
				)
			),
			'tableaub7d2typecontrat' => array(
				'defaults' => array(
					'Search' => array(
						'rdv_structurereferente' => '1'
					)
				),
				'exportcsvcorpus' => array(
					// Allocataire
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Personne.sexe'
				)
			),
			'tableaub7d2familleprofessionnelle' => array(
				'defaults' => array(
					'Search' => array(
						'rdv_structurereferente' => '1'
					)
				),
				'exportcsvcorpus' => array(
					// Allocataire
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Personne.sexe'
				)
			)
		)
	);

	/**
	 * Pour les tableaux de suivi PDV du CG 93, on ne met pas de limite à la
	 * mémoire disponible pour les actions historiser et exportcsvcorpus.
	 */
	Configure::write(
		'Tableauxsuivispdvs93.historiser.ini_set',
		array(
			'memory_limit' => '-1'
		)
	);
	Configure::write(
		'Tableauxsuivispdvs93.exportcsvcorpus.ini_set',
		array(
			'memory_limit' => '-1'
		)
	);
?>