SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-------------------- Ajout 17/12/2009   ------------------------------------------

CREATE TABLE anomalies (
    id                        SERIAL NOT NULL PRIMARY KEY,
    foyer_id              INTEGER NOT NULL REFERENCES foyers(id),
    libano            VARCHAR(50)
);
CREATE INDEX anomalies_foyer_id_idx ON anomalies (foyer_id);

CREATE TABLE transmissionsflux (
    id                        SERIAL NOT NULL PRIMARY KEY,
    identificationflux_id     INTEGER NOT NULL REFERENCES identificationsflux(id),
    nbtotdemrsatransm         INTEGER,
    nbtotdosrsatransmano      INTEGER
);
CREATE INDEX transmissionsflux_identificationflux_id_idx ON transmissionsflux (identificationflux_id);

-------------------- Ajout 17/12/2009   ------------------------------------------
CREATE TABLE personnes_referents (
     id                         SERIAL NOT NULL PRIMARY KEY,
     personne_id                INTEGER NOT NULL REFERENCES personnes(id),
     referent_id                INTEGER NOT NULL REFERENCES referents(id),
     dddesignation              DATE,
     dfdesignation              DATE
);
CREATE INDEX personnes_referents_personne_id_idx ON personnes_referents (personne_id);
CREATE INDEX personnes_referents_referent_id_idx ON personnes_referents (referent_id);

-------------------- Ajout 21/12/2009   ------------------------------------------
ALTER TABLE statutsrdvs ALTER COLUMN libelle TYPE VARCHAR(50);

ALTER TABLE personnes_referents ADD COLUMN structurereferente_id INTEGER NOT NULL REFERENCES structuresreferentes(id);

-------------------- Ajout 22/12/2009   ------------------------------------------
ALTER TABLE permanences ADD COLUMN compladr VARCHAR(50);

CREATE TYPE type_justificatif AS ENUM ( 'CREA', 'CDT', 'CINS' );
ALTER TABLE apres ADD COLUMN justificatif type_justificatif DEFAULT NULL;
ALTER TABLE tiersprestatairesapres ADD COLUMN aidesliees VARCHAR(50);

-------------------- Ajout 23/12/2009   ------------------------------------------
ALTER TABLE formspermsfimo ADD COLUMN montantconfinanceurs NUMERIC(10,2);
ALTER TABLE formspermsfimo ADD COLUMN montantacompte NUMERIC(10,2);
ALTER TABLE formspermsfimo ADD COLUMN dateversementacompte DATE;
ALTER TABLE formspermsfimo ADD COLUMN montantsolde NUMERIC(10,2);
ALTER TABLE formspermsfimo ADD COLUMN dateversementsolde DATE;
ALTER TABLE formspermsfimo ADD COLUMN montanttotal NUMERIC(10,2);
ALTER TABLE formspermsfimo ADD COLUMN dateversementtotal DATE;

ALTER TABLE formsqualifs ADD COLUMN montantconfinanceurs NUMERIC(10,2);
ALTER TABLE formsqualifs ADD COLUMN montantacompte NUMERIC(10,2);
ALTER TABLE formsqualifs ADD COLUMN dateversementacompte DATE;
ALTER TABLE formsqualifs ADD COLUMN montantsolde NUMERIC(10,2);
ALTER TABLE formsqualifs ADD COLUMN dateversementsolde DATE;
ALTER TABLE formsqualifs ADD COLUMN montanttotal NUMERIC(10,2);
ALTER TABLE formsqualifs ADD COLUMN dateversementtotal DATE;

ALTER TABLE actsprofs ADD COLUMN montantacompte NUMERIC(10,2);
ALTER TABLE actsprofs ADD COLUMN dateversementacompte DATE;
ALTER TABLE actsprofs ADD COLUMN montantsolde NUMERIC(10,2);
ALTER TABLE actsprofs ADD COLUMN dateversementsolde DATE;
ALTER TABLE actsprofs ADD COLUMN montanttotal NUMERIC(10,2);
ALTER TABLE actsprofs ADD COLUMN dateversementtotal DATE;

ALTER TABLE accscreaentr ADD COLUMN montantacompte NUMERIC(10,2);
ALTER TABLE accscreaentr ADD COLUMN dateversementacompte DATE;
ALTER TABLE accscreaentr ADD COLUMN montantsolde NUMERIC(10,2);
ALTER TABLE accscreaentr ADD COLUMN dateversementsolde DATE;
ALTER TABLE accscreaentr ADD COLUMN montanttotal NUMERIC(10,2);
ALTER TABLE accscreaentr ADD COLUMN dateversementtotal DATE;

ALTER TABLE amenagslogts ADD COLUMN montantacompte NUMERIC(10,2);
ALTER TABLE amenagslogts ADD COLUMN dateversementacompte DATE;
ALTER TABLE amenagslogts ADD COLUMN montantsolde NUMERIC(10,2);
ALTER TABLE amenagslogts ADD COLUMN dateversementsolde DATE;
ALTER TABLE amenagslogts ADD COLUMN montanttotal NUMERIC(10,2);
ALTER TABLE amenagslogts ADD COLUMN dateversementtotal DATE;

ALTER TABLE acqsmatsprofs ADD COLUMN montantacompte NUMERIC(10,2);
ALTER TABLE acqsmatsprofs ADD COLUMN dateversementacompte DATE;
ALTER TABLE acqsmatsprofs ADD COLUMN montantsolde NUMERIC(10,2);
ALTER TABLE acqsmatsprofs ADD COLUMN dateversementsolde DATE;
ALTER TABLE acqsmatsprofs ADD COLUMN montanttotal NUMERIC(10,2);
ALTER TABLE acqsmatsprofs ADD COLUMN dateversementtotal DATE;

ALTER TABLE locsvehicinsert ADD COLUMN montantacompte NUMERIC(10,2);
ALTER TABLE locsvehicinsert ADD COLUMN dateversementacompte DATE;
ALTER TABLE locsvehicinsert ADD COLUMN montantsolde NUMERIC(10,2);
ALTER TABLE locsvehicinsert ADD COLUMN dateversementsolde DATE;
ALTER TABLE locsvehicinsert ADD COLUMN montanttotal NUMERIC(10,2);
ALTER TABLE locsvehicinsert ADD COLUMN dateversementtotal DATE;

ALTER TABLE permisb ADD COLUMN montantacompte NUMERIC(10,2);
ALTER TABLE permisb ADD COLUMN dateversementacompte DATE;
ALTER TABLE permisb ADD COLUMN montantsolde NUMERIC(10,2);
ALTER TABLE permisb ADD COLUMN dateversementsolde DATE;
ALTER TABLE permisb ADD COLUMN montanttotal NUMERIC(10,2);
ALTER TABLE permisb ADD COLUMN dateversementtotal DATE;

-------------------- Ajout 24/12/2009   ------------------------------------------
UPDATE typesorients SET parentid = NULL WHERE parentid = id;

-------------------- Ajout 29/12/2009 ---------------------------------------------
ALTER TABLE permisb RENAME COLUMN coutform TO montantaide;