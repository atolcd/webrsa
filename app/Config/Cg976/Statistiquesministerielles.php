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
					'Typeorient.parentid' => array( 1 )
				),
				'socioprofessionnel' => array(
					'Typeorient.parentid' => array( 2 )
				),
				'social' => array(
					'Typeorient.parentid' => array( 3 )
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
					'Typeorient.id' => 5,
					'Structurereferente.id' => 2
				),
				'Organisme public de placement professionnel autre que PE (maison de l\'emploi, PLIE, mission locale,...) (2)' => array(),
				'Entreprise de travail temporaire, agence privée de placement (2)' => array(),
				'Organisme d\'appui à la création et au développement d\'entreprise (2)' => array(),
				'Insertion par l\'activité économique (IAE) (uniquement si le référent appartient à l\'IAE) (2)' => array(),
				'Autres organismes de placement professionnel (2)' => array(),
				'Service du département ou de l\'agence départementale d\'insertion (ADI) (3)' => array(
					'Typeorient.parentid' => array( 4, 6 ),
					'NOT' => array( 'Typeorient.id' => array( 3, 8 ) )
				),
				'dont orientation professionnelle ou socioprofessionnelle' => array(
					'Typeorient.parentid' => array( 4 ),
					'NOT' => array( 'Typeorient.id' => array( 3, 8 ) )
				),
				'dont orientation sociale' => array(
					'Typeorient.parentid' => array( 6 ),
					'NOT' => array( 'Typeorient.id' => array( 3, 8 ) )
				),
				'Caf/Établissement des allocations familiales (2) (4)' => array(),
				'Msa (2)' => array(
					'Typeorient.id' => array( 3, 8 )
				),
				'Caisse de prévoyance sociale (2) (4)' => array(),
				'CCAS, CIAS (2)' => array(),
				'Associations d\'insertion (2)' => array(),
				'Autres organismes d\'insertion (2)' => array(),
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
					'Contratinsertion.structurereferente_id' => array( 23 )
				),
				'cer_pro' => array(
					'NOT' => array(
						'Contratinsertion.structurereferente_id' => array( 23 )
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
					'Structurereferente.id' => array(
						// Pôle Emploi
						23,
						// EMPLOI - MSA
						21,
						// 	Mission Nouveaux Emplois (MNE)
						39
					)
				),
				'SPE_PoleEmploi' => array(
					'Structurereferente.id' => array(
						// Pôle Emploi
						23
					)
				),
				'HorsSPE' => array(
					// Qui ne sont pas...
					'NOT' => array(
						'Structurereferente.id' => array(
							// Pôle Emploi
							23,
							// EMPLOI - MSA
							21,
							// 	Mission Nouveaux Emplois (MNE)
							39
						)
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
			'conditions_natures_contrats' => array()
		)
	);
?>