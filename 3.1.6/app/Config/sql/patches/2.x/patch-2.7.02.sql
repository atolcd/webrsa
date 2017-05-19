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
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_positioncui66_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_positioncui66_in_list_chk CHECK (cakephp_validate_in_list(positioncui66::text, ARRAY['attenvoimail'::text, 'dossierrecu'::text, 'dossiereligible'::text, 'attpieces'::text, 'attavismne'::text, 'attaviselu'::text, 'attavisreferent'::text, 'attdecision'::text, 'encours'::text, 'annule'::text, 'decisionsanssuite'::text, 'fincontrat'::text, 'attrenouv'::text, 'perime'::text, 'nonvalide'::text, 'valid'::text, 'validnotifie'::text, 'nonvalidnotifie'::text, 'rupture'::text, 'traite'::text]));

SELECT add_missing_table_field ( 'public', 'cuis', 'renouvellement', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_renouvellement_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_renouvellement_in_list_chk CHECK ( cakephp_validate_in_list( renouvellement, ARRAY['0','1'] ) );



ALTER TABLE cuis ALTER COLUMN decisioncui DROP NOT NULL;
ALTER TABLE cuis ALTER COLUMN decisioncui SET DEFAULT NULL;
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_decisioncui_in_list_chk' );
UPDATE cuis SET decisioncui = NULL WHERE decisioncui ='enattente';
UPDATE cuis SET decisioncui = 'sanssuite' WHERE decisioncui = 'annule';
ALTER TABLE cuis ADD CONSTRAINT cuis_decisioncui_in_list_chk CHECK ( cakephp_validate_in_list( decisioncui, ARRAY['accord','refus','ajourne','sanssuite'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'decisionscuis66', 'decisioncui_decisioncui_in_list_chk' );
ALTER TABLE decisionscuis66 ADD CONSTRAINT decisioncui_decisioncui_in_list_chk CHECK ( cakephp_validate_in_list( decisioncui, ARRAY['accord','refus','ajourne', 'sanssuite'] ) );



SELECT add_missing_table_field('public', 'piecesmailscuis66', 'haspiecejointe', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'piecesmailscuis66', 'piecesmailscuis66_haspiecejointe_in_list_chk' );
ALTER TABLE piecesmailscuis66 ADD CONSTRAINT piecesmailscuis66_haspiecejointe_in_list_chk CHECK ( cakephp_validate_in_list( haspiecejointe, ARRAY['0','1'] ) );
UPDATE piecesmailscuis66 SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE piecesmailscuis66 ALTER COLUMN haspiecejointe SET NOT NULL;

SELECT add_missing_table_field('public', 'cuis', 'datebutoirpiece', 'DATE' );

SELECT add_missing_table_field ( 'public', 'cuis', 'textmailcui66relance_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'cuis', 'cuis_textmailcui66relance_id_fkey', 'textsmailscuis66', 'textmailcui66relance_id', false );

SELECT add_missing_table_field('public', 'cuis', 'commentairemailrelance', 'TEXT' );
SELECT add_missing_table_field('public', 'cuis', 'dateenvoirelance', 'DATE' );

-- 20140922: ajout d'une nouvelle position au CER
SELECT public.alter_enumtype( 'TYPE_POSITIONCER', ARRAY['encours','attvalid','annule','fincontrat','encoursbilan','attrenouv','perime','nonvalid','perimebilanarealiser','bilanrealiseattenteeplparcours'] );

-- 20141006: une des actions du controller orientsstructs a changé de nom
UPDATE acos SET alias = 'Orientsstructs:impression_changement_referent' WHERE alias = 'Orientsstructs:printChangementReferent';

-------------------------------------------------------------------------------------
-- 20141010: Suppression des intitulés tableau1b4new et tableau1b4new dans la contrainte tableauxsuivispdvs93_name_in_list_chk
-------------------------------------------------------------------------------------

UPDATE tableauxsuivispdvs93 SET name = 'tableau1b4' WHERE name = 'tableau1b4new';
UPDATE tableauxsuivispdvs93 SET name = 'tableau1b5' WHERE name = 'tableau1b5new';

SELECT alter_table_drop_constraint_if_exists( 'public', 'tableauxsuivispdvs93', 'tableauxsuivispdvs93_name_in_list_chk' );
ALTER TABLE tableauxsuivispdvs93 ADD CONSTRAINT tableauxsuivispdvs93_name_in_list_chk CHECK ( cakephp_validate_in_list( name, ARRAY['tableaud1', 'tableaud2', 'tableau1b3', 'tableau1b4', 'tableau1b5', 'tableau1b6'] ) );

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
