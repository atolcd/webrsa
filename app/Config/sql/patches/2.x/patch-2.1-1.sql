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

-- *****************************************************************************
DROP TABLE IF EXISTS dossierspcgs66 CASCADE;
DROP TABLE IF EXISTS decisionsdossierspcgs66 CASCADE;
DROP TABLE IF EXISTS personnespcgs66 CASCADE;
DROP TABLE IF EXISTS personnespcgs66_situationspdos CASCADE;
DROP TABLE IF EXISTS personnespcgs66_statutspdos CASCADE;
DROP TABLE IF EXISTS traitementspcgs66 CASCADE;
DROP TABLE IF EXISTS decisionstraitementspcgs66 CASCADE;
DROP TABLE IF EXISTS courrierspdos_traitementspcgs66 CASCADE;
DROP TABLE IF EXISTS composfoyerspcgs66 CASCADE;
DROP TABLE IF EXISTS decisionspcgs66 CASCADE;
DROP TABLE IF EXISTS questionspcgs66 CASCADE;
DROP TABLE IF EXISTS codesromesecteursdsps66 CASCADE;
DROP TABLE IF EXISTS codesromemetiersdsps66 CASCADE;
DROP TABLE IF EXISTS decisionsdossierspcgs66_decisionstraitementspcgs66 CASCADE;
DROP TABLE IF EXISTS objetscontratsprecedents CASCADE;
DROP TABLE IF EXISTS sitescovs58_zonesgeographiques CASCADE;
DROP TABLE IF EXISTS nonorientationsproseps66 CASCADE;
DROP TABLE IF EXISTS decisionsnonorientationsproseps66 CASCADE;
DROP TABLE IF EXISTS typesrsapcgs66 CASCADE;
DROP TABLE IF EXISTS decisionsdossierspcgs66_typesrsapcgs66 CASCADE;

-- *****************************************************************************
DROP TYPE IF EXISTS TYPE_ETATDOSSIERPCG CASCADE;
DROP TYPE IF EXISTS TYPE_DEFAUTINSERTIONPCG66 CASCADE;
DROP TYPE IF EXISTS TYPE_PHASEPCG66 CASCADE;
DROP TYPE IF EXISTS TYPE_CERCMU CASCADE;
DROP TYPE IF EXISTS TYPE_CERCMUC CASCADE;
DROP TYPE IF EXISTS TYPE_OBJETCERPREC CASCADE;
DROP TYPE IF EXISTS TYPE_TYPETRAITEMENT CASCADE;
DROP TYPE IF EXISTS TYPE_ETATOP CASCADE;
DROP TYPE IF EXISTS TYPE_REGULARISATIONEP58 CASCADE;
DROP TYPE IF EXISTS TYPE_DECISIONNONORIENTATIONPROEP66 CASCADE;

-- *****************************************************************************
-- Nouvelle version de la fonction public.add_missing_constraint qui prend en compte
--		la longueur maximale de 63 caractères pour le nom de la contrainte.
-- Il est à présent possible de passer un 6ème paramètre à false pour avoir SET NULL
--		au lieu de CASCADE lors d'une suppression.
-- ***************************************************************************************

CREATE OR REPLACE FUNCTION public.add_missing_constraint (text, text, text, text, text, bool) RETURNS bool AS
$$
	DECLARE
		p_namespace 		alias for $1;
		p_table     		alias for $2;
		p_constraintname	alias for $3;
		p_foreigntable		alias for $4;
		p_foreignkeyname	alias for $5;
		p_deletecascade		alias for $6;
		v_row       		record;
		v_query     		text;
	BEGIN
		SELECT 1 INTO v_row
		FROM information_schema.table_constraints tc
			LEFT JOIN information_schema.key_column_usage kcu ON (
				tc.constraint_catalog = kcu.constraint_catalog
				AND tc.constraint_schema = kcu.constraint_schema
				AND tc.constraint_name = kcu.constraint_name
			)
			LEFT JOIN information_schema.referential_constraints rc ON (
				tc.constraint_catalog = rc.constraint_catalog
				AND tc.constraint_schema = rc.constraint_schema
				AND tc.constraint_name = rc.constraint_name
			)
			LEFT JOIN information_schema.constraint_column_usage ccu ON (
				rc.unique_constraint_catalog = ccu.constraint_catalog
				AND rc.unique_constraint_schema = ccu.constraint_schema
				AND rc.unique_constraint_name = ccu.constraint_name
			)
		WHERE
			tc.table_schema = p_namespace
			AND tc.table_name = p_table
			AND tc.constraint_type = 'FOREIGN KEY'
			AND tc.constraint_name = substring( p_constraintname from 1 for 63 ) -- INFO: les noms sont juste tronqués, pas de chiffre à la fin -> ça ne devrait pas poser de problème
			AND kcu.column_name = p_foreignkeyname
			AND ccu.table_name = p_foreigntable
			AND ccu.column_name = 'id';

		IF NOT FOUND THEN
			RAISE NOTICE 'Upgrade table %.% - add constraint %', p_namespace, p_table, p_constraintname;
			v_query := 'alter table ' || p_namespace || '.' || p_table || ' add constraint ';
			v_query := v_query || p_constraintname || ' FOREIGN KEY (' || p_foreignkeyname || ') REFERENCES ' || p_foreigntable || '(id)';

			IF p_deletecascade THEN
				v_query := v_query || ' ON DELETE CASCADE ON UPDATE CASCADE;';
			ELSE
				v_query := v_query || ' ON DELETE SET NULL ON UPDATE CASCADE;';
			END IF;

			EXECUTE v_query;
			RETURN 't';
		ELSE
			RETURN 'f';
		END IF;
	END;
$$
LANGUAGE plpgsql;

COMMENT ON FUNCTION public.add_missing_constraint (text, text, text, text, text, bool) IS 'Add a constraint to a table if it is missing';

-- ***************************************************************************************

CREATE OR REPLACE FUNCTION public.add_missing_constraint (text, text, text, text, text ) RETURNS bool AS
$$
	BEGIN
		RETURN public.add_missing_constraint( $1, $2, $3, $4, $5, true );
	END
$$
LANGUAGE plpgsql;

SELECT add_missing_constraint ('public', 'decisionssaisinesbilansparcourseps66', 'decisionssaisinesbilansparcourseps66_typeorientprincipale_id_fkey', 'typesorients', 'typeorientprincipale_id');

-- *****************************************************************************
-- Nouvelle version de la fonction public.alter_enumtype qui prend en compte la
-- valeur NULL comme valeur par défaut.
-- *****************************************************************************
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
								( CASE WHEN column_default IS NULL THEN NULL ELSE regexp_replace( column_default, ''^''''(.*)''''::.*$'', E''\\\\1'', ''g'' ) END ) AS column_default
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

-- *****************************************************************************
-- 20110704 : Mise à jour des tables suite à l'évolution des flux CAF
-- Instruction et Bénéficiaire
-- *****************************************************************************
SELECT add_missing_table_field ('public', 'infosagricoles', 'topressevaagri', 'TYPE_BOOLEANNUMBER');
ALTER TABLE infosagricoles ALTER COLUMN topressevaagri SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE infosagricoles SET topressevaagri = '0'::TYPE_BOOLEANNUMBER WHERE topressevaagri IS NULL;
ALTER TABLE infosagricoles ALTER COLUMN topressevaagri SET NOT NULL;


-- *****************************************************************************
-- 20110705 : création des tables pour la nouvelles gestions des pdo du cg66
-- *****************************************************************************

CREATE TYPE TYPE_ETATDOSSIERPCG AS ENUM ('attaffect', 'attinstr', 'instrencours', 'attavistech', 'attval', 'dossiertraite', 'attpj' );

CREATE TABLE dossierspcgs66 (
	id      				SERIAL NOT NULL PRIMARY KEY,
	foyer_id				INTEGER NOT NULL REFERENCES foyers(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typepdo_id				INTEGER NOT NULL REFERENCES typespdos(id) ON DELETE CASCADE ON UPDATE CASCADE,
	datereceptionpdo		DATE NOT NULL,
	etatdossierpcg			TYPE_ETATDOSSIERPCG DEFAULT NULL,
	originepdo_id			INTEGER NOT NULL REFERENCES originespdos(id) ON DELETE CASCADE ON UPDATE CASCADE,
	orgpayeur				TYPE_ORGPAYEUR DEFAULT NULL,
	serviceinstructeur_id	INTEGER DEFAULT NULL REFERENCES servicesinstructeurs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	haspiecejointe			TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0'::TYPE_BOOLEANNUMBER,
	user_id					INTEGER DEFAULT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
	iscomplet				TYPE_ISCOMPLET DEFAULT NULL,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE dossierspcgs66 IS 'Table de gestion des dossiers PCGs au niveau du foyer';
CREATE INDEX dossierspcgs66_foyer_id_idx ON dossierspcgs66 (foyer_id);
CREATE INDEX dossierspcgs66_typepdo_id_idx ON dossierspcgs66 (typepdo_id);
CREATE INDEX dossierspcgs66_originepdo_id_idx ON dossierspcgs66 (originepdo_id);
CREATE INDEX dossierspcgs66_serviceinstructeur_id_idx ON dossierspcgs66 (serviceinstructeur_id);
CREATE INDEX dossierspcgs66_user_id_idx ON dossierspcgs66 (user_id);

CREATE TABLE decisionsdossierspcgs66 (
	id							SERIAL NOT NULL PRIMARY KEY,
	dossierpcg66_id				INTEGER NOT NULL REFERENCES dossierspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	commentaire					TEXT DEFAULT NULL,
	etatdossierpcg				TYPE_ETATDOSSIERPCG DEFAULT NULL,
	avistechnique				TYPE_NO DEFAULT NULL,
	commentaireavistechnique	TEXT DEFAULT NULL,
	dateavistechnique			DATE DEFAULT NULL,
	validationproposition		TYPE_NO DEFAULT NULL,
	commentairevalidation		TEXT DEFAULT NULL,
	datevalidation				DATE DEFAULT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE decisionsdossierspcgs66 IS 'Table de gestion des decisions du dossier PCGs au niveau du foyer';
CREATE INDEX decisionsdossierspcgs66_dossierpcg66_id_idx ON decisionsdossierspcgs66 (dossierpcg66_id);

CREATE TABLE personnespcgs66 (
	id						SERIAL NOT NULL PRIMARY KEY,
	dossierpcg66_id			INTEGER NOT NULL REFERENCES dossierspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	personne_id				INTEGER NOT NULL REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	categoriegeneral		VARCHAR(3) DEFAULT NULL,
	categoriedetail			VARCHAR(3) DEFAULT NULL,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE personnespcgs66 IS 'Table de gestion des dossiers PCGs au niveau des personnes';
CREATE INDEX personnespcgs66_dossierpcg66_id_idx ON personnespcgs66 (dossierpcg66_id);
CREATE INDEX personnespcgs66_personne_id_idx ON personnespcgs66 (personne_id);

CREATE TABLE personnespcgs66_situationspdos (
	id						SERIAL NOT NULL PRIMARY KEY,
	personnepcg66_id		INTEGER NOT NULL REFERENCES personnespcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	situationpdo_id			INTEGER NOT NULL REFERENCES situationspdos(id) ON DELETE CASCADE ON UPDATE CASCADE
);
COMMENT ON TABLE personnespcgs66_situationspdos IS 'Table de jointure entre les dossiers PCGs des personnes et leurs situations';
CREATE INDEX personnespcgs66_situationspdos_personnepcg66_id_idx ON personnespcgs66_situationspdos (personnepcg66_id);
CREATE INDEX personnespcgs66_situationspdos_situationpdo_id_idx ON personnespcgs66_situationspdos (situationpdo_id);

CREATE TABLE personnespcgs66_statutspdos (
	id						SERIAL NOT NULL PRIMARY KEY,
	personnepcg66_id		INTEGER NOT NULL REFERENCES personnespcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	statutpdo_id			INTEGER NOT NULL REFERENCES statutspdos(id) ON DELETE CASCADE ON UPDATE CASCADE
);
COMMENT ON TABLE personnespcgs66_statutspdos IS 'Table de jointure entre les dossiers PCGs des personnes et leurs statuts';
CREATE INDEX personnespcgs66_statutspdos_personnepcg66_id_idx ON personnespcgs66_statutspdos (personnepcg66_id);
CREATE INDEX personnespcgs66_statutspdos_statutpdo_id_idx ON personnespcgs66_statutspdos (statutpdo_id);

CREATE TABLE traitementspcgs66 (
	id							SERIAL NOT NULL PRIMARY KEY,
	personnepcg66_id			INTEGER NOT NULL REFERENCES personnespcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	descriptionpdo_id			INTEGER NOT NULL REFERENCES descriptionspdos(id) ON DELETE CASCADE ON UPDATE CASCADE,
	datereception				DATE DEFAULT NULL,
	datedepart					DATE DEFAULT NULL,
	hascourrier					TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0'::TYPE_BOOLEANNUMBER,
	hasrevenu					TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0'::TYPE_BOOLEANNUMBER,
	haspiecejointe				TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0'::TYPE_BOOLEANNUMBER,
	hasficheanalyse				TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0'::TYPE_BOOLEANNUMBER,
	regime						TYPE_REGIMEFICHECALCUL DEFAULT NULL,
	saisonnier					TYPE_BOOLEANNUMBER DEFAULT NULL,
	nrmrcs						VARCHAR(20) DEFAULT NULL,
	dtdebutactivite				DATE DEFAULT NULL,
	raisonsocial				VARCHAR(100) DEFAULT NULL,
	dtdebutperiode				DATE DEFAULT NULL,
	datefinperiode				DATE DEFAULT NULL,
	dtdebutprisecompte			DATE DEFAULT NULL,
	datefinprisecompte			DATE DEFAULT NULL,
	dtecheance					DATE DEFAULT NULL,
	forfait						FLOAT DEFAULT NULL,
	mtaidesub					FLOAT DEFAULT NULL,
	chaffvnt					FLOAT DEFAULT NULL,
	chaffsrv					FLOAT DEFAULT NULL,
	benefoudef					FLOAT DEFAULT NULL,
	ammortissements				FLOAT DEFAULT NULL,
	salaireexploitant			FLOAT DEFAULT NULL,
	provisionsnonded			FLOAT DEFAULT NULL,
	moinsvaluescession			FLOAT DEFAULT NULL,
	autrecorrection				FLOAT DEFAULT NULL,
	nbmoisactivite				INTEGER DEFAULT NULL,
	mnttotalpriscompte			FLOAT DEFAULT NULL,
	revenus						FLOAT DEFAULT NULL,
	benefpriscompte				FLOAT DEFAULT NULL,
	aidesubvreint				TYPE_AIDESUBVREINT DEFAULT NULL,
	dureeecheance				TYPE_DUREE DEFAULT NULL,
	dureefinprisecompte			TYPE_DUREE DEFAULT NULL,
	dateecheance				DATE DEFAULT NULL,
	daterevision				DATE DEFAULT NULL,
	ficheanalyse				TEXT DEFAULT NULL,
	clos						TYPE_NO DEFAULT 'N'::TYPE_NO,
	annule						TYPE_NO DEFAULT 'N'::TYPE_NO,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE traitementspcgs66 IS 'Table des traitemant des dossiers PCGs des personnes';
CREATE INDEX traitementspcgs66_personnepcg66_id_idx ON traitementspcgs66 (personnepcg66_id);
CREATE INDEX traitementspcgs66_descriptionpdo_id_idx ON traitementspcgs66 (descriptionpdo_id);

CREATE TABLE decisionstraitementspcgs66 (
	id								SERIAL NOT NULL PRIMARY KEY,
	traitementpcg66_id				INTEGER NOT NULL REFERENCES traitementspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	valide							TYPE_NO NOT NULL,
	commentaire						TEXT NOT NULL,
	actif							TYPE_BOOLEANNUMBER DEFAULT '1'::TYPE_BOOLEANNUMBER NOT NULL,
	created							TIMESTAMP WITHOUT TIME ZONE,
	modified						TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE decisionstraitementspcgs66 IS 'Table des décisions des dossiers PCGs des traitements';
CREATE INDEX decisionstraitementspcgs66_traitementpcg66_id_idx ON traitementspcgs66 (id);

CREATE TABLE courrierspdos_traitementspcgs66 (
    id              		SERIAL NOT NULL PRIMARY KEY,
    courrierpdo_id       	INTEGER NOT NULL REFERENCES courrierspdos(id) ON DELETE CASCADE ON UPDATE CASCADE,
	traitementpcg66_id		INTEGER NOT NULL REFERENCES traitementspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE
);
COMMENT ON TABLE courrierspdos_traitementspcgs66 IS 'Table de liaison entre les courriers et le traitement d''une PDO (CG66)';
CREATE INDEX courrierspdos_traitementspcgs66_courrierpdo_id_idx ON courrierspdos_traitementspcgs66 (courrierpdo_id);
CREATE INDEX courrierspdos_traitementspcgs66_traitementpcg66_id_idx ON courrierspdos_traitementspcgs66 (traitementpcg66_id);

-- *****************************************************************************
-- 20110707 -- ajout du champ traitementpcg66 dans la table des EPs des PDOs 66
-- *****************************************************************************
SELECT add_missing_table_field ('public', 'saisinespdoseps66', 'traitementpcg66_id', 'INTEGER');
TRUNCATE saisinespdoseps66;
DELETE FROM dossierseps WHERE themeep = 'saisinespdoseps66';
SELECT add_missing_constraint ('public', 'saisinespdoseps66', 'saisinespdoseps66_traitementpcg66_id_fkey', 'traitementspcgs66', 'traitementpcg66_id');
ALTER TABLE saisinespdoseps66 ALTER COLUMN traitementpcg66_id SET NOT NULL;
SELECT alter_table_drop_column_if_exists( 'public', 'saisinespdoseps66', 'traitementpdo_id' );

-- *****************************************************************************
-- 20110803 -- ajout des tables de gestion des décisionpcgs66
-- *****************************************************************************
CREATE TABLE composfoyerspcgs66 (
	id			SERIAL NOT NULL PRIMARY KEY,
	name		VARCHAR(100) NOT NULL
);
COMMENT ON TABLE composfoyerspcgs66 IS 'Liste des compositions de foyer possible pour les PCGs66';
CREATE UNIQUE INDEX composfoyerspcgs66_name_idx ON composfoyerspcgs66( name );

CREATE TABLE decisionspcgs66 (
	id				SERIAL NOT NULL PRIMARY KEY,
	name			VARCHAR(50) NOT NULL,
	nbmoisecheance	FLOAT NOT NULL DEFAULT 0,
	courriernotif	VARCHAR(50) DEFAULT NULL
);
COMMENT ON TABLE decisionspcgs66 IS 'Liste des décisions pour les PCGs66';
CREATE UNIQUE INDEX decisionspcgs66_name_idx ON decisionspcgs66( name );

CREATE TYPE TYPE_DEFAUTINSERTIONPCG66 AS ENUM ( 'nc_cg', 'nc_pe', 'nr_cg', 'nr_pe', 'nc_no' );
CREATE TYPE TYPE_PHASEPCG66 AS ENUM ( '1', '2', '3' );

CREATE TABLE questionspcgs66 (
	id							SERIAL NOT NULL PRIMARY KEY,
	defautinsertion				TYPE_DEFAUTINSERTIONPCG66 NOT NULL,
	compofoyerpcg66_id			INTEGER NOT NULL REFERENCES composfoyerspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	recidive					TYPE_NO NOT NULL,
	phase						TYPE_PHASEPCG66 NOT NULL,
	descriptionpdo_id			INTEGER NOT NULL REFERENCES descriptionspdos(id) ON DELETE CASCADE ON UPDATE CASCADE
);
COMMENT ON TABLE questionspcgs66 IS 'Table de jointure donnant la ou les décisions possible pour la liste de question plus la composition du foyer';
CREATE INDEX questionspcgs66_compofoyerpcg66_id_idx ON questionspcgs66 (compofoyerpcg66_id);
CREATE INDEX questionspcgs66_descriptionpdo_id_idx ON questionspcgs66 (descriptionpdo_id);

-- *****************************************************************************
-- 20110805 -- Nouveau champ pour la gestion d'une décision pcg après passage
-- en EPL Audition
-- *****************************************************************************
SELECT add_missing_table_field ('public', 'dossierspcgs66', 'bilanparcours66_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'dossierspcgs66', 'dossierspcgs66_bilanparcours66_id_fkey', 'bilansparcours66', 'bilanparcours66_id');
ALTER TABLE dossierspcgs66 ALTER COLUMN bilanparcours66_id SET DEFAULT NULL;

SELECT add_missing_table_field ('public', 'originespdos', 'originepcg', 'TYPE_NO');
ALTER TABLE originespdos ALTER COLUMN originepcg SET DEFAULT 'N'::TYPE_NO;
UPDATE originespdos SET originepcg = 'N' WHERE originepcg IS NULL;
ALTER TABLE originespdos ALTER COLUMN originepcg SET NOT NULL;

SELECT add_missing_table_field ('public', 'typespdos', 'originepcg', 'TYPE_NO');
ALTER TABLE typespdos ALTER COLUMN originepcg SET DEFAULT 'N'::TYPE_NO;
UPDATE typespdos SET originepcg = 'N' WHERE originepcg IS NULL;
ALTER TABLE typespdos ALTER COLUMN originepcg SET NOT NULL;

SELECT add_missing_table_field ('public', 'situationspdos', 'nc', 'TYPE_BOOLEANNUMBER');
SELECT add_missing_table_field ('public', 'situationspdos', 'nr', 'TYPE_BOOLEANNUMBER');
ALTER TABLE situationspdos ALTER COLUMN nc SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
ALTER TABLE situationspdos ALTER COLUMN nr SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE situationspdos SET nc = '0' WHERE nc IS NULL;
UPDATE situationspdos SET nr = '0' WHERE nr IS NULL;
ALTER TABLE situationspdos ALTER COLUMN nc SET NOT NULL;
ALTER TABLE situationspdos ALTER COLUMN nr SET NOT NULL;

-- *****************************************************************************
-- 20110809 -- Modification du traitementpcg66 pour pouvoir sélectionner
-- les décisions à afficher dans les décisionspersonnespcgs66
-- *****************************************************************************
SELECT add_missing_table_field ('public', 'traitementspcgs66', 'eplaudition', 'TYPE_BOOLEANNUMBER');
ALTER TABLE traitementspcgs66 ALTER COLUMN eplaudition SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE traitementspcgs66 SET eplaudition = '0' WHERE eplaudition IS NULL;
ALTER TABLE traitementspcgs66 ALTER COLUMN eplaudition SET NOT NULL;

SELECT add_missing_table_field ('public', 'traitementspcgs66', 'compofoyerpcg66_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'traitementspcgs66', 'traitementspcgs66_compofoyerpcg66_id_fkey', 'composfoyerspcgs66', 'compofoyerpcg66_id');
ALTER TABLE traitementspcgs66 ALTER COLUMN compofoyerpcg66_id SET DEFAULT NULL;

SELECT add_missing_table_field ('public', 'traitementspcgs66', 'recidive', 'TYPE_NO');
ALTER TABLE traitementspcgs66 ALTER COLUMN recidive SET DEFAULT NULL;

-- *****************************************************************************
-- 20110824 -- Ajout de deux tables de paramètrages pour les dsps du cg66
-- plus champs pour stocker les nouvelles valeurs
-- *****************************************************************************
CREATE TABLE codesromesecteursdsps66 (
	id							SERIAL NOT NULL PRIMARY KEY,
	code						VARCHAR(10) NOT NULL,
	name						VARCHAR(100) NOT NULL
);
COMMENT ON TABLE codesromesecteursdsps66 IS 'Liste des secteurs par code rome pour les dsps du cg66';
DROP INDEX IF EXISTS codesromesecteursdsps66_code_name_idx;
CREATE UNIQUE INDEX codesromesecteursdsps66_code_name_idx ON codesromesecteursdsps66 (code, name);

CREATE TABLE codesromemetiersdsps66 (
	id							SERIAL NOT NULL PRIMARY KEY,
	code						VARCHAR(10) NOT NULL,
	name						VARCHAR(100) NOT NULL,
	coderomesecteurdsp66_id		INTEGER NOT NULL REFERENCES codesromesecteursdsps66(id) ON DELETE CASCADE ON UPDATE CASCADE
);
COMMENT ON TABLE codesromemetiersdsps66 IS 'Liste des métiers par code rome pour les dsps du cg66';
CREATE UNIQUE INDEX codesromemetiersdsps66_code_name_idx ON codesromemetiersdsps66 (code, name);

-- *****************************************************************************

SELECT add_missing_table_field ('public', 'dsps', 'libderact66_metier_id', 'INTEGER');
UPDATE dsps SET libderact66_metier_id = NULL;
SELECT add_missing_constraint ('public', 'dsps', 'dsps_libderact66_metier_id_fkey', 'codesromemetiersdsps66', 'libderact66_metier_id');
ALTER TABLE dsps ALTER COLUMN libderact66_metier_id SET DEFAULT NULL;
SELECT add_missing_table_field ('public', 'dsps', 'libsecactderact66_secteur_id', 'INTEGER');
UPDATE dsps SET libsecactderact66_secteur_id = NULL;
SELECT add_missing_constraint ('public', 'dsps', 'dsps_libsecactderact66_secteur_id_fkey', 'codesromesecteursdsps66', 'libsecactderact66_secteur_id');
ALTER TABLE dsps ALTER COLUMN libsecactderact66_secteur_id SET DEFAULT NULL;

SELECT add_missing_table_field ('public', 'dsps', 'libactdomi66_metier_id', 'INTEGER');
UPDATE dsps SET libactdomi66_metier_id = NULL;
SELECT add_missing_constraint ('public', 'dsps', 'dsps_libactdomi66_metier_id_fkey', 'codesromemetiersdsps66', 'libactdomi66_metier_id');
ALTER TABLE dsps ALTER COLUMN libactdomi66_metier_id SET DEFAULT NULL;
SELECT add_missing_table_field ('public', 'dsps', 'libsecactdomi66_secteur_id', 'INTEGER');
UPDATE dsps SET libsecactdomi66_secteur_id = NULL;
SELECT add_missing_constraint ('public', 'dsps', 'dsps_libsecactdomi66_secteur_id_fkey', 'codesromesecteursdsps66', 'libsecactdomi66_secteur_id');
ALTER TABLE dsps ALTER COLUMN libsecactdomi66_secteur_id SET DEFAULT NULL;

SELECT add_missing_table_field ('public', 'dsps', 'libemploirech66_metier_id', 'INTEGER');
UPDATE dsps SET libemploirech66_metier_id = NULL;
SELECT add_missing_constraint ('public', 'dsps', 'dsps_libemploirech66_metier_id_fkey', 'codesromemetiersdsps66', 'libemploirech66_metier_id');
ALTER TABLE dsps ALTER COLUMN libemploirech66_metier_id SET DEFAULT NULL;
SELECT add_missing_table_field ('public', 'dsps', 'libsecactrech66_secteur_id', 'INTEGER');
UPDATE dsps SET libsecactrech66_secteur_id = NULL;
SELECT add_missing_constraint ('public', 'dsps', 'dsps_libsecactrech66_secteur_id_fkey', 'codesromesecteursdsps66', 'libsecactrech66_secteur_id');
ALTER TABLE dsps ALTER COLUMN libsecactrech66_secteur_id SET DEFAULT NULL;

-- *****************************************************************************

SELECT add_missing_table_field ('public', 'dsps_revs', 'libderact66_metier_id', 'INTEGER');
UPDATE dsps_revs SET libderact66_metier_id = NULL;
SELECT add_missing_constraint ('public', 'dsps_revs', 'dsps_revs_libderact66_metier_id_fkey', 'codesromemetiersdsps66', 'libderact66_metier_id');
ALTER TABLE dsps_revs ALTER COLUMN libderact66_metier_id SET DEFAULT NULL;
SELECT add_missing_table_field ('public', 'dsps_revs', 'libsecactderact66_secteur_id', 'INTEGER');
UPDATE dsps_revs SET libsecactderact66_secteur_id = NULL;
SELECT add_missing_constraint ('public', 'dsps_revs', 'dsps_revs_libsecactderact66_secteur_id_fkey', 'codesromesecteursdsps66', 'libsecactderact66_secteur_id');
ALTER TABLE dsps_revs ALTER COLUMN libsecactderact66_secteur_id SET DEFAULT NULL;

SELECT add_missing_table_field ('public', 'dsps_revs', 'libactdomi66_metier_id', 'INTEGER');
UPDATE dsps_revs SET libactdomi66_metier_id = NULL;
SELECT add_missing_constraint ('public', 'dsps_revs', 'dsps_revs_libactdomi66_metier_id_fkey', 'codesromemetiersdsps66', 'libactdomi66_metier_id');
ALTER TABLE dsps_revs ALTER COLUMN libactdomi66_metier_id SET DEFAULT NULL;
SELECT add_missing_table_field ('public', 'dsps_revs', 'libsecactdomi66_secteur_id', 'INTEGER');
UPDATE dsps_revs SET libsecactdomi66_secteur_id = NULL;
SELECT add_missing_constraint ('public', 'dsps_revs', 'dsps_revs_libsecactdomi66_secteur_id_fkey', 'codesromesecteursdsps66', 'libsecactdomi66_secteur_id');
ALTER TABLE dsps_revs ALTER COLUMN libsecactdomi66_secteur_id SET DEFAULT NULL;

SELECT add_missing_table_field ('public', 'dsps_revs', 'libemploirech66_metier_id', 'INTEGER');
UPDATE dsps_revs SET libemploirech66_metier_id = NULL;
SELECT add_missing_constraint ('public', 'dsps_revs', 'dsps_revs_libemploirech66_metier_id_fkey', 'codesromemetiersdsps66', 'libemploirech66_metier_id');
ALTER TABLE dsps_revs ALTER COLUMN libemploirech66_metier_id SET DEFAULT NULL;
SELECT add_missing_table_field ('public', 'dsps_revs', 'libsecactrech66_secteur_id', 'INTEGER');
UPDATE dsps_revs SET libsecactrech66_secteur_id = NULL;
SELECT add_missing_constraint ('public', 'dsps_revs', 'dsps_revs_libsecactrech66_secteur_id_fkey', 'codesromesecteursdsps66', 'libsecactrech66_secteur_id');
ALTER TABLE dsps_revs ALTER COLUMN libsecactrech66_secteur_id SET DEFAULT NULL;

-- *****************************************************************************
-- 20110830 -- Ajout d'un champ pour faire en sorte que les traitements
-- deviennent des décisions
-- *****************************************************************************
SELECT add_missing_table_field ('public', 'descriptionspdos', 'decisionpcg', 'TYPE_NO');
UPDATE descriptionspdos SET decisionpcg = 'N' WHERE decisionpcg IS NULL;
ALTER TABLE descriptionspdos ALTER COLUMN decisionpcg SET DEFAULT 'N'::TYPE_NO;
ALTER TABLE descriptionspdos ALTER COLUMN decisionpcg SET NOT NULL;

SELECT public.alter_enumtype ( 'TYPE_DUREE', ARRAY['0', '0.5', '1', '1.5', '2', '2.5', '3', '3.5', '4', '4.5', '5', '5.5', '6', '6.5', '7', '7.5', '8', '8.5', '9', '9.5', '10', '10.5', '11', '11.5', '12'] );
SELECT add_missing_table_field ('public', 'descriptionspdos', 'nbmoisecheance', 'TYPE_DUREE');
UPDATE descriptionspdos SET nbmoisecheance = '0'::TYPE_DUREE WHERE nbmoisecheance IS NULL;
ALTER TABLE descriptionspdos ALTER COLUMN nbmoisecheance SET DEFAULT '0'::TYPE_DUREE;
ALTER TABLE descriptionspdos ALTER COLUMN nbmoisecheance SET NOT NULL;

-- *****************************************************************************
-- 20110902 -- Table de jointure entre les decisionstraitementspcg66 et
-- les decisionsdossierspcgs66 de façon à garder l'historique complet dans
-- les vues des decisionsdossierspcgs66
-- *****************************************************************************
CREATE TABLE decisionsdossierspcgs66_decisionstraitementspcgs66 (
	id								SERIAL NOT NULL PRIMARY KEY,
	decisiondossierpcg66_id			INTEGER NOT NULL REFERENCES decisionsdossierspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	decisiontraitementpcg66_id		INTEGER NOT NULL REFERENCES decisionstraitementspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE
);
COMMENT ON TABLE decisionsdossierspcgs66_decisionstraitementspcgs66 IS 'Table d''historique liant les décisions foyers aux décision traitements qui lui sont liées';
CREATE INDEX decisionsdossierspcgs66_decisionstraitementspcgs66_decisiondoss ON decisionsdossierspcgs66_decisionstraitementspcgs66 (decisiondossierpcg66_id);
CREATE INDEX decisionsdossierspcgs66_decisionstraitementspcgs66_decisiontrai ON decisionsdossierspcgs66_decisionstraitementspcgs66 (decisiontraitementpcg66_id);
CREATE UNIQUE INDEX decisionsdossierspcgs66_decisionstraitementspcgs66_decisiondos1 ON decisionsdossierspcgs66_decisionstraitementspcgs66 (decisiondossierpcg66_id, decisiontraitementpcg66_id);


-- *****************************************************************************
-- 20111014 -- Ajout de champs supplémentaires pour les traitements PCGs 66
--		Ajout d'un enum pour remlpacer les 4 fieldsets et bouton radio
--		Ajout d'une clé étrangère entre la table traitementspcgs66 et
--		les tables personnespcgs66 et situationspdos
-- *****************************************************************************
CREATE TYPE TYPE_TYPETRAITEMENT AS ENUM ('courrier', 'revenu', 'analyse', 'aucun' );
SELECT add_missing_table_field ('public', 'traitementspcgs66', 'typetraitement', 'TYPE_TYPETRAITEMENT');

SELECT add_missing_table_field ('public', 'traitementspcgs66', 'personnepcg66_situationpdo_id', 'INTEGER');
UPDATE traitementspcgs66 SET personnepcg66_situationpdo_id = NULL;
SELECT add_missing_constraint ('public', 'traitementspcgs66', 'traitementspcgs66_personnepcg66_situationpdo_id_fkey', 'personnespcgs66_situationspdos', 'personnepcg66_situationpdo_id');
TRUNCATE TABLE traitementspcgs66 CASCADE;
ALTER TABLE traitementspcgs66 ALTER COLUMN personnepcg66_situationpdo_id SET NOT NULL;










DROP TABLE IF EXISTS decisionspersonnespcgs66 CASCADE;
CREATE TABLE decisionspersonnespcgs66 (
	id								SERIAL NOT NULL PRIMARY KEY,
	personnepcg66_situationpdo_id	INTEGER NOT NULL REFERENCES personnespcgs66_situationspdos(id) ON DELETE CASCADE ON UPDATE CASCADE,
	datepropositions				DATE NOT NULL,
	decisionpdo_id					INTEGER NOT NULL REFERENCES decisionspdos(id) ON DELETE CASCADE ON UPDATE CASCADE,
	commentaire						TEXT DEFAULT NULL,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE decisionspersonnespcgs66 IS 'Table des décisions des dossiers PCGs des personnes pour chacunes de ses situations';
CREATE INDEX decisionspersonnespcgs66_personnepcg66_situationpdo_id_idx ON decisionspersonnespcgs66 (personnepcg66_situationpdo_id);
CREATE INDEX decisionspersonnespcgs66_decisionpdo_id_idx ON decisionspersonnespcgs66 (decisionpdo_id);

DROP TABLE IF EXISTS decisionsdossierspcgs66_decisionspersonnespcgs66 CASCADE;

CREATE TABLE decisionsdossierspcgs66_decisionspersonnespcgs66 (
	id								SERIAL NOT NULL PRIMARY KEY,
	decisiondossierpcg66_id			INTEGER NOT NULL REFERENCES decisionsdossierspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	decisionpersonnepcg66_id		INTEGER NOT NULL REFERENCES decisionspersonnespcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE
);
COMMENT ON TABLE decisionsdossierspcgs66_decisionspersonnespcgs66 IS 'Table d''historique liant les décisions foyers aux décision traitements qui lui sont liées';
CREATE INDEX decisionsdossierspcgs66_decisionspersonnespcgs66_decisiondoss ON decisionsdossierspcgs66_decisionspersonnespcgs66 (decisiondossierpcg66_id);
CREATE INDEX decisionsdossierspcgs66_decisionspersonnespcgs66_decisiontrai ON decisionsdossierspcgs66_decisionspersonnespcgs66 (decisionpersonnepcg66_id);
CREATE UNIQUE INDEX decisionsdossierspcgs66_decisionspersonnespcgs66_decisiondos1 ON decisionsdossierspcgs66_decisionspersonnespcgs66 (decisiondossierpcg66_id, decisionpersonnepcg66_id);


-- ***************************************************************************************
-- 20111014 -- Ajout de la notion de transmission à un organisme payeur pour les PDOs 66
--		Ajout d'un enum pour l'état de transmission
--		Ajout d'une date de transmission
-- ***************************************************************************************
CREATE TYPE TYPE_ETATOP AS ENUM ( 'atransmettre', 'transmis' );
SELECT add_missing_table_field ('public', 'decisionsdossierspcgs66', 'etatop', 'TYPE_ETATOP');
SELECT add_missing_table_field ('public', 'decisionsdossierspcgs66', 'datetransmissionop', 'DATE');


SELECT public.alter_enumtype ( 'TYPE_ETATDOSSIERPCG', ARRAY['attaffect', 'attinstr', 'instrencours', 'attavistech', 'attval', 'dossiertraite', 'attpj', 'transmisop', 'atttransmisop' ] );


SELECT add_missing_table_field ('public', 'decisionsdossierspcgs66', 'decisionpdo_id', 'INTEGER');
UPDATE decisionsdossierspcgs66 SET decisionpdo_id = NULL;
SELECT add_missing_constraint ('public', 'decisionsdossierspcgs66', 'decisionsdossierspcgs66_decisionpdo_id_fkey', 'decisionspdos', 'decisionpdo_id');
SELECT add_missing_table_field ('public', 'decisionsdossierspcgs66', 'datepropositiontechnicien', 'DATE');
SELECT add_missing_table_field ('public', 'decisionsdossierspcgs66', 'commentairetechnicien', 'TEXT');

-- ***************************************************************************************
-- 20111020: Nettoyage du code: suppresson des tables tempcessations, tempinscriptions,
--           tempradiations, infospoleemploi (tâche 755)
--           Reprise en partie du patch-2.0-rc15 avant la suppression des tables.
-- ***************************************************************************************

CREATE OR REPLACE FUNCTION public.nettoyage_fluxpe() RETURNS bool AS
$$
	DECLARE
		v_row       		record;
	BEGIN
		SELECT COUNT(DISTINCT(table_name)) AS count INTO v_row
			FROM INFORMATION_SCHEMA.tables
			WHERE
				table_schema = 'public'
				AND table_name IN ( 'tempcessations', 'tempinscriptions', 'tempradiations', 'infospoleemploi' );

		RAISE NOTICE  '%', v_row.count;

		IF v_row.count = 4 THEN
			-- Mise à jour des anciennes tables concernant les inscriptions/cessations/radiations Pôle Emploi
			UPDATE tempcessations SET nom = public.noaccents_upper(nom) WHERE nom !~ '^([A-Z]|\-| |'')+$';
			UPDATE tempcessations SET prenom = public.noaccents_upper(nom) WHERE prenom !~ '^([A-Z]|\-| |'')+$';
			UPDATE tempinscriptions SET nom = public.noaccents_upper(nom) WHERE nom !~ '^([A-Z]|\-| |'')+$';
			UPDATE tempinscriptions SET prenom = public.noaccents_upper(nom) WHERE prenom !~ '^([A-Z]|\-| |'')+$';
			UPDATE tempradiations SET nom = public.noaccents_upper(nom) WHERE nom !~ '^([A-Z]|\-| |'')+$';
			UPDATE tempradiations SET prenom = public.noaccents_upper(nom) WHERE prenom !~ '^([A-Z]|\-| |'')+$';

			UPDATE tempinscriptions SET nir15 = CASE WHEN ( nir_correct( nir || calcul_cle_nir( nir ) ) ) THEN nir || calcul_cle_nir( nir ) ELSE NULL END;
			UPDATE tempcessations SET nir15 = CASE WHEN ( nir_correct( nir || calcul_cle_nir( nir ) ) ) THEN nir || calcul_cle_nir( nir ) ELSE NULL END;
			UPDATE tempradiations SET nir15 = CASE WHEN ( nir_correct( nir || calcul_cle_nir( nir ) ) ) THEN nir || calcul_cle_nir( nir ) ELSE NULL END;

			-- Ajout des données ne se trouvant pas encore dans les nouvelles tables
			INSERT INTO informationspe ( nir, nom, prenom, dtnai )
			SELECT
					CASE WHEN ( nir_correct( personnes.nir ) ) THEN personnes.nir
						ELSE NULL
					END AS nir,
					personnes.nom,
					personnes.prenom,
					personnes.dtnai
				FROM infospoleemploi
					INNER JOIN personnes ON (
						infospoleemploi.personne_id = personnes.id
					)
				WHERE
					NOT EXISTS(
						SELECT *
							FROM informationspe
							WHERE
								(
									( nir_correct( personnes.nir ) AND informationspe.nir = personnes.nir )
									OR informationspe.nir IS NULL
								)
								AND informationspe.nom = personnes.nom
								AND informationspe.prenom = personnes.prenom
								AND informationspe.dtnai = personnes.dtnai
					)
				GROUP BY
					personnes.nir,
					personnes.nom,
					personnes.prenom,
					personnes.dtnai
				ORDER BY
					personnes.nir,
					personnes.nom,
					personnes.prenom,
					personnes.dtnai;

			-- A partir des personnes pas encore trouvées (tables tempXXX)
			INSERT INTO informationspe ( nir, nom, prenom, dtnai )
				SELECT
						nir15 AS nir,
						temp.nom,
						temp.prenom,
						temp.dtnai
					FROM (
						SELECT *
							FROM(
								SELECT
										nir15,
										nom,
										prenom,
										dtnai
									FROM tempcessations
								UNION
								SELECT
										nir15,
										nom,
										prenom,
										dtnai
									FROM tempradiations
								UNION
								SELECT
										nir15,
										nom,
										prenom,
										dtnai
									FROM tempinscriptions
							) AS tmptables
					) AS temp
					WHERE (
						SELECT
								COUNT(*)
							FROM informationspe
							WHERE (
									(
										informationspe.nir IS NOT NULL
										AND temp.nir15 IS NOT NULL
										AND informationspe.nir = temp.nir15
									)
									OR (
										informationspe.nom = temp.nom
										AND informationspe.prenom = temp.prenom
										AND informationspe.dtnai = temp.dtnai
									)
								)
					) = 0
					GROUP BY
						temp.nir15,
						temp.nom,
						temp.prenom,
						temp.dtnai;

			RETURN 't';
		ELSE
			RETURN 'f';
		END IF;
	END;
$$
LANGUAGE plpgsql;

SELECT public.nettoyage_fluxpe();
DROP FUNCTION public.nettoyage_fluxpe();

DROP TABLE IF EXISTS tempcessations;
DROP TABLE IF EXISTS tempinscriptions;
DROP TABLE IF EXISTS tempradiations;
DROP TABLE IF EXISTS infospoleemploi;



-- ***************************************************************************************
-- 20111025 -- Ajout de la notion d'actif/inactif pour 
--				les structures référentes,
--				les référents, 
--				les actions des fiches de candidatures
--		Par défaut la valeur est active
-- ***************************************************************************************
SELECT add_missing_table_field ('public', 'structuresreferentes', 'actif', 'TYPE_NO');
ALTER TABLE structuresreferentes ALTER COLUMN actif SET DEFAULT 'O';
UPDATE structuresreferentes SET actif = 'O' WHERE actif IS NULL;

SELECT add_missing_table_field ('public', 'referents', 'actif', 'TYPE_NO');
ALTER TABLE referents ALTER COLUMN actif SET DEFAULT 'O';
UPDATE referents SET actif = 'O' WHERE actif IS NULL;

SELECT add_missing_table_field ('public', 'actionscandidats', 'actif', 'TYPE_NO');
ALTER TABLE actionscandidats ALTER COLUMN actif SET DEFAULT 'O';
UPDATE actionscandidats SET actif = 'O' WHERE actif IS NULL;


-- ***********************************************************************************************************
-- 20111123 -- Ajout des champs des personnes orientatns un allocataire pour le CG58
-- ***********************************************************************************************************
SELECT add_missing_table_field ('public', 'proposorientationscovs58', 'structureorientante_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'proposorientationscovs58', 'proposorientationscovs58_structureorientante_id_fkey', 'structuresreferentes', 'structureorientante_id');
ALTER TABLE proposorientationscovs58 ALTER COLUMN structureorientante_id SET DEFAULT NULL;

SELECT add_missing_table_field ('public', 'proposorientationscovs58', 'referentorientant_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'proposorientationscovs58', 'proposorientationscovs58_referentorientant_id_fkey', 'referents', 'referentorientant_id');
ALTER TABLE proposorientationscovs58 ALTER COLUMN referentorientant_id SET DEFAULT NULL;

SELECT add_missing_table_field ('public', 'decisionsproposcontratsinsertioncovs58', 'dd_ci', 'DATE');
SELECT add_missing_table_field ('public', 'decisionsproposcontratsinsertioncovs58', 'duree_engag', 'INTEGER');
SELECT add_missing_table_field ('public', 'decisionsproposcontratsinsertioncovs58', 'df_ci', 'DATE');



-- ***********************************************************************************************************
-- 20111129 -- Modification de l'ENUM pour l'état du dossier PCG CG66
-- ***********************************************************************************************************
SELECT public.alter_enumtype ( 'TYPE_ETATDOSSIERPCG', ARRAY['attaffect', 'attinstr', 'instrencours', 'attavistech', 'attval', 'dossiertraite', 'decisionvalid', 'decisionnonvalid', 'attpj', 'transmisop', 'atttransmisop' ] );





-- ***********************************************************************************************************
-- 20111207 -- Mise à jour de à Non de la décision des APREs où la décision est vide
-- ***********************************************************************************************************
UPDATE apres SET isdecision='N' WHERE isdecision IS NULL;

-- ***********************************************************************************************************
-- 20111207 -- Ajout d'un champ dans les actions liées à la fiche de candidature 
--				afin de savoir quel document ODT sera généré
-- ***********************************************************************************************************
SELECT add_missing_table_field ('public', 'actionscandidats', 'modele_document', 'VARCHAR(50)');
ALTER TABLE actionscandidats ALTER COLUMN modele_document SET DEFAULT 'fichecandidature';
UPDATE actionscandidats SET modele_document='fichecandidature' WHERE modele_document IS NULL;
ALTER TABLE actionscandidats ALTER COLUMN modele_document SET NOT NULL;

-- ***********************************************************************************************************
-- 20111207 -- Ajout d'un champ dans contratsinsertion pour saisir le motif d'annulation du CER
-- ***********************************************************************************************************
SELECT add_missing_table_field ( 'public', 'contratsinsertion', 'motifannulation', 'TEXT' );

-- ***********************************************************************************************************
-- 20111207 -- Ajout d'un champ dans les decisionsdossierspcgs66 pour sasisir le motif d'annulation du CER
-- ***********************************************************************************************************
SELECT add_missing_table_field ( 'public', 'decisionsdossierspcgs66', 'retouravistechnique', 'TYPE_BOOLEANNUMBER' );
ALTER TABLE decisionsdossierspcgs66 ALTER COLUMN retouravistechnique SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE decisionsdossierspcgs66 SET retouravistechnique = '0'::TYPE_BOOLEANNUMBER WHERE retouravistechnique IS NULL;

-- ***********************************************************************************************************
-- 20111207 -- Ajout d'un nouvel état pour le dossier PCG : décision non validée + retour avis technique
-- ***********************************************************************************************************
SELECT public.alter_enumtype ( 'TYPE_ETATDOSSIERPCG', ARRAY['attaffect', 'attinstr', 'instrencours', 'attavistech', 'attval', 'dossiertraite', 'decisionvalid', 'decisionnonvalid', 'decisionnonvalidretouravis', 'attpj', 'transmisop', 'atttransmisop' ] );


-- ***********************************************************************************************************
-- 20111209 -- Ajout des zones géographiques pour gérer les COVs 58
-- ***********************************************************************************************************
CREATE TABLE sitescovs58_zonesgeographiques (
	id								SERIAL NOT NULL PRIMARY KEY,
	sitecov58_id					INTEGER NOT NULL REFERENCES sitescovs58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	zonegeographique_id				INTEGER NOT NULL REFERENCES zonesgeographiques(id) ON DELETE CASCADE ON UPDATE CASCADE
);
COMMENT ON TABLE sitescovs58_zonesgeographiques IS 'Table de liaison entre les sites des COV et les zones géographiques (CG58)';
CREATE INDEX sitescovs58_zonesgeographiques_sitecov58_id_idx ON sitescovs58_zonesgeographiques (sitecov58_id);
CREATE INDEX sitescovs58_zonesgeographiques_zonegeographique_id_idx ON sitescovs58_zonesgeographiques (zonegeographique_id);
CREATE UNIQUE INDEX sitescovs58_zonesgeographiques_sitecov58_id_zonegeographique_id_idx ON sitescovs58_zonesgeographiques (sitecov58_id, zonegeographique_id);


-- ***********************************************************************************************************
-- 20111209 -- Ajout de champs dans les decisions de sanctions afin d'émettre 2 décisions de sanction dans 1 EP
-- ***********************************************************************************************************
SELECT add_missing_table_field ( 'public', 'decisionssanctionsrendezvouseps58', 'decision2', '	TYPE_DECISIONSANCTIONEP58' );
SELECT add_missing_table_field ( 'public', 'decisionssanctionsrendezvouseps58', 'autrelistesanctionep58_id', 'INTEGER' );
SELECT add_missing_constraint ('public', 'decisionssanctionsrendezvouseps58', 'decisionssanctionsrendezvouseps58_autrelistesanctionep58_id_fkey', 'listesanctionseps58', 'autrelistesanctionep58_id');
ALTER TABLE decisionssanctionsrendezvouseps58 ALTER COLUMN autrelistesanctionep58_id SET DEFAULT NULL;


SELECT add_missing_table_field ( 'public', 'decisionssanctionseps58', 'decision2', '	TYPE_DECISIONSANCTIONEP58' );
SELECT add_missing_table_field ( 'public', 'decisionssanctionseps58', 'autrelistesanctionep58_id', 'INTEGER' );
SELECT add_missing_constraint ('public', 'decisionssanctionseps58', 'decisionssanctionseps58_autrelistesanctionep58_id_fkey', 'listesanctionseps58', 'autrelistesanctionep58_id');
ALTER TABLE decisionssanctionseps58 ALTER COLUMN autrelistesanctionep58_id SET DEFAULT NULL;
-- ***********************************************************************************************************
-- 20111209 -- Ajout d'un champ supplémentaire en cas de rédularisation pour les sanctions EP 58
-- ***********************************************************************************************************
CREATE TYPE TYPE_REGULARISATIONEP58 AS ENUM ('finsanction2', 'annulation1', 'annulation2' );
SELECT add_missing_table_field ( 'public', 'decisionssanctionsrendezvouseps58', 'regularisation', '	TYPE_REGULARISATIONEP58' );
SELECT add_missing_table_field ( 'public', 'decisionssanctionseps58', 'regularisation', '	TYPE_REGULARISATIONEP58' );



-- ***********************************************************************************************************
-- 20111212 -- Ajout du champ userid sur les tables liées aux dossiers PCGs 66
-- ***********************************************************************************************************

SELECT add_missing_table_field ('public', 'decisionsdossierspcgs66', 'user_id', 'INTEGER');
SELECT add_missing_constraint( 'public', 'decisionsdossierspcgs66', 'decisionsdossierspcgs66_user_id_fk', 'users', 'user_id' );
-- ALTER TABLE decisionsdossierspcgs66 ALTER COLUMN user_id SET NOT NULL;
 
DROP INDEX IF EXISTS decisionsdossierspcgs66_user_id_isx;
CREATE INDEX decisionsdossierspcgs66_user_id_isx ON decisionsdossierspcgs66(user_id);

-- -----------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'personnespcgs66', 'user_id', 'INTEGER');
SELECT add_missing_constraint( 'public', 'personnespcgs66', 'personnespcgs66_user_id_fk', 'users', 'user_id' );
-- ALTER TABLE personnespcgs66 ALTER COLUMN user_id SET NOT NULL;
 
DROP INDEX IF EXISTS personnespcgs66_user_id_isx;
CREATE INDEX personnespcgs66_user_id_isx ON personnespcgs66(user_id);

-- -----------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'traitementspcgs66', 'user_id', 'INTEGER');
SELECT add_missing_constraint( 'public', 'traitementspcgs66', 'traitementspcgs66_user_id_fk', 'users', 'user_id' );
-- ALTER TABLE traitementspcgs66 ALTER COLUMN user_id SET NOT NULL;
 
DROP INDEX IF EXISTS traitementspcgs66_user_id_isx;
CREATE INDEX traitementspcgs66_user_id_isx ON traitementspcgs66(user_id);


-- UPDATE originespdos SET originepcg='O' WHERE libelle='CAF';
-- UPDATE typespdos SET originepcg='O' WHERE libelle='Position Mission rSa';



-- ***********************************************************************************************************
-- 20111214 -- Ajout d'une table pour les demandes de maintien en social au CG66
-- ***********************************************************************************************************
CREATE TABLE nonorientationsproseps66 (
	id      				SERIAL NOT NULL PRIMARY KEY,
	dossierep_id			INTEGER NOT NULL REFERENCES dossierseps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	orientstruct_id			INTEGER DEFAULT NULL REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	user_id					INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE nonorientationsproseps66 IS 'Saisines d''EPs créées lors d''une non orientation du social vers le professionel dans une délai de 6, 12 ou 24 mois (CG66)';

DROP INDEX IF EXISTS nonorientationsproseps66_dossierep_id_idx;
CREATE INDEX nonorientationsproseps66_dossierep_id_idx ON nonorientationsproseps66 (dossierep_id);

DROP INDEX IF EXISTS nonorientationsproseps66_orientstruct_id_idx;
CREATE INDEX nonorientationsproseps66_orientstruct_id_idx ON nonorientationsproseps66 (orientstruct_id);

DROP INDEX IF EXISTS nonorientationsproseps66_user_id_idx;
CREATE INDEX nonorientationsproseps66_user_id_idx ON nonorientationsproseps66 (user_id);

-- Mise à jour du type themeep suite à l'ajout de la nouvelle thématique nonorientationproep66
SELECT public.alter_enumtype ( 'TYPE_THEMEEP', ARRAY['reorientationseps93','saisinesbilansparcourseps66','saisinespdoseps66','nonrespectssanctionseps93','defautsinsertionseps66','nonorientationsproseps58','nonorientationsproseps93','regressionsorientationseps58','sanctionseps58','signalementseps93','sanctionsrendezvouseps58','contratscomplexeseps93','nonorientationsproseps66' ] );

CREATE TYPE TYPE_DECISIONNONORIENTATIONPROEP66 AS ENUM ( 'reorientation', 'maintienref' );
COMMENT ON TYPE TYPE_DECISIONNONORIENTATIONPROEP66 IS 'Type de décision pour la non orientation professionnelle dans les délais (CG66)';

CREATE TABLE decisionsnonorientationsproseps66 (
	id      					SERIAL NOT NULL PRIMARY KEY,
	etape						TYPE_ETAPEDECISIONEP NOT NULL,
	decision					TYPE_DECISIONNONORIENTATIONPROEP66 DEFAULT NULL,
	typeorient_id				INTEGER DEFAULT NULL REFERENCES typesorients(id) ON UPDATE CASCADE ON DELETE SET NULL,
	structurereferente_id		INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON UPDATE CASCADE ON DELETE SET NULL,
	commentaire					TEXT DEFAULT NULL,
	raisonnonpassage 			TEXT DEFAULT NULL,
	user_id						INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
	passagecommissionep_id		INTEGER NOT NULL REFERENCES passagescommissionseps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE decisionsnonorientationsproseps66 IS 'Décisions de la saisine d''EP lors d''une non orientation du social vers le professionel dans une délai de 6, 12 ou 24 mois (CG66)';


DROP INDEX IF EXISTS decisionsnonorientationsproseps66_typeorient_id_idx;
CREATE INDEX decisionsnonorientationsproseps66_typeorient_id_idx ON decisionsnonorientationsproseps66 (typeorient_id);

DROP INDEX IF EXISTS decisionsnonorientationsproseps66_structurereferente_id_idx;
CREATE INDEX decisionsnonorientationsproseps66_structurereferente_id_idx ON decisionsnonorientationsproseps66(structurereferente_id);

DROP INDEX IF EXISTS decisionsnonorientationsproseps66_passagecommissionep_id_idx;
CREATE INDEX decisionsnonorientationsproseps66_passagecommissionep_id_idx ON decisionsnonorientationsproseps66(passagecommissionep_id);

DROP INDEX IF EXISTS decisionsnonorientationsproseps66_user_id_idx;
CREATE INDEX decisionsnonorientationsproseps66_user_id_idx ON decisionsnonorientationsproseps66(user_id);


-- ***********************************************************************************************************
-- 20111214 -- Ajout du champ permettant de lier les pièces à une action 
--				dans les paramétrages de la fiche de candidature 66
-- ***********************************************************************************************************
SELECT add_missing_table_field ('public', 'actionscandidats', 'haspiecejointe', 'type_booleannumber');
ALTER TABLE actionscandidats ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE actionscandidats SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE actionscandidats ALTER COLUMN haspiecejointe SET NOT NULL;

-- ***********************************************************************************************************
-- 20111214 -- Mise par défaut du champ isdecision des APREs à N
-- ***********************************************************************************************************
ALTER TABLE apres ALTER COLUMN isdecision SET DEFAULT 'N'::TYPE_NO;
UPDATE apres SET isdecision = 'N'::TYPE_NO WHERE isdecision IS NULL;
ALTER TABLE apres ALTER COLUMN isdecision SET NOT NULL;

-- ***********************************************************************************************************
-- 20111219 -- Ajout d'un étét traité pour l'état du dossier apre
-- ***********************************************************************************************************
SELECT public.alter_enumtype ( 'TYPE_ETATDOSSIERAPRE', ARRAY['COM', 'INC', 'VAL', 'TRA' ] );
SELECT add_missing_table_field ('public', 'apres', 'datenotifapre', 'DATE');

-- ***********************************************************************************************************
-- 20111220 -- Ajout d'un champ  permettant de savoir si l'APRE est traitée par la cellule ou non
-- ***********************************************************************************************************
SELECT add_missing_table_field ('public', 'apres', 'istraite', 'TYPE_BOOLEANNUMBER');
ALTER TABLE apres ALTER COLUMN istraite SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE apres SET istraite = '0'::TYPE_BOOLEANNUMBER WHERE istraite IS NULL;
ALTER TABLE apres ALTER COLUMN istraite SET NOT NULL;

-- ***********************************************************************************************************
-- 20111226 -- Ajout de contraintes de clés étrangères lorsqu'elles manquent et qu'un champ se termine par _id
-- ***********************************************************************************************************

SELECT add_missing_constraint ( 'public', 'apres_comitesapres', 'apres_comitesapres_comite_pcd_id_fkey', 'comitesapres', 'comite_pcd_id' );
SELECT add_missing_constraint ( 'public', 'aros_acos', 'aros_acos_aro_id_fkey', 'aros', 'aro_id' );
SELECT add_missing_constraint ( 'public', 'aros_acos', 'aros_acos_aco_id_fkey', 'acos', 'aco_id' );
SELECT add_missing_constraint ( 'public', 'dossiers', 'dossiers_detaildroitrsa_id_fkey', 'detailsdroitsrsa', 'detaildroitrsa_id' );
SELECT add_missing_constraint ( 'public', 'dossiers', 'dossiers_avispcgdroitrsa_id_fkey', 'avispcgdroitsrsa', 'avispcgdroitrsa_id' );
SELECT add_missing_constraint ( 'public', 'dsps_revs', 'dsps_revs_dsp_id_fkey', 'dsps', 'dsp_id' );
SELECT add_missing_constraint ( 'public', 'jetonsfonctions', 'jetonsfonctions_user_id_fkey', 'users', 'user_id' );
SELECT add_missing_constraint ( 'public', 'traitementspdos', 'traitementspdos_personne_id_fkey', 'personnes', 'personne_id' );

-- ***********************************************************************************************************
-- 20120102 -- Nettoyage de la base de données
-- ***********************************************************************************************************

-- Suppression de tables qui ne sont plus utilisées
DROP TABLE IF EXISTS bilanparcours CASCADE;
DROP TABLE IF EXISTS contactspartenaires_partenaires CASCADE;
DROP TABLE IF EXISTS decisionsparcours CASCADE;
DROP TABLE IF EXISTS precosreorients CASCADE;




-- ***********************************************************************************************************
-- 20120104 -- Création d'ne séquence pour la génération automatique des N° de demande RSA
-- ***********************************************************************************************************
DROP SEQUENCE IF EXISTS dossiers_numdemrsatemp_seq;
CREATE SEQUENCE dossiers_numdemrsatemp_seq START 1;


-- ***********************************************************************************************************
-- 20120105 -- Création d'ENUM pour les champs que l'on va modifier dans la table dossiers
-- ***********************************************************************************************************
CREATE TYPE TYPE_STATUTDEMRSA AS ENUM ('N', 'C', 'A', 'M', 'S' );
ALTER TABLE dossiers ALTER COLUMN statudemrsa TYPE TYPE_STATUTDEMRSA USING CAST(statudemrsa AS TYPE_STATUTDEMRSA);

CREATE TYPE TYPE_FONORGCEDMUT AS ENUM ('CAF', 'MSA', 'OPF');
UPDATE dossiers SET fonorgcedmut = NULL WHERE fonorgcedmut NOT IN ('CAF', 'MSA', 'OPF');
ALTER TABLE dossiers ALTER COLUMN fonorgcedmut TYPE TYPE_FONORGCEDMUT USING CAST(fonorgcedmut AS TYPE_FONORGCEDMUT);

UPDATE dossiers SET fonorgprenmut = NULL WHERE fonorgprenmut NOT IN ('CAF', 'MSA', 'OPF');
ALTER TABLE dossiers ALTER COLUMN fonorgprenmut TYPE TYPE_FONORGCEDMUT USING CAST(fonorgprenmut AS TYPE_FONORGCEDMUT);

SELECT add_missing_table_field ('public', 'permanences', 'actif', 'TYPE_NO');
ALTER TABLE permanences ALTER COLUMN actif SET DEFAULT 'O';
UPDATE permanences SET actif = 'O' WHERE actif IS NULL;

-- ***********************************************************************************************************
-- 20120109 -- Nouvelle fonction de vérification du NIR sur (au moins) 13 caractères
-- ***********************************************************************************************************
CREATE OR REPLACE FUNCTION public.nir_correct13( TEXT ) RETURNS BOOLEAN AS
$body$
	DECLARE
		p_nir text;
	BEGIN
		p_nir:=$1;

		RETURN (
			CHAR_LENGTH( TRIM( BOTH ' ' FROM p_nir ) ) >= 13
			AND (
				cakephp_validate_ssn( SUBSTRING( p_nir FROM 1 FOR 13 ) || calcul_cle_nir( SUBSTRING( p_nir FROM 1 FOR 13 ) ), null, 'fr' )
			)
		);
	END;
$body$
LANGUAGE 'plpgsql';

COMMENT ON FUNCTION public.nir_correct13( TEXT ) IS
	'Vérification du format du NIR sur 13 caractères (la clé est recalculée dans tous les cas) grâce à la fonction public.cakephp_validate_ssn';

-- -----------------------------------------------------------------------------
-- Correction et dédoublonnage des informationspe
-- -----------------------------------------------------------------------------

-- Nettoyage des espaces blancs en début et fin des colonnes nir, nom, prenom
UPDATE informationspe SET
	nir = regexp_replace( regexp_replace( nir, E'[ \t\n\r]*$', '', 'g'), E'^[ \t\n\r]*', '', 'g'),
	nom = regexp_replace( regexp_replace( nom, E'[ \t\n\r]*$', '', 'g'), E'^[ \t\n\r]*', '', 'g'),
	prenom = regexp_replace( regexp_replace( prenom, E'[ \t\n\r]*$', '', 'g'), E'^[ \t\n\r]*', '', 'g');

-- Mise à jour des NIRs sur 15 caractères, ou NULL
UPDATE informationspe SET nir = NULL WHERE NOT nir_correct13( nir );
UPDATE informationspe SET nir = SUBSTRING( nir FROM 1 FOR 13 ) || calcul_cle_nir( SUBSTRING( nir FROM 1 FOR 13 ) ) WHERE NOT nir_correct( nir );

-- Nettoyage des doublons de la table informationspe
CREATE OR REPLACE FUNCTION public.nettoyage_informationspe() RETURNS VOID AS
$$
	DECLARE
		v_row   record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
						i1.id AS keep_id, i2.id AS delete_id
				FROM
					informationspe AS i1,
					informationspe AS i2
				WHERE
					i1.id < i2.id
					AND (
						(
							( TRIM( BOTH ' ' FROM i1.nom ) ) = ( TRIM( BOTH ' ' FROM i2.nom ) )
							AND ( TRIM( BOTH ' ' FROM i1.prenom ) ) = ( TRIM( BOTH ' ' FROM i2.prenom ) )
						)
						OR SUBSTRING( i1.nir FROM 1 FOR 13 ) = SUBSTRING( i2.nir FROM 1 FOR 13 )
					)
					AND i1.dtnai = i2.dtnai
		LOOP
			-- Mise à jour dans la table historiqueetatspe
			v_query := 'UPDATE historiqueetatspe SET informationpe_id = ' || v_row.keep_id || ' WHERE informationpe_id = ' || v_row.delete_id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;

			-- Suppression dans la table informationspe
			v_query := 'DELETE FROM informationspe WHERE id = ' || v_row.delete_id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.nettoyage_informationspe();
DROP FUNCTION public.nettoyage_informationspe();


-- ***********************************************************************************************************
-- 20120113 -- Ajout d'un champ "vu avis technique" afin de conserver l'information comme 
--				quoi l'avis technique a bien pris en compte l'information du décideur
-- ***********************************************************************************************************
SELECT add_missing_table_field ( 'public', 'decisionsdossierspcgs66', 'vuavistechnique', 'TYPE_BOOLEANNUMBER' );
ALTER TABLE decisionsdossierspcgs66 ALTER COLUMN vuavistechnique SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE decisionsdossierspcgs66 SET vuavistechnique = '0'::TYPE_BOOLEANNUMBER WHERE vuavistechnique IS NULL;

SELECT public.alter_enumtype ( 'TYPE_ETATDOSSIERPCG', ARRAY['attaffect', 'attinstr', 'instrencours', 'attavistech', 'attval', 'dossiertraite', 'decisionvalid', 'decisionnonvalid', 'decisionnonvalidretouravis', 'decisionvalidretouravis', 'attpj', 'transmisop', 'atttransmisop' ] );

-- ***********************************************************************************************************
-- 20120116 -- Ajout d'un champ type de rsa dans les décisions des dossiers PCGs
-- ***********************************************************************************************************
CREATE TABLE typesrsapcgs66 (
	id			SERIAL NOT NULL PRIMARY KEY,
	name		VARCHAR(100) NOT NULL
);
COMMENT ON TABLE typesrsapcgs66 IS 'Table de jointure entre les dossiers PCGs des personnes et leurs situations';
CREATE INDEX typesrsapcgs66_name_idx ON typesrsapcgs66 (name);

CREATE TABLE decisionsdossierspcgs66_typesrsapcgs66 (
	id						SERIAL NOT NULL PRIMARY KEY,
	decisiondossierpcg66_id		INTEGER NOT NULL REFERENCES decisionsdossierspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typersapcg66_id			INTEGER NOT NULL REFERENCES typesrsapcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE
);
COMMENT ON TABLE decisionsdossierspcgs66_typesrsapcgs66 IS 'Table permettant de stocker les différents types de rsa pour une décision de dossier PCG donnée (CG66)';
CREATE INDEX decisionsdossierspcgs66_typesrsapcgs66_decisiondossierpcg66_id_idx ON decisionsdossierspcgs66_typesrsapcgs66 (decisiondossierpcg66_id);
CREATE INDEX decisionsdossierspcgs66_typesrsapcgs66_typersapcg66_id_idx ON decisionsdossierspcgs66_typesrsapcgs66 (typersapcg66_id);


ALTER TABLE traitementspcgs66 ALTER COLUMN personnepcg66_situationpdo_id DROP NOT NULL;

-- ***********************************************************************************************************
-- 20120117 -- Ajout d'un champ pour déterminer la date d'impression du dossier PCG
-- ***********************************************************************************************************
SELECT add_missing_table_field ( 'public', 'dossierspcgs66', 'dateimpression', 'DATE' );

SELECT add_missing_table_field ('public', 'dossierspcgs66', 'istransmis', 'TYPE_BOOLEANNUMBER');
ALTER TABLE dossierspcgs66 ALTER COLUMN istransmis SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE dossierspcgs66 SET istransmis = '0'::TYPE_BOOLEANNUMBER WHERE istransmis IS NULL;

SELECT add_missing_table_field ( 'public', 'dossierspcgs66', 'datetransmission', 'DATE' );


-- ***********************************************************************************************************
-- 20120119 -- Ajout d'une modification de la date de validation CER 66
-- ***********************************************************************************************************
SELECT add_missing_table_field ( 'public', 'contratsinsertion', 'datedecision', 'DATE' );

UPDATE contratsinsertion SET datedecision = date_trunc( 'day', datevalidation_ci ) WHERE decision_ci = 'V';
UPDATE contratsinsertion SET datedecision = date_trunc( 'day', modified ) WHERE decision_ci = 'N' AND datedecision IS NULL;


-- ***********************************************************************************************************
-- 20120124 -- Ajout d'une nouvelle valeur sur l'enum des posiitons du CER 66
-- ***********************************************************************************************************

SELECT public.alter_enumtype ( 'TYPE_POSITIONCER', ARRAY['encours','attvalid','annule','fincontrat','encoursbilan','attrenouv','perime', 'nonvalide'] );

-- ***********************************************************************************************************
-- 20120125 -- Ajout de champ supplémentaire pour les éditions de courrier CER 
--				selon la position et le type de structures referentes
-- ***********************************************************************************************************
ALTER TABLE contratsinsertion ALTER COLUMN positioncer SET DEFAULT 'attvalid'::TYPE_POSITIONCER;

CREATE TYPE TYPE_OAMSP AS ENUM ('oa', 'msp' );
SELECT add_missing_table_field ( 'public', 'structuresreferentes', 'typestructure', 'TYPE_OAMSP' );
ALTER TABLE structuresreferentes ALTER COLUMN typestructure SET DEFAULT 'msp'::TYPE_OAMSP;
UPDATE structuresreferentes SET typestructure = 'msp'::TYPE_OAMSP WHERE typestructure IS NULL;

-- ***********************************************************************************************************
-- 20120124 -- Suppression de champs de clés étrangères non utilisés depuis entretiens vers dsps
-- ***********************************************************************************************************

SELECT public.alter_table_drop_column_if_exists( 'public', 'entretiens', 'nv_dsp_id' );
SELECT public.alter_table_drop_column_if_exists( 'public', 'entretiens', 'vx_dsp_id' );

-- *****************************************************************************
COMMIT;
-- *****************************************************************************