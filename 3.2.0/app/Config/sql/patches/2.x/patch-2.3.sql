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


SELECT add_missing_table_field ('public', 'rendezvous', 'isadomicile', 'TYPE_BOOLEANNUMBER');
ALTER TABLE rendezvous ALTER COLUMN isadomicile SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE rendezvous SET isadomicile = '0'::TYPE_BOOLEANNUMBER WHERE isadomicile IS NULL;
ALTER TABLE rendezvous ALTER COLUMN isadomicile SET NOT NULL;



DROP TABLE IF EXISTS histoaprecomplementaires;

CREATE TABLE histoaprecomplementaires (
    id SERIAL NOT NULL PRIMARY KEY,
    nom character varying(255),
    prenom character varying(255),
    num_apre_beneficiaire character varying(255),
    date_reception_sis character varying(255),
    caf character varying(15),
    id_dossier_provisoire integer,
    type_aide character varying(255),
    sexe character varying(1),
    referent character varying(255),
    sexe_referent character varying(1),
    ville character varying(255),
    date_comite character varying(255),
    montant_demande character varying(25),
    montant_accorde character varying(25),
    date_renvoie_dossier character varying(255),
    decision character varying(255),
    commentaire text,
    no_voie character varying(25),
    type_voie character varying(255),
    nom_rue character varying(255),
    code_postale character varying(5),
    ville_beneficiaire character varying(255),
    complement_adresse character varying(255),
    code_banque_benef character varying(5),
    num_compte_benef character varying(11),
    cle_rib_benef character varying(2),
    code_guichet_benef character varying(5),
    sexe_titulaire_rib character varying(1),
    nom_titulaire_rib character varying(255),
    prenom_titulaire_rib character varying(255),
    nom_banque_benef character varying(255),
    nom_rue_banc character varying(255),
    num_rue_banc character varying(255),
    type_rue_banc character varying(255),
    ville_banc character varying(255),
    code_postal_banc integer,
    libelle_organisme character varying(255),
    libelle_action_organisme text,
    nom_responsable_organisme character varying(255),
    prenom_responsable_organisme character varying(255),
    sexe_orga character varying(1),
    num_voie_orga character varying(255),
    type_voie_orga character varying(255),
    nom_rue_orga character varying(255),
    cp_orga integer,
    ville_orga character varying(255),
    complement_adr_orga character varying(255),
    nom_banque_orga character varying(255),
    code_banque_orga character varying(255),
    code_guichet_orga character varying(255),
    num_compte_orga character varying(255),
    cle_orga character varying(255),
    nom_referent character varying(255),
    prenom_referent character varying(255),
    nom_structure_referent character varying(255),
    num_voie_structure_referent character varying(255),
    type_voie_structure_referent character varying(255),
    nom_rue_structure_referent character varying(255),
    code_postale_structure_referent character varying(255),
    adr_total_referent text,
    adr_total_benef text,
    adr_total_orga text,
    ville_structure_referent character varying(255),
    nb_paiements integer,
    entr timestamp(0) without time zone DEFAULT now(),
    date_debut_formation character varying(255),
    date_fin_formation character varying(255),
    imprimey character varying(11) DEFAULT 'non imprimé'::character varying,
    nom_agent character varying(255),
    prenom_agent character varying(255),
    tel_agent character varying(20),
    "Signataire" character varying(2),
    personne_id integer
);


-------------------------------------------------------------------------------------------------------------
-- 20120716 : Ajout d'un champ supplémentaire dans la table actionscandidats pour le CG93
-------------------------------------------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'actionscandidats', 'contractualisation93', 'VARCHAR(250)');


-------------------------------------------------------------------------------------------------------------
-- 20120719: ajout des clés étrangères pour nouvelles orientations suite aux passages en EP.
-------------------------------------------------------------------------------------------------------------

SELECT add_missing_table_field ( 'public', 'defautsinsertionseps66', 'nvorientstruct_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'defautsinsertionseps66', 'defautsinsertionseps66_nvorientstruct_id_fkey', 'orientsstructs', 'nvorientstruct_id', false );

-- On rapatrie les données implicites
/*CREATE OR REPLACE FUNCTION public.update_orientsstructs_decisionsdefautsinsertionseps66() RETURNS VOID AS
$$
	DECLARE
		v_row   record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
					defautsinsertionseps66.id AS thematique_id,
					orientsstructs.id AS orientstruct_id
				FROM dossierseps
					INNER JOIN defautsinsertionseps66 ON ( defautsinsertionseps66.dossierep_id = dossierseps.id )
					INNER JOIN passagescommissionseps ON ( passagescommissionseps.dossierep_id = dossierseps.id )
					INNER JOIN commissionseps ON ( passagescommissionseps.commissionep_id = commissionseps.id )
					INNER JOIN decisionsdefautsinsertionseps66 ON ( decisionsdefautsinsertionseps66.passagecommissionep_id = passagescommissionseps.id )
					INNER JOIN orientsstructs ON ( orientsstructs.personne_id = dossierseps.personne_id )
				WHERE
					dossierseps.themeep = 'defautsinsertionseps66'
					AND passagescommissionseps.etatdossierep = 'traite'
					AND commissionseps.etatcommissionep = 'traite'
					AND (
						decisionsdefautsinsertionseps66.decision = 'reorientation'
						OR (
							decisionsdefautsinsertionseps66.decision = 'maintien'
							AND NOT (
								decisionsdefautsinsertionseps66.changementrefparcours = 'N'
								AND decisionsdefautsinsertionseps66.typeorientprincipale_id IN (
									SELECT id FROM typesorients WHERE lib_type_orient LIKE 'Emploi%'
								)
							)
						)
					)
					AND passagescommissionseps.id IN (
						SELECT
								p.id
							FROM passagescommissionseps AS p
								INNER JOIN commissionseps AS c ON ( p.commissionep_id = c.id )
							WHERE dossierseps.id = p.dossierep_id
							ORDER BY c.dateseance DESC
							LIMIT 1
					)
					AND decisionsdefautsinsertionseps66.id IN (
						SELECT
								d.id
							FROM decisionsdefautsinsertionseps66 AS d
							WHERE passagescommissionseps.id = d.passagecommissionep_id
							ORDER BY ( CASE WHEN d.etape = 'ep' THEN 1 WHEN etape = 'cg' THEN 2 ELSE 0 END ) DESC -- cg, ep
							LIMIT 1
					)
					-- Jointure sur les orientations
					AND orientsstructs.typeorient_id = decisionsdefautsinsertionseps66.typeorient_id
					AND orientsstructs.structurereferente_id = decisionsdefautsinsertionseps66.structurereferente_id
					AND orientsstructs.date_propo = DATE_TRUNC('day', decisionsdefautsinsertionseps66.modified )
					AND orientsstructs.date_valid = DATE_TRUNC('day', decisionsdefautsinsertionseps66.modified )
					AND orientsstructs.user_id = decisionsdefautsinsertionseps66.user_id
					AND orientsstructs.statut_orient = 'Orienté'
					AND orientsstructs.id NOT IN ( SELECT defautsinsertionseps66.nvorientstruct_id FROM defautsinsertionseps66 WHERE defautsinsertionseps66.nvorientstruct_id IS NOT NULL )
				ORDER BY decisionsdefautsinsertionseps66.modified ASC
		LOOP
			-- Mise à jour dans la table defautsinsertionseps66
			v_query := 'UPDATE defautsinsertionseps66 SET nvorientstruct_id = ' || v_row.orientstruct_id || ' WHERE id = ' || v_row.thematique_id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.update_orientsstructs_decisionsdefautsinsertionseps66();
DROP FUNCTION public.update_orientsstructs_decisionsdefautsinsertionseps66();

CREATE UNIQUE INDEX defautsinsertionseps66_nvorientstruct_id_idx ON defautsinsertionseps66 (nvorientstruct_id);*/



-------------------------------------------------------------------------------------------------------------
-- 20120724: Modifications de la table cuis
-------------------------------------------------------------------------------------------------------------
SELECT alter_table_drop_column_if_exists('public', 'cuis', 'convention');

DROP TYPE IF EXISTS TYPE_ISACI CASCADE;
CREATE TYPE TYPE_ISACI AS ENUM ( 'horsaci','enaci');
SELECT add_missing_table_field ('public', 'cuis', 'isaci', 'TYPE_ISACI');

SELECT add_missing_table_field('public', 'cuis', 'cantonemployeur', 'VARCHAR(250)' );
SELECT add_missing_table_field('public', 'cuis', 'cantonemployeur2', 'VARCHAR(250)' );

SELECT public.alter_enumtype ( 'TYPE_STATUTEMPLOYEUR', ARRAY['10','11','21','22','50','60','70','71','72','73','80','90','98','99'] );

SELECT alter_table_drop_column_if_exists('public', 'cuis', 'siret');
SELECT alter_table_drop_column_if_exists('public', 'cuis', 'codenaf2');
SELECT alter_table_drop_column_if_exists('public', 'cuis', 'identconvcollec');
SELECT alter_table_drop_column_if_exists('public', 'cuis', 'effectifemployeur');
SELECT alter_table_drop_column_if_exists('public', 'cuis', 'ribemployeur');

SELECT alter_table_drop_column_if_exists('public', 'cuis', 'atelierchantier');
SELECT alter_table_drop_column_if_exists('public', 'cuis', 'numannexefinanciere');
SELECT alter_table_drop_column_if_exists('public', 'cuis', 'assurancechomage');

SELECT add_missing_table_field('public', 'cuis', 'nomtiturib', 'VARCHAR(250)' );
SELECT add_missing_table_field('public', 'cuis', 'etaban', 'VARCHAR(5)' );
SELECT add_missing_table_field('public', 'cuis', 'guiban', 'VARCHAR(5)' );
SELECT add_missing_table_field('public', 'cuis', 'numcomptban', 'VARCHAR(11)' );
SELECT add_missing_table_field('public', 'cuis', 'nometaban', 'VARCHAR(24)' );
SELECT add_missing_table_field('public', 'cuis', 'clerib', 'VARCHAR(2)' );

DROP TYPE IF EXISTS TYPE_ASSURANCE CASCADE;


SELECT public.alter_enumtype ( 'TYPE_NIVEAUFORMATION', ARRAY['00','10','20','30','40','41','50','51','60','70'] );
SELECT add_missing_table_field('public', 'cuis', 'identifiantpe', 'VARCHAR(11)' );
SELECT alter_table_drop_column_if_exists('public', 'cuis', 'isinscritpe' );

SELECT alter_table_drop_column_if_exists('public', 'cuis', 'numlieucontrat' );
SELECT alter_table_drop_column_if_exists('public', 'cuis', 'typevoielieucontrat' );
SELECT alter_table_drop_column_if_exists('public', 'cuis', 'nomvoielieucontrat' );
SELECT alter_table_drop_column_if_exists('public', 'cuis', 'codepostallieucontrat' );
SELECT alter_table_drop_column_if_exists('public', 'cuis', 'villelieucontrat' );

SELECT alter_table_drop_column_if_exists('public', 'cuis', 'qualtuteur' );
SELECT alter_table_drop_column_if_exists('public', 'cuis', 'nomtuteur' );
SELECT alter_table_drop_column_if_exists('public', 'cuis', 'prenomtuteur' );
SELECT add_missing_table_field('public', 'cuis', 'tuteur', 'VARCHAR(250)' );

SELECT add_missing_table_field('public', 'cuis', 'orgsuivi_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'cuis', 'cuis_orgsuivi_id_fkey', 'structuresreferentes', 'orgsuivi_id', false );
SELECT add_missing_table_field('public', 'cuis', 'prestataire_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'cuis', 'cuis_prestataire_id_fkey', 'referents', 'prestataire_id', false );

ALTER TABLE cuis ALTER COLUMN referent_id DROP NOT NULL;


SELECT add_missing_table_field('public', 'cuis', 'haspiecejointe', 'type_booleannumber' );
ALTER TABLE cuis ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE cuis SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE cuis ALTER COLUMN haspiecejointe SET NOT NULL;

SELECT add_missing_table_field ('public', 'cuis', 'user_id', 'INTEGER');
SELECT add_missing_constraint( 'public', 'cuis', 'cuis_user_id_fk', 'users', 'user_id' );

DROP INDEX IF EXISTS cuis_user_id_idx;
CREATE INDEX cuis_user_id_idx ON cuis(user_id);

SELECT add_missing_table_field('public', 'cuis', 'montantrsapercu', 'NUMERIC(9,2)' );
-------------------------------------------------------------------------------------------------------------
-- 20120725: Création d'une table de propositions de décisions pour le CUI
-------------------------------------------------------------------------------------------------------------
DROP TYPE IF EXISTS TYPE_PROPOSITIONCUI66 CASCADE;
CREATE TYPE TYPE_PROPOSITIONCUI66 AS ENUM ( 'enattente', 'accord','refus', 'sanssuiteemployeur', 'sanssuiteemploye', 'sanssuitesalarie', 'denonciation', 'resilie', 'elementsup' );

DROP TABLE IF EXISTS proposdecisionscuis66 CASCADE;
CREATE TABLE proposdecisionscuis66(
  	id 						SERIAL NOT NULL PRIMARY KEY,
    cui_id             		INTEGER NOT NULL REFERENCES cuis(id) ON DELETE CASCADE ON UPDATE CASCADE,
    propositioncui			TYPE_PROPOSITIONCUI66 DEFAULT NULL,
    datepropositioncui		DATE NOT NULL,
    observcui				TEXT,
    isaviselu				TYPE_BOOLEANNUMBER DEFAULT '0',
    propositioncuielu		TYPE_PROPOSITIONCUI66 DEFAULT NULL,
    datepropositioncuielu		DATE DEFAULT NULL,
    observcuielu				TEXT DEFAULT NULL,
	isavisreferent				TYPE_BOOLEANNUMBER DEFAULT '0',
    propositioncuireferent		TYPE_PROPOSITIONCUI66 DEFAULT NULL,
    datepropositioncuireferent		DATE DEFAULT NULL,
    observcuireferent				TEXT DEFAULT NULL,
    user_id			INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE proposdecisionscuis66 IS 'Table de propositions de décision du CUI (CG66)';

DROP INDEX IF EXISTS proposdecisionscuis66_propositioncui_idx;

CREATE INDEX proposdecisionscuis66_propositioncui_idx ON proposdecisionscuis66(propositioncui);

DROP INDEX IF EXISTS proposdecisionscuis66_propositioncuielu_idx;
CREATE INDEX proposdecisionscuis66_propositioncuielu_idx ON proposdecisionscuis66(propositioncuielu);

DROP INDEX IF EXISTS proposdecisionscuis66_user_id_idx;
CREATE INDEX proposdecisionscuis66_user_id_isx ON proposdecisionscuis66(user_id);


-------------------------------------------------------------------------------------------------------------
-- 20120727: Création d'une table pour les décisions de CUI (CG66)
-------------------------------------------------------------------------------------------------------------
DROP TABLE IF EXISTS decisionscuis66 CASCADE;
CREATE TABLE decisionscuis66(
  	id 						SERIAL NOT NULL PRIMARY KEY,
    cui_id             		INTEGER NOT NULL REFERENCES cuis(id) ON DELETE CASCADE ON UPDATE CASCADE,
    decisioncui			TYPE_PROPOSITIONCUI66 DEFAULT NULL,
    datedecisioncui		DATE NOT NULL,
    observdecisioncui				TEXT,
    user_id			INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE decisionscuis66 IS 'Table de décisions du CUI (CG66)';

DROP INDEX IF EXISTS decisionscuis66_decisioncui_idx;
CREATE INDEX decisionscuis66_decisioncui_idx ON decisionscuis66(decisioncui);

DROP INDEX IF EXISTS decisionscuis66_user_id_idx;
CREATE INDEX decisionscuis66_user_id_idx ON decisionscuis66(user_id);

-------------------------------------------------------------------------------------------------------------
-- 20120727: Création d'une table pour les suspensions/ruptures de CUI (CG66)
-------------------------------------------------------------------------------------------------------------
DROP TYPE IF EXISTS TYPE_SUSPENSIONCUI66 CASCADE;
CREATE TYPE TYPE_SUSPENSIONCUI66 AS ENUM ( 'absence', 'arret' );

DROP TABLE IF EXISTS suspensionscuis66 CASCADE;
CREATE TABLE suspensionscuis66(
  	id 							SERIAL NOT NULL PRIMARY KEY,
    cui_id             			INTEGER NOT NULL REFERENCES cuis(id) ON DELETE CASCADE ON UPDATE CASCADE,
    typesuspensioncui66			TYPE_SUSPENSIONCUI66 DEFAULT NULL,
    user_id						INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE suspensionscuis66 IS 'Table de suspensions/ruptures pour le CUI (CG66)';

DROP INDEX IF EXISTS suspensionscuis66_typesuspensioncui66_idx;
CREATE INDEX suspensionscuis66_typesuspensioncui66_idx ON suspensionscuis66(typesuspensioncui66);

DROP INDEX IF EXISTS suspensionscuis66_user_id_idx;
CREATE INDEX suspensionscuis66_user_id_idx ON suspensionscuis66(user_id);


-------------------------------------------------------------------------------------------------------------
-- 20120727: Création d'une table pour les accompagnements du CUI (CG66)
-------------------------------------------------------------------------------------------------------------
DROP TYPE IF EXISTS TYPE_ACCOMPAGNEMENTCUI66 CASCADE;
CREATE TYPE TYPE_ACCOMPAGNEMENTCUI66 AS ENUM ( 'immersion', 'formation' );

DROP TYPE IF EXISTS TYPE_OBJECTIFIMMERSION CASCADE;
CREATE TYPE TYPE_OBJECTIFIMMERSION AS ENUM ( 'acquerir', 'confirmer', 'decouvrir', 'initier' );

DROP TABLE IF EXISTS accompagnementscuis66 CASCADE;
CREATE TABLE accompagnementscuis66(
  	id 							SERIAL NOT NULL PRIMARY KEY,
    cui_id             			INTEGER NOT NULL REFERENCES cuis(id) ON DELETE CASCADE ON UPDATE CASCADE,
    typeaccompagnementcui66			TYPE_ACCOMPAGNEMENTCUI66 DEFAULT NULL,
	nomentaccueil                   VARCHAR(50) DEFAULT NULL,
    numvoieentaccueil               VARCHAR(6) DEFAULT NULL,
    typevoieentaccueil              VARCHAR(4) DEFAULT NULL,
    nomvoieentaccueil               VARCHAR(50) DEFAULT NULL,
    compladrentaccueil              VARCHAR(50) DEFAULT NULL,
    numtelentaccueil                VARCHAR(14) DEFAULT NULL,
    emailentaccueil                 VARCHAR(78) DEFAULT NULL,
    codepostalentaccueil              CHAR(5) DEFAULT NULL,
    villeentaccueil                 VARCHAR(45) DEFAULT NULL,
    siretentaccueil                 CHAR(14) DEFAULT NULL,
    activiteentaccueil              CHAR(14) DEFAULT NULL,
    datedebperiode                  DATE DEFAULT NULL,
    datefinperiode                  DATE DEFAULT NULL,
    nbjourperiode                   INTEGER DEFAULT NULL,
    secteuraffectation_id            INTEGER REFERENCES codesromesecteursdsps66(id)  ON DELETE CASCADE ON UPDATE CASCADE,
    metieraffectation_id            INTEGER REFERENCES  codesromemetiersdsps66(id)  ON DELETE CASCADE ON UPDATE CASCADE,
    objectifimmersion               TYPE_OBJECTIFIMMERSION DEFAULT NULL,
    datesignatureimmersion          DATE DEFAULT NULL,
    user_id						INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE accompagnementscuis66 IS 'Table des accompagnements (périodes immersion, formations) pour le CUI (CG66)';

DROP INDEX IF EXISTS accompagnementscuis66_typeaccompagnementcui66_idx;
CREATE INDEX accompagnementscuis66_typeaccompagnementcui66_idx ON accompagnementscuis66(typeaccompagnementcui66);

DROP INDEX IF EXISTS accompagnementscuis66_secteuraffectation_id_idx;
CREATE INDEX accompagnementscuis66_secteuraffectation_id_idx ON accompagnementscuis66(secteuraffectation_id);

DROP INDEX IF EXISTS accompagnementscuis66_metieraffectation_id_idx;
CREATE INDEX accompagnementscuis66_metieraffectation_id_idx ON accompagnementscuis66(metieraffectation_id);

DROP INDEX IF EXISTS accompagnementscuis66_user_id_idx;
CREATE INDEX accompagnementscuis66_user_id_idx ON accompagnementscuis66(user_id);

SELECT add_missing_table_field('public', 'cuis', 'secteuractiviteemployeur_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'cuis', 'cuis_secteuractiviteemployeur_id_fkey', 'codesromesecteursdsps66', 'secteuractiviteemployeur_id', false );

SELECT add_missing_table_field('public', 'cuis', 'secteuractiviteemployeur2_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'cuis', 'cuis_secteuractiviteemployeur2_id_fkey', 'codesromesecteursdsps66', 'secteuractiviteemployeur2_id', false );


SELECT alter_table_drop_column_if_exists('public', 'cuis', 'codeemploi' );

SELECT add_missing_table_field('public', 'cuis', 'secteuremploipropose_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'cuis', 'cuis_secteuremploipropose_id_fkey', 'codesromesecteursdsps66', 'secteuremploipropose_id', false );
SELECT add_missing_table_field('public', 'cuis', 'metieremploipropose_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'cuis', 'cuis_metieremploipropose_id_fkey', 'codesromemetiersdsps66', 'metieremploipropose_id', false );


DROP TYPE IF EXISTS TYPE_POSITIONCUI66 CASCADE;
CREATE TYPE TYPE_POSITIONCUI66 AS ENUM ( 'attavismne', 'attaviselu', 'attavisreferent', 'attdecision', 'encours', 'annule', 'fincontrat', 'attrenouv', 'perime', 'nonvalide', 'valid', 'validnotifie', 'nonvalidnotifie' );
SELECT add_missing_table_field('public', 'cuis', 'positioncui66', 'TYPE_POSITIONCUI66' );

ALTER TABLE cuis ALTER COLUMN positioncui66 SET DEFAULT 'attavismne'::TYPE_POSITIONCUI66;
UPDATE cuis SET positioncui66 = 'attavismne'::TYPE_POSITIONCUI66 WHERE positioncui66 IS NULL;
ALTER TABLE cuis ALTER COLUMN positioncui66 SET NOT NULL;

-------------------------------------------------------------------------------------------------------------
-- 20120731: Ajout d'une clé étrangère entre le CUI et le bilan de parcours (CUI vaut CER)
-------------------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'bilansparcours66', 'cui_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'bilansparcours66', 'bilansparcours66_cui_id_fkey', 'cuis', 'cui_id');

SELECT add_missing_table_field ('public', 'defautsinsertionseps66', 'cui_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'defautsinsertionseps66', 'defautsinsertionseps66_cui_id_fkey', 'cuis', 'cui_id');

-------------------------------------------------------------------------------------------------------------
-- 20120801: Ajout du rang pour le cui
-------------------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'cuis', 'rangcui', 'INTEGER');

-------------------------------------------------------------------------------------------------------------
-- 20120802: Ajout de la table pour stocker les motifs de sortie du cui
-------------------------------------------------------------------------------------------------------------

DROP TABLE IF EXISTS motifssortiecuis66 CASCADE;
CREATE TABLE motifssortiecuis66(
  	id 							SERIAL NOT NULL PRIMARY KEY,
    name						TEXT NOT NULL,
    created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE motifssortiecuis66 IS 'Table des motifs de sortie d''un CUI (CG66)';

DROP INDEX IF EXISTS motifssortiecuis66_name_idx;
CREATE UNIQUE INDEX motifssortiecuis66_name_idx ON motifssortiecuis66(name);


SELECT public.alter_enumtype ( 'TYPE_SECTEUR', ARRAY['cie', 'cae', 'CIE', 'CAE'] );
UPDATE cuis SET secteur = 'cie' WHERE secteur = 'CIE';
UPDATE cuis SET secteur = 'cae' WHERE secteur = 'CAE';
SELECT public.alter_enumtype ( 'TYPE_SECTEUR', ARRAY['cie', 'cae'] );

-------------------------------------------------------------------------------------------------------------
-- 20120828: Modification de l'ENUM pour les positions du CER CG66
-------------------------------------------------------------------------------------------------------------

SELECT public.alter_enumtype ( 'TYPE_POSITIONCER', ARRAY['encours','attvalid','attvalidpart','attvalidpartpropopcg','attvalidsimple','annule','fincontrat','encoursbilan','attrenouv','perime','nonvalide','attsignature','valid','nonvalid','validnotifie','nonvalidnotifie'] );
UPDATE contratsinsertion SET positioncer = 'nonvalid' WHERE positioncer = 'nonvalide';
SELECT public.alter_enumtype ( 'TYPE_POSITIONCER', ARRAY['encours','attvalid','attvalidpart','attvalidpartpropopcg','attvalidsimple','annule','fincontrat','encoursbilan','attrenouv','perime','attsignature','valid','nonvalid','validnotifie','nonvalidnotifie'] );

-------------------------------------------------------------------------------------------------------------
-- 20120917: Ajout de pièces jointes  aux decisionsdossierspcgs66
-------------------------------------------------------------------------------------------------------------

SELECT add_missing_table_field('public', 'decisionsdossierspcgs66', 'haspiecejointe', 'type_booleannumber' );
ALTER TABLE decisionsdossierspcgs66 ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE decisionsdossierspcgs66 SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE decisionsdossierspcgs66 ALTER COLUMN haspiecejointe SET NOT NULL;

SELECT add_missing_table_field('public', 'decisionsdossierspcgs66', 'useravistechnique_id', 'INTEGER' );
SELECT add_missing_table_field ( 'public', 'decisionsdossierspcgs66', 'userproposition_id', 'INTEGER' );

ALTER TABLE decisionsdossierspcgs66 ADD CONSTRAINT decisionsdossierspcgs66_useravistechnique_id_fk FOREIGN KEY (useravistechnique_id) REFERENCES users(id);
ALTER TABLE decisionsdossierspcgs66 ADD CONSTRAINT decisionsdossierspcgs66_userproposition_id_fk FOREIGN KEY (userproposition_id) REFERENCES users(id);

SELECT add_missing_table_field('public', 'apres', 'motifannulation', 'TEXT' );
SELECT public.alter_enumtype ( 'TYPE_ETATDOSSIERAPRE', ARRAY['COM', 'INC', 'VAL', 'TRA', 'ANN'] );

-------------------------------------------------------------------------------------------------------------
-- 20120917: Transformation du CHAR(32) en VARCHAR(32) pour les jetons.
-------------------------------------------------------------------------------------------------------------
ALTER TABLE jetons ALTER COLUMN php_sid TYPE VARCHAR(32);
ALTER TABLE jetonsfonctions ALTER COLUMN php_sid TYPE VARCHAR(32);

-------------------------------------------------------------------------------------------------------------
-- 20120919: Ajout d'un lien entre les actionscandidats et le CER et les entretiens
-------------------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ( 'public', 'contratsinsertion', 'actioncandidat_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'contratsinsertion', 'contratsinsertion_actioncandidat_id_fkey', 'actionscandidats', 'actioncandidat_id', false );

SELECT add_missing_table_field ( 'public', 'entretiens', 'actioncandidat_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'entretiens', 'entretiens_actioncandidat_id_fkey', 'actionscandidats', 'actioncandidat_id', false );

SELECT add_missing_table_field('public', 'cuis', 'motifannulation', 'TEXT' );

SELECT public.alter_enumtype ( 'TYPE_DECISION', ARRAY['E', 'V', 'A', 'R', 'C'] );

-------------------------------------------------------------------------------------------------------------
-- 20120924: Ajout d'une colonne pour les paramètres éventuels de la table jetonsfonctions
-------------------------------------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'jetonsfonctions', 'params', 'VARCHAR(255)' );

DROP INDEX IF EXISTS jetonsfonctions_controller_action_params_idx;

DROP INDEX IF EXISTS jetonsfonctions_controller_action_params_idx;
CREATE UNIQUE INDEX jetonsfonctions_controller_action_params_idx ON jetonsfonctions( controller, action, params );

-------------------------------------------------------------------------------------------------------------
-- 20121001: Ajout d'une colonne pour la table typesaidesapres66 afin de
--           déterminer si l'aide se voit passer en cohorte de validation ou pas
-------------------------------------------------------------------------------------------------------------

SELECT add_missing_table_field('public', 'typesaidesapres66', 'isincohorte', 'TYPE_NO' );
ALTER TABLE typesaidesapres66 ALTER COLUMN isincohorte SET DEFAULT 'O'::TYPE_NO;
UPDATE typesaidesapres66 SET isincohorte = 'O'::TYPE_NO WHERE isincohorte IS NULL;
ALTER TABLE typesaidesapres66 ALTER COLUMN isincohorte SET NOT NULL;

SELECT public.alter_enumtype ( 'TYPE_PROPOSITIONBILANPARCOURS', ARRAY['audition','parcours','traitement','auditionpe','parcourspe'] );

-------------------------------------------------------------------------------------------------------------
-- 20121004: Création d'une table de liaison entre les motifs de sortie et les actions
--           d'insertion (en HABTM)
-------------------------------------------------------------------------------------------------------------
DROP TABLE IF EXISTS actionscandidats_motifssortie;
CREATE TABLE actionscandidats_motifssortie (
    id                      SERIAL NOT NULL PRIMARY KEY,
    actioncandidat_id       INTEGER NOT NULL REFERENCES actionscandidats(id) ON DELETE CASCADE ON UPDATE CASCADE,
    motifsortie_id          INTEGER NOT NULL REFERENCES motifssortie(id) ON DELETE CASCADE ON UPDATE CASCADE
);
DROP INDEX IF EXISTS actionscandidats_motifssortie_actioncandidat_id_idx;
CREATE INDEX actionscandidats_motifssortie_actioncandidat_id_idx ON actionscandidats_motifssortie(actioncandidat_id);

DROP INDEX IF EXISTS actionscandidats_motifssortie_motifsortie_id_idx;
CREATE INDEX actionscandidats_motifssortie_motifsortie_id_idx ON actionscandidats_motifssortie(motifsortie_id);


-------------------------------------------------------------------------------------------------------------
-- 20121012: Modification du bilan de parcours suite à la rencontre du 11/10/2012
-------------------------------------------------------------------------------------------------------------

ALTER TABLE bilansparcours66 ALTER COLUMN structurereferente_id DROP NOT NULL;

SELECT add_missing_table_field ( 'public', 'bilansparcours66', 'serviceinstructeur_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'bilansparcours66', 'bilansparcours66_serviceinstructeur_id_fkey', 'servicesinstructeurs', 'serviceinstructeur_id', false );


SELECT add_missing_table_field ('public', 'bilansparcours66', 'user_id', 'INTEGER');
SELECT add_missing_constraint( 'public', 'bilansparcours66', 'bilansparcours66_user_id_fk', 'users', 'user_id' );
DROP INDEX IF EXISTS bilansparcours66_user_id_idx;
CREATE INDEX bilansparcours66_user_id_idx ON bilansparcours66(user_id);

-------------------------------------------------------------------------------------------------------------
-- 20121012: Nouvelle thématique de passage en COV (CG 58)
-------------------------------------------------------------------------------------------------------------

ALTER TABLE statutsrdvs RENAME COLUMN provoquepassageep TO provoquepassagecommission;
ALTER TABLE statutsrdvs_typesrdv RENAME COLUMN nbabsenceavantpassageep TO nbabsenceavantpassagecommission;

DROP TYPE IF EXISTS TYPE_TYPECOMMISSION CASCADE;
CREATE TYPE TYPE_TYPECOMMISSION AS ENUM ( 'cov', 'ep' );
SELECT add_missing_table_field( 'public', 'statutsrdvs_typesrdv', 'typecommission', 'TYPE_TYPECOMMISSION' );
ALTER TABLE statutsrdvs_typesrdv ALTER COLUMN typecommission SET DEFAULT 'ep';
UPDATE statutsrdvs_typesrdv SET typecommission = 'ep' WHERE typecommission IS NULL;
ALTER TABLE statutsrdvs_typesrdv ALTER COLUMN typecommission SET NOT NULL;

-- Nouvelle thématique de COV: orientations sociales de fait

INSERT INTO themescovs58 ( name ) VALUES ( 'proposorientssocialescovs58' );

SELECT add_missing_table_field ( 'public', 'rendezvous', 'rang', 'INTEGER' );

DROP TABLE IF EXISTS proposorientssocialescovs58;
CREATE TABLE proposorientssocialescovs58 (
	id 				SERIAL NOT NULL PRIMARY KEY,
	dossiercov58_id		INTEGER NOT NULL REFERENCES dossierscovs58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	rendezvous_id		INTEGER NOT NULL REFERENCES rendezvous(id) ON DELETE CASCADE ON UPDATE CASCADE,
	nvorientstruct_id		INTEGER DEFAULT NULL REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	commentaire		TEXT DEFAULT NULL,
	user_id			INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
	created			TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE proposorientssocialescovs58 IS 'Orientations sociales de fait en attente de validation par la COV (cg58)';

CREATE UNIQUE INDEX proposorientssocialescovs58_dossiercov58_id_idx ON proposorientssocialescovs58(dossiercov58_id);
CREATE UNIQUE INDEX proposorientssocialescovs58_rendezvous_id_idx ON proposorientssocialescovs58(rendezvous_id);
CREATE UNIQUE INDEX proposorientssocialescovs58_nvorientstruct_id_idx ON proposorientssocialescovs58(nvorientstruct_id);
CREATE INDEX proposorientssocialescovs58_user_id_idx ON proposorientssocialescovs58(user_id);
-- CREATE INDEX proposorientssocialescovs58_typeorient_id_idx ON proposorientssocialescovs58(typeorient_id);
-- CREATE INDEX proposorientssocialescovs58_orientstruct_id_idx ON proposorientssocialescovs58(orientstruct_id);
-- CREATE INDEX proposorientssocialescovs58_structurereferente_id_idx ON proposorientssocialescovs58(structurereferente_id);
-- CREATE INDEX proposorientssocialescovs58_referent_id_idx ON proposorientssocialescovs58(referent_id);
-- CREATE INDEX proposorientssocialescovs58_covtypeorient_id_idx ON proposorientssocialescovs58(covtypeorient_id);
-- CREATE INDEX proposorientssocialescovs58_covstructurereferente_id_idx ON proposorientssocialescovs58(covstructurereferente_id);

-- Décisions de la thématique de COV "orientations sociales de fait"

DROP TABLE IF EXISTS decisionsproposorientssocialescovs58;
CREATE TABLE decisionsproposorientssocialescovs58 (
	id      				SERIAL NOT NULL PRIMARY KEY,
	passagecov58_id	INTEGER NOT NULL REFERENCES passagescovs58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	etapecov			TYPE_ETAPECOV NOT NULL,
	decisioncov		TYPE_DECISIONORIENTATIONCOV NOT NULL,
	typeorient_id 		INTEGER DEFAULT NULL REFERENCES typesorients(id) ON DELETE CASCADE ON UPDATE CASCADE,
	structurereferente_id	INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	referent_id 		INTEGER DEFAULT NULL REFERENCES referents(id) ON DELETE CASCADE ON UPDATE CASCADE,
	datevalidation		DATE,
	commentaire		TEXT DEFAULT NULL,
	created			TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);

CREATE INDEX decisionsproposorientssocialescovs58_passagecov58_id_idx ON decisionsproposorientssocialescovs58( passagecov58_id );
CREATE INDEX decisionsproposorientssocialescovs58_etapecov_idx ON decisionsproposorientssocialescovs58( etapecov );
CREATE INDEX decisionsproposorientssocialescovs58_decisioncov_idx ON decisionsproposorientssocialescovs58( decisioncov );
CREATE UNIQUE INDEX decisionsproposorientssocialescovs58_passagecov58_id_etapecov_idx ON decisionsproposorientssocialescovs58(passagecov58_id, etapecov);

SELECT public.alter_enumtype ( 'TYPE_THEMECOV58', ARRAY[ 'proposorientationscovs58', 'proposcontratsinsertioncovs58', 'proposnonorientationsproscovs58', 'proposorientssocialescovs58' ] );
SELECT add_missing_table_field ('public', 'themescovs58', 'propoorientsocialecov58', 'TYPE_ETAPECOV');

-------------------------------------------------------------------------------------------------------------
-- 20121015: Modification des pièces manquantes liées au traitementpcg66 avec
--				ajout des montants et de dates si la case est cochée
-------------------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'modelestypescourrierspcgs66', 'ismontant', 'TYPE_BOOLEANNUMBER');
ALTER TABLE modelestypescourrierspcgs66 ALTER COLUMN ismontant SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE modelestypescourrierspcgs66 SET ismontant = '0'::TYPE_BOOLEANNUMBER WHERE ismontant IS NULL;
DROP INDEX IF EXISTS modelestypescourrierspcgs66_ismontant_idx;
CREATE INDEX modelestypescourrierspcgs66_ismontant_idx ON modelestypescourrierspcgs66 (ismontant);

SELECT add_missing_table_field ('public', 'modelestypescourrierspcgs66', 'isdates', 'TYPE_BOOLEANNUMBER');
ALTER TABLE modelestypescourrierspcgs66 ALTER COLUMN isdates SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE modelestypescourrierspcgs66 SET isdates = '0'::TYPE_BOOLEANNUMBER WHERE isdates IS NULL;
DROP INDEX IF EXISTS modelestypescourrierspcgs66_isdates_idx;
CREATE INDEX modelestypescourrierspcgs66_isdates_idx ON modelestypescourrierspcgs66(isdates);


SELECT add_missing_table_field ( 'public', 'modelestraitementspcgs66', 'montantsaisi', 'NUMERIC' );
SELECT add_missing_table_field ( 'public', 'modelestraitementspcgs66', 'montantdatedebut', 'DATE' );
SELECT add_missing_table_field ( 'public', 'modelestraitementspcgs66', 'montantdatefin', 'DATE' );

DROP TYPE IF EXISTS TYPE_NONVALIDATIONPARTICULIER66 CASCADE;
CREATE TYPE TYPE_NONVALIDATIONPARTICULIER66 AS ENUM ( 'reprise','radiation');
SELECT add_missing_table_field ( 'public', 'proposdecisionscers66', 'nonvalidationparticulier', 'TYPE_NONVALIDATIONPARTICULIER66' );


-------------------------------------------------------------------------------------------------------------
-- 20121026: Ajout d'un champ pour noter la raison de l'annulation du bilan de parcours 66
-------------------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ( 'public', 'bilansparcours66', 'motifannulation', 'TEXT' );
-- *****************************************************************************
COMMIT;
-- *****************************************************************************