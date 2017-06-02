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

-- 20140922: Suppression des entrées de la table orientsstructs qui ne sont pas "Orienté"
DELETE FROM orientsstructs WHERE orientsstructs.statut_orient <> 'Orienté';

-- 20141006: mise à jour des CER dont la position est annule mais qui n'ont pas de texte d'annulation
UPDATE contratsinsertion SET motifannulation = '...' WHERE positioncer = 'annule' AND motifannulation IS NULL;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
