<?php
	/**
	 * Paramétrage du module "Statistiques ministérielles"
	 *
	 * @param array
	 */
	Configure::write(
		'Statistiqueministerielle',
		array(
			/**
			 * Conditions permettant de définit les allocataires dans le champ
			 * des droits et devoirs.
			 *
			 * Ces conditions seront utilisées dans les différents tableaux.
			 *
			 * Modèles disponibles: Dossier, Detaildroitrsa, Foyer, Situationdossierrsa,
			 * Adressefoyer, Personne, Adresse, Prestation, Calculdroitrsa.
			 */
			'conditions_droits_et_devoirs' => array(
				'Situationdossierrsa.etatdosrsa' => array( '2', '3', '4' ),
				'Calculdroitrsa.toppersdrodevorsa' => '1'
			),
			/**
			 * Catégories et conditions des différents types de parcours du CG.
			 * Les catégories sont: professionnel, socioprofessionnel et social.
			 *
			 * Utilisé dans le tableau "1 - Orientation des personnes ... au sens du type de parcours..."
			 *
			 * Modèles disponibles (en plus de ceux disponibles de base, @see
			 * conditions_droits_et_devoirs): DspRev, Dsp, Orientstruct, Typeorient.
			 */
			'conditions_types_parcours' => array(
				'professionnel' => array(
					'Typeorient.id' => array( 3 )
				),
				'socioprofessionnel' => array(
					'Typeorient.id' => array( 1 )
				),
				'social' => array(
					'Typeorient.id' => array( 2 )
				),
			),
			/**
			 * Catégories (intitulés) et conditions des différents types de référents
			 * uniques (structures référentes) pour la tableau "2 - Organismes de
			 * prise en charge des personnes ... dont le référent unique a été désigné"
			 *
			 * Modèles disponibles (en plus de ceux disponibles de base, @see
			 * conditions_droits_et_devoirs): DspRev, Dsp, Orientstruct, Typeorient,
			 * Structurereferente.
			 */
			'conditions_indicateurs_organismes' => array(
				'Pôle emploi (PE) (2)' => array(
					'Structurereferente.typeorient_id' => 3,
					'Structurereferente.id' => array( 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 94, 95, 96, 97, 99, 113, 118, 133 ),
				),
				'Organisme public de placement professionnel autre que PE (maison de l\'emploi, PLIE, mission locale,...) (2)' => array(),
				'Entreprise de travail temporaire, agence privée de placement (2)' => array(),
				'Organisme d\'appui à la création et au développement d\'entreprise (2)' => array(),
				'Insertion par l\'activité économique (IAE) (uniquement si le référent appartient à l\'IAE) (2)' => array(),
				'Autres organismes de placement professionnel (2)' => array(),
				'Service du département ou de l\'agence départementale d\'insertion (ADI) (3)' => array(
					'Structurereferente.typeorient_id' => 2,
					'Structurereferente.id' => array( 2, 3, 8, 9, 14, 16, 18, 20, 22, 23, 26, 28, 30, 34, 39, 42, 44, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 98, 101, 117, 123, 126, 128, 132 )
				),
				'dont orientation professionnelle ou socioprofessionnelle' => array(),
				'dont orientation sociale' => array(
					'Structurereferente.typeorient_id' => 2,
					'Structurereferente.id' => array( 2, 3, 8, 9, 14, 16, 18, 20, 22, 23, 26, 28, 30, 34, 39, 42, 44, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 98, 101, 117, 123, 126, 128, 132 )
				),
				'Caf/Établissement des allocations familiales (2) (4)' => array(
					'Structurereferente.lib_struc ILIKE' => '%CAF%',
				),
				'Msa (2)' => array(
					'Structurereferente.lib_struc ILIKE' => '%MSA%',
				),
				'Caisse de prévoyance sociale (2) (4)' => array(),
				'CCAS, CIAS (2)' => array(
					'Structurereferente.lib_struc ILIKE' => '%Centre%Communal%Action%Sociale%',
				),
				'Associations d\'insertion (2)' => array(
					'Structurereferente.typeorient_id' => 2,
					'Structurereferente.id' => array( 98, 102, 104, 105, 106, 107, 108, 109, 110, 111, 112, 116, 119, 120, 121, 122, 124, 125, 127, 129, 130, 134 )
				),
				'Autres organismes d\'insertion (2)' => array(
					'Structurereferente.typeorient_id' => 1,
					'Structurereferente.id' => array( 1, 7, 10, 11, 13, 15, 17, 19, 21, 24, 27, 29, 31, 32, 33, 38, 40, 41, 43, 45, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 100, 114 )
				)
			),
			/**
			 * Catégories et délais permettant de différencier les types de contrats.
			 *
			 * Lorsqu'un contrat est signé avec Pôle Emploi, il s'agit à priori
			 * d'un PPAE, alors qu'un CER pro n'est pas signé avec Pôle Emploi.
			 *
			 * Voir aussi Statistiqueministerielle.conditions_types_parcours (les conditions sont ajoutées automatiquement):
			 *	- un CER pro est signé lors d'un type de parcours professionnel
			 *	- un CER social ou professionnel est signé lors d'un type de parcours social ou sociprofessionnel
			 *
			 * Modèles disponibles (en plus de ceux disponibles de base, @see
			 * conditions_droits_et_devoirs): Orientstruct, Typeorient,
			 * Contratinsertion, Structurereferentecer, Typeorientcer.
			 */
			'conditions_types_cers' => array(
				'ppae' => array(
					'Structurereferentecer.lib_struc ILIKE' => '%P%le Emploi%'
				),
				'cer_pro' => array(
					'NOT' => array(
						'Structurereferentecer.lib_struc ILIKE' => '%P%le Emploi%'
					)
				),
				'cer_pro_social' => array(),
			),
			/**
			 * Catégories et conditions permettant de différencier les organismes
			 * SPE et les organismes Hors SPE.
			 *
			 * Les catégories sont: SPE et HorsSPE.
			 *
			 * Modèles disponibles (en plus de ceux disponibles de base, @see
			 * conditions_droits_et_devoirs): DspRev, Dsp, Orientstruct, Typeorient,
			 * Structurereferente, Orientstructpcd, Typeorientpcd,
			 * Structurereferentepcd.
			 *
			 * Utilisé dans les tableaux:
			 *	- "4 - Nombre et profil des personnes réorientées..."
			 *	- "4a - Motifs des réorientations..."
			 *	- "4b - Recours à l'article L262-31"
			 */
			'conditions_organismes' => array(
				'SPE' => array(
					'Typeorient.id' => array( 3 )
				),
				'SPE_PoleEmploi' => array(
					'Structurereferente.lib_struc ILIKE' => '%P%le Emploi%'
				),
				'HorsSPE' => array(
					'NOT' => array(
						'Typeorient.id' => array( 3 )
					)
				)
			),
			/**
			 * Catégories et conditions permettant de différencier les motifs de
			 * réorientations. Une valeur NULL signifie que la donnée sera non
			 * disponible (ND).
			 *
			 * Modèles disponibles (en plus de ceux disponibles de base, @see
			 * conditions_droits_et_devoirs): DspRev, Dsp, Orientstruct, Typeorient,
			 * Structurereferente, Orientstructpcd, Typeorientpcd,
			 * Structurereferentepcd.
			 *
			 * Utilisé dans le tableau "4a - Motifs des réorientations...".
			 */
			'conditions_indicateurs_motifs_reorientation' => array(
				array(
					'orientation_initiale_inadaptee' => null,
					'changement_situation_allocataire' => null
				)
			),
			/**
			 * Conditions utilisées dans le tableau "Indicateurs de natures des
			 * actions des contrats".
			 *
			 * Modèles disponibles (en plus de ceux disponibles de base, @see
			 * conditions_droits_et_devoirs): Contratinsertion, Structurereferentecer,
			 * Typeorientcer.
			 * Modèles disponibles en plus pour le CG 93: Cer93, Cer93Sujetcer93.
			 */
			'conditions_natures_contrats' => array(
				// activités, stages ou formations destinés à acquérir des compétences professionnelles
				'01' => array(
					'OR' => array(
						array(
							'Cer93Sujetcer93.sujetcer93_id' => 2, // La formation
							'Cer93Sujetcer93.soussujetcer93_id' => 6, // Aide ou suivi pour la recherche de stage ou formation
							'Cer93Sujetcer93.valeurparsoussujetcer93_id' => array( 40, 41, 42, 43, 44 ),
						),
						array(
							'Cer93Sujetcer93.sujetcer93_id' => 2, // La formation
							'Cer93Sujetcer93.soussujetcer93_id' => 7, // Demande d'aide financière(FDIF, APRE)
						),
						array(
							'Cer93Sujetcer93.sujetcer93_id' => 1, // L'Emploi
							'Cer93Sujetcer93.soussujetcer93_id' => 2, // Reconversion professionnelle
						),
					)
				),
				// orientation vers le service public de l'emploi, parcours de recherche d'emploi
				'02' => array(
					'OR' => array(
						array(
							'Cer93Sujetcer93.sujetcer93_id' => 1, // L'Emploi
							'Cer93Sujetcer93.soussujetcer93_id' => 1, // Aide ou suivi pour la recherche d'emploi
							'Cer93Sujetcer93.valeurparsoussujetcer93_id' => array( 20, 1, 2, 10, 6, 26, 4, 14, 3, 18, 21, 19, 22, 11, 30, 29, 28, 27, 25, 24, 23, 12, 7, 9, 15, 16, 13, 5, 17 ), // En fait, toutes les valeurs
						),
						array(
							'Cer93Sujetcer93.sujetcer93_id' => 1, // L'Emploi
							'Cer93Sujetcer93.soussujetcer93_id' => 4, // Bilan professionnel et orientation
						),
						array(
							'Cer93Sujetcer93.sujetcer93_id' => 1, // L'Emploi
							'Cer93Sujetcer93.soussujetcer93_id' => 5, // Prescription vers une action :
							'Cer93Sujetcer93.valeurparsoussujetcer93_id' => array( 37, 34, 32 ), // Autre organisme, PLIE, Pôle Emploi
						),
					),
				),
				// mesures d'insertion par l'activité économique (IAE)
				'03' => array(
					'Cer93Sujetcer93.sujetcer93_id' => 1, // L'Emploi
					'Cer93Sujetcer93.soussujetcer93_id' => 5, // Prescription vers une action :
					'Cer93Sujetcer93.valeurparsoussujetcer93_id' => 35, // SIAE
				),
				// aide à la réalisation d’un projet de création, de reprise ou de poursuite d’une activité non salariée
				'04' => array(
					'OR' => array(
						array(
							'Cer93Sujetcer93.sujetcer93_id' => 1, // L'Emploi
							'Cer93Sujetcer93.soussujetcer93_id' => 3, // Création d'entreprise
						),
						array(
							'Cer93Sujetcer93.sujetcer93_id' => 1, // L'Emploi
							'Cer93Sujetcer93.soussujetcer93_id' => 5, // Prescription vers une action :
							'Cer93Sujetcer93.valeurparsoussujetcer93_id' => 36, // Organisme d'appui à la création d'entreprise
						),
					)
				),
				// emploi aidé (hors CIA)
				'05' => false,
				// contrat d'insertion par l'activité (CIA) (3)
				'06' => false,
				// emploi non aidé
				'07' => false,
				// actions facilitant le lien social (développement de l\'autonomie sociale, activités collectives, ...)
				'08' => array(
					'OR' => array(
						array(
							'Cer93Sujetcer93.sujetcer93_id' => 2, // La formation
							'Cer93Sujetcer93.soussujetcer93_id' => 6, // Aide ou suivi pour la recherche de stage ou formation
							'Cer93Sujetcer93.valeurparsoussujetcer93_id' => array( 38, 39 ),
						),
						array(
							'Cer93Sujetcer93.sujetcer93_id' => 3, // L'Autonomie sociale
							'Cer93Sujetcer93.soussujetcer93_id' => 10, // Accés aux droits
							'Cer93Sujetcer93.valeurparsoussujetcer93_id' => array( 56, 57, 58, 59, 60 ),
						),
						array(
							'Cer93Sujetcer93.sujetcer93_id' => 3, // L'Autonomie sociale
							'Cer93Sujetcer93.soussujetcer93_id' => 9, // actions liées à la resolution de difficultés en lien avec la parentalité
							'Cer93Sujetcer93.valeurparsoussujetcer93_id' => 54 // Action visant à lutter contre la maltraitance et la violence
						),
					)
				),
				// actions facilitant la mobilité (permis de conduire, acquisition / location de véhicule, frais de transport...)
				'09' => array(
					'Cer93Sujetcer93.sujetcer93_id' => 3, // L'Autonomie sociale
					'Cer93Sujetcer93.soussujetcer93_id' => 10, // Accés aux droits
					'Cer93Sujetcer93.valeurparsoussujetcer93_id' => 55 // Accés à la mobilité
				),
				// actions visant l\'accès à un logement, relogement ou à l\'amélioration de l\'habitat
				'10' => array(
					'Cer93Sujetcer93.sujetcer93_id' => 5, // Le Logement
					'Cer93Sujetcer93.soussujetcer93_id' => array( 22, 18, 19, 20, 21 ), // ...
				),
				// actions facilitant l\'accès aux soins
				'11' => array(
					'Cer93Sujetcer93.sujetcer93_id' => 4, // La santé
					'Cer93Sujetcer93.soussujetcer93_id' => array( 15, 14, 17, 11, 12, 16 ), // ...
				),
				// actions visant l\'autonomie financière (constitution d\'un dossier de surendettement,...)
				'12' => array(
					'Cer93Sujetcer93.sujetcer93_id' => 3, // L'Autonomie sociale
					'Cer93Sujetcer93.soussujetcer93_id' => 8, // actions liées à la resolution de difficultés financières
					'Cer93Sujetcer93.valeurparsoussujetcer93_id' => array( 47, 49, 48, 45, 46, 61 ) // ...
				),
				// actions visant la famille et la parentalité (soutien familial, garde d\'enfant, ...)
				'13' => array(
					'Cer93Sujetcer93.sujetcer93_id' => 3, // L'Autonomie sociale
					'Cer93Sujetcer93.soussujetcer93_id' => 9, // actions liées à la resolution de difficultés en lien avec la parentalité
					'Cer93Sujetcer93.valeurparsoussujetcer93_id' => array( 52, 50, 53, 51 ) // ...
				),
				// lutte contre l\'illettrisme ; acquisition des savoirs de base
				'14' => false,
				// autres actions
				'15' => array(
					'Cer93Sujetcer93.sujetcer93_id' => 1, // L'Emploi
					'Cer93Sujetcer93.soussujetcer93_id' => 5, // Prescription vers une action :
					'Cer93Sujetcer93.valeurparsoussujetcer93_id' => array( 33, 31 ), // ...
				),
			)
		)
	);
?>