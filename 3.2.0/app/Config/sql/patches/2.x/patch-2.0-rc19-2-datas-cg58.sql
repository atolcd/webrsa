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

UPDATE regroupementseps
	SET
		nonorientationproep58 = 'decisionep',
		regressionorientationep58 = 'decisionep',
		sanctionep58 = 'decisionep',
		sanctionrendezvousep58 = 'decisionep';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
