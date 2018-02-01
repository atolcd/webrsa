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

-- 20171019: ajout du "Régime Micro-bic Agricole" et de la colonne "Chiffre d'Affaire Expl. Agricole"
SELECT alter_table_drop_constraint_if_exists( 'public', 'traitementspcgs66', 'traitementspcgs66_regime_in_list_chk' );
ALTER TABLE traitementspcgs66 ADD CONSTRAINT traitementspcgs66_regime_in_list_chk CHECK ( cakephp_validate_in_list( regime, ARRAY['fagri', 'ragri', 'reel', 'microbic', 'microbicauto', 'microbnc', 'microbicagri'] ) );

SELECT add_missing_table_field ('public', 'traitementspcgs66', 'chaffagri', 'FLOAT');

-- *****************************************************************************
COMMIT;
-- *****************************************************************************