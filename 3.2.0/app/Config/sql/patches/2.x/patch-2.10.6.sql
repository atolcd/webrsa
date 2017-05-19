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
-- APRE -> ADRE
--------------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'apres', 'isapre', 'SMALLINT');
UPDATE apres SET isapre = 1;
SELECT alter_table_drop_constraint_if_exists ('public', 'apres', 'apres_isapre_in_list_chk');
ALTER TABLE apres ADD CONSTRAINT apres_isapre_in_list_chk CHECK (cakephp_validate_in_list(isapre, ARRAY[0,1]));

SELECT add_missing_table_field ('public', 'typesaidesapres66', 'plafondadre', 'NUMERIC(10,2)');
UPDATE typesaidesapres66 SET plafondadre = plafond;

SELECT add_missing_table_field ('public', 'typesaidesapres66', 'typeplafond', 'VARCHAR(4)');
SELECT alter_table_drop_constraint_if_exists ('public', 'typesaidesapres66', 'typesaidesapres66_typeplafond_in_list_chk');
ALTER TABLE typesaidesapres66 ADD CONSTRAINT typesaidesapres66_typeplafond_in_list_chk CHECK (cakephp_validate_in_list(typeplafond, ARRAY['APRE', 'ADRE', 'ALL']));
UPDATE typesaidesapres66 SET typeplafond = 'ALL';
ALTER TABLE typesaidesapres66 ALTER COLUMN plafond DROP NOT NULL;
SELECT alter_table_drop_constraint_if_exists ('public', 'typesaidesapres66', 'typesaidesapres66_plafond_check');
ALTER TABLE typesaidesapres66 ADD CONSTRAINT typesaidesapres66_plafond_check CHECK (COALESCE(plafond, plafondadre) IS NOT NULL);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************