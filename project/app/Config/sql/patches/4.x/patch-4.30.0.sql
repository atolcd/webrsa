SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.30.0', CURRENT_TIMESTAMP);

-- Variable de configuration de l'export csv des données du tableau 2'
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT
'ConfigurableQuery.Tableauxbords93.exportcsv_tableau2_donnees',
'{"filters":{"defaults":{},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":false,"auto":false,"results":{"header":[],"fields":["Personne.qual","Personne.nom","Personne.prenom","Personne.nir","Personne.dtnai","Personne.sexe","Personne.age","Adresse.numvoie","Adresse.libtypevoie","Adresse.nomvoie","Adresse.compladr","Adresse.codepos","Adresse.nomcom","Dossier.dtdemrsa","Dossier.numdemrsa","Dossier.matricule","Prestation.rolepers","Orientstruct.date_valid","Orientstruct.date_impression","Structurereferente.ville","Structurereferente.lib_struc","Foyer.sitfam","Orientstruct.origine","Detaildroitrsa.nbenfautcha","Personne.numfixe","Personne.numport","Personne.email","Typeorient.lib_type_orient"],"innerTable":["Situationdossierrsa.etatdosrsa","Personne.nomcomnai","Personne.dtnai","Adresse.numcom","Personne.nir","Historiqueetatpe.identifiantpe","Modecontact.numtel","Prestation.rolepers","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}',
'',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Tableauxbords93.exportcsv_tableau2_donnees');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable IN ('ConfigurableQuery.Tableauxbords93.exportcsv_tableau2_donnees');

--table pour stocker les données du tableau de bord 2 à historiser
create table if not exists tdb2_histo_corpus (
	id serial4 NOT NULL,
	trimestre integer not null,
	annee integer not null,
	structure_referente integer not null,
	personne_id integer not null,
	nir varchar null,
	caf varchar null,
	identifiant_pe varchar null,
	numrsa varchar null,
	civilite varchar null,
	nom varchar null,
	nom_naissance varchar null,
	prenom varchar null,
	date_naissance varchar null,
    age integer null,
	numvoie varchar null,
	libtypevoie varchar null,
	nomvoie varchar null,
	codepos varchar null,
	numcom varchar null,
	nomcom varchar null,
	tel1 varchar null,
	tel2 varchar null,
	email varchar null,
	datedemrsa varchar null,
	etatdroit varchar null,
	sdd varchar null,
	statutpe varchar null,
	nivetu varchar null,
	sexe varchar null,
	nom_ref varchar null,
	id_ref integer null,
	refe_appartient_struct varchar null,
	referent_actif varchar null,
	tag_diag bool null,
	date_creation_tag varchar null,
	nveau_orient bool null,
	toujours_orient bool null,
	do_tjs_orient varchar null,
	sdd_tjs_orient varchar null,
	dohd_id varchar null,
	dohd_origine varchar null,
	dohd_date_valid varchar null,
	dohd_rgorient varchar null,
	dohd_type varchar null,
	dohd_structurereferente varchar null,
	dod_id varchar null,
	dod_origine varchar null,
	dod_date_valid varchar null,
	dod_rgorient varchar null,
	dod_type varchar null,
	dod_structurereferente varchar null,
	dod_structurereferente_id integer null,
	dod_structureorientante varchar null,
	nb_rdv_indiv integer null,
	nb_rdv_coll varchar null,
	dsp_vide bool null,
	date_drip date null,
	date_drih date null,
	date_drcp date null,
	date_drch date null,
	rdv_prevu_passe bool null,
	d1_existant bool null,
	d1_rempli bool null,
	cer_struct_valide bool null,
	dcerv_structurereferente_id varchar null,
	dcerv_id varchar null,
	dcerv_structurereferente_lib varchar null,
	dcerv_referent varchar null,
	dcerv_created varchar null,
	dcerv_date_valid varchar null,
	dcerv_rang varchar null,
	dcerv_dd varchar null,
	dcerv_df varchar null,
	dcerv_duree varchar null,
	dcerv_position varchar null,
	dcerv_emploi varchar null,
	dcerv_formation varchar null,
	dcerv_autonomie_soc varchar null,
	dcerv_logement varchar null,
	dcerv_sante varchar null,
	dcerv_autre varchar null,
	pas_cer_signe bool null,
	cer_valide_a_date bool null,
	rdv_prevu_toutes_structures bool null,
	pas_rdv_30j bool null,
	pas_rdv_60j bool null,
	rdv_sans_dsp varchar null,
	rdv_coll_sans_indiv varchar null,
	rdv_sans_d1 varchar null,
	rdv_sans_cer varchar null,
	pas_cer_pas_rdv varchar null,
CONSTRAINT tdb93_histo_corpus_pkey PRIMARY KEY (id)
);
-- *****************************************************************************
COMMIT;
-- *****************************************************************************
