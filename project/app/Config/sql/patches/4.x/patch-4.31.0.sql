SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.31.0', CURRENT_TIMESTAMP);

--variable de configuration du nombre de mois pour l'écrasement/ la sauvegarde des données du tableau 1
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT
'Tableauxbords93.Tableau1.Shell.nombres_mois',
'3',
'Nombre de mois à supprimer et recalculer dans le shell du tableau 1 des Tableaux de bords',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Tableauxbords93.Tableau1.Shell.nombres_mois');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable IN ('Tableauxbords93.Tableau1.Shell.nombres_mois');


--table pour stocker les données du tableau de bord 1A
create table if not exists tdb1_a_corpus (
	id serial4 not null,
	annee integer not null,
	mois integer not null,
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
	tagdiag bool null,
	date_creation_tag varchar null,
	nveau_orient bool null,
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
	cer_struct_valide bool null,
	origine_assiette varchar null,
	structuref_assiette integer null,
	structorient_assiette integer null,
	referent_assiette integer null,
	created timestamp not null,
	modified timestamp not null
)

--table pour stocker les données du tableau de bord 1B
create table if not exists tdb1_b_corpus (
	id serial4 not null,
	annee integer not null,
	mois integer not null,
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
	tagdiag bool null,
	date_creation_tag varchar null,
	orient_id varchar null,
	orient_origine varchar null,
	orient_date varchar null,
	orient_rang varchar null,
	orient_type varchar null,
	orient_structuref varchar null,
	structorient_assiette varchar null,
	referent_assiette varchar null,
	rdv_id varchar null,
	rdv_structurereferente varchar null,
	rdv_referent varchar null,
	rdv_date varchar null,
	rdv_heure varchar null,
	rdv_type varchar null,
	rdv_statut varchar null,
	rdv_objectif varchar null,
	rdv_commentaire varchar null,
	thematique_cer boolean null,
	thematique_encoursdeparcours boolean null,
	thematique_premierrdv boolean null,
	thematique_autre boolean null,
	thematique_collectif varchar null,
	created timestamp not null,
	modified timestamp not null
)

--table pour stocker les données du tableau de bord 1C
create table if not exists tdb1_c_corpus (
	id serial4 not null,
	annee integer not null,
	mois integer not null,
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
	tagdiag bool null,
	date_creation_tag varchar null,
	orient_id varchar null,
	orient_origine varchar null,
	orient_date varchar null,
	orient_rang varchar null,
	orient_type varchar null,
	orient_structuref varchar null,
	structorient_assiette varchar null,
	referent_assiette varchar null,
	cer_id varchar null,
	cer_structure varchar null,
	cer_referent varchar null,
	cer_created varchar null,
	cer_datevalidcd date null,
	cer_rang varchar null,
	cer_dd date null,
	cer_df date null,
	cer_duree varchar null,
	cer_statut varchar null,
	sujet_emploi bool null,
	sujet_formation bool null,
	sujet_logement bool null,
	sujet_sante bool null,
	sujet_autonomie bool null,
	sujet_autre bool null,
	created timestamp not null,
	modified timestamp not null
)

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
