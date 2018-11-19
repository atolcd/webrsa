SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- *****************************************************************************
BEGIN;
-- *****************************************************************************



ALTER TABLE decisionsdefautsinsertionseps66 ADD COLUMN commentairebeneficiaire text;
ALTER TABLE decisionssaisinesbilansparcourseps66 ADD COLUMN commentairebeneficiaire text;


-- /****************************************************************
-- Modification des créances

ALTER TABLE creances ADD COLUMN moismoucompta date;
ALTER TABLE creances ADD COLUMN orgcre character(3) NOT NULL DEFAULT 'FLU'::character varying;
ALTER TABLE creances ADD COLUMN mention character varying(255);
ALTER TABLE creances ADD COLUMN haspiecejointe character varying(1) NOT NULL DEFAULT '0'::character

UPDATE creances SET  orgcre='FLU' WHERE orgcre NOT LIKE 'MAN' AND orgcre NOT LIKE 'FLU' AND orgcre IS NULL;

-- /****************************************************************
-- Creation de la table des titres emmis

DROP TABLE IF EXISTS titrescreanciers CASCADE;
CREATE TABLE titrescreanciers (
  id serial NOT NULL,
  creance_id integer NOT NULL,
  dtemissiontitre date,
  dtvalidation date,
  etat character varying(4) NOT NULL,
  numtitr character varying(30) NOT NULL,
  mnttitr numeric(9,2) NOT NULL,
  type integer NOT NULL,
  mention character varying(255),
  qual character varying(3),
  nom character varying(50),
  nir character varying(15),
  iban character varying(32),
  bic character varying(12),
  titulairecompte character varying(80),
  numtel character varying(14),
  haspiecejointe character varying(1) DEFAULT '0'::character varying
);

DROP INDEX IF EXISTS titrescreanciers_id_idx;
CREATE UNIQUE INDEX titrescreanciers_id_idx ON titrescreanciers( id );

-- /****************************************************************
-- Creation de la table  titres creanciers

DROP TABLE IF EXISTS typestitrescreanciers CASCADE;
CREATE TABLE typestitrescreanciers(
  id			SERIAL NOT NULL PRIMARY KEY,
  name			VARCHAR(250) NOT NULL,
  actif			SMALLINT DEFAULT 1,
  created		TIMESTAMP WITHOUT TIME ZONE,
  modified		TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE typestitrescreanciers IS 'Liste des types des titres creanciers';

DROP INDEX IF EXISTS typestitrescreanciers_name_idx;
CREATE UNIQUE INDEX typestitrescreanciers_name_idx ON typestitrescreanciers( name );

-- Insertion des valeur de départ
INSERT INTO typestitrescreanciers
	(name,actif,created,modified)
VALUES
	('Creance Couple - Emis au nom de MR et MME', 1, NOW(), NOW() ),
	('Creance séparée - Emis au nom de MME', 1, NOW(), NOW() ),
	('Creance séparée - Emis au nom de MR', 1, NOW(), NOW() ),
	('Creance complet - Emis au nom de MME', 1, NOW(), NOW() ),
	('Creance complet - Emis au nom de MR', 1, NOW(), NOW() )
;

-- table de lien 
DROP TABLE IF EXISTS typestitrescreanciers_titrescreanciers CASCADE;
CREATE TABLE typestitrescreanciers_titrescreanciers (
  id                 			SERIAL NOT NULL PRIMARY KEY,
  typetitrecreancier_id			INTEGER NOT NULL REFERENCES typestitrescreanciers(id) ON DELETE CASCADE ON UPDATE CASCADE,  
  titrecreancier_id				INTEGER NOT NULL REFERENCES titrescreanciers(id) ON DELETE CASCADE ON UPDATE CASCADE,
  created						TIMESTAMP WITHOUT TIME ZONE,
  modified						TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE typestitrescreanciers_titrescreanciers IS 'Table de liaison entre les types des titres creanciers et les titres creanciers';
DROP INDEX IF EXISTS typestitrescreanciers_titrescreanciers_typetitrecreancier_id_idx;
CREATE INDEX typestitrescreanciers_titrescreanciers_typetitrecreancier_id_idx ON typestitrescreanciers_titrescreanciers(typetitrecreancier_id);

DROP INDEX IF EXISTS typestitrescreanciers_titrescreanciers_titrecreancier_id_idx;
CREATE INDEX typestitrescreanciers_titrescreanciers_titrecreancier_id_idx ON typestitrescreanciers_titrescreanciers(titrecreancier_id);

-- *********************************************************************************
-- Creation de la table des rapport crée par les talends d'intégration des flux CNAF


DROP TABLE IF EXISTS administration.rapportstalendscreances CASCADE;
CREATE TABLE administration.rapportstalendscreances (
  id serial NOT NULL,
  flux character varying(15),
  typeflux character varying(1),
  natflux character varying(1),
  dtflux date,
  dtref date,
  dtexec date,
  fichierflux character varying(80),
  nbtotdosrsatransm numeric(8,0),
  nbtotdosrsatransmano numeric(8,0),
  nbrejete numeric(6,0),
  fichierrejet character varying(40),
  nbinser numeric(6,0),
  nbmaj numeric(6,0),
  message character varying(1000)
);

-- Creation de la table des personnes rejetées par les talends d'intégration des flux CNAF
DROP TABLE IF EXISTS administration.rejetstalendscreances CASCADE;
CREATE TABLE administration.rejetstalendscreances (
	id 					serial NOT NULL,
	fusion 				BOOLEAN DEFAULT FALSE,
	flux 				character varying(15),
	typeflux 			character varying(1),
	natflux 			character varying(1),
	dtflux 				date,
	dtref 				date,
	dtexec 				date,
	fichierflux 		character varying(80),
	matricule			VARCHAR(15) DEFAULT NULL,
	numdemrsa		  	VARCHAR(11) DEFAULT NULL,
	dtdemrsa			DATE NOT NULL,
	ddratdos	   		DATE,
	dfratdos			DATE,
	toprespdos	  		BOOLEAN,
	nir					CHAR(15),
	qual				VARCHAR(3) DEFAULT NULL,
	nom					VARCHAR(50) NOT NULL,
	nomnai				VARCHAR(50) DEFAULT NULL,
	prenom				VARCHAR(50) NOT NULL,
	prenom2				VARCHAR(50) NOT NULL,
	prenom3				VARCHAR(50) NOT NULL,
	dtnai				DATE NOT NULL,
	nomcomnai			VARCHAR(26) DEFAULT NULL,
	typedtnai			CHAR(1),
	typeparte			CHAR(4),
	ideparte			CHAR(3),
	topvalec			BOOLEAN,
	sexe				CHAR(1),
	rgadr	   			CHAR(2),
	dtemm	   			DATE,
	typeadr	 			CHAR(1),
	numvoie	 			VARCHAR(6),
	libtypevoie			VARCHAR(10),
	nomvoie	 			VARCHAR(25),
	complideadr 		VARCHAR(50),
	compladr			VARCHAR(50),
	lieudist			VARCHAR(32),
	numcom   			CHAR(5),
	codepos				CHAR(5),
	dtimplcre			DATE,
	natcre	  			CHAR(3),
	rgcre	   			CHAR(3),
	motiindu			CHAR(2),
	oriindu	 			CHAR(2),
	respindu			CHAR(2),
	ddregucre	 		DATE,
	dfregucre	 		DATE,
	dtdercredcretrans   DATE,
	mtsolreelcretrans   NUMERIC(9,2),
	mtinicre			NUMERIC(9,2),
	moismoucompta 		DATE,
	liblig2adr			VARCHAR(38) DEFAULT NULL,
	liblig3adr			VARCHAR(38) DEFAULT NULL,
	liblig4adr 			VARCHAR(38) DEFAULT NULL,
	liblig5adr 			VARCHAR(38) DEFAULT NULL,
	liblig6adr 			VARCHAR(38) DEFAULT NULL,
	liblig7adr 			VARCHAR(38) DEFAULT NULL
);

-- *****************************************************************************
-- Relances

ALTER TABLE "structuresreferentes" ADD COLUMN "lib_struc_mini" CHARACTER varying(100);

DROP TABLE IF EXISTS relances CASCADE;
CREATE TABLE relances
(
  id serial NOT NULL,
  relancesupport character varying(20) NOT NULL,
  relancetype character varying(20) NOT NULL,
  relancemode character varying(30) NOT NULL,
  nombredejour integer,
  contenu text,
  actif smallint NOT NULL DEFAULT 0,
  created timestamp without time zone,
  modified timestamp without time zone,
  CONSTRAINT relances_pkey PRIMARY KEY (id),
  CONSTRAINT relances_relancesupport_in_list_chk CHECK (cakephp_validate_in_list(relancesupport::text, ARRAY['SMS'::text, 'EMAIL'::text])),
  CONSTRAINT relances_relancetype_in_list_chk CHECK (cakephp_validate_in_list(relancetype::text, ARRAY['RDV'::text, 'EP'::text])),
  CONSTRAINT relances_relancemode_in_list_chk CHECK (cakephp_validate_in_list(relancemode::text, ARRAY['ORANGE_CONTACT_EVERYONE'::text, 'EMAIL'::text]))
)
WITH (
  OIDS=FALSE
);

DROP TABLE IF EXISTS relanceslogs CASCADE;
CREATE TABLE relanceslogs
(
  id serial NOT NULL,
  personne_id integer,
  nom_complet character varying(255),
  numport character varying(255),
  email character varying(255),
  daterdv date,
  heurerdv time without time zone,
  lieurdv character varying(255),
  relancetype character varying(20),
  nombredejour integer,
  contenu text,
  statut character varying(20),
  support character varying(50),
  mode character varying(50),
  created timestamp without time zone,
  modified timestamp without time zone,
  CONSTRAINT relanceslogs_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

-- *****************************************************************************
-- Flux Pôle Emploi


-- informationspe

ALTER TABLE informationspe ADD COLUMN individu_nom_marital character varying(150);
ALTER TABLE informationspe ADD COLUMN individu_certification_identite character varying(1);
ALTER TABLE informationspe ADD COLUMN individu_commune_residence integer;
ALTER TABLE informationspe ADD COLUMN allocataire_identifiant_caf character varying(15);
ALTER TABLE informationspe ADD COLUMN allocataire_identifiant_msa character varying(15);
ALTER TABLE informationspe ADD COLUMN allocataire_code_pe character varying(10);
ALTER TABLE informationspe ADD COLUMN allocataire_identifiant_pe character varying(8);
ALTER TABLE informationspe ADD COLUMN inscription_date_debut_ide date;
ALTER TABLE informationspe ADD COLUMN inscription_code_categorie character varying(10);
ALTER TABLE informationspe ADD COLUMN inscription_lib_categorie character varying(150);
ALTER TABLE informationspe ADD COLUMN inscription_code_situation character varying(10);
ALTER TABLE informationspe ADD COLUMN inscription_lib_situation character varying(150);
ALTER TABLE informationspe ADD COLUMN inscription_date_cessation_ide date;
ALTER TABLE informationspe ADD COLUMN inscription_motif_cessation_ide character varying(150);
ALTER TABLE informationspe ADD COLUMN inscription_lib_cessation_ide character varying(150);
ALTER TABLE informationspe ADD COLUMN inscription_date_radiation_ide date;
ALTER TABLE informationspe ADD COLUMN inscription_motif_radiation_ide character varying(150);
ALTER TABLE informationspe ADD COLUMN inscription_lib_radiation_ide character varying(150);
ALTER TABLE informationspe ADD COLUMN suivi_structure_principale_nom character varying(150);
ALTER TABLE informationspe ADD COLUMN suivi_structure_principale_voie character varying(150);
ALTER TABLE informationspe ADD COLUMN suivi_structure_principale_complement character varying(150);
ALTER TABLE informationspe ADD COLUMN suivi_structure_principale_code_postal character varying(5);
ALTER TABLE informationspe ADD COLUMN suivi_structure_principale_cedex character varying(2);
ALTER TABLE informationspe ADD COLUMN suivi_structure_principale_bureau character varying(150);
ALTER TABLE informationspe ADD COLUMN suivi_structure_deleguee_nom character varying(150);
ALTER TABLE informationspe ADD COLUMN suivi_structure_deleguee_voie character varying(150);
ALTER TABLE informationspe ADD COLUMN suivi_structure_deleguee_complement character varying(150);
ALTER TABLE informationspe ADD COLUMN suivi_structure_deleguee_code_postal character varying(5);
ALTER TABLE informationspe ADD COLUMN suivi_structure_deleguee_cedex character varying(2);
ALTER TABLE informationspe ADD COLUMN suivi_structure_deleguee_bureau character varying(150);
ALTER TABLE informationspe ADD COLUMN formation_code_niveau character varying(10);
ALTER TABLE informationspe ADD COLUMN formation_lib_niveau character varying(150);
ALTER TABLE informationspe ADD COLUMN formation_code_secteur character varying(10);
ALTER TABLE informationspe ADD COLUMN formation_lib_secteur character varying(150);
ALTER TABLE informationspe ADD COLUMN romev3_code_rome character varying(10);
ALTER TABLE informationspe ADD COLUMN romev3_lib_rome character varying(150);
ALTER TABLE informationspe ADD COLUMN ppae_conseiller_pe character varying(150);
ALTER TABLE informationspe ADD COLUMN ppae_date_signature date;
ALTER TABLE informationspe ADD COLUMN ppae_date_notification date;
ALTER TABLE informationspe ADD COLUMN ppae_axe_code character varying(10);
ALTER TABLE informationspe ADD COLUMN ppae_axe_libelle character varying(150);
ALTER TABLE informationspe ADD COLUMN ppae_modalite_code character varying(10);
ALTER TABLE informationspe ADD COLUMN ppae_modalite_libelle character varying(150);
ALTER TABLE informationspe ADD COLUMN ppae_date_dernier_ent date;

ALTER TABLE informationspe ADD COLUMN date_creation timestamp without time zone DEFAULT NULL;
ALTER TABLE informationspe ADD COLUMN date_modification timestamp without time zone DEFAULT NULL;


-- informationsperejets

CREATE TABLE informationsperejets
(
  id serial NOT NULL,
  nir character varying(15) DEFAULT NULL::character varying,
  nom character varying(50) DEFAULT NULL::character varying,
  prenom character varying(50) NOT NULL,
  dtnai date NOT NULL,
  individu_nom_marital character varying(150),
  individu_certification_identite character varying(1),
  individu_commune_residence character varying(15),
  allocataire_identifiant_caf character varying(15),
  allocataire_identifiant_msa character varying(15),
  allocataire_code_pe character varying(10),
  allocataire_identifiant_pe character varying(8),
  inscription_date_debut_ide date,
  inscription_code_categorie character varying(10),
  inscription_lib_categorie character varying(150),
  inscription_code_situation character varying(10),
  inscription_lib_situation character varying(150),
  inscription_date_cessation_ide date,
  inscription_motif_cessation_ide character varying(150),
  inscription_lib_cessation_ide character varying(150),
  inscription_date_radiation_ide date,
  inscription_motif_radiation_ide character varying(150),
  inscription_lib_radiation_ide character varying(150),
  suivi_structure_principale_nom character varying(150),
  suivi_structure_principale_voie character varying(150),
  suivi_structure_principale_complement character varying(150),
  suivi_structure_principale_code_postal character varying(5),
  suivi_structure_principale_cedex character varying(2),
  suivi_structure_principale_bureau character varying(150),
  suivi_structure_deleguee_nom character varying(150),
  suivi_structure_deleguee_voie character varying(150),
  suivi_structure_deleguee_complement character varying(150),
  suivi_structure_deleguee_code_postal character varying(5),
  suivi_structure_deleguee_cedex character varying(2),
  suivi_structure_deleguee_bureau character varying(150),
  formation_code_niveau character varying(10),
  formation_lib_niveau character varying(150),
  formation_code_secteur character varying(10),
  formation_lib_secteur character varying(150),
  romev3_code_rome character varying(10),
  romev3_lib_rome character varying(150),
  ppae_conseiller_pe character varying(150),
  ppae_date_signature date,
  ppae_date_notification date,
  ppae_axe_code character varying(10),
  ppae_axe_libelle character varying(150),
  ppae_modalite_code character varying(10),
  ppae_modalite_libelle character varying(150),
  ppae_date_dernier_ent date,
  errorcode character varying(255),
  errormessage character varying(255),
  CONSTRAINT informationsperejets_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

ALTER TABLE informationsperejets ADD COLUMN date_creation timestamp without time zone DEFAULT NULL;
ALTER TABLE informationsperejets ADD COLUMN date_modification timestamp without time zone DEFAULT NULL;


-- historiqueetatspe

ALTER TABLE historiqueetatspe ADD COLUMN inscription_date_debut_ide date;
ALTER TABLE historiqueetatspe ADD COLUMN inscription_code_categorie character varying(10);
ALTER TABLE historiqueetatspe ADD COLUMN inscription_lib_categorie character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN inscription_code_situation character varying(10);
ALTER TABLE historiqueetatspe ADD COLUMN inscription_lib_situation character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN inscription_date_cessation_ide date;
ALTER TABLE historiqueetatspe ADD COLUMN inscription_motif_cessation_ide character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN inscription_lib_cessation_ide character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN inscription_date_radiation_ide date;
ALTER TABLE historiqueetatspe ADD COLUMN inscription_motif_radiation_ide character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN inscription_lib_radiation_ide character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN suivi_structure_principale_nom character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN suivi_structure_principale_voie character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN suivi_structure_principale_complement character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN suivi_structure_principale_code_postal character varying(5);
ALTER TABLE historiqueetatspe ADD COLUMN suivi_structure_principale_cedex character varying(2);
ALTER TABLE historiqueetatspe ADD COLUMN suivi_structure_principale_bureau character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN suivi_structure_deleguee_nom character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN suivi_structure_deleguee_voie character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN suivi_structure_deleguee_complement character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN suivi_structure_deleguee_code_postal character varying(5);
ALTER TABLE historiqueetatspe ADD COLUMN suivi_structure_deleguee_cedex character varying(2);
ALTER TABLE historiqueetatspe ADD COLUMN suivi_structure_deleguee_bureau character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN formation_code_niveau character varying(10);
ALTER TABLE historiqueetatspe ADD COLUMN formation_lib_niveau character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN formation_code_secteur character varying(10);
ALTER TABLE historiqueetatspe ADD COLUMN formation_lib_secteur character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN romev3_code_rome character varying(10);
ALTER TABLE historiqueetatspe ADD COLUMN romev3_lib_rome character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN ppae_conseiller_pe character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN ppae_date_signature date;
ALTER TABLE historiqueetatspe ADD COLUMN ppae_date_notification date;
ALTER TABLE historiqueetatspe ADD COLUMN ppae_axe_code character varying(10);
ALTER TABLE historiqueetatspe ADD COLUMN ppae_axe_libelle character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN ppae_modalite_code character varying(10);
ALTER TABLE historiqueetatspe ADD COLUMN ppae_modalite_libelle character varying(150);
ALTER TABLE historiqueetatspe ADD COLUMN ppae_date_dernier_ent date;

ALTER TABLE historiqueetatspe ADD COLUMN date_creation timestamp without time zone DEFAULT NULL;
ALTER TABLE historiqueetatspe ADD COLUMN date_modification timestamp without time zone DEFAULT NULL;


-- historiqueetatsperejets

CREATE TABLE historiqueetatsperejets
(
  id serial NOT NULL,
  informationpe_id integer,
  identifiantpe character varying(11),
  date date,
  etat character varying(11),
  code character varying(2) DEFAULT NULL::character varying,
  motif character varying(250) DEFAULT NULL::character varying,
  codeinsee character(5),
  localite character varying(250),
  adresse character varying(255),
  ale character(5),
  individu_nom_marital character varying(150),
  individu_certification_identite character varying(1),
  individu_commune_residence integer,
  allocataire_identifiant_caf character varying(15),
  allocataire_identifiant_msa character varying(15),
  allocataire_code_pe character varying(10),
  allocataire_identifiant_pe character varying(8),
  inscription_date_debut_ide date,
  inscription_code_categorie character varying(10),
  inscription_lib_categorie character varying(150),
  inscription_code_situation character varying(10),
  inscription_lib_situation character varying(150),
  inscription_date_cessation_ide date,
  inscription_motif_cessation_ide character varying(150),
  inscription_lib_cessation_ide character varying(150),
  inscription_date_radiation_ide date,
  inscription_motif_radiation_ide character varying(150),
  inscription_lib_radiation_ide character varying(150),
  suivi_structure_principale_nom character varying(150),
  suivi_structure_principale_voie character varying(150),
  suivi_structure_principale_complement character varying(150),
  suivi_structure_principale_code_postal character varying(5),
  suivi_structure_principale_cedex character varying(2),
  suivi_structure_principale_bureau character varying(150),
  suivi_structure_deleguee_nom character varying(150),
  suivi_structure_deleguee_voie character varying(150),
  suivi_structure_deleguee_complement character varying(150),
  suivi_structure_deleguee_code_postal character varying(5),
  suivi_structure_deleguee_cedex character varying(2),
  suivi_structure_deleguee_bureau character varying(150),
  formation_code_niveau character varying(10),
  formation_lib_niveau character varying(150),
  formation_code_secteur character varying(10),
  formation_lib_secteur character varying(150),
  romev3_code_rome character varying(10),
  romev3_lib_rome character varying(150),
  ppae_conseiller_pe character varying(150),
  ppae_date_signature date,
  ppae_date_notification date,
  ppae_axe_code character varying(10),
  ppae_axe_libelle character varying(150),
  ppae_modalite_code character varying(10),
  ppae_modalite_libelle character varying(150),
  ppae_date_dernier_ent date,
  date_creation timestamp without time zone,
  date_modification timestamp without time zone,
  CONSTRAINT historiqueetatspe_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

ALTER TABLE historiqueetatsperejets ADD COLUMN errorcode character varying(255);
ALTER TABLE historiqueetatsperejets ADD COLUMN errormessage character varying(255);	




-- *****************************************************************************
-- Structures référentes

ALTER TABLE structuresreferentes ADD COLUMN actif_cohorte character varying(1) NOT NULL DEFAULT 'O'::character varying;
ALTER TABLE structuresreferentes ADD CONSTRAINT structuresreferentes_actif_cohorte_in_list_chk CHECK (cakephp_validate_in_list(actif_cohorte::text, ARRAY['O'::text, 'N'::text]));

-- *****************************************************************************
-- Index des tables

CREATE INDEX adresses_cantons_adresse_id_idx ON adresses_cantons USING btree (adresse_id);
CREATE INDEX adresses_cantons_canton_id_idx ON adresses_cantons USING btree (canton_id);

CREATE INDEX savesearchs_user_id_idx ON savesearchs USING btree (user_id);
CREATE INDEX savesearchs_group_id_idxx ON savesearchs USING btree (group_id);
CREATE INDEX savesearchs_action_idx ON savesearchs USING btree (action);

CREATE INDEX typescontratscuis66_actif_idx ON typescontratscuis66 USING btree (actif);

CREATE INDEX codesromesecteursdsps66_code_idx ON codesromesecteursdsps66 USING btree (code);

CREATE INDEX jetons_user_id_php_sid_idx ON jetons USING btree (user_id, php_sid);
CREATE INDEX jetons_modified_idx ON jetons USING btree (modified);
CREATE INDEX jetons_php_sid_idx ON jetons USING btree (php_sid);

CREATE INDEX actionscandidats_personnes_positionfiche_idx ON actionscandidats_personnes USING btree (positionfiche);

CREATE INDEX entites_tags_modele_idx ON entites_tags USING btree (modele);
CREATE INDEX entites_tags_tag_idx ON entites_tags USING btree (tag_id);

CREATE INDEX dossierspcgs66_etatdossierpcg_idx ON dossierspcgs66 USING btree (etatdossierpcg);

CREATE INDEX acos_rght_idx ON acos USING btree (rght);
CREATE INDEX acos_lft_idx ON acos USING btree (lft);

CREATE INDEX structuresreferentes_actif_idx ON structuresreferentes USING btree (actif);

CREATE INDEX nonorientes66_origine_idx ON nonorientes66 USING btree (origine);
CREATE INDEX nonorientes66_datenotification_idx ON nonorientes66 USING btree (datenotification);

CREATE INDEX valeurstags_actif_idx ON valeurstags USING btree (actif);

CREATE INDEX infosfinancieres_type_allocation_idx ON infosfinancieres USING btree (type_allocation);

CREATE INDEX contratsinsertion_rg_ci_idx ON contratsinsertion USING btree (rg_ci);

CREATE INDEX vagues93_dateDebut_idx ON vagues93 USING btree (dateDebut);
CREATE INDEX vagues93_dateFin_idx ON vagues93 USING btree (dateFin);

CREATE INDEX dsps_revs_personne_id_modified_idx ON dsps_revs USING btree (personne_id, modified);

CREATE INDEX dossiers_dtdemrsa_id_idx ON dossiers USING btree (dtdemrsa, id);

-- *****************************************************************************
CREATE INDEX fichesprescriptions93_benef_retour_presente_idx ON fichesprescriptions93 USING btree (benef_retour_presente);
CREATE INDEX fichesprescriptions93_created_idx ON fichesprescriptions93 USING btree (created);


-- *****************************************************************************
-- Création de la table contenant les vagues des orientations du 93

DROP TABLE IF EXISTS vaguesdorientations CASCADE;
CREATE TABLE vaguesdorientations
(
  id serial NOT NULL,
  dateDebut date,
  dateFin date
);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
