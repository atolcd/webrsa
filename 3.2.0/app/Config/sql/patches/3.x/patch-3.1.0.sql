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
-- CUI -
--------------------------------------------------------------------------------

SELECT alter_table_drop_column_if_exists( 'public', 'cuis66', 'commentaireformation' );
ALTER TABLE cuis66 ADD COLUMN commentaireformation TEXT;

SELECT alter_table_drop_column_if_exists( 'public', 'partenairescuis66', 'activiteprincipale' );
ALTER TABLE partenairescuis66 ADD COLUMN activiteprincipale VARCHAR(255);

SELECT alter_table_drop_column_if_exists( 'public', 'cuis', 'decision_cui' );
ALTER TABLE cuis ADD COLUMN decision_cui VARCHAR(1);

ALTER TABLE cuis ADD CONSTRAINT cuis_decision_ci_in_list_chk CHECK ( cakephp_validate_in_list( decision_cui, ARRAY['A','E','V','R'] ) );

--------------------------------------------------------------------------------
-- TAG -
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION create_entites_tags() RETURNS void AS
$$
BEGIN

    IF NOT EXISTS(SELECT * FROM pg_catalog.pg_tables
			WHERE  schemaname = 'public'
			AND    tablename  = 'entites_tags') THEN

        CREATE TABLE entites_tags (
			id					SERIAL NOT NULL PRIMARY KEY,
			tag_id				INTEGER NOT NULL REFERENCES tags(id) ON DELETE CASCADE ON UPDATE CASCADE,
			fk_value			INTEGER NOT NULL,
			modele				VARCHAR(255) NOT NULL
		);

		INSERT INTO entites_tags (tag_id, fk_value, modele) (SELECT tags.id, tags.fk_value, tags.modele FROM tags);

		ALTER TABLE tags DROP COLUMN fk_value;
		ALTER TABLE tags DROP COLUMN modele;

    END IF;

END;
$$
LANGUAGE 'plpgsql';

SELECT create_entites_tags();
DROP FUNCTION create_entites_tags();

SELECT alter_table_drop_constraint_if_exists ( 'public', 'entites_tags', 'entites_tags_fk_value_modele_unique' );
ALTER TABLE entites_tags ADD CONSTRAINT entites_tags_fk_value_modele_unique UNIQUE (tag_id, fk_value, modele);

--------------------------------------------------------------------------------
-- SaveSearch -
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS savesearchs;
CREATE TABLE savesearchs (
	id SERIAL NOT NULL PRIMARY KEY,
	user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
	group_id INTEGER NOT NULL REFERENCES groups(id) ON DELETE CASCADE ON UPDATE CASCADE,
	isforgroup SMALLINT NOT NULL DEFAULT 0,
	isformenu SMALLINT NOT NULL DEFAULT 0,
	name VARCHAR(255) NOT NULL,
	url TEXT NOT NULL,
	controller VARCHAR(255) NOT NULL,
	action VARCHAR(255) NOT NULL,
	created TIMESTAMP WITHOUT TIME ZONE,
    modified TIMESTAMP WITHOUT TIME ZONE
);
ALTER TABLE savesearchs ADD CONSTRAINT savesearchs_isforgroup_in_list_chk CHECK ( cakephp_validate_in_list( isforgroup, ARRAY[0, 1] ) );
ALTER TABLE savesearchs ADD CONSTRAINT savesearchs_isformenu_in_list_chk CHECK ( cakephp_validate_in_list( isformenu, ARRAY[0, 1] ) );

--------------------------------------------------------------------------------
-- Fiche de liaison - Paramêtrages
--------------------------------------------------------------------------------

SELECT alter_table_drop_column_if_exists ('public', 'users', 'service66_id');
DROP TABLE IF EXISTS destinatairesemails_fichedeliaisons, destinatairesemails, avisprimoanalyses, primoanalyses, fichedeliaisons_personnes, avisfichedeliaisons, logicielprimos_primoanalyses, fichedeliaisons, motiffichedeliaisons, services66;

CREATE TABLE motiffichedeliaisons (
	id SERIAL NOT NULL PRIMARY KEY,
	name VARCHAR(255),
	actif SMALLINT
);
ALTER TABLE motiffichedeliaisons ADD CONSTRAINT motiffichedeliaisons_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY[0, 1] ) );

DROP TABLE IF EXISTS logicielprimos;
CREATE TABLE logicielprimos (
	id SERIAL NOT NULL PRIMARY KEY,
	name VARCHAR(255),
	actif SMALLINT
);
ALTER TABLE logicielprimos ADD CONSTRAINT logicielprimos_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY[0, 1] ) );

DROP TABLE IF EXISTS propositionprimos;
CREATE TABLE propositionprimos (
	id SERIAL NOT NULL PRIMARY KEY,
	name VARCHAR(255),
	actif SMALLINT
);
ALTER TABLE propositionprimos ADD CONSTRAINT propositionprimos_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY[0, 1] ) );

CREATE TABLE services66 (
	id							SERIAL NOT NULL PRIMARY KEY,
	name						VARCHAR(255),
	actif						SMALLINT NOT NULL,
	interne						SMALLINT NOT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);
CREATE UNIQUE INDEX services66_name_unique ON services66 (name);
ALTER TABLE services66 ADD CONSTRAINT services66_actif_in_list_chk CHECK (cakephp_validate_in_list(actif, ARRAY[0, 1]));
ALTER TABLE services66 ADD CONSTRAINT services66_interne_in_list_chk CHECK (cakephp_validate_in_list(interne, ARRAY[0, 1]));

ALTER TABLE users ADD COLUMN service66_id INTEGER REFERENCES services66(id) ON DELETE SET NULL ON UPDATE CASCADE;

--------------------------------------------------------------------------------
-- Fiche de liaison - Tables principales
--------------------------------------------------------------------------------

CREATE TABLE fichedeliaisons (
	id SERIAL NOT NULL PRIMARY KEY,
	foyer_id INTEGER NOT NULL REFERENCES foyers(id) ON DELETE CASCADE ON UPDATE CASCADE,
	motiffichedeliaison_id INTEGER NOT NULL REFERENCES motiffichedeliaisons(id) ON DELETE CASCADE ON UPDATE CASCADE,
	expediteur_id INTEGER NOT NULL REFERENCES services66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	destinataire_id INTEGER NOT NULL REFERENCES services66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
	direction VARCHAR(255) NOT NULL,
	datefiche DATE NOT NULL,
	traitementafaire SMALLINT,
	envoiemail SMALLINT,
	dateenvoiemail DATE,
	commentaire TEXT,
	etat VARCHAR(16),
	haspiecejointe CHAR(1) NOT NULL DEFAULT '0',
	created TIMESTAMP WITHOUT TIME ZONE,
    modified TIMESTAMP WITHOUT TIME ZONE
);
ALTER TABLE fichedeliaisons ADD CONSTRAINT fichedeliaisons_etape_in_list_chk CHECK ( cakephp_validate_in_list( etat, ARRAY['attavistech', 'attval', 'decisionnonvalid', 'decisionvalid', 'traite', 'annule'] ) );
ALTER TABLE fichedeliaisons ADD CONSTRAINT fichedeliaisons_direction_in_list_chk CHECK (cakephp_validate_in_list(direction, ARRAY['interne_vers_externe', 'externe_vers_interne']));
ALTER TABLE fichedeliaisons ADD CONSTRAINT fichedeliaisons_traitementafaire_in_list_chk CHECK (cakephp_validate_in_list(traitementafaire, ARRAY[0, 1]));
ALTER TABLE fichedeliaisons ADD CONSTRAINT fichedeliaisons_envoiemail_in_list_chk CHECK (cakephp_validate_in_list(envoiemail, ARRAY[0, 1]));

CREATE TABLE primoanalyses (
	id SERIAL NOT NULL PRIMARY KEY,
	fichedeliaison_id INTEGER NOT NULL REFERENCES fichedeliaisons(id) ON DELETE CASCADE ON UPDATE CASCADE,
	user_id INTEGER REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE, -- Gestionnaire, potentiellement != fichedeliaisons
	dossierpcg66_id INTEGER REFERENCES dossierspcgs66(id) ON DELETE SET NULL ON UPDATE CASCADE,
	propositionprimo_id INTEGER REFERENCES propositionprimos(id) ON DELETE CASCADE ON UPDATE CASCADE,
	createdossierpcg SMALLINT,
	dateaffectation DATE,
	dateprimo DATE,
	commentaire TEXT,
	etat VARCHAR(16),
	actionvu SMALLINT,
	datevu DATE,
	commentairevu TEXT,
	actionafaire SMALLINT,
	dateafaire DATE,
	commentaireafaire TEXT,
	created TIMESTAMP WITHOUT TIME ZONE,
    modified TIMESTAMP WITHOUT TIME ZONE
);
ALTER TABLE primoanalyses ADD CONSTRAINT primoanalyses_etape_in_list_chk CHECK ( cakephp_validate_in_list( etat, ARRAY['attaffect', 'attinstr', 'attavistech', 'attval', 'vu', 'decisionnonvalid', 'traite', 'annule'] ) );
ALTER TABLE primoanalyses ADD CONSTRAINT primoanalyses_createdossierpcg_in_list_chk CHECK ( cakephp_validate_in_list( createdossierpcg, ARRAY[0, 1] ) );
ALTER TABLE primoanalyses ADD CONSTRAINT primoanalyses_actionvu_in_list_chk CHECK (cakephp_validate_in_list(actionvu, ARRAY[0, 1]));
ALTER TABLE primoanalyses ADD CONSTRAINT primoanalyses_actionafaire_in_list_chk CHECK (cakephp_validate_in_list(actionafaire, ARRAY[0, 1]));

CREATE TABLE destinatairesemails (
	id SERIAL NOT NULL PRIMARY KEY,
	fichedeliaison_id INTEGER NOT NULL REFERENCES fichedeliaisons(id) ON DELETE CASCADE ON UPDATE CASCADE,
	name VARCHAR(255),
	type VARCHAR(3),
	created TIMESTAMP WITHOUT TIME ZONE,
    modified TIMESTAMP WITHOUT TIME ZONE
);
ALTER TABLE destinatairesemails ADD CONSTRAINT destinatairesemails_type_in_list_chk CHECK (cakephp_validate_in_list(type, ARRAY['A', 'CC', 'CCI']));

--------------------------------------------------------------------------------
-- Fiche de liaison - Tables de liaisons
--------------------------------------------------------------------------------

CREATE TABLE fichedeliaisons_personnes (
	id SERIAL NOT NULL PRIMARY KEY,
	fichedeliaison_id INTEGER NOT NULL REFERENCES fichedeliaisons(id) ON DELETE CASCADE ON UPDATE CASCADE,
	personne_id INTEGER NOT NULL REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE logicielprimos_primoanalyses (
	id SERIAL NOT NULL PRIMARY KEY,
	logicielprimo_id INTEGER NOT NULL REFERENCES logicielprimos(id) ON DELETE CASCADE ON UPDATE CASCADE,
	primoanalyse_id INTEGER NOT NULL REFERENCES primoanalyses(id) ON DELETE CASCADE ON UPDATE CASCADE,
	consultation DATE,
	commentaire TEXT
);

--------------------------------------------------------------------------------
-- Fiche de liaison - Avis et validations
--------------------------------------------------------------------------------

CREATE TABLE avisfichedeliaisons (
	id SERIAL NOT NULL PRIMARY KEY,
	fichedeliaison_id INTEGER NOT NULL REFERENCES fichedeliaisons(id) ON DELETE CASCADE ON UPDATE CASCADE,
	etape VARCHAR(10),
	date DATE,
	choix SMALLINT NOT NULL,
	commentaire TEXT,
	user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
	created TIMESTAMP WITHOUT TIME ZONE,
    modified TIMESTAMP WITHOUT TIME ZONE
);
ALTER TABLE avisfichedeliaisons ADD CONSTRAINT avisfichedeliaisons_etape_in_list_chk CHECK ( cakephp_validate_in_list( etape, ARRAY['avis', 'validation'] ) );
ALTER TABLE avisfichedeliaisons ADD CONSTRAINT avisfichedeliaisons_choix_in_list_chk CHECK ( cakephp_validate_in_list( choix, ARRAY[0, 1] ) );

CREATE TABLE avisprimoanalyses (
	id SERIAL NOT NULL PRIMARY KEY,
	primoanalyse_id INTEGER NOT NULL REFERENCES primoanalyses(id) ON DELETE CASCADE ON UPDATE CASCADE,
	etape VARCHAR(10),
	date DATE,
	choix SMALLINT NOT NULL,
	commentaire TEXT,
	user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
	created TIMESTAMP WITHOUT TIME ZONE,
    modified TIMESTAMP WITHOUT TIME ZONE
);
ALTER TABLE avisprimoanalyses ADD CONSTRAINT avisprimoanalyses_etape_in_list_chk CHECK ( cakephp_validate_in_list( etape, ARRAY['avis', 'validation'] ) );
ALTER TABLE avisprimoanalyses ADD CONSTRAINT avisprimoanalyses_choix_in_list_chk CHECK ( cakephp_validate_in_list( choix, ARRAY[0, 1] ) );

--------------------------------------------------------------------------------
-- 20160412: définition des communautés de structures référentes pour le ticket #8795 (CG 93)
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS communautessrs CASCADE;
CREATE TABLE communautessrs (
	id SERIAL					NOT NULL PRIMARY KEY,
	name						VARCHAR(255) NOT NULL,
	actif						SMALLINT NOT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE communautessrs IS 'Regroupements de structures référentes utilisés pour les chefs de projets communautaires';

ALTER TABLE communautessrs ADD CONSTRAINT communautessrs_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY[0, 1] ) );

CREATE UNIQUE INDEX communautessrs_name_idx ON communautessrs (name);
CREATE INDEX communautessrs_actif_idx ON communautessrs (actif);

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS communautessrs_structuresreferentes CASCADE;
CREATE TABLE communautessrs_structuresreferentes (
	id						SERIAL NOT NULL PRIMARY KEY,
	communautesr_id			INTEGER NOT NULL REFERENCES communautessrs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	structurereferente_id	INTEGER NOT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX communautessrs_structuresreferentes_communautesr_id_idx ON communautessrs_structuresreferentes (communautesr_id);
CREATE INDEX communautessrs_structuresreferentes_structurereferente_id_idx ON communautessrs_structuresreferentes (structurereferente_id);
CREATE UNIQUE INDEX communautessrs_structuresreferentes_communautesr_id_structurereferente_id_idx ON communautessrs_structuresreferentes (communautesr_id, structurereferente_id);

--------------------------------------------------------------------------------
-- 20160404: ajout du type d'utilisateur externe_cpdvcom pour le ticket #8795 (CG 93)
--------------------------------------------------------------------------------

SELECT add_missing_table_field ( 'public', 'users', 'communautesr_id', 'INTEGER DEFAULT NULL' );
ALTER TABLE users ADD CONSTRAINT users_communautesr_id_fk FOREIGN KEY (communautesr_id) REFERENCES communautessrs(id) ON DELETE SET NULL ON UPDATE CASCADE;
DROP INDEX IF EXISTS users_communautesr_id_idx;
CREATE INDEX users_communautesr_id_idx ON users (communautesr_id);

SELECT alter_table_drop_constraint_if_exists( 'public', 'users', 'users_type_in_list_chk' );
UPDATE users SET type = 'cg', referent_id = NULL, structurereferente_id = NULL, communautesr_id = NULL WHERE type = 'externe_cpdvcom';
ALTER TABLE users ADD CONSTRAINT users_type_in_list_chk CHECK ( cakephp_validate_in_list( type, ARRAY['cg', 'externe_cpdvcom', 'externe_cpdv', 'externe_secretaire', 'externe_ci'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'users', 'users_type_structurereferente_idreferent_id_chk' );
ALTER TABLE users ADD CONSTRAINT users_type_structurereferente_idreferent_id_chk CHECK (
	( type = 'cg' AND structurereferente_id IS NULL AND referent_id IS NULL AND communautesr_id IS NULL )
	OR ( type = 'externe_cpdvcom' AND structurereferente_id IS NULL AND referent_id IS NULL AND communautesr_id IS NOT NULL )
	OR ( type IN ( 'externe_cpdv', 'externe_secretaire' ) AND structurereferente_id IS NOT NULL AND referent_id IS NULL AND communautesr_id IS NULL )
	OR ( type = 'externe_ci' AND structurereferente_id IS NULL AND referent_id IS NOT NULL AND communautesr_id IS NULL )
);

--------------------------------------------------------------------------------
-- 20160413: ajout de la communauté de structures référentes aux tableaux de suivi
--------------------------------------------------------------------------------

-- Ajout de la colonne communautesr_id
SELECT add_missing_table_field( 'public', 'tableauxsuivispdvs93', 'communautesr_id', 'INTEGER');
ALTER TABLE tableauxsuivispdvs93 ALTER COLUMN communautesr_id SET DEFAULT NULL;
-- FIXME: en mode développement
DELETE FROM tableauxsuivispdvs93 WHERE communautesr_id IS NOT NULL;
SELECT add_missing_constraint ( 'public', 'tableauxsuivispdvs93', 'tableauxsuivispdvs93_communautesr_id_fkey', 'communautessrs', 'communautesr_id', true );
DROP INDEX IF EXISTS tableauxsuivispdvs93_communautesr_id_idx;
CREATE INDEX tableauxsuivispdvs93_communautesr_id_idx ON tableauxsuivispdvs93(communautesr_id);

-- Ajout de la colonne type
SELECT add_missing_table_field( 'public', 'tableauxsuivispdvs93', 'type', 'VARCHAR(10)');
SELECT alter_table_drop_constraint_if_exists( 'public', 'tableauxsuivispdvs93', 'tableauxsuivispdvs93_type_in_list_chk' );
ALTER TABLE tableauxsuivispdvs93 ADD CONSTRAINT tableauxsuivispdvs93_type_in_list_chk CHECK (cakephp_validate_in_list(type, ARRAY['cg', 'communaute', 'interne', 'pdv', 'referent']));
DROP INDEX IF EXISTS tableauxsuivispdvs93_type_idx;
CREATE INDEX tableauxsuivispdvs93_type_idx ON tableauxsuivispdvs93 (type);

-- On aura, dans le cadre des statistiques non internes, soit une communauté, soit un PDV, soit un référent
UPDATE tableauxsuivispdvs93 SET type= 'referent', structurereferente_id = NULL WHERE referent_id IS NOT NULL;
UPDATE tableauxsuivispdvs93 SET type= 'pdv' WHERE structurereferente_id IS NOT NULL;
UPDATE tableauxsuivispdvs93 SET type= 'communaute', structurereferente_id = NULL, referent_id = NULL WHERE communautesr_id IS NOT NULL;
UPDATE tableauxsuivispdvs93 SET type= 'cg' WHERE communautesr_id IS NULL AND structurereferente_id IS NULL AND referent_id IS NULL;

SELECT alter_table_drop_constraint_if_exists( 'public', 'tableauxsuivispdvs93', 'tableauxsuivispdvs93_fk_internes_chk' );
ALTER TABLE tableauxsuivispdvs93 ADD CONSTRAINT tableauxsuivispdvs93_fk_internes_chk CHECK (
	( type IN ( 'interne', 'cg' ) AND communautesr_id IS NULL AND structurereferente_id IS NULL AND referent_id IS NULL )
	OR ( type= 'communaute' AND communautesr_id IS NOT NULL AND structurereferente_id IS NULL AND referent_id IS NULL )
	OR ( type= 'pdv' AND communautesr_id IS NULL AND structurereferente_id IS NOT NULL AND referent_id IS NULL )
	OR ( type= 'referent' AND communautesr_id IS NULL AND structurereferente_id IS NULL AND referent_id IS NOT NULL )
);

DROP TABLE IF EXISTS structuresreferentes_tableauxsuivispdvs93 CASCADE;
CREATE TABLE structuresreferentes_tableauxsuivispdvs93 (
	id						SERIAL NOT NULL PRIMARY KEY,
	structurereferente_id	INTEGER NOT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	tableausuivipdv93_id	INTEGER NOT NULL REFERENCES tableauxsuivispdvs93(id) ON DELETE CASCADE ON UPDATE CASCADE
);

--------------------------------------------------------------------------------
-- Dashboard
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS categoriesactionroles, actionroles, roles_users, roles;

CREATE TABLE roles (
	id							SERIAL NOT NULL PRIMARY KEY,
	name						VARCHAR(255) NOT NULL,
	actif						SMALLINT NOT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);
ALTER TABLE roles ADD CONSTRAINT roles_actif_in_list_chk CHECK (cakephp_validate_in_list(actif, ARRAY[0, 1]));
CREATE UNIQUE INDEX roles_name_unique ON roles (name);

CREATE TABLE roles_users (
	id SERIAL					NOT NULL PRIMARY KEY,
	role_id						INTEGER NOT NULL REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE,
	user_id						INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE UNIQUE INDEX roles_users_unique ON roles_users (role_id, user_id);

CREATE TABLE categoriesactionroles (
	id							SERIAL NOT NULL PRIMARY KEY,
	name						VARCHAR(255),
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);
CREATE UNIQUE INDEX categoriesactionroles_name_unique ON categoriesactionroles (name);

CREATE TABLE actionroles (
	id							SERIAL NOT NULL PRIMARY KEY,
	role_id						INTEGER NOT NULL REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE,
	categorieactionrole_id		INTEGER NOT NULL REFERENCES categoriesactionroles(id) ON DELETE CASCADE ON UPDATE CASCADE,
	name						VARCHAR(255),
	description					TEXT,
	url							TEXT,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);
CREATE UNIQUE INDEX actionroles_role_name_unique ON actionroles (role_id, name);


--------------------------------------------------------------------------------
-- ACO
--------------------------------------------------------------------------------

UPDATE acos SET alias = 'Contratsinsertion:reconduction_cer_plus_55_ans' WHERE alias = 'Contratsinsertion:reconductionCERPlus55Ans';

--------------------------------------------------------------------------------
-- Réparation des mauvaises foreign key
--------------------------------------------------------------------------------

ALTER TABLE orgstransmisdossierspcgs66 DROP CONSTRAINT orgstransmisdossierspcgs66_poledossierpcg66_id_fkey;
ALTER TABLE orgstransmisdossierspcgs66 
	ADD CONSTRAINT orgstransmisdossierspcgs66_poledossierpcg66_id_fkey
	FOREIGN KEY (poledossierpcg66_id) REFERENCES polesdossierspcgs66(id)
	ON DELETE SET NULL ON UPDATE CASCADE;




-- *****************************************************************************
COMMIT;
-- *****************************************************************************