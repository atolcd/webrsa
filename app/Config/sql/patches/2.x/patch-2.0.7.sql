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
DROP TYPE IF EXISTS TYPE_CERCMU CASCADE;
DROP TYPE IF EXISTS TYPE_CERCMUC CASCADE;
DROP TYPE IF EXISTS TYPE_OBJETCERPREC CASCADE;

DROP TABLE IF EXISTS objetscontratsprecedents CASCADE;

CREATE OR REPLACE FUNCTION public.alter_enumtype ( p_enumtypename text, p_values text[] ) RETURNS void AS
$$
	DECLARE
		v_row			record;
		v_query			text;
		v_enumtypename	text;
	BEGIN
		-- PostgreSQL stocke ses types en minuscule
		v_enumtypename := LOWER( p_enumtypename );

		v_query := 'DROP TABLE IF EXISTS __alter_enumtype;';
		EXECUTE v_query;

		v_query := 'CREATE TEMP TABLE __alter_enumtype(table_schema TEXT, table_name TEXT, column_name TEXT, column_default TEXT);';
		EXECUTE v_query;

		v_query := 'INSERT INTO __alter_enumtype (
						SELECT
								table_schema,
								table_name,
								column_name,
								regexp_replace( column_default, ''^''''(.*)''''::.*$'', E''\\\\1'', ''g'' ) AS column_default
							FROM information_schema.columns
							WHERE
								data_type = ''USER-DEFINED''
								AND udt_name = ''' || v_enumtypename || '''
							ORDER BY
								table_schema,
								table_name,
								column_name
					);';
		EXECUTE v_query;

		-- Première boucle pour tout transformer en TEXT
		FOR v_row IN
			SELECT
					*
				FROM __alter_enumtype
				ORDER BY
					table_schema,
					table_name,
					column_name
		LOOP
			-- DROP DEFAULT
			IF v_row.column_default IS NOT NULL THEN
				v_query := 'ALTER TABLE ' || v_row.table_schema || '.' || v_row.table_name || ' ALTER COLUMN ' || v_row.column_name || ' DROP DEFAULT;';
				EXECUTE v_query;
			END IF;

			-- ALTER COLUMN
			v_query := 'ALTER TABLE ' || v_row.table_schema || '.' || v_row.table_name || ' ALTER COLUMN ' || v_row.column_name || ' TYPE TEXT USING CAST( ' || v_row.column_name || ' AS TEXT );';
			EXECUTE v_query;
		END LOOP;

		v_query := 'DROP TYPE ' || v_enumtypename || ';';
		EXECUTE v_query;

		v_query := 'CREATE TYPE ' || v_enumtypename || ' AS ENUM (''' || array_to_string( p_values, ''', ''' ) || ''' );';
		EXECUTE v_query;

		-- Seconde boucle pour tout transformer en le nouveau type
		FOR v_row IN
			SELECT
					*
				FROM __alter_enumtype
				ORDER BY
					table_schema,
					table_name,
					column_name
		LOOP
			-- ALTER COLUMN
			v_query := 'ALTER TABLE ' || v_row.table_schema || '.' || v_row.table_name || ' ALTER COLUMN ' || v_row.column_name || ' TYPE ' || v_enumtypename || ' USING CAST( ' || v_row.column_name || ' AS ' || v_enumtypename || ' );';
			EXECUTE v_query;

			-- SET DEFAULT
			IF v_row.column_default IS NOT NULL THEN
				v_query := 'ALTER TABLE ' || v_row.table_schema || '.' || v_row.table_name || ' ALTER COLUMN ' || v_row.column_name || ' SET DEFAULT ''' || v_row.column_default || '''::' || v_enumtypename || ';';
				EXECUTE v_query;
			END IF;
		END LOOP;

		v_query := 'DROP TABLE IF EXISTS __alter_enumtype;';
		EXECUTE v_query;
	END;
$$
LANGUAGE plpgsql;

COMMENT ON FUNCTION public.alter_enumtype ( p_enumtypename text, p_values text[] ) IS 'Modification des valeurs acceptées par un type enum, pour tous les champs qui l''utilisent (PostgreSQL >= 8.3)';

-- ----------------------------------------------------------------------------

-- Ajout de la position "traite" aux position du bilan de parcours du CG 66
SELECT public.alter_enumtype ( 'TYPE_POSITIONBILAN', ARRAY['eplaudit', 'eplparc', 'attcga', 'attct', 'ajourne', 'annule', 'traite'] );

-- ----------------------------------------------------------------------------

-- Ajout des valeurs emploi et professionnel vers professionnel pour les tables
-- saisinesbilansparcourseps66, decisionssaisinesbilansparcourseps66 et bilansparcours66
SELECT public.alter_enumtype ( 'TYPE_ORIENT', ARRAY['social','prepro','pro'] );
SELECT public.alter_enumtype ( 'TYPE_REORIENTATION', ARRAY['SP', 'PS', 'PP'] );


-- *****************************************************************************
-- 20110705 : mise en place de la notion d'avenant pour les CER du cg58
-- *****************************************************************************
SELECT add_missing_table_field ('public', 'contratsinsertion', 'avenant_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'contratsinsertion', 'contratsinsertion_avenant_id_fkey', 'contratsinsertion', 'avenant_id');
ALTER TABLE contratsinsertion ALTER COLUMN avenant_id SET DEFAULT NULL;

SELECT add_missing_table_field ('public', 'proposcontratsinsertioncovs58', 'avenant_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'proposcontratsinsertioncovs58', 'proposcontratsinsertioncovs58_avenant_id_fkey', 'contratsinsertion', 'avenant_id');
ALTER TABLE proposcontratsinsertioncovs58 ALTER COLUMN avenant_id SET DEFAULT NULL;

-- ----------------------------------------------------------------------------
-- 06/09/2011:
-- ----------------------------------------------------------------------------
-- Ajout de la position termine pour les CER du CG66. La position ressemble à
-- fincontrat sauf que fincontrat correspond à un contrat terminé avant terme
-- suite à un passage en EP, alors que termine correspond à un CER arrivé
-- simplement à terme.
-- SELECT public.alter_enumtype ( 'TYPE_POSITIONCER', ARRAY['encours', 'attvalid', 'annule', 'fincontrat', 'encoursbilan', 'attrenouv', 'perime', 'termine'] );

-- ----------------------------------------------------------------------------
-- 09/09/2011:
-- ----------------------------------------------------------------------------
ALTER TABLE actionscandidats_personnes ALTER COLUMN naturemobile TYPE TEXT;
DROP TYPE IF EXISTS TYPE_FICHELIAISONNATUREMOBILE;
CREATE TYPE TYPE_FICHELIAISONNATUREMOBILE AS ENUM ( 'commune', 'canton', 'dept', 'horsdept' );
UPDATE actionscandidats_personnes SET naturemobile = 'commune' WHERE naturemobile = '2501';
UPDATE actionscandidats_personnes SET naturemobile = 'dept' WHERE naturemobile = '2502';
UPDATE actionscandidats_personnes SET naturemobile = 'horsdept' WHERE naturemobile = '2503';
UPDATE actionscandidats_personnes SET naturemobile = null WHERE naturemobile = '2504';
ALTER TABLE actionscandidats_personnes ALTER COLUMN naturemobile TYPE TYPE_FICHELIAISONNATUREMOBILE USING CAST(naturemobile AS TYPE_FICHELIAISONNATUREMOBILE);

SELECT add_missing_table_field ('public', 'typesorients', 'actif', 'type_no');
ALTER TABLE typesorients ALTER COLUMN actif SET DEFAULT 'O';
UPDATE typesorients SET actif = 'O' WHERE actif IS NULL;
-- *****************************************************************************
-- 20110914 -- Nouveaux champs dans le CER pour le cg93
-- *****************************************************************************
SELECT add_missing_table_field ('public', 'contratsinsertion', 'sitfam', 'VARCHAR(3)');
SELECT add_missing_table_field ('public', 'contratsinsertion', 'typeocclog', 'VARCHAR(3)');
SELECT add_missing_table_field ('public', 'contratsinsertion', 'persacharge', 'TEXT');
CREATE TYPE TYPE_CERCMU AS ENUM ( 'oui', 'non', 'encours' );
SELECT add_missing_table_field ('public', 'contratsinsertion', 'cmu', 'TYPE_CERCMU');
CREATE TYPE TYPE_CERCMUC AS ENUM ( 'oui', 'non', 'encours' );
SELECT add_missing_table_field ('public', 'contratsinsertion', 'cmuc', 'TYPE_CERCMUC');
SELECT add_missing_table_field ('public', 'contratsinsertion', 'objetcerprecautre', 'VARCHAR(50)');

CREATE TYPE TYPE_OBJETCERPREC AS ENUM ( 'emploi', 'formation', 'autonomiesoc', 'sante', 'logement', 'autre' );

CREATE TABLE objetscontratsprecedents (
	id								SERIAL NOT NULL PRIMARY KEY,
	contratinsertion_id				INTEGER NOT NULL REFERENCES contratsinsertion(id) ON DELETE CASCADE ON UPDATE CASCADE,
	objetcerprec					TYPE_OBJETCERPREC DEFAULT NULL
);

-- *****************************************************************************
-- 20110919 -- Modification: on enregistre plus un enum pour savoir le type de
-- l'orientation mais l'id du type principal d'orientation (SOCIAL ou Emploi)
-- *****************************************************************************
SELECT add_missing_table_field ('public', 'bilansparcours66', 'accompagnement', 'TEXT');
ALTER TABLE bilansparcours66 ALTER COLUMN accompagnement TYPE TEXT;
ALTER TABLE bilansparcours66 DROP COLUMN accompagnement;
DROP TYPE IF EXISTS type_accompagnement;

SELECT add_missing_table_field ('public', 'bilansparcours66', 'typeorientprincipale_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'bilansparcours66', 'bilansparcours66_typeorientprincipale_id_fkey', 'typesorients', 'typeorientprincipale_id');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'nvtypeorient_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'bilansparcours66', 'bilansparcours66_nvtypeorient_id_fkey', 'typesorients', 'nvtypeorient_id');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'nvstructurereferente_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'bilansparcours66', 'bilansparcours66_nvstructurereferente_id_fkey', 'structuresreferentes', 'nvstructurereferente_id');

SELECT add_missing_table_field ('public', 'bilansparcours66', 'changementref', 'TYPE_NO');

SELECT add_missing_table_field ('public', 'saisinesbilansparcourseps66', 'typeorientprincipale_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'saisinesbilansparcourseps66', 'saisinesbilansparcourseps66_typeorientprincipale_id_fkey', 'typesorients', 'typeorientprincipale_id');

SELECT add_missing_table_field ('public', 'decisionssaisinesbilansparcourseps66', 'typeorientprincipale_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'decisionssaisinesbilansparcourseps66', 'decisionssaisinesbilansparcourseps66_typeorientprincipale_id_fkey', 'typesorients', 'typeorientprincipale_id');


-- *****************************************************************************
-- 20110928 : ajout de la date d'impression pour les demandes d'APRE
-- *****************************************************************************
SELECT add_missing_table_field ('public', 'apres', 'dateimpressionapre', 'DATE');

-- *****************************************************************************
-- 20110930 : Création d'indexes uniques pour les APREs
-- *****************************************************************************

DROP INDEX IF EXISTS apres_comitesapres_apre_id_comiteapre_id;
CREATE UNIQUE INDEX apres_comitesapres_apre_id_comiteapre_id ON apres_comitesapres( apre_id, comiteapre_id );

DROP INDEX formsqualifs_apre_id_idx;
CREATE UNIQUE INDEX formsqualifs_apre_id_idx ON formsqualifs(apre_id);

DROP INDEX formspermsfimo_apre_id_idx;
CREATE UNIQUE INDEX formspermsfimo_apre_id_idx ON formspermsfimo(apre_id);

DROP INDEX actsprofs_apre_id_idx;
CREATE UNIQUE INDEX actsprofs_apre_id_idx ON actsprofs(apre_id);

DROP INDEX permisb_apre_id_idx;
CREATE UNIQUE INDEX permisb_apre_id_idx ON permisb(apre_id);

DROP INDEX amenagslogts_apre_id_idx;
CREATE UNIQUE INDEX amenagslogts_apre_id_idx ON amenagslogts(apre_id);

DROP INDEX accscreaentr_apre_id_idx;
CREATE UNIQUE INDEX accscreaentr_apre_id_idx ON accscreaentr(apre_id);

DROP INDEX acqsmatsprofs_apre_id_idx;
CREATE UNIQUE INDEX acqsmatsprofs_apre_id_idx ON acqsmatsprofs(apre_id);

DROP INDEX locsvehicinsert_apre_id_idx;
CREATE UNIQUE INDEX locsvehicinsert_apre_id_idx ON locsvehicinsert(apre_id);

-- *****************************************************************************
-- 20111010 : Création d'indexes uniques pour situationsdossiersrsa et detailsdroitsrsa
-- *****************************************************************************

-- Flux CAF - VRSB0502-Cristal V3400: Pour chaque foyer: PrestationRSA (1,O) -> SituationDossierRSA (1,O) -> EtatDossierRSA (1,O)
DROP INDEX IF EXISTS situationsdossiersrsa_dossier_id;
CREATE UNIQUE INDEX situationsdossiersrsa_dossier_id ON situationsdossiersrsa(dossier_id);

-- Flux CAF - VRSB0502-Cristal V3400: Pour chaque foyer: PrestationRSA (1,O) -> DetailDroitRSA (1,O) -> TroncCommunDroitRSA (1,O)
DROP INDEX IF EXISTS detailsdroitsrsa_dossier_id;
CREATE UNIQUE INDEX detailsdroitsrsa_dossier_id ON detailsdroitsrsa(dossier_id);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************