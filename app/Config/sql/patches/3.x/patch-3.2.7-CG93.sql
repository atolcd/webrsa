SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- *****************************************************************************
SELECT NOW();
BEGIN;
-- *****************************************************************************

ALTER TABLE fichesprescriptions93 DROP COLUMN rdvprestataire_personne;
ALTER TABLE fichesprescriptions93 DROP COLUMN date_presente_benef;
ALTER TABLE fichesprescriptions93 DROP COLUMN retour_nom_partenaire;
ALTER TABLE fichesprescriptions93 DROP COLUMN date_signature_partenaire;
ALTER TABLE fichesprescriptions93 DROP COLUMN personne_recue;
ALTER TABLE fichesprescriptions93 DROP COLUMN motifnonreceptionfp93_id;
ALTER TABLE fichesprescriptions93 DROP COLUMN personne_nonrecue_autre;
ALTER TABLE fichesprescriptions93 DROP COLUMN personne_souhaite_integrer;
ALTER TABLE fichesprescriptions93 DROP COLUMN motifnonsouhaitfp93_id;
ALTER TABLE fichesprescriptions93 DROP COLUMN personne_nonsouhaite_autre;
 
DROP TABLE motifsnonreceptionsfps93;
DROP TABLE motifsnonsouhaitsfps93;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************

