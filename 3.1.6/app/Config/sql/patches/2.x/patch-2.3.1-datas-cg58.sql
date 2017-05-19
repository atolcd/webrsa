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
-- 20130111 - Pour le CG 58 (les autres CG ne sont pas concernés ni impactés),
-- il existe des contratinsertion validéws mais sans durée. Donc, on corrige à
-- partir des dates de début et des dates de fin (avec une tolérance de 35 jours).
-- Il restera des entrées avec des durées négatives, nulles, ou des durées trop
-- éloignées des durées définies et qui seront à corriger à la main.
--------------------------------------------------------------------------------

UPDATE contratsinsertion
	SET duree_engag = (
		CASE
			WHEN ( ( df_ci - dd_ci ) >= 56 AND ( df_ci - dd_ci ) <= 126 ) THEN 1
			WHEN ( ( df_ci - dd_ci ) >= 149 AND ( df_ci - dd_ci ) <= 218 ) THEN 2
			WHEN ( ( df_ci - dd_ci ) >= 239 AND ( df_ci - dd_ci ) <= 309 ) THEN 3
			WHEN ( ( df_ci - dd_ci ) >= 330 AND ( df_ci - dd_ci ) <= 400 ) THEN 4
			WHEN ( ( df_ci - dd_ci ) >= 513 AND ( df_ci - dd_ci ) <= 583 ) THEN 5
			WHEN ( ( df_ci - dd_ci ) >= 695 AND ( df_ci - dd_ci ) <= 765 ) THEN 6
			ELSE NULL
		END
	)
	WHERE
		duree_engag IS NULL
		AND decision_ci = 'V';

DELETE FROM orientsstructs WHERE statut_orient = 'Non orienté';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************