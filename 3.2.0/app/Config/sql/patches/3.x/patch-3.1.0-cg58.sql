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
-- Parties de patch concernant la structure de la base de données qui n'étaient
-- pas passés
-- *****************************************************************************

-- Ajout du champ foyerid dans la table adresses
-- @see app/Config/sql/patches/2.x/patch-2.0-rc08.sql
SELECT add_missing_table_field( 'public', 'adresses', 'foyerid', 'INTEGER' );

-- Parties du patch 2.6.9 qui n'étaient pas passées
-- Il faut scinder le niveau 1201 des DSP pour les CER.
SELECT public.alter_table_drop_constraint_if_exists( 'public', 'cers93', 'cers93_nivetu_in_list_chk' );
ALTER TABLE cers93 ALTER COLUMN nivetu TYPE VARCHAR(5);
ALTER TABLE cers93 ADD CONSTRAINT cers93_nivetu_in_list_chk CHECK ( cakephp_validate_in_list( nivetu, ARRAY['1201a', '1201b', '1202', '1203', '1204', '1205', '1206', '1207'] ) );

-- 20140526: correction, à présent, la structure référente du questionnaire D2 sera celle stockée dans le RDV lié au D1 (lui-même lié au D2)
SELECT alter_table_drop_column_if_exists( 'public', 'questionnairesd2pdvs93', 'structurereferente_id' );

-- *****************************************************************************
COMMIT;
-- *****************************************************************************