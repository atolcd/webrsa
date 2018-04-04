SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- *****************************************************************************
SELECT NOW();
BEGIN;
-- *****************************************************************************


-- *****************************************************************************
-- /CatégoriesFPS93
-- Ajout de l'année de référence a la fiche de catégorie
-- RAS de plus
-- *****************************************************************************

SELECT add_missing_table_field ('public', 'thematiquesfps93', 'yearthema', 'VARCHAR(4) DEFAULT ''2017''');
COMMENT ON COLUMN thematiquesfps93.yearthema IS 'Année du Référenciel';

-- Index: thematiquesfps93_type_name_idx

DROP INDEX thematiquesfps93_type_name_idx;

CREATE UNIQUE INDEX thematiquesfps93_type_name_idx
  ON thematiquesfps93
  USING btree
  (type, name, yearthema);


ALTER TABLE orientsstructs DROP CONSTRAINT orientsstructs_origine_check;
ALTER TABLE orientsstructs
  ADD CONSTRAINT orientsstructs_origine_check CHECK
	(
	origine IS NULL
	AND date_valid IS NULL

	OR

	origine IS NOT NULL
	AND date_valid IS NOT NULL
	AND
		(
		rgorient = 1
		AND (origine::text = ANY (
			ARRAY[
				'manuelle'::character varying::text,
				'cohorte'::character varying::text,
				'prestadiagno'::character varying::text,
				'prestadefaut'::character varying::text
			]))

		OR

		rgorient > 1
		AND origine::text = 'reorientation'::text

		OR

		rgorient > 1
		AND origine::text = 'demenagement'::text
		)
	);

ALTER TABLE orientsstructs DROP CONSTRAINT orientsstructs_origine_in_list_chk;
ALTER TABLE orientsstructs
  ADD CONSTRAINT orientsstructs_origine_in_list_chk CHECK
	(
	cakephp_validate_in_list
		(
		origine::text,
		ARRAY[
			'manuelle'::text,
			'cohorte'::text,
			'reorientation'::text,
			'demenagement'::text,
			'prestadiagno'::text,
			'prestadefaut'::text
		]));


ALTER TABLE users DROP CONSTRAINT users_type_in_list_chk;

ALTER TABLE users
  ADD CONSTRAINT users_type_in_list_chk CHECK (cakephp_validate_in_list(type::text, ARRAY['cg'::text, 'externe_cpdvcom'::text, 'externe_cpdv'::text, 'externe_secretaire'::text, 'externe_ci'::text, 'prestataire'::text]));


-- *****************************************************************************
COMMIT;
SELECT NOW();
-- *****************************************************************************
