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

SELECT add_missing_table_field ( 'public', 'decisionsnonrespectssanctionseps93', 'user_id', 'INTEGER' );
ALTER TABLE decisionsnonrespectssanctionseps93 ALTER COLUMN user_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'decisionsnonrespectssanctionseps93', 'decisionsnonrespectssanctionseps93_user_id_fk', 'users', 'user_id' );
DROP INDEX IF EXISTS decisionsnonrespectssanctionseps93_user_id_idx;
CREATE INDEX decisionsnonrespectssanctionseps93_user_id_idx ON decisionsnonrespectssanctionseps93 (user_id);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************