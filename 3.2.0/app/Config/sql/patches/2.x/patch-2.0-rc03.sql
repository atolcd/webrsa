
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

BEGIN;

ALTER TABLE typesaidesapres66 ADD COLUMN objetaide TEXT;
ALTER TABLE apres ADD COLUMN dureecontrat   VARCHAR(50);

-- --------------------------------------------------------------------------------

COMMIT;