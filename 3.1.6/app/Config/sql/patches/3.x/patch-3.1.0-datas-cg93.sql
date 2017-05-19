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

INSERT INTO communautessrs ( name, actif, created, modified ) VALUES
	( 'Plaine commune', '1', NOW(), NOW() );

INSERT INTO communautessrs_structuresreferentes ( communautesr_id, structurereferente_id ) VALUES
	( 1, 1 ),
	( 1, 19 ),
	( 1, 63 ),
	( 1, 24 ),
	( 1, 29 ),
	( 1, 67 ),
	( 1, 68 ),
	( 1, 70 ),
	( 1, 32 );

UPDATE users SET referent_id = NULL, structurereferente_id = NULL, type = 'externe_cpdvcom', communautesr_id = 1 WHERE id = 394;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
