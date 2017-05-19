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

CREATE VIEW dps AS
 SELECT dsps.accoemploi,
    dsps.accosocfam,
    dsps.cessderact,
    dsps.duractdomi,
    dsps.libactdomi,
    dsps.libformenv,
    dsps.topcouvsoc,
    dsps.topmoyloco,
    dsps.topprojpro,
    dsps.accosocindi,
    dsps.personne_id,
    dsps.topqualipro,
    dsps.nivdipmaxobt,
    dsps.soutdemarsoc,
    dsps.suivimedical,
    dsps.inscdememploi,
    dsps.libemploirech,
    dsps.libsecactdomi,
    dsps.libsecactrech,
    dsps.sitpersdemrsa,
    dsps.topdomideract,
    dsps.drorsarmianta2,
    dsps.topisogroouenf,
    dsps.toppermicondub,
    dsps.annobtnivdipmax,
    dsps.libautrqualipro,
    dsps.libsecactderact,
    dsps.topdrorsarmiant,
    dsps.libcompeextrapro,
    dsps.statutoccupation,
    dsps.topcompeextrapro,
    dsps.libautrpermicondu,
    dsps.libcooraccoemploi,
    dsps.libcooraccosocfam,
    dsps.topautrpermicondu,
    dsps.topcreareprientre,
    dsps.libcooraccosocindi,
    dsps.topisogrorechemploi,
    dsps.concoformqualiemploi,
    dsps.topengdemarechemploi,
    dsps.libderact66_metier_id,
    dsps.libactdomi66_metier_id,
    dsps.libemploirech66_metier_id,
    dsps.libsecactdomi66_secteur_id,
    dsps.libsecactrech66_secteur_id,
    dsps.libsecactderact66_secteur_id,
    dsps.id,
    dsps.hispro,
    dsps.natlog,
    dsps.nivetu,
    dsps.demarlog,
    dsps.libderact
   FROM dsps;

CREATE VIEW dps_rev AS
 SELECT dsps_revs.accoemploi,
    dsps_revs.accosocfam,
    dsps_revs.cessderact,
    dsps_revs.duractdomi,
    dsps_revs.libactdomi,
    dsps_revs.libformenv,
    dsps_revs.topcouvsoc,
    dsps_revs.topmoyloco,
    dsps_revs.topprojpro,
    dsps_revs.accosocindi,
    dsps_revs.personne_id,
    dsps_revs.topqualipro,
    dsps_revs.nivdipmaxobt,
    dsps_revs.soutdemarsoc,
    dsps_revs.suivimedical,
    dsps_revs.inscdememploi,
    dsps_revs.libemploirech,
    dsps_revs.libsecactdomi,
    dsps_revs.libsecactrech,
    dsps_revs.sitpersdemrsa,
    dsps_revs.topdomideract,
    dsps_revs.drorsarmianta2,
    dsps_revs.topisogroouenf,
    dsps_revs.toppermicondub,
    dsps_revs.annobtnivdipmax,
    dsps_revs.libautrqualipro,
    dsps_revs.libsecactderact,
    dsps_revs.topdrorsarmiant,
    dsps_revs.libcompeextrapro,
    dsps_revs.statutoccupation,
    dsps_revs.topcompeextrapro,
    dsps_revs.libautrpermicondu,
    dsps_revs.libcooraccoemploi,
    dsps_revs.libcooraccosocfam,
    dsps_revs.topautrpermicondu,
    dsps_revs.topcreareprientre,
    dsps_revs.libcooraccosocindi,
    dsps_revs.topisogrorechemploi,
    dsps_revs.concoformqualiemploi,
    dsps_revs.topengdemarechemploi,
    dsps_revs.libderact66_metier_id,
    dsps_revs.libactdomi66_metier_id,
    dsps_revs.libemploirech66_metier_id,
    dsps_revs.libsecactdomi66_secteur_id,
    dsps_revs.libsecactrech66_secteur_id,
    dsps_revs.libsecactderact66_secteur_id,
    dsps_revs.id,
    dsps_revs.dsp_id
   FROM dsps_revs;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************