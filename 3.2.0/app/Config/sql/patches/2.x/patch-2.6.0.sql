
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

----------------------------------------------------------------------------------------
-- 20130729 : Créationd es nouvelles tables pour la partie Accompagnement du CUI (CG66)
----------------------------------------------------------------------------------------
-- On renomme la table accompagnementscuis66 afin de ne rien perdre
DROP TABLE IF EXISTS oldaccompagnementscuis66 CASCADE;
ALTER TABLE accompagnementscuis66 RENAME TO oldaccompagnementscuis66;

ALTER SEQUENCE accompagnementscuis66_id_seq RENAME TO oldaccompagnementscuis66_id_seq;

-- On crée une nouvelle table accompagnementscuis66 avec uniquement les données dont on a besoin
DROP TABLE IF EXISTS accompagnementscuis66 CASCADE;
CREATE TABLE accompagnementscuis66(
    id                          SERIAL NOT NULL PRIMARY KEY,
    cui_id                      INTEGER NOT NULL REFERENCES cuis(id) ON DELETE CASCADE ON UPDATE CASCADE,
    typeaccompagnementcui66     VARCHAR(20) NOT NULL,
    haspiecejointe              VARCHAR(1) NOT NULL DEFAULT '0',
    user_id                     INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
	----------------------------------------------------------------------------
    created                     TIMESTAMP WITHOUT TIME ZONE,
    modified                    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE accompagnementscuis66 IS 'Table des différents accompagnements (périodes immersion, formations et bilans) pour le CUI (CG66)';

DROP INDEX IF EXISTS accompagnementscuis66_cui_id_idx;
CREATE INDEX accompagnementscuis66_cui_id_idx ON accompagnementscuis66( cui_id );

DROP INDEX IF EXISTS accompagnementscuis66_user_id_idx;
CREATE INDEX accompagnementscuis66_user_id_idx ON accompagnementscuis66( user_id );

SELECT alter_table_drop_constraint_if_exists( 'public', 'accompagnementscuis66', 'accompagnementscuis66_typeaccompagnementcui66_in_list_chk' );
ALTER TABLE accompagnementscuis66 ADD CONSTRAINT accompagnementscuis66_typeaccompagnementcui66_in_list_chk CHECK ( cakephp_validate_in_list( typeaccompagnementcui66, ARRAY['immersion', 'formation', 'bilan'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'accompagnementscuis66', 'accompagnementscuis66_haspiecejointe_in_list_chk' );
ALTER TABLE accompagnementscuis66 ADD CONSTRAINT accompagnementscuis66_haspiecejointe_in_list_chk CHECK ( cakephp_validate_in_list( haspiecejointe, ARRAY['0', '1'] ) );

--------------------------------------------------------------------------------
-- On crée une nouvelle table bilanscuis66 pour stocker les accompagnements de type bilan
DROP  TABLE IF EXISTS bilanscuis66 CASCADE;
CREATE TABLE bilanscuis66(
    id                          SERIAL NOT NULL PRIMARY KEY,
    accompagnementcui66_id      INTEGER NOT NULL REFERENCES accompagnementscuis66(id) ON DELETE CASCADE ON UPDATE CASCADE,
    datedebut                   DATE,
    datefin                     DATE,
    observation                 TEXT,
    orgsuivicui66_id              INTEGER NOT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
    refsuivicui66_id              INTEGER NOT NULL REFERENCES referents(id) ON DELETE CASCADE ON UPDATE CASCADE,
    datesignaturebilan          DATE NOT NULL,
	----------------------------------------------------------------------------
    created                     TIMESTAMP WITHOUT TIME ZONE,
    modified                    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE bilanscuis66 IS 'Table des différents bilans d''accompagnement pour le CUI (CG66)';

DROP INDEX IF EXISTS bilanscuis66_accompagnementcui66_id_idx;
CREATE INDEX bilanscuis66_accompagnementcui66_id_idx ON bilanscuis66( accompagnementcui66_id );

DROP INDEX IF EXISTS bilanscuis66_orgsuivicui66_id_idx;
CREATE INDEX bilanscuis66_orgsuivicui66_id_idx ON bilanscuis66( orgsuivicui66_id );

DROP INDEX IF EXISTS bilanscuis66_refsuivicui66_id_idx;
CREATE INDEX bilanscuis66_refsuivicui66_id_idx ON bilanscuis66( refsuivicui66_id );
--------------------------------------------------------------------------------

-- On crée une nouvelle table formationscuis66 pour stocker les accompagnements de type formation
DROP  TABLE IF EXISTS formationscuis66 CASCADE;
CREATE TABLE formationscuis66(
    id                          SERIAL NOT NULL PRIMARY KEY,
    accompagnementcui66_id      INTEGER NOT NULL REFERENCES accompagnementscuis66(id) ON DELETE CASCADE ON UPDATE CASCADE,
    datedebut                   DATE,
    datefin                     DATE,
    observation                 TEXT,
    orgsuivicui66_id              INTEGER NOT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
    refsuivicui66_id              INTEGER NOT NULL REFERENCES referents(id) ON DELETE CASCADE ON UPDATE CASCADE,
    datesignatureformation          DATE NOT NULL,
	----------------------------------------------------------------------------
    created                     TIMESTAMP WITHOUT TIME ZONE,
    modified                    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE formationscuis66 IS 'Table des différentes formations d''accompagnement pour le CUI (CG66)';

DROP INDEX IF EXISTS formationscuis66_accompagnementcui66_id_idx;
CREATE INDEX formationscuis66_accompagnementcui66_id_idx ON formationscuis66( accompagnementcui66_id );

DROP INDEX IF EXISTS formationscuis66_orgsuivicui66_id_idx;
CREATE INDEX formationscuis66_orgsuivicui66_id_idx ON formationscuis66( orgsuivicui66_id );

DROP INDEX IF EXISTS formationscuis66_refsuivicui66_id_idx;
CREATE INDEX formationscuis66_refsuivicui66_id_idx ON formationscuis66( refsuivicui66_id );
--------------------------------------------------------------------------------

-- On crée une nouvelle table periodesimmersioncuis66 pour stocker les accompagnements de type période d'immersion
DROP  TABLE IF EXISTS periodesimmersioncuis66 CASCADE;
CREATE TABLE periodesimmersioncuis66(
    id                          SERIAL NOT NULL PRIMARY KEY,
    accompagnementcui66_id      INTEGER NOT NULL REFERENCES accompagnementscuis66(id) ON DELETE CASCADE ON UPDATE CASCADE,
    nomentaccueil               VARCHAR(50) DEFAULT NULL,
    numvoieentaccueil           VARCHAR(6) DEFAULT NULL,
    typevoieentaccueil          VARCHAR(4) DEFAULT NULL,
    nomvoieentaccueil           VARCHAR(50) DEFAULT NULL,
    compladrentaccueil          VARCHAR(50) DEFAULT NULL,
    codepostalentaccueil        VARCHAR(5) DEFAULT NULL,
    villeentaccueil             VARCHAR(50) DEFAULT NULL,
    activiteentaccueil          VARCHAR(14) DEFAULT NULL,
    datedebperiode              DATE DEFAULT NULL,
    datefinperiode              DATE DEFAULT NULL,
    nbjourperiode               INTEGER DEFAULT NULL,
    secteuraffectation_id       INTEGER REFERENCES codesromesecteursdsps66(id) ON DELETE CASCADE ON UPDATE CASCADE,
    metieraffectation_id        INTEGER REFERENCES codesromemetiersdsps66(id) ON DELETE CASCADE ON UPDATE CASCADE,
    objectifimmersion           VARCHAR(10) NOT NULL,
    datesignatureimmersion      DATE NOT NULL,
	----------------------------------------------------------------------------
    created                     TIMESTAMP WITHOUT TIME ZONE,
    modified                    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE periodesimmersioncuis66 IS 'Table des différentes périodes d''immersion d''accompagnement pour le CUI (CG66)';

DROP INDEX IF EXISTS periodesimmersioncuis66_accompagnementcui66_id_idx;
CREATE INDEX periodesimmersioncuis66_accompagnementcui66_id_idx ON periodesimmersioncuis66( accompagnementcui66_id );

DROP INDEX IF EXISTS periodesimmersioncuis66_secteuraffectation_id_idx;
CREATE INDEX periodesimmersioncuis66_secteuraffectation_id_idx ON periodesimmersioncuis66( secteuraffectation_id );

DROP INDEX IF EXISTS periodesimmersioncuis66_metieraffectation_id_idx;
CREATE INDEX periodesimmersioncuis66_metieraffectation_id_idx ON periodesimmersioncuis66( metieraffectation_id );

SELECT alter_table_drop_constraint_if_exists( 'public', 'periodesimmersioncuis66', 'periodesimmersioncuis66_objectifimmersion_in_list_chk' );
ALTER TABLE periodesimmersioncuis66 ADD CONSTRAINT periodesimmersioncuis66_objectifimmersion_in_list_chk CHECK ( cakephp_validate_in_list( objectifimmersion, ARRAY['acquerir', 'confirmer', 'decouvrir','initier'] ) );

--------------------------------------------------------------------------------

--------------------------------------------------------------------------------
-- 20130730 : Récupération des données suite à modification de la structure ----
--------------------------------------------------------------------------------
SELECT alter_table_drop_constraint_if_exists( 'public', 'oldaccompagnementscuis66', 'accompagnementscuis66_typeaccompagnementcui66_in_list_chk' );

ALTER TABLE oldaccompagnementscuis66 ALTER COLUMN typeaccompagnementcui66 TYPE VARCHAR(20) USING CAST(typeaccompagnementcui66 AS VARCHAR(20));
UPDATE oldaccompagnementscuis66 SET typeaccompagnementcui66 = 'immersion' WHERE typeaccompagnementcui66 = 'periode';

-- On récupère les données de l'ancienne table accompagnementscuis66 et on les insère dans la nouvelle
INSERT INTO accompagnementscuis66 ( id, cui_id, typeaccompagnementcui66, haspiecejointe, user_id, created, modified )
	( SELECT
			id AS id,
			cui_id AS cui_id,
			typeaccompagnementcui66 AS typeaccompagnementcui66,
			haspiecejointe AS haspiecejointe,
			user_id AS user_id,
			created AS created,
			modified AS modified
		FROM oldaccompagnementscuis66
		ORDER BY oldaccompagnementscuis66.id
	);


-- On récupère les données liées aux périodes d'immersion présentes dans l'ancienne table accompagnementscuis66
-- et on les insère dans la nouvelle table periodesimmersioncuis66
-- On ne fait ça que pour les périodes d'immersion car les autres infos (bilans, formations) n'existaient pas jusqu'à maintenant

INSERT INTO periodesimmersioncuis66 ( accompagnementcui66_id, nomentaccueil, numvoieentaccueil, typevoieentaccueil, nomvoieentaccueil, compladrentaccueil, codepostalentaccueil, villeentaccueil, activiteentaccueil, datedebperiode, datefinperiode, nbjourperiode, secteuraffectation_id, metieraffectation_id, objectifimmersion, datesignatureimmersion, created, modified )
	(
		SELECT
			id AS accompagnementcui66_id,
			nomentaccueil AS nomentaccueil,
			numvoieentaccueil AS numvoieentaccueil,
			typevoieentaccueil AS typevoieentaccueil,
			nomvoieentaccueil AS nomvoieentaccueil,
			compladrentaccueil AS compladrentaccueil,
			codepostalentaccueil AS codepostalentaccueil,
			villeentaccueil AS villeentaccueil,
			activiteentaccueil AS activiteentaccueil,
			datedebperiode AS datedebperiode,
			datefinperiode AS datefinperiode,
			nbjourperiode AS nbjourperiode,
			secteuraffectation_id AS secteuraffectation_id,
			metieraffectation_id AS metieraffectation_id,
			objectifimmersion AS objectifimmersion,
			datesignatureimmersion AS datesignatureimmersion,
			created AS created,
			modified AS modified
		FROM
			oldaccompagnementscuis66
		WHERE
			typeaccompagnementcui66 = 'immersion'
		ORDER BY oldaccompagnementscuis66.id
);

 --FIXME: attention au CASCADE, être bien sûr que toutes les données sont bien migrées afin de ne pas supprimer de cui lié !!
-- DROP TABLE IF EXISTS oldaccompagnementscuis66 (( CASCADE ));
 --FIXME: attention au CASCADE, être bien sûr que toutes les données sont bien migrées afin de ne pas supprimer de cui lié !!

--------------------------------------------------------------------------------
-- 20130801 : Ajout dans le bilan de parcours de choisir si on fait 
--              un maintien ou une réorientation sans passage en EP
--------------------------------------------------------------------------------
SELECT add_missing_table_field ( 'public', 'bilansparcours66', 'choixsanspassageep', 'VARCHAR(13)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'bilansparcours66', 'bilansparcours66_choixsanspassageep_in_list_chk' );
ALTER TABLE bilansparcours66 ADD CONSTRAINT bilansparcours66_choixsanspassageep_in_list_chk CHECK ( cakephp_validate_in_list( choixsanspassageep, ARRAY['maintien','reorientation'] ) );

UPDATE bilansparcours66 SET choixsanspassageep='maintien' WHERE choixsanspassageep IS NULL AND proposition='traitement';

SELECT add_missing_table_field ( 'public', 'cuis', 'created', 'TIMESTAMP WITHOUT TIME ZONE' );
SELECT add_missing_table_field ( 'public', 'cuis', 'modified', 'TIMESTAMP WITHOUT TIME ZONE' );
UPDATE cuis SET created = datearrivee WHERE created IS NULL;
UPDATE cuis SET modified = datearrivee WHERE modified IS NULL;

---------------------------------------------------------------------------------------------------------
-- 20130916: Ajout d'une table de paramétrage pour gérer les Pôles travaillant sur les dossiers PCGS 66
---------------------------------------------------------------------------------------------------------
DROP TABLE IF EXISTS polesdossierspcgs66 CASCADE;
CREATE TABLE polesdossierspcgs66 (
    id          SERIAL NOT NULL PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    isactif     VARCHAR(1) NOT NULL DEFAULT '1',
    created     TIMESTAMP WITHOUT TIME ZONE,
    modified    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE polesdossierspcgs66 IS 'Table de paramétrages des Pôles chargés de traiter les dossiers PCGS (CG66)';

DROP INDEX IF EXISTS polesdossierspcgs66_name_idx;
CREATE INDEX polesdossierspcgs66_name_idx ON polesdossierspcgs66( name );

SELECT alter_table_drop_constraint_if_exists( 'public', 'polesdossierspcgs66', 'polesdossierspcgs66_isactif_in_list_chk' );
ALTER TABLE polesdossierspcgs66 ADD CONSTRAINT polesdossierspcgs66_isactif_in_list_chk CHECK ( cakephp_validate_in_list( isactif, ARRAY['0', '1'] ) );

SELECT add_missing_table_field ( 'public', 'users', 'poledossierpcg66_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'users', 'users_poledossierpcg66_id_fkey', 'polesdossierspcgs66', 'poledossierpcg66_id', false );

SELECT add_missing_table_field ( 'public', 'dossierspcgs66', 'poledossierpcg66_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'dossierspcgs66', 'dossierspcgs66_poledossierpcg66_id_fkey', 'polesdossierspcgs66', 'poledossierpcg66_id', false );

-- *****************************************************************************
COMMIT;
-- *****************************************************************************