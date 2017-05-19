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
-- 20130204 : ajout du champ secteurcui_id à la table cuis
-------------------------------------------------------------------------------------

DROP TABLE IF EXISTS secteurscuis CASCADE;
CREATE TABLE secteurscuis(
	id			SERIAL NOT NULL PRIMARY KEY,
	name		VARCHAR(250) NOT NULL,
	isnonmarchand	VARCHAR(1) DEFAULT '0',
	created		TIMESTAMP WITHOUT TIME ZONE,
	modified	TIMESTAMP WITHOUT TIME ZONE
);

COMMENT ON TABLE secteurscuis IS 'Liste des secteurs du CUI paramétrable pour le CUI';

DROP INDEX IF EXISTS secteurscuis_name_idx;
CREATE UNIQUE INDEX secteurscuis_name_idx ON secteurscuis( name );

SELECT alter_table_drop_constraint_if_exists( 'public', 'secteurscuis', 'secteurscuis_isnonmarchand_in_list_chk' );
ALTER TABLE secteurscuis ADD CONSTRAINT secteurscuis_isnonmarchand_in_list_chk CHECK ( cakephp_validate_in_list( isnonmarchand, ARRAY['0','1'] ) );

SELECT add_missing_table_field( 'public', 'cuis', 'secteurcui_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'cuis', 'cuis_secteurcui_id_fkey', 'secteurscuis', 'secteurcui_id', false );
DROP INDEX IF EXISTS cuis_secteurcui_id_idx;
CREATE INDEX cuis_secteurcui_id_idx ON cuis( secteurcui_id );

SELECT add_missing_table_field ( 'public', 'cuis', 'actioncandidat_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'cuis', 'cuis_actioncandidat_id_fkey', 'actionscandidats', 'actioncandidat_id', false );

SELECT add_missing_table_field ( 'public', 'cuis', 'partenaire_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'cuis', 'cuis_partenaire_id_fkey', 'partenaires', 'partenaire_id', false );

SELECT add_missing_table_field ( 'public', 'cuis', 'newemployeur', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_newemployeur_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_newemployeur_in_list_chk CHECK ( cakephp_validate_in_list( newemployeur, ARRAY['0','1'] ) );


SELECT add_missing_table_field ( 'public', 'cuis', 'isinscritpe', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_isinscritpe_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_isinscritpe_in_list_chk CHECK ( cakephp_validate_in_list( isinscritpe, ARRAY['0','1'] ) );

SELECT add_missing_table_field ('public', 'cuis', 'dureeprisecharge', 'INTEGER');
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_dureeprisecharge_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_dureeprisecharge_in_list_chk CHECK ( cakephp_validate_in_list( dureeprisecharge, ARRAY[3, 6, 9, 12] ) );

-------------------------------------------------------------------------------------
-- 20130205 : ajout de champs supplémentaires dans la table partenaires liés au CUI
-------------------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'partenaires', 'iscui', 'VARCHAR(1)');
SELECT alter_table_drop_constraint_if_exists( 'public', 'partenaires', 'partenaires_iscui_in_list_chk' );
ALTER TABLE partenaires ADD CONSTRAINT partenaires_iscui_in_list_chk CHECK ( cakephp_validate_in_list( iscui, ARRAY['0','1'] ) );
ALTER TABLE partenaires ALTER COLUMN iscui SET DEFAULT '1';
UPDATE partenaires SET iscui = '1' WHERE iscui IS NULL;
ALTER TABLE partenaires ALTER COLUMN iscui SET NOT NULL;

SELECT add_missing_table_field ('public', 'partenaires', 'canton', 'VARCHAR(250)');

SELECT add_missing_table_field ('public', 'partenaires', 'secteuractivitepartenaire_id', 'INTEGER');
SELECT add_missing_constraint ( 'public', 'partenaires', 'partenaires_secteuractivitepartenaire_id_fkey', 'codesromesecteursdsps66', 'secteuractivitepartenaire_id', false );

SELECT add_missing_table_field ( 'public', 'partenaires', 'statut', 'VARCHAR(2)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'partenaires', 'partenaires_statut_in_list_chk' );
ALTER TABLE partenaires ADD CONSTRAINT partenaires_statut_in_list_chk CHECK ( cakephp_validate_in_list( statut, ARRAY['10','11','21','22','50','60','70','71','72','73','80','90','98','99'] ) );

-- SELECT add_missing_table_field( 'public', 'partenaires', 'serviceinstructeur_id', 'INTEGER' );
-- SELECT add_missing_constraint ( 'public', 'partenaires', 'partenaires_serviceinstructeur_id_fkey', 'servicesinstructeurs', 'serviceinstructeur_id', false );
-- CREATE INDEX partenaires_serviceinstructeur_id_idx ON partenaires( serviceinstructeur_id );
SELECT alter_table_drop_column_if_exists( 'public', 'partenaires', 'serviceinstructeur_id' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'partenaires', 'partenaires_serviceinstructeur_id_fkey' );
DROP INDEX IF EXISTS partenaires_serviceinstructeur_id_idx;

SELECT add_missing_table_field ('public', 'partenaires', 'nomtiturib', 'VARCHAR(250)');
SELECT add_missing_table_field ('public', 'partenaires', 'codeban', 'VARCHAR(5)');
SELECT add_missing_table_field ('public', 'partenaires', 'guiban', 'VARCHAR(5)');
SELECT add_missing_table_field ('public', 'partenaires', 'numcompt', 'VARCHAR(11)');
SELECT add_missing_table_field ('public', 'partenaires', 'nometaban', 'VARCHAR(250)');
SELECT add_missing_table_field ('public', 'partenaires', 'clerib', 'VARCHAR(2)');

SELECT add_missing_table_field ('public', 'partenaires', 'orgrecouvcotis', 'VARCHAR(3)');
SELECT alter_table_drop_constraint_if_exists( 'public', 'partenaires', 'partenaires_orgrecouvcotis_in_list_chk' );
ALTER TABLE partenaires ADD CONSTRAINT partenaires_orgrecouvcotis_in_list_chk CHECK ( cakephp_validate_in_list( orgrecouvcotis, ARRAY['URS','MSA','AUT'] ) );

-------------------------------------------------------------------------------------------------
-- 20130312 : Ajout du champ toppersentdrodevorsa dans la table calculsdroitsrsa provenant des
--				flux bénéficiaires (type_boolean) permettant de renseigner si la personne est
--				entrante en droits et devoirs depuis le dernier flux quotidien.
------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'calculsdroitsrsa', 'toppersentdrodevorsa', 'CHAR(1)');
ALTER TABLE calculsdroitsrsa ALTER COLUMN toppersentdrodevorsa TYPE CHAR(1) USING CAST(toppersentdrodevorsa AS CHAR(1));
SELECT alter_table_drop_constraint_if_exists( 'public', 'calculsdroitsrsa', 'calculsdroitsrsa_toppersentdrodevorsa_in_list_chk' );
ALTER TABLE calculsdroitsrsa ADD CONSTRAINT calculsdroitsrsa_toppersentdrodevorsa_in_list_chk CHECK ( cakephp_validate_in_list( toppersentdrodevorsa, ARRAY['0','1'] ) );


-------------------------------------------------------------------------------------------------
-- 20130402 : Ajout du type de CUI afin de distinguer un CUI d'un CUI Emploi d'Avenir
------------------------------------------------------------------------------------------------
-- Ajout du champ permettant de distinguer un CUI normal d'un CUI Emploi d'Avenir (EAV)
SELECT add_missing_table_field ('public', 'cuis', 'typecui', 'VARCHAR(6)');
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_typecui_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_typecui_in_list_chk CHECK ( cakephp_validate_in_list( typecui, ARRAY['cui','cuieav'] ) );
ALTER TABLE cuis ALTER COLUMN typecui SET DEFAULT 'cui';
UPDATE cuis SET typecui = 'cui' WHERE typecui IS NULL;
ALTER TABLE cuis ALTER COLUMN typecui SET NOT NULL;

-- Ajout du champ pour stocker le N° SIRET de l'employeur
SELECT add_missing_table_field ('public', 'partenaires', 'siret', 'VARCHAR(14)');
DROP INDEX IF EXISTS partenaires_siret_idx;
CREATE UNIQUE INDEX partenaires_siret_idx ON partenaires( siret );

-- Ajout du champ pour stocker le code d'agrément CDIAE pour les CUI - CAE - ACI
SELECT add_missing_table_field ('public', 'cuis', 'codeagrementcdiae', 'VARCHAR(50)');

-- Ajout de la table permettant de paramétrer les raisons sociales de l'employeur d'un CUI
DROP TABLE IF EXISTS raisonssocialespartenairescuis66 CASCADE;
CREATE TABLE raisonssocialespartenairescuis66 (
	id				SERIAL NOT NULL PRIMARY KEY,
	name			VARCHAR(250) NOT NULL,
	created			TIMESTAMP WITHOUT TIME ZONE,
	modified		TIMESTAMP WITHOUT TIME ZONE
);

DROP INDEX IF EXISTS raisonssocialespartenairescuis66_name_idx;
CREATE UNIQUE INDEX raisonssocialespartenairescuis66_name_idx ON raisonssocialespartenairescuis66( name );

-- Ajout du lien entre la table partenaires (gérant les CUIs) et la raison sociale
SELECT add_missing_table_field( 'public', 'partenaires', 'raisonsocialepartenairecui66_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'partenaires', 'partenaires_raisonsocialepartenairecui66_id_fkey', 'raisonssocialespartenairescuis66', 'raisonsocialepartenairecui66_id', false );
DROP INDEX IF EXISTS partenaires_raisonsocialepartenairecui66_id_idx;
CREATE INDEX partenaires_raisonsocialepartenairecui66_id_idx ON partenaires( raisonsocialepartenairecui66_id );

-- Ajout du champ permettant de stocker la date de fin de titre de séjour si cette dernière est renseignée par l'utilisateur
SELECT add_missing_table_field ('public', 'cuis', 'datefintitresejour', 'DATE');

-- Ajout du champ permettant de distinguer les structures référentes qui gèrent les CUIs des autres
SELECT add_missing_table_field ( 'public', 'structuresreferentes', 'cui', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'structuresreferentes', 'structuresreferentes_cui_in_list_chk' );
ALTER TABLE structuresreferentes ADD CONSTRAINT structuresreferentes_cui_in_list_chk CHECK ( cakephp_validate_in_list( cui, ARRAY['N','O'] ) );
ALTER TABLE structuresreferentes ALTER COLUMN cui SET DEFAULT 'O';
UPDATE structuresreferentes SET cui = 'O' WHERE cui IS NULL;
ALTER TABLE structuresreferentes ALTER COLUMN cui SET NOT NULL;

-- Ajout du champ permettant de noter si l'ardresse de l'alocataire est en Zonr Urbaine Sensible ou en Zone de Revitalisation Rurale)
SELECT add_missing_table_field ( 'public', 'cuis', 'zoneadresseallocataire', 'VARCHAR(3)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_zoneadresseallocataire_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_zoneadresseallocataire_in_list_chk CHECK ( cakephp_validate_in_list( zoneadresseallocataire, ARRAY['zus','zrr'] ) );

-- Transformation des ENUM de la table structuresreferentes en cake_validate_in_list
-- apre
ALTER TABLE structuresreferentes ALTER COLUMN apre TYPE VARCHAR(1) USING CAST(apre AS VARCHAR(1));
SELECT alter_table_drop_constraint_if_exists( 'public', 'structuresreferentes', 'structuresreferentes_apre_in_list_chk' );
ALTER TABLE structuresreferentes ADD CONSTRAINT structuresreferentes_apre_in_list_chk CHECK ( cakephp_validate_in_list( apre, ARRAY['O','N'] ) );
ALTER TABLE structuresreferentes ALTER COLUMN apre SET DEFAULT 'O';
UPDATE structuresreferentes SET apre = 'O' WHERE apre IS NULL;
ALTER TABLE structuresreferentes ALTER COLUMN apre SET NOT NULL;

-- pdo
ALTER TABLE structuresreferentes ALTER COLUMN pdo TYPE VARCHAR(1) USING CAST(pdo AS VARCHAR(1));
SELECT alter_table_drop_constraint_if_exists( 'public', 'structuresreferentes', 'structuresreferentes_pdo_in_list_chk' );
ALTER TABLE structuresreferentes ADD CONSTRAINT structuresreferentes_pdo_in_list_chk CHECK ( cakephp_validate_in_list( pdo, ARRAY['O','N'] ) );
ALTER TABLE structuresreferentes ALTER COLUMN pdo SET DEFAULT 'O';
UPDATE structuresreferentes SET pdo = 'O' WHERE pdo IS NULL;
ALTER TABLE structuresreferentes ALTER COLUMN pdo SET NOT NULL;

-- contratengagement
ALTER TABLE structuresreferentes ALTER COLUMN contratengagement TYPE VARCHAR(1) USING CAST(contratengagement AS VARCHAR(1));
SELECT alter_table_drop_constraint_if_exists( 'public', 'structuresreferentes', 'structuresreferentes_contratengagement_in_list_chk' );
ALTER TABLE structuresreferentes ADD CONSTRAINT structuresreferentes_contratengagement_in_list_chk CHECK ( cakephp_validate_in_list( contratengagement, ARRAY['O','N'] ) );
ALTER TABLE structuresreferentes ALTER COLUMN contratengagement SET DEFAULT 'O';
UPDATE structuresreferentes SET contratengagement = 'O' WHERE contratengagement IS NULL;
ALTER TABLE structuresreferentes ALTER COLUMN contratengagement SET NOT NULL;

-- orientation
ALTER TABLE structuresreferentes ALTER COLUMN orientation TYPE VARCHAR(1) USING CAST(orientation AS VARCHAR(1));
SELECT alter_table_drop_constraint_if_exists( 'public', 'structuresreferentes', 'structuresreferentes_orientation_in_list_chk' );
ALTER TABLE structuresreferentes ADD CONSTRAINT structuresreferentes_orientation_in_list_chk CHECK ( cakephp_validate_in_list( orientation, ARRAY['O','N'] ) );
ALTER TABLE structuresreferentes ALTER COLUMN orientation SET DEFAULT 'O';
UPDATE structuresreferentes SET orientation = 'O' WHERE orientation IS NULL;
ALTER TABLE structuresreferentes ALTER COLUMN orientation SET NOT NULL;

-- active
ALTER TABLE structuresreferentes ALTER COLUMN actif TYPE VARCHAR(1) USING CAST(actif AS VARCHAR(1));
SELECT alter_table_drop_constraint_if_exists( 'public', 'structuresreferentes', 'structuresreferentes_actif_in_list_chk' );
ALTER TABLE structuresreferentes ADD CONSTRAINT structuresreferentes_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY['O','N'] ) );
ALTER TABLE structuresreferentes ALTER COLUMN actif SET DEFAULT 'O';
UPDATE structuresreferentes SET actif = 'O' WHERE actif IS NULL;
ALTER TABLE structuresreferentes ALTER COLUMN actif SET NOT NULL;

-- typestructure
ALTER TABLE structuresreferentes ALTER COLUMN typestructure TYPE VARCHAR(3) USING CAST(typestructure AS VARCHAR(3));
SELECT alter_table_drop_constraint_if_exists( 'public', 'structuresreferentes', 'structuresreferentes_typestructure_in_list_chk' );
ALTER TABLE structuresreferentes ADD CONSTRAINT structuresreferentes_typestructure_in_list_chk CHECK ( cakephp_validate_in_list( typestructure, ARRAY['oa','msp'] ) );
ALTER TABLE structuresreferentes ALTER COLUMN typestructure SET DEFAULT 'msp';
UPDATE structuresreferentes SET typestructure = 'msp' WHERE typestructure IS NULL;
ALTER TABLE structuresreferentes ALTER COLUMN typestructure SET NOT NULL;

-- Ajout d'un index unique sur le libellé de la structure car cette dernière doit être unique
DROP INDEX IF EXISTS structuresreferentes_lib_struc_idx;
CREATE UNIQUE INDEX structuresreferentes_lib_struc_idx ON structuresreferentes( lib_struc );
-- INFO: la création de l'index unique ne passera pas au CG93 car la structure referente
-- "Centre Social CAF" possède 2 entrées avec les IDs 103 et 112, une des 2 entrées doit être
-- supprimée pour que le patch passe. Il faudra donc reporter toutes les entrées de l'une sur l'autre avant la suppression.
/*
-- Toutes les colonnes ayant une clé étrangère vers la table structuresreferentes
SELECT
		DISTINCT *
	FROM (
		SELECT
				table_name AS "table",
				column_name AS "column"
			FROM information_schema.columns
			WHERE
				table_schema = 'public'
				AND column_name = 'structurereferente_id'
		UNION
		SELECT
				kcu.table_name AS "table",
				kcu.column_name AS "column"
			FROM information_schema.table_constraints tc
				LEFT JOIN information_schema.key_column_usage kcu ON ( tc.constraint_catalog = kcu.constraint_catalog AND tc.constraint_schema = kcu.constraint_schema AND tc.constraint_name = kcu.constraint_name )
				LEFT JOIN information_schema.referential_constraints rc ON ( tc.constraint_catalog = rc.constraint_catalog AND tc.constraint_schema = rc.constraint_schema AND tc.constraint_name = rc.constraint_name )
				LEFT JOIN information_schema.constraint_column_usage ccu ON ( rc.unique_constraint_catalog = ccu.constraint_catalog AND rc.unique_constraint_schema = ccu.constraint_schema AND rc.unique_constraint_name = ccu.constraint_name )
				LEFT JOIN information_schema.columns kcc ON ( kcu.table_schema = kcc.table_schema AND kcu.table_name = kcc.table_name AND kcu.column_name = kcc.column_name )
				LEFT JOIN information_schema.columns ccc ON ( ccu.table_schema = ccc.table_schema AND ccu.table_name = ccc.table_name AND ccu.column_name = ccc.column_name )
			WHERE
				ccu.table_schema = 'public'
				AND ccu.table_name = 'structuresreferentes'
				AND tc.constraint_type = 'FOREIGN KEY'
	) AS s
	ORDER BY s.table, s.column;
-- Pour chacun des résultats de la requête ci-dessus, il faudra exécuter une requête du type:
-- UPDATE apres SET structurereferente_id = 112 WHERE structurereferente_id = 103;
*/

ALTER TABLE structuresreferentes ALTER COLUMN code_insee SET NOT NULL;


-- Ajout du champ permettant de saisir l'aide complémentaire CG si contrat EAV
SELECT add_missing_table_field ('public', 'cuis', 'aidecomplementairecg', 'NUMERIC(10,2)');

-- Modification de la table secteurscuis afin de calculer les taux de chaque cuis
DROP TABLE IF EXISTS tauxcgscuis CASCADE;
CREATE TABLE tauxcgscuis (
	id					SERIAL NOT NULL PRIMARY KEY,
	secteurcui_id		INTEGER NOT NULL REFERENCES secteurscuis(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typecui				VARCHAR(6) DEFAULT NULL,
	isaci				VARCHAR(7)  DEFAULT NULL,
	tauxmin				FLOAT DEFAULT NULL,
	tauxmax				FLOAT DEFAULT NULL,
	tauxnominal			FLOAT DEFAULT NULL,
	created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);

SELECT alter_table_drop_constraint_if_exists( 'public', 'tauxcgscuis', 'tauxcgscuis_typecui_in_list_chk' );
ALTER TABLE tauxcgscuis ADD CONSTRAINT tauxcgscuis_typecui_in_list_chk CHECK ( cakephp_validate_in_list( typecui, ARRAY['cui','cuieav'] ) );
SELECT alter_table_drop_constraint_if_exists( 'public', 'tauxcgscuis', 'tauxcgscuis_isaci_in_list_chk' );
ALTER TABLE tauxcgscuis ADD CONSTRAINT tauxcgscuis_isaci_in_list_chk CHECK ( cakephp_validate_in_list( isaci, ARRAY['horsaci','enaci'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_dureeprisecharge_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_dureeprisecharge_inclusive_range_chk CHECK ( cakephp_validate_inclusive_range( dureeprisecharge, 3, 36 ) );

UPDATE cuis SET dureeprisecharge = '3' WHERE dureeprisecharge='1';
UPDATE cuis SET dureeprisecharge = '6' WHERE dureeprisecharge='2';
UPDATE cuis SET dureeprisecharge = '9' WHERE dureeprisecharge='3';
UPDATE cuis SET dureeprisecharge = '12' WHERE dureeprisecharge='4';

SELECT add_missing_table_field ('public', 'cuis', 'dureecdd', 'INTEGER');
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_dureecdd_inclusive_range_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_dureecdd_inclusive_range_chk CHECK ( cakephp_validate_inclusive_range( dureecdd, 3, 36 ) );


SELECT add_missing_table_field('public', 'proposdecisionscuis66', 'haspiecejointe', 'type_booleannumber' );
ALTER TABLE proposdecisionscuis66 ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE proposdecisionscuis66 SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE proposdecisionscuis66 ALTER COLUMN haspiecejointe SET NOT NULL;

ALTER TABLE decisionscuis66 ALTER COLUMN decisioncui TYPE VARCHAR(9) USING CAST(decisioncui AS VARCHAR(9));
SELECT alter_table_drop_constraint_if_exists( 'public', 'decisionscuis66', 'decisioncui_decisioncui_in_list_chk' );
ALTER TABLE decisionscuis66 ADD CONSTRAINT decisioncui_decisioncui_in_list_chk CHECK ( cakephp_validate_in_list( decisioncui, ARRAY['accord','refus','enattente','annule'] ) );
ALTER TABLE decisionscuis66 ALTER COLUMN decisioncui SET NOT NULL;

SELECT add_missing_table_field('public', 'decisionscuis66', 'haspiecejointe', 'type_booleannumber' );
ALTER TABLE decisionscuis66 ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE decisionscuis66 SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE decisionscuis66 ALTER COLUMN haspiecejointe SET NOT NULL;


SELECT add_missing_table_field('public', 'accompagnementscuis66', 'haspiecejointe', 'type_booleannumber' );
ALTER TABLE accompagnementscuis66 ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE accompagnementscuis66 SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE accompagnementscuis66 ALTER COLUMN haspiecejointe SET NOT NULL;

SELECT add_missing_table_field ('public', 'cuis', 'formationinterne', 'CHAR(1)');
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_formationinterne_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_formationinterne_in_list_chk CHECK ( cakephp_validate_in_list( formationinterne, ARRAY['0','1'] ) );

SELECT add_missing_table_field ('public', 'cuis', 'formationexterne', 'CHAR(1)');
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_formationexterne_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_formationexterne_in_list_chk CHECK ( cakephp_validate_in_list( formationexterne, ARRAY['0','1'] ) );

-------------------------------------------------------------------------------------------------
-- Ajout de champs supplémentaires dans la table partenaires (rev 6888)
-------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'partenaires', 'president', 'VARCHAR(100)');
SELECT add_missing_table_field ('public', 'partenaires', 'adressepresident', 'TEXT');
SELECT add_missing_table_field ('public', 'partenaires', 'directeur', 'VARCHAR(100)');
SELECT add_missing_table_field ('public', 'partenaires', 'adressedirecteur', 'TEXT');

-------------------------------------------------------------------------------------------------
-- Ajout de la date d'inscription au PE dans la table cuis (rev 6974)
-------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'cuis', 'dateinscritpe', 'DATE');

-------------------------------------------------------------------------------------------------
-- Mise à jour du champ decisioncui de la table cuis
-------------------------------------------------------------------------------------------------
ALTER TABLE cuis ALTER COLUMN decisioncui TYPE VARCHAR(9) USING CAST(decisioncui AS VARCHAR(9));
UPDATE cuis SET decisioncui = 'enattente' WHERE decisioncui = 'E';
UPDATE cuis SET decisioncui = 'accord' WHERE decisioncui = 'V';
UPDATE cuis SET decisioncui = 'refus' WHERE decisioncui = 'R';
UPDATE cuis SET decisioncui = 'refus' WHERE decisioncui = 'A';
UPDATE cuis SET decisioncui = 'annule' WHERE decisioncui = 'C';
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_decisioncui_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_decisioncui_in_list_chk CHECK ( cakephp_validate_in_list( decisioncui, ARRAY['accord','refus','enattente','annule'] ) );
ALTER TABLE cuis ALTER COLUMN decisioncui SET DEFAULT 'enattente';
ALTER TABLE cuis ALTER COLUMN decisioncui SET NOT NULL;


-------------------------------------------------------------------------------------------------
-- 20130503 : Ajout d'une valeur dans les valeurs possibles pour une orientation du CG66
-------------------------------------------------------------------------------------------------
ALTER TABLE orientsstructs ALTER COLUMN typenotification TYPE VARCHAR(15) USING CAST(typenotification AS VARCHAR(15));
SELECT alter_table_drop_constraint_if_exists( 'public', 'orientsstructs', 'orientsstructs_typenotification_in_list_chk' );
ALTER TABLE orientsstructs ADD CONSTRAINT orientsstructs_typenotification_in_list_chk CHECK ( cakephp_validate_in_list( typenotification, ARRAY['normale','systematique','dejainscritpe'] ) );
ALTER TABLE orientsstructs ALTER COLUMN typenotification SET DEFAULT 'normale';

-------------------------------------------------------------------------------------------------
-- 20130514 : Ajout de la nature de prestation RSA dans le CUI
-------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'cuis', 'naturersa', 'VARCHAR(3)');


-------------------------------------------------------------------------------------------------
-- 20130517 : Ajout d'une valeur prise par la position du CER (CG66)
-------------------------------------------------------------------------------------------------
SELECT public.alter_enumtype( 'TYPE_POSITIONCER', ARRAY['encours','attvalid','annule','fincontrat','encoursbilan','attrenouv','perime','nonvalid','perimebilanarealiser'] );

-------------------------------------------------------------------------------------------------
-- 20130527 : Modification de la coonne numconventonobj car elle prend 13 carac.
-------------------------------------------------------------------------------------------------
ALTER TABLE cuis ALTER COLUMN numconventionobj TYPE VARCHAR(13);
ALTER TABLE cuis ALTER COLUMN montantrsapercu TYPE TEXT;
ALTER TABLE cuis ALTER COLUMN naturersa TYPE TEXT;

SELECT add_missing_table_field ('public', 'cuis', 'datearrivee', 'DATE');

-------------------------------------------------------------------------------------------------
-- 20130527 : Evolution du champ poistion du CUI avec limitation de ces valeurs
-------------------------------------------------------------------------------------------------
ALTER TABLE proposdecisionscuis66 ALTER COLUMN propositioncui TYPE VARCHAR(9) USING CAST(propositioncui AS VARCHAR(9));
SELECT alter_table_drop_constraint_if_exists( 'public', 'proposdecisionscuis66', 'proposdecisionscuis66_propositioncui_in_list_chk' );
ALTER TABLE proposdecisionscuis66 ADD CONSTRAINT proposdecisionscuis66_propositioncui_in_list_chk CHECK ( cakephp_validate_in_list( propositioncui, ARRAY['enattente','accord','refus'] ) );


ALTER TABLE proposdecisionscuis66 ALTER COLUMN propositioncuielu TYPE VARCHAR(9) USING CAST(propositioncuielu AS VARCHAR(9));
SELECT alter_table_drop_constraint_if_exists( 'public', 'proposdecisionscuis66', 'proposdecisionscuis66_propositioncuielu_in_list_chk' );
ALTER TABLE proposdecisionscuis66 ADD CONSTRAINT proposdecisionscuis66_propositioncuielu_in_list_chk CHECK ( cakephp_validate_in_list( propositioncuielu, ARRAY['enattente','accord','refus'] ) );


ALTER TABLE proposdecisionscuis66 ALTER COLUMN propositioncuireferent TYPE VARCHAR(9) USING CAST(propositioncuireferent AS VARCHAR(9));
SELECT alter_table_drop_constraint_if_exists( 'public', 'proposdecisionscuis66', 'proposdecisionscuis66_propositioncuireferent_in_list_chk' );
ALTER TABLE proposdecisionscuis66 ADD CONSTRAINT proposdecisionscuis66_propositioncuireferent_in_list_chk CHECK ( cakephp_validate_in_list( propositioncuireferent, ARRAY['enattente','accord','refus'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'decisionscuis66', 'decisionscuis66_decisioncui_datedecisioncui_chk' );
ALTER TABLE decisionscuis66 aLTER COLUMN datedecisioncui DROP NOT NULL;
ALTER TABLE decisionscuis66 ADD CONSTRAINT decisionscuis66_decisioncui_datedecisioncui_chk CHECK(
	( decisioncui = 'enattente' AND datedecisioncui IS NULL )
	OR ( decisioncui <> 'enattente' AND datedecisioncui IS NOT NULL )
);

-------------------------------------------------------------------------------------------------
-- 20130527 : Modification de la valeur du champ typetraitement dans la table traitementspcgs66
-------------------------------------------------------------------------------------------------
ALTER TABLE traitementspcgs66 ALTER COLUMN typetraitement TYPE VARCHAR(14) USING CAST(typetraitement AS VARCHAR(14));
UPDATE traitementspcgs66 SET typetraitement = 'dossierarevoir' WHERE typetraitement = 'analyse';
UPDATE traitementspcgs66 SET typetraitement = 'documentarrive' WHERE typetraitement = 'aucun';
UPDATE traitementspcgs66 SET typetraitement = 'courrier' WHERE typetraitement = 'courrier';
UPDATE traitementspcgs66 SET typetraitement = 'revenu' WHERE typetraitement = 'revenu';
SELECT alter_table_drop_constraint_if_exists( 'public', 'traitementspcgs66', 'traitementspcgs66_typetraitement_in_list_chk' );
ALTER TABLE traitementspcgs66 ADD CONSTRAINT traitementspcgs66_typetraitement_in_list_chk CHECK ( cakephp_validate_in_list( typetraitement, ARRAY['courrier','revenu','dossierarevoir','documentarrive'] ) );

-- Renommage de la colonne ficheanalyse en dossierarevoir dans la table traitementspcgs66
SELECT public.alter_columnname_ifexists ('public', 'traitementspcgs66', 'ficheanalyse', 'dossierarevoir');

-------------------------------------------------------------------------------------------------
-- 20130527 : Modification de la valeur du champ etatdossierpcg de la table dossierspcgs66
--              avec ajout des valeurs pour le nouvel état du dossier PCG
-------------------------------------------------------------------------------------------------
SELECT alter_table_drop_constraint_if_exists( 'public', 'dossierspcgs66', 'dossierspcgs66_etatdossierpcg_in_list_chk' );
ALTER TABLE dossierspcgs66 ADD CONSTRAINT dossierspcgs66_etatdossierpcg_in_list_chk CHECK ( cakephp_validate_in_list( etatdossierpcg, ARRAY['attaffect', 'attinstr', 'instrencours', 'attavistech', 'attval', 'dossiertraite', 'decisionvalid', 'decisionnonvalid', 'decisionnonvalidretouravis', 'decisionvalidretouravis', 'transmisop', 'atttransmisop', 'annule', 'attinstrattpiece', 'attinstrdocarrive'] ) );

-------------------------------------------------------------------------------------------------
-- 20130530 : Ajout d'une table de paramétrage pour les organismes auxquels les dossiers sont transmis
-------------------------------------------------------------------------------------------------
-- Table de paramétrage permettant de définir à quel organisme le dossier PCG est transmis
DROP TABLE IF EXISTS orgstransmisdossierspcgs66 CASCADE;
CREATE TABLE orgstransmisdossierspcgs66(
	id			SERIAL NOT NULL PRIMARY KEY,
	name		VARCHAR(250) NOT NULL,
	created		TIMESTAMP WITHOUT TIME ZONE,
	modified	TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE orgstransmisdossierspcgs66 IS 'Liste des organismes auxquels sera transmis le dossier PCG une fois ce dernier traité';

DROP INDEX IF EXISTS orgstransmisdossierspcgs66_name_idx;
CREATE UNIQUE INDEX orgstransmisdossierspcgs66_name_idx ON orgstransmisdossierspcgs66( name );

SELECT add_missing_table_field( 'public', 'decisionsdossierspcgs66', 'orgtransmisdossierpcg66_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'decisionsdossierspcgs66', 'decisionsdossierspcgs66_orgtransmisdossierpcg66_id_fkey', 'orgstransmisdossierspcgs66', 'orgtransmisdossierpcg66_id', false );
DROP INDEX IF EXISTS decisionsdossierspcgs66_orgtransmisdossierpcg66_id_idx;
CREATE INDEX decisionsdossierspcgs66_orgtransmisdossierpcg66_id_idx ON decisionsdossierspcgs66( orgtransmisdossierpcg66_id );

-- Table de liaison pour stocker les différents organismes
DROP TABLE IF EXISTS decsdospcgs66_orgsdospcgs66 CASCADE;
CREATE TABLE decsdospcgs66_orgsdospcgs66 (
    id                 					SERIAL NOT NULL PRIMARY KEY,
    decisiondossierpcg66_id			INTEGER NOT NULL REFERENCES decisionsdossierspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
    orgtransmisdossierpcg66_id					INTEGER NOT NULL REFERENCES orgstransmisdossierspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
    created								TIMESTAMP WITHOUT TIME ZONE,
	modified							TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE decsdospcgs66_orgsdospcgs66 IS 'Table de liaison entre les décisions de dossiers pcgs et les organismes auxquels les dossiers traités ont été transmis (PCG66)';
DROP INDEX IF EXISTS decsdospcgs66_orgsdospcgs66_decisiondossierpcg66_id_idx;
CREATE INDEX decsdospcgs66_orgsdospcgs66_decisiondossierpcg66_id_idx ON decsdospcgs66_orgsdospcgs66(decisiondossierpcg66_id);

DROP INDEX IF EXISTS decsdospcgs66_orgsdospcgs66_orgtransmisdossierpcg66_id_idx;
CREATE INDEX decsdospcgs66_orgsdospcgs66_orgtransmisdossierpcg66_id_idx ON decsdospcgs66_orgsdospcgs66(orgtransmisdossierpcg66_id);

DROP INDEX IF EXISTS decsdospcgs66_orgsdospcgs66_decisiondossierpcg66_id_orgtransmisdossierpcg66_id_idx;
CREATE UNIQUE INDEX decsdospcgs66_orgsdospcgs66_decisiondossierpcg66_id_orgtransmisdossierpcg66_id_idx ON decsdospcgs66_orgsdospcgs66(decisiondossierpcg66_id,orgtransmisdossierpcg66_id);

-------------------------------------------------------------------------------------------------
-- 20130531 : Remplissage de la table de liaison entre les décisions et les organismes
-------------------------------------------------------------------------------------------------
INSERT INTO orgstransmisdossierspcgs66 ( name ) VALUES ( 'CAF' ); -- FIXME peut ne pas être nécessaire si le paramétrage est fait

-- Remplissage de la table de liaison afin de ne pas perdre les décisions émises prélablement sur els dossiers existants
INSERT INTO decsdospcgs66_orgsdospcgs66 ( decisiondossierpcg66_id, orgtransmisdossierpcg66_id )
	( SELECT
			id AS decisiondossierpcg66_id,
			( SELECT id FROM orgstransmisdossierspcgs66 WHERE name = 'CAF' ) AS orgtransmisdossierpcg66_id
		FROM decisionsdossierspcgs66
		WHERE
			decisionsdossierspcgs66.dossierpcg66_id IN (
				SELECT id FROM dossierspcgs66 WHERE etatdossierpcg = 'transmisop'
			)
			AND decisionsdossierspcgs66.datetransmissionop IS NOT NULL
		ORDER BY decisionsdossierspcgs66.id
	);


-------------------------------------------------------------------------------------------------
-- 20130605 : Ajout du champ email dans la table servicesinstructeurs
-------------------------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'servicesinstructeurs', 'email', 'VARCHAR(250)' );
ALTER TABLE servicesinstructeurs ALTER COLUMN email SET DEFAULT NULL;

--------------------------------------------------------------------------------
-- 20130530 - Premier jet du formulaire d'entrée D1 (CG 93)
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS situationsallocataires CASCADE;
CREATE TABLE situationsallocataires (
    id                  SERIAL NOT NULL PRIMARY KEY,
    personne_id         INTEGER NOT NULL REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE,
    -- 1°) Champs directement liés à la personne
    qual                VARCHAR(3) DEFAULT NULL,     -- personnes.qual
    nom                 VARCHAR(50) DEFAULT NULL,    -- personnes.nom
    prenom              VARCHAR(50) DEFAULT NULL,    -- personnes.prenom
    nomnai              VARCHAR(50) DEFAULT NULL,    -- personnes.nomnai
    nir                 VARCHAR(15) DEFAULT NULL,    -- personnes.nir
    sexe                VARCHAR(1) DEFAULT NULL,     -- personnes.sexe
    dtnai               DATE DEFAULT NULL,           -- personnes.dtnai
    rolepers            VARCHAR(3) DEFAULT NULL,     -- prestations.rolepers
    toppersdrodevorsa   VARCHAR(1) DEFAULT NULL,     -- calculsdroitsrsa.toppersdrodevorsa
    nati                VARCHAR(1) DEFAULT NULL,     -- personnes.nati
	identifiantpe       VARCHAR(11) DEFAULT NULL,    -- historiqueetatspe.identifiantpe
	datepe              DATE DEFAULT NULL,           -- historiqueetatspe.date
	etatpe              VARCHAR(15) DEFAULT NULL,    -- historiqueetatspe.etat
	codepe              VARCHAR(2) DEFAULT NULL,     -- historiqueetatspe.code
	motifpe             VARCHAR(250) DEFAULT NULL,   -- historiqueetatspe.motif
    -- 2°) Champs indirectement liés à la personne via le foyer RSA, ...
    -- 2°) a°) Adresse (de rang 01) de l'allocataire
    numvoie             VARCHAR(6) DEFAULT NULL,     -- adresses.numvoie
    typevoie            VARCHAR(4) DEFAULT NULL,     -- adresses.typevoie
    nomvoie             VARCHAR(25) DEFAULT NULL,    -- adresses.nomvoie
    complideadr         VARCHAR(38) DEFAULT NULL,    -- adresses.complideadr
    compladr            VARCHAR(26) DEFAULT NULL,    -- adresses.compladr
    numcomptt           VARCHAR(5) DEFAULT NULL,     -- adresses.numcomptt
    numcomrat           VARCHAR(5) DEFAULT NULL,     -- adresses.numcomrat
    codepos             VARCHAR(5) DEFAULT NULL,     -- adresses.codepos
    locaadr             VARCHAR(26) DEFAULT NULL,     -- adresses.locaadr
    -- 2°) b°) Dossier, foyer, situation du dossier de l'allocataire
    numdemrsa           VARCHAR(11) DEFAULT NULL,    -- dossiers.numdemrsa
    matricule           VARCHAR(15) DEFAULT NULL,    -- dossiers.matricule
    fonorg              VARCHAR(3) DEFAULT NULL,     -- dossiers.fonorg
    etatdosrsa          VARCHAR(1) DEFAULT NULL,     -- situationsdossiersrsa.etatdosrsa
    sitfam              VARCHAR(3) DEFAULT NULL,     -- foyers.sitfam
    nbenfants           INTEGER DEFAULT 0,           -- personnes.prestations.rolepers = 'ENF' du foyer
    dtdemrsa            DATE DEFAULT NULL,           -- dossiers.dtdemrsa
    dtdemrmi            DATE DEFAULT NULL,           -- dossiers.dtdemrmi
    statudemrsa         VARCHAR(1) DEFAULT NULL,     -- dossiers.statudemrsa
    numdepins           VARCHAR(3) DEFAULT NULL,     -- suivisinstruction.numdepins
    typeserins          VARCHAR(1) DEFAULT NULL,     -- suivisinstruction.typeserins
    numcomins           VARCHAR(3) DEFAULT NULL,     -- suivisinstruction.numcomins
    numagrins           INTEGER DEFAULT NULL,        -- suivisinstruction.numagrins
    -- 2°) c°) Autres indirectement liés au dossier / foyer de l'allocataire
--     natpf_serialize     VARCHAR(250) DEFAULT NULL,   -- tous les detailscalculsdroitsrsa.natpf avec leur montant, serialisé
    natpf_socle         VARCHAR(1) DEFAULT NULL,     -- detailscalculsdroitsrsa.natpf IN ...
    natpf_majore        VARCHAR(1) DEFAULT NULL,     -- detailscalculsdroitsrsa.natpf IN ...
    natpf_activite      VARCHAR(1) DEFAULT NULL,     -- detailscalculsdroitsrsa.natpf IN ...
    -- 3°) Divers
    created             TIMESTAMP WITHOUT TIME ZONE,
    modified            TIMESTAMP WITHOUT TIME ZONE
);

SELECT alter_table_drop_constraint_if_exists( 'public', 'situationsallocataires', 'situationsallocataires_qual_in_list_chk' );
ALTER TABLE situationsallocataires ADD CONSTRAINT situationsallocataires_qual_in_list_chk CHECK ( cakephp_validate_in_list( qual, ARRAY['MR', 'MME', 'MLE'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'situationsallocataires', 'situationsallocataires_sexe_in_list_chk' );
ALTER TABLE situationsallocataires ADD CONSTRAINT situationsallocataires_sexe_in_list_chk CHECK ( cakephp_validate_in_list( sexe, ARRAY['1', '2'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'situationsallocataires', 'situationsallocataires_rolepers_in_list_chk' );
ALTER TABLE situationsallocataires ADD CONSTRAINT situationsallocataires_rolepers_in_list_chk CHECK ( cakephp_validate_in_list( rolepers, ARRAY['AUT', 'CJT', 'DEM', 'ENF', 'RDO'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'situationsallocataires', 'situationsallocataires_toppersdrodevorsa_in_list_chk' );
ALTER TABLE situationsallocataires ADD CONSTRAINT situationsallocataires_toppersdrodevorsa_in_list_chk CHECK ( cakephp_validate_in_list( toppersdrodevorsa, ARRAY['0', '1'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'situationsallocataires', 'situationsallocataires_nati_in_list_chk' );
ALTER TABLE situationsallocataires ADD CONSTRAINT situationsallocataires_nati_in_list_chk CHECK ( cakephp_validate_in_list( nati, ARRAY['A', 'C', 'F'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'situationsallocataires', 'situationsallocataires_etatpe_in_list_chk' );
ALTER TABLE situationsallocataires ADD CONSTRAINT situationsallocataires_etatpe_in_list_chk CHECK ( cakephp_validate_in_list( etatpe, ARRAY['cessation', 'inscription', 'radiation'] ) );

-- codepe

SELECT alter_table_drop_constraint_if_exists( 'public', 'situationsallocataires', 'situationsallocataires_typevoie_in_list_chk' );
ALTER TABLE situationsallocataires ADD CONSTRAINT situationsallocataires_typevoie_in_list_chk CHECK ( cakephp_validate_in_list( typevoie, ARRAY['ABE', 'ACH', 'AGL', 'AIRE', 'ALL', 'ANSE', 'ARC', 'ART', 'AUT', 'AV', 'BAST', 'BCH', 'BCLE', 'BD', 'BEGI', 'BER', 'BOIS', 'BRE', 'BRG', 'BSTD', 'BUT', 'CALE', 'CAMP', 'CAR', 'CARE', 'CARR', 'CAU', 'CAV', 'CGNE', 'CHE', 'CHEM', 'CHEZ', 'CHI', 'CHL', 'CHP', 'CHS', 'CHT', 'CHV', 'CITE', 'CLOI', 'CLOS', 'COL', 'COLI', 'COR', 'COTE', 'COTT', 'COUR', 'CPG', 'CRS', 'CST', 'CTR', 'CTRE', 'DARS', 'DEG', 'DIG', 'DOM', 'DSC', 'ECL', 'EGL', 'EN', 'ENC', 'ENV', 'ESC', 'ESP', 'ESPA', 'ETNG', 'FG', 'FON', 'FORM', 'FORT', 'FOS', 'FOYR', 'FRM', 'GAL', 'GARE', 'GARN', 'GBD', 'GDEN', 'GPE', 'GPT', 'GR', 'GRI', 'GRIM', 'HAM', 'HCH', 'HIP', 'HLE', 'HLM', 'ILE', 'IMM', 'IMP', 'JARD', 'JTE', 'LD', 'LEVE', 'LOT', 'MAIL', 'MAN', 'MAR', 'MAS', 'MET', 'MF', 'MLN', 'MTE', 'MUS', 'NTE', 'PAE', 'PAL', 'PARC', 'PAS', 'PASS', 'PAT', 'PAV', 'PCH', 'PERI', 'PIM', 'PKG', 'PL', 'PLAG', 'PLAN', 'PLCI', 'PLE', 'PLN', 'PLT', 'PN', 'PNT', 'PONT', 'PORQ', 'PORT', 'POT', 'POUR', 'PRE', 'PROM', 'PRQ', 'PRT', 'PRV', 'PSTY', 'PTA', 'PTE', 'PTR', 'QU', 'QUA', 'R', 'RAC', 'RAID', 'REM', 'RES', 'RLE', 'ROC', 'ROQT', 'RPE', 'RPT', 'RTD', 'RTE', 'SEN', 'SQ', 'STA', 'STDE', 'TOUR', 'TPL', 'TRA', 'TRN', 'TRT', 'TSSE', 'VAL', 'VCHE', 'VEN', 'VGE', 'VIA', 'VLA', 'VOI', 'VTE', 'ZA', 'ZAC', 'ZAD', 'ZI', 'ZONE', 'ZUP'] ) );

-- fonorg

SELECT alter_table_drop_constraint_if_exists( 'public', 'situationsallocataires', 'situationsallocataires_etatdosrsa_in_list_chk' );
ALTER TABLE situationsallocataires ADD CONSTRAINT situationsallocataires_etatdosrsa_in_list_chk CHECK ( cakephp_validate_in_list( etatdosrsa, ARRAY['Z', '0', '1', '2', '3', '4', '5', '6'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'situationsallocataires', 'situationsallocataires_sitfam_in_list_chk' );
ALTER TABLE situationsallocataires ADD CONSTRAINT situationsallocataires_sitfam_in_list_chk CHECK ( cakephp_validate_in_list( sitfam, ARRAY['ABA', 'CEL', 'DIV', 'ISO', 'MAR', 'PAC', 'RPA', 'RVC', 'RVM', 'SEF', 'SEL', 'VEU', 'VIM'] ) );

-- statudemrsa

-- user_id
-- nivetu
-- natlog

DROP TABLE IF EXISTS questionnairesd1pdvs93 CASCADE;
CREATE TABLE questionnairesd1pdvs93 (
    id                          SERIAL NOT NULL PRIMARY KEY,
    personne_id                 INTEGER NOT NULL REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	rendezvous_id               INTEGER NOT NULL REFERENCES rendezvous(id) ON DELETE CASCADE ON UPDATE CASCADE,
	situationallocataire_id     INTEGER NOT NULL REFERENCES situationsallocataires(id) ON DELETE CASCADE ON UPDATE CASCADE,
	-- Champs spécifiques à ce formulaire
    inscritpe                   VARCHAR(1) DEFAULT NULL,
	marche_travail              VARCHAR(25) DEFAULT NULL,
	vulnerable                  VARCHAR(10) DEFAULT NULL,
	diplomes_etrangers          VARCHAR(1) DEFAULT NULL,
	categorie_sociopro          VARCHAR(25) DEFAULT NULL,
    nivetu                      VARCHAR(4) DEFAULT NULL, -- origine: le plus récent de: dsps.nivetu/dsps_revs/cers93
	autre_caracteristique		VARCHAR(25) DEFAULT NULL,
	autre_caracteristique_autre	VARCHAR(250) DEFAULT NULL,
	conditions_logement         VARCHAR(25) DEFAULT NULL,
	conditions_logement_autre   VARCHAR(250) DEFAULT NULL,
	-- Validation du formulaire
	valide        	            VARCHAR(1) NOT NULL DEFAULT '0',
	date_validation	            DATE DEFAULT NULL,
	----------------------------------------------------------------------------
    created                     TIMESTAMP WITHOUT TIME ZONE,
    modified                    TIMESTAMP WITHOUT TIME ZONE
);

CREATE UNIQUE INDEX questionnairesd1pdvs93_rendezvous_id_idx ON questionnairesd1pdvs93( rendezvous_id );
CREATE UNIQUE INDEX questionnairesd1pdvs93_situationallocataire_id_idx ON questionnairesd1pdvs93( situationallocataire_id );

SELECT alter_table_drop_constraint_if_exists( 'public', 'questionnairesd1pdvs93', 'questionnairesd1pdvs93_inscritpe_in_list_chk' );
ALTER TABLE questionnairesd1pdvs93 ADD CONSTRAINT questionnairesd1pdvs93_inscritpe_in_list_chk CHECK ( cakephp_validate_in_list( inscritpe, ARRAY['0', '1'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'questionnairesd1pdvs93', 'questionnairesd1pdvs93_marche_travail_in_list_chk' );
ALTER TABLE questionnairesd1pdvs93 ADD CONSTRAINT questionnairesd1pdvs93_marche_travail_in_list_chk CHECK ( cakephp_validate_in_list( marche_travail, ARRAY['actif_non_independant', 'actif_independant', 'chomeur', 'chomeur_longue_duree', 'inactif_hors_formation', 'inactif_en_formation'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'questionnairesd1pdvs93', 'questionnairesd1pdvs93_vulnerable_in_list_chk' );
ALTER TABLE questionnairesd1pdvs93 ADD CONSTRAINT questionnairesd1pdvs93_vulnerable_in_list_chk CHECK ( cakephp_validate_in_list( vulnerable, ARRAY['migrant', 'minorite', 'handicape', 'autre'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'questionnairesd1pdvs93', 'questionnairesd1pdvs93_diplomes_etrangers_in_list_chk' );
ALTER TABLE questionnairesd1pdvs93 ADD CONSTRAINT questionnairesd1pdvs93_diplomes_etrangers_in_list_chk CHECK ( cakephp_validate_in_list( diplomes_etrangers, ARRAY['0', '1'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'questionnairesd1pdvs93', 'questionnairesd1pdvs93_categorie_sociopro_in_list_chk' );
ALTER TABLE questionnairesd1pdvs93 ADD CONSTRAINT questionnairesd1pdvs93_categorie_sociopro_in_list_chk CHECK ( cakephp_validate_in_list( categorie_sociopro, ARRAY['agriculteur_exploitant', 'artisan_commercant_chef', 'cadre_intellectuel', 'niveau_intermediaire', 'employe', 'ouvrier', 'retraite', 'autre'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'questionnairesd1pdvs93', 'questionnairesd1pdvs93_nivetu_in_list_chk' );
ALTER TABLE questionnairesd1pdvs93 ADD CONSTRAINT questionnairesd1pdvs93_nivetu_in_list_chk CHECK ( cakephp_validate_in_list( nivetu, ARRAY['1201', '1202', '1203', '1204', '1205', '1206', '1207'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'questionnairesd1pdvs93', 'questionnairesd1pdvs93_autre_caracteristique_in_list_chk' );
ALTER TABLE questionnairesd1pdvs93 ADD CONSTRAINT questionnairesd1pdvs93_autre_caracteristique_in_list_chk CHECK ( cakephp_validate_in_list( autre_caracteristique, ARRAY['beneficiaire_minimas', 'contrat_aide', 'jeunes_total', 'jeunes_zus', 'jeunes_hadicapes', 'autres'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'questionnairesd1pdvs93', 'questionnairesd1pdvs93_conditions_logement_in_list_chk' );
ALTER TABLE questionnairesd1pdvs93 ADD CONSTRAINT questionnairesd1pdvs93_conditions_logement_in_list_chk CHECK ( cakephp_validate_in_list( conditions_logement, ARRAY['proprietaire', 'locataire_hlm', 'locataire_non_hlm', 'locataire_hotel_meuble', 'gratuitement', 'heberge', 'foyer', 'centre_accueil', 'mobile', 'fortune', 'sans', 'autre'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'questionnairesd1pdvs93', 'questionnairesd1pdvs93_valide_in_list_chk' );
ALTER TABLE questionnairesd1pdvs93 ADD CONSTRAINT questionnairesd1pdvs93_valide_in_list_chk CHECK ( cakephp_validate_in_list( valide, ARRAY['0', '1'] ) );

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
