<?php
	/**
	 * Le chemin complet vers la base de l'application (utilisé par certains shells)
	 *
	 * @default: null
	 */
	Configure::write( 'Cg.departement', env('CD_DEPARTEMENT') );

	Configure::write( 'MultiDomainsTranslator', array( 'prefix' => 'cg'.Configure::read( 'Cg.departement' ) ) );
	Configure::write( 'Utilisateurs.multilogin', true );
	Configure::write( 'with_parentid', false ); // true = cg66
	Configure::write( 'nom_form_ci_cg', Configure::read( 'MultiDomainsTranslator.prefix') );
	Configure::write( 'CG.cantons', false ); // false = cg93, true = cg66
	Configure::write( 'Zonesegeographiques.CodesInsee', true );
	//Configure::write( 'Etatliquidatif.limit', 1000 ); // INFO: commenter pour ne pas avoir de limite

	/**
	 * Champs spécifique selon le CG pour le formulaire de l'APRE
	 * @default: 'cg93' (pour le CG93), 'cg66' (pour le CG66)
	 */
	Configure::write( 'nom_form_apre_cg', Configure::read( 'MultiDomainsTranslator.prefix') );

	/**
	 * Limit pour le nombre de documents à éditer dans la cohorte par orientation
	 * @default: 2000
	 */
	Configure::write( 'nb_limit_print', 2000 );

	/**
	 * Permet à l'administrateur d'accéder à toutes les parties de l'application
	 * normalement bloquées aux seules parties de paramétrage renseignées.
	 * Voir AppController::_isAdminAction().
	 * @default false
	 */
	Configure::write( 'Admin.unlockall', false );

	/**
	 * Montant maximal des apres complémentaires pour une personne au cours
	 * de la période de temps définie par Apre.periodeMontantMaxComplementaires.
	 * @default 2600
	 * cg66 -> 3000
	 */
	Configure::write( 'Apre.montantMaxComplementaires', 2600 );

	/**
	 * Période (en nombre d'année) utilisée pour la calcul du montant maximal
	 * des apres complémentaires pour une personne.
	 * @default 1 (une année du 01/01 au 31/12)
	 */
	Configure::write( 'Apre.periodeMontantMaxComplementaires', 1 );

	/**
	 * Paramètre à renseigner pour l'utilisation du bon MVC
	 * @default: vide (pour le CG93), sinon '66' pour le CG66
	 */
	Configure::write( 'Apre.suffixe', null );

	/**
	 * Paramètres à renseigner pour les APRE's forfaitaires
	 * FIXME: doc
	 */
	Configure::write( 'Apre.forfaitaire.montantbase', 400 );
	Configure::write( 'Apre.forfaitaire.montantenfant12', 100 );
	Configure::write( 'Apre.forfaitaire.nbenfant12max', 4 );

	/**
	 * FIXME: vérifier l'existance et les droits
	 * FIXME: accès concurrents ?
	 */
	Configure::write( 'Cohorte.dossierTmpPdfs', APP.'tmp/files/pdf' );

	/**
	 * Permet à l'administrateur d'ajouter une adresse pour l'utilisateur
	 * connecté à l'application.
	 * Besoin pour le CG66 lors de l'impression des courriers de rendez-vous
	 * @default false
	 */
	Configure::write( 'User.adresse', false ); // true = cg66, false = cg93

	/**
	 * Paramètre à renseigner pour l'affichage de la bonne fiche de candidature
	 * @default: 'cg93' (pour le CG93), sinon 'cg66' pour le CG66
	 */
	Configure::write( 'ActioncandidatPersonne.suffixe', Configure::read( 'MultiDomainsTranslator.prefix' ) );

	/**
	 *   Paramètre à renseigner dans le cas d'un affichage plus large du menu du dossier
	 *   @default: false
	 */
	Configure::write( 'UI.menu.large', true );

	/**
	 *   Paramètre à renseigner pour le CG58 pour le lien pointant sur leur application
	 */
	Configure::write( 'UI.menu.lienDemandeur', false );

	/**
	 *   Paramètre à renseigner pour déterminer la plage horaire que l'on dispose pour pouvoir accéder
	 *   aux différents boutons possédant ce paramètre.
	 *   On met 48, pour 48H car la plage de date va de minuit à minuit et donc un formulaire saisi
	 *   un jour à 18h ne serait plus modifiable/validable/supprimable le lendemain.
	 *   @default: 48 --> nombre d'heures pendant lesquelles on pourra accéder au bouton
	 */
	Configure::write( 'Periode.modifiable.nbheure', 48 );
	Configure::write( 'Periode.modifiablecer.nbheure', 240 );
	Configure::write( 'Periode.modifiableorientation.nbheure', 240 );

	/**
	 * Champs spécifique selon le CG pour le formulaire des PDOs
	 * @default: 'cg93' (pour le CG93), 'cg66' (pour le CG66)
	 */
	Configure::write( 'nom_form_pdo_cg', Configure::read( 'MultiDomainsTranslator.prefix' ) );

	/**
	 * Champs spécifique selon le CG pour le formulaire du bilan de parcours / Fiche de saisine
	 * @default: 'cg93' (pour le CG93), 'cg66' (pour le CG66)
	 */
	Configure::write( 'nom_form_bilan_cg', Configure::read( 'MultiDomainsTranslator.prefix' ) );

	/**
	 * Champs spécifique selon le CG pour le formulaire du Contrat Unqiue d'Insertion
	 * @default: 'cg93' (pour le CG93), 'cg66' (pour le CG66)
	 */
	Configure::write( 'nom_form_cui_cg', Configure::read( 'MultiDomainsTranslator.prefix' ) );

	/**
	 *   Paramètre pour connaître le pourcentage du 1er versement, lors d'un versement en 2 fois,
	 *   pour les apres présentes dans un état liquidatif
	 *   @default: 60 ---> avant 40 %
	 */
	Configure::write( 'Apre.pourcentage.montantversement', 60 );

	/**
	 * Permet de désactiver l'utilisation des jetons sur les dossiers
	 * Si à false, on utilise les jetons sur les dossiers.
	 * Si à true, on n'utilise pas les jetons sur les dossiers.
	 * @default false
	 */
	Configure::write( 'Jetons2.disabled', false );

	/**
	 *   Paramètre pour définir les taux dans le formulaire du CUI :
	 *   Cui.taux.fixe                   => Taux fixé par l'arrêté du Préfet de région (en % )
	 *   Cui.taux.prisencharge           => Taux de prise en charge effectif si le Conseil Départemental fixe
	 *                                       un taux supérieur au taux fixé par le Préfet de région (en %)
	 *   Cui.taux.financementexclusif    => Financement exclusif du Conseil Départemental, Si oui, taux (en %)
	 *   @default: à définir par chaque CG
	 */
	Configure::write( 'Cui.taux.fixe', 60 );
	Configure::write( 'Cui.taux.prisencharge', 70 );
	Configure::write( 'Cui.taux.financementexclusif', 60 );

	/**
	 * Permet la pagination progressive, cad. qu'on ne compte pas le nombre
	 * d'enregistrements totaux, mais que l'on regarde seulement si la
	 * page suivante existe.
	 *
	 * Ce paramètre concerne toutes les paginations.
	 *
	 * @default: false (pagination normale)
	 */
	Configure::write( 'Optimisations.progressivePaginate', true );

	/**
	 * Surcharge de la pagination progressive pour les cohorte d'orientations, demandes orientées
	 */
	Configure::write( 'Optimisations.Cohortes_orientees.progressivePaginate', false );

	/**
	 * Surcharge de la pagination progressive pour l'action nouveaux
	 * du contrôleur Cohortesci.
	 */
	Configure::write( 'Optimisations.Cohortesci_nouveaux.progressivePaginate', false );

	/**
	 * Surcharge de la pagination progressive pour l'action avisdemande
	 * du contrôleur Cohortespdos.
	 */
	Configure::write( 'Optimisations.Cohortespdos_avisdemande.progressivePaginate', false );

	/**
	 * Surcharge de la pagination progressive pour l'action cohorte
	 * du contrôleur Relancesnonrespectssanctionseps93.
	 */
	Configure::write( 'Optimisations.Relancesnonrespectssanctionseps93_cohorte.progressivePaginate', false );
	
	/**
	 * Variables apparaissant dans la fiche de calcul du journal de traitement d'une PDO
	 */
	Configure::write( 'Traitementpdo.fichecalcul_coefannee1', 34 );
	Configure::write( 'Traitementpdo.fichecalcul_coefannee2', 25 );

	Configure::write( 'Traitementpdo.fichecalcul_cavntmax', 80300 );
	Configure::write( 'Traitementpdo.fichecalcul_casrvmax', 32100 );

	Configure::write( 'Traitementpdo.fichecalcul_abattbicvnt', 71 );
	Configure::write( 'Traitementpdo.fichecalcul_abattbicsrv', 50 );
	Configure::write( 'Traitementpdo.fichecalcul_abattbncsrv', 34 );

	/**
	 * Affiche ou non l'alerte de fin de session et exécute la redirection
	 */
	Configure::write( 'alerteFinSession', false );

	/**
	 * Variable contenant un array avec les id des typesorients du social, sociopro et de l'emploi
	 */
	Configure::write( 'Orientstruct.typeorientprincipale', array( 'Socioprofessionnelle' => array( 1 ), 'Social' => array( 2 ), 'Emploi' => array( 3 ) ) );

	/**
	 * Configuration pour le dialogue avec un CMS (système de gestion de contenu)
	 * comme Alfresco, servant à stocker les PDF
	 */
	Configure::write(
		'Cmis',
		array(
			'url' => env('CMIS_URL'),
			'username' => env('CMIS_USERNAME'),
			'password' => env('CMIS_PASSWORD'),
			'prefix' => env('CMIS_PREFIX'),
		)
	);

	/**
	 * Permet de rajouter des conditions aux conditions de recherches suivant
	 * le paramétrage des service référent dont dépend l'utilisateur connecté.
	 *
	 * @default false
	 */
	Configure::write( 'Recherche.qdFilters.Serviceinstructeur', false );

	/**
	 * Permet de définir si on peut ou non ajouter une nouvelle orientation à un dossier
	 *   - toppersdrodevorsa     --> par défaut 1 (personne soumise à droit et devoir)
	 *   - situationetatdosrsa   --> par défaut array( 'Z', 2, 3, 4) (dossier dans un droit ouvert et versable)
	 *
	 * INFO: depuis le passage en CakePHP 2.x, il faut mettre les valeurs numériques entre apostrophes.
	 */
	Configure::write( 'AjoutOrientationPossible.toppersdrodevorsa', '1' ); // aucune distincton sur le soumis à droit et devoir array( 0,1 )
	Configure::write( 'AjoutOrientationPossible.situationetatdosrsa', array( 'Z', '2', '3', '4', '5', '6' ) );

	/**
	 * Permet de spécifier si les recherches sur l'identifiant Pôle Emploi d'un
	 * allocataire doivent se faire sur les 8 derniers chiffres de l'identifiant
	 * (true) ou sur la totalité de celui-ci (false).
	 *
	 * @default false
	 */
	Configure::write( 'Recherche.identifiantpecourt', false );

	/**
	 * Variable contenant un array avec les id des typesorients du grand social et de l'emploi
	 */
	Configure::write( 'Typeorient.emploi_id', 1 );

	/**
	 * Durée du délai (intervalle) entre la date de validation de l'orientation et la date
	 * d'inscription au Pôle Emploi
	 *
	 * Voir le document app/docs/Documentation administrateurs.odt, partie
	 * "Intervalles PostgreSQL"
	 */
	Configure::write( 'Selectionnoninscritspe.intervalleDetection', '6 weeks' );

	/**
	 * Gestion des anomalies
	 */
	Configure::write( 'Optimisations.Gestionsanomaliesbdds.progressivePaginate', false );
	Configure::write( 'Situationdossierrsa.etatdosrsa.ouvert', array( 'Z', '2', '3', '4' ) );

	/**
	 * 	Délai pour la détection des CERs arrivant à échéance
	 */
	Configure::write( 'Criterecer.delaiavanteecheance', '1 months' );

	/**
	 * 	Pour le CG66: Délai durant lequel les dossiers d'EP ne seront ni détectables, ni sélectionnables dans la corbeille
	 * 	des dossiers devant passer en EP
	 * 	@default: null
	 * 	@CG66: 1 month 15 days
	 * 	Voir le document app/docs/Documentation administrateurs.odt, partie
	 * 	"Intervalles PostgreSQL"
	 */
	Configure::write( 'Dossierep.delaiavantselection', null );

	/**
	 * 	Valeurs prises par le champ natpf pour déterminer si le dossier est en RSA Socle
	 */
	Configure::write( 'Detailcalculdroitrsa.natpf.socle', array( 'RSD', 'RSI', 'RSU', 'RSJ' ) );

	/**
	 * Les conditions sur la dernière entrée venant de Pôle Emploi, utilisées pour la recherche des radiés
	 * de Pôle Emploi.
	 * Si ce paramétrage n'est pas présent, cela équivaut à:
	 * Configure::write( 'Selectionradies.conditions', array( 'Historiqueetatpe.etat' => 'radiation' ) );
	 *
	 * - radiation ou cessation
	 * -  depuis au moins 70 jours
	 * -  dont le code de radiation ou de cessation n'est pas:
	 *	* de 11 à 34
	 *	* 41, 43, 44, 45, 46, 48, 49
	 *	* 72
	 */
	Configure::write(
		'Selectionradies.conditions',
		array(
			'Historiqueetatpe.date < ( DATE( NOW() ) - INTERVAL \'70 days\' )',
			'Historiqueetatpe.etat' => array( 'radiation', 'cessation' ),
			'NOT' => array(
				'Historiqueetatpe.code' => array( '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '41', '43', '44', '45', '46', '48', '49', '72' )
			)
		)
	);

	/**
	 * Permet de rechercher le dernier dossier d'un allocataire dans la table
	 * derniersdossiersallocataires (il faudra mettre le shell Derniersdossiersallocataires
	 * en tâche planifiée) afin de se passer d'une sous-requête coûteuse dans les
	 * recherches.
	 *
	 * @param boolean
	 * @default null
	 */
	Configure::write( 'Optimisations.useTableDernierdossierallocataire', true );

	/**
	 * Permet de donner des valeurs par défaut au formulaire de cohorte des PDOs (cohortespdos/avisdemande)
	 */
	Configure::write(
		'Filtresdefaut.Cohortespdos_avisdemande',
		array(
			'Search' => array(
				'Dossier' => array(
					'dernier' => '1',
				)
			)
		)
	);

	/**
	 * Permet de donner des valeurs par défaut au formulaire de cohorte des PDOs (cohortespdos/valide)
	 */
	Configure::write(
		'Filtresdefaut.Cohortespdos_valide', Configure::read( 'Filtresdefaut.Cohortespdos_avisdemande' )
	);

	/**
	 * Permet-on à l'utilisateur de se reconnecter alors que sa session n'est pas
	 * clôturée ni expirée ?
	 *
	 * @var boolean
	 * @default null (false)
	 */
	Configure::write( 'Utilisateurs.reconnection', true );

	/**
	 * Lorsque apache est utilisé en mode CGI, les fonctions apache_get_version()
	 * et apache_get_modules() ne sont pas disponibles. Du coup, on passe par
	 * la fonction exec() pour interroger directement le binaire apache.
	 *
	 * Ce paramètre de configuration permet de spécifier le chemin complet vers
	 * le binaire apache.
	 *
	 * @default /usr/sbin/apache2
	 */
	Configure::write( 'apache_bin', '/usr/sbin/apache2' );

	/**
	 * Conditions supplémentaires utilisées lors de la sélection des dossiers pour
	 * une commission d'EP (url: /dossierseps/choose/...).
	 *
	 * Au CG 58, il faut que les dossiers sélectionnables soient dans un droit
	 * ouvert et que les allocataires soient soumis à droits et devoirs.
	 *
	 * @param array
	 */
	Configure::write(
		'Dossierseps.conditionsSelection',
		array(
			'Situationdossierrsa.etatdosrsa' => array( 'Z', '2', '3', '4', '5', '6' ),
			'Calculdroitrsa.toppersdrodevorsa' => '1',
		)
	);

	/**
	 * Configuration de la gestion des mots de passe aléatoires.
	 *
	 * Configuration par-défaut:
	 * <pre>
	 * array(
	 *	// Permet-on l'utilisation de la fonctionnalité "Mot de passe oublié" sur la page de login ?
	 * 	'mail_forgotten' => false,
	 *	// Quelles sont les possibilités lors de la création d'un mot de passe aléatoire ?
	 * 	'possible' => array(
	 *		// Nombres -> de 0 à 9
	 * 		'number' => true,
	 *		// Minuscules -> de a à z
	 * 		'lower' => true,
	 *		// Majuscules -> de a à z
	 * 		'upper' => true,
	 *		// Caractères spéciaux -> ,;.!?*+-
	 * 		'symbol' => true,
	 *		// Supprime-t'on les caractères équivoques (1, i, l, I et 0, o, O) ?
	 * 		'typesafe' => true,
	 * 	),
	 *	// Quels sont les caractères obligatoires ?
	 * 	'required' => array(
	 *		// Au moins un nombre ?
	 * 		'number' => true,
	 *		// Au moins une lettre en minuscule ?
	 * 		'lower' => false,
	 *		// Au moins une lettre en majuscule ?
	 * 		'upper' => false,
	 *		// Au moins un caractère spécial ?
	 * 		'symbol' => true,
	 * 	),
	 * )
	 * </pre>
	 */
	Configure::write(
		'Password',
		array(
			'mail_forgotten' => false,
			'possible' => array(
				'number' => true,
				'lower' => true,
				'upper' => true,
				'symbol' => true,
				'typesafe' => true,
			),
			'required' => array(
				'number' => true,
				'lower' => false,
				'upper' => false,
				'symbol' => true,
			),
		)
	);

    /**
	 * Variable contenant une chaîne de caractères (stockée en base) pour le
	 * n° de convention annuelle d'objectifs et de moyens
     * (unqiue par année et qui devant être changé chaque année)
     * Cui.numconventionobj
	 */
    Configure::write( 'Cui.Numeroconvention', '066130000100' );

    /**
	 * Configuration de l'utilisation des thématiques de RDV du module RDV
	 */
	Configure::write('Rendezvous.useThematique', false );

	/**
	 * Etats du dossier RSA pris en compte pour trouver les dossiers à fusionner
	 * dans la gestion des doublons complexes.
	 *
	 * Par défaut, on cherche à fusionner les dossiers créés dans l'application
	 * avec les dossiers envoyer par les flux CAF.
	 *
	 * @param array
	 * @default array( 'Z' )
	 */
	Configure::write( 'Gestiondoublon.Situationdossierrsa2.etatdosrsa', array( 'Z' ) );

	/**
	 * Filtres par défaut du moteur de recherche des doublons complexes.
	 */
	Configure::write(
		'Filtresdefaut.Gestionsdoublons_index',
		array(
			'Search' => array(
				'Situationdossierrsa' => array(
					'etatdosrsa_choice' => true,
					'etatdosrsa' => array( '0', '1', '2', '3', '4', '5', '6' ),
				)
			)
		)
	);

	/**
	 * Filtres par défaut des indicateurs de suivi
	 */
	Configure::write(
		'Filtresdefaut.Indicateurssuivis_search',
		array(
			'Search' => array(
				'Calculdroitrsa' => array(
					'toppersdrodevorsa' => '1'
				),
				'Dossier' => array(
					'dernier' => '1',
				),
				'Pagination' => array(
					'nombre_total' => true
				),
				'Situationdossierrsa' => array(
					'etatdosrsa_choice' => '1',
					'etatdosrsa' => array( 'Z', '2', '3', '4' )
				)
			)
		)
	);

	/**
	 * Filtres par défaut de la recherche par allocataires ayant quitté le département
	 */
	Configure::write(
		'Filtresdefaut.Demenagementshorsdpts_search',
		array(
			'Search' => array(
				'Dossier' => array(
					'dernier' => '1',
				),
				'Pagination' => array(
					'nombre_total' => false
				),
				'Situationdossierrsa' => array(
					'etatdosrsa_choice' => '1',
					'etatdosrsa' => array( '2', '3', '4' )
				)
			)
		)
	);

	/**
	 * Permet de parcourir les détails des allocataires n'ayant pas de prestation
	 * RSA dans le menu du dossier.
	 *
	 * Permet également d'obtenir la liste des dossiers dans lesquels l'allocataire
	 * ne possède plus de prestation RSA mais pour lesquels il existe des enregistrements
	 * dans les tables métier de chacun des modules impactés.
	 *
	 * Permet également de voir dans la page de résumé du dossier les autres dossiers
	 * dans lesquels se trouve l'allocataire avec des enregistrements dans les
	 * tables métiers et pas de prestation.
	 *
	 * Les tables concernées sont: actionscandidats_personnes, apres, bilansparcours66,
	 * contratsinsertion, cuis, dsps, dsps_revs, entretiens, fichesprescriptions93,
	 * memos, orientsstructs, personnes_referents, propospdos, questionnairesd1pdvs93,
	 * questionnairesd2pdvs93, rendezvous.
	 *
	 * ATTENTION: impacte les performances de l'application.
	 *
	 * @var boolean
	 * @default null
	 */
	Configure::write( 'AncienAllocataire.enabled', true );

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
					'Typeorient.id' => (array)Configure::read( 'Typeorient.emploi_id' )
				),
				'socioprofessionnel' => array(
					'Typeorient.id IS NULL'
				),
				'social' => array(
					'NOT' => array(
						'Typeorient.id' => (array)Configure::read( 'Typeorient.emploi_id' )
					)
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
					'Structurereferente.id' => 2
				),
				'Organisme public de placement professionnel autre que PE (maison de l\'emploi, PLIE, mission locale,...) (2)' => array(
					'Structurereferente.id' => array( 23, 24, 25, 28, 29, 30 )
				),
				'Entreprise de travail temporaire, agence privée de placement (2)' => array(),
				'Organisme d\'appui à la création et au développement d\'entreprise (2)' => array(
					'Structurereferente.id' =>  16
				),
				'Insertion par l\'activité économique (IAE) (uniquement si le référent appartient à l\'IAE) (2)' => array(),
				'Autres organismes de placement professionnel (2)' => array(
					'Typeorient.id' => 1,
					'NOT' => array(
						'Structurereferente.id' => array( 2, 23, 24, 25, 26, 16, 28, 29, 30  )
					)
				),
				'Service du département ou de l\'agence départementale d\'insertion (ADI) (3)' => array(
					'Structurereferente.id'=> array( 1, 4, 7, 8, 9, 10, 11, 12, 13, 14, 15 )
				),
				'dont orientation professionnelle ou socioprofessionnelle' => array(),
				'dont orientation sociale' => array(
					'Structurereferente.id' => array( 1, 4, 7, 8, 9, 10, 11, 12, 13, 14, 15 )
				),
				'Caf (2)' => array(
					'Structurereferente.id' => array( 27 )
				),
				'Msa (2)' => array(
					'Structurereferente.id' => array( 3, 26 )
				),
				'CCAS, CIAS (2)' => array(
					'Structurereferente.id' => array( 6 )
				),
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
					'Contratinsertion.structurereferente_id' => array( 2 )
				),
				'cer_pro' => array(
					'NOT' => array(
						'Contratinsertion.structurereferente_id' => array( 2 )
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
						2,
						// Missions locales
						23, 24, 25
					)
				),
				'SPE_PoleEmploi' => array(
					'Structurereferente.id' => array(
						// Pôle Emploi
						2
					)
				),
				'HorsSPE' => array(
					// Qui ne sont pas...
					'NOT' => array(
						'Structurereferente.id' => array(
							// Pôle Emploi
							2,
							// Missions locales
							23, 24, 25
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
			)
		)
	);

	/**
	 * Paramétrage du module "Statistiques DREES"
	 *
	 * @param array
	 */
	Configure::write(
		'Statistiquedrees',
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
			 * Catégories (intitulés) et conditions des différents types de référents
			 * uniques (structures référentes) pour la tableau "2 - Organismes de
			 * prise en charge des personnes ... dont le référent unique a été désigné"
			 *
			 * Modèles disponibles (en plus de ceux disponibles de base, @see
			 * conditions_droits_et_devoirs): DspRev, Dsp, Orientstruct, Typeorient,
			 * Structurereferente.
			 */
			'organismes' => array(
				/*
				 * SPE
				 */

				// Pôle emploi
				'orientes_pole_emploi' => array (74, 75, 76, 77, 78, 133, 99, 80, 83, 84, 85, 86, 87, 88, 113, 79, 89, 90, 118, 91, 94, 95, 96, 97, 81, 82),

				// Mission locale
				'spe_mission_locale' => array (),

				// Maison de l'emploi (MDE), Maison de l’emploi et de la formation (MDEF), Plan local pluriannuel pour l'insertion et l'emploi (PLIE), Cap Emploi
				'spe_mde_mdef_plie' => array(102, 110),

				// Structure d'appui à la création et au développement d'entreprise
				'spe_creation_entreprise' => array(),

				// Structure d'insertion par l'activité économique (IAE)
				'spe_iae' => array(),

				// Autre organisme de placement professionnel ou de formation professionnelle
				'spe_autre_placement_pro' => array(),

				/*
				 * Hors SPE
				 */

				// Service du Conseil départemental / territorial
				'hors_spe_ssd' => array(108, 109, 2, 14, 23, 22, 16, 28, 132, 20, 117, 34, 18, 39, 42, 44, 50, 26, 52, 53, 55, 48, 57, 58, 59, 60, 30, 51, 3, 8, 9, 49, 54, 56, 46, 135, 127, 47, 126),

				// Caisse d'allocations familiales (Caf)
				'hors_spe_caf' => array(129),

				// Mutualité sociale agricole (Msa)
				'hors_spe_msa' => array(130),

				// Centre communal/intercommunal d'action sociale (CCAS/CIAS)
				'hors_spe_ccas_cias' => array(),

				// Autre organisme
				'hors_spe_autre_organisme' => array(136, 100, 19, 24, 114, 11, 10, 13, 17, 1, 21, 15, 67, 31, 29, 33, 32, 70, 41, 40, 45, 43, 61, 62, 63, 64, 27, 38, 65, 66, 69, 68, 7, 71, 72, 73, 104, 119, 116, 134, 122, 112, 128, 123, 105, 121, 106, 98, 107, 125, 120, 101, 111, 124),

			),
			/**
			 *
			 *
			 */
			'actions_cer' => array (
				//… au moins une action visant à trouver des activités, stages ou formations destinés à acquérir des compétences professionnelles
				'acquerir_competences_pro' => array (
					'sujetcer93_id' => array (),
					'soussujetcer93_id' => array (2, 7),
					'valeurparsoussujetcer93_id' => array (42, 43, 44),
				),
				//… au moins une action visant à s'inscrire dans un parcours de recherche d'emploi
				'parcours_recherche_emploi' => array (
					'sujetcer93_id' => array (),
					'soussujetcer93_id' => array (1, 4),
					'valeurparsoussujetcer93_id' => array (32, 34, 37),
				),
				//… au moins une action visant à s'inscrire dans une mesure d'insertion par l'activité économique (IAE)
				'iae' => array (
					'sujetcer93_id' => array (),
					'soussujetcer93_id' => array (),
					'valeurparsoussujetcer93_id' => array (35),
				),
				//… au moins une action aidant à la réalisation d’un projet de création, de reprise ou de poursuite d’une activité non salariée 
				'activite_non_salariale' => array (
					'sujetcer93_id' => array (),
					'soussujetcer93_id' => array (3),
					'valeurparsoussujetcer93_id' => array (36),
				),
				//… au moins une action visant à trouver un emploi aidé 
				'emploi_aide' => array (
					'sujetcer93_id' => array (),
					'soussujetcer93_id' => array (),
					'valeurparsoussujetcer93_id' => array (),
				),
				//… au moins une action visant à trouver un emploi non aidé
				'emploi_non_aide' => array (
					'sujetcer93_id' => array (),
					'soussujetcer93_id' => array (),
					'valeurparsoussujetcer93_id' => array (),
				),
				//… au moins une action visant à faciliter le lien social (développement de l'autonomie sociale, activités collectives,…)
				'lien_social' => array (
					'sujetcer93_id' => array (),
					'soussujetcer93_id' => array (),
					'valeurparsoussujetcer93_id' => array (38, 39, 54, 56, 57, 58, 59, 60),
				),
				//… au moins une action visant la mobilité (permis de conduire, acquisition / location de véhicule, frais de transport…)
				'mobilite' => array (
					'sujetcer93_id' => array (),
					'soussujetcer93_id' => array (),
					'valeurparsoussujetcer93_id' => array (55),
				),
				// … au moins une action visant l'accès à un logement, au relogement ou à l'amélioration de l'habitat
				'acces_logement' => array (
					'sujetcer93_id' => array (5),
					'soussujetcer93_id' => array (),
					'valeurparsoussujetcer93_id' => array (),
				),
				//… au moins une action visant l'accès aux soins
				'acces_soins' => array (
					'sujetcer93_id' => array (4),
					'soussujetcer93_id' => array (),
					'valeurparsoussujetcer93_id' => array (),
				),
				//… au moins une action visant l'autonomie financière (constitution d'un dossier de surendettement,...)
				'autonomie_financiere' => array (
					'sujetcer93_id' => array (),
					'soussujetcer93_id' => array (8),
					'valeurparsoussujetcer93_id' => array (),
				),
				//… au moins une action visant la famille et la parentalité (soutien familial, garde d'enfant, …)
				'famille_parentalite' => array (
					'sujetcer93_id' => array (),
					'soussujetcer93_id' => array (),
					'valeurparsoussujetcer93_id' => array (50, 51, 52, 53),
				),
				// … au moins une action visant la lutte contre l'illettrisme ou l'acquisition des savoirs de base
				'illettrisme' => array (
					'sujetcer93_id' => array (),
					'soussujetcer93_id' => array (),
					'valeurparsoussujetcer93_id' => array (40, 41),
				),
				//… au moins une action visant l'accès aux droits ou l'aide dans les démarches administratives
				'demarches_administratives' => array (
					'sujetcer93_id' => array (),
					'soussujetcer93_id' => array (),
					'valeurparsoussujetcer93_id' => array (),
				),
				//… au moins une action non classée dans les items précédents 
				'autres' => array (
					'sujetcer93_id' => array (6),
					'soussujetcer93_id' => array (),
					'valeurparsoussujetcer93_id' => array (31, 33),
				),
			),
			/**
			 * Complète le querydata avec une jointure sur la table historiquesdroits
			 * et l'ajout éventuel de conditions pour obtenir ou non des allocataires
			 * soumis à droits et devoirs.
			 */
			'useHistoriquedroit' => false,
		)
	);

	/**
	 * Permet de spécifier les noms de serveurs servant d'environnement de
	 * production afin que les mails ne soient pas envoyés à leurs destinataires
	 * "normaux", mais à l'expéditeur du mail.
	 *
	 * @see WebrsaEmailConfig::isTestEnvironment()
	 *
	 * @param array
	 *
	 * @default null
	 */
	Configure::write( 'WebrsaEmailConfig.testEnvironments', array( null ) );

	/**
	 * Permet d'utiliser le module ROME V3:
	 *	- pour tous les départements:
	 *		* shell: ImportCsvCodesRomeV3Shell
	 *		* menu: Administration > Paramétrages > Codes ROME V3 (en fonction des habilitations)
	 *		* menu: Recherches > Par DSP
	 *		* menu du dossier RSA: DEM ou CJT > Droit > DSP d'origine
	 *		* menu du dossier RSA: DEM ou CJT > Droit > MAJ DSP
	 */
	Configure::write( 'Romev3.enabled', false );

	/**
	 * Valeur possible du nombre de résultats possible par recherche
	 */
	Configure::write(
		'ResultatsParPage.nombre_de_resultats',
		array(
			'10' => '10',
			'20' => '20',
			'50' => '50',
			'100' => '100',
		)
	);

	/**
	 * Valeur sélectionnée par défaut du nombre de résultats dans une recherche
	 */
	Configure::write('ResultatsParPage.nombre_par_defaut', '10' );

	/**
	 * Écart (en minutes) entre deux rendez-vous lors d'une ep.
	 */
	Configure::write( 'commissionep.heure.ecart.minute', '15' );

	/**
	 * Nombre de personnes convoquées à la même heure
	 */
	Configure::write( 'commissionep.heure.personnes.convoquees', '3' );

	/**
	 * Heure de début standard d'une ep si elle n'est pas renseignée.
	 */
	Configure::write( 'commissionep.heure.debut.standard', '09:00' );

	/**
	 * Heure de fin standard d'une ep.
	 */
	Configure::write( 'commissionep.heure.fin.standard', '17:00' );

	/**
	 * Heure de début de pause méridienne.
	 */
	Configure::write(
		'commissionep.heure.debut.pause.meridienne',
		array(
			'heure' => '12',
			'minute' => '00',
		)
	);

	/**
	 * Heure de fin de pause méridienne.
	 */
	Configure::write(
		'commissionep.heure.fin.pause.meridienne',
		array(
			'heure' => '14',
			'minute' => '00',
		)
	);

	/**
	 * Alertes des heures de passage en ep
	 */
	Configure::write(
		'commissionep.heure.alertes',
		array(
			'journee.debut' => true,
			'pause.meridienne' => true,
			'journee.fin' => true,
			'meme.heure' => false,
		)
	);

	/**
	 * Afficher heure de passage par défaut
	 */
	Configure::write( 'commissionep.heure.par.defaut', true );

	/**
	 * Tag : affichage des inactifs dans la recherche
	 * 1 : n'affiche que les actifs
	 * null : affiche tous les tags
	 */
	Configure::write( 'tag.affichage.actif.recherche', 1 );

	/**
	 * Tag : affichage des inactifs pour les attributions de tag
	 * 1 : n'affiche que les actifs
	 * null : affiche tous les tags
	 */
	Configure::write( 'tag.affichage.actif.attribution', 1 );

	/**
	 * Paramétrage de la durée d'engagement d'un CER
	 */
	Configure::write(
		'cer.duree.engagement',
		array (
			'3' => '3 mois',
			'6' => '6 mois',
			'9' => '9 mois',
			'12' => '12 mois',
			'18' => '18 mois',
			'24' => '24 mois',
		)
	);
	Configure::write( 'cer.duree.tranche', 24 );

	/**
	 * Activation du module TitreCréanciers
	 * 'Creances.titrescreanciers' => true pour activée, false pour inactivée
	 */
	Configure::write('Creances.titrescreanciers',true);

	/**
	 * Couleur des lignes de résultats en erreur
	 */
	Configure::write('Resultats.ligne.erreur', 'red');

	/**
	 * Activation du module Vagues D'Orientations
	 * Spécifique CD 93
	 * 'Indicateursmensuels.vaguesdorientations' => true pour activée, false pour inactivée
	 */
	Configure::write('Indicateursmensuels.vaguesdorientations',true);

	/**
	 * Variable contenant un integer (âge de l'allocataire) pour la limite d'âge à atteindre
	 * pour que la tacite reconduction soit autorisée.
	 * Permet également d'autoriser la création d'un CER au-delà de la limite des 24 mois
	 */
	Configure::write( 'Tacitereconduction.limiteAge', 55 );

    /**
     * Accès aux données PE
     */
    Configure::write ('Module.fluxpoleemploi.enabled', true);

	/**
	 * Récupération du fichier de config propre au département
	 */
	require_once ('webrsa.cg'.Configure::read( 'Cg.departement' ).'.inc');
