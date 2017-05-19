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

-- INFO: pas de DROP TABLE pour éviter d'effacer accidentellement des données
-- ATTENTION: ce code existe dans les patches SQL 2.9.07 et 2.10.3, ne le passer
-- qu'une fois
CREATE TABLE corpuspdvs93 (
    id						SERIAL NOT NULL PRIMARY KEY,
	tableausuivipdv93_id	INTEGER NOT NULL REFERENCES tableauxsuivispdvs93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	fields					TEXT DEFAULT NULL,
	results					TEXT DEFAULT NULL,
	options					TEXT DEFAULT NULL,
    created					TIMESTAMP WITHOUT TIME ZONE,
    modified				TIMESTAMP WITHOUT TIME ZONE
);

COMMENT ON TABLE corpuspdvs93 IS 'Corpus des tableaux de suivi PDV du CG 93';

DROP INDEX IF EXISTS corpuspdvs93_tableausuivipdv93_id_idx;
CREATE INDEX corpuspdvs93_tableausuivipdv93_id_idx ON corpuspdvs93(tableausuivipdv93_id);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
