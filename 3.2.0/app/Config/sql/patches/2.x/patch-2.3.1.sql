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

SELECT add_missing_table_field ( 'public', 'bilansparcours66', 'nvcontratinsertion_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'bilansparcours66', 'bilansparcours66_nvcontratinsertion_id_fkey', 'contratsinsertion', 'nvcontratinsertion_id', false );

DROP INDEX IF EXISTS bilansparcours66_nvcontratinsertion_id_idx;
CREATE UNIQUE INDEX bilansparcours66_nvcontratinsertion_id_idx ON bilansparcours66(nvcontratinsertion_id);

SELECT public.alter_enumtype ( 'TYPE_PROPOSITIONBILANPARCOURS', ARRAY['audition','parcours','traitement','auditionpe','parcourspe','aucun'] );

-------------------------------------------------------------------------------------------------------------
-- 20121130: Intégration des données DOM manquantes en base
-------------------------------------------------------------------------------------------------------------

--Dans les flux Bénéficiaires : ajouter la table aviscgssdompersonnes

DROP TABLE IF EXISTS aviscgssdompersonnes CASCADE;
CREATE TABLE aviscgssdompersonnes (
	id 					SERIAL NOT NULL PRIMARY KEY,
	personne_id        INTEGER NOT NULL REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	jusactdom			CHAR(1) NOT NULL,
	resujusactdom		CHAR(1)
);
COMMENT ON TABLE aviscgssdompersonnes IS 'Regroupement des décisions de la CGSS (Caisse Générale Sécurité Sociale) liées à la personne (Mayotte)';

DROP INDEX IF EXISTS aviscgssdompersonnes_personne_id_idx;
CREATE UNIQUE INDEX aviscgssdompersonnes_personne_id_idx ON aviscgssdompersonnes( personne_id );

ALTER TABLE aviscgssdompersonnes ADD CONSTRAINT aviscgssdompersonnes_jusactdom_in_list_chk CHECK ( cakephp_validate_in_list( jusactdom, ARRAY['A', 'D', 'F', 'J', 'M'] ) );
ALTER TABLE aviscgssdompersonnes ADD CONSTRAINT aviscgssdompersonnes_resujusactdom_in_list_chk CHECK ( cakephp_validate_in_list( resujusactdom, ARRAY['N', 'T'] ) );


--- Ajouter les 5 champs suivants à la table detailsdroitsrsa
SELECT add_missing_table_field('public', 'detailsdroitsrsa', 'surfagridom', 'NUMERIC(5,2)' );
SELECT add_missing_table_field('public', 'detailsdroitsrsa', 'ddsurfagridom', 'DATE' );
SELECT add_missing_table_field('public', 'detailsdroitsrsa', 'surfagridompla', 'NUMERIC(5,2)' );
SELECT add_missing_table_field('public', 'detailsdroitsrsa', 'nbtotaidefamsurfdom', 'INTEGER' );
SELECT add_missing_table_field('public', 'detailsdroitsrsa', 'nbtotpersmajosurfdom', 'INTEGER' );

--------------------------------------------------------------------------------
-- Ajout d'une valeur finale pour la déicison du CER Particulier CG66
--------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'proposdecisionscers66', 'decisionfinale', 'TYPE_NO' );

-------------------------------------------------------------------------------------------------------------
-- 20130115 : Ajout du booléen permettant de vérifier qu'un fichier est lié au Foyer (Corbeille PCG CG66)
-------------------------------------------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'foyers', 'haspiecejointe', 'type_booleannumber');
ALTER TABLE foyers ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE foyers SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE foyers ALTER COLUMN haspiecejointe SET NOT NULL;

--------------------------------------------------------------------------------
-- 20130110 - Suppression des colonnes decisionssanctionseps58.regularisation et
-- et decisionssanctionsrendezvouseps58.regularisation
--------------------------------------------------------------------------------

-- Table decisionssanctionseps58
UPDATE decisionssanctionseps58
	SET
		arretsanction = CAST( CAST(regularisation AS TEXT) AS type_arretsanctionep58),
		datearretsanction = DATE_TRUNC( 'day', modified )
	WHERE
		arretsanction IS NULL
		AND regularisation = 'finsanction2';

UPDATE decisionssanctionseps58
	SET
		arretsanction = CAST( CAST(regularisation AS TEXT) AS type_arretsanctionep58)
	WHERE
		arretsanction IS NULL
		AND regularisation IN ( 'annulation1', 'annulation2' );

-- Table decisionssanctionsrendezvouseps58
UPDATE decisionssanctionsrendezvouseps58
	SET
		arretsanction = CAST( CAST(regularisation AS TEXT) AS type_arretsanctionep58),
		datearretsanction = DATE_TRUNC( 'day', modified )
	WHERE
		arretsanction IS NULL
		AND regularisation = 'finsanction2';

UPDATE decisionssanctionsrendezvouseps58
	SET
		arretsanction = CAST( CAST(regularisation AS TEXT) AS type_arretsanctionep58)
	WHERE
		arretsanction IS NULL
		AND regularisation IN ( 'annulation1', 'annulation2' );

-- Suppression des colonnes regularisation et du type associé
ALTER TABLE decisionssanctionseps58 DROP COLUMN regularisation;
ALTER TABLE decisionssanctionsrendezvouseps58 DROP COLUMN regularisation;

--------------------------------------------------------------------------------
-- 20130111 - ajout de contraintes NOT NULL aux champs personne_id des tables
-- creancesalimentaires et prestations, suppression des tuples irrécupérables.
--------------------------------------------------------------------------------

DELETE FROM creancesalimentaires WHERE personne_id IS NULL;
ALTER TABLE creancesalimentaires ALTER COLUMN personne_id SET NOT NULL;

DELETE FROM prestations WHERE personne_id IS NULL;
ALTER TABLE prestations ALTER COLUMN personne_id SET NOT NULL;

--------------------------------------------------------------------------------
-- 20130118 : Ajout des fichiers liés au module memos
--------------------------------------------------------------------------------
SELECT add_missing_table_field('public', 'memos', 'haspiecejointe', 'type_booleannumber' );
ALTER TABLE memos ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE memos SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE memos ALTER COLUMN haspiecejointe SET NOT NULL;


--------------------------------------------------------------------------------
-- 20121203 : Ajout d'une table manifestationsbilansparcours66 afin de stocker les
-- éléments reseignés par l'allocataire suite à un passage en EPL Audition
--------------------------------------------------------------------------------
DROP TABLE IF EXISTS manifestationsbilansparcours66 CASCADE;
CREATE TABLE manifestationsbilansparcours66 (
	id					SERIAL NOT NULL PRIMARY KEY,
	bilanparcours66_id 	INTEGER NOT NULL REFERENCES bilansparcours66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	commentaire			TEXT NOT NULL,
	datemanifestation	DATE NOT NULL,
	haspiecejointe		TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0',
	created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE manifestationsbilansparcours66 IS 'Table pour les manifestations de l''allocataire en lien avec un passage en EPL Audition (CG66)';

DROP INDEX IF EXISTS manifestationsbilansparcours66_bilanparcours66_id_idx;
CREATE INDEX manifestationsbilansparcours66_bilanparcours66_id_idx ON manifestationsbilansparcours66( bilanparcours66_id );

--------------------------------------------------------------------------------
-- 20121218: pour les CG 58 et 66, on a des référents du parcours sans date de
-- fin alors qu'ils devraient en avoir.
--------------------------------------------------------------------------------

-- 1°) On a des entrées sans dates de début de désignation
DELETE FROM personnes_referents WHERE dddesignation IS NULL;

-- 2°) On se prémunit contre cette erreur
ALTER TABLE personnes_referents ALTER COLUMN dddesignation SET NOT NULL;

-- 3°) On complète les données restantes
CREATE OR REPLACE FUNCTION public.update_dfdesignation_referents() RETURNS VOID AS
$$
	DECLARE
		v_row_personnes record;
		v_row_personnes_referents record;
		v_query text;
		v_iteration integer;
		v_dddesignationpcd date;
	BEGIN
		FOR v_row_personnes IN
			SELECT DISTINCT(s.personne_id) FROM (
				SELECT personnes_referents.personne_id
					FROM personnes_referents
					WHERE personnes_referents.dfdesignation IS NULL
					GROUP BY personnes_referents.personne_id HAVING COUNT(personnes_referents.id) > 1
				UNION
				SELECT pr1.personne_id
					FROM personnes_referents pr1, personnes_referents pr2
					WHERE pr1.personne_id = pr2.personne_id
						AND pr1.dddesignation < pr2.dddesignation
						AND pr1.dfdesignation IS NULL
			) AS s
			ORDER BY s.personne_id ASC
		LOOP
			v_iteration := 0;
			RAISE NOTICE  'Correction des dates de fin de désignation de la personne %', v_row_personnes.personne_id;
			FOR v_row_personnes_referents IN
				SELECT *
				FROM personnes_referents
				WHERE personnes_referents.personne_id = v_row_personnes.personne_id
				ORDER BY personnes_referents.dddesignation DESC
			LOOP
				RAISE NOTICE  '%', v_row_personnes_referents;
				-- traitement
				IF v_iteration > 0 AND v_row_personnes_referents.dfdesignation IS NULL THEN
					v_query := 'UPDATE personnes_referents SET dfdesignation = \'' || v_dddesignationpcd || '\' WHERE id = ' || v_row_personnes_referents.id || ';';
					RAISE NOTICE  '%', v_query;
					EXECUTE v_query;
				END IF;
				-- traitement
				v_iteration := v_iteration + 1;
				v_dddesignationpcd := v_row_personnes_referents.dddesignation;
			END LOOP;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.update_dfdesignation_referents();
DROP FUNCTION public.update_dfdesignation_referents();

--------------------------------------------------------------------------------
-- 20130118 : Ajout de la date d'envoi du courrier d'un traitement PCG (CG66)
--------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'traitementspcgs66', 'dateenvoicourrier', 'DATE' );

--------------------------------------------------------------------------------
-- 20130121 : Création d'une contrainte d'unicité des entrées de  la "Gestion
-- pour passage en commission par objet et type de RDV".
--------------------------------------------------------------------------------
CREATE UNIQUE INDEX statutsrdvs_typesrdv_statutrdv_id_typerdv_id_idx ON statutsrdvs_typesrdv(statutrdv_id, typerdv_id);

--------------------------------------------------------------------------------
-- 20130123 : Ajout d'une zone de commentaire liée aux pièces jointes d'un dossier PCG (CG66)
--------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'dossierspcgs66', 'commentairepiecejointe', 'TEXT' );

--------------------------------------------------------------------------------
-- 20130131: dédoublonnage de la table pdfs et mise en place d'un index unique
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION public.nettoyage_doublons_pdfs() RETURNS VOID AS
$$
	DECLARE
		v_row_doublon record;
		v_query text;
	BEGIN
		FOR v_row_doublon IN
			SELECT
					modele, fk_value
				FROM pdfs
				GROUP BY modele, fk_value
				HAVING COUNT(*) > 1
				ORDER BY COUNT(*) DESC, modele ASC, fk_value ASC
		LOOP
			v_query := 'DELETE FROM pdfs WHERE modele = ''' || v_row_doublon.modele || ''' AND fk_value = ' || v_row_doublon.fk_value || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.nettoyage_doublons_pdfs();
DROP FUNCTION public.nettoyage_doublons_pdfs();

DROP INDEX IF EXISTS pdfs_modele_fk_value_idx;
CREATE UNIQUE INDEX pdfs_modele_fk_value_idx ON pdfs(modele, fk_value);



--------------------------------------------------------------------------------
-- 20130226: ajout d'un champ serviceinstructeur_id dans la table traitementspcgs66 afin
--	de pouvoir renseigner le service à contacter par le service
--------------------------------------------------------------------------------
SELECT add_missing_table_field ( 'public', 'traitementspcgs66', 'serviceinstructeur_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'traitementspcgs66', 'traitementspcgs66_serviceinstructeur_id_fkey', 'servicesinstructeurs', 'serviceinstructeur_id', false );

CREATE TYPE TYPE_TYPENOTIFICATION AS ENUM ( 'normale', 'systematique' );
SELECT add_missing_table_field ( 'public', 'orientsstructs', 'typenotification', 'TYPE_TYPENOTIFICATION' );
UPDATE orientsstructs SET typenotification = 'normale' WHERE typenotification IS NULL;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
