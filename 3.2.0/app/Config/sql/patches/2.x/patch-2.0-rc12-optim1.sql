SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

/*
	INFO:
		1°) Le renommage des tables se charge apparemment de mettre à jour les clés
			étrangères pointant sur cette table, mais pas le nom de la contrainte.
		2°) Quelles sont toutes les tables qui ont une colonne dossier_rsa_id
			SELECT table_name
				FROM information_schema.columns
				WHERE
					table_catalog = 'webrsacake'
					AND table_schema = 'public'
					AND column_name = 'dossier_rsa_id';
		3°) Le renommage d'une colonne met à jour les indexes, mais pas le nom des indexes
*/

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

/*******************************************************************************
* Suppression des anciennes tables de DSP -> FIXME: les tables liées sont-elles toujours utilisées ?
*******************************************************************************/

DROP TABLE IF EXISTS dspfs_diflogs;
DROP TABLE IF EXISTS diflogs;
DROP TABLE IF EXISTS dspfs_nataccosocfams;
DROP TABLE IF EXISTS dspfs;
DROP TABLE IF EXISTS dspps_accoemplois;
DROP TABLE IF EXISTS dspps_difdisps;
DROP TABLE IF EXISTS dspps_difsocs;
DROP TABLE IF EXISTS dspps_nataccosocindis;
DROP TABLE IF EXISTS dspps_natmobs;
DROP TABLE IF EXISTS dspps_nivetus;
DROP TABLE IF EXISTS dspps;
DROP TABLE IF EXISTS nataccosocfams;
DROP TABLE IF EXISTS accoemplois;
DROP TABLE IF EXISTS difdisps;
DROP TABLE IF EXISTS difsocs;
DROP TABLE IF EXISTS nataccosocindis;
DROP TABLE IF EXISTS natmobs;
DROP TABLE IF EXISTS nivetus;

/*******************************************************************************
* Suppression des tables et des types liés à la béta des EPs
*******************************************************************************/

DROP TABLE IF EXISTS decisionsreorient;
DROP TABLE IF EXISTS sceanceseps_demandesreorient;
DROP TABLE IF EXISTS seanceseps_demandesreorient;
DROP TABLE IF EXISTS demandesreorient_sceanceseps;
DROP TABLE IF EXISTS demandesreorient_seanceseps CASCADE;
DROP TABLE IF EXISTS demandesreorient CASCADE;
DROP TABLE IF EXISTS motifsdemsreorients;
DROP TYPE IF EXISTS type_accordconcertation_reorient;
DROP TYPE IF EXISTS type_etapedecisionep;
DROP TYPE IF EXISTS type_decisionep;
DROP TABLE IF EXISTS partseps_seanceseps;
DROP TABLE IF EXISTS partseps_sceanceseps;
DROP TYPE IF EXISTS type_presenceep CASCADE;
DROP TYPE IF EXISTS type_reponseinvitationep;
DROP TABLE IF EXISTS sceanceseps;
DROP TABLE IF EXISTS seanceseps;
DROP TYPE IF EXISTS type_traitementthemeep;
DROP TABLE IF EXISTS eps_zonesgeographiques;
DROP TABLE IF EXISTS partseps CASCADE;
DROP TYPE IF EXISTS type_rolepartep;
DROP TABLE IF EXISTS fonctionspartseps;
DROP TABLE IF EXISTS eps CASCADE;
DROP TYPE IF EXISTS type_themeep;
DROP TABLE IF EXISTS rolespartseps CASCADE;
DROP TABLE IF EXISTS eps_partseps CASCADE;

/*******************************************************************************
* Tables ne respectant pas les conventions CakePHP
*******************************************************************************/

/*
* Tables de jointure avec colonne id qui n'est pas clé primaire
* Ces clés primaires existent dans certains cas
*/

CREATE OR REPLACE FUNCTION create_missing_pkey(text,text) RETURNS VOID AS
$$
DECLARE
  p_table     alias for $1;
  p_field     alias for $2;
  v_query     text;
  v_row       record;
BEGIN
	SELECT 1 INTO v_row
		FROM
			pg_catalog.pg_class c
			JOIN pg_catalog.pg_namespace n ON n.oid        = c.relnamespace
			JOIN pg_catalog.pg_index i     ON i.indexrelid = c.oid
			JOIN pg_catalog.pg_class t     ON i.indrelid   = t.oid
		WHERE
			c.relkind = 'i'
			AND indisprimary = true
			AND n.nspname = 'public'
			AND pg_catalog.pg_table_is_visible(c.oid)
			AND t.relname = 'orientsstructs_servicesinstructeurs';

	IF NOT found THEN
			raise notice 'Upgrade table % - ADD PRIMARY KEY %', p_table, p_field;
			v_query := 'ALTER TABLE ' || p_table || ' ADD PRIMARY KEY (' || p_field || ');';
			EXECUTE v_query;
		END IF;
END;
$$
LANGUAGE 'plpgsql';

SELECT create_missing_pkey( 'orientsstructs_servicesinstructeurs', 'id' );
SELECT create_missing_pkey( 'structuresreferentes_zonesgeographiques', 'id' );
SELECT create_missing_pkey( 'users_contratsinsertion', 'id' );
SELECT create_missing_pkey( 'zonesgeographiques_regroupementszonesgeo', 'id' );

DROP FUNCTION create_missing_pkey(text,text);

/*
* Renommage de la table dossiers_rsa en dossiers
*/

ALTER TABLE dossiers_rsa RENAME TO dossiers;

-- Renommage des clés étrangères pointant sur dossiers
ALTER TABLE avispcgdroitrsa RENAME COLUMN dossier_rsa_id TO dossier_id;
ALTER TABLE infosfinancieres RENAME COLUMN dossier_rsa_id TO dossier_id;
ALTER TABLE foyers RENAME COLUMN dossier_rsa_id TO dossier_id;
ALTER TABLE situationsdossiersrsa RENAME COLUMN dossier_rsa_id TO dossier_id;
ALTER TABLE suivisinstruction RENAME COLUMN dossier_rsa_id TO dossier_id;
ALTER TABLE detailsdroitsrsa RENAME COLUMN dossier_rsa_id TO dossier_id;

/*
* Renommage de la table adresses_foyers en adressesfoyers
*/

ALTER TABLE adresses_foyers RENAME TO adressesfoyers;

/*
* Renommage de la table titres_sejour en titressejour
*/

ALTER TABLE titres_sejour RENAME TO titressejour;

/*
* Renommage de la table de jointure dont le nom ne suit pas l'ordre alphabétique
*/

ALTER TABLE ressourcesmensuelles_detailsressourcesmensuelles RENAME TO detailsressourcesmensuelles_ressourcesmensuelles;
ALTER TABLE typesaidesapres66_piecesaides66 RENAME TO piecesaides66_typesaidesapres66;
ALTER TABLE typesaidesapres66_piecescomptables66 RENAME TO piecescomptables66_typesaidesapres66;
ALTER TABLE users_contratsinsertion RENAME TO contratsinsertion_users;
ALTER TABLE zonesgeographiques_regroupementszonesgeo RENAME TO regroupementszonesgeo_zonesgeographiques;

/*
* Renommage des tables qui ne sont pas au pluriel
*/

ALTER TABLE avispcgdroitrsa RENAME TO avispcgdroitsrsa;

/*
* Renommage de champs pour que CakePHP puisse trouver la bonne table par déduction
*/

ALTER TABLE dossiers RENAME COLUMN details_droits_rsa_id TO detaildroitrsa_id;
ALTER TABLE dossiers RENAME COLUMN avis_pcg_id TO avispcgdroitrsa_id;
ALTER TABLE dossiers DROP COLUMN acompte_rsa_id;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
/*
-- Champs de clés étrangères portant un nom bizzarre ?
apres_comitesapres	comite_pcd_id
bilanparcours	nvsansep_referent_id
bilanparcours	nvparcours_referent_id
decisionsreorient	nv_typeorient_id
decisionsreorient	nv_structurereferente_id
decisionsreorient	nv_referent_id
entretiens	nv_dsp_id
entretiens	vx_dsp_id
partseps_seanceseps	remplacant_partep_id
typesorients	parentid
acos	parent_id
aros	parent_id
groups	parent_id
*/