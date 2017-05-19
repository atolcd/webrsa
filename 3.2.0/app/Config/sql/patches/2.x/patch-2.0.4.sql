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

SELECT add_missing_table_field ( 'public', 'structuresreferentes', 'numtel', 'VARCHAR(10)' );
ALTER TABLE structuresreferentes ALTER COLUMN numtel SET DEFAULT NULL;

SELECT add_missing_table_field ( 'public', 'passagescommissionseps', 'user_id', 'INTEGER' );
ALTER TABLE passagescommissionseps ALTER COLUMN user_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'passagescommissionseps', 'passagescommissionseps_user_id_fk', 'users', 'user_id' );
DROP INDEX IF EXISTS passagescommissionseps_user_id_idx;
CREATE INDEX passagescommissionseps_user_id_idx ON passagescommissionseps (user_id);

SELECT add_missing_table_field ( 'public', 'relancesnonrespectssanctionseps93', 'user_id', 'INTEGER' );
ALTER TABLE relancesnonrespectssanctionseps93 ALTER COLUMN user_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'relancesnonrespectssanctionseps93', 'relancesnonrespectssanctionseps93_user_id_fk', 'users', 'user_id' );
DROP INDEX IF EXISTS relancesnonrespectssanctionseps93_user_id_idx;
CREATE INDEX relancesnonrespectssanctionseps93_user_id_idx ON relancesnonrespectssanctionseps93 (user_id);

SELECT add_missing_table_field ( 'public', 'reorientationseps93', 'user_id', 'INTEGER' );
ALTER TABLE reorientationseps93 ALTER COLUMN user_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'reorientationseps93', 'reorientationseps93_user_id_fk', 'users', 'user_id' );
DROP INDEX IF EXISTS reorientationseps93_user_id_idx;
CREATE INDEX reorientationseps93_user_id_idx ON reorientationseps93 (user_id);

SELECT add_missing_table_field ( 'public', 'decisionsreorientationseps93', 'user_id', 'INTEGER' );
ALTER TABLE decisionsreorientationseps93 ALTER COLUMN user_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'decisionsreorientationseps93', 'decisionsreorientationseps93_user_id_fk', 'users', 'user_id' );
DROP INDEX IF EXISTS decisionsreorientationseps93_user_id_idx;
CREATE INDEX decisionsreorientationseps93_user_id_idx ON decisionsreorientationseps93 (user_id);

SELECT add_missing_table_field ( 'public', 'decisionsreorientationseps93', 'orientstruct_id', 'INTEGER' );
ALTER TABLE decisionsreorientationseps93 ALTER COLUMN orientstruct_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'decisionsreorientationseps93', 'decisionsreorientationseps93_orientstruct_id_fk', 'orientsstructs', 'orientstruct_id' );
DROP INDEX IF EXISTS decisionsreorientationseps93_orientstruct_id_idx;
CREATE INDEX decisionsreorientationseps93_orientstruct_id_idx ON decisionsreorientationseps93 (orientstruct_id);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************