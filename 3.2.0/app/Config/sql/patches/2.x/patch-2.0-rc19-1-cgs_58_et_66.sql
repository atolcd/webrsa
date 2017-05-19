-- *****************************************************************************
-- Patch à passer avant le patch-2.0-rc19.sql pour les CG ne faisant pas de
-- préorientation
-- *****************************************************************************

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

DELETE
	FROM orientsstructs
	WHERE
		statut_orient IN ( 'Non orienté', 'En attente' )
		AND date_valid IS NULL;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
