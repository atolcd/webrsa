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

UPDATE actionscandidats SET naturecer = '15' WHERE naturecer = '14';
UPDATE actionscandidats SET naturecer = '14' WHERE naturecer = '13';
UPDATE actionscandidats SET naturecer = '13' WHERE naturecer = '12';
UPDATE actionscandidats SET naturecer = '12' WHERE naturecer = '11';
UPDATE actionscandidats SET naturecer = '11' WHERE naturecer = '10';
UPDATE actionscandidats SET naturecer = '10' WHERE naturecer = '09';
UPDATE actionscandidats SET naturecer = '09' WHERE naturecer = '08';
UPDATE actionscandidats SET naturecer = '08' WHERE naturecer = '07';
UPDATE actionscandidats SET naturecer = '07' WHERE naturecer = '06';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
