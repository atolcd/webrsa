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

SELECT add_missing_table_field( 'public', 'users', 'email', 'VARCHAR(250)' );
ALTER TABLE users ALTER COLUMN email SET DEFAULT NULL;

ALTER TABLE expsproscers93 ALTER COLUMN nbduree TYPE FLOAT;

ALTER TABLE cers93 DROP CONSTRAINT cers93_pointparcours_in_list_chk;
ALTER TABLE cers93 ADD CONSTRAINT cers93_pointparcours_in_list_chk CHECK ( cakephp_validate_in_list( pointparcours, ARRAY['aladate','alafin','encours'] ) );


ALTER TABLE cuis ALTER COLUMN typevoieemployeur DROP NOT NULL;
ALTER TABLE cuis ALTER COLUMN nomvoieemployeur DROP NOT NULL;
ALTER TABLE cuis ALTER COLUMN codepostalemployeur DROP NOT NULL;
ALTER TABLE cuis ALTER COLUMN villeemployeur DROP NOT NULL;
-- *****************************************************************************
COMMIT;
-- *****************************************************************************
