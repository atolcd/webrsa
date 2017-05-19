/************************************/
/* Liste index ajouts du 23/07/2009 */
/************************************/

/* Index table personnes */

CREATE INDEX personnes_prenom_idx ON personnes (prenom);
CREATE INDEX personnes_foyer_id_idx ON personnes (foyer_id);
CREATE INDEX personnes_nom_idx ON personnes (nom);
CREATE INDEX personnes_nomnai_idx ON personnes (nomnai);
CREATE INDEX personnes_dtnai_idx ON personnes (dtnai);
CREATE INDEX personnes_nir_idx ON personnes (nir);

/* Index table dossiers_rsa */

CREATE INDEX dossiers_rsa_numdemrsa_idx ON dossiers_rsa (numdemrsa);
CREATE INDEX dossiers_rsa_dtdemrsa_idx ON dossiers_rsa (dtdemrsa);
CREATE INDEX dossiers_rsa_numcli_idx ON dossiers_rsa (numcli);
CREATE INDEX dossiers_rsa_matricule_idx ON dossiers_rsa (matricule);
CREATE INDEX dossiers_rsa_statudemrsa_idx ON dossiers_rsa (statudemrsa);

/* Index table adresses */

CREATE INDEX adresses_nomvoie_idx ON adresses (nomvoie);
CREATE INDEX adresses_numcomrat_idx ON adresses (numcomrat);
CREATE INDEX adresses_numcomptt_idx ON adresses (numcomptt);
CREATE INDEX adresses_codepos_idx ON adresses (codepos);
CREATE INDEX adresses_locaadr_idx ON adresses (locaadr);

/* Index table adresses_foyers */

CREATE INDEX adresses_foyers_adresse_id_idx ON adresses_foyers (adresse_id);
CREATE INDEX adresses_foyers_foyer_id_idx ON adresses_foyers (foyer_id);
CREATE INDEX adresses_foyers_rgadr_idx ON adresses_foyers (rgadr);

/* Index table prestations */

CREATE INDEX prestations_personne_id_idx ON prestations (personne_id);
CREATE INDEX prestations_natprest_idx ON prestations (natprest);
CREATE INDEX prestations_rolepers_idx ON prestations (rolepers);
CREATE INDEX prestations_topchapers_idx ON prestations (topchapers);
CREATE INDEX prestations_toppersdrodevorsa_idx ON prestations (toppersdrodevorsa);


/* Index table foyers */

CREATE INDEX foyers_dossier_rsa_id_idx ON foyers (dossier_rsa_id);
CREATE INDEX foyers_sitfam_idx ON foyers (sitfam);

/* Index table creancesalimentaires */

CREATE INDEX creancesalimentaires_etatcrealim_idx ON creancesalimentaires (etatcrealim);

/* Index table detailsdroitsrsa */

CREATE INDEX detailsdroitsrsa_dossier_rsa_id_idx ON detailsdroitsrsa (dossier_rsa_id);
CREATE INDEX detailsdroitsrsa_topfoydrodevorsa_idx ON detailsdroitsrsa (topfoydrodevorsa);

/* Index table detailscalculsdroitsrsa */

CREATE INDEX detailscalculsdroitsrsa_detaildroitrsa_id_idx ON detailscalculsdroitsrsa (detaildroitrsa_id);
CREATE INDEX detailscalculsdroitsrsa_natpf_idx ON detailscalculsdroitsrsa (natpf);

/* Index table situationsdossiersrsa */

CREATE INDEX situationsdossiersrsa_dossier_rsa_id_idx ON situationsdossiersrsa (dossier_rsa_id);
CREATE INDEX situationsdossiersrsa_etatdosrsa_idx ON situationsdossiersrsa (etatdosrsa);
CREATE INDEX situationsdossiersrsa_dtrefursa_idx ON situationsdossiersrsa (dtrefursa);
CREATE INDEX situationsdossiersrsa_moticlorsa_idx ON situationsdossiersrsa (moticlorsa);
CREATE INDEX situationsdossiersrsa_dtclorsa_idx ON situationsdossiersrsa (dtclorsa);

/* Index table suspensionsversements */

CREATE INDEX suspensionsversements_situationdossierrsa_id_idx ON suspensionsversements (situationdossierrsa_id);
CREATE INDEX suspensionsversements_ddsusversrsa_idx ON suspensionsversements (ddsusversrsa);
CREATE INDEX suspensionsversements_motisusversrsa_idx ON suspensionsversements (motisusversrsa);

/* Index table suspensionsdroits */

CREATE INDEX suspensionsdroits_situationdossierrsa_id_idx ON suspensionsdroits (situationdossierrsa_id);
CREATE INDEX suspensionsdroits_motisusdrorsa_idx ON suspensionsdroits (motisusdrorsa);
CREATE INDEX suspensionsdroits_ddsusdrorsa_idx ON suspensionsdroits (ddsusdrorsa);

/************************************/
/* Liste index ajouts du 24/07/2009 */
/************************************/

/* Index table dspfs */

CREATE INDEX dspfs_foyer_id_idx ON dspfs (foyer_id);
CREATE INDEX dspfs_accosocfam_idx ON dspfs (accosocfam);

/* Index table diflogs */

CREATE INDEX diflogs_name_idx ON diflogs (name);
CREATE INDEX diflogs_code_idx ON diflogs (code);

/* Index table dspfs_diflogs */

CREATE INDEX dspfs_diflogs_diflog_id_idx ON dspfs_diflogs (diflog_id);
CREATE INDEX dspfs_diflogs_dspf_id_idx ON dspfs_diflogs (dspf_id);

/* Index table nataccosocfams */

CREATE INDEX nataccosocfams_name_idx ON nataccosocfams (name);
CREATE INDEX nataccosocfams_code_idx ON nataccosocfams (code);

/* Index table dspfs_nataccosocfams */

CREATE INDEX dspfs_nataccosocfams_nataccosocfam_id_idx ON dspfs_nataccosocfams (nataccosocfam_id);
CREATE INDEX dspfs_nataccosocfams_dspf_id_idx ON dspfs_nataccosocfams (dspf_id);

/* Index table accoemplois */

CREATE INDEX accoemplois_name_idx ON accoemplois (name);
CREATE INDEX accoemplois_code_idx ON accoemplois (code);

/* Index table dspps_accoemplois */

CREATE INDEX dspps_accoemplois_accoemploi_id_idx ON dspps_accoemplois (accoemploi_id);
CREATE INDEX dspps_accoemplois_dspp_id_idx ON dspps_accoemplois (dspp_id);

/* Index table nataccosocindis */

CREATE INDEX nataccosocindis_name_idx ON nataccosocindis (name);
CREATE INDEX nataccosocindis_code_idx ON nataccosocindis (code);

/* Index table dspps_nataccosocindis */

CREATE INDEX dspps_nataccosocindis_nataccosocindi_id_idx ON dspps_nataccosocindis (nataccosocindi_id);
CREATE INDEX dspps_nataccosocindis_dspp_id_idx ON dspps_nataccosocindis (dspp_id);

/* Index table difdisps */

CREATE INDEX difdisps_name_idx ON difdisps (name);
CREATE INDEX difdisps_code_idx ON difdisps (code);

/* Index table dspps_difdisps */

CREATE INDEX dspps_difdisps_difdisp_id_idx ON dspps_difdisps (difdisp_id);
CREATE INDEX dspps_difdisps_dspp_id_idx ON dspps_difdisps (dspp_id);

/* Index table natmobs */

CREATE INDEX natmobs_name_idx ON natmobs (name);
CREATE INDEX natmobs_code_idx ON natmobs (code);

/* Index table dspps_natmobs */

CREATE INDEX dspps_natmobs_natmob_id_idx ON dspps_natmobs (natmob_id);
CREATE INDEX dspps_natmobs_dspp_id_idx ON dspps_natmobs (dspp_id);

/* Index table nivetus */

CREATE INDEX nivetus_name_idx ON nivetus (name);
CREATE INDEX nivetus_code_idx ON nivetus (code);

/* Index table dspps_nivetus */

CREATE INDEX dspps_nivetus_nivetu_id_idx ON dspps_nivetus (nivetu_id);
CREATE INDEX dspps_nivetus_dspp_id_idx ON dspps_nivetus (dspp_id);

/* Index table difsocs */

CREATE INDEX difsocs_name_idx ON difsocs (name);
CREATE INDEX difsocs_code_idx ON difsocs (code);

/* Index table dspps_difsocs */

CREATE INDEX dspps_difsocs_difsoc_id_idx ON dspps_difsocs (difsoc_id);
CREATE INDEX dspps_difsocs_dspp_id_idx ON dspps_difsocs (dspp_id);

/* Index table ressources */

CREATE INDEX ressources_personne_id_idx ON ressources (personne_id);
CREATE INDEX ressources_ddress_idx ON ressources (ddress);
CREATE INDEX ressources_dfress_idx ON ressources (dfress);

/* Index table ressourcesmensuelles */

CREATE INDEX ressourcesmensuelles_ressource_id_idx ON ressourcesmensuelles (ressource_id);
CREATE INDEX ressourcesmensuelles_moisress_idx ON ressourcesmensuelles (moisress);

/* Index table detailsressourcesmensuelles */

CREATE INDEX detailsressourcesmensuelles_ressourcemensuelle_id_idx ON detailsressourcesmensuelles (ressourcemensuelle_id);
CREATE INDEX detailsressourcesmensuelles_natress_idx ON detailsressourcesmensuelles (natress);
CREATE INDEX detailsressourcesmensuelles_dfpercress_idx ON detailsressourcesmensuelles (dfpercress);

/* Index table contratsinsertion */

CREATE INDEX contratsinsertion_personne_id_idx ON contratsinsertion (personne_id);
CREATE INDEX contratsinsertion_structurereferente_id_idx ON contratsinsertion (structurereferente_id);
CREATE INDEX contratsinsertion_typocontrat_id_idx ON contratsinsertion (typocontrat_id);
CREATE INDEX contratsinsertion_dd_ci_idx ON contratsinsertion (dd_ci);
CREATE INDEX contratsinsertion_df_ci_idx ON contratsinsertion (df_ci);
CREATE INDEX contratsinsertion_date_saisi_ci_idx ON contratsinsertion (date_saisi_ci);
CREATE INDEX contratsinsertion_datevalidation_ci_idx ON contratsinsertion (datevalidation_ci);
CREATE INDEX contratsinsertion_decision_ci_idx ON contratsinsertion (decision_ci);

/* Index table typoscontrats */

CREATE INDEX typoscontrats_lib_typo_idx ON typoscontrats (lib_typo);

/* Index table orientsstructs */

CREATE INDEX orientsstructs_personne_id_idx ON orientsstructs (personne_id);
CREATE INDEX orientsstructs_typeorient_id_idx ON orientsstructs (typeorient_id);
CREATE INDEX orientsstructs_structurereferente_id_idx ON orientsstructs (structurereferente_id);
CREATE INDEX orientsstructs_propo_algo_idx ON orientsstructs (propo_algo);
CREATE INDEX orientsstructs_valid_cg_idx ON orientsstructs (valid_cg);
CREATE INDEX orientsstructs_date_propo_idx ON orientsstructs (date_propo);
CREATE INDEX orientsstructs_date_valid_idx ON orientsstructs (date_valid);
CREATE INDEX orientsstructs_statut_orient_idx ON orientsstructs (statut_orient);
CREATE INDEX orientsstructs_date_impression_idx ON orientsstructs (date_impression);

/* Index table structuresreferentes */

CREATE INDEX structuresreferentes_typeorient_id_idx ON structuresreferentes (typeorient_id);
CREATE INDEX structuresreferentes_lib_struc_idx ON structuresreferentes (lib_struc);
CREATE INDEX structuresreferentes_code_postal_idx ON structuresreferentes (code_postal);
CREATE INDEX structuresreferentes_ville_idx ON structuresreferentes (ville);
CREATE INDEX structuresreferentes_code_insee_idx ON structuresreferentes (code_insee);


/* Index table structuresreferentes_zonesgeographiques */

CREATE INDEX structuresreferentes_zonesgeographiques_structurereferente_id_idx ON structuresreferentes_zonesgeographiques (structurereferente_id);
CREATE INDEX structuresreferentes_zonesgeographiques_zonegeographique_id_idx ON structuresreferentes_zonesgeographiques (zonegeographique_id);

/* Index table zonesgeographiques */

CREATE INDEX zonesgeographiques_codeinsee_idx ON zonesgeographiques (codeinsee);
CREATE INDEX zonesgeographiques_libelle_idx ON zonesgeographiques (libelle);

/* Index table servicesinstructeurs */

CREATE INDEX servicesinstructeurs_lib_service_idx ON servicesinstructeurs (lib_service);
CREATE INDEX servicesinstructeurs_code_insee_idx ON servicesinstructeurs (code_insee);
CREATE INDEX servicesinstructeurs_code_postal_idx ON servicesinstructeurs (code_postal);
CREATE INDEX servicesinstructeurs_ville_idx ON servicesinstructeurs (ville);
CREATE INDEX servicesinstructeurs_numdepins_idx ON servicesinstructeurs (numdepins);
CREATE INDEX servicesinstructeurs_typeserins_idx ON servicesinstructeurs (typeserins);
CREATE INDEX servicesinstructeurs_numcomins_idx ON servicesinstructeurs (numcomins);
CREATE INDEX servicesinstructeurs_numagrins_idx ON servicesinstructeurs (numagrins);

/* Index table users */

CREATE INDEX users_group_id_idx ON users (group_id);
CREATE INDEX users_serviceinstructeur_id_idx ON users (serviceinstructeur_id);
CREATE INDEX users_username_idx ON users (username);
CREATE INDEX users_nom_idx ON users (nom);
CREATE INDEX users_prenom_idx ON users (prenom);
CREATE INDEX users_date_deb_hab_idx ON users (date_deb_hab);
CREATE INDEX users_date_fin_hab_idx ON users (date_fin_hab);

/* Index table users_contratsinsertion */

CREATE INDEX users_contratsinsertion_user_id_idx ON users_contratsinsertion (user_id);
CREATE INDEX users_contratsinsertion_contratinsertion_id_idx ON users_contratsinsertion (contratinsertion_id);

/* Index table users_zonesgeographiques */

CREATE INDEX users_zonesgeographiques_user_id_idx ON users_zonesgeographiques (user_id);
CREATE INDEX users_zonesgeographiques_zonegeographique_id_idx ON users_zonesgeographiques (zonegeographique_id);
