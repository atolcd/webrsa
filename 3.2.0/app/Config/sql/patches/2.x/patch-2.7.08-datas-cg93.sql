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

-- Correction des CER dont le statut est "Signé" alors qu'il devrait être "Signé et transféré"
UPDATE cers93
	SET positioncer = '02attdecisioncpdv'
	WHERE
		positioncer = '01signe'
		AND EXISTS(
			SELECT
					*
				FROM histoschoixcers93
				WHERE
					histoschoixcers93.cer93_id = cers93.id
					AND histoschoixcers93.etape = '02attdecisioncpdv'
		) ;


-- Correction des CER dont le statut est "Signé et transféré" alors qu'il devrait être "Envoi Responsable"
UPDATE cers93
	SET positioncer = '01signe'
	WHERE
		positioncer = '02attdecisioncpdv'
		AND datesignature IS NOT NULL
		AND NOT EXISTS(
			SELECT
					*
				FROM histoschoixcers93
				WHERE
					histoschoixcers93.cer93_id = cers93.id
		);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
