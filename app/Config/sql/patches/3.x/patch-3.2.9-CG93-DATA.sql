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

ALTER TABLE structuresreferentes ALTER COLUMN lib_struc TYPE VARCHAR(150);

UPDATE structuresreferentes
  SET lib_struc= ( REPLACE(lib_struc, 'Projet de Ville RSA', 'Projet Insertion Emploi' ) )
  WHERE id IN (
	SELECT id
	FROM structuresreferentes
	WHERE lib_struc LIKE '%Projet de Ville RSA%'
  ) ;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
