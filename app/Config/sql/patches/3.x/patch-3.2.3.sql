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

DROP TABLE IF EXISTS actionrolesresultsusers;
CREATE TABLE actionrolesresultsusers (
	id				SERIAL NOT NULL PRIMARY KEY,
	actionrole_id	INTEGER NOT NULL REFERENCES actionroles(id) ON DELETE CASCADE ON UPDATE CASCADE,
	user_id			INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
	results			INTEGER DEFAULT NULL,
	created			TIMESTAMP WITHOUT TIME ZONE,
	modified		TIMESTAMP WITHOUT TIME ZONE
);

CREATE UNIQUE INDEX actionrolesresultsusers_actionrole_id_user_id_idx ON actionrolesresultsusers (actionrole_id, user_id);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************