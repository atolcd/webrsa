SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Création du user pour se connnecter
INSERT INTO public."groups" (id, "name", parent_id, code) VALUES(1, 'Administrateurs', NULL, 'administrateurs');
INSERT INTO public.servicesinstructeurs
(id, lib_service, num_rue, nom_rue, complement_adr, code_insee, code_postal, ville, numdepins, typeserins, numcomins, numagrins, type_voie, sqrecherche, email)
VALUES(1, 'Conseil Départemental', '99', 'de l''Ouest', '', '99X00', '99X00', 'LIZON', '99X', 'S', '001', 1, 'Rue', NULL, NULL);
INSERT INTO public.users
(group_id, serviceinstructeur_id, username, "password", nom, prenom, date_naissance, date_deb_hab, date_fin_hab, numtel, filtre_zone_geo, numvoie, typevoie, nomvoie, compladr, codepos, ville, isgestionnaire, sensibilite, structurereferente_id, referent_id, "type", email, poledossierpcg66_id, service66_id, communautesr_id, accueil_referent_id, accueil_reference_affichage)
VALUES(1, 1, 'webrsa', '94170aea524f89c35ed8fe461d396dd192709a5c', 'Webrsa', 'Webrsa', '2000-01-01', '2009-01-01', '2029-12-31', '0663427903', false, NULL, NULL, NULL, NULL, NULL, NULL, 'N', 'O', NULL, NULL, 'cg', NULL, NULL, NULL, NULL, NULL, 'AUCUN');

-- Création des catégories de configuration
INSERT INTO public.configurationscategories (id, lib_categorie)
VALUES
	(1, 'webrsa'),
	(2, 'Traitementspcgs66'),
	(3, 'Titresuiviinfopayeur'),
	(4, 'Titrescreanciers'),
	(5, 'Tags'),
	(6, 'Statistiquesministerielles'),
	(7, 'Rendezvous'),
	(8, 'Relances'),
	(9, 'Recoursgracieux'),
	(10, 'Planpauvreterendezvous'),
	(11, 'Planpauvrete'),
	(12, 'Planpauvreteorientations'),
	(13, 'Orientsstructs'),
	(14, 'Nonorientes66'),
	(15, 'Nonorientationsproseps'),
	(16, 'Indus'),
	(17, 'Fluxpoleemplois'),
	(18, 'Entretiens'),
	(19, 'Dsps'),
	(20, 'Dossiers'),
	(21, 'Dossierspcgs66'),
	(22, 'Demenagementshorsdpts'),
	(23, 'Defautsinsertionseps66'),
	(24, 'Cuis'),
	(25, 'Creances'),
	(26, 'Contratsinsertion'),
	(27, 'Changementsadresses'),
	(28, 'Bilansparcours66'),
	(29, 'Apres'),
	(30, 'Apres66'),
	(31, 'ActionscandidatsPersonnes'),
	(32, 'Accueils'),
	(33, 'Transfertspdvs93'),
	(34, 'Tableauxsuivispdvs93'),
	(35, 'Propospdos'),
	(36, 'PersonnesReferents'),
	(37, 'ImportCSVFRSA'),
	(38, 'ImportcsvCataloguespdisfps93'),
	(39, 'Fichesprescriptions93'),
	(40, 'Cohortesrendezvous'),
	(41, 'Cohortescers93'),
	(42, 'Sanctionseps58'),
	(43, 'Nonorientationsproscovs58'),
	(44, 'Historiquescovs58'),
	(45, 'Covs58');

-- Recalcul séquence de la table configurationscategorie
SELECT setval('configurationscategories_id_seq', (SELECT MAX(id) FROM configurationscategories));

-- Création des configurations basée sur le CD66
INSERT INTO public.configurations (id, lib_variable, value_variable, comments_variable, configurationscategorie_id, created, modified)
VALUES
	(1, 'MultiDomainsTranslator', '{"prefix":"cg99x"}', NULL, 1, current_timestamp, current_timestamp),
	(2, 'Utilisateurs.multilogin', 'true', NULL, 1, current_timestamp, current_timestamp),
	(4, 'nom_form_ci_cg', '"cg99X"', NULL, 1, current_timestamp, current_timestamp),
	(6, 'Zonesegeographiques.CodesInsee', 'true', NULL, 1, current_timestamp, current_timestamp),
	(7, 'nom_form_apre_cg', '"cg99X"', 'Champs spécifique selon le CG pour le formulaire de l''APRE
	  @default: ''cg93'' (pour le CG93), ''cg66'' (pour le CG66)', 1, current_timestamp, current_timestamp),
	(8, 'nb_limit_print', '2000', 'Limit pour le nombre de documents à éditer dans la cohorte par orientation
	  @default: 2000', 1, current_timestamp, current_timestamp),
	(9, 'Admin.unlockall', 'false', 'Permet à l''administrateur d''accéder à toutes les parties de l''application
	  normalement bloquées aux seules parties de paramétrage renseignées.
	  Voir AppController::_isAdminAction().
	  @default false', 1, current_timestamp, current_timestamp),
	(11, 'Apre.periodeMontantMaxComplementaires', '1', 'Période (en nombre d''année) utilisée pour la calcul du montant maximal
	  des apres complémentaires pour une personne.
	  @default 1 (une année du 0101 au 3112)', 1, current_timestamp, current_timestamp),
	(13, 'Apre.forfaitaire.montantbase', '400', 'Paramètres à renseigner pour les APRE''s forfaitaires
	  FIXME: doc', 1, current_timestamp, current_timestamp),
	(14, 'Apre.forfaitaire.montantenfant12', '100', NULL, 1, current_timestamp, current_timestamp),
	(15, 'Apre.forfaitaire.nbenfant12max', '4', NULL, 1, current_timestamp, current_timestamp),
	(16, 'Cohorte.dossierTmpPdfs', '"\/var\/www\/66test\/public_html\/app\/tmp\/files\/pdf"', 'FIXME: vérifier l''existance et les droits
	  FIXME: accès concurrents ?', 1, current_timestamp, current_timestamp),
	(18, 'ActioncandidatPersonne.suffixe', '"cg99X"', 'Paramètre à renseigner pour l''affichage de la bonne fiche de candidature
	  @default: ''cg93'' (pour le CG93), sinon ''cg66'' pour le CG66', 1, current_timestamp, current_timestamp),
	(19, 'UI.menu.large', 'true', 'Paramètre à renseigner dans le cas d''un affichage plus large du menu du dossier
	    @default: false', 1, current_timestamp, current_timestamp),
	(20, 'UI.menu.lienDemandeur', 'false', 'Paramètre à renseigner pour le CG58 pour le lien pointant sur leur application', 1, current_timestamp, current_timestamp),
	(21, 'Periode.modifiable.nbheure', '48', 'Paramètre à renseigner pour déterminer la plage horaire que l''on dispose pour pouvoir accéder
	    aux différents boutons possédant ce paramètre.
	    On met 48, pour 48H car la plage de date va de minuit à minuit et donc un formulaire saisi
	    un jour à 18h ne serait plus modifiablevalidablesupprimable le lendemain.
	    @default: 48 --> nombre d''heures pendant lesquelles on pourra accéder au bouton', 1, current_timestamp, current_timestamp),
	(22, 'Periode.modifiablecer.nbheure', '240', NULL, 1, current_timestamp, current_timestamp),
	(23, 'Periode.modifiableorientation.nbheure', '240', NULL, 1, current_timestamp, current_timestamp),
	(24, 'nom_form_pdo_cg', '"cg99X"', 'Champs spécifique selon le CG pour le formulaire des PDOs
	  @default: ''cg93'' (pour le CG93), ''cg66'' (pour le CG66)', 1, current_timestamp, current_timestamp),
	(25, 'nom_form_bilan_cg', '"cg99X"', 'Champs spécifique selon le CG pour le formulaire du bilan de parcours  Fiche de saisine
	  @default: ''cg93'' (pour le CG93), ''cg66'' (pour le CG66)', 1, current_timestamp, current_timestamp),
	(26, 'nom_form_cui_cg', '"cg99X"', 'Champs spécifique selon le CG pour le formulaire du Contrat Unqiue d''Insertion
	  @default: ''cg93'' (pour le CG93), ''cg66'' (pour le CG66)', 1, current_timestamp, current_timestamp),
	(27, 'Apre.pourcentage.montantversement', '60', 'Paramètre pour connaître le pourcentage du 1er versement, lors d''un versement en 2 fois,
	    pour les apres présentes dans un état liquidatif
	    @default: 60 ---> avant 40 %', 1, current_timestamp, current_timestamp),
	(28, 'Jetons2.disabled', 'false', 'Permet de désactiver l''utilisation des jetons sur les dossiers
	  Si à false, on utilise les jetons sur les dossiers.
	  Si à true, on n''utilise pas les jetons sur les dossiers.
	  @default false', 1, current_timestamp, current_timestamp),
	(29, 'Cui.taux.fixe', '60', 'Paramètre pour définir les taux dans le formulaire du CUI :
	    Cui.taux.fixe                   => Taux fixé par l''arrêté du Préfet de région (en % )
	    Cui.taux.prisencharge           => Taux de prise en charge effectif si le Conseil Départemental fixe
	                                        un taux supérieur au taux fixé par le Préfet de région (en %)
	    Cui.taux.financementexclusif    => Financement exclusif du Conseil Départemental, Si oui, taux (en %)
	    @default: à définir par chaque CG', 1, current_timestamp, current_timestamp),
	(30, 'Cui.taux.prisencharge', '70', NULL, 1, current_timestamp, current_timestamp),
	(31, 'Cui.taux.financementexclusif', '60', NULL, 1, current_timestamp, current_timestamp),
	(32, 'Optimisations.progressivePaginate', 'true', 'Permet la pagination progressive, cad. qu''on ne compte pas le nombre
	  d''enregistrements totaux, mais que l''on regarde seulement si la
	  page suivante existe.

	  Ce paramètre concerne toutes les paginations.

	  @default: false (pagination normale)', 1, current_timestamp, current_timestamp),
	(33, 'Optimisations.Cohortes_orientees.progressivePaginate', 'false', 'Surcharge de la pagination progressive pour les cohorte d''orientations, demandes orientées', 1, current_timestamp, current_timestamp),
	(34, 'Optimisations.Cohortesci_nouveaux.progressivePaginate', 'false', 'Surcharge de la pagination progressive pour l''action nouveaux
	  du contrôleur Cohortesci.', 1, current_timestamp, current_timestamp),
	(35, 'Optimisations.Cohortespdos_avisdemande.progressivePaginate', 'false', 'Surcharge de la pagination progressive pour l''action avisdemande
	  du contrôleur Cohortespdos.', 1, current_timestamp, current_timestamp),
	(36, 'Optimisations.Relancesnonrespectssanctionseps93_cohorte.progressivePaginate', 'false', 'Surcharge de la pagination progressive pour l''action cohorte
	  du contrôleur Relancesnonrespectssanctionseps93.', 1, current_timestamp, current_timestamp),
	(37, 'Traitementpdo.fichecalcul_coefannee1', '34', 'Variables apparaissant dans la fiche de calcul du journal de traitement d''une PDO', 1, current_timestamp, current_timestamp),
	(38, 'Traitementpdo.fichecalcul_coefannee2', '25', NULL, 1, current_timestamp, current_timestamp),
	(39, 'Traitementpdo.fichecalcul_cavntmax', '80300', NULL, 1, current_timestamp, current_timestamp),
	(40, 'Traitementpdo.fichecalcul_casrvmax', '32100', NULL, 1, current_timestamp, current_timestamp),
	(41, 'Traitementpdo.fichecalcul_abattbicvnt', '71', NULL, 1, current_timestamp, current_timestamp),
	(42, 'Traitementpdo.fichecalcul_abattbicsrv', '50', NULL, 1, current_timestamp, current_timestamp),
	(43, 'Traitementpdo.fichecalcul_abattbncsrv', '34', NULL, 1, current_timestamp, current_timestamp),
	(12, 'Apre.suffixe', '"66"', 'Configuration des adresses mails d''expéditeur pour l''envoi de mails concernant
	  les pièces manquantes de l''APRE (CG 66).', 1, current_timestamp, current_timestamp),
	(17, 'User.adresse', 'true', 'Variable contenant un id pour les typesorients Social par défaut
	  dans la gestion des réponses des non orientés 66', 1, current_timestamp, current_timestamp),
	(46, 'Recherche.qdFilters.Serviceinstructeur', 'false', 'Permet de rajouter des conditions aux conditions de recherches suivant
	  le paramétrage des service référent dont dépend l''utilisateur connecté.

	  @default false', 1, current_timestamp, current_timestamp),
	(48, 'AjoutOrientationPossible.situationetatdosrsa', 'null', NULL, 1, current_timestamp, current_timestamp),
	(50, 'Typeorient.emploi_id', '1', 'Variable contenant un array avec les id des typesorients du grand social et de l''emploi', 1, current_timestamp, current_timestamp),
	(52, 'Optimisations.Gestionsanomaliesbdds.progressivePaginate', 'false', 'Gestion des anomalies', 1, current_timestamp, current_timestamp),
	(53, 'Situationdossierrsa.etatdosrsa.ouvert', '["Z","2","3","4"]', NULL, 1, current_timestamp, current_timestamp),
	(54, 'Criterecer.delaiavanteecheance', '"1 months"', 'Délai pour la détection des CERs arrivant à échéance', 1, current_timestamp, current_timestamp),
	(55, 'Dossierep.delaiavantselection', 'null', 'Pour le CG66: Délai durant lequel les dossiers d''EP ne seront ni détectables, ni sélectionnables dans la corbeille
	  	des dossiers devant passer en EP
	  	@default: null
	  	@CG66: 1 month 15 days
	  	Voir le document appdocsDocumentation administrateurs.odt, partie
	  	"Intervalles PostgreSQL"', 1, current_timestamp, current_timestamp),
	(56, 'Detailcalculdroitrsa.natpf.socle', '["RSD","RSI","RSU","RSJ"]', 'Valeurs prises par le champ natpf pour déterminer si le dossier est en RSA Socle', 1, current_timestamp, current_timestamp),
	(57, 'Selectionradies.conditions', '{"0":"Historiqueetatpe.date < ( DATE( NOW() ) - INTERVAL ''70 days'' )","Historiqueetatpe.etat":["radiation","cessation"],"NOT":{"Historiqueetatpe.code":["11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","41","43","44","45","46","48","49","72"]}}', NULL, 1, current_timestamp, current_timestamp),
	(58, 'Optimisations.useTableDernierdossierallocataire', 'true', 'Permet de rechercher le dernier dossier d''un allocataire dans la table
	  derniersdossiersallocataires (il faudra mettre le shell Derniersdossiersallocataires
	  en tâche planifiée) afin de se passer d''une sous-requête coûteuse dans les
	  recherches.

	  @param boolean
	  @default null', 1, current_timestamp, current_timestamp),
	(59, 'Filtresdefaut.Cohortespdos_avisdemande', '{"Search":{"Dossier":{"dernier":"1"}}}', 'Permet de donner des valeurs par défaut au formulaire de cohorte des PDOs (cohortespdosavisdemande)', 1, current_timestamp, current_timestamp),
	(60, 'Filtresdefaut.Cohortespdos_valide', '{"Search":{"Dossier":{"dernier":"1"}}}', 'Permet de donner des valeurs par défaut au formulaire de cohorte des PDOs (cohortespdosvalide)', 1, current_timestamp, current_timestamp),
	(61, 'Utilisateurs.reconnection', 'true', 'Permet-on à l''utilisateur de se reconnecter alors que sa session n''est pas
	  clôturée ni expirée ?

	  @var boolean
	  @default null (false)', 1, current_timestamp, current_timestamp),
	(62, 'apache_bin', '"\/usr\/sbin\/apache2"', 'Lorsque apache est utilisé en mode CGI, les fonctions apache_get_version()
	  et apache_get_modules() ne sont pas disponibles. Du coup, on passe par
	  la fonction exec() pour interroger directement le binaire apache.

	  Ce paramètre de configuration permet de spécifier le chemin complet vers
	  le binaire apache.

	  @default usrsbinapache2', 1, current_timestamp, current_timestamp),
	(66, 'Rendezvous.useThematique', 'false', 'Configuration de l''utilisation des thématiques de RDV du module RDV', 1, current_timestamp, current_timestamp),
	(67, 'Gestiondoublon.Situationdossierrsa2.etatdosrsa', '["Z"]', 'Etats du dossier RSA pris en compte pour trouver les dossiers à fusionner
	  dans la gestion des doublons complexes.

	  Par défaut, on cherche à fusionner les dossiers créés dans l''application
	  avec les dossiers envoyer par les flux CAF.

	  @param array
	  @default array( ''Z'' )', 1, current_timestamp, current_timestamp),
	(68, 'Filtresdefaut.Gestionsdoublons_index', '{"Search":{"Situationdossierrsa":{"etatdosrsa_choice":true,"etatdosrsa":["0","1","2","3","4","5","6"]}}}', 'Filtres par défaut du moteur de recherche des doublons complexes.', 1, current_timestamp, current_timestamp),
	(69, 'Filtresdefaut.Indicateurssuivis_search', '{"Search":{"Calculdroitrsa":{"toppersdrodevorsa":"1"},"Dossier":{"dernier":"1"},"Pagination":{"nombre_total":true},"Situationdossierrsa":{"etatdosrsa_choice":"1","etatdosrsa":["Z","2","3","4"]}}}', 'Filtres par défaut des indicateurs de suivi', 1, current_timestamp, current_timestamp),
	(49, 'Recherche.identifiantpecourt', 'true', 'Permet de spécifier si les recherches sur l''identifiant Pôle Emploi d''un
	  allocataire doivent se faire sur les 8 derniers chiffres de l''identifiant
	  (true) ou sur la totalité de celui-ci (false).

	  @default false', 1, current_timestamp, current_timestamp),
	(45, 'Orientstruct.typeorientprincipale', '{"SOCIAL":[4,6],"Emploi":[1]}', 'Variable contenant un array avec les id des typesorients du social, sociopro et de l''emploi', 1, current_timestamp, current_timestamp),
	(51, 'Selectionnoninscritspe.intervalleDetection', '"2 months"', 'Durée du délai (intervalle) entre la date de validation de l''orientation et la date
	  d''inscription au Pôle Emploi

	  Voir le document appdocsDocumentation administrateurs.odt, partie
	  "Intervalles PostgreSQL"', 1, current_timestamp, current_timestamp),
	(64, 'Password', '{"mail_forgotten":true}', 'Validation parametrable pour l''allowEmpty', 1, current_timestamp, current_timestamp),
	(65, 'Cui.Numeroconvention', '"0661300001"', 'Variable contenant une chaîne de caractères (stockée en base) pour le
	  n° de convention annuelle d''objectifs et de moyens
      (unqiue par année et qui devant être changé chaque année)
      Cui.numconventionobj', 1, current_timestamp, current_timestamp),
	(70, 'Filtresdefaut.Demenagementshorsdpts_search', '{"Search":{"Dossier":{"dernier":"1"},"Pagination":{"nombre_total":false},"Situationdossierrsa":{"etatdosrsa_choice":"1","etatdosrsa":["2","3","4"]}}}', 'Filtres par défaut de la recherche par allocataires ayant quitté le département', 1, current_timestamp, current_timestamp),
	(73, 'Statistiquedrees', '{"conditions_droits_et_devoirs":{"Situationdossierrsa.etatdosrsa":["2","3","4"],"Calculdroitrsa.toppersdrodevorsa":"1"},"useHistoriquedroit":false,"actionscandidats":true}', 'Conditions permettant de définit les allocataires dans le champ
			  des droits et devoirs.

			  Ces conditions seront utilisées dans les différents tableaux.

			  Modèles disponibles: Dossier, Detaildroitrsa, Foyer, Situationdossierrsa,
			  Adressefoyer, Personne, Adresse, Prestation, Calculdroitrsa.Catégories et conditions des différents types de parcours du CG.
			  Les catégories sont: professionnel, socioprofessionnel et social.

			  Utilisé dans le tableau "1 - Orientation des personnes ... au sens du type de parcours..."

			  Modèles disponibles (en plus de ceux disponibles de base, @see
			  conditions_droits_et_devoirs): DspRev, Dsp, Orientstruct, Typeorient.Catégories (intitulés) et conditions des différents types de référents
			  uniques (structures référentes) pour la tableau "2 - Organismes de
			  prise en charge des personnes ... dont le référent unique a été désigné"

			  Modèles disponibles (en plus de ceux disponibles de base, @see
			  conditions_droits_et_devoirs): DspRev, Dsp, Orientstruct, Typeorient,
			  Structurereferente.Catégories et délais permettant de différencier les types de contrats.

			  Lorsqu''un contrat est signé avec Pôle Emploi, il s''agit à priori
			  d''un PPAE, alors qu''un CER pro n''est pas signé avec Pôle Emploi.

			  Voir aussi Statistiqueministerielle.conditions_types_parcours (les conditions sont ajoutées automatiquement):
			 	- un CER pro est signé lors d''un type de parcours professionnel
			 	- un CER social ou professionnel est signé lors d''un type de parcours social ou sociprofessionnel

			  Modèles disponibles (en plus de ceux disponibles de base, @see
			  conditions_droits_et_devoirs): Orientstruct, Typeorient,
			  Contratinsertion, Structurereferentecer, Typeorientcer.Catégories et conditions permettant de différencier les organismes
			  SPE et les organismes Hors SPE.

			  Les catégories sont: SPE et HorsSPE.

			  Modèles disponibles (en plus de ceux disponibles de base, @see
			  conditions_droits_et_devoirs): DspRev, Dsp, Orientstruct, Typeorient,
			  Structurereferente, Orientstructpcd, Typeorientpcd,
			  Structurereferentepcd.

			  Utilisé dans les tableaux:
			 	- "4 - Nombre et profil des personnes réorientées..."
			 	- "4a - Motifs des réorientations..."
			 	- "4b - Recours à l''article L262-31"Catégories et conditions permettant de différencier les motifs de
			  réorientations. Une valeur NULL signifie que la donnée sera non
			  disponible (ND).

			  Modèles disponibles (en plus de ceux disponibles de base, @see
			  conditions_droits_et_devoirs): DspRev, Dsp, Orientstruct, Typeorient,
			  Structurereferente, Orientstructpcd, Typeorientpcd,
			  Structurereferentepcd.

			  Utilisé dans le tableau "4a - Motifs des réorientations...".Paramétrage du module "Statistiques DREES"

	  @param array', 1, current_timestamp, current_timestamp),
	(77, 'ResultatsParPage.nombre_par_defaut', '"10"', 'Valeur sélectionnée par défaut du nombre de résultats dans une recherche', 1, current_timestamp, current_timestamp),
	(78, 'commissionep.heure.ecart.minute', '"15"', 'Écart (en minutes) entre deux rendez-vous lors d''une ep.', 1, current_timestamp, current_timestamp),
	(159, 'Nonoriente66.TypeorientIdPrepro', '5', NULL, 1, current_timestamp, current_timestamp),
	(79, 'commissionep.heure.personnes.convoquees', '"3"', 'Nombre de personnes convoquées à la même heure', 1, current_timestamp, current_timestamp),
	(80, 'commissionep.heure.debut.standard', '"09:00"', 'Heure de début standard d''une ep si elle n''est pas renseignée.', 1, current_timestamp, current_timestamp),
	(81, 'commissionep.heure.fin.standard', '"17:00"', 'Heure de fin standard d''une ep.', 1, current_timestamp, current_timestamp),
	(82, 'commissionep.heure.debut.pause.meridienne', '{"heure":"12","minute":"00"}', 'Heure de début de pause méridienne.', 1, current_timestamp, current_timestamp),
	(83, 'commissionep.heure.fin.pause.meridienne', '{"heure":"14","minute":"00"}', 'Heure de fin de pause méridienne.', 1, current_timestamp, current_timestamp),
	(84, 'commissionep.heure.alertes', '{"journee.debut":true,"pause.meridienne":true,"journee.fin":true,"meme.heure":false}', 'Alertes des heures de passage en ep', 1, current_timestamp, current_timestamp),
	(85, 'commissionep.heure.par.defaut', 'true', 'Afficher heure de passage par défaut', 1, current_timestamp, current_timestamp),
	(86, 'tag.affichage.actif.recherche', '1', 'Tag : affichage des inactifs dans la recherche
	  1 : n''affiche que les actifs
	  null : affiche tous les tags', 1, current_timestamp, current_timestamp),
	(87, 'tag.affichage.actif.attribution', '1', 'Tag : affichage des inactifs pour les attributions de tag
	  1 : n''affiche que les actifs
	  null : affiche tous les tags', 1, current_timestamp, current_timestamp),
	(89, 'cer.duree.tranche', '24', NULL, 1, current_timestamp, current_timestamp),
	(90, 'Creances.Titrescreanciers.enabled', 'true', 'Activation du module TitreCréanciers
      ''Creances.Titrescreanciers.enabled'' => true pour activée, false pour inactivée', 1, current_timestamp, current_timestamp),
	(75, 'Romev3.enabled', 'true', 'Permet d''activer l''utilisation du module CUI.

	  @var boolean
	  @default null', 1, current_timestamp, current_timestamp),
	(71, 'AncienAllocataire.enabled', 'false', 'Permet de parcourir les détails des allocataires n''ayant pas de prestation
	  RSA dans le menu du dossier.

	  Permet également d''obtenir la liste des dossiers dans lesquels l''allocataire
	  ne possède plus de prestation RSA mais pour lesquels il existe des enregistrements
	  dans les tables métier de chacun des modules impactés.

	  Permet également de voir dans la page de résumé du dossier les autres dossiers
	  dans lesquels se trouve l''allocataire avec des enregistrements dans les
	  tables métiers et pas de prestation.

	  Les tables concernées sont: actionscandidats_personnes, apres, bilansparcours66,
	  contratsinsertion, cuis, dsps, dsps_revs, entretiens, fichesprescriptions93,
	  memos, orientsstructs, personnes_referents, propospdos, questionnairesd1pdvs93,
	  questionnairesd2pdvs93, rendezvous.

	  ATTENTION: impacte les performances de l''application.

	  @var boolean
	  @default null', 1, current_timestamp, current_timestamp),
	(76, 'ResultatsParPage.nombre_de_resultats', '{"10":"10","20":"20","30":"30","50":"50","100":"100"}', 'Valeur possible du nombre de résultats possible par recherche', 1, current_timestamp, current_timestamp),
	(88, 'cer.duree.engagement', '{"3":"3 mois","6":"6 mois","9":"9 mois","12":"12 mois","24":"24 mois (PACEA)"}', 'Paramétrage de la durée d''engagement d''un CER', 1, current_timestamp, current_timestamp),
	(91, 'Module.Creances.GestionList.enabled', 'true', 'Activation du module Gestion de List des Creances et Titres Créanciers
      ''Module.Creances.GestionList.enabled'' => true pour activée, false pour inactivée', 1, current_timestamp, current_timestamp),
	(92, 'Module.Creances.FICA.enabled', 'true', 'Activation du module d''export et import FICA
      ''Module.Creances.FICA.enabled'' => true pour activée, false pour inactivée
      Attention a bien configurer les autres valeurs dans le dossier de configTitrescreanciers.php', 1, current_timestamp, current_timestamp),
	(94, 'Recoursgracieux.Creancerecoursgracieux.enabled', 'true', 'Permet de baser le fonctionnement des recoursgracieux sur les créances
      Compatible avec Recoursgracieux.Indurecoursgracieux.enabled'' : true', 1, current_timestamp, current_timestamp),
	(95, 'Recoursgracieux.Indurecoursgracieux.enabled', 'true', 'Permet de baser le fonctionnement des recours gracieux sur les indus du flux financier
      Compatible avec Recoursgracieux.Indurecoursgracieux.enabled'' : true
      Necessite le paramétrage de la limitation des recherches sur les InfosFinancières
      Recoursgracieux.Indurecoursgracieux.search_type_allocation ou
      Recoursgracieux.Indurecoursgracieux.search_typeopecompta', 1, current_timestamp, current_timestamp),
	(96, 'Recoursgracieux.Indurecoursgracieux.search_type_allocation', 'true', 'Limitation de l''affichage aux indus recherche par type allocation', 1, current_timestamp, current_timestamp),
	(97, 'Recoursgracieux.Indurecoursgracieux.type_allocation', '["IndusConstates","IndusTransferesCG"]', 'Valeur acceptées lors de la recherche sur type_allocation
      Default : ''IndusConstates'',''IndusTransferesCG''
      Valeur possible en BDD au 20112019 :
      ''IndusConstates'' => ''Indu constaté'',
      ''IndusTransferesCG'' => ''Indu transféré au CD'',
      ''AllocationsComptabilisees'' => ''Allocations comptabilisées'',
      ''RemisesIndus'' => ''Remise d''indu'',
      ''AnnulationsFaibleMontant'' => ''Annulation pour faible montant'',
      ''AutresAnnulations'' => ''Autre annulation''', 1, current_timestamp, current_timestamp),
	(98, 'Recoursgracieux.Indurecoursgracieux.search_typeopecompta', 'true', 'Limitation de l''affichage aux indus recherche par type d''opération comptable', 1, current_timestamp, current_timestamp),
	(99, 'Recoursgracieux.Indurecoursgracieux.typeopecompta', '["CAI","CDC","CIC","CCP"]', 'Valeur acceptées lors de la recherche sur typeopecompta
      Default : ''CAI'',''CDC'',''CIC'',''CCP''''
      Valeur possible en BDD au 20112019 :
      AllocCompta
      ''PME'' => ''Pour le paiement mensuel'',
      ''PRA'' => ''Pour le paiement de rappel sur mois antérieur'',
      ''RAI'' => ''Pour réajustement  suite à annulation d''indus'',
      ''RMU'' => ''Pour réajustement suite à mutation du dossier'',
      ''RTR'' => ''Pour réajustement suite à transformation d''avances ou d''acomptes en indus'',
      Indus constatés
      ''CIC'' => ''Implantation de créance'',
      ''CAI'' => ''Implantation de créance suite à une opération comptable de réajustement. Une opération de type RAI a été effectuée sur un autre dossier allocataire.'',
      ''CDC'' => ''Implantation d''un  débit complémentaire (augmentation de la créance)'',
      Indus transférés
      ''CCP'' => ''Transfert  de la créance au Conseil départemental'',
      Remises indus
      ''CRC'' => ''Remise de la créance par le Conseil départemental'',
      ''CRG'' => ''Remise de la créance par la Caf'',
      Annulation faible
      ''CAF'' => ''Annulation de faible montant  inférieur au seuil réglementaire'',
      ''CFC'' => ''Annulation de faible montant selon seuil fixé par le Conseil départemental (supérieur au seuil réglementaire)'',
      Autre annulations
      ''CEX'' => ''Annulation exceptionelle'',
      ''CES'' => ''Annulation suite à surendettement'',
      ''CRN'' => ''Annulation suite à renouvellement ou revalorisation (publication tardive des baremes, seuils, …)''', 1, current_timestamp, current_timestamp),
	(100, 'Recoursgracieux.PCG.Actifs', 'false', 'Activation du module RecoursGracieux
      ''Recoursgracieux.PCG.Actifs'' => true pour activer la création de PCG lors de la régularisation d''un recours gracieux, false pour inactiver
      Au CG 66 : true
      Default : False', 1, current_timestamp, current_timestamp),
	(101, 'Recoursgracieux.PCG.Etat', '"attinstr"', 'État du PCG à la création de celui-ci', 1, current_timestamp, current_timestamp),
	(102, 'Recoursgracieux.PCG.Dossierpcg66TypepdoId', '34', 'Identifiant du type du PCG à sa création', 1, current_timestamp, current_timestamp),
	(103, 'Recoursgracieux.PCG.Dossierpcg66OriginepdoId', '36', 'Identifiant de l''origine du PCG à sa création', 1, current_timestamp, current_timestamp),
	(104, 'Recoursgracieux.PCG.Dossierpcg66Orgpayeur', '"CAF"', 'Organisme payeur lié au PCG normalement (CAF ou MSA)', 1, current_timestamp, current_timestamp),
	(105, 'Emails.Activer', 'true', 'Activation du module de gestion des Emails pour
      - les recours gracieux,
      - les créances,
      ''Emails.Activer'' => true pour activer le module recours gracieux, false pour inactiver', 1, current_timestamp, current_timestamp),
	(106, 'Resultats.ligne.erreur', '"red"', 'Couleur des lignes de résultats en erreur', 1, current_timestamp, current_timestamp),
	(107, 'Indicateursmensuels.vaguesdorientations', 'true', 'Activation du module Vagues D''Orientations
	  Spécifique CD 93
	  ''Indicateursmensuels.vaguesdorientations'' => true pour activée, false pour inactivée', 1, current_timestamp, current_timestamp),
	(108, 'Tacitereconduction.limiteAge', '55', 'Variable contenant un integer (âge de l''allocataire) pour la limite d''âge à atteindre
	  pour que la tacite reconduction soit autorisée.
	  Permet également d''autoriser la création d''un CER au-delà de la limite des 24 mois', 1, current_timestamp, current_timestamp),
	(110, 'search.conditions.numdemrsa_matricule', '{"before":"*","after":"*"}', 'Options modifiable des champs de recherche numdemrsa et matricule', 1, current_timestamp, current_timestamp),
	(111, 'Module.fluxpoleemploi.enabled', 'true', 'Accès aux données PEActivation de la sauvegarde des CER', 1, current_timestamp, current_timestamp),
	(112, 'cer.pdf.save', 'false', 'Accès aux données Synthèse du suiviParamétrage du module "Statistiques Plan Pauvreté"

      @param array', 1, current_timestamp, current_timestamp),
	(137, 'Module.Planpauvrete.primoaccedant.enabled', 'false', 'Spécifie l''intervalle, par-rapport à la date de fin d''un CER, en deçà duquel
	  un CER sera positionné « En cours:Bilan à réaliser » grâce au shell
	  positioncer66.

	  Voir le document appdocsDocumentation administrateurs.odt, partie
	  "Intervalles PostgreSQL"', 1, current_timestamp, current_timestamp),
	(3, 'with_parentid', 'true', 'Durée du délai (intervalle) entre la date de validation de l''orientation et la date
	  d''inscription au Pôle Emploi

	  Voir le document appdocsDocumentation administrateurs.odt, partie
	  "Intervalles PostgreSQL"', 1, current_timestamp, current_timestamp),
	(5, 'CG.cantons', 'true', 'Mise en paramétrage de la liste des chargés d''insertion et secrétaire liés à une fiche de candidature
	  	@default: id des group auxquels les personnes sont liées
	  	Mise en place suite à la demande d''améliorations du 28022012 ( #5630 )', 1, current_timestamp, current_timestamp),
	(113, 'Module.synthesedusuivi.enabled', 'false', 'Conditions permettant de définit les allocataires dans le champ
              des droits et devoirs.

              Ces conditions seront utilisées dans les différents tableaux.

              Modèles disponibles: Dossier, Detaildroitrsa, Foyer, Situationdossierrsa,
              Adressefoyer, Personne, Adresse, Prestation, Calculdroitrsa.Tableau des etats de dosssiers contenants les personnes suspendus.Complète le querydata avec une jointure sur la table historiquesdroits
              et l''ajout éventuel de conditions pour obtenir ou non des allocataires
              soumis à droits et devoirs.Tranche de délai - en joursOrientation avec rdv => ID en base de donnéeIdentifiants de types de 1er rendez-vous pour le tableau A2aCode Statuts du rendez-vous NonVenu pour le tableau A1Paramétrage du module "Cohorte Plan Pauvreté"

      Vous devez définir le jour de début et de jour de fin de la période
      des nouveaux entrants.
      Cette période est fonction de l''intégratio ndes flux CNAF et PE.

      @param array', 1, current_timestamp, current_timestamp),
	(114, 'Statistiqueplanpauvrete', '{"conditions_droits_et_devoirs":{"Situationdossierrsa.etatdosrsa":["2"],"Calculdroitrsa.toppersdrodevorsa":"1"},"etatSuspendus":[2,3,4],"useHistoriquedroit":true,"delais":{"0_29":"< 30","30_59":"30 - 60","60_89":"60 - 89","90_999":">= 90"},"orientationRdv":{"venu":[1],"prevu":[17],"excuses_recevables":[8,9,10,11,12,13,14,15,16],"excuses_non_recevables":[3,2]},"code_stats":["CHRS","MLJ","ADRH"],"type_rendezvous":["1"],"statut_rendezvous":"NONVENU"}', 'Paramétrage du module "Cohorte Plan Pauvreté"

	  Vous devez définir le jour de début et de jour de fin de la période
	  des nouveaux entrants.
	  Ces valeurs doivent être les mêmes que la variable de configuration
	  PlanPauvrete.Cohorte.Moisprecedent

	  @param array', 1, current_timestamp, current_timestamp),
	(117, 'Module.Statistiques.Plan.Pauvrete.version3', 'true', NULL, 1, current_timestamp, current_timestamp),
	(118, 'Module.Cohorte.Plan.Pauvrete.Menu', 'true', 'Montant maximal des apres complémentaires pour une personne au cours
	  de la période de temps définie par Apre.periodeMontantMaxComplementaires.
	  @default 2600
	  cg66 -> 3000', 1, current_timestamp, current_timestamp),
	(119, 'Module.Cohorte.Plan.Pauvrete.Nouveaux.Menu', 'true', 'Paramètre à renseigner pour l''utilisation du bon MVC
	  @default: vide (pour le CG93), sinon ''66'' pour le CG66', 1, current_timestamp, current_timestamp),
	(120, 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_isemploi', 'true', 'Paramètres à renseigner pour les montants des
	    forfaits de déplacements des APREs pour le CG66
	    @default:   0.30€ pour les forfaits au Km
	                39€ pour les frais d''hebergement
	                4.61€ pour les frais de repas', 1, current_timestamp, current_timestamp),
	(121, 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_isemploi_imprime', 'true', NULL, 1, current_timestamp, current_timestamp),
	(122, 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol', 'true', NULL, 1, current_timestamp, current_timestamp),
	(123, 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_imprime', 'true', 'Permet à l''administrateur d''ajouter une adresse pour l''utilisateur
	  connecté à l''application.
	  Besoin pour le CG66 lors de l''impression des courriers de rendez-vous
	  @default false', 1, current_timestamp, current_timestamp),
	(124, 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_venu_nonvenu_nouveaux', 'true', 'Variables apparaissant dans la fiche de calcul du journal de traitement
	  d''une PDO.

	  Pour "désactiver" un chiffre d''affaire maximum, il suffit de mettre un
	  très grand nombre (par exemple, la constante PHP_INT_MAX).', 1, current_timestamp, current_timestamp),
	(125, 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_second_rdv_nouveaux', 'true', NULL, 1, current_timestamp, current_timestamp),
	(126, 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_imprime_second_rdv_nouveaux', 'true', NULL, 1, current_timestamp, current_timestamp),
	(127, 'Module.Cohorte.Plan.Pauvrete.Stock.Menu', 'true', NULL, 1, current_timestamp, current_timestamp),
	(128, 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_isemploi_stock', 'true', NULL, 1, current_timestamp, current_timestamp),
	(129, 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_isemploi_stock_imprime', 'true', NULL, 1, current_timestamp, current_timestamp),
	(130, 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_stock', 'true', NULL, 1, current_timestamp, current_timestamp),
	(131, 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_imprime_stock', 'true', NULL, 1, current_timestamp, current_timestamp),
	(132, 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_venu_nonvenu_stock', 'true', NULL, 1, current_timestamp, current_timestamp),
	(133, 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_second_rdv_stock', 'true', 'Affiche ou non l''alerte de fin de session et exécute la redirection', 1, current_timestamp, current_timestamp),
	(134, 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_imprime_second_rdv_stock', 'true', 'Durée du délai (en jours) accordé pour la création d''un nouveau contrat pour
	  le thème "non orientation professionelle"', 1, current_timestamp, current_timestamp),
	(135, 'Module.Statistiques.Plan.Pauvrete.version1', 'true', 'Permet de définir si on peut ou non ajouter une nouvelle orientation à un dossier
	    - toppersdrodevorsa     --> par défaut 1 (personne soumise à droit et devoir)
	    - situationetatdosrsa   --> par défaut array( ''Z'', 2, 3, 4) (dossier dans un droit ouvert et versable)

	  INFO: depuis le passage en CakePHP 2.x, il faut mettre les valeurs numériques entre apostrophes.', 1, current_timestamp, current_timestamp),
	(136, 'Module.Statistiques.Plan.Pauvrete.version2', 'true', 'Permet de spécifier si les recherches sur l''identifiant Pôle Emploi d''un
	  allocataire doivent se faire sur les 8 derniers chiffres de l''identifiant
	  (true) ou sur la totalité de celui-ci (false).

	  @default false', 1, current_timestamp, current_timestamp),
	(116, 'PlanPauvrete.Stats.Moisprecedent', '{"deb":"05"}', NULL, 1, current_timestamp, current_timestamp),
	(10, 'Apre.montantMaxComplementaires', '3000', 'Configuration du composant Email de CakePHP pour l''envoi de mails.
	  Ne pas oublier de configurer le fichier php.ini

	  @see http:book.cakephp.org1.2enview481Sending-A-Message-Using-SMTP', 1, current_timestamp, current_timestamp),
	(138, 'Fraisdeplacement66.forfaitvehicule', '0.3', NULL, 1, current_timestamp, current_timestamp),
	(139, 'Fraisdeplacement66.forfaithebergt', '39', 'Délai pour la détection des CERs non validés et notifiés il y a 1 mois et demi', 1, current_timestamp, current_timestamp),
	(140, 'Fraisdeplacement66.forfaitrepas', '4.6', 'Variable contenant un array avec les id des 3 seuls typesorients nécessaires aux non orientés', 1, current_timestamp, current_timestamp),
	(141, 'Traitementpcg66.fichecalcul_coefannee1', '1.8', 'Variable contenant un id pour les typesorients Prépro par défaut
	  dans la gestion des réponses des non orientés 66', 1, current_timestamp, current_timestamp),
	(142, 'Traitementpcg66.fichecalcul_coefannee2', '1.8', 'Configuration des adresses mails d''expéditeur pour l''envoi de mails concernant
	  les fiches de candidature (CG 66).', 1, current_timestamp, current_timestamp),
	(143, 'Traitementpcg66.fichecalcul_cavntmax', '9223372036854775807', NULL, 1, current_timestamp, current_timestamp),
	(144, 'Traitementpcg66.fichecalcul_casrvmax', '9223372036854775807', 'Lorsque l''on enregistre un CER au CG 66, on vérifie si l''allocataire a un dernier rendez-vous
	  de type "01 - Convocation à un Entretien - Contrat" ayant le statut "Prévu".
	  Si c''est le cas, on le passe à "Venu(e)"', 1, current_timestamp, current_timestamp),
	(145, 'Traitementpcg66.fichecalcul_caagrimax', '9223372036854775807', 'Variable contenant un integer (id stocké en base) pour la description du traitement PCG
	  devant être pris en compte pour la corbeille PCG', 1, current_timestamp, current_timestamp),
	(146, 'Traitementpcg66.fichecalcul_abattbicvnt', '71', 'Configuration de la gestion des mots de passe oubliés et du générateur de
	  mots de passes aléatoire.

	  Configuration par défaut:
	  <pre>
	  array(
	 	 Permet-on l''utilisation de la fonctionnalité "Mot de passe oublié" sur la page de login ?
	  	''mail_forgotten'' => false,

	  	''generators'' => array(
	  		''default'' => ''Password.PasswordPassword''
	  	),
	  	''checkers'' => array(
	  		''default'' => ''Password.PasswordAnssi''
	  	),
	    1°) Pour le générateur de mots de passe par défaut Password.PasswordPassword
	  	''length'' => 8,
	 	 Doit-on exclure les caractères équivoques 0, 1, l, i, o, I, O ?
	  	''typesafe'' => true,
	 	 Nombres de 0 à 9
	  	''class_number'' => true,
	 	 Minuscules de a à z
	  	''class_lower'' => true,
	 	 Majuscules de a à z
	  	''class_upper'' => true,
	 	 Caractères spéciaux ,;.!?+-
	  	''class_symbol'' => true
	  )
	  <pre>', 1, current_timestamp, current_timestamp),
	(147, 'Traitementpcg66.fichecalcul_abattbicsrv', '50', 'Variable contenant une chaîne de caractères (stockée en base) pour le
	  n° de convention annuelle d''objectifs et de moyens
      (unqiue par année et qui devant être changé chaque année)
      Cui.numconventionobj', 1, current_timestamp, current_timestamp),
	(148, 'Traitementpcg66.fichecalcul_abattbncsrv', '34', NULL, 1, current_timestamp, current_timestamp),
	(149, 'Traitementpcg66.fichecalcul_abattagriagri', '87', NULL, 1, current_timestamp, current_timestamp),
	(44, 'alerteFinSession', 'true', 'L''id technique de l''enregistrement de la table statutsrdvs ("Statut du RDV")
	  qui indique que l''allocataire était présent

	  Utilisé pour vérifier l''état du RDV et bloquer l''ajout d''un nouveau RDV
      si le RDV en cours est à l''état "Prévu"

	  @var integer
	  @default null', 1, current_timestamp, current_timestamp),
	(150, 'Nonorientationproep66.delaiCreationContrat', '60', NULL, 1, current_timestamp, current_timestamp),
	(47, 'AjoutOrientationPossible.toppersdrodevorsa', '[null,"0","1"]', 'Permet de définir si on peut ou non ajouter une nouvelle orientation à un dossier
	    - toppersdrodevorsa     --> par défaut 1 (personne soumise à droit et devoir)
	    - situationetatdosrsa   --> par défaut array( ''Z'', 2, 3, 4) (dossier dans un droit ouvert et versable)

	  INFO: depuis le passage en CakePHP 2.x, il faut mettre les valeurs numériques entre apostrophes.', 1, current_timestamp, current_timestamp),
	(151, 'Contratinsertion.Cg66.updateEncoursbilan', '"2 month"', NULL, 1, current_timestamp, current_timestamp),
	(152, 'Chargeinsertion.Secretaire.group_id', '[16,7,12]', 'Permet de spécifier les noms de serveurs servant d''environnement de
	  production afin que les mails ne soient pas envoyés à leurs destinataires
	  "normaux", mais à l''expéditeur du mail.

	  @see WebrsaEmailConfig::isTestEnvironment()

	  @param array

	  @default null', 1, current_timestamp, current_timestamp),
	(153, 'Email', '{"smtpOptions":{"port":"25","timeout":"30","host":"","username":"","password":"","client":"smtp_helo_hostname"}}', 'Permet d''utiliser le module ROME V3:
	 	- pour tous les départements:
	 		 shell: ImportCsvCodesRomeV3Shell
	 		 menu: Administration > Paramétrages > Codes ROME V3 (en fonction des habilitations)
	 		 menu: Recherches > Par DSP
	 		 menu du dossier RSA: DEM ou CJT > Droit > DSP d''origine
	 		 menu du dossier RSA: DEM ou CJT > Droit > MAJ DSP', 1, current_timestamp, current_timestamp),
	(154, 'Apre66.EmailPiecesmanquantes.from', '"emailaprefrom@cgxxxx.fr"', 'Lorsque un CER est complexe, il ne sera clôt que si il dépasse la
          date de cloture + la valeur de cette variable

	  Voir le document appdocsDocumentation administrateurs.odt, partie
	  "Intervalles PostgreSQL"', 1, current_timestamp, current_timestamp),
	(155, 'Apre66.EmailPiecesmanquantes.replyto', '"emailaprefrom@cgxxxx.fr"', 'Validation javascript - Options', 1, current_timestamp, current_timestamp),
	(156, 'Criterecer.delaidetectionnonvalidnotifie', '"45 days"', NULL, 1, current_timestamp, current_timestamp),
	(157, 'Nonoriente66.notisemploi.typeorientId', '["5","7","2"]', NULL, 1, current_timestamp, current_timestamp),
	(158, 'Nonoriente66.TypeorientIdSocial', '7', 'Ordre d''affichage des dossiers EP selon différentes actions (voir l''URL,
	  ajouter le suffixe .order).

	  Les clés de configuration sont les suivantes (CG 58, 66 et 93):
	  	- Dossierseps.choose.order
	  	- Commissionseps.decisionep.order
	  	- Commissionseps.decisioncg.order (CG 66 et 93 uniquement)
	  	- Commissionseps.printOrdresDuJour.order
	  	- Commissionseps.traiterep.order
	  	- Commissionseps.traitercg.order (CG 66 et 93 uniquement)', 1, current_timestamp, current_timestamp),
	(160, 'FicheCandidature.Email.from', '"emailaprefrom@cgxxxx.fr"', NULL, 1, current_timestamp, current_timestamp),
	(161, 'FicheCandidature.Email.replyto', '"emailaprefrom@cgxxxx.fr"', NULL, 1, current_timestamp, current_timestamp),
	(162, 'Contratinsertion.Cg66.Rendezvous', '{"conditions":{"typerdv_id":1,"statutrdv_id":17},"statutrdv_id":1}', NULL, 1, current_timestamp, current_timestamp),
	(163, 'Corbeillepcg.descriptionpdoId', '["1"]', NULL, 1, current_timestamp, current_timestamp),
	(165, 'Rendezvous.Ajoutpossible.statutrdv_id', '17', 'Permet de désactiver l''Editeur de requêtes', 1, current_timestamp, current_timestamp),
	(166, 'ActioncandidatPersonne.Partenaire.id', '["61"]', 'Alerte pour le changement d''adresse
	  delai en nombre de mois', 1, current_timestamp, current_timestamp),
	(167, 'Nonorganismeagree.Structurereferente.id', '["23"]', 'Réglages d''imprimante', 1, current_timestamp, current_timestamp),
	(164, 'ActioncandidatPersonne.Actioncandidat.typeregionId', '["124","178"]', 'Permet de désactiver le "cadenas" situé en haut à droite', 1, current_timestamp, current_timestamp),
	(168, 'ActioncandidatPersonne.Actioncandidat.typeregionPoursuitecgId', '["178"]', 'Option dynamique dans les EPs', 1, current_timestamp, current_timestamp),
	(74, 'WebrsaEmailConfig.testEnvironments', '[""]', 'ID de Emploi - Pôle emploi, peut contenir plusieurs valeurs (si besoin)', 1, current_timestamp, current_timestamp),
	(169, 'Contratinsertion.Cg66.toleranceDroitClosCerComplexe', '"6 months"', 'ID de la valeur du Tag à créer lorsque on "Tag" depuis Gestionsdoublons::index()', 1, current_timestamp, current_timestamp),
	(170, 'ValidationJS.enabled', 'true', NULL, 1, current_timestamp, current_timestamp),
	(171, 'ValidationOnchange.enabled', 'true', 'Options modifiable des cohortes liés aux tags (TAG et DossierPCG)', 1, current_timestamp, current_timestamp),
	(172, 'ValidationOnsubmit.enabled', 'true', 'Sauvegarde recherches', 1, current_timestamp, current_timestamp),
	(173, 'Dossierseps.choose.order', '["Personne.nom","Personne.prenom"]', NULL, 1, current_timestamp, current_timestamp),
	(174, 'Commissionseps.decisionep.order', '["Personne.nom","Personne.prenom"]', NULL, 1, current_timestamp, current_timestamp),
	(175, 'Commissionseps.decisioncg.order', '["Personne.nom","Personne.prenom"]', 'Fiche de liaison
	  Défini l''origine d''un dossier PCG crée par la primoanalyse d''une fiche de liaison', 1, current_timestamp, current_timestamp),
	(176, 'Commissionseps.printOrdresDuJour.order', '["Personne.nom","Personne.prenom"]', 'Affiche les anciens moteurs de cohorte et de recherche pour comparaison', 1, current_timestamp, current_timestamp),
	(177, 'Commissionseps.traiterep.order', '["Personne.nom","Personne.prenom"]', 'Export CSV des droits des groupes', 1, current_timestamp, current_timestamp),
	(178, 'Commissionseps.traitercg.order', '["Personne.nom","Personne.prenom"]', 'Nouveaux moteurs de recherche, permet d''afficher la liste des codes INSEE
	  sous forme de cases à cocher multiples plutôt que sous forme de liste
	  déroulante.

	  La clé "multiple" permet d''activer cette fonctionnalité.
	  La clé "multiple_larger_1" permet de retrouver la liste déroulante
	  "classique" si le nombre de codes INSEE est inférieur ou égal à 1.

	  @var boolean
	  @default null', 1, current_timestamp, current_timestamp),
	(179, 'ValidateAllowEmpty.Adresse.libtypevoie', 'true', 'Affichage de Fleches pour effectuer un order sur les colonnes de résultat', 1, current_timestamp, current_timestamp),
	(180, 'ValidateAllowEmpty.Adresse.nomvoie', 'true', 'Nouveau système d''attribution des droits (par Controllers)', 1, current_timestamp, current_timestamp),
	(181, 'ValidateAllowEmpty.Dossier.dtdemrsa', 'false', 'Options modifiable des moteurs de recherche et de cohorte', 1, current_timestamp, current_timestamp),
	(182, 'Etatjetons.enabled', 'true', 'Visualisation des données CAF d''une personne', 1, current_timestamp, current_timestamp),
	(183, 'Requestmanager.enabled', 'true', 'Configuration des plages horaires d''accès à l''application.

	  Lorsque cette fonctionnalité est activée, il n''est possible d''être connecté
	  que dans une plage horaire et hormis certains jours de la semaine.
	  Il est possible de spécifier certains groupes d''utilisateurs qui ne seront
	  pas affectés par cette limitation.

	  Les clés de configuration sont les suivantes:
	 	- enabled: mettre à true pour utiliser cette fonctionnalité; booléen, null
	 	  (false) par défaut
	 	- heure_debut: l''heure à partir de laquelle (incluse) les utilisateurs
	 	  peuvent se connecter, integer entre 0 et 23; 1 par défaut
	 	- heure_fin: l''heure jusqu''à laquelle (incluse) les utilisateurs peuvent
	 	  se connecter; integer entre 0 et 23; 23 par défaut
	 	- jours_weekend: les jours de la semaine qui constituent le week-end;
	 	  array de string parmi ''Mon'', ''Tue'', ''Wed'', ''Thu'', ''Fri'', ''Sat'', ''Sun'';
	 	  array( ''Sat'', ''Sun'' ) par défaut
	 	- groupes_acceptes: ids techniques des groupes d''utilisateurs non soumis
	 	  à ces restrictions (ex. les Administrateurs); array d''integers; array()
	 	  par défaut

	  La configuration est vérifiée dans la partie "Administration" > "Vérification
	  de l''application".', 1, current_timestamp, current_timestamp),
	(184, 'Canton.useAdresseCanton', 'true', 'Suffix des fichiers de traductions spécialisés pour le CG
	  ex: controller_action_suffix.po -> dossiers_index_cg01.po', 1, current_timestamp, current_timestamp),
	(185, 'Alerte.changement_adresse.enabled', 'true', '!\ N''activer ce module que sur une période courte !\
	  Permet de logger tous les appels de pages, active également l''accès au module de visualisation dans administration
	  Désactiver et supprimer apptmplogstrace.log après utilisation', 1, current_timestamp, current_timestamp),
	(186, 'Alerte.changement_adresse.delai', '2', NULL, 1, current_timestamp, current_timestamp),
	(187, 'Dossierspcgs66.imprimer.Impression.RectoVerso', 'true', 'Active le module date picker (calendrier javascript)', 1, current_timestamp, current_timestamp),
	(188, 'Dossierspcgs66.imprimer_cohorte.Impression.RectoVerso', 'true', 'Tableau de bord principal', 1, current_timestamp, current_timestamp),
	(189, 'Commissionseps.defautinsertionep66.decision.type', '{"maintienorientsoc":["social","social"],"reorientationprofverssoc":["emploi","social"],"reorientationsocversprof":["social","emploi"]}', 'Permet le redimensionnement automatique des textarea

	  textarea.auto_resize.all => tout les textarea
	  textarea.auto_resize.controllername.all => tout les textarea d''un controlleur en particulier
	  textarea.auto_resize.controllername.action => tout les textarea d''un controlleur et d''une action en particulier', 1, current_timestamp, current_timestamp),
	(190, 'Commissionseps.defautinsertionep66.isemploi', '[2,30]', 'Filtre de recherche par Prestations
	  Il est possible de remplacer la clef "common" par "Controller.action"
	  pour spécifier par Controller et par action
	  Si "common" est spécifié en plus du "Controller.action",
	  la configuration de "Controller.action" prend le dessus

	  Valeurs possibles (array) :
	  0 = Sans prestation
	  1 = Demandeur ou Conjoint
	  ''DEM'' = Demandeur
	  ''CJT'' = Conjoint
	  ''ENF'' = Enfant
	  ''AUT'' = Autre
	  ''RDO'' = Responsable du dossier', 1, current_timestamp, current_timestamp),
	(191, 'Module.Cui.enabled', 'true', 'Permet l''affichage des erreurs cachés', 1, current_timestamp, current_timestamp),
	(223, 'Bilanparcours66.Fichesynthese.Impression', 'false', NULL, 1, current_timestamp, current_timestamp),
	(63, 'Dossierseps.conditionsSelection', '[]', 'Conditions supplémentaires utilisées lors de la sélection des dossiers pour
	  une commission d''EP (url: dossiersepschoose...).

	  Au CG 58, il faut que les dossiers sélectionnables soient dans un droit
	  ouvert et que les allocataires soient soumis à droits et devoirs.

	  @param array', 1, current_timestamp, current_timestamp),
	(192, 'Gestionsdoublons.index.useTag', 'true', 'Permet de filtrer les modules visibles dans l''onglet "Droits" des formulaires
	  d''ajout et de modification des groupes et des utilisateurs en fonction du
	  département connecté ainsi que des modules activés.

	  L''idée est que les modules en question ne sont normalement pas accessibles
	  et qu''il ne font donc qu''ajouter de la complexité dans ces écrans de droits.

	  Une valeur "null" ou "false" filtre les permissions, une valeur "true" ne
	  les filtre pas (comme avant la version 3.2.0).

	  @default null', 1, current_timestamp, current_timestamp),
	(193, 'Gestionsdoublons.index.Tag.valeurtag_id', '1', 'Permet de faire apparaître ou non dans le menu "Administration" le
	  sous-menu "Flux CNAF" qui ne sert qu''aux développeurs car ces écrans
	  présentent le format technique des flux CNAF.', 1, current_timestamp, current_timestamp),
	(194, 'Tag.Options.enums', '{"Personne":{"trancheage":{"0_24":"< 25","25_30":"25 - 30","31_55":"31 - 55","56_65":"56 - 65","66_999":"> 65"}},"Foyer":{"nb_enfants":["0",">= 1",">= 2",">= 3",">= 4",">= 5"]},"Detailcalculdroitrsa":{"mtrsavers":{"0_99":"< 100€","100_199":"100€ - 199€","200_299":"200€ - 299€","300_399":"300€ - 399€","400_499":"400€ - 499€","500_599":"500€ - 599€","600_699":"600€ - 699€","800_999":"800€ - 999€","999_9999":"> 1000 €"}}}', 'Nombre d''enregistrements de la table correspondancespersonnes à sauvegarder
	  par "tranche" via le shell CorrespondancepersonneShell afin d''éviter de
	  saturer la mémoire lors du traitement.

	  @type integer
	  @default 250000', 1, current_timestamp, current_timestamp),
	(195, 'Module.Savesearch.enabled', 'true', 'Permet d''ajouter une orientation "Non orienté" aux bénéficiaires d''un foyer
	  dès lors qu''ils sont soumis à droits et devoirs et que l''on ajoute ou
	  modifie une personne du foyer ou que l''on ajoute ou modifie une ressource
	  d''une personne du foyer.

	  @type boolean
	  @default null|false', 1, current_timestamp, current_timestamp),
	(196, 'Module.Savesearch.mon_menu.enabled', 'true', 'Blocage des thématiques pour les EP', 1, current_timestamp, current_timestamp),
	(197, 'Module.Savesearch.mon_menu.name', '"Mon menu"', 'Spécifie les valeurs attendu dans le champ décision selon le type d''EP
      EP Parcours regroupement_id 1
      EP Audition regroupement_id 2', 1, current_timestamp, current_timestamp),
	(198, 'Fichedeliaisons.typepdo_id', '18', 'Permet de parcourir les détails des allocataires n''ayant pas de prestation
      RSA dans le menu du dossier.', 1, current_timestamp, current_timestamp),
	(199, 'Anciensmoteurs.enabled', 'false', 'Spécifie les valeurs de Contratinsertion.positioncer utilisé
      pour l''activation du bouton de reconduction tacite.', 1, current_timestamp, current_timestamp),
	(200, 'Module.Synthesedroits.enabled', 'true', NULL, 1, current_timestamp, current_timestamp),
	(201, 'ConfigurableQuery.common.filters.Adresse.numcom', '{"multiple":false,"multiple_larger_1":false}', 'Paramétrage du module "Cohorte Plan Pauvreté"

      Vous devez définir le jour de début et de jour de fin de la période
      des nouveaux entrants.
      Cette période est fonction de l''intégratio ndes flux CNAF et PE.

      @param array', 1, current_timestamp, current_timestamp),
	(202, 'ConfigurableQuery.common.two_ways_order.enabled', 'true', 'Paramétrage du module "Cohorte Plan Pauvreté"

      Vous devez définir le jour de début et de jour de fin de la période
      des nouveaux entrants.
      Ces valeurs doivent être les mêmes que la variable de configuration
      PlanPauvrete.Cohorte.Moisprecedent

      @param array', 1, current_timestamp, current_timestamp),
	(203, 'Module.Attributiondroits.enabled', 'true', 'Valeur possible du nombre de résultats possible par recherche', 1, current_timestamp, current_timestamp),
	(109, 'Search.Options.enums', '{"Personne":{"trancheage":{"0_24":"- 25 ans","25_34":"25 - 34 ans","35_44":"35 - 44 ans","45_54":"45 - 54 ans","55_999":"+ 55 ans"}}}', 'Nom du model odt pour les fiches de liaison des Bilan parcours', 1, current_timestamp, current_timestamp),
	(204, 'Module.Donneescaf.enabled', 'true', 'Bilanparcours impression fiche synthèse activate
      True => Montrer et activé le bouton
      False => Cacher le bouton', 1, current_timestamp, current_timestamp),
	(205, 'Module.PlagesHoraires', '{"enabled":false,"heure_debut":8,"heure_fin":19,"jours_weekend":["Sat","Sun"],"groupes_acceptes":[1]}', 'Paramétrage des durées d''engagement d''un CER
      pour les recherches et les ajoutsmodifs', 1, current_timestamp, current_timestamp),
	(206, 'WebrsaTranslator.suffix', '"cg99X"', NULL, 1, current_timestamp, current_timestamp),
	(207, 'Module.Logtrace.enabled', 'false', NULL, 1, current_timestamp, current_timestamp),
	(208, 'Module.Logtrace.total_duration', '3600', NULL, 1, current_timestamp, current_timestamp),
	(209, 'Module.Datepicker.enabled', 'true', NULL, 1, current_timestamp, current_timestamp),
	(210, 'Module.Dashboards.enabled', 'true', NULL, 1, current_timestamp, current_timestamp),
	(211, 'textarea.auto_resize.all', 'true', NULL, 1, current_timestamp, current_timestamp),
	(212, 'ConfigurableQuery.common.filters.has_prestation', '[0,1,"DEM","CJT"]', NULL, 1, current_timestamp, current_timestamp),
	(213, 'Module.DisplayValidationErrors.enabled', 'true', NULL, 1, current_timestamp, current_timestamp),
	(214, 'Module.Permissions.all', 'false', NULL, 1, current_timestamp, current_timestamp),
	(215, 'Module.Fluxcnaf.enabled', 'false', NULL, 1, current_timestamp, current_timestamp),
	(216, 'Correspondancepersonne.max', '250000', NULL, 1, current_timestamp, current_timestamp),
	(217, 'Foyer.refreshSoumisADroitsEtDevoirs.ajoutOrientstruct', 'null', NULL, 1, current_timestamp, current_timestamp),
	(218, 'Blocage.thematique.ep', '[]', NULL, 1, current_timestamp, current_timestamp),
	(219, 'Contratinsertion.DateEP.DecisionParGroupement', '{"1":"maintien","2":"maintienorientsoc"}', NULL, 1, current_timestamp, current_timestamp),
	(220, 'Contratinsertion.Reconduction.Allow', '["encours","fincontrat","perime"]', NULL, 1, current_timestamp, current_timestamp),
	(221, 'Contratinsertion.Reconduction.Duree', '24', NULL, 1, current_timestamp, current_timestamp),
	(115, 'PlanPauvrete.Cohorte.Moisprecedent', '{"deb":"05"}', 'Activation du module des cohortes du plan pauvreté.Activation de la cohorte : Nouveaux entrantsActivation de la cohorte : Nouveaux entrants inscrits à Pôle EmploiActivation de la cohorte : Impression des orientations des nouveaux entrants inscrits à Pôle EmploiActivation de la cohorte : Nouveaux entrants à envoyer en information collectiveActivation de la cohorte : Impression des courriers d''information collective à envoyer aux nouveaux entrantsActivation de la cohorte : Liste des nouveaux entrant convoqués en information collectiveActivation de la cohorte : Convocation du SECOND RENDEZ VOUS des nouveaux entrantsActivation de la cohorte : Impression des courriers DU SECOND RENDEZ-VOUS d''information collective à envoyer aux nouveaux entrantsActivation de la cohorte : Allocataires du stockActivation de la cohorte : Allocataires du stock inscrits à Pôle EmploiActivation de la cohorte : Impression des orientations des allocataires du stock inscrits à Pôle EmploiActivation de la cohorte : Stock à envoyer en information collectiveActivation de la cohorte : Impression des courriers d''information collective à envoyer au stockActivation de la cohorte : Liste des stocks convoqués en information collectiveActivation de la cohorte : Convocation du SECOND RENDEZ VOUS des stocksActivation de la cohorte : Impression des courriers DU SECOND RENDEZ-VOUS d''information collective à envoyer au stockActivation du module des cohortes du plan pauvreté.Activation du module des cohortes du plan pauvreté.Activation de la page de recherche des primo-accédants', 1, current_timestamp, current_timestamp),
	(222, 'Bilanparcours66.ficheLiaisonodt', '"bilanparcours_ficheliaison"', NULL, 1, current_timestamp, current_timestamp),
	(230, 'ConfigurableQuery.Titrescreanciers.cohorte_validation', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Prestation":{"rolepers":"DEM"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":["Creance.moismoucompta"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Personne.nom_complet_court","1":"Dossier.numdemrsa","2":"Titrecreancier.mnttitr","3":"Titrecreancier.etat","4":"Motifemissiontitrecreancier.nom","5":"Titrecreancier.commentaire_complet","\/Titrescreanciers\/index\/#Creance.id#":{"class":"view external"}},"innerTable":{"0":"Dossier.matricule","1":"Personne.dtnai","Adresse.numcom":{"options":[]},"2":"Prestation.rolepers","3":"Structurereferenteparcours.lib_struc","4":"Referentparcours.nom_complet"}},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Menu "CohortesGestion de Listes" > "Créances" > "Validation"
	  Sources Cohorte > Titrecreanciers > cohorte_validation


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
					),
					''Prestation'' => array(
						''rolepers'' => ''DEM'',
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(
					''Contratinsertion.forme_ci'' => ''S'',
					''OR'' => array(
						''Contratinsertion.decision_ci IS NULL'',
						''Contratinsertion.decision_ci'' => ''E'',
					)
				),
				 2.3 Tri par défaut
				''order'' => array( ''Creance.moismoucompta'' )
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Personne.nom_complet_court'',
					''Dossier.numdemrsa'',
					''Titrecreancier.mnttitr'',
					''Titrecreancier.etat'',
					''Motifemissiontitrecreancier.nom'',
					''Titrecreancier.commentaire_complet'',
					''Titrescreanciersindex#Creance.id#'' => array( ''class'' => ''view external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Dossier.matricule'',
					''Personne.dtnai'',
					''Adresse.numcom'' => array(
						''options'' => array()
					),
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''512M''
			)
		)', 4, current_timestamp, current_timestamp),
	(246, 'Tags.cohorte.range_date_butoir', '{"1":"1 mois","1.5":"1 mois et demi","2":"2 mois","3":"3 mois","6":"6 mois","12":"1 an","24":"2 ans","36":"3 ans"}', 'Choix possible pour le préremplissage de la date butoir


		array(
			''1'' => ''1 mois'',
			''1.5'' => ''1 mois et demi'',  Supporte les nombres de type float
			2 => ''2 mois'',
			3 => ''3 mois'',
			6 => ''6 mois'',
			12 => ''1 an'',
			24 => ''2 ans'',
			36 => ''3 ans'',
		)', 5, current_timestamp, current_timestamp),
	(255, 'relances.prestataire.mail.expediteur', '""', 'Expéditeur du message du mail contenant les les SMS à envoyer.', 8, current_timestamp, current_timestamp),
	(224, 'ConfigurableQuery.Traitementspcgs66.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet_court","2":"Dossierpcg66.datereceptionpdo","3":"Dossierpcg66.user_id","4":"Traitementpcg66.typetraitement","5":"Traitementpcg66.created","6":"Situationpdo.libelle","7":"Traitementpcg66.descriptionpdo_id","8":"Traitementpcg66.datereception","9":"Traitementpcg66.dateecheance","10":"Traitementpcg66.clos","11":"Traitementpcg66.annule","12":"Fichiermodule.nb_fichiers_lies","13":"Dossier.locked","14":"Canton.canton","\/Traitementspcgs66\/index\/#Personnepcg66.personne_id#\/#Dossierpcg66.id#":{"class":"view"}},"innerTable":["Situationdossierrsa.etatdosrsa","Personne.nomcomnai","Personne.dtnai","Adresse.numcom","Personne.nir","Dossier.matricule","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu "Recherches"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Personne.nom_complet_court'',
					''Dossierpcg66.datereceptionpdo'',
					''Dossierpcg66.user_id'',
					''Traitementpcg66.typetraitement'',
					''Traitementpcg66.created'',
					''Situationpdo.libelle'',
					''Traitementpcg66.descriptionpdo_id'',
					''Traitementpcg66.datereception'',
					''Traitementpcg66.dateecheance'',
					''Traitementpcg66.clos'',
					''Traitementpcg66.annule'',
					''Fichiermodule.nb_fichiers_lies'',
					''Dossier.locked'',
					''Canton.canton'',
					''Traitementspcgs66index#Personnepcg66.personne_id##Dossierpcg66.id#'' => array( ''class'' => ''view'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Situationdossierrsa.etatdosrsa'',
					''Personne.nomcomnai'',
					''Personne.dtnai'',
					''Adresse.numcom'',
					''Personne.nir'',
					''Dossier.matricule'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 2, current_timestamp, current_timestamp),
	(225, 'ConfigurableQuery.Traitementspcgs66.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":[]},"results":{"fields":["Dossier.numdemrsa","Personne.nom_complet_court","User.nom_complet","Traitementpcg66.typetraitement","Traitementpcg66.created","Situationpdo.libelle","Traitementpcg66.descriptionpdo_id","Dossierpcg66.datereceptionpdo","Traitementpcg66.datereception","Traitementpcg66.dateecheance","Traitementpcg66.clos","Traitementpcg66.annule","Fichiermodule.nb_fichiers_lies","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV,  menu "Recherches"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Traitementspcgs66.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Traitementspcgs66.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Personne.nom_complet_court'',
					''User.nom_complet'',
					''Traitementpcg66.typetraitement'',
					''Traitementpcg66.created'',
					''Situationpdo.libelle'',
					''Traitementpcg66.descriptionpdo_id'',
					''Dossierpcg66.datereceptionpdo'',
					''Traitementpcg66.datereception'',
					''Traitementpcg66.dateecheance'',
					''Traitementpcg66.clos'',
					''Traitementpcg66.annule'',
					''Fichiermodule.nb_fichiers_lies'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Traitementspcgs66.search.ini_set'' ),
		)', 2, current_timestamp, current_timestamp),
	(226, 'QueryImpression.Titresuiviinfopayeur', '{"fields":["Dossier.numdemrsa","Dossier.dtdemrsa","Personne.nir","Situationdossierrsa.etatdosrsa","Personne.nom_complet_prenoms","Personne.dtnai","Adresse.numvoie","Adresse.libtypevoie","Adresse.nomvoie","Adresse.complideadr","Adresse.compladr","Adresse.codepos","Adresse.nomcom","Typeorient.lib_type_orient","Personne.idassedic","Dossier.matricule","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Personne.sexe","Dsp.natlog","Canton.canton","Typetitrecreancierinfopayeur.nom","Titresuiviinfopayeur.commentaire","Titresuiviinfopayeur.dtenvoipayeur","Titresuiviinfopayeur.retourpayeur","Recourgracieux.etat","Recourgracieux.dtarrivee","Recourgracieux.dtbutoir","Recourgracieux.dtreception","Recourgracieux.dtaffectation","Recourgracieux.dtdecision","Recourgracieux.mention"],"recursive":1,"conditions":[],"joins":[]}', 'Champs qui seront passés à la fonction de transformation des ODT dans les emails des Titresuiviinfopayeur


		array(
			''fields'' => array(
				''Dossier.numdemrsa'',
				''Dossier.dtdemrsa'',
				''Personne.nir'',
				''Situationdossierrsa.etatdosrsa'',
				''Personne.nom_complet_prenoms'',  FIXME: nom completcourtprenoms ?
				''Personne.dtnai'',
				''Adresse.numvoie'',
				''Adresse.libtypevoie'',
				''Adresse.nomvoie'',
				''Adresse.complideadr'',
				''Adresse.compladr'',
				''Adresse.codepos'',
				''Adresse.nomcom'',
				''Typeorient.lib_type_orient'',
				''Personne.idassedic'',
				''Dossier.matricule'',
				''Structurereferenteparcours.lib_struc'',
				''Referentparcours.nom_complet'',
				''Personne.sexe'',
				''Dsp.natlog'',
				''Canton.canton'',
				''Typetitrecreancierinfopayeur.nom'',
				''Titresuiviinfopayeur.commentaire'',
				''Titresuiviinfopayeur.dtenvoipayeur'',
				''Titresuiviinfopayeur.retourpayeur'',
				''Recourgracieux.etat'',
				''Recourgracieux.dtarrivee'',
				''Recourgracieux.dtbutoir'',
				''Recourgracieux.dtreception'',
				''Recourgracieux.dtaffectation'',
				''Recourgracieux.dtdecision'',
				''Recourgracieux.mention'',
			),
			''recursive'' => 1,
			''conditions'' => array(),
			''joins'' => array()
		)', 3, current_timestamp, current_timestamp),
	(227, 'CompleteDataImpression.Titresuiviinfopayeur.modeles', '[]', 'Modèles necessaire à transformation des ODT dans les emails des Titresuiviinfopayeur


		array()', 3, current_timestamp, current_timestamp),
	(228, 'ConfigurableQuery.Titrescreanciers.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"},"Prestation":{"rolepers":"DEM"}},"accepted":[],"skip":[],"has":{"0":"Cui","Orientstruct":{"Orientstruct.statut_orient":"Orienté"},"Contratinsertion":{"Contratinsertion.decision_ci":"V"},"1":"Personnepcg66"}},"query":{"restrict":{"Prestation.rolepers":"DEM"},"conditions":[],"order":["Personne.nom"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Dossier.dtdemrsa","2":"Personne.nom_complet_court","3":"Creance.natcre","4":"Creance.motiindu","5":"Creance.oriindu","6":"Creance.mtsolreelcretrans","7":"Titrecreancier.etat","Creance.id":{"hidden":true},"\/Titrescreanciers\/index\/#Creance.id#":{"class":"view"}},"innerTable":{"0":"Dossier.matricule","1":"Personne.dtnai","Adresse.numcom":{"options":[]},"2":"Prestation.rolepers","3":"Structurereferenteparcours.lib_struc","4":"Referentparcours.nom_complet"}},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Menu "Recherches" > "Par créances" > "Par titre créanciers"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
						 Case à cocher "Filtrer par date de demande RSA"
						''dtdemrsa'' => ''0'',
						 Du (inclus)
						''dtdemrsa_from'' => ''TAB::-1WEEK'',
						 Au (inclus)
						''dtdemrsa_to'' => ''TAB::NOW'',
					),
					''Prestation'' => array(
						 ''rolepers'' => ''DEM''
					),  Demandeur du RSA
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array(
					''Cui'',
					''Orientstruct'' => array(
						''Orientstruct.statut_orient'' => ''Orienté'',
						 Orientstruct possède des conditions supplémentaire dans le modèle WebrsaRechercheDossier pour le CD66
					),
					''Contratinsertion'' => array(
						''Contratinsertion.decision_ci'' => ''V''
					),
					''Personnepcg66''
				)
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Prestation.rolepers'' => ''DEM'',  Demandeur du RSA
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array( ''Personne.nom'' )
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nom_complet_court'',
					''Creance.natcre'',
					''Creance.motiindu'',
					''Creance.oriindu'',
					''Creance.mtsolreelcretrans'',
					''Titrecreancier.etat'',
					''Creance.id'' => array (''hidden'' => true),
					''Titrescreanciersindex#Creance.id#'' => array( ''class'' => ''view'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Dossier.matricule'',
					''Personne.dtnai'',
					''Adresse.numcom'' => array(
						''options'' => array()
					),
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''512M''
			)
		)', 4, current_timestamp, current_timestamp),
(229, 'ConfigurableQuery.Titrescreanciers.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"},"Prestation":{"rolepers":"DEM"}},"accepted":[],"skip":[],"has":{"0":"Cui","Orientstruct":{"Orientstruct.statut_orient":"Orienté"},"Contratinsertion":{"Contratinsertion.decision_ci":"V"},"1":"Personnepcg66"}},"query":{"restrict":{"Prestation.rolepers":"DEM"},"conditions":[],"order":["Personne.nom"]},"results":{"fields":["Dossier.numdemrsa","Dossier.dtdemrsa","Personne.nir","Situationdossierrsa.etatdosrsa","Personne.nom_complet_prenoms","Personne.dtnai","Adresse.numvoie","Adresse.libtypevoie","Adresse.nomvoie","Adresse.complideadr","Adresse.compladr","Adresse.codepos","Adresse.nomcom","Personne.idassedic","Dossier.matricule","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Personne.sexe","Canton.canton"]},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Menu "Recherches" > "Par créances" > "Par titre créanciers"
	  Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Titrescreanciers.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Titrescreanciers.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Personne.nom_complet_prenoms'',  FIXME: nom completcourtprenoms ?
					''Personne.dtnai'',
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Personne.idassedic'',
					''Dossier.matricule'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Personne.sexe'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Titrescreanciers.search.ini_set'' ),
		)', 4, current_timestamp, current_timestamp),
(231, 'ConfigurableQuery.Titrescreanciers.exportcsv_validation', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Prestation":{"rolepers":"DEM"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":["Creance.moismoucompta"]},"results":{"fields":["Dossier.numdemrsa","Dossier.dtdemrsa","Personne.nir","Situationdossierrsa.etatdosrsa","Personne.nom_complet_prenoms","Personne.dtnai","Adresse.numvoie","Adresse.libtypevoie","Adresse.nomvoie","Adresse.complideadr","Adresse.compladr","Adresse.codepos","Adresse.nomcom","Personne.idassedic","Dossier.matricule","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Personne.sexe","Canton.canton"]},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Menu "CohortesGestion de Listes" > "Créances" > "Validation"
	  Sources Cohorte > Titrecreanciers > cohorte_validation
	  Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Titrescreanciers.cohorte_validation.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Titrescreanciers.cohorte_validation.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
					''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Personne.nom_complet_prenoms'',  FIXME: nom completcourtprenoms ?
					''Personne.dtnai'',
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Personne.idassedic'',
					''Dossier.matricule'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Personne.sexe'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Titrescreanciers.cohorte_validation.ini_set'' ),
		)', 4, current_timestamp, current_timestamp),
(232, 'ConfigurableQuery.Titrescreanciers.cohorte_transmissioncompta', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Prestation":{"rolepers":"DEM"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":["Creance.moismoucompta"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Personne.nom_complet_court","1":"Dossier.numdemrsa","2":"Titrecreancier.mnttitr","3":"Titrecreancier.etat","4":"Titrecreancier.mention","\/Titrescreanciers\/index\/#Creance.id#":{"class":"view external"}},"innerTable":{"0":"Dossier.matricule","1":"Personne.dtnai","Adresse.numcom":{"options":[]},"2":"Prestation.rolepers","3":"Structurereferenteparcours.lib_struc","4":"Referentparcours.nom_complet"}},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Menu "CohortesGestion de Listes" > "Créances" > "Transmission compta"
	  Sources Cohorte > Titrecreanciers > cohorte_transmissioncompta


		array(
			 1. Filtres de recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Titrescreanciers.cohorte_validation.filters'' ),
			 2. Recherche
			''query'' => Configure::read( ''ConfigurableQuery.Titrescreanciers.cohorte_validation.query'' ),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Personne.nom_complet_court'',
					''Dossier.numdemrsa'',
					''Titrecreancier.mnttitr'',
					''Titrecreancier.etat'',
					''Titrecreancier.mention'',
					''Titrescreanciersindex#Creance.id#'' => array( ''class'' => ''view external'' )
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Dossier.matricule'',
					''Personne.dtnai'',
					''Adresse.numcom'' => array(
						''options'' => array()
					),
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''512M''
			)
		)', 4, current_timestamp, current_timestamp),
(233, 'ConfigurableQuery.Titrescreanciers.exportcsv_transmissioncompta', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Prestation":{"rolepers":"DEM"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":["Creance.moismoucompta"]},"results":{"fields":["Dossier.numdemrsa","Dossier.dtdemrsa","Personne.nir","Situationdossierrsa.etatdosrsa","Personne.nom_complet_prenoms","Personne.dtnai","Adresse.numvoie","Adresse.libtypevoie","Adresse.nomvoie","Adresse.complideadr","Adresse.compladr","Adresse.codepos","Adresse.nomcom","Personne.idassedic","Dossier.matricule","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Personne.sexe","Canton.canton"]},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Menu "CohortesGestion de Listes" > "Créances" > "Transmission compta"
	  Sources Cohorte > Titrecreanciers > cohorte_transmissioncompta
	  Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Titrescreanciers.cohorte_validation.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Titrescreanciers.cohorte_validation.query'' ),
			 3. Résultats de la recherche
			''results'' => Configure::read( ''ConfigurableQuery.Titrescreanciers.exportcsv_validation.results'' ),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Titrescreanciers.cohorte_validation.ini_set'' )
		)', 4, current_timestamp, current_timestamp),
	(234, 'Titrescreanciers.csvfica.delimiteur', '";"', 'Export FICA paramétrages
	  Menu "CohortesGestion de Listes" > "Créances" > "Transmission compta"
	  Menu "Recherches" > "Par créances" > "Par titre créanciers" > Action : Export CSV FICA

'';''', 4, current_timestamp, current_timestamp),
	(235, 'Titrescreanciers.csvfica.fieldid.reftitrecreancier', '2', '2', 4, current_timestamp, current_timestamp),
	(236, 'Titrescreanciers.csvfica.fieldid.numtier', '1', '1', 4, current_timestamp, current_timestamp),
	(237, 'Titrescreanciers.csvfica.fieldid.dtbordereau', '17', '17', 4, current_timestamp, current_timestamp),
	(238, 'Titrescreanciers.csvfica.fieldid.numbordereau', '18', '18', 4, current_timestamp, current_timestamp),
	(239, 'Titrescreanciers.csvfica.fieldid.numtitr', '19', '19', 4, current_timestamp, current_timestamp),
	(240, 'Creances.FICA.NumAppliTiers', '"nn"', '''nn''', 4, current_timestamp, current_timestamp),
	(241, 'Creances.FICA.TypePaiement', '"TD"', '''TD''', 4, current_timestamp, current_timestamp),
	(242, 'Creances.FICA.CodeTiers', 'null', 'null', 4, current_timestamp, current_timestamp),
	(243, 'Creances.FICA.Champs', '["PAIEMENT","CODTIERS","REF","SCC","MONTANT","LIBVIR","OBJET","OBS","OBS2","RIB","$VCODE.016","NUMTITRE","NUMBORDERAU","NUMTIERS"]', 'array (
				''PAIEMENT'',''CODTIERS'',''REF'',''SCC'',''MONTANT'',''LIBVIR'',''OBJET'',''OBS'',''OBS2'',''RIB'',
				''LIBRIB'',''DESTCIVILITE'',''DESTNOM'',''DESTPRENOM'',''DESTCODPOSTAL'',''DESTCOMMUNE'',''DESTADRESSE'',''DESTADRESSE2'',
				''DOSSIER'',''PRESTATION'',''DECINUM'',''DECIREM'',''DECIDAT'',''DECIDATEFF'',''DECIDATFIN'',''DECINATURE'',''DECIPERIOD'',''DECIMONTANT'',
				''BENECIVILITE'',''BENENOM'',''BENEPRENOM'',''BENECODPOSTAL'',''BENECOMMUNE'',''BENEADRESSE'',''BENEADRESSE2'',''BENEDATNAIS'',
				''$VCODE.016'', ''NUMTITRE'',''NUMBORDERAU'',''NUMTIERS''
			)', 4, current_timestamp, current_timestamp),
(244, 'Creances.FICA.SCC', '{"RSD":25796,"RSI":25797,"RSU":25796,"RSB":25796,"RCD":25796,"RCI":25797,"RCU":25796,"RCB":25796,"RSJ":25796,"RCJ":25796}', 'Détermination de la valeur de SCC en fonction de la valeuf de NATPF :

	  Valeurs possibles pour NATPF
		"RSD" : RSA Socle (Financement sur fonds Conseil général)
		"RSI" : RSA Socle majoré (Financement sur fonds Conseil général)
		"RSU" : RSA Socle Etat Contrat aidé  (Financement sur fonds Etat)
		"RSB" : RSA Socle Local (Financement sur fonds Conseil général)
		"RCD" : RSA Activité (Financement sur fonds Etat)
		"RCI" : RSA Activité majoré (Financement sur fonds Etat)
		"RCU" : RSA Activité Etat Contrat aidé (Financement sur fonds Etat)
		"RCB" : RSA Activité Local (Financement sur fonds Conseil général)
		"RSJ" : RSA socle Jeune (Financement sur fonds Etat)
		"RCJ" : RSA activité Jeune (Financement sur fonds Etat)
	  La valeur de SCC devrait etre :
			"rsa socle" => 25796,
			"rsa socle majoré" => 25797



		array(
			"RSD" => 25796,
			"RSI" => 25797,
			"RSU" => 25796,
			"RSB" => 25796,
			"RCD" => 25796,
			"RCI" => 25797,
			"RCU" => 25796,
			"RCB" => 25796,
			"RSJ" => 25796,
			"RCJ" => 25796
		)', 4, current_timestamp, current_timestamp),
(245, 'Tags.cohorte.allowed.Requestgroup.id', '[7]', 'Catégories des requetes obtenus par le request manager affiché par actions


		array(
			7,  Noter nom de catégorie - Cohorte de tag
		)', 5, current_timestamp, current_timestamp),
(247, 'ConfigurableQuery.Tags.cohorte', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":{"0":"Cui","Orientstruct":{"Orientstruct.statut_orient":"Orienté"},"Contratinsertion":{"Contratinsertion.decision_ci":"V"},"1":"Personnepcg66"}},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Dossier.dtdemrsa","2":"Personne.nir","3":"Situationdossierrsa.etatdosrsa","4":"Personne.nom_complet_prenoms","5":"Adresse.complete","6":"Canton.canton","\/Dossiers\/view\/#Dossier.id#":{"class":"external"}},"innerTable":{"0":"Dossier.matricule","1":"Personne.dtnai","Adresse.numcom":{"options":[]},"2":"Prestation.rolepers","3":"Structurereferenteparcours.lib_struc","4":"Referentparcours.nom_complet"}},"ini_set":[]}', 'Menu "Recherches"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array(
					''Cui'',
					''Orientstruct'' => array(
						''Orientstruct.statut_orient'' => ''Orienté'',
						 Orientstruct possède des conditions supplémentaire dans le modèle WebrsaRechercheDossier pour le CD66
					),
					''Contratinsertion'' => array(
						''Contratinsertion.decision_ci'' => ''V''
					),
					''Personnepcg66''
				)
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Personne.nom_complet_prenoms'',
					''Adresse.complete'',
					''Canton.canton'',
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Dossier.matricule'',
					''Personne.dtnai'',
					''Adresse.numcom'' => array(
						''options'' => array()
					),
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 5, current_timestamp, current_timestamp),
(72, 'Statistiqueministerielle', '{"conditions_droits_et_devoirs":{"Situationdossierrsa.etatdosrsa":["2","3","4"],"Calculdroitrsa.toppersdrodevorsa":"1"},"useHistoriquedroit":false,"conditions_types_parcours":{"professionnel":{"Typeorient.parentid":[1]},"socioprofessionnel":{"Typeorient.parentid":[4]},"social":{"Typeorient.parentid":[6]}},"conditions_indicateurs_organismes":{"Pôle emploi (PE) (2)":{"Typeorient.id":2,"Structurereferente.id":23},"dont accompagnement de droit commun":[],"dont accompagnement global":[],"Organisme public de placement professionnel autre que PE (maison de l''emploi, PLIE, mission locale,...) (2)":[],"Entreprise de travail temporaire, agence privée de placement (2)":[],"Organisme d''appui à la création et au développement d''entreprise (2)":[],"Structure d''Insertion par l''activité économique (IAE) (2)":[],"Autres organismes de placement professionnel et autres organismes appartenant ou participant au SPE (2) (3)":[],"Service du Conseil Départemental\/Territorial ou de l''Agence Départementale d''Insertion (ADI) (2) (4)":{"Typeorient.parentid":[4,6],"NOT":{"Typeorient.id":[3,8]}},"dont orientation professionnelle ou socioprofessionnelle":{"Typeorient.parentid":[4],"NOT":{"Typeorient.id":[3,8]}},"dont orientation sociale":{"Typeorient.parentid":[6],"NOT":{"Typeorient.id":[3,8]}},"Caf\/Établissement des allocations familiales (2) (5)":[],"Msa (2) (5)":{"Typeorient.id":[3,8]},"Caisse de Prévoyance Sociale (CPS) (2) (5)":[],"CCAS\/CIAS (2) (5)":[],"Association d''insertion hors SPE (2) (3)":[],"Autres organismes hors SPE (2) (3)":[]},"conditions_types_cers":{"ppae":{"Contratinsertion.structurereferente_id":[23]},"cer_pro":{"NOT":{"Contratinsertion.structurereferente_id":[23]}},"cer_pro_social":[]},"conditions_organismes":{"SPE":{"Structurereferente.id":[23,21,39]},"SPE_PoleEmploi":{"Structurereferente.id":[23]},"HorsSPE":{"NOT":{"Structurereferente.id":[23,21,39]}}},"conditions_indicateurs_motifs_reorientation":[{"orientation_initiale_inadaptee":null,"changement_situation_allocataire":null}],"structure_cer_orientation":null}', 'Paramétrage du module "Statistiques ministérielles"

	  @param array


		array(

			  Conditions permettant de définit les allocataires dans le champ
			  des droits et devoirs.

			  Ces conditions seront utilisées dans les différents tableaux.

			  Modèles disponibles: Dossier, Detaildroitrsa, Foyer, Situationdossierrsa,
			  Adressefoyer, Personne, Adresse, Prestation, Calculdroitrsa.

			''conditions_droits_et_devoirs'' => array(
				''Situationdossierrsa.etatdosrsa'' => array( ''2'', ''3'', ''4'' ),
				''Calculdroitrsa.toppersdrodevorsa'' => ''1''
			),

			  Permet d''indiquer que l''on souhaite utiliser la table
			  historiquesdroits pour déterminer si un allocataire est soumis
			  à droits et devoirs (données historisées) plutôt que d''utiliser
			  une copie de la base de données au 3112YYYY pour savoir si
			  l''allocataire était soumis à droits et devoirs au 3112

			  @var boolean true pour utiliser la table historiquesdroits
			  @default null

			''useHistoriquedroit'' => false,

			  Catégories et conditions des différents types de parcours du CG.
			  Les catégories sont: professionnel, socioprofessionnel et social.

			  Utilisé dans le tableau "1 - Orientation des personnes ... au sens du type de parcours..."

			  Modèles disponibles (en plus de ceux disponibles de base, @see
			  conditions_droits_et_devoirs): DspRev, Dsp, Orientstruct, Typeorient.

			''conditions_types_parcours'' => array(
				''professionnel'' => array(
					''Typeorient.parentid'' => array( 1 )
				),
				''socioprofessionnel'' => array(
					''Typeorient.parentid'' => array( 4 )
				),
				''social'' => array(
					''Typeorient.parentid'' => array( 6 )
				),
			),

			  Catégories (intitulés) et conditions des différents types de référents
			  uniques (structures référentes) pour la tableau "2 - Organismes de
			  prise en charge des personnes ... dont le référent unique a été désigné"

			  Modèles disponibles (en plus de ceux disponibles de base, @see
			  conditions_droits_et_devoirs): DspRev, Dsp, Orientstruct, Typeorient,
			  Structurereferente.

			''conditions_indicateurs_organismes'' => array(
				''Pôle emploi (PE) (2)'' => array(
					''Typeorient.id'' => 2,
					''Structurereferente.id'' => 23
				),
				''dont accompagnement de droit commun'' => array(),
				''dont accompagnement global'' => array(),
				''Organisme public de placement professionnel autre que PE (maison de l''emploi, PLIE, mission locale,...) (2)'' => array(),
				''Entreprise de travail temporaire, agence privée de placement (2)'' => array(),
				''Organisme d''appui à la création et au développement d''entreprise (2)'' => array(),
				''Structure d''Insertion par l''activité économique (IAE) (2)'' => array(),
				''Autres organismes de placement professionnel et autres organismes appartenant ou participant au SPE (2) (3)'' => array(),
				''Service du Conseil DépartementalTerritorial ou de l''Agence Départementale d''Insertion (ADI) (2) (4)'' => array(
					''Typeorient.parentid'' => array( 4, 6 ),
					''NOT'' => array( ''Typeorient.id'' => array( 3, 8 ) )
				),
				''dont orientation professionnelle ou socioprofessionnelle'' => array(
					''Typeorient.parentid'' => array( 4 ),
					''NOT'' => array( ''Typeorient.id'' => array( 3, 8 ) )
				),
				''dont orientation sociale'' => array(
					''Typeorient.parentid'' => array( 6 ),
					''NOT'' => array( ''Typeorient.id'' => array( 3, 8 ) )
				),
				''CafÉtablissement des allocations familiales (2) (5)'' => array(),
				''Msa (2) (5)'' => array(
					''Typeorient.id'' => array( 3, 8 )
				),
				''Caisse de Prévoyance Sociale (CPS) (2) (5)'' => array(),
				''CCASCIAS (2) (5)'' => array(),
				''Association d''insertion hors SPE (2) (3)'' => array(),
				''Autres organismes hors SPE (2) (3)'' => array(),
			),

			  Catégories et délais permettant de différencier les types de contrats.

			  Lorsqu''un contrat est signé avec Pôle Emploi, il s''agit à priori
			  d''un PPAE, alors qu''un CER pro n''est pas signé avec Pôle Emploi.

			  Voir aussi Statistiqueministerielle.conditions_types_parcours (les conditions sont ajoutées automatiquement):
			 	- un CER pro est signé lors d''un type de parcours professionnel
			 	- un CER social ou professionnel est signé lors d''un type de parcours social ou sociprofessionnel

			  Modèles disponibles (en plus de ceux disponibles de base, @see
			  conditions_droits_et_devoirs): Orientstruct, Typeorient,
			  Contratinsertion, Structurereferentecer, Typeorientcer.

			''conditions_types_cers'' => array(
				''ppae'' => array(
					''Contratinsertion.structurereferente_id'' => array( 23 )
				),
				''cer_pro'' => array(
					''NOT'' => array(
						''Contratinsertion.structurereferente_id'' => array( 23 )
					)
				),
				''cer_pro_social'' => array(),
			),

			  Catégories et conditions permettant de différencier les organismes
			  SPE et les organismes Hors SPE.

			  Les catégories sont: SPE et HorsSPE.

			  Modèles disponibles (en plus de ceux disponibles de base, @see
			  conditions_droits_et_devoirs): DspRev, Dsp, Orientstruct, Typeorient,
			  Structurereferente, Orientstructpcd, Typeorientpcd,
			  Structurereferentepcd.

			  Utilisé dans les tableaux:
			 	- "4 - Nombre et profil des personnes réorientées..."
			 	- "4a - Motifs des réorientations..."
			 	- "4b - Recours à l''article L262-31"

			''conditions_organismes'' => array(
				''SPE'' => array(
					''Structurereferente.id'' => array(
						 Pôle Emploi
						23,
						 EMPLOI - MSA
						21,
						 	Mission Nouveaux Emplois (MNE)
						39
					)
				),
				''SPE_PoleEmploi'' => array(
					''Structurereferente.id'' => array(
						 Pôle Emploi
						23
					)
				),
				''HorsSPE'' => array(
					 Qui ne sont pas...
					''NOT'' => array(
						''Structurereferente.id'' => array(
							 Pôle Emploi
							23,
							 EMPLOI - MSA
							21,
							 	Mission Nouveaux Emplois (MNE)
							39
						)
					)
				)
			),

			  Catégories et conditions permettant de différencier les motifs de
			  réorientations. Une valeur NULL signifie que la donnée sera non
			  disponible (ND).

			  Modèles disponibles (en plus de ceux disponibles de base, @see
			  conditions_droits_et_devoirs): DspRev, Dsp, Orientstruct, Typeorient,
			  Structurereferente, Orientstructpcd, Typeorientpcd,
			  Structurereferentepcd.

			  Utilisé dans le tableau "4a - Motifs des réorientations...".

			''conditions_indicateurs_motifs_reorientation'' => array(
				array(
					''orientation_initiale_inadaptee'' => null,
					''changement_situation_allocataire'' => null
				)
			),

			  Permet d''indiquer, pour les tableaux "Indicateurs de caractéristiques
			  des contrats" et "Indicateurs de natures des actions des contrats"
			  que la structure référente de l''orientation (du référent unique)
			  et du CER doivent etre identiques.

			  @var boolean true pour s''assurer que les structures sont les mêmes
			  @default null

			''structure_cer_orientation'' => null,
		)', 6, current_timestamp, current_timestamp),
(248, 'ConfigurableQuery.Rendezvous.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":["Rendezvous.daterdv"]},"limit":10,"auto":false,"results":{"header":[],"fields":["Personne.nom_complet_court","Adresse.nomcom","Structurereferente.lib_struc","Referent.nom_complet","Typerdv.libelle","Rendezvous.daterdv","Rendezvous.heurerdv","Statutrdv.libelle","Canton.canton","\/Rendezvous\/index\/#Rendezvous.personne_id#","\/Rendezvous\/impression\/#Rendezvous.id#"],"innerTable":["Personne.dtnai","Adresse.numcom","Personne.nir","Prestation.rolepers","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"256M"}}', 'Menu "Recherches" > "Par rendez-vous"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					),
					''Rendezvous'' => array(
						 Case à cocher "Filtrer par date de RDV"
						''daterdv'' => ''0'',
						 Du (inclus)
						''daterdv_from'' => ''TAB::-1WEEK'',
						 Au (inclus)
						''daterdv_to'' => ''TAB::NOW'',
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array( ''Rendezvous.daterdv'' )
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Structurereferente.lib_struc'',
					''Permanence.libpermanence'',
					''Referent.nom_complet'',
					''Typerdv.libelle'',
					''Rendezvous.daterdv'',
					''Rendezvous.heurerdv'',
					''Statutrdv.libelle'',
					 FIXME: caché dans le title, attention au thead
					''Dossier.numdemrsa'' => array(
						''condition'' => false
					),
					''Canton.canton'',
					''Rendezvousindex#Rendezvous.personne_id#'',
					''Rendezvousimpression#Rendezvous.id#''
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Personne.dtnai'',
					''Adresse.numcom'',
					''Personne.nir'',
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''256M''
			)
		)', 7, current_timestamp, current_timestamp),
(249, 'ConfigurableQuery.Rendezvous.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":["Rendezvous.daterdv"]},"results":{"fields":["Personne.qual","Personne.nom","Personne.prenom","Personne.email","Personne.numport","Personne.numfixe","Dossier.matricule","Adresse.numvoie","Adresse.libtypevoie","Adresse.nomvoie","Adresse.complideadr","Adresse.compladr","Adresse.codepos","Adresse.nomcom","Canton.canton","Structurereferente.lib_struc","Structurereferente.num_voie","Structurereferente.type_voie","Structurereferente.nom_voie","Structurereferente.code_postal","Structurereferente.ville","Referent.qual","Referent.nom","Referent.prenom","Rendezvous.typerdv_id","Rendezvous.statutrdv_id","Rendezvous.daterdv","Rendezvous.heurerdv","Rendezvous.objetrdv","Rendezvous.commentairerdv","Situationdossierrsa.etatdosrsa","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"256M"}}', 'Export CSV,  menu "Recherches" > "Par rendez-vous"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Rendezvous.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Rendezvous.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Personne.qual'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.email'',
					''Personne.numport'',
					''Personne.numfixe'',
					''Dossier.matricule'' ,
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Canton.canton'',
					''Structurereferente.lib_struc'',
					''Structurereferente.num_voie'',
					''Structurereferente.type_voie'',
					''Structurereferente.nom_voie'',
					''Structurereferente.code_postal'',
					''Structurereferente.ville'',
					''Referent.qual'',
					''Referent.nom'',
					''Referent.prenom'',
					''Rendezvous.typerdv_id'',
					''Rendezvous.statutrdv_id'',
					''Rendezvous.daterdv'',
					''Rendezvous.heurerdv'',
					''Rendezvous.objetrdv'',
					''Rendezvous.commentairerdv'',
					''Situationdossierrsa.etatdosrsa'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Rendezvous.search.ini_set'' ),
		)', 7, current_timestamp, current_timestamp),
(250, 'relances.variables', '{"#DateRV#":"Date du rendez-vous (JJ\/MM\/AA)","#HRV#":"Heure du rendez-vous (HH:MM)","#LieuRV#":"Lieu du rendez-vous"}', 'Relance : variables possibles

        array (
                ''#DateRV#'' => ''Date du rendez-vous (JJMMAA)'',
                ''#HRV#'' => ''Heure du rendez-vous (HH:MM)'',
                ''#LieuRV#'' => ''Lieu du rendez-vous'',
        )', 8, current_timestamp, current_timestamp),
(251, 'relances.prestataire.mail.destinataire', '', 'Adresse électronique du destinataire du mail contenant les SMS à envoyer.', 8, current_timestamp, current_timestamp),
(252, 'relances.prestataire.mail.sujet.sujet', '"Relance par SMS"', 'Objet du message du mail contenant les les SMS à envoyer.
  ''Relance par SMS''', 8, current_timestamp, current_timestamp),
(253, 'relances.prestataire.mail.message', '"Liste des allocataires à relancer."', 'Contenu du message du mail contenant les les SMS à envoyer.
  ''Liste des allocataires à relancer.''', 8, current_timestamp, current_timestamp),
(254, 'relances.prestataire.mail.domaine', '"CD 66 - Webrsa"', 'Domaine du message du mail contenant les les SMS à envoyer.
  ''CD 66 - Webrsa''', 8, current_timestamp, current_timestamp),
(256, 'relances.prestataire.mail.envoi.prestataire', 'true', 'Exécute ou non l''envoi du mail contenant les les SMS à envoyer.
  true', 8, current_timestamp, current_timestamp),
(257, 'relances.prestataire.mail.envoi.allocataire', 'false', 'Exécute ou non l''envoi des mails de relance aux allocataires.
  false', 8, current_timestamp, current_timestamp),
(258, 'relances.prestataire.mail.sujet.allocataire', '"Rappel - Convocation"', 'Objet du message des mails de relance aux allocataires.
  ''Rappel - Convocation''', 8, current_timestamp, current_timestamp),
(259, 'relances.prestataire.mail.phase.test', 'true', 'Définit ou non un contexte de débogage où les adresses mail des allocataires
  sont remplacées par celle du destinataire du mail contenant les SMS à envoyer.
  true', 8, current_timestamp, current_timestamp),
(260, 'ConfigurableQuery.Recoursgracieux.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":[],"skip":[],"has":{"0":"Cui","Orientstruct":{"Orientstruct.statut_orient":"Orienté"},"Contratinsertion":{"Contratinsertion.decision_ci":"V"},"1":"Personnepcg66"}},"query":{"restrict":{"Prestation.rolepers":"DEM"},"conditions":[],"order":["Personne.nom"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Dossier.dtdemrsa","2":"Personne.nom_complet_court","3":"Recourgracieux.etat","4":"Recourgracieux.dtarrivee","5":"Recourgracieux.dtbutoir","6":"Recourgracieux.dtreception","7":"Recourgracieux.dtaffectation","8":"Recourgracieux.dtdecision","Recourgracieux.foyer_id":{"hidden":true},"\/Recoursgracieux\/index\/#Recourgracieux.foyer_id#":{"class":"view"}},"innerTable":{"0":"Dossier.matricule","1":"Personne.dtnai","Adresse.numcom":{"options":[]},"2":"Prestation.rolepers","3":"Structurereferenteparcours.lib_struc","4":"Referentparcours.nom_complet"}},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Menu "Recherches" > "Par indus transférés"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
						 Case à cocher "Filtrer par date de demande RSA"
						''dtdemrsa'' => ''0'',
						 Du (inclus)
						''dtdemrsa_from'' => ''TAB::-1WEEK'',
						 Au (inclus)
						''dtdemrsa_to'' => ''TAB::NOW'',
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array(
					''Cui'',
					''Orientstruct'' => array(
						''Orientstruct.statut_orient'' => ''Orienté'',
						 Orientstruct possède des conditions supplémentaire dans le modèle WebrsaRechercheDossier pour le CD66
					),
					''Contratinsertion'' => array(
						''Contratinsertion.decision_ci'' => ''V''
					),
					''Personnepcg66''
				)
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Prestation.rolepers'' => ''DEM'',  Demandeur du RSA
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array( ''Personne.nom'' )
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nom_complet_court'',
					''Recourgracieux.etat'',
					''Recourgracieux.dtarrivee'',
					''Recourgracieux.dtbutoir'',
					''Recourgracieux.dtreception'',
					''Recourgracieux.dtaffectation'',
					''Recourgracieux.dtdecision'',
					''Recourgracieux.foyer_id'' => array (''hidden'' => true),
					''Recoursgracieuxindex#Recourgracieux.foyer_id#'' => array( ''class'' => ''view'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Dossier.matricule'',
					''Personne.dtnai'',
					''Adresse.numcom'' => array(
						''options'' => array()
					),
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''512M''
			)
		)', 9, current_timestamp, current_timestamp),
(261, 'ConfigurableQuery.Recoursgracieux.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":[],"skip":[],"has":{"0":"Cui","Orientstruct":{"Orientstruct.statut_orient":"Orienté"},"Contratinsertion":{"Contratinsertion.decision_ci":"V"},"1":"Personnepcg66"}},"query":{"restrict":{"Prestation.rolepers":"DEM"},"conditions":[],"order":["Personne.nom"]},"results":{"fields":["Dossier.numdemrsa","Dossier.dtdemrsa","Personne.nir","Situationdossierrsa.etatdosrsa","Personne.nom_complet_prenoms","Personne.dtnai","Adresse.numvoie","Adresse.libtypevoie","Adresse.nomvoie","Adresse.complideadr","Adresse.compladr","Adresse.codepos","Adresse.nomcom","Typeorient.lib_type_orient","Personne.idassedic","Dossier.matricule","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Personne.sexe","Dsp.natlog","Canton.canton","Recourgracieux.etat","Recourgracieux.dtarrivee","Recourgracieux.dtbutoir","Recourgracieux.dtreception","Recourgracieux.dtaffectation","Recourgracieux.dtdecision","Recourgracieux.mention"]},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Export CSV, menu "Recherches" > "indus transférés"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Recoursgracieux.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Recoursgracieux.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Personne.nom_complet_prenoms'',  FIXME: nom completcourtprenoms ?
					''Personne.dtnai'',
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Typeorient.lib_type_orient'',
					''Personne.idassedic'',
					''Dossier.matricule'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Personne.sexe'',
					''Dsp.natlog'',
					''Canton.canton'',
					''Recourgracieux.etat'',
					''Recourgracieux.dtarrivee'',
					''Recourgracieux.dtbutoir'',
					''Recourgracieux.dtreception'',
					''Recourgracieux.dtaffectation'',
					''Recourgracieux.dtdecision'',
					''Recourgracieux.mention'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Recoursgracieux.search.ini_set'' ),
		)', 9, current_timestamp, current_timestamp),
(272, 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Dossier.numdemrsa","Dossier.matricule","Dossier.dernier","Detailcalculdroitrsa.natpf","Dossier.anciennete_dispositif","Dossier.fonorg","Adresse.nomvoie","Adresse.nomcom","Adresse.numcom","Canton.canton","Personne.nom","Personne.nomnai","Personne.prenom","Prestation.rolepers","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Personne.nom_complet_prenoms":{"sort":false},"Adresse.complete":{"sort":false},"Canton.canton":{"sort":false},"Modecontact.numtel":{"class":"numtelCAF","sort":false},"Personne.numport":{"class":"numtelCD","sort":false},"Rendezvous.daterdv":{"sort":false},"Rendezvous.heurerdv":{"sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Personnes\/coordonnees\/#Personne.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_NOUVEAUX","Statutrdv.code_statut":"PREVU"},"save":{"Typerdv.code_type":"INFO_COLL_NOUVEAUX","Statutrdv.code_statut":""}}},"ini_set":[]}', 'Cohorte Information Collectives - Venu  Non venu - Nouveaux entrant


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					),
					''Rendezvous'' => array(
						 Case à cocher "Filtrer par date de RDV"
						''daterdv'' => ''0'',
						 Du (inclus)
						''daterdv_from'' => ''TAB::-1WEEK'',
						 Au (inclus)
						''daterdv_to'' => ''TAB::NOW'',
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.filters.skip''),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Personne.id'' => array (''hidden'' => true),
					''Dossier.numdemrsa'' => array ( ''sort'' => false ),
					''Dossier.matricule'' => array ( ''sort'' => false ),
					''Dossier.dtdemrsa'' => array ( ''sort'' => false ),
					''Personne.nom_complet_prenoms'' => array ( ''sort'' => false ),
					''Adresse.complete'' => array ( ''sort'' => false ),
					''Canton.canton'' => array ( ''sort'' => false ),
					''Modecontact.numtel'' => array(''class'' => ''numtelCAF'', ''sort'' => false ),
					''Personne.numport'' => array(''class'' => ''numtelCD'', ''sort'' => false ),
					''Rendezvous.daterdv'' => array ( ''sort'' => false ),
					''Rendezvous.heurerdv'' => array ( ''sort'' => false ),
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''view external'' ),
					''Personnescoordonnees#Personne.id#'' => array( ''class'' => ''view external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
				)
			),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
				),
				 Configuration des paramètres
				''config'' => array(
					 Valeurs utilisées pour la recherche de la cohorte
					''recherche'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_NOUVEAUX'',
						''Statutrdv.code_statut'' => ''PREVU''
					),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_NOUVEAUX'',
						''Statutrdv.code_statut'' => ''''
					),
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 10, current_timestamp, current_timestamp),
(262, 'QueryImpression.Recoursgracieux', '{"fields":["Dossier.numdemrsa","Dossier.dtdemrsa","Personne.nir","Situationdossierrsa.etatdosrsa","Personne.nom_complet_prenoms","Personne.dtnai","Adresse.numvoie","Adresse.libtypevoie","Adresse.nomvoie","Adresse.complideadr","Adresse.compladr","Adresse.codepos","Adresse.nomcom","Typeorient.lib_type_orient","Personne.idassedic","Dossier.matricule","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Personne.sexe","Dsp.natlog","Canton.canton","Recourgracieux.etat","Recourgracieux.dtarrivee","Recourgracieux.dtbutoir","Recourgracieux.dtreception","Recourgracieux.dtaffectation","Recourgracieux.dtdecision","Recourgracieux.mention"],"recursive":1,"conditions":[],"joins":[]}', 'Champs qui seront passés à la fonction de transformation des ODT dans les emails des Recoursgracieux


		array(
			''fields'' => array(
				''Dossier.numdemrsa'',
				''Dossier.dtdemrsa'',
				''Personne.nir'',
				''Situationdossierrsa.etatdosrsa'',
				''Personne.nom_complet_prenoms'',  FIXME: nom completcourtprenoms ?
				''Personne.dtnai'',
				''Adresse.numvoie'',
				''Adresse.libtypevoie'',
				''Adresse.nomvoie'',
				''Adresse.complideadr'',
				''Adresse.compladr'',
				''Adresse.codepos'',
				''Adresse.nomcom'',
				''Typeorient.lib_type_orient'',
				''Personne.idassedic'',
				''Dossier.matricule'',
				''Structurereferenteparcours.lib_struc'',
				''Referentparcours.nom_complet'',
				''Personne.sexe'',
				''Dsp.natlog'',
				''Canton.canton'',
				''Recourgracieux.etat'',
				''Recourgracieux.dtarrivee'',
				''Recourgracieux.dtbutoir'',
				''Recourgracieux.dtreception'',
				''Recourgracieux.dtaffectation'',
				''Recourgracieux.dtdecision'',
				''Recourgracieux.mention'',
			),
			''recursive'' => 1,
			''conditions'' => array(),
			''joins'' => array()
		)', 9, current_timestamp, current_timestamp),
(263, 'CompleteDataImpression.Recoursgracieux.modeles', '[]', 'Modèles necessaire à transformation des ODT dans les emails des Recoursgracieux


		array()', 9, current_timestamp, current_timestamp),
(264, 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Historiquedroit":{"created":"1","created_from":"TAB::-3MONTHS","created_to":"TAB::-1MONTH"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Personne.nom_complet_prenoms":{"sort":false},"Adresse.complete":{"sort":false},"Canton.canton":{"sort":false},"Dossier.ddarrmut":{"sort":false},"Modecontact.numtel":{"class":"numtelCAF","sort":false},"Personne.numport":{"class":"numtelCD","sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Personnes\/coordonnees\/#Personne.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":[],"save":{"Typerdv.code_type":"INFO_COLL_NOUVEAUX","Statutrdv.code_statut":"PREVU"}}},"ini_set":[]}', 'Cohorte Information Collectives - Nouveaux entrants


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					),
					Dates par défaut de la recherche
					''Historiquedroit'' => array(
						''created'' => ''1'',
						''created_from'' => ''TAB::-3MONTHS'',
						 Au (inclus)
						''created_to'' => ''TAB::-1MONTH'',
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(
					''Calculdroitrsa.toppersdrodevorsa'',
					''Dossier.dtdemrsa'',
					''Detaildroitrsa.oridemrsa'',
					''Foyer.sitfam'',
					''Adresse.nomvoie'',
					''Personne.dtnai'',
					''Personne.dtnai_month'',
					''Personne.dtnai_year'',
					''Personne.nir'',
					''Personne.sexe'',
					''Personne.trancheage'',
					''Situationdossierrsa.etatdosrsa'',
					''Serviceinstructeur.id'',
					''Suiviinstruction.typeserins'',
					''PersonneReferent.structurereferente_id'',
					''PersonneReferent.referent_id'',
					''Prestation.rolepers'',
					 Filtre par tag
					''ByTag.tag_choice''
				),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Personne.id'' => array (''hidden'' => true),
					''Dossier.numdemrsa'' => array ( ''sort'' => false ),
					''Dossier.matricule'' => array ( ''sort'' => false ),
					''Dossier.dtdemrsa'' => array ( ''sort'' => false ),
					''Personne.nom_complet_prenoms'' => array ( ''sort'' => false ),
					''Adresse.complete'' => array ( ''sort'' => false ),
					''Canton.canton'' => array ( ''sort'' => false ),
					''Dossier.ddarrmut'' => array ( ''sort'' => false ),
					''Modecontact.numtel'' => array(''class'' => ''numtelCAF'',''sort'' => false ),
					''Personne.numport'' => array(''class'' => ''numtelCD'', ''sort'' => false ),
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''view external'' ),
					''Personnescoordonnees#Personne.id#'' => array( ''class'' => ''view external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
				)
			),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
				),
				 Configuration des paramètres
				''config'' => array(
					 Valeurs utilisées pour la recherche de la cohorte
					''recherche'' => array(),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_NOUVEAUX'',
						''Statutrdv.code_statut'' => ''PREVU''
					)
				),
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 10, current_timestamp, current_timestamp),
(290, 'ConfigurableQuery.Planpauvreteorientations.cohorte_isemploi_stock', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Historiquedroit":{"created":"1","created_from":"TAB::-3MONTHS","created_to":"TAB::-1MONTH"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Historiqueetatpe.identifiantpe":{"sort":false},"Historiqueetatpe.date":{"sort":false},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Personne.nom_complet":{"sort":false},"Adresse.complete":{"sort":false},"Canton.canton":{"sort":false},"Dossier.ddarrmut":{"sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":[]},"ini_set":[]}', 'Cohorte


		Configure::read(''ConfigurableQuery.Planpauvreteorientations.cohorte_isemploi'')', 12, current_timestamp, current_timestamp),
(265, 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Historiquedroit":{"created":"1","created_from":"TAB::-3MONTHS","created_to":"TAB::-1MONTH"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Personne.nom":{"sort":false},"Personne.prenom":{"sort":false},"Personne.dtnai":{"sort":false},"Personne.nir":{"sort":false},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Dossier.ddarrmut":{"sort":false},"Historiqueetatpe.identifiantpe":{"sort":false},"Historiqueetatpe.date":{"sort":false},"Adresse.nomcom":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
					''fields'' => array(
						''Personne.id'' => array (''hidden'' => true),
						''Personne.nom'' => array ( ''sort'' => false ),
						''Personne.prenom'' => array ( ''sort'' => false ),
						''Personne.dtnai'' => array ( ''sort'' => false ),
						''Personne.nir'' => array ( ''sort'' => false ),
						''Dossier.numdemrsa'' => array ( ''sort'' => false ),
						''Dossier.matricule'' => array ( ''sort'' => false ),
						''Dossier.dtdemrsa'' => array ( ''sort'' => false ),
						''Dossier.ddarrmut'' => array ( ''sort'' => false ),
						''Historiqueetatpe.identifiantpe'' => array ( ''sort'' => false ),
						''Historiqueetatpe.date'' => array ( ''sort'' => false ),
						''Adresse.nomcom'' => array ( ''sort'' => false ),
						''Adresse.codepos'' => array ( ''sort'' => false ),
						''Adresse.numcom'' => array ( ''sort'' => false ),
						''Canton.canton'' => array ( ''sort'' => false ),
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol.ini_set'' ),
		)', 10, current_timestamp, current_timestamp),
(266, 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_stock', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Historiquedroit":{"created":"1","created_from":"TAB::-3MONTHS","created_to":"TAB::-1MONTH"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Personne.nom_complet_prenoms":{"sort":false},"Adresse.complete":{"sort":false},"Canton.canton":{"sort":false},"Dossier.ddarrmut":{"sort":false},"Modecontact.numtel":{"class":"numtelCAF","sort":false},"Personne.numport":{"class":"numtelCD","sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Personnes\/coordonnees\/#Personne.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":[],"save":{"Typerdv.code_type":"INFO_COLL_STOCK","Statutrdv.code_statut":"PREVU"}}},"ini_set":[]}', 'Cohorte Information Collectives - Stock


		array(
			 1. Filtres de recherche
			''filters'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol.filters''),
			 2. Recherche
			''query'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol.query''),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol.results''),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(),
				 Configuration des paramètres
				''config'' => array(
					 Valeurs utilisées pour la recherche de la cohorte
					''recherche'' => array(),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_STOCK'',
						''Statutrdv.code_statut'' => ''PREVU''
					)
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 10, current_timestamp, current_timestamp),
(267, 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_stock', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Historiquedroit":{"created":"1","created_from":"TAB::-3MONTHS","created_to":"TAB::-1MONTH"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Personne.nom":{"sort":false},"Personne.prenom":{"sort":false},"Personne.dtnai":{"sort":false},"Personne.nir":{"sort":false},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Dossier.ddarrmut":{"sort":false},"Historiqueetatpe.identifiantpe":{"sort":false},"Historiqueetatpe.date":{"sort":false},"Adresse.nomcom":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}', 'Export CSV


		Configure::read(''ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol'')', 10, current_timestamp, current_timestamp),
(268, 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Dossier.numdemrsa","Dossier.matricule","Dossier.dernier","Detailcalculdroitrsa.natpf","Dossier.anciennete_dispositif","Dossier.fonorg","Adresse.nomvoie","Adresse.nomcom","Adresse.numcom","Canton.canton","Personne.nom","Personne.nomnai","Personne.prenom","Prestation.rolepers","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Structurereferente.lib_struc":{"sort":false},"Referent.nom_complet":{"sort":false},"Typerdv.libelle":{"sort":false},"Rendezvous.daterdv":{"sort":false},"Rendezvous.heurerdv":{"sort":false},"Statutrdv.libelle":{"sort":false},"Canton.canton":{"sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Rendezvous\/impression\/#Rendezvous.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_NOUVEAUX","Statutrdv.code_statut":"PREVU"},"save":[]}},"ini_set":[]}', 'Cohorte Information Collectives - Impression convocations - Nouveaux entrants


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					),
					''Rendezvous'' => array(
						 Case à cocher "Filtrer par date de RDV"
						''daterdv'' => ''0'',
						 Du (inclus)
						''daterdv_from'' => ''TAB::-1WEEK'',
						 Au (inclus)
						''daterdv_to'' => ''TAB::NOW'',
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(
					 Bloc de recherche par dossier
					''Calculdroitrsa.toppersdrodevorsa'',
					''Dossier.dtdemrsa'',
					''Detaildroitrsa.oridemrsa'',
					''Foyer.sitfam'',
					''Situationdossierrsa.etatdosrsa'',
					''Serviceinstructeur.id'',
					''Suiviinstruction.typeserins'',
					''PersonneReferent.structurereferente_id'',
					''PersonneReferent.referent_id'',
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Dossier.dernier'',
					''Detailcalculdroitrsa.natpf'',
					''Dossier.anciennete_dispositif'',
					''Dossier.fonorg'',
					 Bloc de recherche par adresse
					''Adresse.nomvoie'',
					''Adresse.nomcom'',
					''Adresse.numcom'',
					''Canton.canton'',
					 Bloc de recherche par allocataire
					''Personne.nom'',
					''Personne.nomnai'',
					''Personne.prenom'',
					''Prestation.rolepers'',
					''Personne.dtnai'',
					''Personne.dtnai_month'',
					''Personne.dtnai_year'',
					''Personne.nir'',
					''Personne.sexe'',
					''Personne.trancheage'',
					 Filtre par tag
					''ByTag.tag_choice''
				),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Personne.id'' => array (''hidden'' => true),
					''Personne.nom_complet_court'' => array ( ''sort'' => false ),
					''Adresse.nomcom'' => array ( ''sort'' => false ),
					''Structurereferente.lib_struc'' => array ( ''sort'' => false ),
					''Referent.nom_complet'' => array ( ''sort'' => false ),
					''Typerdv.libelle'' => array(''sort'' => false),
					''Rendezvous.daterdv'' => array ( ''sort'' => false ),
					''Rendezvous.heurerdv'' => array ( ''sort'' => false ),
					''Statutrdv.libelle'' => array(''sort'' => false),
					''Canton.canton'' => array(''sort'' => false),
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''view external'' ),
					''Rendezvousimpression#Rendezvous.id#'' => array( ''class'' => ''view external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
				)
			),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
				),
				 Configuration des paramètres
				''config'' => array(
					 Valeurs utilisées pour la recherche de la cohorte
					''recherche'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_NOUVEAUX'',
						''Statutrdv.code_statut'' => ''PREVU''
					),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(),
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 10, current_timestamp, current_timestamp),
(269, 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_imprime', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Dossier.numdemrsa","Dossier.matricule","Dossier.dernier","Detailcalculdroitrsa.natpf","Dossier.anciennete_dispositif","Dossier.fonorg","Adresse.nomvoie","Adresse.nomcom","Adresse.numcom","Canton.canton","Personne.nom","Personne.nomnai","Personne.prenom","Prestation.rolepers","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
					''fields'' => array(
						''Personne.id'' => array (''hidden'' => true),
						''Dossier.numdemrsa'' => array ( ''sort'' => false ),
						''Personne.nom_complet_court'' => array ( ''sort'' => false ),
						''Adresse.nomcom'' => array ( ''sort'' => false ),
						''Personne.dtnai'' => array ( ''sort'' => false ),
						''Dossier.matricule'' => array ( ''sort'' => false ),
						''Personne.nir'' => array ( ''sort'' => false ),
						''Adresse.codepos'' => array ( ''sort'' => false ),
						''Adresse.numcom'' => array ( ''sort'' => false ),
						''Canton.canton'' => array ( ''sort'' => false ),
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.ini_set'' ),
		)', 10, current_timestamp, current_timestamp),
(270, 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime_stock', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Dossier.numdemrsa","Dossier.matricule","Dossier.dernier","Detailcalculdroitrsa.natpf","Dossier.anciennete_dispositif","Dossier.fonorg","Adresse.nomvoie","Adresse.nomcom","Adresse.numcom","Canton.canton","Personne.nom","Personne.nomnai","Personne.prenom","Prestation.rolepers","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Structurereferente.lib_struc":{"sort":false},"Referent.nom_complet":{"sort":false},"Typerdv.libelle":{"sort":false},"Rendezvous.daterdv":{"sort":false},"Rendezvous.heurerdv":{"sort":false},"Statutrdv.libelle":{"sort":false},"Canton.canton":{"sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Rendezvous\/impression\/#Rendezvous.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_STOCK","Statutrdv.code_statut":"PREVU"},"save":[]}},"ini_set":[]}', 'Cohorte Information Collectives - Impression convocations - Stock


		array(
			 1. Filtres de recherche
			''filters'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.filters''),
			 2. Recherche
			''query'' => Configure::read (''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.query''),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.results''),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
				),
				 Configuration des paramètres
				''config'' => array(
					 Valeurs utilisées pour la recherche de la cohorte
					''recherche'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_STOCK'',
						''Statutrdv.code_statut'' => ''PREVU''
					),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(),
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 10, current_timestamp, current_timestamp),
(271, 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_imprime_stock', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Dossier.numdemrsa","Dossier.matricule","Dossier.dernier","Detailcalculdroitrsa.natpf","Dossier.anciennete_dispositif","Dossier.fonorg","Adresse.nomvoie","Adresse.nomcom","Adresse.numcom","Canton.canton","Personne.nom","Personne.nomnai","Personne.prenom","Prestation.rolepers","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}', 'Export CSV


		Configure::read(''ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_imprime'')', 10, current_timestamp, current_timestamp),
(288, 'ConfigurableQuery.Planpauvreteorientations.cohorte_isemploi', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Historiquedroit":{"created":"1","created_from":"TAB::-3MONTHS","created_to":"TAB::-1MONTH"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Historiqueetatpe.identifiantpe":{"sort":false},"Historiqueetatpe.date":{"sort":false},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Personne.nom_complet":{"sort":false},"Adresse.complete":{"sort":false},"Canton.canton":{"sort":false},"Dossier.ddarrmut":{"sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":[]},"ini_set":[]}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					),
					Dates par défaut de la recherche
					''Historiquedroit'' => array(
						''created'' => ''1'',
						''created_from'' => ''TAB::-3MONTHS'',
						 Au (inclus)
						''created_to'' => ''TAB::-1MONTH'',
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(
					''Calculdroitrsa.toppersdrodevorsa'',
					''Dossier.dtdemrsa'',
					''Detaildroitrsa.oridemrsa'',
					''Foyer.sitfam'',
					''Personne.trancheage'',
					''Situationdossierrsa.etatdosrsa'',
					''Serviceinstructeur.id'',
					''Suiviinstruction.typeserins'',
					''PersonneReferent.structurereferente_id'',
					''PersonneReferent.referent_id'',
					''Prestation.rolepers'',
					 Filtre par tag
					''ByTag.tag_choice''
				),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Personne.id'' => array (''hidden'' => true),
					''Historiqueetatpe.identifiantpe'' => array ( ''sort'' => false ),
					''Historiqueetatpe.date'' => array ( ''sort'' => false ),
					''Dossier.numdemrsa'' => array ( ''sort'' => false ),
					''Dossier.matricule'' => array ( ''sort'' => false ),
					''Dossier.dtdemrsa'' => array ( ''sort'' => false ),
					''Personne.nom_complet'' => array ( ''sort'' => false ),
					''Adresse.complete'' => array ( ''sort'' => false ),
					''Canton.canton'' => array ( ''sort'' => false ),
					''Dossier.ddarrmut'' => array ( ''sort'' => false ),
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''view external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
				)
			),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
                ),
                 Valeurs à utiliser pour le préremplissage de la cohorte
                ''config'' => array(
                )
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 12, current_timestamp, current_timestamp),
(273, 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_venu_nonvenu_nouveaux', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Dossier.numdemrsa","Dossier.matricule","Dossier.dernier","Detailcalculdroitrsa.natpf","Dossier.anciennete_dispositif","Dossier.fonorg","Adresse.nomvoie","Adresse.nomcom","Adresse.numcom","Canton.canton","Personne.nom","Personne.nomnai","Personne.prenom","Prestation.rolepers","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
					''fields'' => array(
						''Personne.id'' => array (''hidden'' => true),
						''Dossier.numdemrsa'' => array ( ''sort'' => false ),
						''Personne.nom_complet_court'' => array ( ''sort'' => false ),
						''Adresse.nomcom'' => array ( ''sort'' => false ),
						''Personne.dtnai'' => array ( ''sort'' => false ),
						''Dossier.matricule'' => array ( ''sort'' => false ),
						''Personne.nir'' => array ( ''sort'' => false ),
						''Adresse.codepos'' => array ( ''sort'' => false ),
						''Adresse.numcom'' => array ( ''sort'' => false ),
						''Canton.canton'' => array ( ''sort'' => false ),
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux.ini_set'' ),
		)', 10, current_timestamp, current_timestamp),
(274, 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_stock', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Dossier.numdemrsa","Dossier.matricule","Dossier.dernier","Detailcalculdroitrsa.natpf","Dossier.anciennete_dispositif","Dossier.fonorg","Adresse.nomvoie","Adresse.nomcom","Adresse.numcom","Canton.canton","Personne.nom","Personne.nomnai","Personne.prenom","Prestation.rolepers","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Personne.nom_complet_prenoms":{"sort":false},"Adresse.complete":{"sort":false},"Canton.canton":{"sort":false},"Modecontact.numtel":{"class":"numtelCAF","sort":false},"Personne.numport":{"class":"numtelCD","sort":false},"Rendezvous.daterdv":{"sort":false},"Rendezvous.heurerdv":{"sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Personnes\/coordonnees\/#Personne.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_STOCK","Statutrdv.code_statut":"PREVU"},"save":{"Typerdv.code_type":"INFO_COLL_STOCK","Statutrdv.code_statut":""}}},"ini_set":[]}', 'Cohorte Information Collectives - Venu  Non venu - Stock


		array(
			 1. Filtres de recherche
			''filters'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux.filters''),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			''results'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux.results''),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
				),
				 Configuration des paramètres
				''config'' => array(
					 Valeurs utilisées pour la recherche de la cohorte
					''recherche'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_STOCK'',
						''Statutrdv.code_statut'' => ''PREVU''
					),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_STOCK'',
						''Statutrdv.code_statut'' => ''''
					),
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 10, current_timestamp, current_timestamp),
(275, 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_venu_nonvenu_stock', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Dossier.numdemrsa","Dossier.matricule","Dossier.dernier","Detailcalculdroitrsa.natpf","Dossier.anciennete_dispositif","Dossier.fonorg","Adresse.nomvoie","Adresse.nomcom","Adresse.numcom","Canton.canton","Personne.nom","Personne.nomnai","Personne.prenom","Prestation.rolepers","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}', 'Export CSV


		Configure::read(''ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_venu_nonvenu_nouveaux'')', 10, current_timestamp, current_timestamp),
(276, 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_second_rdv_nouveaux', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Personne.nom_complet_prenoms":{"sort":false},"Adresse.complete":{"sort":false},"Structurereferente.lib_struc":{"sort":false},"Permanence.libpermanence":{"sort":false},"Referent.nom_complet":{"sort":false},"Canton.canton":{"sort":false},"Modecontact.numtel":{"class":"numtelCAF","sort":false},"Personne.numport":{"class":"numtelCD","sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Personnes\/coordonnees\/#Personne.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_NOUVEAUX","Statutrdv.code_statut":"NONVENU"},"save":{"Typerdv.code_type":"INFO_COLL_SECOND_RDV_NOUVEAUX","Statutrdv.code_statut":"PREVU"}}},"ini_set":[]}', 'Cohorte Information Collectives - Second RDV - Nouveaux entrant


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol.filters.skip''),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Personne.id'' => array (''hidden'' => true),
					''Dossier.numdemrsa'' => array ( ''sort'' => false ),
					''Dossier.matricule'' => array ( ''sort'' => false ),
					''Dossier.dtdemrsa'' => array ( ''sort'' => false ),
					''Personne.nom_complet_prenoms'' => array ( ''sort'' => false ),
					''Adresse.complete'' => array ( ''sort'' => false ),
					''Structurereferente.lib_struc'' => array ( ''sort'' => false ),
					''Permanence.libpermanence'' => array ( ''sort'' => false ),
					''Referent.nom_complet'' => array ( ''sort'' => false ),
					''Canton.canton'' => array ( ''sort'' => false ),
					''Modecontact.numtel'' => array(''class'' => ''numtelCAF'',''sort'' => false ),
					''Personne.numport'' => array(''class'' => ''numtelCD'', ''sort'' => false ),
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''view external'' ),
					''Personnescoordonnees#Personne.id#'' => array( ''class'' => ''view external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
				)
			),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
				),
				 Configuration des paramètres
				''config'' => array(
					 Valeurs utilisées pour la recherche de la cohorte
					''recherche'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_NOUVEAUX'',
						''Statutrdv.code_statut'' => ''NONVENU''
					),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_SECOND_RDV_NOUVEAUX'',
						''Statutrdv.code_statut'' => ''PREVU''
					),
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 10, current_timestamp, current_timestamp),
(277, 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_second_rdv_nouveaux', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_second_rdv_nouveaux.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_second_rdv_nouveaux.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
					''fields'' => array(
						''Personne.id'' => array (''hidden'' => true),
						''Dossier.numdemrsa'' => array ( ''sort'' => false ),
						''Personne.nom_complet_court'' => array ( ''sort'' => false ),
						''Adresse.nomcom'' => array ( ''sort'' => false ),
						''Personne.dtnai'' => array ( ''sort'' => false ),
						''Dossier.matricule'' => array ( ''sort'' => false ),
						''Personne.nir'' => array ( ''sort'' => false ),
						''Adresse.codepos'' => array ( ''sort'' => false ),
						''Adresse.numcom'' => array ( ''sort'' => false ),
						''Canton.canton'' => array ( ''sort'' => false ),
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_second_rdv_nouveaux.ini_set'' ),
		)', 10, current_timestamp, current_timestamp),
(278, 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_second_rdv_stock', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Personne.nom_complet_prenoms":{"sort":false},"Adresse.complete":{"sort":false},"Structurereferente.lib_struc":{"sort":false},"Permanence.libpermanence":{"sort":false},"Referent.nom_complet":{"sort":false},"Canton.canton":{"sort":false},"Modecontact.numtel":{"class":"numtelCAF","sort":false},"Personne.numport":{"class":"numtelCD","sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Personnes\/coordonnees\/#Personne.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_STOCK","Statutrdv.code_statut":"NONVENU"},"save":{"Typerdv.code_type":"INFO_COLL_SECOND_RDV_STOCK","Statutrdv.code_statut":"PREVU"}}},"ini_set":[]}', 'Cohorte Information Collectives - Second RDV - Stock


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_second_rdv_nouveaux.filters.skip''),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_second_rdv_nouveaux.results''),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
				),
				 Configuration des paramètres
				''config'' => array(
					 Valeurs utilisées pour la recherche de la cohorte
					''recherche'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_STOCK'',
						''Statutrdv.code_statut'' => ''NONVENU''
					),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_SECOND_RDV_STOCK'',
						''Statutrdv.code_statut'' => ''PREVU''
					),
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 10, current_timestamp, current_timestamp),
(279, 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_second_rdv_stock', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}', 'Export CSV


		Configure::read(''ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_second_rdv_nouveaux'')', 10, current_timestamp, current_timestamp),
(291, 'ConfigurableQuery.Planpauvreteorientations.exportcsv_isemploi_stock', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Historiquedroit":{"created":"1","created_from":"TAB::-3MONTHS","created_to":"TAB::-1MONTH"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Personne.nom":{"sort":false},"Personne.prenom":{"sort":false},"Personne.dtnai":{"sort":false},"Personne.nir":{"sort":false},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Dossier.ddarrmut":{"sort":false},"Historiqueetatpe.identifiantpe":{"sort":false},"Historiqueetatpe.date":{"sort":false},"Adresse.nomcom":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}', 'Export CSV


		Configure::read(''ConfigurableQuery.Planpauvreteorientations.exportcsv_isemploi'')', 12, current_timestamp, current_timestamp),
(280, 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime_second_rdv_nouveaux', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Dossier.numdemrsa","Dossier.matricule","Dossier.dernier","Detailcalculdroitrsa.natpf","Dossier.anciennete_dispositif","Dossier.fonorg","Adresse.nomvoie","Adresse.nomcom","Adresse.numcom","Canton.canton","Personne.nom","Personne.nomnai","Personne.prenom","Prestation.rolepers","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Structurereferente.lib_struc":{"sort":false},"Referent.nom_complet":{"sort":false},"Typerdv.libelle":{"sort":false},"Rendezvous.daterdv":{"sort":false},"Rendezvous.heurerdv":{"sort":false},"Statutrdv.libelle":{"sort":false},"Canton.canton":{"sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Rendezvous\/impression\/#Rendezvous.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_SECOND_RDV_NOUVEAUX","Statutrdv.code_statut":"PREVU"},"save":[]}},"ini_set":[]}', 'Cohorte Information Collectives - Impression convocations SECOND RENDEZ-VOUS - Nouveaux entrants


		array(
			 1. Filtres de recherche
			''filters'' => Configure::read (''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.filters''),
			 2. Recherche
			''query'' => Configure::read (''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.query''),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => Configure::read (''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.results''),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
				),
				 Configuration des paramètres
				''config'' => array(
					 Valeurs utilisées pour la recherche de la cohorte
					''recherche'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_SECOND_RDV_NOUVEAUX'',
						''Statutrdv.code_statut'' => ''PREVU''
					),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(),
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 10, current_timestamp, current_timestamp),
(281, 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_imprime_second_rdv_nouveaux', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Dossier.numdemrsa","Dossier.matricule","Dossier.dernier","Detailcalculdroitrsa.natpf","Dossier.anciennete_dispositif","Dossier.fonorg","Adresse.nomvoie","Adresse.nomcom","Adresse.numcom","Canton.canton","Personne.nom","Personne.nomnai","Personne.prenom","Prestation.rolepers","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime_second_rdv_nouveaux.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime_second_rdv_nouveaux.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
					''fields'' => array(
						''Personne.id'' => array (''hidden'' => true),
						''Dossier.numdemrsa'' => array ( ''sort'' => false ),
						''Personne.nom_complet_court'' => array ( ''sort'' => false ),
						''Adresse.nomcom'' => array ( ''sort'' => false ),
						''Personne.dtnai'' => array ( ''sort'' => false ),
						''Dossier.matricule'' => array ( ''sort'' => false ),
						''Personne.nir'' => array ( ''sort'' => false ),
						''Adresse.codepos'' => array ( ''sort'' => false ),
						''Adresse.numcom'' => array ( ''sort'' => false ),
						''Canton.canton'' => array ( ''sort'' => false ),
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime_second_rdv_nouveaux.ini_set'' ),
		)', 10, current_timestamp, current_timestamp),
(315, 'Profil.Fluxpoleemplois.access', '{"by-default":{"individu":true,"inscription":true,"formation":true,"romev3":true,"allocataire":true,"structure_principale":true,"structure_deleguee":true,"ppae":true}}', 'Blocs d''informations du flux Pôle Emploi

	  Profil :
	   - correspond aux profils des groups dans la partie administration

	  Blocs :
	   - individu
	   - allocataire
	   - inscription
	   - structure_principale
	   - structure_deleguee
	   - formation
	   - romev3
	   - ppae



		array (
			 Affichage par défaut.
			''by-default'' => array (
				''individu'' => true,
				''inscription'' => true,
				''formation'' => true,
				''romev3'' => true,
				''allocataire'' => true,
				''structure_principale'' => true,
				''structure_deleguee'' => true,
				''ppae'' => true,
			),
		)', 17, current_timestamp, current_timestamp),
(282, 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime_second_rdv_stock', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Dossier.numdemrsa","Dossier.matricule","Dossier.dernier","Detailcalculdroitrsa.natpf","Dossier.anciennete_dispositif","Dossier.fonorg","Adresse.nomvoie","Adresse.nomcom","Adresse.numcom","Canton.canton","Personne.nom","Personne.nomnai","Personne.prenom","Prestation.rolepers","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Structurereferente.lib_struc":{"sort":false},"Referent.nom_complet":{"sort":false},"Typerdv.libelle":{"sort":false},"Rendezvous.daterdv":{"sort":false},"Rendezvous.heurerdv":{"sort":false},"Statutrdv.libelle":{"sort":false},"Canton.canton":{"sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Rendezvous\/impression\/#Rendezvous.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_SECOND_RDV_STOCK","Statutrdv.code_statut":"PREVU"},"save":[]}},"ini_set":[]}', 'Cohorte Information Collectives - Impression convocations SECOND RENDEZ-VOUS - Stock


		array(
			 1. Filtres de recherche
			''filters'' => Configure::read (''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.filters''),
			 2. Recherche
			''query'' => Configure::read (''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.query''),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => Configure::read (''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.results''),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
				),
				 Valeurs à utiliser pour le préremplissage de la cohorte
				 Configuration des paramètres
				''config'' => array(
					 Valeurs utilisées pour la recherche de la cohorte
					''recherche'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_SECOND_RDV_STOCK'',
						''Statutrdv.code_statut'' => ''PREVU''
					),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(),
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 10, current_timestamp, current_timestamp),
(283, 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_imprime_second_rdv_stock', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Dossier.numdemrsa","Dossier.matricule","Dossier.dernier","Detailcalculdroitrsa.natpf","Dossier.anciennete_dispositif","Dossier.fonorg","Adresse.nomvoie","Adresse.nomcom","Adresse.numcom","Canton.canton","Personne.nom","Personne.nomnai","Personne.prenom","Prestation.rolepers","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}', 'Export CSV


		Configure::read(''ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_imprime_second_rdv_nouveaux'')', 10, current_timestamp, current_timestamp),
(292, 'ConfigurableQuery.Planpauvreteorientations.cohorte_isemploi_imprime', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Orientstruct.id":{"hidden":true},"Historiqueetatpe.identifiantpe":{"sort":false},"Historiqueetatpe.date":{"sort":false},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Dossier.ddarrmut":{"sort":false},"Personne.nom_complet":{"sort":false},"Personne.nomnai":{"sort":false},"Personne.dtnati":{"sort":false},"Canton.canton":{"sort":false},"Situationdossierrsa.etatdosrsa":{"sort":false},"Structurereferente.lib_struc":{"sort":false},"Orientstruct.statut_orient":{"sort":false},"Orientstruct.date_valid":{"sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Orientsstructs\/impression\/#Orientstruct.id#":{"class":"external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":[]},"ini_set":[]}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(
					''Calculdroitrsa.toppersdrodevorsa'',
					''Dossier.dtdemrsa'',
					''Detaildroitrsa.oridemrsa'',
					''Foyer.sitfam'',
					''Personne.trancheage'',
					''Situationdossierrsa.etatdosrsa'',
					''Serviceinstructeur.id'',
					''Suiviinstruction.typeserins'',
					''PersonneReferent.structurereferente_id'',
					''PersonneReferent.referent_id'',
					''Prestation.rolepers'',
					 Filtre par tag
					''ByTag.tag_choice''
				),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Orientstruct.date_valid DESC'')
				''order'' => array(''Personne.id DESC'')
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Personne.id'' => array (''hidden'' => true),
					''Orientstruct.id'' => array (''hidden'' => true),
					''Historiqueetatpe.identifiantpe'' => array ( ''sort'' => false ),
					''Historiqueetatpe.date'' => array ( ''sort'' => false ),
					''Dossier.numdemrsa'' => array ( ''sort'' => false ),
					''Dossier.matricule'' => array ( ''sort'' => false ),
					''Dossier.dtdemrsa'' => array ( ''sort'' => false ),
					''Dossier.ddarrmut'' => array ( ''sort'' => false ),
					''Personne.nom_complet'' => array ( ''sort'' => false ),
					''Personne.nomnai'' => array ( ''sort'' => false ),
					''Personne.dtnati'' => array ( ''sort'' => false ),
					''Canton.canton'' => array ( ''sort'' => false ),
					''Situationdossierrsa.etatdosrsa'' => array ( ''sort'' => false ),
					''Structurereferente.lib_struc'' => array ( ''sort'' => false ),
					''Orientstruct.statut_orient'' => array ( ''sort'' => false ),
					''Orientstruct.date_valid'' => array ( ''sort'' => false ),
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''view external'' ),
					''Orientsstructsimpression#Orientstruct.id#'' => array(
						''class'' => ''external''
					)
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
				)
			),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
                ),
                 Valeurs à utiliser pour le préremplissage de la cohorte
                ''config'' => array(
                )
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 12, current_timestamp, current_timestamp),
(284, 'ConfigurableQuery.Planpauvrete.searchprimoaccedant', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Dossier.matricule","2":"Dossier.dtdemrsa","3":"Personne.nom_complet","4":"Personne.nomnai","5":"Personne.dtnati","6":"Situationdossierrsa.etatdosrsa","7":"Canton.canton","\/Dossiers\/view\/#Dossier.id#":{"class":"view external"}},"innerTable":[]},"ini_set":[]}', 'Menu "Recherches" > Par Primo accédants


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(
					''Calculdroitrsa.toppersdrodevorsa'',
					''Dossier.dtdemrsa'',
					''Detaildroitrsa.oridemrsa'',
					''Foyer.sitfam'',
					''Personne.trancheage'',
					''Situationdossierrsa.etatdosrsa'',
					''Serviceinstructeur.id'',
					''Suiviinstruction.typeserins'',
					''PersonneReferent.structurereferente_id'',
					''PersonneReferent.referent_id'',
					''Prestation.rolepers'',
					 Filtre par tag
					''ByTag.tag_choice''
				),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Dossier.dtdemrsa'',
					''Personne.nom_complet'',
					''Personne.nomnai'',
					''Personne.dtnati'',
					''Situationdossierrsa.etatdosrsa'',
					''Canton.canton'',
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''view external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 11, current_timestamp, current_timestamp),
(286, 'PlanPauvrete.Cohorte.ValeursOrientations', '{"structureorientante_id":"49","referentorientant_id":"466","typeorient_id":"30","structurereferente_id":"49","referent_id":"466","typenotification":"systematique"}', 'Paramétrage du module "Cohorte Plan Pauvreté"

	  @param array


		array (
			Identifiant de la structure d''orientation
			''structureorientante_id''=> ''49'',
			 Identifiant du référent de l''orientation
			''referentorientant_id'' => ''466'',
			Identifiant du type d''orientation
			''typeorient_id'' => ''30'',
			Identifiant de la structure reférente
			''structurereferente_id'' => ''49'',
			Identifiant du référent
			''referent_id'' => ''466'',
			Type de notification
			''typenotification'' => ''systematique''
		)', 12, current_timestamp, current_timestamp),
(287, 'PlanPauvrete.Cohorte.Orientations.Limite', '{"structureorientante_id":"49","typeorient_id":"30"}', 'Paramétrage du module "Cohorte Plan Pauvreté"
	  Limites de la recherche avec orientations
	  Table de valeur limitantes, dont les élement possibles sont :
	  ''structureorientante_id'' => int,
	  ''typeorient_id'' => int
	  ''typenotification'' = string

	  @param array


		array (
			Identifiant de la structure d''orientation
			''structureorientante_id'' => Configure::read(''PlanPauvrete.Cohorte.ValeursOrientations.structurereferente_id''),
			Identifiant du type d''orientation
			''typeorient_id'' => Configure::read(''PlanPauvrete.Cohorte.ValeursOrientations.typeorient_id''),
		)', 12, current_timestamp, current_timestamp),
(289, 'ConfigurableQuery.Planpauvreteorientations.exportcsv_isemploi', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Historiquedroit":{"created":"1","created_from":"TAB::-3MONTHS","created_to":"TAB::-1MONTH"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Personne.nom":{"sort":false},"Personne.prenom":{"sort":false},"Personne.dtnai":{"sort":false},"Personne.nir":{"sort":false},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Dossier.ddarrmut":{"sort":false},"Historiqueetatpe.identifiantpe":{"sort":false},"Historiqueetatpe.date":{"sort":false},"Adresse.nomcom":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Planpauvreteorientations.cohorte_isemploi.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Planpauvreteorientations.cohorte_isemploi.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
					''fields'' => array(
						''Personne.id'' => array (''hidden'' => true),
						''Personne.nom'' => array ( ''sort'' => false ),
						''Personne.prenom'' => array ( ''sort'' => false ),
						''Personne.dtnai'' => array ( ''sort'' => false ),
						''Personne.nir'' => array ( ''sort'' => false ),
						''Dossier.numdemrsa'' => array ( ''sort'' => false ),
						''Dossier.matricule'' => array ( ''sort'' => false ),
						''Dossier.dtdemrsa'' => array ( ''sort'' => false ),
						''Dossier.ddarrmut'' => array ( ''sort'' => false ),
						''Historiqueetatpe.identifiantpe'' => array ( ''sort'' => false ),
						''Historiqueetatpe.date'' => array ( ''sort'' => false ),
						''Adresse.nomcom'' => array ( ''sort'' => false ),
						''Adresse.codepos'' => array ( ''sort'' => false ),
						''Adresse.numcom'' => array ( ''sort'' => false ),
						''Canton.canton'' => array ( ''sort'' => false ),
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Planpauvreteorientations.cohorte_isemploi.ini_set'' ),
		)', 12, current_timestamp, current_timestamp),
(293, 'ConfigurableQuery.Planpauvreteorientations.exportcsv_isemploi_imprime', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Historiquedroit":{"created":"1","created_from":"TAB::-3MONTHS","created_to":"TAB::-1MONTH"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Dossier.ddarrmut":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false},"Historiqueetatpe.identifiantpe":{"sort":false},"Historiqueetatpe.date":{"sort":false}}},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Planpauvreteorientations.cohorte_isemploi.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Planpauvreteorientations.cohorte_isemploi.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
					''fields'' => array(
						''Personne.id'' => array (''hidden'' => true),
						''Dossier.numdemrsa'' => array ( ''sort'' => false ),
						''Personne.nom_complet_court'' => array ( ''sort'' => false ),
						''Adresse.nomcom'' => array ( ''sort'' => false ),
						''Personne.dtnai'' => array ( ''sort'' => false ),
						''Dossier.matricule'' => array ( ''sort'' => false ),
						''Dossier.dtdemrsa'' => array ( ''sort'' => false ),
						''Dossier.ddarrmut'' => array ( ''sort'' => false ),
						''Personne.nir'' => array ( ''sort'' => false ),
						''Adresse.codepos'' => array ( ''sort'' => false ),
						''Adresse.numcom'' => array ( ''sort'' => false ),
						''Canton.canton'' => array ( ''sort'' => false ),
						''Historiqueetatpe.identifiantpe'' => array ( ''sort'' => false ),
						''Historiqueetatpe.date'' => array ( ''sort'' => false ),
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Planpauvreteorientations.cohorte_isemploi.ini_set'' ),
		)', 12, current_timestamp, current_timestamp),
(294, 'ConfigurableQuery.Planpauvreteorientations.cohorte_isemploi_stock_imprime', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Orientstruct.id":{"hidden":true},"Historiqueetatpe.identifiantpe":{"sort":false},"Historiqueetatpe.date":{"sort":false},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Dossier.ddarrmut":{"sort":false},"Personne.nom_complet":{"sort":false},"Personne.nomnai":{"sort":false},"Personne.dtnati":{"sort":false},"Canton.canton":{"sort":false},"Situationdossierrsa.etatdosrsa":{"sort":false},"Structurereferente.lib_struc":{"sort":false},"Orientstruct.statut_orient":{"sort":false},"Orientstruct.date_valid":{"sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Orientsstructs\/impression\/#Orientstruct.id#":{"class":"external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":[]},"ini_set":[]}', 'Cohorte


		Configure::read(''ConfigurableQuery.Planpauvreteorientations.cohorte_isemploi_imprime'')', 12, current_timestamp, current_timestamp),
(295, 'ConfigurableQuery.Planpauvreteorientations.exportcsv_isemploi_stock_imprime', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Historiquedroit":{"created":"1","created_from":"TAB::-3MONTHS","created_to":"TAB::-1MONTH"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Dossier.ddarrmut":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false},"Historiqueetatpe.identifiantpe":{"sort":false},"Historiqueetatpe.date":{"sort":false}}},"ini_set":[]}', 'Export CSV


		Configure::read(''ConfigurableQuery.Planpauvreteorientations.exportcsv_isemploi_imprime'')', 12, current_timestamp, current_timestamp),
(296, 'ConfigurableQuery.Orientsstructs.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet_court","2":"Adresse.nomcom","3":"Dossier.dtdemrsa","4":"Orientstruct.date_valid","Structureorientante.lib_struc":{"label":"Structure orientante"},"5":"Typeorient.lib_type_orient","6":"Structurereferente.lib_struc","7":"Orientstruct.statut_orient","Calculdroitrsa.toppersdrodevorsa":{"type":"boolean"},"8":"Canton.canton","\/Orientsstructs\/index\/#Orientstruct.personne_id#":{"class":"view"}},"innerTable":["Situationdossierrsa.etatdosrsa","Personne.nomcomnai","Personne.dtnai","Adresse.numcom","Personne.nir","Historiqueetatpe.identifiantpe","Modecontact.numtel","Prestation.rolepers","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}', 'Menu "Recherches" > "Par orientation"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
						 Case à cocher "Filtrer par date de demande RSA"
						''dtdemrsa'' => ''0'',
						 Du (inclus)
						''dtdemrsa_from'' => ''TAB::-1WEEK'',
						 Au (inclus)
						''dtdemrsa_to'' => ''TAB::NOW'',
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Dossier.dtdemrsa'',
					''Orientstruct.date_valid'',
					''Structureorientante.lib_struc'' => array(
						''label'' => ''Structure orientante''
					),
					''Typeorient.lib_type_orient'',
					''Structurereferente.lib_struc'',
					''Orientstruct.statut_orient'',
					''Calculdroitrsa.toppersdrodevorsa'' => array( ''type'' => ''boolean'' ),
					''Canton.canton'',
					''Orientsstructsindex#Orientstruct.personne_id#'' => array( ''class'' => ''view'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Situationdossierrsa.etatdosrsa'',
					''Personne.nomcomnai'',
					''Personne.dtnai'',
					''Adresse.numcom'',
					''Personne.nir'',
					''Historiqueetatpe.identifiantpe'',
					''Modecontact.numtel'',
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''1024M''
			)
		)', 13, current_timestamp, current_timestamp),
(316, 'ConfigurableQuery.Fluxpoleemplois.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"},"Situationdossierrsa":{"etatdosrsa_choice":"0","etatdosrsa":["0","2","3","4"]}},"accepted":[],"skip":[],"has":{"Contratinsertion":{"Contratinsertion.decision_ci":"V"}}},"query":{"restrict":[],"conditions":[],"order":{"Dossier.dtdemrsa":"DESC","Personne.nom":"ASC"}},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Dossier.dtdemrsa","2":"Personne.nir","3":"Situationdossierrsa.etatdosrsa","4":"Personne.nom_complet_prenoms","5":"Adresse.nomcom","Dossier.locked":{"type":"boolean","class":"dossier_locked"},"6":"\/Dossiers\/view\/#Dossier.id#"},"innerTable":{"0":"Dossier.matricule","1":"Personne.dtnai","Adresse.numcom":{"options":[]},"2":"Prestation.rolepers","3":"Structurereferenteparcours.lib_struc","4":"Referentparcours.nom_complet"}},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', '?>
<?php

	  Menu "Recherches" > "Par Pôle Emploi"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
						 Case à cocher "Filtrer par date de demande RSA"
						''dtdemrsa'' => ''0'',
						 Du (inclus)
						''dtdemrsa_from'' => ''TAB::-1WEEK'',
						 Au (inclus)
						''dtdemrsa_to'' => ''TAB::NOW'',
					),
					''Situationdossierrsa'' => array(
						''etatdosrsa_choice'' => ''0'',
						''etatdosrsa'' => array( ''0'',''2'', ''3'', ''4'' )
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array(
					''Contratinsertion'' => array(
						''Contratinsertion.decision_ci'' => ''V''
					),
				)
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(
					''Dossier.dtdemrsa'' => ''DESC'',
					''Personne.nom'' => ''ASC''
				)
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Personne.nom_complet_prenoms'',
					''Adresse.nomcom'',
					''Dossier.locked'' => array(
						''type'' => ''boolean'',
						''class'' => ''dossier_locked''
					),
					''Dossiersview#Dossier.id#''
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Dossier.matricule'',
					''Personne.dtnai'',
					''Adresse.numcom'' => array(
						''options'' => array()
					),
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''512M''
			)
		)', 17, current_timestamp, current_timestamp),
(297, 'ConfigurableQuery.Orientsstructs.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"results":{"fields":{"0":"Dossier.numdemrsa","1":"Personne.qual","2":"Personne.nom","3":"Personne.prenom","4":"Personne.nir","5":"Personne.dtnai","6":"Dossier.matricule","7":"Historiqueetatpe.identifiantpe","8":"Modecontact.numtel","9":"Adresse.numvoie","10":"Adresse.libtypevoie","11":"Adresse.nomvoie","12":"Adresse.complideadr","13":"Adresse.compladr","14":"Adresse.codepos","15":"Adresse.nomcom","16":"Canton.canton","17":"Dossier.dtdemrsa","18":"Situationdossierrsa.etatdosrsa","19":"Structurereferenteparcours.lib_struc","20":"Referentparcours.nom_complet","21":"Orientstruct.date_valid","22":"Typeorient.lib_type_orient","23":"Structurereferente.lib_struc","Structureorientante.lib_struc":{"label":"Structure orientante"},"24":"Orientstruct.statut_orient","25":"Calculdroitrsa.toppersdrodevorsa"}},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}', 'Export CSV,  menu "Recherches" > "Par orientation"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Orientsstructs.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Orientsstructs.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Personne.qual'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.nir'',
					''Personne.dtnai'',
					''Dossier.matricule'',
					''Historiqueetatpe.identifiantpe'',
					''Modecontact.numtel'',
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Canton.canton'',
					''Dossier.dtdemrsa'',
					''Situationdossierrsa.etatdosrsa'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Orientstruct.date_valid'',
					''Typeorient.lib_type_orient'',
					''Structurereferente.lib_struc'',
					''Structureorientante.lib_struc'' => array(
						''label'' => ''Structure orientante''
					),
					''Orientstruct.statut_orient'',
					''Calculdroitrsa.toppersdrodevorsa'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Orientsstructs.search.ini_set'' ),
		)', 13, current_timestamp, current_timestamp),
(298, 'ConfigurableQuery.Orientsstructs.cohorte_nouvelles', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet_court","2":"Adresse.nomcom","3":"Dossier.dtdemrsa","4":"Orientstruct.date_valid","Structureorientante.lib_struc":{"label":"Structure orientante"},"5":"Typeorient.lib_type_orient","6":"Structurereferente.lib_struc","7":"Orientstruct.statut_orient","Calculdroitrsa.toppersdrodevorsa":{"type":"boolean"},"8":"Canton.canton","\/Orientsstructs\/index\/#Orientstruct.personne_id#":{"class":"view"}},"innerTable":["Situationdossierrsa.etatdosrsa","Personne.nomcomnai","Personne.dtnai","Adresse.numcom","Personne.nir","Historiqueetatpe.identifiantpe","Modecontact.numtel","Prestation.rolepers","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}', 'Menu "Cohortes" > "Orientation" > "Demandes non orientées"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Orientsstructs.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Orientsstructs.search.query'' ),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => Configure::read( ''ConfigurableQuery.Orientsstructs.search.results'' ),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''1024M''
			)
		)', 13, current_timestamp, current_timestamp),
(299, 'ConfigurableQuery.Orientsstructs.cohorte_orientees', '{"filters":{"defaults":{"Detailcalculdroitrsa":{"natpf_choice":"1","natpf":["RSD","RSI"]},"Detaildroitrsa":{"oridemrsa_choice":"1","oridemrsa":["DEM"]},"Situationdossierrsa":{"etatdosrsa_choice":"1","etatdosrsa":[2,3,4]}},"accepted":{"Situationdossierrsa.etatdosrsa":["Z",2,3,4],"Detailcalculdroitrsa.natpf":["RSD","RSI","RSU","RSJ"]},"skip":["Dossier.numdemrsa","Dossier.matricule","Dossier.anciennete_dispositif","Serviceinstructeur.id","Dossier.fonorg","Foyer.sitfam","Personne.dtnai","Personne.nomnai","Personne.nir","Personne.sexe","Personne.trancheage"],"has":["Dsp"]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["Z",2,3,4],"Detailcalculdroitrsa.natpf_choice":"1","Detailcalculdroitrsa.natpf":["RSD","RSI","RSU","RSJ"]},"conditions":[],"order":["Dossier.dtdemrsa"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Adresse.nomcom","1":"Personne.nom_complet_court","2":"Dossier.dtdemrsa","Personne.has_dsp":{"type":"boolean"},"3":"Suiviinstruction.typeserins","4":"Orientstruct.origine","5":"Orientstruct.propo_algo","6":"Typeorient.lib_type_orient","7":"Structurereferente.lib_struc","8":"Orientstruct.statut_orient","9":"Orientstruct.date_propo","10":"Orientstruct.date_valid","11":"Canton.canton","\/Orientsstructs\/impression\/#Orientstruct.id#":{"class":"external"}},"innerTable":["Dossier.numdemrsa","Dossier.dtdemrsa","Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Situationdossierrsa.dtclorsa","Situationdossierrsa.moticlorsa","Prestation.rolepers","Situationdossierrsa.etatdosrsa","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}', 'Menu "Cohortes" > "Orientation" > "Demandes orientées"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Detailcalculdroitrsa'' => array(
						''natpf_choice'' => ''1'',
						''natpf'' => array( ''RSD'', ''RSI'' )
					),
					''Detaildroitrsa'' => array(
						''oridemrsa_choice'' => ''1'',
						''oridemrsa'' => array( ''DEM'' )
					),
					''Situationdossierrsa'' => array(
						''etatdosrsa_choice'' => ''1'',
						''etatdosrsa'' => array( 2, 3, 4 )
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(
					''Situationdossierrsa.etatdosrsa'' => array( ''Z'', 2, 3, 4 ),
					''Detailcalculdroitrsa.natpf'' => array( ''RSD'', ''RSI'', ''RSU'', ''RSJ'' )
				),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Dossier.anciennete_dispositif'',
					''Serviceinstructeur.id'',
					''Dossier.fonorg'',
					''Foyer.sitfam'',
					''Personne.dtnai'',
					''Personne.nomnai'',
					''Personne.nir'',
					''Personne.sexe'',
					''Personne.trancheage''
				),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array( ''Dsp'' )
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Situationdossierrsa.etatdosrsa_choice'' => ''1'',
					''Situationdossierrsa.etatdosrsa'' => array( ''Z'', 2, 3, 4 ),
					''Detailcalculdroitrsa.natpf_choice'' => ''1'',
					''Detailcalculdroitrsa.natpf'' => array( ''RSD'', ''RSI'', ''RSU'', ''RSJ'' )
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array( ''Dossier.dtdemrsa'' )
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Adresse.nomcom'',
					''Personne.nom_complet_court'',
					''Dossier.dtdemrsa'',
					''Personne.has_dsp'' => array(
						''type'' => ''boolean''
					),
					''Suiviinstruction.typeserins'',
					''Orientstruct.origine'',
					''Orientstruct.propo_algo'',
					''Typeorient.lib_type_orient'',
					''Structurereferente.lib_struc'',
					''Orientstruct.statut_orient'',
					''Orientstruct.date_propo'',
					''Orientstruct.date_valid'',
					''Canton.canton'',
					''Orientsstructsimpression#Orientstruct.id#'' => array(
						''class'' => ''external''
					)
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.dtnai'',
					''Dossier.matricule'',
					''Personne.nir'',
					''Adresse.codepos'',
					''Situationdossierrsa.dtclorsa'',
					''Situationdossierrsa.moticlorsa'',
					''Prestation.rolepers'',
					''Situationdossierrsa.etatdosrsa'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''1024M''
			)
		)', 13, current_timestamp, current_timestamp),
(300, 'ConfigurableQuery.Nonorientes66.cohorte_isemploi', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Dossier.matricule","2":"Dossier.dtdemrsa","3":"Personne.nom_complet_court","4":"Personne.nomnai","5":"Personne.dtnai","6":"Situationdossierrsa.etatdosrsa","7":"Adresse.nomcom","8":"Foyer.enerreur","9":"Canton.canton","\/Dossiers\/view\/#Dossier.id#":{"class":"view external"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"cohorte":{"options":[],"values":{"Orientstruct.typeorient_id":2,"Orientstruct.structurereferente_id":23}},"ini_set":[]}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Dossier.dtdemrsa'',
					''Personne.nom_complet_court'',
					''Personne.nomnai'',
					''Personne.dtnai'',
					''Situationdossierrsa.etatdosrsa'',
					''Adresse.nomcom'',
					''Foyer.enerreur'',
					''Canton.canton'',
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''view external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
					''Orientstruct.typeorient_id'' => 2,  Type d''orientation - Emploi - Pôle emploi
					''Orientstruct.structurereferente_id'' => 23,  Structure référente - Pôle emploi
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 14, current_timestamp, current_timestamp),
(301, 'ConfigurableQuery.Nonorientes66.exportcsv_isemploi', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":["Dossier.numdemrsa","Dossier.matricule","Dossier.dtdemrsa","Personne.nom_complet_court","Personne.nomnai","Personne.dtnai","Situationdossierrsa.etatdosrsa","Adresse.nomcom","Foyer.enerreur","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Nonorientes66.cohorte_isemploi.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Nonorientes66.cohorte_isemploi.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Dossier.dtdemrsa'',
					''Personne.nom_complet_court'',
					''Personne.nomnai'',
					''Personne.dtnai'',
					''Situationdossierrsa.etatdosrsa'',
					''Adresse.nomcom'',
					''Foyer.enerreur'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Nonorientes66.cohorte_isemploi.ini_set'' ),
		)', 14, current_timestamp, current_timestamp),
(302, 'ConfigurableQuery.Nonorientes66.cohorte_imprimeremploi', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Dossier.matricule","2":"Dossier.dtdemrsa","3":"Personne.nom_complet_court","4":"Situationdossierrsa.etatdosrsa","5":"Adresse.nomcom","6":"Historiqueetatpe.etat","7":"Foyer.nbenfants","8":"Foyer.enerreur","9":"Canton.canton","\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Nonorientes66\/imprimeremploi\/#Personne.id#":{"class":"print imprimer"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Dossier.dtdemrsa'',
					''Personne.nom_complet_court'',
					''Situationdossierrsa.etatdosrsa'',
					''Adresse.nomcom'',
					''Historiqueetatpe.etat'',
					''Foyer.nbenfants'',
					''Foyer.enerreur'',
					''Canton.canton'',
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''view external'' ),
					''Nonorientes66imprimeremploi#Personne.id#'' => array( ''class'' => ''print imprimer'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 14, current_timestamp, current_timestamp),
(322, 'ConfigurableQuery.Dossiers.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":[],"skip":[],"has":{"0":"Cui","Orientstruct":{"Orientstruct.statut_orient":"Orienté"},"Contratinsertion":{"Contratinsertion.decision_ci":"V"},"1":"Personnepcg66"}},"query":{"restrict":[],"conditions":[],"order":["Personne.nom"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Dossier.dtdemrsa","2":"Personne.nir","3":"Situationdossierrsa.etatdosrsa","4":"Personne.qual","5":"Personne.nom","6":"Personne.prenom","7":"Personne.nom_complet_prenoms","8":"Adresse.complete","Dossier.locked":{"type":"boolean","class":"dossier_locked"},"9":"Canton.canton","10":"\/Dossiers\/view\/#Dossier.id#"},"innerTable":{"Dossier.fonorg":{"verifMSA":true},"0":"Dossier.matricule","1":"Personne.dtnai","Adresse.numcom":{"options":[]},"2":"Prestation.rolepers","3":"Structurereferenteparcours.lib_struc","4":"Referentparcours.nom_complet"}},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Menu "Recherches" > "Par dossier  allocataire"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
						 Case à cocher "Filtrer par date de demande RSA"
						''dtdemrsa'' => ''0'',
						 Du (inclus)
						''dtdemrsa_from'' => ''TAB::-1WEEK'',
						 Au (inclus)
						''dtdemrsa_to'' => ''TAB::NOW'',
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array(
					''Cui'',
					''Orientstruct'' => array(
						''Orientstruct.statut_orient'' => ''Orienté'',
						 Orientstruct possède des conditions supplémentaire dans le modèle WebrsaRechercheDossier pour le CD66
					),
					''Contratinsertion'' => array(
						''Contratinsertion.decision_ci'' => ''V''
					),
					''Personnepcg66''
				)
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array( ''Personne.nom'' )
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Personne.qual'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.nom_complet_prenoms'',  FIXME: nom completcourtprenoms ?
					''Adresse.complete'',
					''Dossier.locked'' => array(
						''type'' => ''boolean'',
						''class'' => ''dossier_locked''
					),
					''Canton.canton'',
					''Dossiersview#Dossier.id#''
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
				    ''Dossier.fonorg'' => array(
						''verifMSA''=>trueajoute la couleur dans le cas d''un bénéficiaire MSA
					),
					''Dossier.matricule'',
					''Personne.dtnai'',
					''Adresse.numcom'' => array(
						''options'' => array()
					),
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''512M''
			)
		)', 20, current_timestamp, current_timestamp),
(303, 'ConfigurableQuery.Nonorientes66.exportcsv_imprimeremploi', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":["Dossier.numdemrsa","Dossier.matricule","Dossier.dtdemrsa","Personne.nom_complet_court","Situationdossierrsa.etatdosrsa","Adresse.nomcom","Historiqueetatpe.etat","Foyer.nbenfants","Foyer.enerreur","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Nonorientes66.cohorte_imprimeremploi.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Nonorientes66.cohorte_imprimeremploi.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Dossier.dtdemrsa'',
					''Personne.nom_complet_court'',
					''Situationdossierrsa.etatdosrsa'',
					''Adresse.nomcom'',
					''Historiqueetatpe.etat'',
					''Foyer.nbenfants'',
					''Foyer.enerreur'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Nonorientes66.cohorte_imprimeremploi.ini_set'' ),
		)', 14, current_timestamp, current_timestamp),
(304, 'ConfigurableQuery.Nonorientes66.cohorte_reponse', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Dossier.matricule","2":"Dossier.dtdemrsa","3":"Personne.nom_complet_court","4":"Situationdossierrsa.etatdosrsa","5":"Adresse.nomcom","6":"Nonoriente66.dateimpression","7":"Foyer.nbenfants","8":"Foyer.enerreur","9":"Canton.canton","\/Dossiers\/view\/#Dossier.id#":{"class":"view external"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Dossier.dtdemrsa'',
					''Personne.nom_complet_court'',
					''Situationdossierrsa.etatdosrsa'',
					''Adresse.nomcom'',
					''Nonoriente66.dateimpression'',
					''Foyer.nbenfants'',
					''Foyer.enerreur'',
					''Canton.canton'',
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''view external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 14, current_timestamp, current_timestamp),
(305, 'ConfigurableQuery.Nonorientes66.exportcsv_reponse', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":["Dossier.numdemrsa","Dossier.matricule","Dossier.dtdemrsa","Personne.nom_complet_court","Situationdossierrsa.etatdosrsa","Adresse.nomcom","Nonoriente66.dateimpression","Foyer.nbenfants","Foyer.enerreur","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Nonorientes66.cohorte_reponse.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Nonorientes66.cohorte_reponse.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Dossier.dtdemrsa'',
					''Personne.nom_complet_court'',
					''Situationdossierrsa.etatdosrsa'',
					''Adresse.nomcom'',
					''Nonoriente66.dateimpression'',
					''Foyer.nbenfants'',
					''Foyer.enerreur'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Nonorientes66.cohorte_reponse.ini_set'' ),
		)', 14, current_timestamp, current_timestamp),
(306, 'ConfigurableQuery.Nonorientes66.cohorte_imprimernotifications', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Dossier.matricule","2":"Dossier.dtdemrsa","3":"Personne.nom_complet_court","4":"Situationdossierrsa.etatdosrsa","5":"Adresse.nomcom","6":"Typeorient.lib_type_orient","7":"Structurereferente.lib_struc","8":"Foyer.enerreur","9":"Canton.canton","\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Nonorientes66\/imprimernotifications\/#Orientstruct.id#":{"class":"print imprimer"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Dossier.dtdemrsa'',
					''Personne.nom_complet_court'',
					''Situationdossierrsa.etatdosrsa'',
					''Adresse.nomcom'',
					''Typeorient.lib_type_orient'',
					''Structurereferente.lib_struc'',
					''Foyer.enerreur'',
					''Canton.canton'',
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''view external'' ),
					''Nonorientes66imprimernotifications#Orientstruct.id#'' => array( ''class'' => ''print imprimer'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 14, current_timestamp, current_timestamp),
(307, 'ConfigurableQuery.Nonorientes66.exportcsv_imprimernotifications', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":["Dossier.numdemrsa","Dossier.matricule","Dossier.dtdemrsa","Personne.nom_complet_court","Situationdossierrsa.etatdosrsa","Adresse.nomcom","Historiqueetatpe.etat","Foyer.nbenfants","Foyer.enerreur","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Nonorientes66.cohorte_imprimernotifications.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Nonorientes66.cohorte_imprimernotifications.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Dossier.dtdemrsa'',
					''Personne.nom_complet_court'',
					''Situationdossierrsa.etatdosrsa'',
					''Adresse.nomcom'',
					''Historiqueetatpe.etat'',
					''Foyer.nbenfants'',
					''Foyer.enerreur'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Nonorientes66.cohorte_imprimernotifications.ini_set'' ),
		)', 14, current_timestamp, current_timestamp),
(308, 'ConfigurableQuery.Nonorientes66.recherche_notifie', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Dossier.matricule","2":"Dossier.dtdemrsa","3":"Personne.nom_complet_court","4":"Situationdossierrsa.etatdosrsa","5":"Adresse.nomcom","6":"Typeorient.lib_type_orient","7":"Structurereferente.lib_struc","8":"Foyer.enerreur","9":"Orientstruct.nbfichier_lies","10":"Canton.canton","\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Nonorientes66\/imprimernotifications\/#Orientstruct.id#":{"class":"print imprimer"},"\/Orientsstructs\/filelink\/#Orientstruct.id#":{"class":"external"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Dossier.dtdemrsa'',
					''Personne.nom_complet_court'',
					''Situationdossierrsa.etatdosrsa'',
					''Adresse.nomcom'',
					''Typeorient.lib_type_orient'',
					''Structurereferente.lib_struc'',
					''Foyer.enerreur'',
					''Orientstruct.nbfichier_lies'',
					''Canton.canton'',
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''view external'' ),
					''Nonorientes66imprimernotifications#Orientstruct.id#'' => array( ''class'' => ''print imprimer'' ),
					''Orientsstructsfilelink#Orientstruct.id#'' => array( ''class'' => ''external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 14, current_timestamp, current_timestamp),
(309, 'ConfigurableQuery.Nonorientes66.exportcsv_notifie', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":[]},"results":{"fields":["Dossier.numdemrsa","Dossier.matricule","Dossier.dtdemrsa","Personne.nom_complet_court","Situationdossierrsa.etatdosrsa","Adresse.nomcom","Typeorient.lib_type_orient","Structurereferente.lib_struc","Foyer.enerreur","Orientstruct.nbfichier_lies","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Nonorientes66.recherche_notifie.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Nonorientes66.recherche_notifie.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Dossier.dtdemrsa'',
					''Personne.nom_complet_court'',
					''Situationdossierrsa.etatdosrsa'',
					''Adresse.nomcom'',
					''Typeorient.lib_type_orient'',
					''Structurereferente.lib_struc'',
					''Foyer.enerreur'',
					''Orientstruct.nbfichier_lies'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Nonorientes66.recherche_notifie.ini_set'' ),
		)', 14, current_timestamp, current_timestamp),
(310, 'ConfigurableQuery.Nonorientationsproseps.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":{"(DATE_PART(''day'', NOW() - Contratinsertion.df_ci))":"DESC"}},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet_court","2":"Personne.dtnai","3":"Adresse.codepos","Foyer.enerreur":{"type":"string","class":"foyer_enerreur"},"4":"Orientstruct.date_valid","5":"Contratinsertion.nbjours","6":"Typeorient.lib_type_orient","7":"Structurereferente.lib_struc","8":"Referent.nom_complet","9":"Canton.canton","\/Rendezvous\/index\/#Personne.id#":{"class":"view"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu "Recherches"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array("(DATE_PART(''day'', NOW() - Contratinsertion.df_ci))" => ''DESC'')
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Personne.nom_complet_court'',
					''Personne.dtnai'',
					''Adresse.codepos'',
					''Foyer.enerreur'' => array( ''type'' => ''string'', ''class'' => ''foyer_enerreur'' ),
					''Orientstruct.date_valid'',
					''Contratinsertion.nbjours'',
					''Typeorient.lib_type_orient'',
					''Structurereferente.lib_struc'',
					''Referent.nom_complet'',
					''Canton.canton'',
					''Rendezvousindex#Personne.id#'' => array( ''class'' => ''view'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 15, current_timestamp, current_timestamp),
(311, 'ConfigurableQuery.Nonorientationsproseps.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"results":{"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet_court","2":"Personne.dtnai","3":"Adresse.codepos","Foyer.enerreur":{"type":"string","class":"foyer_enerreur"},"4":"Orientstruct.date_valid","5":"Contratinsertion.nbjours","6":"Typeorient.lib_type_orient","7":"Structurereferente.lib_struc","8":"Referent.nom_complet","9":"Structurereferenteparcours.lib_struc","10":"Referentparcours.nom_complet","11":"Canton.canton"}},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Orientsstructs.exportcsv.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Orientsstructs.exportcsv.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Personne.nom_complet_court'',
					''Personne.dtnai'',
					''Adresse.codepos'',
					''Foyer.enerreur'' => array( ''type'' => ''string'', ''class'' => ''foyer_enerreur'' ),
					''Orientstruct.date_valid'',
					''Contratinsertion.nbjours'',
					''Typeorient.lib_type_orient'',
					''Structurereferente.lib_struc'',
					''Referent.nom_complet'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Orientsstructs.exportcsv.ini_set'' ),
		)', 15, current_timestamp, current_timestamp),
(312, 'ConfigurableQuery.Indus.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":{"Situationdossierrsa.etatdosrsa":["Z",2,3,4]},"skip":[]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["Z",2,3,4]},"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet_court","2":"Dossier.typeparte","3":"Situationdossierrsa.etatdosrsa","Indu.moismoucompta":{"type":"date","format":"%B %Y"},"IndusConstates.mtmoucompta":{"type":"float"},"IndusTransferesCG.mtmoucompta":{"type":"float"},"RemisesIndus.mtmoucompta":{"type":"float"},"4":"Canton.canton","\/Indus\/view\/#Dossier.id#":{"class":"view"}},"innerTable":["Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Adresse.numcom","Prestation.rolepers","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu "Recherches" > "Par indus"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
						 Case à cocher "Filtrer par date de demande RSA"
						''dtdemrsa'' => ''0'',
						 Du (inclus)
						''dtdemrsa_from'' => ''TAB::-1WEEK'',
						 Au (inclus)
						''dtdemrsa_to'' => ''TAB::NOW'',
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(
					''Situationdossierrsa.etatdosrsa'' => array( ''Z'', 2, 3, 4 )
				),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Situationdossierrsa.etatdosrsa_choice'' => ''1'',
					''Situationdossierrsa.etatdosrsa'' => array( ''Z'', 2, 3, 4 )
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Personne.nom_complet_court'',
					''Dossier.typeparte'',
					''Situationdossierrsa.etatdosrsa'',
					''Indu.moismoucompta'' => array( ''type'' => ''date'', ''format'' => ''%B %Y'' ),
					''IndusConstates.mtmoucompta'' => array( ''type'' => ''float'' ),
					''IndusTransferesCG.mtmoucompta'' => array( ''type'' => ''float'' ),
					''RemisesIndus.mtmoucompta'' => array( ''type'' => ''float'' ),
					''Canton.canton'',
					''Indusview#Dossier.id#'' => array( ''class'' => ''view'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Personne.dtnai'',
					''Dossier.matricule'',
					''Personne.nir'',
					''Adresse.codepos'',
					''Adresse.numcom'',
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 16, current_timestamp, current_timestamp),
(313, 'ConfigurableQuery.Indus.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":{"Situationdossierrsa.etatdosrsa":["Z",2,3,4]},"skip":[]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["Z",2,3,4]},"conditions":[],"order":[]},"results":{"fields":{"0":"Dossier.numdemrsa","1":"Dossier.matricule","2":"Personne.qual","3":"Personne.nom","4":"Personne.prenom","5":"Adresse.numvoie","6":"Adresse.libtypevoie","7":"Adresse.nomvoie","8":"Adresse.complideadr","9":"Adresse.compladr","10":"Adresse.codepos","11":"Adresse.nomcom","12":"Dossier.typeparte","13":"Situationdossierrsa.etatdosrsa","Indu.moismoucompta":{"type":"date","format":"%B %Y"},"IndusConstates.mtmoucompta":{"type":"float"},"IndusTransferesCG.mtmoucompta":{"type":"float"},"RemisesIndus.mtmoucompta":{"type":"float"},"14":"Structurereferenteparcours.lib_struc","15":"Referentparcours.nom_complet","16":"Canton.canton"}},"ini_set":[]}', 'Export CSV,  menu "Recherches" > "Par indus"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Indus.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Indus.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Personne.qual'',
					''Personne.nom'',
					''Personne.prenom'',
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Dossier.typeparte'',
					''Situationdossierrsa.etatdosrsa'',
					''Indu.moismoucompta'' => array( ''type'' => ''date'', ''format'' => ''%B %Y'' ),
					''IndusConstates.mtmoucompta'' => array( ''type'' => ''float'' ),
					''IndusTransferesCG.mtmoucompta'' => array( ''type'' => ''float'' ),
					''RemisesIndus.mtmoucompta'' => array( ''type'' => ''float'' ),
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Indus.search.ini_set'' ),
		)', 16, current_timestamp, current_timestamp),
(314, 'Module.Fluxpoleemplois.enabled', 'true', 'Permet de faire apparaître ou non dans le menu "Administration" le
	  sous-menu "Flux Pôle Emploi".

 true', 17, current_timestamp, current_timestamp),
(317, 'ConfigurableQuery.Fluxpoleemplois.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"},"Situationdossierrsa":{"etatdosrsa_choice":"0","etatdosrsa":["0","2","3","4"]}},"accepted":[],"skip":[],"has":{"Contratinsertion":{"Contratinsertion.decision_ci":"V"}}},"query":{"restrict":[],"conditions":[],"order":{"Dossier.dtdemrsa":"DESC","Personne.nom":"ASC"}},"results":{"fields":{"0":"Dossier.numdemrsa","1":"Dossier.dtdemrsa","2":"Personne.nir","3":"Situationdossierrsa.etatdosrsa","4":"Prestation.natprest","5":"Calculdroitrsa.toppersdrodevorsa","6":"Personne.nom_complet_prenoms","7":"Personne.qual","8":"Personne.nom","9":"Personne.prenom","10":"Personne.dtnai","Personne.age":{"label":"Age"},"11":"Adresse.numvoie","12":"Adresse.libtypevoie","13":"Adresse.nomvoie","14":"Adresse.complideadr","15":"Adresse.compladr","16":"Adresse.codepos","17":"Adresse.nomcom","18":"Personne.email","19":"Personne.numfixe","20":"Typeorient.lib_type_orient","21":"Personne.idassedic","22":"Dsp.inscdememploi","23":"Dossier.matricule","24":"Structurereferenteparcours.lib_struc","25":"Referentparcours.nom_complet","26":"Personne.sexe","27":"Dsp.inscdememploi","28":"Dsp.natlog","29":"Dsp.nivetu"}},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Export CSV, menu "Recherches" > "Par Pôle Emploi"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Fluxpoleemplois.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Fluxpoleemplois.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Prestation.natprest'',
					''Calculdroitrsa.toppersdrodevorsa'',
					''Personne.nom_complet_prenoms'',
					''Personne.qual'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Personne.age'' => array( ''label'' => ''Age'' ),
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Personne.email'',
					''Personne.numfixe'',
					''Typeorient.lib_type_orient'',
					''Personne.idassedic'',
					''Dsp.inscdememploi'',
					''Dossier.matricule'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Personne.sexe'',
					''Dsp.inscdememploi'',
					''Dsp.natlog'' ,
					''Dsp.nivetu''
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Fluxpoleemplois.search.ini_set'' ),
		)', 17, current_timestamp, current_timestamp),
(318, 'ConfigurableQuery.Entretiens.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Entretien.dateentretien","1":"Personne.nom_complet_court","2":"Adresse.nomcom","3":"Structurereferente.lib_struc","4":"Referent.nom_complet","5":"Entretien.typeentretien","6":"Objetentretien.name","Entretien.arevoirle":{"format":"%B %Y"},"7":"Canton.canton","8":"\/Entretiens\/index\/#Entretien.personne_id#"},"innerTable":["Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Adresse.numcom","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}', 'Menu "Recherches" > "Par entretiens"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
						 Case à cocher "Filtrer par date de demande RSA"
						''dtdemrsa'' => ''0'',
						 Du (inclus)
						''dtdemrsa_from'' => ''TAB::-1WEEK'',
						 Au (inclus)
						''dtdemrsa_to'' => ''TAB::NOW'',
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Entretien.dateentretien'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Structurereferente.lib_struc'',
					''Referent.nom_complet'',
					''Entretien.typeentretien'',
					''Objetentretien.name'',
					''Entretien.arevoirle'' => array(
						''format'' => ''%B %Y''
					),
					''Canton.canton'',
					''Entretiensindex#Entretien.personne_id#''
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Personne.dtnai'',
					''Dossier.matricule'',
					''Personne.nir'',
					''Adresse.codepos'',
					''Adresse.numcom'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''1024M''
			)
		)', 18, current_timestamp, current_timestamp),
(319, 'ConfigurableQuery.Entretiens.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"results":{"fields":{"0":"Entretien.dateentretien","1":"Personne.nom_complet_court","2":"Dossier.matricule","3":"Adresse.numvoie","4":"Adresse.libtypevoie","5":"Adresse.nomvoie","6":"Adresse.complideadr","7":"Adresse.compladr","8":"Adresse.codepos","9":"Adresse.nomcom","10":"Structurereferente.lib_struc","11":"Referent.nom_complet","12":"Entretien.typeentretien","13":"Objetentretien.name","Entretien.arevoirle":{"format":"%B %Y"},"14":"Referentparcours.nom_complet","15":"Structurereferenteparcours.lib_struc","16":"Canton.canton"}},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}', 'Export CSV,  menu "Recherches" > "Par entretiens"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Entretiens.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Entretiens.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Entretien.dateentretien'',
					''Personne.nom_complet_court'',
					''Dossier.matricule'',
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Structurereferente.lib_struc'',
					''Referent.nom_complet'',
					''Entretien.typeentretien'',
					''Objetentretien.name'',
					''Entretien.arevoirle'' => array(
						''format'' => ''%B %Y''
					),
					''Referentparcours.nom_complet'',
					''Structurereferenteparcours.lib_struc'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Entretiens.search.ini_set'' ),
		)', 18, current_timestamp, current_timestamp),
(320, 'ConfigurableQuery.Dsps.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.matricule","1":"Personne.nom","2":"Personne.prenom","3":"Personne.dtnai","4":"Adresse.nomcom","5":"Canton.canton","6":"Donnees.toppermicondub","7":"Donnees.topmoyloco","Donnees.difdisp":{"type":"list"},"8":"Donnees.nivetu","9":"Donnees.nivdipmaxobt","10":"Donnees.topengdemarechemploi","11":"Actrechromev3.familleromev3","12":"Actrechromev3.domaineromev3","13":"Actrechromev3.metierromev3","14":"Actrechromev3.appellationromev3","15":"Libemploirech66Metier.name","16":"Deractromev3.appellationromev3","17":"Libsecactrech66Secteur.name","18":"Libderact66Metier.name","19":"Donnees.libautrqualipro","20":"Donnees.nb_fichiers_lies","\/Dsps\/view_revs\/#DspRev.id#":{"class":"view","condition":"trim(\"#DspRev.id#\") !== \"\""},"\/Dsps\/view\/#Personne.id#":{"class":"view","condition":"trim(\"#DspRev.id#\") === \"\""}},"innerTable":["Situationdossierrsa.etatdosrsa","Calculdroitrsa.toppersdrodevorsa","Foyer.sitfam","Foyer.nbenfants","Personne.numfixe","Personne.numport","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu "Recherches" > "Par DSPs"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
						 Case à cocher "Filtrer par date de demande RSA"
						''dtdemrsa'' => ''0'',
						 Du (inclus)
						''dtdemrsa_from'' => ''TAB::-1WEEK'',
						 Au (inclus)
						''dtdemrsa_to'' => ''TAB::NOW'',
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Dossier.matricule'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Adresse.nomcom'',
					''Canton.canton'',
					''Donnees.toppermicondub'',  Permis de conduire Cat B
					''Donnees.topmoyloco'',  Moyen de transport Coll. Ou IndiV.
					''Donnees.difdisp'' => array(  Obstacles à une recherche d''emploi
						''type'' => ''list''
					),
					''Donnees.nivetu'',  Niveau d''étude
					''Donnees.nivdipmaxobt'',  Diplomes le plus élevé
					''Donnees.topengdemarechemploi'',  Disponibilité à la recherche d''emploi
					''Actrechromev3.familleromev3'',  Code Famille de l''emploi recherché
					''Actrechromev3.domaineromev3'',  Code Domaine de l''emploi recherché
					''Actrechromev3.metierromev3'',  Code Emploi de l''emploi recherché
					''Actrechromev3.appellationromev3'',  Appellattion de l''emploi recherché (rome V3)
					''Libemploirech66Metier.name'',  Emploi recherché (rome V2)
					''Deractromev3.appellationromev3'',  Appellattion de la derniere activité (rome V3)
					''Libsecactrech66Secteur.name'',  Le secteur d''activité recherché (rome v2)
					''Libderact66Metier.name'',  La derniere activité (rome V2)
					''Donnees.libautrqualipro'',  Qualification ou certificats professionnels
					''Donnees.nb_fichiers_lies'',  Nb Fichiers Liés des dsp
					''Dspsview_revs#DspRev.id#'' => array(
						''class'' => ''view'',
						''condition'' => ''trim("#DspRev.id#") !== ""''
					),
					''Dspsview#Personne.id#'' => array(
						''class'' => ''view'',
						''condition'' => ''trim("#DspRev.id#") === ""''
					)
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Situationdossierrsa.etatdosrsa'',  Position du droit
					''Calculdroitrsa.toppersdrodevorsa'',  Soumis à Droit et Devoir
					''Foyer.sitfam'',  Situation de famille
					''Foyer.nbenfants'',  Nbre d''enfants
					''Personne.numfixe'',  N° téléphone fixe
					''Personne.numport'',  N° téléphone portable
					''Referentparcours.nom_complet'', Nom du référent
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 19, current_timestamp, current_timestamp),
(321, 'ConfigurableQuery.Dsps.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"results":{"fields":{"0":"Dossier.matricule","1":"Personne.nom","2":"Personne.prenom","3":"Personne.dtnai","4":"Situationdossierrsa.etatdosrsa","5":"Calculdroitrsa.toppersdrodevorsa","6":"Foyer.sitfam","7":"Foyer.nbenfants","8":"Personne.numfixe","9":"Personne.numport","10":"Referentparcours.nom_complet","11":"Adresse.nomcom","12":"Canton.canton","13":"Donnees.toppermicondub","14":"Donnees.topmoyloco","Donnees.difdisp":{"type":"list"},"15":"Donnees.nivetu","16":"Donnees.nivdipmaxobt","17":"Donnees.topengdemarechemploi","18":"Actrechromev3.familleromev3","19":"Actrechromev3.domaineromev3","20":"Actrechromev3.metierromev3","21":"Actrechromev3.appellationromev3","22":"Libemploirech66Metier.name","23":"Deractromev3.appellationromev3","24":"Libsecactrech66Secteur.name","25":"Libderact66Metier.name","26":"Donnees.libautrqualipro","27":"Donnees.nb_fichiers_lies"}},"ini_set":[]}', 'Export CSV,  menu "Recherches" > "Par DSPs"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Dsps.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Dsps.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.matricule'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Situationdossierrsa.etatdosrsa'',  Position du droit
					''Calculdroitrsa.toppersdrodevorsa'',  Soumis à Droit et Devoir
					''Foyer.sitfam'',  Situation de famille
					''Foyer.nbenfants'',  Nbre d''enfants
					''Personne.numfixe'',  N° téléphone fixe
					''Personne.numport'',  N° téléphone portable
					''Referentparcours.nom_complet'', Nom du référent
					''Adresse.nomcom'',
					''Canton.canton'',
					''Donnees.toppermicondub'',  Permis de conduire Cat B
					''Donnees.topmoyloco'',  Moyen de transport Coll. Ou IndiV.
					''Donnees.difdisp'' => array(  Obstacles à une recherche d''emploi
						''type'' => ''list''
					),
					''Donnees.nivetu'',  Niveau d''étude
					''Donnees.nivdipmaxobt'',  Diplomes le plus élevé
					''Donnees.topengdemarechemploi'',  Disponibilité à la recherche d''emploi
					''Actrechromev3.familleromev3'',  Code Famille de l''emploi recherché
					''Actrechromev3.domaineromev3'',  Code Domaine de l''emploi recherché
					''Actrechromev3.metierromev3'',  Code Emploi de l''emploi recherché
					''Actrechromev3.appellationromev3'',  Appellattion de l''emploi recherché (rome V3)
					''Libemploirech66Metier.name'',  Emploi recherché (rome V2)
					''Deractromev3.appellationromev3'',  Appellattion de la derniere activité (rome V3)
					''Libsecactrech66Secteur.name'',  Le secteur d''activité recherché (rome v2)
					''Libderact66Metier.name'',  La derniere activité (rome V2)
					''Donnees.libautrqualipro'',  Qualification ou certificats professionnels
					''Donnees.nb_fichiers_lies'',  Nb Fichiers Liés des dsp
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Dsps.search.ini_set'' ),
		)', 19, current_timestamp, current_timestamp),
(285, 'ConfigurableQuery.Dossiers.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":[],"skip":[],"has":{"0":"Cui","Orientstruct":{"Orientstruct.statut_orient":"Orienté"},"Contratinsertion":{"Contratinsertion.decision_ci":"V"},"1":"Personnepcg66"}},"query":{"restrict":[],"conditions":[],"order":["Personne.nom"]},"results":{"fields":["Dossier.matricule","Dossier.numdemrsa","Dossier.dtdemrsa","Personne.nir","Situationdossierrsa.etatdosrsa","Prestation.rolepers","Calculdroitrsa.toppersdrodevorsa","Personne.qual","Personne.nom","Personne.prenom","Personne.nom_complet_prenoms","Personne.dtnai","Adresse.numvoie","Adresse.libtypevoie","Adresse.nomvoie","Adresse.complideadr","Adresse.compladr","Adresse.codepos","Adresse.nomcom","Typeorient.lib_type_orient","Personne.idassedic","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Personne.sexe","Dsp.natlog","Canton.canton"]},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Export CSV, menu "Recherches" > "Par dossier  allocataire"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Dossiers.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Dossiers.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.matricule'',
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Prestation.rolepers'',
					''Calculdroitrsa.toppersdrodevorsa'',
					''Personne.qual'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.nom_complet_prenoms'',  FIXME: nom completcourtprenoms ?
					''Personne.dtnai'',
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Typeorient.lib_type_orient'',
					''Personne.idassedic'',

					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Personne.sexe'',
					''Dsp.natlog'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Dossiers.search.ini_set'' ),
		)', 20, current_timestamp, current_timestamp),
(323, 'ConfigurableQuery.Dossierspcgs66.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personnepcg66.noms_complet","2":"Originepdo.libelle","3":"Typepdo.libelle","4":"Dossier.dtdemrsa","5":"Dossierpcg66.datereceptionpdo","6":"Poledossierpcg66.name","7":"User.nom_complet","8":"Situationpdo.libelles","9":"Statutpdo.libelles","Traitementpcg66.datereception":{"label":"Date de réception des pièces demandées"},"10":"Dossierpcg66.nbpropositions","11":"Decisionpdo.libelle","12":"Decisiondossierpcg66.datepropositiontechnicien","13":"Dossierpcg66.etatdossierpcg","14":"Decisiondossierpcg66.org_id","15":"Decisiondossierpcg66.datetransmissionop","16":"Canton.canton","\/Dossierspcgs66\/ajax_view_decisions\/#Dossierpcg66.id#":{"class":"view ajax-link","msgid":"Voir propositions (#Decisiondossierpcg66.count#)","disabled":"!''#Decisiondossierpcg66.count#''"},"\/Dossierspcgs66\/index\/#Dossierpcg66.foyer_id#":{"class":"view"},"\/Dossierspcgs66\/edit\/#Dossierpcg66.id#":{"class":"edit"}},"innerTable":["Situationdossierrsa.etatdosrsa","Personne.nomcomnai","Personne.dtnai","Adresse.numcom","Personne.nir","Dossier.matricule","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu Recherche de dossiers PCGs


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array( ''Personne.nom'', ''Personne.prenom'' )
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					 ''Personne.nom_complet_court'',  Demandeur rsa
					''Personnepcg66.noms_complet'',  Liste des personnes dans le dossier pcg
					''Originepdo.libelle'',
					''Typepdo.libelle'',
					''Dossier.dtdemrsa'',
					''Dossierpcg66.datereceptionpdo'',
					''Poledossierpcg66.name'',
					''User.nom_complet'',
					''Situationpdo.libelles'',
					''Statutpdo.libelles'',
					''Traitementpcg66.datereception'' => array(''label'' => ''Date de réception des pièces demandées''),
					''Dossierpcg66.nbpropositions'',
					''Decisionpdo.libelle'',
					''Decisiondossierpcg66.datepropositiontechnicien'',
					''Dossierpcg66.etatdossierpcg'',
					''Decisiondossierpcg66.org_id'',
					''Decisiondossierpcg66.datetransmissionop'',
					''Canton.canton'',
					''Dossierspcgs66ajax_view_decisions#Dossierpcg66.id#'' => array(
						''class'' => ''view ajax-link'',
						''msgid'' => ''Voir propositions (#Decisiondossierpcg66.count#)'',
						''disabled'' => "!''#Decisiondossierpcg66.count#''"
					),
					''Dossierspcgs66index#Dossierpcg66.foyer_id#'' => array( ''class'' => ''view'' ),
					''Dossierspcgs66edit#Dossierpcg66.id#'' => array( ''class'' => ''edit'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Situationdossierrsa.etatdosrsa'',
					''Personne.nomcomnai'',
					''Personne.dtnai'',
					''Adresse.numcom'',
					''Personne.nir'',
					''Dossier.matricule'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 21, current_timestamp, current_timestamp),
(324, 'ConfigurableQuery.Dossierspcgs66.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[]},"results":{"fields":["Dossier.numdemrsa","Dossier.dtdemrsa","Dossier.matricule","Personne.qual","Personne.nom_complet_court","Personne.dtnai","Calculdroitrsa.toppersdrodevorsa","Prestation.rolepers","Dossierpcg66.originepdo_id","Dossierpcg66.typepdo_id","Dossierpcg66.created","Dossierpcg66.dateaffectation","Traitementpcg66.created","Traitementpcg66.datereception","Decisiondossierpcg66.datepropositiontechnicien","Decisiondossierpcg66.dateavistechnique","Decisiondossierpcg66.datevalidation","Dossierpcg66.datetransmission","Dossierpcg66.dateimpression","Dossierpcg66.datereceptionpdo","Dossierpcg66.poledossierpcg66_id","User.nom_complet","Decisionpdo.libelle","Dossierpcg66.nbpropositions","Dossierpcg66.etatdossierpcg","Decisiondossierpcg66.org_id","Decisiondossierpcg66.datetransmissionop","Situationpdo.libelles","Statutpdo.libelles","Fichiermodule.nb_fichiers_lies","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Familleromev3.name","Domaineromev3.name","Metierromev3.name","Appellationromev3.name","Categoriemetierromev2.code","Categoriemetierromev2.name","Canton.canton"]},"ini_set":[]}', 'Export CSV,  menu "Recherches" > "Par entretiens"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Dossier.matricule'',
					''Personne.qual'',
					''Personne.nom_complet_court'',
					''Personne.dtnai'',
					''Calculdroitrsa.toppersdrodevorsa'',
					''Prestation.rolepers'',
					''Dossierpcg66.originepdo_id'',
					''Dossierpcg66.typepdo_id'',
					''Dossierpcg66.created'',
					''Dossierpcg66.dateaffectation'',
					''Traitementpcg66.created'',
					''Traitementpcg66.datereception'',
					''Decisiondossierpcg66.datepropositiontechnicien'',
					''Decisiondossierpcg66.dateavistechnique'',
					''Decisiondossierpcg66.datevalidation'',
					''Dossierpcg66.datetransmission'',
					''Dossierpcg66.dateimpression'',
					''Dossierpcg66.datereceptionpdo'',
					''Dossierpcg66.poledossierpcg66_id'',
					''User.nom_complet'',
					''Decisionpdo.libelle'',
					''Dossierpcg66.nbpropositions'',
					''Dossierpcg66.etatdossierpcg'',
					''Decisiondossierpcg66.org_id'',
					''Decisiondossierpcg66.datetransmissionop'',
					''Situationpdo.libelles'',
					''Statutpdo.libelles'',
					''Fichiermodule.nb_fichiers_lies'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Familleromev3.name'',
					''Domaineromev3.name'',
					''Metierromev3.name'',
					''Appellationromev3.name'',
					''Categoriemetierromev2.code'',
					''Categoriemetierromev2.name'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.search.ini_set'' ),
		)', 21, current_timestamp, current_timestamp),
(325, 'ConfigurableQuery.Dossierspcgs66.search_affectes', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":{"Dossierpcg66.etatdossierpcg":"attinstr"},"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet_court","2":"Adresse.nomcom","3":"Dossierpcg66.datereceptionpdo","4":"Typepdo.libelle","5":"Originepdo.libelle","6":"Dossierpcg66.orgpayeur","7":"Serviceinstructeur.lib_service","8":"Dossierpcg66.poledossierpcg66_id","9":"Dossierpcg66.user_id","10":"Dossierpcg66.dateaffectation","11":"Canton.canton","\/Dossierspcgs66\/index\/#Dossierpcg66.foyer_id#":{"class":"view"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu Recherche de dossiers PCGs


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Dossierpcg66.etatdossierpcg'' => ''attinstr''
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Dossierpcg66.datereceptionpdo'',
					''Typepdo.libelle'',
					''Originepdo.libelle'',
					''Dossierpcg66.orgpayeur'',
					''Serviceinstructeur.lib_service'',
					''Dossierpcg66.poledossierpcg66_id'',
					''Dossierpcg66.user_id'',
					''Dossierpcg66.dateaffectation'',
					''Canton.canton'',
					''Dossierspcgs66index#Dossierpcg66.foyer_id#'' => array( ''class'' => ''view'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(),
		)', 21, current_timestamp, current_timestamp),
(334, 'ConfigurableQuery.Dossierspcgs66.exportcsv_atransmettre', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"results":{"fields":["Dossier.numdemrsa","Dossier.matricule","Personne.nom_complet_court","Adresse.nomcom","Dossierpcg66.datereceptionpdo","Typepdo.libelle","Originepdo.libelle","Dossierpcg66.orgpayeur","Serviceinstructeur.lib_service","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.cohorte_atransmettre.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.cohorte_atransmettre.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Dossierpcg66.datereceptionpdo'',
					''Typepdo.libelle'',
					''Originepdo.libelle'',
					''Dossierpcg66.orgpayeur'',
					''Serviceinstructeur.lib_service'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				),
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.cohorte_atransmettre.ini_set'' ),
		)', 21, current_timestamp, current_timestamp),
(326, 'ConfigurableQuery.Dossierspcgs66.exportcsv_affectes', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":{"Dossierpcg66.etatdossierpcg":"attinstr"},"conditions":[],"order":[]},"results":{"fields":["Dossier.numdemrsa","Dossier.matricule","Personne.nom_complet_court","Adresse.nomcom","Dossierpcg66.datereceptionpdo","Typepdo.libelle","Originepdo.libelle","Dossierpcg66.orgpayeur","Serviceinstructeur.lib_service","Dossierpcg66.poledossierpcg66_id","Dossierpcg66.user_id","Dossierpcg66.dateaffectation","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV,  menu "Recherches" > "Par entretiens"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.search_affectes.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.search_affectes.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Dossierpcg66.datereceptionpdo'',
					''Typepdo.libelle'',
					''Originepdo.libelle'',
					''Dossierpcg66.orgpayeur'',
					''Serviceinstructeur.lib_service'',
					''Dossierpcg66.poledossierpcg66_id'',
					''Dossierpcg66.user_id'',
					''Dossierpcg66.dateaffectation'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.search_affectes.ini_set'' ),
		)', 21, current_timestamp, current_timestamp),
(327, 'ConfigurableQuery.Dossierspcgs66.cohorte_imprimer', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet_court","2":"Adresse.nomcom","3":"Dossierpcg66.datereceptionpdo","4":"Typepdo.libelle","5":"Originepdo.libelle","6":"Dossierpcg66.orgpayeur","7":"Serviceinstructeur.lib_service","8":"Dossierpcg66.poledossierpcg66_id","9":"Dossierpcg66.user_id","10":"Dossierpcg66.dateaffectation","11":"Decisiondossierpcg66.decisionpdo_id","12":"Canton.canton","\/Dossierspcgs66\/index\/#Dossierpcg66.foyer_id#":{"class":"view"},"\/Dossierspcgs66\/edit\/#Dossierpcg66.id#":{"class":"edit"},"\/Dossierspcgs66\/imprimer\/#Dossierpcg66.id#\/#Decisiondossierpcg66.id#":{"class":"print"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu Recherche de dossiers PCGs


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Dossierpcg66.datereceptionpdo'',
					''Typepdo.libelle'',
					''Originepdo.libelle'',
					''Dossierpcg66.orgpayeur'',
					''Serviceinstructeur.lib_service'',
					''Dossierpcg66.poledossierpcg66_id'',
					''Dossierpcg66.user_id'',
					''Dossierpcg66.dateaffectation'',
					''Decisiondossierpcg66.decisionpdo_id'',
					''Canton.canton'',
					''Dossierspcgs66index#Dossierpcg66.foyer_id#'' => array( ''class'' => ''view'' ),
					''Dossierspcgs66edit#Dossierpcg66.id#'' => array( ''class'' => ''edit'' ),
					''Dossierspcgs66imprimer#Dossierpcg66.id##Decisiondossierpcg66.id#'' => array( ''class'' => ''print'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(),
		)', 21, current_timestamp, current_timestamp),
(328, 'ConfigurableQuery.Dossierspcgs66.exportcsv_imprimer', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"results":{"fields":["Dossier.numdemrsa","Dossier.matricule","Personne.nom_complet_court","Adresse.nomcom","Dossierpcg66.datereceptionpdo","Typepdo.libelle","Originepdo.libelle","Dossierpcg66.orgpayeur","Serviceinstructeur.lib_service","Dossierpcg66.poledossierpcg66_id","Dossierpcg66.user_id","Dossierpcg66.dateaffectation","Decisiondossierpcg66.decisionpdo_id","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV,  menu "Recherches" > "Par entretiens"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.cohorte_imprimer.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.cohorte_imprimer.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Dossierpcg66.datereceptionpdo'',
					''Typepdo.libelle'',
					''Originepdo.libelle'',
					''Dossierpcg66.orgpayeur'',
					''Serviceinstructeur.lib_service'',
					''Dossierpcg66.poledossierpcg66_id'',
					''Dossierpcg66.user_id'',
					''Dossierpcg66.dateaffectation'',
					''Decisiondossierpcg66.decisionpdo_id'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.cohorte_imprimer.ini_set'' ),
		)', 21, current_timestamp, current_timestamp),
(329, 'ConfigurableQuery.Dossierspcgs66.search_gestionnaire', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Dossierpcg66":{"etatdossierpcg":["attinstr","decisionvalid","decisionnonvalid"]}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personnepcg66.noms_complet","2":"Dossierpcg66.originepdo_id","3":"Dossierpcg66.typepdo_id","4":"Traitementpcg66.dateecheances","5":"Dossierpcg66.user_id","6":"Dossierpcg66.nbpropositions","7":"Personnepcg66.nbtraitements","8":"Dossierpcg66.listetraitements","9":"Dossierpcg66.etatdossierpcg","10":"Decisiondossierpcg66.decisionpdo_id","11":"Situationpdo.libelles","12":"Statutpdo.libelles","13":"Fichiermodule.nb_fichiers_lies","14":"Dossier.locked","15":"Canton.canton","\/Dossierspcgs66\/index\/#Dossierpcg66.foyer_id#":{"class":"view"},"\/Dossierspcgs66\/edit\/#Dossierpcg66.id#":{"class":"edit"}},"innerTable":["Situationdossierrsa.etatdosrsa","Personne.nomcomnai","Personne.dtnai","Adresse.numcom","Personne.nir","Dossier.matricule","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu Gestionnaire de dossiers PCG


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					),
					''Dossierpcg66'' => array(
						''etatdossierpcg'' => array(
							''attinstr'',  En attente d''instruction
							''decisionvalid'',  Décision validée
							''decisionnonvalid'',  Décision validée
						),
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					 ''Personne.nom_complet_court'',  Demandeur rsa
					''Personnepcg66.noms_complet'',  Liste des personnes dans le dossier pcg
					''Dossierpcg66.originepdo_id'',
					''Dossierpcg66.typepdo_id'',
					''Traitementpcg66.dateecheances'',
					''Dossierpcg66.user_id'',
					''Dossierpcg66.nbpropositions'',
					''Personnepcg66.nbtraitements'',
					''Dossierpcg66.listetraitements'',
					''Dossierpcg66.etatdossierpcg'',
					''Decisiondossierpcg66.decisionpdo_id'',
					''Situationpdo.libelles'',
					''Statutpdo.libelles'',
					''Fichiermodule.nb_fichiers_lies'',
					''Dossier.locked'',
					''Canton.canton'',
					''Dossierspcgs66index#Dossierpcg66.foyer_id#'' => array( ''class'' => ''view'' ),
					''Dossierspcgs66edit#Dossierpcg66.id#'' => array( ''class'' => ''edit'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Situationdossierrsa.etatdosrsa'',
					''Personne.nomcomnai'',
					''Personne.dtnai'',
					''Adresse.numcom'',
					''Personne.nir'',
					''Dossier.matricule'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 21, current_timestamp, current_timestamp),
(336, 'ConfigurableQuery.Dossierspcgs66.exportcsv_heberge', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Adresse":{"heberge":"1"}},"accepted":{"Requestmanager.name":["Cohorte de tag"]},"skip":{"0":"Situationdossierrsa.etatdosrsa_choice","1":"Situationdossierrsa.etatdosrsa","Detailcalculdroitrsa.natpf_choice":"1","2":"Detailcalculdroitrsa.natpf","3":"Calculdroitrsa.toppersdrodevorsa"},"has":{"0":"Cui","Orientstruct":{"Orientstruct.statut_orient":"Orienté"},"Contratinsertion":{"Contratinsertion.decision_ci":"V"},"1":"Personnepcg66"}},"query":{"restrict":{"Tag.valeurtag_id":"2","Prestation.rolepers":"DEM","Adresse.heberge":"1","Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["2"],"Detailcalculdroitrsa.natpf_choice":"1","Detailcalculdroitrsa.natpf":["RSD","RSI"],"Calculdroitrsa.toppersdrodevorsa":"1"},"conditions":[],"order":[]},"results":{"fields":{"0":"Dossier.matricule","1":"Dossier.matricule","2":"Personne.nom_complet_prenoms","3":"Detailcalculdroitrsa.mtrsavers","Foyer.nb_enfants":{"options":[]},"4":"Adresse.nomcom","5":"Adressefoyer.dtemm","6":"Structurereferenteparcours.lib_struc","7":"Referentparcours.nom_complet","8":"Canton.canton"}},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.cohorte_heberge.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.cohorte_heberge.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.matricule'',
					''Dossier.matricule'',
					''Personne.nom_complet_prenoms'',
					''Detailcalculdroitrsa.mtrsavers'',
					''Foyer.nb_enfants'' => array( ''options'' => array() ),
					''Adresse.nomcom'',
					''Adressefoyer.dtemm'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				),
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.cohorte_heberge.ini_set'' ),
		)', 21, current_timestamp, current_timestamp),
(330, 'ConfigurableQuery.Dossierspcgs66.exportcsv_gestionnaire', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[]},"results":{"fields":["Dossier.numdemrsa","Dossier.matricule","Personne.nom_complet_court","Dossierpcg66.originepdo_id","Dossierpcg66.typepdo_id","Dossierpcg66.datereceptionpdo","User.nom_complet","Dossierpcg66.nbpropositions","Personnepcg66.nbtraitements","Dossierpcg66.listetraitements","Dossierpcg66.etatdossierpcg","Fichiermodule.nb_fichiers_lies","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV,  menu "Recherches" > "Par entretiens"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Personne.nom_complet_court'',
					''Dossierpcg66.originepdo_id'',
					''Dossierpcg66.typepdo_id'',
					''Dossierpcg66.datereceptionpdo'',
					''User.nom_complet'',
					''Dossierpcg66.nbpropositions'',
					''Personnepcg66.nbtraitements'',
					''Dossierpcg66.listetraitements'',
					''Dossierpcg66.etatdossierpcg'',
					''Fichiermodule.nb_fichiers_lies'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.search.ini_set'' ),
		)', 21, current_timestamp, current_timestamp),
(331, 'ConfigurableQuery.Dossierspcgs66.cohorte_enattenteaffectation', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet_court","2":"Adresse.nomcom","3":"Dossierpcg66.datereceptionpdo","4":"Typepdo.libelle","5":"Originepdo.libelle","6":"Dossierpcg66.orgpayeur","7":"Serviceinstructeur.lib_service","8":"Canton.canton","\/Dossierspcgs66\/index\/#Dossierpcg66.foyer_id#":{"class":"view"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu Dossiers PCGs en attente d''affectation


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Dossierpcg66.datereceptionpdo'',
					''Typepdo.libelle'',
					''Originepdo.libelle'',
					''Dossierpcg66.orgpayeur'',
					''Serviceinstructeur.lib_service'',
					''Canton.canton'',
					''Dossierspcgs66index#Dossierpcg66.foyer_id#'' => array( ''class'' => ''view'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 21, current_timestamp, current_timestamp),
(332, 'ConfigurableQuery.Dossierspcgs66.exportcsv_enattenteaffectation', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"results":{"fields":["Dossier.numdemrsa","Dossier.matricule","Personne.nom_complet_court","Adresse.nomcom","Dossierpcg66.datereceptionpdo","Typepdo.libelle","Originepdo.libelle","Dossierpcg66.orgpayeur","Serviceinstructeur.lib_service","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.cohorte_enattenteaffectation.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.cohorte_enattenteaffectation.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Dossierpcg66.datereceptionpdo'',
					''Typepdo.libelle'',
					''Originepdo.libelle'',
					''Dossierpcg66.orgpayeur'',
					''Serviceinstructeur.lib_service'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				),
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.cohorte_enattenteaffectation.ini_set'' ),
		)', 21, current_timestamp, current_timestamp),
(333, 'ConfigurableQuery.Dossierspcgs66.cohorte_atransmettre', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet_court","2":"Adresse.nomcom","3":"Dossierpcg66.datereceptionpdo","4":"Typepdo.libelle","5":"Originepdo.libelle","6":"Dossierpcg66.orgpayeur","7":"Serviceinstructeur.lib_service","8":"Canton.canton","\/Dossierspcgs66\/index\/#Dossierpcg66.foyer_id#":{"class":"view"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu Dossiers PCGs en attente d''affectation


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Dossierpcg66.datereceptionpdo'',
					''Typepdo.libelle'',
					''Originepdo.libelle'',
					''Dossierpcg66.orgpayeur'',
					''Serviceinstructeur.lib_service'',
					''Canton.canton'',
					''Dossierspcgs66index#Dossierpcg66.foyer_id#'' => array( ''class'' => ''view'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 21, current_timestamp, current_timestamp),
(357, 'ConfigurableQuery.Contratsinsertion.search_valides', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":{"0":"Contratinsertion.decision_ci IS NOT NULL","Contratinsertion.decision_ci !=":"E"},"order":["Contratinsertion.df_ci"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet_court","2":"Adresse.nomcom","3":"Contratinsertion.dd_ci","4":"Contratinsertion.df_ci","5":"Contratinsertion.decision_ci","6":"Contratinsertion.datevalidation_ci","7":"Contratinsertion.observ_ci","8":"Contratinsertion.forme_ci","9":"Contratinsertion.positioncer","10":"Canton.canton","\/Contratsinsertion\/index\/#Contratinsertion.personne_id#":{"class":"view external"},"\/Contratsinsertion\/ficheliaisoncer\/#Contratinsertion.id#":{"class":"print","id":"ficheliaisoncer_#Contratinsertion.id#","positioncer":"#Contratinsertion.positioncer#","decision_ci":"#Contratinsertion.decision_ci#"},"\/Contratsinsertion\/notifbenef\/#Contratinsertion.id#":{"class":"print","id":"notifbenef_#Contratinsertion.id#"},"\/Contratsinsertion\/notificationsop\/#Contratinsertion.id#":{"class":"print","id":"notificationsop_#Contratinsertion.id#"},"\/Contratsinsertion\/impression\/#Contratinsertion.id#":{"class":"print","id":"impression_#Contratinsertion.id#"}},"innerTable":["Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Adresse.numcom","Prestation.rolepers","Situationdossierrsa.etatdosrsa","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
					),
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(
					''Contratinsertion.decision_ci IS NOT NULL'',
					''Contratinsertion.decision_ci !='' => ''E'',
				),
				 2.3 Tri par défaut
				''order'' => array( ''Contratinsertion.df_ci'' )
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Contratinsertion.dd_ci'',
					''Contratinsertion.df_ci'',
					''Contratinsertion.decision_ci'',
					''Contratinsertion.datevalidation_ci'',
					''Contratinsertion.observ_ci'',
					''Contratinsertion.forme_ci'',
					''Contratinsertion.positioncer'',
					''Canton.canton'',
					''Contratsinsertionindex#Contratinsertion.personne_id#'' => array( ''class'' => ''view external'' ),
					''Contratsinsertionficheliaisoncer#Contratinsertion.id#'' => array(
						''class'' => ''print'',
						''id'' => ''ficheliaisoncer_#Contratinsertion.id#'',
						''positioncer'' => ''#Contratinsertion.positioncer#'',
						''decision_ci'' => ''#Contratinsertion.decision_ci#'',
					),
					''Contratsinsertionnotifbenef#Contratinsertion.id#'' => array(
						''class'' => ''print'',
						''id'' => ''notifbenef_#Contratinsertion.id#'',
					),
					''Contratsinsertionnotificationsop#Contratinsertion.id#'' => array(
						''class'' => ''print'',
						''id'' => ''notificationsop_#Contratinsertion.id#'',
					),
					''Contratsinsertionimpression#Contratinsertion.id#'' => array(
						''class'' => ''print'',
						''id'' => ''impression_#Contratinsertion.id#'',
					),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Personne.dtnai'',
					''Dossier.matricule'',
					''Personne.nir'',
					''Adresse.codepos'',
					''Adresse.numcom'',
					''Prestation.rolepers'',
					''Situationdossierrsa.etatdosrsa'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''512M''
			)
		)', 26, current_timestamp, current_timestamp),
(338, 'ConfigurableQuery.Dossierspcgs66.exportcsv_rsamajore', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Adresse":{"heberge":"1"}},"accepted":{"Requestmanager.name":["Cohorte de tag"]},"skip":{"0":"Situationdossierrsa.etatdosrsa_choice","1":"Situationdossierrsa.etatdosrsa","Detailcalculdroitrsa.natpf_choice":"1","2":"Detailcalculdroitrsa.natpf","3":"Calculdroitrsa.toppersdrodevorsa"},"has":{"0":"Cui","Orientstruct":{"Orientstruct.statut_orient":"Orienté"},"Contratinsertion":{"Contratinsertion.decision_ci":"V"},"1":"Personnepcg66"}},"query":{"restrict":{"Tag.valeurtag_id":"3","Prestation.rolepers":"DEM","Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["2"],"Detailcalculdroitrsa.natpf_choice":"1","Detailcalculdroitrsa.natpf":["RSI"],"Calculdroitrsa.toppersdrodevorsa":"1"},"conditions":[],"order":[]},"results":{"fields":{"0":"Dossier.matricule","1":"Dossier.matricule","2":"Personne.nom_complet_prenoms","3":"Detailcalculdroitrsa.mtrsavers","Foyer.nb_enfants":{"options":[]},"4":"Adresse.nomcom","5":"Foyer.ddsitfam","6":"Structurereferenteparcours.lib_struc","7":"Referentparcours.nom_complet","8":"Canton.canton"}},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.cohorte_rsamajore.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.cohorte_rsamajore.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.matricule'',
					''Dossier.matricule'',
					''Personne.nom_complet_prenoms'',
					''Detailcalculdroitrsa.mtrsavers'',
					''Foyer.nb_enfants'' => array( ''options'' => array() ),
					''Adresse.nomcom'',
					''Foyer.ddsitfam'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				),
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Dossierspcgs66.cohorte_rsamajore.ini_set'' ),
		)', 21, current_timestamp, current_timestamp),
(339, 'ConfigurableQuery.Demenagementshorsdpts.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Pagination":{"nombre_total":"0"},"Situationdossierrsa":{"etatdosrsa_choice":"1","etatdosrsa":["2","3","4"]}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[{" ":{"colspan":2}},{"Adresse de rang 01":{"colspan":2}},{"Adresse de rang 02":{"colspan":2}},{"Adresse de rang 03":{"colspan":2}},{" ":[]},{" ":{"class":"action noprint"}}],"fields":{"0":"Dossier.matricule","1":"Personne.nom_complet_court","2":"Adressefoyer.dtemm","3":"Adresse.localite","Adressefoyer2.dtemm":{"type":"date"},"4":"Adresse2.localite","Adressefoyer3.dtemm":{"type":"date"},"5":"Adresse3.localite","Dossier.locked":{"type":"boolean","class":"dossier_locked"},"\/Dossiers\/view\/#Dossier.id#":{"class":"view"}},"innerTable":[]},"ini_set":[]}', 'Menu "Recherches" > "Par allocataires sortants" > "Hors département"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						''dernier'' => ''1'',
					),
					''Pagination'' => array(
						''nombre_total'' => ''0''
					),
					''Situationdossierrsa'' => array(
						''etatdosrsa_choice'' => ''1'',
						''etatdosrsa'' => array( ''2'', ''3'', ''4'' )
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(
					array( '' '' => array( ''colspan'' => 2 ) ),
					array( ''Adresse de rang 01'' => array( ''colspan'' => 2 ) ),
					array( ''Adresse de rang 02'' => array( ''colspan'' => 2 ) ),
					array( ''Adresse de rang 03'' => array( ''colspan'' => 2 ) ),
					array( '' '' => array() ),
					array( '' '' => array( ''class'' => ''action noprint'' ) ),
				),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.matricule'',
					''Personne.nom_complet_court'',
					''Adressefoyer.dtemm'',
					''Adresse.localite'',
					''Adressefoyer2.dtemm'' => array( ''type'' => ''date'' ),
					''Adresse2.localite'',
					''Adressefoyer3.dtemm'' => array( ''type'' => ''date'' ),
					''Adresse3.localite'',
					''Dossier.locked'' => array(
						''type'' => ''boolean'',
						''class'' => ''dossier_locked''
					),
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''view'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 22, current_timestamp, current_timestamp),
(340, 'ConfigurableQuery.Demenagementshorsdpts.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Pagination":{"nombre_total":"0"},"Situationdossierrsa":{"etatdosrsa_choice":"1","etatdosrsa":["2","3","4"]}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"results":{"fields":{"0":"Dossier.matricule","1":"Personne.nom_complet_court","2":"Adressefoyer.dtemm","3":"Adresse.localite","Adressefoyer2.dtemm":{"type":"date"},"4":"Adresse2.localite","Adressefoyer3.dtemm":{"type":"date"},"5":"Adresse3.localite"}},"ini_set":[]}', 'Export CSV,  menu "Recherches" > "Par allocataires sortants" > "Hors département"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Demenagementshorsdpts.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Demenagementshorsdpts.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.matricule'',
					''Personne.nom_complet_court'',
					''Adressefoyer.dtemm'',
					''Adresse.localite'',
					''Adressefoyer2.dtemm'' => array( ''type'' => ''date'' ),
					''Adresse2.localite'',
					''Adressefoyer3.dtemm'' => array( ''type'' => ''date'' ),
					''Adresse3.localite'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Demenagementshorsdpts.search.ini_set'' ),
		)', 22, current_timestamp, current_timestamp),
(341, 'ConfigurableQuery.Defautsinsertionseps66.search_noninscrits', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Situationdossierrsa":{"etatdosrsa":["Z","2","3","4"]}},"accepted":{"Situationdossierrsa.etatdosrsa":["Z","2","3","4"]},"skip":[],"has":[]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["Z","2","3","4"]},"conditions":[],"order":["Orientstruct.date_valid"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Personne.nom","1":"Personne.prenom","2":"Personne.dtnai","3":"Orientstruct.date_valid","Foyer.enerreur":{"type":"string","class":"foyer_enerreur"},"4":"Situationdossierrsa.etatdosrsa","5":"Canton.canton","\/Bilansparcours66\/add\/#Personne.id#\/Bilanparcours66__examenauditionpe:noninscriptionpe":{"class":"add external"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu "Recherches"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					),
					''Situationdossierrsa'' => array(
						''etatdosrsa'' => array(''Z'', ''2'', ''3'', ''4'')
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(
					''Situationdossierrsa.etatdosrsa'' => array(''Z'', ''2'', ''3'', ''4'')
				),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Situationdossierrsa.etatdosrsa_choice'' => ''1'',
					''Situationdossierrsa.etatdosrsa'' => array(''Z'', ''2'', ''3'', ''4'')
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Orientstruct.date_valid'')
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Orientstruct.date_valid'',
					''Foyer.enerreur'' => array( ''type'' => ''string'', ''class'' => ''foyer_enerreur'' ),
					''Situationdossierrsa.etatdosrsa'',
					''Canton.canton'',
					''Bilansparcours66add#Personne.id#Bilanparcours66__examenauditionpe:noninscriptionpe'' => array( ''class'' => ''add external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 23, current_timestamp, current_timestamp),
(342, 'ConfigurableQuery.Defautsinsertionseps66.exportcsv_noninscrits', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Situationdossierrsa":{"etatdosrsa":["Z","2","3","4"]}},"accepted":{"Situationdossierrsa.etatdosrsa":["Z","2","3","4"]},"skip":[],"has":[]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["Z","2","3","4"]},"conditions":[],"order":["Orientstruct.date_valid"]},"results":{"fields":{"0":"Personne.nom","1":"Personne.prenom","2":"Personne.dtnai","3":"Orientstruct.date_valid","Foyer.enerreur":{"type":"string","class":"foyer_enerreur"},"4":"Situationdossierrsa.etatdosrsa","5":"Structurereferenteparcours.lib_struc","6":"Referentparcours.nom_complet","7":"Canton.canton"}},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Defautsinsertionseps66.search_noninscrits.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Defautsinsertionseps66.search_noninscrits.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Orientstruct.date_valid'',
					''Foyer.enerreur'' => array( ''type'' => ''string'', ''class'' => ''foyer_enerreur'' ),
					''Situationdossierrsa.etatdosrsa'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Defautsinsertionseps66.search_noninscrits.ini_set'' ),
		)', 23, current_timestamp, current_timestamp),
(343, 'ConfigurableQuery.Defautsinsertionseps66.search_radies', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Situationdossierrsa":{"etatdosrsa":["Z","2","3","4"]}},"accepted":{"Situationdossierrsa.etatdosrsa":["Z","2","3","4"]},"skip":[],"has":[]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["Z","2","3","4"]},"conditions":[],"order":["Historiqueetatpe.date","Historiqueetatpe.id"]},"results":{"fields":{"0":"Personne.nom","1":"Personne.prenom","2":"Personne.dtnai","3":"Orientstruct.date_valid","Foyer.enerreur":{"type":"string","class":"foyer_enerreur"},"4":"Situationdossierrsa.etatdosrsa","5":"Canton.canton","\/Bilansparcours66\/add\/#Personne.id#\/Bilanparcours66__examenauditionpe:radiationpe":{"class":"add external"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'menu "Recherches"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Defautsinsertionseps66.search_noninscrits.filters'' ),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Situationdossierrsa.etatdosrsa_choice'' => ''1'',
					''Situationdossierrsa.etatdosrsa'' => array(''Z'', ''2'', ''3'', ''4'')
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Historiqueetatpe.date'', ''Historiqueetatpe.id'')
			),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Orientstruct.date_valid'',
					''Foyer.enerreur'' => array( ''type'' => ''string'', ''class'' => ''foyer_enerreur'' ),
					''Situationdossierrsa.etatdosrsa'',
					''Canton.canton'',
					''Bilansparcours66add#Personne.id#Bilanparcours66__examenauditionpe:radiationpe'' => array( ''class'' => ''add external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Defautsinsertionseps66.search_noninscrits.ini_set'' ),
		)', 23, current_timestamp, current_timestamp),
(344, 'ConfigurableQuery.Defautsinsertionseps66.exportcsv_radies', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Situationdossierrsa":{"etatdosrsa":["Z","2","3","4"]}},"accepted":{"Situationdossierrsa.etatdosrsa":["Z","2","3","4"]},"skip":[],"has":[]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["Z","2","3","4"]},"conditions":[],"order":["Historiqueetatpe.date","Historiqueetatpe.id"]},"results":{"fields":{"0":"Personne.nom","1":"Personne.prenom","2":"Personne.dtnai","3":"Orientstruct.date_valid","Foyer.enerreur":{"type":"string","class":"foyer_enerreur"},"4":"Situationdossierrsa.etatdosrsa","5":"Structurereferenteparcours.lib_struc","6":"Referentparcours.nom_complet","7":"Canton.canton"}},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Defautsinsertionseps66.search_radies.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Defautsinsertionseps66.search_radies.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Orientstruct.date_valid'',
					''Foyer.enerreur'' => array( ''type'' => ''string'', ''class'' => ''foyer_enerreur'' ),
					''Situationdossierrsa.etatdosrsa'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Defautsinsertionseps66.search_radies.ini_set'' ),
		)', 23, current_timestamp, current_timestamp),
(345, 'ConfigurableQuery.Cuis.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.nom","Personne.prenom","Cui.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.matricule","1":"Personne.nom_complet_court","2":"Adresse.nomcom","3":"Cui66.typecontrat","Historiquepositioncui66.created":{"type":"date"},"4":"Partenairecui.raisonsociale","5":"Cui.effetpriseencharge","6":"Cui.finpriseencharge","7":"Decisioncui66.decision","Decisioncui66.datedecision":{"type":"date"},"Emailcui.textmailcui66_id":{"type":"varchar"},"Emailcui.dateenvoi":{"type":"date"},"8":"Canton.canton","\/Cuis\/index\/#Cui.personne_id#":{"class":"view"}},"innerTable":[]},"ini_set":[]}', 'Menu "Recherches"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.nom'', ''Personne.prenom'', ''Cui.id'')
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.matricule'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Cui66.typecontrat'',
					''Historiquepositioncui66.created'' => array( ''type'' => ''date'' ),  Type datetime
					''Partenairecui.raisonsociale'',
					''Cui.effetpriseencharge'',
					''Cui.finpriseencharge'',
					''Decisioncui66.decision'',
					''Decisioncui66.datedecision'' => array( ''type'' => ''date'' ),  Type datetime
					''Emailcui.textmailcui66_id'' => array( ''type'' => ''varchar'' ),  Type integer
					''Emailcui.dateenvoi'' => array( ''type'' => ''date'' ),  Type datetime
					''Canton.canton'',
					''Cuisindex#Cui.personne_id#'' => array( ''class'' => ''view'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array()
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 24, current_timestamp, current_timestamp),
(346, 'ConfigurableQuery.Cuis.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.nom","Personne.prenom","Cui.id"]},"results":{"fields":{"0":"Dossier.matricule","1":"Personne.nom_complet_court","2":"Adresse.nomcom","3":"Cui.positioncui66","4":"Cui.typecontrat","Historiquepositioncui66.created":{"type":"date"},"5":"Partenairecui.raisonsociale","6":"Partenairecui.siret","7":"Partenairecui.statut","8":"Cui.effetpriseencharge","9":"Cui.finpriseencharge","10":"Decisioncui66.decision","Decisioncui66.datedecision":{"type":"date"},"Emailcui.textmailcui66_id":{"type":"varchar"},"Emailcui.dateenvoi":{"type":"date"},"11":"Canton.canton"}},"ini_set":[]}', 'Export CSV,  menu "Recherches"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Cuis.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Cuis.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.matricule'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Cui.positioncui66'',
					''Cui.typecontrat'',
					''Historiquepositioncui66.created'' => array( ''type'' => ''date'' ),
					''Partenairecui.raisonsociale'',
					''Partenairecui.siret'',
					''Partenairecui.statut'',
					''Cui.effetpriseencharge'',
					''Cui.finpriseencharge'',
					''Decisioncui66.decision'',
					''Decisioncui66.datedecision'' => array( ''type'' => ''date'' ),
					''Emailcui.textmailcui66_id'' => array( ''type'' => ''varchar'' ),
					''Emailcui.dateenvoi'' => array( ''type'' => ''date'' ),
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Cuis.search.ini_set'' ),
		)', 24, current_timestamp, current_timestamp),
(347, 'ConfigurableQuery.Creances.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"},"Prestation":{"rolepers":"DEM"}},"accepted":[],"skip":[],"has":{"0":"Cui","Orientstruct":{"Orientstruct.statut_orient":"Orienté"},"Contratinsertion":{"Contratinsertion.decision_ci":"V"},"1":"Personnepcg66"}},"query":{"restrict":{"Prestation.rolepers":"DEM"},"conditions":[],"order":["Personne.nom"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Dossier.dtdemrsa","2":"Personne.nom_complet_court","3":"Creance.natcre","4":"Creance.motiindu","5":"Creance.oriindu","6":"Creance.mtsolreelcretrans","Creance.foyer_id":{"hidden":true},"\/Creances\/index\/#Creance.foyer_id#":{"class":"view"}},"innerTable":{"0":"Dossier.matricule","1":"Personne.dtnai","Adresse.numcom":{"options":[]},"2":"Prestation.rolepers","3":"Structurereferenteparcours.lib_struc","4":"Referentparcours.nom_complet"}},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Menu "Recherches" > "Par créances" > "Par créances"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
						 Case à cocher "Filtrer par date de demande RSA"
						''dtdemrsa'' => ''0'',
						 Du (inclus)
						''dtdemrsa_from'' => ''TAB::-1WEEK'',
						 Au (inclus)
						''dtdemrsa_to'' => ''TAB::NOW'',
					),
					''Prestation'' => array(
						 ''rolepers'' => ''DEM''
					),  Demandeur du RSA
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array(
					''Cui'',
					''Orientstruct'' => array(
						''Orientstruct.statut_orient'' => ''Orienté'',
						 Orientstruct possède des conditions supplémentaire dans le modèle WebrsaRechercheDossier pour le CD66
					),
					''Contratinsertion'' => array(
						''Contratinsertion.decision_ci'' => ''V''
					),
					''Personnepcg66''
				)
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Prestation.rolepers'' => ''DEM'',  Demandeur du RSA
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array( ''Personne.nom'' )
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nom_complet_court'',
					''Creance.natcre'',
					''Creance.motiindu'',
					''Creance.oriindu'',
					''Creance.mtsolreelcretrans'',
					''Creance.foyer_id'' => array (''hidden'' => true),
					''Creancesindex#Creance.foyer_id#'' => array( ''class'' => ''view'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Dossier.matricule'',
					''Personne.dtnai'',
					''Adresse.numcom'' => array(
						''options'' => array()
					),
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''512M''
			)
		)', 25, current_timestamp, current_timestamp),
(348, 'ConfigurableQuery.Creances.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"},"Prestation":{"rolepers":"DEM"}},"accepted":[],"skip":[],"has":{"0":"Cui","Orientstruct":{"Orientstruct.statut_orient":"Orienté"},"Contratinsertion":{"Contratinsertion.decision_ci":"V"},"1":"Personnepcg66"}},"query":{"restrict":{"Prestation.rolepers":"DEM"},"conditions":[],"order":["Personne.nom"]},"results":{"fields":["Dossier.numdemrsa","Dossier.dtdemrsa","Personne.nir","Situationdossierrsa.etatdosrsa","Personne.nom_complet_prenoms","Personne.dtnai","Adresse.numvoie","Adresse.libtypevoie","Adresse.nomvoie","Adresse.complideadr","Adresse.compladr","Adresse.codepos","Adresse.nomcom","Personne.idassedic","Dossier.matricule","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Personne.sexe","Canton.canton"]},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Menu "Recherches" > "Par créances" > "Par créances"
	  Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Creances.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Creances.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Personne.nom_complet_prenoms'',  FIXME: nom completcourtprenoms ?
					''Personne.dtnai'',
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Personne.idassedic'',
					''Dossier.matricule'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Personne.sexe'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Creances.search.ini_set'' ),
		)', 25, current_timestamp, current_timestamp),
(349, 'ConfigurableQuery.Creances.cohorte_preparation', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Prestation":{"rolepers":"DEM"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":["Creance.moismoucompta"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Personne.nom_complet_court","1":"Dossier.numdemrsa","2":"Creance.mtsolreelcretrans","3":"Creance.etat","4":"Creance.natcre","5":"Creance.motiindu","\/Creances\/index\/#Creance.foyer_id#":{"class":"view external"},"\/Titrescreanciers\/index\/#Creance.id#":{"class":"edit external"}},"innerTable":{"0":"Dossier.matricule","1":"Personne.dtnai","Adresse.numcom":{"options":[]},"2":"Prestation.rolepers","3":"Structurereferenteparcours.lib_struc","4":"Referentparcours.nom_complet"}},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Menu "CohortesGestion de Listes" > "Créances" > "Préparation"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
					),
					''Prestation'' => array(
						 ''rolepers'' => ''DEM''
					),  Demandeur du RSA
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(
					''Contratinsertion.forme_ci'' => ''S'',
					''OR'' => array(
						''Contratinsertion.decision_ci IS NULL'',
						''Contratinsertion.decision_ci'' => ''E'',
					)
				),
				 2.3 Tri par défaut
				''order'' => array( ''Creance.moismoucompta'' )
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Personne.nom_complet_court'',
					''Dossier.numdemrsa'',
					''Creance.mtsolreelcretrans'',
					''Creance.etat'',
					''Creance.natcre'',
					''Creance.motiindu'',
					''Creancesindex#Creance.foyer_id#'' => array( ''class'' => ''view external'' ),
					''Titrescreanciersindex#Creance.id#'' => array( ''class'' => ''edit external'' ),
					''Contratsinsertionindex#Contratinsertion.personne_id#'' => array( ''class'' => ''view external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Dossier.matricule'',
					''Personne.dtnai'',
					''Adresse.numcom'' => array(
						''options'' => array()
					),
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''512M''
			)
		)', 25, current_timestamp, current_timestamp),
(359, 'ConfigurableQuery.Changementsadresses.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Adressefoyer.dtemm DESC","Adressefoyer.id ASC"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Personne.nom_complet_court","1":"Dossier.matricule","2":"Adresse.complete","3":"Adressefoyer.dtemm","4":"Referentparcours.nom_complet","5":"Canton.canton","\/Dossiers\/view\/#Dossier.id#":{"class":"view"}},"innerTable":[]},"ini_set":[]}', 'Menu "Recherches"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(
					''Adressefoyer.dtemm DESC'',
					''Adressefoyer.id ASC'',
				)
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Personne.nom_complet_court'',
					''Dossier.matricule'',
					''Adresse.complete'',
					''Adressefoyer.dtemm'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
					''Dossiersview#Dossier.id#'' => array(''class'' => ''view''),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array()
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 27, current_timestamp, current_timestamp),
(350, 'ConfigurableQuery.Creances.exportcsv_preparation', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Prestation":{"rolepers":"DEM"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":["Creance.moismoucompta"]},"results":{"fields":["Dossier.numdemrsa","Dossier.dtdemrsa","Personne.nir","Situationdossierrsa.etatdosrsa","Personne.nom_complet_prenoms","Personne.dtnai","Adresse.numvoie","Adresse.libtypevoie","Adresse.nomvoie","Adresse.complideadr","Adresse.compladr","Adresse.codepos","Adresse.nomcom","Personne.idassedic","Dossier.matricule","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Personne.sexe","Canton.canton"]},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Menu "CohortesGestion de Listes" > "Créances" > "Préparation"
	  Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Creances.cohorte_preparation.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Creances.cohorte_preparation.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
					''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Personne.nom_complet_prenoms'',  FIXME: nom completcourtprenoms ?
					''Personne.dtnai'',
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Personne.idassedic'',
					''Dossier.matricule'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Personne.sexe'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Creances.cohorte_preparation.ini_set'' ),
		)', 25, current_timestamp, current_timestamp),
(351, 'ConfigurableQuery.Contratsinsertion.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"},"Pagination":{"nombre_total":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":["Contratinsertion.df_ci"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Personne.nom_complet_court","1":"Adresse.nomcom","2":"Referent.nom_complet","3":"Dossier.matricule","4":"Contratinsertion.created","5":"Contratinsertion.rg_ci","6":"Contratinsertion.decision_ci","7":"Contratinsertion.datevalidation_ci","8":"Contratinsertion.forme_ci","9":"Contratinsertion.positioncer","10":"Contratinsertion.df_ci","11":"Canton.canton","\/Contratsinsertion\/index\/#Contratinsertion.personne_id#":{"class":"view"}},"innerTable":["Personne.dtnai","Adresse.numcom","Personne.nir","Prestation.rolepers","Situationdossierrsa.etatdosrsa","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Menu "Recherches" > "Par contrats" > "Par CER"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
						 Case à cocher "Filtrer par date de demande RSA"
						''dtdemrsa'' => ''0'',
						 Du (inclus)
						''dtdemrsa_from'' => ''TAB::-1WEEK'',
						 Au (inclus)
						''dtdemrsa_to'' => ''TAB::NOW'',
					),
					 Obtenir le nombre total de résultats
					''Pagination'' => array(
						''nombre_total'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array( ''Contratinsertion.df_ci'' )
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Referent.nom_complet'',
					''Dossier.matricule'',
					''Contratinsertion.created'',
					''Contratinsertion.rg_ci'',
					''Contratinsertion.decision_ci'',
					''Contratinsertion.datevalidation_ci'',
					''Contratinsertion.forme_ci'',
					''Contratinsertion.positioncer'',
					''Contratinsertion.df_ci'',
					''Canton.canton'',
					''Contratsinsertionindex#Contratinsertion.personne_id#'' => array( ''class'' => ''view'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Personne.dtnai'',
					''Adresse.numcom'',
					''Personne.nir'',
					''Prestation.rolepers'',
					''Situationdossierrsa.etatdosrsa'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''512M''
			)
		)', 26, current_timestamp, current_timestamp),
(352, 'ConfigurableQuery.Contratsinsertion.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"},"Pagination":{"nombre_total":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":["Contratinsertion.df_ci"]},"results":{"fields":{"0":"Dossier.numdemrsa","1":"Dossier.matricule","2":"Situationdossierrsa.etatdosrsa","3":"Personne.qual","4":"Personne.nom","5":"Personne.prenom","6":"Dossier.matricule","7":"Adresse.numvoie","8":"Adresse.libtypevoie","9":"Adresse.nomvoie","10":"Adresse.complideadr","11":"Adresse.compladr","12":"Adresse.codepos","13":"Adresse.nomcom","14":"Typeorient.lib_type_orient","15":"Referent.nom_complet","16":"Structurereferente.lib_struc","17":"Contratinsertion.num_contrat","18":"Contratinsertion.positioncer","Contratinsertion.dd_ci":{"type":"date"},"19":"Contratinsertion.duree_engag","Contratinsertion.df_ci":{"type":"date"},"20":"Contratinsertion.decision_ci","Contratinsertion.datevalidation_ci":{"type":"date"},"21":"Structurereferenteparcours.lib_struc","22":"Referentparcours.nom_complet","23":"Canton.canton"}},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Export CSV, menu "Recherches" > "Par contrats" > "Par CER"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Contratsinsertion.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Contratsinsertion.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
					''fields'' => array(
						''Dossier.numdemrsa'',
						''Dossier.matricule'',
						''Situationdossierrsa.etatdosrsa'',
						''Personne.qual'',
						''Personne.nom'',
						''Personne.prenom'',
						''Dossier.matricule'',
						''Adresse.numvoie'',
						''Adresse.libtypevoie'',
						''Adresse.nomvoie'',
						''Adresse.complideadr'',
						''Adresse.compladr'',
						''Adresse.codepos'',
						''Adresse.nomcom'',
						''Typeorient.lib_type_orient'',
						''Referent.nom_complet'',
						''Structurereferente.lib_struc'',
						''Contratinsertion.num_contrat'',
						''Contratinsertion.positioncer'',
						''Contratinsertion.dd_ci'' => array( ''type'' => ''date'' ),
						''Contratinsertion.duree_engag'',
						''Contratinsertion.df_ci'' => array( ''type'' => ''date'' ),
						''Contratinsertion.decision_ci'',
						''Contratinsertion.datevalidation_ci'' => array( ''type'' => ''date'' ),
						''Structurereferenteparcours.lib_struc'',
						''Referentparcours.nom_complet'',
						''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Contratsinsertion.search.ini_set'' ),
		)', 26, current_timestamp, current_timestamp),
(335, 'ConfigurableQuery.Dossierspcgs66.cohorte_heberge', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Adresse":{"heberge":"1"}},"accepted":{"Requestmanager.name":["Cohorte de tag"]},"skip":{"0":"Situationdossierrsa.etatdosrsa_choice","1":"Situationdossierrsa.etatdosrsa","Detailcalculdroitrsa.natpf_choice":"1","2":"Detailcalculdroitrsa.natpf","3":"Calculdroitrsa.toppersdrodevorsa"},"has":{"0":"Cui","Orientstruct":{"Orientstruct.statut_orient":"Orienté"},"Contratinsertion":{"Contratinsertion.decision_ci":"V"},"1":"Personnepcg66"}},"query":{"restrict":{"Tag.valeurtag_id":"2","Prestation.rolepers":"DEM","Adresse.heberge":"1","Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["2"],"Detailcalculdroitrsa.natpf_choice":"1","Detailcalculdroitrsa.natpf":["RSD","RSI"],"Calculdroitrsa.toppersdrodevorsa":"1"},"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.matricule","1":"Personne.nom_complet_prenoms","2":"Detailcalculdroitrsa.mtrsavers","Foyer.nb_enfants":{"options":[]},"3":"Adresse.nomcom","4":"Adressefoyer.dtemm","5":"Canton.canton","\/Dossiers\/view\/#Dossier.id#":{"class":"external"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"cohorte":{"options":{"Tag.calcullimite":{"1":"1 mois","1.5":"1 mois et demi","2":"2 mois","3":"3 mois","6":"6 mois","12":"1 an","24":"2 ans","36":"3 ans"},"Traitementpcg66.typetraitement":{"courrier":"Courrier","dossierarevoir":"Dossier à revoir"}},"values":{"Dossierpcg66.typepdo_id":16,"Dossierpcg66.datereceptionpdo":"TEXT::NOW","Dossierpcg66.serviceinstructeur_id":null,"Dossierpcg66.commentairepiecejointe":null,"Dossierpcg66.dateaffectation":"TEXT::NOW","Situationpdo.Situationpdo":34,"Dossierpcg66.originepdo_id":21,"Dossierpcg66.poledossierpcg66_id":1,"Traitementpcg66.typecourrierpcg66_id":9,"Traitementpcg66.descriptionpdo_id":1,"Traitementpcg66.datereception":null,"Modeletraitementpcg66.modeletypecourrierpcg66_id":82,"Modeletraitementpcg66.montantdatedebut":"TEXT::NOW","Modeletraitementpcg66.montantdatefin":"TEXT::+3MONTHS","Piecemodeletypecourrierpcg66.0_Piecemodeletypecourrierpcg66":131,"Piecemodeletypecourrierpcg66.1_Piecemodeletypecourrierpcg66":132,"Piecemodeletypecourrierpcg66.2_Piecemodeletypecourrierpcg66":129,"Piecemodeletypecourrierpcg66.3_Piecemodeletypecourrierpcg66":133,"Piecemodeletypecourrierpcg66.4_Piecemodeletypecourrierpcg66":128,"Piecemodeletypecourrierpcg66.5_Piecemodeletypecourrierpcg66":130,"Traitementpcg66.serviceinstructeur_id":null,"Traitementpcg66.datedepart":"TEXT::NOW","Tag.valeurtag_id":2}},"ini_set":[],"view":false}', 'array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					),
					''Adresse'' => array(
						''heberge'' => ''1''
					),
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(
					''Requestmanager.name'' => array( ''Cohorte de tag'' ),  Noter nom de catégorie - Cohorte de tag
				),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(
					''Situationdossierrsa.etatdosrsa_choice'',
					''Situationdossierrsa.etatdosrsa'',
					''Detailcalculdroitrsa.natpf_choice'' => ''1'',
					''Detailcalculdroitrsa.natpf'',
					''Calculdroitrsa.toppersdrodevorsa''
				),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array(
					''Cui'',
					''Orientstruct'' => array(
						''Orientstruct.statut_orient'' => ''Orienté'',
						 Orientstruct possède des conditions supplémentaire dans le modèle WebrsaRechercheDossier pour le CD66
					),
					''Contratinsertion'' => array(
						''Contratinsertion.decision_ci'' => ''V''
					),
					''Personnepcg66''
				)
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Tag.valeurtag_id'' => ''2'',  Valeur du tag pour la cohorte hebergé
					''Prestation.rolepers'' => ''DEM'',  Demandeur du RSA
					''Adresse.heberge'' => ''1'',  Conditions pour trouver les allocataires hébergé
					''Situationdossierrsa.etatdosrsa_choice'' => ''1'',
					''Situationdossierrsa.etatdosrsa'' => array(''2''),  Droit ouvert et versable
					''Detailcalculdroitrsa.natpf_choice'' => ''1'',
					''Detailcalculdroitrsa.natpf'' => array(
						''RSD'',  RSA Socle (Financement sur fonds Conseil général)
						''RSI'',  RSA Socle majoré (Financement sur fonds Conseil général)
					),
					''Calculdroitrsa.toppersdrodevorsa'' => ''1'',  Personne soumise à droits et devoirs ? > Oui
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Dossier.matricule'',
					''Personne.nom_complet_prenoms'',
					''Detailcalculdroitrsa.mtrsavers'',
					''Foyer.nb_enfants'' => array( ''options'' => array() ),
					''Adresse.nomcom'',
					''Adressefoyer.dtemm'',
					''Canton.canton'',
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(
					''Tag.calcullimite'' => array(
						''1'' => ''1 mois'',
						''1.5'' => ''1 mois et demi'',  Supporte les nombres de type float
						2 => ''2 mois'',
						3 => ''3 mois'',
						6 => ''6 mois'',
						12 => ''1 an'',
						24 => ''2 ans'',
						36 => ''3 ans'',
					),
					''Traitementpcg66.typetraitement'' => array(
						''courrier'' => ''Courrier'',
						''dossierarevoir'' => ''Dossier à revoir'',
					)
				),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
					''Dossierpcg66.typepdo_id'' => 16,  Position mission PDU-MMR
					''Dossierpcg66.datereceptionpdo'' => ''TAB::NOW-1MONTHS'',  Date de réception du dossier
					''Dossierpcg66.serviceinstructeur_id'' => null,  Service instructeur
					''Dossierpcg66.commentairepiecejointe'' => null,  Commentaire
					''Dossierpcg66.dateaffectation'' => ''TEXT::NOW'',  Date d''affectation
					''Situationpdo.Situationpdo'' => 34,  Cible hébergé
					''Dossierpcg66.originepdo_id'' => 21,  PDU - MMR Cible Imposition
					''Dossierpcg66.poledossierpcg66_id'' => 1,  PDU
					''Traitementpcg66.typecourrierpcg66_id'' => 9,  PDU - Cibles
					''Traitementpcg66.descriptionpdo_id'' => 1,  Courrier à l''allocataire
					''Traitementpcg66.datereception'' => null,  Date de reception
					''Modeletraitementpcg66.modeletypecourrierpcg66_id'' => 82,  Cible hébergé
					''Modeletraitementpcg66.montantdatedebut'' => ''TEXT::NOW'',
					''Modeletraitementpcg66.montantdatefin'' => ''TEXT::+3MONTHS'',
					''Piecemodeletypecourrierpcg66.0_Piecemodeletypecourrierpcg66'' => 131,  Attestation ci-jointe dûment complétée
					''Piecemodeletypecourrierpcg66.1_Piecemodeletypecourrierpcg66'' => 132,  Attestation d''hébergement dûment remplie (en pièce jointe)
					''Piecemodeletypecourrierpcg66.2_Piecemodeletypecourrierpcg66'' => 129,  Avis d''imposition sur les revenus de l''année précédente...
					''Piecemodeletypecourrierpcg66.3_Piecemodeletypecourrierpcg66'' => 133,  Justificatifs de résidence de moins de 3 mois...
					''Piecemodeletypecourrierpcg66.4_Piecemodeletypecourrierpcg66'' => 128,  Pièce d''identité et passeport en intégralité et en cours...
					''Piecemodeletypecourrierpcg66.5_Piecemodeletypecourrierpcg66'' => 130,  Relevés bancaires des 3 derniers mois
					''Traitementpcg66.serviceinstructeur_id'' => null,  Service à contacter (insertion)
					''Traitementpcg66.datedepart'' => ''TEXT::NOW'',  Date de départ (pour le calcul de l''échéance)
					''Tag.valeurtag_id'' => 2,  Valeur du tag
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(),
			 7. Affichage vertical des résultats
			''view'' => false,
		)', 21, current_timestamp, current_timestamp),
(353, 'ConfigurableQuery.Contratsinsertion.cohorte_cersimpleavalider', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":{"Contratinsertion.forme_ci":"S","OR":{"0":"Contratinsertion.decision_ci IS NULL","Contratinsertion.decision_ci":"E"}},"order":["Contratinsertion.df_ci"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet_court","2":"Adresse.nomcom","3":"Referent.nom_complet","4":"Contratinsertion.dd_ci","5":"Contratinsertion.df_ci","6":"Canton.canton","\/Contratsinsertion\/index\/#Contratinsertion.personne_id#":{"class":"view external"}},"innerTable":["Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Adresse.numcom","Contratinsertion.positioncer","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
					),
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(
					''Contratinsertion.forme_ci'' => ''S'',
					''OR'' => array(
						''Contratinsertion.decision_ci IS NULL'',
						''Contratinsertion.decision_ci'' => ''E'',
					)
				),
				 2.3 Tri par défaut
				''order'' => array( ''Contratinsertion.df_ci'' )
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Referent.nom_complet'',
					''Contratinsertion.dd_ci'',
					''Contratinsertion.df_ci'',
					''Canton.canton'',
					''Contratsinsertionindex#Contratinsertion.personne_id#'' => array( ''class'' => ''view external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Personne.dtnai'',
					''Dossier.matricule'',
					''Personne.nir'',
					''Adresse.codepos'',
					''Adresse.numcom'',
					''Contratinsertion.positioncer'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''512M''
			)
		)', 26, current_timestamp, current_timestamp),
(354, 'ConfigurableQuery.Contratsinsertion.exportcsv_cersimpleavalider', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":{"Contratinsertion.forme_ci":"S","OR":{"0":"Contratinsertion.decision_ci IS NULL","Contratinsertion.decision_ci":"E"}},"order":["Contratinsertion.df_ci"]},"results":{"fields":["Dossier.numdemrsa","Personne.nom_complet_court","Adresse.nomcom","Referent.nom_complet","Contratinsertion.dd_ci","Contratinsertion.df_ci","Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Adresse.numcom","Contratinsertion.positioncer","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Contratsinsertion.cohorte_cersimpleavalider.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Contratsinsertion.cohorte_cersimpleavalider.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
					''fields'' => array(
						''Dossier.numdemrsa'',
						''Personne.nom_complet_court'',
						''Adresse.nomcom'',
						''Referent.nom_complet'',
						''Contratinsertion.dd_ci'',
						''Contratinsertion.df_ci'',
						''Personne.dtnai'',
						''Dossier.matricule'',
						''Personne.nir'',
						''Adresse.codepos'',
						''Adresse.numcom'',
						''Contratinsertion.positioncer'',
						''Structurereferenteparcours.lib_struc'',
						''Referentparcours.nom_complet'',
						''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Contratsinsertion.cohorte_cersimpleavalider.ini_set'' ),
		)', 26, current_timestamp, current_timestamp),
(355, 'ConfigurableQuery.Contratsinsertion.cohorte_cerparticulieravalider', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":{"Contratinsertion.forme_ci":"C","OR":{"0":"Contratinsertion.decision_ci IS NULL","Contratinsertion.decision_ci":"E"}},"order":["Contratinsertion.df_ci"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet_court","2":"Adresse.nomcom","3":"Referent.nom_complet","4":"Contratinsertion.dd_ci","5":"Contratinsertion.df_ci","6":"Canton.canton","\/Contratsinsertion\/index\/#Contratinsertion.personne_id#":{"class":"view external"}},"innerTable":["Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Adresse.numcom","Contratinsertion.positioncer","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
					),
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(
					''Contratinsertion.forme_ci'' => ''C'',
					''OR'' => array(
						''Contratinsertion.decision_ci IS NULL'',
						''Contratinsertion.decision_ci'' => ''E'',
					)
				),
				 2.3 Tri par défaut
				''order'' => array( ''Contratinsertion.df_ci'' )
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Referent.nom_complet'',
					''Contratinsertion.dd_ci'',
					''Contratinsertion.df_ci'',
					''Canton.canton'',
					''Contratsinsertionindex#Contratinsertion.personne_id#'' => array( ''class'' => ''view external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Personne.dtnai'',
					''Dossier.matricule'',
					''Personne.nir'',
					''Adresse.codepos'',
					''Adresse.numcom'',
					''Contratinsertion.positioncer'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''512M''
			)
		)', 26, current_timestamp, current_timestamp),
(356, 'ConfigurableQuery.Contratsinsertion.exportcsv_cerparticulieravalider', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":{"Contratinsertion.forme_ci":"C","OR":{"0":"Contratinsertion.decision_ci IS NULL","Contratinsertion.decision_ci":"E"}},"order":["Contratinsertion.df_ci"]},"results":{"fields":["Dossier.numdemrsa","Personne.nom_complet_court","Adresse.nomcom","Referent.nom_complet","Contratinsertion.dd_ci","Contratinsertion.df_ci","Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Adresse.numcom","Contratinsertion.positioncer","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Contratsinsertion.cohorte_cerparticulieravalider.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Contratsinsertion.cohorte_cerparticulieravalider.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
					''fields'' => array(
						''Dossier.numdemrsa'',
						''Personne.nom_complet_court'',
						''Adresse.nomcom'',
						''Referent.nom_complet'',
						''Contratinsertion.dd_ci'',
						''Contratinsertion.df_ci'',
						''Personne.dtnai'',
						''Dossier.matricule'',
						''Personne.nir'',
						''Adresse.codepos'',
						''Adresse.numcom'',
						''Contratinsertion.positioncer'',
						''Structurereferenteparcours.lib_struc'',
						''Referentparcours.nom_complet'',
						''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Contratsinsertion.cohorte_cerparticulieravalider.ini_set'' ),
		)', 26, current_timestamp, current_timestamp),
(358, 'ConfigurableQuery.Contratsinsertion.exportcsv_search_valides', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":{"0":"Contratinsertion.decision_ci IS NOT NULL","Contratinsertion.decision_ci !=":"E"},"order":["Contratinsertion.df_ci"]},"results":{"fields":["Dossier.numdemrsa","Personne.nom_complet_court","Adresse.nomcom","Referent.nom_complet","Structurereferente.lib_struc","Typocontrat.lib_typo","Contratinsertion.dd_ci","Contratinsertion.duree_engag","Contratinsertion.df_ci","Contratinsertion.decision_ci","Contratinsertion.datevalidation_ci","Contratinsertion.current_action","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Contratsinsertion.search_valides.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Contratsinsertion.search_valides.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Referent.nom_complet'',
					''Structurereferente.lib_struc'',
					''Typocontrat.lib_typo'',
					''Contratinsertion.dd_ci'',
					''Contratinsertion.duree_engag'',
					''Contratinsertion.df_ci'',
					''Contratinsertion.decision_ci'',
					''Contratinsertion.datevalidation_ci'',
					''Contratinsertion.current_action'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Contratsinsertion.search_valides.ini_set'' ),
		)', 26, current_timestamp, current_timestamp),
(360, 'ConfigurableQuery.Changementsadresses.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Adressefoyer.dtemm DESC","Adressefoyer.id ASC"]},"results":{"fields":["Personne.nom_complet_court","Dossier.matricule","Adresse.complete","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV,  menu "Recherches"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Changementsadresses.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Changementsadresses.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Personne.nom_complet_court'',
					''Dossier.matricule'',
					''Adresse.complete'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Changementsadresses.search.ini_set'' ),
		)', 27, current_timestamp, current_timestamp),
(361, 'ConfigurableQuery.Bilansparcours66.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Bilanparcours66.datebilan"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Bilanparcours66.datebilan","2":"Personne.nom_complet_court","3":"Structurereferente.lib_struc","4":"Referent.nom_complet","5":"Bilanparcours66.proposition","6":"Bilanparcours66.positionbilan","7":"Bilanparcours66.choixparcours","8":"Bilanparcours66.examenaudition","9":"Bilanparcours66.examenauditionpe","10":"Dossierep.themeep","11":"Canton.canton","\/Bilansparcours66\/index\/#Bilanparcours66.personne_id#":{"class":"view"}},"innerTable":["Adresse.numcom","Adresse.nomcom","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu "Recherches"
	  	1. Filtres de recherche
	 		''filters'' => array(
	 			 1.1 Valeurs par défaut des filtres de recherche
	 			''defaults'' => array(
	 				''Dossier'' => array(
	 					 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
	 					''dernier'' => ''1''
	 				)
	 			),
	 			 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
	 			''accepted'' => array(),
	 			 1.3 Ne pas afficher ni traiter certains filtres de recherche
	 			''skip'' => array(),
	 			 1.4 Filtres additionnels : La personne possède un(e)...
	 			''has'' => array()
	 		),
	 		 2. Recherche
	 		''query'' => array(
	 			 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
	 			''restrict'' => array(),
	 			 2.2 Conditions supplémentaires optionnelles
	 			''conditions'' => array(),
	 			 2.3 Tri par défaut
	 			''order'' => array(''Bilanparcours66.datebilan'')
	 		),
	 		 3. Nombre d''enregistrements par page
	 		''limit'' => 10,
	 		 4. Lancer la recherche au premier accès à la page ?
	 		''auto'' => false,
	 		 5. Résultats de la recherche
	 		''results'' => array(
	 			 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
	 			''header'' => array(),
	 			 5.2 Colonnes du tableau de résultats
	 			''fields'' => array (),
	 			 5.3 Infobulle optionnelle du tableau de résultats
	 			''innerTable'' => array()
	 		),
	 		 6. Temps d''exécution, mémoire maximum, ...
	 		''ini_set'' => array()


		array(
			''filters'' => array(
				''defaults'' => array(
					''Dossier'' => array(
						''dernier'' => ''1''
					)
				),
				''accepted'' => array(),
				''skip'' => array(),
				''has'' => array()
			),
			''query'' => array(
				''restrict'' => array(),
				''conditions'' => array(),
				''order'' => array(''Bilanparcours66.datebilan'')
			),
			''limit'' => 10,
			''auto'' => false,
			''results'' => array(
				''header'' => array(),
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Bilanparcours66.datebilan'',
					''Personne.nom_complet_court'',
					''Structurereferente.lib_struc'',
					''Referent.nom_complet'',
					''Bilanparcours66.proposition'',
					''Bilanparcours66.positionbilan'',
					''Bilanparcours66.choixparcours'',
					''Bilanparcours66.examenaudition'',
					''Bilanparcours66.examenauditionpe'',
					''Dossierep.themeep'',
					''Canton.canton'',
					''Bilansparcours66index#Bilanparcours66.personne_id#'' => array( ''class'' => ''view'' ),
				),
				''innerTable'' => array(
					''Adresse.numcom'',
					''Adresse.nomcom'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			''ini_set'' => array()
		)', 28, current_timestamp, current_timestamp),
(362, 'ConfigurableQuery.Bilansparcours66.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Bilanparcours66.datebilan"]},"results":{"fields":["Bilanparcours66.datebilan","Personne.nom_complet_court","Dossier.matricule","Structurereferente.lib_struc","Referent.nom_complet","Bilanparcours66.proposition","Bilanparcours66.positionbilan","Bilanparcours66.choixparcours","Bilanparcours66.examenaudition","Bilanparcours66.examenauditionpe","Adresse.numcom","Adresse.nomcom","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV,  menu "Recherches"


		array(
			''filters'' => Configure::read( ''ConfigurableQuery.Bilansparcours66.search.filters'' ),
			''query'' => Configure::read( ''ConfigurableQuery.Bilansparcours66.search.query'' ),
			''results'' => array(
				''fields'' => array(
					''Bilanparcours66.datebilan'',
					''Personne.nom_complet_court'',
					''Dossier.matricule'',
					''Structurereferente.lib_struc'',
					''Referent.nom_complet'',
					''Bilanparcours66.proposition'',
					''Bilanparcours66.positionbilan'',
					''Bilanparcours66.choixparcours'',
					''Bilanparcours66.examenaudition'',
					''Bilanparcours66.examenauditionpe'',
					''Adresse.numcom'',
					''Adresse.nomcom'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			''ini_set'' => Configure::read( ''ConfigurableQuery.Bilansparcours66.search.ini_set'' ),
		)', 28, current_timestamp, current_timestamp),
(363, 'ConfigurableQuery.Apres.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Apre.numeroapre","2":"Personne.nom_complet_court","3":"Adresse.nomcom","4":"Aideapre66.datedemande","5":"Structurereferente.lib_struc","6":"Referent.nom_complet","7":"Apre.activitebeneficiaire","8":"Apre.etatdossierapre","9":"Apre.isdecision","10":"Aideapre66.decisionapre","11":"Canton.canton","\/Apres66\/index\/#Apre.personne_id#":{"class":"view"}},"innerTable":["Dossier.matricule","Personne.dtnai","Adresse.numcom","Personne.nir","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu "Recherches" > "Par APREs"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.numdemrsa'',
					''Apre.numeroapre'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Aideapre66.datedemande'',
					''Structurereferente.lib_struc'',
					''Referent.nom_complet'',
					''Apre.activitebeneficiaire'',
					''Apre.etatdossierapre'',
					''Apre.isdecision'',
					''Aideapre66.decisionapre'',
					''Canton.canton'',
					''Apres66index#Apre.personne_id#'' => array( ''class'' => ''view'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Dossier.matricule'',
					''Personne.dtnai'',
					''Adresse.numcom'',
					''Personne.nir'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 30, current_timestamp, current_timestamp),
(364, 'ConfigurableQuery.Apres.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"results":{"fields":{"0":"Personne.nom_complet_court","Aideapre66.datedemande":{"type":"date"},"1":"Themeapre66.name","2":"Typeaideapre66.name","3":"Structurereferente.lib_struc","4":"Referent.nom_complet","5":"Apre.etatdossierapre","6":"Aideapre66.decisionapre","7":"Aideapre66.montantaccorde","8":"Canton.canton","9":"Structurereferenteparcours.lib_struc","10":"Referentparcours.nom_complet"}},"ini_set":[]}', 'Export CSV,  menu "Recherches" > "Par APREs"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Apres.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Apres.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Personne.nom_complet_court'',
					''Aideapre66.datedemande'' => array( ''type'' => ''date'' ),
					''Themeapre66.name'',
					''Typeaideapre66.name'',
					''Structurereferente.lib_struc'',
					''Referent.nom_complet'',
					''Apre.etatdossierapre'',
					''Aideapre66.decisionapre'',
					''Aideapre66.montantaccorde'',
					''Canton.canton'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Apres.search.ini_set'' ),
		)', 30, current_timestamp, current_timestamp),
(365, 'ConfigurableQuery.Apres66.cohorte_validation', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":{"Apre66.etatdossierapre":"COM","Apre66.isdecision":"N"},"conditions":[],"order":["Personne.nom","Personne.prenom"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Apre66.numeroapre","1":"Dossier.numdemrsa","2":"Personne.nom_complet_court","3":"Referentapre.nom_complet","4":"Aideapre66.datedemande","5":"Aideapre66.montantpropose","6":"Canton.canton","\/Apres66\/index\/#Personne.id#":{"class":"view external"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Apre66.etatdossierapre'' => ''COM'',
					''Apre66.isdecision'' => ''N'',
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(
					''Personne.nom'',
					''Personne.prenom''
				)
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Apre66.numeroapre'',
					''Dossier.numdemrsa'',
					''Personne.nom_complet_court'',
					''Referentapre.nom_complet'',
					''Aideapre66.datedemande'',
					''Aideapre66.montantpropose'',
					''Canton.canton'',
					''Apres66index#Personne.id#'' => array( ''class'' => ''view external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 30, current_timestamp, current_timestamp),
(366, 'ConfigurableQuery.Apres66.exportcsv_validation', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":{"Apre66.etatdossierapre":"COM","Apre66.isdecision":"N"},"conditions":[],"order":["Personne.nom","Personne.prenom"]},"results":{"fields":["Apre66.numeroapre","Dossier.numdemrsa","Personne.nom_complet_court","Referentapre.nom_complet","Apre66.datedemandeapre","Aideapre66.montantpropose","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Apres66.cohorte_validation.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Apres66.cohorte_validation.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Apre66.numeroapre'',
					''Dossier.numdemrsa'',
					''Personne.nom_complet_court'',
					''Referentapre.nom_complet'',
					''Apre66.datedemandeapre'',
					''Aideapre66.montantpropose'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Apres66.cohorte_validation.ini_set'' ),
		)', 30, current_timestamp, current_timestamp),
(367, 'ConfigurableQuery.Apres66.cohorte_imprimer', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":{"Apre66.etatdossierapre":"VAL"},"conditions":{"0":"Apre66.datenotifapre IS NULL","Typeaideapre66.isincohorte":"O"},"order":["Personne.nom","Personne.prenom"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Apre66.numeroapre","1":"Personne.nom_complet_court","2":"Referentapre.nom_complet","3":"Aideapre66.datedemande","4":"Aideapre66.decisionapre","5":"Aideapre66.montantaccorde","6":"Aideapre66.motifrejetequipe","7":"Aideapre66.datemontantaccorde","8":"Canton.canton","\/Apres66\/index\/#Personne.id#":{"class":"view external"},"\/Apres66\/notifications\/#Apre66.id#":{"class":"print"}},"innerTable":["Themeapre66.name","Typeaideapre66.name","Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Adresse.nomcom","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Apre66.etatdossierapre'' => ''VAL'',
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(
					''Apre66.datenotifapre IS NULL'',
					''Typeaideapre66.isincohorte'' => ''O''
				),
				 2.3 Tri par défaut
				''order'' => array(
					''Personne.nom'',
					''Personne.prenom''
				)
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Apre66.numeroapre'',
					''Personne.nom_complet_court'',
					''Referentapre.nom_complet'',
					''Aideapre66.datedemande'',
					''Aideapre66.decisionapre'',
					''Aideapre66.montantaccorde'',
					''Aideapre66.motifrejetequipe'',
					''Aideapre66.datemontantaccorde'',
					''Canton.canton'',
					''Apres66index#Personne.id#'' => array( ''class'' => ''view external'' ),
					''Apres66notifications#Apre66.id#'' => array( ''class'' => ''print'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Themeapre66.name'',
					''Typeaideapre66.name'',
					''Personne.dtnai'',
					''Dossier.matricule'',
					''Personne.nir'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 30, current_timestamp, current_timestamp),
(368, 'ConfigurableQuery.Apres66.exportcsv_imprimer', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":{"Apre66.etatdossierapre":"VAL"},"conditions":{"0":"Apre66.datenotifapre IS NULL","Typeaideapre66.isincohorte":"O"},"order":["Personne.nom","Personne.prenom"]},"results":{"fields":["Apre66.numeroapre","Personne.nom_complet_court","Referentapre.nom_complet","Aideapre66.datedemande","Aideapre66.decisionapre","Aideapre66.montantaccorde","Aideapre66.motifrejetequipe","Aideapre66.datemontantaccorde","Themeapre66.name","Typeaideapre66.name","Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Adresse.nomcom","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Apres66.cohorte_imprimer.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Apres66.cohorte_imprimer.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Apre66.numeroapre'',
					''Personne.nom_complet_court'',
					''Referentapre.nom_complet'',
					''Aideapre66.datedemande'',
					''Aideapre66.decisionapre'',
					''Aideapre66.montantaccorde'',
					''Aideapre66.motifrejetequipe'',
					''Aideapre66.datemontantaccorde'',
					''Themeapre66.name'',
					''Typeaideapre66.name'',
					''Personne.dtnai'',
					''Dossier.matricule'',
					''Personne.nir'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Apres66.cohorte_imprimer.ini_set'' ),
		)', 30, current_timestamp, current_timestamp),
(369, 'ConfigurableQuery.Apres66.cohorte_notifiees', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":{"Apre66.etatdossierapre":"VAL"},"conditions":{"0":"Apre66.datenotifapre IS NOT NULL","Typeaideapre66.isincohorte":"O"},"order":["Personne.nom","Personne.prenom"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Apre66.numeroapre","1":"Personne.nom_complet_court","2":"Referentapre.nom_complet","3":"Aideapre66.datedemande","4":"Aideapre66.decisionapre","5":"Aideapre66.montantaccorde","6":"Aideapre66.motifrejetequipe","7":"Aideapre66.datemontantaccorde","8":"Canton.canton","\/Apres66\/index\/#Personne.id#":{"class":"view external"},"\/Apres66\/notifications\/#Apre66.id#":{"class":"print"}},"innerTable":["Themeapre66.name","Typeaideapre66.name","Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Adresse.nomcom","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Apres66.cohorte_imprimer.filters'' ),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Apre66.etatdossierapre'' => ''VAL'',
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(
					''Apre66.datenotifapre IS NOT NULL'',
					''Typeaideapre66.isincohorte'' => ''O''
				),
				 2.3 Tri par défaut
				''order'' => array(
					''Personne.nom'',
					''Personne.prenom''
				)
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => Configure::read( ''ConfigurableQuery.Apres66.cohorte_imprimer.results'' ),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Apres66.cohorte_imprimer.ini_set'' ),
		)', 30, current_timestamp, current_timestamp),
(370, 'ConfigurableQuery.Apres66.exportcsv_notifiees', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":{"Apre66.etatdossierapre":"VAL"},"conditions":{"0":"Apre66.datenotifapre IS NULL","Typeaideapre66.isincohorte":"O"},"order":["Personne.nom","Personne.prenom"]},"results":{"fields":["Apre66.numeroapre","Personne.nom_complet_court","Referentapre.nom_complet","Aideapre66.datedemande","Aideapre66.decisionapre","Aideapre66.montantaccorde","Aideapre66.motifrejetequipe","Aideapre66.datemontantaccorde","Themeapre66.name","Typeaideapre66.name","Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Adresse.nomcom","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Apres66.exportcsv_imprimer.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Apres66.exportcsv_imprimer.query'' ),
			 3. Résultats de la recherche
			''results'' => Configure::read( ''ConfigurableQuery.Apres66.exportcsv_imprimer.results'' ),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Apres66.exportcsv_imprimer.ini_set'' ),
		)', 30, current_timestamp, current_timestamp),
(371, 'ConfigurableQuery.Apres66.cohorte_transfert', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":{"Apre66.etatdossierapre":"VAL"},"conditions":{"0":"Apre66.datenotifapre IS NOT NULL","Apre66.istraite":"0","Apre66.istransfere":"0","Typeaideapre66.isincohorte":"O"},"order":["Personne.nom","Personne.prenom"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Apre66.numeroapre","1":"Personne.nom_complet_court","2":"Referentapre.nom_complet","3":"Aideapre66.datedemande","4":"Aideapre66.decisionapre","5":"Aideapre66.montantaccorde","6":"Aideapre66.motifrejetequipe","7":"Aideapre66.datemontantaccorde","8":"Canton.canton","Apre66.nb_fichiers_lies":{"class":"center ajax_refresh"},"\/Apres66\/filelink\/#Apre66.id#":{"class":"external"},"\/Apres66\/index\/#Personne.id#":{"class":"view external"},"\/Apres66\/notifications\/#Apre66.id#":{"class":"print"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Apre66.etatdossierapre'' => ''VAL'',
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(
					''Apre66.datenotifapre IS NOT NULL'',
					''Apre66.istraite'' => ''0'',
					''Apre66.istransfere'' => ''0'',
					''Typeaideapre66.isincohorte'' => ''O''
				),
				 2.3 Tri par défaut
				''order'' => array(
					''Personne.nom'',
					''Personne.prenom''
				)
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Apre66.numeroapre'',
					''Personne.nom_complet_court'',
					''Referentapre.nom_complet'',
					''Aideapre66.datedemande'',
					''Aideapre66.decisionapre'',
					''Aideapre66.montantaccorde'',
					''Aideapre66.motifrejetequipe'',
					''Aideapre66.datemontantaccorde'',
					''Canton.canton'',
					''Apre66.nb_fichiers_lies'' => array( ''class'' => ''center ajax_refresh'' ),
					''Apres66filelink#Apre66.id#'' => array( ''class'' => ''external'' ),
					''Apres66index#Personne.id#'' => array( ''class'' => ''view external'' ),
					''Apres66notifications#Apre66.id#'' => array( ''class'' => ''print'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 30, current_timestamp, current_timestamp),
(372, 'ConfigurableQuery.Apres66.exportcsv_transfert', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":{"Apre66.etatdossierapre":"VAL"},"conditions":{"0":"Apre66.datenotifapre IS NOT NULL","Apre66.istraite":"0","Apre66.istransfere":"0","Typeaideapre66.isincohorte":"O"},"order":["Personne.nom","Personne.prenom"]},"results":{"fields":["Apre66.numeroapre","Personne.nom_complet_court","Referentapre.nom_complet","Aideapre66.datedemande","Aideapre66.decisionapre","Aideapre66.montantaccorde","Aideapre66.motifrejetequipe","Aideapre66.datemontantaccorde","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Apres66.cohorte_transfert.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Apres66.cohorte_transfert.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Apre66.numeroapre'',
					''Personne.nom_complet_court'',
					''Referentapre.nom_complet'',
					''Aideapre66.datedemande'',
					''Aideapre66.decisionapre'',
					''Aideapre66.montantaccorde'',
					''Aideapre66.motifrejetequipe'',
					''Aideapre66.datemontantaccorde'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Apres66.cohorte_transfert.ini_set'' ),
		)', 30, current_timestamp, current_timestamp),
(337, 'ConfigurableQuery.Dossierspcgs66.cohorte_rsamajore', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Adresse":{"heberge":"1"}},"accepted":{"Requestmanager.name":["Cohorte de tag"]},"skip":{"0":"Situationdossierrsa.etatdosrsa_choice","1":"Situationdossierrsa.etatdosrsa","Detailcalculdroitrsa.natpf_choice":"1","2":"Detailcalculdroitrsa.natpf","3":"Calculdroitrsa.toppersdrodevorsa"},"has":{"0":"Cui","Orientstruct":{"Orientstruct.statut_orient":"Orienté"},"Contratinsertion":{"Contratinsertion.decision_ci":"V"},"1":"Personnepcg66"}},"query":{"restrict":{"Tag.valeurtag_id":"3","Prestation.rolepers":"DEM","Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["2"],"Detailcalculdroitrsa.natpf_choice":"1","Detailcalculdroitrsa.natpf":["RSI"],"Calculdroitrsa.toppersdrodevorsa":"1"},"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.matricule","1":"Personne.nom_complet_prenoms","2":"Detailcalculdroitrsa.mtrsavers","Foyer.nb_enfants":{"options":[]},"3":"Adresse.nomcom","4":"Foyer.ddsitfam","5":"Canton.canton","\/Dossiers\/view\/#Dossier.id#":{"class":"external"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"cohorte":{"options":{"Tag.calcullimite":{"1":"1 mois","1.5":"1 mois et demi","2":"2 mois","3":"3 mois","6":"6 mois","12":"1 an","24":"2 ans","36":"3 ans"},"Traitementpcg66.typetraitement":{"courrier":"Courrier","dossierarevoir":"Dossier à revoir"}},"values":{"Dossierpcg66.typepdo_id":16,"Dossierpcg66.datereceptionpdo":"TEXT::NOW","Dossierpcg66.serviceinstructeur_id":null,"Dossierpcg66.commentairepiecejointe":null,"Dossierpcg66.dateaffectation":"TEXT::NOW","Situationpdo.Situationpdo":38,"Dossierpcg66.originepdo_id":21,"Dossierpcg66.poledossierpcg66_id":1,"Traitementpcg66.typecourrierpcg66_id":9,"Traitementpcg66.descriptionpdo_id":1,"Traitementpcg66.datereception":null,"Modeletraitementpcg66.modeletypecourrierpcg66_id":90,"Modeletraitementpcg66.montantdatedebut":"TEXT::NOW","Modeletraitementpcg66.montantdatefin":"TEXT::+3MONTHS","Piecemodeletypecourrierpcg66.0_Piecemodeletypecourrierpcg66":185,"Traitementpcg66.serviceinstructeur_id":null,"Traitementpcg66.datedepart":"TEXT::NOW","Tag.valeurtag_id":3}},"ini_set":[],"view":false}', 'array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					),
					''Adresse'' => array(
						''heberge'' => ''1''
					),
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(
					''Requestmanager.name'' => array( ''Cohorte de tag'' ),  Noter nom de catégorie - Cohorte de tag
				),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(
					''Situationdossierrsa.etatdosrsa_choice'',
					''Situationdossierrsa.etatdosrsa'',
					''Detailcalculdroitrsa.natpf_choice'' => ''1'',
					''Detailcalculdroitrsa.natpf'',
					''Calculdroitrsa.toppersdrodevorsa''
				),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array(
					''Cui'',
					''Orientstruct'' => array(
						''Orientstruct.statut_orient'' => ''Orienté'',
						 Orientstruct possède des conditions supplémentaire dans le modèle WebrsaRechercheDossier pour le CD66
					),
					''Contratinsertion'' => array(
						''Contratinsertion.decision_ci'' => ''V''
					),
					''Personnepcg66''
				)
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Tag.valeurtag_id'' => ''3'',  Valeur du tag pour la cohorte hebergé
					''Prestation.rolepers'' => ''DEM'',  Demandeur du RSA
					''Situationdossierrsa.etatdosrsa_choice'' => ''1'',
					''Situationdossierrsa.etatdosrsa'' => array(''2''),  Droit ouvert et versable
					''Detailcalculdroitrsa.natpf_choice'' => ''1'',
					''Detailcalculdroitrsa.natpf'' => array(
						''RSI'',  RSA Socle majoré (Financement sur fonds Conseil général)
					),
					''Calculdroitrsa.toppersdrodevorsa'' => ''1'',  Personne soumise à droits et devoirs ? > Oui
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Dossier.matricule'',
					''Personne.nom_complet_prenoms'',
					''Detailcalculdroitrsa.mtrsavers'',
					''Foyer.nb_enfants'' => array( ''options'' => array() ),
					''Adresse.nomcom'',
					''Foyer.ddsitfam'',
					''Canton.canton'',
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(
					''Tag.calcullimite'' => array(
						''1'' => ''1 mois'',
						''1.5'' => ''1 mois et demi'',  Supporte les nombres de type float
						2 => ''2 mois'',
						3 => ''3 mois'',
						6 => ''6 mois'',
						12 => ''1 an'',
						24 => ''2 ans'',
						36 => ''3 ans'',
					),
					''Traitementpcg66.typetraitement'' => array(
						''courrier'' => ''Courrier'',
						''dossierarevoir'' => ''Dossier à revoir'',
					)
				),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
					''Dossierpcg66.typepdo_id'' => 16,  Position mission PDU-MMR
					''Dossierpcg66.datereceptionpdo'' => ''TEXT::NOW'',  Date de réception du dossier
					''Dossierpcg66.serviceinstructeur_id'' => null,  Service instructeur
					''Dossierpcg66.commentairepiecejointe'' => null,  Commentaire
					''Dossierpcg66.dateaffectation'' => ''TEXT::NOW'',  Date d''affectation
					''Situationpdo.Situationpdo'' => 38,  Cible majoré
					''Dossierpcg66.originepdo_id'' => 21,  PDU - MMR Cible Imposition
					''Dossierpcg66.poledossierpcg66_id'' => 1,  PDU
					''Traitementpcg66.typecourrierpcg66_id'' => 9,  PDU - Cibles
					''Traitementpcg66.descriptionpdo_id'' => 1,  Courrier à l''allocataire
					''Traitementpcg66.datereception'' => null,  Date de reception
					''Modeletraitementpcg66.modeletypecourrierpcg66_id'' => 90,  Cible majoré
					''Modeletraitementpcg66.montantdatedebut'' => ''TEXT::NOW'',
					''Modeletraitementpcg66.montantdatefin'' => ''TEXT::+3MONTHS'',
					''Piecemodeletypecourrierpcg66.0_Piecemodeletypecourrierpcg66'' => 185,  Attestation ci-jointe dûment complétée
					''Piecemodeletypecourrierpcg66.1_Piecemodeletypecourrierpcg66'' => 132,  Attestation d''hébergement dûment remplie (en pièce jointe)
					''Piecemodeletypecourrierpcg66.2_Piecemodeletypecourrierpcg66'' => 129,  Avis d''imposition sur les revenus de l''année précédente...
					''Piecemodeletypecourrierpcg66.3_Piecemodeletypecourrierpcg66'' => 133,  Justificatifs de résidence de moins de 3 mois...
					''Piecemodeletypecourrierpcg66.4_Piecemodeletypecourrierpcg66'' => 128,  Pièce d''identité et passeport en intégralité et en cours...
					''Piecemodeletypecourrierpcg66.5_Piecemodeletypecourrierpcg66'' => 130,  Relevés bancaires des 3 derniers mois
					''Traitementpcg66.serviceinstructeur_id'' => null,  Service à contacter (insertion)
					''Traitementpcg66.datedepart'' => ''TEXT::NOW'',  Date de départ (pour le calcul de l''échéance)
					''Tag.valeurtag_id'' => 3,  Valeur du tag
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(),
			 7. Affichage vertical des résultats
			''view'' => false,
		)', 21, current_timestamp, current_timestamp),
(373, 'ConfigurableQuery.Apres66.cohorte_traitement', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":{"Apre66.etatdossierapre":"TRA"},"conditions":{"0":"Apre66.datenotifapre IS NOT NULL","Apre66.istraite":"0","Apre66.istransfere":"1","Typeaideapre66.isincohorte":"O"},"order":["Personne.nom","Personne.prenom"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Apre66.numeroapre","1":"Personne.nom_complet_court","2":"Referentapre.nom_complet","3":"Aideapre66.datedemande","4":"Aideapre66.decisionapre","5":"Aideapre66.montantaccorde","6":"Aideapre66.motifrejetequipe","7":"Aideapre66.datemontantaccorde","8":"Canton.canton","\/Apres66\/index\/#Personne.id#":{"class":"view external"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Apre66.etatdossierapre'' => ''TRA'',
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(
					''Apre66.datenotifapre IS NOT NULL'',
					''Apre66.istraite'' => ''0'',
					''Apre66.istransfere'' => ''1'',
					''Typeaideapre66.isincohorte'' => ''O''
				),
				 2.3 Tri par défaut
				''order'' => array(
					''Personne.nom'',
					''Personne.prenom''
				)
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Apre66.numeroapre'',
					''Personne.nom_complet_court'',
					''Referentapre.nom_complet'',
					''Aideapre66.datedemande'',
					''Aideapre66.decisionapre'',
					''Aideapre66.montantaccorde'',
					''Aideapre66.motifrejetequipe'',
					''Aideapre66.datemontantaccorde'',
					''Canton.canton'',
					''Apres66index#Personne.id#'' => array( ''class'' => ''view external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 30, current_timestamp, current_timestamp),
(374, 'ConfigurableQuery.Apres66.exportcsv_traitement', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[]},"query":{"restrict":{"Apre66.etatdossierapre":"TRA"},"conditions":{"0":"Apre66.datenotifapre IS NOT NULL","Apre66.istraite":"0","Apre66.istransfere":"1","Typeaideapre66.isincohorte":"O"},"order":["Personne.nom","Personne.prenom"]},"results":{"fields":["Apre66.numeroapre","Personne.nom_complet_court","Referentapre.nom_complet","Aideapre66.datedemande","Aideapre66.decisionapre","Aideapre66.montantaccorde","Aideapre66.motifrejetequipe","Aideapre66.datemontantaccorde","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Apres66.cohorte_traitement.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Apres66.cohorte_traitement.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Apre66.numeroapre'',
					''Personne.nom_complet_court'',
					''Referentapre.nom_complet'',
					''Aideapre66.datedemande'',
					''Aideapre66.decisionapre'',
					''Aideapre66.montantaccorde'',
					''Aideapre66.motifrejetequipe'',
					''Aideapre66.datemontantaccorde'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Apres66.cohorte_traitement.ini_set'' ),
		)', 30, current_timestamp, current_timestamp),
(375, 'ConfigurableQuery.ActionscandidatsPersonnes.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Actioncandidat.name","1":"Partenaire.libstruc","2":"Personne.nom_complet_court","3":"Referent.nom_complet","4":"ActioncandidatPersonne.positionfiche","5":"ActioncandidatPersonne.datesignature","6":"Canton.canton","\/ActionscandidatsPersonnes\/index\/#ActioncandidatPersonne.personne_id#":{"class":"view"}},"innerTable":["Adresse.numcom","Adresse.nomcom","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu "Recherches"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Actioncandidat.name'',
					''Partenaire.libstruc'',
					''Personne.nom_complet_court'',
					''Referent.nom_complet'',
					''ActioncandidatPersonne.positionfiche'',
					''ActioncandidatPersonne.datesignature'',
					''Canton.canton'',
					''ActionscandidatsPersonnesindex#ActioncandidatPersonne.personne_id#'' => array( ''class'' => ''view'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Adresse.numcom'',
					''Adresse.nomcom'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 31, current_timestamp, current_timestamp),
(376, 'ConfigurableQuery.ActionscandidatsPersonnes.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":[],"order":[]},"results":{"fields":["ActioncandidatPersonne.datesignature","Personne.nom_complet_court","Dossier.matricule","Referent.nom_complet","Actioncandidat.name","ActioncandidatPersonne.formationregion","ActioncandidatPersonne.nomprestataire","Progfichecandidature66.name","Partenaire.libstruc","ActioncandidatPersonne.positionfiche","ActioncandidatPersonne.sortiele","ActioncandidatPersonne.motifsortie_id","Adresse.numcom","Adresse.nomcom","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV,  menu "Recherches"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.ActionscandidatsPersonnes.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.ActionscandidatsPersonnes.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''ActioncandidatPersonne.datesignature'',
					''Personne.nom_complet_court'',
					''Dossier.matricule'',
					''Referent.nom_complet'',
					''Actioncandidat.name'',
					''ActioncandidatPersonne.formationregion'',
					''ActioncandidatPersonne.nomprestataire'',
					''Progfichecandidature66.name'',
					''Partenaire.libstruc'',
					''ActioncandidatPersonne.positionfiche'',
					''ActioncandidatPersonne.sortiele'',
					''ActioncandidatPersonne.motifsortie_id'',
					''Adresse.numcom'',
					''Adresse.nomcom'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.ActionscandidatsPersonnes.search.ini_set'' ),
		)', 31, current_timestamp, current_timestamp),
(93, 'Module.Recoursgracieux.enabled', 'true', 'Activation du module de gestion des RecoursGracieux
      ''Module.Recoursgracieux.enabled'' => true pour activer le module recours gracieux, false pour inactiver', 1, current_timestamp, current_timestamp),
(377, 'ConfigurableQuery.ActionscandidatsPersonnes.cohorte_enattente', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":{"ActioncandidatPersonne.positionfiche":"enattente"},"order":["ActioncandidatPersonne.datesignature"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.matricule","1":"Personne.nom_complet_court","2":"Adresse.nomcom","3":"Actioncandidat.name","4":"Partenaire.libstruc","5":"Referent.nom_complet","6":"ActioncandidatPersonne.datesignature","7":"Canton.canton","\/ActionscandidatsPersonnes\/index\/#ActioncandidatPersonne.personne_id#":{"class":"view"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(
					''ActioncandidatPersonne.positionfiche'' => ''enattente''
				),
				 2.3 Tri par défaut
				''order'' => array(''ActioncandidatPersonne.datesignature'')
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.matricule'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Actioncandidat.name'',
					''Partenaire.libstruc'',
					''Referent.nom_complet'',
					''ActioncandidatPersonne.datesignature'',
					''Canton.canton'',
					''ActionscandidatsPersonnesindex#ActioncandidatPersonne.personne_id#'' => array( ''class'' => ''view'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 31, current_timestamp, current_timestamp),
(378, 'ConfigurableQuery.ActionscandidatsPersonnes.exportcsv_enattente', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":{"ActioncandidatPersonne.positionfiche":"enattente"},"order":["ActioncandidatPersonne.datesignature"]},"results":{"fields":["Dossier.matricule","Personne.nom_complet_court","Adresse.nomcom","Actioncandidat.name","Partenaire.libstruc","Referent.nom_complet","ActioncandidatPersonne.datesignature","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.ActionscandidatsPersonnes.cohorte_enattente.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.ActionscandidatsPersonnes.cohorte_enattente.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.matricule'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Actioncandidat.name'',
					''Partenaire.libstruc'',
					''Referent.nom_complet'',
					''ActioncandidatPersonne.datesignature'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.ActionscandidatsPersonnes.cohorte_enattente.ini_set'' ),
		)', 31, current_timestamp, current_timestamp),
(379, 'ConfigurableQuery.ActionscandidatsPersonnes.cohorte_encours', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":{"ActioncandidatPersonne.positionfiche":"encours"},"order":["ActioncandidatPersonne.datesignature"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.matricule","1":"Personne.nom_complet_court","2":"Adresse.nomcom","3":"Actioncandidat.name","4":"Partenaire.libstruc","5":"Referent.nom_complet","6":"ActioncandidatPersonne.datesignature","7":"ActioncandidatPersonne.bilanvenu","8":"ActioncandidatPersonne.bilanretenu","9":"Canton.canton","\/ActionscandidatsPersonnes\/index\/#ActioncandidatPersonne.personne_id#":{"class":"view external"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Cohorte


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(
					''ActioncandidatPersonne.positionfiche'' => ''encours''
				),
				 2.3 Tri par défaut
				''order'' => array(''ActioncandidatPersonne.datesignature'')
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.matricule'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Actioncandidat.name'',
					''Partenaire.libstruc'',
					''Referent.nom_complet'',
					''ActioncandidatPersonne.datesignature'',
					''ActioncandidatPersonne.bilanvenu'',
					''ActioncandidatPersonne.bilanretenu'',
					''Canton.canton'',
					''ActionscandidatsPersonnesindex#ActioncandidatPersonne.personne_id#'' => array( ''class'' => ''view external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 31, current_timestamp, current_timestamp),
(380, 'ConfigurableQuery.ActionscandidatsPersonnes.exportcsv_encours', '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":[],"has":[]},"query":{"restrict":[],"conditions":{"ActioncandidatPersonne.positionfiche":"encours"},"order":["ActioncandidatPersonne.datesignature"]},"results":{"fields":["Dossier.matricule","Personne.nom_complet_court","Adresse.nomcom","Actioncandidat.name","Partenaire.libstruc","Referent.nom_complet","ActioncandidatPersonne.datesignature","ActioncandidatPersonne.bilanvenu","ActioncandidatPersonne.bilanretenu","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Canton.canton"]},"ini_set":[]}', 'Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.ActionscandidatsPersonnes.cohorte_encours.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.ActionscandidatsPersonnes.cohorte_encours.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.matricule'',
					''Personne.nom_complet_court'',
					''Adresse.nomcom'',
					''Actioncandidat.name'',
					''Partenaire.libstruc'',
					''Referent.nom_complet'',
					''ActioncandidatPersonne.datesignature'',
					''ActioncandidatPersonne.bilanvenu'',
					''ActioncandidatPersonne.bilanretenu'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Canton.canton'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.ActionscandidatsPersonnes.cohorte_encours.ini_set'' ),
		)', 31, current_timestamp, current_timestamp),
(381, 'page.accueil.profil', '{"by-default":{"rendezvous":{"limite":1},"dernierscersperimes":[],"recoursgracieux":{"limite":7}}}', 'Accueil : blocs d''information à afficher

	  Profil :
	   - correspond aux profils des groups dans la partie administration

	  Blocs :
	   - vous pouvez ordonnez les blocs comme vous le souhaitez
	   - blocs possibles :
	  cers
	  fichesprescription (93)
	  rendezvous
	  dernierscersperimes (66)
	  recoursgracieux (66)



		array (
			 Affichage par défaut.
			''by-default'' => array (
				''rendezvous'' => array (
					''limite'' => 1  Nombre de jour après à la date du jour
				),
				''dernierscersperimes'' => array (),
				''recoursgracieux'' => array (
					''limite'' => 7,  Nombre de jours avant la dute butoir
				),
			),
		)', 32, current_timestamp, current_timestamp);
-- Racalcule de la séquence de la table configuration
SELECT setval('configurations_id_seq', (SELECT MAX(id) FROM configurations));

-- Ajout des configurations lié aux autres départements
INSERT INTO public.configurations (lib_variable, value_variable, comments_variable, configurationscategorie_id, created, modified)
VALUES
	('ConfigurableQuery.Transfertspdvs93.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Situationdossierrsa":{"etatdosrsa_choice":"0","etatdosrsa":["0","2","3","4"]}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Adresse.localite":{"sort":false},"Personne.nom_complet":{"sort":false},"Prestation.rolepers":{"sort":false},"Transfertpdv93.created":{"sort":false,"format":"%d\/%m\/%Y"},"VxStructurereferente.lib_struc":{"sort":false},"NvStructurereferente.lib_struc":{"sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"external"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu "Recherches" > "Par allocataires sortants" > "Intra-département"

		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						''dernier'' => ''1''
					),
					''Situationdossierrsa'' => array(
						''etatdosrsa_choice'' => ''0'',
						''etatdosrsa'' => array( ''0'',''2'', ''3'', ''4'' )
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Dossier.numdemrsa'' => array( ''sort'' => false ),
					''Dossier.matricule'' => array( ''sort'' => false ),
					''Adresse.localite'' => array( ''sort'' => false ),
					''Personne.nom_complet'' => array( ''sort'' => false ),
					''Prestation.rolepers'' => array( ''sort'' => false ),
					''Transfertpdv93.created'' => array(
						''sort'' => false,
						''format'' => ''%d%m%Y''
					),
					''VxStructurereferente.lib_struc'' => array( ''sort'' => false ),
					''NvStructurereferente.lib_struc'' => array( ''sort'' => false ),
					''Dossiersview#Dossier.id#'' => array(
						''class'' => ''external''
					)
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 33, current_timestamp, current_timestamp),
	('ConfigurableQuery.Transfertspdvs93.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Situationdossierrsa":{"etatdosrsa_choice":"0","etatdosrsa":["0","2","3","4"]}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"results":{"fields":{"0":"Dossier.numdemrsa","1":"Dossier.matricule","2":"Adresse.codepos","3":"Adresse.nomcom","4":"Personne.qual","5":"Personne.nom","6":"Personne.prenom","7":"Prestation.rolepers","Transfertpdv93.created":{"type":"date"},"8":"VxStructurereferente.lib_struc","9":"NvStructurereferente.lib_struc","10":"Structurereferenteparcours.lib_struc","11":"Referentparcours.nom_complet"}},"ini_set":[]}', 'Export CSV,  menu "Recherches" > "Par allocataires sortants" > "Intra-département"

		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Transfertspdvs93.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Transfertspdvs93.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.matricule'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Personne.qual'',
					''Personne.nom'',
					''Personne.prenom'',
					''Prestation.rolepers'',
					''Transfertpdv93.created'' => array( ''type'' => ''date'' ),
					''VxStructurereferente.lib_struc'',
					''NvStructurereferente.lib_struc'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Transfertspdvs93.search.ini_set'' ),
		)', 33, current_timestamp, current_timestamp),
	('ConfigurableQuery.Transfertspdvs93.cohorte_atransferer', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":{"year":"2020","month":"04","day":"22"},"dtdemrsa_to":{"year":"2020","month":"04","day":"29"}},"Calculdroitrsa":{"toppersdrodevorsa":"1"},"Situationdossierrsa":{"etatdosrsa_choice":"1","etatdosrsa":["2","3","4"]},"Orientstruct":{"typeorient_id":"1"}},"accepted":[],"skip":["Personne.sexe","Detailcalculdroitrsa.natpf","Detaildroitrsa.oridemrsa","Dossier.anciennete_dispositif","Serviceinstructeur.id","Dossier.fonorg","Foyer.sitfam"]},"query":{"restrict":[],"conditions":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Adresse.localite":{"sort":false,"label":"Adresse actuelle"},"Personne.nom_complet":{"sort":false},"Prestation.rolepers":{"sort":false},"Cer93.positioncer":{"sort":false,"label":"Position CER"},"Structurereferente.lib_struc":{"sort":false,"label":"Structure référente source"},"\/Cers93\/index\/#Personne.id#":{"class":"external"}},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu "Cohortes" > "Transferts PDV" > "Allocataires à transférer"

		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
						 Case à cocher "Filtrer par date de demande RSA"
						''dtdemrsa'' => ''0'',
						 Du (inclus)
						''dtdemrsa_from'' => date_sql_to_cakephp( date( ''Y-m-d'', strtotime( ''-1 week'' ) ) ),
						 Au (inclus)
						''dtdemrsa_to'' => date_sql_to_cakephp( date( ''Y-m-d'', strtotime( ''now'' ) ) ),
					),
					''Calculdroitrsa'' => array(
						''toppersdrodevorsa'' => ''1''
					),
					''Situationdossierrsa'' => array(
						''etatdosrsa_choice'' => ''1'',
						''etatdosrsa'' => array( ''2'', ''3'', ''4'' ),

					),
					''Orientstruct'' => array(
						''typeorient_id'' => ''1''
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(
					''Personne.sexe'',
					''Detailcalculdroitrsa.natpf'',
					''Detaildroitrsa.oridemrsa'',
					''Dossier.anciennete_dispositif'',
					''Serviceinstructeur.id'',
					''Dossier.fonorg'',
					''Foyer.sitfam'',
				)
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Dossier.numdemrsa'' => array( ''sort'' => false ),
					''Dossier.matricule'' => array( ''sort'' => false ),
					''Adresse.localite'' => array(
						''sort'' => false,
						''label'' => ''Adresse actuelle''
					),
					''Personne.nom_complet'' => array( ''sort'' => false ),
					''Prestation.rolepers'' => array( ''sort'' => false ),
					''Cer93.positioncer'' => array(
						''sort'' => false,
						''label'' => ''Position CER''
					),
					''Structurereferente.lib_struc'' => array(
						''sort'' => false,
						''label'' => ''Structure référente source''
					),
					''Cers93index#Personne.id#'' => array(
						''class'' => ''external''
					)
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 33, current_timestamp, current_timestamp),
		('Tableauxsuivispdvs93', '{"tableaud1":{"defaults":{"Search":{"soumis_dd_dans_annee":"1"}}},"tableaud2":{"defaults":{"Search":{"soumis_dd_dans_annee":"1"}}},"tableau1b3":{"defaults":{"Search":{"dsps_maj_dans_annee":"1"}},"exportcsvcorpus":["Rendezvous.daterdv","Structurereferente.lib_struc","Referent.nom_complet","Personne.nom","Personne.prenom","Personne.dtnai","Personne.sexe","Prestation.rolepers","Adresse.codepos","Adresse.nomcom","Foyer.sitfam","Dossier.matricule","Difficulte.sante","Difficulte.logement","Difficulte.familiales","Difficulte.modes_gardes","Difficulte.surendettement","Difficulte.administratives","Difficulte.linguistiques","Difficulte.mobilisation","Difficulte.qualification_professionnelle","Difficulte.acces_emploi","Difficulte.autres"]},"tableau1b4":{"defaults":{"Search":{"rdv_structurereferente":"1"}},"exportcsvcorpus":["Ficheprescription93.date_signature","Structurereferente.lib_struc","Referent.nom_complet","Personne.nom","Personne.prenom","Personne.dtnai","Personne.sexe","Prestation.rolepers","Adresse.codepos","Adresse.nomcom","Foyer.sitfam","Dossier.matricule","Thematiquefp93.type","Thematiquefp93.name","Thematiquefp93.yearthema","Categoriefp93.name"]},"tableau1b5":{"defaults":{"Search":{"rdv_structurereferente":"1"}},"exportcsvcorpus":["Ficheprescription93.date_signature","Structurereferente.lib_struc","Referent.nom_complet","Ficheprescription93.personne_a_integre","Ficheprescription93.personne_pas_deplace","Ficheprescription93.en_attente","Personne.nom","Personne.prenom","Personne.dtnai","Personne.sexe","Prestation.rolepers","Adresse.codepos","Adresse.nomcom","Foyer.sitfam","Dossier.matricule","Thematiquefp93.type","Thematiquefp93.name","Thematiquefp93.yearthema","Categoriefp93.name"]},"tableau1b6":{"defaults":{"Search":{"rdv_structurereferente":"0"}},"exportcsvcorpus":["Rendezvous.daterdv","Thematiquerdv.name","Statutrdv.libelle","Structurereferente.lib_struc","Referent.nom_complet","Personne.nom","Personne.prenom","Personne.dtnai","Personne.sexe","Prestation.rolepers","Adresse.codepos","Adresse.nomcom","Foyer.sitfam","Dossier.matricule"]},"tableaub7":{"defaults":{"Search":{"rdv_structurereferente":"1"}},"exportcsvcorpus":[]},"tableaub7d2typecontrat":{"defaults":{"Search":{"rdv_structurereferente":"1"}},"exportcsvcorpus":[]},"tableaub7d2familleprofessionnelle":{"defaults":{"Search":{"rdv_structurereferente":"1"}},"exportcsvcorpus":[]},"tableaub8":{"exportcsvcorpus":[]},"tableaub9":{"exportcsvcorpus":[]},"historiser":{"ini_set":{"memory_limit":"-1"}},"exportcsvcorpus":{"ini_set":{"memory_limit":"-1"}}}', 'Liste des valeurs par défaut des moteurs de recherches pour les tableaux
	  de suivi D1, D2, 1B3, 1B4, 1B5 et 1B6 (clé Tableauxsuivispdvs93.<tableau>.defaults).

	  Liste des champs pris en compte dans l''export CSV des corpus des tableaux
	  de suivi 1B3, 1B4, 1B5 et 1B6 (clé Tableauxsuivispdvs93.<tableau>.exportcsvcorpus).

	  La liste complète des champs utilisables pour chacun des tableaux se
	  trouvera dans le répertoire apptmplogs après le lancement du shell de
	  Prechargement, lorsque la valeur de "production" sera à true dans le fichier
	  appConfigcore.php.

	  Les fichiers concernés sont: Tableausuivipdv93__tableau1b3.csv, Tableausuivipdv93__tableau1b4.csv,
	  Tableausuivipdv93__tableau1b5.csv et Tableausuivipdv93__tableau1b6.csv.

	  Après avoir configuré ces champs, vérifiez qu''il n''y ait pas d''erreur en
	  vous rendant dans le partie "Vérification de l''application", onglet "Environnement logiciel"
	  > "WebRSA" > "Champs spécifiés dans le webrsa.inc" (ceux qui commencent par "Tableauxsuivispdvs93").

	  @var array


		array(
			''tableaud1'' => array(
				''defaults'' => array(
					''Search'' => array(
						''soumis_dd_dans_annee'' => ''1''
					)
				),
			),
			''tableaud2'' => array(
				''defaults'' => array(
					''Search'' => array(
						''soumis_dd_dans_annee'' => ''1''
					)
				),
			),
			''tableau1b3'' => array(
				''defaults'' => array(
					''Search'' => array(
						''dsps_maj_dans_annee'' => ''1''
					)
				),
				''exportcsvcorpus'' => array(
					 Rendez-vous
					''Rendezvous.daterdv'',
					''Structurereferente.lib_struc'',
					''Referent.nom_complet'',
					 Allocataire
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Personne.sexe'',
					''Prestation.rolepers'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Foyer.sitfam'',
					''Dossier.matricule'',
					 Difficultés exprimées
					''Difficulte.sante'',
					''Difficulte.logement'',
					''Difficulte.familiales'',
					''Difficulte.modes_gardes'',
					''Difficulte.surendettement'',
					''Difficulte.administratives'',
					''Difficulte.linguistiques'',
					''Difficulte.mobilisation'',
					''Difficulte.qualification_professionnelle'',
					''Difficulte.acces_emploi'',
					''Difficulte.autres''
				)
			),
			''tableau1b4'' => array(
				''defaults'' => array(
					''Search'' => array(
						''rdv_structurereferente'' => ''1''
					)
				),
				''exportcsvcorpus'' => array(
					 Fiche de prescription
					''Ficheprescription93.date_signature'',
					''Structurereferente.lib_struc'',
					''Referent.nom_complet'',
					 Allocataire
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Personne.sexe'',
					''Prestation.rolepers'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Foyer.sitfam'',
					''Dossier.matricule'',
					 Fiche de prescription
					''Thematiquefp93.type'',
					''Thematiquefp93.name'',
					''Thematiquefp93.yearthema'',
					''Categoriefp93.name'',
				)
			),
			''tableau1b5'' => array(
				''defaults'' => array(
					''Search'' => array(
						''rdv_structurereferente'' => ''1''
					)
				),
				''exportcsvcorpus'' => array(
					 Fiche de prescription
					''Ficheprescription93.date_signature'',
					''Structurereferente.lib_struc'',
					''Referent.nom_complet'',
					''Ficheprescription93.personne_a_integre'',
					''Ficheprescription93.personne_pas_deplace'',
					''Ficheprescription93.en_attente'',
					 Allocataire
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Personne.sexe'',
					''Prestation.rolepers'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Foyer.sitfam'',
					''Dossier.matricule'',
					 Fiche de prescription
					''Thematiquefp93.type'',
					''Thematiquefp93.name'',
					''Thematiquefp93.yearthema'',
					''Categoriefp93.name'',
				)
			),
			''tableau1b6'' => array(
				''defaults'' => array(
					''Search'' => array(
						''rdv_structurereferente'' => ''0''
					)
				),
				''exportcsvcorpus'' => array(
					 Rendez-vous
					''Rendezvous.daterdv'',
					''Thematiquerdv.name'',
					''Statutrdv.libelle'',
					''Structurereferente.lib_struc'',
					''Referent.nom_complet'',
					 Allocataire
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Personne.sexe'',
					''Prestation.rolepers'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Foyer.sitfam'',
					''Dossier.matricule'',
				)
			),
			''tableaub7'' => array(
				''defaults'' => array(
					''Search'' => array(
						''rdv_structurereferente'' => ''1''
					)
				),
				''exportcsvcorpus'' => array(
					''Personne.id'',
					''0.maintien'',
					''0.sortie_obligation'',
					''Structurereferente.lib_struc'',  PIE
					''Referent.nom'',  Référent
					''Referent.prenom'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Personne.sexe'',
					''Prestation.rolepers'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Foyer.sitfam'',
					''Dossier.matricule'',
					''0.inscritpoleemploi'', Inscrit à Pôle Emploi
					''Contratinsertion.dd_ci'',
					''Contratinsertion.df_ci'',
					 Thématique dernier CER
					''Typeemploi.name'',
					''Dureeemploi.name'',
					''Questionnaireb7pdv93.dateemploi'',
					''FamilleRomeV3.name'',
					''DomaineRomeV3.name'',
					''MetierRomeV3.name'',
					''AppellationRomeV3.name'',
					''Ficheprescription93.created'',
					''0.typethematiquefp93'',  Type
					''Thematiquefp93.name'',  Type
					''Categoriefp93.name'',
					''Filierefp93.name'',
					''0.personne_a_integre'',
					''0.personne_acheve'',
					''Motifactionachevefp93.name'',
				)
			),
			''tableaub7d2typecontrat'' => array(
				''defaults'' => array(
					''Search'' => array(
						''rdv_structurereferente'' => ''1''
					)
				),
				''exportcsvcorpus'' => array(
					''Personne.id'',
					''0.maintien'',
					''0.sortie_obligation'',
					''Structurereferente.lib_struc'',  PIE
					''Referent.nom'',  Référent
					''Referent.prenom'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Personne.sexe'',
					''Prestation.rolepers'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Foyer.sitfam'',
					''Dossier.matricule'',
					''0.inscritpoleemploi'', Inscrit à Pôle Emploi
					''Contratinsertion.dd_ci'',
					''Contratinsertion.df_ci'',
					 Thématique dernier CER
					''Typeemploi.name'',
					''Dureeemploi.name'',
					''Questionnaireb7pdv93.dateemploi'',
					''FamilleRomeV3.name'',
					''DomaineRomeV3.name'',
					''MetierRomeV3.name'',
					''AppellationRomeV3.name'',
					''Ficheprescription93.created'',
					''0.typethematiquefp93'',  Type
					''Thematiquefp93.name'',  Type
					''Categoriefp93.name'',
					''Filierefp93.name'',
					''0.personne_a_integre'',
					''0.personne_acheve'',
					''Motifactionachevefp93.name'',
				)
			),
			''tableaub7d2familleprofessionnelle'' => array(
				''defaults'' => array(
					''Search'' => array(
						''rdv_structurereferente'' => ''1''
					)
				),
				''exportcsvcorpus'' => array(
					''Personne.id'',
					''0.maintien'',
					''0.sortie_obligation'',
					''Structurereferente.lib_struc'',  PIE
					''Referent.nom'',  Référent
					''Referent.prenom'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Personne.sexe'',
					''Prestation.rolepers'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Foyer.sitfam'',
					''Dossier.matricule'',
					''0.inscritpoleemploi'', Inscrit à Pôle Emploi
					''Contratinsertion.dd_ci'',
					''Contratinsertion.df_ci'',
					 Thématique dernier CER
					''Typeemploi.name'',
					''Dureeemploi.name'',
					''Questionnaireb7pdv93.dateemploi'',
					''FamilleRomeV3.name'',
					''DomaineRomeV3.name'',
					''MetierRomeV3.name'',
					''AppellationRomeV3.name'',
					''Ficheprescription93.created'',
					''0.typethematiquefp93'',  Type
					''Thematiquefp93.name'',  Type
					''Categoriefp93.name'',
					''Filierefp93.name'',
					''0.personne_a_integre'',
					''0.personne_acheve'',
					''Motifactionachevefp93.name'',
				)
				),
				''tableaub8'' => array(
					''exportcsvcorpus'' => array(
						''Personne.qual'',
						''Personne.nom'',
						''Personne.prenom'',
						''Personne.dtnai'',
						''Adresse.numvoie'',
						''Adresse.libtypevoie'',
						''Adresse.nomvoie'',
						''Adresse.complideadr'',
						''Adresse.codepos'',
						''Adresse.nomcom'',
						''Dossier.dtdemrsa'',
						''Dossier.numdemrsa'',
						''Dossier.matricule'',
						''Orientstruct.statut_orient'',
						''struc_orientation.lib_struc'',
						''Orientstruct.date_valid'',
						''struc_signataire_cer.lib_struc'',
						''Contratinsertion.dd_ci'',
						''Contratinsertion.df_ci'',
						''Contratinsertion.rg_ci'',
						''Cer93.positioncer'',
						''Cer93.datesignature'',
						''Cer93.created'',
						''Cer93.modified'',
						''Referent.qual'',
						''Referent.nom'',
						''Referent.prenom'',
						''Referent.fonction''
					)
				),
				''tableaub9'' => array(
					''exportcsvcorpus'' => array(
						''Communautesr.name'',
						''Structurereferente.lib_struc'',
						''Personne.qual'',
						''Personne.nom'',
						''Personne.prenom'',
						''Personne.dtnai'',
						''Adresse.numvoie'',
						''Adresse.libtypevoie'',
						''Adresse.nomvoie'',
						''Adresse.complideadr'',
						''Adresse.codepos'',
						''Adresse.nomcom'',
						''Dossier.numdemrsa'',
						''Dossier.matricule'',
						''Orientstruct.statut_orient'',
						''Structurereferente.lib_struc'',
						''Contratinsertion.dd_ci'',
						''Contratinsertion.df_ci'',
						''Contratinsertion.rg_ci'',
						''Cer93.positioncer'',
						''Cer93.datesignature'',
						''Cer93.created'',
						''Cer93.modified'',
						''Referent.qual'',
						''Referent.nom'',
						''Referent.prenom'',
						''Referent.fonction'',
						''Typerdv.libelle'',
						''Statutrdv.libelle'',
						''Rendezvous.daterdv'',
						''Rendezvous.commentairerdv'',
						''Rendezvous.objetrdv''
					)
				)
		)', 34, current_timestamp, current_timestamp),
	('Tableauxsuivispdvs93.historiser.ini_set', '{"memory_limit":"-1"}', 'Pour les tableaux de suivi PDV du CG 93, on ne met pas de limite à la
	  mémoire disponible pour les actions historiser et exportcsvcorpus.


		array(
			''memory_limit'' => ''-1''
		)', 34, current_timestamp, current_timestamp),
	('Tableauxsuivispdvs93.exportcsvcorpus.ini_set', '{"memory_limit":"-1"}', 'array(
			''memory_limit'' => ''-1''
		)', 34, current_timestamp, current_timestamp),
	('ConfigurableQuery.Propospdos.search_possibles', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":{"year":"2020","month":"04","day":"22"},"dtdemrsa_to":{"year":"2020","month":"04","day":"29"}}},"accepted":{"Situationdossierrsa.etatdosrsa":["0"]},"skip":[]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["0"]},"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet","2":"Situationdossierrsa.etatdosrsa","\/Propospdos\/index\/#Personne.id#":{"class":"view"}},"innerTable":{"0":"Personne.nomcomnai","1":"Personne.dtnai","Adresse.numcom":{"options":[]},"2":"Personne.nir","3":"Dossier.matricule","4":"Prestation.rolepers","5":"Structurereferenteparcours.lib_struc","6":"Referentparcours.nom_complet"}},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}', 'Menu "Recherches" > "Par PDOs" > "Nouvelles PDOs"

		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
						 Case à cocher "Filtrer par date de demande RSA"
						''dtdemrsa'' => ''0'',
						 Du (inclus)
						''dtdemrsa_from'' => date_sql_to_cakephp( date( ''Y-m-d'', strtotime( ''-1 week'' ) ) ),
						 Au (inclus)
						''dtdemrsa_to'' => date_sql_to_cakephp( date( ''Y-m-d'', strtotime( ''now'' ) ) ),
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(
					''Situationdossierrsa.etatdosrsa'' => array( ''0'' )
				),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Situationdossierrsa.etatdosrsa_choice'' => ''1'',
					''Situationdossierrsa.etatdosrsa'' => array( ''0'' )
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Personne.nom_complet'',
					''Situationdossierrsa.etatdosrsa'',
					''Propospdosindex#Personne.id#'' => array(
						''class'' => ''view''
					)
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Personne.nomcomnai'',
					''Personne.dtnai'',
					''Adresse.numcom'' => array(
						''options'' => array()
					),
					''Personne.nir'',
					''Dossier.matricule'',
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''1024M''
			)
		)', 35, current_timestamp, current_timestamp),
	('ConfigurableQuery.Propospdos.exportcsv_possibles', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":{"year":"2020","month":"04","day":"22"},"dtdemrsa_to":{"year":"2020","month":"04","day":"29"}}},"accepted":{"Situationdossierrsa.etatdosrsa":["0"]},"skip":[]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["0"]},"conditions":[],"order":[]},"results":{"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet","2":"Situationdossierrsa.etatdosrsa","3":"Personne.nomcomnai","4":"Personne.dtnai","Adresse.numcom":{"options":[]},"5":"Personne.nir","6":"Dossier.matricule","7":"Prestation.rolepers","8":"Structurereferenteparcours.lib_struc","9":"Referentparcours.nom_complet"}},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}', 'Export CSV, menu "Recherches" > "Par PDOs" > "Nouvelles PDOs"

		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Propospdos.search_possibles.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Propospdos.search_possibles.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Personne.nom_complet'',
					''Situationdossierrsa.etatdosrsa'',
					''Personne.nomcomnai'',
					''Personne.dtnai'',
					''Adresse.numcom'' => array(
						''options'' => array()
					),
					''Personne.nir'',
					''Dossier.matricule'',
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Propospdos.search_possibles.ini_set'' ),
		)', 35, current_timestamp, current_timestamp),
	('ConfigurableQuery.Propospdos.search', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":{"year":"2020","month":"04","day":"22"},"dtdemrsa_to":{"year":"2020","month":"04","day":"29"}}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":["Dossier.numdemrsa","Personne.nom_complet","Decisionpdo.libelle","Originepdo.libelle","Propopdo.motifpdo","Propopdo.datereceptionpdo","User.nom_complet","Propopdo.etatdossierpdo","\/Propospdos\/index\/#Propopdo.personne_id#"],"innerTable":{"0":"Situationdossierrsa.etatdosrsa","1":"Personne.nomcomnai","2":"Personne.dtnai","Adresse.numcom":{"options":[]},"3":"Personne.nir","4":"Dossier.matricule","5":"Prestation.rolepers","6":"Structurereferenteparcours.lib_struc","7":"Referentparcours.nom_complet"}},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}', 'Menu "Recherches" > "Par PDOs" > "Liste des PDOs"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
						 Case à cocher "Filtrer par date de demande RSA"
						''dtdemrsa'' => ''0'',
						 Du (inclus)
						''dtdemrsa_from'' => date_sql_to_cakephp( date( ''Y-m-d'', strtotime( ''-1 week'' ) ) ),
						 Au (inclus)
						''dtdemrsa_to'' => date_sql_to_cakephp( date( ''Y-m-d'', strtotime( ''now'' ) ) ),
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Personne.nom_complet'',
					''Decisionpdo.libelle'',
					''Originepdo.libelle'',
					''Propopdo.motifpdo'',
					''Propopdo.datereceptionpdo'',
					''User.nom_complet'',
					''Propopdo.etatdossierpdo'',
					''Propospdosindex#Propopdo.personne_id#''
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Situationdossierrsa.etatdosrsa'',
					''Personne.nomcomnai'',
					''Personne.dtnai'',
					''Adresse.numcom'' => array(
						''options'' => array()
					),
					''Personne.nir'',
					''Dossier.matricule'',
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''1024M''
			)
		)', 35, current_timestamp, current_timestamp),
		('ConfigurableQuery.Propospdos.exportcsv', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":{"year":"2020","month":"04","day":"22"},"dtdemrsa_to":{"year":"2020","month":"04","day":"29"}}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"results":{"fields":["Dossier.numdemrsa","Personne.nom_complet","Dossier.matricule","Adresse.numvoie","Adresse.libtypevoie","Adresse.nomvoie","Adresse.complideadr","Adresse.compladr","Adresse.codepos","Adresse.nomcom","Decisionpdo.libelle","Propopdo.motifpdo","Decisionpropopdo.datedecisionpdo","User.nom_complet","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}', 'Export CSV, menu "Recherches" > "Par PDOs" > "Liste des PDOs"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Propospdos.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Propospdos.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Personne.nom_complet'',
					''Dossier.matricule'',
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Decisionpdo.libelle'',
					''Propopdo.motifpdo'',
					''Decisionpropopdo.datedecisionpdo'',
					''User.nom_complet'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Propospdos.search.ini_set'' ),
		)', 35, current_timestamp, current_timestamp),
	('ConfigurableQuery.Propospdos.cohorte_nouvelles', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":{"year":"2020","month":"04","day":"22"},"dtdemrsa_to":{"year":"2020","month":"04","day":"29"}}},"accepted":{"Situationdossierrsa.etatdosrsa":["0"]},"skip":[]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["0"]},"conditions":[],"order":["Dossier.dtdemrsa","Propopdo.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.nom_complet_court":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Adresse.nomcom":{"sort":false},"\/Propospdos\/index\/#Propopdo.personne_id#":{"class":"external"}},"innerTable":{"0":"Dossier.numdemrsa","1":"Personne.dtnai","2":"Dossier.matricule","3":"Personne.nir","Adresse.numcom":{"options":[]},"4":"Situationdossierrsa.etatdosrsa","5":"Structurereferenteparcours.lib_struc","6":"Referentparcours.nom_complet"}},"ini_set":[]}', 'Menu "Cohortes" > "PDOs" > "Nouvelles demandes"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
						 Case à cocher "Filtrer par date de demande RSA"
						''dtdemrsa'' => ''0'',
						 Du (inclus)
						''dtdemrsa_from'' => date_sql_to_cakephp( date( ''Y-m-d'', strtotime( ''-1 week'' ) ) ),
						 Au (inclus)
						''dtdemrsa_to'' => date_sql_to_cakephp( date( ''Y-m-d'', strtotime( ''now'' ) ) ),
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(
					''Situationdossierrsa.etatdosrsa'' => array( ''0'' )
				),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Situationdossierrsa.etatdosrsa_choice'' => ''1'',
					''Situationdossierrsa.etatdosrsa'' => array( ''0'' )
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array( ''Dossier.dtdemrsa'', ''Propopdo.id'' )
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Personne.nom_complet_court'' => array(
						''sort'' => false
					),
					''Dossier.dtdemrsa'' => array(
						''sort'' => false
					),
					''Adresse.nomcom'' => array(
						''sort'' => false
					),
					''Propospdosindex#Propopdo.personne_id#'' => array(
						''class'' => ''external''
					)
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Dossier.numdemrsa'',
					''Personne.dtnai'',
					''Dossier.matricule'',
					''Personne.nir'',
					''Adresse.numcom'' => array(
						''options'' => array()
					),
					''Situationdossierrsa.etatdosrsa'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 35, current_timestamp, current_timestamp),
	('ConfigurableQuery.Propospdos.cohorte_validees', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":{"year":"2020","month":"04","day":"22"},"dtdemrsa_to":{"year":"2020","month":"04","day":"29"}}},"accepted":{"Situationdossierrsa.etatdosrsa":["0"]},"skip":[]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["0"]},"conditions":[],"order":["Dossier.dtdemrsa","Propopdo.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Personne.nom_complet_court","1":"Dossier.matricule","2":"Adresse.nomcom","3":"Dossier.dtdemrsa","4":"User.nom_complet","5":"Decisionpropopdo.commentairepdo","\/Propospdos\/index\/#Propopdo.personne_id#":{"class":"external"}},"innerTable":{"0":"Personne.dtnai","1":"Personne.nir","Adresse.numcom":{"options":[]},"2":"Structurereferenteparcours.lib_struc","3":"Referentparcours.nom_complet"}},"ini_set":[]}', 'Menu "Cohortes" > "PDOs" > "Liste PDOs"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1'',
						 Case à cocher "Filtrer par date de demande RSA"
						''dtdemrsa'' => ''0'',
						 Du (inclus)
						''dtdemrsa_from'' => date_sql_to_cakephp( date( ''Y-m-d'', strtotime( ''-1 week'' ) ) ),
						 Au (inclus)
						''dtdemrsa_to'' => date_sql_to_cakephp( date( ''Y-m-d'', strtotime( ''now'' ) ) ),
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(
					''Situationdossierrsa.etatdosrsa'' => array( ''0'' )
				),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Situationdossierrsa.etatdosrsa_choice'' => ''1'',
					''Situationdossierrsa.etatdosrsa'' => array( ''0'' )
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array( ''Dossier.dtdemrsa'', ''Propopdo.id'' )
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Personne.nom_complet_court'',
					''Dossier.matricule'',
					''Adresse.nomcom'',
					''Dossier.dtdemrsa'',
					''User.nom_complet'',
					''Decisionpropopdo.commentairepdo'',
					''Propospdosindex#Propopdo.personne_id#'' => array(
						''class'' => ''external''
					)
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Personne.dtnai'',
					''Personne.nir'',
					''Adresse.numcom'' => array(
						''options'' => array()
					),
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 35, current_timestamp, current_timestamp),
	('ConfigurableQuery.Propospdos.exportcsv_validees', '{"filters":{"defaults":{"Dossier":{"dernier":"1","dtdemrsa":"0","dtdemrsa_from":{"year":"2020","month":"04","day":"22"},"dtdemrsa_to":{"year":"2020","month":"04","day":"29"}}},"accepted":{"Situationdossierrsa.etatdosrsa":["0"]},"skip":[]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["0"]},"conditions":[],"order":["Dossier.dtdemrsa","Propopdo.id"]},"results":{"fields":{"Dossier.numdemrsa":{"label":"N° demande RSA"},"Dossier.dtdemrsa":{"label":"Date demande RSA"},"Personne.nom_complet_court":{"label":"Nom\/Prénom allocataire"},"0":"Personne.dtnai","Adresse.nomcom":{"label":"Commune"},"Typepdo.libelle":{"label":"Type de PDO"},"Decisionpropopdo.datedecisionpdo":{"label":"Date de soumission PDO"},"Decisionpdo.libelle":{"label":"Décision PDO"},"Propopdo.motifpdo":{"label":"Motif PDO"},"Decisionpropopdo.commentairepdo":{"label":"Commentaires PDO"},"1":"User.nom_complet","2":"Structurereferenteparcours.lib_struc","3":"Referentparcours.nom_complet"}},"ini_set":[]}', 'Export CSV, menu "Cohortes" > "PDOs" > "Liste PDOs"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Propospdos.cohorte_validees.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Propospdos.cohorte_validees.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'' => array(
						''label'' => ''N° demande RSA''
					),
					''Dossier.dtdemrsa'' => array(
						''label'' => ''Date demande RSA''
					),
					''Personne.nom_complet_court'' => array(
						''label'' => ''NomPrénom allocataire'',
					),
					''Personne.dtnai'',
					''Adresse.nomcom'' => array(
						''label'' => ''Commune'',
					),
					''Typepdo.libelle'' => array(
						''label'' => ''Type de PDO'',
					),
					''Decisionpropopdo.datedecisionpdo'' => array(
						''label'' => ''Date de soumission PDO'',
					),
					''Decisionpdo.libelle'' => array(
						''label'' => ''Décision PDO'',
					),
					''Propopdo.motifpdo'' => array(
						''label'' => ''Motif PDO'',
					),
					''Decisionpropopdo.commentairepdo'' => array(
						''label'' => ''Commentaires PDO'',
					),
					''User.nom_complet'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Propospdos.cohorte_validees.ini_set'' ),
		)', 35, current_timestamp, current_timestamp),
		('ConfigurableQuery.PersonnesReferents.cohorte_affectation93', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Situationdossierrsa":{"etatdosrsa_choice":"1","etatdosrsa":[2,3,4]}},"accepted":[],"skip":["Detailcalculdroitrsa.natpf","Detaildroitrsa.oridemrsa","Dossier.anciennete_dispositif","Serviceinstructeur.id","Dossier.fonorg","Foyer.sitfam","Personne.sexe"],"has":["Dsp","Contratinsertion"]},"query":{"restrict":[],"conditions":[],"order":{"Personne.situation":"ASC","0":"Orientstruct.date_valid ASC","1":"Personne.nom ASC","2":"Personne.prenom ASC"}},"limit":10,"auto":true,"results":{"header":[],"fields":{"0":"Adresse.nomcom","1":"Dossier.dtdemrsa","2":"Orientstruct.date_valid","3":"Personne.dtnai","Calculdroitrsa.toppersdrodevorsa":{"type":"boolean"},"Personne.has_dsp":{"type":"boolean"},"4":"Personne.nom_complet_court","5":"Contratinsertion.rg_ci","6":"Cer93.positioncer","7":"Contratinsertion.df_ci","8":"PersonneReferent.dddesignation","9":"Structurereferentepcd.lib_struc","\/PersonnesReferents\/index\/#Personne.id#":{"title":false}},"innerTable":{"0":"Dossier.numdemrsa","1":"Dossier.dtdemrsa","2":"Personne.dtnai","3":"Dossier.matricule","4":"Personne.nir","5":"Adresse.codepos","6":"Situationdossierrsa.dtclorsa","7":"Situationdossierrsa.moticlorsa","8":"Prestation.rolepers","9":"Dsp.exists","Adresse.complete":{"label":"Adresse"},"Contratinsertion.interne":{"label":"CER signé dans la structure"},"Personne.situation":{"label":"Situation allocataire"}}},"ini_set":[]}', 'Menu "Cohortes" > "Orientation" > "Demandes non orientées"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						''dernier'' => ''1''
					),
					''Situationdossierrsa'' => array(
						''etatdosrsa_choice'' => ''1'',
						''etatdosrsa'' => array( 2, 3, 4 )
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(
					''Detailcalculdroitrsa.natpf'',
					''Detaildroitrsa.oridemrsa'',
					''Dossier.anciennete_dispositif'',
					''Serviceinstructeur.id'',
					''Dossier.fonorg'',
					''Foyer.sitfam'',
					''Personne.sexe'',
				),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array(
					''Dsp'',
					''Contratinsertion''
				)
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(
					''Personne.situation'' => ''ASC'',
					''Orientstruct.date_valid ASC'',
					''Personne.nom ASC'',
					''Personne.prenom ASC'',
				)
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => true,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Adresse.nomcom'',
					''Dossier.dtdemrsa'',
					''Orientstruct.date_valid'',
					''Personne.dtnai'',
					''Calculdroitrsa.toppersdrodevorsa'' => array(
						''type'' => ''boolean''
					),
					''Personne.has_dsp'' => array(
						''type'' => ''boolean''
					),
					''Personne.nom_complet_court'',
					''Contratinsertion.rg_ci'',
					''Cer93.positioncer'',
					''Contratinsertion.df_ci'',
					''PersonneReferent.dddesignation'',
					''Structurereferentepcd.lib_struc'',
					''PersonnesReferentsindex#Personne.id#'' => array(
						''title'' => false
					)
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.dtnai'',
					''Dossier.matricule'',
					''Personne.nir'',
					''Adresse.codepos'',
					''Situationdossierrsa.dtclorsa'',
					''Situationdossierrsa.moticlorsa'',
					''Prestation.rolepers'',
					''Dsp.exists'',
					''Adresse.complete'' => array(
						''label'' => ''Adresse''
					),
					''Contratinsertion.interne'' => array(
						''label'' => ''CER signé dans la structure''
					),
					''Personne.situation'' => array(
						''label'' => ''Situation allocataire''
					)
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 36, current_timestamp, current_timestamp),
		('ConfigurableQuery.PersonnesReferents.exportcsv_affectation93', '{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Situationdossierrsa":{"etatdosrsa_choice":"1","etatdosrsa":[2,3,4]}},"accepted":[],"skip":["Detailcalculdroitrsa.natpf","Detaildroitrsa.oridemrsa","Dossier.anciennete_dispositif","Serviceinstructeur.id","Dossier.fonorg","Foyer.sitfam","Personne.sexe"],"has":["Dsp","Contratinsertion"]},"query":{"restrict":[],"conditions":[],"order":{"Personne.situation":"ASC","0":"Orientstruct.date_valid ASC","1":"Personne.nom ASC","2":"Personne.prenom ASC"}},"results":{"fields":{"0":"Adresse.nomcom","1":"Dossier.dtdemrsa","2":"Orientstruct.date_valid","3":"Personne.dtnai","4":"Calculdroitrsa.toppersdrodevorsa","Dsp.exists":{"type":"boolean","label":"Présence d''une DSP"},"5":"Personne.nom_complet_court","6":"Contratinsertion.rg_ci","7":"Cer93.positioncer","8":"Contratinsertion.df_ci","PersonneReferent.dddesignation":{"label":"Date de début d''affectation"},"Referent.nom_complet":{"label":"Affectation"},"Dossier.numdemrsa":{"label":"N° de dossier"},"9":"Dossier.matricule","10":"Personne.nir","11":"Adresse.codepos","12":"Situationdossierrsa.dtclorsa","13":"Situationdossierrsa.moticlorsa","Prestation.rolepers":{"label":"Rôle"},"14":"Situationdossierrsa.etatdosrsa","15":"Adresse.complete","Contratinsertion.interne":{"label":"CER signé dans la structure"}}},"ini_set":[]}', 'Export CSV, mpenu "Cohortes" > "Orientation" > "Demandes non orientées"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.PersonnesReferents.cohorte_affectation93.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.PersonnesReferents.cohorte_affectation93.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Adresse.nomcom'',
					''Dossier.dtdemrsa'',
					''Orientstruct.date_valid'',
					''Personne.dtnai'',
					''Calculdroitrsa.toppersdrodevorsa'',
					''Dsp.exists'' => array(
						''type'' => ''boolean'',
						''label'' => ''Présence d''une DSP''
					),
					''Personne.nom_complet_court'',
					''Contratinsertion.rg_ci'',
					''Cer93.positioncer'',
					''Contratinsertion.df_ci'',
					''PersonneReferent.dddesignation'' => array(
						''label'' => ''Date de début d''affectation''
					),
					''Referent.nom_complet'' => array(
						''label'' => ''Affectation''
					),
					''Dossier.numdemrsa'' => array(
						''label'' => ''N° de dossier''
					),
					''Dossier.matricule'',
					''Personne.nir'',
					''Adresse.codepos'',
					''Situationdossierrsa.dtclorsa'',
					''Situationdossierrsa.moticlorsa'',
					''Prestation.rolepers'' => array(
						''label'' => ''Rôle''
					),
					''Situationdossierrsa.etatdosrsa'',
					''Adresse.complete'',
					''Contratinsertion.interne'' => array(
						''label'' => ''CER signé dans la structure''
					)
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.PersonnesReferents.cohorte_affectation93.ini_set'' ),
		)', 36, current_timestamp, current_timestamp),
		('CSVImport.FRSA.Beneficiaire.Correspondances', '["Personne.id","Personne.numfixe","Personne.numport","Personne.email","Personnelangue.maternelles","Personnelangue.francais_niveau","Personnelangue.francais_niveau_validation","Personnelangue.niveaux_professionnels","Personnefrsadiplomexper.nivetu","Personnefrsadiplomexper.diplome","Personnefrsadiplomexper.expprof","Personnefrsadiplomexper.formations","Personnefrsadiplomexper.permisb","Personnefrsadiplomexper.autreexpersavoir"]', 'Liste des champs qui correspondent aux entêtes dans le même ordre


		array (
			''Personne.id'',
			''Personne.numfixe'',
			''Personne.numport'',
			''Personne.email'',
			''Personnelangue.maternelles'',
			''Personnelangue.francais_niveau'',
			''Personnelangue.francais_niveau_validation'',
			''Personnelangue.niveaux_professionnels'',
			''Personnefrsadiplomexper.nivetu'',
			''Personnefrsadiplomexper.diplome'',
			''Personnefrsadiplomexper.expprof'',
			''Personnefrsadiplomexper.formations'',
			''Personnefrsadiplomexper.permisb'',
			''Personnefrsadiplomexper.autreexpersavoir''
		)', 37, current_timestamp, current_timestamp),
	('CSVImport.FRSA.Positionnement.ModelDetails', '{"Ficheprescription93":{"paths":["Ficheprescription93.id","Ficheprescription93.frsa_id","Ficheprescription93.rdvprestataire_date","Ficheprescription93.motifcontactfp93_id","Ficheprescription93.benef_retour_presente","Ficheprescription93.personne_retenue","Ficheprescription93.motifnonretenuefp93_id","Ficheprescription93.personne_nonretenue_autre","Ficheprescription93.personne_a_integre","Ficheprescription93.motifnonintegrationfp93_id","Ficheprescription93.personne_nonintegre_autre","Ficheprescription93.personne_acheve","Ficheprescription93.motifnonactionachevefp93_id","Ficheprescription93.personne_acheve_autre","Ficheprescription93.motifactionachevefp93_id","Ficheprescription93.personne_nonacheve_autre"]}}', 'Champs à gérer pour chaque Modele


		array (
			''Ficheprescription93'' => array(
				''paths'' => array(
					''Ficheprescription93.id'',
					''Ficheprescription93.frsa_id'',
					''Ficheprescription93.rdvprestataire_date'',
					''Ficheprescription93.motifcontactfp93_id'',
					''Ficheprescription93.benef_retour_presente'',
					''Ficheprescription93.personne_retenue'',
					''Ficheprescription93.motifnonretenuefp93_id'',
					''Ficheprescription93.personne_nonretenue_autre'',
					''Ficheprescription93.personne_a_integre'',
					''Ficheprescription93.motifnonintegrationfp93_id'',
					''Ficheprescription93.personne_nonintegre_autre'',
					''Ficheprescription93.personne_acheve'',
					''Ficheprescription93.motifnonactionachevefp93_id'',
					''Ficheprescription93.personne_acheve_autre'',
					''Ficheprescription93.motifactionachevefp93_id'',
					''Ficheprescription93.personne_nonacheve_autre''
				)
			)
		)', 37, current_timestamp, current_timestamp),
	('CSVImport.FRSA.CataloguePDIE.Headers', '["action_identifiant","numero_convention","sous_parcours","fiche_action","filiere","porteur_projet","intitule","adresse","code_postal","commune","action_date_debut","duree_en_mois","suivi_contact_telephone","candidatures_date_debut"]', 'Imports CSV :

	  Headers : Entêtes du fichier CSV à lire
	  Correspondance : Correspondance entre les entêtes et les Champs des Models
	  processModelsDetails : Champs à gérer pour chaque Modêle





	  Import CSV FRSA Catalogue PDIE



	   Liste des entêtes du fichier CSV à lire
	   ''Identifiant FRSA'',
	   ''Numero Convention Action'',
	   ''Thematique'',
	   ''Categorie Action'',
	   ''Filiere'',
	   ''Prestataire'',
	   ''Intitulé d''Action'',
	   ''Adresse Action'',
	   ''CP Action'',
	   ''Commune Action'',
	   '''',
	   ''Duree Action'',
	   ''Tel_Action'',
	   Annee''



		array (
			''action_identifiant'',
			''numero_convention'',
			''sous_parcours'',
			''fiche_action'',
			''filiere'',
			''porteur_projet'',
			''intitule'',
			''adresse'',
			''code_postal'',
			''commune'',
			''action_date_debut'',
			''duree_en_mois'',
			''suivi_contact_telephone'',
			''candidatures_date_debut''
		)', 37, current_timestamp, current_timestamp),
	('CSVImport.FRSA.CataloguePDIE.Correspondances', '["Actionfp93.frsa_id","Actionfp93.numconvention","Thematiquefp93.name","Categoriefp93.name","Filierefp93.name","Prestatairefp93.name","Actionfp93.name","Adresseprestatairefp93.adresse","Adresseprestatairefp93.codepos","Adresseprestatairefp93.localite","Actionfp93.annee","Actionfp93.duree","Adresseprestatairefp93.tel","Thematiquefp93.yearthema"]', 'Liste des champs qui correspondent aux entêtes dans le même ordre


		array (
			''Actionfp93.frsa_id'',
			''Actionfp93.numconvention'',
			''Thematiquefp93.name'',
			''Categoriefp93.name'',
			''Filierefp93.name'',
			''Prestatairefp93.name'',
			''Actionfp93.name'',
			''Adresseprestatairefp93.adresse'',
			''Adresseprestatairefp93.codepos'',
			''Adresseprestatairefp93.localite'',
			''Actionfp93.annee'',
			''Actionfp93.duree'',
			''Adresseprestatairefp93.tel'',
			''Thematiquefp93.yearthema''
		)', 37, current_timestamp, current_timestamp),
	('CSVImport.FRSA.CataloguePDIE.ModelDetails', '{"Thematiquefp93":{"paths":["Thematiquefp93.type","Thematiquefp93.name","Thematiquefp93.yearthema"]},"Categoriefp93":{"paths":["Categoriefp93.thematiquefp93_id","Categoriefp93.name"]},"Filierefp93":{"paths":["Filierefp93.categoriefp93_id","Filierefp93.name"]},"Prestatairefp93":{"paths":["Prestatairefp93.name"]},"Adresseprestatairefp93":{"paths":["Adresseprestatairefp93.prestatairefp93_id","Adresseprestatairefp93.adresse","Adresseprestatairefp93.codepos","Adresseprestatairefp93.localite","Adresseprestatairefp93.tel"]},"Actionfp93":{"paths":["Actionfp93.frsa_id","Actionfp93.filierefp93_id","Actionfp93.adresseprestatairefp93_id","Actionfp93.numconvention","Actionfp93.name","Actionfp93.duree","Actionfp93.annee"],"complement":{"Actionfp93.actif":"1"}}}', 'Champs à gérer pour chaque Modele


		array (
			''Thematiquefp93'' => array(
				''paths'' => array(
					''Thematiquefp93.type'',
					''Thematiquefp93.name'',
					''Thematiquefp93.yearthema''
				)
			),
			''Categoriefp93'' => array(
				''paths'' => array(
					''Categoriefp93.thematiquefp93_id'',
					''Categoriefp93.name''
				)
			),
			''Filierefp93'' => array(
				''paths'' => array(
					''Filierefp93.categoriefp93_id'',
					''Filierefp93.name''
				)
			),
			''Prestatairefp93'' => array(
				''paths'' => array(
					''Prestatairefp93.name''
				)
			),
			''Adresseprestatairefp93'' => array(
				''paths'' => array(
					''Adresseprestatairefp93.prestatairefp93_id'',
					''Adresseprestatairefp93.adresse'',
					''Adresseprestatairefp93.codepos'',
					''Adresseprestatairefp93.localite'',
					''Adresseprestatairefp93.tel''
				)
			),
			''Actionfp93'' => array(
				''paths'' => array(
					''Actionfp93.frsa_id'',
					''Actionfp93.filierefp93_id'',
					''Actionfp93.adresseprestatairefp93_id'',
					''Actionfp93.numconvention'',
					''Actionfp93.name'',
					''Actionfp93.duree'',
					''Actionfp93.annee'',
				),
				''complement'' => array(
					''Actionfp93.actif'' => ''1''
				)
			),
		)', 37, current_timestamp, current_timestamp),
	('CSVImport.FRSA.Beneficiaire.Headers', '["identifiant","telephone_fixe","telephone_mobile","email","profil_langues_maternelles","profil_francais_niveau","profil_francais_niveau_validation","profil_langues_niveaux_professionnels","profil_etudes_niveau","profil_diplomes_ou_certifications","profil_experiences_professionnelles","profil_autres_formations_ou_certifications","profil_permis_b","profil_autres_experiences_et_savoirs"]', 'Import CSV FRSA Beneficiaire


	  Liste des entêtes du fichier CSV à lire
	  Personne -> Identifiant,
	  Personne -> numfixe  et Infocontactpersonne -> fixe
	  Personne -> numport  et Infocontactpersonne -> mobile
	  Personne -> email  et Infocontactpersonne -> email
	  Personnelangue -> maternelles
	  Personnelangue -> francais_niveau
	  Personnelangue -> francais_niveau_validation
	  ''Personnelangue -> niveaux_professionnels
	  Personnefrsadiplomexper -> nivetu
	  ''Personnefrsadiplomexper -> diplome
	  ''Personnefrsadiplomexper -> expprof
	  Personnefrsadiplomexper -> formations
	  Personnefrsadiplomexper -> permisb
	  Personnefrsadiplomexper -> autreexpersavoir



		array (
			''identifiant'',
			''telephone_fixe'',
			''telephone_mobile'',
			''email'',
			''profil_langues_maternelles'',
			''profil_francais_niveau'',
			''profil_francais_niveau_validation'',
			''profil_langues_niveaux_professionnels'',
			''profil_etudes_niveau'',
			''profil_diplomes_ou_certifications'',
			''profil_experiences_professionnelles'',
			''profil_autres_formations_ou_certifications'',
			''profil_permis_b'',
			''profil_autres_experiences_et_savoirs''
		)', 37, current_timestamp, current_timestamp),
	('CSVImport.FRSA.Beneficiaire.ModelDetails', '{"Personne":{"paths":["Personne.id","Personne.numfixe","Personne.numport","Personne.email"]},"Personnelangue":{"paths":["Personnelangue.personne_id","Personnelangue.maternelles","Personnelangue.francais_niveau","Personnelangue.francais_niveau_validation","Personnelangue.niveaux_professionnels"]},"Personnefrsadiplomexper":{"paths":["Personnefrsadiplomexper.personne_id","Personnefrsadiplomexper.nivetu","Personnefrsadiplomexper.diplome","Personnefrsadiplomexper.expprof","Personnefrsadiplomexper.formations","Personnefrsadiplomexper.permisb","Personnefrsadiplomexper.autreexpersavoir"]},"Infocontactpersonne":{"paths":["Infocontactpersonne.personne_id","Infocontactpersonne.fixe","Infocontactpersonne.mobile","Infocontactpersonne.email"]}}', 'Champs à gérer pour chaque Modele


		array (
			''Personne'' => array(
				''paths'' => array(
					''Personne.id'',
					''Personne.numfixe'',
					''Personne.numport'',
					''Personne.email'',
				)
			),
			''Personnelangue'' => array(
				''paths'' => array(
					''Personnelangue.personne_id'',
					''Personnelangue.maternelles'',
					''Personnelangue.francais_niveau'',
					''Personnelangue.francais_niveau_validation'',
					''Personnelangue.niveaux_professionnels''
				)
			),
			''Personnefrsadiplomexper'' => array(
				''paths'' => array(
					''Personnefrsadiplomexper.personne_id'',
					''Personnefrsadiplomexper.nivetu'',
					''Personnefrsadiplomexper.diplome'',
					''Personnefrsadiplomexper.expprof'',
					''Personnefrsadiplomexper.formations'',
					''Personnefrsadiplomexper.permisb'',
					''Personnefrsadiplomexper.autreexpersavoir''
				)
			),
			''Infocontactpersonne'' => array(
				''paths'' => array(
					''Infocontactpersonne.personne_id'',
					''Infocontactpersonne.fixe'',
					''Infocontactpersonne.mobile'',
					''Infocontactpersonne.email'',
				)
			)
		)', 37, current_timestamp, current_timestamp),
	('CSVImport.FRSA.Positionnement.Headers', '["identifiant_webrsa","identifiant_frsa","date_entretien","motif_entretien","beneficiaire_present","beneficiaire_retenu","beneficiaire_rejete_motif","beneficiaire_rejete_motif_autre","beneficiaire_integre_action","beneficiaire_pas_integre_action_raison","beneficiaire_pas_integre_action_raison_autre","beneficiaire_termine_action","beneficaire_abandon_action_raison","beneficaire_abandon_action_raison_autre","resultat_action","resultat_action_autre"]', 'Import CSV FRSA Positionnements



	  Liste des entêtes du fichier CSV à lire

	  Ficheprescription93 -> id,
	  Ficheprescription93 -> frsa_id,
	  Ficheprescription93 -> rdvprestataire_date
	  Ficheprescription93 -> motifcontactfp93_id
	  Ficheprescription93 -> benef_retour_presente
	  Ficheprescription93 -> personne_retenue
	  Ficheprescription93 -> motifnonretenuefp93_id
	  Ficheprescription93 -> personne_nonretenue_autre
	  Ficheprescription93 -> personne_a_integre
	  Ficheprescription93 -> motifnonintegrationfp93_id
	  Ficheprescription93 -> personne_nonintegre_autre
	  Ficheprescription93 -> personne_acheve
	  Ficheprescription93 -> motifnonactionachevefp93_id
	  Ficheprescription93 -> personne_acheve_autre
	  Ficheprescription93 -> motifactionachevefp93_id
	  Ficheprescription93 -> personne_acheve_autre
	  Ficheprescription93 -> date_bilan_mi_parcours
	  Ficheprescription93 -> date_bilan_final



		array (
			''identifiant_webrsa'',
			''identifiant_frsa'',
			''date_entretien'',
			''motif_entretien'',
			''beneficiaire_present'',
			''beneficiaire_retenu'',
			''beneficiaire_rejete_motif'',
			''beneficiaire_rejete_motif_autre'',
			''beneficiaire_integre_action'',
			''beneficiaire_pas_integre_action_raison'',
			''beneficiaire_pas_integre_action_raison_autre'',
			''beneficiaire_termine_action'',
			''beneficaire_abandon_action_raison'',
			''beneficaire_abandon_action_raison_autre'',
			''resultat_action'',
			''resultat_action_autre''
		)', 37, current_timestamp, current_timestamp),
	('CSVImport.FRSA.Positionnement.Correspondances', '["Ficheprescription93.id","Ficheprescription93.frsa_id","Ficheprescription93.rdvprestataire_date","Ficheprescription93.motifcontactfp93_id","Ficheprescription93.benef_retour_presente","Ficheprescription93.personne_retenue","Ficheprescription93.motifnonretenuefp93_id","Ficheprescription93.personne_nonretenue_autre","Ficheprescription93.personne_a_integre","Ficheprescription93.motifnonintegrationfp93_id","Ficheprescription93.personne_nonintegre_autre","Ficheprescription93.personne_acheve","Ficheprescription93.motifnonactionachevefp93_id","Ficheprescription93.personne_acheve_autre","Ficheprescription93.motifactionachevefp93_id","Ficheprescription93.personne_nonacheve_autre"]', 'Liste des champs qui correspondent aux entêtes dans le même ordre


		array (
			''Ficheprescription93.id'',
			''Ficheprescription93.frsa_id'',
			''Ficheprescription93.rdvprestataire_date'',
			''Ficheprescription93.motifcontactfp93_id'',
			''Ficheprescription93.benef_retour_presente'',
			''Ficheprescription93.personne_retenue'',
			''Ficheprescription93.motifnonretenuefp93_id'',
			''Ficheprescription93.personne_nonretenue_autre'',
			''Ficheprescription93.personne_a_integre'',
			''Ficheprescription93.motifnonintegrationfp93_id'',
			''Ficheprescription93.personne_nonintegre_autre'',
			''Ficheprescription93.personne_acheve'',
			''Ficheprescription93.motifnonactionachevefp93_id'',
			''Ficheprescription93.personne_acheve_autre'',
			''Ficheprescription93.motifactionachevefp93_id'',
			''Ficheprescription93.personne_nonacheve_autre''
		)', 37, current_timestamp, current_timestamp),
	('CSVImport.FRSA.AutoPositionnement.Headers', '["identifiant","beneficiaire_identifiant","beneficiaire_numero_caf","beneficiaire_telephone_fixe","beneficiaire_telephone_mobile","beneficiaire_email","profil_langues_maternelles","profil_francais_niveau","profil_francais_niveau_validation","profil_langues_niveaux_professionnels","profil_etudes_niveau","profil_diplomes_ou_certifications","profil_experiences_professionnelles","profil_autres_formations_ou_certifications","profil_permis_b","profil_autres_experiences_et_savoirs","action_identifiant","numero_convention","sous_parcours","fiche_action","filiere","porteur_projet","intitule","adresse","code_postal","commune","action_date_debut","duree_en_mois","suivi_contact_telephone","candidatures_date_debut","decouverte_action","motivation","date_engagement"]', 'Import CSV FRSA AutoPositionnement



		array (
			''identifiant'',
			''beneficiaire_identifiant'',
			''beneficiaire_numero_caf'',
			''beneficiaire_telephone_fixe'',
			''beneficiaire_telephone_mobile'',
			''beneficiaire_email'',
			''profil_langues_maternelles'',
			''profil_francais_niveau'',
			''profil_francais_niveau_validation'',
			''profil_langues_niveaux_professionnels'',
			''profil_etudes_niveau'',
			''profil_diplomes_ou_certifications'',
			''profil_experiences_professionnelles'',
			''profil_autres_formations_ou_certifications'',
			''profil_permis_b'',
			''profil_autres_experiences_et_savoirs'',
			''action_identifiant'',
			''numero_convention'',
			''sous_parcours'',
			''fiche_action'',
			''filiere'',
			''porteur_projet'',
			''intitule'',
			''adresse'',
			''code_postal'',
			''commune'',
			''action_date_debut'',
			''duree_en_mois'',
			''suivi_contact_telephone'',
			''candidatures_date_debut'',
			''decouverte_action'',
			''motivation'',
			''date_engagement''
		)', 37, current_timestamp, current_timestamp),
	('CSVImport.FRSA.AutoPositionnement.Correspondances', '["Ficheprescription93.frsa_id","Personne.id","Dossier.matricule","Personne.numfixe","Personne.numport","Personne.email","Personnelangue.maternelles","Personnelangue.francais_niveau","Personnelangue.francais_niveau_validation","Personnelangue.niveaux_professionnels","Personnefrsadiplomexper.nivetu","Personnefrsadiplomexper.diplome","Personnefrsadiplomexper.expprof","Personnefrsadiplomexper.formations","Personnefrsadiplomexper.permisb","Personnefrsadiplomexper.autreexpersavoir","Actionfp93.frsa_id","Actionfp93.numconvention","Thematiquefp93.name","Categoriefp93.name","Filierefp93.name","Prestatairefp93.name","Actionfp93.name","Adresseprestatairefp93.adresse","Adresseprestatairefp93.codepos","Adresseprestatairefp93.localite","Actionfp93.annee","Actionfp93.duree","Adresseprestatairefp93.tel","Thematiquefp93.yearthema","Ficheprescription93.frsa_decouverteaction","Ficheprescription93.frsa_motivation","Ficheprescription93.date_signature"]', 'Liste des champs qui correspondent aux entêtes dans le même ordre


		array (
			''Ficheprescription93.frsa_id'',
			''Personne.id'',
			''Dossier.matricule'',
			''Personne.numfixe'',
			''Personne.numport'',
			''Personne.email'',
			''Personnelangue.maternelles'',
			''Personnelangue.francais_niveau'',
			''Personnelangue.francais_niveau_validation'',
			''Personnelangue.niveaux_professionnels'',
			''Personnefrsadiplomexper.nivetu'',
			''Personnefrsadiplomexper.diplome'',
			''Personnefrsadiplomexper.expprof'',
			''Personnefrsadiplomexper.formations'',
			''Personnefrsadiplomexper.permisb'',
			''Personnefrsadiplomexper.autreexpersavoir'',
			''Actionfp93.frsa_id'',
			''Actionfp93.numconvention'',
			''Thematiquefp93.name'',
			''Categoriefp93.name'',
			''Filierefp93.name'',
			''Prestatairefp93.name'',
			''Actionfp93.name'',
			''Adresseprestatairefp93.adresse'',
			''Adresseprestatairefp93.codepos'',
			''Adresseprestatairefp93.localite'',
			''Actionfp93.annee'',
			''Actionfp93.duree'',
			''Adresseprestatairefp93.tel'',
			''Thematiquefp93.yearthema'',
			''Ficheprescription93.frsa_decouverteaction'',
			''Ficheprescription93.frsa_motivation'',
			''Ficheprescription93.date_signature''
		)', 37, current_timestamp, current_timestamp),
	('CSVImport.FRSA.AutoPositionnement.ModelDetails', '{"Ficheprescription93":{"paths":["Ficheprescription93.posorigine","Ficheprescription93.frsa_id","Ficheprescription93.frsa_decouverteaction","Ficheprescription93.frsa_motivation","Ficheprescription93.date_signature","Ficheprescription93.personne_id","Ficheprescription93.filierefp93_id","Ficheprescription93.actionfp93_id","Ficheprescription93.dd_action","Ficheprescription93.duree_action","Ficheprescription93.adresseprestatairefp93_id"]},"Thematiquefp93":{"paths":["Thematiquefp93.type","Thematiquefp93.name","Thematiquefp93.yearthema"]},"Categoriefp93":{"paths":["Categoriefp93.thematiquefp93_id","Categoriefp93.name"]},"Filierefp93":{"paths":["Filierefp93.categoriefp93_id","Filierefp93.name"]},"Prestatairefp93":{"paths":["Prestatairefp93.name"]},"Adresseprestatairefp93":{"paths":["Adresseprestatairefp93.prestatairefp93_id","Adresseprestatairefp93.adresse","Adresseprestatairefp93.codepos","Adresseprestatairefp93.localite","Adresseprestatairefp93.tel"]},"Actionfp93":{"paths":["Actionfp93.filierefp93_id","Actionfp93.adresseprestatairefp93_id","Actionfp93.numconvention","Actionfp93.name","Actionfp93.duree","Actionfp93.annee","Actionfp93.frsa_id"],"complement":{"Actionfp93.actif":"1"}},"Personne":{"paths":["Personne.id","Personne.numfixe","Personne.numport","Personne.email"]},"Personnelangue":{"paths":["Personnelangue.personne_id","Personnelangue.maternelles","Personnelangue.francais_niveau","Personnelangue.francais_niveau_validation","Personnelangue.niveaux_professionnels"]},"Personnefrsadiplomexper":{"paths":["Personnefrsadiplomexper.personne_id","Personnefrsadiplomexper.nivetu","Personnefrsadiplomexper.diplome","Personnefrsadiplomexper.expprof","Personnefrsadiplomexper.formations","Personnefrsadiplomexper.permisb","Personnefrsadiplomexper.autreexpersavoir"]},"Infocontactpersonne":{"paths":["Infocontactpersonne.personne_id","Infocontactpersonne.fixe","Infocontactpersonne.mobile","Infocontactpersonne.email"]}}', 'Champs à gérer pour chaque Modele


		array (
			''Ficheprescription93'' => array(
				''paths'' => array(
					''Ficheprescription93.posorigine'',
					''Ficheprescription93.frsa_id'',
					''Ficheprescription93.frsa_decouverteaction'',
					''Ficheprescription93.frsa_motivation'',
					''Ficheprescription93.date_signature'',
					''Ficheprescription93.personne_id'',
					''Ficheprescription93.filierefp93_id'',
					''Ficheprescription93.actionfp93_id'',
					''Ficheprescription93.dd_action'',
					''Ficheprescription93.duree_action'',
					''Ficheprescription93.adresseprestatairefp93_id''
				)
			),
			''Thematiquefp93'' => array(
				''paths'' => array(
					''Thematiquefp93.type'',
					''Thematiquefp93.name'',
					''Thematiquefp93.yearthema''
				)
			),
			''Categoriefp93'' => array(
				''paths'' => array(
					''Categoriefp93.thematiquefp93_id'',
					''Categoriefp93.name''
				)
			),
			''Filierefp93'' => array(
				''paths'' => array(
					''Filierefp93.categoriefp93_id'',
					''Filierefp93.name''
				)
			),
			''Prestatairefp93'' => array(
				''paths'' => array(
					''Prestatairefp93.name''
				)
			),
			''Adresseprestatairefp93'' => array(
				''paths'' => array(
					''Adresseprestatairefp93.prestatairefp93_id'',
					''Adresseprestatairefp93.adresse'',
					''Adresseprestatairefp93.codepos'',
					''Adresseprestatairefp93.localite'',
					''Adresseprestatairefp93.tel''
				)
			),
			''Actionfp93'' => array(
				''paths'' => array(
					''Actionfp93.filierefp93_id'',
					''Actionfp93.adresseprestatairefp93_id'',
					''Actionfp93.numconvention'',
					''Actionfp93.name'',
					''Actionfp93.duree'',
					''Actionfp93.annee'',
					''Actionfp93.frsa_id''
				),
				''complement'' => array(
					''Actionfp93.actif'' => ''1''
				)
			),
			''Personne'' => array(
				''paths'' => array(
					''Personne.id'',
					''Personne.numfixe'',
					''Personne.numport'',
					''Personne.email'',
				)
			),
			''Personnelangue'' => array(
				''paths'' => array(
					''Personnelangue.personne_id'',
					''Personnelangue.maternelles'',
					''Personnelangue.francais_niveau'',
					''Personnelangue.francais_niveau_validation'',
					''Personnelangue.niveaux_professionnels''
				)
			),
			''Personnefrsadiplomexper'' => array(
				''paths'' => array(
					''Personnefrsadiplomexper.personne_id'',
					''Personnefrsadiplomexper.nivetu'',
					''Personnefrsadiplomexper.diplome'',
					''Personnefrsadiplomexper.expprof'',
					''Personnefrsadiplomexper.formations'',
					''Personnefrsadiplomexper.permisb'',
					''Personnefrsadiplomexper.autreexpersavoir''
				)
			),
			''Infocontactpersonne'' => array(
				''paths'' => array(
					''Infocontactpersonne.personne_id'',
					''Infocontactpersonne.fixe'',
					''Infocontactpersonne.mobile'',
					''Infocontactpersonne.email'',
				)
			)
		)', 37, current_timestamp, current_timestamp),
	('CSVImport.FRSA.AutoPositionnement.ReferentOption', '"2"', 'Option Spéciaux pour les reférents
	  Option 1: Si on décide d''utiliser l''identifiant du réferent de la personne.
	  Option 2: Si l''on décide d''utiliser l''Identifiant d''une référent Fixe

	Option Choisit
 ''2''', 37, current_timestamp, current_timestamp),
	('CSVImport.FRSA.AutoPositionnement.ReferentID', '"891"', 'Identifiant du référent fixe, Si l''option 2 est choisit
 ''891''', 37, current_timestamp, current_timestamp),
 	('CSVImport.CataloguePDI.Headers', '["Thematique","Annee","Categorie Action","Numero Convention Action","Prestataire","Intitulé d''Action","Filiere","Tel_Action","Adresse Action","CP Action","Commune Action","Duree Action","Annee"]', 'Imports CSV :

	  Headers : Entêtes du fichier CSV à lire
	  Correspondance : Correspondance entre les entes et les Champs de Models
	  processModelsDetails : Champs à gérer pour chaque Modêle





	  Import CSV Catalogue PDI




	  Import CSV FRSA AutoPositionnement



		array (
			''Thematique'',
			''Annee'',
			''Categorie Action'',
			''Numero Convention Action'',
			''Prestataire'',
			''Intitulé d''Action'',
			''Filiere'',
			''Tel_Action'',
			''Adresse Action'',
			''CP Action'',
			''Commune Action'',
			''Duree Action'',
			''Annee''
		)', 38, current_timestamp, current_timestamp),
	('CSVImport.CataloguePDI.Correspondances', '["Thematiquefp93.name","Thematiquefp93.yearthema","Categoriefp93.name","Actionfp93.numconvention","Prestatairefp93.name","Actionfp93.name","Filierefp93.name","Adresseprestatairefp93.tel","Adresseprestatairefp93.adresse","Adresseprestatairefp93.codepos","Adresseprestatairefp93.localite","Actionfp93.duree","Actionfp93.annee"]', 'Liste des champs qui correspondent aux entêtes dans le même ordre


		array (
			''Thematiquefp93.name'',
			''Thematiquefp93.yearthema'',
			''Categoriefp93.name'',
			''Actionfp93.numconvention'',
			''Prestatairefp93.name'',
			''Actionfp93.name'',
			''Filierefp93.name'',
			''Adresseprestatairefp93.tel'',
			''Adresseprestatairefp93.adresse'',
			''Adresseprestatairefp93.codepos'',
			''Adresseprestatairefp93.localite'',
			''Actionfp93.duree'',
			''Actionfp93.annee''
		)', 38, current_timestamp, current_timestamp),
	('CSVImport.CataloguePDI.ModelDetails', '{"Thematiquefp93":{"paths":["Thematiquefp93.type","Thematiquefp93.name","Thematiquefp93.yearthema"]},"Categoriefp93":{"paths":["Categoriefp93.thematiquefp93_id","Categoriefp93.name"]},"Filierefp93":{"paths":["Filierefp93.categoriefp93_id","Filierefp93.name"]},"Prestatairefp93":{"paths":["Prestatairefp93.name"]},"Adresseprestatairefp93":{"paths":["Adresseprestatairefp93.prestatairefp93_id","Adresseprestatairefp93.adresse","Adresseprestatairefp93.codepos","Adresseprestatairefp93.localite","Adresseprestatairefp93.tel"]},"Actionfp93":{"paths":["Actionfp93.filierefp93_id","Actionfp93.adresseprestatairefp93_id","Actionfp93.numconvention","Actionfp93.name","Actionfp93.duree","Actionfp93.annee"],"complement":{"Actionfp93.actif":"1"}}}', 'Champs à gérer pour chaque Modele


		array (
			''Thematiquefp93'' => array(
				''paths'' => array(
					''Thematiquefp93.type'',
					''Thematiquefp93.name'',
					''Thematiquefp93.yearthema''
				)
			),
			''Categoriefp93'' => array(
				''paths'' => array(
					''Categoriefp93.thematiquefp93_id'',
					''Categoriefp93.name''
				)
			),
			''Filierefp93'' => array(
				''paths'' => array(
					''Filierefp93.categoriefp93_id'',
					''Filierefp93.name''
				)
			),
			''Prestatairefp93'' => array(
				''paths'' => array(
					''Prestatairefp93.name''
				)
			),
			''Adresseprestatairefp93'' => array(
				''paths'' => array(
					''Adresseprestatairefp93.prestatairefp93_id'',
					''Adresseprestatairefp93.adresse'',
					''Adresseprestatairefp93.codepos'',
					''Adresseprestatairefp93.localite'',
					''Adresseprestatairefp93.tel''
				)
			),
			''Actionfp93'' => array(
				''paths'' => array(
					''Actionfp93.filierefp93_id'',
					''Actionfp93.adresseprestatairefp93_id'',
					''Actionfp93.numconvention'',
					''Actionfp93.name'',
					''Actionfp93.duree'',
					''Actionfp93.annee''
				),
				''complement'' => array(
					''Actionfp93.actif'' => ''1''
				)
			),
		)', 38, current_timestamp, current_timestamp),
		('ConfigurableQuery.Fichesprescriptions93.search', '{"filters":{"defaults":{"Calculdroitrsa":{"toppersdrodevorsa":"1"},"Dossier":{"dernier":"1"},"Ficheprescription93":{"exists":"1"},"Pagination":{"nombre_total":"0"},"Situationdossierrsa":{"etatdosrsa_choice":"0","etatdosrsa":["0","2","3","4"]}},"accepted":{"Situationdossierrsa.etatdosrsa":[0,1,2,3,4,5,6]},"skip":[]},"query":{"restrict":[],"conditions":{"Situationdossierrsa.etatdosrsa <>":"Z"},"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.matricule","1":"Personne.nom_complet","2":"Adresse.nomcom","3":"Ficheprescription93.statut","4":"Actionfp93.name","5":"Prestatairefp93.name","Dossier.locked":{"type":"boolean","class":"dossier_locked"},"Referent.horszone":{"hidden":true},"Ficheprescription93.id":{"hidden":true},"\/Fichesprescriptions93\/edit\/#Ficheprescription93.id#":{"disabled":"( ''#Referent.horszone#'' == true || ''#Ficheprescription93.id#'' == '''' || ''#\/Fichesprescriptions93\/edit#'' == false )","class":"external"},"\/Fichesprescriptions93\/index\/#Personne.id#":{"title":"Voir les fiches de proposition de #Personne.nom_complet#","disabled":"( ''#Referent.horszone#'' == true )","class":"view external"}},"innerTable":{"0":"Calculdroitrsa.toppersdrodevorsa","Personne.age":{"label":"Age"},"1":"Ficheprescription93.benef_retour_presente","2":"Ficheprescription93.personne_a_integre","3":"Ficheprescription93.personne_acheve","4":"Personne.dtnai","5":"Dossier.numdemrsa","6":"Personne.nir","7":"Adresse.codepos","8":"Adresse.numcom","9":"Prestation.rolepers","10":"Structurereferenteparcours.lib_struc","11":"Referentparcours.nom_complet"}},"ini_set":[]}', 'Menu "Recherches" > "Par fiches de prescription"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Calculdroitrsa'' => array(
						''toppersdrodevorsa'' => ''1''
					),
					''Dossier'' => array(
						''dernier'' => ''1'',
					),
					''Ficheprescription93'' => array(
						''exists'' => ''1''
					),
					''Pagination'' => array(
						''nombre_total'' => ''0''
					),
					''Situationdossierrsa'' => array(
						''etatdosrsa_choice'' => ''0'',
						''etatdosrsa'' => array( ''0'',''2'', ''3'', ''4'' )
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(
					''Situationdossierrsa.etatdosrsa'' => array( 0, 1, 2, 3, 4, 5, 6 )
				),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(
					''Situationdossierrsa.etatdosrsa <>'' => ''Z''
				),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.matricule'',
					''Personne.nom_complet'',
					''Adresse.nomcom'',
					''Ficheprescription93.statut'',
					''Actionfp93.name'',
					''Prestatairefp93.name'',
					''Dossier.locked'' => array(
						''type'' => ''boolean'',
						''class'' => ''dossier_locked''
					),
					 Début: données nécessaires pour les permissions sur les liens, sans affichage
					''Referent.horszone'' => array( ''hidden'' => true ),
					''Ficheprescription93.id'' => array( ''hidden'' => true ),
					 Fin: données nécessaires pour les permissions sur les liens, sans affichage
					''Fichesprescriptions93edit#Ficheprescription93.id#'' => array(
						''disabled'' => "( ''#Referent.horszone#'' == true || ''#Ficheprescription93.id#'' == '''' || ''#Fichesprescriptions93edit#'' == false )",
						''class'' => ''external''
					),
					''Fichesprescriptions93index#Personne.id#'' => array(
						''title'' => ''Voir les fiches de proposition de #Personne.nom_complet#'',
						''disabled'' => "( ''#Referent.horszone#'' == true )",
						''class'' => ''view external''
					),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Calculdroitrsa.toppersdrodevorsa'',
					''Personne.age'' => array( ''label'' => ''Age'' ),
					''Ficheprescription93.benef_retour_presente'',
					''Ficheprescription93.personne_a_integre'',
					''Ficheprescription93.personne_acheve'',
					''Personne.dtnai'',
					''Dossier.numdemrsa'',
					''Personne.nir'',
					''Adresse.codepos'',
					''Adresse.numcom'',
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 39, current_timestamp, current_timestamp),
	('ConfigurableQuery.Fichesprescriptions93.exportcsv', '{"filters":{"defaults":{"Calculdroitrsa":{"toppersdrodevorsa":"1"},"Dossier":{"dernier":"1"},"Ficheprescription93":{"exists":"1"},"Pagination":{"nombre_total":"0"},"Situationdossierrsa":{"etatdosrsa_choice":"0","etatdosrsa":["0","2","3","4"]}},"accepted":{"Situationdossierrsa.etatdosrsa":[0,1,2,3,4,5,6]},"skip":[]},"query":{"restrict":[],"conditions":{"Situationdossierrsa.etatdosrsa <>":"Z"},"order":[]},"results":{"fields":{"0":"Dossier.numdemrsa","1":"Dossier.dtdemrsa","2":"Dossier.matricule","3":"Personne.qual","4":"Personne.nom","5":"Personne.prenom","6":"Prestation.rolepers","7":"Ficheprescription93.statut","Referent.nom_complet":{"label":"Referent etablissant la FP"},"Adresse.numvoie":{"domain":"adresse"},"Adresse.libtypevoie":{"domain":"adresse"},"Adresse.nomvoie":{"domain":"adresse"},"Adresse.complideadr":{"domain":"adresse"},"Adresse.compladr":{"domain":"adresse"},"Adresse.lieudist":{"domain":"adresse"},"Adresse.numcom":{"domain":"adresse"},"Adresse.codepos":{"domain":"adresse"},"Adresse.nomcom":{"domain":"adresse"},"8":"Ficheprescription93.rdvprestataire_date","Actionfp93.numconvention":{"domain":"cataloguespdisfps93"},"Thematiquefp93.type":{"domain":"cataloguespdisfps93"},"Thematiquefp93.name":{"domain":"cataloguespdisfps93"},"Thematiquefp93.yearthema":{"domain":"cataloguespdisfps93"},"Categoriefp93.name":{"domain":"cataloguespdisfps93"},"Filierefp93.name":{"domain":"cataloguespdisfps93"},"Prestatairefp93.name":{"domain":"cataloguespdisfps93"},"Actionfp93.name":{"domain":"cataloguespdisfps93"},"9":"Ficheprescription93.benef_retour_presente","10":"Ficheprescription93.dd_action","11":"Ficheprescription93.df_action","12":"Ficheprescription93.date_signature","13":"Ficheprescription93.date_transmission","14":"Ficheprescription93.date_retour","15":"Ficheprescription93.motifcontactfp93_id","16":"Ficheprescription93.personne_retenue","17":"Ficheprescription93.motifnonretenuefp93_id","18":"Ficheprescription93.personne_nonretenue_autre","19":"Ficheprescription93.personne_a_integre","20":"Ficheprescription93.personne_date_integration","21":"Ficheprescription93.motifnonintegrationfp93_id","22":"Ficheprescription93.personne_nonintegre_autre","23":"Ficheprescription93.personne_acheve","24":"Ficheprescription93.motifactionachevefp93_id","25":"Ficheprescription93.motifnonactionachevefp93_id","26":"Ficheprescription93.personne_acheve_autre","27":"Ficheprescription93.date_bilan_mi_parcours","28":"Ficheprescription93.date_bilan_final"}},"ini_set":[]}', 'Export CSV,  menu "Recherches" > "Par fiches de prescription"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Fichesprescriptions93.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Fichesprescriptions93.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Dossier.matricule'',
					''Personne.qual'',
					''Personne.nom'',
					''Personne.prenom'',
					''Prestation.rolepers'',
					''Ficheprescription93.statut'',
					''Referent.nom_complet'' => array( ''label'' => ''Referent etablissant la FP'' ),
					''Adresse.numvoie'' => array( ''domain'' => ''adresse'' ),
					''Adresse.libtypevoie'' => array( ''domain'' => ''adresse'' ),
					''Adresse.nomvoie'' => array( ''domain'' => ''adresse'' ),
					''Adresse.complideadr'' => array( ''domain'' => ''adresse'' ),
					''Adresse.compladr'' => array( ''domain'' => ''adresse'' ),
					''Adresse.lieudist'' => array( ''domain'' => ''adresse'' ),
					''Adresse.numcom'' => array( ''domain'' => ''adresse'' ),
					''Adresse.numcom'' => array( ''domain'' => ''adresse'' ),
					''Adresse.codepos'' => array( ''domain'' => ''adresse'' ),
					''Adresse.nomcom'' => array( ''domain'' => ''adresse'' ),
					''Ficheprescription93.rdvprestataire_date'',
					''Actionfp93.numconvention'' => array( ''domain'' => ''cataloguespdisfps93'' ),
					''Thematiquefp93.type'' => array( ''domain'' => ''cataloguespdisfps93'' ),
					''Thematiquefp93.name'' => array( ''domain'' => ''cataloguespdisfps93'' ),
					''Thematiquefp93.yearthema'' => array( ''domain'' => ''cataloguespdisfps93'' ),
					''Categoriefp93.name'' => array( ''domain'' => ''cataloguespdisfps93'' ),
					''Filierefp93.name'' => array( ''domain'' => ''cataloguespdisfps93'' ),
					''Prestatairefp93.name'' => array( ''domain'' => ''cataloguespdisfps93'' ),
					''Actionfp93.name'' => array( ''domain'' => ''cataloguespdisfps93'' ),
					''Ficheprescription93.benef_retour_presente'',
					''Ficheprescription93.dd_action'',
					''Ficheprescription93.df_action'',
					''Ficheprescription93.date_signature'',
					''Ficheprescription93.date_transmission'',
					''Ficheprescription93.date_retour'',
					''Ficheprescription93.motifcontactfp93_id'',
					''Ficheprescription93.personne_retenue'',
					''Ficheprescription93.motifnonretenuefp93_id'',
					''Ficheprescription93.personne_nonretenue_autre'',
					''Ficheprescription93.personne_a_integre'',
					''Ficheprescription93.personne_date_integration'',
					''Ficheprescription93.motifnonintegrationfp93_id'',
					''Ficheprescription93.personne_nonintegre_autre'',
					''Ficheprescription93.personne_acheve'',
					''Ficheprescription93.motifactionachevefp93_id'',
					''Ficheprescription93.motifnonactionachevefp93_id'',
					''Ficheprescription93.personne_acheve_autre'',
					''Ficheprescription93.date_bilan_mi_parcours'',
					''Ficheprescription93.date_bilan_final'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Fichesprescriptions93.search.ini_set'' ),
		)', 39, current_timestamp, current_timestamp),
	('Cataloguepdifp93.urls', '{"Consultation du catalogue des actions (PDIE)":"https:\/\/formation-rsa.seinesaintdenis.fr","Consultation du site Defi Metiers":"http:\/\/www.carif-idf.org\/","Consultation INSER''ECO93":"http:\/\/www.insereco93.com\/"}', '--------------------------------------------------------------------------


	  Liste des intitulés et des URL à faire apparaître dans le cadre
	  "PrescripteurRéférent" de la fiche de prescription du CG 93.

	  @var array


		array(
			''Consultation du catalogue des actions (PDIE)'' => ''https:formation-rsa.seinesaintdenis.fr'',
			''Consultation du site Defi Metiers'' => ''http:www.carif-idf.org'',
			''Consultation INSER''ECO93'' => ''http:www.insereco93.com'',
		)', 39, current_timestamp, current_timestamp),
	('Evidence.Fichesprescriptions93.add', '{"fields":["#Ficheprescription93StructurereferenteId","#Ficheprescription93Typethematiquefp93Id","#Ficheprescription93Thematiquefp93Id","#Ficheprescription93Yearthematiquefp93Id","#Ficheprescription93Categoriefp93Id","#Ficheprescription93DdActionDay","#Ficheprescription93DateSignatureDay"]}', 'Mise en évidence de certains champs du formulaire des fiches de
	  prescription, pour remplir les tableaux de bord B5


		array(
			''fields'' => array(
				 Structure du référent
				''#Ficheprescription93StructurereferenteId'',
				 Type
				''#Ficheprescription93Typethematiquefp93Id'',
				 Thématique
				''#Ficheprescription93Thematiquefp93Id'',
				 Année
				''#Ficheprescription93Yearthematiquefp93Id'',
				 Catégorie
				''#Ficheprescription93Categoriefp93Id'',
				 Date de début de l''action
				''#Ficheprescription93DdActionDay'',
				 Signé le
				''#Ficheprescription93DateSignatureDay''
			)
		)', 39, current_timestamp, current_timestamp),
	('Evidence.Fichesprescriptions93.edit', '{"fields":["#Ficheprescription93StructurereferenteId","#Ficheprescription93Typethematiquefp93Id","#Ficheprescription93Thematiquefp93Id","#Ficheprescription93Yearthematiquefp93Id","#Ficheprescription93Categoriefp93Id","#Ficheprescription93DdActionDay","#Ficheprescription93DateSignatureDay"]}', 'Configure::read( ''Evidence.Fichesprescriptions93.add'' )', 39, current_timestamp, current_timestamp),
	('Filtresdefaut.Cohortesrendezvous_cohorte', '{"Search":{"Dossier":{"dernier":"1"},"Rendezvous":{"statutrdv_id":2,"daterdv":"1","daterdv_from":{"year":"2020","month":"04","day":"01"},"daterdv_to":{"year":"2020","month":"04","day":"29"}},"Pagination":{"nombre_total":"0"}}}', 'Valeurs par défaut du filtre de recherche de la cohorte de RDV.


		array(
			''Search'' => array(
				''Dossier'' => array(
					''dernier'' => ''1''
				),
				''Rendezvous'' => array(
					''statutrdv_id'' => 2,  Statut "prévu"
					''daterdv'' => ''1'',
					''daterdv_from'' => date_sql_to_cakephp( date( ''Y-m-d'', strtotime( ''first day of this month'' ) ) ),
					''daterdv_to'' => date_sql_to_cakephp( date( ''Y-m-d'', strtotime( ''now'' ) ) ),
				),
				''Pagination'' => array(
					''nombre_total'' => ''0''
				)
			)
		)', 40, current_timestamp, current_timestamp),
	('Cohortesrendezvous', '{"cohorte":{"fields":["Personne.nom_complet","Adresse.nomcom","Structurereferente.lib_struc","Referent.nom_complet","Typerdv.libelle","Rendezvous.daterdv","Rendezvous.heurerdv","Statutrdv.libelle"]},"exportcsv":{"0":"Personne.nom_complet","1":"Adresse.nomcom","2":"Structurereferente.lib_struc","3":"Referent.nom_complet","4":"Typerdv.libelle","5":"Rendezvous.daterdv","6":"Rendezvous.heurerdv","7":"Statutrdv.libelle","Personne.numfixe":{"label":"Num de telephone fixe"},"Personne.numport":{"label":"Num de telephone portable"},"Personne.email":{"label":"Adresse mail"}}}', 'Liste des champs devant apparaître dans la cohorte de rendez-vous.
	 	- Cohortesrendezvous.cohorte.fields contient les champs de chaque ligne du tableau de résultats
	 	- Cohortesrendezvous.cohorte.innerTable contient les champs de l''infobulle de chaque ligne du tableau de résultats
	 	- Cohortesrendezvous.exportcsv contient les champs de chaque ligne du tableau à télécharger au format CSV

	  Voir l''onglet "Environnement logiciel" > "WebRSA" > "Champs spécifiés dans
	  le webrsa.inc" de la vérification de l''application.


		array(
			''cohorte'' => array(
				''fields'' => array(
					''Personne.nom_complet'',
					''Adresse.nomcom'',
					''Structurereferente.lib_struc'',
					''Referent.nom_complet'',
					''Typerdv.libelle'',
					''Rendezvous.daterdv'',
					''Rendezvous.heurerdv'',
					''Statutrdv.libelle''
				),
				''innerTable'' => array(
					''Personne.dtnai'',
					''Adresse.numcom'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Donnees.nivetu'',
					''Donnees.hispro'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Deractromev3.familleromev3'',
					''Deractromev3.appellationromev3'',
					''Actrechromev3.familleromev3'',
					''Actrechromev3.appellationromev3''
				),
				''header'' => array(
					array( ''Dossier'' => array( ''colspan'' => 3 ) ),
					array( ''Accompagnement et difficultés'' => array( ''colspan'' => 3 ) ),
					array( ''Code ROME'' => array( ''colspan'' => 4 ) ),
					array( ''Hors code ROME'' => array( ''colspan'' => 4 ) ),
					array( '' '' => array( ''class'' => ''action noprint'' ) ),
					array( '' '' => array( ''style'' => ''display: none'' ) ),
				)
			),
			''exportcsv'' => array(
				''Personne.nom_complet'',
				''Adresse.nomcom'',
				''Structurereferente.lib_struc'',
				''Referent.nom_complet'',
				''Typerdv.libelle'',
				''Rendezvous.daterdv'',
				''Rendezvous.heurerdv'',
				''Statutrdv.libelle'',
				''Personne.numfixe'' => array( ''label'' => ''Num de telephone fixe'' ),
				''Personne.numport'' => array( ''label'' => ''Num de telephone portable'' ),
				''Personne.email'' => array( ''label'' => ''Adresse mail'' )
			)
		)', 40, current_timestamp, current_timestamp),
		('Filtresdefaut.Cohortescers93_validationcs', '{"Search":{"Contratinsertion":{"dernier":true},"Dossier":{"dernier":true},"Cer93":{"positioncer_choice":true,"positioncer":["04premierelecture"]}}}', 'Valeurs par défaut du filtre de recherche de la validationcs des CERs.


	  	array(
	  		''Search'' => array(
				''Contratinsertion'' => array(
					''dernier'' => true
				),
				''Dossier'' => array(
					''dernier'' => true
				),
				''Cer93'' => array (
					''positioncer_choice'' => true,
					''positioncer'' => array(''04premierelecture''),
				)
			)
		)', 41, current_timestamp, current_timestamp),
	('ConfigurableQuery.Sanctionseps58.cohorte_radiespe', '{"filters":{"defaults":[],"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Situationdossierrsa.etatdosrsa"]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":["Dossier.matricule","Personne.nom","Personne.prenom","Personne.dtnai","Adresse.nomcom","Historiqueetatpe.etat","Historiqueetatpe.code","Historiqueetatpe.motif","Historiqueetatpe.date","Structureorientante.lib_struc","Typeorient.lib_type_orient","Structurereferente.lib_struc"],"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu "Recherches" > "Par dossiers EP" > "Radiation de Pôle Emploi"

	  @see les Configure::read() pour les conditions dans appModelAbstractclassAbstractWebrsaCohorteSanctionep58.php
	  qui pourraient éventuellement se trouver ici ?


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				 INFO: pas besoin du restrict ci-dessous, des conditions plus générales se trouvent déjà dans: ''Dossierseps.conditionsSelection''
				''skip'' => array(
					''Calculdroitrsa.toppersdrodevorsa'',
					''Situationdossierrsa.etatdosrsa''
				)
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.matricule'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Adresse.nomcom'',
					''Historiqueetatpe.etat'',
					''Historiqueetatpe.code'',
					''Historiqueetatpe.motif'',
					''Historiqueetatpe.date'',
					''Structureorientante.lib_struc'',
					''Typeorient.lib_type_orient'',
					''Structurereferente.lib_struc'',
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 42, current_timestamp, current_timestamp),
	('ConfigurableQuery.Sanctionseps58.exportcsv_radiespe', '{"filters":{"defaults":[],"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Situationdossierrsa.etatdosrsa"]},"query":{"restrict":[],"conditions":[],"order":[]},"results":{"fields":["Personne.nom","Personne.prenom","Personne.dtnai","Adresse.nomcom","Historiqueetatpe.etat","Historiqueetatpe.code","Historiqueetatpe.motif","Historiqueetatpe.date","Serviceinstructeur.lib_service","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Export CSV,  menu "Recherches" > "Par dossiers EP" > "Radiation de Pôle Emploi"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Sanctionseps58.cohorte_radiespe.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Sanctionseps58.cohorte_radiespe.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Adresse.nomcom'',
					''Historiqueetatpe.etat'',
					''Historiqueetatpe.code'',
					''Historiqueetatpe.motif'',
					''Historiqueetatpe.date'',
					''Serviceinstructeur.lib_service'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Sanctionseps58.cohorte_radiespe.ini_set'' ),
		)', 42, current_timestamp, current_timestamp),
	('ConfigurableQuery.Sanctionseps58.cohorte_noninscritspe', '{"filters":{"defaults":[],"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Situationdossierrsa.etatdosrsa"]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":["Dossier.matricule","Personne.nom","Personne.prenom","Personne.dtnai","Adresse.nomcom","Typeorient.lib_type_orient","Structurereferente.lib_struc","Structureorientante.lib_struc","Orientstruct.date_valid"],"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu "Recherches" > "Par dossiers EP" > "Non inscription à Pôle Emploi"

	  @see les Configure::read() pour les conditions dans appModelAbstractclassAbstractWebrsaCohorteSanctionep58.php
	  qui pourraient éventuellement se trouver ici ?


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				 INFO: pas besoin du restrict ci-dessous, des conditions plus générales se trouvent déjà dans: ''Dossierseps.conditionsSelection''
				''skip'' => array(
					''Calculdroitrsa.toppersdrodevorsa'',
					''Situationdossierrsa.etatdosrsa''
				)
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Dossier.matricule'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Adresse.nomcom'',
					''Typeorient.lib_type_orient'',
					''Structurereferente.lib_struc'',
					''Structureorientante.lib_struc'',
					''Orientstruct.date_valid'',
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 42, current_timestamp, current_timestamp),
	('ConfigurableQuery.Sanctionseps58.exportcsv_noninscritspe', '{"filters":{"defaults":[],"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Situationdossierrsa.etatdosrsa"]},"query":{"restrict":[],"conditions":[],"order":[]},"results":{"fields":["Personne.nom","Personne.prenom","Personne.dtnai","Adresse.nomcom","Typeorient.lib_type_orient","Structurereferente.lib_struc","Orientstruct.date_valid","Serviceinstructeur.lib_service","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Export CSV,  menu "Recherches" > "Par dossiers EP" > "Radiation de Pôle Emploi"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Sanctionseps58.cohorte_noninscritspe.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Sanctionseps58.cohorte_noninscritspe.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Adresse.nomcom'',
					''Typeorient.lib_type_orient'',
					''Structurereferente.lib_struc'',
					''Orientstruct.date_valid'',
					''Serviceinstructeur.lib_service'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Sanctionseps58.cohorte_noninscritspe.ini_set'' ),
		)', 42, current_timestamp, current_timestamp),
	('ConfigurableQuery.Nonorientationsproscovs58.cohorte', '{"filters":{"defaults":{"Contratinsertion":{"df_ci_from":"TAB::-1WEEK","df_ci_to":"TAB::NOW"},"Pagination":{"nombre_total":0}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dernier","Situationdossierrsa.etatdosrsa"]},"query":{"restrict":{"Calculdroitrsa.toppersdrodevorsa":"1","Dossier.dernier":"1","Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["Z",2,3,4]},"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet","2":"Personne.dtnai","3":"Adresse.codepos","Foyer.enerreur":{"sort":false},"4":"Orientstruct.date_valid","5":"Contratinsertion.nbjours","6":"Typeorient.lib_type_orient","7":"Structurereferente.lib_struc","8":"Referent.nom_complet","9":"\/Orientsstructs\/index\/#Personne.id#"},"innerTable":["Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Menu "Recherches" > "Par dossiers COV" > "Demandes de maintien dans le social"


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Contratinsertion'' => array(
						''df_ci_from'' => ''TAB::-1WEEK'',
						''df_ci_to'' => ''TAB::NOW''
					),
					''Pagination'' => array(
						''nombre_total'' => 0
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(
					''Calculdroitrsa.toppersdrodevorsa'',
					''Dossier.dernier'',
					''Situationdossierrsa.etatdosrsa''
				)
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(
					''Calculdroitrsa.toppersdrodevorsa'' => ''1'',
					''Dossier.dernier'' => ''1'',
					''Situationdossierrsa.etatdosrsa_choice'' => ''1'',
					''Situationdossierrsa.etatdosrsa'' => array( ''Z'', 2, 3, 4 )
				),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				 TODO: ORDER BY ( DATE_PART( ''day'', NOW() - "Contratinsertion"."df_ci" ) ) DESC
				''order'' => array()
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Personne.nom_complet'',
					''Personne.dtnai'',
					''Adresse.codepos'',
					''Foyer.enerreur'' => array( ''sort'' => false ),
					''Orientstruct.date_valid'',
					''Contratinsertion.nbjours'',
					''Typeorient.lib_type_orient'',
					''Structurereferente.lib_struc'',
					''Referent.nom_complet'',
					''Orientsstructsindex#Personne.id#''
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', 43, current_timestamp, current_timestamp),
	('ConfigurableQuery.Nonorientationsproscovs58.exportcsv', '{"filters":{"defaults":{"Contratinsertion":{"df_ci_from":"TAB::-1WEEK","df_ci_to":"TAB::NOW"},"Pagination":{"nombre_total":0}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dernier","Situationdossierrsa.etatdosrsa"]},"query":{"restrict":{"Calculdroitrsa.toppersdrodevorsa":"1","Dossier.dernier":"1","Situationdossierrsa.etatdosrsa_choice":"1","Situationdossierrsa.etatdosrsa":["Z",2,3,4]},"conditions":[],"order":[]},"results":{"fields":["Dossier.numdemrsa","Personne.nom","Personne.prenom","Personne.dtnai","Adresse.nomcom","Orientstruct.date_valid","Contratinsertion.nbjours","Typeorient.lib_type_orient","Structurereferente.lib_struc","Referent.nom_complet","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":[]}', 'Export CSV,  menu "Recherches" > "Par dossiers COV" > "Demandes de maintien dans le social"


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Nonorientationsproscovs58.cohorte.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Nonorientationsproscovs58.cohorte.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Adresse.nomcom'',
					''Orientstruct.date_valid'',
					''Contratinsertion.nbjours'',
					''Typeorient.lib_type_orient'',
					''Structurereferente.lib_struc'',
					''Referent.nom_complet'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet''
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Nonorientationsproscovs58.cohorte.ini_set'' ),
		)', 43, current_timestamp, current_timestamp),
		('ConfigurableQuery.Historiquescovs58.view', '{"common":["Cov58.datecommission","Sitecov58.name","Dossiercov58.themecov58","Passagecov58.etatdossiercov","Dossiercov58.created"],"proposorientationscovs58":["Typeorient.lib_type_orient","Structurereferente.lib_struc","Referent.nom_complet","Propoorientationcov58.commentaire"],"decisionsproposorientationscovs58":["Decisionpropoorientationcov58.decisioncov","NvTypeorient.lib_type_orient","NvStructurereferente.lib_struc","NvReferent.nom_complet","Decisionpropoorientationcov58.commentaire"],"proposcontratsinsertioncovs58":["Propocontratinsertioncov58.num_contrat","Propocontratinsertioncov58.dd_ci","Propocontratinsertioncov58.duree","Propocontratinsertioncov58.df_ci","VxStructurereferente.lib_struc","VxReferent.nom_complet","Propocontratinsertioncov58.commentaire"],"decisionsproposcontratsinsertioncovs58":["Decisionpropocontratinsertioncov58.decisioncov","Decisionpropocontratinsertioncov58.datevalidation","Decisionpropocontratinsertioncov58.dd_ci","Decisionpropocontratinsertioncov58.duree_engag","Decisionpropocontratinsertioncov58.df_ci","Decisionpropocontratinsertioncov58.commentaire"],"proposnonorientationsproscovs58":{"VxOrientstruct.date_valid":{"type":"date"},"0":"VxTypeorient.lib_type_orient","1":"VxStructurereferente.lib_struc","2":"VxReferent.nom_complet","3":"Propononorientationprocov58.commentaire"},"decisionsproposnonorientationsproscovs58":["Decisionpropononorientationprocov58.decisioncov","NvTypeorient.lib_type_orient","NvStructurereferente.lib_struc","NvReferent.nom_complet","Decisionpropononorientationprocov58.commentaire"],"proposorientssocialescovs58":["Propoorientsocialecov58.commentaire","Rendezvous.daterdv","Rendezvous.heurerdv","Structurereferenterdv.lib_struc","Referentrdv.nom_complet","Typerdv.libelle","Statutrdv.libelle"],"decisionsproposorientssocialescovs58":["Decisionpropoorientsocialecov58.decisioncov","NvTypeorient.lib_type_orient","NvStructurereferente.lib_struc","NvReferent.nom_complet","Decisionpropoorientsocialecov58.commentaire"],"nonorientationsproscovs58":{"VxOrientstruct.date_valid":{"type":"date"},"0":"VxTypeorient.lib_type_orient","1":"VxStructurereferente.lib_struc","2":"VxReferent.nom_complet","3":"Nonorientationprocov58.commentaire"},"decisionsnonorientationsproscovs58":["Decisionnonorientationprocov58.decisioncov","NvTypeorient.lib_type_orient","NvStructurereferente.lib_struc","NvReferent.nom_complet","Decisionnonorientationprocov58.commentaire"],"regressionsorientationscovs58":{"VxOrientstruct.date_valid":{"type":"date"},"0":"VxTypeorient.lib_type_orient","1":"VxStructurereferente.lib_struc","2":"VxReferent.nom_complet","3":"Regressionorientationcov58.commentaire"},"decisionsregressionsorientationscovs58":["Decisionregressionorientationcov58.decisioncov","NvTypeorient.lib_type_orient","NvStructurereferente.lib_struc","NvReferent.nom_complet","Decisionregressionorientationcov58.commentaire"]}', 'array(
			''common'' => array(
				''Cov58.datecommission'',
				''Sitecov58.name'',
				''Dossiercov58.themecov58'',
				''Passagecov58.etatdossiercov'',
				''Dossiercov58.created''
			),
			 ---------------------------------------------------------------------
			''proposorientationscovs58'' => array(
				''Typeorient.lib_type_orient'',
				''Structurereferente.lib_struc'',
				''Referent.nom_complet'',
				''Propoorientationcov58.commentaire''
			),
			''decisionsproposorientationscovs58'' => array(
				''Decisionpropoorientationcov58.decisioncov'',
				''NvTypeorient.lib_type_orient'',
				''NvStructurereferente.lib_struc'',
				''NvReferent.nom_complet'',
				''Decisionpropoorientationcov58.commentaire''
			),
			 ---------------------------------------------------------------------
			''proposcontratsinsertioncovs58'' => array(
				''Propocontratinsertioncov58.num_contrat'',
				''Propocontratinsertioncov58.dd_ci'',
				''Propocontratinsertioncov58.duree'',
				''Propocontratinsertioncov58.df_ci'',
				''VxStructurereferente.lib_struc'',
				''VxReferent.nom_complet'',
				''Propocontratinsertioncov58.commentaire''
			),
			''decisionsproposcontratsinsertioncovs58'' => array(
				''Decisionpropocontratinsertioncov58.decisioncov'',
				''Decisionpropocontratinsertioncov58.datevalidation'',
				''Decisionpropocontratinsertioncov58.dd_ci'',
				''Decisionpropocontratinsertioncov58.duree_engag'',
				''Decisionpropocontratinsertioncov58.df_ci'',
				''Decisionpropocontratinsertioncov58.commentaire''
			),
			 ---------------------------------------------------------------------
			''proposnonorientationsproscovs58'' => array(
				''VxOrientstruct.date_valid'' => array( ''type'' => ''date'' ),
				''VxTypeorient.lib_type_orient'',
				''VxStructurereferente.lib_struc'',
				''VxReferent.nom_complet'',
				''Propononorientationprocov58.commentaire''
			),
			''decisionsproposnonorientationsproscovs58'' => array(
				''Decisionpropononorientationprocov58.decisioncov'',
				''NvTypeorient.lib_type_orient'',
				''NvStructurereferente.lib_struc'',
				''NvReferent.nom_complet'',
				''Decisionpropononorientationprocov58.commentaire''
			),
			 ---------------------------------------------------------------------
			''proposorientssocialescovs58'' => array(
				''Propoorientsocialecov58.commentaire'',
				''Rendezvous.daterdv'',
				''Rendezvous.heurerdv'',
				''Structurereferenterdv.lib_struc'',
				''Referentrdv.nom_complet'',
				''Typerdv.libelle'',
				''Statutrdv.libelle''
			),
			''decisionsproposorientssocialescovs58'' => array(
				''Decisionpropoorientsocialecov58.decisioncov'',
				''NvTypeorient.lib_type_orient'',
				''NvStructurereferente.lib_struc'',
				''NvReferent.nom_complet'',
				''Decisionpropoorientsocialecov58.commentaire''
			),
			 ---------------------------------------------------------------------
			''nonorientationsproscovs58'' => array(
				''VxOrientstruct.date_valid'' => array( ''type'' => ''date'' ),
				''VxTypeorient.lib_type_orient'',
				''VxStructurereferente.lib_struc'',
				''VxReferent.nom_complet'',
				''Nonorientationprocov58.commentaire''
			),
			''decisionsnonorientationsproscovs58'' => array(
				''Decisionnonorientationprocov58.decisioncov'',
				''NvTypeorient.lib_type_orient'',
				''NvStructurereferente.lib_struc'',
				''NvReferent.nom_complet'',
				''Decisionnonorientationprocov58.commentaire''
			),
			 ---------------------------------------------------------------------
			''regressionsorientationscovs58'' => array(
				''VxOrientstruct.date_valid'' => array( ''type'' => ''date'' ),
				''VxTypeorient.lib_type_orient'',
				''VxStructurereferente.lib_struc'',
				''VxReferent.nom_complet'',
				''Regressionorientationcov58.commentaire''
			),
			''decisionsregressionsorientationscovs58'' => array(
				''Decisionregressionorientationcov58.decisioncov'',
				''NvTypeorient.lib_type_orient'',
				''NvStructurereferente.lib_struc'',
				''NvReferent.nom_complet'',
				''Decisionregressionorientationcov58.commentaire''
			)
		)', 44, current_timestamp, current_timestamp),
		('ConfigurableQuery.Covs58.visualisationdecisions', '{"proposorientationscovs58":{"0":"Personne.nir","1":"Personne.nom_complet","2":"Adresse.complete","3":"Personne.dtnai","Dossiercov58.created":{"type":"date"},"4":"Decisionpropoorientationcov58.decisioncov","5":"NvTypeorient.lib_type_orient","6":"NvStructurereferente.lib_struc","7":"NvReferent.nom_complet","\/Covs58\/impressiondecision\/#Passagecov58.id#":{"title":false,"class":"print"},"\/Orientsstructs\/index\/#Personne.id#":{"title":false,"class":"view"},"\/Historiquescovs58\/view\/#Passagecov58.id#":{"title":false}},"proposcontratsinsertioncovs58":{"0":"Personne.nir","1":"Personne.nom_complet","2":"Adresse.complete","3":"Personne.dtnai","Dossiercov58.created":{"type":"date"},"VxReferent.nom_complet":{"label":"Nom du prescripteur"},"4":"Decisionpropocontratinsertioncov58.decisioncov","5":"Decisionpropocontratinsertioncov58.dd_ci","6":"Decisionpropocontratinsertioncov58.duree_engag","7":"Decisionpropocontratinsertioncov58.df_ci","\/Covs58\/impressiondecision\/#Passagecov58.id#":{"title":false,"class":"print"},"\/Contratsinsertion\/index\/#Personne.id#":{"title":false,"class":"view"},"\/Historiquescovs58\/view\/#Passagecov58.id#":{"title":false}},"proposnonorientationsproscovs58":{"0":"Personne.nir","1":"Personne.nom_complet","2":"Adresse.complete","3":"Personne.dtnai","Dossiercov58.created":{"type":"date"},"4":"Decisionpropononorientationprocov58.decisioncov","5":"NvTypeorient.lib_type_orient","6":"NvStructurereferente.lib_struc","7":"NvReferent.nom_complet","\/Covs58\/impressiondecision\/#Passagecov58.id#":{"title":false,"class":"print"},"\/Orientsstructs\/index\/#Personne.id#":{"title":false,"class":"view"},"\/Historiquescovs58\/view\/#Passagecov58.id#":{"title":false}},"proposorientssocialescovs58":{"0":"Personne.nir","1":"Personne.nom_complet","2":"Adresse.complete","3":"Personne.dtnai","Dossiercov58.created":{"type":"date"},"4":"Decisionpropoorientsocialecov58.decisioncov","5":"NvTypeorient.lib_type_orient","6":"NvStructurereferente.lib_struc","7":"NvReferent.nom_complet","\/Covs58\/impressiondecision\/#Passagecov58.id#":{"title":false,"class":"print"},"\/Orientsstructs\/index\/#Personne.id#":{"title":false,"class":"view"},"\/Historiquescovs58\/view\/#Passagecov58.id#":{"title":false}},"nonorientationsproscovs58":{"0":"Personne.nir","1":"Personne.nom_complet","2":"Adresse.complete","3":"Personne.dtnai","Dossiercov58.created":{"type":"date"},"4":"Decisionnonorientationprocov58.decisioncov","5":"NvTypeorient.lib_type_orient","6":"NvStructurereferente.lib_struc","7":"NvReferent.nom_complet","\/Covs58\/impressiondecision\/#Passagecov58.id#":{"title":false,"class":"print"},"\/Orientsstructs\/index\/#Personne.id#":{"title":false,"class":"view"},"\/Historiquescovs58\/view\/#Passagecov58.id#":{"title":false}},"regressionsorientationscovs58":{"0":"Personne.nir","1":"Personne.nom_complet","2":"Adresse.complete","3":"Personne.dtnai","Dossiercov58.created":{"type":"date"},"4":"Decisionregressionorientationcov58.decisioncov","5":"NvTypeorient.lib_type_orient","6":"NvStructurereferente.lib_struc","7":"NvReferent.nom_complet","\/Covs58\/impressiondecision\/#Passagecov58.id#":{"title":false,"class":"print"},"\/Orientsstructs\/index\/#Personne.id#":{"title":false,"class":"view"},"\/Historiquescovs58\/view\/#Passagecov58.id#":{"title":false}}}', 'array(
			''proposorientationscovs58'' => array(
				''Personne.nir'',
				''Personne.nom_complet'',
				''Adresse.complete'',
				''Personne.dtnai'',
				''Dossiercov58.created'' => array( ''type'' => ''date'' ),
				''Decisionpropoorientationcov58.decisioncov'',
				''NvTypeorient.lib_type_orient'',
				''NvStructurereferente.lib_struc'',
				''NvReferent.nom_complet'',
				''Covs58impressiondecision#Passagecov58.id#'' => array(
					''title'' => false,
					''class'' => ''print''
				),
				''Orientsstructsindex#Personne.id#'' => array(
					''title'' => false,
					''class'' => ''view''
				),
				''Historiquescovs58view#Passagecov58.id#'' => array(
					''title'' => false
				)
			),
			''proposcontratsinsertioncovs58'' => array(
				''Personne.nir'',
				''Personne.nom_complet'',
				''Adresse.complete'',
				''Personne.dtnai'',
				''Dossiercov58.created'' => array(
					''type'' => ''date''
				),
				''VxReferent.nom_complet'' => array(
					''label'' => ''Nom du prescripteur''
				),
				''Decisionpropocontratinsertioncov58.decisioncov'',
				''Decisionpropocontratinsertioncov58.dd_ci'',
				''Decisionpropocontratinsertioncov58.duree_engag'',
				''Decisionpropocontratinsertioncov58.df_ci'',
				''Covs58impressiondecision#Passagecov58.id#'' => array(
					''title'' => false,
					''class'' => ''print''
					 FIXME: conditions
				),
				''Contratsinsertionindex#Personne.id#'' => array(
					''title'' => false,
					''class'' => ''view''
				),
				''Historiquescovs58view#Passagecov58.id#'' => array(
					''title'' => false
				)
			),
			''proposnonorientationsproscovs58'' => array(
				''Personne.nir'',
				''Personne.nom_complet'',
				''Adresse.complete'',
				''Personne.dtnai'',
				''Dossiercov58.created'' => array( ''type'' => ''date'' ),
				''Decisionpropononorientationprocov58.decisioncov'',
				''NvTypeorient.lib_type_orient'',
				''NvStructurereferente.lib_struc'',
				''NvReferent.nom_complet'',
				''Covs58impressiondecision#Passagecov58.id#'' => array(
					''title'' => false,
					''class'' => ''print''
					 FIXME: conditions
				),
				''Orientsstructsindex#Personne.id#'' => array(
					''title'' => false,
					''class'' => ''view''
				),
				''Historiquescovs58view#Passagecov58.id#'' => array(
					''title'' => false
				)
			),
			''proposorientssocialescovs58'' => array(
				''Personne.nir'',
				''Personne.nom_complet'',
				''Adresse.complete'',
				''Personne.dtnai'',
				''Dossiercov58.created'' => array( ''type'' => ''date'' ),
				''Decisionpropoorientsocialecov58.decisioncov'',
				''NvTypeorient.lib_type_orient'',
				''NvStructurereferente.lib_struc'',
				''NvReferent.nom_complet'',
				''Covs58impressiondecision#Passagecov58.id#'' => array(
					''title'' => false,
					''class'' => ''print''
					 FIXME: conditions
				),
				''Orientsstructsindex#Personne.id#'' => array(
					''title'' => false,
					''class'' => ''view''
				),
				''Historiquescovs58view#Passagecov58.id#'' => array(
					''title'' => false
				)
			),
			''nonorientationsproscovs58'' => array(
				''Personne.nir'',
				''Personne.nom_complet'',
				''Adresse.complete'',
				''Personne.dtnai'',
				''Dossiercov58.created'' => array( ''type'' => ''date'' ),
				''Decisionnonorientationprocov58.decisioncov'',
				''NvTypeorient.lib_type_orient'',
				''NvStructurereferente.lib_struc'',
				''NvReferent.nom_complet'',
				''Covs58impressiondecision#Passagecov58.id#'' => array(
					''title'' => false,
					''class'' => ''print''
					 FIXME: conditions
				),
				''Orientsstructsindex#Personne.id#'' => array(
					''title'' => false,
					''class'' => ''view''
				),
				''Historiquescovs58view#Passagecov58.id#'' => array(
					''title'' => false
				)
			),
			''regressionsorientationscovs58'' => array(
				''Personne.nir'',
				''Personne.nom_complet'',
				''Adresse.complete'',
				''Personne.dtnai'',
				''Dossiercov58.created'' => array( ''type'' => ''date'' ),
				''Decisionregressionorientationcov58.decisioncov'',
				''NvTypeorient.lib_type_orient'',
				''NvStructurereferente.lib_struc'',
				''NvReferent.nom_complet'',
				''Covs58impressiondecision#Passagecov58.id#'' => array(
					''title'' => false,
					''class'' => ''print''
					 FIXME: conditions
				),
				''Orientsstructsindex#Personne.id#'' => array(
					''title'' => false,
					''class'' => ''view''
				),
				''Historiquescovs58view#Passagecov58.id#'' => array(
					''title'' => false
				)
			),
		)', 45, current_timestamp, current_timestamp),
		('StatistiquePP.tableauA2', '{"lignes_non_affichees":[]}', 'Liste des lignes pouvant ne pas être affichés dans le tableau A2 - Parcous des Brsa (si tableau vide, toutes les lignes seront affichées)
''SSD''				Nombre de personnes Soumises à droits et devoirs
''Orientes''			Nombre de Pers. SDD orientées
	''total''				Nombre total
	''percent''				Equivalent en %
	''Emploi''				Orientées Emploi / professionnelles
	''percentEmploi''		Equivalent en %
	''Prepro''				Orientées Préprofessionnelles
	''percentPrepro''		Equivalent en %
	''Social''				Orientées Sociales
	''percentSocial''		Equivalent en %
	''PE''					Orientées PE
	''percentPE''			Equivalent en %
	''CD''					Orientées CD
	''percentCD''			Equivalent en %
	''OA''					Orientées OA
	''percentOA''			Equivalent en %
''Contrat''				Pers. SSD contrat actif
	''total''				Nombre total
	''percent''				Equivalent en %
	''PEPPAE''				Pers. SDD orientées PE ayant un PPAE
	''percentPEPPAE''		Equivalent en %
	''CDCER''				Pers. SDD orientées CD ayant un CER
	''percentCDCER''		Equivalent en %
	''PEAider''				Pers. SDD orientées PE ayant un contrat aidé
	''percentPEAider''		Equivalent en %
	''PEAccomp''			Pers. SDD orientées PE ayant un contrat d’accompagnement (Chrs, MLI, ADRH)
	''percentPEAccomp''		Equivalent en %
''CDCER''				Pers. SDD orientés CD ayant un CER “actif» par mois
	''total''				Nombre total
	''Social''				orientées “Social” avec un CER “actif”
	''percentSocial''		Equivalent en %
	''Prepro''				orientées “Pré pro” avec un CER “actif”
	''percentPrepro''		Equivalent en %
''RDVCER''				Rdv pour CER et suivi', 1, current_timestamp, current_timestamp),
    ('StatistiquePP.tableauB1', '{"lignes_non_affichees":[]}', 'Listes des lignes à ne pas afficher pour le tableau B1 - Suivi de la 1ère orientation
''total''				Nombres des nouvelles Pers. SDD
''Orientes''			Pers. SDD orientés (1ère orientation)
	''total''			Nombre total
	''emploi''		orientées Emploi / Professionnelle
	''prepro''		orientées Prépro
	''social''		orientées Social
	''pe''			orientées PE
	''cd''			orientées CD
	''oa''			orientées OA
''NonOrientes''		Pers. SDD non orientée
	''total''			Nombre total
	''prevu''			Convocation prévue
	''bilan''			Bilan EP
	''autres''		Autres
	)
''delai_moyen''		Délai moyen en jours
''orient_31jours''	Orientation en moins de 31 jours
''delai''				Délais
''taux_orient''		Taux d''orientation', 1, current_timestamp, current_timestamp),
    ('StatistiquePP.tableauB4', '{"lignes_non_affichees":[]}', 'Listes des champs à ne pas afficher dans le tableau B4 : Suivi de l''accompagnement
''total''					Nombre de 1er rendez-vous fixé suite à une orientation
''Social''				Orientation sociale
	''total''
	''venu''
	''excuse_recevable''
	''sans_excuse''
	''delai_moyen''
	''delai''
		''0_29''
		''30_59''
		''60_89''
		''90_999''
	''taux_presence''
''Prepro''				Prépro
	''total''
	''venu''
	''excuse_recevable''
	''sans_excuse''
	''delai_moyen''
	''delai''
		''0_29''
		''30_59''
		''60_89''
		''90_999''
	''taux_presence''' , 1, current_timestamp, current_timestamp),
    ('StatistiquePP.tableauB5', '{"lignes_non_affichees":[]}', 'Liste des champs à ne pas afficher pour le tableau B5 : Suivi des CER
''orient_valid''		Nombre de 1ère orientation
''cer_social''		Nombre de CER "Social" validé
''cer_prepro''		Nombre de CER "Pré pro" validé
''delai_moyen''		Délais moyen de contractualisation en jours
''delai_social''		Délais moyen de contractualisation "Social" en jours
''delai_prepro''		Délais moyen de contractualisation "Pré pro" en jours
''signe15jrs''		Nombre de CER signés en moins de 15 jours
''delai''				Délais de contractualisation
''taux_contrat''		Taux de contractualisation', 1, current_timestamp, current_timestamp),
    ('StatistiquePP.tableauA1v2', '{"lignes_non_affichees":[]}', 'Listes des champs à ne pas afficher dans le tableau A1 - L''orientation des nouveaux entrant avec notion de suspendu et hors suspendu:
	''Tous''						Nombre total des Personnes (Suspendu & Hors Supendu)
		''total''					Nouveaux entrants par mois
		''nbFoyerInconnu''			Primo arrivants
		''nbFoyerRadiSusp''			Suspendus et non orientés ou radiés
		''nbToppers''				BRSA non-SDD qui le sont désormais
		''nbFoyerJoin''				BRSA rejoignant un foyer RSA
		''nbEMM''					BRSA venant de s’installer sur le Dpt (mutation)
		''Orientes''				Nombre de nouveaux entrants orientés en moins d’un mois
			''total''				Nombre Total
			''Emploi''				Pers. SDD orientées Emploi
			''percentEmploi''		Équivalent en %
			''Prepro''				Pers. SDD orientées Emploi
			''percentPrepro''		Équivalent en %
			''Social''				Pers. SDD orientées Emploi
			''percentSocial''		Équivalent en %
			''PE''					Pers. SDD orientées Emploi
			''percentPE''			Équivalent en %
			''CD''					Pers. SDD orientées Emploi
			''percentCD''			Équivalent en %
			''OA''					Pers. SDD orientées Emploi
			''percentOA''			Équivalent en %
		''percentOrientes''			Taux d''orientation
		''Nonvenu''				Nombre de personnes non venu en info coll
			''RDV''				Nombre total
			''percentRDV''			Équivalent en %
	''Horssuspendus''				Uniquement les hors suspendus
		''total''
		''nbFoyerInconnu''
		''nbFoyerRadiSusp''
		''nbToppers''
		''nbFoyerJoin''
		''nbEMM''
		''Orientes''
			''total''
			''Emploi''
			''percentEmploi''
			''Prepro''
			''percentPrepro''
			''Social''
			''percentSocial''
			''PE''
			''percentPE''
			''CD''
			''percentCD''
			''OA''
			''percentOA''
		''percentOrientes''
		''Nonvenu''
			''RDV''
			''percentRDV''
	''Suspendus''					Uniquement les suspendus
		''total''
		''nbFoyerInconnu''
		''nbFoyerRadiSusp''
		''nbToppers''
		''nbFoyerJoin''
		''nbEMM''
		''Orientes''
			''total''
			''Emploi''
			''percentEmploi''
			''Prepro''
			''percentPrepro''
			''Social''
			''percentSocial''
			''PE''
			''percentPE''
			''CD''
			''percentCD''
			''OA''
			''percentOA''
		''percentOrientes''
		''Nonvenu''
			''RDV''
			''percentRDV''', 1, current_timestamp, current_timestamp),
    ('StatistiquePP.tableauA2av2', '{"lignes_non_affichees":[]}', 'Listes des champs à ne pas afficher dans le tableau A2a v2 - Les parcours d’accompagnement, A - le 1er rdv d’accompagnement
	''Tous''					Nombre total des Personnes (Suspendu & Hors Supendu)
		''Orientes_CD''		Nombre de nouveaux entrants orientés CD
		''Orientes''			Avec 1er RDV ficé suite orientation CD
			''RDV''			Total
			''RDV_Prepro''	Dont Prépro
			''RDV_Social''	Dont Social
		''Orientes1m''		Orienté en moins d''un mois
		''Orientes15j''		orienté dans un délai de 15 jours
			''RDV''			Total
			''RDV_Prepro''	Dont Prépro
			''RDV_Social''	Dont Social
		''Taux''				Taux de 1er rdv fixés dans les délais du plan pauvreté
	''Horssuspendus''			Uniquement les hors suspendus
		''Orientes_CD''
		''Orientes''
			''RDV''
			''RDV_Prepro''
			''RDV_Social''
		''Orientes1m''
		''Orientes15j''
			''RDV''
			''RDV_Prepro''
			''RDV_Social''
		''Taux''
	''Suspendus''			Uniquement les suspendus
		''Orientes_CD''
		''Orientes''
			''RDV''
			''RDV_Prepro''
			''RDV_Social''
		''Orientes1m''
		''Orientes15j''
			''RDV''
			''RDV_Prepro''
			''RDV_Social''
		''Taux''', 1, current_timestamp, current_timestamp),
    ('StatistiquePP.tableauA2bv2', '{"lignes_non_affichees":[]}', 'Listes des champs à ne pas afficher dans le tableau A2b v2 - Les parcours d’accompagnement, B - le 1er rdv CER
	''Tous''					Nombre total des Personnes (Suspendu & Hors Supendu)
		''Orientes_CD''		Nombre de nouveaux entrants orientés CD
		''Orientes''			Avec 1er RDV ficé suite orientation CD
			''CER''			Total
			''CER_Prepro''	Dont Prépro
			''CER_Social''	Dont Social
		''Orientes1m''		Orienté en moins d''un mois
		''Orientes2m''		orienté dans un délai de 2 mois
			''CER''			Total
			''CER_Prepro''	Dont Prépro
			''CER_Social''	Dont Social
		''Taux''				Taux de 1er CER fixés dans les délais du plan pauvreté
	''Horssuspendus''			Uniquement les hors suspendus
		''Orientes_CD''
		''Orientes''
			''CER''
			''CER_Prepro''
			''CER_Social''
		''Orientes1m''
		''Orientes2m''
			''CER''
			''CER_Prepro''
			''CER_Social''
		''Taux''
	''Suspendus''				Uniquement les suspendus
		''Orientes_CD''
		''Orientes''
			''CER''
			''CER_Prepro''
			''CER_Social''
		''Orientes1m''
		''Orientes2m''
			''CER''
			''CER_Prepro''
			''CER_Social''
		''Taux''', 1, current_timestamp, current_timestamp),
    ('StatistiquePP.tableauA1v3', '{"lignes_non_affichees":[]}', 'Listes des champs à ne pas afficher dans le tableau A1 v3 - L''orientation des nouveaux entrant :
	''total''					Nouveaux entrants par mois
	''nbFoyerInconnu''			Primo arrivants
	''nbFoyerRadiSusp''			Suspendus et non orientés ou radiés
	''nbToppers''				BRSA non-SDD qui le sont désormais
	''nbFoyerJoin''				BRSA rejoignant un foyer RSA
	''nbEMM''					BRSA venant de s’installer sur le Dpt (mutation)
	''Orientes''				Nombre de nouveaux entrants orientés en moins d’un mois
		''total''				Nombre Total
		''Emploi''				Pers. SDD orientées Emploi
		''percentEmploi''		Équivalent en %
		''Prepro''				Pers. SDD orientées Emploi
		''percentPrepro''		Équivalent en %
		''Social''				Pers. SDD orientées Emploi
		''percentSocial''		Équivalent en %
		''PE''					Pers. SDD orientées Emploi
		''percentPE''			Équivalent en %
		''CD''					Pers. SDD orientées Emploi
		''percentCD''			Équivalent en %
		''OA''					Pers. SDD orientées Emploi
		''percentOA''			Équivalent en %
	''percentOrientes''			Taux d''orientation
	''Nonvenu''				Nombre de personnes non venu en info coll
		''RDV''				Nombre totalnull
		''percentRDV''			Équivalent en %', 1, current_timestamp, current_timestamp),
    ('StatistiquePP.tableauA2av3', '{"lignes_non_affichees":[]}', 'Listes des champs à ne pas afficher dans le tableau A2a v3 - Les parcours d’accompagnement, A - le 1er rdv d’accompagnement
	''Orientes_CD''		Nombre de nouveaux entrants orientés CD
	''Orientes''			Avec 1er RDV ficé suite orientation CD
		''RDV''			Total
		''RDV_Prepro''	Dont Prépro
		''RDV_Social''	Dont Social
	''Orientes1m''		Orienté en moins d''un mois
	''Orientes15j''		orienté dans un délai de 15 jours
		''RDV''			Total
		''RDV_Prepro''	Dont Prépro
		''RDV_Social''	Dont Social
	''Taux''				Taux de 1er rdv fixés dans les délais du plan pauvreté', 1, current_timestamp, current_timestamp),
    ('StatistiquePP.tableauA2bv3', '{"lignes_non_affichees":[]}', 'Listes des champs à ne pas afficher dans le tableau A2b v3 - Les parcours d’accompagnement, B - le 1er rdv CER
	''Orientes_CD''		Nombre de nouveaux entrants orientés CD
	''Orientes''			Avec 1er RDV ficé suite orientation CD
		''CER''			Total
		''CER_Prepro''	Dont Prépro
		''CER_Social''	Dont Social
	''Orientes1m''		Orienté en moins d''un mois
	''Orientes2m''		orienté dans un délai de 2 mois
		''CER''			Total
		''CER_Prepro''	Dont Prépro
		''CER_Social''	Dont Social
	''Taux''				Taux de 1er CER fixés dans les délais du plan pauvreté', 1, current_timestamp, current_timestamp);

-- Ajout de la documentation de la variable de gestion des statistiques PP
UPDATE configurations SET comments_variable  = 'Configuration des statistiques du Plan pauvreté.

"conditions_droits_et_devoirs"
	"Situationdossierrsa.etatdosrsa" => états des dossiers permettant de s''assurer qu''un allocataire est des droits et devoirs.
	"Calculdroitrsa.toppersdrodevorsa" =>possède un droit ouvert et versable.

"etatSuspendus" => listes des états suspendus.

"useHistoriquedroit" => utilise l''historique ou non.

"delais" => délai de première orientation

"orientationRdv"
	"venu" => identifiant des statuts de rendez-vous pour lesquels le bénéficiaire est venu.
	"prevu" => identifiant des statuts de rendez-vous prévus.
	"excuses_recevables" => identifiant des statuts de rendez-vous pour lesquels le bénéficiaire n''est pas venu et qui a une excuse recevable.
	"excuses_non_recevables" => identifiant des statuts de rendez-vous pour lesquels le bénéficiaire n''est pas venu et qui n''a pas d''excuse recevable.

"code_stats" => dans le tableau A2, seul ces codes sont pris en compte (par exemple : "CHRS", "MLJ", "ADRH").

"type_rendezvous" => dans les tableaux A2, a2av2 et a2av3 seul ce type de rendez-vous est pris en compte.

"statut_rendezvous" => dans les tableaux a1_v2 et a1_v3, seul ce statut de rendez-vous est pris en compte.'
WHERE lib_variable = 'Statistiqueplanpauvrete';


-- Création de la configuration de la gestion des doublons
INSERT INTO public.configurations (lib_variable, value_variable, comments_variable, configurationscategorie_id, created, modified)
VALUES
    ('Gestionsdoublons.index.deleteAllUnBound.cascade', 'true', 'Activation de la suppression en cascade des fichiers des modules.', 1, current_timestamp, current_timestamp);


-- Ajout de la configuration permettant d'avoir les primo accédant ou les
-- nouveaux entrant dans les cohortes PP
INSERT INTO public.configurations (lib_variable, value_variable, comments_variable, created, modified)
    VALUES('PlanPauvrete.Cohorte.Primoaccedant', 'false', 'Si la variable est à true alors les cohortes Inscrits PE et Information collective du menu Plan Pauvreté / Nouveaux entrants ne prendront en compte que les primo accédants.
Sinon, elle prendra en compte tous les nouveaux entrants', current_timestamp, current_timestamp);
UPDATE public.configurations SET configurationscategorie_id = configurationscategories.id FROM configurationscategories WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable = 'PlanPauvrete.Cohorte.Primoaccedant';

UPDATE public.configurations SET value_variable = '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Orientstruct.id":{"hidden":true},"Historiqueetatpe.identifiantpe":{"sort":false},"Historiqueetatpe.date":{"sort":false},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Dossier.ddarrmut":{"sort":false},"Personne.nom_complet":{"sort":false},"Personne.nomnai":{"sort":false},"Personne.dtnati":{"sort":false},"Canton.canton":{"sort":false},"Situationdossierrsa.etatdosrsa":{"sort":false},"Structurereferente.lib_struc":{"sort":false},"Orientstruct.statut_orient":{"sort":false},"Orientstruct.date_valid":{"sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Orientsstructs\/impression\/#Orientstruct.id#":{"class":"external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":[]},"ini_set":[]}' WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreteorientations.cohorte_isemploi_imprime';
UPDATE public.configurations SET value_variable = '{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Orientstruct.id":{"hidden":true},"Historiqueetatpe.identifiantpe":{"sort":false},"Historiqueetatpe.date":{"sort":false},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Dossier.ddarrmut":{"sort":false},"Personne.nom_complet":{"sort":false},"Personne.nomnai":{"sort":false},"Personne.dtnati":{"sort":false},"Canton.canton":{"sort":false},"Situationdossierrsa.etatdosrsa":{"sort":false},"Structurereferente.lib_struc":{"sort":false},"Orientstruct.statut_orient":{"sort":false},"Orientstruct.date_valid":{"sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Orientsstructs\/impression\/#Orientstruct.id#":{"class":"external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":[]},"ini_set":[]}' WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreteorientations.cohorte_isemploi_stock_imprime';

-- Modification de la variable qui configure le dossier tempraire des PDF
UPDATE public.configurations SET value_variable = '"tmp\/files\/pdf"' WHERE lib_variable LIKE 'Cohorte.dossierTmpPdfs';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
