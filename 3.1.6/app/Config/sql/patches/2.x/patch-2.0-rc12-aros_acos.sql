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

/* -----------------------------------------------------------------------------
FIXME

CG66: Deux aros possédant un groupe "Administrateurs" (le CG58 a le même problème ?)

-- aros
5188	NULL	Group	0	Administrateurs	1	10
5496	0		Group	1	Administrateurs	609	610

-- groups
1	Administrateurs	0

-- 4
SELECT COUNT(*)
    FROM aros
    WHERE parent_id = 5188;

-- 0
SELECT COUNT(*)
    FROM aros
    WHERE parent_id = 5496;

-- 576
SELECT COUNT(*)
    FROM aros_acos
    WHERE aro_id = 5496;

-- ---------------------------------------------------------------------------*/

DELETE FROM aros_acos
	WHERE aros_acos.aro_id IN(
		SELECT aros.id
			FROM aros
			WHERE aros.model = 'Group'
				AND aros.foreign_key NOT IN (
					SELECT id FROM groups
				)
		);

DELETE FROM aros
	WHERE aros.model = 'Group'
	AND aros.foreign_key NOT IN (
		SELECT id FROM groups
	);

-- -----------------------------------------------------------------------------
-- Remise en état de certaines données ayant une valeur bizarre
-- -----------------------------------------------------------------------------

UPDATE acos
	SET parent_id = 0
	WHERE parent_id IS NULL;

UPDATE acos
	SET foreign_key = 0
	WHERE foreign_key IS NULL;

UPDATE aros
	SET parent_id = 0
	WHERE parent_id IS NULL;

UPDATE aros
	SET foreign_key = groups.id
	FROM groups
	WHERE aros.alias = groups.name
	AND aros.model = 'Group';

-- Ajout de contraintes pour éviter que cela ne se reproduise
ALTER TABLE acos ALTER COLUMN parent_id SET NOT NULL;
ALTER TABLE acos ALTER COLUMN parent_id SET DEFAULT 0;
ALTER TABLE acos ALTER COLUMN foreign_key SET NOT NULL;
ALTER TABLE acos ALTER COLUMN foreign_key SET DEFAULT 0;

-- ALTER TABLE aros ALTER COLUMN parent_id SET NOT NULL;
ALTER TABLE aros ALTER COLUMN parent_id SET DEFAULT 0;

-- -----------------------------------------------------------------------------
-- Création des indexes
-- -----------------------------------------------------------------------------

-- acos
DROP INDEX IF EXISTS acos_alias_idx;
CREATE UNIQUE INDEX acos_alias_idx ON acos(alias);

DROP INDEX IF EXISTS acos_parent_id_idx;
CREATE INDEX acos_parent_id_idx ON acos(parent_id);

-- aros

DROP INDEX IF EXISTS aros_model_alias_idx;
CREATE UNIQUE INDEX aros_model_alias_idx ON aros(model,alias);

DROP INDEX IF EXISTS aros_alias_idx;
CREATE INDEX aros_alias_idx ON aros(alias);

DROP INDEX IF EXISTS aros_model_idx;
CREATE INDEX aros_model_idx ON aros(model);

DROP INDEX IF EXISTS aros_foreign_key_idx;
CREATE INDEX aros_foreign_key_idx ON aros(foreign_key);

DROP INDEX IF EXISTS aros_parent_id_idx;
CREATE INDEX aros_parent_id_idx ON aros(parent_id);

-- aros_acos
DROP INDEX IF EXISTS aros_aros_aro_id_aco_id_idx;
CREATE UNIQUE INDEX aros_aros_aro_id_aco_id_idx ON aros_acos(aro_id,aco_id);

DROP INDEX IF EXISTS aros_aros_aro_id_idx;
CREATE INDEX aros_aros_aro_id_idx ON aros_acos(aro_id);

DROP INDEX IF EXISTS aros_aros_aco_id_idx;
CREATE INDEX aros_aros_aco_id_idx ON aros_acos(aco_id);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************