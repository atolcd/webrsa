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


-- ***************************************************************************************
-- CG58
-- 20111025 -- Ajout d'une table pour les maintiens dans le social passant en COV 58
-- ***************************************************************************************
INSERT INTO themescovs58 ( name ) VALUES
	( 'proposnonorientationsproscovs58' )
;

DROP TABLE IF EXISTS proposnonorientationsproscovs58;
CREATE TABLE proposnonorientationsproscovs58 (
	id 							SERIAL NOT NULL PRIMARY KEY,
	dossiercov58_id				INTEGER NOT NULL REFERENCES dossierscovs58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typeorient_id 				INTEGER NOT NULL REFERENCES typesorients(id) ON DELETE CASCADE ON UPDATE CASCADE,
	orientstruct_id 			INTEGER NOT NULL REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	structurereferente_id 		INTEGER NOT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	referent_id 				INTEGER DEFAULT NULL REFERENCES referents(id) ON DELETE CASCADE ON UPDATE CASCADE,
	datedemande 				DATE NOT NULL,
	rgorient 					INTEGER NOT NULL,
	covtypeorient_id 			INTEGER DEFAULT NULL REFERENCES typesorients(id) ON DELETE CASCADE ON UPDATE CASCADE,
	covstructurereferente_id	INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	datevalidation				DATE DEFAULT NULL,
	commentaire 				TEXT DEFAULT NULL
);
COMMENT ON TABLE proposnonorientationsproscovs58 IS 'Demandes de maintien en social en attente de validation par la COV (cg58)';

CREATE INDEX proposnonorientationsproscovs58_dossiercov58_id_idx ON proposnonorientationsproscovs58(dossiercov58_id);
CREATE INDEX proposnonorientationsproscovs58_typeorient_id_idx ON proposnonorientationsproscovs58(typeorient_id);
CREATE INDEX proposnonorientationsproscovs58_orientstruct_id_idx ON proposnonorientationsproscovs58(orientstruct_id);
CREATE INDEX proposnonorientationsproscovs58_structurereferente_id_idx ON proposnonorientationsproscovs58(structurereferente_id);
CREATE INDEX proposnonorientationsproscovs58_referent_id_idx ON proposnonorientationsproscovs58(referent_id);
CREATE INDEX proposnonorientationsproscovs58_covtypeorient_id_idx ON proposnonorientationsproscovs58(covtypeorient_id);
CREATE INDEX proposnonorientationsproscovs58_covstructurereferente_id_idx ON proposnonorientationsproscovs58(covstructurereferente_id);


DROP TABLE IF EXISTS passagescovs58 CASCADE;
DROP TYPE IF EXISTS TYPE_ETATDOSSIERCOV CASCADE;

CREATE TYPE TYPE_ETATDOSSIERCOV AS ENUM ( 'cree','associe', 'traite', 'annule', 'reporte' );

CREATE TABLE passagescovs58 (
	id						SERIAL NOT NULL PRIMARY KEY,
	cov58_id				INTEGER DEFAULT NULL REFERENCES covs58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	dossiercov58_id			INTEGER NOT NULL REFERENCES dossierscovs58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	user_id				INTEGER DEFAULT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
	etatdossiercov			TYPE_ETATDOSSIERCOV NOT NULL,
	impressiondecision		DATE,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);

COMMENT ON TABLE passagescovs58 IS 'Passage des dossiers COVs en COVs (CG 58)';

CREATE INDEX passagescovs58_cov58_id_idx ON passagescovs58(cov58_id);
CREATE INDEX passagescovs58_dossiercov58_id_idx ON passagescovs58(dossiercov58_id);
CREATE INDEX passagescovs58_user_id_idx ON passagescovs58(user_id);
CREATE UNIQUE INDEX passagescovs58_etatdossiercov_idx ON passagescovs58(etatdossiercov);
DROP INDEX IF EXISTS passagescovs58_etatdossiercov_idx;

------------------> Décisions sur les propositions d'orientation par la COV
DROP TABLE IF EXISTS decisionsproposorientationscovs58;
DROP TYPE IF EXISTS TYPE_DECISIONORIENTATIONCOV;
CREATE TYPE TYPE_DECISIONORIENTATIONCOV AS ENUM ( 'valide', 'refuse', 'annule', 'reporte' );
CREATE TABLE decisionsproposorientationscovs58 (
	id      					SERIAL NOT NULL PRIMARY KEY,
	passagecov58_id				INTEGER NOT NULL REFERENCES passagescovs58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	etapecov					TYPE_ETAPECOV NOT NULL,
	decisioncov					TYPE_DECISIONORIENTATIONCOV NOT NULL,
	typeorient_id 			INTEGER DEFAULT NULL REFERENCES typesorients(id) ON DELETE CASCADE ON UPDATE CASCADE,
	structurereferente_id	INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	referent_id 				INTEGER DEFAULT NULL REFERENCES referents(id) ON DELETE CASCADE ON UPDATE CASCADE,
	datevalidation				DATE,
	commentaire					TEXT DEFAULT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);

CREATE INDEX decisionsproposorientationscovs58_passagecov58_id_idx ON decisionsproposorientationscovs58( passagecov58_id );
CREATE INDEX decisionsproposorientationscovs58_etapecov_idx ON decisionsproposorientationscovs58( etapecov );
CREATE INDEX decisionsproposorientationscovs58_decisioncov_idx ON decisionsproposorientationscovs58( decisioncov );
CREATE UNIQUE INDEX decisionsproposorientationscovs58_passagecov58_id_etapecov_idx ON decisionsproposorientationscovs58(passagecov58_id, etapecov);
DROP INDEX IF EXISTS decisionsproposorientationscovs58_passagecov58_id_etapecov_idx;

------------------> Décisions sur les propositions de CER par la COV
DROP TABLE IF EXISTS decisionsproposcontratsinsertioncovs58;
DROP TYPE IF EXISTS TYPE_DECISIONCONTRATCOV;
CREATE TYPE TYPE_DECISIONCONTRATCOV AS ENUM ( 'valide', 'refuse', 'annule', 'reporte' );
CREATE TABLE decisionsproposcontratsinsertioncovs58 (
	id      					SERIAL NOT NULL PRIMARY KEY,
	passagecov58_id				INTEGER NOT NULL REFERENCES passagescovs58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	etapecov					TYPE_ETAPECOV NOT NULL,
	decisioncov					TYPE_DECISIONCONTRATCOV NOT NULL,
	datevalidation				DATE,
	commentaire					TEXT DEFAULT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);

CREATE INDEX decisionsproposcontratsinsertioncovs58_passagecov58_id_idx ON decisionsproposcontratsinsertioncovs58( passagecov58_id );
CREATE INDEX decisionsproposcontratsinsertioncovs58_etapecov_idx ON decisionsproposcontratsinsertioncovs58( etapecov );
CREATE INDEX decisionsproposcontratsinsertioncovs58_decisioncov_idx ON decisionsproposcontratsinsertioncovs58( decisioncov );
CREATE UNIQUE INDEX decisionsproposcontratsinsertioncovs58_passagecov58_id_etapecov_idx ON decisionsproposcontratsinsertioncovs58(passagecov58_id, etapecov);


------------------> Décisions sur les demandes de maintien dans le social par la COV
DROP TABLE IF EXISTS decisionsproposnonorientationsproscovs58;
CREATE TABLE decisionsproposnonorientationsproscovs58 (
	id      					SERIAL NOT NULL PRIMARY KEY,
	passagecov58_id				INTEGER NOT NULL REFERENCES passagescovs58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	etapecov					TYPE_ETAPECOV NOT NULL,
	decisioncov					TYPE_DECISIONORIENTATIONCOV NOT NULL,
	typeorient_id 			INTEGER DEFAULT NULL REFERENCES typesorients(id) ON DELETE CASCADE ON UPDATE CASCADE,
	structurereferente_id	INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	referent_id 				INTEGER DEFAULT NULL REFERENCES referents(id) ON DELETE CASCADE ON UPDATE CASCADE,
	datevalidation				DATE,
	commentaire					TEXT DEFAULT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);

CREATE INDEX decisionsproposnonorientationsproscovs58_passagecov58_id_idx ON decisionsproposnonorientationsproscovs58( passagecov58_id );
CREATE INDEX decisionsproposnonorientationsproscovs58_etapecov_idx ON decisionsproposnonorientationsproscovs58( etapecov );
CREATE INDEX decisionsproposnonorientationsproscovs58_decisioncov_idx ON decisionsproposnonorientationsproscovs58( decisioncov );
CREATE UNIQUE INDEX decisionsproposnonorientationsproscovs58_passagecov58_id_etapecov_idx ON decisionsproposnonorientationsproscovs58(passagecov58_id, etapecov);

-- 
INSERT INTO passagescovs58 ( cov58_id, dossiercov58_id, user_id, etatdossiercov )
	SELECT cov58_id, id, 6, CAST( CASE WHEN etapecov = 'cree' THEN 'cree' WHEN etapecov = 'traitement' THEN 'associe' WHEN etapecov = 'finalise' THEN 'traite' WHEN etapecov = 'ajourne' THEN 'reporte' END AS type_etatdossiercov ) FROM dossierscovs58 WHERE cov58_id IS NOT NULL;

------------------> Suppression des anciens attributs des tables des COVs
SELECT alter_table_drop_column_if_exists( 'public', 'dossierscovs58', 'cov58_id' );
SELECT alter_table_drop_column_if_exists( 'public', 'dossierscovs58', 'etapecov' );

------------------> Ajout de nouveaux attributs pour les tables des COVs
SELECT add_missing_table_field ('public', 'dossierscovs58', 'created', 'TIMESTAMP WITHOUT TIME ZONE');
SELECT add_missing_table_field ('public', 'dossierscovs58', 'modified', 'TIMESTAMP WITHOUT TIME ZONE');


-- ***************************************************************************************
-- 20111026 -- Ajout d'un ENUM dans la table des thématiques COV58
-- ***************************************************************************************

SELECT add_missing_table_field ('public', 'themescovs58', 'propoorientationcov58', 'TYPE_ETAPECOV');
SELECT add_missing_table_field ('public', 'themescovs58', 'propocontratinsertioncov58', 'TYPE_ETAPECOV');
SELECT add_missing_table_field ('public', 'themescovs58', 'propononorientationprocov58', 'TYPE_ETAPECOV');

DROP TYPE IF EXISTS TYPE_THEMECOV58 CASCADE;
CREATE TYPE TYPE_THEMECOV58 AS ENUM ( 'proposorientationscovs58', 'proposcontratsinsertioncovs58', 'proposnonorientationsproscovs58' );
SELECT add_missing_table_field ('public', 'dossierscovs58', 'themecov58', 'TYPE_THEMECOV58');

UPDATE dossierscovs58
	SET themecov58 = ( SELECT CAST( themescovs58.name AS type_themecov58 ) FROM themescovs58 WHERE themescovs58.id = dossierscovs58.themecov58_id );

ALTER TABLE passagescovs58 ALTER COLUMN etatdossiercov SET DEFAULT 'associe'::TYPE_ETATDOSSIERCOV;

SELECT public.alter_enumtype ( 'TYPE_ETATCOV', ARRAY['cree', 'associe', 'valide', 'decision', 'traite', 'finalise', 'annule', 'reporte'] );


-- ***************************************************************************************
-- 20111028 -- Ajout d'un lien entre la table des décisions de maintien en social
--				et la table des non orientaitons pro pour passage en EP pour le CG58
-- ***************************************************************************************
SELECT add_missing_table_field ( 'public', 'nonorientationsproseps58', 'decisionpropononorientationprocov58_id', 'INTEGER' );
ALTER TABLE nonorientationsproseps58 ALTER COLUMN decisionpropononorientationprocov58_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'nonorientationsproseps58', 'nonorientationsproseps58_decisionpropononorientationprocov58_id_fk', 'decisionsproposnonorientationsproscovs58', 'decisionpropononorientationprocov58_id' );

-- ***********************************************************************************************************
-- 20111103 -- Ajout de la clé étrangère referent_id à la table de décisions des maintiens en social 58
-- ***********************************************************************************************************
SELECT add_missing_table_field ( 'public', 'decisionsnonorientationsproseps58', 'referent_id', 'INTEGER' );
ALTER TABLE decisionsnonorientationsproseps58 ALTER COLUMN referent_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'decisionsnonorientationsproseps58', 'decisionsnonorientationsproseps58_referent_id_fk', 'referents', 'referent_id' );


-- ***********************************************************************************************************
-- 20111104 -- Ajout d'une table de liasion entre les objets de RDVs et les statuts pour le CG58
-- ***********************************************************************************************************

DROP TABLE IF EXISTS statutsrdvs_typesrdv;
CREATE TABLE statutsrdvs_typesrdv (
	id								SERIAL NOT NULL PRIMARY KEY,
	statutrdv_id				INTEGER NOT NULL REFERENCES statutsrdvs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typerdv_id				INTEGER NOT NULL REFERENCES typesrdv(id) ON DELETE CASCADE ON UPDATE CASCADE,
	nbabsenceavantpassageep				INTEGER NOT NULL,
	motifpassageep				TEXT
);
DROP INDEX IF EXISTS statutsrdvs_typesrdv_statutrdv_id_idx;
DROP INDEX IF EXISTS statutsrdvs_typesrdv_typerdv_id_idx;
CREATE INDEX statutsrdvs_typesrdv_typerdv_id_idx ON statutsrdvs_typesrdv(typerdv_id);
CREATE INDEX statutsrdvs_typesrdv_statutrdv_id_idx ON statutsrdvs_typesrdv(statutrdv_id);


-- ***********************************************************************************************************
-- CG66
-- 20111108 -- Ajout d'un champ pour annulation de la fiche de candidature 66
-- ***********************************************************************************************************
SELECT add_missing_table_field ( 'public', 'actionscandidats_personnes', 'motifannulation', 'TEXT' );

-- ***********************************************************************************************************
-- 20111109 -- Ajout de 2 champs pour le lieu et la personne du RDV prévu dans la fiche de candidature CG66
-- ***********************************************************************************************************
SELECT add_missing_table_field ( 'public', 'actionscandidats_personnes', 'lieurdvpartenaire', 'VARCHAR(255)' );
SELECT add_missing_table_field ( 'public', 'actionscandidats_personnes', 'personnerdvpartenaire', 'VARCHAR(100)' );


DROP SEQUENCE IF EXISTS apres_numeroapre_seq;
CREATE OR REPLACE FUNCTION public.init_apres_numeroapre_seq() RETURNS bool AS
$$
	DECLARE
		v_row       		record;
		v_query     		text;
	BEGIN
		SELECT ( CAST( regexp_replace( COALESCE( MAX(numeroapre), '0' ), '^[0-9]{6}0+', '') AS INTEGER ) + 1 ) AS numeroapre FROM apres INTO v_row;

		IF FOUND THEN
			v_query := 'CREATE SEQUENCE apres_numeroapre_seq START ' || v_row.numeroapre || ';';
			EXECUTE v_query;
			RETURN 't';
		ELSE
			RETURN 'f';
		END IF;
	END;
$$
LANGUAGE plpgsql;
SELECT public.init_apres_numeroapre_seq();
DROP FUNCTION public.init_apres_numeroapre_seq();

-- ***********************************************************************************************************
-- 20111115 -- Ajout d'un champ pour l'email de la personne
-- ***********************************************************************************************************
SELECT add_missing_table_field ( 'public', 'personnes', 'email', 'VARCHAR(100)' );
-- *****************************************************************************
-- Nouvelle version de la fonction public.add_missing_constraint qui prend en compte
--		la longueur maximale de 63 caractères pour le nom de la contrainte.
-- ***************************************************************************************

CREATE OR REPLACE FUNCTION public.add_missing_constraint (text, text, text, text, text) RETURNS bool AS
$$
	DECLARE
		p_namespace 		alias for $1;
		p_table     		alias for $2;
		p_constraintname	alias for $3;
		p_foreigntable		alias for $4;
		p_foreignkeyname	alias for $5;
		v_row       		record;
		v_query     		text;
	BEGIN
		SELECT 1 INTO v_row
		FROM information_schema.table_constraints tc
			LEFT JOIN information_schema.key_column_usage kcu ON (
				tc.constraint_catalog = kcu.constraint_catalog
				AND tc.constraint_schema = kcu.constraint_schema
				AND tc.constraint_name = kcu.constraint_name
			)
			LEFT JOIN information_schema.referential_constraints rc ON (
				tc.constraint_catalog = rc.constraint_catalog
				AND tc.constraint_schema = rc.constraint_schema
				AND tc.constraint_name = rc.constraint_name
			)
			LEFT JOIN information_schema.constraint_column_usage ccu ON (
				rc.unique_constraint_catalog = ccu.constraint_catalog
				AND rc.unique_constraint_schema = ccu.constraint_schema
				AND rc.unique_constraint_name = ccu.constraint_name
			)
		WHERE
			tc.table_schema = p_namespace
			AND tc.table_name = p_table
			AND tc.constraint_type = 'FOREIGN KEY'
			AND tc.constraint_name = substring( p_constraintname from 1 for 63 ) -- INFO: les noms sont juste tronqués, pas de chiffre à la fin -> ça ne devrait pas poser de problème
			AND kcu.column_name = p_foreignkeyname
			AND ccu.table_name = p_foreigntable
			AND ccu.column_name = 'id';

		IF NOT FOUND THEN
			RAISE NOTICE 'Upgrade table %.% - add constraint %', p_namespace, p_table, p_constraintname;
			v_query := 'alter table ' || p_namespace || '.' || p_table || ' add constraint ';
			v_query := v_query || p_constraintname || ' FOREIGN KEY (' || p_foreignkeyname || ') REFERENCES ' || p_foreigntable || '(id) ON DELETE CASCADE ON UPDATE CASCADE;';
			EXECUTE v_query;
			RETURN 't';
		ELSE
			RETURN 'f';
		END IF;
	END;
$$
LANGUAGE plpgsql;

COMMENT ON FUNCTION public.add_missing_constraint (text, text, text, text, text) IS 'Add a constraint to a table if it is missing';

-- *****************************************************************************
-- 20111104 : ajout des relations entre les tables de décisions EP et la table users
-- *****************************************************************************

SELECT add_missing_table_field ('public', 'decisionsnonorientationsproseps93', 'user_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'decisionsnonorientationsproseps93', 'decisionsnonorientationsproseps93_user_id_fkey', 'users', 'user_id');
ALTER TABLE decisionsnonorientationsproseps93 ALTER COLUMN user_id SET NOT NULL;

DROP INDEX IF EXISTS decisionsnonorientationsproseps93_user_id_isx;
CREATE INDEX decisionsnonorientationsproseps93_user_id_isx ON decisionsnonorientationsproseps93(user_id);

-- -----------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'decisionscontratscomplexeseps93', 'user_id', 'INTEGER');
SELECT add_missing_constraint( 'public', 'decisionscontratscomplexeseps93', 'decisionscontratscomplexeseps93_user_id_fk', 'users', 'user_id' );
ALTER TABLE decisionscontratscomplexeseps93 ALTER COLUMN user_id SET NOT NULL;
 
DROP INDEX IF EXISTS decisionscontratscomplexeseps93_user_id_isx;
CREATE INDEX decisionscontratscomplexeseps93_user_id_isx ON decisionscontratscomplexeseps93(user_id);

-- -----------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'decisionsdefautsinsertionseps66', 'user_id', 'INTEGER');
SELECT add_missing_constraint( 'public', 'decisionsdefautsinsertionseps66', 'decisionsdefautsinsertionseps66_user_id_fk', 'users', 'user_id' );
ALTER TABLE decisionsdefautsinsertionseps66 ALTER COLUMN user_id SET NOT NULL;
 
DROP INDEX IF EXISTS decisionsdefautsinsertionseps66_user_id_isx;
CREATE INDEX decisionsdefautsinsertionseps66_user_id_isx ON decisionsdefautsinsertionseps66(user_id);

-- -----------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'decisionsnonorientationsproseps58', 'user_id', 'INTEGER');
SELECT add_missing_constraint( 'public', 'decisionsnonorientationsproseps58', 'decisionsnonorientationsproseps58_user_id_fk', 'users', 'user_id' );
ALTER TABLE decisionsnonorientationsproseps58 ALTER COLUMN user_id SET NOT NULL;
 
DROP INDEX IF EXISTS decisionsnonorientationsproseps58_user_id_isx;
CREATE INDEX decisionsnonorientationsproseps58_user_id_isx ON decisionsnonorientationsproseps58(user_id);

-- -----------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'decisionsnonorientationsproseps93', 'user_id', 'INTEGER');
SELECT add_missing_constraint( 'public', 'decisionsnonorientationsproseps93', 'decisionsnonorientationsproseps93_user_id_fk', 'users', 'user_id' );
ALTER TABLE decisionsnonorientationsproseps93 ALTER COLUMN user_id SET NOT NULL;
 
DROP INDEX IF EXISTS decisionsnonorientationsproseps93_user_id_isx;
CREATE INDEX decisionsnonorientationsproseps93_user_id_isx ON decisionsnonorientationsproseps93(user_id);

-- -----------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'decisionsregressionsorientationseps58', 'user_id', 'INTEGER');
SELECT add_missing_constraint( 'public', 'decisionsregressionsorientationseps58', 'decisionsregressionsorientationseps58_user_id_fk', 'users', 'user_id' );
ALTER TABLE decisionsregressionsorientationseps58 ALTER COLUMN user_id SET NOT NULL;
 
DROP INDEX IF EXISTS decisionsregressionsorientationseps58_user_id_isx;
CREATE INDEX decisionsregressionsorientationseps58_user_id_isx ON decisionsregressionsorientationseps58(user_id);

-- -----------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'decisionssaisinesbilansparcourseps66', 'user_id', 'INTEGER');
SELECT add_missing_constraint( 'public', 'decisionssaisinesbilansparcourseps66', 'decisionssaisinesbilansparcourseps66_user_id_fk', 'users', 'user_id' );
ALTER TABLE decisionssaisinesbilansparcourseps66 ALTER COLUMN user_id SET NOT NULL;
 
DROP INDEX IF EXISTS decisionssaisinesbilansparcourseps66_user_id_isx;
CREATE INDEX decisionssaisinesbilansparcourseps66_user_id_isx ON decisionssaisinesbilansparcourseps66(user_id);

-- -----------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'decisionssaisinespdoseps66', 'user_id', 'INTEGER');
SELECT add_missing_constraint( 'public', 'decisionssaisinespdoseps66', 'decisionssaisinespdoseps66_user_id_fk', 'users', 'user_id' );
ALTER TABLE decisionssaisinespdoseps66 ALTER COLUMN user_id SET NOT NULL;
 
DROP INDEX IF EXISTS decisionssaisinespdoseps66_user_id_isx;
CREATE INDEX decisionssaisinespdoseps66_user_id_isx ON decisionssaisinespdoseps66(user_id);

-- -----------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'decisionssanctionseps58', 'user_id', 'INTEGER');
SELECT add_missing_constraint( 'public', 'decisionssanctionseps58', 'decisionssanctionseps58_user_id_fk', 'users', 'user_id' );
ALTER TABLE decisionssanctionseps58 ALTER COLUMN user_id SET NOT NULL;
 
DROP INDEX IF EXISTS decisionssanctionseps58_user_id_isx;
CREATE INDEX decisionssanctionseps58_user_id_isx ON decisionssanctionseps58(user_id);

-- -----------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'decisionssanctionsrendezvouseps58', 'user_id', 'INTEGER');
SELECT add_missing_constraint( 'public', 'decisionssanctionsrendezvouseps58', 'decisionssanctionsrendezvouseps58_user_id_fk', 'users', 'user_id' );
ALTER TABLE decisionssanctionsrendezvouseps58 ALTER COLUMN user_id SET NOT NULL;
 
DROP INDEX IF EXISTS decisionssanctionsrendezvouseps58_user_id_isx;
CREATE INDEX decisionssanctionsrendezvouseps58_user_id_isx ON decisionssanctionsrendezvouseps58(user_id);

-- -----------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'decisionssignalementseps93', 'user_id', 'INTEGER');
SELECT add_missing_constraint( 'public', 'decisionssignalementseps93', 'decisionssignalementseps93_user_id_fk', 'users', 'user_id' );
ALTER TABLE decisionssignalementseps93 ALTER COLUMN user_id SET NOT NULL;
 
DROP INDEX IF EXISTS decisionssignalementseps93_user_id_isx;
CREATE INDEX decisionssignalementseps93_user_id_isx ON decisionssignalementseps93(user_id);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************