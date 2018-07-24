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
UPDATE groups SET code = 'utilisateurs' WHERE id = 2;
UPDATE groups SET code = 'sous-administrateurs' WHERE id = 3;
UPDATE groups SET code = 'gestion-des-cohortes' WHERE id = 4;
UPDATE groups SET code = 'cesdi' WHERE id = 5;
UPDATE groups SET code = 'apre' WHERE id = 6;
UPDATE groups SET code = 'bat' WHERE id = 7;
UPDATE groups SET code = 'bijas' WHERE id = 8;
UPDATE groups SET code = 'anomalie' WHERE id = 9;
UPDATE groups SET code = 'bada-gestionnaire' WHERE id = 10;
UPDATE groups SET code = 'bada-cs' WHERE id = 11;
UPDATE groups SET code = 'bada-cadre' WHERE id = 12;
UPDATE groups SET code = 'bip-gestionnaire' WHERE id = 13;
UPDATE groups SET code = 'bip-cadre' WHERE id = 14;
UPDATE groups SET code = 'referent-structure' WHERE id = 15;
UPDATE groups SET code = 'secretaire-structure' WHERE id = 16;
UPDATE groups SET code = 'responsable-structure' WHERE id = 17;
UPDATE groups SET code = 'bijas-gestionnaire' WHERE id = 18;
UPDATE groups SET code = 'bijas-cadre' WHERE id = 19;
UPDATE groups SET code = 'bat-gestionnaire' WHERE id = 20;
UPDATE groups SET code = 'bat-cadre' WHERE id = 21;
UPDATE groups SET code = 'bbag-gestionnaire' WHERE id = 22;
UPDATE groups SET code = 'bbag-cadre' WHERE id = 23;
UPDATE groups SET code = 'parametrage' WHERE id = 24;
UPDATE groups SET code = 'responsable-asso' WHERE id = 25;
UPDATE groups SET code = 'secretaire-asso' WHERE id = 26;
UPDATE groups SET code = 'referent-asso' WHERE id = 27;
UPDATE groups SET code = 'consultation' WHERE id = 28;
UPDATE groups SET code = 'prestataire_non_orientes' WHERE id = 29;
ALTER TABLE groups ALTER COLUMN code SET NOT NULL;
ALTER TABLE groups ADD CONSTRAINT groups_code_by_default CHECK (code != 'by-default');

-- *****************************************************************************
COMMIT;
-- *****************************************************************************