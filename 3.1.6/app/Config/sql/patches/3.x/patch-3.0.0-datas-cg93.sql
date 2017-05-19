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
CREATE VIEW administration.allocatairestransferes AS
 SELECT o2.id AS vx_orientstruct_id, o1.id AS nv_orientstruct_id, a2.id AS vx_adressefoyer_id, a1.id AS nv_adressefoyer_id, o1.user_id, o1.date_valid AS created, o1.date_valid AS modified
   FROM personnes
   JOIN orientsstructs o1 ON personnes.id = o1.personne_id AND o1.origine = 'demenagement'
   JOIN orientsstructs o2 ON personnes.id = o2.personne_id
   JOIN adressesfoyers a1 ON a1.foyer_id = personnes.foyer_id AND a1.rgadr = '01'
   JOIN adressesfoyers a2 ON a2.foyer_id = personnes.foyer_id AND a2.rgadr = '02'
   JOIN adresses r1 ON a1.adresse_id = r1.id
   JOIN adresses r2 ON a2.adresse_id = r2.id
  WHERE o1.rgorient = (o2.rgorient + 1) AND ((personnes.id, o1.id) IN ( SELECT orientsstructs.personne_id, max(orientsstructs.id) AS max
   FROM orientsstructs
  GROUP BY orientsstructs.personne_id))
  ORDER BY o1.date_valid;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
