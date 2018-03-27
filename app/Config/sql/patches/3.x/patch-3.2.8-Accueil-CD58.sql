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

ALTER TABLE groups ADD COLUMN code character varying(50);
CREATE UNIQUE INDEX groups_code_idx ON groups USING btree (code);
UPDATE groups SET code = 'administrateurs' WHERE id = 1;
UPDATE groups SET code = 'agents-consultation' WHERE id = 2;
UPDATE groups SET code = 'agents-saisie' WHERE id = 3;
UPDATE groups SET code = 'ccas' WHERE id = 4;
UPDATE groups SET code = 'observatoire' WHERE id = 5;
ALTER TABLE groups ALTER COLUMN code SET NOT NULL;
ALTER TABLE groups ADD CONSTRAINT groups_code_by_default CHECK (code != 'by-default');

-- *****************************************************************************
COMMIT;
-- *****************************************************************************