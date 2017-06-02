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

-------------------------------------------------------------------------------------
-- 20130325 : ajout du champ motifannulation dans les tables dossierspcgs66, traitementspcgs66 
--				et decisionsdossierspcgs66
-------------------------------------------------------------------------------------

SELECT add_missing_table_field( 'public', 'dossierspcgs66', 'motifannulation', 'TEXT' );
SELECT add_missing_table_field( 'public', 'decisionsdossierspcgs66', 'motifannulation', 'TEXT' );
SELECT add_missing_table_field( 'public', 'traitementspcgs66', 'motifannulation', 'TEXT' );

-- Modification de létat du dossier avec ajout de l'état annulé

ALTER TABLE dossierspcgs66 ALTER COLUMN etatdossierpcg TYPE VARCHAR(30) USING CAST(etatdossierpcg AS VARCHAR(30));
ALTER TABLE decisionsdossierspcgs66 ALTER COLUMN etatdossierpcg TYPE VARCHAR(30) USING CAST(etatdossierpcg AS VARCHAR(30));

DROP TYPE IF EXISTS TYPE_ETATDOSSIERPCG CASCADE;

SELECT alter_table_drop_constraint_if_exists( 'public', 'dossierspcgs66', 'dossierspcgs66_etatdossierpcg_in_list_chk' );
ALTER TABLE dossierspcgs66 ADD CONSTRAINT dossierspcgs66_etatdossierpcg_in_list_chk CHECK ( cakephp_validate_in_list( etatdossierpcg, ARRAY['attaffect','attinstr','instrencours','attavistech','attval','dossiertraite','decisionvalid','decisionnonvalid','decisionnonvalidretouravis','decisionvalidretouravis','transmisop','atttransmisop','annule'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'decisionsdossierspcgs66', 'decisionsdossierspcgs66_etatdossierpcg_in_list_chk' );
ALTER TABLE decisionsdossierspcgs66 ADD CONSTRAINT decisionsdossierspcgs66_etatdossierpcg_in_list_chk CHECK ( cakephp_validate_in_list( etatdossierpcg, ARRAY['attaffect','attinstr','instrencours','attavistech','attval','dossiertraite','decisionvalid','decisionnonvalid','decisionnonvalidretouravis','decisionvalidretouravis','transmisop','atttransmisop','annule'] ) );


SELECT add_missing_table_field( 'public', 'actionscandidats_personnes', 'poursuitesuivicg', 'CHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'actionscandidats_personnes', 'actionscandidats_personnes_poursuitesuivicg_in_list_chk' );
ALTER TABLE actionscandidats_personnes ADD CONSTRAINT actionscandidats_personnes_poursuitesuivicg_in_list_chk CHECK ( cakephp_validate_in_list( poursuitesuivicg, ARRAY['0','1'] ) );

-- *****************************************************************************
COMMIT;
-- *****************************************************************************