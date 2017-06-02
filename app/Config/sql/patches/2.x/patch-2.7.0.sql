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

CREATE OR REPLACE FUNCTION public.enumtype_to_validate_in_list( p_schema TEXT, p_table TEXT, p_field TEXT ) RETURNS VOID AS
$$
	DECLARE
		v_row_field RECORD;
		v_select_query TEXT;
		v_count_query TEXT;
		v_row_count RECORD;
		v_enum_query TEXT;
		v_enum_row RECORD;
		v_enum_value TEXT;
		v_enum_i INTEGER;

		v_altercolumn_query TEXT;
		v_droptype_query TEXT;
		v_inlist_query TEXT;
		v_inlist_maxlength INTEGER;
		v_columndefault_query TEXT;
	BEGIN
		v_select_query := 'SELECT isc.table_schema, isc.table_name, isc.column_name, isc.udt_name, isc.column_default
			FROM information_schema.columns AS isc
			WHERE
				isc.data_type = ''USER-DEFINED''
				AND isc.udt_name IN (
					SELECT isc2.udt_name
						FROM information_schema.columns AS isc2
						WHERE
							isc2.data_type = ''USER-DEFINED''
							AND isc2.table_schema = ''' || p_schema || '''
							AND isc2.table_name = ''' || p_table || '''
							AND isc2.column_name = ''' || p_field || '''
			)';

		v_count_query := 'SELECT COUNT(tables.*) AS count FROM ( ' || v_select_query || ' ) AS tables';
		EXECUTE v_count_query INTO v_row_count;

		-- FIXME: pas d'erreur en-dehors des tests
		IF v_row_count.count = 0 THEN
			RAISE EXCEPTION 'Le champ %.%.% n''est pas de type ENUM', p_schema, p_table, p_field;
		END IF;

		-- Transformation de la requête pour ne concerner que le champ demandé
		v_select_query := v_select_query
			|| 'AND isc.table_schema = ''' || p_schema || '''
			AND isc.table_name = ''' || p_table || '''
			AND isc.column_name = ''' || p_field || ''';';

		FOR v_row_field IN EXECUTE v_select_query
		LOOP
			-- 1°) Récupération des valeurs
			v_enum_query := 'SELECT enum_range( null::' || v_row_field.udt_name || ' )::TEXT[] AS enum;';
			EXECUTE v_enum_query INTO v_enum_row;

			-- Recherche de la longueur maximale du champ
			v_inlist_maxlength := 0;
			FOR v_enum_value IN SELECT unnest( v_enum_row.enum )
			LOOP
				IF LENGTH( v_enum_value ) > v_inlist_maxlength THEN
					v_inlist_maxlength := LENGTH( v_enum_value );
				END IF;
			END LOOP;

			-- 2°) Transformation de la colonne
 			v_altercolumn_query := 'ALTER TABLE ' || p_schema || '.' || p_table || ' ALTER COLUMN ' || p_field || ' TYPE VARCHAR(' || v_inlist_maxlength || ') USING CAST(' || p_field || ' AS VARCHAR(' || v_inlist_maxlength || '));';
			EXECUTE v_altercolumn_query;

			-- 3°) Ajout de la contrainte cakephp_calidate_in_list()
			v_inlist_query := 'ALTER TABLE ' || p_schema || '.' || p_table || ' ADD CONSTRAINT ' || p_table || '_' || p_field || '_in_list_chk CHECK ( cakephp_validate_in_list( ' || p_field || ', ARRAY[''' || ARRAY_TO_STRING( v_enum_row.enum, ''', ''' ) || '''] ) );';
			EXECUTE v_inlist_query;

			-- 4°) Changement du type de la valeur par défaut
			IF v_row_field.column_default ~ '::[^'']+$' THEN
				v_columndefault_query := 'ALTER TABLE ' || p_schema || '.' || p_table || ' ALTER COLUMN ' || p_field || ' SET DEFAULT ' || REGEXP_REPLACE( v_row_field.column_default, '::[^'']+$', '' ) || ';';
				EXECUTE v_columndefault_query;
			END IF;

			-- 5°) Suppression de l'ENUM si on est le seul à l'utiliser
			IF v_row_count.count = 1 THEN
				v_droptype_query := 'DROP TYPE IF EXISTS ' || v_row_field.udt_name || ';';
				EXECUTE v_droptype_query;
			END IF;
		END LOOP;
	END;
$$
LANGUAGE plpgsql VOLATILE;

COMMENT ON FUNCTION public.enumtype_to_validate_in_list( p_schema TEXT, p_table TEXT, p_field TEXT ) IS
	'Permet de transformer un champ de type ENUM en un champ de type VARCHAR, avec ajout d''une contrainte cakephp_validate_in_list() et la suppression du type s''il n''est plus utilisé.';

--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION public.table_enumtypes_to_validate_in_list( p_schema TEXT, p_table TEXT ) RETURNS VOID AS
$$
	DECLARE
		v_row_field RECORD;
		v_select_query TEXT;
		v_function_query TEXT;
	BEGIN
		v_select_query := 'SELECT isc.table_schema, isc.table_name, isc.column_name
			FROM information_schema.columns AS isc
			WHERE
				isc.data_type = ''USER-DEFINED''
				AND isc.table_schema = ''' || p_schema || '''
				AND isc.table_name = ''' || p_table || ''';';

		FOR v_row_field IN EXECUTE v_select_query
		LOOP
			EXECUTE public.enumtype_to_validate_in_list( v_row_field.table_schema, v_row_field.table_name, v_row_field.column_name );
		END LOOP;
	END;
$$
LANGUAGE plpgsql VOLATILE;

COMMENT ON FUNCTION public.table_enumtypes_to_validate_in_list( p_schema TEXT, p_table TEXT ) IS
	'Permet de transformer tous les champs de type ENUM d''une table en champs de type VARCHAR, avec ajout d''une contrainte cakephp_validate_in_list() et la suppression du type s''il n''est plus utilisé.';

--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION public.table_defaultvalues_enumtypes_to_varchar( p_schema TEXT, p_table TEXT ) RETURNS VOID AS
$$
	DECLARE
		v_row_field RECORD;
		v_select_query TEXT;
		v_columndefault_query TEXT;
	BEGIN
		v_select_query := 'SELECT isc.table_schema, isc.table_name, isc.column_name, isc.udt_name, isc.column_default
			FROM information_schema.columns AS isc
			WHERE
				isc.data_type <> ''USER-DEFINED''
				AND isc.table_schema = ''' || p_schema || '''
				AND isc.table_name = ''' || p_table || '''
				AND isc.column_default ~ ''::type_[^'''']+$''
			;';

		FOR v_row_field IN EXECUTE v_select_query
		LOOP
			v_columndefault_query := 'ALTER TABLE ' || v_row_field.table_schema || '.' || v_row_field.table_name || ' ALTER COLUMN ' || v_row_field.column_name || ' SET DEFAULT ' || REGEXP_REPLACE( v_row_field.column_default, '::[^'']+$', '' ) || ';';
			EXECUTE v_columndefault_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql VOLATILE;

COMMENT ON FUNCTION public.table_defaultvalues_enumtypes_to_varchar( p_schema TEXT, p_table TEXT ) IS
	'Permet de transformer toutes les valeurs par défaut de type ENUM des champs de type non ENUM d''une table.';

--------------------------------------------------------------------------------

SELECT public.table_enumtypes_to_validate_in_list( 'public', 'cuis' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'cuis' );

--------------------------------------------------------------------------------
-- 20130121: ajout des valeurs 'annule' et 'reporte' à la décision de la thématique
-- d'EP nonorientationsproseps66.
--------------------------------------------------------------------------------

SELECT public.alter_enumtype( 'TYPE_DECISIONNONORIENTATIONPROEP66', ARRAY['reorientation','maintienref','annule','reporte'] );

--------------------------------------------------------------------------------
-- 20140121: Création des nouvelles tables intégrant les nouveaux Codes ROME
--------------------------------------------------------------------------------
DROP TABLE IF EXISTS codesfamillesromev3 CASCADE;
CREATE TABLE codesfamillesromev3 (
    id          SERIAL NOT NULL PRIMARY KEY,
    code        VARCHAR(1) NOT NULL,
    name        VARCHAR(150) NOT NULL,
    created     TIMESTAMP WITHOUT TIME ZONE,
    modified    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE codesfamillesromev3 IS 'Codes ROME V3 - Codes familles';

DROP INDEX IF EXISTS codesfamillesromev3_name_idx;
CREATE INDEX codesfamillesromev3_name_idx ON codesfamillesromev3( name );
DROP INDEX IF EXISTS codesfamillesromev3_code_idx;
CREATE INDEX codesfamillesromev3_code_idx ON codesfamillesromev3( code );

DROP TABLE IF EXISTS codesdomainesprosromev3 CASCADE;
CREATE TABLE codesdomainesprosromev3 (
    id                          SERIAL NOT NULL PRIMARY KEY,
    codefamilleromev3_id          INTEGER NOT NULL REFERENCES codesfamillesromev3(id) ON DELETE CASCADE ON UPDATE CASCADE,
    code                        VARCHAR(2) NOT NULL,
    name                        VARCHAR(150) NOT NULL,
    created                     TIMESTAMP WITHOUT TIME ZONE,
    modified                    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE codesdomainesprosromev3 IS 'Codes ROME V3 - Domaines professionnels';

DROP INDEX IF EXISTS codesdomainesprosromev3_name_idx;
CREATE INDEX codesdomainesprosromev3_name_idx ON codesdomainesprosromev3( name );
DROP INDEX IF EXISTS codesdomainesprosromev3_code_idx;
CREATE INDEX codesdomainesprosromev3_code_idx ON codesdomainesprosromev3( code );
DROP INDEX IF EXISTS codesdomainesprosromev3_codefamilleromev3_id_idx;
CREATE INDEX codesdomainesprosromev3_codefamilleromev3_id_idx ON codesdomainesprosromev3( codefamilleromev3_id );

DROP TABLE IF EXISTS codesmetiersromev3 CASCADE;
CREATE TABLE codesmetiersromev3 (
    id                              SERIAL NOT NULL PRIMARY KEY,
    codedomaineproromev3_id          INTEGER NOT NULL REFERENCES codesdomainesprosromev3(id) ON DELETE CASCADE ON UPDATE CASCADE,
    code                            VARCHAR(2) NOT NULL,
    name                            VARCHAR(150) NOT NULL,
    created                         TIMESTAMP WITHOUT TIME ZONE,
    modified                        TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE codesmetiersromev3 IS 'Codes ROME V3 - Codes métiers';

DROP INDEX IF EXISTS codesmetiersromev3_name_idx;
CREATE INDEX codesmetiersromev3_name_idx ON codesmetiersromev3( name );
DROP INDEX IF EXISTS codesmetiersromev3_code_idx;
CREATE INDEX codesmetiersromev3_code_idx ON codesmetiersromev3( code );
DROP INDEX IF EXISTS codesmetiersromev3_codedomaineprorome_id_idx;
CREATE INDEX codesmetiersromev3_codedomaineproromev3_id_idx ON codesmetiersromev3( codedomaineproromev3_id );

DROP TABLE IF EXISTS codesappellationsromev3 CASCADE;
CREATE TABLE codesappellationsromev3 (
    id                              SERIAL NOT NULL PRIMARY KEY,
    codemetierromev3_id               INTEGER NOT NULL REFERENCES codesmetiersromev3(id) ON DELETE CASCADE ON UPDATE CASCADE,
    name                            VARCHAR(150) NOT NULL,
    created                         TIMESTAMP WITHOUT TIME ZONE,
    modified                        TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE codesappellationsromev3 IS 'Codes ROME V3 - Codes appellations métiers';

DROP INDEX IF EXISTS codesappellationsromev3_name_idx;
CREATE INDEX codesappellationsromev3_name_idx ON codesappellationsromev3( name );
DROP INDEX IF EXISTS codesappellationsromev3_codemetierrome_id_idx;
CREATE INDEX codesappellationsromev3_codemetierromev3_id_idx ON codesappellationsromev3( codemetierromev3_id );


ALTER TABLE decisionsdossierspcgs66 ALTER COLUMN infotransmise TYPE TEXT;

-- -----------------------------------------------------------------------------
-- Règlde validation CakePHP alphaNumeric ( string|array $check )
-- -----------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION cakephp_validate_alpha_numeric( p_check text ) RETURNS boolean AS
$$
	BEGIN
		RETURN p_check IS NULL OR ( p_check ~ E'^[^[:punct:]|[:blank:]|[:space:]|[:cntrl:]]+$'  );
	END;
$$
LANGUAGE plpgsql IMMUTABLE;

COMMENT ON FUNCTION cakephp_validate_alpha_numeric( p_check text ) IS
	'@see http://api.cakephp.org/2.2/class-Validation.html#_alphaNumeric';

-- INFO: voir http://postgresql.developpez.com/sources/?page=chaines
CREATE OR REPLACE FUNCTION "public"."noaccents_upper" (text) RETURNS text AS
$body$
	DECLARE
		st text;

	BEGIN
		-- On transforme les caractèes accentués et on passe en majuscule
		st:=translate($1,'aàäâeéèêëiïîoôöuùûücçñAÀÄÂEÉÈÊËIÏÎOÔÖUÙÛÜCÇÑ','AAAAEEEEEIIIOOOUUUUCCNAAAAEEEEEIIIOOOUUUUCCN');
		st:=upper(st);

		return st;
	END;
$body$
LANGUAGE 'plpgsql' IMMUTABLE RETURNS NULL ON NULL INPUT SECURITY INVOKER;

-- *****************************************************************************
-- 20140307: ajout du fax pour la structure référente
-- *****************************************************************************
SELECT add_missing_table_field( 'public', 'structuresreferentes', 'numfax', 'VARCHAR(20)' );
ALTER TABLE structuresreferentes ALTER COLUMN numfax SET DEFAULT NULL;

-- *****************************************************************************
-- Fiche de prescription - CG 93
-- lib/Cake/Console/cake Graphviz.GraphvizMpd -t "/(^personnes$|^referents$|^fichesprescriptions93$|fps93$|^situationsallocataires$|^structuresreferentes$)/" && dot -K fdp -T png -o ./graphviz_mpd.png ./graphviz_mpd.dot && gwenview ./graphviz_mpd.png > /dev/null 2>&1
-- *****************************************************************************

DROP TABLE IF EXISTS thematiquesfps93 CASCADE;
CREATE TABLE thematiquesfps93 (
    id			SERIAL NOT NULL PRIMARY KEY,
	type		VARCHAR(10) NOT NULL,
    name		VARCHAR(250) NOT NULL,
    created     TIMESTAMP WITHOUT TIME ZONE,
    modified    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE thematiquesfps93 IS 'Thématiques pour la fiche de prescription - CG 93';

CREATE UNIQUE INDEX thematiquesfps93_type_name_idx ON thematiquesfps93( type, NOACCENTS_UPPER( name ) );

ALTER TABLE thematiquesfps93 ADD CONSTRAINT thematiquesfps93_type_in_list_chk CHECK ( cakephp_validate_in_list( type, ARRAY['pdi','horspdi'] ) );

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS categoriesfps93 CASCADE;
CREATE TABLE categoriesfps93 (
    id					SERIAL NOT NULL PRIMARY KEY,
	thematiquefp93_id	INTEGER NOT NULL REFERENCES thematiquesfps93(id),
    name				VARCHAR(250) NOT NULL,
    created				TIMESTAMP WITHOUT TIME ZONE,
    modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE categoriesfps93 IS 'Catégories pour la fiche de prescription - CG 93';

CREATE UNIQUE INDEX categoriesfps93_thematiquefp93_id_name_idx ON categoriesfps93( thematiquefp93_id, NOACCENTS_UPPER( name ) );

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS filieresfps93 CASCADE;
CREATE TABLE filieresfps93 (
    id					SERIAL NOT NULL PRIMARY KEY,
	categoriefp93_id	INTEGER NOT NULL REFERENCES categoriesfps93(id),
    name				VARCHAR(250) NOT NULL,
    created				TIMESTAMP WITHOUT TIME ZONE,
    modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE filieresfps93 IS 'Filières pour la fiche de prescription - CG 93';

CREATE UNIQUE INDEX filieresfps93_categoriefp93_id_name_idx ON filieresfps93( categoriefp93_id, NOACCENTS_UPPER( name ) );

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS prestatairesfps93 CASCADE;
CREATE TABLE prestatairesfps93 (
    id					SERIAL NOT NULL PRIMARY KEY,
    name				VARCHAR(250) NOT NULL,
	-- TODO: mettre le reste des champs
    created				TIMESTAMP WITHOUT TIME ZONE,
    modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE prestatairesfps93 IS 'Prestataires pour la fiche de prescription - CG 93';

CREATE UNIQUE INDEX prestatairesfps93_name_idx ON prestatairesfps93( NOACCENTS_UPPER( name ) );

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS adressesprestatairesfps93 CASCADE;
CREATE TABLE adressesprestatairesfps93 (
    id					SERIAL NOT NULL PRIMARY KEY,
	prestatairefp93_id	INTEGER NOT NULL REFERENCES prestatairesfps93( id ) ON DELETE CASCADE ON UPDATE CASCADE,
	adresse				TEXT NOT NULL,
	codepos				VARCHAR(5) NOT NULL,
	localite			VARCHAR(250) NOT NULL,
	tel					VARCHAR(10) DEFAULT NULL,
	fax					VARCHAR(10) DEFAULT NULL,
	email				VARCHAR(100) DEFAULT NULL,
    created				TIMESTAMP WITHOUT TIME ZONE,
    modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE adressesprestatairesfps93 IS 'Adresses des prestataires pour la fiche de prescription - CG 93';

CREATE INDEX adressesprestatairesfps93_prestatairefp93_id_idx ON adressesprestatairesfps93( prestatairefp93_id );

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS actionsfps93 CASCADE;
CREATE TABLE actionsfps93 (
    id							SERIAL NOT NULL PRIMARY KEY,
	filierefp93_id				INTEGER NOT NULL REFERENCES filieresfps93(id),
	adresseprestatairefp93_id	INTEGER NOT NULL REFERENCES adressesprestatairesfps93(id),
    name						VARCHAR(250) NOT NULL,
    numconvention				VARCHAR(250) NOT NULL,
	annee						INTEGER NOT NULL,
	duree						VARCHAR(100) DEFAULT NULL,
	actif						CHAR(1) NOT NULL,
    created						TIMESTAMP WITHOUT TIME ZONE,
    modified					TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE actionsfps93 IS 'Actions pour la fiche de prescription - CG 93';

CREATE INDEX actionsfps93_filierefp93_id_idx ON actionsfps93( filierefp93_id );
CREATE INDEX actionsfps93_adresseprestatairefp93_id_idx ON actionsfps93( adresseprestatairefp93_id );
CREATE UNIQUE INDEX actionsfps93_upper_numconvention_idx ON actionsfps93( UPPER( numconvention ) );
CREATE UNIQUE INDEX actionsfps93_filierefp93_id_adresseprestatairefp93_id_name_annee_actif_idx ON actionsfps93( filierefp93_id, adresseprestatairefp93_id, NOACCENTS_UPPER( name ), annee ) WHERE actif = '1';

ALTER TABLE actionsfps93 ADD CONSTRAINT actionsfps93_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY['0','1'] ) );
ALTER TABLE actionsfps93 ADD CONSTRAINT actionsfps93_numconvention_alpha_numeric_chk CHECK ( cakephp_validate_alpha_numeric( numconvention ) );

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS motifsnonreceptionsfps93 CASCADE;
CREATE TABLE motifsnonreceptionsfps93 (
    id			SERIAL NOT NULL PRIMARY KEY,
    name		VARCHAR(250) NOT NULL,
    autre		VARCHAR(1) NOT NULL DEFAULT '0',
    created     TIMESTAMP WITHOUT TIME ZONE,
    modified    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE motifsnonreceptionsfps93 IS 'Motifs de non réception pour la fiche de prescription - CG 93';

CREATE UNIQUE INDEX motifsnonreceptionsfps93_name_idx ON motifsnonreceptionsfps93( name );

ALTER TABLE motifsnonreceptionsfps93 ADD CONSTRAINT motifsnonreceptionsfps93_autre_in_list_chk CHECK ( cakephp_validate_in_list( autre, ARRAY['0','1'] ) );

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS motifsnonretenuesfps93 CASCADE;
CREATE TABLE motifsnonretenuesfps93 (
    id			SERIAL NOT NULL PRIMARY KEY,
    name		VARCHAR(250) NOT NULL,
    autre		VARCHAR(1) NOT NULL DEFAULT '0',
    created     TIMESTAMP WITHOUT TIME ZONE,
    modified    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE motifsnonretenuesfps93 IS 'Motifs de non réception pour la fiche de prescription - CG 93';

CREATE UNIQUE INDEX motifsnonretenuesfps93_name_idx ON motifsnonretenuesfps93( name );

ALTER TABLE motifsnonretenuesfps93 ADD CONSTRAINT motifsnonretenuesfps93_autre_in_list_chk CHECK ( cakephp_validate_in_list( autre, ARRAY['0','1'] ) );

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS motifsnonsouhaitsfps93 CASCADE;
CREATE TABLE motifsnonsouhaitsfps93 (
    id			SERIAL NOT NULL PRIMARY KEY,
    name		VARCHAR(250) NOT NULL,
    autre		VARCHAR(1) NOT NULL DEFAULT '0',
    created     TIMESTAMP WITHOUT TIME ZONE,
    modified    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE motifsnonsouhaitsfps93 IS 'Motifs de non souhait d''intégration pour la fiche de prescription - CG 93';

CREATE UNIQUE INDEX motifsnonsouhaitsfps93_name_idx ON motifsnonsouhaitsfps93( name );

ALTER TABLE motifsnonsouhaitsfps93 ADD CONSTRAINT motifsnonsouhaitsfps93_autre_in_list_chk CHECK ( cakephp_validate_in_list( autre, ARRAY['0','1'] ) );

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS motifsnonintegrationsfps93 CASCADE;
CREATE TABLE motifsnonintegrationsfps93 (
    id			SERIAL NOT NULL PRIMARY KEY,
    name		VARCHAR(250) NOT NULL,
    autre		VARCHAR(1) NOT NULL DEFAULT '0',
    created     TIMESTAMP WITHOUT TIME ZONE,
    modified    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE motifsnonintegrationsfps93 IS 'Motifs de non intégration pour la fiche de prescription - CG 93';

CREATE UNIQUE INDEX motifsnonintegrationsfps93_name_idx ON motifsnonintegrationsfps93( name );

ALTER TABLE motifsnonintegrationsfps93 ADD CONSTRAINT motifsnonintegrationsfps93_autre_in_list_chk CHECK ( cakephp_validate_in_list( autre, ARRAY['0','1'] ) );

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS prestataireshorspdifps93 CASCADE;
CREATE TABLE prestataireshorspdifps93 (
    id					SERIAL NOT NULL PRIMARY KEY,
	name				VARCHAR(250) NOT NULL,
	adresse				TEXT NOT NULL,
	codepos				VARCHAR(5) NOT NULL,
	localite			VARCHAR(250) NOT NULL,
	tel					VARCHAR(10) DEFAULT NULL,
	fax					VARCHAR(10) DEFAULT NULL,
	email				VARCHAR(100) DEFAULT NULL,
    created				TIMESTAMP WITHOUT TIME ZONE,
    modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE prestataireshorspdifps93 IS 'Prestataires hors PDI pour la fiche de prescription - CG 93';

CREATE INDEX prestataireshorspdifps93_name_idx ON prestataireshorspdifps93( name );

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS fichesprescriptions93 CASCADE;
CREATE TABLE fichesprescriptions93 (
    id							SERIAL NOT NULL PRIMARY KEY,
    personne_id					INTEGER NOT NULL REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	statut						VARCHAR(30) NOT NULL,
	-- Bloc "Prescripteur/Référent"
    referent_id					INTEGER NOT NULL REFERENCES referents(id) ON DELETE CASCADE ON UPDATE CASCADE,
    objet						TEXT DEFAULT NULL,
	-- Bloc "Prestataire/Partenaire"
	rdvprestataire_date			TIMESTAMP WITHOUT TIME ZONE DEFAULT NULL,
	rdvprestataire_personne		TEXT DEFAULT NULL,
    filierefp93_id				INTEGER NOT NULL REFERENCES filieresfps93(id) ON DELETE CASCADE ON UPDATE CASCADE,
    actionfp93_id				INTEGER DEFAULT NULL REFERENCES actionsfps93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	-- Pour le catalogue Hors PDI, on stocke l'intitulé dans la fiche
    actionfp93					VARCHAR(250) DEFAULT NULL,
	-- Prestataire PDI ou Hors PDI
	adresseprestatairefp93_id	INTEGER DEFAULT NULL REFERENCES adressesprestatairesfps93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	prestatairehorspdifp93_id	INTEGER DEFAULT NULL REFERENCES prestataireshorspdifps93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	rdvprestataire_adresse		TEXT DEFAULT NULL,
	dd_action					DATE DEFAULT NULL,
	df_action					DATE DEFAULT NULL,
	duree_action				VARCHAR(100) DEFAULT NULL,
	documentbeneffp93_autre		TEXT DEFAULT NULL,
	-- Bloc "Engagement"
	date_signature				DATE DEFAULT NULL,
	-- Bloc "Modalités de transmission"
	date_transmission			DATE DEFAULT NULL,
	-- Bloc "Résultat de l'effectivité de la prescription"
	date_retour					DATE DEFAULT NULL,
	benef_retour_presente		VARCHAR(6) DEFAULT NULL,
	date_presente_benef			DATE DEFAULT NULL,
	retour_nom_partenaire		TEXT DEFAULT NULL,
	date_signature_partenaire	DATE DEFAULT NULL,
	-- Bloc "Suivi de l'action"
	personne_recue				VARCHAR(1) DEFAULT NULL,
	motifnonreceptionfp93_id	INTEGER DEFAULT NULL REFERENCES motifsnonreceptionsfps93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	personne_nonrecue_autre		TEXT DEFAULT NULL,

	personne_retenue			VARCHAR(1) DEFAULT NULL,
	motifnonretenuefp93_id		INTEGER DEFAULT NULL REFERENCES motifsnonretenuesfps93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	personne_nonretenue_autre	TEXT DEFAULT NULL,

	personne_souhaite_integrer	VARCHAR(1) DEFAULT NULL,
	motifnonsouhaitfp93_id		INTEGER DEFAULT NULL REFERENCES motifsnonsouhaitsfps93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	personne_nonsouhaite_autre	TEXT DEFAULT NULL,

	personne_a_integre			VARCHAR(1) DEFAULT NULL,
	personne_date_integration	DATE DEFAULT NULL,
	motifnonintegrationfp93_id	INTEGER DEFAULT NULL REFERENCES motifsnonintegrationsfps93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	personne_nonintegre_autre	TEXT DEFAULT NULL,

	date_bilan_mi_parcours		DATE DEFAULT NULL,
	date_bilan_final			DATE DEFAULT NULL,

	motif_annulation			TEXT DEFAULT NULL,
	date_annulation				DATE DEFAULT NULL,

    created						TIMESTAMP WITHOUT TIME ZONE,
    modified					TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE fichesprescriptions93 IS 'Fiche de prescription - CG 93';

CREATE INDEX fichesprescriptions93_personne_id_idx ON fichesprescriptions93( personne_id );
CREATE INDEX fichesprescriptions93_referent_id_idx ON fichesprescriptions93( referent_id );
CREATE INDEX fichesprescriptions93_filierefp93_id_idx ON fichesprescriptions93( filierefp93_id );
CREATE INDEX fichesprescriptions93_actionfp93_id_idx ON fichesprescriptions93( actionfp93_id );
CREATE INDEX fichesprescriptions93_actionfp93_idx ON fichesprescriptions93( actionfp93 );
CREATE INDEX fichesprescriptions93_adresseprestatairefp93_id_idx ON fichesprescriptions93( adresseprestatairefp93_id );
CREATE UNIQUE INDEX fichesprescriptions93_prestatairehorspdifp93_id_idx ON fichesprescriptions93( prestatairehorspdifp93_id );

ALTER TABLE fichesprescriptions93 ADD CONSTRAINT fichesprescriptions93_statut_in_list_chk CHECK ( cakephp_validate_in_list( statut, ARRAY['01renseignee', '02signee', '03transmise_partenaire', '04effectivite_renseignee', '05suivi_renseigne', '99annulee'] ) );
ALTER TABLE fichesprescriptions93 ADD CONSTRAINT fichesprescriptions93_benef_retour_presente_in_list_chk CHECK ( cakephp_validate_in_list( benef_retour_presente, ARRAY['oui', 'non', 'excuse'] ) );
ALTER TABLE fichesprescriptions93 ADD CONSTRAINT fichesprescriptions93_personne_recue_in_list_chk CHECK ( cakephp_validate_in_list( personne_recue, ARRAY['0', '1'] ) );
ALTER TABLE fichesprescriptions93 ADD CONSTRAINT fichesprescriptions93_personne_retenue_in_list_chk CHECK ( cakephp_validate_in_list( personne_retenue, ARRAY['0', '1'] ) );
ALTER TABLE fichesprescriptions93 ADD CONSTRAINT fichesprescriptions93_personne_souhaite_integrer_in_list_chk CHECK ( cakephp_validate_in_list( personne_souhaite_integrer, ARRAY['0', '1'] ) );
ALTER TABLE fichesprescriptions93 ADD CONSTRAINT fichesprescriptions93_personne_a_integre_in_list_chk CHECK ( cakephp_validate_in_list( personne_a_integre, ARRAY['0', '1'] ) );

ALTER TABLE fichesprescriptions93 ADD CONSTRAINT fichesprescriptions93_adresseprestatairefp93_id_or_prestatairehorspdifp93_id_isnull_chk CHECK(
	( adresseprestatairefp93_id IS NULL AND prestatairehorspdifp93_id IS NOT NULL )
	OR ( adresseprestatairefp93_id IS NOT NULL AND prestatairehorspdifp93_id IS NULL )
);

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS modstransmsfps93 CASCADE;
CREATE TABLE modstransmsfps93 (
    id			SERIAL NOT NULL PRIMARY KEY,
    name		VARCHAR(250) NOT NULL,
    created		TIMESTAMP WITHOUT TIME ZONE,
    modified	TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE modstransmsfps93 IS 'Paramétrage des modalités de transmission pour la fiche de prescription - CG 93';

CREATE UNIQUE INDEX modstransmsfps93_name_idx ON modstransmsfps93( name );

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS fichesprescriptions93_modstransmsfps93 CASCADE;
CREATE TABLE fichesprescriptions93_modstransmsfps93 (
    id						SERIAL NOT NULL PRIMARY KEY,
	ficheprescription93_id	INTEGER NOT NULL REFERENCES fichesprescriptions93( id ) ON UPDATE CASCADE ON DELETE CASCADE,
	modtransmfp93_id		INTEGER NOT NULL REFERENCES modstransmsfps93( id ) ON UPDATE CASCADE ON DELETE CASCADE,
    created					TIMESTAMP WITHOUT TIME ZONE,
    modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE fichesprescriptions93_modstransmsfps93 IS 'Modalités de transmission pour la fiche de prescription - CG 93';

CREATE INDEX fichesprescriptions93_modstransmsfps93_ficheprescription93_id_idx ON fichesprescriptions93_modstransmsfps93( ficheprescription93_id );
CREATE INDEX fichesprescriptions93_modstransmsfps93_modtransmfp93_id_idx ON fichesprescriptions93_modstransmsfps93( modtransmfp93_id );
CREATE UNIQUE INDEX fichesprescriptions93_modstransmsfps93_ficheprescription93_id_modtransmfp93_id_idx ON fichesprescriptions93_modstransmsfps93( ficheprescription93_id, modtransmfp93_id );

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS instantanesdonneesfps93 CASCADE;
CREATE TABLE instantanesdonneesfps93 (
    id						SERIAL NOT NULL PRIMARY KEY,
    ficheprescription93_id	INTEGER NOT NULL REFERENCES fichesprescriptions93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	-- Partie "Bloc Prescripteur / Référent"
	referent_fonction		VARCHAR(30) NOT NULL,
	structure_name			VARCHAR(100) NOT NULL,
	structure_num_voie		VARCHAR(15) NOT NULL,
	structure_type_voie 	VARCHAR(6) NOT NULL,
	structure_nom_voie		VARCHAR(50) NOT NULL,
	structure_code_postal	CHAR(5) NOT NULL,
	structure_ville			VARCHAR(45) NOT NULL,
	structure_tel			VARCHAR(10) DEFAULT NULL,
	structure_fax			VARCHAR(10) DEFAULT NULL,
	referent_email			VARCHAR(78) DEFAULT NULL,
	-- Partie "Bénéficiaire"
    benef_qual				VARCHAR(3) DEFAULT NULL,
    benef_nom				VARCHAR(50) DEFAULT NULL,
    benef_prenom			VARCHAR(50) DEFAULT NULL,
    benef_dtnai				DATE DEFAULT NULL,
    benef_numvoie			VARCHAR(6) DEFAULT NULL,
    benef_typevoie			VARCHAR(4) DEFAULT NULL,
    benef_nomvoie			VARCHAR(25) DEFAULT NULL,
    benef_complideadr		VARCHAR(38) DEFAULT NULL,
    benef_compladr			VARCHAR(26) DEFAULT NULL,
    benef_numcomptt			VARCHAR(5) DEFAULT NULL,
    benef_numcomrat			VARCHAR(5) DEFAULT NULL,
    benef_codepos			VARCHAR(5) DEFAULT NULL,
    benef_locaadr			VARCHAR(26) DEFAULT NULL,
	benef_tel_fixe			VARCHAR(14) DEFAULT NULL,
	benef_tel_port			VARCHAR(14) DEFAULT NULL,
	benef_email				VARCHAR(100) DEFAULT NULL,
	benef_identifiantpe     VARCHAR(11) DEFAULT NULL,
	benef_inscritpe         VARCHAR(1) DEFAULT NULL,
    benef_matricule			VARCHAR(15) DEFAULT NULL,
    benef_natpf_socle		VARCHAR(1) DEFAULT NULL,
    benef_natpf_majore		VARCHAR(1) DEFAULT NULL,
    benef_natpf_activite	VARCHAR(1) DEFAULT NULL,
	benef_natpf_3mois		VARCHAR(25) DEFAULT NULL,
	benef_nivetu			VARCHAR(4) DEFAULT NULL,
	benef_dernier_dip		VARCHAR(250) DEFAULT NULL,
	benef_dip_ce			VARCHAR(1) DEFAULT NULL,
	benef_etatdosrsa        VARCHAR(1) DEFAULT NULL,
	benef_toppersdrodevorsa VARCHAR(1) DEFAULT NULL,
	benef_dd_ci				DATE DEFAULT NULL,
	benef_df_ci				DATE DEFAULT NULL,
	benef_positioncer		VARCHAR(13) DEFAULT NULL,
    created					TIMESTAMP WITHOUT TIME ZONE,
    modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE instantanesdonneesfps93 IS '"Instantané" de certaines données pour la fiche de prescription - CG 93';

CREATE UNIQUE INDEX instantanesdonneesfps93_ficheprescription93_id_idx ON instantanesdonneesfps93( ficheprescription93_id );

ALTER TABLE instantanesdonneesfps93 ADD CONSTRAINT instantanesdonneesfps93_benef_natpf_socle_in_list_chk CHECK ( cakephp_validate_in_list( benef_natpf_socle, ARRAY['0', '1'] ) );
ALTER TABLE instantanesdonneesfps93 ADD CONSTRAINT instantanesdonneesfps93_benef_natpf_majore_in_list_chk CHECK ( cakephp_validate_in_list( benef_natpf_majore, ARRAY['0', '1'] ) );
ALTER TABLE instantanesdonneesfps93 ADD CONSTRAINT instantanesdonneesfps93_benef_natpf_activite_in_list_chk CHECK ( cakephp_validate_in_list( benef_natpf_activite, ARRAY['0', '1'] ) );
ALTER TABLE instantanesdonneesfps93 ADD CONSTRAINT instantanesdonneesfps93_benef_nivetu_in_list_chk CHECK ( cakephp_validate_in_list( benef_nivetu, ARRAY['1201', '1202', '1203', '1204', '1205', '1206', '1207'] ) );
ALTER TABLE instantanesdonneesfps93 ADD CONSTRAINT instantanesdonneesfps93_benef_dip_ce_in_list_chk CHECK ( cakephp_validate_in_list( benef_dip_ce, ARRAY['0', '1'] ) );
ALTER TABLE instantanesdonneesfps93 ADD CONSTRAINT instantanesdonneesfps93_benef_inscritpe_in_list_chk CHECK ( cakephp_validate_in_list( benef_inscritpe, ARRAY['0', '1'] ) );
ALTER TABLE instantanesdonneesfps93 ADD CONSTRAINT instantanesdonneesfps93_benef_positioncer_in_list_chk CHECK ( cakephp_validate_in_list( benef_positioncer, ARRAY['validationpdv', 'validationcg', 'valide', 'aucun'] ) );
ALTER TABLE instantanesdonneesfps93 ADD CONSTRAINT instantanesdonneesfps93_benef_etatdosrsa_in_list_chk CHECK ( cakephp_validate_in_list( benef_etatdosrsa, ARRAY['Z', '0', '1', '2', '3', '4', '5', '6'] ) );
ALTER TABLE instantanesdonneesfps93 ADD CONSTRAINT instantanesdonneesfps93_benef_toppersdrodevorsa_in_list_chk CHECK ( cakephp_validate_in_list( benef_toppersdrodevorsa, ARRAY['0', '1'] ) );

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS documentsbenefsfps93 CASCADE;
CREATE TABLE documentsbenefsfps93 (
    id			SERIAL NOT NULL PRIMARY KEY,
	name		VARCHAR(250) NOT NULL,
	autre		VARCHAR(1) NOT NULL DEFAULT '0',
    created		TIMESTAMP WITHOUT TIME ZONE,
    modified	TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE documentsbenefsfps93 IS 'Liste des documents dont le bénéficiaire est invité à se munir pour la fiche de prescription - CG 93';

CREATE UNIQUE INDEX documentsbenefsfps93_name_idx ON documentsbenefsfps93( name );

ALTER TABLE documentsbenefsfps93 ADD CONSTRAINT documentsbenefsfps93_autre_in_list_chk CHECK ( cakephp_validate_in_list( autre, ARRAY['0','1'] ) );

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS documentsbenefsfps93_fichesprescriptions93 CASCADE;
CREATE TABLE documentsbenefsfps93_fichesprescriptions93 (
    id						SERIAL NOT NULL PRIMARY KEY,
	documentbeneffp93_id	INTEGER NOT NULL REFERENCES documentsbenefsfps93( id ) ON UPDATE CASCADE ON DELETE CASCADE,
	ficheprescription93_id	INTEGER NOT NULL REFERENCES fichesprescriptions93( id ) ON UPDATE CASCADE ON DELETE CASCADE,
    created					TIMESTAMP WITHOUT TIME ZONE,
    modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE documentsbenefsfps93_fichesprescriptions93 IS 'Documents dont le bénéficiaire est invité à se munir pour la fiche de prescription - CG 93';

CREATE INDEX documentsbenefsfps93_fichesprescriptions93_fp93_id_idx ON documentsbenefsfps93_fichesprescriptions93( ficheprescription93_id );
CREATE INDEX documentsbenefsfps93_fichesprescriptions93_documentbeneffp93_id_idx ON documentsbenefsfps93_fichesprescriptions93( documentbeneffp93_id );
CREATE UNIQUE INDEX documentsbenefsfps93_fichesprescriptions93_fp93_id_documentbeneffp93_id_idx ON documentsbenefsfps93_fichesprescriptions93( ficheprescription93_id, documentbeneffp93_id );

--------------------------------------------------------------------------------
-- 20140221: Ajout de la date d'affectation du gestionnaire au dossier PCG (CG66)
--------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'dossierspcgs66', 'dateaffectation', 'DATE' );

-------------------------------------------------------------------------------------
-- 20140225 : Ajout d'un champ isactif pour masquer les infos du modules courriers des dossiers PCGs (CG66)
-------------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'typescourrierspcgs66', 'isactif', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'typescourrierspcgs66', 'typescourrierspcgs66_isactif_in_list_chk' );
ALTER TABLE typescourrierspcgs66 ADD CONSTRAINT typescourrierspcgs66_isactif_in_list_chk CHECK ( cakephp_validate_in_list( isactif, ARRAY['0','1'] ) );
UPDATE typescourrierspcgs66 SET isactif = '1' WHERE isactif IS NULL;
ALTER TABLE typescourrierspcgs66 ALTER COLUMN isactif SET DEFAULT '1'::VARCHAR(1);
ALTER TABLE typescourrierspcgs66 ALTER COLUMN isactif SET NOT NULL;

SELECT add_missing_table_field( 'public', 'modelestypescourrierspcgs66', 'isactif', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'modelestypescourrierspcgs66', 'modelestypescourrierspcgs66_isactif_in_list_chk' );
ALTER TABLE modelestypescourrierspcgs66 ADD CONSTRAINT modelestypescourrierspcgs66_isactif_in_list_chk CHECK ( cakephp_validate_in_list( isactif, ARRAY['0','1'] ) );
UPDATE modelestypescourrierspcgs66 SET isactif = '1' WHERE isactif IS NULL;
ALTER TABLE modelestypescourrierspcgs66 ALTER COLUMN isactif SET DEFAULT '1'::VARCHAR(1);
ALTER TABLE modelestypescourrierspcgs66 ALTER COLUMN isactif SET NOT NULL;

SELECT add_missing_table_field( 'public', 'piecesmodelestypescourrierspcgs66', 'isactif', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'piecesmodelestypescourrierspcgs66', 'piecesmodelestypescourrierspcgs66_isactif_in_list_chk' );
ALTER TABLE piecesmodelestypescourrierspcgs66 ADD CONSTRAINT piecesmodelestypescourrierspcgs66_isactif_in_list_chk CHECK ( cakephp_validate_in_list( isactif, ARRAY['0','1'] ) );
UPDATE piecesmodelestypescourrierspcgs66 SET isactif = '1' WHERE isactif IS NULL;
ALTER TABLE piecesmodelestypescourrierspcgs66 ALTER COLUMN isactif SET DEFAULT '1'::VARCHAR(1);
ALTER TABLE piecesmodelestypescourrierspcgs66 ALTER COLUMN isactif SET NOT NULL;

-------------------------------------------------------------------------------------
-- 20140225 : Ajout d'un champ pour stocker le cumul des durées des CERs
-- respectant la requête limitecumulCER présente dans le modèle Contratinsertion.php
-------------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'contratsinsertion', 'cumulduree', 'INTEGER' );


-------------------------------------------------------------------------------------
-- 20140307 : Ajout d'un état supplémentaire pour les dossiers PCGs (dossier à revoir)
-------------------------------------------------------------------------------------
-- Un dossier ne possède pas d'état PCG dossierpcg66_id = 4634 en base CG66
-- UPDATE dossierspcgs66 SET etatdossierpcg = 'transmisop' WHERE id = '4634';
SELECT alter_table_drop_constraint_if_exists( 'public', 'dossierspcgs66', 'dossierspcgs66_etatdossierpcg_in_list_chk' );
ALTER TABLE dossierspcgs66 ADD CONSTRAINT dossierspcgs66_etatdossierpcg_in_list_chk CHECK ( cakephp_validate_in_list( etatdossierpcg, ARRAY['attaffect', 'attinstr', 'instrencours', 'attavistech', 'attval', 'decisionvalid', 'decisionnonvalid', 'decisionnonvalidretouravis', 'decisionvalidretouravis', 'transmisop', 'atttransmisop', 'annule', 'attinstrattpiece', 'attinstrdocarrive','arevoir'] ) );

-------------------------------------------------------------------------------------
-- 20140408: création d'un index sur la concaténation du nom et du prénom de l'allocataire
-------------------------------------------------------------------------------------

DROP INDEX IF EXISTS personnes_nom_complet_court_idx;
CREATE INDEX personnes_nom_complet_court_idx ON personnes( ( COALESCE( nom, '' ) || ' ' || COALESCE( prenom, '' ) ) );

-------------------------------------------------------------------------------------
-- 20140625: modifications de la table instantanesdonneesfps93 suite à la mise en place des nouvelles adresses CAF
-------------------------------------------------------------------------------------

-- Racine > InfoDemandeRSA > DonneesAdministratives > Adresse > AdresseDetailleeFrance
ALTER TABLE instantanesdonneesfps93 ALTER COLUMN benef_compladr TYPE VARCHAR(38);

-- La colonne typevoie est remplacée par benef_libtypevoie
UPDATE instantanesdonneesfps93 SET benef_typevoie = NULL WHERE TRIM( BOTH ' ' FROM benef_typevoie ) = '';
SELECT add_missing_table_field( 'public', 'instantanesdonneesfps93', 'benef_libtypevoie', 'VARCHAR(30)' );
ALTER TABLE instantanesdonneesfps93 ALTER COLUMN benef_libtypevoie SET DEFAULT NULL;
ALTER TABLE instantanesdonneesfps93 ALTER COLUMN benef_typevoie SET DEFAULT NULL;

-- La colonne benef_nomvoie passe à 32 caractères
ALTER TABLE instantanesdonneesfps93 ALTER COLUMN benef_nomvoie TYPE VARCHAR(32);

-- La colonne benef_numcomrat est remplacée par benef_numcom (CHAR5)
SELECT add_missing_table_field( 'public', 'instantanesdonneesfps93', 'benef_numcom', 'CHAR(5)' );
ALTER TABLE instantanesdonneesfps93 ALTER COLUMN benef_numcom SET DEFAULT NULL;
ALTER TABLE instantanesdonneesfps93 ALTER COLUMN benef_numcomrat SET DEFAULT NULL;

-- La colonne benef_numcomptt est supprimée
ALTER TABLE instantanesdonneesfps93 ALTER COLUMN benef_numcomptt SET DEFAULT NULL;

-- La colonne benef_locaadr est remplacée par benef_nomcom (VARCHAR32)
SELECT add_missing_table_field( 'public', 'instantanesdonneesfps93', 'benef_nomcom', 'VARCHAR(32)' );
ALTER TABLE instantanesdonneesfps93 ALTER COLUMN benef_locaadr SET DEFAULT NULL;

-- FIXME: commenter ?
SELECT alter_table_drop_column_if_exists( 'public', 'instantanesdonneesfps93', 'benef_typevoie' );
SELECT alter_table_drop_column_if_exists( 'public', 'instantanesdonneesfps93', 'benef_numcomrat' );
SELECT alter_table_drop_column_if_exists( 'public', 'instantanesdonneesfps93', 'benef_numcomptt' );
SELECT alter_table_drop_column_if_exists( 'public', 'instantanesdonneesfps93', 'benef_locaadr' );

------------------------------------------------------------------------------------------
-- 20140627: Modification du foirmulaire du CUI: arnaud
------------------------------------------------------------------------------------------

-- Mise à NULL de la valeur par défaut de la position CUI
ALTER TABLE cuis ALTER COLUMN positioncui66 SET DEFAULT NULL;
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_positioncui66_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_positioncui66_in_list_chk CHECK (cakephp_validate_in_list(positioncui66::text, ARRAY['attenvoimail'::text, 'dossierrecu'::text, 'dossiereligible'::text, 'attpieces'::text, 'attavismne'::text, 'attaviselu'::text, 'attavisreferent'::text, 'attdecision'::text, 'encours'::text, 'annule'::text, 'decisionsanssuite'::text, 'fincontrat'::text, 'attrenouv'::text, 'perime'::text, 'nonvalide'::text, 'valid'::text, 'validnotifie'::text, 'nonvalidnotifie'::text, 'rupture'::text]));
-- ALTER TABLE cuis ALTER COLUMN positioncui66 SET DEFAULT 'attenvoimail'::VARCHAR(10);
-- ALTER TABLE cuis ALTER COLUMN positioncui66 SET NOT NULL;

SELECT add_missing_table_field( 'public', 'cuis', 'sendmailemployeur', 'VARCHAR(1)' );
ALTER TABLE cuis ALTER COLUMN sendmailemployeur SET DEFAULT '0'::VARCHAR(1);
UPDATE cuis SET sendmailemployeur = '0' WHERE sendmailemployeur IS NULL;
ALTER TABLE cuis ALTER COLUMN sendmailemployeur SET NOT NULL;
SELECT add_missing_table_field( 'public', 'cuis', 'retourmail', 'DATE' );
SELECT add_missing_table_field( 'public', 'cuis', 'commentairemail', 'TEXT' );


-- 20140627!: ajout d'une table pour stocker es pièces liées aux mails employeur
DROP TABLE IF EXISTS piecesmailscuis66 CASCADE;
CREATE TABLE piecesmailscuis66(
    id                          SERIAL NOT NULL PRIMARY KEY,
    name                        VARCHAR(250) NOT NULL,
    isactif                     VARCHAR(1) NOT NULL DEFAULT '1',
	----------------------------------------------------------------------------
    created                     TIMESTAMP WITHOUT TIME ZONE,
    modified                    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE piecesmailscuis66 IS 'Table des différentes pièces liées aux mails employeur des CUIs (CG66)';

DROP INDEX IF EXISTS piecesmailscuis66_name_idx;
CREATE INDEX piecesmailscuis66_name_idx ON piecesmailscuis66( name );

DROP INDEX IF EXISTS piecesmailscuis66_isactif_idx;
CREATE INDEX piecesmailscuis66_isactif_idx ON piecesmailscuis66( isactif );

SELECT alter_table_drop_constraint_if_exists( 'public', 'piecesmailscuis66', 'piecesmailscuis66_isactif_in_list_chk' );
ALTER TABLE piecesmailscuis66 ADD CONSTRAINT piecesmailscuis66_isactif_in_list_chk CHECK ( cakephp_validate_in_list( isactif, ARRAY['0', '1'] ) );



----------------------------------------------------------------------------------------
-- 20130927 : Création d'une table de liaison entre
--            les pièces liées aux mails employeur et les CUIs
----------------------------------------------------------------------------------------
DROP TABLE IF EXISTS cuis_piecesmailscuis66 CASCADE;
CREATE TABLE cuis_piecesmailscuis66(
    id                              SERIAL NOT NULL PRIMARY KEY,
    cui_id      INTEGER NOT NULL REFERENCES cuis(id) ON DELETE CASCADE ON UPDATE CASCADE,
    piecemailcui66_id       INTEGER NOT NULL REFERENCES piecesmailscuis66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	----------------------------------------------------------------------------
    created                     TIMESTAMP WITHOUT TIME ZONE,
    modified                    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE cuis_piecesmailscuis66 IS 'Table de liaison entre les pièces liées aux mails employeur et les CUIs (CG66)';

DROP INDEX IF EXISTS cuis_piecesmailscuis66_cui_id_idx;
CREATE INDEX cuis_piecesmailscuis66_cui_id_idx ON cuis_piecesmailscuis66( cui_id );

DROP INDEX IF EXISTS cuis_piecesmailscuis66_piecemailcui66_id_idx;
CREATE INDEX cuis_piecesmailscuis66_piecemailcui66_id_idx ON cuis_piecesmailscuis66( piecemailcui66_id );

DROP INDEX IF EXISTS cuis_piecesmailscuis66_cui_id_piecemailcui66_id_idx;
CREATE UNIQUE INDEX cuis_piecesmailscuis66_cui_id_piecemailcui66_id_idx ON cuis_piecesmailscuis66(cui_id,piecemailcui66_id);

SELECT add_missing_table_field( 'public', 'partenaires', 'nomresponsable', 'VARCHAR(100)' );
SELECT add_missing_table_field( 'public', 'cuis', 'postepropose', 'VARCHAR(250)' );

SELECT add_missing_table_field( 'public', 'cuis', 'isperennisation', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_isperennisation_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_isperennisation_in_list_chk CHECK ( cakephp_validate_in_list( isperennisation, ARRAY['0', '1'] ) );

SELECT add_missing_table_field( 'public', 'cuis', 'subventionaccordee', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_subventionaccordee_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_subventionaccordee_in_list_chk CHECK ( cakephp_validate_in_list( subventionaccordee, ARRAY['0', '1'] ) );

SELECT add_missing_table_field( 'public', 'cuis', 'dateentreedispositif', 'VARCHAR(10)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_dateentreedispositif_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_dateentreedispositif_in_list_chk CHECK ( cakephp_validate_in_list( dateentreedispositif, ARRAY['6', '1', '2', '3', '4', '5','0'] ) );


SELECT add_missing_table_field( 'public', 'cuis', 'dossierrecu', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_dossierrecu_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_dossierrecu_in_list_chk CHECK ( cakephp_validate_in_list( dossierrecu, ARRAY['0', '1'] ) );
SELECT add_missing_table_field( 'public', 'cuis', 'datedossierrecu', 'DATE' );


SELECT add_missing_table_field( 'public', 'cuis', 'dossiereligible', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_dossiereligible_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_dossiereligible_in_list_chk CHECK ( cakephp_validate_in_list( dossiereligible, ARRAY['0', '1'] ) );
SELECT add_missing_table_field( 'public', 'cuis', 'datedossiereligible', 'DATE' );

SELECT add_missing_table_field( 'public', 'cuis', 'dossiercomplet', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_dossiercomplet_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_dossiercomplet_in_list_chk CHECK ( cakephp_validate_in_list( dossiercomplet, ARRAY['0', '1'] ) );
SELECT add_missing_table_field( 'public', 'cuis', 'datedossiercomplet', 'DATE' );


-- 20140630: Table permettatn de sasir des mails types pour envoi à l'employeur CUI
DROP TABLE IF EXISTS textsmailscuis66 CASCADE;
CREATE TABLE textsmailscuis66 (
    id			SERIAL NOT NULL PRIMARY KEY,
	name		VARCHAR(250) NOT NULL,
	sujet       VARCHAR(150) NOT NULL,
	contenu     TEXT NOT NULL,
	actif       VARCHAR(1) NOT NULL DEFAULT '1',
    created		TIMESTAMP WITHOUT TIME ZONE,
    modified	TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE textsmailscuis66 IS 'Table permettant de stocker les mails types envoyés aux employeurs liés au CUI - CG66';

CREATE UNIQUE INDEX textsmailscuis66_name_idx ON textsmailscuis66( name );

ALTER TABLE textsmailscuis66 ADD CONSTRAINT textsmailscuis66_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY['0','1'] ) );

SELECT add_missing_table_field( 'public', 'cuis', 'textmailcui66_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'cuis', 'cuis_textmailcui66_id_fkey', 'textsmailscuis66', 'textmailcui66_id', false );
SELECT add_missing_table_field( 'public', 'cuis', 'dateenvoimail', 'DATE' );

-------------------------------------------------------------------------------------
-- 20140701: FIXME: ajout des intitulés tableau1b4new et tableau1b4new dans la contrainte tableauxsuivispdvs93_name_in_list_chk
-------------------------------------------------------------------------------------

SELECT alter_table_drop_constraint_if_exists( 'public', 'tableauxsuivispdvs93', 'tableauxsuivispdvs93_name_in_list_chk' );
ALTER TABLE tableauxsuivispdvs93 ADD CONSTRAINT tableauxsuivispdvs93_name_in_list_chk CHECK ( cakephp_validate_in_list( name, ARRAY['tableaud1', 'tableaud2', 'tableau1b3', 'tableau1b4', 'tableau1b4new', 'tableau1b5', 'tableau1b5new', 'tableau1b6'] ) );


----------------------------------------------------------------------------------------
-- 20140710 : Liaison entre la décison du CUI et les modèles de mail type
----------------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'decisionscuis66', 'textmailcui66_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'decisionscuis66', 'decisionscuis66_textmailcui66_id_fkey', 'textsmailscuis66', 'textmailcui66_id', false );
SELECT add_missing_table_field( 'public', 'decisionscuis66', 'dateenvoimail', 'DATE' );

--------------------------------------------------------------------------------
-- 201407731: Correction des types d'orientations ne reflétant pas le stype de
-- la structure référente des orientations.
-- Après vérification dans les dump, il n'y a pas de souci entre les référents et
-- leurs structures. Concernant les erreurs entre les structures et leurs types
-- d'orientations, on a: 7@cg58_20140724, 1818@cg66_20140627, 133@cg93_20140710.
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION public.correction_orientsstructs_typeorient_id_structurereferente_id() RETURNS VOID AS
$$
	DECLARE
		v_row record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
					orientsstructs.id,
					typesorientsstructuresreferentes.id AS typeorient_id
				FROM orientsstructs
					INNER JOIN typesorients AS typesorientsorientsstructs ON ( typesorientsorientsstructs.id = orientsstructs.typeorient_id )
					INNER JOIN structuresreferentes ON ( structuresreferentes.id = orientsstructs.structurereferente_id )
					INNER JOIN typesorients AS typesorientsstructuresreferentes ON ( typesorientsstructuresreferentes.id = structuresreferentes.typeorient_id )
				WHERE
					orientsstructs.statut_orient = 'Orienté'
					AND orientsstructs.typeorient_id <> structuresreferentes.typeorient_id
				ORDER BY date_valid ASC
		LOOP
			-- 1. Mise à jour de l'information dans la table orientsstructs
			v_query := 'UPDATE orientsstructs SET typeorient_id = ' || v_row.typeorient_id || ' WHERE id = ' || v_row.id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;

			-- 2. Suppression du PDF lié stocké dans la table pdfs
			v_query := 'DELETE FROM pdfs WHERE modele = ''Orientstruct'' AND fk_value = ' || v_row.id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.correction_orientsstructs_typeorient_id_structurereferente_id();
DROP FUNCTION public.correction_orientsstructs_typeorient_id_structurereferente_id();

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
