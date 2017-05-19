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

-- 20150803: ajout d'un index proposé par le CG 93
DROP INDEX IF EXISTS questionnairesd1pdvs93_personne_id_idx;
CREATE INDEX questionnairesd1pdvs93_personne_id_idx ON questionnairesd1pdvs93(personne_id);

-- 20150807: Ajout de la notion de sortie de procédure suite à une inscription à PE
-- Modification de l'existant (sortienvcontrat) pour le rendre générique
SELECT add_missing_table_field ( 'public', 'nonrespectssanctionseps93', 'sortieprocedure', 'TEXT DEFAULT NULL' );
ALTER TABLE nonrespectssanctionseps93 ALTER COLUMN sortieprocedure TYPE TEXT;
DROP TYPE IF EXISTS TYPE_SORTIEPROCEDURENRS93;
CREATE TYPE TYPE_SORTIEPROCEDURENRS93 AS ENUM ( 'nvcontrat', 'inscriptionpe' );
ALTER TABLE nonrespectssanctionseps93 ALTER COLUMN sortieprocedure TYPE TYPE_SORTIEPROCEDURENRS93 USING CAST(sortieprocedure AS TYPE_SORTIEPROCEDURENRS93);
ALTER TABLE nonrespectssanctionseps93 ALTER COLUMN sortieprocedure SET DEFAULT NULL;

ALTER TABLE nonrespectssanctionseps93 ALTER COLUMN sortienvcontrat DROP NOT NULL;

SELECT add_missing_table_field ( 'public', 'nonrespectssanctionseps93', 'nvcontratinsertion_id', 'INTEGER DEFAULT NULL' );
SELECT add_missing_constraint ( 'public', 'nonrespectssanctionseps93', 'nonrespectssanctionseps93_nvcontratinsertion_id_fkey', 'contratsinsertion', 'nvcontratinsertion_id', false );

SELECT add_missing_table_field ( 'public', 'nonrespectssanctionseps93', 'nvhistoriqueetatpe_id', 'INTEGER DEFAULT NULL' );
SELECT add_missing_constraint ( 'public', 'nonrespectssanctionseps93', 'nonrespectssanctionseps93_nvhistoriqueetatpe_id_fkey', 'historiqueetatspe', 'nvhistoriqueetatpe_id', false );

UPDATE nonrespectssanctionseps93 SET sortieprocedure = 'nvcontrat' WHERE sortienvcontrat ='1';

-- 20150807: Ajout de la notion de dossier d'EP inactif (équivalent d'annulé mais sans passer par une commission)
SELECT add_missing_table_field ( 'public', 'dossierseps', 'actif', 'SMALLINT DEFAULT 1' );
DROP INDEX IF EXISTS dossierseps_actif_idx;
CREATE INDEX dossierseps_actif_idx ON dossierseps(actif);


-- CG 93: Fonction permettant de regénérer les entrées de dossierseps qui étaient
-- supprimées lorsqu'un nouveau CER était validé. A présent, on se servira du nouveau
CREATE OR REPLACE FUNCTION public.regenerate_dossierseps_nonrespectssanctionseps93() RETURNS VOID AS
$$
	DECLARE
		v_row record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
					nonrespectssanctionseps93.id,
					nvcontratsinsertion.id AS nvcontratinsertion_id,
					nvcontratsinsertion.personne_id AS personne_id,
					nvcers93.modified AS modified,
					nvcontratsinsertion.datevalidation_ci AS datevalidation_ci,
					-- Création du dossier d'EP pour la seconde relance
					relancesnonrespectssanctionseps93.daterelance AS daterelance,
					EXISTS (
						SELECT *
							FROM relancesnonrespectssanctionseps93 AS relancesdossiers
							WHERE
								relancesnonrespectssanctionseps93.nonrespectsanctionep93_id = nonrespectssanctionseps93.id
								AND relancesdossiers.numrelance = 2
					) AS has_dossierep
				FROM nonrespectssanctionseps93
					LEFT OUTER JOIN orientsstructs ON ( nonrespectssanctionseps93.orientstruct_id = orientsstructs.id )
					LEFT OUTER JOIN contratsinsertion ON ( nonrespectssanctionseps93.contratinsertion_id = contratsinsertion.id )
					LEFT OUTER JOIN contratsinsertion AS nvcontratsinsertion ON (
						nvcontratsinsertion.personne_id = ( CASE
							WHEN orientsstructs.personne_id IS NOT NULL THEN orientsstructs.personne_id
							WHEN contratsinsertion.personne_id IS NOT NULL THEN contratsinsertion.personne_id
							ELSE NULL
						END )
						AND nvcontratsinsertion.decision_ci = 'V'
					)
					LEFT OUTER JOIN cers93 AS nvcers93 ON ( nvcers93.contratinsertion_id = nvcontratsinsertion.id )
					LEFT OUTER JOIN relancesnonrespectssanctionseps93 ON ( relancesnonrespectssanctionseps93.nonrespectsanctionep93_id = nonrespectssanctionseps93.id )
				WHERE
					nonrespectssanctionseps93.sortieprocedure = 'nvcontrat'
					AND nonrespectssanctionseps93.active = '0'
					-- On prend simplement le contrat qui a un rang de plus que celui qui a envoyé en préocédure
					AND COALESCE( contratsinsertion.rg_ci, 0 ) + 1 = nvcontratsinsertion.rg_ci
					-- On prend la première relance pour peupler le champ created
					AND (
						relancesnonrespectssanctionseps93.id IS NULL
						OR relancesnonrespectssanctionseps93.id IN (
							SELECT relances.id
								FROM relancesnonrespectssanctionseps93 AS relances
								WHERE relances.nonrespectsanctionep93_id = nonrespectssanctionseps93.id
								ORDER BY relances.daterelance ASC
								LIMIT 1
						)
					)

		LOOP
			IF v_row.has_dossierep THEN
				-- 1. Suppression du dossiersps inactif
				v_query := 'DELETE FROM dossierseps WHERE personne_id = ' || v_row.personne_id || ' AND themeep = ''nonrespectssanctionseps93'' AND actif = 0;';
				RAISE NOTICE  '%', v_query;
				EXECUTE v_query;

				-- 2. Insertion d'un dossiersps inactif
				v_query := 'INSERT INTO dossierseps ( personne_id, themeep, actif, created, modified ) VALUES ( ' || v_row.personne_id || ', ''nonrespectssanctionseps93'', 0, ''' || v_row.daterelance || ''', ''' || COALESCE( v_row.datevalidation_ci, v_row.modified ) || ''' );';
				RAISE NOTICE  '%', v_query;
				EXECUTE v_query;

				-- 3. Mise à jour des données de la table nonrespectssanctionseps93
				v_query := 'UPDATE nonrespectssanctionseps93 SET dossierep_id = currval( pg_get_serial_sequence( ''dossierseps'', ''id'' ) ), nvcontratinsertion_id = ' || v_row.nvcontratinsertion_id || ' WHERE id = ' || v_row.id || ';';
				RAISE NOTICE  '%', v_query;
				EXECUTE v_query;
			ELSE
				-- Mise à jour des données de la table nonrespectssanctionseps93
				v_query := 'UPDATE nonrespectssanctionseps93 SET nvcontratinsertion_id = ' || v_row.nvcontratinsertion_id || ' WHERE id = ' || v_row.id || ';';
				RAISE NOTICE  '%', v_query;
				EXECUTE v_query;
			END IF;
		END LOOP;
	END;
$$
LANGUAGE plpgsql VOLATILE;

SELECT public.regenerate_dossierseps_nonrespectssanctionseps93();
DROP FUNCTION public.regenerate_dossierseps_nonrespectssanctionseps93();

-------------------------------------------------------------------------------------
-- 20150825 : Mademoiselle devient Madame
-------------------------------------------------------------------------------------

UPDATE membreseps SET qual = 'Mme.'::TYPE_QUAL WHERE qual = 'Mlle.'::TYPE_QUAL;
SELECT public.alter_enumtype( 'TYPE_QUAL', ARRAY['M.', 'Mme.'] );

UPDATE cers93 SET qual = 'MME' WHERE qual = 'MLE';
UPDATE composfoyerscers93 SET qual = 'MME' WHERE qual = 'MLE';
UPDATE contactspartenaires SET qual = 'MME' WHERE qual = 'MLE';
UPDATE participantscomites SET qual = 'MME' WHERE qual = 'MLE';
UPDATE personnes SET qual = 'MME' WHERE qual = 'MLE';
UPDATE referents SET qual = 'MME' WHERE qual = 'MLE';
UPDATE situationsallocataires SET qual = 'MME' WHERE qual = 'MLE';
UPDATE suivisaidesapres SET qual = 'MME' WHERE qual = 'MLE';

-------------------------------------------------------------------------------------
-- 20150827 : Nouveau champ CUI66
-------------------------------------------------------------------------------------

ALTER TABLE cuis66 ADD COLUMN montantrsa FLOAT;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************