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

-- Suppression des vrais doublons de la table personnes_referents ayant une
-- dfdesignation à NULL pour le même personne_id.
DELETE FROM personnes_referents
	WHERE
		(personne_id, referent_id) IN (
			SELECT
				personne_id,
				referent_id
			FROM personnes_referents AS doublon
			WHERE
				doublon.dfdesignation IS NULL
				AND personnes_referents.personne_id = doublon.personne_id
				AND personnes_referents.referent_id = doublon.referent_id
				AND personnes_referents.dddesignation = doublon.dddesignation
				AND NOT EXISTS (
					SELECT *
						FROM fichiersmodules
						WHERE
							fichiersmodules.modele = 'PersonneReferent'
							AND fichiersmodules.fk_value = doublon.id
				)
			GROUP BY doublon.personne_id, doublon.referent_id, doublon.dddesignation
			HAVING COUNT(*) > 1
		)
		AND id NOT IN (
			SELECT
				MIN(doublon.id)
			FROM personnes_referents AS doublon
			WHERE
				doublon.dfdesignation IS NULL
				AND personnes_referents.personne_id = doublon.personne_id
				AND personnes_referents.referent_id = doublon.referent_id
				AND personnes_referents.dddesignation = doublon.dddesignation
				AND NOT EXISTS (
					SELECT *
						FROM fichiersmodules
						WHERE
							fichiersmodules.modele = 'PersonneReferent'
							AND fichiersmodules.fk_value = doublon.id
				)
			GROUP BY doublon.personne_id, doublon.referent_id, doublon.dddesignation
			HAVING COUNT(*) > 1
		)
		AND dfdesignation IS NULL
		AND NOT EXISTS (
			SELECT *
				FROM fichiersmodules
				WHERE
					fichiersmodules.modele = 'PersonneReferent'
					AND fichiersmodules.fk_value = personnes_referents.id
		);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
