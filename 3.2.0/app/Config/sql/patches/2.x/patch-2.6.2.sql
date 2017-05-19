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
-- 20131118 : Modification de la table avec ajout d'un champ permettant de
--      distinguer les organismes auxquels les dossiers seront transmis pour une
--      génération auto d'un dossier PCG (FIXME)
----------------------------------------------------------------------------------------
-- SELECT add_missing_table_field ( 'public', 'orgstransmisdossierspcgs66', 'isinfotransmisdecision', 'VARCHAR(1)' );
-- SELECT alter_table_drop_constraint_if_exists( 'public', 'orgstransmisdossierspcgs66', 'orgstransmisdossierspcgs66_isinfotransmisdecision_in_list_chk' );
-- ALTER TABLE orgstransmisdossierspcgs66 ADD CONSTRAINT orgstransmisdossierspcgs66_isinfotransmisdecision_in_list_chk CHECK ( cakephp_validate_in_list( isinfotransmisdecision, ARRAY['0', '1'] ) );
-- UPDATE orgstransmisdossierspcgs66 SET isinfotransmisdecision = '0' WHERE isinfotransmisdecision IS NULL;

SELECT add_missing_table_field ( 'public', 'polesdossierspcgs66', 'originepdo_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'polesdossierspcgs66', 'polesdossierspcgs66_originepdo_id_fkey', 'originespdos', 'originepdo_id', false );

SELECT add_missing_table_field ( 'public', 'polesdossierspcgs66', 'typepdo_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'polesdossierspcgs66', 'polesdossierspcgs66_typepdo_id_fkey', 'typespdos', 'typepdo_id', false );

SELECT add_missing_table_field ( 'public', 'decisionsdossierspcgs66', 'infotransmise', 'VARCHAR(250)' );

SELECT add_missing_table_field ( 'public', 'decisionsdossierspcgs66', 'orgtransmisdossierpcg66_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'decisionsdossierspcgs66', 'decisionsdossierspcgs66_orgtransmisdossierpcg66_id_fkey', 'orgstransmisdossierspcgs66', 'orgtransmisdossierpcg66_id', false );

-- Ajout du champ;poledossierpcg66_id dans la table orgs TODO
SELECT add_missing_table_field ( 'public', 'orgstransmisdossierspcgs66', 'poledossierpcg66_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'orgstransmisdossierspcgs66', 'orgstransmisdossierspcgs66_poledossierpcg66_id_fkey', 'orgstransmisdossierspcgs66', 'poledossierpcg66_id', false );

SELECT add_missing_table_field ( 'public', 'dossierspcgs66', 'dossierpcg66pcd_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'dossierspcgs66', 'dossierspcgs66_dossierpcg66pcd_id_fkey', 'dossierspcgs66', 'dossierpcg66pcd_id', false );

DROP INDEX IF EXISTS dossierspcgs66_dossierpcg66pcd_id_idx;
CREATE UNIQUE INDEX dossierspcgs66_dossierpcg66pcd_id_idx ON dossierspcgs66( dossierpcg66pcd_id );

--==============================================================================
-- 20131203: suppression de la contrainte pour la thématique des sanctions de
-- l'EP du CG 58
--==============================================================================

SELECT alter_table_drop_constraint_if_exists( 'public', 'sanctionseps58', 'sanctionseps58_orientstruct_id_origine_chk' );

--------------------------------------------------------------------------------
-- Mise à jour de sanctionseps58.orientstruct_id car il faut une orientation en emploi
--------------------------------------------------------------------------------
UPDATE sanctionseps58
	SET orientstruct_id = (
		SELECT
				orientsstructs.id
			FROM sanctionseps58 AS s
				INNER JOIN dossierseps ON ( dossierseps.id = s.dossierep_id )
				INNER JOIN personnes ON ( dossierseps.personne_id = personnes.id )
				INNER JOIN orientsstructs ON ( orientsstructs.personne_id = personnes.id )
			WHERE
				s.id = sanctionseps58.id
				AND s.orientstruct_id IS NULL
				AND s.origine IN ( 'noninscritpe', 'radiepe' )
				-- la dernière, par-rapport à la date de création du dossier
				AND orientsstructs.id IN (
					SELECT
							o.id
						FROM orientsstructs AS o
						WHERE
							o.personne_id = personnes.id
							AND o.statut_orient = 'Orienté'
							AND o.date_valid <= DATE_TRUNC( 'day', s.created )
						ORDER BY o.date_valid DESC
						LIMIT 1
				)
				AND orientsstructs.structurereferente_id = 2
	)
	WHERE
		sanctionseps58.orientstruct_id IS NULL
		AND sanctionseps58.origine IN ( 'noninscritpe', 'radiepe' );

--------------------------------------------------------------------------------
-- Suppression des dossiers d'EP de la thématique des sanctions du CG 58
-- pour les allocataires non inscrits ou radiés de Pôle Emploi pour lesquels
-- le dossier d'EP n'aurait pas dû être créé.
--------------------------------------------------------------------------------
DELETE FROM dossierseps WHERE id IN (
		SELECT
				d.id
			FROM dossierseps AS d
				INNER JOIN sanctionseps58 AS s ON ( s.dossierep_id = d.id )
				LEFT OUTER JOIN orientsstructs ON ( s.orientstruct_id = orientsstructs.id )
			WHERE
				d.themeep = 'sanctionseps58'
				AND s.origine IN ( 'noninscritpe', 'radiepe' )
				AND (
					s.orientstruct_id IS NULL
					OR orientsstructs.structurereferente_id <> 2
				)
				AND d.id NOT IN (
					SELECT passagescommissionseps.dossierep_id
						FROM passagescommissionseps
							INNER JOIN commissionseps ON ( passagescommissionseps.commissionep_id = commissionseps.id )
						WHERE
							passagescommissionseps.dossierep_id = d.id
							AND commissionseps.etatcommissionep NOT IN ( 'cree', 'quorum', 'associe' )
				)
);


-- Ajout du champ;progfichecandidature66_id dans la table actionscandidats_personnes
SELECT add_missing_table_field ( 'public', 'actionscandidats_personnes', 'progfichecandidature66_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'actionscandidats_personnes', 'actionscandidats_personnes_progfichecandidature66_id_fkey', 'progsfichescandidatures66', 'progfichecandidature66_id', false );

----------------------------------------------------------------------------------------
-- 20131211 : Ajout du champ nom du prestataire dans la table actionscandidats_personnes
--            Ajout du champ Email du partenaire/prestataire dans la table actionscandidats
----------------------------------------------------------------------------------------
SELECT add_missing_table_field ( 'public', 'actionscandidats_personnes', 'nomprestataire', 'VARCHAR(250)' );
SELECT add_missing_table_field ( 'public', 'actionscandidats', 'emailprestataire', 'VARCHAR(250)' );

--------------------------------------------------------------------------------
-- 20131217 : Ajout du champ date_validation dans la table questionnairesd2pdvs93
--------------------------------------------------------------------------------
SELECT add_missing_table_field ( 'public', 'questionnairesd2pdvs93', 'date_validation', 'DATE' );

UPDATE questionnairesd2pdvs93 SET date_validation = DATE_TRUNC( 'day', created );
ALTER TABLE questionnairesd2pdvs93 ALTER COLUMN date_validation SET NOT NULL;

--------------------------------------------------------------------------------
-- 20131220 : Modification du champ libelle de la table typespdos 30 -> 150
--------------------------------------------------------------------------------
ALTER TABLE typespdos ALTER COLUMN libelle TYPE VARCHAR(150) USING CAST(libelle AS VARCHAR(150));


-------------------------------------------------------------------------------------
-- 20131223 : Ajout d'un champ isactif pour masquer les decisions pdos n'étant plus actives
-------------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'decisionspdos', 'isactif', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'decisionspdos', 'decisionspdos_isactif_in_list_chk' );
ALTER TABLE decisionspdos ADD CONSTRAINT decisionspdos_isactif_in_list_chk CHECK ( cakephp_validate_in_list( isactif, ARRAY['0','1'] ) );
UPDATE decisionspdos SET isactif = '1' WHERE isactif IS NULL;
ALTER TABLE decisionspdos ALTER COLUMN isactif SET DEFAULT '1'::VARCHAR(1);

-------------------------------------------------------------------------------------
-- 20131223 : Ajout d'un champ isactif pour masquer les decisions pdos n'étant plus actives
-------------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'statutspdos', 'isactif', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'statutspdos', 'statutspdos_isactif_in_list_chk' );
ALTER TABLE statutspdos ADD CONSTRAINT statutspdos_isactif_in_list_chk CHECK ( cakephp_validate_in_list( isactif, ARRAY['0','1'] ) );
UPDATE statutspdos SET isactif = '1' WHERE isactif IS NULL;
ALTER TABLE statutspdos ALTER COLUMN isactif SET DEFAULT '1'::VARCHAR(1);

-------------------------------------------------------------------------------------
-- 20131223 : Ajout d'un champ isactif pour masquer les decisions pdos n'étant plus actives
-------------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'situationspdos', 'isactif', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'situationspdos', 'situationspdos_isactif_in_list_chk' );
ALTER TABLE situationspdos ADD CONSTRAINT situationspdos_isactif_in_list_chk CHECK ( cakephp_validate_in_list( isactif, ARRAY['0','1'] ) );
UPDATE situationspdos SET isactif = '1' WHERE isactif IS NULL;
ALTER TABLE situationspdos ALTER COLUMN isactif SET DEFAULT '1'::VARCHAR(1);

-------------------------------------------------------------------------------------
-- 20131223 : Ajout d'une valeur instrencours pour l'état du dossier PCG
-------------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'decisionsdossierspcgs66', 'instrencours', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'decisionsdossierspcgs66', 'decisionsdossierspcgs66_instrencours_in_list_chk' );
ALTER TABLE decisionsdossierspcgs66 ADD CONSTRAINT decisionsdossierspcgs66_instrencours_in_list_chk CHECK ( cakephp_validate_in_list( instrencours, ARRAY['0','1'] ) );

-- *****************************************************************************
COMMIT;
-- *****************************************************************************