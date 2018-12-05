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

-- Historisation des tableaux B7

ALTER TABLE tableauxsuivispdvs93 DROP CONSTRAINT tableauxsuivispdvs93_name_in_list_chk;
ALTER TABLE tableauxsuivispdvs93 ADD CONSTRAINT tableauxsuivispdvs93_name_in_list_chk CHECK (cakephp_validate_in_list(name::text, ARRAY['tableaud1'::text, 'tableaud2'::text, 'tableau1b3'::text, 'tableau1b4'::text, 'tableau1b5'::text, 'tableau1b6'::text, 'tableaub7'::text, 'tableaub7d2typecontrat'::text, 'tableaub7d2familleprofessionnelle'::text]));

-- *****************************************************************************
COMMIT;
-- *****************************************************************************