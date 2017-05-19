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
-- Tables de corespondance entre personne_id
--------------------------------------------------------------------------------

SELECT alter_table_drop_constraint_if_exists ( 'public', 'correspondancespersonnes', 'correspondancespersonnes_personne1_id_fkey' );
SELECT alter_table_drop_constraint_if_exists ( 'public', 'correspondancespersonnes', 'correspondancespersonnes_personne2_id_fkey' );
ALTER TABLE correspondancespersonnes ADD CONSTRAINT correspondancespersonnes_personne1_id_fkey FOREIGN KEY (personne1_id) REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE correspondancespersonnes ADD CONSTRAINT correspondancespersonnes_personne2_id_fkey FOREIGN KEY (personne2_id) REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE;

--------------------------------------------------------------------------------
-- Nouvelle position du CUI
--------------------------------------------------------------------------------

SELECT alter_table_drop_constraint_if_exists ( 'public', 'cuis66', 'cuis66_etatdossiercui66_in_list_chk' );
ALTER TABLE cuis66 ADD CONSTRAINT cuis66_etatdossiercui66_in_list_chk CHECK ( cakephp_validate_in_list( etatdossiercui66, ARRAY['attentepiece','dossierrecu','dossiernonrecu','dossierrelance','dossiereligible','attentemail','formulairecomplet','attenteavis','attentedecision','attentenotification','notifie','encours','perime','rupturecontrat','contratsuspendu','decisionsanssuite','nonvalide','annule'] ) );

-- *****************************************************************************
COMMIT;
-- *****************************************************************************