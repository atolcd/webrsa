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
-- INFO: attention à ne pas passer ce morceau plusieurs fois!
--------------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION public.update_duree_engag_integer( p_table TEXT ) RETURNS BOOLEAN AS
$$
	DECLARE
		v_query text;
	BEGIN
		v_query := 'UPDATE ' || p_table || '
			SET duree_engag =
				CASE
					WHEN duree_engag = 6 THEN 24
					WHEN duree_engag = 5 THEN 18
					WHEN duree_engag = 4 THEN 12
					WHEN duree_engag = 3 THEN 9
					WHEN duree_engag = 2 THEN 6
					WHEN duree_engag = 1 THEN 3
					ELSE 999
				END
			WHERE duree_engag IS NOT NULL;';

		RAISE NOTICE  '%', v_query;
		EXECUTE v_query;

		RETURN true;
	END;
$$
LANGUAGE plpgsql;

-- On met à jour les durées SSI la table version n'existe pas (le patch 2.9.0 n'a pas encore été passé)
SELECT
		EXISTS(SELECT * FROM information_schema.columns WHERE table_name = 'version')
		OR (
			( SELECT public.update_duree_engag_integer( 'bilansparcours66' ) )
			AND ( SELECT public.update_duree_engag_integer( 'contratsinsertion' ) )
			AND ( SELECT public.update_duree_engag_integer( 'proposcontratsinsertioncovs58' ) )
			AND ( SELECT public.update_duree_engag_integer( 'decisionsproposcontratsinsertioncovs58' ) )
		);

DROP FUNCTION public.update_duree_engag_integer( p_table TEXT );

--------------------------------------------------------------------------------
-- 20150403: Création de deux nouvelles thématiques de COV pour le CG 58:
--           nonorientationsproscovs58 et regressionsorientationscovs58.
-- -> decisionsnonorientationsproscovs58, decisionsregressionsorientationscovs58
--------------------------------------------------------------------------------

SELECT alter_enumtype( 'TYPE_THEMECOV58', ARRAY['proposorientationscovs58','proposcontratsinsertioncovs58','proposnonorientationsproscovs58','proposorientssocialescovs58','nonorientationsproscovs58','regressionsorientationscovs58']);

DELETE FROM themescovs58 WHERE name IN ( 'nonorientationsproscovs58', 'regressionsorientationscovs58' );
INSERT INTO themescovs58 ( name ) VALUES
	( 'nonorientationsproscovs58' ),
	( 'regressionsorientationscovs58' );

SELECT add_missing_table_field ( 'public', 'themescovs58', 'nonorientationprocov58', 'TYPE_ETAPECOV' );
SELECT add_missing_table_field ( 'public', 'themescovs58', 'regressionorientationcov58', 'TYPE_ETAPECOV' );

--==============================================================================

DROP TABLE IF EXISTS nonorientationsproscovs58 CASCADE;
CREATE TABLE nonorientationsproscovs58 (
    id					SERIAL NOT NULL PRIMARY KEY,
	dossiercov58_id		INTEGER NOT NULL REFERENCES dossierscovs58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	orientstruct_id		INTEGER NOT NULL REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	user_id				INTEGER DEFAULT NULL REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
	nvorientstruct_id	INTEGER DEFAULT NULL REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
    created				TIMESTAMP WITHOUT TIME ZONE,
    modified			TIMESTAMP WITHOUT TIME ZONE
);

CREATE UNIQUE INDEX nonorientationsproscovs58_dossiercov58_id_idx ON nonorientationsproscovs58(dossiercov58_id);
CREATE INDEX nonorientationsproscovs58_orientstruct_id_idx ON nonorientationsproscovs58(orientstruct_id);
CREATE INDEX nonorientationsproscovs58_user_id_idx ON nonorientationsproscovs58(user_id);
CREATE INDEX nonorientationsproscovs58_nvorientstruct_id_idx ON nonorientationsproscovs58(nvorientstruct_id);

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS decisionsnonorientationsproscovs58;
CREATE TABLE decisionsnonorientationsproscovs58 (
	id						SERIAL NOT NULL PRIMARY KEY,
	passagecov58_id			INTEGER NOT NULL REFERENCES passagescovs58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	etapecov				VARCHAR(10) NOT NULL,
	decisioncov				VARCHAR(15) NOT NULL,
	typeorient_id			INTEGER DEFAULT NULL REFERENCES typesorients(id) ON DELETE CASCADE ON UPDATE CASCADE,
	structurereferente_id	INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	referent_id				INTEGER DEFAULT NULL REFERENCES referents(id) ON DELETE CASCADE ON UPDATE CASCADE,
	datevalidation			DATE,
	commentaire				TEXT DEFAULT NULL,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);

ALTER TABLE decisionsnonorientationsproscovs58 ADD CONSTRAINT decisionsnonorientationsproscovs58_etapecov_in_list_chk CHECK ( cakephp_validate_in_list( etapecov, ARRAY['cree','traitement','ajourne','finalise'] ) );
ALTER TABLE decisionsnonorientationsproscovs58 ADD CONSTRAINT decisionsnonorientationsproscovs58_decisioncov_in_list_chk CHECK ( cakephp_validate_in_list( decisioncov, ARRAY['reorientation','maintienref','annule','reporte'] ) );

CREATE INDEX decisionsnonorientationsproscovs58_passagecov58_id_idx ON decisionsnonorientationsproscovs58( passagecov58_id );
CREATE INDEX decisionsnonorientationsproscovs58_etapecov_idx ON decisionsnonorientationsproscovs58( etapecov );
CREATE INDEX decisionsnonorientationsproscovs58_decisioncov_idx ON decisionsnonorientationsproscovs58( decisioncov );
CREATE UNIQUE INDEX decisionsnonorientationsproscovs58_passagecov58_id_etapecov_idx ON decisionsnonorientationsproscovs58(passagecov58_id, etapecov);

--==============================================================================

DROP TABLE IF EXISTS regressionsorientationscovs58 CASCADE;
CREATE TABLE regressionsorientationscovs58 (
    id						SERIAL NOT NULL PRIMARY KEY,
	dossiercov58_id			INTEGER NOT NULL REFERENCES dossierscovs58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	orientstruct_id			INTEGER NOT NULL REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typeorient_id			INTEGER NOT NULL REFERENCES typesorients(id) ON DELETE CASCADE ON UPDATE CASCADE,
	structurereferente_id	INTEGER NOT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	referent_id				INTEGER DEFAULT NULL REFERENCES referents(id) ON DELETE CASCADE ON UPDATE CASCADE,
	datedemande				DATE NOT NULL,
	user_id					INTEGER DEFAULT NULL REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
	nvorientstruct_id		INTEGER DEFAULT NULL REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
    created					TIMESTAMP WITHOUT TIME ZONE,
    modified				TIMESTAMP WITHOUT TIME ZONE
);

CREATE UNIQUE INDEX regressionsorientationscovs58_dossiercov58_id_idx ON regressionsorientationscovs58(dossiercov58_id);
CREATE INDEX regressionsorientationscovs58_orientstruct_id_idx ON regressionsorientationscovs58(orientstruct_id);
CREATE INDEX regressionsorientationscovs58_typeorient_id_idx ON regressionsorientationscovs58(typeorient_id);
CREATE INDEX regressionsorientationscovs58_structurereferente_id_idx ON regressionsorientationscovs58(structurereferente_id);
CREATE INDEX regressionsorientationscovs58_referent_id_idx ON regressionsorientationscovs58(referent_id);
CREATE INDEX regressionsorientationscovs58_user_id_idx ON regressionsorientationscovs58(user_id);
CREATE INDEX regressionsorientationscovs58_nvorientstruct_id_idx ON regressionsorientationscovs58(nvorientstruct_id);

--------------------------------------------------------------------------------

DROP TABLE IF EXISTS decisionsregressionsorientationscovs58;
CREATE TABLE decisionsregressionsorientationscovs58 (
	id						SERIAL NOT NULL PRIMARY KEY,
	passagecov58_id			INTEGER NOT NULL REFERENCES passagescovs58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	etapecov				VARCHAR(10) NOT NULL,
	decisioncov				VARCHAR(15) NOT NULL,
	typeorient_id			INTEGER DEFAULT NULL REFERENCES typesorients(id) ON DELETE CASCADE ON UPDATE CASCADE,
	structurereferente_id	INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	referent_id				INTEGER DEFAULT NULL REFERENCES referents(id) ON DELETE CASCADE ON UPDATE CASCADE,
	datevalidation			DATE,
	commentaire				TEXT DEFAULT NULL,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);

ALTER TABLE decisionsregressionsorientationscovs58 ADD CONSTRAINT decisionsregressionsorientationscovs58_etapecov_in_list_chk CHECK ( cakephp_validate_in_list( etapecov, ARRAY['cree','traitement','ajourne','finalise'] ) );
ALTER TABLE decisionsregressionsorientationscovs58 ADD CONSTRAINT decisionsregressionsorientationscovs58_decisioncov_in_list_chk CHECK ( cakephp_validate_in_list( decisioncov, ARRAY['accepte','refuse','annule','reporte'] ) );

CREATE INDEX decisionsregressionsorientationscovs58_passagecov58_id_idx ON decisionsregressionsorientationscovs58( passagecov58_id );
CREATE INDEX decisionsregressionsorientationscovs58_etapecov_idx ON decisionsregressionsorientationscovs58( etapecov );
CREATE INDEX decisionsregressionsorientationscovs58_decisioncov_idx ON decisionsregressionsorientationscovs58( decisioncov );
CREATE UNIQUE INDEX decisionsregressionsorientationscovs58_passagecov58_id_etapecov_idx ON decisionsregressionsorientationscovs58(passagecov58_id, etapecov);

-- *****************************************************************************
-- Version
-- *****************************************************************************

DROP TABLE IF EXISTS version;
CREATE TABLE version
(
	webrsa VARCHAR(255)
);
INSERT INTO version(webrsa) VALUES ('2.9.0');

--------------------------------------------------------------------------------
-- 20150407: CG 66, ajout d'une valeur d'enum pour les decisions EP
--------------------------------------------------------------------------------

SELECT alter_enumtype('TYPE_DECISIONDEFAUTINSERTIONEP66', ARRAY['suspensionnonrespect', 'suspensiondefaut', 'suspensionsanction', 'maintien', 'maintienorientsoc', 'reorientationprofverssoc', 'reorientationsocversprof', 'annule', 'reporte']);

--------------------------------------------------------------------------------
-- 20150420: ajout de la règle de validation "comparison"
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION cakephp_validate_comparison( p_check1 float,p_operator text,p_check2 float ) RETURNS boolean AS
$$
	BEGIN
		RETURN p_check1 IS NULL
			OR(
				p_operator IS NOT NULL
				AND p_check2 IS NOT NULL
				AND (
					( p_operator IN ( '>', 'is greater' ) AND p_check1 > p_check2 )
					OR ( p_operator IN ( '>=', 'greater or equal' ) AND p_check1 >= p_check2 )
					OR ( p_operator IN ( '==', 'equal to' ) AND p_check1 = p_check2 )
					OR ( p_operator IN ( '!=', 'not equal' ) AND p_check1 <> p_check2 )
					OR ( p_operator IN ( '<', 'is less' ) AND p_check1 < p_check2 )
					OR ( p_operator IN ( '<=', 'less or equal' ) AND p_check1 <= p_check2 )
				)
			);
	END;
$$
LANGUAGE 'plpgsql' IMMUTABLE;

COMMENT ON FUNCTION cakephp_validate_comparison( p_check1 float,p_operator text,p_check2 float ) IS
	'@see http://api.cakephp.org/2.2/class-Validation.html#_comparison';

--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION cakephp_validate_compare_dates( p_check1 TIMESTAMP, p_check2 TIMESTAMP, p_comparator text ) RETURNS boolean AS
$$
	BEGIN
		RETURN ( p_check1 IS NULL OR p_check2 IS NULL )
			OR(
				p_comparator IS NOT NULL
				AND p_check2 IS NOT NULL
				AND (
					( p_comparator IN ( '>', 'is greater' ) AND p_check1 > p_check2 )
					OR ( p_comparator IN ( '>=', 'greater or equal' ) AND p_check1 >= p_check2 )
					OR ( p_comparator IN ( '==', 'equal to' ) AND p_check1 = p_check2 )
					OR ( p_comparator IN ( '<', 'is less' ) AND p_check1 < p_check2 )
					OR ( p_comparator IN ( '<=', 'less or equal' ) AND p_check1 <= p_check2 )
				)
			);
	END;
$$
LANGUAGE 'plpgsql' IMMUTABLE;

COMMENT ON FUNCTION cakephp_validate_compare_dates( p_check1 TIMESTAMP, p_check2 TIMESTAMP, p_comparator text ) IS
	'@see Validation2.Validation2RulesComparisonBehavior::compareDates()';

--------------------------------------------------------------------------------
-- 20150420: CG 58 (et autres CG); la durée du CER doit être un nombre entier
-- positif et la date de fin doit être strictement supérieure à la date de début
--------------------------------------------------------------------------------

SELECT alter_table_drop_constraint_if_exists ( 'public', 'proposcontratsinsertioncovs58', 'proposcontratsinsertioncovs58_duree_engag_comparison_chk' );
ALTER TABLE proposcontratsinsertioncovs58 ADD CONSTRAINT proposcontratsinsertioncovs58_duree_engag_comparison_chk CHECK ( cakephp_validate_comparison( duree_engag, '>', 0 ) );
SELECT alter_table_drop_constraint_if_exists ( 'public', 'proposcontratsinsertioncovs58', 'proposcontratsinsertioncovs58_dd_ci_compare_dates_chk' );
ALTER TABLE proposcontratsinsertioncovs58 ADD CONSTRAINT proposcontratsinsertioncovs58_dd_ci_compare_dates_chk CHECK ( cakephp_validate_compare_dates( dd_ci, df_ci, '<' ) );
SELECT alter_table_drop_constraint_if_exists ( 'public', 'proposcontratsinsertioncovs58', 'proposcontratsinsertioncovs58_df_ci_compare_dates_chk' );
ALTER TABLE proposcontratsinsertioncovs58 ADD CONSTRAINT proposcontratsinsertioncovs58_df_ci_compare_dates_chk CHECK ( cakephp_validate_compare_dates( df_ci, dd_ci, '>' ) );

SELECT alter_table_drop_constraint_if_exists ( 'public', 'decisionsproposcontratsinsertioncovs58', 'decisionsproposcontratsinsertioncovs58_duree_engag_comparison_chk' );
SELECT alter_table_drop_constraint_if_exists ( 'public', 'decisionsproposcontratsinsertioncovs58', 'decisionsproposcontratsinsertioncovs58_duree_engag_comparison_c' );
ALTER TABLE decisionsproposcontratsinsertioncovs58 ADD CONSTRAINT decisionsproposcontratsinsertioncovs58_duree_engag_comparison_chk CHECK ( cakephp_validate_comparison( duree_engag, '>', 0 ) );
SELECT alter_table_drop_constraint_if_exists ( 'public', 'decisionsproposcontratsinsertioncovs58', 'decisionsproposcontratsinsertioncovs58_dd_ci_compare_dates_chk' );
ALTER TABLE decisionsproposcontratsinsertioncovs58 ADD CONSTRAINT decisionsproposcontratsinsertioncovs58_dd_ci_compare_dates_chk CHECK ( cakephp_validate_compare_dates( dd_ci, df_ci, '<' ) );
SELECT alter_table_drop_constraint_if_exists ( 'public', 'decisionsproposcontratsinsertioncovs58', 'decisionsproposcontratsinsertioncovs58_df_ci_compare_dates_chk' );
ALTER TABLE decisionsproposcontratsinsertioncovs58 ADD CONSTRAINT decisionsproposcontratsinsertioncovs58_df_ci_compare_dates_chk CHECK ( cakephp_validate_compare_dates( df_ci, dd_ci, '>' ) );

/*
FIXME: SELECT * FROM contratsinsertion WHERE dd_ci > df_ci;
CG 58
	-> 1@cg58_20150402_orig
CG 66
	-> 1@cg66_20140923_orig
	-> 1@cg66_20150318_orig
CG 93
	-> 9@cg93_20150211_orig
CG 976
	-> 0@cg976_20141127_orig
	-> 0@cg976_20141215_orig
*/
-- ALTER TABLE contratsinsertion ADD CONSTRAINT contratsinsertion_duree_engag_comparison_chk CHECK ( cakephp_validate_comparison( duree_engag, '>', 0 ) );
-- ALTER TABLE contratsinsertion ADD CONSTRAINT contratsinsertion_dd_ci_compare_dates_chk CHECK ( cakephp_validate_compare_dates( dd_ci, df_ci, '<' ) );
-- ALTER TABLE contratsinsertion ADD CONSTRAINT contratsinsertion_df_ci_compare_dates_chk CHECK ( cakephp_validate_compare_dates( df_ci, dd_ci, '>' ) );

-- *****************************************************************************
-- CUI (CG 66)
-- *****************************************************************************

--------------------------------------------------------------------------------
-- On détruit les contraintes de Foreign Key pour éviter les problêmes
--------------------------------------------------------------------------------

SELECT alter_table_drop_constraint_if_exists ( 'public', 'accompagnementscuis66', 'accompagnementscuis66_cui66_id_fkey' );
--ALTER TABLE accompagnementscuis66 DROP CONSTRAINT accompagnementscuis66_cui66_id_fkey;
SELECT alter_table_drop_constraint_if_exists ( 'public', 'accompagnementscuis66', 'accompagnementscuis66_immersioncui_id_fkey' );
--ALTER TABLE accompagnementscuis66 DROP CONSTRAINT accompagnementscuis66_immersioncui_id_fkey;
SELECT alter_table_drop_constraint_if_exists ( 'public', 'cuis', 'cuis_adressecui_id_fkey' );
--ALTER TABLE cuis DROP CONSTRAINT cuis_adressecui_id_fkey;
SELECT alter_table_drop_constraint_if_exists ( 'public', 'cuis', 'cuis_partenairecui_id_fkey' );
--ALTER TABLE cuis DROP CONSTRAINT cuis_partenairecui_id_fkey;
SELECT alter_table_drop_constraint_if_exists ( 'public', 'cuis', 'cuis_personne_id_fkey1' );
--ALTER TABLE cuis DROP CONSTRAINT cuis_personne_id_fkey1;
SELECT alter_table_drop_constraint_if_exists ( 'public', 'cuis', 'cuis_personnecui_id_fkey' );
--ALTER TABLE cuis DROP CONSTRAINT cuis_personnecui_id_fkey;
SELECT alter_table_drop_constraint_if_exists ( 'public', 'cuis', 'cuis_user_id_fkey' );
--ALTER TABLE cuis DROP CONSTRAINT cuis_user_id_fkey;
SELECT alter_table_drop_constraint_if_exists ( 'public', 'decisionscuis66', 'decisionscuis66_cui66_id_fkey' );
--ALTER TABLE decisionscuis66 DROP CONSTRAINT decisionscuis66_cui66_id_fkey;



--------------------------------------------------------------------------------
-- Règle de validation inList pour des SMALLINT
--------------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION cakephp_validate_in_list( smallint, smallint[] ) RETURNS boolean AS
$$
	-- SELECT $1 IS NULL OR ( ARRAY[CAST($1 AS TEXT)] <@ CAST($2 AS TEXT[]) );
	SELECT cakephp_validate_in_list( CAST($1 AS TEXT), CAST($2 AS TEXT[]) );
$$
LANGUAGE 'sql' IMMUTABLE;

COMMENT ON FUNCTION cakephp_validate_in_list( smallint, smallint[] ) IS
	'@see http://api.cakephp.org/class/validation#method-ValidationinList';

--------------------------------------------------------------------------------
-- On supprime avant de recreer CUI
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS historiquepositionscuis66,
emailscuis,
rupturescuis66,
suspensionscuis66,
accompagnementscuis66,
immersionscuis66,
decisionscuis66,
propositionscuis66,
cuis66,
cuis,
partenairescuis66,
personnescuis,
partenairescuis,
adressescuis CASCADE;

--------------------------------------------------------------------------------
-- On Creer la table adressescuis (CERFA)
--------------------------------------------------------------------------------

CREATE TABLE adressescuis
(
  id SERIAL NOT NULL PRIMARY KEY,
  numvoie VARCHAR(6) NOT NULL,
  typevoie VARCHAR(6) NOT NULL,
  nomvoie VARCHAR(30) NOT NULL,
  complement VARCHAR(255),
  codepostal CHAR(5) NOT NULL,
  commune VARCHAR(45) NOT NULL,
  numtel VARCHAR(10),
  email VARCHAR(100),
  numfax VARCHAR(10),
  canton VARCHAR(50),
  numvoie2 VARCHAR(6),
  typevoie2 VARCHAR(50),
  nomvoie2 VARCHAR(32),
  complement2 VARCHAR(255),
  codepostal2 CHAR(5),
  commune2 VARCHAR(100),
  numtel2 VARCHAR(10),
  email2 VARCHAR(100),
  numfax2 VARCHAR(10),
  canton2 VARCHAR(50)
);
COMMENT ON TABLE adressescuis IS 'Adresses des personnes et des partenaires';

CREATE INDEX adressescuis_commune_idx ON adressescuis(commune);

--------------------------------------------------------------------------------
-- On Creer la table partenairescuis (CERFA)
--------------------------------------------------------------------------------

CREATE TABLE partenairescuis
(
	id SERIAL NOT NULL PRIMARY KEY,
	raisonsociale VARCHAR(100) NOT NULL,
	enseigne VARCHAR(255),
	adressecui_id INTEGER REFERENCES adressescuis(id) ON DELETE CASCADE ON UPDATE CASCADE,
	siret VARCHAR(14),
	naf VARCHAR(5),
	statut INTEGER,
	effectif INTEGER,
	organismerecouvrement VARCHAR(6),
	assurancechomage SMALLINT,
	ajourversement SMALLINT
);
COMMENT ON TABLE partenairescuis IS 'Entreprise/mairie/association partenaire du CUI';

CREATE INDEX partenairescuis_raisonsociale_idx ON partenairescuis(raisonsociale);

-- Booleans CHAR(1)
ALTER TABLE partenairescuis ADD CONSTRAINT partenairescuis_assurancechomage_in_list_chk CHECK ( cakephp_validate_in_list( assurancechomage, ARRAY[0,1] ) );
ALTER TABLE partenairescuis ADD CONSTRAINT partenairescuis_ajourversement_in_list_chk CHECK ( cakephp_validate_in_list( ajourversement, ARRAY[0,1] ) );

-- Enums VARCHAR(6)
ALTER TABLE partenairescuis ADD CONSTRAINT partenairescuis_organismerecouvrement_in_list_chk CHECK ( cakephp_validate_in_list( organismerecouvrement, ARRAY['URS','MSA','AUT'] ) );

-- INTEGER basic rule
ALTER TABLE partenairescuis ADD CONSTRAINT partenairescuis_statut_inclusive_range CHECK ( cakephp_validate_inclusive_range (statut, 0, 2147483647) );
ALTER TABLE partenairescuis ADD CONSTRAINT partenairescuis_effectif_inclusive_range CHECK ( cakephp_validate_inclusive_range (effectif, 0, 2147483647) );

-- Elargissement de la capacité du libstruc de la table Partenaire
ALTER TABLE partenaires ALTER COLUMN libstruc TYPE VARCHAR(100);

--------------------------------------------------------------------------------
-- On Creer la table personnescuis (CERFA)
--------------------------------------------------------------------------------

CREATE TABLE personnescuis
(
  id SERIAL NOT NULL PRIMARY KEY,
  civilite VARCHAR(3),
  nomfamille VARCHAR(50),
  nomusage VARCHAR(50),
  prenom1 VARCHAR(50),
  prenom2 VARCHAR(50),
  prenom3 VARCHAR(50),
  adressecui_id INTEGER REFERENCES adressescuis(id) ON DELETE CASCADE ON UPDATE CASCADE,
  numeroide INTEGER,
  datenaissance DATE,
  villenaissance VARCHAR(100),
  nir CHAR(15),
  nationalite VARCHAR(7),
  numallocataire INTEGER,
  organismepayeur VARCHAR(7)
);
COMMENT ON TABLE personnescuis IS 'Lié au CERFA CUI';

CREATE INDEX personnescuis_nomusage_idx ON personnescuis(nomusage);
CREATE INDEX personnescuis_nir_idx ON personnescuis(nir);
CREATE INDEX personnescuis_numallocataire_idx ON personnescuis(numallocataire);

--------------------------------------------------------------------------------
-- On Creer la table partenairescuis66 (CG 66)
--------------------------------------------------------------------------------

CREATE TABLE partenairescuis66
(
	id SERIAL NOT NULL PRIMARY KEY,
	partenairecui_id INTEGER REFERENCES partenairescuis(id) ON DELETE CASCADE ON UPDATE CASCADE,
	codepartenaire VARCHAR(100),		-- Employeur
	objet VARCHAR(100),					-- Employeur
	nomtitulairerib VARCHAR(100),		-- Employeur
	codebanque INTEGER,					-- Employeur
	codeguichet INTEGER,				-- Employeur
	numerocompte VARCHAR(100),			-- Employeur
	etablissementbancaire VARCHAR(100),	-- Employeur
	clerib INTEGER,						-- Employeur
	nblits INTEGER,						-- Employeur
	nbcontratsaideshorscg INTEGER,		-- Employeur
	nbcontratsaidescg INTEGER			-- Employeur
);
CREATE INDEX partenairescuis66_partenairecui_id_idx ON partenairescuis66(partenairecui_id);

-- INTEGER basic rule
ALTER TABLE partenairescuis66 ADD CONSTRAINT cuis_partenaires66_codebanque_inclusive_range CHECK ( cakephp_validate_inclusive_range (codebanque, 0, 2147483647) );
ALTER TABLE partenairescuis66 ADD CONSTRAINT cuis_partenaires66_codeguichet_inclusive_range CHECK ( cakephp_validate_inclusive_range (codeguichet, 0, 2147483647) );
ALTER TABLE partenairescuis66 ADD CONSTRAINT cuis_partenaires66_clerib_inclusive_range CHECK ( cakephp_validate_inclusive_range (clerib, 0, 2147483647) );
ALTER TABLE partenairescuis66 ADD CONSTRAINT cuis_partenaires66_nblits_inclusive_range CHECK ( cakephp_validate_inclusive_range (nblits, 0, 2147483647) );
ALTER TABLE partenairescuis66 ADD CONSTRAINT cuis_partenaires66_nbcontratsaideshorscg_inclusive_range CHECK ( cakephp_validate_inclusive_range (nbcontratsaideshorscg, 0, 2147483647) );
ALTER TABLE partenairescuis66 ADD CONSTRAINT cuis_partenaires66_nbcontratsaidescg_inclusive_range CHECK ( cakephp_validate_inclusive_range (nbcontratsaidescg, 0, 2147483647) );

--------------------------------------------------------------------------------
-- On Creer la table cuis (CERFA)
--------------------------------------------------------------------------------

DROP INDEX IF EXISTS cui_personne_id_idx;

CREATE TABLE cuis
(
	id							SERIAL NOT NULL PRIMARY KEY,
	personne_id					INTEGER NOT NULL REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	partenaire_id				INTEGER REFERENCES partenaires(id) ON DELETE SET NULL ON UPDATE CASCADE,
	partenairecui_id			INTEGER REFERENCES partenairescuis(id) ON DELETE SET NULL ON UPDATE CASCADE,
--	personnecui_id				INTEGER REFERENCES personnescuis(id) ON DELETE CASCADE ON UPDATE CASCADE,
	secteurmarchand				SMALLINT NOT NULL,
	numconventionindividuelle	VARCHAR(100),		-- Cadre reserve au prescripteur
	numconventionobjectif		VARCHAR(100),		-- Cadre reserve au prescripteur
	datedepot					DATE,				-- Cadre reserve au prescripteur
	prescripteur				VARCHAR(100),		-- Cadre reserve au prescripteur
	embaucheinsertion			SMALLINT,
	numannexefinanciere			VARCHAR(100),		-- Employeur
	niveauformation				VARCHAR(2),			-- Situation du salarié
	inscritpoleemploi			VARCHAR(10),		-- Situation du salarié
	sansemploi					VARCHAR(10),		-- Situation du salarié
	beneficiaire_ass			SMALLINT,
	beneficiaire_aah			SMALLINT,
	beneficiaire_ata			SMALLINT,
	beneficiaire_rsa			SMALLINT,
	majorationrsa				SMALLINT,
	rsadepuis					VARCHAR(10),		-- Situation du salarié
	travailleurhandicape		SMALLINT,
	typecontrat					VARCHAR(10),		-- Contrat de travail
	dateembauche				DATE,				-- Contrat de travail
	findecontrat				DATE,				-- Contrat de travail
	entreeromev3_id				INTEGER REFERENCES entreesromesv3(id) ON DELETE SET NULL ON UPDATE CASCADE,			-- Contrat de travail
	salairebrut					NUMERIC(6,2),			-- Contrat de travail
	dureehebdo					INTEGER,			-- Contrat de travail
	modulation					SMALLINT,
	dureecollectivehebdo		INTEGER,			-- Contrat de travail
	nomtuteur					VARCHAR(100),		-- Action d'accompagnement formation
	fonctiontuteur				VARCHAR(100),		-- Action d'accompagnement formation
	organismedesuivi			VARCHAR(100),		-- Action d'accompagnement formation
	nomreferent					VARCHAR(100),		-- Action d'accompagnement formation
	actionaccompagnement		SMALLINT,
	remobilisationemploi		SMALLINT,			-- Actions d'accompagnement pro
	aidepriseposte				SMALLINT,			-- Actions d'accompagnement pro
	elaborationprojet			SMALLINT,			-- Actions d'accompagnement pro
	evaluationcompetences		SMALLINT,			-- Actions d'accompagnement pro
	aiderechercheemploi			SMALLINT,			-- Actions d'accompagnement pro
	autre						SMALLINT,			-- Actions d'accompagnement pro
	autrecommentaire			VARCHAR(100),		-- Actions d'accompagnement pro
	adaptationauposte			SMALLINT,			-- Action de formation
	remiseaniveau				SMALLINT,			-- Action de formation
	prequalification			SMALLINT,			-- Action de formation
	acquisitioncompetences		SMALLINT,			-- Action de formation
	formationqualifiante		SMALLINT,			-- Action de formation
	formation					VARCHAR(7),			-- Action de formation
	periodeprofessionnalisation SMALLINT,
	niveauqualif				VARCHAR(2),			-- Action de formation
	validationacquis			SMALLINT,
	periodeimmersion			SMALLINT,
	effetpriseencharge			DATE,				-- Decision de prise en charge
	finpriseencharge			DATE,				-- Decision de prise en charge
	decisionpriseencharge		DATE,				-- Decision de prise en charge
	dureehebdoretenu			INTEGER,			-- Decision de prise en charge
	operationspeciale			VARCHAR(100),		-- Decision de prise en charge
	tauxfixeregion				SMALLINT,			-- Decision de prise en charge
	priseenchargeeffectif		SMALLINT,			-- Decision de prise en charge
	exclusifcg					SMALLINT,
	tauxcg						SMALLINT,			-- Decision de prise en charge
	organismepayeur				VARCHAR(5),			-- Decision de prise en charge
	intituleautreorganisme		VARCHAR(100),		-- Decision de prise en charge
	adressautreorganisme		VARCHAR(100),		-- Decision de prise en charge
	faitle						DATE,				-- Dates
	signaturele					DATE,				-- Dates
	created						TIMESTAMP WITHOUT TIME ZONE, -- Créé le...
	modified					TIMESTAMP WITHOUT TIME ZONE, -- Modifié le...
	user_id						INTEGER NOT NULL REFERENCES users(id),	-- Modifié par...
	haspiecejointe				CHAR(1) NOT NULL DEFAULT '0' -- Pieces Jointes
);
COMMENT ON TABLE cuis IS 'CERFA CUI';
COMMENT ON COLUMN cuis.secteurmarchand IS 'Cadre reserve au prescripteur';
COMMENT ON COLUMN cuis.numconventionindividuelle IS 'Cadre reserve au prescripteur';
COMMENT ON COLUMN cuis.numconventionobjectif IS 'Cadre reserve au prescripteur';
COMMENT ON COLUMN cuis.datedepot IS 'Cadre reserve au prescripteur';
COMMENT ON COLUMN cuis.prescripteur IS 'Cadre reserve au prescripteur';
COMMENT ON COLUMN cuis.embaucheinsertion IS 'Employeur';
COMMENT ON COLUMN cuis.numannexefinanciere IS 'Employeur';
COMMENT ON COLUMN cuis.niveauformation IS 'Situation du salarié';
COMMENT ON COLUMN cuis.inscritpoleemploi IS 'Situation du salarié';
COMMENT ON COLUMN cuis.sansemploi IS 'Situation du salarié';
COMMENT ON COLUMN cuis.majorationrsa IS 'Situation du salarié';
COMMENT ON COLUMN cuis.rsadepuis IS 'Situation du salarié';
COMMENT ON COLUMN cuis.travailleurhandicape IS 'Situation du salarié';
COMMENT ON COLUMN cuis.typecontrat IS 'Contrat de travail';
COMMENT ON COLUMN cuis.dateembauche IS 'Contrat de travail';
COMMENT ON COLUMN cuis.findecontrat IS 'Contrat de travail';
COMMENT ON COLUMN cuis.entreeromev3_id IS 'Contrat de travail';
COMMENT ON COLUMN cuis.salairebrut IS 'Contrat de travail';
COMMENT ON COLUMN cuis.dureehebdo IS 'Contrat de travail';
COMMENT ON COLUMN cuis.modulation IS 'Contrat de travail';
COMMENT ON COLUMN cuis.dureecollectivehebdo IS 'Contrat de travail';
COMMENT ON COLUMN cuis.nomtuteur IS 'Action d''accompagnement formation';
COMMENT ON COLUMN cuis.fonctiontuteur IS 'Action d''accompagnement formation';
COMMENT ON COLUMN cuis.organismedesuivi IS 'Action d''accompagnement formation';
COMMENT ON COLUMN cuis.nomreferent IS 'Action d''accompagnement formation';
COMMENT ON COLUMN cuis.actionaccompagnement IS 'Action d''accompagnement formation';
COMMENT ON COLUMN cuis.remobilisationemploi IS 'Actions d''accompagnement pro';
COMMENT ON COLUMN cuis.aidepriseposte IS 'Actions d''accompagnement pro';
COMMENT ON COLUMN cuis.elaborationprojet IS 'Actions d''accompagnement pro';
COMMENT ON COLUMN cuis.evaluationcompetences IS 'Actions d''accompagnement pro';
COMMENT ON COLUMN cuis.aiderechercheemploi IS 'Actions d''accompagnement pro';
COMMENT ON COLUMN cuis.autre IS 'Actions d''accompagnement pro';
COMMENT ON COLUMN cuis.autrecommentaire IS 'Actions d''accompagnement pro';
COMMENT ON COLUMN cuis.adaptationauposte IS 'Action de formation';
COMMENT ON COLUMN cuis.remiseaniveau IS 'Action de formation';
COMMENT ON COLUMN cuis.prequalification IS 'Action de formation';
COMMENT ON COLUMN cuis.acquisitioncompetences IS 'Action de formation';
COMMENT ON COLUMN cuis.formationqualifiante IS 'Action de formation';
COMMENT ON COLUMN cuis.formation IS 'Action de formation';
COMMENT ON COLUMN cuis.periodeprofessionnalisation IS 'Action de formation';
COMMENT ON COLUMN cuis.niveauqualif IS 'Action de formation';
COMMENT ON COLUMN cuis.validationacquis IS 'Action de formation';
COMMENT ON COLUMN cuis.periodeimmersion IS 'CAE';
COMMENT ON COLUMN cuis.effetpriseencharge IS 'Decision de prise en charge';
COMMENT ON COLUMN cuis.finpriseencharge IS 'Decision de prise en charge';
COMMENT ON COLUMN cuis.decisionpriseencharge IS 'Decision de prise en charge';
COMMENT ON COLUMN cuis.dureehebdoretenu IS 'Decision de prise en charge';
COMMENT ON COLUMN cuis.operationspeciale IS 'Decision de prise en charge';
COMMENT ON COLUMN cuis.tauxfixeregion IS 'Decision de prise en charge';
COMMENT ON COLUMN cuis.priseenchargeeffectif IS 'Decision de prise en charge';
COMMENT ON COLUMN cuis.exclusifcg IS 'Decision de prise en charge';
COMMENT ON COLUMN cuis.tauxcg IS 'Decision de prise en charge';
COMMENT ON COLUMN cuis.organismepayeur IS 'Decision de prise en charge';
COMMENT ON COLUMN cuis.intituleautreorganisme IS 'Decision de prise en charge';
COMMENT ON COLUMN cuis.adressautreorganisme IS 'Decision de prise en charge';
COMMENT ON COLUMN cuis.created IS 'Dates';
COMMENT ON COLUMN cuis.signaturele IS 'Dates';
COMMENT ON COLUMN cuis.modified IS 'Modifié le...';
COMMENT ON COLUMN cuis.user_id IS 'Modifié par...';

CREATE INDEX cui_personne_id_idx ON cuis(personne_id);
CREATE INDEX cui_personne_partenaire_id_idx ON cuis(partenaire_id);
CREATE INDEX cui_personne_personne_id_idx ON cuis(personne_id);

-- Booleans SMALLINT(1)
ALTER TABLE cuis ADD CONSTRAINT cuis_secteurmarchand_in_list_chk CHECK ( cakephp_validate_in_list( secteurmarchand, ARRAY[0,1] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_embaucheinsertion_in_list_chk CHECK ( cakephp_validate_in_list( embaucheinsertion, ARRAY[0,1] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_majorationrsa_in_list_chk CHECK ( cakephp_validate_in_list( majorationrsa, ARRAY[0,1] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_travailleurhandicape_in_list_chk CHECK ( cakephp_validate_in_list( travailleurhandicape, ARRAY[0,1] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_modulation_in_list_chk CHECK ( cakephp_validate_in_list( modulation, ARRAY[0,1] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_actionaccompagnement_in_list_chk CHECK ( cakephp_validate_in_list( actionaccompagnement, ARRAY[0,1] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_periodeprofessionnalisation_in_list_chk CHECK ( cakephp_validate_in_list( periodeprofessionnalisation, ARRAY[0,1] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_validationacquis_in_list_chk CHECK ( cakephp_validate_in_list( validationacquis, ARRAY[0,1] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_periodeimmersion_in_list_chk CHECK ( cakephp_validate_in_list( periodeimmersion, ARRAY[0,1] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_exclusifcg_in_list_chk CHECK ( cakephp_validate_in_list( exclusifcg, ARRAY[0,1] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_beneficiaire_ass_in_list_chk CHECK ( cakephp_validate_in_list( beneficiaire_ass, ARRAY[0,1] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_beneficiaire_aah_in_list_chk CHECK ( cakephp_validate_in_list( beneficiaire_aah, ARRAY[0,1] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_beneficiaire_ata_in_list_chk CHECK ( cakephp_validate_in_list( beneficiaire_ata, ARRAY[0,1] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_beneficiaire_rsa_in_list_chk CHECK ( cakephp_validate_in_list( beneficiaire_rsa, ARRAY[0,1] ) );

-- Enums VARCHAR(10)
ALTER TABLE cuis ADD CONSTRAINT cuis_inscritpoleemploi_in_list_chk CHECK ( cakephp_validate_in_list( inscritpoleemploi, ARRAY['0_5','6_11','12_23','24_999'] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_sansemploi_in_list_chk CHECK ( cakephp_validate_in_list( sansemploi, ARRAY['0_5','6_11','12_23','24_999'] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_rsadepuis_in_list_chk CHECK ( cakephp_validate_in_list( rsadepuis, ARRAY['0_5','6_11','12_23','24_999'] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_typecontrat_in_list_chk CHECK ( cakephp_validate_in_list( typecontrat, ARRAY['CDI','CDD'] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_formation_in_list_chk CHECK ( cakephp_validate_in_list( formation, ARRAY['interne','externe'] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_organismepayeur_in_list_chk CHECK ( cakephp_validate_in_list( organismepayeur, ARRAY['CG','CAF','MSA','ASP','AUTRE'] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_niveauformation_in_list_chk CHECK ( cakephp_validate_in_list( niveauformation, ARRAY['00','10','20','30','40','41','50','51','60','70'] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_niveauqualif_in_list_chk CHECK ( cakephp_validate_in_list( niveauqualif, ARRAY['00','10','20','30','40','41','50','51','60','70'] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_haspiecejointe_in_list_chk CHECK ( cakephp_validate_in_list( haspiecejointe, ARRAY['0','1'] ) );

-- Enums Accompagnement/Formation
ALTER TABLE cuis ADD CONSTRAINT cuis_remobilisationemploi_in_list_chk CHECK ( cakephp_validate_in_list( remobilisationemploi, ARRAY[1,2,3] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_aidepriseposte_in_list_chk CHECK ( cakephp_validate_in_list( aidepriseposte, ARRAY[1,2,3] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_elaborationprojet_in_list_chk CHECK ( cakephp_validate_in_list( elaborationprojet, ARRAY[1,2,3] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_evaluationcompetences_in_list_chk CHECK ( cakephp_validate_in_list( evaluationcompetences, ARRAY[1,2,3] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_aiderechercheemploi_in_list_chk CHECK ( cakephp_validate_in_list( aiderechercheemploi, ARRAY[1,2,3] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_autre_in_list_chk CHECK ( cakephp_validate_in_list( autre, ARRAY[1,2,3] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_adaptationauposte_in_list_chk CHECK ( cakephp_validate_in_list( adaptationauposte, ARRAY[1,2,3] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_remiseaniveau_in_list_chk CHECK ( cakephp_validate_in_list( remiseaniveau, ARRAY[1,2,3] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_prequalification_in_list_chk CHECK ( cakephp_validate_in_list( prequalification, ARRAY[1,2,3] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_acquisitioncompetences_in_list_chk CHECK ( cakephp_validate_in_list( acquisitioncompetences, ARRAY[1,2,3] ) );
ALTER TABLE cuis ADD CONSTRAINT cuis_formationqualifiante_in_list_chk CHECK ( cakephp_validate_in_list( formationqualifiante, ARRAY[1,2,3] ) );

-- INTEGER basic rule
ALTER TABLE cuis ADD CONSTRAINT cuis_salairebrut_inclusive_range CHECK ( cakephp_validate_inclusive_range (salairebrut, 0, 999999) );
ALTER TABLE cuis ADD CONSTRAINT cuis_dureehebdo_inclusive_range CHECK ( cakephp_validate_inclusive_range (dureehebdo, 0, 10080) );
ALTER TABLE cuis ADD CONSTRAINT cuis_dureecollectivehebdo_inclusive_range CHECK ( cakephp_validate_inclusive_range (dureecollectivehebdo, 0, 10080) );
ALTER TABLE cuis ADD CONSTRAINT cuis_dureehebdoretenu_inclusive_range CHECK ( cakephp_validate_inclusive_range (dureehebdoretenu, 0, 10080) );
ALTER TABLE cuis ADD CONSTRAINT cuis_tauxfixeregion_inclusive_range CHECK ( cakephp_validate_inclusive_range (tauxfixeregion, 0, 100) );
ALTER TABLE cuis ADD CONSTRAINT cuis_priseenchargeeffectif_inclusive_range CHECK ( cakephp_validate_inclusive_range (priseenchargeeffectif, 0, 100) );
ALTER TABLE cuis ADD CONSTRAINT cuis_tauxcg_inclusive_range CHECK ( cakephp_validate_inclusive_range (tauxcg, 0, 100) );

--------------------------------------------------------------------------------
-- On Creer la table cuis66 (CG 66)
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS cuis66;
CREATE TABLE cuis66
(
	id SERIAL NOT NULL PRIMARY KEY,
	cui_id INTEGER NOT NULL REFERENCES cuis(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typeformulaire VARCHAR(10) NOT NULL,-- CUI, CUI - Emploi avenir
	typecontrat VARCHAR(255) NOT NULL,	-- ACI, Hors ACI, CIE, EAV
	codecdiae VARCHAR(100),				-- CDIAE
	renouvellement SMALLINT,
	dossierrecu SMALLINT,				-- etat dossier
	datereception DATE,					-- etat dossier
	dossiereligible SMALLINT,			-- etat dossier
	dateeligibilite DATE,				-- etat dossier
	dossiercomplet SMALLINT,			-- etat dossier
	notedossier TEXT,					-- etat dossier
	datecomplet DATE,					-- etat dossier
	commentairedossier TEXT,			-- etat dossier
	zonecouverte VARCHAR(3),			-- le salarié
	datefinsejour DATE,					-- le salarié
	encouple SMALLINT,					-- le salarié
	avecenfant SMALLINT,				-- le salarié
	inscritpoleemploi SMALLINT,			-- le salarié
	dateinscriptionpoleemploi DATE,		-- le salarié
	numdemandeuremploi VARCHAR(100),	-- le salarié
	secteuremploi INTEGER,				-- contrat de travail
	postepropose VARCHAR(100),			-- contrat de travail
	perenisation SMALLINT,				-- contrat de travail
	dureepriseencharge INTEGER,			-- Prise en charge
	aidecomplementaire VARCHAR(100),	-- Prise en charge
	operationspeciale VARCHAR(100),		-- Prise en charge
	subventionemployeur SMALLINT,		-- Prise en charge
	demandeenregistree DATE,				-- Date
	datebutoir DATE,					-- Date
	etatdossiercui66 VARCHAR(20),		-- Hors formulaire
	notifie SMALLINT NOT NULL DEFAULT 0,-- Hors formulaire
	raisonannulation TEXT				-- Hors formulaire
);
COMMENT ON TABLE cuis66 IS 'CUI CG66';
COMMENT ON COLUMN cuis66.typeformulaire IS 'CUI, CUI - Emploi avenir';
COMMENT ON COLUMN cuis66.typecontrat IS 'ACI, Hors ACI, CIE, EAV';
COMMENT ON COLUMN cuis66.codecdiae IS 'CDIAE';
COMMENT ON COLUMN cuis66.dossierrecu IS 'Etat dossier';
COMMENT ON COLUMN cuis66.datereception IS 'Etat dossier';
COMMENT ON COLUMN cuis66.dossiereligible IS 'Etat dossier';
COMMENT ON COLUMN cuis66.dateeligibilite IS 'Etat dossier';
COMMENT ON COLUMN cuis66.dossiercomplet IS 'Etat dossier';
COMMENT ON COLUMN cuis66.datecomplet IS 'Etat dossier';
COMMENT ON COLUMN cuis66.zonecouverte IS 'Allocataire';
COMMENT ON COLUMN cuis66.datefinsejour IS 'Allocataire';
COMMENT ON COLUMN cuis66.encouple IS 'Allocataire';
COMMENT ON COLUMN cuis66.avecenfant IS 'Allocataire';
COMMENT ON COLUMN cuis66.inscritpoleemploi IS 'Allocataire';
COMMENT ON COLUMN cuis66.dateinscriptionpoleemploi IS 'Allocataire';
COMMENT ON COLUMN cuis66.numdemandeuremploi IS 'Allocataire';
COMMENT ON COLUMN cuis66.secteuremploi IS 'Contrat de travail';
COMMENT ON COLUMN cuis66.postepropose IS 'Contrat de travail';
COMMENT ON COLUMN cuis66.perenisation IS 'Contrat de travail';
COMMENT ON COLUMN cuis66.dureepriseencharge IS 'Prise en charge';
COMMENT ON COLUMN cuis66.aidecomplementaire IS 'Prise en charge';
COMMENT ON COLUMN cuis66.operationspeciale IS 'Prise en charge';
COMMENT ON COLUMN cuis66.subventionemployeur IS 'Prise en charge';
COMMENT ON COLUMN cuis66.demandeenregistree IS 'Date';
COMMENT ON COLUMN cuis66.datebutoir IS 'Date';
COMMENT ON COLUMN cuis66.etatdossiercui66 IS 'Hors formulaire';

CREATE INDEX cuis66_etatdossiercui66_idx ON cuis66(etatdossiercui66);
CREATE INDEX cuis66_typecontrat_idx ON cuis66(typecontrat);
CREATE INDEX cuis66_cui_id_idx ON cuis66(cui_id);

ALTER TABLE cuis66 ADD CONSTRAINT cuis66_typeformulaire_in_list_chk CHECK ( cakephp_validate_in_list( typeformulaire, ARRAY['CUI','CUIAvenir'] ) );
ALTER TABLE cuis66 ADD CONSTRAINT cuis66_zonecouverte_in_list_chk CHECK ( cakephp_validate_in_list( zonecouverte, ARRAY['ZUS','ZRR'] ) );
ALTER TABLE cuis66 ADD CONSTRAINT cuis66_etatdossiercui66_in_list_chk CHECK ( cakephp_validate_in_list( etatdossiercui66, ARRAY['attentepiece','dossierrecu','dossiernonrecu','dossierrelance','dossiereligible','attentemail','formulairecomplet','attenteavis','attentedecision','attentenotification','encours','perime','rupturecontrat','contratsuspendu','decisionsanssuite','nonvalide','annule'] ) );

ALTER TABLE cuis66 ADD CONSTRAINT cuis66_dossierrecu_in_list_chk CHECK ( cakephp_validate_in_list( dossierrecu, ARRAY[0,1] ) );
ALTER TABLE cuis66 ADD CONSTRAINT cuis66_dossiereligible_in_list_chk CHECK ( cakephp_validate_in_list( dossiereligible, ARRAY[0,1] ) );
ALTER TABLE cuis66 ADD CONSTRAINT cuis66_dossiercomplet_in_list_chk CHECK ( cakephp_validate_in_list( dossiercomplet, ARRAY[0,1] ) );
ALTER TABLE cuis66 ADD CONSTRAINT cuis66_notifie_in_list_chk CHECK ( cakephp_validate_in_list( notifie, ARRAY[0,1] ) );
ALTER TABLE cuis66 ADD CONSTRAINT cuis66_renouvellement_in_list_chk CHECK ( cakephp_validate_in_list( renouvellement, ARRAY[0,1] ) );


--------------------------------------------------------------------------------
-- On Creer la table propositionscuis66 (CG 66)
--------------------------------------------------------------------------------

CREATE TABLE propositionscuis66
(
	id							SERIAL NOT NULL PRIMARY KEY,
	cui66_id					INTEGER NOT NULL REFERENCES cuis66(id),
	donneuravis					VARCHAR(8) NOT NULL,
	dateproposition				DATE NOT NULL,
	observation					TEXT,
	avis						VARCHAR(15) NOT NULL,
	motif						INTEGER,
	created						TIMESTAMP WITHOUT TIME ZONE, -- Créé le...
	modified					TIMESTAMP WITHOUT TIME ZONE, -- Modifié le...
	user_id						INTEGER NOT NULL REFERENCES users(id),	-- Modifié par...
	haspiecejointe				CHAR(1) NOT NULL DEFAULT '0' -- Pieces Jointes
);
CREATE INDEX propositionscuis66_id_idx ON propositionscuis66(cui66_id);

ALTER TABLE propositionscuis66 ADD CONSTRAINT cuis_propositions66_donneuravis_in_list_chk CHECK ( cakephp_validate_in_list( donneuravis, ARRAY['PRE','referent','elu'] ) );
ALTER TABLE propositionscuis66 ADD CONSTRAINT cuis_propositions66_avis_in_list_chk CHECK ( cakephp_validate_in_list( avis, ARRAY['attentedecision','accord','refus','avisreserve'] ) );


--------------------------------------------------------------------------------
-- On Creer la table decisionscuis66 (CG 66)
--------------------------------------------------------------------------------

CREATE TABLE decisionscuis66
(
	id SERIAL					NOT NULL PRIMARY KEY,
	cui66_id					INTEGER NOT NULL REFERENCES cuis66(id),
	decision					VARCHAR(9) NOT NULL,
	motif						INTEGER,
	datedecision				TIMESTAMP WITHOUT TIME ZONE NOT NULL,
	observation TEXT,
	created						TIMESTAMP WITHOUT TIME ZONE, -- Créé le...
	modified					TIMESTAMP WITHOUT TIME ZONE, -- Modifié le...
	user_id						INTEGER NOT NULL REFERENCES users(id),	-- Modifié par...
	haspiecejointe				CHAR(1) NOT NULL DEFAULT '0' -- Pieces Jointes
);
CREATE INDEX decisionscuis66_cui66_id_idx ON decisionscuis66(cui66_id);

ALTER TABLE decisionscuis66 ADD CONSTRAINT cuis_decisions66_decision_in_list_chk CHECK ( cakephp_validate_in_list( decision, ARRAY['accord','refus','ajourne','sanssuite'] ) );


--------------------------------------------------------------------------------
-- On Creer la table immersionscuis66 (CG 66)
--------------------------------------------------------------------------------

CREATE TABLE immersionscuis66
(
	id SERIAL					NOT NULL PRIMARY KEY,
	nomentreprise				VARCHAR(50) NOT NULL,
	numvoie						VARCHAR(50),
	typevoie					VARCHAR(50),
	nomvoie						VARCHAR(50),
	complementadresse			VARCHAR(50),
	codepostal					VARCHAR(50),
	commune						VARCHAR(50),
	activiteprincipale			VARCHAR(50),
	entreeromev3_id				INTEGER REFERENCES entreesromesv3(id) ON DELETE SET NULL ON UPDATE CASCADE,
	objectifprincipal			VARCHAR(20)
);
CREATE INDEX immersionscuis66_nomentreprise_idx ON immersionscuis66(nomentreprise);

ALTER TABLE immersionscuis66 ADD CONSTRAINT cuis_accompagnement_immersions66_objectifprincipal_in_list_chk CHECK ( cakephp_validate_in_list( objectifprincipal, ARRAY['aquisitioncompetence','projetpro','decouvertemetier','demarcherecrutement'] ) );


--------------------------------------------------------------------------------
-- On Creer la table accompagnementscuis66 (CG 66)
--------------------------------------------------------------------------------

CREATE TABLE accompagnementscuis66
(
	id SERIAL NOT NULL PRIMARY KEY,
	cui66_id INTEGER NOT NULL REFERENCES cuis66(id),
	genre VARCHAR(9) NOT NULL,
	immersioncui66_id INTEGER REFERENCES immersionscuis66(id),
	organismesuivi VARCHAR(50),			-- Formation / bilan
	nomredacteur VARCHAR(50),			-- Formation / bilan
	observation TEXT,					-- Formation / bilan
	datededebut DATE NOT NULL,
	datedefin DATE NOT NULL,
	datedesignature DATE NOT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE, -- Créé le...
	modified					TIMESTAMP WITHOUT TIME ZONE, -- Modifié le...
	user_id						INTEGER NOT NULL REFERENCES users(id),	-- Modifié par...
	haspiecejointe				CHAR(1) NOT NULL DEFAULT '0' -- Pieces Jointes
);
CREATE INDEX accompagnementscuis66_cui66_id_idx ON accompagnementscuis66(cui66_id);

ALTER TABLE accompagnementscuis66 ADD CONSTRAINT cuis_accompagnements66_genre_in_list_chk CHECK ( cakephp_validate_in_list( genre, ARRAY['immersion','formation','bilan'] ) );


--------------------------------------------------------------------------------
-- On Creer la table suspensionscuis66 (CG 66)
--------------------------------------------------------------------------------

CREATE TABLE suspensionscuis66
(
	id SERIAL					NOT NULL PRIMARY KEY,
	cui66_id					INTEGER NOT NULL REFERENCES cuis66(id),
	observation					TEXT,
	duree						VARCHAR(9),
	datedebut					DATE NOT NULL,
	datefin						DATE NOT NULL,
	motif						INTEGER NOT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE, -- Créé le...
	modified					TIMESTAMP WITHOUT TIME ZONE, -- Modifié le...
	user_id						INTEGER NOT NULL REFERENCES users(id),	-- Modifié par...
	haspiecejointe				CHAR(1) NOT NULL DEFAULT '0' -- Pieces Jointes
);
CREATE INDEX suspensionscuis66_cui66_id_idx ON suspensionscuis66(cui66_id);

ALTER TABLE suspensionscuis66 ADD CONSTRAINT cuis_suspensions66_duree_in_list_chk CHECK ( cakephp_validate_in_list( duree, ARRAY['matin','apresmidi','journee'] ) );


--------------------------------------------------------------------------------
-- On Creer la table rupturescuis66 (CG 66)
--------------------------------------------------------------------------------

CREATE TABLE rupturescuis66
(
	id SERIAL					NOT NULL PRIMARY KEY,
	cui66_id					INTEGER NOT NULL REFERENCES cuis66(id),
	observation					TEXT,
	daterupture					DATE NOT NULL,
	dateenregistrement			DATE NOT NULL,
	motif						INTEGER NOT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE, -- Créé le...
	modified					TIMESTAMP WITHOUT TIME ZONE, -- Modifié le...
	user_id						INTEGER NOT NULL REFERENCES users(id),	-- Modifié par...
	haspiecejointe				CHAR(1) NOT NULL DEFAULT '0' -- Pieces Jointes
);
CREATE INDEX rupturescuis66_cui66_id_idx ON rupturescuis66(cui66_id);

--------------------------------------------------------------------------------
-- On Creer la table rupturescuis66 (CG 66)
--------------------------------------------------------------------------------

-- Présence de fausses foreign key pour faciliter la récupération des données pour les mails
CREATE TABLE emailscuis
(
	id							SERIAL NOT NULL PRIMARY KEY,
	cui_id						INTEGER NOT NULL REFERENCES cuis(id) ON DELETE CASCADE ON UPDATE CASCADE,
	personne_id					INTEGER NOT NULL REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	textmailcui66_id			INTEGER,
	cui66_id					INTEGER,
	partenairecui_id			INTEGER,
	partenairecui66_id			INTEGER,
	adressecui_id				INTEGER,
	emailredacteur				VARCHAR(255),
	emailemployeur				VARCHAR(255) NOT NULL,
	titre						VARCHAR(255) NOT NULL,
	pj							VARCHAR(255),
	piecesmanquantes			VARCHAR(255),
	message						TEXT NOT NULL,
	insertiondate				DATE,
	commentaire					TEXT,
	dateenvoi					TIMESTAMP WITHOUT TIME ZONE, -- Créé le...
	created						TIMESTAMP WITHOUT TIME ZONE, -- Créé le...
	modified					TIMESTAMP WITHOUT TIME ZONE, -- Modifié le...
	user_id						INTEGER NOT NULL REFERENCES users(id),	-- Modifié par...
	haspiecejointe				CHAR(1) NOT NULL DEFAULT '0' -- Pieces Jointes
);
CREATE INDEX emailscuis_cui_id_idx ON emailscuis(cui_id);
CREATE INDEX emailscuis_personne_id_idx ON emailscuis(personne_id);

--------------------------------------------------------------------------------
-- Historique des changements de positions du Cui (CG 66)
--------------------------------------------------------------------------------

CREATE TABLE historiquepositionscuis66
(
	id							SERIAL NOT NULL PRIMARY KEY,
	cui66_id					INTEGER NOT NULL REFERENCES cuis66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	etatdossiercui66			VARCHAR(20),
	created						TIMESTAMP WITHOUT TIME ZONE -- Créé le...
);
CREATE INDEX historiquepositionscuis66_cui66_id_idx ON historiquepositionscuis66(cui66_id);

--------------------------------------------------------------------------------
-- Pieces manquante d'un Cui pour les e-mails (CG 66)
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS piecesmanquantescuis66;
CREATE TABLE piecesmanquantescuis66
(
  id serial NOT NULL,
  name character varying(250) NOT NULL,
  created timestamp without time zone,
  modified timestamp without time zone,
  CONSTRAINT piecesmanquantescuis66_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE piecesmanquantescuis66
  OWNER TO webrsa;
COMMENT ON TABLE piecesmanquantescuis66
  IS 'Liste des pièces manquantes pour les CUIs (CG66)';

-- Index: piecesmanquantescuis66_name_idx

-- DROP INDEX piecesmanquantescuis66_name_idx;

CREATE UNIQUE INDEX piecesmanquantescuis66_name_idx ON piecesmanquantescuis66(name);


-------------------------------------------------------------------------------------
-- Modification des parametrages des taux CG
-------------------------------------------------------------------------------------

DROP TABLE IF EXISTS tauxcgscuis;
DROP TABLE IF EXISTS tauxcgscuis66;
CREATE TABLE tauxcgscuis66
(
	id						SERIAL NOT NULL PRIMARY KEY,
	typeformulaire			VARCHAR(255),
	secteurmarchand			VARCHAR(255),
	typecontrat				VARCHAR(255),
	tauxfixeregion			SMALLINT,
	priseenchargeeffectif	SMALLINT,
	tauxcg					SMALLINT,
	created					TIMESTAMP WITHOUT TIME ZONE, -- Créé le...
	modified				TIMESTAMP WITHOUT TIME ZONE -- Modifié le...
);


-------------------------------------------------------------------------------------
-- Ajout d'une table de paramétrage pour les motifs de décision de refus de CUI
-------------------------------------------------------------------------------------

DROP TABLE IF EXISTS motifsrefuscuis66 CASCADE;
CREATE TABLE motifsrefuscuis66(
  id			SERIAL NOT NULL PRIMARY KEY,
  name			VARCHAR(250) NOT NULL,
  created		TIMESTAMP WITHOUT TIME ZONE,
  modified		TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE motifsrefuscuis66 IS 'Liste des motifs de décision de refus de CUIs (CG66)';


-------------------------------------------------------------------------------------
-- Ajout d'une table de paramétrage pour les motifs de décision de refus de CUI
-------------------------------------------------------------------------------------

DROP TABLE IF EXISTS typescontratscuis66 CASCADE;
CREATE TABLE typescontratscuis66(
  id			SERIAL NOT NULL PRIMARY KEY,
  name			VARCHAR(250) NOT NULL,
  actif			SMALLINT DEFAULT 1,
  created		TIMESTAMP WITHOUT TIME ZONE,
  modified		TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE typescontratscuis66 IS 'Liste des type de contrat CUI (CG66)';


-------------------------------------------------------------------------------------
-- Ajout de la case Actif sur les paramétrages liés au CUI
-------------------------------------------------------------------------------------

SELECT alter_table_drop_column_if_exists ('public', 'textsmailscuis66', 'actif');
SELECT add_missing_table_field ('public', 'motifsrupturescuis66', 'actif', 'SMALLINT DEFAULT 1');
SELECT add_missing_table_field ('public', 'motifssuspensioncuis66', 'actif', 'SMALLINT DEFAULT 1');
SELECT add_missing_table_field ('public', 'motifsrefuscuis66', 'actif', 'SMALLINT DEFAULT 1');
SELECT add_missing_table_field ('public', 'piecesmailscuis66', 'actif', 'SMALLINT DEFAULT 1');
SELECT add_missing_table_field ('public', 'piecesmanquantescuis66', 'actif', 'SMALLINT DEFAULT 1');
SELECT add_missing_table_field ('public', 'textsmailscuis66', 'actif', 'SMALLINT DEFAULT 1');

SELECT alter_table_drop_constraint_if_exists ( 'public', 'motifsrupturescuis66', 'motifsrupturescuis66_actif_in_list_chk' );
ALTER TABLE motifsrupturescuis66 ADD CONSTRAINT motifsrupturescuis66_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY[0,1] ) );
SELECT alter_table_drop_constraint_if_exists ( 'public', 'motifssuspensioncuis66', 'motifssuspensioncuis66_actif_in_list_chk' );
ALTER TABLE motifssuspensioncuis66 ADD CONSTRAINT motifssuspensioncuis66_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY[0,1] ) );
SELECT alter_table_drop_constraint_if_exists ( 'public', 'motifsrefuscuis66', 'motifsrefuscuis66_actif_in_list_chk' );
ALTER TABLE motifsrefuscuis66 ADD CONSTRAINT motifsrefuscuis66_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY[0,1] ) );
SELECT alter_table_drop_constraint_if_exists ( 'public', 'piecesmailscuis66', 'piecesmailscuis66_actif_in_list_chk' );
ALTER TABLE piecesmailscuis66 ADD CONSTRAINT piecesmailscuis66_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY[0,1] ) );
SELECT alter_table_drop_constraint_if_exists ( 'public', 'piecesmanquantescuis66', 'piecesmanquantescuis66_actif_in_list_chk' );
ALTER TABLE piecesmanquantescuis66 ADD CONSTRAINT piecesmanquantescuis66_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY[0,1] ) );
SELECT alter_table_drop_constraint_if_exists ( 'public', 'textsmailscuis66', 'textsmailscuis66_actif_in_list_chk' );
ALTER TABLE textsmailscuis66 ADD CONSTRAINT textsmailscuis66_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY[0,1] ) );
SELECT alter_table_drop_constraint_if_exists ( 'public', 'typescontratscuis66', 'typescontratscuis66_actif_in_list_chk' );
ALTER TABLE typescontratscuis66 ADD CONSTRAINT typescontratscuis66_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY[0,1] ) );

--------------------------------------------------------------------------------
-- Ticket #6054: ajout du référent aux tableaux de suivi
--------------------------------------------------------------------------------

SELECT add_missing_table_field( 'public', 'tableauxsuivispdvs93', 'referent_id', 'INTEGER');
ALTER TABLE tableauxsuivispdvs93 ALTER COLUMN referent_id SET DEFAULT NULL;
SELECT add_missing_constraint ( 'public', 'tableauxsuivispdvs93', 'tableauxsuivispdvs93_referent_id_fkey', 'referents', 'referent_id', true );
DROP INDEX IF EXISTS tableauxsuivispdvs93_referent_id_idx;
CREATE INDEX tableauxsuivispdvs93_referent_id_idx ON tableauxsuivispdvs93(referent_id);


--------------------------------------------------------------------------------
-- Ticket #9354: Les décisions passent à annulé si la position est à annulé
--------------------------------------------------------------------------------

ALTER TABLE contratsinsertion DROP CONSTRAINT contratsinsertion_decision_ci_datevalidation_ci_check;
ALTER TABLE contratsinsertion ADD CONSTRAINT contratsinsertion_decision_ci_datevalidation_ci_check CHECK(
    ( decision_ci = 'V' AND datevalidation_ci IS NOT NULL )
    OR ( decision_ci <> 'V' AND datevalidation_ci IS NULL )
    OR ( decision_ci = 'A' )
);
UPDATE contratsinsertion SET decision_ci = 'A' WHERE positioncer = 'annule';

--------------------------------------------------------------------------------
-- 20150602: table permettant de stocker les ids ... pris
-- en compte pour le tableau 1B3
--------------------------------------------------------------------------------
DROP TABLE IF EXISTS populationsb3pdvs93 CASCADE;
CREATE TABLE populationsb3pdvs93 (
    id                          SERIAL NOT NULL PRIMARY KEY,
    rendezvous_id				INTEGER NOT NULL REFERENCES rendezvous(id) ON DELETE CASCADE ON UPDATE CASCADE,
    dsp_id						INTEGER NOT NULL REFERENCES dsps(id) ON DELETE CASCADE ON UPDATE CASCADE,
    dsp_rev_id					INTEGER DEFAULT NULL REFERENCES dsps_revs(id) ON DELETE CASCADE ON UPDATE CASCADE,
    tableausuivipdv93_id		INTEGER NOT NULL REFERENCES tableauxsuivispdvs93(id) ON DELETE CASCADE ON UPDATE CASCADE,
    created                     TIMESTAMP WITHOUT TIME ZONE,
    modified                    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE populationsb3pdvs93 IS 'La population prise en compte pour le tableau hisoricisé 1B3';

--------------------------------------------------------------------------------
-- 20150601: table permettant de stocker les ids des fiches de prescription pris
-- en compte pour les tableaux 1B4 et 1B5
--------------------------------------------------------------------------------
DROP TABLE IF EXISTS populationsb4b5pdvs93 CASCADE;
CREATE TABLE populationsb4b5pdvs93 (
    id                          SERIAL NOT NULL PRIMARY KEY,
    ficheprescription93_id		INTEGER NOT NULL REFERENCES fichesprescriptions93(id) ON DELETE CASCADE ON UPDATE CASCADE,
    tableausuivipdv93_id		INTEGER NOT NULL REFERENCES tableauxsuivispdvs93(id) ON DELETE CASCADE ON UPDATE CASCADE,
    created                     TIMESTAMP WITHOUT TIME ZONE,
    modified                    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE populationsb4b5pdvs93 IS 'La population prise en compte pour les tableaux hisoricisés 1B4 et 1B5';

--------------------------------------------------------------------------------
-- 20150601: table permettant de stocker les ids des rendez-vous pris en compte
-- pour le tableau 1B6
--------------------------------------------------------------------------------
DROP TABLE IF EXISTS populationsb6pdvs93 CASCADE;
CREATE TABLE populationsb6pdvs93 (
    id                          SERIAL NOT NULL PRIMARY KEY,
    rendezvous_id				INTEGER NOT NULL REFERENCES rendezvous(id) ON DELETE CASCADE ON UPDATE CASCADE,
    tableausuivipdv93_id		INTEGER NOT NULL REFERENCES tableauxsuivispdvs93(id) ON DELETE CASCADE ON UPDATE CASCADE,
    created                     TIMESTAMP WITHOUT TIME ZONE,
    modified                    TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE populationsb6pdvs93 IS 'La population prise en compte pour le tableau hisoricisé 1B6';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************