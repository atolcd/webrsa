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

-- *****************************************************************************

DROP TABLE IF EXISTS regressionsorientationseps93 CASCADE;
DROP TABLE IF EXISTS decisionsregressionsorientationseps93 CASCADE;

-- *****************************************************************************

DROP INDEX IF EXISTS regressionsorientationseps93_dossierep_id_idx;
DROP INDEX IF EXISTS regressionsorientationseps93_typeorient_id_idx;
DROP INDEX IF EXISTS regressionsorientationseps93_structurereferente_id_idx;
DROP INDEX IF EXISTS regressionsorientationseps93_referent_id_idx;
DROP INDEX IF EXISTS regressionsorientationseps93_user_id_idx;

DROP INDEX IF EXISTS decisionsregressionsorientationseps93_regressionorientationep93_id_idx;
DROP INDEX IF EXISTS decisionsregressionsorientationseps93_typeorient_id_idx;
DROP INDEX IF EXISTS decisionsregressionsorientationseps93_structurereferente_id_idx;
DROP INDEX IF EXISTS decisionsregressionsorientationseps93_referent_id_idx;

-- *****************************************************************************

SELECT add_missing_table_field ('public', 'contratsinsertion', 'current_action', 'TEXT');

CREATE TABLE regressionsorientationseps93 (
	id      				SERIAL NOT NULL PRIMARY KEY,
	dossierep_id			INTEGER DEFAULT NULL REFERENCES dossierseps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typeorient_id			INTEGER NOT NULL REFERENCES typesorients(id) ON DELETE CASCADE ON UPDATE CASCADE,
	structurereferente_id	INTEGER NOT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	datedemande				DATE NOT NULL,
	referent_id				INTEGER DEFAULT NULL REFERENCES referents(id) ON DELETE SET NULL ON UPDATE CASCADE,
	user_id					INTEGER DEFAULT NULL REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
	commentaire				TEXT DEFAULT NULL,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE regressionsorientationseps93 IS 'Thématique pour la réorientation du professionel vers le social (CG93)';

CREATE INDEX regressionsorientationseps93_dossierep_id_idx ON regressionsorientationseps93 (dossierep_id);
CREATE INDEX regressionsorientationseps93_typeorient_id_idx ON regressionsorientationseps93 (typeorient_id);
CREATE INDEX regressionsorientationseps93_structurereferente_id_idx ON regressionsorientationseps93 (structurereferente_id);
CREATE INDEX regressionsorientationseps93_referent_id_idx ON regressionsorientationseps93 (referent_id);
CREATE INDEX regressionsorientationseps93_user_id_idx ON regressionsorientationseps93 (user_id);

SELECT add_missing_table_field ('public', 'eps', 'regressionorientationep93', 'TYPE_NIVEAUDECISIONEP');
ALTER TABLE eps ALTER COLUMN regressionorientationep93 SET DEFAULT 'nontraite';
UPDATE eps SET regressionorientationep93 = 'nontraite' WHERE regressionorientationep93 IS NULL;
ALTER TABLE eps ALTER COLUMN regressionorientationep93 SET NOT NULL;

CREATE TABLE decisionsregressionsorientationseps93 (
	id      						SERIAL NOT NULL PRIMARY KEY,
	regressionorientationep93_id	INTEGER NOT NULL REFERENCES regressionsorientationseps93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typeorient_id					INTEGER DEFAULT NULL REFERENCES typesorients(id) ON UPDATE CASCADE ON DELETE SET NULL,
	structurereferente_id			INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON UPDATE CASCADE ON DELETE SET NULL,
	referent_id						INTEGER DEFAULT NULL REFERENCES referents(id) ON DELETE SET NULL ON UPDATE CASCADE,
	etape							TYPE_ETAPEDECISIONEP NOT NULL,
	commentaire						TEXT DEFAULT NULL,
	created							TIMESTAMP WITHOUT TIME ZONE,
	modified						TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE decisionsregressionsorientationseps93 IS 'Décisions pour la thématique de la réorientation du professionel vers le social (CG93)';

CREATE INDEX decisionsregressionsorientationseps93_regressionorientationep93_id_idx ON decisionsregressionsorientationseps93 (regressionorientationep93_id);
CREATE INDEX decisionsregressionsorientationseps93_typeorient_id_idx ON decisionsregressionsorientationseps93 (typeorient_id);
CREATE INDEX decisionsregressionsorientationseps93_structurereferente_id_idx ON decisionsregressionsorientationseps93 (structurereferente_id);
CREATE INDEX decisionsregressionsorientationseps93_referent_id_idx ON decisionsregressionsorientationseps93 (referent_id);

-- *****************************************************************************

CREATE OR REPLACE FUNCTION public.alter_tablename_ifexists ( p_namespace text, p_namefrom text, p_nameto text ) RETURNS bool AS
$$
	DECLARE
		v_row       		record;
		v_query     		text;
	BEGIN
		select 1 into v_row
		from information_schema.tables ta
		where ta.table_name = p_namefrom;
		if found then
			raise notice 'Upgrade table %.% - rename to %', p_namespace, p_namefrom, p_nameto;
			v_query := 'alter table ' || p_namespace || '.' || p_namefrom || ' rename to ' || p_nameto || ';';
			execute v_query;
			return 't';
		else
			raise notice 'Table %.% not found', p_namespace, p_namefrom;
			return 'f';
		end if;
	END;
$$
LANGUAGE plpgsql;

COMMENT ON FUNCTION public.alter_tablename_ifexists ( p_namespace text, p_namefrom text, p_nameto text ) IS 'Renomage de table p_namefrom en p_nameto si elle existe';

-- *****************************************************************************

CREATE OR REPLACE FUNCTION public.alter_columnname_ifexists ( p_namespace text, p_tablename text, p_columnnamefrom text, p_columnnameto text ) RETURNS bool AS
$$
	DECLARE
		v_row       		record;
		v_query     		text;
	BEGIN
		select 1 into v_row
		from information_schema.columns tc
		where tc.table_name = p_tablename
			and tc.column_name = p_columnnamefrom;
		if found then
			raise notice 'Upgrade table %.% - rename colum % to %', p_namespace, p_tablename, p_columnnamefrom, p_columnnameto;
			v_query := 'alter table ' || p_namespace || '.' || p_tablename || ' rename column ' || p_columnnamefrom || ' to ' || p_columnnameto || ';';
			execute v_query;
			return 't';
		else
			raise notice 'Column % not found in table %.%', p_columnnamefrom, p_namespace, p_tablename;
			return 'f';
		end if;
	END;
$$
LANGUAGE plpgsql;

COMMENT ON FUNCTION public.alter_columnname_ifexists ( p_namespace text, p_tablename text, p_columnnamefrom text, p_columnnameto text ) IS 'Renomage de la colonne p_columnnamefrom en p_columnnameto de la table p_tablename si elle existe';

-- *****************************************************************************

SELECT public.alter_tablename_ifexists( 'public', 'membreseps_seanceseps', 'commissionseps_membreseps' );

SELECT public.alter_columnname_ifexists( 'public', 'dossierseps', 'seanceep_id', 'commissionep_id' );
SELECT public.alter_columnname_ifexists( 'public', 'commissionseps_membreseps', 'seanceep_id', 'commissionep_id' );
SELECT public.alter_tablename_ifexists( 'public', 'seanceseps', 'commissionseps' );

SELECT public.alter_tablename_ifexists( 'public', 'nvsrsepsreorientsrs93', 'decisionsreorientationseps93' );

SELECT public.alter_columnname_ifexists( 'public', 'decisionsreorientationseps93', 'saisineepreorientsr93_id', 'reorientationep93_id' );
SELECT public.alter_tablename_ifexists( 'public', 'saisinesepsreorientsrs93', 'reorientationseps93' );

SELECT public.alter_tablename_ifexists( 'public', 'nvsepdspdos66', 'decisionssaisinespdoseps66' );

SELECT public.alter_columnname_ifexists( 'public', 'decisionssaisinespdoseps66', 'saisineepdpdo66_id', 'saisinepdoep66_id' );
SELECT public.alter_tablename_ifexists( 'public', 'saisinesepdspdos66', 'saisinespdoseps66' );

SELECT public.alter_tablename_ifexists( 'public', 'nvsrsepsreorient66', 'decisionssaisinesbilansparcourseps66' );

SELECT public.alter_columnname_ifexists( 'public', 'decisionssaisinesbilansparcourseps66', 'saisineepbilanparcours66_id', 'saisinebilanparcoursep66_id' );
SELECT public.alter_tablename_ifexists( 'public', 'saisinesepsbilansparcours66', 'saisinesbilansparcourseps66' );

SELECT public.alter_columnname_ifexists( 'public', 'reorientationseps93', 'motifreorient_id', 'motifreorientep93_id' );
SELECT public.alter_tablename_ifexists( 'public', 'motifsreorients', 'motifsreorientseps93' );

SELECT public.alter_columnname_ifexists( 'public', 'decisionsnonorientationspros58', 'nonorientationpro58_id', 'nonorientationproep58_id' );
SELECT public.alter_tablename_ifexists( 'public', 'nonorientationspros58', 'nonorientationsproseps58' );
SELECT public.alter_columnname_ifexists( 'public', 'decisionsnonorientationspros66', 'nonorientationpro66_id', 'nonorientationproep66_id' );
SELECT public.alter_tablename_ifexists( 'public', 'nonorientationspros66', 'nonorientationsproseps66' );
SELECT public.alter_columnname_ifexists( 'public', 'decisionsnonorientationspros93', 'nonorientationpro93_id', 'nonorientationproep93_id' );
SELECT public.alter_tablename_ifexists( 'public', 'nonorientationspros93', 'nonorientationsproseps93' );

SELECT public.alter_tablename_ifexists( 'public', 'decisionsnonorientationspros58', 'decisionsnonorientationsproseps58' );
SELECT public.alter_tablename_ifexists( 'public', 'decisionsnonorientationspros66', 'decisionsnonorientationsproseps66' );
SELECT public.alter_tablename_ifexists( 'public', 'decisionsnonorientationspros93', 'decisionsnonorientationsproseps93' );

SELECT public.alter_columnname_ifexists( 'public', 'eps', 'saisineepbilanparcours66', 'saisinebilanparcoursep66' );
SELECT public.alter_columnname_ifexists( 'public', 'eps', 'saisineepdpdo66', 'saisinepdoep66' );
SELECT public.alter_columnname_ifexists( 'public', 'eps', 'saisineepreorientsr93', 'reorientationep93' );
SELECT public.alter_columnname_ifexists( 'public', 'eps', 'nonorientationpro58', 'nonorientationproep58' );
SELECT public.alter_columnname_ifexists( 'public', 'eps', 'nonorientationpro93', 'nonorientationproep93' );

ALTER TABLE dossierseps ALTER COLUMN themeep TYPE TEXT;
UPDATE dossierseps SET themeep = 'saisinesbilansparcourseps66' WHERE themeep = 'saisinesepsbilansparcours66';
UPDATE dossierseps SET themeep = 'saisinespdoseps66' WHERE themeep = 'saisinesepdspdos66';
UPDATE dossierseps SET themeep = 'reorientationseps93' WHERE themeep = 'saisinesepsreorientsrs93';
UPDATE dossierseps SET themeep = 'nonorientationsproseps58' WHERE themeep = 'nonorientationspros58';
UPDATE dossierseps SET themeep = 'nonorientationsproseps93' WHERE themeep = 'nonorientationspros93';
DROP TYPE IF EXISTS TYPE_THEMEEP;
CREATE TYPE TYPE_THEMEEP AS ENUM ( 'reorientationseps93', 'saisinesbilansparcourseps66', 'saisinespdoseps66', 'nonrespectssanctionseps93', 'defautsinsertionseps66', 'nonorientationsproseps58', 'nonorientationsproseps93', 'regressionsorientationseps58', 'sanctionseps58' );
ALTER TABLE dossierseps ALTER COLUMN themeep TYPE TYPE_THEMEEP USING CAST(themeep AS TYPE_THEMEEP);

UPDATE acos SET alias = regexp_replace(alias, 'MembresepsSeanceseps', 'CommissionsepsMembreseps') WHERE alias LIKE '%MembresepsSeanceseps%';
UPDATE acos SET alias = regexp_replace(alias, 'Seanceseps', 'Commissionseps') WHERE alias LIKE '%Seanceseps%';
UPDATE acos SET alias = regexp_replace(alias, 'Nvsrsepsreorientsrs93', 'Decisionsreorientationseps93') WHERE alias LIKE '%Nvsrsepsreorientsrs93%';
UPDATE acos SET alias = regexp_replace(alias, 'Saisinesepsreorientsrs93', 'Reorientationseps93') WHERE alias LIKE '%Saisinesepsreorientsrs93%';
UPDATE acos SET alias = regexp_replace(alias, 'Nvsepdspdos66', 'Decisionssaisinespdoseps66') WHERE alias LIKE '%Nvsepdspdos66%';
UPDATE acos SET alias = regexp_replace(alias, 'Saisinesepdspdos66', 'Saisinespdoseps66') WHERE alias LIKE '%Saisinesepdspdos66%';
UPDATE acos SET alias = regexp_replace(alias, 'Nvsrsepsreorient66', 'Decisionssaisinesbilansparcourseps66') WHERE alias LIKE '%Nvsrsepsreorient66%';
UPDATE acos SET alias = regexp_replace(alias, 'Saisinesepsbilansparcours66', 'Saisinesbilansparcourseps66') WHERE alias LIKE '%Saisinesepsbilansparcours66%';
UPDATE acos SET alias = regexp_replace(alias, 'Motifsreorients', 'Motifsreorientseps93') WHERE alias LIKE '%Motifsreorients%';
UPDATE acos SET alias = regexp_replace(alias, 'Nonorientationspros58', 'Nonorientationsproseps58') WHERE alias LIKE '%Nonorientationspros58%';
UPDATE acos SET alias = regexp_replace(alias, 'Nonorientationspros66', 'Nonorientationsproseps66') WHERE alias LIKE '%Nonorientationspros66%';
UPDATE acos SET alias = regexp_replace(alias, 'Nonorientationspros93', 'Nonorientationsproseps93') WHERE alias LIKE '%Nonorientationspros93%';
UPDATE acos SET alias = regexp_replace(alias, 'Decisionsnonorientationspros58', 'Decisionsnonorientationsproseps58') WHERE alias LIKE '%Decisionsnonorientationspros58%';
UPDATE acos SET alias = regexp_replace(alias, 'Decisionsnonorientationspros66', 'Decisionsnonorientationsproseps66') WHERE alias LIKE '%Decisionsnonorientationspros66%';
UPDATE acos SET alias = regexp_replace(alias, 'Decisionsnonorientationspros93', 'Decisionsnonorientationsproseps93') WHERE alias LIKE '%Decisionsnonorientationspros93%';

-- 20110401
ALTER TABLE apres ALTER COLUMN etatdossierapre TYPE TEXT;
ALTER TABLE relancesapres ALTER COLUMN etatdossierapre TYPE TEXT;
DROP TYPE IF EXISTS TYPE_ETATDOSSIERAPRE;
CREATE TYPE TYPE_ETATDOSSIERAPRE AS ENUM ( 'COM', 'INC', 'VAL' );
ALTER TABLE apres ALTER COLUMN etatdossierapre TYPE TYPE_ETATDOSSIERAPRE USING CAST(etatdossierapre AS TYPE_ETATDOSSIERAPRE);
ALTER TABLE relancesapres ALTER COLUMN etatdossierapre TYPE TYPE_ETATDOSSIERAPRE USING CAST(etatdossierapre AS TYPE_ETATDOSSIERAPRE);

-- 20110405: réparation suite au renommage des tables: renommage des séquences
CREATE OR REPLACE FUNCTION public.rename_sequence_ifexists( p_namefrom text, p_nameto text ) RETURNS bool AS
$$
	DECLARE
		v_row   record;
		v_query text;
	BEGIN
		SELECT 1 INTO v_row
			FROM pg_class c
			WHERE c.relname = p_namefrom || '_id_seq' AND c.relkind = 'S';
		IF FOUND THEN
			RAISE NOTICE 'Upgrade sequence %_id_seq - rename to %_id_seq', p_namefrom, p_nameto;
			v_query := 'ALTER TABLE ' || p_namefrom || '_id_seq RENAME TO ' || p_nameto || '_id_seq;';
			EXECUTE v_query;
			return 't';
		else
			RAISE NOTICE 'Sequence %_id_seq not found', p_namefrom;
			RETURN 'f';
		end if;
	END;
$$
LANGUAGE plpgsql;

COMMENT ON FUNCTION public.rename_sequence_ifexists( p_namefrom text, p_nameto text ) IS 'Renommage de la séquence p_namefrom en p_nameto si elle existe';

SELECT rename_sequence_ifexists( 'membreseps_seanceseps', 'commissionseps_membreseps' );
SELECT rename_sequence_ifexists( 'seanceseps', 'commissionseps' );
SELECT rename_sequence_ifexists( 'nvsrsepsreorientsrs93', 'decisionsreorientationseps93' );
SELECT rename_sequence_ifexists( 'saisinesepsreorientsrs93', 'reorientationseps93' );
SELECT rename_sequence_ifexists( 'nvsepdspdos66', 'decisionssaisinespdoseps66' );
SELECT rename_sequence_ifexists( 'saisinesepdspdos66', 'saisinespdoseps66' );
SELECT rename_sequence_ifexists( 'nvsrsepsreorient66', 'decisionssaisinesbilansparcourseps66' );
SELECT rename_sequence_ifexists( 'saisinesepsbilansparcours66', 'saisinesbilansparcourseps66' );
SELECT rename_sequence_ifexists( 'motifsreorients', 'motifsreorientseps93' );
SELECT rename_sequence_ifexists( 'nonorientationspros58', 'nonorientationsproseps58' );
SELECT rename_sequence_ifexists( 'nonorientationspros66', 'nonorientationsproseps66' );
SELECT rename_sequence_ifexists( 'nonorientationspros93', 'nonorientationsproseps93' );
SELECT rename_sequence_ifexists( 'decisionsnonorientationspros58', 'decisionsnonorientationsproseps58' );
SELECT rename_sequence_ifexists( 'decisionsnonorientationspros66', 'decisionsnonorientationsproseps66' );
SELECT rename_sequence_ifexists( 'decisionsnonorientationspros93', 'decisionsnonorientationsproseps93' );

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
