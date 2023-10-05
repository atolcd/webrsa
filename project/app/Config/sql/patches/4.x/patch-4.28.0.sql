SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('ALI', CURRENT_TIMESTAMP);

--Table pour les sujets à transmettre
create table if not exists administration.sujetsreferentiels (
	id serial4 NOT NULL,
	code varchar(255) not null,
	libelle varchar(255) not null,
	nom_table varchar(255) null,
	modele_enum varchar(255) null,
	nom_enum varchar(255) null,
	nom_config varchar(255) null,
	correspondance_colonnes varchar null,
	actif bool not null default true,
	CONSTRAINT sujetsreferentiels_pkey PRIMARY KEY (id)
);

-- Table pour stocker les enum
create table if not exists administration.correspondancesreferentiels (
	id serial4 NOT NULL,
	sujetsreferentiels_id integer not null,
	id_dans_table integer null,
	code varchar(255) null,
	libelle varchar(255) null,
	actif bool not NULL DEFAULT true,
	typesorients_parent_id integer NULL,
	zonesgeographiques_code_insee varchar null,
	structuresreferentes_typeorient_id integer null,
	structuresreferentes_numvoie varchar null,
	structuresreferentes_typevoie varchar null,
	structuresreferentes_nomvoie varchar null,
	structuresreferentes_codepostal varchar null,
	structuresreferentes_ville varchar null,
	structuresreferentes_codeinsee varchar null,
	structuresreferentes_numtel varchar null,
	structuresreferentes_email varchar null,
	structuresreferentes_zonesgeo varchar null,
	referents_structurereferente_id integer null,
	referents_civilite varchar null,
	referents_nom varchar null,
	referents_prenom varchar null,
	referents_email varchar null,
	referents_fonction varchar null,
	referents_numtel varchar null,
	referents_date_cloture date null,
	rdv_thematique_typerdv_id integer null,
	rdv_thematique_statutrdv_id integer null,
	rdv_thematique_acomptabiliser bool null,
	rdv_thematique_unefoisparan bool null,
	code_domaine_codefamille_id integer null,
	code_metier_codedomaine_id integer null,
	appell_metier_codemetier_id integer null,
	nature_contrat_definir_duree bool null,
	cer_sujet_champ_texte bool null,
	cer_sous_sujet_sujet_id integer null,
	cer_sous_sujet_champ_texte bool null,
	cer_valeurs_sous_sujet_sujet_id integer null,
	cer_valeurs_sous_sujet_champ_texte bool null,
	cer_commentaire_champ_texte bool null,
	mot_sortie_oblign_acc_parent varchar null,
	mot_sortie_oblig_acc_typeemploi_code varchar null,
	type_emploi_code_type_emploi varchar null,
	created timestamp NOT NULL,
	modified timestamp NOT NULL,
	CONSTRAINT correspondancesreferentiels_pkey PRIMARY KEY (id),
	CONSTRAINT correspondancesreferentiels_sujetsreferentiels_fkey FOREIGN KEY (sujetsreferentiels_id) REFERENCES administration.sujetsreferentiels(id) ON DELETE CASCADE
);

--Insertion de la variable de configuration pour les origines d'orientation utilisables par les ALI
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT
'Orientstruct.origine.utilisable_ALI',
'{
"reorientation":"Réorientation",
"entdiag":"Diagnostic"
}',
'Liste des origines d''orientations utilisables par les ALI',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Orientstruct.origine.utilisable_ALI');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable IN ('Orientstruct.origine.utilisable_ALI');

--Ajout d'une colonne pour déterminer les structures avec lesquelles il existe un échange de données
alter table public.structuresreferentes ADD COLUMN IF NOT exists export_donnees bool not null default false;

--Ajout de colonnes pour stocker les id ALI
alter table public.orientsstructs ADD COLUMN IF NOT exists id_base_ali integer null;
alter table public.cers93 ADD COLUMN IF NOT exists id_base_ali integer null;
alter table public.dsps ADD COLUMN IF NOT exists id_base_ali integer null;
alter table public.dsps_revs ADD COLUMN IF NOT exists id_base_ali integer null;
alter table public.rendezvous ADD COLUMN IF NOT exists id_base_ali integer null;
alter table public.questionnairesd1pdvs93 ADD COLUMN IF NOT exists id_base_ali integer null;
alter table public.questionnairesd2pdvs93 ADD COLUMN IF NOT exists id_base_ali integer null;
alter table public.questionnairesb7pdvs93 ADD COLUMN IF NOT exists id_base_ali integer null;

--Créations de tables pour les rapports d'export / import
create table if not exists administration.rapportsechangesALI (
id serial4 NOT NULL,
nom_fichier varchar not null,
type varchar(25) not null,
debut timestamp NOT NULL,
created timestamp NOT NULL,
date_fichier timestamp NULL,
ali_id integer not null,
stock boolean not null,
CONSTRAINT rapports_echanges_ALI_pkey PRIMARY KEY (id),
CONSTRAINT rapports_echanges_ALI_ali_id_fkey FOREIGN KEY (ali_id) REFERENCES public.structuresreferentes(id) ON DELETE cascade
);


create table if not exists administration.erreursechangesALI (
id serial4 NOT NULL,
rapport_id integer NOT NULL,
personne_id integer NULL,
code varchar not null,
bloc varchar null,
commentaire varchar null,
CONSTRAINT erreurs_echanges_ALI_pkey PRIMARY KEY (id),
CONSTRAINT erreurs_echanges_ALI_rapport_id_fkey FOREIGN KEY (rapport_id) REFERENCES administration.rapportsechangesALI(id) ON DELETE cascade,
CONSTRAINT erreurs_echanges_ALI_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES public.personnes(id) ON DELETE cascade
);

create table if not exists administration.personnesechangesALI (
	id serial4 NOT NULL,
	rapport_id integer NOT NULL,
	personne_id integer not null,
	referentparcours boolean null,
	rendezvous boolean null,
	dsp boolean null,
	cer boolean null,
	orient boolean null,
	d1 boolean null,
	d2 boolean null,
	b7 boolean null,
	CONSTRAINT personnes_echanges_ALI_pkey PRIMARY KEY (id),
	CONSTRAINT personnes_echanges_ALI_rapport_id_fkey FOREIGN KEY (rapport_id) REFERENCES administration.rapportsechangesALI(id) ON DELETE cascade,
	CONSTRAINT personnes_echanges_ALI_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES public.personnes(id) ON DELETE cascade
);


--Création des trigger pour stocker les dates de modifs des tables qui n'en ont pas 
CREATE OR REPLACE FUNCTION update_created() RETURNS trigger AS $update_created$
    BEGIN
        NEW.created := current_timestamp;
        RETURN NEW;
    END;
$update_created$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION update_modified() RETURNS trigger AS $update_modified$
    BEGIN
        NEW.modified := current_timestamp;
        RETURN NEW;
    END;
$update_modified$ LANGUAGE plpgsql;


--Ajouter les colonnes dans les tables où il n'y en a pas
alter table public.personnes_referents ADD COLUMN IF NOT exists created timestamp null;
alter table public.personnes_referents ADD COLUMN IF NOT exists modified timestamp null;
alter table public.orientsstructs  ADD COLUMN IF NOT exists created timestamp null;
alter table public.orientsstructs  ADD COLUMN IF NOT exists modified timestamp null;
alter table public.cers93 ADD COLUMN IF NOT exists created timestamp null;
alter table public.cers93 ADD COLUMN IF NOT exists modified timestamp null;
alter table public.dsps ADD COLUMN IF NOT exists created timestamp null;
alter table public.adresses ADD COLUMN IF NOT exists created timestamp null;
alter table public.adresses ADD COLUMN IF NOT exists modified timestamp null;
alter table public.adressesfoyers ADD COLUMN IF NOT exists created timestamp null;
alter table public.adressesfoyers ADD COLUMN IF NOT exists modified timestamp null;
alter table public.foyers ADD COLUMN IF NOT exists created timestamp null;
alter table public.foyers ADD COLUMN IF NOT exists modified timestamp null;
alter table public.detailsdroitsrsa ADD COLUMN IF NOT exists created timestamp null;
alter table public.detailsdroitsrsa ADD COLUMN IF NOT exists modified timestamp null;
alter table public.personnes ADD COLUMN IF NOT exists created timestamp null;
alter table public.personnes ADD COLUMN IF NOT exists modified timestamp null;

alter table dsps_revs alter modified type timestamp;
alter table dsps_revs alter created type timestamp;



--Création des trigger pour l'ajout et la modif
DROP TRIGGER IF EXISTS update_modified ON public.personnes_referents;
CREATE TRIGGER update_modified BEFORE INSERT OR UPDATE ON public.personnes_referents
    FOR EACH ROW EXECUTE PROCEDURE update_modified();

DROP TRIGGER IF EXISTS update_created ON public.personnes_referents;
CREATE TRIGGER update_created BEFORE INSERT ON public.personnes_referents
  FOR EACH ROW EXECUTE PROCEDURE update_created();

 DROP TRIGGER IF EXISTS update_modified ON public.orientsstructs;
 CREATE TRIGGER update_modified BEFORE INSERT OR UPDATE ON public.orientsstructs
    FOR EACH ROW EXECUTE PROCEDURE update_modified();

DROP TRIGGER IF EXISTS update_created ON public.orientsstructs;
CREATE TRIGGER update_created BEFORE INSERT ON public.orientsstructs
  FOR EACH ROW EXECUTE PROCEDURE update_created();

DROP TRIGGER IF EXISTS update_modified ON public.cers93;
CREATE TRIGGER update_modified BEFORE INSERT OR UPDATE ON public.cers93
  FOR EACH ROW EXECUTE PROCEDURE update_modified();

DROP TRIGGER IF EXISTS update_created ON public.cers93;
CREATE TRIGGER update_created BEFORE INSERT ON public.cers93
  FOR EACH ROW EXECUTE PROCEDURE update_created();

DROP TRIGGER IF EXISTS update_modified ON public.contratsinsertion;
CREATE TRIGGER update_modified BEFORE INSERT OR UPDATE ON public.contratsinsertion
  FOR EACH ROW EXECUTE PROCEDURE update_modified();

DROP TRIGGER IF EXISTS update_created ON public.contratsinsertion;
CREATE TRIGGER update_created BEFORE INSERT ON public.contratsinsertion
  FOR EACH ROW EXECUTE PROCEDURE update_created();

DROP TRIGGER IF EXISTS update_created ON public.dsps;
CREATE TRIGGER update_created BEFORE INSERT ON public.dsps
  FOR EACH ROW EXECUTE PROCEDURE update_created();

 DROP TRIGGER IF EXISTS update_modified ON public.adresses;
 CREATE TRIGGER update_modified BEFORE INSERT OR UPDATE ON public.adresses
  FOR EACH ROW EXECUTE PROCEDURE update_modified();

DROP TRIGGER IF EXISTS update_created ON public.adresses;
CREATE TRIGGER update_created BEFORE INSERT ON public.adresses
  FOR EACH ROW EXECUTE PROCEDURE update_created();

 DROP TRIGGER IF EXISTS update_modified ON public.adressesfoyers;
 CREATE TRIGGER update_modified BEFORE INSERT OR UPDATE ON public.adressesfoyers
  FOR EACH ROW EXECUTE PROCEDURE update_modified();

 DROP TRIGGER IF EXISTS update_created ON public.adressesfoyers;
CREATE TRIGGER update_created BEFORE INSERT ON public.adressesfoyers
  FOR EACH ROW EXECUTE PROCEDURE update_created();

  DROP TRIGGER IF EXISTS update_modified ON public.foyers;
 CREATE TRIGGER update_modified BEFORE INSERT OR UPDATE ON public.foyers
  FOR EACH ROW EXECUTE PROCEDURE update_modified();

 DROP TRIGGER IF EXISTS update_created ON public.foyers;
CREATE TRIGGER update_created BEFORE INSERT ON public.foyers
  FOR EACH ROW EXECUTE PROCEDURE update_created();

 DROP TRIGGER IF EXISTS update_modified ON public.detailsdroitsrsa;
 CREATE TRIGGER update_modified BEFORE INSERT OR UPDATE ON public.detailsdroitsrsa
  FOR EACH ROW EXECUTE PROCEDURE update_modified();

 DROP TRIGGER IF EXISTS update_created ON public.detailsdroitsrsa;
CREATE TRIGGER update_created BEFORE INSERT ON public.detailsdroitsrsa
  FOR EACH ROW EXECUTE PROCEDURE update_created();

 DROP TRIGGER IF EXISTS update_modified ON public.personnes;
 CREATE TRIGGER update_modified BEFORE INSERT OR UPDATE ON public.personnes
  FOR EACH ROW EXECUTE PROCEDURE update_modified();

 DROP TRIGGER IF EXISTS update_created ON public.personnes;
CREATE TRIGGER update_created BEFORE INSERT ON public.personnes
  FOR EACH ROW EXECUTE PROCEDURE update_created();


--Variable de configuration pour le chemin vers les fichiers de validation
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT
'EchangeALI.CheminValidation',
'"app/XML_ALI/XSD"',
'Chemin du dossier dans lequel se trouvent les fichiers .xsd pour la validation des fichiers d''échange de données avec les ALI',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'EchangeALI.CheminValidation');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable IN ('EchangeALI.CheminValidation');

--Variable de configuration pour le chemin vers les fichiers de validation
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT
'EchangeALI.CheminRapports',
'"chemin/vers/Rapports"',
'Chemin du dossier dans lequel s''enregistrent les fichiers .csv de rapport d''erreurs',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'EchangeALI.CheminRapports');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable IN ('EchangeALI.CheminRapports');


--Ajout d'une colonne code dans la table des catégories d'utilisateur
alter table categoriesutilisateurs add column if not exists code varchar(255);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
