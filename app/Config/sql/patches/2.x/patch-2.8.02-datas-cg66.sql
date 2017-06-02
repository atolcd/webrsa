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

--------------------------------------------------------------------------------
-- 20150420: Supression des jetons périmés
--------------------------------------------------------------------------------
DELETE FROM jetons WHERE modified < (now() - '4 hours'::interval);

--------------------------------------------------------------------------------
-- 20150420: Supression des orientations vides
--------------------------------------------------------------------------------
DELETE FROM orientsstructs WHERE statut_orient <> 'Orienté';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
