
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

BEGIN;

-- -----------------------------------------------------------------------------


/*CREATE TABLE modeles (
    id                      SERIAL NOT NULL PRIMARY KEY,
    modele                  VARCHAR(100) NOT NULL,
    type                    VARCHAR(100) NOT NULL,
    name                    VARCHAR(255) DEFAULT NULL,
    size                    INTEGER NOT NULL,
    extension               VARCHAR(255) DEFAULT NULL,
    content                 BYTEA NOT NULL
);

ALTER TABLE orientsstructs ADD COLUMN modele_pdf BYTEA;*/

CREATE TABLE pdfs (
	id			SERIAL NOT NULL PRIMARY KEY,
	modele		VARCHAR(250) NOT NULL,
	modeledoc	VARCHAR(250) NOT NULL,
	fk_value	INTEGER NOT NULL,
	document	BYTEA NOT NULL
);

-- ( 1, 'proposition_orientation_vers_SS_ou_PDV', 84057, '...' )

CREATE INDEX pdfs_modele_idx ON pdfs (modele);
CREATE INDEX pdfs_modeledoc_idx ON pdfs (modeledoc);
CREATE INDEX pdfs_fk_value_idx ON pdfs (fk_value);


-- -----------------------------------------------------------------------------

-- Transformation evenements de m-n en 0-n
ALTER TABLE evenements ADD COLUMN foyer_id INTEGER REFERENCES foyers(id) DEFAULT NULL;
UPDATE evenements
    SET foyer_id = (
        SELECT foyers_evenements.foyer_id
            FROM foyers_evenements
            WHERE foyers_evenements.evenement_id = evenements.id
    );
DROP TABLE foyers_evenements;
ALTER TABLE evenements ALTER COLUMN foyer_id SET NOT NULL;


-- Transformation creances de m-n en 0-n
ALTER TABLE creances ADD COLUMN foyer_id INTEGER REFERENCES foyers(id) DEFAULT NULL;
UPDATE creances
    SET foyer_id = (
        SELECT foyers_creances.foyer_id
            FROM foyers_creances
            WHERE foyers_creances.creance_id = creances.id
    );
DROP TABLE foyers_creances;
ALTER TABLE creances ALTER COLUMN foyer_id SET NOT NULL;


ALTER TABLE infosfinancieres ALTER COLUMN heutraimoucompta TYPE timestamp with time zone;
ALTER TABLE infosfinancieres ALTER COLUMN heutraimoucompta TYPE time;

-- -----------------------------------------------------------------------------
-- 27/04/2010 @ 10h35
ALTER TABLE pdfs ADD COLUMN created TIMESTAMP WITH TIME ZONE;
ALTER TABLE pdfs ADD COLUMN modified TIMESTAMP WITH TIME ZONE;

-- -----------------------------------------------------------------------------

COMMIT;