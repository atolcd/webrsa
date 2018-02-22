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


-- *****************************************************************************
COMMIT;
SELECT NOW();
-- *****************************************************************************
