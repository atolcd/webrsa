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

-- Nouvelles données dans les flux Pôle Emploi, novembre 2011
SELECT add_missing_table_field ('public', 'historiqueetatspe', 'codeinsee', 'CHAR(5)');
SELECT add_missing_table_field ('public', 'historiqueetatspe', 'localite', 'VARCHAR(26)');
SELECT add_missing_table_field ('public', 'historiqueetatspe', 'adresse', 'VARCHAR(255)');
SELECT add_missing_table_field ('public', 'historiqueetatspe', 'ale', 'CHAR(5)');

-- *****************************************************************************
COMMIT;
-- *****************************************************************************