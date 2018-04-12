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
-- Annulation commission EP

ALTER TABLE dossierspcgs66 DROP CONSTRAINT dossierspcgs66_etatdossierpcg_in_list_chk;
ALTER TABLE dossierspcgs66 ADD CONSTRAINT dossierspcgs66_etatdossierpcg_in_list_chk CHECK
(
	cakephp_validate_in_list
	(
		etatdossierpcg::text, 
		ARRAY[
			'attaffect'::text, 
			'attinstr'::text, 
			'instrencours'::text, 
			'attavistech'::text, 
			'attval'::text, 
			'decisionvalid'::text, 
			'decisionnonvalid'::text, 
			'decisionnonvalidretouravis'::text, 
			'decisionvalidretouravis'::text, 
			'transmisop'::text, 
			'atttransmisop'::text, 
			'annule'::text, 
			'attinstrattpiece'::text, 
			'attinstrdocarrive'::text, 
			'arevoir'::text,
			'annulationep'::text
		]
	)
);


-- *****************************************************************************
-- Page d'accueil

CREATE TABLE accueilsarticles
(
  id serial NOT NULL,
  title character varying(255),
  content text,
  created timestamp without time zone,
  modified timestamp without time zone,
  actif smallint NOT NULL DEFAULT 0,
  publicationto timestamp without time zone,
  publicationfrom timestamp without time zone,
  CONSTRAINT accueilsarticles_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE valeurstags
  OWNER TO webrsa;


-- *****************************************************************************
COMMIT;
-- *****************************************************************************
