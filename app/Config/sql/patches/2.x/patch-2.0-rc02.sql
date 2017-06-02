
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
-- DROP TABLE fraisdeplacements66;
CREATE TABLE fraisdeplacements66 (
    id                          SERIAL NOT NULL PRIMARY KEY,
    aideapre66_id               INTEGER NOT NULL REFERENCES aidesapres66(id),
    lieuresidence               VARCHAR(100),
    destination                 VARCHAR(100),
    --partie véhicule personnel
    nbkmvoiture                 DECIMAL(10,2),
    nbtrajetvoiture             DECIMAL(10,2),
    nbtotalkm                   DECIMAL(10,2),
    totalvehicule               DECIMAL(10,2),
    --partie transport public
    nbtrajettranspub            DECIMAL(10,2),
    prixbillettranspub          DECIMAL(10,2),
    totaltranspub               DECIMAL(10,2),
    --partie hébergement
    nbnuithebergt               DECIMAL(10,2),
    totalhebergt                DECIMAL(10,2),
    -- partie repas
    nbrepas                     DECIMAL(10,2),
    totalrepas                  DECIMAL(10,2)
);

CREATE INDEX fraisdeplacements66_aideapre66_id_idx ON fraisdeplacements66 (aideapre66_id);
COMMENT ON TABLE fraisdeplacements66 IS 'Table pour les frais de déplacements liés à l''APRE CG66';
-- -----------------------------------------------------------------------------

ALTER TABLE aidesapres66 ADD COLUMN motifrejet TEXT;
ALTER TABLE aidesapres66 ADD COLUMN montantpropose DECIMAL(10,2);
ALTER TABLE aidesapres66 ADD COLUMN datemontantpropose DATE;

CREATE TYPE type_decisionapre AS ENUM ( 'ACC', 'REF' );
ALTER TABLE aidesapres66 ADD COLUMN decisionapre type_decisionapre;
ALTER TABLE aidesapres66 ADD COLUMN montantaccorde DECIMAL(10,2);
ALTER TABLE aidesapres66 ADD COLUMN datemontantaccorde DATE;

ALTER TABLE aidesapres66 ADD COLUMN creancier VARCHAR (250);

COMMIT;
-- *****************************************************************************
--      Passage Cristal version 28 - @rSa version 3.2  - 20100423 09h56
-- *****************************************************************************

-- -----------------------------------------------------------------------------
--          modifs table allocationssoutienfamilial
-- -----------------------------------------------------------------------------
BEGIN;
-- -----------------------------------------------------------------------------
--          modifs table accscreaentr
-- -----------------------------------------------------------------------------

ALTER TABLE accscreaentr DROP COLUMN montantacompte;
ALTER TABLE accscreaentr DROP COLUMN dateversementacompte;
ALTER TABLE accscreaentr DROP COLUMN montantsolde;
ALTER TABLE accscreaentr DROP COLUMN dateversementsolde;
ALTER TABLE accscreaentr DROP COLUMN montanttotal;
ALTER TABLE accscreaentr DROP COLUMN dateversementtotal;

-- -----------------------------------------------------------------------------
--          modifs table acqsmatsprofs
-- -----------------------------------------------------------------------------

ALTER TABLE acqsmatsprofs DROP COLUMN montantacompte;
ALTER TABLE acqsmatsprofs DROP COLUMN dateversementacompte;
ALTER TABLE acqsmatsprofs DROP COLUMN montantsolde;
ALTER TABLE acqsmatsprofs DROP COLUMN dateversementsolde;
ALTER TABLE acqsmatsprofs DROP COLUMN montanttotal;
ALTER TABLE acqsmatsprofs DROP COLUMN dateversementtotal;

-- -----------------------------------------------------------------------------
--          modifs table actsprofs
-- -----------------------------------------------------------------------------

ALTER TABLE actsprofs DROP COLUMN montantacompte;
ALTER TABLE actsprofs DROP COLUMN dateversementacompte;
ALTER TABLE actsprofs DROP COLUMN montantsolde;
ALTER TABLE actsprofs DROP COLUMN dateversementsolde;
ALTER TABLE actsprofs DROP COLUMN montanttotal;
ALTER TABLE actsprofs DROP COLUMN dateversementtotal;

-- -----------------------------------------------------------------------------
--          modifs table amenagslogts
-- -----------------------------------------------------------------------------

ALTER TABLE amenagslogts DROP COLUMN montantacompte;
ALTER TABLE amenagslogts DROP COLUMN dateversementacompte;
ALTER TABLE amenagslogts DROP COLUMN montantsolde;
ALTER TABLE amenagslogts DROP COLUMN dateversementsolde;
ALTER TABLE amenagslogts DROP COLUMN montanttotal;
ALTER TABLE amenagslogts DROP COLUMN dateversementtotal;

-- -----------------------------------------------------------------------------
--          modifs table formspermsfimo
-- -----------------------------------------------------------------------------

ALTER TABLE formspermsfimo DROP COLUMN montantacompte;
ALTER TABLE formspermsfimo DROP COLUMN dateversementacompte;
ALTER TABLE formspermsfimo DROP COLUMN montantsolde;
ALTER TABLE formspermsfimo DROP COLUMN dateversementsolde;
ALTER TABLE formspermsfimo DROP COLUMN montanttotal;
ALTER TABLE formspermsfimo DROP COLUMN dateversementtotal;

-- -----------------------------------------------------------------------------
--          modifs table formsqualifs
-- -----------------------------------------------------------------------------

ALTER TABLE formsqualifs DROP COLUMN montantacompte;
ALTER TABLE formsqualifs DROP COLUMN dateversementacompte;
ALTER TABLE formsqualifs DROP COLUMN montantsolde;
ALTER TABLE formsqualifs DROP COLUMN dateversementsolde;
ALTER TABLE formsqualifs DROP COLUMN montanttotal;
ALTER TABLE formsqualifs DROP COLUMN dateversementtotal;

-- -----------------------------------------------------------------------------
--          modifs table locsvehicinsert
-- -----------------------------------------------------------------------------

ALTER TABLE locsvehicinsert DROP COLUMN montantacompte;
ALTER TABLE locsvehicinsert DROP COLUMN dateversementacompte;
ALTER TABLE locsvehicinsert DROP COLUMN montantsolde;
ALTER TABLE locsvehicinsert DROP COLUMN dateversementsolde;
ALTER TABLE locsvehicinsert DROP COLUMN montanttotal;
ALTER TABLE locsvehicinsert DROP COLUMN dateversementtotal;

-- -----------------------------------------------------------------------------
--          modifs table permisb
-- -----------------------------------------------------------------------------

ALTER TABLE permisb DROP COLUMN montantacompte;
ALTER TABLE permisb DROP COLUMN dateversementacompte;
ALTER TABLE permisb DROP COLUMN montantsolde;
ALTER TABLE permisb DROP COLUMN dateversementsolde;
ALTER TABLE permisb DROP COLUMN montanttotal;
ALTER TABLE permisb DROP COLUMN dateversementtotal;


ALTER TABLE allocationssoutienfamilial ADD COLUMN topasf type_booleannumber ;
ALTER TABLE allocationssoutienfamilial ADD COLUMN topdemasf type_booleannumber ;
ALTER TABLE allocationssoutienfamilial ADD COLUMN topenfreconn type_booleannumber ;

-- -----------------------------------------------------------------------------
--          modif table foyers
-- -----------------------------------------------------------------------------

ALTER TABLE foyers ADD COLUMN raisoctieelectdom VARCHAR(32);

-- -----------------------------------------------------------------------------
--          modifs table adresses
-- -----------------------------------------------------------------------------

ALTER TABLE adresses ADD COLUMN typeres  CHAR (1);
ALTER TABLE adresses ADD COLUMN topresetr type_booleannumber;

-- -----------------------------------------------------------------------------
--          modifs table rattachements
-- -----------------------------------------------------------------------------
DROP TABLE rattachements;

CREATE TABLE rattachements (
    id                      SERIAL NOT NULL PRIMARY KEY,
    personne_id             INTEGER NOT NULL REFERENCES personnes(id),
    nomnai                  VARCHAR(28),
    prenom                  VARCHAR(32),
    typepar                 CHAR(3),
    dtnai                   DATE,
    nir                     CHAR(15)
);
-- ALTER TABLE rattachements ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;
-- ALTER TABLE rattachements ADD COLUMN nomnai VARCHAR(28);
-- ALTER TABLE rattachements ADD COLUMN prenom VARCHAR(32);
-- ALTER TABLE rattachements ADD COLUMN dtnai DATE;
-- ALTER TABLE rattachements ADD COLUMN nir CHAR(15);
-- ALTER TABLE rattachements DROP COLUMN rattache_id;


-- *****************************************************************************
--      Passage Cristal version 29 - @rSa version 3.3  - 20100305 17h40
-- *****************************************************************************
-- -----------------------------------------------------------------------------
--          modif table transmissionsflux
-- -----------------------------------------------------------------------------

ALTER TABLE transmissionsflux ADD COLUMN nbtotdosrsatransm INTEGER DEFAULT NULL;


COMMIT;