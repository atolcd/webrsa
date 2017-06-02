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

-- Création du langage plpgsl s'il n'existe pas
-- INFO: http://andreas.scherbaum.la/blog/archives/346-create-language-if-not-exist.html
CREATE OR REPLACE FUNCTION public.create_plpgsql_language ()
	RETURNS TEXT
	AS $$
		CREATE LANGUAGE plpgsql;
		SELECT 'language plpgsql created'::TEXT;
	$$
LANGUAGE 'sql';

SELECT CASE WHEN
	( SELECT true::BOOLEAN FROM pg_language WHERE lanname='plpgsql')
THEN
	(SELECT 'language already installed'::TEXT)
ELSE
	(SELECT public.create_plpgsql_language())
END;

DROP FUNCTION public.create_plpgsql_language ();

-- *****************************************************************************
-- 21/07/2010: mise à jour pour la V.30 du flux bénéficiaire
-- *****************************************************************************

-- Il est possible que le champ existe déjà:
CREATE OR REPLACE FUNCTION create_field_situationsdossiersrsa_motirefursa() RETURNS VOID AS
$$
BEGIN
	IF NOT EXISTS(
		SELECT *
			FROM pg_namespace n, pg_class c, pg_attribute a
			WHERE
				nspname = 'public'
				AND c.relnamespace = n.oid
				AND a.attrelid = c.oid
				AND relname = 'situationsdossiersrsa'
				AND attname = 'motirefursa'
	)
	THEN
		ALTER TABLE situationsdossiersrsa ADD COLUMN motirefursa CHAR(3);
	END IF;
END;
$$
LANGUAGE 'plpgsql';

SELECT create_field_situationsdossiersrsa_motirefursa();
DROP FUNCTION create_field_situationsdossiersrsa_motirefursa();

-----------------------------------------------------------------------------

-- Il est possible que la table existe déjà:
CREATE OR REPLACE FUNCTION create_table_controlesadministratifs() RETURNS VOID AS
$$
BEGIN
	IF NOT EXISTS(
		SELECT *
			FROM pg_tables
			WHERE schemaname = 'public'
				AND tablename = 'controlesadministratifs'
	)
	THEN
		CREATE TABLE controlesadministratifs (
			id              	SERIAL NOT NULL PRIMARY KEY,
			dteffcibcontro  	DATE,
			cibcontro        	CHAR(3),
			cibcontromsa        CHAR(3),
			dtdeteccontro       DATE,
			dtclocontro        	DATE,
			libcibcontro        VARCHAR(45),
			famcibcontro        CHAR(2),
			natcibcontro        CHAR(3),
			commacontro        	CHAR(3),
			typecontro        	CHAR(2),
			typeimpaccontro     CHAR(1),
			mtindursacgcontro	DECIMAL(11,2),
			mtraprsacgcontro    DECIMAL(11,2)
		);
	END IF;
END;
$$
LANGUAGE 'plpgsql';

SELECT create_table_controlesadministratifs();
DROP FUNCTION create_table_controlesadministratifs();

--

ALTER TABLE tiersprestatairesapres ADD COLUMN nometaban VARCHAR(24);

-----------------------------------------------------------------------------

ALTER TABLE users ADD COLUMN sensibilite type_no DEFAULT NULL;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************