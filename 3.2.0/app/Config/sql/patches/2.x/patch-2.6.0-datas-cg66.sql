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

---------------------------------------------------------------------------------------------------------
-- 20131001: Script de mise à jour des infos des dossiers PCGs
--          une fois les données renseignées dans les tables
--          polesdossierspcgs66 et users
---------------------------------------------------------------------------------------------------------

UPDATE dossierspcgs66
	SET poledossierpcg66_id = (
		SELECT users.poledossierpcg66_id
			FROM users
			WHERE
				users.poledossierpcg66_id IS NOT NULL
				and users.id = dossierspcgs66.user_id
	)
	WHERE
		dossierspcgs66.poledossierpcg66_id IS NULL
		AND dossierspcgs66.user_id IS NOT NULL;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
