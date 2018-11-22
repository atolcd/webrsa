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

-- Ajout condition Eligible FSE dans les actions (CD66 uniquement)

ALTER TABLE actionscandidats ADD COLUMN eligiblefse character varying(1);
ALTER TABLE actionscandidats ALTER COLUMN eligiblefse SET DEFAULT 'O'::character varying;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************