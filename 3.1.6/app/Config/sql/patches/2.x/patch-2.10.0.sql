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
-- Tables de corespondance entre personne_id
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS correspondancespersonnes CASCADE;
CREATE TABLE correspondancespersonnes (
    id                          SERIAL NOT NULL PRIMARY KEY,
	personne1_id				INTEGER NOT NULL REFERENCES personnes(id),
	personne2_id				INTEGER NOT NULL REFERENCES personnes(id),
	anomalie					BOOLEAN DEFAULT FALSE
);
COMMENT ON TABLE correspondancespersonnes IS 'correspondancespersonnes';

--------------------------------------------------------------------------------
-- Nouveaux champs du dossierpcg66
--------------------------------------------------------------------------------

SELECT alter_table_drop_column_if_exists('public', 'traitementspcgs66', 'imprimer');
SELECT alter_table_drop_column_if_exists('public', 'traitementspcgs66', 'etattraitementpcg');
ALTER TABLE traitementspcgs66 ADD COLUMN imprimer SMALLINT DEFAULT 0;
ALTER TABLE traitementspcgs66 ADD COLUMN etattraitementpcg VARCHAR(9);

SELECT alter_table_drop_constraint_if_exists ( 'public', 'traitementspcgs66', 'traitementspcgs66_imprimer_in_list_chk' );
SELECT alter_table_drop_constraint_if_exists ( 'public', 'traitementspcgs66', 'traitementspcgs66_etattraitementpcg_in_list_chk' );
ALTER TABLE traitementspcgs66 ADD CONSTRAINT traitementspcgs66_imprimer_in_list_chk CHECK ( cakephp_validate_in_list( imprimer, ARRAY[0,1] ) );
ALTER TABLE traitementspcgs66 ADD CONSTRAINT traitementspcgs66_etattraitementpcg_in_list_chk CHECK ( cakephp_validate_in_list( etattraitementpcg, ARRAY['contrôler','imprimer','attente','envoyé'] ) );

UPDATE traitementspcgs66 SET etattraitementpcg = 'envoyé' WHERE typetraitement = 'courrier' AND dateenvoicourrier IS NOT NULL;
UPDATE traitementspcgs66 SET etattraitementpcg = 'contrôler' WHERE typetraitement = 'courrier' AND dateenvoicourrier IS NULL;


--------------------------------------------------------------------------------
-- Nouveaux champs du CUI
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS personnescuis66 CASCADE;
CREATE TABLE personnescuis66 (
	id                          SERIAL NOT NULL PRIMARY KEY,
	adressecomplete				VARCHAR(512),
	canton						VARCHAR(255),
	departement					VARCHAR(255),
	referent					VARCHAR(255),
	orgpayeur					VARCHAR(255),
	nbpersacharge				VARCHAR(255),
	dtdemrsa					DATE,
	montantrsa					FLOAT
);
COMMENT ON TABLE personnescuis66 IS 'Instantané des informations additionnelles affichés sur allocataire : Personnecui66';

SELECT alter_table_drop_column_if_exists('public', 'cuis', 'personnecui_id');
SELECT alter_table_drop_column_if_exists('public', 'cuis66', 'fonction');
SELECT alter_table_drop_column_if_exists('public', 'cuis66', 'personnecui66_id');
SELECT alter_table_drop_column_if_exists('public', 'cuis66', 'perennisation');
SELECT alter_table_drop_column_if_exists('public', 'personnescuis', 'numallocataire');
SELECT alter_table_drop_column_if_exists('public', 'personnescuis', 'adressecui_id');
SELECT alter_table_drop_column_if_exists('public', 'partenairescuis66', 'commentaire');
SELECT alter_table_drop_column_if_exists('public', 'partenairescuis66', 'responsable');
SELECT alter_table_drop_column_if_exists('public', 'partenairescuis66', 'telresponsable');
SELECT alter_table_drop_column_if_exists('public', 'partenairescuis66', 'subventioncg');
SELECT alter_table_drop_column_if_exists('public', 'partenairescuis66', 'conseillerdep');
SELECT alter_table_drop_column_if_exists('public', 'partenairescuis', 'statut');
ALTER TABLE cuis ADD COLUMN personnecui_id INTEGER REFERENCES personnescuis(id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE cuis66 ADD COLUMN fonction VARCHAR(255);
ALTER TABLE cuis66 ADD COLUMN personnecui66_id INTEGER REFERENCES personnescuis66(id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE cuis66 ADD COLUMN perennisation SMALLINT;
ALTER TABLE personnescuis ADD COLUMN numallocataire VARCHAR(255);
ALTER TABLE partenairescuis66 ADD COLUMN commentaire TEXT;
ALTER TABLE partenairescuis66 ADD COLUMN responsable VARCHAR(255);
ALTER TABLE partenairescuis66 ADD COLUMN telresponsable VARCHAR(255);
ALTER TABLE partenairescuis66 ADD COLUMN subventioncg SMALLINT;
ALTER TABLE partenairescuis66 ADD COLUMN conseillerdep VARCHAR(255);
ALTER TABLE partenairescuis ADD COLUMN statut SMALLINT;

ALTER TABLE cuis66 ADD CONSTRAINT cuis66_perennisation_in_list_chk CHECK ( cakephp_validate_in_list( perennisation, ARRAY[0,1] ) );
ALTER TABLE partenairescuis66 ADD CONSTRAINT partenairescuis66_subventioncg_in_list_chk CHECK ( cakephp_validate_in_list( subventioncg, ARRAY[0,1] ) );
ALTER TABLE partenairescuis ADD CONSTRAINT partenairescuis_statut_in_list_chk CHECK ( cakephp_validate_in_list( statut, ARRAY[10,11,21,22,50,60,70,71,72,73,80,90,98,99] ) );


--------------------------------------------------------------------------------
-- Requestmanager
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS requestgroups CASCADE;
CREATE TABLE requestgroups (
	id                          SERIAL NOT NULL PRIMARY KEY,
	parent_id					INTEGER REFERENCES requestgroups(id) ON DELETE SET NULL,
	name						VARCHAR(255),
	actif						SMALLINT
);
ALTER TABLE requestgroups ADD CONSTRAINT requestgroups_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY[0,1] ) );


DROP TABLE IF EXISTS requestsmanager CASCADE;
CREATE TABLE requestsmanager (
	id                          SERIAL NOT NULL PRIMARY KEY,
	requestgroup_id				INTEGER REFERENCES requestgroups(id),
	name						VARCHAR(255) NOT NULL UNIQUE,
	model						VARCHAR(255) NOT NULL,
	json						TEXT NOT NULL,
	actif						SMALLINT
);
COMMENT ON TABLE requestsmanager IS 'Sauvegarde des requetes effectué par l editeur de requete';
ALTER TABLE requestsmanager ADD CONSTRAINT requestsmanager_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY[0,1] ) );

--------------------------------------------------------------------------------
-- GESTION DOUBLONS COMPLEXES
--------------------------------------------------------------------------------

SELECT alter_table_drop_constraint_if_exists( 'public', 'prestations', 'prestations_personne_id_fkey' );
ALTER TABLE prestations ADD CONSTRAINT prestations_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES personnes(id) ON UPDATE CASCADE ON DELETE CASCADE;

--------------------------------------------------------------------------------
-- Traitement PCG 66
--------------------------------------------------------------------------------

SELECT alter_table_drop_column_if_exists( 'public', 'traitementspcgs66', 'affiche_couple' );
ALTER TABLE traitementspcgs66 ADD COLUMN affiche_couple SMALLINT;
ALTER TABLE traitementspcgs66 ADD CONSTRAINT traitementspcgs66_affiche_couple_in_list_chk CHECK ( cakephp_validate_in_list( affiche_couple, ARRAY[0,1] ) );
UPDATE traitementspcgs66 SET affiche_couple = 0 WHERE typetraitement = 'courrier';

--------------------------------------------------------------------------------
-- Bilanparcours66
--------------------------------------------------------------------------------

SELECT alter_table_drop_column_if_exists( 'public', 'bilansparcours66', 'motifreport' );
ALTER TABLE bilansparcours66 ADD COLUMN motifreport TEXT;

--------------------------------------------------------------------------------
-- numvoie et typevoie ne doivent pas être obligatoire
--------------------------------------------------------------------------------

ALTER TABLE partenaires ALTER COLUMN numvoie DROP NOT NULL;
ALTER TABLE partenaires ALTER COLUMN typevoie DROP NOT NULL;
ALTER TABLE adressescuis ALTER COLUMN numvoie DROP NOT NULL;
ALTER TABLE adressescuis ALTER COLUMN typevoie DROP NOT NULL;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************