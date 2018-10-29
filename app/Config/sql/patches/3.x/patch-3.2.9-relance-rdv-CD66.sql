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