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

DROP TABLE IF EXISTS regressionsorientationseps58 CASCADE;
DROP TABLE IF EXISTS decisionsregressionsorientationseps58 CASCADE;
DROP TABLE IF EXISTS sanctionseps93 CASCADE;
DROP TABLE IF EXISTS decisionssanctionseps93 CASCADE;
DROP TABLE IF EXISTS listesanctionseps58 CASCADE;
DROP TABLE IF EXISTS sanctionseps58 CASCADE;
DROP TABLE IF EXISTS decisionssanctionseps58 CASCADE;
DROP TABLE IF EXISTS objetsentretien CASCADE;

DROP INDEX IF EXISTS dsps_personne_id_idx;
CREATE INDEX dsps_personne_id_idx ON dsps(personne_id);

DROP INDEX IF EXISTS regressionsorientationseps58_dossierep_id_idx;
DROP INDEX IF EXISTS regressionsorientationseps58_typeorient_id_idx;
DROP INDEX IF EXISTS regressionsorientationseps58_structurereferente_id_idx;
DROP INDEX IF EXISTS regressionsorientationseps58_referent_id_idx;
DROP INDEX IF EXISTS decisionsregressionsorientationseps58_regressionorientationep58_id_idx;
DROP INDEX IF EXISTS decisionsregressionsorientationseps58_typeorient_id_idx;
DROP INDEX IF EXISTS decisionsregressionsorientationseps58_structurereferente_id_idx;
DROP INDEX IF EXISTS decisionsregressionsorientationseps58_referent_id_idx;
DROP INDEX IF EXISTS decisionssanctionseps58_sanctionep58_id_idx;
DROP INDEX IF EXISTS decisionssanctionseps58_listesanctionep58_id_idx;
DROP INDEX IF EXISTS regressionsorientationseps58_user_id_idx;

-- *****************************************************************************

DROP TYPE IF EXISTS TYPE_ORIGINESANCTION CASCADE;
DROP TYPE IF EXISTS TYPE_DECISIONSANCTIONEP58 CASCADE;
DROP TYPE IF EXISTS TYPE_TYPEAUDITIONPE CASCADE;
DROP TYPE IF EXISTS TYPE_DECISIONSUPDEFAUTEP66 CASCADE;

ALTER TABLE dossierseps ALTER COLUMN themeep TYPE TEXT;
DROP TYPE IF EXISTS TYPE_THEMEEP;
CREATE TYPE TYPE_THEMEEP AS ENUM ( 'saisinesepsreorientsrs93', 'saisinesepsbilansparcours66', /*'suspensionsreductionsallocations93',*/ 'saisinesepdspdos66', 'nonrespectssanctionseps93', 'defautsinsertionseps66', 'nonorientationspros58', 'nonorientationspros93', 'regressionsorientationseps58', 'sanctionseps58' );
ALTER TABLE dossierseps ALTER COLUMN themeep TYPE TYPE_THEMEEP USING CAST(themeep AS TYPE_THEMEEP);

ALTER TABLE propospdos ALTER COLUMN etatdossierpdo TYPE TEXT;
ALTER TABLE decisionspropospdos ALTER COLUMN etatdossierpdo TYPE TEXT;
DROP TYPE IF EXISTS TYPE_ETATDOSSIERPDO;
--SELECT * FROM pg_catalog.pg_type where typname= 'TYPE_ETATDOSSIERPDO';
UPDATE propospdos SET etatdossierpdo = 'instrencours' WHERE etatdossierpdo = 'decisionval';
UPDATE decisionspropospdos SET etatdossierpdo = 'instrencours' WHERE etatdossierpdo = 'decisionval';
CREATE TYPE TYPE_ETATDOSSIERPDO AS ENUM ( 'attaffect', 'attinstr', 'instrencours', 'attavistech', 'attval'/*, 'decisionval'*/, 'dossiertraite', 'attpj' );
ALTER TABLE propospdos ALTER COLUMN etatdossierpdo TYPE TYPE_ETATDOSSIERPDO USING CAST(etatdossierpdo AS TYPE_ETATDOSSIERPDO);
ALTER TABLE decisionspropospdos ALTER COLUMN etatdossierpdo TYPE TYPE_ETATDOSSIERPDO USING CAST(etatdossierpdo AS TYPE_ETATDOSSIERPDO);

ALTER TABLE bilansparcours66 ALTER COLUMN proposition TYPE TEXT;
DROP TYPE IF EXISTS TYPE_PROPOSITIONBILANPARCOURS;
CREATE TYPE TYPE_PROPOSITIONBILANPARCOURS AS ENUM ( 'audition', 'parcours', 'traitement', 'auditionpe' );
ALTER TABLE bilansparcours66 ALTER COLUMN proposition TYPE TYPE_PROPOSITIONBILANPARCOURS USING CAST(proposition AS TYPE_PROPOSITIONBILANPARCOURS);
ALTER TABLE bilansparcours66 ALTER COLUMN proposition SET NOT NULL;

ALTER TABLE nonrespectssanctionseps93 ALTER COLUMN origine TYPE TEXT;
DROP TYPE IF EXISTS TYPE_ORIGINESANCTIONEP93;
CREATE TYPE TYPE_ORIGINESANCTIONEP93 AS ENUM ( 'orientstruct', 'contratinsertion', 'pdo', 'radiepe' );
ALTER TABLE nonrespectssanctionseps93 ALTER COLUMN origine TYPE TYPE_ORIGINESANCTIONEP93 USING CAST(origine AS TYPE_ORIGINESANCTIONEP93);
ALTER TABLE nonrespectssanctionseps93 ALTER COLUMN origine SET NOT NULL;

-- *****************************************************************************

CREATE OR REPLACE FUNCTION public.add_missing_constraint (text, text, text, text, text)
RETURNS bool as '
DECLARE
	p_namespace 		alias for $1;
	p_table     		alias for $2;
	p_constraintname	alias for $3;
	p_foreigntable		alias for $4;
	p_foreignkeyname	alias for $5;
	v_row       		record;
	v_query     		text;
BEGIN
	select 1 into v_row
	from information_schema.table_constraints tc
		left join information_schema.key_column_usage kcu on (
			tc.constraint_catalog = kcu.constraint_catalog
			and tc.constraint_schema = kcu.constraint_schema
			and tc.constraint_name = kcu.constraint_name
		)
		left join information_schema.referential_constraints rc on (
			tc.constraint_catalog = rc.constraint_catalog
			and tc.constraint_schema = rc.constraint_schema
			and tc.constraint_name = rc.constraint_name
		)
		left join information_schema.constraint_column_usage ccu on (
			rc.unique_constraint_catalog = ccu.constraint_catalog
			and rc.unique_constraint_schema = ccu.constraint_schema
			and rc.unique_constraint_name = ccu.constraint_name
		)
	where tc.table_name = p_table
		and tc.constraint_type = ''FOREIGN KEY''
		and tc.constraint_name = p_constraintname;
	if not found then
		raise notice ''Upgrade table %.% - add constraint %'', p_namespace, p_table, p_constraintname;
		v_query := ''alter table '' || p_namespace || ''.'' || p_table || '' add constraint '';
		v_query := v_query || p_constraintname || '' FOREIGN KEY ('' || p_foreignkeyname || '') REFERENCES '' || p_foreigntable || ''(id) ON DELETE CASCADE ON UPDATE CASCADE;'';
		execute v_query;
		return ''t'';
	else
		return ''f'';
	end if;
END;' language plpgsql;

COMMENT ON FUNCTION public.add_missing_constraint (text, text, text, text, text) IS 'Add a constraint to a table if it is missing';

-- *****************************************************************************

CREATE TABLE regressionsorientationseps58 (
	id      				SERIAL NOT NULL PRIMARY KEY,
	dossierep_id			INTEGER DEFAULT NULL REFERENCES dossierseps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typeorient_id			INTEGER NOT NULL REFERENCES typesorients(id) ON DELETE CASCADE ON UPDATE CASCADE,
	structurereferente_id	INTEGER NOT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	datedemande				DATE NOT NULL,
	referent_id				INTEGER DEFAULT NULL REFERENCES referents(id) ON DELETE SET NULL ON UPDATE CASCADE,
	user_id					INTEGER DEFAULT NULL REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
	commentaire				TEXT DEFAULT NULL,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE regressionsorientationseps58 IS 'Thématique pour la réorientation du professionel vers le social (CG58)';

CREATE INDEX regressionsorientationseps58_dossierep_id_idx ON regressionsorientationseps58 (dossierep_id);
CREATE INDEX regressionsorientationseps58_typeorient_id_idx ON regressionsorientationseps58 (typeorient_id);
CREATE INDEX regressionsorientationseps58_structurereferente_id_idx ON regressionsorientationseps58 (structurereferente_id);
CREATE INDEX regressionsorientationseps58_referent_id_idx ON regressionsorientationseps58 (referent_id);
CREATE INDEX regressionsorientationseps58_user_id_idx ON regressionsorientationseps58 (user_id);

SELECT add_missing_table_field ('public', 'eps', 'regressionorientationep58', 'TYPE_NIVEAUDECISIONEP');
ALTER TABLE eps ALTER COLUMN regressionorientationep58 SET DEFAULT 'nontraite';
UPDATE eps SET regressionorientationep58 = 'nontraite' WHERE regressionorientationep58 IS NULL;
ALTER TABLE eps ALTER COLUMN regressionorientationep58 SET NOT NULL;

CREATE TABLE decisionsregressionsorientationseps58 (
	id      						SERIAL NOT NULL PRIMARY KEY,
	regressionorientationep58_id	INTEGER NOT NULL REFERENCES regressionsorientationseps58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typeorient_id					INTEGER DEFAULT NULL REFERENCES typesorients(id) ON UPDATE CASCADE ON DELETE SET NULL,
	structurereferente_id			INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON UPDATE CASCADE ON DELETE SET NULL,
	referent_id						INTEGER DEFAULT NULL REFERENCES referents(id) ON DELETE SET NULL ON UPDATE CASCADE,
	etape							TYPE_ETAPEDECISIONEP NOT NULL,
	commentaire						TEXT DEFAULT NULL,
	created							TIMESTAMP WITHOUT TIME ZONE,
	modified						TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE decisionsregressionsorientationseps58 IS 'Décisions pour la thématique de la réorientation du professionel vers le social (CG58)';

CREATE INDEX decisionsregressionsorientationseps58_regressionorientationep58_id_idx ON decisionsregressionsorientationseps58 (regressionorientationep58_id);
CREATE INDEX decisionsregressionsorientationseps58_typeorient_id_idx ON decisionsregressionsorientationseps58 (typeorient_id);
CREATE INDEX decisionsregressionsorientationseps58_structurereferente_id_idx ON decisionsregressionsorientationseps58 (structurereferente_id);
CREATE INDEX decisionsregressionsorientationseps58_referent_id_idx ON decisionsregressionsorientationseps58 (referent_id);


-- -----------------------------------------------------------------------------
-- 20110221
-- -----------------------------------------------------------------------------
SELECT alter_table_drop_column_if_exists( 'public', 'contratsinsertion', 'datesuspensionparticulier' );
SELECT alter_table_drop_column_if_exists( 'public', 'contratsinsertion', 'dateradiationparticulier' );
ALTER TABLE contratsinsertion ADD COLUMN datesuspensionparticulier DATE DEFAULT NULL;
ALTER TABLE contratsinsertion ADD COLUMN dateradiationparticulier DATE DEFAULT NULL;

-- -----------------------------------------------------------------------------
-- 20110222
-- -----------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION public.calcul_cle_nir( TEXT ) RETURNS TEXT AS
$body$
	DECLARE
		p_nir text;
		cle text;
		correction BIGINT;

	BEGIN
		correction:=0;
		p_nir:=$1;

		IF NOT p_nir ~ '^[0-9]{6}(A|B|[0-9])[0-9]{6}$' THEN
			RETURN NULL;
		END IF;

		IF p_nir ~ '^.{6}(A|B)' THEN
			IF p_nir ~ '^.{6}A' THEN
				correction:=1000000;
			ELSE
				correction:=2000000;
			END IF;
			p_nir:=regexp_replace( p_nir, '(A|B)', '0' );
		END IF;

		cle:=LPAD( CAST( 97 - ( ( CAST( p_nir AS BIGINT ) - correction ) % 97 ) AS VARCHAR(13)), 2, '0' );
		RETURN cle;
	END;
$body$ LANGUAGE plpgsql;

COMMENT ON FUNCTION public.calcul_cle_nir( TEXT ) IS
	'Calcul de la clé d''un NIR. Retourne NULL si le NIR n''est pas sur 13 caractères (6 chiffres - A, B ou un chiffre - 6 chiffres) ou une chaîne de 2 caractères correspondant à la clé.';

-- -----------------------------------------------------------------------------
-- 20110301
-- -----------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'proposorientationscovs58', 'user_id', 'integer');
SELECT add_missing_constraint ('public', 'proposorientationscovs58', 'proposorientationscovs58_user_id_fkey', 'users', 'user_id');
-- FIXME : à rendre not null !!!
-- ALTER TABLE proposorientationscovs58 ALTER COLUMN user_id SET NOT NULL;

-- -----------------------------------------------------------------------------
-- 20110302
-- -----------------------------------------------------------------------------
CREATE TYPE TYPE_ORIGINESANCTION AS ENUM ( 'radiepe', 'noninscritpe' );
CREATE TYPE TYPE_DECISIONSANCTIONEP58 AS ENUM ( 'maintien', 'sanction' );

CREATE TABLE listesanctionseps58 (
	id      				SERIAL NOT NULL PRIMARY KEY,
	rang					INTEGER NOT NULL,
	sanction				VARCHAR(20) NOT NULL,
	duree					INTEGER NOT NULL
);

CREATE TABLE sanctionseps58 (
	id      				SERIAL NOT NULL PRIMARY KEY,
	dossierep_id			INTEGER DEFAULT NULL REFERENCES dossierseps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	origine					TYPE_ORIGINESANCTION NOT NULL,
	listesanctionep58_id	INTEGER NOT NULL REFERENCES listesanctionseps58(id) ON DELETE SET NULL ON UPDATE CASCADE,
	commentaire				TEXT DEFAULT NULL,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE sanctionseps58 IS 'Thématique de détection des radiés et non inscrits à Pôle Emploi (CG58)';

CREATE INDEX sanctionseps58_listesanctionep58_id_idx ON sanctionseps58 (listesanctionep58_id);

SELECT add_missing_table_field ('public', 'eps', 'sanctionep58', 'TYPE_NIVEAUDECISIONEP');
ALTER TABLE eps ALTER COLUMN sanctionep58 SET DEFAULT 'nontraite';
UPDATE eps SET sanctionep58 = 'nontraite' WHERE sanctionep58 IS NULL;
ALTER TABLE eps ALTER COLUMN sanctionep58 SET NOT NULL;

CREATE TABLE decisionssanctionseps58 (
	id      						SERIAL NOT NULL PRIMARY KEY,
	sanctionep58_id					INTEGER NOT NULL REFERENCES sanctionseps58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	etape							TYPE_ETAPEDECISIONEP NOT NULL,
	decision						TYPE_DECISIONSANCTIONEP58 DEFAULT NULL,
	commentaire						TEXT DEFAULT NULL,
	created							TIMESTAMP WITHOUT TIME ZONE,
	modified						TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE decisionssanctionseps58 IS 'Décisions pour la thématique de détection des radiés et non inscrits Pôle Emploi (CG58)';

CREATE INDEX decisionssanctionseps58_sanctionep58_id_idx ON decisionssanctionseps58 (sanctionep58_id);

-- -----------------------------------------------------------------------------
-- 20110302
-- -----------------------------------------------------------------------------
SELECT alter_table_drop_column_if_exists( 'public', 'contratsinsertion', 'faitsuitea' );
ALTER TABLE contratsinsertion ADD COLUMN faitsuitea type_booleannumber DEFAULT NULL;


-- -----------------------------------------------------------------------------
-- 20110303
-- -----------------------------------------------------------------------------

-- Ajout d'une table objet de l'entretien pour paramétrer les types d'entretien existants
CREATE TABLE objetsentretien(
    id                      SERIAL NOT NULL PRIMARY KEY,
    name                    VARCHAR(255) NOT NULL,
    modeledocument          VARCHAR (50)
);
COMMENT ON TABLE objetsentretien IS 'Objets des entretiens';
CREATE UNIQUE INDEX objetsentretien_name_idx ON objetsentretien (name);

-- Ajout d'une clé étrangère entre la table objetsentretien et entretiens
SELECT alter_table_drop_column_if_exists( 'public', 'entretiens', 'objetentretien_id' );
ALTER TABLE entretiens ADD COLUMN objetentretien_id INTEGER DEFAULT NULL REFERENCES objetsentretien (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- Ajout d'un enregistrement à objetsentretiens pour pouvoir utiliser
-- une clé étrangère NOT NULL si on a des enregistrements dans entretiens.
INSERT INTO objetsentretien (name) VALUES ( 'Objet d''entretien 1' );

UPDATE entretiens
	SET objetentretien_id = ( SELECT id FROM objetsentretien WHERE name = 'Objet d''entretien 1' )
	WHERE objetentretien_id IS NULL;

ALTER TABLE entretiens ALTER COLUMN objetentretien_id SET NOT NULL;

-- -----------------------------------------------------------------------------

DROP TYPE IF EXISTS TYPE_POSITIONCER CASCADE;
CREATE TYPE TYPE_POSITIONCER AS ENUM ( 'encours', 'attvalid', 'annule', 'fincontrat', 'encoursbilan', 'attrenouv', 'perime' );
SELECT add_missing_table_field ('public', 'contratsinsertion', 'positioncer', 'TYPE_POSITIONCER');

ALTER TABLE pdfs ALTER COLUMN document DROP NOT NULL;
SELECT alter_table_drop_column_if_exists( 'public', 'pdfs', 'cmspath' );
ALTER TABLE pdfs ADD COLUMN cmspath VARCHAR(250) DEFAULT NULL;

-- -----------------------------------------------------------------------------
-- 20110304
-- -----------------------------------------------------------------------------

SELECT alter_table_drop_column_if_exists( 'public', 'entretiens', 'arevoirle' );
ALTER TABLE entretiens ADD COLUMN arevoirle DATE DEFAULT NULL;

DROP TYPE IF EXISTS TYPE_POSITIONBILAN CASCADE;
CREATE TYPE TYPE_POSITIONBILAN AS ENUM ( 'eplaudit', 'eplparc', 'attcga', 'attct', 'ajourne', 'annule' );
SELECT add_missing_table_field ('public', 'bilansparcours66', 'positionbilan', 'TYPE_POSITIONBILAN');

-- -----------------------------------------------------------------------------
-- 20110307
-- -----------------------------------------------------------------------------

SELECT alter_table_drop_column_if_exists( 'public', 'orientsstructs', 'user_id' );
ALTER TABLE orientsstructs ADD COLUMN user_id INTEGER DEFAULT NULL REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE;

SELECT add_missing_table_field ('public', 'nonorientationspros58', 'user_id', 'integer');
SELECT add_missing_constraint ('public', 'nonorientationspros58', 'nonorientationspros58_user_id_fkey', 'users', 'user_id');
SELECT add_missing_table_field ('public', 'nonorientationspros66', 'user_id', 'integer');
SELECT add_missing_constraint ('public', 'nonorientationspros66', 'nonorientationspros66_user_id_fkey', 'users', 'user_id');
SELECT add_missing_table_field ('public', 'nonorientationspros93', 'user_id', 'integer');
SELECT add_missing_constraint ('public', 'nonorientationspros93', 'nonorientationspros93_user_id_fkey', 'users', 'user_id');

SELECT alter_table_drop_column_if_exists( 'public', 'contratsinsertion', 'created' );
ALTER TABLE contratsinsertion ADD COLUMN created TIMESTAMP WITHOUT TIME ZONE;
UPDATE contratsinsertion SET created = dd_ci WHERE created IS NULL;
ALTER TABLE contratsinsertion ALTER COLUMN created SET NOT NULL;

SELECT alter_table_drop_column_if_exists( 'public', 'contratsinsertion', 'modified' );
ALTER TABLE contratsinsertion ADD COLUMN modified TIMESTAMP WITHOUT TIME ZONE;
UPDATE contratsinsertion SET modified = dd_ci WHERE modified IS NULL;
ALTER TABLE contratsinsertion ALTER COLUMN modified SET NOT NULL;

-- -----------------------------------------------------------------------------
-- 20110309
-- -----------------------------------------------------------------------------
SELECT alter_table_drop_column_if_exists( 'public', 'bilansparcours66', 'datecourrierimpression' );
ALTER TABLE bilansparcours66 ADD COLUMN datecourrierimpression DATE DEFAULT NULL;

SELECT add_missing_table_field ('public', 'decisionspdos', 'clos', 'TYPE_NO');
ALTER TABLE decisionspdos ALTER COLUMN clos SET DEFAULT 'N';
UPDATE decisionspdos SET clos = 'N' WHERE clos IS NULL;
ALTER TABLE decisionspdos ALTER COLUMN clos SET NOT NULL;

-- -----------------------------------------------------------------------------

-- /gedooos/orientstruct/301764 -> /gedooos/orientstruct/301764
-- /cohortes/impression_individuelle/78908 -> ???

UPDATE acos
	SET alias = 'Gedooos:orientstruct'
	WHERE alias = 'Orientsstructs:impression';

DELETE FROM aros_acos WHERE aco_id IN ( SELECT id FROM acos WHERE alias = 'Cohortes:impression_individuelle' );
DELETE FROM acos WHERE alias = 'Cohortes:impression_individuelle';

UPDATE acos
	SET alias = 'Relancesnonrespectssanctionseps93:impression'
	WHERE alias = 'Relancesnonrespectssanctionseps93:impression_individuelle';

-- -----------------------------------------------------------------------------
-- 20110310
-- -----------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'decisionspropospdos', 'created', 'TIMESTAMP WITHOUT TIME ZONE');
SELECT add_missing_table_field ('public', 'decisionspropospdos', 'modified', 'TIMESTAMP WITHOUT TIME ZONE');

SELECT add_missing_table_field( 'public', 'traitementspdos', 'dureedepart', 'TEXT' );
SELECT add_missing_table_field( 'public', 'traitementspdos', 'dureeecheance', 'TEXT' );
ALTER TABLE traitementspdos ALTER COLUMN dureedepart TYPE TEXT;
ALTER TABLE traitementspdos ALTER COLUMN dureeecheance TYPE TEXT;
DROP TYPE IF EXISTS TYPE_DUREE;
CREATE TYPE TYPE_DUREE AS ENUM ( '1', '1.5', '2', '2.5', '3', '3.5', '4', '4.5', '5', '5.5', '6', '6.5', '7', '7.5', '8', '8.5', '9', '9.5', '10', '10.5', '11', '11.5', '12' );
ALTER TABLE traitementspdos ALTER COLUMN dureedepart TYPE TYPE_DUREE USING CAST(dureedepart AS TYPE_DUREE);
ALTER TABLE traitementspdos ALTER COLUMN dureedepart SET DEFAULT NULL;
ALTER TABLE traitementspdos ALTER COLUMN dureeecheance TYPE TYPE_DUREE USING CAST(dureeecheance AS TYPE_DUREE);
ALTER TABLE traitementspdos ALTER COLUMN dureeecheance SET DEFAULT NULL;

-- -----------------------------------------------------------------------------
-- 20110310
-- -----------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'seanceseps', 'lieuseance', 'VARCHAR(50)');
SELECT add_missing_table_field ('public', 'seanceseps', 'adresseseance', 'VARCHAR(100)');
SELECT add_missing_table_field ('public', 'seanceseps', 'codepostalseance', 'CHAR(5)');
SELECT add_missing_table_field ('public', 'seanceseps', 'villeseance', 'VARCHAR(100)');

CREATE OR REPLACE FUNCTION updateLieuSeanceEp() RETURNS text AS
$$
DECLARE
	v_row	record;
BEGIN
	SELECT 1 INTO v_row
		FROM information_schema.columns
		WHERE
			information_schema.columns.table_catalog = current_database()
			AND table_schema = 'public'
			AND table_name = 'seanceseps'
			AND column_name = 'structurereferente_id';

	IF FOUND THEN
		UPDATE seanceseps
			SET
				lieuseance = ( SELECT structuresreferentes.lib_struc FROM structuresreferentes WHERE structuresreferentes.id = structurereferente_id ),
				adresseseance = ( SELECT structuresreferentes.num_voie || ' ' || structuresreferentes.type_voie || ' ' || structuresreferentes.nom_voie FROM structuresreferentes WHERE structuresreferentes.id = structurereferente_id ),
				codepostalseance = ( SELECT structuresreferentes.code_postal FROM structuresreferentes WHERE structuresreferentes.id = structurereferente_id ),
				villeseance = ( SELECT structuresreferentes.ville FROM structuresreferentes WHERE structuresreferentes.id = structurereferente_id )
			WHERE
				structurereferente_id IS NOT NULL
				AND lieuseance IS NULL
				AND adresseseance IS NULL
				AND codepostalseance IS NULL
				AND villeseance IS NULL;
		ALTER TABLE seanceseps DROP COLUMN structurereferente_id;
		RETURN 'Colonne structurereferente_id trouvée dans la table seanceseps et traitée';
	ELSE
		RETURN 'Colonne structurereferente_id non trouvée dans la table seanceseps';
	END IF;
END;
$$
LANGUAGE plpgsql;

SELECT updateLieuSeanceEp();
DROP FUNCTION updateLieuSeanceEp();

ALTER TABLE seanceseps ALTER COLUMN lieuseance SET NOT NULL;
ALTER TABLE seanceseps ALTER COLUMN adresseseance SET NOT NULL;
ALTER TABLE seanceseps ALTER COLUMN codepostalseance SET NOT NULL;
ALTER TABLE seanceseps ALTER COLUMN villeseance SET NOT NULL;

SELECT add_missing_table_field ('public', 'decisionsdefautsinsertionseps66', 'referent_id', 'integer');
SELECT add_missing_constraint ('public', 'decisionsdefautsinsertionseps66', 'decisionsdefautsinsertionseps66_referent_id_fkey', 'referents', 'referent_id');
SELECT add_missing_table_field ('public', 'nvsrsepsreorient66', 'referent_id', 'integer');
SELECT add_missing_constraint ('public', 'nvsrsepsreorient66', 'nvsrsepsreorient66_referent_id_fkey', 'referents', 'referent_id');


-- -----------------------------------------------------------------------------
-- 20110314
-- -----------------------------------------------------------------------------
CREATE TYPE TYPE_TYPEAUDITIONPE AS ENUM ( 'noninscriptionpe', 'radiationpe' );
SELECT add_missing_table_field ('public', 'bilansparcours66', 'examenauditionpe', 'TYPE_TYPEAUDITIONPE');

-- Ajout de la gestion des fichiers attachés aux PDOs
DROP TABLE IF EXISTS fichierstraitementspdos;
DROP TYPE IF EXISTS TYPE_TYPEFICHIERTRAITEMENTPDO CASCADE;

CREATE TYPE TYPE_TYPEFICHIERTRAITEMENTPDO AS ENUM ( 'courrier', 'piecejointe' );
CREATE TABLE fichierstraitementspdos (
	id      				SERIAL NOT NULL PRIMARY KEY,
	name					VARCHAR(255) NOT NULL,
	traitementpdo_id		INTEGER NOT NULL REFERENCES traitementspdos ON UPDATE CASCADE ON DELETE CASCADE,
	type					TYPE_TYPEFICHIERTRAITEMENTPDO NOT NULL,
	document				BYTEA DEFAULT NULL,
	cmspath					VARCHAR(255) DEFAULT NULL,
	mime					VARCHAR(255) NOT NULL,-- FIXME ?
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);

CREATE INDEX fichierstraitementspdos_name_idx ON fichierstraitementspdos( name );
CREATE INDEX fichierstraitementspdos_traitementpdo_id_idx ON fichierstraitementspdos( traitementpdo_id );
CREATE INDEX fichierstraitementspdos_type_idx ON fichierstraitementspdos( type );
CREATE INDEX fichierstraitementspdos_mime_idx ON fichierstraitementspdos( mime );
CREATE UNIQUE INDEX fichierstraitementspdos_cmspath_idx ON fichierstraitementspdos( cmspath );

-- -----------------------------------------------------------------------------
-- 20110314
-- -----------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'eps', 'nonorientationpro93', 'TYPE_NIVEAUDECISIONEP');
ALTER TABLE eps ALTER COLUMN nonorientationpro93 SET DEFAULT 'nontraite';
UPDATE eps SET nonorientationpro93 = 'nontraite' WHERE nonorientationpro93 IS NULL;
ALTER TABLE eps ALTER COLUMN nonorientationpro93 SET NOT NULL;

-- -----------------------------------------------------------------------------
-- 20110316
-- -----------------------------------------------------------------------------

ALTER TABLE traitementspdos ALTER COLUMN hascourrier SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE traitementspdos SET hascourrier = '0'::TYPE_BOOLEANNUMBER WHERE hascourrier IS NULL;
ALTER TABLE traitementspdos ALTER COLUMN hascourrier SET NOT NULL;

ALTER TABLE traitementspdos ALTER COLUMN hasrevenu SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE traitementspdos SET hasrevenu = '0'::TYPE_BOOLEANNUMBER WHERE hasrevenu IS NULL;
ALTER TABLE traitementspdos ALTER COLUMN hasrevenu SET NOT NULL;

ALTER TABLE traitementspdos ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE traitementspdos SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE traitementspdos ALTER COLUMN haspiecejointe SET NOT NULL;

ALTER TABLE traitementspdos ALTER COLUMN hasficheanalyse SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE traitementspdos SET hasficheanalyse = '0'::TYPE_BOOLEANNUMBER WHERE hasficheanalyse IS NULL;
ALTER TABLE traitementspdos ALTER COLUMN hasficheanalyse SET NOT NULL;


-- -----------------------------------------------------------------------------
-- 20110317
-- -----------------------------------------------------------------------------
ALTER TABLE detailsdifdisps ALTER COLUMN difdisp TYPE text;
ALTER TABLE detailsdifdisps_revs ALTER COLUMN difdisp TYPE text;
DROP TYPE IF EXISTS type_difdisp;
CREATE TYPE type_difdisp AS ENUM ( '0501', '0502', '0503', '0504', '0505', '0506', '0507', '0508', '0509', '0510', '0511', '0512', '0513', '0514' );

ALTER TABLE detailsdifdisps ALTER COLUMN difdisp TYPE type_difdisp USING CAST(difdisp AS type_difdisp);
ALTER TABLE detailsdifdisps_revs ALTER COLUMN difdisp TYPE type_difdisp USING CAST(difdisp AS type_difdisp);

-- -----------------------------------------------------------------------------
-- 20110317: morceaux de conditions pour les moteurs de recherche (CG 58)
-- -----------------------------------------------------------------------------

-- FIXME: c'est dans servicesinstructeurs car users n'est pas lié à structuresreferentes
SELECT add_missing_table_field ('public', 'servicesinstructeurs', 'sqrecherche', 'TEXT');
ALTER TABLE servicesinstructeurs ALTER COLUMN sqrecherche SET DEFAULT NULL;

-- Donner un exemple (cf. patch-2.0-rc16-datas-cg58.sql)

-- Stocké dans la session -> déco / reco pour les changements
-- Les requêtes sont vérifiées lors de l'ajoutt ou de la modification d'un service instructeur
-- La partie "vérification de l'application" vérifie pour tous les services instructeurs

-- Voir + commentaires:
--	* Configure::write( 'Recherche.qdFilters.Serviceinstructeur' )
-- 	* AppController::_qdAddFilters
-- 	* Serviceinstructeur::sqrechercheErrors, _queryDataError, validateSqrecherche
-- 	* ChecksController::_checkSqrecherche

-- OK pour (FIXME: y-a-t-il d'autres endroits où ces fonctions search sont utilisées ?)
-- 	* dossiers/index, dossiers/exportcsv (Dossier->search)
-- 	* criteres/index, criteres/exportcsv (Critere->search)
-- 	* criteresci/index, criteresci/exportcsv (Cohorteci->search)
-- 	* criterescuis/index, criterescuis/exportcsv (Criterecui->search)
-- 	* cohortesindus/index, cohortesindus/exportcsv (Cohorteindu->search)
-- 	* criteresrdv/index, criteresrdv/exportcsv (Critererdv->search)
-- 	* criterespdos/index, criterespdos/nouvelles, criterespdos/exportcsv ($this->Criterepdo->listeDossierPDO, Criterepdo->search)

-- INFO: pas de vérification pour $this->Criterepdo->listeDossierPDO -> FIXME ?

-- -----------------------------------------------------------------------------
-- 20110317
-- -----------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'nonrespectssanctionseps93', 'historiqueetatpe_id', 'integer');
SELECT add_missing_constraint ('public', 'nonrespectssanctionseps93', 'nonrespectssanctionseps93_historiqueetatpe_id_fkey', 'historiqueetatspe', 'historiqueetatpe_id');

ALTER TABLE nonrespectssanctionseps93 DROP CONSTRAINT nonrespectssanctionseps93_valid_entry_chk;
ALTER TABLE nonrespectssanctionseps93 ADD CONSTRAINT nonrespectssanctionseps93_valid_entry_chk CHECK (
	( propopdo_id IS NOT NULL ) OR ( orientstruct_id IS NOT NULL ) OR ( contratinsertion_id IS NOT NULL ) OR ( historiqueetatpe_id IS NOT NULL )
);


DROP TABLE IF EXISTS decisionsradiespoleemploieps58 CASCADE;
DROP TABLE IF EXISTS decisionsradiespoleemploieps93 CASCADE;

ALTER TABLE nonrespectssanctionseps93 ALTER COLUMN decision TYPE text;
ALTER TABLE decisionsnonrespectssanctionseps93 ALTER COLUMN decision TYPE text;
DROP TYPE IF EXISTS TYPE_DECISIONSANCTIONEP93;
CREATE TYPE TYPE_DECISIONSANCTIONEP93 AS ENUM ( '1reduction', '1maintien', '1sursis', '1pasavis', '1delai', '2suspensiontotale', '2suspensionpartielle', '2maintien', '2pasavis', '2report' );
ALTER TABLE nonrespectssanctionseps93 ALTER COLUMN decision TYPE TYPE_DECISIONSANCTIONEP93 USING CAST(decision AS TYPE_DECISIONSANCTIONEP93);
ALTER TABLE decisionsnonrespectssanctionseps93 ALTER COLUMN decision TYPE TYPE_DECISIONSANCTIONEP93 USING CAST(decision AS TYPE_DECISIONSANCTIONEP93);




ALTER TABLE decisionspropospdos ALTER COLUMN avistechnique TYPE text;
UPDATE decisionspropospdos SET avistechnique = '1' WHERE avistechnique = 'O';
UPDATE decisionspropospdos SET avistechnique = '0' WHERE avistechnique = 'N';
ALTER TABLE decisionspropospdos ALTER COLUMN avistechnique TYPE type_booleannumber USING CAST(avistechnique AS type_booleannumber);

ALTER TABLE decisionspropospdos ALTER COLUMN validationdecision TYPE text;
UPDATE decisionspropospdos SET validationdecision = '1' WHERE validationdecision = 'O';
UPDATE decisionspropospdos SET validationdecision = '0' WHERE validationdecision = 'N';
ALTER TABLE decisionspropospdos ALTER COLUMN validationdecision TYPE type_booleannumber USING CAST(validationdecision AS type_booleannumber);


-- -----------------------------------------------------------------------------
-- 20110322
-- -----------------------------------------------------------------------------

CREATE TYPE TYPE_DECISIONSUPDEFAUTEP66 AS ENUM ( 'suspensionnonrespect', 'suspensiondefaut', 'maintien' );

SELECT add_missing_table_field ('public', 'decisionsdefautsinsertionseps66', 'decisionsup', 'TYPE_DECISIONSUPDEFAUTEP66');
ALTER TABLE decisionsdefautsinsertionseps66 ALTER COLUMN decisionsup SET DEFAULT NULL;


-- *****************************************************************************
COMMIT;
-- *****************************************************************************
