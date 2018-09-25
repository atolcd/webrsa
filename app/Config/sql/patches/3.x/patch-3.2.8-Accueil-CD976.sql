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
UPDATE groups SET code = 'assistant-administratif' WHERE id = 2;
UPDATE groups SET code = 'sous-administrateurs' WHERE id = 3;
UPDATE groups SET code = 'referent-unique' WHERE id = 4;
UPDATE groups SET code = 'chef-service' WHERE id = 5;
UPDATE groups SET code = 'assistant-direction' WHERE id = 11;
UPDATE groups SET code = 'directeur' WHERE id = 12;
UPDATE groups SET code = 'directeur-adjoint' WHERE id = 13;
UPDATE groups SET code = 'PartenairesCAF-PE' WHERE id = 14;
UPDATE groups SET code = 'apre' WHERE id = 15;
UPDATE groups SET code = 'agent-accueil' WHERE id = 16;
UPDATE groups SET code = 'charge-dossiers' WHERE id = 17;
UPDATE groups SET code = 'chef-bureau' WHERE id = 18;
UPDATE groups SET code = 'equipe-pluridisciplinaire' WHERE id = 19;
UPDATE groups SET code = 'cui' WHERE id = 20;
UPDATE groups SET code = 'correspondantRSA' WHERE id = 21;
UPDATE groups SET code = 'assistant-social' WHERE id = 22;
ALTER TABLE groups ALTER COLUMN code SET NOT NULL;
ALTER TABLE groups ADD CONSTRAINT groups_code_by_default CHECK (code != 'by-default');

-- *****************************************************************************
COMMIT;
-- *****************************************************************************