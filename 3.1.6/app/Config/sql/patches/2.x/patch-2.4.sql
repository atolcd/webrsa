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
-- 20121002: ajout du champ structurereferente_id à la table users
-------------------------------------------------------------------------------------

SELECT add_missing_table_field( 'public', 'users', 'structurereferente_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'users', 'users_structurereferente_id_fkey', 'structuresreferentes', 'structurereferente_id', false );
DROP INDEX IF EXISTS users_structurereferente_id_idx;
CREATE INDEX users_structurereferente_id_idx ON users( structurereferente_id );

-------------------------------------------------------------------------------------
-- 20121003: ajout du champ referent_id à la table users
-------------------------------------------------------------------------------------

SELECT add_missing_table_field( 'public', 'users', 'referent_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'users', 'users_referent_id_fkey', 'referents', 'referent_id', false );
DROP INDEX IF EXISTS users_referent_id_idx;
CREATE INDEX users_referent_id_idx ON users( referent_id );

-------------------------------------------------------------------------------------
-- Règles de validation CakePHP adaptées à PostgreSQL
-- @see Pgsqlcake.PgsqlAutovalidateBehavior.php
-- @see /2011/03/20110307
-------------------------------------------------------------------------------------

-- Règle de validation inList pour des entiers
CREATE OR REPLACE FUNCTION cakephp_validate_in_list( integer, integer[] ) RETURNS boolean AS
$$
	SELECT $1 IS NULL OR ( ARRAY[CAST($1 AS TEXT)] <@ CAST($2 AS TEXT[]) );
$$
LANGUAGE 'sql' IMMUTABLE;

COMMENT ON FUNCTION cakephp_validate_in_list( integer, integer[] ) IS
	'@see http://api.cakephp.org/class/validation#method-ValidationinList';

-- FIXME: cakephp_validate_range
CREATE OR REPLACE FUNCTION cakephp_validate_range( p_check float, p_lower float, p_upper float ) RETURNS boolean AS
$$
	BEGIN
		RETURN p_check IS NULL
			OR p_lower IS NULL
			OR p_upper IS NULL
			OR(
				p_check > p_lower
				AND p_check < p_upper
			);
	END;
$$
LANGUAGE 'plpgsql' IMMUTABLE;

COMMENT ON FUNCTION cakephp_validate_range( p_check float, p_lower float, p_upper float ) IS
	'@see http://api.cakephp.org/class/validation#method-Validationrange';

CREATE OR REPLACE FUNCTION cakephp_validate_inclusive_range( p_check float, p_lower float, p_upper float ) RETURNS boolean AS
$$
	BEGIN
		RETURN p_check IS NULL
			OR p_lower IS NULL
			OR p_upper IS NULL
			OR(
				p_check >= p_lower
				AND p_check <= p_upper
			);
	END;
$$
LANGUAGE 'plpgsql' IMMUTABLE;

COMMENT ON FUNCTION cakephp_validate_inclusive_range( p_check float, p_lower float, p_upper float ) IS
	'Comme cakephp_validate_range(), mais avec les bornes incluses';


--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION public.alter_table_drop_constraint_if_exists( text, text, text ) RETURNS bool as
$$
	DECLARE
		p_namespace			alias for $1;
		p_table				alias for $2;
		p_constraintname    alias for $3;
		v_row				record;
		v_query				text;
	BEGIN
		SELECT
				1 INTO v_row
			FROM pg_constraint
				INNER JOIN pg_namespace ON ( pg_constraint.connamespace = pg_namespace.oid )
				INNER JOIN pg_class ON ( pg_constraint.conrelid = pg_class.oid )
			WHERE
				pg_namespace.nspname = p_namespace
				AND pg_class.relname = p_table
				AND pg_constraint.conname = p_constraintname;
		IF FOUND THEN
			RAISE NOTICE 'Alter table %.% - drop constraint %', p_namespace, p_table, p_constraintname;
			v_query := 'ALTER TABLE ' || p_namespace || '.' || p_table || ' DROP constraint ' || p_constraintname || ';';
			EXECUTE v_query;
			RETURN 't';
		ELSE
			RETURN 'f';
		END IF;
	END;
$$
LANGUAGE 'plpgsql';

COMMENT ON FUNCTION public.alter_table_drop_constraint_if_exists( text, text, text ) IS
	'Équivalent de la fonctionnalité ALTER TABLE <table> DROP CONSTRAINT <name> disponible à partir de PostgreSQl 9.1';

--------------------------------------------------------------------------------


-------------------------------------------------------------------------------------
-- 20121003: nouveau CER pour le CG 93
-------------------------------------------------------------------------------------

-- tables liées au cers93
DROP TABLE IF EXISTS naturescontrats CASCADE;
CREATE TABLE naturescontrats (
	id				SERIAL NOT NULL PRIMARY KEY,
	name			VARCHAR(250) NOT NULL,
	isduree			VARCHAR(1) DEFAULT '0'
);
COMMENT ON TABLE naturescontrats IS 'Liste des natures de contrat paramétrable pour le CER93)';

DROP INDEX IF EXISTS naturescontrats_name_idx;
CREATE UNIQUE INDEX naturescontrats_name_idx ON naturescontrats( name );

ALTER TABLE naturescontrats ADD CONSTRAINT naturescontrats_isduree_in_list_chk CHECK ( cakephp_validate_in_list( isduree, ARRAY['0', '1'] ) );

-------------------------------------------------------------------------------------

DROP TABLE IF EXISTS metiersexerces CASCADE;
CREATE TABLE metiersexerces (
	id				SERIAL NOT NULL PRIMARY KEY,
	name			TEXT
);
COMMENT ON TABLE metiersexerces IS 'Métiers exercés en lien avec le CER 93 (bloc 4, formation et expérience)';

DROP INDEX IF EXISTS metiersexerces_name_idx;
CREATE UNIQUE INDEX metiersexerces_name_idx ON metiersexerces( name );

-------------------------------------------------------------------------------------
DROP TABLE IF EXISTS secteursactis CASCADE;
CREATE TABLE secteursactis (
	id				SERIAL NOT NULL PRIMARY KEY,
	name			TEXT
);
COMMENT ON TABLE secteursactis IS 'Secteurs d''activités exercés en lien avec le CER 93 (bloc 4, formation et expérience)';

DROP INDEX IF EXISTS secteursactis_name_idx;
CREATE UNIQUE INDEX secteursactis_name_idx ON secteursactis( name );

-------------------------------------------------------------------------------------

DROP TABLE IF EXISTS sujetscers93 CASCADE;
CREATE TABLE sujetscers93 (
	id					SERIAL NOT NULL PRIMARY KEY,
	name				VARCHAR(250) NOT NULL,
	isautre				VARCHAR(1) DEFAULT '0', --FIXME: un seul enregistrement à vrai
	created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE sujetscers93 IS 'Sujets sur lequel porte le CER CG93 (bloc 6)';
DROP INDEX IF EXISTS sujetscers93_name_idx;
CREATE UNIQUE INDEX sujetscers93_name_idx ON sujetscers93( name );
ALTER TABLE sujetscers93 ADD CONSTRAINT sujetscers93_isautre_in_list_chk CHECK ( cakephp_validate_in_list( isautre, ARRAY['0','1'] ) );
-------------------------------------------------------------------------------------

DROP TABLE IF EXISTS soussujetscers93 CASCADE;
CREATE TABLE soussujetscers93 (
	id					SERIAL NOT NULL PRIMARY KEY,
	name				VARCHAR(250) NOT NULL,
	sujetcer93_id		INTEGER NOT NULL REFERENCES sujetscers93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE soussujetscers93 IS 'Types de sujet sur lequel porte le CER CG93 (bloc 6)';
DROP INDEX IF EXISTS soussujetscers93_name_idx;
CREATE UNIQUE INDEX soussujetscers93_name_idx ON soussujetscers93( name );

DROP INDEX IF EXISTS soussujetscers93_sujetcer93_id_idx;
CREATE INDEX soussujetscers93_sujetcer93_id_idx ON soussujetscers93(sujetcer93_id);

-- Tables devenues obsolètes au cours des développements
DROP TABLE IF EXISTS etatscivilscers93 CASCADE;

-- DROP TYPE IF EXISTS TYPE_CMU CASCADE;
-- CREATE TYPE TYPE_CMU AS ENUM ( 'oui', 'non', 'encours' );

-- DROP TYPE IF EXISTS TYPE_POSITIONCER93 CASCADE;
-- CREATE TYPE TYPE_POSITIONCER93 AS ENUM ( '00enregistre', '01signe', '02attdecisioncpdv', '03attdecisioncg', '04premierelecture', '05secondelecture', '06attaviscadre', '07attavisep', '99rejete', '99valide' );

-- Données spécifiques au CG 93
DROP TABLE IF EXISTS cers93 CASCADE;
CREATE TABLE cers93 (
	id						SERIAL NOT NULL PRIMARY KEY,
	contratinsertion_id		INTEGER NOT NULL REFERENCES contratsinsertion(id) ON DELETE CASCADE ON UPDATE CASCADE,
	user_id					INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
	-- Bloc 2: état cvil
	matricule				VARCHAR(15) DEFAULT NULL,
	dtdemrsa				DATE NOT NULL,
	qual					VARCHAR(3) DEFAULT NULL,
	nom						VARCHAR(50) NOT NULL,
	nomnai					VARCHAR(50) DEFAULT NULL,
	prenom					VARCHAR(50) NOT NULL,
	dtnai					DATE NOT NULL,
	adresse					VARCHAR(250) DEFAULT NULL,
	codepos					VARCHAR(5) DEFAULT NULL,
	locaadr					VARCHAR(50) DEFAULT NULL,
	sitfam					VARCHAR(3) NOT NULL,
	natlog					VARCHAR(4) DEFAULT NULL,
	incoherencesetatcivil	TEXT DEFAULT NULL,
	-- Bloc 3: vérification des droits
	inscritpe				VARCHAR(1) DEFAULT NULL,
	cmu						VARCHAR(10) DEFAULT NULL,
	cmuc					VARCHAR(10) DEFAULT NULL,
	-- Bloc 4: formation et expérience
	nivetu					VARCHAR(4) DEFAULT NULL,
	numdemrsa				VARCHAR(11) DEFAULT NULL,
	rolepers				CHAR(3) DEFAULT NULL,
	identifiantpe			VARCHAR(11) DEFAULT NULL,
	positioncer				VARCHAR(20) NOT NULL DEFAULT '00enregistre',
	formeci					CHAR(1) DEFAULT NULL,
	datesignature			DATE DEFAULT NULL,
	autresexps				VARCHAR(250) DEFAULT NULL,
	isemploitrouv			VARCHAR(1) DEFAULT NULL,
	metierexerce_id			INTEGER REFERENCES metiersexerces(id) ON DELETE CASCADE ON UPDATE CASCADE,
	secteuracti_id			INTEGER REFERENCES secteursactis(id) ON DELETE CASCADE ON UPDATE CASCADE,
	naturecontrat_id		INTEGER REFERENCES naturescontrats(id) ON DELETE CASCADE ON UPDATE CASCADE,
	dureehebdo				INTEGER DEFAULT NULL,
	dureecdd				VARCHAR(3) DEFAULT NULL,
	prevu					TEXT DEFAULT NULL,
	bilancerpcd				TEXT DEFAULT NULL,
	duree					INTEGER DEFAULT NULL,
	pointparcours			VARCHAR(25) DEFAULT NULL,
	datepointparcours		DATE DEFAULT NULL,
	pourlecomptede			VARCHAR(250)  DEFAULT NULL,
	observpro				TEXT,
	observbenef				TEXT,
	structureutilisateur	VARCHAR(250) DEFAULT NULL,
	nomutilisateur 			VARCHAR(100) DEFAULT NULL,
	prevupcd				TEXT DEFAULT NULL,
	sujetpcd				TEXT DEFAULT NULL,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE

);
COMMENT ON TABLE cers93 IS 'Données du CER spécifiques au CG 93';

DROP INDEX IF EXISTS cers93_contratinsertion_id_idx;
CREATE UNIQUE INDEX cers93_contratinsertion_id_idx ON cers93( contratinsertion_id );

ALTER TABLE cers93 ADD CONSTRAINT cers93_inscritpe_in_list_chk CHECK ( cakephp_validate_in_list( inscritpe, ARRAY['0', '1'] ) );
ALTER TABLE cers93 ADD CONSTRAINT cers93_cmu_in_list_chk CHECK ( cakephp_validate_in_list( cmu, ARRAY['oui', 'non', 'encours'] ) );
ALTER TABLE cers93 ADD CONSTRAINT cers93_cmuc_in_list_chk CHECK ( cakephp_validate_in_list( cmuc, ARRAY['oui', 'non', 'encours'] ) );
SELECT public.alter_table_drop_constraint_if_exists( 'public', 'cers93', 'cers93_nivetu_in_list_chk' );
ALTER TABLE cers93 ADD CONSTRAINT cers93_nivetu_in_list_chk CHECK ( cakephp_validate_in_list( nivetu, ARRAY['1201', '1202', '1203', '1204', '1205', '1206', '1207', '1208'] ) );
SELECT public.alter_table_drop_constraint_if_exists( 'public', 'cers93', 'cers93_positioncer_in_list_chk' );
ALTER TABLE cers93 ADD CONSTRAINT cers93_positioncer_in_list_chk CHECK ( cakephp_validate_in_list( positioncer, ARRAY['00enregistre', '01signe', '02attdecisioncpdv', '03attdecisioncg', '99rejetecpdv', '04premierelecture', '05secondelecture', '07attavisep', '99rejete', '99valide'] ) );
ALTER TABLE cers93 ADD CONSTRAINT cers93_formeci_in_list_chk CHECK ( cakephp_validate_in_list( formeci, ARRAY['S', 'C'] ) );
ALTER TABLE cers93 ADD CONSTRAINT cers93_isemploitrouv_in_list_chk CHECK ( cakephp_validate_in_list( isemploitrouv, ARRAY['N', 'O'] ) );
ALTER TABLE cers93 ADD CONSTRAINT cers93_dureehebdo_inclusive_range_chk CHECK ( cakephp_validate_inclusive_range( dureehebdo, 0, 39 ) );
ALTER TABLE cers93 ADD CONSTRAINT cers93_duree_in_list_chk CHECK ( cakephp_validate_in_list( duree, ARRAY[3, 6, 9, 12] ) );
ALTER TABLE cers93 ADD CONSTRAINT cers93_pointparcours_in_list_chk CHECK ( cakephp_validate_in_list( pointparcours, ARRAY['aladate','alafin'] ) );

-------------------------------------------------------------------------------------

DROP TABLE IF EXISTS composfoyerscers93 CASCADE;
CREATE TABLE composfoyerscers93 (
	id			SERIAL NOT NULL PRIMARY KEY,
	cer93_id	INTEGER NOT NULL REFERENCES cers93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	rolepers	VARCHAR(3) DEFAULT NULL,
	qual		VARCHAR(3) DEFAULT NULL,
	nom			VARCHAR(50) NOT NULL,
	prenom		VARCHAR(50) NOT NULL,
	dtnai		DATE NOT NULL,
	created		TIMESTAMP WITHOUT TIME ZONE,
	modified	TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE composfoyerscers93 IS 'Compositions du foyer pour le CER 93 (bloc 2, état civil)';

DROP INDEX IF EXISTS composfoyerscers93_cer93_id_idx;
CREATE INDEX composfoyerscers93_cer93_id_idx ON composfoyerscers93( cer93_id );

-------------------------------------------------------------------------------------

DROP TABLE IF EXISTS diplomescers93 CASCADE;
CREATE TABLE diplomescers93 (
	id			SERIAL NOT NULL PRIMARY KEY,
	cer93_id	INTEGER NOT NULL REFERENCES cers93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	name		VARCHAR(250) NOT NULL,
	annee		INTEGER NOT NULL,
	isetranger	TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0',
	created		TIMESTAMP WITHOUT TIME ZONE,
	modified	TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE diplomescers93 IS 'Diplômes obtenus pour le CER 93 (bloc 4, formation et expérience)';

DROP INDEX IF EXISTS diplomescers93_cer93_id_idx;
CREATE INDEX diplomescers93_cer93_id_idx ON diplomescers93( cer93_id );

-------------------------------------------------------------------------------------
DROP TABLE IF EXISTS expsproscers93 CASCADE;
CREATE TABLE expsproscers93 (
	id					SERIAL NOT NULL PRIMARY KEY,
	cer93_id			INTEGER NOT NULL REFERENCES cers93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	metierexerce_id		INTEGER NOT NULL REFERENCES metiersexerces(id) ON DELETE CASCADE ON UPDATE CASCADE,
	secteuracti_id		INTEGER NOT NULL REFERENCES secteursactis(id) ON DELETE CASCADE ON UPDATE CASCADE,
	anneedeb			INTEGER NOT NULL,
	duree				VARCHAR(25) NOT NULL,
	created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE expsproscers93 IS 'Expériences professionnelles significatives pour le CER 93 (bloc 4, formation et expérience)';

DROP INDEX IF EXISTS expsproscers93_cer93_id_idx;
CREATE INDEX expsproscers93_cer93_id_idx ON expsproscers93( cer93_id );

-------------------------------------------------------------------------------------

DROP TABLE IF EXISTS histoschoixcers93 CASCADE;
CREATE TABLE histoschoixcers93 (
	id					SERIAL NOT NULL PRIMARY KEY,
	cer93_id			INTEGER NOT NULL REFERENCES cers93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	user_id				INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
	commentaire			VARCHAR(250) DEFAULT NULL,
	formeci				CHAR(1) NOT NULL,
	etape				VARCHAR(20) NOT NULL,
	prevalide			VARCHAR(20) DEFAULT NULL,
	decisioncs			VARCHAR(20) DEFAULT NULL,
	decisioncadre		VARCHAR(20) DEFAULT NULL,
	datechoix			DATE DEFAULT NULL,
	isrejet				VARCHAR(1) DEFAULT NULL,
	duree				INTEGER DEFAULT NULL, -- ARNAUD
	created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE histoschoixcers93 IS 'Historiques des choix pris sur le CER 93 (signature, transfert cpdv, ...)';

DROP INDEX IF EXISTS histoschoixcers93_cer93_id_idx;
CREATE INDEX histoschoixcers93_cer93_id_idx ON histoschoixcers93( cer93_id );

DROP INDEX IF EXISTS histoschoixcers93_cer93_id_etape_idx;
CREATE UNIQUE INDEX histoschoixcers93_cer93_id_etape_idx ON histoschoixcers93( cer93_id, etape );

SELECT public.alter_table_drop_constraint_if_exists( 'public', 'histoschoixcers93', 'histoschoixcers93_formeci_in_list_chk' );
ALTER TABLE histoschoixcers93 ADD CONSTRAINT histoschoixcers93_formeci_in_list_chk CHECK ( cakephp_validate_in_list( formeci, ARRAY['S', 'C'] ) );
SELECT public.alter_table_drop_constraint_if_exists( 'public', 'histoschoixcers93', 'histoschoixcers93_etape_in_list_chk' );
ALTER TABLE histoschoixcers93 ADD CONSTRAINT histoschoixcers93_etape_in_list_chk CHECK ( cakephp_validate_in_list( etape, ARRAY['00enregistre', '01signe', '02attdecisioncpdv', '03attdecisioncg',  '99rejetecpdv','04premierelecture', '05secondelecture', '06attaviscadre', '07attavisep', '99rejete', '99valide'] ) );
SELECT public.alter_table_drop_constraint_if_exists( 'public', 'histoschoixcers93', 'histoschoixcers93_prevalide_in_list_chk' );
ALTER TABLE histoschoixcers93 ADD CONSTRAINT histoschoixcers93_prevalide_in_list_chk CHECK ( cakephp_validate_in_list( prevalide, ARRAY['arelire', 'prevalide'] ) );
SELECT public.alter_table_drop_constraint_if_exists( 'public', 'histoschoixcers93', 'histoschoixcers93_decisioncs_in_list_chk' );
ALTER TABLE histoschoixcers93 ADD CONSTRAINT histoschoixcers93_decisioncs_in_list_chk CHECK ( cakephp_validate_in_list( decisioncs, ARRAY['valide', 'aviscadre', 'passageep'] ) );
SELECT public.alter_table_drop_constraint_if_exists( 'public', 'histoschoixcers93', 'histoschoixcers93_decisioncadre_in_list_chk' );
ALTER TABLE histoschoixcers93 ADD CONSTRAINT histoschoixcers93_decisioncadre_in_list_chk CHECK ( cakephp_validate_in_list( decisioncadre, ARRAY['valide', 'rejete', 'passageep'] ) );
SELECT public.alter_table_drop_constraint_if_exists( 'public', 'histoschoixcers93', 'histoschoixcers93_isrejet_in_list_chk' );
ALTER TABLE histoschoixcers93 ADD CONSTRAINT histoschoixcers93_isrejet_in_list_chk CHECK ( cakephp_validate_in_list( isrejet, ARRAY['0', '1'] ) );
SELECT public.alter_table_drop_constraint_if_exists( 'public', 'histoschoixcers93', 'histoschoixcers93_duree_in_list_chk' );
ALTER TABLE histoschoixcers93 ADD CONSTRAINT histoschoixcers93_duree_in_list_chk CHECK ( cakephp_validate_in_list( duree, ARRAY[3, 6, 9, 12] ) ); -- ARNAUD

-------------------------------------------------------------------------------------------------------------

DROP TABLE IF EXISTS valeursparsoussujetscers93 CASCADE;
CREATE TABLE valeursparsoussujetscers93 (
	id					SERIAL NOT NULL PRIMARY KEY,
	name				VARCHAR(250) NOT NULL,
	soussujetcer93_id		INTEGER NOT NULL REFERENCES soussujetscers93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE valeursparsoussujetscers93 IS 'Valeurs en lien avec les sous-types de sujet sur lequel porte le CER CG93 (bloc 6)';
DROP INDEX IF EXISTS valeursparsoussujetscers93_name_idx;
CREATE UNIQUE INDEX valeursparsoussujetscers93_name_idx ON valeursparsoussujetscers93( name );

-------------------------------------------------------------------------------------------------------------

DROP TABLE IF EXISTS cers93_sujetscers93 CASCADE;
CREATE TABLE cers93_sujetscers93 (
    id                 			SERIAL NOT NULL PRIMARY KEY,
    cer93_id       				INTEGER NOT NULL REFERENCES cers93(id) ON DELETE CASCADE ON UPDATE CASCADE,
    sujetcer93_id				INTEGER NOT NULL REFERENCES sujetscers93(id) ON DELETE CASCADE ON UPDATE CASCADE,
 	soussujetcer93_id			INTEGER DEFAULT NULL REFERENCES soussujetscers93(id) ON DELETE CASCADE ON UPDATE CASCADE,
 	valeurparsoussujetcer93_id	INTEGER DEFAULT NULL REFERENCES valeursparsoussujetscers93(id) ON DELETE CASCADE ON UPDATE CASCADE,
 	commentaireautre			VARCHAR(250) DEFAULT NULL,
    created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);
DROP INDEX IF EXISTS cers93_sujetscers93_cer93_id_idx;
CREATE INDEX cers93_sujetscers93_cer93_id_idx ON cers93_sujetscers93(cer93_id);

DROP INDEX IF EXISTS cers93_sujetscers93_sujetcer93_id_idx;
CREATE INDEX cers93_sujetscers93_sujetcer93_id_idx ON cers93_sujetscers93(sujetcer93_id);

DROP INDEX IF EXISTS cers93_sujetscers93_soussujetcer93_id_idx;
CREATE INDEX cers93_sujetscers93_soussujetcer93_id_idx ON cers93_sujetscers93(soussujetcer93_id);

DROP INDEX IF EXISTS cers93_sujetscers93_valeurparsoussujetcer93_id_idx;
CREATE INDEX cers93_sujetscers93_valeurparsoussujetcer93_id_idx ON cers93_sujetscers93(valeurparsoussujetcer93_id);
--------------------------------------------------------------------------------
-- 20121026: la table derniersdossiersallocataires permet de se passer d'une
-- sous-requête très coûteuse (à condition de lancer le shell
-- Derniersdossiersallocataires) afin de trouver le dernier dossier d'un allocataire.
--
-- La contrainte d'index unique est violée dans la BDD de prod du 05/10/2012 du
-- CG 66 car les allocataires ont plusieurs prestations.
-- personne_id	dossier_id	count
-- 75820		30492		2
-- 77064		38530		2
-- 126883		52453		2
-- Ces problèmes NE peuvent PAS être corrigés en passant par la partie "Administration"
-- => "Gestion des anomalies" => "Gestion des doublons simples".
-- Il faudra effectuer des requêtes du genre DELETE FROM prestations WHERE id = XXXXX.
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS derniersdossiersallocataires CASCADE;
CREATE TABLE derniersdossiersallocataires (
	id 				SERIAL NOT NULL PRIMARY KEY,
	personne_id		INTEGER NOT NULL REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	dossier_id		INTEGER NOT NULL REFERENCES dossiers(id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX derniersdossiersallocataires_personne_id_idx ON derniersdossiersallocataires(personne_id);
CREATE INDEX derniersdossiersallocataires_dossier_id_idx ON derniersdossiersallocataires(dossier_id);
CREATE UNIQUE INDEX derniersdossiersallocataires_personne_id_dossier_id_idx ON derniersdossiersallocataires(personne_id,dossier_id);

--------------------------------------------------------------------------------
-- Mise à jour des rangs de CER
--------------------------------------------------------------------------------
UPDATE contratsinsertion SET rg_ci = NULL;

UPDATE contratsinsertion
	SET rg_ci = (
		SELECT ( COUNT(contratsinsertionpcd.id) + 1 )
			FROM contratsinsertion AS contratsinsertionpcd
			WHERE contratsinsertionpcd.personne_id = contratsinsertion.personne_id
				AND contratsinsertionpcd.id <> contratsinsertion.id
				AND contratsinsertionpcd.decision_ci = 'V'
				AND contratsinsertionpcd.dd_ci IS NOT NULL
				AND contratsinsertionpcd.datevalidation_ci IS NOT NULL

				AND (
					contratsinsertionpcd.dd_ci < contratsinsertion.dd_ci
					OR (
						contratsinsertionpcd.dd_ci = contratsinsertion.dd_ci
						AND contratsinsertionpcd.datevalidation_ci < contratsinsertion.datevalidation_ci
					)
					OR (
						contratsinsertionpcd.dd_ci = contratsinsertion.dd_ci
						AND contratsinsertionpcd.datevalidation_ci = contratsinsertion.datevalidation_ci
						AND contratsinsertionpcd.id < contratsinsertion.id
					)
				)

				AND (
					contratsinsertion.positioncer IS NULL
					OR contratsinsertion.positioncer <> 'annule'
				)
	)
	WHERE
		contratsinsertion.dd_ci IS NOT NULL
		AND contratsinsertion.datevalidation_ci IS NOT NULL
		AND contratsinsertion.decision_ci = 'V'
		AND (
			contratsinsertion.positioncer IS NULL
			OR contratsinsertion.positioncer <> 'annule'
		);

DROP INDEX IF EXISTS contratsinsertion_personne_id_rg_ci_idx;
CREATE UNIQUE INDEX contratsinsertion_personne_id_rg_ci_idx ON contratsinsertion( personne_id, rg_ci ) WHERE rg_ci IS NOT NULL;

--------------------------------------------------------------------------------
-- 20121127 - début du patch pour la version 2.4beta4
--------------------------------------------------------------------------------

-- INFO: lorsqu'on change le type d'une colonne et que cette colonne est utilisée
-- dans des contraintes, la conversion de type pose problème.
ALTER TABLE orientsstructs DROP CONSTRAINT orientsstructs_origine_check;
SELECT public.alter_enumtype( 'TYPE_ORIGINEORIENTSTRUCT', ARRAY['manuelle','cohorte','reorientation','demenagement'] );
ALTER TABLE orientsstructs ADD CONSTRAINT orientsstructs_origine_check CHECK(
	( origine IS NULL AND date_valid IS NULL )
	OR (
		( origine IS NOT NULL AND date_valid IS NOT NULL )
		AND (
			( rgorient = 1 AND origine IN ( 'manuelle', 'cohorte' ) )
			OR ( rgorient > 1 AND origine = 'reorientation' )
			OR ( rgorient > 1 AND origine = 'demenagement' )
		)
	)
);

-- FIXME: que pour les développements
DELETE FROM pdfs WHERE modele = 'Orientstruct' AND fk_value IN ( SELECT orientsstructs.id FROM orientsstructs WHERE origine = 'demenagement' );
DELETE FROM orientsstructs WHERE origine = 'demenagement';

DROP TABLE IF EXISTS transfertspdvs93;
CREATE TABLE transfertspdvs93 (
	id							SERIAL NOT NULL PRIMARY KEY,
	vx_orientstruct_id			INTEGER NOT NULL REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	nv_orientstruct_id			INTEGER NOT NULL REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	-- FIXME references adressesfoyers −> on delete set null ?
	-- FIXME: remplacer vx/nv_adressefoyer_id par vx_codeinsee/nv_codeinsee + INDEX UNIQUE (pas de sens de transférer de bagnolet à bagnolet)
	vx_adressefoyer_id			INTEGER NOT NULL REFERENCES adressesfoyers(id) ON DELETE CASCADE ON UPDATE CASCADE,
	nv_adressefoyer_id			INTEGER NOT NULL REFERENCES adressesfoyers(id) ON DELETE CASCADE ON UPDATE CASCADE,
	user_id						INTEGER NOT NULL REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);

DROP INDEX IF EXISTS transfertspdvs93_vx_orientstruct_id_idx;
CREATE INDEX transfertspdvs93_vx_orientstruct_id_idx ON transfertspdvs93(vx_orientstruct_id);

DROP INDEX IF EXISTS transfertspdvs93_nv_orientstruct_id_idx;
CREATE INDEX transfertspdvs93_nv_orientstruct_id_idx ON transfertspdvs93(nv_orientstruct_id);

DROP INDEX IF EXISTS transfertspdvs93_user_id_idx;
CREATE INDEX transfertspdvs93_user_id_idx ON transfertspdvs93(user_id);

-- TODO: le faire dans le formulaire
SELECT add_missing_table_field( 'public', 'users', 'type', 'VARCHAR(50)' );
ALTER TABLE users ALTER COLUMN type SET DEFAULT NULL;
SELECT alter_table_drop_constraint_if_exists( 'public', 'users', 'users_type_in_list_chk' );
ALTER TABLE users ADD CONSTRAINT users_type_in_list_chk CHECK ( cakephp_validate_in_list( type, ARRAY['cg', 'externe_cpdv', 'externe_secretaire', 'externe_ci'] ) );
UPDATE users SET type = 'externe_ci' WHERE referent_id IS NOT NULL AND type IS NULL;
UPDATE users SET type = 'externe_cpdv' WHERE structurereferente_id IS NOT NULL AND type IS NULL;
UPDATE users SET type = 'cg' WHERE type IS NULL;
ALTER TABLE users ALTER COLUMN type SET NOT NULL;

SELECT alter_table_drop_constraint_if_exists( 'public', 'users', 'users_type_structurereferente_idreferent_id_chk' );
ALTER TABLE users ADD CONSTRAINT users_type_structurereferente_idreferent_id_chk CHECK (
	( type = 'cg' AND structurereferente_id IS NULL AND referent_id IS NULL )
	OR ( type IN ( 'externe_cpdv', 'externe_secretaire' ) AND structurereferente_id IS NOT NULL AND referent_id IS NULL )
	OR ( type = 'externe_ci' AND structurereferente_id IS NULL AND referent_id IS NOT NULL )
);

--------------------------------------------------------------------------------
-- 20121203 : Ajout d'une valeur finale pour la déicison du CER Particulier CG66
--------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'proposdecisionscers66', 'decisionfinale', 'TYPE_NO' );
/*
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
CREATE INDEX manifestationsbilansparcours66_bilanparcours66_id_idx ON manifestationsbilansparcours66( bilanparcours66_id );*/


-- SELECT add_missing_table_field('public', 'memos', 'haspiecejointe', 'type_booleannumber' );
-- ALTER TABLE memos ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
-- UPDATE memos SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
-- ALTER TABLE memos ALTER COLUMN haspiecejointe SET NOT NULL;

--------------------------------------------------------------------------------
-- 20121203 : Modification de l'enum pour les positions du CER
--------------------------------------------------------------------------------
UPDATE contratsinsertion SET positioncer = CAST ( ( CASE WHEN positioncer = 'valid' THEN 'encours' WHEN positioncer = 'attvalidpart' THEN 'attvalid' WHEN positioncer = 'attvalidpartpropopcg' THEN 'attvalid' WHEN positioncer = 'attvalidsimple' THEN 'attvalid' WHEN positioncer = 'validnotifie' THEN 'encours' WHEN positioncer = 'nonvalidnotifie' THEN 'nonvalid' ELSE positioncer END ) AS type_positioncer );

SELECT public.alter_enumtype( 'TYPE_POSITIONCER', ARRAY['encours', 'attvalid', 'annule', 'fincontrat', 'encoursbilan', 'attrenouv', 'perime', 'nonvalid'] );

--------------------------------------------------------------------------------
-- 20121205 : Modification de la table cuis avec ajout d'un enum sur la composition familiale
--------------------------------------------------------------------------------
-- FIXME
SELECT add_missing_table_field( 'public', 'cuis', 'compofamiliale', 'VARCHAR(20)' );
ALTER TABLE cuis ADD CONSTRAINT cuis_compofamiliale_in_list_chk CHECK ( cakephp_validate_in_list( compofamiliale, ARRAY['couple', 'coupleenfant','isole','isoleenfant'] ) );

SELECT public.alter_columnname_ifexists( 'public', 'cuis', 'numsecteur', 'numconvention' );
SELECT public.alter_columnname_ifexists( 'public', 'cuis', 'numconventioncollect', 'numconventionobj' );

SELECT add_missing_table_field( 'public', 'cuis', 'serviceinstructeur_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'cuis', 'cuis_serviceinstructeur_id_fkey', 'servicesinstructeurs', 'serviceinstructeur_id', false );
DROP INDEX IF EXISTS cuis_serviceinstructeur_id_idx;
CREATE INDEX cuis_serviceinstructeur_id_idx ON cuis( serviceinstructeur_id );

--------------------------------------------------------------------------------
-- 20121211 : Création de la table permettant de définir des commentaires
--				commun aux décisons CI et CPDV
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS commentairesnormescers93 CASCADE;
CREATE TABLE commentairesnormescers93 (
	id					SERIAL NOT NULL PRIMARY KEY,
	name				VARCHAR(250) NOT NULL,
	isautre				VARCHAR(1) DEFAULT '0',
	created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE commentairesnormescers93 IS 'Commentaires normés pour les décision CI et CPDV du CER CG93';
DROP INDEX IF EXISTS commentairesnormescers93_name_idx;
-- DROP INDEX IF EXISTS commentairesnormescers93_isautre_true_idx;
CREATE UNIQUE INDEX commentairesnormescers93_name_idx ON commentairesnormescers93( name );
-- FIXME: trouver un autre moyen de le faire
-- CREATE UNIQUE INDEX commentairesnormescers93_isautre_true_idx ON commentairesnormescers93( isautre ) WHERE isautre='1';
ALTER TABLE commentairesnormescers93 ADD CONSTRAINT commentairesnormescers93_isautre_in_list_chk CHECK ( cakephp_validate_in_list( isautre, ARRAY['0','1'] ) );



DROP TABLE IF EXISTS commentairesnormescers93_histoschoixcers93 CASCADE;
CREATE TABLE commentairesnormescers93_histoschoixcers93 (
    id                 					SERIAL NOT NULL PRIMARY KEY,
    commentairenormecer93_id			INTEGER NOT NULL REFERENCES commentairesnormescers93(id) ON DELETE CASCADE ON UPDATE CASCADE,
    histochoixcer93_id					INTEGER NOT NULL REFERENCES histoschoixcers93(id) ON DELETE CASCADE ON UPDATE CASCADE,
 	commentaireautre					VARCHAR(250) DEFAULT NULL,
    created								TIMESTAMP WITHOUT TIME ZONE,
	modified							TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE commentairesnormescers93_histoschoixcers93 IS 'Table de liaison entre les historiques de CER et les commentaires choisis (CER93)';
DROP INDEX IF EXISTS commentairesnormescers93_histoschoixcers93_commentairenormecer93_id_idx;
CREATE INDEX commentairesnormescers93_histoschoixcers93_commentairenormecer93_id_idx ON commentairesnormescers93_histoschoixcers93(commentairenormecer93_id);

DROP INDEX IF EXISTS commentairesnormescers93_histoschoixcers93_histochoixcer93_id_idx;
CREATE INDEX commentairesnormescers93_histoschoixcers93_histochoixcer93_id_idx ON commentairesnormescers93_histoschoixcers93(histochoixcer93_id);

DROP INDEX IF EXISTS commentairesnormescers93_histoschoixcers93_commentairenormecer93_id_histochoixcer93_id_idx;
CREATE UNIQUE INDEX commentairesnormescers93_histoschoixcers93_commentairenormecer93_id_histochoixcer93_id_idx ON commentairesnormescers93_histoschoixcers93(commentairenormecer93_id, histochoixcer93_id);


--------------------------------------------------------------------------------
-- 20121214 : Clôture en masse des référents
--------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'referents', 'datecloture', 'DATE' );

--------------------------------------------------------------------------------
-- 20121218 : Ajout de champ de type texte dans la table valeursparsoussujetscers93
--------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'valeursparsoussujetscers93', 'isautre', 'VARCHAR(1)' );
ALTER TABLE valeursparsoussujetscers93 ALTER COLUMN isautre SET DEFAULT '0';
SELECT alter_table_drop_constraint_if_exists( 'public', 'valeursparsoussujetscers93', 'valeursparsoussujetscers93_isautre_in_list_chk' );
ALTER TABLE valeursparsoussujetscers93 ADD CONSTRAINT valeursparsoussujetscers93_isautre_in_list_chk CHECK ( cakephp_validate_in_list( isautre, ARRAY['0','1'] ) );
SELECT add_missing_table_field( 'public', 'cers93_sujetscers93', 'autrevaleur', 'TEXT' );

--------------------------------------------------------------------------------
-- 20121218 : Ajout de champ de type texte dans la table soussujetscers93
--------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'soussujetscers93', 'isautre', 'VARCHAR(1)' );
ALTER TABLE soussujetscers93 ALTER COLUMN isautre SET DEFAULT '0';
SELECT alter_table_drop_constraint_if_exists( 'public', 'soussujetscers93', 'soussujetscers93_isautre_in_list_chk' );
ALTER TABLE soussujetscers93 ADD CONSTRAINT soussujetscers93_isautre_in_list_chk CHECK ( cakephp_validate_in_list( isautre, ARRAY['0','1'] ) );
SELECT add_missing_table_field( 'public', 'cers93_sujetscers93', 'autresoussujet', 'TEXT' );


--------------------------------------------------------------------------------
-- 20130103 : Ajout du choix de la fonction du membre lors de la prise de réponse / présence
--------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'commissionseps_membreseps', 'fonctionreponsesuppleant_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'commissionseps_membreseps', 'commissionseps_membreseps_fonctionreponsesuppleant_id_fkey', 'fonctionsmembreseps', 'fonctionreponsesuppleant_id');
SELECT add_missing_table_field ('public', 'commissionseps_membreseps', 'fonctionpresencesuppleant_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'commissionseps_membreseps', 'commissionseps_membreseps_fonctionpresencesuppleant_id_fkey', 'fonctionsmembreseps', 'fonctionpresencesuppleant_id');

-- SELECT add_missing_table_field ('public', 'expsproscers93', 'duree', 'INTEGER');
-- ALTER TABLE histoschoixcers93 ADD CONSTRAINT histoschoixcers93_duree_in_list_chk CHECK ( cakephp_validate_in_list( duree, ARRAY[3, 6, 9, 12] ) );
--------------------------------------------------------------------------------
-- 20130120 : Modification de la colonne duree en 2 parties
--	suite à la demande d'améliorations #6268
--------------------------------------------------------------------------------
ALTER TABLE expsproscers93 ALTER COLUMN duree DROP NOT NULL;
SELECT add_missing_table_field ('public', 'expsproscers93', 'nbduree', 'NUMERIC(2)');
-- ALTER TABLE expsproscers93 ALTER COLUMN nbduree SET NOT NULL;
SELECT add_missing_table_field ('public', 'expsproscers93', 'typeduree', 'VARCHAR(5)');
-- ALTER TABLE expsproscers93 ALTER COLUMN typeduree SET NOT NULL;
SELECT alter_table_drop_constraint_if_exists( 'public', 'expsproscers93', 'expsproscers93_typeduree_in_list_chk' );
ALTER TABLE expsproscers93 ADD CONSTRAINT expsproscers93_typeduree_in_list_chk CHECK ( cakephp_validate_in_list( typeduree, ARRAY['jour','mois','annee'] ) );

--------------------------------------------------------------------------------
-- 20130103 : Ajout de la date d'impression de la décision dans la table cers93
--	nécessaire pour les écrans 4. Validation CG
--------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'cers93', 'dateimpressiondecision', 'DATE' );

--------------------------------------------------------------------------------
-- 20130122: Création d'un index unique sur les transferts PDVs
--------------------------------------------------------------------------------

DROP INDEX IF EXISTS transfertspdvs93_nv_adressefoyer_id_vx_adressefoyer_id_idx;
DROP INDEX IF EXISTS transfertspdvs93_nv_adressefoyer_id_vx_adressefoyer_id_vx_orientstruct_id_idx;
CREATE UNIQUE INDEX transfertspdvs93_nv_adressefoyer_id_vx_adressefoyer_id_vx_orientstruct_id_idx ON transfertspdvs93( nv_adressefoyer_id, vx_adressefoyer_id, vx_orientstruct_id  );

--------------------------------------------------------------------------------
-- 20130131 : Ajout du champ pour les observations des courriers de décision des CERs complexe
--			dans les tables cers93 et histoschoixcers93 lorsque ces derniers sont validés ou rejetés
--------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'cers93', 'observationdecision', 'TEXT' );
SELECT add_missing_table_field( 'public', 'histoschoixcers93', 'observationdecision', 'TEXT' );
SELECT add_missing_table_field( 'public', 'decisionscontratscomplexeseps93', 'observationdecision', 'TEXT' );

SELECT alter_table_drop_constraint_if_exists( 'public', 'decisionscontratscomplexeseps93', 'decisionscontratscomplexeseps93_decision_observationdecision_chk' );
ALTER TABLE decisionscontratscomplexeseps93 ADD CONSTRAINT decisionscontratscomplexeseps93_decision_observationdecision_chk CHECK ( observationdecision IS NULL OR decision IN ( 'valide', 'rejete' ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'cers93', 'cers93_positioncer_observationdecision_chk' );
ALTER TABLE cers93 ADD CONSTRAINT cers93_positioncer_observationdecision_chk CHECK (observationdecision IS NULL OR positioncer IN( '99valide', '99rejete' ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'histoschoixcers93', 'histoschoixcers93_decisioncs_observationdecision_chk' );
ALTER TABLE histoschoixcers93 ADD CONSTRAINT histoschoixcers93_decisioncs_observationdecision_chk CHECK ( etape <> '05secondelecture' OR observationdecision IS NULL OR decisioncs = 'valide' );

SELECT alter_table_drop_constraint_if_exists( 'public', 'histoschoixcers93', 'histoschoixcers93_decisioncadre_observationdecision_chk' );
ALTER TABLE histoschoixcers93 ADD CONSTRAINT histoschoixcers93_decisioncadre_observationdecision_chk CHECK ( etape <> '06attaviscadre' OR observationdecision IS NULL OR decisioncadre IN ( 'valide', 'rejete' ) );

--------------------------------------------------------------------------------
-- 20130204: dédoublonnage et création d'un index unique pour les prestations
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION public.dedoublonnage_prestations() RETURNS VOID AS
$$
	DECLARE
		v_row_doublon record;
		v_row_prestation record;
		v_row_test record;
		v_query text;
	BEGIN
		FOR v_row_doublon IN
			SELECT
					personne_id, natprest
				FROM prestations
				GROUP BY personne_id, natprest
				HAVING COUNT(*) > 1
				ORDER BY COUNT(*) DESC, personne_id ASC, natprest ASC
		LOOP
			CREATE TEMPORARY TABLE omega(id INTEGER, personne_id INTEGER, natprest CHARACTER(3));

			INSERT INTO omega ( id, personne_id, natprest )
				SELECT id, personne_id, natprest
					FROM prestations
					WHERE
						prestations.personne_id = v_row_doublon.personne_id
						AND prestations.natprest = v_row_doublon.natprest;

			FOR v_row_prestation IN
				SELECT *
					FROM omega
					WHERE omega.id NOT IN (
						SELECT MAX(id) FROM omega
					)
					ORDER BY id ASC
			LOOP
				v_query := 'DELETE FROM prestations WHERE id = ' || v_row_prestation.id || ';';
				RAISE NOTICE  '%', v_query;
				EXECUTE v_query;
			END LOOP;

			DROP TABLE omega;
		END LOOP;
	END;
$$
LANGUAGE 'plpgsql';

SELECT public.dedoublonnage_prestations();
DROP FUNCTION public.dedoublonnage_prestations();

DROP INDEX IF EXISTS prestations_personne_id_natprest_idx;
CREATE UNIQUE INDEX prestations_personne_id_natprest_idx ON prestations(personne_id, natprest);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************