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
-- Contraintes oubliées dans le CUI
--------------------------------------------------------------------------------

SELECT alter_table_drop_constraint_if_exists ( 'public', 'rupturescuis66', 'rupturescuis66_haspiecejointe_in_list_chk' );
ALTER TABLE rupturescuis66 ADD CONSTRAINT rupturescuis66_haspiecejointe_in_list_chk CHECK ( cakephp_validate_in_list( haspiecejointe, ARRAY['0','1'] ) );

SELECT alter_table_drop_constraint_if_exists ( 'public', 'suspensionscuis66', 'suspensionscuis66_haspiecejointe_in_list_chk' );
ALTER TABLE suspensionscuis66 ADD CONSTRAINT suspensionscuis66_haspiecejointe_in_list_chk CHECK ( cakephp_validate_in_list( haspiecejointe, ARRAY['0','1'] ) );

SELECT alter_table_drop_constraint_if_exists ( 'public', 'accompagnementscuis66', 'accompagnementscuis66_haspiecejointe_in_list_chk' );
ALTER TABLE accompagnementscuis66 ADD CONSTRAINT accompagnementscuis66_haspiecejointe_in_list_chk CHECK ( cakephp_validate_in_list( haspiecejointe, ARRAY['0','1'] ) );

SELECT alter_table_drop_constraint_if_exists ( 'public', 'decisionscuis66', 'decisionscuis66_haspiecejointe_in_list_chk' );
ALTER TABLE decisionscuis66 ADD CONSTRAINT decisionscuis66_haspiecejointe_in_list_chk CHECK ( cakephp_validate_in_list( haspiecejointe, ARRAY['0','1'] ) );

SELECT alter_table_drop_constraint_if_exists ( 'public', 'propositionscuis66', 'propositionscuis66_haspiecejointe_in_list_chk' );
ALTER TABLE propositionscuis66 ADD CONSTRAINT propositionscuis66_haspiecejointe_in_list_chk CHECK ( cakephp_validate_in_list( haspiecejointe, ARRAY['0','1'] ) );

--------------------------------------------------------------------------------
-- Option manquante dans la décision d'une EP AUDITION (decisionsup)
--------------------------------------------------------------------------------

SELECT alter_enumtype('TYPE_DECISIONSUPDEFAUTEP66', ARRAY['suspensionnonrespect', 'suspensiondefaut', 'suspensionsanction', 'maintien']);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
