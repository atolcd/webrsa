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
UPDATE groups SET code = 'msp-referent' WHERE id = 4;
UPDATE groups SET code = 'msp-direction' WHERE id = 5;
UPDATE groups SET code = 'msp-administratif' WHERE id = 6;
UPDATE groups SET code = 'mission-insertion' WHERE id = 7;
UPDATE groups SET code = 'mission-rsa' WHERE id = 8;
UPDATE groups SET code = 'dtr' WHERE id = 9;
UPDATE groups SET code = 'instructeur-cga' WHERE id = 10;
UPDATE groups SET code = 'spi' WHERE id = 11;
UPDATE groups SET code = 'dps-admin' WHERE id = 12;
UPDATE groups SET code = 'msp-administratif-plus' WHERE id = 13;
UPDATE groups SET code = 'msp-redacteur' WHERE id = 14;
UPDATE groups SET code = 'enregistrement-cga' WHERE id = 15;
UPDATE groups SET code = 'insertion-pdo' WHERE id = 16;
UPDATE groups SET code = 'cellule-apre' WHERE id = 17;
UPDATE groups SET code = 'mission-insertion-renfort' WHERE id = 18;
UPDATE groups SET code = 'service-retour-emploi' WHERE id = 19;
UPDATE groups SET code = 'enregistrement-cga-ajout-fiche' WHERE id = 20;
UPDATE groups SET code = 'dps-lecture' WHERE id = 21;
UPDATE groups SET code = 'cis' WHERE id = 22;
UPDATE groups SET code = 'adrh-saisie' WHERE id = 23;
UPDATE groups SET code = 'adrh-consulte' WHERE id = 24;
UPDATE groups SET code = 'mlj-saisie' WHERE id = 25;
UPDATE groups SET code = 'det' WHERE id = 26;
UPDATE groups SET code = 'dps-recours' WHERE id = 27;
UPDATE groups SET code = 'aucun-droit' WHERE id = 28;
UPDATE groups SET code = 'assso-joseph-sauvy' WHERE id = 29;
ALTER TABLE groups ALTER COLUMN code SET NOT NULL;
ALTER TABLE groups ADD CONSTRAINT groups_code_by_default CHECK (code != 'by-default');

-- *****************************************************************************
COMMIT;
-- *****************************************************************************