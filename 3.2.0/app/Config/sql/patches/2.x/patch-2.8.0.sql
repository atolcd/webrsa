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

--------------------------------------------------------------------------------
-- Nettoyage de la version de développement
--------------------------------------------------------------------------------
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps', 'deractfamilleromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps', 'deractdomaineromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps', 'deractmetierromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps', 'deractappellationromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps', 'deractdomifamilleromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps', 'deractdomidomaineromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps', 'deractdomimetierromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps', 'deractdomiappellationromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps', 'actrechfamilleromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps', 'actrechdomaineromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps', 'actrechmetierromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps', 'actrechappellationromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps_revs', 'deractfamilleromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps_revs', 'deractdomaineromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps_revs', 'deractmetierromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps_revs', 'deractappellationromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps_revs', 'deractdomifamilleromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps_revs', 'deractdomidomaineromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps_revs', 'deractdomimetierromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps_revs', 'deractdomiappellationromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps_revs', 'actrechfamilleromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps_revs', 'actrechdomaineromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps_revs', 'actrechmetierromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps_revs', 'actrechappellationromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps', 'deractromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps', 'deractdomiromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps', 'actrechromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps_revs', 'deractromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps_revs', 'deractdomiromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'dsps_revs', 'actrechromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'partenaires', 'entreeromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'cuis', 'emploiproposeromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'periodesimmersioncuis66', 'affectationromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'personnespcgs66', 'categorieromev3_id' );
SELECT alter_table_drop_column_if_exists ( 'public', 'expsproscers93', 'entreeromev3_id' );

--------------------------------------------------------------------------------
-- Nettoyage des anciennes tables des codes ROME V3
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS codesappellationsromev3;
DROP TABLE IF EXISTS codesmetiersromev3;
DROP TABLE IF EXISTS codesdomainesprosromev3;
DROP TABLE IF EXISTS codesfamillesromev3;

--------------------------------------------------------------------------------
-- Codes familles ROME V3
--------------------------------------------------------------------------------
DROP TABLE IF EXISTS famillesromesv3 CASCADE;
CREATE TABLE famillesromesv3 (
    id          SERIAL NOT NULL PRIMARY KEY,
    code        VARCHAR(1) NOT NULL,
    name        VARCHAR(255) NOT NULL,
    created     TIMESTAMP WITHOUT TIME ZONE,
    modified    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE famillesromesv3 IS 'Codes ROME V3 - Codes familles';

CREATE UNIQUE INDEX famillesromesv3_code_idx ON famillesromesv3 (code);
CREATE UNIQUE INDEX famillesromesv3_name_idx ON famillesromesv3 (name);

--------------------------------------------------------------------------------
-- Codes domaines ROME V3
--------------------------------------------------------------------------------
DROP TABLE IF EXISTS domainesromesv3 CASCADE;
CREATE TABLE domainesromesv3 (
    id					SERIAL NOT NULL PRIMARY KEY,
	familleromev3_id	INTEGER NOT NULL REFERENCES famillesromesv3(id) ON DELETE CASCADE ON UPDATE CASCADE,
    code				VARCHAR(2) NOT NULL,
    name				VARCHAR(255) NOT NULL,
    created				TIMESTAMP WITHOUT TIME ZONE,
    modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE domainesromesv3 IS 'Codes ROME V3 - Codes domaines';

CREATE INDEX domainesromesv3_familleromev3_id_idx ON domainesromesv3 (familleromev3_id);
CREATE UNIQUE INDEX domainesromesv3_familleromev3_id_code_idx ON domainesromesv3 (familleromev3_id, code);
CREATE UNIQUE INDEX domainesromesv3_familleromev3_id_name_idx ON domainesromesv3 (familleromev3_id, name);

--------------------------------------------------------------------------------
-- Codes métiers ROME V3
--------------------------------------------------------------------------------
DROP TABLE IF EXISTS metiersromesv3 CASCADE;
CREATE TABLE metiersromesv3 (
    id					SERIAL NOT NULL PRIMARY KEY,
	domaineromev3_id	INTEGER NOT NULL REFERENCES domainesromesv3(id) ON DELETE CASCADE ON UPDATE CASCADE,
    code				VARCHAR(2) NOT NULL,
    name				VARCHAR(255) NOT NULL,
    created				TIMESTAMP WITHOUT TIME ZONE,
    modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE metiersromesv3 IS 'Codes ROME V3 - Codes métiers';

CREATE INDEX metiersromesv3_domaineromev3_id_idx ON metiersromesv3 (domaineromev3_id);
CREATE UNIQUE INDEX metiersromesv3_domaineromev3_id_code_idx ON metiersromesv3 (domaineromev3_id, code);
CREATE UNIQUE INDEX metiersromesv3_domaineromev3_id_name_idx ON metiersromesv3 (domaineromev3_id, name);

--------------------------------------------------------------------------------
-- Appellations ROME V3
--------------------------------------------------------------------------------
DROP TABLE IF EXISTS appellationsromesv3 CASCADE;
CREATE TABLE appellationsromesv3 (
    id					SERIAL NOT NULL PRIMARY KEY,
	metierromev3_id		INTEGER NOT NULL REFERENCES metiersromesv3(id) ON DELETE CASCADE ON UPDATE CASCADE,
    name				VARCHAR(255) NOT NULL,
    created				TIMESTAMP WITHOUT TIME ZONE,
    modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE appellationsromesv3 IS 'Codes ROME V3 - Appellations';

CREATE INDEX appellationsromesv3_metierromev3_id_idx ON appellationsromesv3 (metierromev3_id);
CREATE UNIQUE INDEX appellationsromesv3_metierromev3_id_name_idx ON appellationsromesv3 (metierromev3_id, name);
CREATE INDEX appellationsromesv3_metierromev3_id_name_noaccents_upper ON appellationsromesv3( NOACCENTS_UPPER( name ) );

--------------------------------------------------------------------------------
-- Correspondances ROME V2 <-> ROME V3
--------------------------------------------------------------------------------
DROP TABLE IF EXISTS correspondancesromesv2v3 CASCADE;
CREATE TABLE correspondancesromesv2v3 (
    id						SERIAL NOT NULL PRIMARY KEY,
	coderomemetierdsp66_id	INTEGER NOT NULL REFERENCES codesromemetiersdsps66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	appellationromev2		VARCHAR(255) NOT NULL,
	metierromev3_id			INTEGER NOT NULL REFERENCES metiersromesv3(id) ON DELETE CASCADE ON UPDATE CASCADE,
	appellationromev3_id	INTEGER NOT NULL REFERENCES appellationsromesv3(id) ON DELETE CASCADE ON UPDATE CASCADE
);
COMMENT ON TABLE correspondancesromesv2v3 IS 'Codes ROME V3 - Correspondances entre les codes ROME V2 et V3';

--------------------------------------------------------------------------------
-- Table entreesromesv3
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS entreesromesv3 CASCADE;
CREATE TABLE entreesromesv3 (
    id						SERIAL NOT NULL PRIMARY KEY,
	familleromev3_id		INTEGER NOT NULL REFERENCES famillesromesv3(id) ON DELETE SET NULL ON UPDATE CASCADE,
	domaineromev3_id		INTEGER DEFAULT NULL REFERENCES domainesromesv3(id) ON DELETE SET NULL ON UPDATE CASCADE,
	metierromev3_id			INTEGER DEFAULT NULL REFERENCES metiersromesv3(id) ON DELETE SET NULL ON UPDATE CASCADE,
	appellationromev3_id	INTEGER DEFAULT NULL REFERENCES appellationsromesv3(id) ON DELETE SET NULL ON UPDATE CASCADE,
    created					TIMESTAMP WITHOUT TIME ZONE,
    modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE entreesromesv3 IS 'Codes ROME V3 - liste des 4 niveaux liés à un enregistrement';

DROP INDEX IF EXISTS entreesromesv3_familleromev3_id_idx;
CREATE INDEX entreesromesv3_familleromev3_id_idx ON entreesromesv3(familleromev3_id);

DROP INDEX IF EXISTS entreesromesv3_domaineromev3_id_idx;
CREATE INDEX entreesromesv3_domaineromev3_id_idx ON entreesromesv3(domaineromev3_id);

DROP INDEX IF EXISTS entreesromesv3_metierromev3_id_idx;
CREATE INDEX entreesromesv3_metierromev3_id_idx ON entreesromesv3(metierromev3_id);

DROP INDEX IF EXISTS entreesromesv3_appellationromev3_id_idx;
CREATE INDEX entreesromesv3_appellationromev3_id_idx ON entreesromesv3(appellationromev3_id);

ALTER TABLE entreesromesv3 ADD CONSTRAINT entreesromesv3_appellationromev3_id_metierromev3_id_not_null_chk
	CHECK( appellationromev3_id IS NULL OR metierromev3_id IS NOT NULL );

ALTER TABLE entreesromesv3 ADD CONSTRAINT entreesromesv3_metierromev3_id_domaineromev3_id_not_null_chk
	CHECK( metierromev3_id IS NULL OR domaineromev3_id IS NOT NULL );

ALTER TABLE entreesromesv3 ADD CONSTRAINT entreesromesv3_domaineromev3_id_familleromev3_id_not_null_chk
	CHECK( domaineromev3_id IS NULL OR familleromev3_id IS NOT NULL );

--------------------------------------------------------------------------------
-- 1. Ajout dans les DSP
--------------------------------------------------------------------------------
-- 1.1 Dernière activité
SELECT add_missing_table_field ( 'public', 'dsps', 'deractromev3_id', 'INTEGER' );
ALTER TABLE dsps ALTER COLUMN deractromev3_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'dsps', 'dsps_deractromev3_id_fkey', 'entreesromesv3', 'deractromev3_id', false );
DROP INDEX IF EXISTS dsps_deractromev3_id_idx;
CREATE UNIQUE INDEX dsps_deractromev3_id_idx ON dsps(deractromev3_id);

-- 1.2 Dernière activité dominante
SELECT add_missing_table_field ( 'public', 'dsps', 'deractdomiromev3_id', 'INTEGER' );
ALTER TABLE dsps ALTER COLUMN deractdomiromev3_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'dsps', 'dsps_deractdomiromev3_id_fkey', 'entreesromesv3', 'deractdomiromev3_id', false );
DROP INDEX IF EXISTS dsps_deractdomiromev3_id_idx;
CREATE UNIQUE INDEX dsps_deractdomiromev3_id_idx ON dsps(deractdomiromev3_id);

-- 1.3 Emploi recherché
SELECT add_missing_table_field ( 'public', 'dsps', 'actrechromev3_id', 'INTEGER' );
ALTER TABLE dsps ALTER COLUMN actrechromev3_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'dsps', 'dsps_actrechromev3_id_fkey', 'entreesromesv3', 'actrechromev3_id', false );
DROP INDEX IF EXISTS dsps_actrechromev3_id_idx;
CREATE UNIQUE INDEX dsps_actrechromev3_id_idx ON dsps(actrechromev3_id);

--------------------------------------------------------------------------------
-- 2. Ajout dans les DSP CG
--------------------------------------------------------------------------------
-- 1.1 Dernière activité
SELECT add_missing_table_field ( 'public', 'dsps_revs', 'deractromev3_id', 'INTEGER' );
ALTER TABLE dsps_revs ALTER COLUMN deractromev3_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'dsps_revs', 'dsps_revs_deractromev3_id_fkey', 'entreesromesv3', 'deractromev3_id', false );
DROP INDEX IF EXISTS dsps_revs_deractromev3_id_idx;
CREATE UNIQUE INDEX dsps_revs_deractromev3_id_idx ON dsps_revs(deractromev3_id);

-- 1.2 Dernière activité dominante
SELECT add_missing_table_field ( 'public', 'dsps_revs', 'deractdomiromev3_id', 'INTEGER' );
ALTER TABLE dsps_revs ALTER COLUMN deractdomiromev3_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'dsps_revs', 'dsps_revs_deractdomiromev3_id_fkey', 'entreesromesv3', 'deractdomiromev3_id', false );
DROP INDEX IF EXISTS dsps_revs_deractdomiromev3_id_idx;
CREATE UNIQUE INDEX dsps_revs_deractdomiromev3_id_idx ON dsps_revs(deractdomiromev3_id);

-- 1.3 Emploi recherché
SELECT add_missing_table_field ( 'public', 'dsps_revs', 'actrechromev3_id', 'INTEGER' );
ALTER TABLE dsps_revs ALTER COLUMN actrechromev3_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'dsps_revs', 'dsps_revs_actrechromev3_id_fkey', 'entreesromesv3', 'actrechromev3_id', false );
DROP INDEX IF EXISTS dsps_revs_actrechromev3_id_idx;
CREATE UNIQUE INDEX dsps_revs_actrechromev3_id_idx ON dsps_revs(actrechromev3_id);

/*--------------------------------------------------------------------------------
-- 1. CG 66
--------------------------------------------------------------------------------
SELECT add_missing_table_field ( 'public', 'partenaires', 'entreeromev3_id', 'INTEGER' );
ALTER TABLE partenaires ALTER COLUMN entreeromev3_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'partenaires', 'partenaires_entreeromev3_id_fkey', 'entreesromesv3', 'entreeromev3_id', false );
DROP INDEX IF EXISTS partenaires_entreeromev3_id_idx;
CREATE UNIQUE INDEX partenaires_entreeromev3_id_idx ON partenaires(entreeromev3_id);

SELECT add_missing_table_field ( 'public', 'cuis', 'emploiproposeromev3_id', 'INTEGER' );
ALTER TABLE cuis ALTER COLUMN emploiproposeromev3_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'cuis', 'cuis_emploiproposeromev3_id_fkey', 'entreesromesv3', 'emploiproposeromev3_id', false );
DROP INDEX IF EXISTS cuis_emploiproposeromev3_id_idx;
CREATE UNIQUE INDEX cuis_emploiproposeromev3_id_idx ON cuis(emploiproposeromev3_id);

SELECT add_missing_table_field ( 'public', 'periodesimmersioncuis66', 'affectationromev3_id', 'INTEGER' );
ALTER TABLE  periodesimmersioncuis66 ALTER COLUMN affectationromev3_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'periodesimmersioncuis66', 'periodesimmersioncuis66_affectationromev3_id_fkey', 'entreesromesv3', 'affectationromev3_id', false );
DROP INDEX IF EXISTS  periodesimmersioncuis66_affectationromev3_id_idx;
CREATE UNIQUE INDEX  periodesimmersioncuis66_affectationromev3_id_idx ON  periodesimmersioncuis66(affectationromev3_id);
*/
SELECT add_missing_table_field ( 'public', 'personnespcgs66', 'categorieromev3_id', 'INTEGER' );
ALTER TABLE  personnespcgs66 ALTER COLUMN categorieromev3_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'personnespcgs66', 'personnespcgs66_categorieromev3_id_fkey', 'entreesromesv3', 'categorieromev3_id', false );
DROP INDEX IF EXISTS  personnespcgs66_categorieromev3_id_idx;
CREATE UNIQUE INDEX  personnespcgs66_categorieromev3_id_idx ON  personnespcgs66(categorieromev3_id);

--------------------------------------------------------------------------------
-- 2. CG 93, Tableau "Expériences professionnelles significatives" du CER
--------------------------------------------------------------------------------

SELECT add_missing_table_field ( 'public', 'expsproscers93', 'entreeromev3_id', 'INTEGER' );
ALTER TABLE expsproscers93 ALTER COLUMN entreeromev3_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'expsproscers93', 'expsproscers93_entreeromev3_id_fkey', 'entreesromesv3', 'entreeromev3_id', false );
DROP INDEX IF EXISTS expsproscers93_entreeromev3_id_idx;
CREATE UNIQUE INDEX expsproscers93_entreeromev3_id_idx ON expsproscers93(entreeromev3_id);

-- Les anciennes colonnes (INSEE) doivent à présent accepter les valeurs NULL
ALTER TABLE expsproscers93 ALTER COLUMN metierexerce_id DROP NOT NULL;
ALTER TABLE expsproscers93 ALTER COLUMN metierexerce_id SET DEFAULT NULL;
ALTER TABLE expsproscers93 ALTER COLUMN secteuracti_id DROP NOT NULL;
ALTER TABLE expsproscers93 ALTER COLUMN secteuracti_id SET DEFAULT NULL;

--------------------------------------------------------------------------------
-- 3. CG 93, Partie "Avez-vous trouvé un emploi ?" > "Si oui, veuillez préciser " du CER
--------------------------------------------------------------------------------

SELECT add_missing_table_field ( 'public', 'cers93', 'emptrouvromev3_id', 'INTEGER' );
ALTER TABLE cers93 ALTER COLUMN emptrouvromev3_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'cers93', 'cers93_emptrouvromev3_id_fkey', 'entreesromesv3', 'emptrouvromev3_id', false );
DROP INDEX IF EXISTS cers93_emptrouvromev3_id_idx;
CREATE UNIQUE INDEX cers93_emptrouvromev3_id_idx ON cers93(emptrouvromev3_id);

--------------------------------------------------------------------------------
-- 4. CG 93, Partie "Votre contrat porte sur" > logiquement lorsque l'on sélectionne "L'Emploi" du CER
--------------------------------------------------------------------------------

SELECT add_missing_table_field ( 'public', 'cers93', 'sujetromev3_id', 'INTEGER' );
ALTER TABLE cers93 ALTER COLUMN sujetromev3_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'cers93', 'cers93_sujetromev3_id_fkey', 'entreesromesv3', 'sujetromev3_id', false );
DROP INDEX IF EXISTS cers93_sujetromev3_id_idx;
CREATE UNIQUE INDEX cers93_sujetromev3_id_idx ON cers93(sujetromev3_id);

--------------------------------------------------------------------------------
-- 20141215: CG 66, matérialisation des liaisons entre personnespcgs66 et les
-- codes ROME V2
--------------------------------------------------------------------------------

ALTER TABLE personnespcgs66 ALTER COLUMN categoriegeneral DROP DEFAULT;
ALTER TABLE personnespcgs66 ALTER COLUMN categoriegeneral TYPE INTEGER USING CAST(categoriegeneral AS INTEGER);
SELECT alter_table_drop_constraint_if_exists( 'public', 'personnespcgs66', 'personnespcgs66_categoriegeneral_fk' );
ALTER TABLE personnespcgs66 ADD CONSTRAINT personnespcgs66_categoriegeneral_fk FOREIGN KEY (categoriegeneral) REFERENCES codesromesecteursdsps66(id) ON UPDATE CASCADE ON DELETE SET NULL;
ALTER TABLE personnespcgs66 ALTER COLUMN categoriegeneral SET DEFAULT NULL;

ALTER TABLE personnespcgs66 ALTER COLUMN categoriedetail DROP DEFAULT;
ALTER TABLE personnespcgs66 ALTER COLUMN categoriedetail TYPE INTEGER USING CAST(categoriedetail AS INTEGER);
SELECT alter_table_drop_constraint_if_exists( 'public', 'personnespcgs66', 'personnespcgs66_categoriedetail_fk' );
ALTER TABLE personnespcgs66 ADD CONSTRAINT personnespcgs66_categoriedetail_fk FOREIGN KEY (categoriedetail) REFERENCES codesromemetiersdsps66(id) ON UPDATE CASCADE ON DELETE SET NULL;
ALTER TABLE personnespcgs66 ALTER COLUMN categoriedetail SET DEFAULT NULL;

--------------------------------------------------------------------------------
-- 20141222: CG 93, ajout de colonnes concernant l'annulation de CER
--------------------------------------------------------------------------------

SELECT add_missing_table_field ( 'public', 'cers93', 'date_annulation', 'DATE' );
ALTER TABLE  cers93 ALTER COLUMN date_annulation SET DEFAULT NULL;

SELECT add_missing_table_field ( 'public', 'cers93', 'annulateur_id', 'INTEGER' );
ALTER TABLE cers93 ALTER COLUMN annulateur_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'cers93', 'cers93_annulateur_id_fkey', 'users', 'annulateur_id', false );
DROP INDEX IF EXISTS  cers93_annulateur_id_idx;
CREATE INDEX cers93_annulateur_id_idx ON cers93(annulateur_id);

SELECT public.alter_table_drop_constraint_if_exists( 'public', 'cers93', 'cers93_positioncer_in_list_chk' );
ALTER TABLE cers93 ADD CONSTRAINT cers93_positioncer_in_list_chk CHECK ( cakephp_validate_in_list( positioncer, ARRAY['00enregistre', '01signe', '02attdecisioncpdv', '03attdecisioncg', '99rejetecpdv', '04premierelecture', '05secondelecture', '07attavisep', '99rejete', '99valide', '99annule'] ) );

UPDATE cers93 SET observationdecision = NULL WHERE observationdecision IS NOT NULL AND ( TRIM( BOTH ' ' FROM observationdecision ) = '' );

SELECT alter_table_drop_constraint_if_exists( 'public', 'cers93', 'cers93_positioncer_observationdecision_chk' );
ALTER TABLE cers93 ADD CONSTRAINT cers93_positioncer_observationdecision_chk CHECK (observationdecision IS NULL OR positioncer IN( '99valide', '99rejete', '99annule' ) );

--------------------------------------------------------------------------------
-- 20150129: CG 66, ajout d'une valeur d'enum pour les decisions EP
--------------------------------------------------------------------------------

SELECT alter_enumtype('TYPE_DECISIONDEFAUTINSERTIONEP66', ARRAY['suspensionnonrespect', 'suspensiondefaut', 'maintien', 'maintienorientsoc', 'reorientationprofverssoc', 'reorientationsocversprof', 'annule', 'reporte']);

--------------------------------------------------------------------------------
-- 20150223: CG 93, ajout de la colonne "Nature du contrat" dans les "Expériences
-- professionnelles significatives"
--------------------------------------------------------------------------------

SELECT add_missing_table_field ( 'public', 'expsproscers93', 'naturecontrat_id', 'INTEGER' );
ALTER TABLE expsproscers93 ALTER COLUMN naturecontrat_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'expsproscers93', 'expsproscers93_naturecontrat_id_fkey', 'naturescontrats', 'naturecontrat_id', false );

--------------------------------------------------------------------------------
-- 20150302: CG 93, ajout de la possibilité d'activer ou de désactiver les sujets,
-- ... du CER
--------------------------------------------------------------------------------

-- Pour sujetscers93
SELECT add_missing_table_field ( 'public', 'sujetscers93', 'actif', 'CHAR(1)' );
ALTER TABLE sujetscers93 ALTER COLUMN actif SET DEFAULT '1';
UPDATE sujetscers93 SET actif = '1' WHERE actif IS NULL;
ALTER TABLE sujetscers93 ALTER COLUMN actif SET NOT NULL;
SELECT public.alter_table_drop_constraint_if_exists( 'public', 'sujetscers93', 'sujetscers93_actif_in_list_chk' );
ALTER TABLE sujetscers93 ADD CONSTRAINT sujetscers93_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY['0','1'] ) );
DROP INDEX IF EXISTS sujetscers93_actif_idx;
CREATE INDEX sujetscers93_actif_idx ON sujetscers93(actif);

-- Pour soussujetscers93
SELECT add_missing_table_field ( 'public', 'soussujetscers93', 'actif', 'CHAR(1)' );
ALTER TABLE soussujetscers93 ALTER COLUMN actif SET DEFAULT '1';
UPDATE soussujetscers93 SET actif = '1' WHERE actif IS NULL;
ALTER TABLE soussujetscers93 ALTER COLUMN actif SET NOT NULL;
SELECT public.alter_table_drop_constraint_if_exists( 'public', 'soussujetscers93', 'soussujetscers93_actif_in_list_chk' );
ALTER TABLE soussujetscers93 ADD CONSTRAINT soussujetscers93_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY['0','1'] ) );
DROP INDEX IF EXISTS soussujetscers93_actif_idx;
CREATE INDEX soussujetscers93_actif_idx ON soussujetscers93(actif);

-- Pour valeursparsoussujetscers93
SELECT add_missing_table_field ( 'public', 'valeursparsoussujetscers93', 'actif', 'CHAR(1)' );
ALTER TABLE valeursparsoussujetscers93 ALTER COLUMN actif SET DEFAULT '1';
UPDATE valeursparsoussujetscers93 SET actif = '1' WHERE actif IS NULL;
ALTER TABLE valeursparsoussujetscers93 ALTER COLUMN actif SET NOT NULL;
SELECT public.alter_table_drop_constraint_if_exists( 'public', 'valeursparsoussujetscers93', 'valeursparsoussujetscers93_actif_in_list_chk' );
ALTER TABLE valeursparsoussujetscers93 ADD CONSTRAINT valeursparsoussujetscers93_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY['0','1'] ) );
DROP INDEX IF EXISTS valeursparsoussujetscers93_actif_idx;
CREATE INDEX valeursparsoussujetscers93_actif_idx ON valeursparsoussujetscers93(actif);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
