SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

--Insertion des sujets du référentiel
insert into administration.sujetsreferentiels
(code, libelle, nom_table, modele_enum, nom_enum, nom_config, correspondance_colonnes) values
('typesorients', 'Types d''orientation', 'typesorients', null, null, null, '{"id_dans_table":"id","libelle":"lib_type_orient","typesorients_parent_id":"parentid","actif":"''O''"}'),
('zonesgeographiques', 'Zones géographiques', 'zonesgeographiques', null, null, null, '{"id_dans_table":"id","libelle":"libelle","zonesgeographiques_code_insee":"codeinsee"}'),
('structuresreferentes', 'Structures référentes', 'structuresreferentes', null, null, null, '{"id_dans_table":"id","structuresreferentes_typeorient_id":"typeorient_id","libelle":"lib_struc","structuresreferentes_numvoie":"num_voie","structuresreferentes_typevoie":"type_voie","structuresreferentes_nomvoie":"nom_voie","structuresreferentes_codepostal":"code_postal","structuresreferentes_ville":"ville","structuresreferentes_codeinsee":"code_insee","structuresreferentes_numtel":"numtel","structuresreferentes_email":"email","actif":"''O''"}'),
('civilite', 'Civilité', null, 'Personne', 'qual', null, null),
('etatdos', 'Etat du droit', null, 'Dossier', 'etatdosrsa', null, null),
('rolepers', 'Rôle personne', null, 'Prestation', 'rolepers', null, null),
('sitfam', 'Situation familiale', null, 'Foyer', 'sitfam', null, null),
('orient_origine', 'Origine de l''orientation', null, 'Orientstruct', 'origine', null, null),
('orient_origine_utilisable_ALI', 'Origine de l''orientation utilisable par les ALI', null, null, null, 'Orientstruct.origine.utilisable_ALI', null),
('orient_statut', 'Statut de l''orientation', null, 'Orientstruct', 'statut_orient', null, null),
('cer_statut', 'Statut du CER', null, 'Cer93', 'positioncer', null, null),
('referents', 'Référents', 'referents', null, null, null, '{"id_dans_table":"id","referents_structurereferente_id":"structurereferente_id","referents_civilite":"qual","referents_nom":"nom","referents_prenom":"prenom","referents_email":"email","referents_fonction":"fonction","referents_numtel":"numero_poste","referents_date_cloture":"datecloture","actif":"''O''"}'),
('rdv_objet', 'Objets de rendez-vous', 'typesrdv', null, null, null, '{"id_dans_table":"id","libelle":"libelle"}'),
('rdv_thematique', 'Thématiques de rendez-vous', 'thematiquesrdvs', null, null, null, '{"id_dans_table":"id","libelle":"name","rdv_thematique_typerdv_id":"typerdv_id","rdv_thematique_statutrdv_id":"statutrdv_id","rdv_thematique_acomptabiliser":"acomptabiliser","actif":"1"}'),
('rdv_statut', 'Statuts de rendez-vous', 'statutsrdvs', null, null, null, '{"id_dans_table":"id","libelle":"libelle","actif":"''1''"}'),
('cmu', 'CMU', null, 'Cer93', 'cmu', null, null),
('cer_nivetu', 'Niveau d''étude CER', null, 'Cer93', 'nivetu', null, null),
('code_famille', 'Code famille', 'famillesromesv3', null, null, null, '{"id_dans_table":"id","libelle":"name","code":"code"}'),
('code_domaine', 'Code domaine', 'domainesromesv3', null, null, null, '{"id_dans_table":"id","libelle":"name","code":"code","code_domaine_codefamille_id":"familleromev3_id"}'),
('code_metier', 'Code métier', 'metiersromesv3', null, null, null, '{"id_dans_table":"id","libelle":"name","code":"code","code_metier_codedomaine_id":"domaineromev3_id"}'),
('appellation_metier', 'Appellation métier', 'appellationsromesv3', null, null, null, '{"id_dans_table":"id","libelle":"name","appell_metier_codemetier_id":"metierromev3_id"}'),
('nature_contrat', 'Nature du contrat', 'naturescontrats', null, null, null, '{"id_dans_table":"id","libelle":"name","nature_contrat_definir_duree":"isduree"}'),
('type_duree_contrat', 'Unité de durée du contrat', null, 'Expprocer93', 'typeduree', null, null),
('secteur_activite', 'Secteur d''activité', 'secteursactis', null, null, null, '{"id_dans_table":"id","libelle":"name"}'),
('metier_exerce', 'Métier exercé', 'metiersexerces', null, null, null, '{"id_dans_table":"id","libelle":"name"}'),
('duree_cdd', 'Durée CDD', null, 'Contratinsertion', 'duree_cdd', null, null),
('cer_sujet', 'Sujet du CER', 'sujetscers93', null, null, null, '{"id_dans_table":"id","libelle":"name","cer_sujet_champ_texte":"isautre","actif":"''1''"}'),
('cer_sous_sujet', 'Sous-sujet du CER', 'soussujetscers93', null, null, null, '{"id_dans_table":"id","libelle":"name","cer_sous_sujet_sujet_id":"sujetcer93_id","cer_sous_sujet_champ_texte":"isautre","actif":"''1''"}'),
('cer_valeurs_sous_sujet', 'Valeur par sous-sujet du CER', 'valeursparsoussujetscers93', null, null, null, '{"id_dans_table":"id","libelle":"name","cer_valeurs_sous_sujet_sujet_id":"soussujetcer93_id","cer_valeurs_sous_sujet_champ_texte":"isautre","actif":"''1''"}'),
('cer_duree', 'Durée du CER', null, null, null, 'cer.duree.engagement', null),
('cer_pointparcours', 'Point parcours du CER', null, 'Cer93', 'pointparcours', null, null),
('cer_forme', 'Forme du CER', null, 'Cer93', 'formeci', null, null),
('cer_commentaire', 'Commentaires normés du CER', 'commentairesnormescers93', null, null, null, '{"id_dans_table":"id","libelle":"name","cer_commentaire_champ_texte":"isautre"}'),
('reorientation_motifs', 'Motfis de demande de réorientation', 'motifsreorientseps93', null, null, null, '{"id_dans_table":"id","libelle":"name"}'),
('nationalite', 'Nationalité', null, 'Situationallocataire', 'nati', null, null),
('marche_travail', 'Statut sur le marché du travail', null, 'Questionnaired1pdv93', 'marche_travail', null, null),
('groupe_vulnerable', 'Groupe vulnérable', null, 'Questionnaired1pdv93', 'vulnerable', null, null),
('cat_sociopro', 'Professions et catégories socioprofessionnelles', null, 'Questionnaired1pdv93', 'categorie_sociopro', null, null),
('conditions_logement', 'Conditions de logement', null, 'Questionnaired1pdv93', 'conditions_logement', null, null),
('statut_accompagnement', 'Statut de l''accompagnement', null, 'Questionnaired2pdv93', 'situationaccompagnement', null, null),
('motif_sortie_obligation_accompagnement', 'Motifs de sortie de l''obligation d''accompagnement', 'sortiesaccompagnementsd2pdvs93', null, null, null, '{"id_dans_table":"id","libelle":"name","code":"code","mot_sortie_oblig_acc_typeemploi_code":"codetypeemploi","actif":"1","condition":"parent_id is not null and (code != ''SORTIE_D2'' or code is null)"}'),
('motif_changement_admin', 'Motifs changement administratif', null, 'questionnaired2pdv93','chgmentsituationadmin', null, '{"id_dans_table":"id","libelle":"name","code":"code","actif":"1"}'),
('temps_travail', 'Temps de travail', 'dureeemplois', null, null, null, '{"id_dans_table":"id","libelle":"name"}'),
('dsp_nivetu', 'Niveau d''études DSP', null, 'Dsp', 'nivetu', null, null),
('diplome_max', 'Diplôme le plus élevé', null, 'Dsp', 'nivdipmaxobt', null, null),
('type_emploi', 'Type d''emploi', 'typeemplois', null, null, null, '{"id_dans_table":"id","libelle":"name", "type_emploi_code_type_emploi":"codetypeemploi"}');


-- *****************************************************************************
COMMIT;
-- *****************************************************************************
