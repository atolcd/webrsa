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

-- /****************************************************************
-- Modification des créances

ALTER TABLE creances ADD COLUMN moismoucompta date;
ALTER TABLE creances ADD COLUMN orgcre character(3) NOT NULL DEFAULT 'FLU'::character varying;
ALTER TABLE creances ADD COLUMN haspiecejointe character varying(1) NOT NULL DEFAULT '0'::character varying;
ALTER TABLE creances ADD COLUMN hastitrecreancier integer NOT NULL DEFAULT 0;

 UPDATE creances SET  orgcre='FLU' WHERE orgcre NOT LIKE 'MAN' AND orgcre NOT LIKE 'FLU' AND orgcre IS NULL;

-- /****************************************************************
-- Creation de la table des titres emmis

CREATE TABLE titrescreanciers (
  id serial NOT NULL,
  creance_id integer NOT NULL,
  dtemissiontitre date,
  dtvalidation date,
  etat integer NOT NULL,
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

-- Etats des titres créanciers
DROP TABLE IF EXISTS etatstitrescreanciers CASCADE;
CREATE TABLE etatstitrescreanciers(
  id			SERIAL NOT NULL PRIMARY KEY,
  name			VARCHAR(250) NOT NULL,
  actif			SMALLINT DEFAULT 1,
  created		TIMESTAMP WITHOUT TIME ZONE,
  modified		TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE etatstitrescreanciers IS 'Liste des etats des titres creanciers';

DROP INDEX IF EXISTS etatstitrescreanciers_name_idx;
CREATE UNIQUE INDEX etatstitrescreanciers_name_idx ON etatstitrescreanciers( name );

-- Insertion de 'CRE', 'VAL', 'SEN', 'PAY','SUP'
INSERT INTO etatstitrescreanciers
	( name, actif, created, modified )
VALUES
	('Supprimer', 1, NOW(), NOW() ),
	('Créer', 1, NOW(), NOW() ),
	('Vérifier', 1, NOW(), NOW() ),
	('Valider', 1, NOW(), NOW() ),
	('Imprimer', 1, NOW(), NOW() )
;

-- table de lien 
DROP TABLE IF EXISTS etatstitrescreanciers_titrescreanciers CASCADE;
CREATE TABLE etatstitrescreanciers_titrescreanciers (
  id                 			SERIAL NOT NULL PRIMARY KEY,
  etattitrecreancier_id			INTEGER NOT NULL REFERENCES etatstitrescreanciers(id) ON DELETE CASCADE ON UPDATE CASCADE,  
  titrecreancier_id				INTEGER NOT NULL REFERENCES titrescreanciers(id) ON DELETE CASCADE ON UPDATE CASCADE,
  created						TIMESTAMP WITHOUT TIME ZONE,
  modified						TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE etatstitrescreanciers_titrescreanciers IS 'Table de liaison entre les etats des titres creanciers et les titres creanciers';
DROP INDEX IF EXISTS etatstitrescreanciers_titrescreanciers_etattitrecreancier_id_idx;
CREATE INDEX etatstitrescreanciers_titrescreanciers_etattitrecreancier_id_idx ON etatstitrescreanciers_titrescreanciers(etattitrecreancier_id);

DROP INDEX IF EXISTS etatstitrescreanciers_titrescreanciers_titrecreancier_id_idx;
CREATE INDEX etatstitrescreanciers_titrescreanciers_titrecreancier_id_idx ON etatstitrescreanciers_titrescreanciers(titrecreancier_id);

-- Type des titres creanciers
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
COMMIT;
-- *****************************************************************************
